<div class="row">
    <div class="col-sm-12">
        <h4 class="header large lighter blue"><i class="fa fa-search"></i>
            {{$panel}}
        </h4>
    </div>
    <div class="col-sm-12">
        {!!Form::open(['route'=>$base_route,'method'=>'GET','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
            <div class="form-group">
                <label class="col-sm-2 control-label">{{env('course_label')}}</label>
                <div class="col-sm-2">
                    {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','id'=>'course']) !!}
                    
                </div>
                
                <label class="col-sm-2 control-label">Section</label>
                <div class="col-sm-2">
                     {!! Form::select('semesters_id', $data['semester'], null, ['class' => 'form-control semesters_id getSubject','id'=>'sem','onChange' => 'loadSubject(this);']) !!}


                </div>
                <label class="col-sm-2 control-label">subject</label>
                <div class="col-sm-2">
                    {!! Form::select('timetable_subjects_id', $data['subjects'], null, ['class' => 'form-control semester_subject','id'=>'subject']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="clearfix form-actions">
                    <div class="align-right">            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-info" type="submit" id="filter-btn">
                                 <i class="fa fa-search bigger-110"></i>
                                    Search
                            </button>
                    </div>
                </div> 
            </div>
        {!!Form::close()!!}
    </div>
</div>