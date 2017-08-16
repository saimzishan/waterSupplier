@extends('admin.layouts.app')

@section('header')
    <h1>
        Sales
    </h1>
@endsection
@section('content')
    <div class="row" ng-app="salesApp" ng-controller="salesController" ng-cloak>
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-1 data-loader" ng-if="isDisabled"><img src="{{ asset('admin/dist/img/loading.gif') }}" alt="Data uploading...." /></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <div class="alert alert-success text-center custom-margin" ng-if="notification">
                            @{{ message }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-4">Search:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" ng-model="search">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-2 text-right">
                        <button type="button" ng-click="loadModal()" class="btn btn-primary btn-sm">New Sale</button>
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
                            <th>Solid To</th>
                            <th>From Stock</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr dir-paginate="record in Users|orderBy:sortKey:reverse|filter:search|itemsPerPage:10">
                            <td>@{{ record.quantity }}</td>
                            <td>@{{ record.saleMen }}</td>
                            <td>@{{ record.first_name }} @{{ record.last_name }}</td>
                            <td>@{{ record.product_name }}</td>
                            <td>@{{ record.created_at }}</td>
                            <td style="width: 15%;">
                                <button type="button" class="btn btn-small btn-xs btn-warning" ng-click="editAction(record)">Edit</button> |
                                <button type="button" class="btn btn-small btn-xs btn-danger" ng-click="deleteModal(record.id)">Delete</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <dir-pagination-controls
                            max-size="100"
                            direction-links="true"
                            boundary-links="true" >
                    </dir-pagination-controls>
                </div>
                <!-- /.box-body -->
                <!-- Add/update Modal -->
                <div id="addStock" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content border-radius">
                            <div class="modal-header">
                                <div class="col-md-1 loader" ng-if="isDisabled"><img src="{{ asset('admin/dist/img/loading.gif') }}" alt="Data uploading...." /></div>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" ng-if="newData.id">Update Sale</h4>
                                <h4 class="modal-title" ng-if="!newData.id">New Sale</h4>
                            </div>
                            <div class="modal-body">
                                <form ng-submit="submitForm()" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="id" ng-model="newData.id" value="" />
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Select SaleMen:</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="salesmen_id" ng-model="newData.salesmen_id"
                                                    ng-options="row.id as row.first_name for row in salesMen" ng-change="get_StockByID(newData.salesmen_id)" required>
                                                <option value="" selected disabled>-- Select Sales Men --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Solid to User:</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="user_id" ng-model="newData.user_id"
                                                    ng-options="row.id as row.last_name for row in users" required>
                                                <option value="" selected disabled>-- Select User --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="product_name">Quantity:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control number-only" name="quantity" ng-model="newData.quantity" placeholder="Product name" required>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger text-center custom-margin" ng-if="isError">
                                                Please enter less then <strong class="text-info"> @{{ isData }}</strong> and grater then <strong class="text-info"> 0 </strong>.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-8">
                                            <input class="form-control number-only" type="hidden" name="stock_id" ng-model="newData.stock_id" readonly>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="alert alert-warning text-center custom-margin" ng-if="isData">
                                               Total Quantity in selected item is <strong class="text-info"> @{{ isData }}</strong> , Please enter less then <strong class="text-info"> @{{ isData }}</strong>.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" ng-if="newData.id" ng-disabled="isDisabled" class="btn btn-success">Update</button>
                                            <button type="submit" ng-if="!newData.id" ng-disabled="isDisabled" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-danger" ng-click="closeForm()">Cancel</button>
                                        </div>
                                            <div class="alert @{{ alert_class }} text-center custom-margin" ng-if="isResponse">
                                                @{{ message }}
                                            </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <!--End Add Modal -->
                <!-- Delete Modal -->
                <div id="confirmDelete" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Delete user</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure want to delete</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" ng-click="deleteAction(delete_id)">Delete</button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
@endsection

@section('script')
    <script src="{{ asset('/js/salesApp.js') }}"></script>
    <script src="{{ asset('/js/dirPagination.js') }}"></script>
    <script>
        myApp.constant("CSRF_TOKEN", '{!! csrf_token() !!}')
    </script>
    <script src="{{ asset('/js/controller/salesController.js') }}"></script>
    <script src="{{ asset('/js/service/salesService.js') }}"></script>
    <script>
        $('.number-only').numeric();
    </script>
@endsection