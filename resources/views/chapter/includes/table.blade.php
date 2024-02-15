 <h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
    <table id="dynamic-table" class="table table-striped table-hover">
        <thead>
            <th>S.No.</th>
            <th>class</th>
            <th>section</th>
            <th>subject</th>
            <th>chapter</th>
            <th>Edit / Delete</th>
        </thead>

        @php($i=1)
        
        @foreach($data['list'] as $key=>$val)
        
            <tr>
                <td>{{$i}}</td>
                <td>{{$val->faculty}}</td>
                <td>{{$val->semester}}</td>
                <td>{{$val->subject}}</td>
                <td>{{$val->title}}</td>
                <td><a href="{{route($base_route.'.edit',[$val->id])}}" class='btn btn-minier btn-success' title="Edit"><i class="fa fa-pencil" ></i></a>
                   
                  <a href="{{route($base_route.'.delete',[$val->id])}}" class='btn btn-minier btn-danger bootbox-confirm' title="Delete"><i class="fa fa-trash-o" ></i></a> 
                </td>
            </tr>
            @php($i++)
        @endforeach
    
    </table>