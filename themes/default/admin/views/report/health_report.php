
 
   <!--Data Table Declarations-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>
<!--/Data Table Declarations-->

<script>
	$( document ).ready(function() {	
	 oTable = $('#UsrTable').DataTable({
		 "aaSorting": [[0, "desc"]],
		 "processing": true,
		 paging: true,
    	 searching: true,
         'sAjaxSource': '<?= admin_url('report/getHealthReport?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&is_country='.$_GET['is_country']) ?>',
		 "aoColumns": [ {"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status}],
		 "pageLength": 10,
		 "dom": 'lBfrtip<"actions">',
		 buttons: [
        
        {
            extend: 'csv',
			className: 'add_se_btn pull-right',
            text: 'Export to CSV',
			title: 'account-owner-csv-<?=date('YmdHis');?>',
            exportOptions: {
                columns:   [0,1,2,3,4,5,6]
            }
        },
        {
            extend: 'excel',
			className: 'add_se_btn pull-right',
            text: 'Export to excel',
			title: 'account-owner-excel-<?=date('YmdHis');?>',
            exportOptions: {
                columns:  [0,1,2,3,4,5,6]
            },
			
        },
		
    ]
	});	
});
	
</script>
<style>
div.dt-buttons{
		float: right !important;
	}
	.dataTables_filter input[type=search] {
		background-color: #FFFFFF !important;
		background-image: none !important;
		border: 1px solid #CCCCCC !important;
		box-shadow: 0 1px 1px rgb(0 0 0 / 8%) inset;
		color: #555555 !important;
		font-size: 14px !important;
		padding: 6px 12px !important;
		margin: 0px 15px 0px 0px !important;
		transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s !important;
		vertical-align: middle !important;
	}
</style>

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
                        	
                            <th class="col-xs-3"><?php echo lang('create_date'); ?></th>                            
                            <th class="col-xs-2"><?php echo lang('driver_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('driver_email'); ?></th>
                            <th class="col-xs-2"><?php echo lang('driver_mobile'); ?></th>
                            <th class="col-xs-2"><?php echo lang('health_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('health_hours'); ?></th>                            
                            <th class="col-xs-2"><?php echo lang('instance'); ?></th>
                            
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
		window.location.href = site+"admin/report/health_report?sdate="+sdate+"&edate="+edate+"&is_country="+is_country;
		
    });

});

</script>
<script>
//var date = document.getElementById('start_date');



</script>