<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Index
    public function index()
    {
        $categories = Category::when(request()->search, function ($categories) {
            $categories = $categories->where('name', 'LIKE', '%' . request()->search . '%');
        })->latest()->paginate(5);

        // appends query string to paginate links
        $categories->appends(['search' => request()->search]);

        // response success with api resource
        return new CategoryResource(true, 'List data categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'name'          => 'required|string|unique:categories,name'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-')
        ]);


        if ($category) {
            // response success with api resource
            return new CategoryResource(true, 'Category berhasil dibuat', $category);
        }

        // response failed with api resource
        return new CategoryResource(false, 'Category gagal dibuat', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $category = Category::whereId($id)->first();


        if ($category) {
            // response success with api resource
            return new CategoryResource(true, 'Detail data category', $category);
        }

        // response faild with api resource 
        return new CategoryResource(false, 'Data tidak ditemukan', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|unique:categories,name,' . $category->id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);

        if ($category) {
            // response success with api resource
            return new CategoryResource(true, 'Data category berhasil di update', $category);
        }

        // response failed with api resource
        return new CategoryResource(false, 'Data category gagal di update', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Category $category)
    {
        // response success with api resource
        if ($category->delete()) {
            return new CategoryResource(true, 'Data category berhasil di hapus', null);
        }

        // response failed with api resource 
        return new CategoryResource(false, 'Data category gagal di hapus', null);
    }

    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        //get categories
        $categories = Category::latest()->get();

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }
}
