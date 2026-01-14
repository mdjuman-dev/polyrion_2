<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
   public function __construct()
   {
      // Permission checks are handled in routes
   }

   /**
    * Display all withdrawal requests
    */
   public function index(Request $request)
   {
      // Optimize: Select only necessary columns and eager load relationships with select
      $query = Withdrawal::select([
         'id', 'user_id', 'amount', 'currency', 'status', 'payment_method',
         'wallet_address', 'approved_by', 'admin_note', 'created_at', 'processed_at'
      ])
      ->with([
         'user' => function($q) {
            $q->select(['id', 'name', 'email', 'username']);
         },
         'approver' => function($q) {
            $q->select(['id', 'name', 'email']);
         }
      ]);

      // Filter by status
      if ($request->has('status') && $request->status !== '') {
         $query->where('status', $request->status);
      }

      // Search by user email or name
      if ($request->has('search') && $request->search !== '') {
         $search = $request->search;
         $query->whereHas('user', function ($q) use ($search) {
            $q->select(['id', 'name', 'email'])
              ->where('email', 'like', "%{$search}%")
               ->orWhere('name', 'like', "%{$search}%");
         });
      }

      $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

      // Optimize: Get stats in single query using conditional aggregation
      $statsQuery = Withdrawal::selectRaw('
         COUNT(*) as total,
         SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
         SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing,
         SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
         SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
      ')->first();

      $stats = [
         'pending' => $statsQuery->pending ?? 0,
         'processing' => $statsQuery->processing ?? 0,
         'completed' => $statsQuery->completed ?? 0,
         'rejected' => $statsQuery->rejected ?? 0,
         'total' => $statsQuery->total ?? 0,
      ];

      return view('backend.withdrawal.index', compact('withdrawals', 'stats'));
   }

   /**
    * Show withdrawal details
    */
   public function show($id)
   {
      // Optimize: Select only necessary columns
      $withdrawal = Withdrawal::select([
         'id', 'user_id', 'amount', 'currency', 'status', 'payment_method',
         'wallet_address', 'approved_by', 'admin_note', 'response_data',
         'created_at', 'processed_at', 'updated_at'
      ])
      ->with([
         'user' => function($q) {
            $q->select(['id', 'name', 'email', 'username', 'number']);
         },
         'approver' => function($q) {
            $q->select(['id', 'name', 'email']);
         }
      ])
      ->findOrFail($id);
      return view('backend.withdrawal.show', compact('withdrawal'));
   }

   /**
    * Approve withdrawal
    */
   public function approve(Request $request, $id)
   {

      try {
         DB::beginTransaction();

         $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

         if ($withdrawal->status !== 'pending' && $withdrawal->status !== 'processing') {
            return response()->json([
               'success' => false,
               'message' => 'Withdrawal is not in pending status.'
            ], 400);
         }

         $admin = Auth::guard('admin')->user();

         // Update withdrawal status
         $withdrawal->status = 'completed';
         $withdrawal->approved_by = $admin->id;
         $withdrawal->admin_note = $request->admin_note;
         $withdrawal->processed_at = now();
         $withdrawal->save();

         // Note: The amount was already deducted from wallet when withdrawal was created
         // So we don't need to deduct again here

         DB::commit();

         return back()->with('success', 'Withdrawal approved successfully.');
      } catch (\Exception $e) {
         DB::rollBack();

         Log::error("Withdrawal approval failed", [
            'withdrawal_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to approve withdrawal. Please try again.'
         ], 500);
      }
   }

   /**
    * Reject withdrawal
    */
   public function reject(Request $request, $id)
   {

      try {
         DB::beginTransaction();

         $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

         $admin = Auth::guard('admin')->user();

         // Refund the amount back to user's main wallet
         $user = $withdrawal->user;
         $wallet = Wallet::lockForUpdate()
            ->firstOrCreate(
               ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
               ['balance' => 0, 'status' => 'active', 'currency' => $withdrawal->currency]
            );

         $balanceBefore = (float) $wallet->balance;
         $wallet->balance += $withdrawal->amount;
         $wallet->save();

         // Update withdrawal status
         $withdrawal->status = 'rejected';
         $withdrawal->approved_by = $admin->id;
         $withdrawal->admin_note = $request->admin_note;
         $withdrawal->processed_at = now();
         $withdrawal->save();

         // Create wallet transaction for refund
         WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'refund',
            'amount' => $withdrawal->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balance,
            'reference_type' => Withdrawal::class,
            'reference_id' => $withdrawal->id,
            'description' => 'Withdrawal rejected - Amount refunded',
            'metadata' => [
               'withdrawal_id' => $withdrawal->id,
               'admin_note' => $request->admin_note,
               'rejected_by' => $admin->name,
            ],
         ]);

         DB::commit();



         return back()->with('success', 'Withdrawal rejected and amount refunded to user wallet.');
      } catch (\Exception $e) {
         DB::rollBack();

         Log::error("Withdrawal rejection failed", [
            'withdrawal_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to reject withdrawal. Please try again.'
         ], 500);
      }
   }

   /**
    * Mark withdrawal as processing
    */
   public function processing($id)
   {
      try {
         $withdrawal = Withdrawal::findOrFail($id);

         if ($withdrawal->status !== 'pending') {
            return response()->json([
               'success' => false,
               'message' => 'Withdrawal is not in pending status.'
            ], 400);
         }

         $withdrawal->status = 'processing';
         $withdrawal->save();

         return back()->with('success', 'Withdrawal marked as processing.');
      } catch (\Exception $e) {
         Log::error("Failed to mark withdrawal as processing", [
            'withdrawal_id' => $id,
            'error' => $e->getMessage()
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to update withdrawal status.'
         ], 500);
      }
   }
}
