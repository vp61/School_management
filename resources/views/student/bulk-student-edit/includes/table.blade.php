<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="table-header">
            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
        </div>
            
            
        <!-- div.table-responsive -->
        {{@Form::open(['route'=>$base_route.'.bulk_edit_student.edit','method'=>'post'])}}
        <div class="table-responsive">
            <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                        <th>S.N.</th>
                        <th>Reg No</th>
                        <th>Name of Student</th>
                        <th>Father Name</th>
                        <?php 
                           if($edit==1){
                             $type= "Roll No";
                           }
                           elseif($edit==2){
                            $type= "Section";
                           }
                           else{
                            $type ='';
                           }
                        ?>
                        <th>Edit&nbsp;{{$type}}</th>
                        
                     </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['student']) && $data['student']->count() > 0)
                    <input type="hidden" name="edit" value="{{$edit}}">
                        @php($i=1)
                        @foreach($data['student'] as $student)
                            
                            <tr>
                                <td>{{ $i }}</td>
                                <td> {{$student->reg_no }}</td>
                                <td> {{$student->first_name }}</td>
                                <td> {{$student->father_first_name }}</td>
                                <td>
                                  @if($edit==1)
                                   {{ Form::text("roll_no[$student->id]", $student->roll_no, ['class'=>'form-control'])}} 

                                   @else
                                   {{ Form::select("Semester[$student->id]", $data['section'],$student->Semester, ['class'=>'form-control'])}} 

                                   @endif   
                                
                                </td>   
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">No {{ $panel }} data found.</td>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                   
                </table>
                <div class="form-group" align="right">
                     <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    Save
                                </button>
                </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


