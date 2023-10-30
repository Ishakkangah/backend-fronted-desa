<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get products
        $products = Product::when(request()->search, function ($products) {
            $products = $products->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $products->appends(['search' => request()->search]);

        //return with Api Resource
        return new ProductResource(true, 'List Data Products', $products);
    }

    // // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required',
            'content'       => 'required',
            'image'         => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'owner'         => 'required',
            'price'        => 'required',
            'phone'         => 'required',
            'address'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // create product
        $product = Product::create([
            'user_id'       => auth()->guard('api')->user()->id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'content'       => $request->content,
            'image'         => $image->hashName(),
            'owner'         => $request->owner,
            'price'         => $request->price,
            'phone'         => $request->phone,
            'address'       => $request->address,
        ]);

        if ($product) {
            // response success with api resource
            return new ProductResource(true, 'Data product berhasild disimpan.', $product);
        }

        // response failed with api resource
        return new ProductResource(false, 'Data product gagal disimpan', null);
    }

    // show
    public function show($id)
    {
        $product = Product::whereId($id)->first();

        // response success with api resource
        if ($product) {
            return new ProductResource(true, 'Detail data product', $product);
        }

        // response failed with api resource
        return new ProductResource(false, 'Data product tidak ditemukan', null);
    }

    // update
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required',
            'content'       => 'required',
            'owner'         => 'required',
            'price'        => 'required',
            'phone'         => 'required',
            'address'       => 'required'
        ]);

        if ($validator->failed()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/products/' . $product->image);
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product->update([
                'title'         => $request->title,
                'slug'          => Str::slug($request->name, '-'),
                'content'       => $request->content,
                'owner'         => $request->owner,
                'price'         => $request->price,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'image'         => $image->hashName(),
                'user_id'       => auth()->guard('api')->user()->id
            ]);
        } else {
            // update product
            $product->update([
                'title'         => $request->title,
                'slug'          => Str::slug($request->name, '-'),
                'content'       => $request->content,
                'owner'         => $request->owner,
                'price'         => $request->price,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'user_id'       => auth()->guard('api')->user()->id
            ]);
        }

        if ($product) {
            // response success with api resource
            return new ProductResource(true, 'Data product berhasil di update', $product);
        }

        // response failed with api resource
        return new ProductResource(false, 'Data product gagal di update', null);
    }

    // destroy
    public function destroy(Product $product)
    {
        // remove image
        Storage::disk('local')->delete('public/products/' . basename($product->image));
        // response success with api resource
        if ($product->delete()) {
            return new ProductResource(true, 'Data product berhasil dihapus', null);
        }

        // response failed with api resource
        return new ProductResource(false, 'Data product gagal dihapus', null);
    }
}
