<?php

namespace App\Http\Controllers;


use App\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stock = Area::all();
        if($request->is('api/*')){
            return response()->json($stock);
        }
        return view('admin.area.index');
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
                'area_name' => 'required',
            ];
        } else {
            $rules = [
                'area_name' => 'required',
            ];
        }

        $message = [
            'area_name.required' => 'Area Name field is required.',
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
                    $createArea = Area::where('id', $request->id)->update([
                        'area_name' => $request->area_name,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Area information updated successfully!']);
                    }
                    return Redirect::route('area')->with('success', 'Area updated successfully!');
                }
                $createArea = Area::create([
                    'area_name' => $request->area_name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if($request->wantsJson()){
                    return Response()->json(['success' => 'Area created successfully!']);
                }
                return Redirect::route('area')->with('success', 'Area created successfully!');
            }
            catch(\Exception $e){
                // dd($e->getMessage());
                if($request->wantsJson()){
                    return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
                }
                return Redirect::route('area')->with('error', 'Sorry something went worng. Please try again.');
            }
        }
    }



    public function destroy($id)
    {
        try{
            Area::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
        }
    }
}
