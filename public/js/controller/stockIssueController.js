
angular.module('stockIssueController', [])
.controller('stockIssueController', function($scope, $http, StockIssue, CSRF_TOKEN, $timeout){

	$scope.isResponse = false;
	$scope.alert_class = '';
	$scope.message = '';
	$scope.delete_id = '';
	$scope.notification = false;
	$scope.isDisabled = false;
	$scope.newData = {};
	$scope.isData = false;
	$scope.isError = false;
    $scope.preData = false;
    $scope.stock_id = false;

    var loadData = function(){
		$scope.isDisabled = true;
		StockIssue.get()
		.success(function(data){
			$scope.Users = data;
			$scope.isDisabled = false;
		});
		StockIssue.getSalesMen()
		.success(function(data){
			$scope.salesMen = data;
			$scope.isDisabled = false;
		});
		StockIssue.getStock()
		.success(function(data){
			$scope.stock = data;
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
		if ($scope.newData.quantity > $scope.isData || $scope.newData.quantity < 1){
			$scope.isError = true;
            $timeout(function(){
                $scope.isError = false;
            }, 2000);
        return;
		}
		$scope.isDisabled = true;
		$scope.newData._token = CSRF_TOKEN;
        StockIssue.save($scope.newData)
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
				}, 4000);
			}
		});
	},
	$scope.editAction = function(data){
		$scope.newData = {};
		$scope.newData.salesmen_id = data.salesmen_id;
		$scope.newData.quantity = data.quantity;
		$scope.newData.stock_id = data.stock_id;
		$scope.stock_id = data.stock_id;
		$scope.newData.id = data.id;
        $scope.preData = data.quantity;
        $scope.check_validity($scope.newData.stock_id);
		$scope.openModal();
	},
	$scope.deleteModal = function(id){
		$("#confirmDelete").modal('show');
		$scope.delete_id = id;
	},
	$scope.deleteAction = function(id){
        StockIssue.deleteRow(id)
		.success(function(response){
			loadData();
			$scope.closeForm();
			$scope.notification = true;
			$scope.message = 'Record deleted successfully!';
			$timeout(function(){
				$scope.notification = false;
				$scope.message = '';
			}, 4000);
		});
	},
	$scope.closeForm = function(){
		$("#addStock,#confirmDelete").modal('hide');
		$scope.isResponse = false;
		$scope.alert_class = '';
		$scope.message = '';
		$scope.newData = {};
		$scope.isData = false;
	},
	$scope.openModal = function(){
		$("#addStock").modal('show');
	}
    $scope.check_validity = function(id) {
        StockIssue.getStockbyID(id)
            .success(function(data){
                $scope.isData = parseInt(data.product_quantity) - parseInt(data.issued);

                if($scope.stock_id == id)
                {
                    $scope.isData = parseInt(data.product_quantity) - parseInt(data.issued) ;
                    $scope.isData = parseInt($scope.isData) + parseInt($scope.preData);
                }
            });
	}
	
})