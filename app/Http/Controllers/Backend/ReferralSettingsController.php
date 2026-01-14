<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReferralSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:manage global settings,admin');
    }

    /**
     * Display referral settings page
     */
    public function index()
    {
        $settings = ReferralSetting::orderBy('level')->get();
        
        // Ensure all 3 levels exist with trade_volume commission type
        $levels = [1, 2, 3];
        $hasCommissionTypeColumn = Schema::hasColumn('referral_settings', 'commission_type');
        
        foreach ($levels as $level) {
            $existing = $settings->where('level', $level)->first();
            if (!$existing) {
                $data = [
                    'level' => $level,
                    'commission_percent' => $level === 1 ? 5.00 : ($level === 2 ? 3.00 : 1.00),
                    'is_active' => true,
                ];
                
                if ($hasCommissionTypeColumn) {
                    $data['commission_type'] = 'trade_volume';
                }
                
                ReferralSetting::create($data);
            } elseif ($hasCommissionTypeColumn && !$existing->commission_type) {
                // Update existing settings without commission_type
                $existing->commission_type = 'trade_volume';
                $existing->save();
            }
        }

        // Refresh settings
        $settings = ReferralSetting::orderBy('level')->get();

        return view('backend.referral-settings.index', compact('settings'));
    }

    /**
     * Update referral settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'level_1_percent' => 'required|numeric|min:0|max:100',
            'level_2_percent' => 'required|numeric|min:0|max:100',
            'level_3_percent' => 'required|numeric|min:0|max:100',
            'level_1_active' => 'nullable|boolean',
            'level_2_active' => 'nullable|boolean',
            'level_3_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $hasCommissionTypeColumn = \Schema::hasColumn('referral_settings', 'commission_type');
            
            // Update Level 1
            $level1Query = ReferralSetting::where('level', 1);
            if ($hasCommissionTypeColumn) {
                $level1Query->where('commission_type', 'trade_volume');
            }
            $level1 = $level1Query->firstOrFail();
            $level1->commission_percent = $request->level_1_percent;
            $level1->is_active = $request->has('level_1_active') ? true : false;
            if ($hasCommissionTypeColumn) {
                $level1->commission_type = 'trade_volume';
            }
            $level1->save();

            // Update Level 2
            $level2Query = ReferralSetting::where('level', 2);
            if ($hasCommissionTypeColumn) {
                $level2Query->where('commission_type', 'trade_volume');
            }
            $level2 = $level2Query->firstOrFail();
            $level2->commission_percent = $request->level_2_percent;
            $level2->is_active = $request->has('level_2_active') ? true : false;
            if ($hasCommissionTypeColumn) {
                $level2->commission_type = 'trade_volume';
            }
            $level2->save();

            // Update Level 3
            $level3Query = ReferralSetting::where('level', 3);
            if ($hasCommissionTypeColumn) {
                $level3Query->where('commission_type', 'trade_volume');
            }
            $level3 = $level3Query->firstOrFail();
            $level3->commission_percent = $request->level_3_percent;
            $level3->is_active = $request->has('level_3_active') ? true : false;
            if ($hasCommissionTypeColumn) {
                $level3->commission_type = 'trade_volume';
            }
            $level3->save();

            DB::commit();

            // Clear referral settings cache (both deposit and trade commission caches)
            $referralService = new ReferralService();
            $referralService->clearCache();
            // Also clear trade commission cache
            \Illuminate\Support\Facades\Cache::forget('referral_settings_trade_volume');
            // Clear cache using job's method
            \App\Jobs\ProcessTradeCommission::clearCache();

            Log::info('Referral settings updated by admin', [
                'admin_id' => auth('admin')->id(),
                'level_1' => $level1->commission_percent,
                'level_2' => $level2->commission_percent,
                'level_3' => $level3->commission_percent,
            ]);

            return redirect()->route('admin.referral-settings.index')
                ->with('success', 'Referral settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update referral settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.referral-settings.index')
                ->with('error', 'Failed to update referral settings: ' . $e->getMessage());
        }
    }
}

