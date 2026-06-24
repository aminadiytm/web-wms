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
                            <h5 class="mb-0">All {{ $title }} </h5>
                        </div>
                        @php
                            use App\Helpers\MenuPermissionHelper;
                        @endphp
                        @if (MenuPermissionHelper::canCreate('masterdata.catIndex'))
                            <button type="button"
                                class="btn btn-premium"
                                data-bs-toggle="modal"
                                data-bs-target="#catModal">
                                <i class="fas fa-plus me-2"></i>
                                New Category
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table" id="cat_tbl" style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Action</th>
                                    <th class="text-center text-xs font-weight-bolder">
                                        No
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Name
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Description
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Created By
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Updated By
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Created At
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Updated At
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('masterdata.category.ext_category')
@endsection