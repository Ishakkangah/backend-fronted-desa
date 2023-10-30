<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // index role
    public function index()
    {
        // get roles
        $roles              = Role::when(request()->search, function ($roles) {
            $roles          = $roles->where('name', 'LIKE', '%' . request()->search . "%");
        })->with('permissions')->latest()->paginate(5);



        // append query string to pagination links
        $roles->appends(['search' => request()->search]);

        // return with Api resource 
        return new RoleResource(true, 'List Data Roles', $roles);
    }




    public function store(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required'
        ]);

        // validator fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create role
        $role               = Role::create(['name' => $request->name]);
        //assign permissions to role
        $role->givePermissionTo($request->permissions);

        // jika berhasil
        if ($role) {
            //return success with Api Resource
            return new RoleResource(true, 'Data Role Berhasil Disimpan!', $role);
        }

        //return failed with Api Resource
        return new RoleResource(false, 'Data Role Gagal Disimpan!', null);
    }


    public function show($id)
    {
        $role             = Role::with('permissions')->findOrFail($id);

        if ($role) {
            //return success with Api Resource
            return new RoleResource(true, 'Detail Data Role!', $role);
        }

        //return failed with Api Resource
        return new RoleResource(false, 'Detail Data Role Tidak Ditemukan!', null);
    }


    public function update(Request $request, Role $role)
    {
        /**
         * validate request
         */

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update role
        $role->update(['name' => $request->name]);

        //sync permissions
        $role->syncPermissions($request->permissions);

        if ($role) {
            //return success with Api Resource
            return new RoleResource(true, 'Data Role Berhasil Diupdate!', $role);
        }

        //return failed with Api Resource
        return new RoleResource(false, 'Data Role Gagal Diupdate!', null);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //find role by ID
        $role           = Role::findOrFail($id);

        // Role delete
        if ($role->delete()) {
            return new RoleResource(true, 'Data role berhasil dihapus', $role);
        }

        // Response failde with api resource
        return new RoleResource(false, 'Data role gagal dihapus', null);
    }


    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        // get roles
        $roles          = Role::latest()->get();

        //return with Api Resource
        return new RoleResource(true, 'List Data Roles', $roles);
    }
}
