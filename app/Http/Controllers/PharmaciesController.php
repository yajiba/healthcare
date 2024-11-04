<?php

namespace App\Http\Controllers;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PharmaciesController extends Controller
{
    public function list (){
        $data = Pharmacy::all(); /* DB::table('pharmacies')->get() */
        if (request()->ajax()) {
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $btn = '<a href="javascript:void(0)"
                data-id="'.$row->id.'"
                data-name="'.$row->pharmacy_name.'"
                data-address="'.$row->address.'"
                data-contact_number="'.$row->contact_number.'"
                data-email="'.$row->email.'"
                class="edit "><i class="fa-regular fa-pen-to-square"></i></a>';
                $status = ($row->status == 1)? 'deactivate':'activate';
                $btn .= ' <a href="javascript:void(0)" data-id="'.$row->id.'" class="delete" data-status="'.$status.'"><i class="fa-solid fa-trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('pharmacy.list');
    }

    public function store(Request $request){
       $validate =  $request->validate([
            'pharmacy_name' => ['required', 'string', 'max:255','regex:/^[A-Za-z0-9\s]+$/','unique:pharmacies,pharmacy_name'],
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255','unique:pharmacies,contact_number'],
            'email' => ['required', 'email','unique:pharmacies,email'],
        ] );

         $insert = Pharmacy::create($validate);

         if($insert) {
             return response()->json(['status'=> 'success', 'message' => 'Pharmacy Successfully Added'], 200);
         }else {
            return response()->json(['status'=> 'error', 'message' => 'Error when adding data'], 500);

         }
     }
    public function update(Request $request){
        $pharmacy = Pharmacy::find($request->input('id'));
        $validate = $request->validate([
            'pharmacy_name' => ['required', 'string', 'max:255','regex:/^[A-Za-z0-9\s]+$/','unique:pharmacies,pharmacy_name,' . $pharmacy->id . ',id'],
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255','unique:pharmacies,contact_number,' . $pharmacy->id. ',id'],
            'email' => ['required', 'email','unique:pharmacies,email, ' . $pharmacy->id. ',id'],
        ] );

         $update = $pharmacy->update($validate);

         if($update) {
             return response()->json(['status'=> 'success', 'message' => 'Pharmacy Successfully updated'], 200);
         }else {
            return response()->json(['status'=> 'error', 'message' => 'Error when adding data'], 500);

         }
    }
    public function update_status($id){
        $pharmacy = Pharmacy::find($id);
        $pharmacy->status = ($pharmacy->status == 1)?  0: 1;
        $pharmacy->save();
    }
}
