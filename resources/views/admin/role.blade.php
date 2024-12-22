@extends('admin.includes.master')
@section('head-area')
<style>
.role.rounded-circle{
    height: 120px;
    width: 120px;
}
</style>
@endsection
@section('content')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
        <li class="breadcrumb-item active">Roles</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <div class="card mb-3">
    <form action="{{ route('admin.role-store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-md-4">
                    <x-input-box name="role" required='true' />
                </div>
                <div class="col-md-4">
                    <x-input-box type="file" name="image" required='true' />
                </div>
                <div class="col-md-2 mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
  </div>
  <div class="container">
    <div class="row">
        @foreach ($roles as $role)
        <div class="col-md-3">
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                  <img src="{{ asset('storage/' . $role->media?->file ?? '') }}"  alt="{{$role->media?->file??''}}" class="role rounded-circle">
                  <h2>{{ ucfirst($role->name) }}</h2>

                  <p>
                    Users <span class="badge bg-success"> {{ $role->users->count()??0 }} </span>
                  </p>
                  <p>
                    Permissions <a href="{{ route('admin.permission-edit',$role->id) }}"><span class="badge bg-primary">{{ $role->permissions->count()??0 }}</span></a>
                  </p>
                </div>
              </div>
        </div>
        @endforeach
    </div>
  </div>
  @endsection
