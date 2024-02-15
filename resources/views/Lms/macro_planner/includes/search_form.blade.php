<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search {{$panel}}</h4>
{!! Form::open(['route' => $base_route, 'method' => 'GET', 'class' => 'form-horizontal']) !!}
    <div class="form-horizontal" id="filterDiv">
        <div class="row">
            <div class="col-md-12">
                    <div class="form-group">
                        
                        <label class="col-sm-1 control-label">{{ env('course_label') }}</label>
                        <div class="col-sm-2">
                           {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','id'=>'course']) !!}
                        </div>
                        
                        <label class="col-sm-1 control-label">Section</label>
                        <div class="col-sm-2">
                        {!! Form::select('semesters_id', $data['semester'], null, ['class' => 'form-control semesters_id getSubject','id'=>'sem','onChange' => 'loadSubject(this);']) !!}
                        </div>
                        <label class="col-sm-1 control-label">Subject</label>
                        <div class="col-sm-2">
                             {!! Form::select('subjects_id', $data['subjects'], null, ['class' => 'form-control semester_subject','id'=>'subject']) !!}
                        </div>
                         <label class="col-sm-1 control-label">Teacher</label>
                        <div class="col-sm-2">
                            {!! Form::select('teacher_id', $data['staff'], null, ['class' => 'form-control']) !!}
                        </div>
                       
                    </div> 
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Month</label>
                        <div class="col-sm-2">
                            {!! Form::select('months_id', $data['month'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','id'=>'tite']) !!}
                        </div>
                        
                        {!! Form::label('publish_date', 'Publish Date', ['class' => 'col-sm-2 control-label']) !!}
                        <div class=" col-sm-4">
                            <div class="input-group ">
                                {!! Form::date('publish_date_start', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                <span class="input-group-addon">
                                        <i class="fa fa-exchange"></i>
                                    </span>
                                {!! Form::date('publish_date_end', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            </div>
                        </div>

                    </div>

                    <div class="clearfix form-actions">
                        <div class="col-md-12 align-right">
                            <button class="btn btn-info" type="submit" id="filter-btn">
                                <i class="fa fa-filter bigger-110"></i>
                                Search
                            </button>
                        </div>
                    </div>
            </div>
        </div>
</div>

{!! Form::close() !!}