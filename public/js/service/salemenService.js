angular.module('salemenService', [])
.factory('Salemen', function($http){
	
	return {
		get: function(){
			return $http.get('/api/dashboard/salemenList');
		},
        getArea: function(){
            return $http.get('/api/dashboard/getArea');
        },
		save: function(newData){
			return $http({
				method: 'POST',
				url: '/dashboard/create/salemen/save',
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(newData)
			});
		},
		deleteRow: function(id){
			return $http({
				method: 'GET',
				url: '/dashboard/salemen/delete/'+id,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
			});
		}
	}
})