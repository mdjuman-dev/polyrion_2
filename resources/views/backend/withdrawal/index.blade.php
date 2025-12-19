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
                                    <table class="table table-bordered table-hover">
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
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.withdrawal.show', $withdrawal->id) }}"
                                                                class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @if ($withdrawal->status == 'pending')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success approve-btn"
                                                                    data-id="{{ $withdrawal->id }}" title="Approve">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger reject-btn"
                                                                    data-id="{{ $withdrawal->id }}" title="Reject">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning processing-btn"
                                                                    data-id="{{ $withdrawal->id }}"
                                                                    title="Mark as Processing">
                                                                    <i class="fa fa-spinner"></i>
                                                                </button>
                                                            @elseif ($withdrawal->status == 'completed')
                                                                <span class="badge bg-success">
                                                                    <i class="fa fa-check-circle"></i> Approved
                                                                </span>
                                                            @elseif ($withdrawal->status == 'rejected')
                                                                <span class="badge bg-danger">
                                                                    <i class="fa fa-times-circle"></i> Rejected
                                                                </span>
                                                            @elseif ($withdrawal->status == 'processing')
                                                                <span class="badge bg-info">
                                                                    <i class="fa fa-spinner fa-spin"></i> Processing
                                                                </span>
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

                                @if ($withdrawals->hasPages())
                                    <div class="mt-3">
                                        {{ $withdrawals->links() }}
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

    @push('script')
        <script>
            $(document).ready(function() {
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

