<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="table-header">
            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
        </div>
            
            
        <!-- div.table-responsive -->
        {{@Form::open(['route'=>$base_route.'.store','method'=>'post'])}}
        <div class="table-responsive">
            <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                        <th>S.N.</th>
                        <th>Reg. Number</th>
                        <th>Name of Student</th>
                         @if(isset($data['disciplin']))
                          @foreach($data['disciplin'] as $k=>$v)
                           <th>{{$v->title}}</th>
                          @endforeach
                          <th>Remark</th>
                         @endif
                        
                     </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['student']) && $data['student']->count() > 0)
                    <input type="hidden" name="term_id" value="{{$term}}">
                        @php($i=1)
                        @foreach($data['student'] as $student)
                              <?php
                                    $remark= DB::table('exam_student_remark')->select('remark')
                                    ->where('student_id',$student->id)
                                    ->where('term_id',$term)
                                   ->first();
                                    
                                   ?> 
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $student->reg_no }}</td>
                                <td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</td>
                                 @if(isset($data['disciplin']))
                                  @foreach($data['disciplin'] as $k=>$v)

                                   <?php
                                    $grade= DB::table('exam_student_remark')->select('disciplin_grade')
                                    ->where('student_id',$student->id)
                                    ->where('term_id',$term)
                                    ->where('disciplin_id',$v->id)->first();
                                    $grade_id= isset($grade->disciplin_grade)?$grade->disciplin_grade:'';

                                  
                                   ?> 
                                   <td>
                                     @if(($v->exam_disciplin_master_parent_id!=4) && $v->exam_disciplin_master_parent_id!=5)
                                    
                                      {{ Form::select("grade[$student->id][$v->id]", $data['disciplin_remark'], $grade_id, ['class'=>'form-control'])}}
                                     @else
                                     
                                      <input type="text" name="grade[{{$student->id}}][{{$v->id}}]" class="form-control" placeholder="{{$v->title}} grade" value="{{isset($grade->disciplin_grade)?$grade->disciplin_grade:''}}">
                                     @endif
                                    
                                   
                                  </td>
                                  @endforeach
                                 @endif
                                 <td><input type="text" name="remark[{{$student->id}}]" placeholder="Remark" value="{{isset($remark->remark)?$remark->remark:''}}"></td>
                               
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


