@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Assign Fees
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            

            <div class="page-content">
<form action="{{route('assign_fee')}}" method="post">
   {{ csrf_field() }}
                <div class="row" style="border:1px solid black; border-radius:20px; padding:5px 0px;">
                    <div class="col-sm-3">
                        <ul class="list-group">
                            <li class="list-group-item  active">Choose From Fees Listed:</li>
                        @foreach($head_list as $heads)
<li class="list-group-item">
    <input type="checkbox" class="head" name="heads[]" id="{{$heads->id}}" value="{{$heads->id}}"  /> &nbsp;
    <b> {{$heads->fee_head_title}} </b>

</li>

                        @endforeach
                        </ul>
<span class="error">{{ $errors->first('head') }}</span>
                    </div>
                    <div class="col-sm-9">
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
<td style="padding-top:10px;">Branch:</td>
<td>
    <select name="branch_id" class="branch_drop form-control" required>
        <option value="{{$branch_list[0]->id}}">{{$branch_list[0]->branch_name}}</option>
    </select>
    
    <span class="error">{{ $errors->first('branch_id') }}</span>
</td>

<td style="padding-top:10px;">Session:</td>
<td>{{ Form::select('session', $session_list, $current_session, ['class'=>'form-control sesn', 'required'=>'required'])}}
    <span class="error">{{ $errors->first('session') }}</span>
</td>
</tr>
<tr>
<td style="padding-top:10px;">{{ env('course_label') }}:</td>
<td>
    {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true'])}}
<span class="error">{{ $errors->first('course') }}</span>
</td>

<td style="padding-top:10px;">Subject:</td>
<td>{{ Form::select('subject', $subj_list, '', ['class'=>'form-control'])}}</td>

</tr><tr>
<td style="padding-top:10px;">Student:</td>
<td colspan="3">{{ Form::select('student', $student_list, '', ['class'=>'student form-control selectpicker', 'data-live-search'=>'true'])}}</td>
                            </tr>
                            @foreach($head_list as $heads)
<tr style="display:none;" class="dspl_{{$heads->id}}">
                            <td>{{$heads->fee_head_title}}</td>
            <td>
<input type="number" name="fee_amnt[]" placeholder="Enter {{$heads->fee_head_title}} Amount" class="ttla form-control" />
<input type="hidden" name="fee_hd[]" value="{{$heads->id}}" class="form-control" />
            </td>
            
            <td>Collection Type</td>
            <td><select name="times[]" class="ttla form-control">
                <option value="">----Enter Collection-Type----</option>
                <option>Yearly</option>
                <option>Section-Wise</option>
            </select></td>
</tr>

                            @endforeach
<tr><td colspan="4" class="text-center">
    <input type="submit" name="submit" value="Save" class="btn btn-info" />
</td></tr>
                        </table>
                    </div>
                </div></form>
            </div>

            <div class="page-content"><div class="table-responsive">
                <h3>Assigned fee list</h3>
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>      
                    <th>S.N.</th>
                    <th>{{ env('course_label') }}</th>
                    <th>Session</th>
                    <th>Fee Head</th>
                    <th>Amount</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <!--th>Action</th-->
                    
                </tr>
                </thead>
                <tbody> 
                    <?php $i =1;?>
                    @foreach($assign_list as $x)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        {{$i++}}
                                    </label>
                                </td>
                                <td>{{$x->faculty}}</td>
                                <td>{{$x->session_name}}</td>
                                <td>{{$x->fee_head_title}}</td>
                                <td>{{$x->fee_amount}}</td>
                                <td>{{$x->name}}</td>
                <td>{{date('d-m-Y', strtotime($x->created_at))}}</td>
                                <!--td>
<?php $id = $x->id;?>
<!--button class="fa fa-play" style="font-size:15px">
    <a href="{{ url('admission/'.$x->id) }}">form</a>
</button>

                                <hr class="hr-8">

<button class="fa fa-edit" style="font-size:15px">
    <a href="{{ url('enquiry/edit/'.$x->id) }}">Edit</a>
</button>

                                </td-->

                            </tr>
                           @endforeach
                   @if(empty($assign_list))
                        <tr>
                            <td colspan="7">No Fees Assign data found.</td>
                            </td>
                        </tr>
                    @endif
                    

                    
                </tbody>
              
            </table>
            {{ $assign_list->links() }}
        </div>
        </div>
    </div></div>
@endsection

