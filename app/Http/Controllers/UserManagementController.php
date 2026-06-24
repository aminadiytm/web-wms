<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $title = 'Users';
        $pageTitle = 'User List';
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('title', 'pageTitle', 'roles'));
    }

    public function userList()
    {
        $users = User::query()->latest();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_name', function ($row) {
                return $row->roles->pluck('name')->join(', ') ?: '-';
            })
            ->addColumn('crea', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '-';
            })
            ->addColumn('up', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d M Y') : '-';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $userId = $request->user_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($userId) {
            $user = User::findOrFail($userId);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $user->syncRoles($request->roles ?? []);
            
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diupdate.'
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dibuat.'
        ]);
    }

    public function getEdit($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values(),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset.'
        ]);
    }

    public function delete($id)
    {
        if (Auth::user()->id == $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak bisa menghapus akun sendiri.'
            ], 422);
        }

        User::findOrFail($id)->delete();

        return response()->json([
            'title' => 'Deleted!',
            'status' => 'success',
            'message' => 'User berhasil dihapus.'
        ]);
    }
}