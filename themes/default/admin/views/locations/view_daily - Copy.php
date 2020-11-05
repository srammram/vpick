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
						<td>City</td>
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
			  <legend>Base Hours:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					
                    <tr>
						<td>Min Distance</td>
						<td>:</td>
						<td><?= $result->base_min_distance ?> </td>
					</tr>
                    <tr>
						<td>Min Distance Price</td>
						<td>:</td>
						<td><?= $result->base_min_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Type</td>
						<td>:</td>
						<td><?= $result->base_price_type == 1 ? '<p>Percentage</p>' : '<p>Fixed</p>' ?> </td>
					</tr>
                    <tr>
						<td>Distance</td>
						<td>:</td>
						<td><?= $result->base_per_distance ?> </td>
					</tr>
                    <tr>
						<td>Distance Price</td>
						<td>:</td>
						<td><?= $result->base_per_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Start Time</td>
						<td>:</td>
						<td><?= $result->base_start_time ?> </td>
					</tr>
                    <tr>
						<td>End Time</td>
						<td>:</td>
						<td><?= $result->base_end_time ?> </td>
					</tr>
                    <tr>
						<td>Waiting Minute</td>
						<td>:</td>
						<td><?= $result->base_waiting_minute ?> </td>
					</tr>
                    <tr>
						<td>Waiting Price</td>
						<td>:</td>
						<td><?= $result->base_waiting_price ?> </td>
					</tr>
					
				</tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>Peak Hours:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					
                    <tr>
						<td>Min Distance</td>
						<td>:</td>
						<td><?= $result->peak_min_distance ?> </td>
					</tr>
                    <tr>
						<td>Min Distance Price</td>
						<td>:</td>
						<td><?= $result->peak_min_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Type</td>
						<td>:</td>
						<td><?= $result->peak_price_type == 1 ? '<p>Percentage</p>' : '<p>Fixed</p>' ?> </td>
					</tr>
                    <tr>
						<td>Distance</td>
						<td>:</td>
						<td><?= $result->peak_per_distance ?> </td>
					</tr>
                    <tr>
						<td>Distance Price</td>
						<td>:</td>
						<td><?= $result->peak_per_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Start Time</td>
						<td>:</td>
						<td><?= $result->peak_start_time ?> </td>
					</tr>
                    <tr>
						<td>End Time</td>
						<td>:</td>
						<td><?= $result->peak_end_time ?> </td>
					</tr>
                    <tr>
						<td>Waiting Minute</td>
						<td>:</td>
						<td><?= $result->peak_waiting_minute ?> </td>
					</tr>
                    <tr>
						<td>Waiting Price</td>
						<td>:</td>
						<td><?= $result->peak_waiting_price ?> </td>
					</tr>
					
				</tbody>
			  </table>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Night Hours:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					
                    <tr>
						<td>Min Distance</td>
						<td>:</td>
						<td><?= $result->night_min_distance ?> </td>
					</tr>
                    <tr>
						<td>Min Distance Price</td>
						<td>:</td>
						<td><?= $result->night_min_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Type</td>
						<td>:</td>
						<td><?= $result->night_price_type == 1 ? '<p>Percentage</p>' : '<p>Fixed</p>' ?> </td>
					</tr>
                    <tr>
						<td>Distance</td>
						<td>:</td>
						<td><?= $result->night_per_distance ?> </td>
					</tr>
                    <tr>
						<td>Distance Price</td>
						<td>:</td>
						<td><?= $result->night_per_distance_price ?> </td>
					</tr>
                    <tr>
						<td>Start Time</td>
						<td>:</td>
						<td><?= $result->night_start_time ?> </td>
					</tr>
                    <tr>
						<td>End Time</td>
						<td>:</td>
						<td><?= $result->night_end_time ?> </td>
					</tr>
                    <tr>
						<td>Waiting Minute</td>
						<td>:</td>
						<td><?= $result->night_waiting_minute ?> </td>
					</tr>
                    <tr>
						<td>Waiting Price</td>
						<td>:</td>
						<td><?= $result->night_waiting_price ?> </td>
					</tr>
					
				</tbody>
			  </table>
			</fieldset>
		</div>
	</div>
</div>

