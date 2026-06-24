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
                        @if (MenuPermissionHelper::canCreate('masterdata.locIndex'))
                            <button type="button" 
                                class="btn btn-premium" 
                                data-bs-toggle="modal" 
                                data-bs-target="#locModal">
                                <i class="fas fa-plus me-2"></i>
                                New Location
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table" id="loc_tbl" style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Actions</th>
                                    <th class="text-center text-xs font-weight-bolder">
                                        No
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Location Code
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Location Description
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Warehouse
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Status
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
<div class="modal fade premium-modal" id="locModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Create Location</h5>
                    <small class="text-muted">Add a new location to your inventory master.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="locForm">
                @csrf

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse</label>
                                <div class="dm-select">
                                    <select class="form-control" name="locwh" id="loc_wh">
                                      <option value="">Select Warehouse</option>
                                      @foreach ($warehouses as $wh)
                                        <option value="{{ $wh->wh_id }}">
                                          {{ $wh->wh_name }}
                                        </option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Location Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="loccd"
                                        id="loc_code"
                                        placeholder="Enter location code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Location Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="locdesc"
                                        id="loc_desc"
                                        placeholder="Enter location description">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label d-block">Is Active</label>
                            
                                <label class="switch">
                                    <input type="checkbox" name="act" id="is_act" value="1" checked>
                                    <span class="slider"></span>
                                </label>
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
<div class="modal fade premium-modal" id="e_locModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Create Location</h5>
                    <small class="text-muted">Update location to your inventory master.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="e_locForm">
                @csrf
                <input type="hidden" name="locid" id="e_loc_id">

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Warehouse</label>
                                <div class="dm-select">
                                    <select class="form-control" name="locwh" id="eloc_wh">
                                      <option value="">Select Warehouse</option>
                                      @foreach ($warehouses as $wh)
                                        <option value="{{ $wh->wh_id }}">
                                          {{ $wh->wh_name }}
                                        </option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Location Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="loccd"
                                        id="eloc_code"
                                        placeholder="Enter location code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Location Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="locdesc"
                                        id="eloc_desc"
                                        placeholder="Enter location description">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label d-block">Is Active</label>
                            
                                <label class="switch">
                                    <input type="checkbox" name="act" id="eis_act">
                                    <span class="slider"></span>
                                </label>
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


@include('masterdata.location.ext_loc')
@endsection