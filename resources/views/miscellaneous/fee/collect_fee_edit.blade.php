@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Edit Fee Collection
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            <div class="page-content">
                <h4 class="header large lighter blue"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit Fee Collection</h4>
               {!! Form::open(['route' => ['miscellaneous.edit.fee_collection',$id], 'method' => 'POST', 'class' => 'form-horizontal',
                                                'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                   <div class="form-group">
                       {!!Form::label('reciept_no','Reciept No.',['class'=>'col-sm-2 control-label'])!!}
                       <div class="col-sm-2">
                           {!!Form::text('reciept_no',$data->reciept_no,['class'=>'form-control ','disabled'=>'disabled'])!!}
                       </div>
                      {!!Form::label('fee_head','Fee Head',['class'=>'col-sm-2 control-label'])!!}
                      <div class="col-sm-2">
                          {!!Form::select('assign_fee_id',$fee_head,$data->assign_fee_id,['class'=>'form-control','required'=>'required'])!!}
                      </div>
                      {!!Form::label('amount','Amount Paid',['class'=>'col-sm-2 control-label'])!!}
                      <div class="col-sm-2">
                          {!!Form::text('amount',$data->amount_paid,['class'=>'form-control','required'=>'required'])!!}
                      </div>
                   </div>
                   <div class="form-group">
                       {!!Form::label('ref_no','Reference No',['class'=>'col-sm-2 control-label'])!!}
                       <div class="col-sm-2">
                           {!!Form::text('ref_no',$data->reference,['class'=>'form-control ','required'=>'required'])!!}
                       </div>
                      {!!Form::label('pay_mode','Payment Mode',['class'=>'col-sm-2 control-label'])!!}
                      <div class="col-sm-2">
                          {!!Form::select('pay_mode',$pay_type,$data->payment_type,['class'=>'form-control','required'=>'required'])!!}
                      </div>
                      {!!Form::label('date','Reciept Date',['class'=>'col-sm-2 control-label','required'=>'required'])!!}
                      <div class="col-sm-2">
                        @php($date=\Carbon\Carbon::parse($data->reciept_date)->format('Y-m-d'))
                          {!!Form::date('date',$date,['class'=>'form-control date-picker','required'=>'required'])!!}
                      </div>
                   </div>
                   <div class="clearfix align-right">
                       <button type="submit" class="btn btn-success">Update</button>
                   </div>
               </form>
            </div>    
        </div>
    </div>
    
@endsection

