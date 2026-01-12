@extends('backend.layouts.master')
@section('title', 'KYC Verifications')
@section('content')
<div class="content-wrapper">
   <div class="container-full">
      <section class="content">
         <div class="row">
            <div class="col-12">
               <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                  <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none; border-radius: 16px 16px 0 0;">
                     <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                           <i class="fa fa-id-card me-2"></i> KYC Verification Management
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

               <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                  <div class="box-body" style="padding: 25px;">
                     <div class="table-responsive">
                        <table id="kycTable" class="table table-bordered table-hover" style="width:100%">
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
                                 <td>—</td>
                                 <td>—</td>
                                 <td>—</td>
                                 <td>—</td>
                                 <td>—</td>
                                 <td>—</td>
                              </tr>
                              @endforelse
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
</div>

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    /* DataTables Custom Styling */
    .dataTables_wrapper {
        padding: 0;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }
    
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 600;
        color: #374151;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .dataTables_wrapper .dataTables_info {
        color: #6b7280;
        font-weight: 500;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        color: #374151 !important;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #fff !important;
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #fff !important;
        border-color: #667eea;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .dataTables_wrapper table.dataTable thead th {
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }
    
    .dataTables_wrapper table.dataTable tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .dataTables_wrapper table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }
    
    /* Remove sorting indicators from first and last columns */
    .dataTables_wrapper table.dataTable thead th:first-child.sorting::before,
    .dataTables_wrapper table.dataTable thead th:first-child.sorting::after,
    .dataTables_wrapper table.dataTable thead th:last-child.sorting::before,
    .dataTables_wrapper table.dataTable thead th:last-child.sorting::after {
        display: none !important;
    }
    
    /* Remove red border/box from first and last columns */
    .dataTables_wrapper table.dataTable tbody td:first-child,
    .dataTables_wrapper table.dataTable thead th:first-child {
        border-left: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
    
    .dataTables_wrapper table.dataTable tbody td:last-child,
    .dataTables_wrapper table.dataTable thead th:last-child {
        border-right: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
   $(document).ready(function() {
      // Initialize DataTables for KYC Table
      if ($.fn.DataTable.isDataTable('#kycTable')) {
         $('#kycTable').DataTable().destroy();
      }
      
      // Remove empty row if it exists (DataTables will handle empty state)
      $('#kycTable tbody tr').each(function() {
         var $row = $(this);
         var isEmpty = true;
         $row.find('td').each(function() {
            if ($(this).text().trim() !== '—') {
               isEmpty = false;
               return false;
            }
         });
         if (isEmpty) {
            $row.remove();
         }
      });
      
      $('#kycTable').DataTable({
         responsive: true,
         pageLength: 25,
         lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
         order: [[4, 'desc']], // Sort by date (submitted date column)
         columnDefs: [
            { orderable: false, targets: [0, 5] }, // Disable sorting on ID and Actions columns
            { responsivePriority: 1, targets: 1 }, // User column priority
            { responsivePriority: 2, targets: 4 }, // Date column priority
         ],
         language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            emptyTable: "<div style='padding: 40px; text-align: center;'><i class='fa fa-id-card fa-3x text-muted mb-3'></i><p class='text-muted mb-0'>No KYC verifications found.</p></div>",
            paginate: {
               first: "First",
               last: "Last",
               next: "Next",
               previous: "Previous"
            }
         },
         dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
      });
   });
</script>
@endpush

@endsection

