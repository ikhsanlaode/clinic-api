<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Json;
use App\Transformers\Field;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    private $user_dtl;

    public function __construct(Request $request)
    {
        $this->user_dtl = $request->user();
    }

    public function index(Request $request) {
        $users = User::query();

        if($request->has('role_id')) {
            $users->where('role_id',$request->role_id);
        }

        if($request->has('status')) {
            $users->where('status',$request->status);
        }

        if ($request->has('entities')) {
            $entities = explode(',', $request->entities);

            try {
                $users = $users->with($entities); 
            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                return Json::exception('Error relation');
            }
        }

        if($request->has('sort')){
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                $field = preg_replace('/[-]/', '', $sort);
                if (preg_match('/^[-]/', $sort)) {
                    $users->orderBy($field, 'desc');
                } else {
                    $users->orderBy($field, 'asc');
                }
            }
        }

        $users = $users->paginate($request->input('offset', 10))->appends($request->all());

        return Json::response($users);
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        return Json::response($user);
    }

    public function store(Request $request) {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->created_by = $this->user_dtl->id;
        $user->updated_by = $this->user_dtl->id;
        $user->save();

        return Json::response($user);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->updated_by = $this->user_dtl->id;
        $user->save();

        return Json::response($user);
    }

    public function delete(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->delete();

        return Json::response($user);
    }

}
