@extends('backend.layouts.master')
@section('title', 'All Deposits')
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
                                        <i class="fa fa-credit-card"></i> Deposit Management
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
                                                <h3 class="mb-0">{{ $stats['failed'] }}</h3>
                                                <p class="mb-0 text-muted">Failed</p>
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
                                                    <i class="fa fa-keyboard fa-2x text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h3 class="mb-0">{{ $stats['manual_pending'] }}</h3>
                                                <p class="mb-0 text-muted">Manual Pending</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <form method="GET" action="{{ route('admin.deposits.index') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by trade no, transaction ID, user email"
                                            value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                                                Failed</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>
                                                Expired</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Payment Method</label>
                                        <select name="payment_method" class="form-control">
                                            <option value="">All Methods</option>
                                            <option value="manual"
                                                {{ request('payment_method') == 'manual' ? 'selected' : '' }}>Manual
                                            </option>
                                            <option value="binancepay"
                                                {{ request('payment_method') == 'binancepay' ? 'selected' : '' }}>Binance
                                                Pay</option>
                                            <option value="metamask"
                                                {{ request('payment_method') == 'metamask' ? 'selected' : '' }}>MetaMask
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Search
                                            </button>
                                            @if (request('search') || request('status') || request('payment_method'))
                                                <a href="{{ route('admin.deposits.index') }}" class="btn btn-secondary">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Deposits Table -->
                        <div class="box">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Status</th>
                                                <th>Trade No</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($deposits as $deposit)
                                                <tr>
                                                    <td>#{{ $deposit->id }}</td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $deposit->user->name }}</strong><br>
                                                            <small class="text-muted">{{ $deposit->user->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>{{ number_format($deposit->amount, 2) }}
                                                            {{ $deposit->currency }}</strong>
                                                    </td>
                                                    <td>{{ ucfirst($deposit->payment_method ?? 'N/A') }}</td>
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
                                                    <td>
                                                        <small><code>{{ $deposit->merchant_trade_no }}</code></small>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $deposit->created_at->format('M d, Y H:i') }}</strong>
                                                            @if ($deposit->completed_at)
                                                                <br><small class="text-muted">Completed:
                                                                    {{ $deposit->completed_at->format('M d, Y H:i') }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.deposits.show', $deposit->id) }}"
                                                                class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @if ($deposit->status == 'pending')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success approve-btn"
                                                                    data-id="{{ $deposit->id }}" title="Approve">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger reject-btn"
                                                                    data-id="{{ $deposit->id }}" title="Reject">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            @elseif ($deposit->status == 'completed')
                                                                <span class="badge bg-success">
                                                                    <i class="fa fa-check-circle"></i> Approved
                                                                </span>
                                                            @elseif ($deposit->status == 'failed')
                                                                <span class="badge bg-danger">
                                                                    <i class="fa fa-times-circle"></i> Rejected
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">No deposits found.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if ($deposits->hasPages())
                                    <div class="mt-3">
                                        {{ $deposits->links() }}
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
                            <small class="text-muted">This will add the deposit amount to user's wallet balance.</small>
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
            $(document).ready(function() {
                // Approve button
                $(document).on('click', '.approve-btn', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    $('#approve_deposit_id').val(id);
                    $('#approve_admin_note').val('');
                    $('#approveModal').modal('show');
                });

                // Reject button
                $(document).on('click', '.reject-btn', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    $('#reject_deposit_id').val(id);
                    $('#reject_admin_note').val('');
                    $('#rejectModal').modal('show');
                });

                // Approve form submission
                $('#approveForm').on('submit', function(e) {
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
                        success: function(response) {
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
                                // Close modal
                                $('#approveModal').modal('hide');
                            } else {
                                toastr.error(response.message || 'Failed to approve deposit');
                            }
                        },
                        error: function(xhr) {
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
                            console.error('Deposit approval error:', xhr);
                        }
                    });
                });

                // Reject form submission
                $('#rejectForm').on('submit', function(e) {
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
                        success: function(response) {
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
                        error: function(xhr) {
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
