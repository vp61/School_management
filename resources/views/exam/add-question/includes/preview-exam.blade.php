<h4 class="header large lighter blue">Exam</h4>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div>
            <table class="table table-striped table-bordered table-hover">
                <tr >
                    <td class="label-info white">Term</td>
                    <td>{{ $dropdowns['exam']->term }}</td>
                    <td class="label-info white">Exam Type</td>
                    <td>{{$dropdowns['exam']->type}}</td>
                    <td class="label-info white">{{env('course_label')}}(Sec.)</td>
                    <td> {{ $dropdowns['exam']->faculty}} (
                        {!! $dropdowns['exam']->section !!})</td>
                    
                </tr>
                <tr >
                    <td class="label-info white">Subject</td>
                    <td>
                        {{ $dropdowns['exam']->subject}}
                    </td>
                    <td class="label-info white">Maximum Mark
                    <td id="max_mark">
                        {{ $dropdowns['exam']->max_mark}} 
                    </td>
                    <td class="label-info white">  Passing Mark</td>
                    <td>
                        {{ $dropdowns['exam']->pass_mark}}
                    </td>
                </tr>
               
                <tr >
                    <td class="label-info white">Title</td>
                    <td colspan="5">{!! $dropdowns['exam']->exam_title !!}</td>
                </tr>
                <tr >
                    <td class="label-info white">Detail</td>
                    <td colspan="5">{!! $dropdowns['exam']->exam_description !!}</td>
                </tr>
            </table>
        </div>
    </div>
</div>