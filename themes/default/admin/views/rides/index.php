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
		}else if(y[0] == 8){
			return '<span class="label label-success">Rejected</span>';
		}else if(y[0] == 9){
			return '<span class="label label-success">Incompleted</span>';
		}else if(y[0] == 10){
			return '<span class="label label-success">Next Ride</span>';
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
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('rides/getOnRides?status='.$_GET['status'].'&booked_type='.$_GET['booked_type'].'&is_country='.$_GET['is_country'].'&sdate='.$_GET['sdate'].'&edate='.$_GET['edate']) ?>',
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
   echo admin_form_open('rides/rides_actions', 'id="action-form"');
} ?>
<div class="box">

    <a href="javascript:void(0)" id="excel" data-action="export_excel"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("export_to_excel"); ?></button></a>
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
               <div class="col-lg-4">
               <div class="form-group">
					<?php echo lang('status', 'status'); ?>
                    <?php
                    $s[''] = array('0' => lang('all'), '1' => lang('Requested'), '2' => lang('Confirmed'), '3' => lang('Ongoing'), '5' => lang('Completed'), '6' => lang('Cancelled'), '7' => lang('Scheduled'));
                    echo form_dropdown('status', $s,0, 'class="tip form-control" id="status" data-placeholder="' . lang("select") . ' ' . lang("ride status") . '"');
                    ?>
                </div>
                </div> 
                <div class="col-lg-4">
               <div class="form-group">
					<?php echo lang('type', 'Ride Type'); ?>
                    <?php
                    $b[''] = array('0' => lang('all'), '1' => lang('cityride'), '2' => lang('rental'), '3' => lang('outstation'));
                    echo form_dropdown('booked_type', $b, 0, 'class="tip form-control" id="booked_type" data-placeholder="' . lang("select") . ' ' . lang("ride type") . '" ');
                    ?>
                </div>
                </div>
                <div class="col-lg-3">
            <div class="form-group">
                <?php echo lang('start_date', 'Start Date'); ?>
                <div class="controls">
                    <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                </div>
             </div>
            </div>
            <div class="col-lg-3">        
                    <div class="form-group">
                        <?php echo lang('end_date', 'End Date'); ?>
                        <div class="controls">
                            <input type="text" id="end_date" name="end_date" class="form-control" onkeypress="dateCheck(this);"  value="<?= $_GET['edate'] ?>"/>
                        </div>
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
                            
                            <th style="width:100px;"><?php echo lang('booking_timestamp'); ?></th>
                            <th class="col-xs-2"><?php echo lang('trip_code'); ?></th>
                            
                            <th style="width:100px;"><?php echo lang('registration_number'); ?></th>
                           <th style="width:100px;"><?php echo lang('customer_name'); ?></th>
                            <th style="width:100px;"><?php echo lang('customer_mobile'); ?></th>
                            <th style="width:100px;"><?php echo lang('driver_name'); ?></th>
                            <th style="width:100px;"><?php echo lang('driver_mobile'); ?></th>
                            <th style="width:100px;"><?php echo lang('pickup_location'); ?></th>
                            <th style="width:100px;"><?php echo lang('pickup_timing'); ?></th>
                            <th style="width:100px;"><?php echo lang('dropoff_location'); ?></th>
                             <th style="width:100px;"><?php echo lang('dropoff_timing'); ?></th>
                             
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
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

<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
    
<script>
$(document).ready(function(e) {
	var m_new = new Date();
	var month_new = m_new.getMonth() - <?= $due_month ?>;
	m_new.setMonth(month_new);
	
	var yearRangeMin =  '-<?= $due_year ?>:+0';
	var yearRangeMax =  '-0:+<?= $due_year ?>';
	
	function getDate(element) {
     var date;
     try {
       date = element.value;
     } catch (error) {
       date = null;
     }

     return date;
   }

	var dateFormat =  "dd/mm/yy";
		
	var start_date = $("#start_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		maxDate: 0,
		numberOfMonths: 1,
		yearRange: '-100:+0',
		
	})
	.on("change", function() {
		end_date.datepicker("option", "minDate", getDate(this));
	});
	
	var end_date = $("#end_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		maxDate: 0,
		numberOfMonths: 1
	})
	.on("change", function() {
		start_date.datepicker("option", "maxDate", getDate(this));
	});
    $('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var status_val = $('#status').val();
		var booked_val = $('#booked_type').val();
		var sdate = $('#start_date').val();
		var edate = $('#end_date').val();
		var is_country = $('#is_country').val();
		window.location.href = site+"admin/rides?status="+status_val+"&booked_type="+booked_val+"&is_country="+is_country+"&sdate="+sdate+"&edate="+edate;
		
    });
});
</script>
<?php } ?>