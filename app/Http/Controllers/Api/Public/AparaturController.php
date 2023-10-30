<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;
use Illuminate\Http\Request;

class AparaturController extends Controller
{
    // index 
    public function index()
    {
        // get data from oldert
        $aparaturs = Aparatur::oldest()->get();

        // response success with api resource
        return new AparaturResource(true, 'Data list aparaturs', $aparaturs);
    }
}
