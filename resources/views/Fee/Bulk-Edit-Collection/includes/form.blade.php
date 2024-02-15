{!! Form::open(['route' =>'bulkEditCollection', 'method' => 'GET', 'class' => 'form-horizontal',
    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
        <div class="form-group">
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-3">
                {!! Form::select('faculty', $data['faculty'], null, ['class' => 'form-control', 'onChange' => 'loadFee(this)','required'=>'required']) !!}

            </div>
            {!! Form::label('reg_date', 'Fee Heads', ['class' => 'col-sm-2 control-label']) !!}
            <div class=" col-sm-3">
                 {!! Form::select('assign_fee_id',[''=>'-select--'], null, ['class' => 'form-control','id'=>'assign_fee_heads','onChange' => 'loadStudentCollection(this)','required'=>'required']) !!}
            </div>
            <div class="col-sm-2" align="right">
            <button class="btn btn-info" type="submit">
                    <i class="icon-ok bigger-110"></i>
                         search
            </button>
           </div>
    </div>


{!! Form::close() !!}



