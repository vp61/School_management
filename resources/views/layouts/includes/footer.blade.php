<div class="footer">
    <div class="footer-inner hidden-print">
        <div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder">
				     @php
                        $branchId = Session::get('activeBranch');
                        
                        $branchInfo = App\Branch::select('branch_name')->where('id',$branchId)->get();
                        echo (isset($branchInfo[0]->branch_name))? $branchInfo[0]->branch_name : env('APPLICATION_TITLE');
                      @endphp
				</span>
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
	if($cameraIncludeStatus == 1 && 2==9){
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
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

<!-- Added JS & CSS to make Seletor with Search-->
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script>
	$(function(){
		$('.batch_wise_cousre').change(function(){
		    $('#getFeeByBatch').html('');
            var course_id = $(this).val(); 
            var session_id = "{{Session::get('activeSession')}}";
            $.post(
                "{{route('getBatchByCourse')}}",
                 {course:course_id , session_id : session_id, _token:'{{ csrf_token() }}'},
                 function(response){
                    var data = $.parseJSON(response);
                    
                    if(data.error){
                        toastr.warning(data.msg,"Warning");
                        var resp = "<option value=''>No Batch Found</option>";
                        
                    }
                    else{
                        toastr.success(data.msg,"Success");
                        var resp = "<option value=''>--Select Batch--</option>";
                        $.each(data.batch,function($k,$v){
                            resp +="<option value='"+$v.id+"'>"+$v.title+"</option>";
                        });
                    }
                    $('select#batch').html(resp);
                   
                    $('select.student').html("<option value=''>Select Student</option>");
                    $('.selectpicker').selectpicker('refresh');
            });
        });
	    $('.batch').change(function(){
	        var cors_id = $('#batch_wise_cousre').val(); 
	        var batch_id = $(this).val(); 
	        var brnch=$('.branch_drop').val();
	        var selected_session=$('.sesn').val();
	        $.post("{{route('getStudentByBatch')}}", {branch:brnch,selected_session:selected_session,course:cors_id,batch:batch_id, _token:'{{ csrf_token() }}'}, function(response){
	           var data = $.parseJSON(response);
	                if(data.error){
	                    toastr.warning(data.msg,"Warning");
	                    var resp = "<option value=''>No Student Found</option>";
	                    
	                }
	                else{
	                    toastr.success(data.msg,"Success");
	                    var resp = "<option value=''>--Select Student--</option>";
	                    $.each(data.student,function($k,$v){
	                        resp +="<option value='"+$v.id+"'>'"+$v.first_name+" ("+$v.reg_no+")</option>";
	                    });
	                }
	                $('select.student').html(resp);
	                $('.selectpicker').selectpicker('refresh');
	        });
	    })

		$('.select_live_search').select2();
		$('.head').click(function(){
			var cHk=$(this).prop('checked');
			var selector="dspl_"+$(this).prop("id");
			//alert("inside ==> "+cHk+" >>"+$(this).prop("id"));
			if(cHk){ $("."+selector).show();
				$("."+selector).find('.ttla').attr('required', 'required');
			}else{ 
				var inp=$("."+selector).find('input');
				inp.val('');
				$("."+selector).hide();
				$("."+selector).find('.ttla').attr('required',false);
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
			// $.post("{{route('load_feeStructure')}}", {branch:brnch,session:selected_session,course:cors_id,_token:'{{ csrf_token()}}'},
			// 	function(response){
			// 		$('.multiple_select').html(response);
			// 		$('.multiple_select').multiselect();
			// 	});
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
			<?php if(Session::get('isCourseBatch')){
				?>
				$('.student').change(function(){
					var cors_id=$('select.batch_wise_cousre').val(); 
					var stud = $(this).val(); 
					var brnch=$('.branch_drop').val();
					var batch=$('#batch').val();
					var session=$('.sesn').val();
					$.post("{{route('student_fee')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,batch:batch, _token:'{{ csrf_token() }}'}, function(response){
							$('.fee_box').html(response);
					});
					$.post("{{route('student_fee_history')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,batch:batch, _token:'{{ csrf_token() }}'}, function(response){
						
						$('#dynamic-table-fee-list').html(response);
					});
				});
			<?php } else { ?>
				$('.student').change(function(){
					var cors_id=$('select.cors').val(); 
					var stud = $(this).val(); 
					var brnch=$('.branch_drop').val();
					var month=$('#due_month').val();
					var session=$('.sesn').val();
					$.post("{{route('student_fee')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,due_month:month, _token:'{{ csrf_token() }}'}, function(response){
							
							$('.fee_box').html(response);
					});
					$.post("{{route('student_fee_history')}}", {branch:brnch, ssn:session, course:cors_id, student:stud, _token:'{{ csrf_token() }}'}, function(response){
						
						$('#dynamic-table-fee-list').html(response);
					});
				});
			<?php } ?>		
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
				alert("For Successful Promotion Session Status And {{ env('course_label') }} Need To Be Specified.."); 
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