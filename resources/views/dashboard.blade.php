@extends('layouts.user_type.auth')

@section('content')
<style>
    .dash-hero {
        background: linear-gradient(135deg, #111827 0%, #312e81 50%, #7c3aed 100%);
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        box-shadow: 0 18px 45px rgba(49, 46, 129, 0.28);
        overflow: hidden;
        position: relative;
    }

    .dash-hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,0.11);
        top: -120px;
        right: -80px;
    }

    .dash-card {
        border: 1px solid #edf2f7;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
    }

    .metric-card {
        position: relative;
        border: 1px solid #edf2f7;
        border-radius: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        padding: 1.2rem;
        min-height: 138px;
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f3ff;
        color: #7c3aed;
        font-size: 1.25rem;
    }

    .metric-label {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .35rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0;
    }

    .metric-caption {
        color: #94a3b8;
        font-size: .8rem;
        margin-top: .5rem;
        margin-bottom: 0;
    }

    .section-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0;
    }

    .section-subtitle {
        font-size: .84rem;
        color: #64748b;
        margin-bottom: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .75rem .9rem;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        min-width: 145px;
    }

    .status-pill strong {
        color: #0f172a;
        font-size: 1rem;
    }

    .status-pill span {
        color: #64748b;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #7c3aed;
        box-shadow: 0 0 0 5px rgba(124, 58, 237, .12);
    }

    .premium-table th {
        color: #64748b;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 1px solid #e2e8f0;
    }

    .premium-table td {
        color: #334155;
        font-size: .84rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .code-badge {
        display: inline-flex;
        padding: .35rem .6rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        font-size: .76rem;
    }

    .stock-row {
        padding: .85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .stock-row:last-child {
        border-bottom: none;
    }

    .stock-bar {
        height: 8px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .stock-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #7c3aed, #06b6d4);
        border-radius: 999px;
        transition: width .35s ease;
    }

    .loading-skeleton {
        height: 18px;
        border-radius: 8px;
        background: linear-gradient(90deg, #f1f5f9, #e2e8f0, #f1f5f9);
        background-size: 200% 100%;
        animation: shimmer 1.2s infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="container-fluid py-4">

    {{-- HERO --}}
    <div class="dash-hero mb-4 mx-2 mx-md-4">
        <div class="position-relative z-index-2">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <p class="mb-2 text-white-50 fw-bold text-uppercase" style="font-size:.75rem;letter-spacing:.08em;">
                        Warehouse Management System
                    </p>
                    <h3 class="mb-1 text-white fw-bold">Operational Dashboard</h3>
                    <p class="mb-0 text-white-50">
                        Monitor inbound, outbound, approval, and stock availability in real time.
                    </p>
                </div>

                <div class="text-lg-end">
                    <span class="badge bg-white text-dark px-3 py-2">
                        <i class="fas fa-sync-alt me-2"></i>
                        Live Monitoring
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- METRICS --}}
    <div class="row g-4 mb-4 mx-0 mx-md-2">
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label">Inbound Today</p>
                        <h3 class="metric-value" id="card-inbound-today">0</h3>
                        <p class="metric-caption">New inbound documents</p>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-dolly-flatbed"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label">Outbound Today</p>
                        <h3 class="metric-value" id="card-outbound-today">0</h3>
                        <p class="metric-caption">New outbound documents</p>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label">Pending Approval</p>
                        <h3 class="metric-value" id="card-pending-approval">0</h3>
                        <p class="metric-caption">Waiting manager action</p>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label">Low Stock</p>
                        <h3 class="metric-value" id="card-low-stock">0</h3>
                        <p class="metric-caption">Need stock attention</p>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS --}}
    <div class="row g-4 mb-4 mx-0 mx-md-2">
        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="section-title">Inbound Status</h6>
                    <p class="section-subtitle">Current inbound document state</p>
                </div>
                <div class="card-body">
                    <div id="inbound-status-wrapper" class="d-flex flex-wrap gap-2">
                        <div class="loading-skeleton w-100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="section-title">Outbound Status</h6>
                    <p class="section-subtitle">Current outbound document state</p>
                </div>
                <div class="card-body">
                    <div id="outbound-status-wrapper" class="d-flex flex-wrap gap-2">
                        <div class="loading-skeleton w-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT TRANSACTIONS --}}
    <div class="row g-4 mb-4 mx-0 mx-md-2">
        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="section-title">Recent Inbound</h6>
                    <p class="section-subtitle">Latest inbound activity</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table premium-table mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-inbound-body">
                                <tr><td colspan="3"><div class="loading-skeleton w-100"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="section-title">Recent Outbound</h6>
                    <p class="section-subtitle">Latest outbound activity</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table premium-table mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-outbound-body">
                                <tr><td colspan="3"><div class="loading-skeleton w-100"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STOCK --}}
    <div class="row g-4 mx-0 mx-md-2">
        <div class="col-12">
            <div class="card dash-card">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="section-title">Stock by Warehouse</h6>
                            <p class="section-subtitle">Available stock summary by warehouse</p>
                        </div>
                        <a href="{{ route('inventory.stockIndex') }}" class="btn btn-sm btn-outline-primary">
                            View Stock
                        </a>
                    </div>
                </div>

                <div class="card-body" id="stock-warehouse-body">
                    <div class="loading-skeleton w-100"></div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
$(function () {
    loadDashboard();
});

function loadDashboard()
{
    $.ajax({
        url: '{{ route('dashboard.summary') }}',
        type: 'GET',
        success: function (res) {
            $('#card-inbound-today').text(res.cards.inbound_today ?? 0);
            $('#card-outbound-today').text(res.cards.outbound_today ?? 0);
            $('#card-pending-approval').text(res.cards.pending_approval ?? 0);
            $('#card-low-stock').text(res.cards.low_stock ?? 0);

            renderStatus('#inbound-status-wrapper', res.inbound_status);
            renderStatus('#outbound-status-wrapper', res.outbound_status);

            renderRecentInbound(res.recent_inbound);
            renderRecentOutbound(res.recent_outbound);
            renderStockWarehouse(res.stock_by_warehouse);
        }
    });
}

function renderStatus(wrapper, data)
{
    let html = '';

    if (!data || Object.keys(data).length === 0) {
        html = '<span class="text-muted">No data</span>';
    } else {
        Object.entries(data).forEach(([status, total]) => {
            html += `
                <div class="status-pill">
                    <div>
                        <span>${status}</span><br>
                        <strong>${total}</strong>
                    </div>
                    <div class="status-dot"></div>
                </div>
            `;
        });
    }

    $(wrapper).html(html);
}

function renderRecentInbound(rows)
{
    let html = '';

    if (!rows || rows.length === 0) {
        html = `<tr><td colspan="3" class="text-center text-muted">No inbound data</td></tr>`;
    } else {
        rows.forEach(row => {
            html += `
                <tr>
                    <td><span class="code-badge">${row.inb_code}</span></td>
                    <td>${row.inb_supplier ?? '-'}</td>
                    <td>${statusBadge(row.inb_stat)}</td>
                </tr>
            `;
        });
    }

    $('#recent-inbound-body').html(html);
}

function renderRecentOutbound(rows)
{
    let html = '';

    if (!rows || rows.length === 0) {
        html = `<tr><td colspan="3" class="text-center text-muted">No outbound data</td></tr>`;
    } else {
        rows.forEach(row => {
            html += `
                <tr>
                    <td><span class="code-badge">${row.outb_code}</span></td>
                    <td>${row.outb_customer ?? '-'}</td>
                    <td>${statusBadge(row.outb_stat)}</td>
                </tr>
            `;
        });
    }

    $('#recent-outbound-body').html(html);
}

function renderStockWarehouse(rows)
{
    let html = '';

    if (!rows || rows.length === 0) {
        html = `<p class="text-center text-muted mb-0">No stock data</p>`;
    } else {
        const maxQty = Math.max(...rows.map(row => Number(row.available_qty ?? 0)), 1);

        rows.forEach(row => {
            const qty = Number(row.available_qty ?? 0);
            const percent = Math.min((qty / maxQty) * 100, 100);

            html += `
                <div class="stock-row">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong class="text-dark">${row.wh_name}</strong>
                            <div class="text-muted text-xs">Available stock</div>
                        </div>
                        <strong>${qty.toLocaleString()}</strong>
                    </div>
                    <div class="stock-bar">
                        <div class="stock-bar-fill" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        });
    }

    $('#stock-warehouse-body').html(html);
}

function statusBadge(status)
{
    const s = (status || 'DRAFT').toUpperCase();

    let cls = 'bg-gradient-secondary';

    if (['ISSUED'].includes(s)) cls = 'bg-gradient-info';
    if (['PARTIAL'].includes(s)) cls = 'bg-gradient-warning';
    if (['RECEIVED', 'SHIPPED', 'APPROVED'].includes(s)) cls = 'bg-gradient-success';
    if (['CANCEL', 'REJECTED'].includes(s)) cls = 'bg-gradient-danger';

    return `<span class="badge badge-sm ${cls}">${s}</span>`;
}
</script>
@endpush
@endsection