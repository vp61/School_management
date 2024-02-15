@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Collect Fees
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            

            <div class="page-content">
<form action="{{route('collect_fee')}}" method="post">
   {{ csrf_field() }}
                <div class="row">
                    <div class="col-sm-12">
                        @if(Session::has('msG'))
                        <div class="alert alert-success">{{Session::get('msG')}}</div>
                        @endif                    
                        <table class="table table-striped">
            @if($errors->any())<div class="alert alert-danger">
               
                @foreach($errors->all() as $err)
                    <div>{{$err}}</div>
                @endforeach
            </div>@endif
                            <tr>
<td style="padding-top:10px;">Session:</td>
<td>{{ Form::select('session', $session_list, $current_session, ['class'=>'sesn form-control', 'required'=>'required'])}}
    <span class="error">{{ $errors->first('session') }}</span>
</td>

<td style="padding-top:10px;">Branch:</td>
<td>
     <select name="branch_id" class="branch_drop form-control" required>
        <option value="{{$branch_list[0]->id}}">{{$branch_list[0]->branch_name}}</option>
    </select>
    <span class="error">{{ $errors->first('branch_id') }}</span>
</td>

<td style="padding-top:10px;">Course:</td>
<td>
    {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true'])}}
<span class="error">{{ $errors->first('course') }}</span>
</td>
<td style="padding-top:10px;">Student:</td>
<td>{{ Form::select('student', $student_list, '', ['class'=>'student form-control selectpicker', 'data-live-search'=>'true'])}}</td>

                            </tr>
<tr><td colspan="8" class="fee_box"><div style="min-height:200px;"></div></td></tr>
<tr>
<td><b>Payment Type: </b></td>
<td><!--select required name="payment_type" class="form-control">
    <option value="">----Select Payment Type----</option>
    <option>Cash</option>
    <option>Net Banking</option>
    <option>Paytm</option>
</select-->
    {{ Form::select('payment_type', $pay_type_list, '', ['class'=>'form-control', 'required'=>'required']) }}

</td>
<td><b>Reference: </b></td>
<td><input type="text" name="reference" class="form-control" placeholder="Enter Reference" /></td>
<td><b>Date</b></td>

<td><input type="date" name="reciept_date" value="<?php echo date("Y-m-d"); ?>" required /></td>
<td colspan="2" class="text-center">
    <input type="submit" name="submit" value="Save" class="btn btn-info" />
</td></tr>
                        </table>
                    </div>
                </div></form>
            </div>


           

           

            
    </div></div>
   <div class="label label-warning arrowed-in arrowed-right arrowed">Paymet History</div>
<hr class="hr-8">


     <div class="table-responsive ">
            <table id="dynamic-table-fee-list" class="table table-striped table-bordered table-hover">

            </table>
        </div>
@endsection

