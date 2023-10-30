<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use PDO;

class PageController extends Controller
{
    // index
    public function index()
    {
        $pages = Page::latest()->paginate(9);

        // response success with api resource
        return new PageResource(true, 'Data list pages', $pages);
    }

    // show
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();

        // response success with api resource
        if ($page) {
            return new PageResource(true, 'Detail data pages', $page);
        }

        // response failde with api resource
        return new PageResource(false, 'Data tidak ditemukan', null);
    }

    // home page
    public function homePage()
    {
        $pages = Page::latest()->take(9)->get();

        return new PageResource(false, 'Data list pages homepage', $pages);
    }
}
