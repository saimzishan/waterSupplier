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
                        ->select('solid', 'quantity', 'id')
                        ->where( 'stock_id', $request->stock_id)
                        ->where( 'salesmen_id', $request->salesmen_id)->first();

                    if($temp->solid == $temp->quantity)
                    {
                        return Response()->json(['error' => 'Sorry you can not update this record, becoze this record is solid out' ]);
                    }
                    $SaleUpdate = DB::table('sales')
                        ->select('quantity', 'price')
                        ->where( 'id', $request->id)
                        ->first();
                    $saleTemp = $SaleUpdate->quantity;
                    $price = $SaleUpdate->price;

                    $stockIssued = $temp;
                    $updateIssue = $stockIssued->solid;
                    if($SaleUpdate->quantity > $request->quantity )
                    {
                        $newSale = $SaleUpdate->quantity - $request->quantity;
                        $updateIssue = $updateIssue - $newSale;

                        $saleTemp = $SaleUpdate->quantity - $newSale;
                        $price = $saleTemp*20;
                    }
                    if($SaleUpdate->quantity < $request->quantity )
                    {
                        $newSale = $request->quantity - $SaleUpdate->quantity;
                        $updateIssue = $updateIssue + $newSale;

                        $saleTemp = $SaleUpdate->quantity + $newSale;
                        $price = $saleTemp*20;
                    }
                    $createSales = Sales::where('id', $request->id)->update([
                        'quantity' => $saleTemp,
                        'price' => $price,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $updation = StockIssue::where('id', $stockIssued->id)->update([
                        'solid' => $updateIssue,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);


                    if($request->wantsJson()){
                        return Response()->json(['success' => 'Sales information updated successfully!']);
                    }
                    return Redirect::route('sales')->with('success', 'Sales updated successfully!');
                }

                // new insertation code start here
                $temp = DB::table('sales')
                    ->select('user_id', 'salesmen_id', 'quantity', 'id')
                    ->where('stock_id', $request->stock_id)
                    ->where('user_id', $request->user_id)
                    ->where('salesmen_id', $request->salesmen_id)->first();
                   if($temp) {
                     $temps = $temp->quantity + $request->quantity;
                     $price = $temps * 20;
                       $updation = Sales::where('id', $temp->id)->update([
                           'quantity' => $temps,
                           'price' => $price,
                           'updated_at' => date('Y-m-d H:i:s')
                       ]);
                   $temp1 = DB::table('stockissue')
                       ->select('solid')
                       ->where('stock_id', $request->stock_id)
                       ->where('salesmen_id', $request->salesmen_id)->first();
                   $temps = $temp1->solid + $request->quantity;
                   $updateStock = StockIssue::where('stock_id', $request->stock_id)->update([
                       'solid' => $temps,
                       'updated_at' => date('Y-m-d H:i:s')
                   ]);
                   return Response()->json(['success' => 'Solid successfully!']);
               } else if (!$temp){
                   $temp1 = DB::table('sales')
                       ->select('user_id', 'salesmen_id', 'quantity', 'id')
                       ->where('user_id', $request->user_id)
                       ->where('salesmen_id', $request->salesmen_id)->first();
                   if($temp1) {
                       $temps = $temp1->quantity + $request->quantity;
                       $price = $temps * 20;
                       $updation = Sales::where('id', $temp1->id)->update([
                           'quantity' => $temps,
                           'price' => $price,
                           'updated_at' => date('Y-m-d H:i:s')

                       ]);
                   }
                   $temp1 = DB::table('stockissue')
                       ->select('solid')
                       ->where('stock_id', $request->stock_id)
                       ->where('salesmen_id', $request->salesmen_id)->first();
                   $temps = $temp1->solid + $request->quantity;
                       $updateStock = StockIssue::where('stock_id', $request->stock_id)->update([
                           'solid' => $temps,
                           'updated_at' => date('Y-m-d H:i:s')
                       ]);
               } else {
                   /*$updation = StockIssue::where('stock_id', $request->stock_id)->update([
                       'solid' => $request->quantity,
                       'updated_at' => date('Y-m-d H:i:s')
                   ]);*/
               }
               $price = $request->quantity * 20;
              $createSales = Sales::create([
                  'quantity' => $request->quantity,
                  'salesmen_id' => $request->salesmen_id,
                  'user_id' => $request->user_id,
                  'stock_id' => $request->stock_id,
                  'price' => $price,
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
