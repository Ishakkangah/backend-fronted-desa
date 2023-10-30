<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    // index 
    public function index()
    {
        $photos = Photo::when(request()->search, function ($photos) {
            $photos = $photos->where('caption', 'LIKE', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $photos->appends(['search' => request()->search]);

        // success with api resource
        return new PhotoResource(true, 'Data list photo', $photos);
    }

    // store
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),  [
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'caption'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/photos', $image->hashName());

        $photo = Photo::create([
            'image'     => $image->hashName(),
            'caption'   => $request->caption
        ]);

        // response success with api resource
        if ($photo) {
            return new PhotoResource(true, 'Photo berhasil disimpan', $photo);
        }

        // response failed with api resource
        return new PhotoResource(false, 'Photo gagal disimpan', null);
    }


    // destory
    public function destroy(Photo $photo)
    {
        Storage::disk('local')->delete('public/photos/' . basename($photo->image));

        if ($photo->delete()) {
            // success with api response 
            return new PhotoResource(true, 'Data photo berhasil dihapus', null);
        }

        // success with api response 
        return new PhotoResource(false, 'Data photo gagal dihapus', null);
    }
}
