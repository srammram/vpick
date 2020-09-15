<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script>
<style>
    .modal-dialog{
    width: 894px;
    }
    .modal-body{
        /*height: 500px;*/
        max-height:1000px;
    }
    .recipe-group-list ul li{
        list-style: none;
        float: left;
    position: relative;
    margin-right: 20px;
    width: 200px;
    }
    #add-more{
            float: left;
    }
</style>

<script>
var oTable;
    $(document).ready(function () {
        oTable = $('#SupData').dataTable({
            "aaSorting": [[2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('system_settings/get_customer_discount') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
             'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
               
                $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            "aoColumns": [null,{
                "bSortable": false,
                "mRender": checkbox
            },  null, null, {"mRender": cus_discount_status},{"bSortable": false}]
        }).dtFilter([
            {column_number: 2, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            //{column_number: 2, filter_default_label: "[<?=lang('type');?>]", filter_type: "text", data: []},
            //{column_number: 3, filter_default_label: "[<?=lang('value');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('date');?>]", filter_type: "text", data: []},
        ], "footer");
    });
     $(document).on('click',".modal .close",function(){
        $("#myModal").html("");
        $("#myModal2").html("");
    });
</script>
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('system_settings/customer_discount_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('customer_discounts'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('system_settings/add_customer_discounts'); ?>" data-toggle="modal"><i class="fa fa-plus-circle"></i> <?= lang("add_customer_discount"); ?></a></li>
                        <!-- <li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li class="divider"></li> -->
                        <!-- <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_billers") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_customer_discount') ?></a></li> -->
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="SupData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th><?=lang('s.no')?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("name"); ?></th>
                            <!--<th><?= lang("type"); ?></th>
                            <th><?= lang("vale"); ?></th>-->
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("status"); ?></th>
                            <th style="width:85px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>                            
                            <th></th>
                            <th></th>
                            <!--<th></th>-->
                            <th style="width:85px;" class="text-center"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<!-- <?php if ($action && $action == 'add') {
    echo '<script>$(document).ready(function(){$("#add").trigger("click");});</script>';
}
?> -->
	

