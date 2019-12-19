<script>
    $(document).ready(function () {
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('masters/getLicense_type') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},  {"mRender": taxi_fuel_status}, {"mRender": empty_status}, {"bSortable": false}]
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
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('license_type'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('masters/add_license_type'); ?>"  data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang("add_license_type"); ?></a></li>
                        
                    </ul>
                </li>
            </ul>
        </div>
    </div><?php */?>
    <a href="<?= admin_url('masters/add_license_type'); ?>"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_license_type"); ?></button></a>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                

                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                           <th style="width:100px;"><?php echo lang('country'); ?></th>
                            <th style="width:100px;"><?php echo lang('name'); ?></th>
                            <th style="width:100px;"><?php echo lang('description'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
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