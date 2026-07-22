<?php

namespace App\Http\Controllers\Security;

use App\Helpers\MenuPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\MenuList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RolePermissionController extends Controller
{
    public function index()
    {
        $title = 'Role Permission';
        $pageTitle = 'Security / Role Permission';

        $menus = MenuList::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn ($menu) => $menu->menu_group ?: 'Main');

        return view('security.role.index', compact(
            'title',
            'pageTitle',
            'menus'
        ));
    }

    public function list()
    {
        $query = Role::where('guard_name', 'web')
            ->withCount('permissions')
            ->orderBy('name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('total_permission', fn ($row) => $row->permissions_count)
            ->addColumn('action', function ($row) {
                $canEdit   = MenuPermissionHelper::canEdit('security.roleIndex');
                $canDelete = MenuPermissionHelper::canDelete('security.roleIndex');

                $action = '';
            
                if ($canEdit) {
                    $action .= '
                        <button
                            type="button"
                            class="action-icon action-icon-edit btn-edit"
                            data-id="'.$row->id.'"
                            title="Edit"
                        >
                            <i class="fas fa-pen"></i>
                        </button>
                    ';
                }
            
                if ($canDelete) {
                    $action .= '
                        <button
                            type="button"
                            class="action-icon action-icon-delete btn-delete"
                            data-id="'.$row->id.'"
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
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => strtoupper($request->role_name),
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($request->permissions ?? []);
        });

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil disimpan',
        ]);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->values(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);

            $role->update([
                'name' => strtoupper($request->role_name),
            ]);

            $role->syncPermissions($request->permissions ?? []);
        });

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'Role ADMIN tidak boleh dihapus',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus',
        ]);
    }
}