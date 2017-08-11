angular.module('stockService', [])
.factory('Stock', function($http){
	
	return {
		get: function(){
			return $http.get('/api/dashboard/stockList');
		},
		save: function(newData){
			return $http({
				method: 'POST',
				url: '/dashboard/create/stock/save',
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(newData)
			});
		},
		deleteRow: function(id){
			return $http({
				method: 'GET',
				url: '/dashboard/stock/delete/'+id,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
			});
		}
	}
})