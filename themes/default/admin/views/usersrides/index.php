<script>
function cab_status(x) {
        var y = x.split("__");
		if(y[0] == 1){
			return '<span class="label label-success">Requested</span>';
		}else if(y[0] == 2){
			return '<span class="label label-success">Confirmed</span>';
		}else if(y[0] == 3){
			return '<span class="label label-success">Ongoing</span>';
		}else if(y[0] == 4){
			return '<span class="label label-success">Waiting</span>';
		}else if(y[0] == 5){
			return '<span class="label label-success">Completed</span>';
		}else if(y[0] == 6){
			return '<span class="label label-success">Cancelled</span>';
		}else if(y[0] == 7){
			return '<span class="label label-success">Scheduled</span>';
		}
        /*return y[0] == 1 ?
        '<a href="'+site.base_url+'people/employee_status/active/'+ y[1] +'"><span class="label label-success">  '+lang['active']+'</span></a>' :
        '<a href="'+site.base_url+'people/employee_status/deactive/'+ y[1] +'"><span class="label label-danger">  '+lang['inactive']+'</span><a/>';*/
    }
	
	function mobile_status(mob) {
		
		
		
		if(mob != null){
			var mobile = mob.slice(-4);	
			return '******'+mobile;
		}else{
			return '';
		}
		
    }
	
    var oTable;
    $(document).ready(function () {
        //kapp_rides.booking_timing,booking_no,taxi_code,t.number,driver_code,driver_name,kapp_rides.start,kapp_rides.end,kapp_rides.status,Actions
        oTable = $('#RidesTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('usersrides/getOnRides?status='.$_GET['status'].'&booked_type='.$_GET['booked_type'].'&is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [ {"mRender": empty_status},  {"mRender": empty_status},{"mRender": empty_status}, {"mRender": empty_status}, {"mRender": mobile_status}, {"mRender": empty_status}, {"mRender": mobile_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},{"mRender": empty_status},{"mRender": cab_status}, {"mRender": empty_status}, {"bSortable": true}]
        });
    });
</script>
<style>.table td:nth-child(6) {
        text-align: right;
        width: 10%;
    }

    .table td:nth-child(8) {
        text-align: center;
    }</style>
<?php if ($Owner) {
    echo admin_form_open('taxi/user_actions', 'id="action-form"');
} ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
               <div class="col-lg-4">
               <div class="form-group">
					<?php echo lang('Ride Statue', 'status'); ?>
                    <?php
                    $s[''] = array('0' => lang('all'), '1' => lang('Requested'), '2' => lang('Confirmed'), '3' => lang('Ongoing'), '5' => lang('Completed'), '6' => lang('Cancelled'), '7' => lang('Scheduled'));
                    echo form_dropdown('status', $s,0, 'class="tip form-control" id="status" data-placeholder="' . lang("select") . ' ' . lang("ride status") . '"');
                    ?>
                </div>
                </div> 
                <div class="col-lg-4">
               <div class="form-group">
					<?php echo lang('Ride Type', 'Ride Type'); ?>
                    <?php
                    $b[''] = array('0' => lang('all'), '1' => lang('cityride'), '2' => lang('rental'), '3' => lang('outstation'));
                    echo form_dropdown('booked_type', $b, 0, 'class="tip form-control" id="booked_type" data-placeholder="' . lang("select") . ' ' . lang("ride type") . '" ');
                    ?>
                </div>
                </div>
                <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <div class="form-group">
            <?php echo lang('country', 'Country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                <option value="">Select Country</option>
                <?php
                foreach($AllCountrys as $AllCountry){
                ?>
                <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                <?php
                }
                ?>
            </select>
            </div>
            </div> 
            
            <div class="col-lg-3 row">
            <div class="form-group col-lg-7">
                <?php echo lang('&nbsp;'); ?><br>
               <a href="javascript:void(0)" id="filte_ride" class="btn btn-primary btn-block"><?= lang('search') ?></a>
            </div>
           
            <div class="form-group col-lg-5">            	
                <?php echo lang('&nbsp;'); ?><br>
                 <?php
				$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
				?>
               <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$uri_parts[0]; ?>" id="resetfilter"  class="btn btn-primary btn-block"><?= lang('reset') ?></a>
            </div>            
            </div>
            <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="RidesTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            
                            <th style="width:100px;"><?php echo lang('Booking Timestamp'); ?></th>
                            <th class="col-xs-2"><?php echo lang('Trip Code'); ?></th>
                            
                            <th style="width:100px;"><?php echo lang('Registration number'); ?></th>
                           <th style="width:100px;"><?php echo lang('Customer Name'); ?></th>
                            <th style="width:100px;"><?php echo lang('Customer Mobile'); ?></th>
                            <th style="width:100px;"><?php echo lang('Driver Name'); ?></th>
                            <th style="width:100px;"><?php echo lang('Driver Mobile'); ?></th>
                            <th style="width:100px;"><?php echo lang('Pickup Location'); ?></th>
                            <th style="width:100px;"><?php echo lang('Pickup Timing'); ?></th>
                            <th style="width:100px;"><?php echo lang('Dropoff Location'); ?></th>
                             <th style="width:100px;"><?php echo lang('Dropoff Timing'); ?></th>
                             
                            <th style="width:100px;"><?php echo lang('Status'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                            <th style="width:100px;"><?php echo lang('action'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function(e) {
    $('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var status_val = $('#status').val();
		var booked_val = $('#booked_type').val();
		window.location.href = site+"admin/userrides?status="+status_val+"&booked_type="+booked_val+"&is_country="+is_country;
		
    });
});
</script>