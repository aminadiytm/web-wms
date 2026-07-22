<header>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</header>

@extends('layouts.user_type.auth')

@section('content')
<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">All {{ $title }}</h5>
                        </div>
                        @php
                            use App\Helpers\MenuPermissionHelper;
                        @endphp
                        @if (MenuPermissionHelper::canCreate('admin.users.usrIndex'))
                            <button type="button"
                                class="btn btn-premium"
                                data-bs-toggle="modal"
                                data-bs-target="#userModal">
                                <i class="fas fa-plus me-2"></i>
                                New User
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table"
                            id="user_tbl"
                            style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Actions</th>
                                    <th class="text-center text-xs font-weight-bolder">No</th>
                                    <th class="text-xs font-weight-bolder">Name</th>
                                    <th class="text-xs font-weight-bolder">Email</th>
                                    <th class="text-xs font-weight-bolder">Role</th>
                                    <th class="text-xs font-weight-bolder">Created At</th>
                                    <th class="text-xs font-weight-bolder">Updated At</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade premium-modal" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Admin</p>
                    <h5 class="modal-title mb-0">Create User</h5>
                    <small class="text-muted">Add a new user account.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="userForm">
                @csrf

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="premium-input" name="name" id="user_name" placeholder="Enter name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Email</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="premium-input" name="email" id="user_email" placeholder="Enter email">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Password</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="premium-input" name="password" id="user_password" placeholder="Enter password">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Role</label>
                                <select class="premium-input user-role-select" name="roles[]" id="user_roles" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button type="button" class="btn btn-cancel-premium" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-save-premium">
                        <i class="fas fa-save me-2"></i>
                        Save User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade premium-modal" id="e_userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Admin</p>
                    <h5 class="modal-title mb-0">Edit User</h5>
                    <small class="text-muted">Update user information.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="e_userForm">
                @csrf
                <input type="hidden" name="user_id" id="e_user_id">

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="premium-input" name="name" id="e_user_name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Email</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="premium-input" name="email" id="e_user_email">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Role</label>
                                <select class="premium-input user-role-select" name="roles[]" id="e_user_roles" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button type="button" class="btn btn-cancel-premium" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-save-premium">
                        <i class="fas fa-save me-2"></i>
                        Update User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- RESET PASSWORD MODAL --}}
<div class="modal fade premium-modal" id="resetUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Admin</p>
                    <h5 class="modal-title mb-0">Reset Password</h5>
                    <small class="text-muted">Set new password for this user.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="resetUserForm">
                @csrf
                <input type="hidden" name="user_id" id="reset_user_id">

                <div class="modal-body premium-modal-body">
                    <div class="premium-form-group">
                        <label class="premium-label">New Password</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="premium-input" name="password" id="reset_password" placeholder="New Password">
                        </div>
                    </div>

                    <div class="premium-form-group">
                        <label class="premium-label">Confirm Password</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="premium-input" name="password_confirmation" id="reset_password_confirmation" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button type="button" class="btn btn-cancel-premium" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-save-premium">
                        Reset Password
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@include('admin.users.ext_user')
@endsection