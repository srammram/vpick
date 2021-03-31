
   
 
  <!--Data Table Declarations-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>
<!--/Data Table Declarations-->

<script>
	var oTable;
	$(document).ready(function() {	
	 oTable = $('#UsrTable').DataTable({
		 "aaSorting": [[0, "desc"]],
		 "processing": true,
		 paging: true,
    	 searching: true,
         'sAjaxSource': '<?= admin_url('users/getPermission?is_country='.$_GET['is_country']) ?>',
		 "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},  {"bSortable": true}],
		 "pageLength": 10,
		 "dom": 'lBfrtip<"actions">',
		 buttons: [
        
        {
            extend: 'csv',
			className: 'add_se_btn pull-right',
            text: 'Export to CSV',
			title: 'bank-csv-<?=date('YmdHis');?>',
            exportOptions: {
                columns:  [0,1,2,3,4]
            }
        },
        {
            extend: 'excel',
			className: 'add_se_btn pull-right',
            text: 'Export to excel',
			title: 'bank-excel-<?=date('YmdHis');?>',
            exportOptions: {
                columns:  [0,1,2,3,4]
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
   
    
</script>
<style>.table td:nth-child(6) {
        text-align: right;
        width: 10%;
    }

    .table td:nth-child(8) {
        text-align: center;
    }</style>
<?php if ($Owner) {
    echo admin_form_open('masters/bank_actions', 'id="action-form"');
} ?>
<div class="box">
    
    <a href="<?= admin_url('users/permission'); ?>"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_permission"); ?></button></a>
    
   
    
    <!--<a href="<?= admin_url('masters/import_csv_bank'); ?>" id="import"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("import_to_csv"); ?></button></a>-->
    
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <div class="form-group">
            <?php echo lang('country', 'country'); ?>
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
            
            <div class="col-lg-3 row <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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

                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            
                            <th class="col-xs-2"><?php echo lang('group_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('department'); ?></th>
                            <th class="col-xs-2"><?php echo lang('position'); ?></th>
                            <th class="col-xs-2"><?php echo lang('access_area'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                            <th style="width:80px;"><?php echo lang('actions'); ?></th>
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
            $('#filte_ride').click(function(e) {
				var site = '<?php echo site_url() ?>';
				var is_country = $('#is_country').val();
				
				window.location.href = site+"admin/masters/bank?is_country="+is_country;
				
			});

        });
    </script>

<?php } ?>