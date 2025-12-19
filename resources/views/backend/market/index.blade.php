@extends('backend.layouts.master')
@section('title', 'All Markets')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Action Header -->
                        <div class="box mb-3"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0" style="color: #fff; font-weight: 600; font-size: 20px;">
                                        <i class="fa fa-chart-line me-2"></i> Markets Management
                                    </h4>
                                    <span class="badge"
                                        style="background: rgba(255,255,255,0.25); color: #fff; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                                        Total: {{ $markets->total() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter Section -->
                        <div class="box search-filter-box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body" style="padding: 25px;">
                                <form method="GET" action="{{ route('admin.market.index') }}" class="search-filter-form">
                                    @if (request('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    style="font-weight: 600; color: #374151; margin-bottom: 8px;">
                                                    <i class="fa fa-search me-2" style="color: #667eea;"></i> Search Markets
                                                </label>
                                                <input type="text" name="search" class="form-control"
                                                    style="border-radius: 10px; border: 2px solid #e5e7eb; padding: 12px 15px; transition: all 0.3s;"
                                                    placeholder="Search by question, description or slug..."
                                                    value="{{ request('search') }}"
                                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    style="font-weight: 600; color: #374151; margin-bottom: 12px;">
                                                    <i class="fa fa-filter me-2" style="color: #667eea;"></i> Filter by
                                                    Status
                                                </label>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('admin.market.index', array_merge(request()->except('status'), ['status' => ''])) }}"
                                                        class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-list me-1"></i> All
                                                    </a>
                                                    <a href="{{ route('admin.market.index', array_merge(request()->except('status'), ['status' => 'active'])) }}"
                                                        class="btn {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-check-circle me-1"></i> Active
                                                    </a>
                                                    <a href="{{ route('admin.market.index', array_merge(request()->except('status'), ['status' => 'inactive'])) }}"
                                                        class="btn {{ request('status') === 'inactive' ? 'btn-warning' : 'btn-outline-warning' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-exclamation-circle me-1"></i> Inactive
                                                    </a>
                                                    <a href="{{ route('admin.market.index', array_merge(request()->except('status'), ['status' => 'closed'])) }}"
                                                        class="btn {{ request('status') === 'closed' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-times-circle me-1"></i> Closed
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-label" style="margin-bottom: 8px;">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary"
                                                        style="border-radius: 10px; padding: 12px 25px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s;"
                                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)'"
                                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)'">
                                                        <i class="fa fa-filter me-2"></i> Filter
                                                    </button>
                                                    <a href="{{ route('admin.market.index') }}" class="btn btn-default"
                                                        style="border-radius: 10px; padding: 12px 25px; font-weight: 600; border: 2px solid #e5e7eb; transition: all 0.3s;"
                                                        onmouseover="this.style.borderColor='#667eea'; this.style.color='#667eea'"
                                                        onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit'">
                                                        <i class="fa fa-refresh me-2"></i> Reset
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Markets Table -->
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-list me-2"></i> Markets List
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">
                                                    ID</th>
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">
                                                    Question</th>
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">
                                                    Event</th>
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">
                                                    Volume</th>
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">
                                                    Status</th>
                                                <th
                                                    style="padding: 15px; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; text-align: center;">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($markets as $market)
                                                <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.2s;"
                                                    onmouseover="this.style.backgroundColor='#f9fafb'; this.style.transform='scale(1.01)'"
                                                    onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)'">
                                                    <td style="padding: 15px; color: #6b7280; font-weight: 600;">
                                                        #{{ $market->id }}</td>
                                                    <td style="padding: 15px;">
                                                        <strong
                                                            style="color: #1f2937; font-size: 15px;">{{ \Illuminate\Support\Str::limit($market->question ?? 'N/A', 50) }}</strong>
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @if ($market->event)
                                                            <a href="{{ route('admin.events.show', $market->event->id) }}"
                                                                style="color: #667eea; font-weight: 500; text-decoration: none; transition: all 0.2s;"
                                                                onmouseover="this.style.color='#764ba2'; this.style.textDecoration='underline'"
                                                                onmouseout="this.style.color='#667eea'; this.style.textDecoration='none'">
                                                                <i class="fa fa-calendar me-1"></i>
                                                                {{ \Illuminate\Support\Str::limit($market->event->title ?? 'N/A', 30) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        <span style="color: #059669; font-weight: 700; font-size: 15px;">
                                                            <i
                                                                class="fa fa-dollar-sign me-1"></i>{{ number_format($market->volume ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @if ($market->closed)
                                                            <span class="badge"
                                                                style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
                                                                <i class="fa fa-times-circle me-1"></i> Closed
                                                            </span>
                                                        @elseif ($market->active)
                                                            <span class="badge"
                                                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                                                                <i class="fa fa-check-circle me-1"></i> Active
                                                            </span>
                                                        @else
                                                            <span class="badge"
                                                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);">
                                                                <i class="fa fa-exclamation-circle me-1"></i> Inactive
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; text-align: center;">
                                                        <a href="{{ route('admin.market.show', $market->id) }}"
                                                            class="btn btn-sm"
                                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s;"
                                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)'"
                                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)'">
                                                            <i class="fa fa-eye me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center" style="padding: 60px;">
                                                        <i class="fa fa-chart-line fa-4x text-muted mb-3"
                                                            style="opacity: 0.3;"></i>
                                                        <p class="text-muted mb-0" style="font-size: 16px;">No markets
                                                            found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $markets->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
