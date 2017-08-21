<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('user_type', 0)->get();
        if($request->is('api/*')){
            return response()->json($users);
        }
        return view('admin.users.index');
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
                'password' => 'required|string|min:5|confirmed',
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
                        $createUser = User::where('id', $request->id)->update([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'address' => $request->address,
                            'parent_id' => $request->parent_id,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $createUser = User::where('id', $request->id)->update([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'address' => $request->address,
                            'phone' => $request->phone,
                            'parent_id' => $request->parent_id,
                            'email' => $request->email,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'User information updated successfully!']);
                    }
                    return Redirect::route('users')->with('success', 'User updated successfully!');
                }

                $token = $this->getToken($request->last_name);
                $createUser = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'user_type' => $request->user_type,
                    'email' => $request->email,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'parent_id' => $request->parent_id,
                    'password' => bcrypt($request->password),
                    'refToken' => $token,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if($request->wantsJson()){
                    return Response()->json(['success' => 'User created successfully!']);
                }
                return Redirect::route('users')->with('success', 'User created successfully!');
            }
            catch(\Exception $e){
                if($request->wantsJson()){
                    return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
                }
                return Redirect::route('users')->with('error', ' Sorry something went worng. Please try again.');
            }
        }
    }



    public function destroy($id)
    {
        try{
            User::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
        }
    }

    protected function getToken($string)
    {
        $token = str_random(10);
        $token = strtotime(date('Y-m-d H:i:s')).$token.$string.'-AX';
        return $token;
    }

}
