app.factory('CollegeCourse', ['$http', function ($http) {

return {    
getCollegeCourse: function () {
            return $http.get('api/getcourseby/'+cid);
        }
    }

}]);

app.factory('Coursebycollege', ['$http', function ($http) {

return {  
getCoursebycollege: function () {
        return $http.get('api/getcourse');
        }
    }	
	
     }]);

app.factory('FeebyCourse', ['$http', function ($http) {

return {    
getFeebyCourse: function (courseid) {
            var curSes=$('select.reg_cur_sessn').val();
            return $http.get('../api/feebycourse/'+courseid+'/'+curSes);
        }
    }	

    
	
     }]);



app.factory('formfeebyCourse', ['$http', function ($http) {

return {    
getformfeeCourse: function (id,host) {
            var urls = host+'/api/getfeebycourse/'+id; 
            return $http.get(urls);
        }
    }
    
     }]);




