<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordChangeOtp extends Model
{
    /** @use HasFactory<\Database\Factories\PasswordChangeOtpFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check if OTP is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Generate a 6-digit OTP
     */
    public static function generateOtp(): string
    {
        return str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for user
     */
    public static function createForUser($userId): self
    {
        // Invalidate all previous OTPs for this user
        self::where('user_id', $userId)
            ->where('used', false)
            ->update(['used' => true]);

        return self::create([
            'user_id' => $userId,
            'otp' => self::generateOtp(),
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false,
        ]);
    }

    /**
     * Verify OTP for user
     */
    public static function verifyOtp($userId, $otp): ?self
    {
        $otpRecord = self::where('user_id', $userId)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $otpRecord && $otpRecord->isValid() ? $otpRecord : null;
    }
}
