
angular.module('areaController', [])
.controller('areaController', function($scope, $http, Area, CSRF_TOKEN, $timeout){

	$scope.isResponse = false;
	$scope.alert_class = '';
	$scope.message = '';
	$scope.delete_id = '';
	$scope.notification = false;
	$scope.isDisabled = false;
	$scope.newData = {};
	
	var loadData = function(){
		$scope.isDisabled = true;
		Area.get()
		.success(function(data){
			$scope.Users = data;
			$scope.isDisabled = false;
		});
	};
	loadData();
	$scope.sort = function(keyname){
		$scope.sortKey = keyname;   //set the sortKey to the param passed
		$scope.reverse = !$scope.reverse; //if true make it false and vice versa
	},
	
	$scope.loadModal = function(){
		$scope.openModal();
		$scope.newData = {};
	},
	$scope.submitForm = function(){
		$scope.isDisabled = true;
		$scope.newData._token = CSRF_TOKEN;
        Area.save($scope.newData)
		.success(function(data){
			if(data.success){
				loadData();
				$scope.isDisabled = false;
				$scope.isResponse = true;
				$scope.alert_class = 'alert-success';
				$scope.message = data.success;
				$scope.newData = {};
				$timeout(function(){
					$scope.isResponse = false;
					$scope.alert_class = '';
					$scope.message = '';
					$scope.closeForm();
				}, 1500);
			} else {
				$scope.isDisabled = false;
				$scope.isResponse = true;
				$scope.alert_class = 'alert-danger';
				$scope.message = data.error;
				$timeout(function(){
					$scope.isResponse = false;
					$scope.alert_class = '';
					$scope.message = '';
				}, 1500);
			}
		});
	},
	$scope.editAction = function(data){
		$scope.newData = {};
		$scope.newData.area_name = data.area_name;
		$scope.newData.id = data.id;
		$scope.openModal();
	},
	$scope.deleteModal = function(id){
		$("#confirmDelete").modal('show');
		$scope.delete_id = id;
	},
	$scope.deleteAction = function(id){
        Area.deleteRow(id)
		.success(function(response){
			loadData();
			$scope.closeForm();
			$scope.notification = true;
			$scope.message = 'Record deleted successfully!';
			$timeout(function(){
				$scope.notification = false;
				$scope.message = '';
			}, 2000);
		});
	},
	$scope.closeForm = function(){
		$("#addArea,#confirmDelete").modal('hide');
		$scope.isResponse = false;
		$scope.alert_class = '';
		$scope.message = '';
		$scope.newData = {};
	},
	$scope.openModal = function(){
		$("#addArea").modal('show');
	}
	
})