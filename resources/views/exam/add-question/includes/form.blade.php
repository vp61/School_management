


@if(isset($data['row']))
    <div id="question_main_div">
        <div class="question_count">
            <div class="form-group">
                {!! Form::label('question', 'Question Title', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::textarea('question_title',null, ["class" => "form-control border-form","required",'rows'=>4]) !!}
                </div>
                {!! Form::label('question', 'Question Description', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::textarea('question_description',null, ["class" => "form-control border-form",'rows'=>4]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('question_type', 'Question Type', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::select('question_type',$dropdowns['question-type'],null, ["class" => "form-control border-form","required",'onChange'=>'getOptions('.$question_id.',this)']) !!}
                </div>
                {!! Form::label('question_mark', 'Question Mark', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::number('mark',null, ["class" => "form-control border-form mark","required"]) !!}
                </div>
            </div>
           
            <div class="form-group" id="question_type_{{$question_id}}">
                @if($data['row']->question_type == 2 || $data['row']->question_type == 3 || $data['row']->question_type == 4)
                    <div class='col-sm-2'>
                        {!! Form::text('option_1',null, ["class" => "form-control border-form ","required"]) !!}
                    </div>
                    <div class='col-sm-2'>
                        {!! Form::text('option_2',null, ["class" => "form-control border-form ","required"]) !!}
                    </div>
                    <div class='col-sm-2'>
                       {!! Form::text('option_3',null, ["class" => "form-control border-form "]) !!}
                    </div>
                    <div class='col-sm-2'>
                       {!! Form::text('option_4',null, ["class" => "form-control border-form "]) !!}
                    </div>
                    <div class='col-sm-2'>
                       {!! Form::text('option_5',null, ["class" => "form-control border-form "]) !!}
                    </div>
                    <div class='col-sm-2'>
                       {!! Form::text('option_6',null, ["class" => "form-control border-form "]) !!}
                    </div>
                </div>
                <div class='form-group'>
                    <label class=' col-sm-2 control-label'>Correct Option</label>
                    <div class='col-sm-4'>
                        <select class='form-control' required name='correct_answer'>
                            <option value=''>--Select Correct Option--</option>
                            <option value='option_1'>Option 1</option>
                            <option value='option_2'>Option 2</option>
                            <option value='option_3'>Option 3</option>
                            <option value='option_4'>Option 4</option>
                            <option value='option_5'>Option 5</option>
                            <option value='option_6'>Option 6</option>
                        </select>
                    </div>
                </div>    
                @elseif($data['row']->question_type == 1)    
                    <div class='col-sm-12'>
                        <input type='text' name='option_1' placeholder='Answer text will be here' disabled class='form-control'>
                    </div>  
                </div>      
                @elseif($data['row']->question_type == 5)
                    <div class='col-sm-12'>
                        <input type='date' name='option_1' placeholder='Enter Option 1' class='form-control' disabled>
                    </div>
                </div>                    
                @elseif($data['row']->question_type == 6)
                    <div class='col-sm-12'>
                        <input type='file' name='option_1' placeholder='Enter Option 1' class='form-control' disabled>
                    </div>
                </div>    
                @endif
            
            <div class="form-group">
                 {!! Form::label('is_required','Is Required', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::select('is_required',['1'=>'Yes','2'=>'No'],null, ["class" => "form-control border-form ","required"]) !!}
                </div>
            </div>
        </div>   
            <div class="clearfix form-actions">
                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                    <button class="btn btn-info" type="submit" id="filter-btn">
                            Update
                    </button>
                </div>
            </div>
    </div>        
@else
<div id="question_main_div"  style="">
</div>    
{!!Form::hidden('exam_id',$exam_id)!!}
    <div id="question_div" style="display: none;">
        <div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f5f9ff;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;">
            <h4 class="header large lighter blue"><i class="fa fa-plus"></i> Add Question</h4>
            <div class="form-group">
                {!! Form::label('question', 'Question Title', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::textarea('question_title[question_id]',null, ["class" => "form-control border-form ","add_required",'rows'=>4]) !!}
                </div>
                {!! Form::label('question', 'Question Description', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::textarea('question_description[question_id]',null, ["class" => "form-control border-form ",'rows'=>4]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('question_type', 'Question Type', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::select('question_type[question_id]',$dropdowns['question-type'],null, ["class" => "form-control border-form ","add_required",'onChange'=>'getOptions(question_id,this)']) !!}
                </div>
                {!! Form::label('question_mark', 'Question Mark', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::number('question_mark[question_id]',null, ["class" => "form-control border-form mark","add_required"]) !!}
                </div>
            </div>
            <div  id="question_type_question_id">
                
            </div>
            <div class="form-group">
                 {!! Form::label('is_required','Is Required', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::select('is_required[question_id]',['1'=>'Yes','2'=>'No'],null, ["class" => "form-control border-form remove_required","add_required"]) !!}
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-danger pull-right" onClick='closest(".question_count").remove()'> Remove <i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>    
    </div>

<div class="form-group">
    <div class="col-sm-12 ">
       <button type="button" class="btn btn-info pull-right " id="add_button">Add Question</button>
    </div>
</div>

<div class="clearfix form-actions">
    <div class="align-right">            &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-plus bigger-110"></i>
                Add 
        </button>
    </div>
</div>
@endif