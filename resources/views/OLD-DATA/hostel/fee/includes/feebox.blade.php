<div class="row no-gutter " id="fee" style="display: none;" >
	<div class="col-sm-12" id="nodata">
			<table class="table table-striped">	
				<tr id="head" class="">
					<th class="col-sm-2">Fee Head</th>
					<th class="col-sm-1">Fees</th>
					<th class="col-sm-1">Paid</th>
					<th class="col-sm-2">Amount</th>
					<th class="col-sm-1">Due</th>
					<th class="col-sm-1">Discount</th>
					<th class="col-sm-2">Remark</th>
				</tr>
					
			</table>
			<div class="form-group">		
				<label class="col-sm-2 control-label">
					Payment Type:
				</label>
				<div class="col-sm-2">
					 {!! Form::select('pay_mode',$data['pay_type'], '', ['class'=>'form-control border-form' ,'required' => 'required']); !!}
				</div>
				{!!Form::label('ref_no','Reference No:',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					<input type="text" name="ref_no" class="form-control">
				</div>
				{!!Form::label('ref_no','Date:',['class'=>'col-sm-1 control-label'])!!}
				<div class="col-sm-2">
					<input type="date" name="reciept_date" class="input-mask-date date-picker" required />
				</div>

				<input type="submit" name="submit" value="Save" class="btn btn-info" >
			</div>
	</div>

</div>
<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-history" aria-hidden="true"></i>&nbsp;Payment History</h4>
<div class="row no-gutter" id="hist" style="display: none;">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered table-hover" id="dynamic-table" >
				<tr id="history" >
					<th>S.N.</th>
					<th>Receipt No</th>
					<th>Fee Head (Duration)</th>
					<th>Paid</th>
					<th>Mode</th>
					<th>Date</th>
					<th>Status</th>
					<th >Print</th>
				</tr>
		</table>
	</div>
</div>