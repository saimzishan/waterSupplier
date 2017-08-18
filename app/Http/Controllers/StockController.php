<?php

namespace App\Http\Controllers;

use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stock = Stock::all();
        if($request->is('api/*')){
            return response()->json($stock);
        }
        return view('admin.stock.index');
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
                'product_name' => 'required',
                'price_per' => 'required',
                'product_quantity' => 'required'
            ];
        } else {
            $rules = [
                'product_name' => 'required',
                'price_per' => 'required',
                'product_quantity' => 'required'
            ];
        }

        $message = [
            'product_name.required' => 'Product Name field is required.',
            'product_quantity.required' => 'Product Quantity field is required.',
            'price_per.required' => 'Price field is required.',
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
                    $createStock = Stock::where('id', $request->id)->update([
                        'product_name' => $request->product_name,
                        'product_quantity' => $request->product_quantity,
                        'price_per' => $request->price_per,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Stock information updated successfully!']);
                    }
                    return Redirect::route('stock')->with('success', 'Stock updated successfully!');
                }
                $createStock = Stock::create([
                    'product_name' => $request->product_name,
                    'product_quantity' => $request->product_quantity,
                    'price_per' => $request->price_per,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if($request->wantsJson()){
                    return Response()->json(['success' => 'Stock created successfully!']);
                }
                return Redirect::route('stock')->with('success', 'Stock created successfully!');
            }
            catch(\Exception $e){
                // dd($e->getMessage());
                if($request->wantsJson()){
                    return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
                }
                return Redirect::route('stock')->with('error', 'Sorry something went worng. Please try again.');
            }
        }
    }



    public function destroy($id)
    {
        try{
            Stock::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
        }
    }
}
