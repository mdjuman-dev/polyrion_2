@extends('backend.layouts.master')
@section('title', 'All Withdrawals')
@section('content')
<div class="content-wrapper">
   <div class="container-full">
      <section class="content">
         <div class="row">
            <div class="col-12">
               <!-- Header -->
               <div class="box mb-3">
                  <div class="box-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                           <i class="fa fa-money-bill-wave"></i> Withdrawal Management
                        </h4>
                     </div>
                  </div>
               </div>

               <!-- Statistics Cards -->
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
                                 <div class="bg-info rounded-circle p-3">
                                    <i class="fa fa-spinner fa-2x text-white"></i>
                                 </div>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                 <h3 class="mb-0">{{ $stats['processing'] }}</h3>
                                 <p class="mb-0 text-muted">Processing</p>
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
                                 <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                                 <p class="mb-0 text-muted">Completed</p>
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
               </div>

               <!-- Search and Filter -->
               <div class="box mb-3">
                  <div class="box-body">
                     <form method="GET" action="{{ route('admin.withdrawal.index') }}" class="row g-3">
                        <div class="col-md-4">
                           <label class="form-label">Search</label>
                           <input type="text" name="search" class="form-control"
                              placeholder="Search by user email or name" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Status</label>
                           <select name="status" class="form-control">
                              <option value="">All Status</option>
                              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                 Pending</option>
                              <option value="processing"
                                 {{ request('status') == 'processing' ? 'selected' : '' }}>Processing
                              </option>
                              <option value="completed"
                                 {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                              <option value="rejected"
                                 {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                           </select>
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">&nbsp;</label>
                           <div>
                              <button type="submit" class="btn btn-primary">
                                 <i class="fa fa-search"></i> Search
                              </button>
                              @if (request('search') || request('status'))
                              <a href="{{ route('admin.withdrawal.index') }}" class="btn btn-secondary">
                                 <i class="fa fa-refresh"></i> Reset
                              </a>
                              @endif
                           </div>
                        </div>
                     </form>
                  </div>
               </div>

               <!-- Withdrawals Table -->
               <div class="box">
                  <div class="box-body">
                     <div class="table-responsive">
                        <table id="withdrawalsTable" class="table table-bordered table-hover" style="width:100%">
                           <thead>
                              <tr>
                                 <th>ID</th>
                                 <th>User</th>
                                 <th>Amount</th>
                                 <th>Method</th>
                                 <th>Status</th>
                                 <th>Date</th>
                                 <th>Approved By</th>
                                 <th>Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              @forelse($withdrawals as $withdrawal)
                              <tr>
                                 <td>#{{ $withdrawal->id }}</td>
                                 <td>
                                    <div>
                                       <strong>{{ $withdrawal->user->name }}</strong><br>
                                       <small
                                          class="text-muted">{{ $withdrawal->user->email }}</small>
                                    </div>
                                 </td>
                                 <td>
                                    <strong>{{ number_format($withdrawal->amount, 2) }}
                                       {{ $withdrawal->currency }}</strong>
                                 </td>
                                 <td>{{ ucfirst($withdrawal->payment_method) }}</td>
                                 <td>
                                    @if ($withdrawal->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($withdrawal->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                    @elseif($withdrawal->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                    @elseif($withdrawal->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                 </td>
                                 <td>
                                    <div>
                                       <strong>{{ $withdrawal->created_at->format('M d, Y H:i') }}</strong>
                                       @if ($withdrawal->processed_at)
                                       <br><small class="text-muted">Processed:
                                          {{ $withdrawal->processed_at->format('M d, Y H:i') }}</small>
                                       @endif
                                    </div>
                                 </td>
                                 <td>
                                    @if ($withdrawal->approver)
                                    <div>
                                       <strong>{{ $withdrawal->approver->name }}</strong>
                                       @if ($withdrawal->admin_note)
                                       <br><small class="text-muted"
                                          title="{{ $withdrawal->admin_note }}">
                                          <i class="fa fa-comment"></i> Note added
                                       </small>
                                       @endif
                                    </div>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                 </td>
                                 <td>
                                    <div class="btn-group gap-1" role="group">
                                       <a href="{{ route('admin.withdrawal.show', $withdrawal->id) }}"
                                          class="btn btn-sm btn-outline-info"
                                          title="View Details">
                                          <i class="fa fa-eye"></i>
                                       </a>

                                       @if ($withdrawal->status == 'pending')
                                       <a href="{{ route('admin.withdrawal.approve', $withdrawal->id) }}"
                                          class="btn btn-sm btn-success approve-btn"
                                          data-id="{{ $withdrawal->id }}"
                                          title="Approve">
                                          <i class="fa fa-check"></i>
                                       </a>

                                       <a href="{{ route('admin.withdrawal.reject', $withdrawal->id) }}"
                                          class="btn btn-sm btn-danger reject-btn"
                                          data-id="{{ $withdrawal->id }}"
                                          title="Reject">
                                          <i class="fa fa-times"></i>
                                       </a>

                                       <a href="{{ route('admin.withdrawal.processing', $withdrawal->id) }}"
                                          class="btn btn-sm btn-warning processing-btn"
                                          data-id="{{ $withdrawal->id }}"
                                          title="Mark as Processing">
                                          <i class="fa fa-spinner"></i>
                                       </a>

                                       @elseif ($withdrawal->status == 'completed')
                                       <span class="badge bg-success py-2 px-3">
                                          <i class="fa fa-check-circle"></i> Approved
                                       </span>

                                       @elseif ($withdrawal->status == 'rejected')
                                       <span class="badge bg-danger py-2 px-3">
                                          <i class="fa fa-times-circle"></i> Rejected
                                       </span>

                                       @elseif ($withdrawal->status == 'processing')
                                       <span class="badge bg-info py-2 px-3 text-white">
                                          <i class="fa fa-spinner fa-spin"></i> Processing
                                       </span>

                                       <a href="{{ route('admin.withdrawal.approve', $withdrawal->id) }}"
                                          class="btn btn-sm btn-success ms-1" title="Approve">
                                          <i class="fa fa-check"></i>
                                       </a>
                                       @endif
                                    </div>
                                 </td>
                              </tr>
                              @empty
                              <tr>
                                 <td colspan="8" class="text-center py-4">
                                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No withdrawals found.</p>
                                 </td>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Approve Withdrawal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <form id="approveForm">
            <div class="modal-body">
               <input type="hidden" id="approve_withdrawal_id" name="withdrawal_id">
               <div class="mb-3">
                  <label class="form-label">Admin Note (Optional)</label>
                  <textarea class="form-control" id="approve_admin_note" name="admin_note" rows="3"></textarea>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success">Approve</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Reject Withdrawal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <form id="rejectForm">
            <div class="modal-body">
               <input type="hidden" id="reject_withdrawal_id" name="withdrawal_id">
               <div class="mb-3">
                  <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="reject_admin_note" name="admin_note" rows="3" required></textarea>
                  <small class="text-muted">This reason will be shown to the user and the amount will be
                     refunded.</small>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-danger">Reject</button>
            </div>
         </form>
      </div>
   </div>
</div>

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
   $(document).ready(function() {
      // Initialize DataTables for Withdrawals Table
      if ($.fn.DataTable.isDataTable('#withdrawalsTable')) {
         $('#withdrawalsTable').DataTable().destroy();
      }
      $('#withdrawalsTable').DataTable({
         responsive: true,
         pageLength: 25,
         lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
         order: [[5, 'desc']], // Sort by date column
         columnDefs: [
            { orderable: false, targets: [0, 7] }, // Disable sorting on ID and Actions columns
         ],
         language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
               first: "First",
               last: "Last",
               next: "Next",
               previous: "Previous"
            }
         },
         dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
      });
      
      // Approve button
      $(document).on('click', '.approve-btn', function() {
         const id = $(this).data('id');
         $('#approve_withdrawal_id').val(id);
         $('#approve_admin_note').val(''); // Reset note
         // Use Bootstrap 5 modal if available, otherwise jQuery modal
         if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
         } else {
            $('#approveModal').modal('show');
         }
      });

      // Reject button
      $(document).on('click', '.reject-btn', function() {
         const id = $(this).data('id');
         $('#reject_withdrawal_id').val(id);
         $('#reject_admin_note').val(''); // Reset note
         // Use Bootstrap 5 modal if available, otherwise jQuery modal
         if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
         } else {
            $('#rejectModal').modal('show');
         }
      });

      // Processing button
      $('.processing-btn').on('click', function() {
         const id = $(this).data('id');

         Swal.fire({
            title: 'Mark as Processing?',
            text: 'This will mark the withdrawal as processing.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark as processing'
         }).then((result) => {
            if (result.isConfirmed) {
               $.ajax({
                  url: `/admin/withdrawal/${id}/processing`,
                  method: 'POST',
                  headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                     if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                     }
                  },
                  error: function(xhr) {
                     let errorMsg = 'An error occurred';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                     } else if (xhr.status === 0) {
                        errorMsg =
                           'Network error. Please check your connection.';
                     } else if (xhr.status === 500) {
                        errorMsg = 'Server error. Please try again later.';
                     }
                     toastr.error(errorMsg);
                  }
               });
            }
         });
      });

      // Approve form submission
      $('#approveForm').on('submit', function(e) {
         e.preventDefault();
         const id = $('#approve_withdrawal_id').val();
         const adminNote = $('#approve_admin_note').val();

         $.ajax({
            url: `/admin/withdrawal/${id}/approve`,
            method: 'POST',
            data: {
               admin_note: adminNote
            },
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
               if (response.success) {
                  toastr.success(response.message);
                  // Close modal
                  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                     const modal = bootstrap.Modal.getInstance(document
                        .getElementById('approveModal'));
                     if (modal) modal.hide();
                  } else {
                     $('#approveModal').modal('hide');
                  }
                  setTimeout(() => location.reload(), 1000);
               } else {
                  toastr.error(response.message || 'Failed to approve withdrawal');
               }
            },
            error: function(xhr) {
               let errorMsg = 'An error occurred';
               if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMsg = xhr.responseJSON.message;
               } else if (xhr.status === 0) {
                  errorMsg = 'Network error. Please check your connection.';
               } else if (xhr.status === 500) {
                  errorMsg = 'Server error. Please try again later.';
               }
               toastr.error(errorMsg);
            }
         });
      });

      // Reject form submission
      $('#rejectForm').on('submit', function(e) {
         e.preventDefault();
         const id = $('#reject_withdrawal_id').val();
         const adminNote = $('#reject_admin_note').val();

         if (!adminNote.trim()) {
            toastr.error('Please provide a rejection reason');
            return;
         }

         $.ajax({
            url: `/admin/withdrawal/${id}/reject`,
            method: 'POST',
            data: {
               admin_note: adminNote
            },
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
               if (response.success) {
                  toastr.success(response.message);
                  // Close modal
                  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                     const modal = bootstrap.Modal.getInstance(document
                        .getElementById('rejectModal'));
                     if (modal) modal.hide();
                  } else {
                     $('#rejectModal').modal('hide');
                  }
                  setTimeout(() => location.reload(), 1000);
               } else {
                  toastr.error(response.message || 'Failed to reject withdrawal');
               }
            },
            error: function(xhr) {
               let errorMsg = 'An error occurred';
               if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMsg = xhr.responseJSON.message;
               } else if (xhr.status === 0) {
                  errorMsg = 'Network error. Please check your connection.';
               } else if (xhr.status === 500) {
                  errorMsg = 'Server error. Please try again later.';
               }
               toastr.error(errorMsg);
            }
         });
      });
   });
</script>
@endpush
@endsection