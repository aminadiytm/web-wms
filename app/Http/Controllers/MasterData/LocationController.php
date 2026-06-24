<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index() {
        $title = 'Location';
        $pageTitle = 'Master Data / Location';
        $warehouses = Warehouse::orderBy('wh_name', 'asc')->get(['wh_id', 'wh_name']);

        return view('masterdata.location.loc', compact('title', 'pageTitle', 'warehouses'));
    }

    public function list() {
        $loc = Location::join('warehouses as wh', 'wh.wh_id', '=', 'locations.ref_wh_id')
        ->select([
            'loc_id as id',
            'loc_code as code',
            'loc_desc as locdesc',
            'wh.wh_name as locwh',
            'loc_act as act',
            'loc_add_by as add',
            'loc_upd_by as upd',
            'locations.created_at as crea',
            'locations.updated_at as up'
        ])->orderBy('loc_code', 'asc');

        return DataTables::of($loc)
                ->addIndexColumn()
                ->toJson();
    }

    public function store(Request $request) {
        $validate = $request->validate(
            [
                'loccd' => $request->locid ? 'required|string|max:100|unique:locations,loc_code,'.$request->locid.',loc_id' : 'required|string|max:100|unique:locations,loc_code',
                'locwh' => 'required',
                'locdesc' => 'required',
            ],
            [
                'loccd' => 'Location Code Required!',
                'locwh' => 'Warehouse Required!',
                'locdesc' => 'Location Description Required!',
            ]
        );

        $data = [
            'loc_code'      => $validate['loccd'],
            'ref_wh_id'     => $validate['locwh'],
            'loc_desc'      => $validate['locdesc'],
            'loc_act'       => $request->act ? true : false,
        ];

        if($request->locid) {
            $upd = Location::findorfail($request->locid);
            $data['loc_upd_by'] = Auth::user()?->name;
            $upd->update($data);

            $message = 'Location Successfully Updated!';
        } else {
            $data['loc_add_by'] = Auth::user()?->name;
            Location::create($data);

            $message = 'Location Successfully Created!';

        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function getdata($id) {
        $get = Location::where('loc_id', $id)
                ->join('warehouses as wh', 'wh.wh_id', '=', 'locations.ref_wh_id')
                ->firstorfail();

        $data = [
            'loc_id'        => $get->loc_id,
            'loc_code'      => $get->loc_code,
            'loc_desc'      => $get->loc_desc,
            'ref_wh'        => $get->ref_wh_id,
            'loc_act'       => $get->loc_act,
        ];

        return response()->json($data);
    }

    public function delete($id)
    {
        $prd = Location::findorfail($id);

        if($prd->stocks()->exists()) {
            return response()->json([
                'title' => 'Errors!',
                'status' => 'error',
                'message'=> 'Location Still Used In Stocks!'
            ]);
        }
        
        $prd->delete();

        return response()->json([
            'title' => 'Deleted!',
            'status' => 'success',
            'message'=> 'Data Successfully Deleted!'
        ]);

    }
}
