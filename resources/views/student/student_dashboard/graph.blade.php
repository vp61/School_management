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
                      <div class='col-md-4'>
                        <div id="barchart_material" style="width: 100%; height: 500px;"></div>

                      </div>
                      <div class="col-md-8">
                        <div id="barchart_demo" style="width: 100%; height: 500px;"></div>

                      </div>
                    </div>
                    <div class="row">
                      <div class='col-lg-12'>
                        <div id="section" style="width: 100%; height: 999px;"></div>

                      </div>

                    </div>
                  </div>
                </div>
              </div>
@endsection
@section('js')
<script src="{{asset('js/Chart.min.js')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/chart-loader.js')}}"></script>
  <script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['status', 'count'],

            @php
              foreach($data['gph'] as $product) {
                  echo "['".$product->status."',".$product->total."],";
              }
            @endphp
        ]);

        var options = {
          chart: {
            title: 'active / Inactive',
          },
          bars: 'vertical',
          vAxis: {format: 'decimal'}
        };
        var chart = new google.charts.Bar(document.getElementById('barchart_material'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
  </script>
  <script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['faculty','total_reg','today'],

            @php
            
              foreach($data['course'] as $product) {
                 //dd($product);
                  echo "['".$product->faculty."',".$product->total_reg.",".$product->today."],";
                  //dd($product->reg_date);
              }

            @endphp
        ]);

        var options = {
          chart: {
            title: 'total  | Reg By Course| today',
          },
          bars: 'vertical',
      
        };
        var chart = new google.charts.Bar(document.getElementById('barchart_demo'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
  
  </script>
   <script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Class student by section','total'],

            @php
            
              foreach($data['sem'] as $product) {
                $txt= $product->faculty.'('.$product->semester.')';
                 //dd($txt);
                  echo "['".$txt."' , ".$product->total."],";

                  //dd($product->reg_date);
              }

            @endphp
        ]);
        var options = {
          chart: {
            title: 'total ',
          },
           axes: {
            x: {
              0: { side: 'top'} // Top x-axis.
            }
          },
          bars: 'horizontal',
      
        };
        var chart = new google.charts.Bar(document.getElementById('section'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
  
  </script>

@endsection
