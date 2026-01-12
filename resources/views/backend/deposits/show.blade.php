@extends('backend.layouts.master')
@section('title', 'Deposit Details')
@section('content')
   <div class="content-wrapper">
      <div class="container-full">
         <section class="content">
            <div class="row">
               <div class="col-12">
                  <!-- Header -->
                  <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                     <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none; border-radius: 16px 16px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                           <h4 class="mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                              <i class="fa fa-credit-card me-2"></i> Deposit Details #{{ $deposit->id }}
                           </h4>
                           <a href="{{ route('admin.deposits.index') }}" class="btn btn-lg" style="background: rgba(255, 255, 255, 0.25); color: #fff; font-weight: 600; padding: 10px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); border: 2px solid rgba(255, 255, 255, 0.4); transition: all 0.3s ease; text-decoration: none; backdrop-filter: blur(10px);">
                              <i class="fa fa-arrow-left me-2"></i> Back to List
                           </a>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <!-- Main Details -->
                     <div class="col-lg-8">
                        <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                           <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                              <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                 <i class="fa fa-info-circle me-2"></i> Deposit Information
                              </h5>
                           </div>
                           <div class="box-body" style="padding: 25px;">
                              <table class="table table-borderless mb-0">
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Deposit ID</th>
                                    <td style="padding: 15px 0; color: #1f2937; font-weight: 600;">
                                       <span class="badge" style="background: #f3f4f6; color: #374151; padding: 6px 12px; border-radius: 8px;">#{{ $deposit->id }}</span>
                                    </td>
                                 </tr>
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">User</th>
                                    <td style="padding: 15px 0; color: #1f2937;">
                                       <strong>{{ $deposit->user->name }}</strong><br>
                                       <small class="text-muted">{{ $deposit->user->email }}</small>
                                    </td>
                                 </tr>
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Amount</th>
                                    <td style="padding: 15px 0;">
                                       <h4 class="mb-0" style="color: #667eea; font-weight: 700; font-size: 24px;">
                                          {{ number_format($deposit->amount, 2) }} {{ $deposit->currency }}
                                       </h4>
                                    </td>
                                 </tr>
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Payment Method</th>
                                    <td style="padding: 15px 0; color: #1f2937; font-weight: 600;">{{ ucfirst($deposit->payment_method ?? 'N/A') }}</td>
                                 </tr>
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Status</th>
                                    <td style="padding: 15px 0;">
                                       @if ($deposit->status == 'pending')
                                          <span class="badge bg-warning" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Pending</span>
                                       @elseif($deposit->status == 'completed')
                                          <span class="badge bg-success" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Completed</span>
                                       @elseif($deposit->status == 'failed')
                                          <span class="badge bg-danger" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Failed</span>
                                       @elseif($deposit->status == 'expired')
                                          <span class="badge bg-secondary" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Expired</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Merchant Trade No</th>
                                    <td style="padding: 15px 0; color: #1f2937;">
                                       <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $deposit->merchant_trade_no }}</code>
                                    </td>
                                 </tr>
                                 @if ($deposit->transaction_id)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Transaction ID</th>
                                       <td style="padding: 15px 0; color: #1f2937;">
                                          <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $deposit->transaction_id }}</code>
                                       </td>
                                    </tr>
                                 @endif
                                 <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Requested Date</th>
                                    <td style="padding: 15px 0; color: #1f2937;">{{ $deposit->created_at->format('F d, Y H:i:s') }}</td>
                                 </tr>
                                 @if ($deposit->completed_at)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Completed Date</th>
                                       <td style="padding: 15px 0; color: #1f2937;">{{ $deposit->completed_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                 @endif
                                 @php
                                    $responseData = $deposit->response_data ?? [];
                                   @endphp
                                 @if (isset($responseData['approved_by']))
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Approved By</th>
                                       <td style="padding: 15px 0; color: #1f2937;">
                                          Admin ID: {{ $responseData['approved_by'] }}<br>
                                          <small class="text-muted">Approved at:
                                             {{ $responseData['approved_at'] ?? 'N/A' }}</small>
                                       </td>
                                    </tr>
                                 @endif
                                 @if (isset($responseData['admin_note']))
                                    <tr>
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Admin Note</th>
                                       <td style="padding: 15px 0; color: #1f2937;">{{ $responseData['admin_note'] }}</td>
                                    </tr>
                                 @endif
                              </table>
                           </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-top: 20px;">
                           <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                              <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                 <i class="fa fa-exchange-alt me-2"></i> Transaction Details
                              </h5>
                           </div>
                           <div class="box-body" style="padding: 25px;">
                              @php
                                 $responseData = $deposit->response_data ?? [];
                               @endphp

                              @if ($deposit->payment_method == 'manual')
                                 <table class="table table-borderless mb-0">
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Query Code</th>
                                       <td style="padding: 15px 0; color: #1f2937;">
                                          <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $responseData['query_code'] ?? '—' }}</code>
                                       </td>
                                    </tr>
                                    <tr>
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Submitted At</th>
                                       <td style="padding: 15px 0; color: #1f2937;">{{ $responseData['submitted_at'] ?? $deposit->created_at->format('F d, Y H:i:s') }}
                                       </td>
                                    </tr>
                                 </table>
                              @elseif($deposit->payment_method == 'binancepay')
                                 <table class="table table-borderless mb-0">
                                    @if ($deposit->prepay_id)
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Prepay ID</th>
                                          <td style="padding: 15px 0; color: #1f2937;">
                                             <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $deposit->prepay_id }}</code>
                                          </td>
                                       </tr>
                                    @endif
                                    @if ($deposit->transaction_id)
                                       <tr>
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Transaction ID</th>
                                          <td style="padding: 15px 0; color: #1f2937;">
                                             <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $deposit->transaction_id }}</code>
                                          </td>
                                       </tr>
                                    @endif
                                 </table>
                              @elseif($deposit->payment_method == 'metamask')
                                 <table class="table table-borderless mb-0">
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Network</th>
                                       <td style="padding: 15px 0; color: #1f2937;">{{ $responseData['network'] ?? '—' }}</td>
                                    </tr>
                                    @if (isset($responseData['merchant_address']))
                                       <tr>
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Merchant Address</th>
                                          <td style="padding: 15px 0; color: #1f2937;">
                                             <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $responseData['merchant_address'] }}</code>
                                          </td>
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
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-cog me-2"></i> Actions
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 <a href="{{ route('admin.deposits.approve', $deposit->id) }}"
                                    class="btn btn-primary-gradient btn-block mb-3 approve-btn" data-id="{{ $deposit->id }}" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                    <i class="fa fa-check me-2"></i> Approve & Add Balance
                                 </a>
                                 <a href="{{ route('admin.deposits.reject', $deposit->id) }}"
                                    class="btn btn-danger btn-block mb-3 reject-btn" data-id="{{ $deposit->id }}" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
                                    <i class="fa fa-times me-2"></i> Reject
                                 </a>
                              </div>
                           </div>
                        @elseif($deposit->status == 'completed')
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-info-circle me-2"></i> Status
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 <div class="alert alert-success" style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; border-radius: 10px; padding: 15px;">
                                    <i class="fa fa-check-circle me-2"></i> This deposit has been approved and the
                                    balance has been added to user's wallet.
                                 </div>
                              </div>
                           </div>
                        @elseif($deposit->status == 'failed')
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-info-circle me-2"></i> Status
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 <div class="alert alert-danger" style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; border-radius: 10px; padding: 15px;">
                                    <i class="fa fa-times-circle me-2"></i> This deposit has been rejected.
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
   <div class="modal fade" id="approveModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header primary-gradient" style="border-radius: 15px 15px 0 0; border: none; padding: 20px 25px;">
               <h5 class="modal-title" style="color: #fff; font-weight: 700;">
                  <i class="fa fa-check-circle me-2"></i> Approve Deposit
               </h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
               <div class="modal-body" style="padding: 25px;">
                  <input type="hidden" id="approve_deposit_id" name="deposit_id">
                  <div class="mb-3">
                     <label class="form-label" style="font-weight: 600; color: #374151;">Admin Note (Optional)</label>
                     <textarea class="form-control" id="approve_admin_note" name="admin_note" rows="3" style="border-radius: 8px; padding: 12px 15px;"></textarea>
                     <small class="text-muted">This will add {{ number_format($deposit->amount, 2) }}
                        {{ $deposit->currency }} to user's wallet balance.</small>
                  </div>
               </div>
               <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 25px;">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">Cancel</button>
                  <button type="submit" class="btn btn-primary-gradient" style="font-weight: 600; padding: 10px 25px; border-radius: 8px;">Approve & Add Balance</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Reject Modal -->
   <div class="modal fade" id="rejectModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 15px 15px 0 0; border: none; padding: 20px 25px;">
               <h5 class="modal-title" style="color: #fff; font-weight: 700;">
                  <i class="fa fa-times-circle me-2"></i> Reject Deposit
               </h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
               <div class="modal-body" style="padding: 25px;">
                  <input type="hidden" id="reject_deposit_id" name="deposit_id">
                  <div class="mb-3">
                     <label class="form-label" style="font-weight: 600; color: #374151;">Rejection Reason <span class="text-danger">*</span></label>
                     <textarea class="form-control" id="reject_admin_note" name="admin_note" rows="3" required style="border-radius: 8px; padding: 12px 15px;"></textarea>
                     <small class="text-muted">This reason will be stored for record keeping.</small>
                  </div>
               </div>
               <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 25px;">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">Cancel</button>
                  <button type="submit" class="btn btn-danger" style="font-weight: 600; padding: 10px 25px; border-radius: 8px;">Reject</button>
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
               const id = {{ $deposit->id }};
               $('#approve_deposit_id').val(id);
               $('#approve_admin_note').val('');
               // Use Bootstrap 5 modal if available
               if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                  const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                  modal.show();
               } else {
                  $('#approveModal').modal('show');
               }
            });

            // Reject button
            $(document).on('click', '.reject-btn', function (e) {
               e.preventDefault();
               const id = {{ $deposit->id }};
               $('#reject_deposit_id').val(id);
               $('#reject_admin_note').val('');
               // Use Bootstrap 5 modal if available
               if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                  const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                  modal.show();
               } else {
                  $('#rejectModal').modal('show');
               }
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
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                           const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                           if (modal) modal.hide();
                        } else {
                           $('#rejectModal').modal('hide');
                        }
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