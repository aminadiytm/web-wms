<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupportController extends Controller
{
    function support_wh() {
        $data = Warehouse::get();

        return response()->json($data);
    }

    function support_prd() {
        $data = Product::get();

        return response()->json($data);
    }

    function support_loc($whid) {
        
        $data = Location::where('ref_wh_id', $whid)->get();
        
        return response()->json($data);
    }


}
