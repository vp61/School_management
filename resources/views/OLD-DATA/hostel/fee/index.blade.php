@extends('layouts.master')

@section('css')
        <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
@endsection

@section('content')
		<div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       {{$panel}} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Collect
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    @include('hostel.includes.buttons')
                    <div class="col-xs-12 ">
                        
                         @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
           
                     {!!Form::open(['route'=>$base_route.'.collect','method'=>'POST','class'=>'form-horizontal','id'=>'validation-form',"enctype" => "multipart/form-data"])!!}  
                       @include($view_path.'.includes.search_form')
                       @include($view_path.'.includes.feebox')

                      {!!Form::close()!!} 
                       
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script type="text/javascript">
	 $(document).ready(function () {
            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
               
                $('#user').change(function(){ 
                    $('#course').select2();
                    $('#student').select2();
                     $('#staff').select2();
                   var user= document.getElementById('user').value;
                    if(user==2){
                         $('.onstd').hide();
                         $('.onstaff').show();
                        document.getElementById('section').required=false;
                        document.getElementById('course').required=false;
                        document.getElementById('student').required=false;
                        document.getElementById('student').name="";
                        document.getElementById('staff').name="memberId";
                        document.getElementById('staff').required=true;
                    }
                    if(user==1){
                        $('.onstaff').hide();
                        $('.onstd').show();
                        document.getElementById('student').name="memberId";
                        document.getElementById('staff').name="";
                        document.getElementById('section').required=true;
                        document.getElementById('course').required=true;
                        document.getElementById('student').required=true;
                         document.getElementById('staff').required=false;
                    }
                });
            });
            /*end capital function*/
        });

	function loadStudent() {
		var course=document.getElementById('course').value;
		var section=document.getElementById('section').value;
		 $.ajax({
            type:'POST',
            url: '{{route('hostel.fee.load-student')}}',
            data: {
                _token : '{{csrf_token()}}',
                course : course,
                section : section
            },
            success:function(response){
                var data = $.parseJSON(response);
                if(data.error){
                    $.notify(data.message,"warning");
                }else{
                    $('#student').html(' ').append('<option value="">--Select Student--</option>');
                    $.each(data.student,function(key,val){
                        $('#student').append('<option value="'+val.id+'">'+val.name+'</option>');
                    });
                }
            }
           });
	}
	function loadFee(){
		var userType=document.getElementById('user').value;
		if(userType==1){
            var memberId=document.getElementById('student').value;
        }
        if(userType==2){
            var memberId=document.getElementById('staff').value;
        }
		var sessionId= '{{$data['session']}}';
		var branchId='{{$data['branch']}}';
		$.ajax({
            type:'POST',
            url: '{{route('hostel.fee.load-fee')}}',
            data: {
                _token : '{{csrf_token()}}',
                type : userType,
                userId : memberId,
                session : sessionId,
                branch : branchId
            },
            success:function(response){
                var data = $.parseJSON(response);
                if(data.error){
                    $.notify(data.message,"warning");
                }else{
                    if(data.fee.length>0){
                       $('#fee').css("display","block"); 
                       $('#hist').css("display","block"); 
                       $(".feeRow").remove();
                            
                           $.each(data.fee,function(key,val){
                                    $('#head').after('<tr id="paid" class="feeRow"><td><b>Hostel Fee </b></td><td><b>'+val.rent+'</b></td>');

                                    $.each(data.paid,function(key,value){

                                        $.each(value,function(ke,va){
                                            if(key==val.id){
                                                if(va.total_paid){
                                                    $('#paid').append('<td>'+va.total_paid+'</td>');
                                                }
                                                else{
                                                    $('#paid').append('<td>0</td>');
                                                }
                                            //AMOUNT    
                                                if(va.total_paid==val.rent){
                                                    $('#paid').append('<td>0</td>');
                                                }
                                                else{
                                                    $('#paid').append('<td><input type="number" placeholder="Enter Amount"  name='+'amount['+val.id+'] max="'+(val.rent - va.total_paid)+'" required="required"  ></td>');
                                                }
                                            // DUE
                                                $('#paid').append('<td>'+ (val.rent - va.total_paid )+'</td>');
                                            //DISCOUNT  
                                            $('#paid').append('<td></td>');
                                            //remark
                                                if(va.total_paid==val.rent){
                                                    $('#paid').append('<td></td></tr>');
                                                }
                                                else{
                                                    $('#paid').append('<td><input type="text" placeholder="Enter Remark" name='+'remark['+val.id+']></td></tr>');
                                                }
                                            }
                                            
                                        });
                                    });
                                }); 
                           var i=data.pay.length;   
                                $.each(data.pay,function(key,value){
                                    $('#history').after('<tr class="feeRow"><td>'+i+'</td><td>'+value.receipt_no+'</td><td>Hostel Fee</td><td>'+value.amount_paid+'</td><td>'+value.pay_mode+'</td><td>'+value.created_at+'</td><td>PAID</td><td><a href="print/'+value.receipt_no+'" target="_blank" title="print"><i class="fa fa-print fa-2x" aria-hidden="true"></i></td></tr>');
                                    i--;
                                });
                    }
                    else{
                        $('#fee').css("display","none"); 
                            $('#hist').css("display","none");     
                            $(".feeRow").remove();
                    }
                }
            }
        });
	}
</script>
@endsection