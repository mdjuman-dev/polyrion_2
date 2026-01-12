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
                                    <table id="depositsTable" class="table table-bordered table-hover" style="width:100%">
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
            // Initialize DataTables for Deposits Table
            if ($.fn.DataTable.isDataTable('#depositsTable')) {
                $('#depositsTable').DataTable().destroy();
            }
            $('#depositsTable').DataTable({
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[6, 'desc']], // Sort by date (last column)
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
        });
    </script>
    @endpush

@endsection
