<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index()
    {
        $title = 'Stock Inventory';
        $pageTitle = 'Inventory / Stock';

        return view('inventory.stock.stock', compact(
            'title',
            'pageTitle'
        ));
    }

    public function list()
    {
        $query = Stock::query()
            ->leftJoin('products as prd', 'prd.prd_id', '=', 'stocks.ref_prd_id')
            ->leftJoin('warehouses as wh', 'wh.wh_id', '=', 'stocks.ref_wh_id')
            ->leftJoin('locations as loc', 'loc.loc_id', '=', 'stocks.ref_loc_id')
            ->select([
                'stocks.st_id',
                'prd.prd_code as product_code',
                'prd.prd_name as product_name',
                'wh.wh_code as warehouse_code',
                'wh.wh_name as warehouse_name',
                'loc.loc_code as location_code',
                'loc.loc_desc as location_name',
                'stocks.qty_on_hand',
                'stocks.qty_reserved',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('available_qty', function ($row) {
                return $row->qty_on_hand - $row->qty_reserved;
            })
            ->editColumn('qty_on_hand', function ($row) {
                return number_format($row->qty_on_hand, 2);
            })
            ->editColumn('qty_reserved', function ($row) {
                return number_format($row->qty_reserved, 2);
            })
            ->toJson();
    }

    public function available(Request $request)
    {
        $stock = Stock::where('ref_prd_id', $request->product_id)
            ->where('ref_wh_id', $request->warehouse_id)
            ->where('ref_loc_id', $request->location_id)
            ->first();

        $available = $stock
            ? $stock->qty_on_hand - $stock->qty_reserved
            : 0;

        return response()->json([
            'available_qty' => $available,
        ]);
    }

}