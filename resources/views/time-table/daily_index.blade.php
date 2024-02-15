@extends('layouts.master')

@section('css')
   <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-clockpicker.min.css') }}" />
     <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-loader.css') }}" />
   <style type="text/css">
    .sch_box{
      padding: 5%;
      background: #96eba8d4;
      text-align: center;
      font-weight: 600;
           
    }

   </style>
@endsection

@section('content')
  <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['timetable'][0]->branch_name}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['timetable'][0]->branch_logo}}" style="width: 78px; border-radius: 43px;">
                        </div>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                         
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 text-right">
                        <div class="receipt-right">
                           
                            <p><i class="fa fa-phone"></i> &nbsp; {{$data['timetable'][0]->branch_mobile}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$data['timetable'][0]->branch_email}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp; {{$data['timetable'][0]->branch_address}}</p>
                        </div>
                    </div>
                </div>
            </div><!-- /.page-header -->
            
             <div class="row hidden-print">
               <hr >
                <div class="col-sm-8">
                      
                  <div class="row hidden-print">
                    <div class="col-sm-6">
                      
                    </div>
                   
                     <div class="col-sm-6">
                       <img src="{{asset('images/loader/three-bar-loader.gif')}}" class="pull-left" height="20px" width="100%" id="assign-loader" style="display: none;">
                     </div>    
                    
                  </div>
                    
                </div>
                <div class="col-sm-4 align-right">
                  <button class="btn btn-primary" onclick='print()'><i class="fa fa-print"></i> Print</button>
                  <button class="btn" onclick="altTeacher()"><i class="fa fa-repeat"> </i>  Re-schedule (Auto Assign)</button>
                </div>
                      
             </div>
            <hr class="hidden-print">
                @include('includes.flash_messages')
               @include($view_path.'.includes.daily_table')
                       
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
  
  <script src="{{ asset('assets/js/select2.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap-clockpicker.min.js') }}"></script>
  <script type="text/javascript">
    
    $(document).ready(function(){
      $("#course").select2();
      $('.clockpicker').clockpicker(); 
    });
     function altTeacher(){
      document.getElementById('assign-loader').style.display="block";
      
      var att=[];
    
      $.ajax({
        type: 'POST',
        url: '/api/timetable/alternateTeacher',
        data:{
            _token: "{{csrf_token()}}",
        },
        success: function(response){
          var data=$.parseJSON(response);

          if(data.error){
            document.getElementById('assign-loader').style.display="none";
            toastr.warning(data.message, "warning");
          }else{
            document.getElementById('assign-loader').style.display="none";
            if(data['assign'].length>0){
               toastr.success(data.success, "Success");
            }
          }
        }
      });
     }


  </script>
@endsection