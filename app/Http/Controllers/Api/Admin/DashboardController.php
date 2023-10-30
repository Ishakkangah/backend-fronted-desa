<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aparatur;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // count categories
        $categories         = Category::count();
        // count posts
        $posts              = Post::count();
        // count products
        $products           = Product::count();
        // count aparaturs
        $aparaturs          = Aparatur::count();

        // return response 
        return response()->json([
            'success'       => true,
            'message'       => 'List data on dashboard',
            'data'          => [
                'categories' => $categories,
                'posts'     => $posts,
                'products'  => $products,
                'aparaturs' => $aparaturs
            ]
        ], 200);
        dd($categories);
    }
}
