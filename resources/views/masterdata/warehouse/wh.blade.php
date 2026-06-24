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
                        @if (MenuPermissionHelper::canCreate('masterdata.whIndex'))
                            <button type="button" 
                                class="btn btn-premium" 
                                data-bs-toggle="modal" 
                                data-bs-target="#whModal">
                                <i class="fas fa-plus me-2"></i>
                                New Warehouse
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table" id="wh_tbl" style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Actions</th>
                                    <th class="text-center text-xs font-weight-bolder">
                                        No
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Warehouse Code
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Warehouse Name
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Warehouse Address
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

{{-- ADD MODAL --}}
<div class="modal fade premium-modal" id="whModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Create Warehouse</h5>
                    <small class="text-muted">Add a new Warehouse.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="whForm">
                @csrf

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whcd"
                                        id="wh_code"
                                        placeholder="Enter Warehouse code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-warehouse"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whnm"
                                        id="wh_name"
                                        placeholder="Enter warehouse name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Address</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whaddr"
                                        id="wh_address"
                                        placeholder="Enter warehouse address">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whdesc"
                                        id="wh_desc"
                                        placeholder="Enter description">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button
                        type="button"
                        class="btn btn-cancel-premium"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-save-premium"
                        id="btn-save">
                        <i class="fas fa-save me-2"></i>
                        Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade premium-modal" id="e_whModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Edit Warehouse</h5>
                    <small class="text-muted">Update a new Warehouse.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="e_whForm">
                @csrf
                <input type="hidden" name="whid" id="ewh_id">

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whcd"
                                        id="ewh_code"
                                        placeholder="Enter Warehouse code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-warehouse"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whnm"
                                        id="ewh_name"
                                        placeholder="Enter warehouse name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse Address</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whaddr"
                                        id="ewh_address"
                                        placeholder="Enter warehouse address">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="whdesc"
                                        id="ewh_desc"
                                        placeholder="Enter description">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button
                        type="button"
                        class="btn btn-cancel-premium"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-save-premium"
                        id="ebtn-save">
                        <i class="fas fa-save me-2"></i>
                        Update Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


@include('masterdata.warehouse.ext_wh')
@endsection
