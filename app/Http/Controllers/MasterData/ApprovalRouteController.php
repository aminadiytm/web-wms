<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\MenuPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\ApprovalRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ApprovalRouteController extends Controller
{
    public function index()
    {
        $title = 'Routing Approval';
        $pageTitle = 'Master / Routing Approval';

        $users = User::orderBy('name')->get();

        return view('masterdata.approval_route.index', compact(
            'title',
            'pageTitle',
            'users'
        ));
    }

    public function list()
    {
        $query = ApprovalRoute::with('approver')
            ->orderBy('transaction_type')
            ->orderByDesc('is_default')
            ->orderBy('route_name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('approver_name', function ($row) {
                return $row->approver->name ?? '-';
            })
            ->editColumn('is_default', function ($row) {
                return $row->is_default
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                $canEdit   = MenuPermissionHelper::canEdit('masterdata.approvalRouteIndex');
                $canDelete = MenuPermissionHelper::canDelete('masterdata.approvalRouteIndex');
                $action = '';

                if($canEdit) {
                    $action .= '
                        <button
                            type="button"
                            class="action-icon action-icon-edit btn-edit"
                            data-id="' . $row->approval_route_id . '"
                            title="Edit"
                        >
                            <i class="fas fa-pen"></i>
                        </button>
                    ';
                }
                
                if($canDelete) {
                    $action .= '
                        <button
                            type="button"
                            class="action-icon action-icon-delete btn-delete"
                            data-id="' . $row->approval_route_id . '"
                            title="Delete"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }

                if ($action === '') {
                    return '
                        <span class="text-muted" title="No action permission">
                            <i class="fas fa-lock"></i>
                        </span>
                    ';
                }
            
                return '
                    <div class="action-group d-flex justify-content-center align-items-center gap-2">
                        '.$action.'
                    </div>
                ';

            })
            ->rawColumns(['is_default', 'is_active', 'action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_code' => 'required|string|max:50|unique:approval_routes,route_code',
            'route_name' => 'required|string|max:150',
            'transaction_type' => 'required|in:INBOUND,OUTBOUND',
            'approver_user_id' => 'required|exists:users,id',
            'is_default' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->boolean('is_default')) {
                ApprovalRoute::where('transaction_type', $request->transaction_type)
                    ->update(['is_default' => false]);
            }

            ApprovalRoute::create([
                'route_code' => strtoupper($request->route_code),
                'route_name' => $request->route_name,
                'transaction_type' => $request->transaction_type,
                'approver_user_id' => $request->approver_user_id,
                'is_default' => $request->is_default,
                'is_active' => $request->is_active,
                'add_by' => Auth::user()?->name,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Routing approval berhasil disimpan',
        ]);
    }

    public function edit($id)
    {
        return response()->json(
            ApprovalRoute::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'route_code' => 'required|string|max:50|unique:approval_routes,route_code,' . $id . ',approval_route_id',
            'route_name' => 'required|string|max:150',
            'transaction_type' => 'required|in:INBOUND,OUTBOUND',
            'approver_user_id' => 'required|exists:users,id',
            'is_default' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            $route = ApprovalRoute::findOrFail($id);

            if ($request->boolean('is_default')) {
                ApprovalRoute::where('transaction_type', $request->transaction_type)
                    ->where('approval_route_id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $route->update([
                'route_code' => strtoupper($request->route_code),
                'route_name' => $request->route_name,
                'transaction_type' => $request->transaction_type,
                'approver_user_id' => $request->approver_user_id,
                'is_default' => $request->is_default,
                'is_active' => $request->is_active,
                'upd_by' => Auth::user()?->name,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Routing approval berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        ApprovalRoute::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Routing approval berhasil dihapus',
        ]);
    }
}