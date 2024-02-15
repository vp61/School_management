@extends('layouts.master')
@section('css')
    <!-- page specific plugin styles -->

   {{-- <!-- text fonts -->
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.googleapis.com.css') }}" />

    <!-- ace styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />

    <link rel="stylesheet" href="{{ asset('assets/css/ace-skins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ace-rtl.min.css') }}" />



    <!-- ace settings handler -->
    <script src="{{ asset('assets/js/ace-extra.min.js') }}"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

  
--}}
    <!-- inline styles related to this page -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />


@endsection
@section('content')
 <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    @include('includes.flash_messages')
                    
                </div><!-- /.page-header -->

                    <div class="row">
                      <div class='col-md-12'>
                        <div id="section" style="width: 100%; height: 999px;"></div>

                      </div>

                    </div>
                    <div class="row">
                      <div class='col-md-12'>
                        <div id="staff" style="width: 100%; height:600px;"></div>

                      </div>

                    </div>
                  </div>
                </div>
              </div>
@endsection
@section('js')
<script src="{{asset('js/Chart.min.js')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/loader.js')}}"></script>
<script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Class student by section','Total','Absent','Present'],                     

            @php
             //dd($attendanceStatus);
              foreach($attendanceStatus as $product) {
                foreach($product as $value){
                  //dd($value);
                  $txt= $value['course_id'].'('.$value['semester'].')';
                  //dd($txt);
                  echo "['".$txt."',".$value['total'].",".$value['total_abst'].",".$value['total_prst']."],";
                 }
                 //dd($txt);

                  //dd($product->reg_date);
              }

            @endphp
            
        ]);
        var options = {
          chart: {
            title: 'Student AttendanceStatus ',
          },
           axes: {
            x: {
              0: { side: 'top'} // Top x-axis.
            }
          },
          bar: { groupWidth: '99%' },
          
          bars: 'horizontal',
      
        };
        var chart = new google.charts.Bar(document.getElementById('section'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
  
</script>
<br>
<script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Staff is here','Total','Absent','Present'],                     

            @php
             //dd($attendanceStaff);
              foreach($attendanceStaff as $product) {
                
                  //dd($product);
                  //dd($txt);
                  echo "['".$product['designation']."',".$product['total'].",".$product['total_abst'].",".$product['total_prst']."],";
                 }
            @endphp
            
        ]);
        var options = {
          chart: {
            title: 'Staff AttendanceStatus ',
          },
          /* axes: {
            x: {
              0: { side: 'top'} // Top x-axis.
            }
          },*/
          
          bars: 'vertical',
      
        };
        var chart = new google.charts.Bar(document.getElementById('staff'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
  
</script>

@endsection
