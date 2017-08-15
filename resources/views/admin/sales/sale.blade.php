@extends('admin.layouts.app')

@section('header')
<h1>
    Sales
</h1>
@endsection
@section('content')
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <div class="col-md-1"></div>
                <div class="col-md-2"></div>
                <div class="col-md-4">
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th ng-click="sort('product_name')" class="cursor-pointer">Product quantity
                            <span class="glyphicon sort-icon" ng-show="sortKey=='product_name'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span>
                        </th>
                        <th>Solid by</th>
                        <th>From Stock</th>
                        <th>Created at</th>
                    </tr>
                    </thead>
                    @foreach($sales as $sale)
                        <tbody>
                        <tr>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ $sale->first_name }}</td>
                            <td>{{ $sale->product_name }}</td>
                            <td>{{ $sale->created_at }}</td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
@endsection
