<?php

namespace App\Http\Controllers;

use App\Salemen;
use App\Stock;
use App\StockIssue;
use App\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      //  $stock = StockIssue::all();
        if($request->is('api/*')){
            $stackData = DB::table('sales')
                ->select(['sales.*','salesmen.first_name', 'stock.product_name'])
                ->join('salesmen', 'sales.salesmen_id', '=', 'salesmen.id')
                ->join('stock', 'sales.stock_id', '=', 'stock.id')->get();
            return response()->json($stackData);
        }
        return view('admin.sales.index');
    }
    public function getSalesMen(Request $request)
    {
        $saleMen = Salemen::all();
        if($request->is('api/*')){
            return response()->json($saleMen);
        }
    }
    public function getStock(Request $request)
    {
        $stock = Stock::all();
        if($request->is('api/*')){
            return response()->json($stock);
        }
    }
    public function getStockbyID(Request $request, $id)
    {
        $stackData = DB::table('stockissue')
            ->where('salesmen_id', $id)
            ->select(['stockissue.*', 'stock.*'])
            ->join('stock', 'stockissue.stock_id', '=', 'stock.id')->first();
        if($request->is('api/*')){
            return response()->json($stackData);
        }
    }
    public function check_validiti(Request $request, $id)
    {
        $stockByID = StockIssue::where('salesmen_id', $id)->select('quantity' , 'stock_id')->first();
        if($request->is('api/*')){
            return response()->json($stockByID);
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
                'quantity' => 'required',
                'salesmen_id' => 'required',
                'stock_id' => 'required'
            ];
        } else {
            $rules = [
                'quantity' => 'required',
                'salesmen_id' => 'required',
                'stock_id' => 'required'
            ];
        }

        $message = [
            'quantity.required' => 'Quantity Name field is required.',
            'salesmen_id.required' => 'Salesmen ID Quantity field is required.',
            'stock_id.required' => 'Stock ID Quantity field is required.',
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
                    $createStockIssue = Sales::where('id', $request->id)->update([
                        'quantity' => $request->quantity,
                        'salesmen_id' => $request->salesmen_id,
                        'stock_id' => $request->stock_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Sales information updated successfully!']);
                    }
                    return Redirect::route('sales')->with('success', 'Sales updated successfully!');
                }
                $createSales = Sales::create([
                    'quantity' => $request->quantity,
                    'salesmen_id' => $request->salesmen_id,
                    'stock_id' => $request->stock_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                if($request->wantsJson()){
                    return Response()->json(['success' => 'Sales created successfully!']);
                }
                return Redirect::route('sales')->with('success', 'Sales created successfully!');
            }
            catch(\Exception $e){
                // dd($e->getMessage());
                if($request->wantsJson()){
                    return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
                }
                return Redirect::route('sales')->with('error', 'Sorry something went worng. Please try again.');
            }
        }
    }



    public function destroy($id)
    {
        try{
            Sales::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
        }
    }
}
