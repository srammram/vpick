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
						<td>Package Price</td>
						<td>:</td>
						<td><?= $result->package_price ?> </td>
					</tr>
                    
                    <tr>
						<td>Option Type</td>
						<td>:</td>
						<td><?php if($result->option_type == 1){ echo 'Distance'; }elseif($result->option_type == 2){ echo 'Hours'; }else{ echo 'All'; } ?> </td>
					</tr>
                    
                    <tr>
						<td>Option Price</td>
						<td>:</td>
						<td><?php if($result->option_price == 1){ echo 'Lower'; }elseif($result->option_price == 2){ echo 'Higher'; }else{ echo ''; } ?> </td>
					</tr>
                    <?php
					if($result->option_type != 2){
					?>
                    <tr>
						<td>Package Distance</td>
						<td>:</td>
						<td><?= $result->package_distance ?> </td>
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
                    <?php
					}
					?>
					<?php
					if($result->option_type != 1){
					?>
                    <tr>
						<td>Package Time</td>
						<td>:</td>
						<td><?= $result->package_time ?> </td>
					</tr>
                    
                    <tr>
						<td>Time</td>
						<td>:</td>
						<td><?= $result->per_time ?> </td>
					</tr>
                    <tr>
						<td>Time Price</td>
						<td>:</td>
						<td><?= $result->per_time_price ?> </td>
					</tr>
                     <?php
					}
					?>
					
				</tbody>
			  </table>
			</fieldset>
		</div>
	</div>
</div>

