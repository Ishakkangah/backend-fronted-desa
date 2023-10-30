<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Contracts\Role;

class UserController extends Controller
{
    // get user
    public function index()
    {
        $users          = User::when(request()->search, function ($users) {
            $users      = $users->where('name', 'LIKE', "%" . request()->search . "%");
        })->with('roles')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'name'          => 'required',
            'roles'         => 'required',
            'email'         => 'required|unique:users,email',
            'password'      => 'required|confirmed'
        ]);


        // return validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create user
        $user               = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password)
        ]);

        // Assign role to user
        $user->assignRole($request->roles);

        // success save data
        if ($user) {
            return new UserResource(true, 'User berhasil dibuat', $user);
        }

        // return response with api resource
        return new UserResource(false, 'User gagal dibuat', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $user       = User::with('roles')->whereId($id)->first();

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Detail Data user', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Detail Data User Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,' . $user->id,
            'roles'    => 'required',
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "") {

            //update user without password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);
        } else {

            //update user with new password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);
        }

        //assign roles to user
        $user->syncRoles($request->roles);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Diupdate!', null);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(User $user)
    {
        if ($user->delete()) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Dihapus!', null);
    }
}
