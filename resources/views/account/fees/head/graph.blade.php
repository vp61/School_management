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
                    <div class="col-md-12">
                        <div id="barchart_material" style="width: 100%; height: 500px;"></div>

                    </div>
                </div>
                <div class="row">
                     <div class="col-md-12">
                        <div id="barchart" style="width: 100%; height: 500px;"></div>

                    </div> 
                    
                </div><!-- /.row -->
                <div class="row">
                     <div class="col-md-12">
                        <div id="months" style="width: 100%; height: 500px;"></div>

                    </div> 
                    
                </div>
              <!-- PAGE CONTENT ENDS -->
           
        </div><!-- /.row -->
      </div><!-- /.page-content -->
    </div><!-- /.main-content -->

@endsection
@section('js')
<script src="{{asset('js/Chart.min.js')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/loader.js')}}"></script>
    <script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['faculty','totalcoll','today','paid',],

            @php
              foreach($fees['show'] as $product) {
               //dd($fees['show']);
                echo "['".$product->faculty."',".$product->totalcoll.",".$product->paid.",".$product->today."],";
              }
            @endphp
        ]);

        var options = {
          chart: {
            title: 'total  | fee classwise',
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
            ['fee_head_title','assign','collect','today',],

            @php
              foreach($head['assign'] as $product) {
               //dd($head['assign']);
                echo "['".$product->fee_head_title."',".$product->assign.",".$product->collect.",".$product->today."],";
              }
            @endphp
        ]);

        var options = {
          chart: {
            title: 'total  | Fees headwise',
          },
          bars: 'vertical',
          vAxis: {format: 'decimal'}
        };
        var chart = new google.charts.Bar(document.getElementById('barchart'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
    <script type="text/javascript">

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['title','assign','total_collect','collect',],

            @php
              foreach($assing as $product) {
               //dd($product);
                echo "['".$product['title']."',".$product['assign'].",".$product['total_collect'].",".$product['collect']."],";
              }
            @endphp
        ]);

        var options = {
          chart: {
            title: 'total  | Fees monthwise',
          },
          bars: 'vertical',
          vAxis: {format: 'decimal'}
        };
        var chart = new google.charts.Bar(document.getElementById('months'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>

@endsection

