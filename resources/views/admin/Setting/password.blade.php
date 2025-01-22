@extends('admin.includes.master')
@section('title', 'Change Password')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Password /</span> Password</h4>

    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body">
                    <form id="formAccountSettings" action="{{ route('admin.password-update',Auth::user()->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="currentPassword">Current Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                        class="form-control"
                                        type="password"
                                        name="current_password"
                                        id="currentPassword"
                                        placeholder="Enter Current Password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="newPassword">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                        class="form-control"
                                        type="password"
                                        name="password"
                                        id="newPassword"
                                        placeholder="Enter New Password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                        class="form-control"
                                        type="password"
                                        name="confirm_password"
                                        id="confirmPassword"
                                        placeholder="Confirm New Password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <h6>Password Requirements:</h6>
                            <ul class="ps-3 mb-0">
                              <li class="mb-1">Minimum 8 characters long - the more, the better</li>
                              <li class="mb-1">At least one lowercase character</li>
                              <li>At least one number, symbol, or whitespace character</li>
                            </ul>
                          </div>
                        <div>
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
              </div>
        </div>
    </div>
</div>
@endsection
