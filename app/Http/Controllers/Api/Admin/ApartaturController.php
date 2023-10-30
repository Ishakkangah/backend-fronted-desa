<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartaturController extends Controller
{
    // index
    public function index()
    {
        $aparaturs = Aparatur::when(request()->search, function ($aparaturs) {
            $aparaturs = $aparaturs->where('name', 'LIKE', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $aparaturs->appends(['search' => request()->search]);

        return new AparaturResource(true, 'Data list aparatur', $aparaturs);
    }


    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'role'      => 'required',
            'image'     => 'required|image|mimes:png,jpg,jpeng|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/aparaturs', $image->hashName());

        // create aparaturs
        $aparaturs = Aparatur::create([
            'name'  => $request->name,
            'role'  => $request->role,
            'image' => $image->hashName()
        ]);

        // response success with api resource
        if ($aparaturs) {
            return new AparaturResource(true, 'Data aparaturs berhasil disampan', $aparaturs);
        }

        // response failed with api resource
        return new AparaturResource(false, 'Data aparaturs gagal disimpan.', null);
    }

    // show
    public function show($id)
    {
        $aparatur = Aparatur::whereId($id)->first();

        // response success with api resource
        if ($aparatur) {
            return new AparaturResource(true, 'Detail aparturs', $aparatur);
        }

        // response failed with api resource
        return new AparaturResource(false, 'Data aparaturs tidak ditemukan.', null);
    }

    // update
    public function update(Request $request, Aparatur $aparatur)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'role'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));
            // upload image
            $image = $request->file('image');
            $image->storeAs('public/aparaturs', $image->hashName());

            // create aparaturs
            $aparatur = $aparatur->update([
                'name'  => $request->name,
                'role'  => $request->role,
                'image' => $image->hashName()
            ]);
        } else {
            // create aparaturs
            $aparatur->update([
                'name'  => $request->name,
                'role'  => $request->role,
            ]);
        }

        // response success with api resource
        if ($aparatur) {
            return new AparaturResource(true, 'Data aparaturs berhasil update', $aparatur);
        }

        // response failed with api resource
        return new AparaturResource(false, 'Data aparaturs gagal update.', null);
    }

    // destroy
    public function destroy(Aparatur $aparatur)
    {
        // remove image
        storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));

        // response success with api resource
        if ($aparatur->delete()) {
            return new AparaturResource(true, 'Data aparaturs berhasil hapus', null);
        }

        // response failed with api resource
        return new AparaturResource(true, 'Data aparaturs gagal hapus', null);
    }
}
