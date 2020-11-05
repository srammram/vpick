<script>
  
	
	function payment_mode_status(x2) {
		
        return x2 == 0 ?
         '<a href="javascript:void(0)"><span class="text-success">  Offline</span></a>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  Online</span></a>';
    }
	function account_verify_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Yes</span></a>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  No</span></a>';
    }
	
	function bank_status_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Yes</span></a>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  No</span></a>';
    }
	
	function office_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Branch Office</span></a>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  Head Office</span></a>';
    }
	
	
	function reconciliation_status_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Yes</span></a>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  No</span></a>';
    }
	
	
	function account_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Bank Process</span></a>' :
		 x2 == 2 ?
        '<a href="javascript:void(0)"><span class="text-info">  Reconcilation</span><a/>' :
		x2 == 4 ?
		 '<a href="javascript:void(0)"><span class="text-danger">  Settlement</span><a/>' :
		 x2 == 3 ?
        '<a href="javascript:void(0)"><span class="text-primary">  Complete</span><a/>' :
		
		'<a href="javascript:void(0)"><span class="text-warning">  Pending</span></a>';
    }
	
	function type_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Company</span></a>' :
		
		
		'<a href="javascript:void(0)"><span class="text-warning">  Wallet</span></a>';
    }
	
	function usertype_status(x2) {
		
        return x2 == 1 ?
         '<a href="javascript:void(0)"><span class="text-success">  Customer</span></a>' :
		 x2 == 2 ?
        '<a href="javascript:void(0)"><span class="text-info">  Driver</span><a/>' :
		x2 == 4 ?
		 '<a href="javascript:void(0)"><span class="text-danger">  Vendor</span><a/>' :
		
		
		'<a href="javascript:void(0)"><span class="text-warning">  Admin</span></a>';
    }
	
	
	
	
	
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('account/getAccountOwner?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			
			"aoColumnDefs": [ { "sClass": "hidden", "aTargets": [ 0, 14, 15 ] } ],
			"fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var credit_fare = 0, debit_fee = 0, subtotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    credit_fare += parseFloat(aaData[aiDisplay[i]][11]);
                    debit_fee += parseFloat(aaData[aiDisplay[i]][12]);
                   
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[11].innerHTML = credit_fare.toFixed(2);
				nCells[12].innerHTML = debit_fee.toFixed(2);
                nCells[18].innerHTML = (credit_fare - debit_fee).toFixed(2);
            },
			
            "aoColumns": [ {"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status}, {"mRender": office_status},{"mRender": usertype_status},  {"mRender": empty_status}, {"mRender": type_status}, {"mRender": payment_mode_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": account_status}, {"mRender": bank_status_status}, {"mRender": reconciliation_status_status}, {"mRender": account_verify_status}, {"mRender": empty_status},    {"bSortable": true}]
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
            
             <!--<div class="col-lg-3">
            <div class="form-group">
                <?php echo lang('user_type', 'user_type'); ?>
                <div class="controls">
                    <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                </div>
             </div>
            </div>
            
             <div class="col-lg-3">
            <div class="form-group">
                <?php echo lang('users', 'users'); ?>
                <div class="controls">
                    <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                </div>
             </div>
            </div>
            
             <div class="col-lg-3">
            <div class="form-group">
                <?php echo lang('transaction_no', 'transaction_no'); ?>
                <div class="controls">
                    <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                </div>
             </div>
            </div>-->
            
            
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
                        	<th class="col-xs-3"><?php echo lang('id'); ?></th>
                            <th class="col-xs-3"><?php echo lang('create_date'); ?></th>
                            <th class="col-xs-2"><?php echo lang('company'); ?></th>
                            <th class="col-xs-2"><?php echo lang('office'); ?></th>
                            <th class="col-xs-2"><?php echo lang('user_type'); ?></th>
                            <th class="col-xs-2"><?php echo lang('name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('type'); ?></th>
                            <th class="col-xs-2"><?php echo lang('payment_mode'); ?></th>
                            <th class="col-xs-2"><?php echo lang('payment_type'); ?></th>
                            <th class="col-xs-2"><?php echo lang('transaction_no'); ?></th>
                            <th class="col-xs-2"><?php echo lang('transaction_date'); ?></th>
                            <th class="col-xs-2"><?php echo lang('credit'); ?></th>
                            <th class="col-xs-2"><?php echo lang('debit'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
                            <th class="col-xs-2"><?php echo lang('bank_status'); ?></th>
                            <th class="col-xs-2"><?php echo lang('reconciliation'); ?></th>
                            <th style="width:100px;"><?php echo lang('account_verify'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                            <th style="width:80px;"><?php echo lang('actions'); ?></th>
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
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Subtotal</th>
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
		window.location.href = site+"admin/account/account_owner?sdate="+sdate+"&edate="+edate+"&is_country="+is_country;
		
    });

});

</script>
<script>
//var date = document.getElementById('start_date');



</script>