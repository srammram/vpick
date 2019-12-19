<script>
    function currency_status(x) {
        var y = x.split("__");
        return y[0] == 1 ?
        '<a href="'+site.base_url+'masters/bank_status/deactivate/'+ y[1] +'"><span class="label label-success">  '+lang['active']+'</span></a>' :
        '<a href="'+site.base_url+'masters/bank_status/activate/'+ y[1] +'"><span class="label label-danger">  '+lang['inactive']+'</span><a/>';
    }
	
	function bank_default(a) {
       
        return a == 1 ?
        '<a href=""><span class="label label-success">Yes</span></a>' :
        '<a href=""><span class="label label-warning">No</span></a>';
    }
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('masters/getBank?is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": bank_default}, {"mRender": currency_status}, {"mRender": empty_status}, {"bSortable": true}]
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
    echo admin_form_open('masters/bank_actions', 'id="action-form"');
} ?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('bank'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('masters/add_bank'); ?>"><i class="fa fa-plus-circle"></i> <?= lang("add_bank"); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><?php */?>
    <a href="<?= admin_url('masters/add_bank'); ?>"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_bank"); ?></button></a>
    
    <a href="javascript:void(0)" id="excel" data-action="export_excel"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("export_to_excel"); ?></button></a>
    
    <a href="<?= admin_url('masters/import_csv_bank'); ?>" id="import"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("import_to_csv"); ?></button></a>
    
    
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
                            
                            <th class="col-xs-2"><?php echo lang('name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('account_no'); ?></th>
                            <th class="col-xs-2"><?php echo lang('bank_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('branch_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('ifsc_code'); ?></th>
                            <th style="width:100px;"><?php echo lang('default'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
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