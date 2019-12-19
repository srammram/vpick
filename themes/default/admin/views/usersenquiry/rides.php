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
			return '<span class="label label-success">InComplete</span>';
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
	
	function id_status(id) {
		
		
		
		if(id != null){
			var html = '<input type="radio" name="ride_id" value="'+id+'">';	
			return html;
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
            'sAjaxSource': '<?= admin_url('usersenquiry/getOnRides?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&customer_type='.$_GET['customer_type'].'&user_id='.$_GET['user_id'].'&is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [ {"mRender": id_status}, {"mRender": empty_status},  {"mRender": empty_status},  {"mRender": empty_status},  {"mRender": empty_status},{"mRender": cab_status}, {"mRender": empty_status}]
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

<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
             
				
                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
               // echo admin_form_open_multipart("enquiry/create_ticket/?customer_id=".$_GET['user_id'], $attrib);
				
                ?>
                <input type="hidden" name="customer_id" value="<?= $_GET['user_id'] ?>" >
                <div class="table-responsive">
                    <table id="RidesTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="width:50px;"><?php echo lang('checkbox'); ?></th>
                            <th style="width:100px;"><?php echo lang('booking_timestamp'); ?></th>
                            <th class="col-xs-2"><?php echo lang('trip_code'); ?></th>
                            
                            
                          
                            <th style="width:100px;"><?php echo lang('pickup_location'); ?></th>
                            
                            <th style="width:100px;"><?php echo lang('dropoff_location'); ?></th>
                            
                             
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
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

				  <div class="modal-footer">
                  	<button type="button" id="filte_ride" class="btn btn-primary change_btn_save center-block">Submit</button>
					<?php //echo form_submit('ticket', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
                </div>
                
			 <?php echo form_close(); ?>
             
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function(e) {
    $('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var customer_id = '<?php echo $_GET['user_id']; ?>';
		var ride = $("input[name='ride_id']:checked").val();
		if(ride_id == 'undefined'){
			var ride_id = 0;
		}else{
			var ride_id = ride;	
		}
		
		//alert(site+"admin/enquiry/create_ticket/");
		
		window.location.href = site+"admin/usersenquiry/create_ticket/?customer_id="+customer_id+"&ride_id="+ride_id;
		
    });
});
</script>