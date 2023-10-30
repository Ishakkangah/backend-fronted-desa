<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::when(request()->search, function ($pages) {
            $pages = $pages->where('title', 'LIKE', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $pages->appends(['search' => request()->search]);

        // response success with api resource
        return new PageResource(true, 'Data list page', $pages);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pages = Page::create([
            'user_id'       => auth()->guard('api')->user()->id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'content'       => $request->content
        ]);

        if ($pages) {
            // response success with api resource
            return new PageResource(true, 'Data page berhasil disimpan', $pages);
        }

        // response failed with api resource
        return new PageResource(false, 'Data page gagal disimpan', null);
    }

    // show
    public function show($id)
    {
        $page = Page::whereId($id)->first();

        if ($page) {
            // response success with api resource
            return new PageResource(true, 'Detail data page', $page);
        }

        // response failed with api resource
        return new PageResource(false, 'Data tidak ditemukan', null);
    }


    // store
    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page->update([
            'user_id'       => auth()->guard('api')->user()->id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'content'       => $request->content
        ]);

        if ($page) {
            // response success with api resource
            return new PageResource(true, 'Data page berhasil di update', $page);
        }

        // response failed with api resource
        return new PageResource(false, 'Data page gagal di update', null);
    }

    // destroy
    public function destroy(Page $page)
    {
        if ($page->delete()) {
            // response success with api resource 
            return new PageResource(true, 'Data page berhasil', null);
        }

        // response failed with api resource 
        return new PageResource(true, 'Data page berhasil dihapus', null);
    }
}
