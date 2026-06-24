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
                        @if(MenuPermissionHelper::canCreate('transaction.outbIndex'))
                            <a href="{{ route('transaction.outbCreate') }}" class="btn btn-premium">
                                <i class="fas fa-plus me-2"></i>
                                New Outbound
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table"
                               id="outb_tbl"
                               style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Actions</th>
                                    <th class="text-center text-xs font-weight-bolder">No</th>
                                    <th class="text-xs font-weight-bolder">Outbound Code</th>
                                    <th class="text-xs font-weight-bolder">Warehouse</th>
                                    <th class="text-xs font-weight-bolder">Customer</th>
                                    <th class="text-xs font-weight-bolder">Status</th>
                                    <th class="text-xs font-weight-bolder">Shipped Date</th>
                                    <th class="text-xs font-weight-bolder">Created By</th>
                                    <th class="text-xs font-weight-bolder">Updated By</th>
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

    {{-- DETAIL PANEL --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Outbound Detail</h5>
                    <p class="text-sm text-secondary mb-0" id="detail-subtitle">
                        Click an outbound row to show item details
                    </p>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table"
                               id="outb_detail_tbl"
                               style="width:100%;font-size:0.75em;">
                            <thead>
                                <tr>
                                    <th class="text-center text-xs font-weight-bolder">No</th>
                                    <th class="text-xs font-weight-bolder">Product</th>
                                    <th class="text-xs font-weight-bolder">Location</th>
                                    <th class="text-end text-xs font-weight-bolder">Qty Request</th>
                                    <th class="text-end text-xs font-weight-bolder">Qty Picked</th>
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

@include('transaction.outbound.ext_outbound')
@endsection