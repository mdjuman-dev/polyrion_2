<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserKycVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class KycVerificationController extends Controller
{
    public function __construct()
    {
        // Permission checks are handled in routes
    }

    public function index(Request $request)
    {
        $query = UserKycVerification::with('user');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                   ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $kycVerifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => UserKycVerification::where('status', 'pending')->count(),
            'approved' => UserKycVerification::where('status', 'approved')->count(),
            'rejected' => UserKycVerification::where('status', 'rejected')->count(),
            'total' => UserKycVerification::count(),
        ];

        return view('backend.kyc.index', compact('kycVerifications', 'stats'));
    }

    public function show($id)
    {
        $kycVerification = UserKycVerification::with('user')->findOrFail($id);
        return view('backend.kyc.show', compact('kycVerification'));
    }

    public function edit($id)
    {
        $kycVerification = UserKycVerification::with('user')->findOrFail($id);
        return view('backend.kyc.edit', compact('kycVerification'));
    }

    public function update(Request $request, $id)
    {
        $kycVerification = UserKycVerification::findOrFail($id);

        $rules = [
            'status' => 'required|in:pending,approved,rejected',
        ];

        if ($kycVerification->id_type === 'Driving License') {
            $rules['license_number'] = 'nullable|string|max:255';
            $rules['full_name'] = 'nullable|string|max:255';
            $rules['dob'] = 'nullable|date';
            $rules['license_front_photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
        } elseif ($kycVerification->id_type === 'Passport') {
            $rules['passport_number'] = 'nullable|string|max:255';
            $rules['full_name'] = 'nullable|string|max:255';
            $rules['dob'] = 'nullable|date';
            $rules['passport_biodata_photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
        } elseif ($kycVerification->id_type === 'NID') {
            $rules['nid_front_photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
            $rules['nid_back_photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
        }

        $validated = $request->validate($rules);

        try {
            $idVerificationPath = storage_path('app/public/id_verifications');
            if (!File::exists($idVerificationPath)) {
                File::makeDirectory($idVerificationPath, 0755, true);
            }

            if ($kycVerification->id_type === 'NID') {
                if ($request->hasFile('nid_front_photo')) {
                    if ($kycVerification->nid_front_photo) {
                        Storage::disk('public')->delete($kycVerification->nid_front_photo);
                    }
                    $frontPhoto = $request->file('nid_front_photo');
                    $frontName = time() . '_' . uniqid() . '_front.' . $frontPhoto->getClientOriginalExtension();
                    $validated['nid_front_photo'] = $frontPhoto->storeAs('id_verifications', $frontName, 'public');
                }
                if ($request->hasFile('nid_back_photo')) {
                    if ($kycVerification->nid_back_photo) {
                        Storage::disk('public')->delete($kycVerification->nid_back_photo);
                    }
                    $backPhoto = $request->file('nid_back_photo');
                    $backName = time() . '_' . uniqid() . '_back.' . $backPhoto->getClientOriginalExtension();
                    $validated['nid_back_photo'] = $backPhoto->storeAs('id_verifications', $backName, 'public');
                }
            } elseif ($kycVerification->id_type === 'Driving License') {
                if ($request->has('license_number')) {
                    $validated['license_number'] = $request->license_number;
                }
                if ($request->has('full_name')) {
                    $validated['full_name'] = $request->full_name;
                }
                if ($request->has('dob')) {
                    $validated['dob'] = $request->dob;
                }
                if ($request->hasFile('license_front_photo')) {
                    if ($kycVerification->license_front_photo) {
                        Storage::disk('public')->delete($kycVerification->license_front_photo);
                    }
                    $frontPhoto = $request->file('license_front_photo');
                    $frontName = time() . '_' . uniqid() . '_dl_front.' . $frontPhoto->getClientOriginalExtension();
                    $validated['license_front_photo'] = $frontPhoto->storeAs('id_verifications', $frontName, 'public');
                }
            } elseif ($kycVerification->id_type === 'Passport') {
                if ($request->has('passport_number')) {
                    $validated['passport_number'] = $request->passport_number;
                }
                if ($request->has('full_name')) {
                    $validated['full_name'] = $request->full_name;
                }
                if ($request->has('dob')) {
                    $validated['dob'] = $request->dob;
                }
                if ($request->hasFile('passport_biodata_photo')) {
                    if ($kycVerification->passport_biodata_photo) {
                        Storage::disk('public')->delete($kycVerification->passport_biodata_photo);
                    }
                    $biodataPhoto = $request->file('passport_biodata_photo');
                    $biodataName = time() . '_' . uniqid() . '_passport_biodata.' . $biodataPhoto->getClientOriginalExtension();
                    $validated['passport_biodata_photo'] = $biodataPhoto->storeAs('id_verifications', $biodataName, 'public');
                }
            }

            $kycVerification->update($validated);

            if ($validated['status'] === 'approved') {
                $kycVerification->user->update(['email_verified_at' => now()]);
            }

            return redirect()->route('admin.kyc.index')->with('success', 'KYC verification updated successfully.');
        } catch (\Exception $e) {
            \Log::error('KYC update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating KYC verification: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $kycVerification = UserKycVerification::findOrFail($id);
        $kycVerification->update(['status' => 'approved']);
        $kycVerification->user->update(['email_verified_at' => now()]);
        return redirect()->back()->with('success', 'KYC verification approved.');
    }

    public function reject($id)
    {
        $kycVerification = UserKycVerification::findOrFail($id);
        $kycVerification->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'KYC verification rejected.');
    }
}
