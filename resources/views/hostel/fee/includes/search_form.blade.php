<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student/Staff</h4>
<div class="form-horizontal" id="filterDiv">
    <div class="form-group">
        {!! Form::label('user_type', 'Type', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('member_type', [""=>"Select Type","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control','id'=>'type','required'=>'required','id'=>'user' ]) !!}
        </div>
        <span class="onstd"> 
            {!!Form::label('course',env('course_label'),['class'=>'col-sm-1 control-label'])!!}
            <div class="col-sm-2">
                 {!! Form::select('course', $data['course'], null, ['class' => 'form-control',"onChange"=>"loadStudent()", "id"=>"course",'required'=>'required']) !!}
            </div>
            {!!Form::label('section','Section',['class'=>'col-sm-1 control-label'])!!}
            <div class="col-sm-2">
                {!! Form::select('section', $data['section'], null, ['class' => 'form-control',"onChange"=>"loadStudent()", "id"=>"section",'required'=>'required']) !!}
            </div>
             {!!Form::label('student','Student',['class'=>'col-sm-1 control-label'])!!}
            <div class="col-sm-2">
                {!! Form::select('member',[""=>'select'], null, ['class' => 'form-control',"onChange"=>"loadFee()", "id"=>"student",'required'=>'required']) !!}
            </div>
        </span>
        <span class="onstaff" style="display: none;">    
            {!!Form::label('staff','Staff',['class'=>'col-sm-1 control-label'])!!}
            <div class="col-sm-2">
                {!! Form::select('member',$data['staff'], null, ['class' => 'form-control', "id"=>"staff",'required'=>'required'])!!}
            </div>
        </span>    
    </div>       
</div>