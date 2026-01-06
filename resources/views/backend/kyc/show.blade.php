@extends('backend.layouts.master')
@section('title', 'KYC Verification Details')
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
                           <i class="fa fa-id-card"></i> KYC Verification Details
                        </h4>
                        <a href="{{ route('admin.kyc.index') }}" class="btn btn-secondary">
                           <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="box">
                        <div class="box-header">
                           <h4 class="box-title">User Information</h4>
                        </div>
                        <div class="box-body">
                           <table class="table table-bordered">
                              <tr>
                                 <th width="40%">Name</th>
                                 <td>{{ $kycVerification->user->name }}</td>
                              </tr>
                              <tr>
                                 <th>Email</th>
                                 <td>{{ $kycVerification->user->email }}</td>
                              </tr>
                              <tr>
                                 <th>ID Type</th>
                                 <td>{{ $kycVerification->id_type }}</td>
                              </tr>
                              <tr>
                                 <th>Status</th>
                                 <td>
                                    @if ($kycVerification->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($kycVerification->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($kycVerification->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                 </td>
                              </tr>
                              <tr>
                                 <th>Submitted Date</th>
                                 <td>{{ $kycVerification->created_at->format('M d, Y H:i') }}</td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="box">
                        <div class="box-header">
                           <h4 class="box-title">Verification Details</h4>
                        </div>
                        <div class="box-body">
                           <table class="table table-bordered">
                              @if($kycVerification->id_type === 'NID')
                                 @if($kycVerification->nid_front_photo || $kycVerification->nid_back_photo)
                                    <tr>
                                       <th colspan="2">Photos</th>
                                    </tr>
                                    <tr>
                                       <td colspan="2">
                                          <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                             @if($kycVerification->nid_front_photo)
                                                <div>
                                                   <p style="font-size: 0.85rem; margin-bottom: 0.5rem;">Front Photo</p>
                                                   <img src="{{ asset('storage/' . $kycVerification->nid_front_photo) }}" 
                                                      alt="NID Front" 
                                                      style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                                </div>
                                             @endif
                                             @if($kycVerification->nid_back_photo)
                                                <div>
                                                   <p style="font-size: 0.85rem; margin-bottom: 0.5rem;">Back Photo</p>
                                                   <img src="{{ asset('storage/' . $kycVerification->nid_back_photo) }}" 
                                                      alt="NID Back" 
                                                      style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                                </div>
                                             @endif
                                          </div>
                                       </td>
                                    </tr>
                                 @endif
                              @elseif($kycVerification->id_type === 'Driving License')
                                 @if($kycVerification->license_number)
                                    <tr>
                                       <th width="40%">License Number</th>
                                       <td>{{ $kycVerification->license_number }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->full_name)
                                    <tr>
                                       <th>Full Name</th>
                                       <td>{{ $kycVerification->full_name }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->dob)
                                    <tr>
                                       <th>Date of Birth</th>
                                       <td>{{ $kycVerification->dob->format('d M, Y') }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->license_front_photo)
                                    <tr>
                                       <th colspan="2">Front Page Photo</th>
                                    </tr>
                                    <tr>
                                       <td colspan="2">
                                          <img src="{{ asset('storage/' . $kycVerification->license_front_photo) }}" 
                                             alt="License Front" 
                                             style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                       </td>
                                    </tr>
                                 @endif
                              @elseif($kycVerification->id_type === 'Passport')
                                 @if($kycVerification->passport_number)
                                    <tr>
                                       <th width="40%">Passport Number</th>
                                       <td>{{ $kycVerification->passport_number }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->full_name)
                                    <tr>
                                       <th>Full Name</th>
                                       <td>{{ $kycVerification->full_name }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->dob)
                                    <tr>
                                       <th>Date of Birth</th>
                                       <td>{{ $kycVerification->dob->format('d M, Y') }}</td>
                                    </tr>
                                 @endif
                                 @if($kycVerification->passport_biodata_photo)
                                    <tr>
                                       <th colspan="2">Biodata Page Photo</th>
                                    </tr>
                                    <tr>
                                       <td colspan="2">
                                          <img src="{{ asset('storage/' . $kycVerification->passport_biodata_photo) }}" 
                                             alt="Passport Biodata" 
                                             style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                       </td>
                                    </tr>
                                 @endif
                              @endif
                           </table>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="box">
                  <div class="box-body">
                     <div class="d-flex gap-2">
                        <a href="{{ route('admin.kyc.edit', $kycVerification->id) }}" class="btn btn-primary">
                           <i class="fa fa-edit"></i> Edit
                        </a>
                        @if ($kycVerification->status == 'pending')
                        <a href="{{ route('admin.kyc.approve', $kycVerification->id) }}" class="btn btn-success"
                           onclick="return confirm('Are you sure you want to approve this KYC verification?')">
                           <i class="fa fa-check"></i> Approve
                        </a>
                        <a href="{{ route('admin.kyc.reject', $kycVerification->id) }}" class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to reject this KYC verification?')">
                           <i class="fa fa-times"></i> Reject
                        </a>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
</div>
@endsection

