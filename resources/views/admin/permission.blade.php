@extends('admin.includes.master')
@section('content')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Permission</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                Permissions allow for  <b>{{ $role->name }}</b>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permission-update',$role->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row mt-2">
                        @foreach ($permissionsWithStatus as $permission)
                            <div class="col-sm-3">
                                <x-input-box name="permissions[]" type="checkbox"
                                    label="{{ ucfirst($permission['permission']) }}" value="{{ $permission['permission'] }}"
                                      checked="{{ $permission['has_permission'] }}" />
                            </div>
                        @endforeach
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary">Update Permissions</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
