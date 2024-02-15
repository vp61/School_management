@extends('layouts.master')

@section('css')
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       {{$panel}} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           @if(isset($data['row']))
                            Edit
                           @else
                            Add
                           @endif 
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        <div class="row">
                            <div class="col-xs-5">  
                                <h4 class="header large lighter blue"> 
                                @if(isset($data['row']))
                                     <i class="fa fa-pencil bigger-110"></i> Edit
                                @else     

                                  <i class="fa fa-plus bigger-110"></i> Add
                                 @endif 
                              </h4>
                              
                                 {!!Form::open(['route'=>$base_route.'.followupStore','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                                     
                                        @include($view_path.'.followup.includes.form')  
                                  {!!Form::close()!!}
                            </div>

                            <div class="col-xs-7" style="border:double  red;">
                                              <h4><center>@if($data['row']->type==1)
                                    Teaching
                                    @else
                                    Non-Teaching
                                    @endif Data</center></h4>
               
                                <div class="row" style="margin-left: 0px;border-top: 1px dotted lightgray;padding-top: 3px;  padding-bottom: 3px;">
                                   
                                         <div class="col-xs-9 col-sm-9 col-md-9 text-left">
                                            <div class="receipt-left">
                                               <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b> Candidate Name :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->candidate_name}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>Father Name :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->father_name}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>Email. :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                       {{$data['row']->email}}
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>Address :</b> 
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                      {{$data['row']->per_add}}
                                                    </div>
                                                </div>
                                                @if($data['row']->type==1)
                                                <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b> Mother Teacher :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->mother_teacher}}
                                                    </div>
                                              </div>
                                              
                                              <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>PRT :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->prt}}
                                                    </div>
                                              </div>
                                              <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>TGT :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->tgt}}
                                                    </div>
                                              </div>
                                              @endif
                                               @if($data['row']->type==2)
                                              <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>Applied For:</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->post_applied_for}}
                                                    </div>
                                              </div>
                                               <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b>Qualification:</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->qualification}}
                                                    </div>
                                              </div>
                                              @endif

                                               <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b> Your Experience (Years)  :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->year_of_experience}}
                                                    </div>
                                              </div>
                                              @if($data['row']->type==1)
                                              <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Presently Teaching(subject/Class)  :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->classes_presently_teaching}}
                                                    </div>
                                              </div>
                                                @endif
                                                 @if($data['row']->type==2)
                                                 <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Present Organization(Name/Place) :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->pesent_organization}}
                                                    </div>
                                              </div>
                                              @endif
                                              <div class="row">
                                                    <div class="col-sm-3 col-xs-3 col-md-3 nopad">
                                                        <b> Languages Known :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->languages_known}}
                                                    </div>
                                              </div>
                                                
                                            
                                            </div>
                                           
                                        </div>    
                                       
                                       
                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                            <div class="receipt-right" style="">
                                             <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Gender:</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                       {{$data['row']->gender}}
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>DOB :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                         {{Carbon\Carbon::parse($data['row']->dob)->format('d-m-Y')}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Moble :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->mobile}}
                                                    </div>
                                                </div>
                                                
                                                @if($data['row']->type==1)
                                                <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Experience :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->experience}}
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>PGT :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->pgt}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>NTT :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->ntt}}
                                                    </div>
                                                </div>
                                                 @endif
                                               @if($data['row']->type==2)
                                               <div class="row">
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        <b>Board  :</b>
                                                    </div>
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        {{$data['row']->board}}
                                                    </div>
                                              </div>
                                              @endif
                                              <div class="row">
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        <b>Current Salary  :</b>
                                                    </div>
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        {{$data['row']->current_salary}}
                                                    </div>
                                              </div>
                                              <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Join Within :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        {{$data['row']->join_day}}
                                                    </div>
                                              </div>

                                            
                                              <div class="row">
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        <b>Leaving Reason :</b>
                                                    </div>
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        {{$data['row']->leaving_reason}}
                                                    </div>
                                              </div>
                                               <div class="row">
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        <b>Status :</b>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                       {{$data['row']->status}}
                                                    </div>
                                              </div>
                                               <div class="row">
                                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                                        <b>Last Followup :</b>
                                                    </div>
                                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                                        {{!empty($data['row']->followup_date)?Carbon\Carbon::parse($data['row']->followup_date)->format('d-m-Y'):''}}
                                                    </div>
                                              </div>
                                               
                                            </div>
                                        </div>         
                                </div>
                            </div>
                            <div class="col-xs-12">
                               @include($view_path.'.followup.includes.table')
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 @include('includes.scripts.delete_confirm')
 @include('includes.scripts.dataTable_scripts')


@endsection