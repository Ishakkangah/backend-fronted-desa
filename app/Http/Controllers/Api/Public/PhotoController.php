<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    // index
    public function index()
    {
        // get photos
        $photos = Photo::latest()->paginate(6);

        if ($photos) {
            return new PhotoResource(true, 'Data list photos', $photos);
        }

        // failed with api resource
        return new PhotoResource(false, 'Tidak ada photo ditemukan', null);
    }
}
