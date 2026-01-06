<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKycVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_type',
        'nid_front_photo',
        'nid_back_photo',
        'license_number',
        'full_name',
        'dob',
        'license_front_photo',
        'passport_number',
        'passport_expiry_date',
        'passport_biodata_photo',
        'passport_cover_photo',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
