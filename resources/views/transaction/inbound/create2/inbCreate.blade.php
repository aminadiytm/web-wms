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

    .section-subtitle {
        color: #64748b;
        font-size: 0.92rem;
        margin-bottom: 0;
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

    .form-control:focus,
    .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.10);
    }

    .btn-premium-soft {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.7rem 1.15rem;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.18);
    }

    .btn-premium-soft:hover {
        color: #fff;
        opacity: 0.96;
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

    #detail-entry-table {
        margin-bottom: 0;
    }

    #detail-entry-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
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

    .sticky-action-bar {
        position: sticky;
        bottom: 0;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(10px);
        border-top: 1px solid #edf2f7;
        padding: 1rem 1.5rem;
        z-index: 5;
    }

    .empty-detail-note {
        color: #64748b;
        font-size: 0.88rem;
    }
</style>

<div class="container-fluid py-4">
    <form action="@if($isEdit)
                {{ route('transaction.inbUpdate', $inbound->inb_id) }}
            @elseif($isReceive)
                {{ route('transaction.inbReceiveUpdate', $inbound->inb_id) }}
            @else
                {{ route('transaction.inbStore') }}
            @endif"
            
            method="POST" id="inbound-form" data-mode="{{ $mode }}">
        @csrf

        @if(!$isCreate)
            @method('PUT')
        @endif

        <input
            type="hidden"
            id="inb_id"
            value="{{ $inbound->inb_id ?? '' }}"
        >
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-hero p-4 mx-2 mx-md-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <div class="info-chip mb-3">
                                <i class="fas fa-box-open"></i>
                                Inbound Transaction
                            </div>
                            <h3 class="mb-1 fw-bold text-dark">{{ $title }}</h3>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('transaction.inbIndex') }}" class="btn btn-light-soft">
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
                        <h5 class="section-title">Inbound Information</h5>
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
                                <label class="form-label form-label-modern">Supplier</label>
                                <input
                                    type="text"
                                    name="inb_supplier"
                                    id="inb_supplier"
                                    class="form-control"
                                    value="{{ old('inb_supplier') }}"
                                    placeholder="Type supplier name"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Status</label>
                                <input
                                    type="hidden"
                                    name="inb_stat"
                                    id="inb_stat"
                                    value="{{ $inbound->inb_stat ?? 'DRAFT' }}"
                                >

                                <input
                                    type="text"
                                    id="inb_stat_display"
                                    class="form-control"
                                    value="{{ $inbound->inb_stat ?? 'DRAFT' }}"
                                    disabled
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Receive Date</label>
                                <input
                                    type="date"
                                    name="inb_rcv"
                                    id="inb_rcv"
                                    class="form-control"
                                    value="{{ old('inb_rcv', now()->format('Y-m-d')) }}"
                                >
                            </div>

                            @if($isCreate)
                            <div class="col-md-4">
                                <label class="form-label form-label-modern">Routing Approval</label>
                                <select name="approval_route_id" id="approval_route_id" class="form-select" required>
                                    @foreach($approvalRoutes as $route)
                                        <option value="{{ $route->approval_route_id }}" @selected($route->is_default)>
                                            {{ $route->route_name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <h5 class="section-title">Inbound Detail</h5>
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
                                            <th class="text-end" style="min-width: 140px;">Qty Order</th>
                                            <th class="text-end" style="min-width: 140px;">Qty Receive</th>
                                            <th class="text-end" style="min-width: 140px;">Outstanding</th>
                                            <th class="text-center" style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <small class="empty-detail-note">
                                Add at least one detail row before saving this inbound transaction.
                            </small>
                            <span class="info-chip">
                                <i class="fas fa-layer-group"></i>
                                Multi-row detail supported
                            </span>
                        </div>
                    </div>

                    <div class="sticky-action-bar d-flex justify-content-end gap-2">
                        <a href="{{ route('transaction.inbIndex') }}" class="btn btn-light-soft">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-premium-soft">
                            <i class="fas fa-save me-2"></i>
                            {{ $isReceive ? 'Receive Inbound' : ($isEdit ? 'Update Inbound' : 'Save Inbound') }}
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

@include('transaction.inbound.create2.ext_inbCreate')
@endsection