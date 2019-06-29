<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use App\MedicalHistory;
use App\Medicine;
use App\Treatment;
use App\MedicalHistoryDetail;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Json;
use DB;

class MedicalHistoryController extends BaseController
{
    private $user_dtl;

    public function __construct(Request $request)
    {
        $this->user_dtl = $request->user();
    }

    public function index(Request $request) {
        $medic = MedicalHistory::query();

        if($request->has('user_id')) {
            $medic->where('user_id',$request->user_id);
        }

        if($request->has('status')) {
            $medic->where('status',$request->status);
        }

        if ($request->has('entities')) {
            $entities = explode(',', $request->entities);

            try {
                $medic = $medic->with($entities); 
            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                return Json::exception('Error relation');
            }
        }

        if($request->has('sort')){
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                $field = preg_replace('/[-]/', '', $sort);
                if (preg_match('/^[-]/', $sort)) {
                    $medic->orderBy($field, 'desc');
                } else {
                    $medic->orderBy($field, 'asc');
                }
            }
        }

        if ($request->has('start_at') && $request->has('end_at')) {
            $medic->whereBetween(\DB::raw('date(created_at)'),[$request->start_at.' 00:00:00', $request->end_at.' 23:59:59']);     
        }

        $medic = $medic->paginate($request->input('offset', 10))->appends($request->all());

        return Json::response($medic);
    }

    public function show(Request $request, $id)
    {
        $medic = MedicalHistory::findOrFail($id);
        
        return Json::response($medic);
    }

    public function store(Request $request) {
        $medic = new MedicalHistory;
        $medic->user_id = $request->user_id;
        $medic->doctor_id = $request->doctor_id;
        $medic->image_id = $request->image_id;
        $medic->note = $request->note;
        $medic->status = $request->status;
        $medic->created_by = $this->user_dtl->id;
        $medic->updated_by = $this->user_dtl->id;
        $medic->save();

        foreach($request->treatments as $treatment) {
            $tm = new MedicalHistoryDetail;
            $tm->medical_history_id = $medic->id;
            $tm->source_id = $treatment['treatment_id'];
            $tm->source_type = Treatment::class;
            $tm->save();
        }

        foreach($request->medicines as $medicine) {
            $tm = new MedicalHistoryDetail;
            $tm->medical_history_id = $medic->id;
            $tm->source_id = $medicine['medicine_id'];
            $tm->source_type = Medicine::class;
            $tm->quantity = $medicine['quantity'];
            $tm->save();
        }
    
        return Json::response($medic);
    }

    public function update(Request $request, $id) {
        $medic = MedicalHistory::findOrFail($id);
        $medic->user_id = $request->user_id;
        $medic->doctor_id = $request->doctor_id;
        $medic->image_id = $request->image_id;
        $medic->note = $request->note;
        $medic->status = $request->status;
        $medic->created_by = $this->user_dtl->id;
        $medic->updated_by = $this->user_dtl->id;
        $medic->save();

        return Json::response($medic);
    }

    public function delete(Request $request, $id) {
        $medic = MedicalHistory::findOrFail($id);
        $medic->delete();

        return Json::response($medic);
    }

}
