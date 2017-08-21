@extends('admin.layouts.app')

@section('header')
    <h1>
        Users
    </h1>
@endsection
@section('content')
    <div class="row" ng-app="userApp" ng-controller="userController" ng-cloak>
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
                        <button type="button" ng-click="loadModal()" class="btn btn-primary btn-sm">Add new user</button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th ng-click="sort('first_name')" class="cursor-pointer">First name
                                <span class="glyphicon sort-icon" ng-show="sortKey=='first_name'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span>
                            </th>
                            <th>Last name</th>
                            <th>Email</th>
                            <th>Phone #</th>
                            <th>Adderss</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr dir-paginate="record in Users|orderBy:sortKey:reverse|filter:search|itemsPerPage:10">
                            <td>@{{ record.first_name }}</td>
                            <td>@{{ record.last_name }}</td>
                            <td>@{{ record.email }}</td>
                            <td>@{{ record.phone }}</td>
                            <td>@{{ record.address }}</td>
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
                <div id="addDriver" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content border-radius">
                            <div class="modal-header">
                                <div class="col-md-1 loader" ng-if="isDisabled"><img src="{{ asset('admin/dist/img/loading.gif') }}" alt="Data uploading...." /></div>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" ng-if="newData.id">{{trans('list.updateDriver')}}</h4>
                                <h4 class="modal-title" ng-if="!newData.id">Add New User</h4>
                            </div>
                            <div class="modal-body">
                                <form ng-submit="submitForm()" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="id" ng-model="newData.id" value="" />
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="first_name">First Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="first_name" ng-model="newData.first_name" placeholder="First name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="last_name">Last Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="last_name" ng-model="newData.last_name" placeholder="Last name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Refer by :</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="parent_id" ng-model="newData.parent_id"
                                                    ng-options="row.id as row.first_name for row in Users">
                                                <option value="" selected disabled>-- Refer by  --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="email">Email Adress:</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control" name="email" ng-model="newData.email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Phone">Phnoe #:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="phone" ng-model="newData.phone" placeholder="Phone no" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Address">Address:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="address" ng-model="newData.address" placeholder="Address" required>
                                        </div>
                                    </div>
                                    <div class="form-group" ng-if="!newData.id">
                                        <label class="control-label col-sm-4" for="last_name">Password:</label>
                                        <div class="col-sm-8">
                                            <input type="password" class="form-control" name="password" ng-model="newData.password" placeholder="Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group" ng-if="!newData.id">
                                        <label class="control-label col-sm-4" for="password_confirmation">Confrom Password:</label>
                                        <div class="col-sm-8">
                                            <input type="password" class="form-control" name="password_confirmation" ng-model="newData.password_confirmation" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" ng-if="newData.id" ng-disabled="isDisabled" class="btn btn-success">Update</button>
                                            <button type="submit" ng-if="!newData.id" ng-disabled="isDisabled" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-danger" ng-click="closeForm()">Cancel</button>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="alert @{{ alert_class }} text-center custom-margin" ng-if="isResponse">
                                                @{{ message }}
                                            </div>
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
    <script src="{{ asset('/js/userapp.js') }}"></script>
    <script src="{{ asset('/js/dirPagination.js') }}"></script>
    <script>
        myApp.constant("CSRF_TOKEN", '{!! csrf_token() !!}')
        myApp.constant("user_type", '0')
    </script>
    <script src="{{ asset('/js/controller/userController.js') }}"></script>
    <script src="{{ asset('/js/service/userService.js') }}"></script>
@endsection