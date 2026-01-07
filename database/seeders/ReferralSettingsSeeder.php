<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if settings already exist
        $existingSettings = DB::table('referral_settings')->count();
        
        if ($existingSettings > 0) {
            // Update existing settings to add commission_type if missing
            DB::table('referral_settings')
                ->whereNull('commission_type')
                ->update(['commission_type' => 'trade_volume']);
            
            // Insert trade_volume settings if they don't exist
            $existingTradeVolume = DB::table('referral_settings')
                ->where('commission_type', 'trade_volume')
                ->count();
            
            if ($existingTradeVolume === 0) {
                $tradeSettings = [
                    [
                        'level' => 1,
                        'commission_percent' => 1.00,
                        'commission_type' => 'trade_volume',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'level' => 2,
                        'commission_percent' => 0.50,
                        'commission_type' => 'trade_volume',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'level' => 3,
                        'commission_percent' => 0.10,
                        'commission_type' => 'trade_volume',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];
                
                DB::table('referral_settings')->insert($tradeSettings);
            }
        } else {
            // Insert both deposit and trade settings for new installations
            $settings = [
                // Deposit-based commissions (original)
                [
                    'level' => 1,
                    'commission_percent' => 10.00,
                    'commission_type' => 'trade_volume', // Will be updated to deposit later if needed
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 2,
                    'commission_percent' => 5.00,
                    'commission_type' => 'trade_volume',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 3,
                    'commission_percent' => 2.00,
                    'commission_type' => 'trade_volume',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Trade-based commissions
                [
                    'level' => 1,
                    'commission_percent' => 1.00,
                    'commission_type' => 'trade_volume',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 2,
                    'commission_percent' => 0.50,
                    'commission_type' => 'trade_volume',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'level' => 3,
                    'commission_percent' => 0.10,
                    'commission_type' => 'trade_volume',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('referral_settings')->insert($settings);
        }
    }
}
