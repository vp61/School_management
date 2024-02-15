<div class="footer">
    <div class="footer-inner hidden-print">
        <div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder">ASHA EDUCATION GROUP</span>
			</span>
			<span class="bigger-25">
				
				<span class="blue bolder"> - Powered by &copy; adminmitra.com</span>
			</span>

            {{--<span class="action-buttons">
				<a href="#">
					<i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
				</a>

				<a href="#">
					<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
				</a>

				<a href="#">
					<i class="ace-icon fa fa-rss-square orange bigger-150"></i>
				</a>
			</span>--}}
        </div>
    </div>
	{{--<footer class="onlyprint">footer text for print<!--Content Goes Here--></footer>--}}
</div>
<?php
$cameraIncludeStatus = 0;

$pathUr = $_SERVER['REQUEST_URI'];
if (strpos($pathUr,'edit') !== false && strpos($pathUr,'student') !== false) {
    $cameraIncludeStatus = 1;
}

if (strpos($pathUr,'registration') !== false && strpos($pathUr,'student') !== false) {
   $cameraIncludeStatus = 1;
}

?>
<!-- basic scripts -->
<!--[if !IE]> -->
<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>

<script src="{{ asset('assets/js/table2excel.js') }}"></script>
{{--<script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>--}}
<script src="{{ asset('assets/js/webcam.min.js') }}"></script>

<!-- Configure a few settings and attach camera -->
<?php
	if($cameraIncludeStatus == 1){
?>
<script language="JavaScript">
    Webcam.set({
        width: 290,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
  
    Webcam.attach( '#my_camera' );
  
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }
</script>
<?php } ?>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>
<![endif]-->

<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('assets/js/jquery.mobile.custom.min.js') }}'>"+"<"+"/script>");
</script>

<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

{{--<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>--}}

<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('js/angular.min.js') }}"></script>
<script>
var app=angular.module('Gmodule', []);
app.controller('expandCtrl', function($scope) {
   
    $scope.myadd = false;
    $scope.isExpanded = !$scope.isExpanded;
    $scope.addtoggle = function() {
        $scope.myadd = !$scope.myadd;
    };
});
app.controller('tempaddCtrl', function($scope) {
   
    $scope.mytempadd = false;
    $scope.isExpanded = !$scope.isExpanded;
    $scope.tempaddtoggle = function() {
        $scope.mytempadd = !$scope.mytempadd;
    };
});
</script> 
<script src="{{ asset('js/app/app.js') }}"></script>
<script src="{{ asset('js/controllers/courseCtrl.js') }}"></script>
<script src="{{ asset('js/service/service.js') }}"></script>

<!-- Added JS & CSS to make Seletor with Search-->
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script>
	$(function(){
		$('.head').click(function(){
			var cHk=$(this).prop('checked');
			var selector=".dspl_"+$(this).prop("id");
			//alert("inside ==> "+cHk+" >>"+$(this).prop("id"));
			if(cHk){ $(selector).slideDown(500);
				$(selector).find('.ttla').attr('required', 'required');
			}else{ 
				$(selector).slideUp(500);
				$(selector).find('.ttla').attr('required', '');
			}
		});

		$('.branch_drop').change(function(){
			var id=$(this).val(); var session = $('.sesn').val();
			$.post("{{route('branch_select')}}", {frm:'brncH', vl:id, _token:'{{ csrf_token() }}'}, function(response){
				$('.cors').html(response);
			});
		});

		$('.cors').change(function(){
			var cors_id = $(this).val(); 
			var brnch=$('.branch_drop').val();
			var selected_session=$('.sesn').val();
			var smstr=$('.semester').val();
			$.post("{{route('student_select')}}", {branch:brnch,selected_session:selected_session,semester:smstr,course:cors_id, _token:'{{ csrf_token() }}'}, function(response){
				$('select.student').html(response);
				$('.selectpicker').selectpicker('refresh');
			});
		});

		$('.semester').change(function(){
			var cors_id = $('#course_drop').val();
			//alert("==>"+cors_id);
			var brnch=$('.branch_drop').val();
			var selected_session=$('.sesn').val();
			var smstr=$(this).val();
			$.post("{{route('student_select')}}", {branch:brnch,selected_session:selected_session,semester:smstr,course:cors_id, _token:'{{ csrf_token() }}'}, function(response){
				$('select.student').html(response);
				$('.selectpicker').selectpicker('refresh');
			});
		});

		$('.student').change(function(){
	var cors_id=$('select.cors').val(); 
	var stud = $(this).val(); 
	var brnch=$('.branch_drop').val(); 
	var session=$('.sesn').val();
			$.post("{{route('student_fee')}}", {branch:brnch, ssn:session, course:cors_id, student:stud, _token:'{{ csrf_token() }}'}, function(response){
				
				$('.fee_box').html(response);
			});
			// payment history 
			$.post("{{route('student_fee_history')}}", {branch:brnch, ssn:session, course:cors_id, student:stud, _token:'{{ csrf_token() }}'}, function(response){
				
				$('#dynamic-table-fee-list').html(response);
			});
		});
	});
	
	
	$(document).ready(function () {
      //called when key is pressed in textbox
      $(".mobileKValidationCheck").keypress(function (e) {
         //if the letter is not digit then display error and don't type anything
         if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            // $(".errmsgMobile").html("Mobile number should be 10 digit numeric only.").show().delay(200).fadeTo("slow", 0.6);
            alert("Mobile number should be 10 digit numeric only.");
                   return false;
        }
       });
       
       $('.selectpicker').selectpicker();
       
       //[RV: added code for protion student]
       
        
	$('body').on('click', '.btn_promote', function(e){
			e.preventDefault();
			var $this = $(this);
			var session=$this.parent().parent().find('.sessn').val();
			var status=$this.parent().parent().find('.status').val();
			var course=$this.parent().parent().find('.course').val();
			var scholar_no = $this.parent().parent().find('.scholar_no').html();
			var smstr = $this.parent().parent().find('.smstr').val();
			var old_ssn=$this.parent().parent().find('.old_ssn').html();
			if(session != "" && status != "" && course != "" && scholar_no != ""){
				$.ajax({
					type:"get",
					url: '{{route("create_promotion")}}',
					data: {old_sessions:old_ssn, sessions:session, status:status, cours:course, scholar:scholar_no, semester:smstr},
					success: function(data) {
						var inner="<td colspan='11'><div class='alert alert-success'>"+data+"</div></td>";
						
						$this.parent().parent().html(inner); 
					}
				}); 
			}else{ 
				var sn='';
				if(status==""){ sn='.status'; }else if(session==""){ sn='.sessn'; }else if(course==""){ sn='.course'; }
				if(sn != ""){
					$this.parent().parent().find(sn).css('border', '2px solid red');
				}else{
                	$this.parent().parent().find('.course').css('border', '1px solid #D5D5D5');
                	$this.parent().parent().find('.status').css('border', '1px solid #D5D5D5');
                	$this.parent().parent().find('.sessn').css('border', '1px solid #D5D5D5');
				}
				alert("For Successful Promotion Session Status And Course Need To Be Specified.."); 
			}
    });
       //[RV:END]
    });


</script>

    <script type="text/javascript">
        function Export(fileName="") {
        	if(fileName==""){
        		fileName = "Course-Wise-Due-Report";
        	}
            $("#tblFeeHeadWiseExl").table2excel({
                filename: fileName+".xls"
            });
        }
    </script>