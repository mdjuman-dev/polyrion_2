<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        // Ensure all 3 levels exist
        $levels = [1, 2, 3];
        foreach ($levels as $level) {
            if (!$settings->where('level', $level)->first()) {
                ReferralSetting::create([
                    'level' => $level,
                    'commission_percent' => $level === 1 ? 10.00 : ($level === 2 ? 5.00 : 2.00),
                    'is_active' => true,
                ]);
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
            // Update Level 1
            $level1 = ReferralSetting::where('level', 1)->firstOrFail();
            $level1->commission_percent = $request->level_1_percent;
            $level1->is_active = $request->has('level_1_active') ? true : false;
            $level1->save();

            // Update Level 2
            $level2 = ReferralSetting::where('level', 2)->firstOrFail();
            $level2->commission_percent = $request->level_2_percent;
            $level2->is_active = $request->has('level_2_active') ? true : false;
            $level2->save();

            // Update Level 3
            $level3 = ReferralSetting::where('level', 3)->firstOrFail();
            $level3->commission_percent = $request->level_3_percent;
            $level3->is_active = $request->has('level_3_active') ? true : false;
            $level3->save();

            DB::commit();

            // Clear referral settings cache
            $referralService = new ReferralService();
            $referralService->clearCache();

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

