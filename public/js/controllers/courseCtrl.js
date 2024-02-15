
app.controller('feeCtrl', function($scope, $http ,CollegeCourse,FeebyCourse,Coursebycollege,formfeebyCourse,) {
    var today = new Date();
     $scope.toggle = {};
  $scope.toggle.switch = false;
  
  var host = window.location.origin;



$scope.selectcourse = function()
{
    var cid =  $scope.branch_id;
     Coursebycollege.getCoursebycollege().
     then(function (responce) 
    {
    $scope.allcoursedata=responce.data.data;
     // console.log($scope.allcoursedata);
    });
   


}

$scope.feechange= function()
{
id= $scope.formfeebycourse;

formfeebyCourse.getformfeeCourse(id,host).
     then(function (responce) 
    {
    $scope.coursefees=responce.data.data[0].form_fees;
     // console.log($scope.coursefees);
    });
}
	
$scope.update = function () 
{ 
	
	
    cid =$scope.selectedcollege;
	
	// course = course.trim();
	// session =$scope.selectedsession;
	CollegeCourse.getCollegeCourse(cid).
	 then(function (responce) 
	{
    $scope.allcoursedata=responce.data.data;
     

  });
}

 $scope.myVar = false;
    $scope.isExpanded = !$scope.isExpanded;
    $scope.toggle = function() {
        $scope.myVar = !$scope.myVar;
    };

 $scope.myadd = false;
    $scope.isExpanded = !$scope.isExpanded;
    $scope.addtoggle = function() {
        $scope.myadd = !$scope.myadd;
    };

$scope.mytempadd = false;
    $scope.isExpanded = !$scope.isExpanded;
    $scope.tempaddtoggle = function() {
        $scope.mytempadd = !$scope.mytempadd;
    };



    $scope.coursechange= function()
    {
        
        var courseid = $scope.selectedCourse;
    FeebyCourse.getFeebyCourse(courseid).
     then(function (responce) 
    {
    $scope.feedata=responce.data.data;
    console.log(courseid);
     
    });
    }

    $scope.categoryselect = function(category_id)
    {
        //console.log(category_id)
        if (category_id == 'General'|| category_id =='O.B.C.') {
        var minAge = 17;
        var maxAge = 25
        $scope.minAge = new Date(today.getFullYear() - minAge, today.getMonth(), today.getDate());
        $scope.maxAge = new Date(today.getFullYear() - maxAge, today.getMonth(), today.getDate());
        $scope.msg= 'You must between 17  to 25 years old ';
        //console.log($scope.msg)

        }
        else {

            var minAge = 17;
            var maxAge = 30
        $scope.minAge = new Date(today.getFullYear() - minAge, today.getMonth(), today.getDate());
        $scope.maxAge = new Date(today.getFullYear() - maxAge, today.getMonth(), today.getDate()); 
         $scope.msg= 'You must between 17 and 1/2  to 30 years old ';
        }

       
    }



    $scope.studentfgfjselect =function()
    {

        console.log('helloo');
    }

});



