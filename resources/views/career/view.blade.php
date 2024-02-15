@extends('layouts.master')
@section('css')
<style type="text/css">
    .nopad{
        padding: 0px;
    }
  
   
</style>
@endsection
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->
<hr class="hr-8">

<div class="container-fluid" style="font-size:12px;" >
    <div class="row" style="height: 50%;margin:0px;">
     
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$branch->branch_name}}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="receipt-header">
                        <div class="col-xs-5 col-sm-5 col-md-5">
                            <div class="receipt-left">
                                <img class="img-responsive" alt="iamgurdeeposahan" src="{{URL::asset('images/logo/')}}/{{$branch->branch_logo}}" style="width: 78px;margin-top: -20px;">
                                
                            </div>
                        </div>
                      
                        <div class="col-xs-7 col-sm-7 col-md-7 text-right">
                            <div class="receipt-right">
                                
                                <p><i class="fa fa-phone"></i> &nbsp; {{$branch->branch_mobile}}</p>
                                <p><i class="fa fa-envelope-o"></i> &nbsp; {{$branch->branch_email}}</p>
                                <p><i class="fa fa-location-arrow"></i> &nbsp; {{$branch->branch_address}}</p>
                            </div>
                        </div>
                    </div>
                </div>
               <h4><center>@if($data['row']->type==1)
                                    Teaching
                                    @else
                                    Non-Teaching
                                    @endif Form</center></h4>
               
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
                                <div class="row">
                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                        <b> Job Type :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                    @if($data['row']->type==1)
                                    Teaching
                                    @else
                                    Non-Teaching
                                    @endif
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
                               
                            </div>
                        </div>         
                </div>
                @if($data['row']->type==1)
                 <div>
                     <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Stream</th>
                                <th>Board</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><?php
                              $tenth_percent= '10_percentage';
                              $twelve_percent= '12_percentage';
                              $twelve_stream= '12_stream';
                              

                            ?>
                               <td>10</td>
                               <td></td>
                               <td>{{$data['row']->board}}</td>
                               <td>{{$data['row']->$tenth_percent}}</td>
                            
                            </tr>
                            <tr>
                               <td>12</td>
                               <td>{{$data['row']->$twelve_stream}}</td>
                               <td>{{$data['row']->board}}</td>
                               <td>{{$data['row']->$twelve_percent}}</td>
                            
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Course</th>
                                <th>Pursuing Year</th>
                                <th>Is completed</th>
                                <th>Subject</th>
                                <th>percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr>
                                <td>Graduation</td>
                               <td>{{$data['row']->graduation}}</td>
                               <td></td>
                               <td></td>
                               <td>{{$data['row']->graduation_subject}}</td>
                               <td>{{$data['row']->graduation_percentage}}</td>
                           </tr>
                           <tr>
                               <td> Post Graduation</td>
                               <td>{{$data['row']->post_graduation}}</td>
                               <td>{{$data['row']->post_graduation_pursuing_year}}</td>
                               <td>{{$data['row']->is_pg_completed}}</td>
                               <td>{{$data['row']->post_graduation_subject}}</td>
                               <td>{{$data['row']->post_graduation_percentage}}</td>
                           </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Pursuing Year</th>
                                <th>Is completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                               <td>B.Ed.</td>
                               <td>{{$data['row']->b_ed_pursuing_year}}</td>
                               <td>{{$data['row']->is_b_ed_completed}}</td>
                            
                            </tr>
                            <tr>
                               <td>M.Ed.</td>
                               <td>{{$data['row']->m_ed_pursuing_year}}</td>
                               <td>{{$data['row']->is_m_ed_completed}}</td>
                            
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
               
                
            </div>
            
        </div>    
    </div>
    <hr>
</div>


 <script>

  window.print();

</script>

    @endsection
 

