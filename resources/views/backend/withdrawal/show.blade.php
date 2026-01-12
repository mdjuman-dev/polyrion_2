@extends('backend.layouts.master')
@section('title', 'Withdrawal Details')
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
                                 <i class="fa fa-money-bill-wave me-2"></i> Withdrawal Details #{{ $withdrawal->id }}
                              </h4>
                              <a href="{{ route('admin.withdrawal.index') }}" class="btn btn-lg" style="background: rgba(255, 255, 255, 0.25); color: #fff; font-weight: 600; padding: 10px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); border: 2px solid rgba(255, 255, 255, 0.4); transition: all 0.3s ease; text-decoration: none; backdrop-filter: blur(10px);">
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
                                    <i class="fa fa-info-circle me-2"></i> Withdrawal Information
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 <table class="table table-borderless mb-0">
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Withdrawal ID</th>
                                       <td style="padding: 15px 0; color: #1f2937; font-weight: 600;">
                                          <span class="badge" style="background: #f3f4f6; color: #374151; padding: 6px 12px; border-radius: 8px;">#{{ $withdrawal->id }}</span>
                                       </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">User</th>
                                       <td style="padding: 15px 0; color: #1f2937;">
                                          <strong>{{ $withdrawal->user->name }}</strong><br>
                                          <small class="text-muted">{{ $withdrawal->user->email }}</small>
                                       </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Amount</th>
                                       <td style="padding: 15px 0;">
                                          <h4 class="mb-0" style="color: #667eea; font-weight: 700; font-size: 24px;">
                                             {{ number_format($withdrawal->amount, 2) }} {{ $withdrawal->currency }}
                                          </h4>
                                       </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Payment Method</th>
                                       <td style="padding: 15px 0; color: #1f2937; font-weight: 600;">{{ ucfirst($withdrawal->payment_method) }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Status</th>
                                       <td style="padding: 15px 0;">
                                          @if($withdrawal->status == 'pending')
                                             <span class="badge bg-warning" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Pending</span>
                                          @elseif($withdrawal->status == 'processing')
                                             <span class="badge bg-info" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Processing</span>
                                          @elseif($withdrawal->status == 'completed')
                                             <span class="badge bg-success" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Completed</span>
                                          @elseif($withdrawal->status == 'rejected')
                                             <span class="badge bg-danger" style="padding: 8px 16px; border-radius: 8px; font-weight: 600;">Rejected</span>
                                          @endif
                                       </td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                       <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Requested Date</th>
                                       <td style="padding: 15px 0; color: #1f2937;">{{ $withdrawal->created_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                    @if($withdrawal->processed_at)
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Processed Date</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $withdrawal->processed_at->format('F d, Y H:i:s') }}</td>
                                       </tr>
                                    @endif
                                    @if($withdrawal->approver)
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Approved By</th>
                                          <td style="padding: 15px 0; color: #1f2937;">
                                             <strong>{{ $withdrawal->approver->name }}</strong><br>
                                             <small class="text-muted">{{ $withdrawal->approver->email }}</small>
                                          </td>
                                       </tr>
                                    @endif
                                    @if($withdrawal->admin_note)
                                       <tr>
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Admin Note</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $withdrawal->admin_note }}</td>
                                       </tr>
                                    @endif
                                 </table>
                              </div>
                           </div>

                           <!-- Payment Details -->
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-top: 20px;">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-credit-card me-2"></i> Payment Details
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 @php
   $details = is_array($withdrawal->payment_details)
      ? $withdrawal->payment_details
      : json_decode($withdrawal->payment_details, true);
                                 @endphp

                                 @if($withdrawal->payment_method == 'bank')
                                    <table class="table table-borderless mb-0">
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Bank Name</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['bank_name'] ?? '—' }}</td>
                                       </tr>
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Account Number</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['account_number'] ?? '—' }}</td>
                                       </tr>
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Account Holder</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['account_holder'] ?? '—' }}</td>
                                       </tr>
                                       <tr>
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">SWIFT/IBAN Code</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['swift_code'] ?? '—' }}</td>
                                       </tr>
                                    </table>
                                 @elseif($withdrawal->payment_method == 'crypto')
                                    <table class="table table-borderless mb-0">
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">Cryptocurrency</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['crypto_type'] ?? '—' }}</td>
                                       </tr>
                                       <tr style="border-bottom: 1px solid #e5e7eb;">
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Wallet Address</th>
                                          <td style="padding: 15px 0; color: #1f2937;">
                                             <code style="background: #f3f4f6; padding: 8px 12px; border-radius: 8px; color: #667eea; font-weight: 600;">{{ $details['wallet_address'] ?? '—' }}</code>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th style="padding: 15px 0; color: #6b7280; font-weight: 600;">Network</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['network'] ?? '—' }}</td>
                                       </tr>
                                    </table>
                                 @elseif($withdrawal->payment_method == 'paypal')
                                    <table class="table table-borderless mb-0">
                                       <tr>
                                          <th width="200" style="padding: 15px 0; color: #6b7280; font-weight: 600;">PayPal Email</th>
                                          <td style="padding: 15px 0; color: #1f2937;">{{ $details['paypal_email'] ?? '—' }}</td>
                                       </tr>
                                    </table>
                                 @endif
                              </div>
                           </div>
                        </div>

                        <!-- Actions Sidebar -->
                     <div class="col-lg-4">
                        {{-- Status pending ba processing holei action box dekhabe --}}
                        @if(in_array($withdrawal->status, ['pending', 'processing']))
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-cog me-2"></i> Actions
                                 </h5>
                              </div>
                              <div class="box-body" style="padding: 25px;">
                                 {{-- Approve ebong Reject button pending/processing duitar jonnoi thakbe --}}
                                 <a href="{{ route('admin.withdrawal.approve', $withdrawal->id) }}"
                                    class="btn btn-primary-gradient btn-block mb-3 approve-btn" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                    <i class="fa fa-check me-2"></i> Approve
                                 </a>

                                 <a href="{{ route('admin.withdrawal.reject', $withdrawal->id) }}"
                                    class="btn btn-danger btn-block mb-3 reject-btn" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
                                    <i class="fa fa-times me-2"></i> Reject
                                 </a>

                                 {{-- Sudhu status pending thaklei Processing button-ta dekhabe --}}
                                 @if($withdrawal->status == 'pending')
                                    <a href="{{ route('admin.withdrawal.processing', $withdrawal->id) }}"
                                       class="btn btn-warning btn-block processing-btn" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                                       <i class="fa fa-spinner me-2"></i> Mark as Processing
                                    </a>
                                 @endif
                              </div>
                           </div>
                        @else
                           {{-- Completed ba Rejected hole status badge dekhate paren --}}
                           <div class="box" style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                              <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                 <h5 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-info-circle me-2"></i> Request Settled
                                 </h5>
                              </div>
                              <div class="box-body text-center" style="padding: 30px 25px;">
                                 @if($withdrawal->status == 'completed')
                                    <span class="badge bg-success p-3" style="font-size: 16px; border-radius: 10px; font-weight: 600;">
                                       <i class="fa fa-check-circle me-2"></i> Already Approved
                                    </span>
                                 @else
                                    <span class="badge bg-danger p-3" style="font-size: 16px; border-radius: 10px; font-weight: 600;">
                                       <i class="fa fa-times-circle me-2"></i> Already Rejected
                                    </span>
                                 @endif
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
                     <i class="fa fa-check-circle me-2"></i> Approve Withdrawal
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
               </div>
               <form id="approveForm">
                  <div class="modal-body" style="padding: 25px;">
                     <input type="hidden" id="approve_withdrawal_id" name="withdrawal_id">
                     <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Admin Note (Optional)</label>
                        <textarea class="form-control" id="approve_admin_note" name="admin_note" rows="3" style="border-radius: 8px; padding: 12px 15px;"></textarea>
                     </div>
                  </div>
                  <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 25px;">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">Cancel</button>
                     <button type="submit" class="btn btn-primary-gradient" style="font-weight: 600; padding: 10px 25px; border-radius: 8px;">Approve</button>
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
                     <i class="fa fa-times-circle me-2"></i> Reject Withdrawal
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
               </div>
               <form id="rejectForm">
                  <div class="modal-body" style="padding: 25px;">
                     <input type="hidden" id="reject_withdrawal_id" name="withdrawal_id">
                     <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_admin_note" name="admin_note" rows="3" required style="border-radius: 8px; padding: 12px 15px;"></textarea>
                        <small class="text-muted">This reason will be shown to the user and the amount will be refunded.</small>
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
               $('.approve-btn').on('click', function (e) {
                  e.preventDefault();
                  const id = {{ $withdrawal->id }};
                  $('#approve_withdrawal_id').val(id);
                  // Use Bootstrap 5 modal if available
                  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                     const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                     modal.show();
                  } else {
                     $('#approveModal').modal('show');
                  }
               });

               // Reject button
               $('.reject-btn').on('click', function (e) {
                  e.preventDefault();
                  const id = {{ $withdrawal->id }};
                  $('#reject_withdrawal_id').val(id);
                  // Use Bootstrap 5 modal if available
                  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                     const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                     modal.show();
                  } else {
                     $('#rejectModal').modal('show');
                  }
               });

               // Processing button
               $('.processing-btn').on('click', function (e) {
                  e.preventDefault();
                  const id = {{ $withdrawal->id }};

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
                           success: function (response) {
                              if (response.success) {
                                 toastr.success(response.message);
                                 setTimeout(() => location.reload(), 1000);
                              }
                           },
                           error: function (xhr) {
                              toastr.error(xhr.responseJSON?.message || 'An error occurred');
                           }
                        });
                     }
                  });
               });

               // Approve form submission
               $('#approveForm').on('submit', function (e) {
                  e.preventDefault();
                  const id = $('#approve_withdrawal_id').val();
                  const adminNote = $('#approve_admin_note').val();

                  $.ajax({
                     url: `/admin/withdrawal/${id}/approve`,
                     method: 'POST',
                     data: { admin_note: adminNote },
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     success: function (response) {
                        if (response.success) {
                           toastr.success(response.message);
                           // Close modal
                           if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                              const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                              if (modal) modal.hide();
                           } else {
                              $('#approveModal').modal('hide');
                           }
                           setTimeout(() => location.reload(), 1000);
                        }
                     },
                     error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                     }
                  });
               });

               // Reject form submission
               $('#rejectForm').on('submit', function (e) {
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
                     data: { admin_note: adminNote },
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     success: function (response) {
                        if (response.success) {
                           toastr.success(response.message);
                           // Close modal
                           if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                              const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                              if (modal) modal.hide();
                           } else {
                              $('#rejectModal').modal('hide');
                           }
                           setTimeout(() => location.reload(), 1000);
                        }
                     },
                     error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                     }
                  });
               });
            });
         </script>
      @endpush
@endsection