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
                                                                <!-- <form
                                                                    action="{{ route('admin.deposits.approve', $deposit->id) }}"
                                                                    method="POST" style="display: inline-block;"
                                                                    onsubmit="return confirm('Are you sure you want to approve this deposit? This will add ${{ number_format($deposit->amount, 2) }} to user wallet.');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success"
                                                                        title="Approve">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                </form>
                                                                <form
                                                                    action="{{ route('admin.deposits.reject', $deposit->id) }}"
                                                                    method="POST" style="display: inline-block;">
                                                                    @csrf
                                                                    <input type="hidden" name="admin_note"
                                                                        id="reject_reason_{{ $deposit->id }}"
                                                                        value="">
                                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                                        title="Reject">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </form> -->
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

@endsection
