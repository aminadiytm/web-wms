<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function index() {
        $title = 'Warehouse';
        $pageTitle = 'Master Data / Warehouse';

        return view('masterdata.warehouse.wh', compact('title','pageTitle'));
    }

    public function list() {
        $get = Warehouse::select([
            'wh_id as id',
            'wh_code as code',
            'wh_name as name',
            'wh_addr as addr',
            'wh_desc as desc',
            'wh_add_by as add',
            'wh_upd_by as upd',
            'created_at as crea',
            'updated_at as up',
        ])->orderBy('wh_code', 'asc');

        return DataTables::of($get)
                ->addIndexColumn()
                ->toJson();
    }

    public function store(Request $request) {
        // Validate
        $validate = $request->validate(
            [
                'whcd'   => $request->whid ? 'required|string|max:100|unique:warehouses,wh_code,'.$request->whid.',wh_id' : 'required|string|unique:warehouses,wh_code',
                'whnm'   => 'required',
                'whaddr' => 'required',
                'whdesc' => 'required',
            ],
            [
                'whcd'   => 'Warehouse Code Required!',
                'whnm'   => 'Warehouse Code Required!',
                'whaddr' => 'Warehouse Code Required!',
                'whdesc' => 'Warehouse Code Required!'
            ]
        );
        
        // Store
        $data = [
            'wh_code' => $validate['whcd'],
            'wh_name' => $validate['whnm'],
            'wh_addr' => $validate['whaddr'],
            'wh_desc' => $validate['whdesc'],
        ];

        if($request->whid) {
            $upd = Warehouse::findorfail($request->whid);
            $data['wh_upd_by'] = Auth::user()?->name;

            $upd->update($data);

            $message = 'Warehouse Successfully Updated!';

        } else {
            $data['wh_add_by'] = Auth::user()?->name;

            Warehouse::create($data);

            $message = 'Warehouse Successfully Created!';
        }


        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);

    }

    public function getdata($id) {
        $get = Warehouse::where('wh_id', $id)->firstorfail();

        $data = [
        'wh_id'   => $get->wh_id,
        'wh_code' => $get->wh_code,
        'wh_name' => $get->wh_name,
        'wh_addr' => $get->wh_addr,
        'wh_desc' => $get->wh_desc
        ];

        return response()->json($data);
    }

    public function delete($id)
    {
        $wh = Warehouse::findorfail($id);

        if($wh->locations()->exists()) {
            return response()->json([
                'title' => 'Errors!',
                'status' => 'error',
                'message'=> 'Warehouse Still Used In Locations!'
            ]);
        }
        
        $wh->delete();

        return response()->json([
            'title' => 'Deleted!',
            'status' => 'success',
            'message'=> 'Data Successfully Deleted!'
        ]);

    }

}
