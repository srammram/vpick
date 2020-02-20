<script>
    
	
	function active_status(x) {
		
        var y = x.split("__");
		
        return y[1] == 1 ?
        '<a href="'+site.base_url+'people/status/deactive/'+ y[0] +'"><span class="label label-success">  '+lang['active']+'</span></a>' :
        '<a href="'+site.base_url+'people/status/active/'+ y[0] +'"><span class="label label-danger">  '+lang['inactive']+'</span><a/>';
    }
	
	
	
	function driver1_status(x1) {
		
        var y1 = x1.split("__");
		
        return y1[1] == 1 ?
         '<a href="javascript:void(0)"><span class="label label-success">  Complete</span></a>' :
        '<a href="'+site.base_url+'account/driver_to_admin/active/'+ y1[0] +'"><span class="label label-danger">  Pending</span><a/>';
    }
	
	function payment_status(x2) {
		
        var y2 = x2.split("__");
		
        return y2[1] == 1 ?
         '<a href="javascript:void(0)"><span class="label label-success">  Online</span></a>' :
		 y2[1] == 2 ?
        '<a href="javascript:void(0)"><span class="label label-info">  Offline</span><a/>' :
		
		'<a href="javascript:void(0)"><span class="label label-warning">  Process</span></a>';
    }
	
	function admin_status(x3) {
		
        var y3 = x3.split("__");
		
        return y3[1] == 3 ?
         '<a href="javascript:void(0)"><span class="label label-success">  Complete</span></a>' :
		 
		  y3[1] == 1 ?
		  '<a href="javascript:void(0)"><span class="label label-danger">  Transfer Process</span><a/>':
		   y3[1] == 2 ?
		 '<a href="javascript:void(0)"><span class="label label-danger">  Payment Waiting Response</span><a/>':
        '<a href="javascript:void(0)"><span class="label label-danger">  Process</span><a/>';
    }
	
	
	
	function mobile_status(mob) {
		if(mob == null){
			return 'N/A';
		}else{
			var mobile = mob.slice(-4);		
			return '******'+mobile;
		}
    }
	
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('account/getWithdraw?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&driver_id='.$_GET['driver_id'].'&is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "purchase_link";
                return nRow;
            },
			"fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total_fare = 0, heyycab_fee = 0, driver_share = 0, settlement = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total_fare += parseFloat(aaData[aiDisplay[i]][4]);
                    heyycab_fee += parseFloat(aaData[aiDisplay[i]][6]);
					driver_share += parseFloat(aaData[aiDisplay[i]][7]);
					settlement += parseFloat(aaData[aiDisplay[i]][9]);
                   
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = total_fare.toFixed(2);
               // nCells[6].innerHTML = heyycab_fee;
				//nCells[7].innerHTML = driver_share;
				//nCells[9].innerHTML = settlement;
               
            },
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},  {"mRender": mobile_status},  {"mRender": empty_status},  {"mRender": admin_status}, {"mRender": empty_status}]
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
    echo admin_form_open('auth/user_actions', 'id="action-form"');
} ?>
<div class="box">
   
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                
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
            
            <div class="col-lg-3">        
                    <div class="form-group">
                        <?php echo lang('user', 'User'); ?>
                        <div class="controls">
                           <select class="form-control" name="driver_id" id="driver_id">
                           		<option value="">Select User</option>
                           		<?php
								foreach($drivers as $driver_row){
								?>
                           		<option value="<?= $driver_row->id ?>" <?= $_GET['driver_id'] == $driver_row->id ? 'selected' : '' ?>><?= $driver_row->first_name.'('.$driver_row->mobile.')'; ?></option>
                                <?php
								}
								?>
                           </select>
                        </div>
                    </div>
                    
            </div>
            
            
            
            <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <div class="form-group">
            <?php echo lang('country', 'Country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
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
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-3"><?php echo lang('create_date'); ?></th>
                            <th class="col-xs-2"><?php echo lang('first_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('last_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('mobile'); ?></th>
                            <th class="col-xs-2"><?php echo lang('amount'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                            <!--<th style="width:80px;"><?php echo lang('actions'); ?></th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            
                        </tr>
                        </tfoot>
                       
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

    <script language="javascript">
        $(document).ready(function () {
            $('#set_admin').click(function () {
                $('#usr-form-btn').trigger('click');
            });

        });
    </script>

<?php } ?>

<script>
$(document).ready(function(){
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
		var sdate = $('#start_date').val();
		var edate = $('#end_date').val();
		var is_country = $('#is_country').val();
		var driver_id = $('#driver_id').val();
		window.location.href = site+"admin/account/withdraw?sdate="+sdate+"&edate="+edate+"&driver_id="+driver_id+"&is_country="+is_country;
		
    });

});

</script>
<script>
//var date = document.getElementById('start_date');



</script>