<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserAccessController extends Controller
{
    public function index()
    {
        $title = 'User Access';
        $pageTitle = 'Security / User Access';

        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('security.user_access.index', compact(
            'title',
            'pageTitle',
            'roles'
        ));
    }

    public function list()
    {
        $query = User::with('roles')->orderBy('name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('role_name', function ($row) {
                return $row->roles->pluck('name')->join(', ') ?: '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'roles' => 'nullable|array',
        ]);

        $user = User::findOrFail($id);

        $user->syncRoles($request->roles ?? []);

        return response()->json([
            'success' => true,
            'message' => 'User access berhasil diupdate',
        ]);
    }
}