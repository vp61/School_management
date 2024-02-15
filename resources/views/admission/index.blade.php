@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')


    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                     Registration Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>Registration
                        </small>
                    </h1>
    @if(Session::has('msg'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>Heads Up!</strong> {!!Session::get('msg')!!}
        </div>
    @endif

                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                 
                    <!-- PAGE CONTENT BEGINS -->
                       
                        <div class="align-right">
                        <a class="{!! request()->is('admissionlist*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('.admission_list') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Registration List</a>
                        </div>
                        {{--Route::current()->getName()--}}
                {{--@if(Route::current()->getName() == '.admission_form')
                    @php $routed=['.admissionupdate', $id]; @endphp
                @else--}}

                @if(Route::current()->getName() == '.admissionedit')
                    @php $routed=['.admissionupdate', $id]; @endphp
                @else
                    @php $routed='.admission'; @endphp
                @endif

{!! Form::open(['route' => $routed, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
@if(isset($enq_id))
    {!!Form::hidden('enquiry_id',$enq_id)!!}
@endif
                        
<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">
                        {{--
                        @if(Route::current()->getName() == '.admission_form')
                        @include('admission.form')
                        @endif

                        @if(Route::current()->getName() == '.admission')
                          @include('admission.emptyform') 
                        @endif
                        --}}

                      @include('admission.emptyform')

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="icon-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110" onclick="myFunction()"></i>
                                    Submit Admission
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
<script>
    function handicap_cat(id) {
            var type=id.value;
            if(type==1){
               
                $('#handicap').attr('required',true);
            }else{
                $('#handicap').prop('selectedIndex','');
                $('#handicap').attr('required',false);
            }
    }
    function CopyCommAddress(f) {
        if(f.permanent_address_copier.checked == true) {
            f.comm_address.value = f.address.value;
            f.comm_state.value = f.state.value;
            f.comm_country.value = f.country.value;
            f.comm_city.value = f.city.value;
        }
    }
</script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection

