<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Json;

class RoleController extends BaseController
{
    private $user_dtl;

    public function __construct(Request $request)
    {
        $this->user_dtl = $request->user();
    }

    public function index(Request $request) {
        $roles = Role::query();

        if($request->has('name')) {
            $roles->where('name',$request->name);
        }

        if($request->has('status')) {
            $roles->where('status',$request->status);
        }

        if ($request->has('entities')) {
            $entities = explode(',', $request->entities);

            try {
                $roles = $roles->with($entities); 
            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                return Json::exception('Error relation');
            }
        }

        if($request->has('sort')){
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                $field = preg_replace('/[-]/', '', $sort);
                if (preg_match('/^[-]/', $sort)) {
                    $roles->orderBy($field, 'desc');
                } else {
                    $roles->orderBy($field, 'asc');
                }
            }
        }

        $roles = $roles->paginate($request->input('offset', 10))->appends($request->all());

        return Json::response($roles);
    }

    public function show(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        return Json::response($role);
    }

    public function store(Request $request) {
        $role = new Role;
        $role->name = str_slug($request->display_name);
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->created_by = $this->user_dtl->id;
        $role->updated_by = $this->user_dtl->id;
        $role->save();

        return Json::response($role);
    }

    public function update(Request $request, $id) {
        $role = Role::findOrFail($id);
        $role->name = str_slug($request->display_name);
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->updated_by = $this->user_dtl->id;
        $role->save();

        return Json::response($user);
    }

    public function delete(Request $request, $id) {
        $user = Role::findOrFail($id);
        $user->delete();

        return Json::response($user);
    }

}
