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
        text-transform: uppercase;
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
        padding: 1px !important;

    }
    .student_table{
        margin-bottom: 2px !important;
    }
    td{
        padding: 2px !important;
    }
    .no-pad{
        padding: 0px;
    }
</style>

@if(isset($term))
    @if(count($term) == 1)
      <style type="text/css"> 
       @page{
        /*size: A4;*/
        /*orientation: landscape;*/
        /*margin: 1mm 1mm 100mm 0mm !important;*/
        
      }
      </style>
    @endif
@endif
@endsection
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->


<div class="container-fluid" style="font-size: xx-small;padding: 0px">
    <div class="row" >
      
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 ">
                <table  class="table-bordered table-striped table-hover">
                    @if(isset($main_sub_master))
           
                    <tr>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                      
                       @foreach($main_sub_master as $sub_k=>$sub_val)
                          <?php $col_span= count($sub_val);
                           $sub= explode('-',$sub_k);
                          ?>
                         
                         <th colspan="{{$col_span * 4}}" class="text-center strong">
                                        {{$sub[1]}}
                            </th>
                       @endforeach
                      <th colspan="5"></th>
                    </tr>           
                    <tr>
                       <th>Roll.No.</th>
                       <th>Name Of Student</th>
                       <th>Father Name</th>
                       <th>Dob</th>
                       <th>Attendance</th>
                        @foreach($main_sub_master as $sub_k => $sub_val)
                                       
                            @foreach($sub_val as $type_id => $type_title)
                                <th colspan="3" class="text-center">{{$type_title}}</th>
                            @endforeach
                            <th>Total</th>
                            <th>Grade</th>
                        @endforeach
                        <th>Grand Total</th>
                        <th>%</th>
                        <th>ML</th>
                        <th>Max Marks</th>
                        <th>Grade</th>
                    </tr>
            
                    <tbody>
                       @if(isset($student))
                         @foreach($student as $std_k=>$std_v)
                         <tr>
                          <td>{{$std_v->reg_no}}</td>
                          <td>{{$std_v->first_name}}</td>
                          <td>{{$std_v->father_first_name}}</td>
                          <td>{{$std_v->date_of_birth}}</td>
                          <td></td>
                         </tr>
                         @endforeach
                       @endif
                    </tbody>
                @endif
        </table>
    </div>    
    </div>
    
</div>


 <script>

  // window.print();

</script>

    @endsection
 

@section('js')
   
@endsection