@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Collect
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    @include('transport.includes.buttons')
                    <div class="col-xs-12 ">
                        <hr class="hr-6">
                         @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @include($view_path.'.includes.search_form')
                        @include($view_path.'.includes.feebox')
                        {!! Form::close() !!}
                       
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection

@section('js')
    

    <script type="text/javascript">
    	

    	function loadTravellers(){
                   $('#travel').empty();	
		    		var typeId = document.getElementById('type').value;
		    		var travelId = document.getElementById('travel').value;
    				var route_id = document.getElementById('route').value;
    				var sessionId = "{{$data['session']}}";
    				var branchId = "{{$data['branch']}}";
		    		if(typeId==" "){
		    			alert('Select Type');

		    		}
		    		$.ajax({
		                type: 'POST',
		                url: '{{ route('transport.list') }}',
		                data: {
		                    _token: '{{ csrf_token() }}',
		                    route_id: route_id,
		                    type: typeId
		                },
		    			success: function(response){
		    				var data=$.parseJSON(response);
		    				 if (data.error) {
		                        toastr.warning(data.error,"Warning");
                                
		                    } else {
                                  toastr.success(data.success,"Success");

		                        $('#travel').html('').append('<option value="">--Select Traveller--</option>');
		                        $.each(data.traveller, function(key,valueObj){
		                            $('#travel').append('<option value="'+valueObj.id+'">'+valueObj.first_name+' ('+valueObj.reg_no+')</option>');
		   
		                        });

		                      }
		      	
			    			}
		    		});
    	}
    	function abc(){ 
    		var typeId = document.getElementById('type').value;
    		var route_id = document.getElementById('route').value;
            var travelId = document.getElementById('travel').value;
    		var sessionId = "{{$data['session']}}";
    		var branchId = "{{$data['branch']}}";
    		$.ajax({
	            type: 'POST',
	            url: '{{ route('transport.fee') }}',
	            data: {
	                _token: '{{ csrf_token() }}',
	                route_id: route_id,
	                type: typeId,
	                member_id : travelId,
                    sId : sessionId,
                    bId:branchId
	            },
				success: function(response){

					var data=$.parseJSON(response);
    				if(data.error){
    					 $.notify(data.message, "warning");
    					} else {
    						
    						$('#fee').css("display","block"); 
                            $('#hist').css("display","block");     
                            $(".feeRow").remove();
    					$.each(data.user,function(key,val){
    						$('#head').after('<tr id="paid" class="feeRow"><td><b>Transport Fee </b></td><td><b>'+val.duration+'<td>'+val.total_rent+'</b></td>');

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
    									if(va.total_paid==val.total_rent){
    										$('#paid').append('<td>0</td>');
    									}
    									else{
    										$('#paid').append('<td><input type="number" placeholder="Enter Amount"  name='+'amount['+val.id+'] max="'+(val.total_rent - va.total_paid)+'" required="required"  ></td>');
    									}
    								// DUE
    									$('#paid').append('<td>'+ (val.total_rent - va.total_paid )+'</td>');
    								//DISCOUNT	
    								$('#paid').append('<td></td>');
    								//remark
    									if(va.total_paid==val.total_rent){
    										$('#paid').append('<td></td></tr>');
    									}
    									else{

    										
    										$('#paid').append('<td><input type="text" placeholder="Enter Remark" name='+'remark['+val.id+']></td></tr>');
    									}
    								}
    								
    							});
    						});
    					});	

                        var i=data.history.length;   
                        $.each(data.history,function(key,value){
                            $('#history').after('<tr class="feeRow"><td>'+i+'</td><td>'+value.receipt_no+'</td><td>Transport Fee('+value.duration+')</td><td>'+value.amount_paid+'</td><td>'+value.pay_mode+'</td><td>'+value.created_at+'</td><td>PAID</td><td><a href="print/'+value.receipt_no+'" target="_blank" title="print"><i class="fa fa-print fa-2x" aria-hidden="true"></i></td></tr>');
                            i--;
                        });
    				}
	    		}
			});
    	}

	    function CheckTransportMaster(){
            var typeId = document.getElementById('type').value;
            var transport_master= "{{ env('Transport_Master')}}";
            if(transport_master==1){
        
                 if(typeId==1){
                   alert("fees Can Not Be Collected");
                 }
            }
            
        }

    	
    </script>

@endsection
	