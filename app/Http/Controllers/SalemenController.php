<?php

namespace App\Http\Controllers;

use App\Salemen;
use App\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class SalemenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = Salemen::all();
        if($request->is('api/*')){
            return response()->json($users);
        }
        return view('admin.salemen.index');
    }

        public function getArea(Request $request)
    {
        $area = Area::all();
        if($request->is('api/*')){
            return response()->json($area);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->id) && empty($request->password) && !$request->wantsJson()){
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required',
                'address' => 'required'
            ];
        } else {
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                ];
        }

        $message = [
            'first_name.required' => 'First Name field is required.',
            'phone.required' => 'Phone no field is required.',
            'address.required' => 'Address field is required.',
            'last_name.required' => 'Last Name field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if($validator->fails()){
            if($request->wantsJson()){
                return Response()->json(['error' => $validator->errors()->first()]);
            }
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            try{
                if(!empty($request->id)){
                    if(empty($request->password)){
                        $createUser = Salemen::where('id', $request->id)->update([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'address' => $request->email,
                            'phone' => $request->email,
                            'area_id' => $request->area_id,
                            'email' => $request->email,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $createUser = Salemen::where('id', $request->id)->update([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'address' => $request->address,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'area_id' => $request->area_id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Salemen information updated successfully!']);
                    }
                    return Redirect::route('salemen')->with('success', 'Salemen updated successfully!');
                }
                $createSalemen = Salemen::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'area_id' => $request->area_id,
                    'phone' => $request->phone,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if($request->wantsJson()){
                    return Response()->json(['success' => 'Salemen created successfully!']);
                }
                return Redirect::route('salemen')->with('success', 'Salemen created successfully!');
            }
            catch(\Exception $e){
                if($request->wantsJson()){
                    return Response()->json(['error' => $e->getMessage()]);
                }
                return Redirect::route('salemen')->with('error', $e->getMessage());
            }
        }
    }



    public function destroy($id)
    {
        try{
            Salemen::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => $e->getMessage()]);
        }
    }

}
