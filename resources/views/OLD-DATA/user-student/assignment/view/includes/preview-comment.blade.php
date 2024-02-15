<div class="container-fluid">
	<div class="row" style="">
		<div class="col-sm-12 col-xs-12" style="overflow: scroll;height: 30vh;">
			@foreach($data['teachercomments'] as $comment)
				<div class="row no-gutters" >
					<div class="col-sm-12" >
						@if($comment->member_type==2)
							<h5 style="border-bottom: 1px solid;margin-right:30%;padding: 1%; font-weight: 600;border-radius: 4px 4px;overflow-wrap: break-word;overflow: hidden;" class="bg-info bg-minier">{{$comment->comment}}<i class="pull-right">Staff->{{$comment->member_name}} : {{$comment->created_at}}</i></h5>
						@endif	
					
					
						@if($comment->member_type==1)
							<p style="border-bottom: 1px solid;margin-left:30%;padding: 1%; font-weight: 600;border-radius: 4px 4px;overflow-wrap: break-word;overflow: hidden;" class="bg-danger ">{{$comment->comment}}<i class="pull-right">Student->{{$comment->member_name}} : {{$comment->created_at}}</i></p>
						@endif	
					</div>
				</div>
						
			@endforeach
		</div>
	</div>
</div>