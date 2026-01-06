@extends('backend.layouts.master')
@section('title', 'Contact Messages')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Contact Messages</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-envelope"></i> Contact Messages
                                </h4>
                                <div class="box-tools">
                                    <a href="{{ route('admin.contact.settings') }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-cog"></i> Support Settings
                                    </a>
                                </div>
                            </div>
                            <div class="box-body">
                                @if($messages->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Name</th>
                                                    <th width="20%">Email</th>
                                                    <th width="25%">Subject</th>
                                                    <th width="20%">Message</th>
                                                    <th width="10%">Created At</th>
                                                    <th width="5%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($messages as $message)
                                                    <tr>
                                                        <td>{{ $message->id }}</td>
                                                        <td>{{ $message->name }}</td>
                                                        <td>{{ $message->email }}</td>
                                                        <td>{{ Str::limit($message->subject, 40) }}</td>
                                                        <td>{{ Str::limit(strip_tags($message->message), 50) }}</td>
                                                        <td>{{ $message->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.contact.show', $message->id) }}" class="btn btn-sm btn-info" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
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
                                        <i class="fa fa-envelope fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No contact messages found.</p>
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

