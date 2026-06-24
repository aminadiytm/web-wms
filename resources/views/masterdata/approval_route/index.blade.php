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
                        <button type="button"
                            class="btn btn-premium"
                            id="btn-add">
                            <i class="fas fa-plus me-2"></i>
                            New Routing
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 nowrap head-table premium-table" id="approval_route_tbl" style="width:100%;font-size:0.7em;">
                            <thead>
                                <tr>
                                    <th class="text-center">Action</th>
                                    <th>No</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Transaction</th>
                                    <th>Approver</th>
                                    <th>Default</th>
                                    <th>Active</th>
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

@include('masterdata.approval_route.modal')
@include('masterdata.approval_route.script')
@endsection