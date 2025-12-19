@extends('backend.layouts.master')
@section('title', 'Withdrawal Details')
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
                                    <i class="fa fa-money-bill-wave"></i> Withdrawal Details #{{ $withdrawal->id }}
                                </h4>
                                <a href="{{ route('admin.withdrawal.index') }}" class="btn btn-secondary">
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
                                    <h5 class="box-title">Withdrawal Information</h5>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="200">Withdrawal ID</th>
                                            <td>#{{ $withdrawal->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>User</th>
                                            <td>
                                                <strong>{{ $withdrawal->user->name }}</strong><br>
                                                <small class="text-muted">{{ $withdrawal->user->email }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Amount</th>
                                            <td>
                                                <h4 class="mb-0 text-primary">
                                                    {{ number_format($withdrawal->amount, 2) }} {{ $withdrawal->currency }}
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method</th>
                                            <td>{{ ucfirst($withdrawal->payment_method) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($withdrawal->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($withdrawal->status == 'processing')
                                                    <span class="badge bg-info">Processing</span>
                                                @elseif($withdrawal->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($withdrawal->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requested Date</th>
                                            <td>{{ $withdrawal->created_at->format('F d, Y H:i:s') }}</td>
                                        </tr>
                                        @if($withdrawal->processed_at)
                                            <tr>
                                                <th>Processed Date</th>
                                                <td>{{ $withdrawal->processed_at->format('F d, Y H:i:s') }}</td>
                                            </tr>
                                        @endif
                                        @if($withdrawal->approver)
                                            <tr>
                                                <th>Approved By</th>
                                                <td>
                                                    <strong>{{ $withdrawal->approver->name }}</strong><br>
                                                    <small class="text-muted">{{ $withdrawal->approver->email }}</small>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($withdrawal->admin_note)
                                            <tr>
                                                <th>Admin Note</th>
                                                <td>{{ $withdrawal->admin_note }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="box">
                                <div class="box-header">
                                    <h5 class="box-title">Payment Details</h5>
                                </div>
                                <div class="box-body">
                                    @php
                                        $details = is_array($withdrawal->payment_details) 
                                            ? $withdrawal->payment_details 
                                            : json_decode($withdrawal->payment_details, true);
                                    @endphp

                                    @if($withdrawal->payment_method == 'bank')
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="200">Bank Name</th>
                                                <td>{{ $details['bank_name'] ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Account Number</th>
                                                <td>{{ $details['account_number'] ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Account Holder</th>
                                                <td>{{ $details['account_holder'] ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <th>SWIFT/IBAN Code</th>
                                                <td>{{ $details['swift_code'] ?? '—' }}</td>
                                            </tr>
                                        </table>
                                    @elseif($withdrawal->payment_method == 'crypto')
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="200">Cryptocurrency</th>
                                                <td>{{ $details['crypto_type'] ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Wallet Address</th>
                                                <td>
                                                    <code>{{ $details['wallet_address'] ?? '—' }}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Network</th>
                                                <td>{{ $details['network'] ?? '—' }}</td>
                                            </tr>
                                        </table>
                                    @elseif($withdrawal->payment_method == 'paypal')
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="200">PayPal Email</th>
                                                <td>{{ $details['paypal_email'] ?? '—' }}</td>
                                            </tr>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions Sidebar -->
                        <div class="col-lg-4">
                            @if($withdrawal->status == 'pending')
                                <div class="box">
                                    <div class="box-header">
                                        <h5 class="box-title">Actions</h5>
                                    </div>
                                    <div class="box-body">
                                        <button type="button" class="btn btn-success btn-block mb-2 approve-btn" 
                                            data-id="{{ $withdrawal->id }}">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger btn-block mb-2 reject-btn" 
                                            data-id="{{ $withdrawal->id }}">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                        <button type="button" class="btn btn-warning btn-block processing-btn" 
                                            data-id="{{ $withdrawal->id }}">
                                            <i class="fa fa-spinner"></i> Mark as Processing
                                        </button>
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
                        <small class="text-muted">This reason will be shown to the user and the amount will be refunded.</small>
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

@push('script')
<script>
$(document).ready(function() {
    // Approve button
    $('.approve-btn').on('click', function() {
        const id = $(this).data('id');
        $('#approve_withdrawal_id').val(id);
        $('#approveModal').modal('show');
    });

    // Reject button
    $('.reject-btn').on('click', function() {
        const id = $(this).data('id');
        $('#reject_withdrawal_id').val(id);
        $('#rejectModal').modal('show');
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
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
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
            data: { admin_note: adminNote },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#approveModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'An error occurred');
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
            data: { admin_note: adminNote },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#rejectModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'An error occurred');
            }
        });
    });
});
</script>
@endpush
@endsection


