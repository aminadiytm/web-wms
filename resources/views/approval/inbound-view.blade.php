@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card mx-4">
        <div class="card-header">
            <h4>Inbound Approval - {{ $inbound->inb_code }}</h4>
            <span class="badge bg-warning">{{ $inbound->inb_stat }}</span>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>Warehouse</strong>
                    <p>{{ $inbound->warehouse->wh_name ?? '-' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Supplier</strong>
                    <p>{{ $inbound->inb_supplier }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Receive Date</strong>
                    <p>{{ $inbound->inb_rcv }}</p>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th class="text-end">Qty Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inbound->inbounddets as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->product->prd_name ?? '-' }}</td>
                            <td>{{ $detail->product->prd_desc ?? '-' }}</td>
                            <td>{{ $detail->location->loc_desc ?? '-' }}</td>
                            <td class="text-end">{{ $detail->qty_order }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="alert alert-info mt-4">
                Untuk approve atau not approve, silakan klik link yang dikirim melalui WhatsApp.
            </div>
        </div>
    </div>
</div>
@endsection