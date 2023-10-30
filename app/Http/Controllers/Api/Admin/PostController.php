<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'category')->when(request()->search, function ($posts) {
            $posts = $posts->where('title', 'LIKE', '%' . request()->search . '%');
        })->where('user_id', auth()->user()->id)->latest()->paginate(5);

        // appends query string to pagination link
        $posts->appends(['search' => request()->search]);

        // response success with api resource 
        return new PostResource(true, 'Data list posts', $posts);
    }

    // store
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title'         => 'required|required|unique:posts',
            'image'         => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'content'       => 'required',
            'category_id'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'content'       => $request->content,
            'slug'          => Str::slug($request->title, '-'),
            'user_id'       => auth()->guard('api')->user()->id,
            'category_id'   => $request->category_id,
        ]);

        if ($post) {
            // response success with api resource
            return new PostResource(true, 'Data post berhasil disimpan', $post);
        }

        // response failed api resource 
        return new PostResource(false, 'Data post gagal disimpan!', null);
    }

    // show
    public function show($id)
    {
        $post = Post::with('user', 'category')->whereId($id)->first();

        // response success with api resource
        if ($post) {
            return new PostResource(true, 'Detail data post', $post);
        }

        // response failed with api resource
        return new PostResource(false, 'Data tidak ditemukan!', null);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:posts,title,' . $post->id,
            'content'       => 'required',
            'category_id'   => 'required'
        ]);



        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }



        // check image update
        if ($request->file('image')) {
            // delete old image
            Storage::disk('local')->delete('public/posts/' . basename($post->image));

            // upload new image 
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $post->update([
                'category_id'   => $request->category_id,
                'user_id'       => auth()->guard('api')->user()->id,
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'content'       => $request->content,
                'image'         => $image->hashName()
            ]);
        } else {
            $post->update([
                'category_id'   => $request->category_id,
                'user_id'       => auth()->guard('api')->user()->id,
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'content'       => $request->content,
            ]);
        }

        if ($post) {
            // respnse success with api resource 
            return new PostResource(true, 'Data post berhasil di update', $post);
        }

        // response failed with api resource
        return new PostResource(false, 'Data post gagal di update', null);
    }

    // destroy
    public function destroy(Post $post)
    {
        // remove image
        Storage::disk('local')->delete('public/posts/' . basename($post->image));

        // response success with api resource
        if ($post->delete()) {
            return new PostResource(true, 'Data post berhasil di hapus', null);
        }

        // response failed with api resource
        return new PostResource(false, 'Data post gagal di hapus', null);
    }
}
