<?php

namespace App\Http\Controllers;

use App\Salemen;
use App\Stock;
use App\StockIssue;
use App\Sales;
use App\User;
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
        if($request->is('api/*')){
            $stackData = DB::table('sales')
                ->select(['sales.*','salesmen.first_name as saleMen', 'stock.product_name', 'users.first_name', 'users.last_name'])
                ->join('salesmen', 'sales.salesmen_id', '=', 'salesmen.id')
                ->join('stock', 'sales.stock_id', '=', 'stock.id')
                ->join('users', 'sales.user_id', '=', 'users.id')
                ->where('sales.deleted_at', null)->get();
           // dd($stackData);
            return response()->json($stackData);
        }
        return view('admin.sales.index');
    }
    public function getSalesMen(Request $request)
    {
        $saleMen = DB::table('salesmen')
            ->where('stock_issue', 1)
            ->select(['salesmen.*'])->get();
        if($request->is('api/*')){
            return response()->json($saleMen);
        }
    }
    public function getUsers(Request $request)
    {
       $users =  DB::table('users')
            ->select(['users.*',])
            ->where('user_type', 0)->get();
        if($request->is('api/*')){
            return response()->json($users);
        }
    }
    public function getStock(Request $request)
    {
        $stock = Stock::all();
        if($request->is('api/*')){
            return response()->json($stock);
        }
    }
    public function getSalesByID($id)
    {

        // return ('worge');
        $sales = DB::table('sales')
            ->select(['sales.*','salesmen.first_name', 'stock.product_name'])
            ->join('salesmen', 'sales.salesmen_id', '=', 'salesmen.id')
            ->join('stock', 'sales.stock_id', '=', 'stock.id')
            ->where('sales.deleted_at', null)
            ->where('salesmen_id', $id)->get();
        return view('admin.sales.sale', compact('sales'));

    }
    public function getStockbyID(Request $request, $id)
    {
        $stackData = DB::table('stockissue')
            ->select(['stockissue.*', 'stock.*'])
            ->join('stock', 'stockissue.stock_id', '=', 'stock.id')
            ->where('stockissue.salesmen_id', $id)
            ->first();
        if($request->is('api/*')){
            return response()->json($stackData);
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
                'user_id' => 'required',
                'stock_id' => 'required'
            ];
        } else {
            $rules = [
                'quantity' => 'required',
                'salesmen_id' => 'required',
                'user_id' => 'required',
                'stock_id' => 'required'
            ];
        }

        $message = [
            'quantity.required' => 'Quantity Name field is required.',
            'salesmen_id.required' => 'Salesmen ID Quantity field is required.',
            'user_id.required' => 'userID Quantity field is required.',
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
                    $temp = DB::table('stockissue')
                        ->select('solid', 'quantity')
                        ->where('id', $request->id)->first();
                    $updateStockIssue = $request->quantity;
                    if($temp->solid == $temp->quantity)
                    {
                        return Response()->json(['error' => 'Sorry you can not update this record, becoze this record is solid out' ]);
                    }
                    $Sale = DB::table('stockissue')
                        ->select('solid')
                        ->where('id', $request->id)->first();
                    $updateStockIssue = $request->quantity;
                    if($Sale->solid > $request->quantity )
                    {
                        $newSale = $Sale->solid - $request->quantity;
                        $updateStockIssue = $Sale->solid - $newSale;
                    }
                    if($Sale->solid < $request->quantity )
                    {
                        $newSale = $request->quantity - $Sale->solid;
                        $updateStockIssue = $Sale->solid + $newSale;
                    }
                    $updation = StockIssue::where('stock_id', $request->stock_id)->update([
                        'solid' => $updateStockIssue,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $createSales = Sales::where('id', $request->id)->update([
                        'quantity' => $request->quantity,
                        'salesmen_id' => $request->salesmen_id,
                        'user_id' => $request->user_id,
                        'stock_id' => $request->stock_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Sales information updated successfully!']);
                    }
                    return Redirect::route('sales')->with('success', 'Sales updated successfully!');
                }
                // insertation
                $temp = DB::table('sales')
                    ->select('user_id', 'salesmen_id', 'quantity')
                    ->where('stock_id', $request->stock_id)->first();
                 if($temp) {
                     if( ($temp->salesmen_id == $request->salesmen_id) && ($temp->user_id == $request->user_id) )
                     {
                         // dd('yes');
                         $temp = $temp->quantity + $request->quantity;
                          dd($temp);
                         $updation = Sales::where('stock_id', $request->stock_id && 'user_id',$request->user_id)->update([
                             'quantity' => $temp,
                             'updated_at' => date('Y-m-d H:i:s')
                         ]);
                         $updation = StockIssue::where('stock_id', $request->stock_id && 'salesmen_id',$request->salesmen_id)->update([
                             'solid' => $temp,
                             'updated_at' => date('Y-m-d H:i:s')
                         ]);
                         return Response()->json(['success' => 'Solid successfully!']);
                     }
                 }
                 if(!$temp)
                 {
                     $updation = StockIssue::where('stock_id', $request->stock_id)->update([
                         'solid' => $request->quantity,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
                 }else if( $temp && ( $temp->stock_id == $request->stock_id || $temp && $temp->user_id == $request->user_id ))
                 {
                     $temp = $temp->quantity + $request->quantity;
                     $updateStock = StockIssue::where('stock_id', $request->stock_id)->update([
                         'solid' => $temp,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
                 }
                $createSales = Sales::create([
                    'quantity' => $request->quantity,
                    'salesmen_id' => $request->salesmen_id,
                    'user_id' => $request->user_id,
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
                    return Response()->json(['error' => $e->getMessage()]);
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
