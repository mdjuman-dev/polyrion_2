@extends('backend.layouts.master')
@section('title', 'Deposit Details')
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
                              <i class="fa fa-credit-card"></i> Deposit Details #{{ $deposit->id }}
                           </h4>
                           <a href="{{ route('admin.deposits.index') }}" class="btn btn-secondary">
                              <i class="fa fa-arrow-left"></i> Back to List
                           </a>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <!-- Main Details -->
                     <div class="col-lg-8">
                        <div class="box">
                           <div class="box-header">
                              <h5 class="box-title">Deposit Information</h5>
                           </div>
                           <div class="box-body">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="200">Deposit ID</th>
                                    <td>#{{ $deposit->id }}</td>
                                 </tr>
                                 <tr>
                                    <th>User</th>
                                    <td>
                                       <strong>{{ $deposit->user->name }}</strong><br>
                                       <small class="text-muted">{{ $deposit->user->email }}</small>
                                    </td>
                                 </tr>
                                 <tr>
                                    <th>Amount</th>
                                    <td>
                                       <h4 class="mb-0 text-primary">
                                          {{ number_format($deposit->amount, 2) }} {{ $deposit->currency }}
                                       </h4>
                                    </td>
                                 </tr>
                                 <tr>
                                    <th>Payment Method</th>
                                    <td>{{ ucfirst($deposit->payment_method ?? 'N/A') }}</td>
                                 </tr>
                                 <tr>
                                    <th>Status</th>
                                    <td>
                                       @if ($deposit->status == 'pending')
                                          <span class="badge bg-warning">Pending</span>
                                       @elseif($deposit->status == 'completed')
                                          <span class="badge bg-success">Completed</span>
                                       @elseif($deposit->status == 'failed')
                                          <span class="badge bg-danger">Failed</span>
                                       @elseif($deposit->status == 'expired')
                                          <span class="badge bg-secondary">Expired</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <th>Merchant Trade No</th>
                                    <td><code>{{ $deposit->merchant_trade_no }}</code></td>
                                 </tr>
                                 @if ($deposit->transaction_id)
                                    <tr>
                                       <th>Transaction ID</th>
                                       <td><code>{{ $deposit->transaction_id }}</code></td>
                                    </tr>
                                 @endif
                                 <tr>
                                    <th>Requested Date</th>
                                    <td>{{ $deposit->created_at->format('F d, Y H:i:s') }}</td>
                                 </tr>
                                 @if ($deposit->completed_at)
                                    <tr>
                                       <th>Completed Date</th>
                                       <td>{{ $deposit->completed_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                 @endif
                                 @php
                                    $responseData = $deposit->response_data ?? [];
                                   @endphp
                                 @if (isset($responseData['approved_by']))
                                    <tr>
                                       <th>Approved By</th>
                                       <td>
                                          Admin ID: {{ $responseData['approved_by'] }}<br>
                                          <small class="text-muted">Approved at:
                                             {{ $responseData['approved_at'] ?? 'N/A' }}</small>
                                       </td>
                                    </tr>
                                 @endif
                                 @if (isset($responseData['admin_note']))
                                    <tr>
                                       <th>Admin Note</th>
                                       <td>{{ $responseData['admin_note'] }}</td>
                                    </tr>
                                 @endif
                              </table>
                           </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="box">
                           <div class="box-header">
                              <h5 class="box-title">Transaction Details</h5>
                           </div>
                           <div class="box-body">
                              @php
                                 $responseData = $deposit->response_data ?? [];
                               @endphp

                              @if ($deposit->payment_method == 'manual')
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Query Code</th>
                                       <td><code>{{ $responseData['query_code'] ?? '—' }}</code></td>
                                    </tr>
                                    <tr>
                                       <th>Submitted At</th>
                                       <td>{{ $responseData['submitted_at'] ?? $deposit->created_at->format('F d, Y H:i:s') }}
                                       </td>
                                    </tr>
                                 </table>
                              @elseif($deposit->payment_method == 'binancepay')
                                 <table class="table table-bordered">
                                    @if ($deposit->prepay_id)
                                       <tr>
                                          <th width="200">Prepay ID</th>
                                          <td><code>{{ $deposit->prepay_id }}</code></td>
                                       </tr>
                                    @endif
                                    @if ($deposit->transaction_id)
                                       <tr>
                                          <th>Transaction ID</th>
                                          <td><code>{{ $deposit->transaction_id }}</code></td>
                                       </tr>
                                    @endif
                                 </table>
                              @elseif($deposit->payment_method == 'metamask')
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Network</th>
                                       <td>{{ $responseData['network'] ?? '—' }}</td>
                                    </tr>
                                    @if (isset($responseData['merchant_address']))
                                       <tr>
                                          <th>Merchant Address</th>
                                          <td><code>{{ $responseData['merchant_address'] }}</code></td>
                                       </tr>
                                    @endif
                                 </table>
                              @endif
                           </div>
                        </div>
                     </div>

                     <!-- Actions Sidebar -->
                     <div class="col-lg-4">
                        @if ($deposit->status == 'pending')
                           <div class="box">
                              <div class="box-header">
                                 <h5 class="box-title">Actions</h5>
                              </div>
                              <div class="box-body">
                                 <a href="{{ route('admin.deposits.approve', $deposit->id) }}"
                                    class="btn btn-success btn-block mb-2 approve-btn" data-id="{{ $deposit->id }}">
                                    <i class="fa fa-check"></i> Approve & Add Balance
                                 </a>
                                 <a href="{{ route('admin.deposits.reject', $deposit->id) }}"
                                    class="btn btn-danger btn-block mb-2 reject-btn" data-id="{{ $deposit->id }}">
                                    <i class="fa fa-times"></i> Reject
                                 </a>
                              </div>
                           </div>
                        @elseif($deposit->status == 'completed')
                           <div class="box">
                              <div class="box-header">
                                 <h5 class="box-title">Status</h5>
                              </div>
                              <div class="box-body">
                                 <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i> This deposit has been approved and the
                                    balance has been added to user's wallet.
                                 </div>
                              </div>
                           </div>
                        @elseif($deposit->status == 'failed')
                           <div class="box">
                              <div class="box-header">
                                 <h5 class="box-title">Status</h5>
                              </div>
                              <div class="box-body">
                                 <div class="alert alert-danger">
                                    <i class="fa fa-times-circle"></i> This deposit has been rejected.
                                 </div>
                              </div>
                           </div>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </div>
   </div>

   <!-- Approve Modal -->
   <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Approve Deposit</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form id="approveForm">
               <div class="modal-body">
                  <input type="hidden" id="approve_deposit_id" name="deposit_id">
                  <div class="mb-3">
                     <label class="form-label">Admin Note (Optional)</label>
                     <textarea class="form-control" id="approve_admin_note" name="admin_note" rows="3"></textarea>
                     <small class="text-muted">This will add {{ number_format($deposit->amount, 2) }}
                        {{ $deposit->currency }} to user's wallet balance.</small>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success">Approve & Add Balance</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Reject Modal -->
   <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Reject Deposit</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form id="rejectForm">
               <div class="modal-body">
                  <input type="hidden" id="reject_deposit_id" name="deposit_id">
                  <div class="mb-3">
                     <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                     <textarea class="form-control" id="reject_admin_note" name="admin_note" rows="3" required></textarea>
                     <small class="text-muted">This reason will be stored for record keeping.</small>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Reject</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   @push('script')
      <script>
         $(document).ready(function () {
            // Approve button
            $(document).on('click', '.approve-btn', function (e) {
               e.preventDefault();
               const id = $(this).data('id');
               $('#approve_deposit_id').val(id);
               $('#approve_admin_note').val('');
               $('#approveModal').modal('show');
            });

            // Reject button
            $(document).on('click', '.reject-btn', function (e) {
               e.preventDefault();
               const id = $(this).data('id');
               $('#reject_deposit_id').val(id);
               $('#reject_admin_note').val('');
               $('#rejectModal').modal('show');
            });

            // Approve form submission
            $('#approveForm').on('submit', function (e) {
               e.preventDefault();
               const id = $('#approve_deposit_id').val();
               const adminNote = $('#approve_admin_note').val();

               $.ajax({
                  url: `/admin/deposits/${id}/approve`,
                  method: 'POST',
                  data: {
                     admin_note: adminNote
                  },
                  headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                     'Accept': 'application/json'
                  },
                  success: function (response) {
                     if (response.success) {
                        if (typeof Swal !== 'undefined') {
                           Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: response.message,
                              confirmButtonColor: '#28a745'
                           }).then(() => {
                              location.reload();
                           });
                        } else {
                           toastr.success(response.message);
                           setTimeout(() => location.reload(), 1000);
                        }
                     } else {
                        toastr.error(response.message || 'Failed to approve deposit');
                     }
                  },
                  error: function (xhr) {
                     let errorMsg = 'An error occurred';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                     }
                     toastr.error(errorMsg);
                  }
               });
            });

            // Reject form submission
            $('#rejectForm').on('submit', function (e) {
               e.preventDefault();
               const id = $('#reject_deposit_id').val();
               const adminNote = $('#reject_admin_note').val();

               if (!adminNote.trim()) {
                  toastr.error('Please provide a rejection reason');
                  return;
               }

               $.ajax({
                  url: `/admin/deposits/${id}/reject`,
                  method: 'POST',
                  data: {
                     admin_note: adminNote
                  },
                  headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                     'Accept': 'application/json'
                  },
                  success: function (response) {
                     if (response.success) {
                        if (typeof Swal !== 'undefined') {
                           Swal.fire({
                              icon: 'success',
                              title: 'Rejected!',
                              text: response.message,
                              confirmButtonColor: '#dc3545'
                           }).then(() => {
                              location.reload();
                           });
                        } else {
                           toastr.success(response.message);
                           setTimeout(() => location.reload(), 1000);
                        }
                        // Close modal
                        $('#rejectModal').modal('hide');
                     } else {
                        toastr.error(response.message || 'Failed to reject deposit');
                     }
                  },
                  error: function (xhr) {
                     let errorMsg = 'An error occurred';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                     } else if (xhr.status === 422) {
                        errorMsg = 'Validation error. Please check your input.';
                     } else if (xhr.status === 404) {
                        errorMsg = 'Deposit not found.';
                     } else if (xhr.status === 500) {
                        errorMsg = 'Server error. Please try again.';
                     }
                     toastr.error(errorMsg);
                     console.error('Deposit rejection error:', xhr);
                  }
               });
            });
         });
      </script>
   @endpush
@endsection