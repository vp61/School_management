<div class="container-fluid">
	<div class="row no-gutters">
		<div class="col-xs-12 col-sm-12">

			<form  method="POST" action="{{URL::current()}}" >
				{{ csrf_field() }}

				<div class="form-group">
					<textarea class="form-control" rows="4" name="comment">
					</textarea>
				</div>
				<div class="form-group">		
					<input type="hidden" name="urlaa" value="{{ URL::current()}}">
						<button class="btn btn-primary pull-right" type="submit" name="">
									Comment
						</button>
				</div>	
				
			</form>
		</div>
	</div>
</div>