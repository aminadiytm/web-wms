@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card mx-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $title }}</h5>
            <p class="text-sm text-secondary mb-0">
                View current stock by product, warehouse, and location
            </p>
        </div>

        <div class="card-body">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 nowrap head-table premium-table" id="stock_tbl" style="width:100%;font-size:0.7em;">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center text-xs font-weight-bolder">Product Code</th>
                            <th class="text-xs font-weight-bolder">Product Name</th>
                            <th class="text-xs font-weight-bolder">Warehouse</th>
                            <th class="text-xs font-weight-bolder">Location</th>
                            <th class="text-end text-xs font-weight-bolder">Qty On Hand</th>
                            <th class="text-end text-xs font-weight-bolder">Qty Reserved</th>
                            <th class="text-end text-xs font-weight-bolder">Available Qty</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('inventory.stock.ext_stock')
@endsection