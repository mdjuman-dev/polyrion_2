@extends('backend.layouts.master')
@section('title', 'KYC Verifications')
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
                           <i class="fa fa-id-card"></i> KYC Verification Management
                        </h4>
                     </div>
                  </div>
               </div>

               <div class="row mb-3">
                  <div class="col-lg-3 col-md-6">
                     <div class="box">
                        <div class="box-body">
                           <div class="d-flex align-items-center">
                              <div class="flex-shrink-0">
                                 <div class="bg-warning rounded-circle p-3">
                                    <i class="fa fa-clock fa-2x text-white"></i>
                                 </div>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                 <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                                 <p class="mb-0 text-muted">Pending</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                     <div class="box">
                        <div class="box-body">
                           <div class="d-flex align-items-center">
                              <div class="flex-shrink-0">
                                 <div class="bg-success rounded-circle p-3">
                                    <i class="fa fa-check-circle fa-2x text-white"></i>
                                 </div>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                 <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                                 <p class="mb-0 text-muted">Approved</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                     <div class="box">
                        <div class="box-body">
                           <div class="d-flex align-items-center">
                              <div class="flex-shrink-0">
                                 <div class="bg-danger rounded-circle p-3">
                                    <i class="fa fa-times-circle fa-2x text-white"></i>
                                 </div>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                 <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                                 <p class="mb-0 text-muted">Rejected</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                     <div class="box">
                        <div class="box-body">
                           <div class="d-flex align-items-center">
                              <div class="flex-shrink-0">
                                 <div class="bg-info rounded-circle p-3">
                                    <i class="fa fa-list fa-2x text-white"></i>
                                 </div>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                 <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                 <p class="mb-0 text-muted">Total</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="box mb-3">
                  <div class="box-body">
                     <form method="GET" action="{{ route('admin.kyc.index') }}" class="row g-3">
                        <div class="col-md-4">
                           <label class="form-label">Search</label>
                           <input type="text" name="search" class="form-control"
                              placeholder="Search by user email or name" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Status</label>
                           <select name="status" class="form-control">
                              <option value="">All Status</option>
                              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                           </select>
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">&nbsp;</label>
                           <div>
                              <button type="submit" class="btn btn-primary">
                                 <i class="fa fa-search"></i> Search
                              </button>
                              @if (request('search') || request('status'))
                              <a href="{{ route('admin.kyc.index') }}" class="btn btn-secondary">
                                 <i class="fa fa-refresh"></i> Reset
                              </a>
                              @endif
                           </div>
                        </div>
                     </form>
                  </div>
               </div>

               <div class="box">
                  <div class="box-body">
                     <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                           <thead>
                              <tr>
                                 <th>ID</th>
                                 <th>User</th>
                                 <th>ID Type</th>
                                 <th>Status</th>
                                 <th>Submitted Date</th>
                                 <th>Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              @forelse($kycVerifications as $kyc)
                              <tr>
                                 <td>#{{ $kyc->id }}</td>
                                 <td>
                                    <div>
                                       <strong>{{ $kyc->user->name }}</strong><br>
                                       <small class="text-muted">{{ $kyc->user->email }}</small>
                                    </div>
                                 </td>
                                 <td>{{ $kyc->id_type }}</td>
                                 <td>
                                    @if ($kyc->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($kyc->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($kyc->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                 </td>
                                 <td>{{ $kyc->created_at->format('M d, Y H:i') }}</td>
                                 <td>
                                    <div class="btn-group gap-1" role="group">
                                       <a href="{{ route('admin.kyc.show', $kyc->id) }}"
                                          class="btn btn-sm btn-outline-info" title="View">
                                          <i class="fa fa-eye"></i>
                                       </a>
                                       <a href="{{ route('admin.kyc.edit', $kyc->id) }}"
                                          class="btn btn-sm btn-outline-primary" title="Edit">
                                          <i class="fa fa-edit"></i>
                                       </a>
                                       @if ($kyc->status == 'pending')
                                       <a href="{{ route('admin.kyc.approve', $kyc->id) }}"
                                          class="btn btn-sm btn-success" title="Approve"
                                          onclick="return confirm('Are you sure you want to approve this KYC verification?')">
                                          <i class="fa fa-check"></i>
                                       </a>
                                       <a href="{{ route('admin.kyc.reject', $kyc->id) }}"
                                          class="btn btn-sm btn-danger" title="Reject"
                                          onclick="return confirm('Are you sure you want to reject this KYC verification?')">
                                          <i class="fa fa-times"></i>
                                       </a>
                                       @endif
                                    </div>
                                 </td>
                              </tr>
                              @empty
                              <tr>
                                 <td colspan="6" class="text-center">No KYC verifications found.</td>
                              </tr>
                              @endforelse
                           </tbody>
                        </table>
                     </div>
                     <div class="mt-3">
                        {{ $kycVerifications->links() }}
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
</div>
@endsection

