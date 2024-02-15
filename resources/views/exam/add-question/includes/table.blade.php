@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Question Type</th>
                    <th>Option 1</th>
                    <th>Option 2</th>
                    <th>Option 3</th>
                    <th>Option 4</th>
                    <th>Option 5</th>
                    <th>Option 6</th>
                    <th>Correct Option</th>
                    <th>Mark</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($dropdowns['questions']) && $dropdowns['questions']->count() > 0)
                    @php($i=1)
                    @foreach($dropdowns['questions'] as $exam)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $exam->question_title }}</td>
                            <td>{{ $exam->question_description }}</td>
                            <td>{{ $exam->question_type }}</td>
                            <td>{{ $exam->option_1 }}</td>
                            <td>{{ $exam->option_2 }}</td>
                            <td>{{ $exam->option_3 }}</td>
                            <td>{{ $exam->option_4 }}</td>
                            <td>{{ $exam->option_5 }}</td>
                            <td>{{ $exam->option_6 }}</td>
                            <td>{{ str_replace('option_','Option ',$exam->correct_answer) }}</td>
                            <?php 
                                $id_attr = '';
                                if (isset($data['row'])) {
                                    if($data['row']->id == $exam->id){
                                        $id_attr = 'id=edit_question';
                                    }
                                }
                            ?>
                            <td class="mark" {{$id_attr}}>{{ $exam->mark }}</td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green" href="{{ route($base_route.'.edit', ['exam_id'=>$exam_id,'id' => $exam->id]) }}" title="Edit">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['exam_id'=>$exam_id,'id' => $exam->id]) }}" class="red bootbox-confirm" title="Delete">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endif
            </tbody>
        </table>