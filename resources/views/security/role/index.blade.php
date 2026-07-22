@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card mx-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">{{ $title }}</h5>
                <p class="text-sm text-secondary mb-0">Manage role and menu permissions</p>
            </div>
            @php
                use App\Helpers\MenuPermissionHelper;
            @endphp
            @if (MenuPermissionHelper::canCreate('security.roleIndex'))
                <button type="button" class="btn btn-primary" id="btn-add">
                    <i class="fas fa-plus me-2"></i>
                    New Role
                </button>
            @endif

        </div>

        <div class="card-body">
            <table class="table align-items-center mb-0 nowrap head-table premium-table" id="role_tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th class="text-center" width="5px">Action</th>
                        <th class="text-center text-xs font-weight-bolder" width="5px">No</th>
                        <th class="text-xs font-weight-bolder">Role Name</th>
                        <th class="text-xs font-weight-bolder">Total Permission</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@include('security.role.modal')
@include('security.role.script')
@endsection