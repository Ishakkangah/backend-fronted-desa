<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    // index 
    public function index()
    {
        // get slider
        $slider = Slider::latest()->paginate(5);

        // response with api resource
        return new SliderResource(true, 'List slider photo', $slider);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        // create slider
        $slider = Slider::create([
            'image'     => $image->hashName()
        ]);

        // response success with api resource
        if ($slider) {
            return new SliderResource(true, 'Photo slider berhasil di simpan', $slider);
        }

        // response failed with api resource
        return new SliderResource(false, 'Photo slider gagal di simpan', null);
    }

    // destroy
    public function destroy(Slider $slider)
    {
        //remove image
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        // response failed with api resource
        if ($slider->delete()) {
            return new SliderResource(true, 'Data slider berhasil di hapus', null);
        }

        // response failed with api resource
        return new SliderResource(false, 'Data slider gagal dihapus', null);
    }
}
