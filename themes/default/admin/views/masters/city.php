<script>
    $(document).ready(function () {
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('masters/getCity?state='.$_GET['state'].'&zone='.$_GET['zone'].'&country='.$_GET['country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
           
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},   {"bSortable": false}]
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
    echo admin_form_open('masters/city_actions', 'id="action-form"');
} ?>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('city'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('masters/add_city'); ?>"  data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang("add_city"); ?></a></li>
                        
                    </ul>
                </li>
            </ul>
        </div>
    </div><?php */?>
    <a href="<?= admin_url('masters/add_city'); ?>" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_city"); ?></button></a>
    
     <a href="javascript:void(0)" id="excel" data-action="export_excel"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("export_to_excel"); ?></button></a>
     
     <a href="<?= admin_url('masters/import_csv_city'); ?>" id="import"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("import_to_csv"); ?></button></a>
     
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="col-lg-3">
               <div class="form-group">
					<?php echo lang('country', 'country'); ?>
                    <?php
                   
					
					foreach ($countrys as $country) {
						$c[$country->id] = $country->name;
					}
                    echo form_dropdown('country', $c, $_GET['country'], 'class=" form-control select-country select" id="country" data-placeholder="' . lang("select") . ' ' . lang(" country") . '"');
                    ?>
                </div>
                </div> 
                
                <div class="col-lg-3">
               <div class="form-group">
					<?php echo lang('zone', 'zone'); ?>
                    <?php
                   foreach ($zones as $zone) {
						$z[$zone->id] = $zone->name;
					}
                    echo form_dropdown('zone', $z, $_GET['zone'], 'class=" form-control select-zone select" id="zone" data-placeholder="' . lang("select") . ' ' . lang(" zone") . '"');
                    ?>
                </div>
                </div> 
                
                <div class="col-lg-3">
               <div class="form-group">
					<?php echo lang('state', 'state'); ?>
                    <?php
                   foreach ($states as $state) {
						$s[$state->id] = $state->name;
					}
                    echo form_dropdown('state', $s, $_GET['state'], 'class=" form-control select-state select" id="state" data-placeholder="' . lang("select") . ' ' . lang(" state") . '"');
                    ?>
                </div>
                </div> 
                
                <div class="col-lg-3">
               <div class="form-group">
					<?php echo lang('&nbsp;'); ?><br>
                   <a href="javascript:void(0)" id="filte_ride" class="btn btn-primary btn-block"><?= lang('search') ?></a>
                </div>
                </div>
                

                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            
                            <th class="col-xs-2"><?php echo lang('name'); ?></th>
                            <th style="width:100px;"><?php echo lang('state'); ?></th>
                            <th style="width:100px;"><?php echo lang('zone'); ?></th>
                            <th style="width:100px;"><?php echo lang('country'); ?></th>
                            <th class="col-xs-2"><?php echo lang('action'); ?></th>
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

   

<?php } ?>
<script>
$(document).ready(function(e) {
    $('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var state = $('#state').val();
		var zone = $('#zone').val();
		var country = $('#country').val();
		window.location.href = site+"admin/masters/city?state="+state+"&zone="+zone+"&country="+country;
		
    });
});
</script>