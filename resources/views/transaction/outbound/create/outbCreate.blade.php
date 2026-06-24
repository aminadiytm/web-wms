@extends('layouts.user_type.auth')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #edf2f7;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .soft-card {
        border: 1px solid #edf2f7;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .soft-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding-top: 1.25rem;
        padding-bottom: 1rem;
    }

    .section-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }

    .form-label-modern {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.55rem;
    }

    .form-control,
    .form-select {
        min-height: 46px;
        border-radius: 12px;
        border: 1px solid #dbe3ec;
        box-shadow: none;
    }

    .btn-premium-soft {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.7rem 1.15rem;
        font-weight: 600;
    }

    .btn-light-soft {
        background: #fff;
        border: 1px solid #dbe3ec;
        color: #334155;
        border-radius: 12px;
        padding: 0.7rem 1.15rem;
        font-weight: 600;
    }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.8rem;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .detail-table-wrap {
        border: 1px solid #edf2f7;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    #detail-entry-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
    }

    #detail-entry-table tbody td {
        vertical-align: middle;
        border-color: #f1f5f9;
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
    }

    .detail-picker-cell {
        cursor: pointer;
        color: #475569;
    }

    .sticky-action-bar {
        position: sticky;
        bottom: 0;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(10px);
        border-top: 1px solid #edf2f7;
        padding: 1rem 1.5rem;
        z-index: 5;
    }
</style>

<div class="container-fluid py-4">
    <form
        action="@if($isEdit)
                    {{ route('transaction.outbUpdate', $outbound->outb_id) }}
                @elseif($isConfirm)
                    {{ route('transaction.outbConfirmUpdate', $outbound->outb_id) }}
                @else
                    {{ route('transaction.outbStore') }}
                @endif"
        method="POST"
        id="outbound-form"
        data-mode="{{ $mode }}"
    >
        @csrf

        @if(!$isCreate)
            @method('PUT')
        @endif

        <input type="hidden" id="outb_id" value="{{ $outbound->outb_id ?? '' }}">

        <div class="row mb-4">
            <div class="col-12">
                <div class="page-hero p-4 mx-2 mx-md-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <div class="info-chip mb-3">
                                <i class="fas fa-truck"></i>
                                Outbound Transaction
                            </div>

                            <h3 class="mb-1 fw-bold text-dark">{{ $title }}</h3>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('transaction.outbIndex') }}" class="btn btn-light-soft">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- HEADER --}}
        <div class="row">
            <div class="col-12">
                <div class="card soft-card mb-4 mx-2 mx-md-4">
                    <div class="card-header">
                        <h5 class="section-title">Outbound Information</h5>
                    </div>

                    <div class="card-body pt-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Warehouse</label>
                                <select name="wh" id="wh" class="form-select" required>
                                    <option value="" disabled selected hidden></option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Customer</label>
                                <input
                                    type="text"
                                    name="outb_customer"
                                    id="outb_customer"
                                    class="form-control"
                                    value="{{ old('outb_customer') }}"
                                    placeholder="Type customer name"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Status</label>
                                <input
                                    type="hidden"
                                    name="outb_stat"
                                    id="outb_stat"
                                    value="{{ $outbound->outb_stat ?? 'DRAFT' }}"
                                >

                                <input
                                    type="text"
                                    id="outb_stat_display"
                                    class="form-control"
                                    value="{{ $outbound->outb_stat ?? 'DRAFT' }}"
                                    disabled
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Shipped Date</label>
                                <input
                                    type="date"
                                    name="outb_shipped"
                                    id="outb_shipped"
                                    class="form-control"
                                    value="{{ old('outb_shipped', now()->format('Y-m-d')) }}"
                                    required
                                >
                            </div>

                            @if($isCreate)
                                <div class="col-md-4">
                                    <label class="form-label form-label-modern">Routing Approval</label>

                                    @php
                                        $defaultRoute = $approvalRoutes->firstWhere('is_default', true)
                                            ?? $approvalRoutes->first();
                                    @endphp

                                    <select class="form-select" disabled>
                                        @foreach($approvalRoutes as $route)
                                            <option
                                                value="{{ $route->approval_route_id }}"
                                                @selected($defaultRoute && $route->approval_route_id == $defaultRoute->approval_route_id)
                                            >
                                                {{ $route->route_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input
                                        type="hidden"
                                        name="approval_route_id"
                                        id="approval_route_id"
                                        value="{{ $defaultRoute->approval_route_id ?? '' }}"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL --}}
        <div class="row">
            <div class="col-12">
                <div class="card soft-card mb-4 mx-2 mx-md-4">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <h5 class="section-title">Outbound Detail</h5>
                            </div>

                            <button type="button" class="btn btn-premium-soft" id="btn-detail">
                                <i class="fas fa-plus me-2"></i>
                                Add Detail Row
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="detail-table-wrap">
                            <div class="table-responsive">
                                <table class="table align-items-center" id="detail-entry-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 70px;">No</th>
                                            <th style="min-width: 200px;">Product</th>
                                            <th style="min-width: 240px;">Product Description</th>
                                            <th style="min-width: 200px;">Location</th>
                                            <th class="text-end" style="min-width: 140px;">Available</th>
                                            <th class="text-end" style="min-width: 140px;">Qty Request</th>
                                            <th class="text-end" style="min-width: 140px;">Qty Picked</th>
                                            <th class="text-end" style="min-width: 140px;">Outstanding</th>
                                            <th class="text-center" style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-table-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="sticky-action-bar d-flex justify-content-end gap-2">
                        <a href="{{ route('transaction.outbIndex') }}" class="btn btn-light-soft">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-premium-soft">
                            <i class="fas fa-save me-2"></i>
                            {{ $isConfirm ? 'Confirm Outbound' : ($isEdit ? 'Update Outbound' : 'Save Outbound') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="selectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="selectModalTitle">Select Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <select id="selectModalOption" class="form-select"></select>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnSelectModal" class="btn btn-primary">
                    Select
                </button>
            </div>
        </div>
    </div>
</div>

@include('transaction.outbound.create.ext_outbCreate')
@endsection