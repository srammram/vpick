<script>
function admin_status(x3) {
		
        var y3 = x3.split("__");
		
        return y3[1] == 1 ?
         '<a href="'+site.base_url+'people/status/deactive/'+ y3[0] +'"><span class="label label-success">  Complete</span></a>' :
        '<a href="'+site.base_url+'people/status/active/'+ y3[0] +'"><span class="label label-danger">  Pending</span><a/>';
    }
 $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('account/getDriverpayment?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&driver_id='.$id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},  {"mRender": admin_status}, {"mRender": empty_status}]
        });
    });
</script>
<div class="col-md-12 col-xs-12 box box_view_sec">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>User Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>First Name *</td>
						<td>:</td>
						<td><?= $user->first_name ?> </td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td>:</td>
						<td><?= $user->last_name ?> </td>
					</tr>
					<tr>
						<td>Email *</td>
						<td>:</td>
						<td><?= $user->email ?></td>
					</tr>
					
					<tr>
						<td>mobile *</td>
						<td>:</td>
						<td>(+<?= $user->country_code ?>) ****** <?= substr($user->mobile, -4) ?></td>
					</tr>
                     <tr>
						<td>Gender</td>
						<td>:</td>
						<td><?= $user->gender ?> </td>
					</tr>
                    <tr>
						<td>DOB</td>
						<td>:</td>
						<td><?= $user->dob == '0000-00-00' || $user->dob == NULL ? '' : $user->dob ?> </td>
					</tr>
                    <tr>
						<td>Photo</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->photo_img ?>" class="img"  data-large-img-url="<?= $user->photo_img ?>"  data-large-img-wrapper="preview">  
									</a>
								</div>
							</div>
							<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
						<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
					

				</tbody>
			  </table>
			</fieldset>
		</div>
        <?php if(!empty($payment)){ ?>
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Last Ride Payment Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
                	<tr>
						<td>Admin status</td>
						<td>:</td>
						<td>
						<?php
						
						if($payment->admin_status == 1){
							echo '<p class="btn btn-success">Verified</p>';
						}elseif($payment->payment_status == 2){
							echo '<a href="'.admin_url('account/admin_to_driver/deposit/').$payment->driver_id.'"><p class="btn btn-success">Deposit Process</p></a>';
						}elseif($payment->payment_status == 3){
							echo '<a href="'.admin_url('account/admin_to_driver/credit/').$payment->driver_id.'"><p class="btn btn-success">Credit Process </p></a>';
						}else{
						
							echo '<p class="btn btn-info">Process</p>';
						}
						?>
                        
						</td>
					</tr>
					<tr>
						<td>Total Ride</td>
						<td>:</td>
						<td><?= $payment->total_ride ?></td>
					</tr>
					<tr>
						<td>Total Amount</td>
						<td>:</td>
						<td><?= $payment->total_ride_amount ?></td>
					</tr>
					<tr>
						<td>Date</td>
						<td>:</td>
						<td><?= $payment->ride_end_date ?></td>
					</tr>
					<tr>
						<td>Extand Date</td>
						<td>:</td>
						<td><?= $payment->ride_end_date ?></td>
					</tr>
					<tr>
						<td>Payment Percentage</td>
						<td>:</td>
						<td><?= $payment->payment_percentage ?></td>
					</tr>
                    <tr>
						<td>Payment Amount</td>
						<td>:</td>
						<td><?= $payment->payment_amount ?></td>
					</tr>
                    <tr>
						<td>Paid Amount</td>
						<td>:</td>
						<td><?= $payment->paid_amount ?></td>
					</tr>
                    <tr>
						<td>Balance Amount</td>
						<td>:</td>
						<td><?= $payment->balance_amount ?></td>
					</tr>
                    <tr>
						<td>Unit Amount</td>
						<td>:</td>
						<td><?= $payment->unit_amount ?></td>
					</tr>
                    <tr>
						<td>Tax</td>
						<td>:</td>
						<td><?= $payment->tax_name ?></td>
					</tr>
                    <tr>
						<td>Tax Amount</td>
						<td>:</td>
						<td><?= $payment->tax_amount ?></td>
					</tr>
                    <tr>
						<td>Final Amount</td>
						<td>:</td>
						<td><?= $payment->net_amount ?></td>
					</tr>
                    <tr>
						<td>Payment Date</td>
						<td>:</td>
						<td><?= $payment->payment_date ?></td>
					</tr>
                    <tr>
						<td>Transaction No</td>
						<td>:</td>
						<td><?= $payment->transaction_no ?></td>
					</tr>
                    <tr>
						<td>Transaction date</td>
						<td>:</td>
						<td><?= $payment->transaction_date ?></td>
					</tr>
                    <tr>
						<td>Transaction Document</td>
						<td>:</td>
                        <td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $payment->transaction_image ?>" class="without-caption image-link  magnifier-thumb-wrapper">
										<img src="<?= $payment->transaction_image ?>" class="img"  data-large-img-url="<?= $payment->transaction_image ?>"  data-large-img-wrapper="preview1">  
									</a>
								</div>
							</div>
								
						   <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
						<div class="magnifier-preview" id="preview1" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
                    
                    <tr>
						<td>Payment status</td>
						<td>:</td>
						<td>
						<?php
						if($payment->payment_status == 1){
							echo '<p class="btn btn-success">Online</p>';
						}elseif($payment->payment_status == 2){
							echo '<p class="btn btn-success">Offline</p>';
						}else{
							echo '<p class="btn btn-info">Process</p>';
						}
						?>
                        
						</td>
					</tr>

				</tbody>
			  </table>
			</fieldset>
			
			
		</div>
        <?php } ?>
        <div class="col-lg-12">
        	<fieldset class="filed_sec">
			  <legend>Payment History:</legend>
        		<div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-3"><?php echo lang('Start Ride'); ?></th>
                            <th class="col-xs-2"><?php echo lang('End Ride'); ?></th>
                            <th class="col-xs-2"><?php echo lang('Total Ride'); ?></th>
                            <th class="col-xs-2"><?php echo lang('Total Amount'); ?></th>
                            <th class="col-xs-2"><?php echo lang('Payment Type'); ?></th>
                            <th style="width:100px;"><?php echo lang('Payment date'); ?></th>
                            <th class="col-xs-2"><?php echo lang('Transaction No'); ?></th>
                            <th style="width:100px;"><?php echo lang('admin_status'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                       
                    </table>
                </div>
            </fieldset>
        </div>
        
	</div>
</div>

