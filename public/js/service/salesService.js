angular.module('salesService', [])
.factory('Sales', function($http){
	
	return {
		get: function(){
			return $http.get('/api/dashboard/sales');
		},
        getSalesMen: function(){
			return $http.get('/api/dashboard/getsalesMen');
		},
        getUsers: function(){
			return $http.get('/api/dashboard/getUsers');
		},
        getStock: function(){
			return $http.get('/api/dashboard/getstock');
		},
		getStockbyID: function(id){
			return $http.get('/api/dashboard/getStockbyId/'+id);
		},
        check_validiti: function(id){
			return $http.get('/api/dashboard/check_validiti/'+id);
		},
		save: function(newData){
			return $http({
				method: 'POST',
				url: '/dashboard/create/sales/save',
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(newData)
			});
		},
		deleteRow: function(id){
			return $http({
				method: 'GET',
				url: '/dashboard/sales/delete/'+id,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
			});
		}
	}
})