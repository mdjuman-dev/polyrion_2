@extends('backend.layouts.master')
@section('title', 'Edit KYC Verification')
@section('content')
<div class="content-wrapper">
   <div class="container-full">
      <section class="content">
         <div class="row">
            <div class="col-12">
               <div class="box mb-3">
                  <div class="box-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                           <i class="fa fa-edit"></i> Edit KYC Verification
                        </h4>
                        <a href="{{ route('admin.kyc.show', $kycVerification->id) }}" class="btn btn-secondary">
                           <i class="fa fa-arrow-left"></i> Back
                        </a>
                     </div>
                  </div>
               </div>

               @if(session('success'))
               <div class="alert alert-success alert-dismissible fade show">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
               @endif

               @if(session('error'))
               <div class="alert alert-danger alert-dismissible fade show">
                  {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
               @endif

               <form action="{{ route('admin.kyc.update', $kycVerification->id) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="box">
                     <div class="box-header">
                        <h4 class="box-title">Status</h4>
                     </div>
                     <div class="box-body">
                        <div class="form-group">
                           <label>Status <span class="text-danger">*</span></label>
                           <select name="status" class="form-control" required>
                              <option value="pending" {{ $kycVerification->status == 'pending' ? 'selected' : '' }}>Pending</option>
                              <option value="approved" {{ $kycVerification->status == 'approved' ? 'selected' : '' }}>Approved</option>
                              <option value="rejected" {{ $kycVerification->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  @if($kycVerification->id_type === 'NID')
                     <div class="box">
                        <div class="box-header">
                           <h4 class="box-title">NID Photos</h4>
                        </div>
                        <div class="box-body">
                           @if($kycVerification->nid_front_photo)
                              <div class="mb-3">
                                 <label>Current Front Photo</label>
                                 <div>
                                    <img src="{{ asset('storage/' . $kycVerification->nid_front_photo) }}" 
                                       alt="NID Front" 
                                       style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 0.5rem;">
                                 </div>
                              </div>
                           @endif
                           <div class="form-group">
                              <label>Update Front Photo</label>
                              <input type="file" name="nid_front_photo" class="form-control" accept="image/jpeg,image/png,jpg">
                           </div>

                           @if($kycVerification->nid_back_photo)
                              <div class="mb-3">
                                 <label>Current Back Photo</label>
                                 <div>
                                    <img src="{{ asset('storage/' . $kycVerification->nid_back_photo) }}" 
                                       alt="NID Back" 
                                       style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 0.5rem;">
                                 </div>
                              </div>
                           @endif
                           <div class="form-group">
                              <label>Update Back Photo</label>
                              <input type="file" name="nid_back_photo" class="form-control" accept="image/jpeg,image/png,jpg">
                           </div>
                        </div>
                     </div>
                  @elseif($kycVerification->id_type === 'Driving License')
                     <div class="box">
                        <div class="box-header">
                           <h4 class="box-title">Driving License Details</h4>
                        </div>
                        <div class="box-body">
                           <div class="form-group">
                              <label>License Number</label>
                              <input type="text" name="license_number" class="form-control" value="{{ $kycVerification->license_number }}">
                           </div>
                           <div class="form-group">
                              <label>Full Name</label>
                              <input type="text" name="full_name" class="form-control" value="{{ $kycVerification->full_name }}">
                           </div>
                           <div class="form-group">
                              <label>Date of Birth</label>
                              <input type="date" name="dob" class="form-control" value="{{ $kycVerification->dob ? $kycVerification->dob->format('Y-m-d') : '' }}">
                           </div>
                           @if($kycVerification->license_front_photo)
                              <div class="mb-3">
                                 <label>Current Front Photo</label>
                                 <div>
                                    <img src="{{ asset('storage/' . $kycVerification->license_front_photo) }}" 
                                       alt="License Front" 
                                       style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 0.5rem;">
                                 </div>
                              </div>
                           @endif
                           <div class="form-group">
                              <label>Update Front Page Photo</label>
                              <input type="file" name="license_front_photo" class="form-control" accept="image/jpeg,image/png,jpg">
                           </div>
                        </div>
                     </div>
                  @elseif($kycVerification->id_type === 'Passport')
                     <div class="box">
                        <div class="box-header">
                           <h4 class="box-title">Passport Details</h4>
                        </div>
                        <div class="box-body">
                           <div class="form-group">
                              <label>Passport Number</label>
                              <input type="text" name="passport_number" class="form-control" value="{{ $kycVerification->passport_number }}">
                           </div>
                           <div class="form-group">
                              <label>Full Name</label>
                              <input type="text" name="full_name" class="form-control" value="{{ $kycVerification->full_name }}">
                           </div>
                           <div class="form-group">
                              <label>Date of Birth</label>
                              <input type="date" name="dob" class="form-control" value="{{ $kycVerification->dob ? $kycVerification->dob->format('Y-m-d') : '' }}">
                           </div>
                           @if($kycVerification->passport_biodata_photo)
                              <div class="mb-3">
                                 <label>Current Biodata Photo</label>
                                 <div>
                                    <img src="{{ asset('storage/' . $kycVerification->passport_biodata_photo) }}" 
                                       alt="Passport Biodata" 
                                       style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 0.5rem;">
                                 </div>
                              </div>
                           @endif
                           <div class="form-group">
                              <label>Update Biodata Page Photo</label>
                              <input type="file" name="passport_biodata_photo" class="form-control" accept="image/jpeg,image/png,jpg">
                           </div>
                        </div>
                     </div>
                  @endif

                  <div class="box">
                     <div class="box-body">
                        <button type="submit" class="btn btn-primary">
                           <i class="fa fa-save"></i> Update KYC Verification
                        </button>
                        <a href="{{ route('admin.kyc.show', $kycVerification->id) }}" class="btn btn-secondary">
                           Cancel
                        </a>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </section>
   </div>
</div>
@endsection

