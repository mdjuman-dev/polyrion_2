@extends('backend.layouts.master')
@section('title', 'View Contact Message')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}">Contact Messages</a></li>
                                <li class="breadcrumb-item active">View</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-envelope-open"></i> Contact Message Details
                                </h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Name:</strong>
                                        <p>{{ $message->name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Email:</strong>
                                        <p><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <strong>Subject:</strong>
                                        <p>{{ $message->subject }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <strong>Message:</strong>
                                        <div style="background: #f5f5f5; padding: 1rem; border-radius: 6px; margin-top: 0.5rem;">
                                            {!! nl2br(e($message->message)) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Submitted:</strong>
                                        <p>{{ $message->created_at->format('F d, Y h:i A') }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                    <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

