@extends('layouts.master')

@section('css')
<style>
	.strong{
		font-weight: 600 !important;
	}
	.noborder{
		border: none;
	}
</style>
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
                            @if(isset($data['row']))
                              Edit
                            @else
                              Add
                            @endif  
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="text-right" >
                       <a class="btn-primary btn-sm" href="{{ route('inventory.product') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Search Product</a>
                   </div>
                   <hr>
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                            <div class="col-xs-12">  
                                <h4 class="header large lighter blue strong"> 
                                @if(isset($data['row']))

                                     <i class="fa fa-pencil bigger-110"></i> Edit
                                @else     

                                  <i class="fa fa-plus bigger-110"></i> Add <i class="fa fa-angle-double-down pull-right add_icon" title="Click to view form" style="display: none;"></i>
                                 <!--  <i class="fa fa-angle-double-up pull-right add_icon" title="Click to hide form" ></i> -->
                                 @endif Product
                              </h4>
                              @include('includes.validation_error_messages')
                              <div class="hidden_add_form" style="padding: 10px;">
                              @if(isset($data['row']))
                                 {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$data['row']->id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                              @else 
                                 {!!Form::open(['route'=>$base_route.'.store','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                              @endif          
                                        @include($view_path.'.includes.form')  
                                  {!!Form::close()!!}
                              </div>      
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 @include('includes.scripts.dataTable_scripts')
 @include('includes.scripts.delete_confirm')
 <script type="">
  function remove_inv_img($img_id){
    var btn_id='remove_img_'+$img_id;
    var cnf=confirm('This image will be deleted permanently.Are you sure?');

    if(cnf){
      $.post("{{route('inventory.product.remove_image')}}",{
        _token: "{{csrf_token()}}",
        image_id:$img_id
      },function(response){
        var data=$.parseJSON(response);
        if(data.error){
          toastr.warning(data.msg,"Warning");
        }else{
          toastr.success(data.success,"Success");
           $('#'+btn_id).closest('.image_div').remove();
        }
      });
    }
   
  }
  
  
  function load_subcategory($this){
    $("#sub_category").html('').append('<option value="">Sub Category</option>');
    $('#subcategory').prop('SelectedIndex','');
    $.post(
      '{{route('inventory.load_subcategory')}}',{cat: $this.value,_token:'{{ csrf_token() }}'},function(response){
        var data= $.parseJSON(response);
        if(data.error){
          toastr.warning(data.msg,"warning");
        }else{
          toastr.success(data.msg,"Success");
          $("#sub_category").html('').append('<option value="">--Select Sub Category--</option>');
          $.each(data.data,function(key,val){
             $("#sub_category").append('<option value="'+val.id+'">'+val.title+'</option>');
          });
        }
      }
    );
  }
 	{{--$('.add_icon').click(function(){
          $('.add_icon').toggle();
          $('.hidden_add_form').slideToggle();
        });--}} 
  $('.search_show_icon').click(function(){
    $('.search_show_icon').toggle();
    $('.hidden_search_form').slideToggle();
  });
	$('.new_row').click(function(){
		var data=document.getElementById('head_tbl').innerHTML;
		var last_id=$('#variation_tbl tr:last').attr('id');
		$('#variation_tbl').append('<tr>'+data+'</tr>');
	});
	function amount_calculate(){
		var price=$('#price').val();
		var gst=$('#gst').val();
		if(gst != ''){
			if(price == '')
				var amt="Please Fill Purchase Price First";
			else
			var amt=((gst/100)*price)+parseInt(price);
			
		}else{
			var amt=price;
		}
		$('#amt').val(amt);
	}
 </script>
@endsection