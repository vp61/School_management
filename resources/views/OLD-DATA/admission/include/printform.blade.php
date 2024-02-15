@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')


    <main role="main" class="container">
        <div class="row">
            <div class="col-8" style="padding:0px 12px 0px 12px !important"> 
            	<h2 class="text-center">{{$branches[0]['branch_name']}}</h2>
            	<h5 class="text-center">{{$branches[0]['branch_address']}}</h5>
            	<h5 class="text-center">{{$branches[0]['branch_mobile']}}</h5>
            	 @if(!empty($branches[0]['branch_logo']))
            <div class="text-left logo" >
             		
    <img src="{{asset('images/logo/'.$branches[0]['branch_logo'])}}" style="height: 90px;
    width: 130px;     margin: -65px 0px 0px -0px;">


             </div>
             @endif
              @if(empty($branches[0]['branch_logo']))
            <div class="text-left logo" style="margin: -98px 2px 1px -9px;">

             </div>
             @endif
           
                <h5 class="text-right date" style="margin: 0px 0px -14px 0px;"><strong>Date</strong>  :{{date('d-m-Y',strtotime($lastrecord->admission_date))}}</h5>
                <p class="text-left"><strong>Receipt No.</strong> : AES{{10000+$lastrecord->id}}
               
                </p>
            </div>

        </div>
        <hr/>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Student name</th>
                    <th scope="col">Course</th>
                    <th scope="col">Academic Status</th>
                    <th scope="col">Mobile</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">{{$lastrecord->first_name}}</th>
                    <td>{{$lastrecord->course}}</td>
                    <td>
					@if($lastrecord->academic_status!="")
						{{$lastrecord->academic_status}}
					@else
						{{"-"}}
					@endif
					
					</td>
                    <td>{{$lastrecord->mobile}}</td>
                </tr>
                <br/>
 
                <tr>
                    <th scope="col">Student email</th>
                    <th scope="col">Admission date</th>
                    <th scope="col">Address</th>
                    <th scope="col">Gender</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">
					
					@if($lastrecord->email!="")
						{{$lastrecord->email}}
					@else
						{{"-"}}
					@endif
					
					</th>
                    <td>{{date('d-m-Y',strtotime($lastrecord->admission_date))}}</td>
                    <td>
					@if($lastrecord->address!="")
						{{$lastrecord->address}}
					@else
						{{"-"}}
					@endif
					</td>
                    <td>
					@if($lastrecord->gender!="")
						{{$lastrecord->gender}}
					@else
						{{"-"}}
					@endif
				</td>
                </tr>
               
            </tbody>
            <tbody>
                <tr style="background-color: #dad9d9;">
                	
                	<td> <Strong>Admission fee :</strong> Rs {{$lastrecord->admission_fee}}</td>
                    <td><Strong>Payment Type: </strong> {{$lastrecord->payment_type}}</td>
					<td colspan="2"><strong>Invoice / Ref No:</strong> 
                	{{$lastrecord->reference_no}}</td>
                    
                </tr>
               
            </tbody>

        </table>
         
        <div class="row" style="padding-top: 5px;">
            <div class="text-left col-sm-6">
                <strong>Receipt By: {{$lastrecord->name}}</strong> 
            </div>
            <div class="text-right  col-sm-6"> 
                    <strong>Auth.Sign: </strong>-------------------- 
            </div>
        </div>




    </main>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script> -->






@endsection

@section('js')
<script>

  window.print();

</script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection


