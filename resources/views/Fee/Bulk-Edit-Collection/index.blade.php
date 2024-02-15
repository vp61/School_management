@extends('layouts.master')

@section('css')
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       FEE
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                          Bulk Edit Collection
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-sm-12">  
                                
                                    @include('Fee.Bulk-Edit-Collection.includes.form')
                                  
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                @include('Fee.Bulk-Edit-Collection.includes.table')
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

 @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')
 <script >

    

    
    function loadFee($this){ 
       
       var session = "{{Session::get('activeSession')}}";
       var branch = "{{Session::get('activeBranch')}}";
        $.ajax({
          type: 'POST',
          url: "{{route('loadAssignFee')}}",
          data:{
            _token: "{{csrf_token()}}",
            faculty_id: $this.value,
            session_id: session,
            branch_id: branch,
          },
          success: function(response){

            var data= $.parseJSON(response);
            if(data.error){
                toastr.warning(data.msg,'warning');
            } else{
               toastr.success(data.msg,'success');
              $("#assign_fee_heads").html('').append('<option value="">--Select Fee Heads--</option');
              $.each(data.fees, function(key,val){
                  $("#assign_fee_heads").append('<option value="'+val.id+'">'+val.fee_head_title+'</option');
              });
            }
          }
        })
    }

   

  




   
 </script>
@endsection