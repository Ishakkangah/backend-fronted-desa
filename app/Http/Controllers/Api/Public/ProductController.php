<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // index
    public function index()
    {
        $products = Product::latest()->paginate(9);

        // response success with api message 
        return new ProductResource(true, 'Data list products', $products);
    }

    // show
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            // response with api resource
            return new ProductResource(true, 'Detail data product', $product);
        }

        // response failed api resource
        return new ProductResource(false, 'Data tidak ditemukan', null);
    }

    // home page
    public function homepage()
    {
        $products = Product::latest()->take(6)->get();

        // response with api resource
        return new ProductResource(true, 'Data list product homepage', $products);
    }
}
