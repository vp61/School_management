@extends('layouts.master')
@section('css')
<style type="text/css">
    .nopad{
        padding: 0px;
    }
    .row{
        margin: 0px !important;
    }
    .strong{
        font-weight: 600;

        /*text-transform: uppercase;*/
    }
    .font-emoji{
        font-family: emoji;
    }
    .name_fields{
        font-size: 13px;
    }
    .border_bottom{
        border-bottom: 1px dotted black;
    }
    .no_border_table td{
        border-top: none !important;
    }
    .student_table td{
        padding: 1px 0px 0px 1px !important;

    }
    .student_table{
        margin-bottom: 2px !important;
    }
    td{
        padding: 2px !important;
    }
     th{
        padding: 2px !important;
    }
    .no-pad{
        padding: 0px;
    }
    table{
        margin-bottom:0px !important;
    }
    .uppercase{
        text-transform: uppercase;
    }
    .border_color{
        border:solid 1px #000000!important;

    }
    .no_border_bottom{
        border-bottom: none!important;
    }
    .no_border_top{
        border-top: none!important;
    }
     .no_border_left{
        border-left: none!important;
    }

   .font_size_td{
        font-size: 10px!important;
    }
    .red{
        color: red;

    }
    .text-center{
        text-align: center;
    }
    .orange{
        color: orange;
    }


    .width_10{
        width: 10% ;
    }
    .subject_font_size{
        font-size: 10px!important;
    }
    .subject_width{
        width: 15% ;
    }
    .cell_border td {
        border: 1px solid black !important;
    }
    .gt_font_size{
        font-size: 12px!important;
    }
    .border-top-color:gray;
    @media print{

        .border_color{
            border:solid 1px #000000!important;

        }
        .no_border_bottom{
            border-bottom: none!important;
        }
        .no_border_top{
            border-top: none!important;
        }
         .no_border_left{
            border-left: none!important;
        }
       /* .logo{
        height: 0px!important;
        width: auto!important;*/
     }

    }
     /*.logo{
        height: 90px;
        width: auto;
     }*/
</style>

@if(isset($term))
    @if(count($term) == 1)
      <style type="text/css"> 
          body{
             font-size:8px !important; 
          }
       @page{
        size: A4;
        /*orientation: landscape;*/
        margin: 0mm 6mm 0mm 12mm !important;
        
      }
      </style>
    @endif
@endif
@endsection
@section('content')



<div class="container-fluid" style="font-size: 8px;padding: 0px">
    <div class="row" >
      
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 nopad">
            @if($report_type == 1)
                @include('exam.report.includes.result_type_1')
            @elseif($report_type == 2)
                @include('exam.report.includes.result_type_2')
            @endif
        </div>    
    </div>
    
</div>


 <script>

  // window.print();

</script>

    @endsection
 

@section('js')
   
@endsection