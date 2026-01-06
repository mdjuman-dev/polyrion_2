@extends('backend.layouts.master')
@section('title', 'FAQ Management')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">FAQs</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-question-circle"></i> FAQ Management
                                </h4>
                                <div class="box-tools">
                                    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Add New FAQ
                                    </a>
                                </div>
                            </div>
                            <div class="box-body">
                                @if($faqs->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="40%">Question</th>
                                                    <th width="35%">Answer</th>
                                                    <th width="10%">Status</th>
                                                    <th width="10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($faqs as $faq)
                                                    <tr>
                                                        <td>{{ $faq->id }}</td>
                                                        <td>{{ Str::limit($faq->question, 60) }}</td>
                                                        <td>{{ Str::limit(strip_tags($faq->answer), 80) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $faq->status === 'active' ? 'success' : 'danger' }}">
                                                                {{ ucfirst($faq->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-info" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No FAQs found. <a href="{{ route('admin.faqs.create') }}">Create your first FAQ</a></p>
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

