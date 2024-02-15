@extends('layouts.master')
@section('content')

{!! Form::model($data['id'] , ['route' => [$base_route.'.leave_store', $data['id']], 'method' => 'POST', 'class' => 'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ $panel }} School Leaving Certificate</h4>
<div class="row">
    <div class="col-md-12">
        
        <div class="form-group">
            <label class="col-sm-2 control-label">Book No.</label>
            <div class="col-sm-2">
                {!! Form::text('book_no', $data['res']->book_no,  ['class' => 'form-control']) !!}
            </div>
            <label class="col-sm-2 control-label">SI No.</label>
            <div class="col-sm-2">
                {!! Form::text('si_no',$data['res']->si_no,  ["class" =>"form-control border-form"]) !!}
            </div>
            <label class="col-sm-2 control-label">Admission No.</label>
            <div class="col-sm-2">
                {!! Form::text('admi_no',$data['res']->admi_no,  ["class" =>"form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        
        <div class="form-group">
            <label class="col-sm-3 control-label">Student</label>
            <div class="col-sm-3">
                {!! Form::text('student_name', $data['res']->student_name,  ['class' => 'form-control']) !!}
            </div>
            <label class="col-sm-3 control-label">Father’s/Guardian’s Name</label>
            <div class="col-sm-3">
                {!! Form::text('father_name',$data['res']->father_name,  ["class" =>"form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
             <label class="col-sm-3 control-label">Mother's Name</label>
            <div class="col-sm-3">
                {!! Form::text('mother_name',$data['res']->mother_name, ["class" =>"form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Nationality</label>
            <div class="col-sm-3">
                {!! Form::text('nationality',$data['res']->nationality, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Whether the candidate belongs to Schedule Caste or Schedule Tribe</label>
            <div class="col-sm-3">
                {!! Form::text('schedule_caste', $data['res']->schedule_caste, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Date of first admission in the School with Class & Year</label>
            <div class="col-sm-3">
                {!! Form::text('first_admission_date', $data['res']->first_admission_date, ["class" => "form-control border-form"]) !!}
            </div>
           
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Date of Birth (in Christian era) according to Admission Register</label>
            <div class="col-sm-3">
                {!! Form::date('date_of_birth',  $data['res']->date_of_birth, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Class in which the pupil last studied</label>
            <div class="col-sm-3">
                {!! Form::text('pupil_last_studied', $data['res']->pupil_last_studied, ["class" => "form-control border-form"]) !!}
            </div>
           
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            
            <label class="col-sm-3 control-label">Percentage scored in class in which the pupil last studied</label>
            <div class="col-sm-3">
                {!! Form::text('last_percentage_scored', $data['res']->last_percentage_scored, ["class" => "form-control border-form"]) !!}
            </div>
           <label class="col-sm-3 control-label">School/Boards Annual examination last taken</label>
            <div class="col-sm-3">
                {!! Form::text('annual_examination_last', $data['res']->annual_examination_last, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Result of School/Boards Annual examination last taken</label>
            <div class="col-sm-3">
                {!! Form::text('examination_last_result', $data['res']->examination_last_result, ["class" => "form-control border-form"]) !!}
            </div>
           <label class="col-sm-3 control-label">Whether failed, if so once/twice in the same class</label>
            <div class="col-sm-3">
                {!! Form::text('whether_failed', $data['res']->whether_failed, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Subjects Studied </label>
            <div class="col-sm-3">
                {!! Form::text('subjects_name', $data['res']->subjects_name, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Whether qualified for promotion to the higher class</label>
            <div class="col-sm-3">
                {!! Form::text('whether_qualified', $data['res']->whether_qualified, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Month up to which the (pupil has paid) school dues paid </label>
            <div class="col-sm-3">
                {!! Form::text('school_dues_paid', $data['res']->school_dues_paid, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Any fee concession availed of: If so, the nature of such concession</label>
            <div class="col-sm-3">
                {!! Form::text('fee_concession', $data['res']->fee_concession, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Total No. of working days in the academic session  </label>
            <div class="col-sm-3">
                {!! Form::text('academic_session', $data['res']->academic_session, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Total No. of presence in the academic session </label>
            <div class="col-sm-3">
                {!! Form::text('total_no_presence', $data['res']->total_no_presence, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">Whether NCC Cadet/Boy Scout/Girl Guide (details may be given) </label>
            <div class="col-sm-3">
                {!! Form::text('whether_ncc_cadet', $data['res']->whether_ncc_cadet, ["class" => "form-control border-form"]) !!}
            </div>
             <label class="col-sm-3 control-label">Games played or extracurricular activities in which the pupil usually took part(Mention achievement level therein)
            </label>
            <div class="col-sm-3">
                {!! Form::text('games_played', $data['res']->games_played, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-3 control-label">General conducts </label>
            <div class="col-sm-3">
                {!! Form::text('general_conducts', $data['res']->general_conducts, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Date of application for certificate
            </label>
            <div class="col-sm-3">
                {!! Form::date('date_of_application', $data['res']->date_of_application, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            
            <label class="col-sm-3 control-label">Date on which pupils name was struck off the rolls of the school  </label>
            <div class="col-sm-3">
                {!! Form::date('struck_off', $data['res']->struck_off, ["class" => "form-control border-form"]) !!}
            </div>
            <label class="col-sm-3 control-label">Date of issue of certificate
            </label>
            <div class="col-sm-3">
                {!! Form::date('date_issue_certificate', $data['res']->date_issue_certificate, ["class" => "form-control border-form"]) !!}
            </div>
            
        </div>  
    </div>
    <div class="col-md-12">
        <div class="form-group">
            
            <label class="col-sm-3 control-label">Reasons for leaving the school</label>
            <div class="col-sm-3">
                {!! Form::text('other_remark', $data['res']->other_remark, ["class" => "form-control border-form"]) !!}
            </div>
             <label class="col-sm-3 control-label">Any other remarks
            </label>
            <div class="col-sm-3">
                {!! Form::text('detail', $data['res']->detail, ["class" => "form-control border-form"]) !!}
            </div>
        </div>  
    </div>
</div>
<div class="clearfix form-actions">
<div class="col-md-12 align-right">
    <button class="btn btn-info" type="submit">
        <i class="icon-ok bigger-110"></i>
        Print
    </button>
</div>
</div>
<div class="hr hr-24"></div>
{!! Form::close() !!}


@endsection
 

