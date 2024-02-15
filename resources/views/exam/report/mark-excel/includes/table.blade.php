@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
           @if(isset($main_sub_master))
            <thead>
                <tr>
                   <th></th>
                   <th></th>
                   <th></th>
                   <th></th>
                   <th></th>
                  
                   @foreach($main_sub_master as $sub_k=>$sub_val)
                      <?php $col_span= count($sub_val);
                       $sub= explode('-',$sub_k);
                      ?>
                     
                     <th colspan="{{$col_span * 4}}" class="text-center">
                                    {{$sub[1]}}
                        </th>
                   @endforeach
                  <th colspan="5"></th>
                </tr>
                <tr>
                   <th>Reg. No.</th>
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
            </thead>
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