angular.module('stockIssueService', [])
.factory('StockIssue', function($http){
	
	return {
		get: function(){
			return $http.get('/api/dashboard/stockIssue');
		},
        getSalesMen: function(){
			return $http.get('/api/dashboard/getSalesMen');
		},
        getStock: function(){
			return $http.get('/api/dashboard/getStock');
		},
		getStockbyID: function(id){
			return $http.get('/api/dashboard/getStockbyID/'+id);
		},
		save: function(newData){
			return $http({
				method: 'POST',
				url: '/dashboard/create/stockIssue/save',
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(newData)
			});
		},
		deleteRow: function(id){
			return $http({
				method: 'GET',
				url: '/dashboard/stockIssue/delete/'+id,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
			});
		}
	}
})