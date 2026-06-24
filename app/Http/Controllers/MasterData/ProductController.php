<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index() {
        $title = 'Product';
        $pageTitle = 'Master Data / Product';
        $categories = Category::orderBy('cat_name', 'asc')->get(['cat_id', 'cat_name']);

        return view('masterdata.product.product', compact('title', 'pageTitle', 'categories'));
    }

    public function list() {
        $prd = Product::join('categories as cat', 'cat.cat_id', '=', 'products.ref_cat_id')
        ->select([
            'prd_id as id',
            'prd_code as code',
            'prd_name as prdnm',
            'prd_unit as prdunit',
            'cat.cat_name as prdcat',
            'prd_min_stock as prdstck',
            'prd_desc as prddesc',
            'prd_add_by as add',
            'prd_upd_by as upd',
            'products.created_at as crea',
            'products.updated_at as up'
        ])->orderBy('prd_code', 'asc');

        return DataTables::of($prd)
                ->addIndexColumn()
                ->toJson();
    }

    public function store(Request $request) {
        $validate = $request->validate(
            [
                'prdcd' => $request->prd_id ? 'required|string|max:100|unique:products,prd_code,'.$request->prd_id.',prd_id' : 'required|string|max:100|unique:products,prd_code',
                'prdnm' => 'required',
                'prdunit' => 'required',
                'prdcat' => 'required',
                'prdstck' => 'required',
            ],
            [
                'prdcd' => 'Product Code Required!',
                'prdnm' => 'Product Name Required!',
                'prdunit' => 'Product Unit Required!',
                'prdcat' => 'Category Required!',
                'prdstck' => 'Min Stock Required!',
            ]
        );

        $data = [
            'prd_code'      => $validate['prdcd'],
            'prd_name'      => $validate['prdnm'],
            'prd_unit'      => $validate['prdunit'],
            'ref_cat_id'    => $validate['prdcat'],
            'prd_min_stock' => $validate['prdstck'],
            'prd_desc'      => $request->prddesc ?? null,
        ];

        if($request->prd_id) {
            $upd = Product::findorfail($request->prd_id);
            $data['prd_upd_by'] = Auth::user()?->name;
            $upd->update($data);

            $message = 'Product Successfully Updated!';
        } else {
            $data['prd_add_by'] = Auth::user()?->name;
            Product::create($data);

            $message = 'Product Successfully Created!';

        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function getedit($id) {
        $get = Product::where('prd_id', $id)
                ->join('categories as cat', 'cat.cat_id', '=', 'products.ref_cat_id')
                ->firstorfail();

        $data = [
            'prd_id'        => $get->prd_id,
            'prd_code'      => $get->prd_code,
            'prd_name'      => $get->prd_name,
            'prd_unit'      => $get->prd_unit,
            'ref_cat'       => $get->ref_cat_id,
            'prd_min_stock' => $get->prd_min_stock,
            'prd_desc'      => $get->prd_desc,
        ];

        return response()->json($data);
    }

    public function delete($id)
    {
        $prd = Product::findorfail($id);

        if($prd->stocks()->exists()) {
            return response()->json([
                'title' => 'Errors!',
                'status' => 'error',
                'message'=> 'Product Still Used In Stocks!'
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
