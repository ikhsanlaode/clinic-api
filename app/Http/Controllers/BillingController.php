<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use App\MedicalHistoryDetail;
use App\Billing;
use App\BillingMedicine;
use App\BillingTreatment;
use App\Treatment;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Json;

class BillingController extends BaseController
{
    private $user_dtl;

    public function __construct(Request $request)
    {
        $this->user_dtl = $request->user();
    }

    public function index(Request $request) {
        $bill = Billing::query();

        if($request->has('patient_id')) {
            $bill->where('patient_id',$request->patient_id);
        }

        if($request->has('doctor_id')) {
            $bill->where('doctor_id',$request->doctor_id);
        }

        if($request->has('medical_history_id')) {
            $bill->where('medical_history_id',$request->medical_history_id);
        }

        if($request->has('status')) {
            $bill->where('status',$request->status);
        }

        if ($request->has('entities')) {
            $entities = explode(',', $request->entities);

            try {
                $bill = $bill->with($entities); 
            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                return Json::exception('Error relation');
            }
        }

        if($request->has('sort')){
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                $field = preg_replace('/[-]/', '', $sort);
                if (preg_match('/^[-]/', $sort)) {
                    $bill->orderBy($field, 'desc');
                } else {
                    $bill->orderBy($field, 'asc');
                }
            }
        }

        $bill = $bill->paginate($request->input('offset', 10))->appends($request->all());

        return Json::response($bill);
    }

    public function show(Request $request, $id)
    {
        $bill = Billing::findOrFail($id);
        
        return Json::response($bill);
    }

    public function store(Request $request) {

        $treatment = MedicalHistoryDetail::where('source_type','App\Treatment')->where('medical_history_id', $request->medical_history_id)->get();
        $tr_fee = Treatment::whereIn('id',$treatment->pluck('id'))->sum('price');

        $medicine = MedicalHistoryDetail::where('source_type','App\Medicine')->where('medical_history_id', $request->medical_history_id)->get();
        $md_fee = Medicine::whereIn('id',$medicine->pluck('id'))->sum('price');

        
        $bill = new Billing;
        $bill->code = 'example';
        $bill->patient_id = $request->patient_id;
        $bill->doctor_id = $request->doctor_id;
        $bill->medical_history_id = $request->medical_history_id;
        $bill->consultation_fee = $request->consultation_fee;
        $bill->treatment_fee = $tr_fee;
        $bill->medicine_fee = $md_fee;
        $bill->status = 0;
        $bill->save();
        
        foreach($medicine as $m) {
            $med = new BillingMedicine;
            $med->billing_id = $bill->id;
            $med->medicine_id = $m->id;
            $med->quantity = $m->quantity;
            $med->total = $m->medicine->price;
            $med->save();
        }

        foreach($treatment as $t) {
            $med = new BillingTreatment;
            $med->billing_id = $bill->id;
            $med->medicine_id = $m->id;
            $med->quantity = $m->quantity;
            $med->total = $m->treatment->price;
            $med->save();
        }
        return Json::response($bill);
    }

    public function approve(Request $request, $id) {
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
