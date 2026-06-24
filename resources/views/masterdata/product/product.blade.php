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
                        @if (MenuPermissionHelper::canCreate('masterdata.prdIndex'))
                            <button type="button" 
                                class="btn btn-premium" 
                                data-bs-toggle="modal" 
                                data-bs-target="#prdModal">
                                <i class="fas fa-plus me-2"></i>
                                New Product
                            </button>                            
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table" id="prd_tbl" style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5px">Actions</th>
                                    <th class="text-center text-xs font-weight-bolder">
                                        No
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Product Code
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Product Name
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Product Unit
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Category
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Min Stock
                                    </th>
                                    <th class="text-xs font-weight-bolder">
                                        Product Description
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
<div class="modal fade premium-modal" id="prdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Create Product</h5>
                    <small class="text-muted">Add a new product to your inventory master.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="prdForm">
                @csrf

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdcd"
                                        id="prd_code"
                                        placeholder="Enter product code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdnm"
                                        id="prd_name"
                                        placeholder="Enter product name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Unit</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-cubes"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdunit"
                                        id="prd_unit"
                                        placeholder="Enter product unit">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Category</label>
                                <div class="dm-select">
                                    <select class="form-control" name="prdcat" id="prd_cat">
                                      <option value="">Select Category</option>
                                      @foreach ($categories as $cat)
                                        <option value="{{ $cat->cat_id }}">
                                          {{ $cat->cat_name }}
                                        </option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Min Stock</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-sort-numeric-up"></i>
                                    </span>
                                    <input
                                        type="number"
                                        class="premium-input"
                                        name="prdstck"
                                        id="prd_stck"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prddesc"
                                        id="prd_desc"
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
<div class="modal fade premium-modal" id="e_prdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Edit Product</h5>
                    <small class="text-muted">Update product information.</small>
                </div>
                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="e_prdForm">
                @csrf
                <input type="hidden" name="prd_id" id="e_prd_id">

                <div class="modal-body premium-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Code</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdcd"
                                        id="eprd_code"
                                        placeholder="Enter product code">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Name</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdnm"
                                        id="eprd_name"
                                        placeholder="Enter product name">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Unit</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-cubes"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prdunit"
                                        id="eprd_unit"
                                        placeholder="Enter product unit">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Category</label>
                                <div class="dm-select">
                                    {{-- <span class="premium-input-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </span> --}}
                                    {{-- <input
                                        type="text"
                                        class="premium-input"
                                        name="prdcat"
                                        id="eprd_cat"
                                        placeholder="Enter category"> --}}
                                      <select class="form-control" name="prdcat" id="eprd_cat">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                          <option value="{{$cat->cat_id}}">{{$cat->cat_name}}</option>
                                        @endforeach
                                      </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Min Stock</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-sort-numeric-up"></i>
                                    </span>
                                    <input
                                        type="number"
                                        class="premium-input"
                                        name="prdstck"
                                        id="eprd_stck"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="premium-form-group">
                                <label class="premium-label">Product Description</label>
                                <div class="premium-input-wrap">
                                    <span class="premium-input-icon">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="premium-input"
                                        name="prddesc"
                                        id="eprd_desc"
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

@include('masterdata.product.ext_product')
@endsection