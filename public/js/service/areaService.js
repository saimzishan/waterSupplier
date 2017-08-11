angular.module('areaService', [])
.factory('Area', function($http){
	
	return {
		get: function(){
			return $http.get('/api/dashboard/areaList');
		},
		save: function(newData){
			return $http({
				method: 'POST',
				url: '/dashboard/create/area/save',
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(newData)
			});
		},
		deleteRow: function(id){
			return $http({
				method: 'GET',
				url: '/dashboard/area/delete/'+id,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
			});
		}
	}
})