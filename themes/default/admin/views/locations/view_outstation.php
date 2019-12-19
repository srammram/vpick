<div class="col-md-12 col-xs-12 box box_view_sec">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Taxi Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					
                    <tr>
						<td>Taxi Type</td>
						<td>:</td>
						<td><?= $result->type_name ?> </td>
					</tr>
                    <tr>
						<td>From City</td>
						<td>:</td>
						<td><?= $result->city_name ?> </td>
					</tr>
                    <tr>
						<td>To City</td>
						<td>:</td>
						<td><?= $result->city_name ?> </td>
					</tr>
                    <tr>
						<td>Default</td>
						<td>:</td>
						<td><?= $result->is_default == 1 ? '<p class="btn btn-success">Yes</p>' : '<p class="btn btn-danger">No</p>' ?></td>
					</tr>
					<tr>
						<td>Status</td>
						<td>:</td>
						<td><?= $result->status == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>

				</tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>Package Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					
                    <tr>
						<td>Package</td>
						<td>:</td>
						<td><?= $result->package_name ?> </td>
					</tr>
                    <tr>
						<td>One Way Package Price</td>
						<td>:</td>
						<td><?= $result->oneway_package_price ?> </td>
					</tr>
                    <tr>
						<td>Round Way Package Price</td>
						<td>:</td>
						<td><?= $result->twoway_package_price ?> </td>
					</tr>
                    
                     <tr>
						<td>Min Distance</td>
						<td>:</td>
						<td><?= $result->min_per_distance ?> </td>
					</tr>
                    <tr>
						<td>Min Distance Price</td>
						<td>:</td>
						<td><?= $result->min_per_distance_price ?> </td>
					</tr>
                    
                    <tr>
						<td>Distance</td>
						<td>:</td>
						<td><?= $result->per_distance ?> </td>
					</tr>
                    <tr>
						<td>Distance Price</td>
						<td>:</td>
						<td><?= $result->per_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Driver Allowance</td>
						<td>:</td>
						<td><?= $result->driver_allowance_per_day ?> </td>
					</tr>
                    <tr>
						<td>Driver Night Stay</td>
						<td>:</td>
						<td><?= $result->driver_night_per_day ?> </td>
					</tr>
                    
					
				</tbody>
			  </table>
			</fieldset>
		</div>
	</div>
</div>

