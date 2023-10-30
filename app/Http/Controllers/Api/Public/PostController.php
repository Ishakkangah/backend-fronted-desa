<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // index 
    public function index()
    {
        $posts = Post::with('category', 'user')->latest()->paginate(9);
        //return with Api Resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    // show
    public function show($slug)
    {
        $post = Post::with('category', 'user')->where('slug', $slug)->first();

        // response success with api message
        if ($post) {
            return new PostResource(true, 'Detail data post', $post);
        }

        // response failed with api resource
        return new PostResource(false, 'Tidak ada data post', null);
    }

    // home page
    public function homePage()
    {
        $posts = Post::with('user', 'category')->latest()->take(6)->get();

        return new PostResource(true, 'Data list post homepage', $posts);
    }
}
