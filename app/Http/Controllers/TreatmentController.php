<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use App\Treatment;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Json;
use App\Transformers\Field;
use Illuminate\Support\Facades\Hash;

class TreatmentController extends BaseController
{
    private $user_dtl;

    public function __construct(Request $request)
    {
        $this->user_dtl = $request->user();
    }

    public function index(Request $request) {
        $treat = Treatment::query();

        if($request->has('doctor_id')) {
            $treat->where('doctor_id',$request->doctor_id);
        }

        if($request->has('status')) {
            $treat->where('status',$request->status);
        }

        if ($request->has('entities')) {
            $entities = explode(',', $request->entities);

            try {
                $treat = $treat->with($entities); 
            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                return Json::exception('Error relation');
            }
        }

        if($request->has('sort')){
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                $field = preg_replace('/[-]/', '', $sort);
                if (preg_match('/^[-]/', $sort)) {
                    $treat->orderBy($field, 'desc');
                } else {
                    $treat->orderBy($field, 'asc');
                }
            }
        }

        $treat = $treat->paginate($request->input('offset', 10))->appends($request->all());

        return Json::response($treat);
    }

    public function show(Request $request, $id)
    {
        $treat = Treatment::findOrFail($id);
        
        return Json::response($treat);
    }

    public function store(Request $request) {
        $treat = new Treatment;
        $treat->doctor_id = $request->doctor_id;
        $treat->name = $request->name;
        $treat->price = $request->price;
        $treat->status = $request->status;
        $treat->save();

        return Json::response($treat);
    }

    public function update(Request $request, $id) {
        $treat = Treatment::findOrfail($id);
        $treat->doctor_id = $request->doctor_id;
        $treat->name = $request->name;
        $treat->price = $request->price;
        $treat->status = $request->status;
        $treat->save();

        return Json::response($treat);
    }

    public function delete(Request $request, $id) {
        $treat = Treatment::findOrFail($id);
        $treat->delete();

        return Json::response($treat);
    }

}
