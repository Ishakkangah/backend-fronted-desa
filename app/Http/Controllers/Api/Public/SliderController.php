<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    // index 
    public function index()
    {
        $slider = Slider::latest()->paginate(9);

        // response success with api resource
        return new SliderResource(true, 'Data list photos', $slider);
    }
}
