<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>
         @php
            $branchId = Session::get('activeBranch');
            
            $branchInfo = App\Branch::select('branch_name')->where('id',$branchId)->get();
            echo (isset($branchInfo[0]->branch_name))? $branchInfo[0]->branch_name : env('APPLICATION_TITLE');
          @endphp
    </title>

    <meta name="description" content="top menu &amp; navigation" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @if(isset($generalSetting->favicon))
        <link rel="icon" href="#" type="image/x-icon" />
    @endif

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />

    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />

    <!-- text fonts -->
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.googleapis.com.css') }}" />

    <!-- ace styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{ asset('assets/css/ace-part2.min.css') }}" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="{{ asset('assets/css/ace-skins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ace-rtl.min.css') }}" />

    <!-- inline styles related to this page -->

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{ asset('assets/css/ace-ie.min.css') }}" />
    <![endif]-->


    <style>
        @media(min-width:992px){
           .sidebar.h-sidebar .nav-list>li>a{
            padding: 10px 8px !important;
           } 
        }
        
    </style>
    @yield('css')

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />


    <!-- ace settings handler -->
    <script src="{{ asset('assets/js/ace-extra.min.js') }}"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="{{ asset('assets/js/html5shiv.min.js') }}"></script>
    <script src="{{ asset('assets/js/respond.min.js') }}"></script>
    <![endif]-->

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">

    @yield('top-script')

</head>
{{--<header class="onlyprint">header text for print<!--Content Goes Here--></header>--}}

