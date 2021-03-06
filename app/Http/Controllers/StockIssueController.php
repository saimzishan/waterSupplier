<?php

namespace App\Http\Controllers;

use App\Salemen;
use App\Stock;
use App\StockIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class StockIssueController extends Controller
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
            $stackData = DB::table('stockissue')
                ->select(['stockissue.*','salesmen.first_name', 'stock.product_name'])
                ->join('salesmen', 'stockissue.salesmen_id', '=', 'salesmen.id')
                ->join('stock', 'stockissue.stock_id', '=', 'stock.id')
                ->where('stockissue.deleted_at', null)
                ->get();
            return response()->json($stackData);
        }
        return view('admin.stockIssue.index');
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
        $stockByID = Stock::where('id', $id)->select('product_quantity', 'issued')->first();
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
                   // dd($request->id);
                    $stockIssued = DB::table('stockissue')
                        ->select('quantity')
                        ->where('salesmen_id', $request->salesmen_id)
                        ->where('stock_id', $request->stock_id)->first();

                    $stockissue = DB::table('stockissue')
                        ->select('stock_id')
                        ->where('id', $request->id)->first();
                    $stock = DB::table('stock')
                        ->select('issued')
                        ->where('id', $stockissue->stock_id)->first();

                    $newData = $stockIssued->quantity;
                    $newIssued = $stock->issued;

                    if($stockIssued->quantity > $request->quantity){
                        $newData = $stockIssued->quantity - $request->quantity;
                        $newIssued = $stock->issued - $newData;

                        $newData = $stockIssued->quantity - $newData;
                    }
                    if($stockIssued->quantity < $request->quantity){
                        $newData = $request->quantity - $stockIssued->quantity;
                        $newIssued = $stock->issued + $newData;

                        $newData = $stockIssued->quantity + $newData;
                    }

                    $updation = StockIssue::where('salesmen_id', $request->salesmen_id && 'stock_id', $request->stock_id )->update([
                        'quantity' => $newData,
                        'salesmen_id' => $request->salesmen_id,
                        'stock_id' => $request->stock_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $updation = Stock::where('id', $stockissue->stock_id)->update([
                        'issued' => $newIssued,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);


                    if($request->wantsJson()){
                        return Response()->json(['success' => 'StockIssue information updated successfully!']);
                    }
                    return Redirect::route('stockIssue')->with('success', 'StockIssue updated successfully!');
                }

                $stockIssued = DB::table('stockissue')
                    ->select('solid', 'quantity', 'id')
                    ->where('salesmen_id', $request->salesmen_id)
                    ->where('stock_id', $request->stock_id)->first();
                 if($stockIssued) {
                     $quantity = $stockIssued->quantity + $request->quantity;
                     $updation = StockIssue::where('id', $stockIssued->id)->update([
                         'quantity' => $quantity,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
                 }
                if(!$stockIssued) {
                    $createStockIssue = StockIssue::create([
                        'quantity' => $request->quantity,
                        'salesmen_id' => $request->salesmen_id,
                        'stock_id' => $request->stock_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                $stockIssued = DB::table('stock')
                    ->select('issued')
                    ->where('id', $request->stock_id)->first();
                $quantity = $stockIssued->issued + $request->quantity;
                $updation = Stock::where('id', $request->stock_id)->update([
                    'issued' => $quantity,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $updation = Salemen::where('id', $request->salesmen_id)->update([
                    'stock_issue' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                if($request->wantsJson()){
                    return Response()->json(['success' => 'StockIssue created successfully!']);
                }
                return Redirect::route('stockIssue')->with('success', 'StockIssue created successfully!');
            }
            catch(\Exception $e){
                // dd($e->getMessage());
                if($request->wantsJson()){
                    return Response()->json(['error' => $e->getMessage() ]);
                }
                return Redirect::route('stockIssue')->with('error', 'Sorry something went worng. Please try again.');
            }
        }
    }



    public function destroy($id)
    {
        try{
            StockIssue::destroy($id);
            return Response()->json(array('success' => 'Record deleted successfully!'));
        } catch(\Exception $e){
            return Response()->json(['error' => 'Sorry something went worng. Please try again.']);
        }
    }
}
