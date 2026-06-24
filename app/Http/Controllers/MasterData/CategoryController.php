<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    function index() {
        $title = "Category";
        $description = "Category";
        $pageTitle = "Master Data / Category";
        
        return view('masterdata.category.category', compact('title', 'description', 'pageTitle'));
    }

    function list() {
        $cat = Category::select([
            'cat_id',
            'cat_name',
            'cat_desc',
            'cat_add_by',
            'cat_upd_by',
            'created_at',
            'updated_at',
        ])->orderBy('cat_id', 'asc');
        
        return DataTables::of($cat)
                ->addIndexColumn()
                ->toJson();
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => $request->cat_id 
                            ? 'required|string|max:100|unique:categories,cat_name,'.$request->cat_id.',cat_id'
                            : 'required|string|max:100|unique:categories,cat_name',
                'desc' => 'required|string|max:255',
            ],
            [
                'name.required' => 'Name Required!',
                'name.max'      => 'Maximum Name 100 Characters',
                'desc.required' => 'Description Required!',
                'desc.max'      => 'Maximum Description 255 Characters',
            ]
        );

        $data = [
            'cat_name'   => $validated['name'],
            'cat_desc'   => $validated['desc'],
        ];

        if ($request->cat_id) {
            $upd = Category::findorfail($request->cat_id);
            $data['cat_upd_by'] = Auth::user()?->name;
            $upd->update($data);

            $messages = 'Category Successfully Updated!';

        } else {
            $data['cat_add_by'] = Auth::user()?->name;
            Category::create($data);

            $messages = 'Category Successfully Created!';
        }

        return response()->json([
            'status' => 'success',
            'message' => $messages,
        ]);
    }

    public function getData($id)
    {
        $cat = Category::where('cat_id', $id)
            ->select(
                'cat_id',
                'cat_name',
                'cat_desc'
            )
            ->firstorfail();

        return response()->json($cat);
    }

    public function delete($id)
    {
        $cat = Category::findorfail($id);

        if($cat->products()->exists()) {
            return response()->json([
                'title' => 'Errors!',
                'status' => 'error',
                'message'=> 'Category Still Used In Product!'
            ]);
        }
        
        $cat->delete();

        return response()->json([
            'title' => 'Deleted!',
            'status' => 'success',
            'message'=> 'Data Successfully Deleted!'
        ]);

    }

}
