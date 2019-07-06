<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Category;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CategoryController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $categories = Category::latest()
                ->with(['services'])
                ->leftJoin('barber_services', 'barber_services.category_id', '=', 'categories.id')
                ->select('categories.*', DB::raw("COUNT(DISTINCT(barber_id)) as count"))
                ->groupBy('categories.id')
                ->get();
        //prd($categories);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $category = new Category();
        $category->name = $request->get('name');
        $category->unique_id = $this->unique_key("CAT", "categories");
        $category->save();

        return response()->json(['success' => __('messages.category_add_success')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $category = Category::findOrFail($request->cat_id);
        $category->update($request->all());
        return back();
        //return dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category) {
        $serviceCount = Service::where('category_id', '=', $category->id)->get()->count();

        if ($serviceCount > 0) {
            return redirect('admin/categories')->with('error', __('messages.category_del_success'));
        } else {
            Category::find($category->id)->delete();
            return redirect('admin/categories')->with('success', __('messages.category_cannot_del'));
        }
    }

}
