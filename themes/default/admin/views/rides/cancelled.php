<script>
    var oTable;
    $(document).ready(function () {
        'use strict';
        oTable = $('#RidesTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('rides/getCancelledRides?by='.$_GET['by'].'&type='.$_GET['type']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                var $index = oSettings._iDisplayStart+parseInt(iDisplayIndex) +parseInt(1) ;
                $("td:first", nRow).html($index);
                return nRow;
            },
            "aoColumns": [{"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status},{"mRender": empty_status}]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            //{
            //    column_number: 2, select_type: 'select2',
            //    select_type_options: {
            //        placeholder: '<?=lang('status');?>',
            //        width: '100%',
            //        style: 'width:100%;',
            //        minimumResultsForSearch: -1,
            //        allowClear: true
            //    },
            //    data: [{value: '1', label: '<?=lang('active');?>'}, {value: '0', label: '<?=lang('inactive');?>'}]
            //}
        ], "footer");
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
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('cancelled_rides'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <!--<li><a href="<?= admin_url('taxi/add_type'); ?>"  data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang("add_type"); ?></a></li>
                        <li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li class="divider"></li>-->
                        
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                

                <div class="table-responsive">
                    <table id="RidesTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="width:100px;"><?php echo lang('#'); ?></th>
                            <th style="width:100px;"><?php echo lang('ride_id'); ?></th>
                            <th class="col-xs-2"><?php echo lang('driver'); ?></th>
                            <th style="width:100px;"><?php echo lang('cab'); ?></th>
                            <th style="width:100px;"><?php echo lang('pick_up'); ?></th>
                            <th style="width:100px;"><?php echo lang('drop_off'); ?></th>
                            <th style="width:100px;"><?php echo lang('booked_time'); ?></th>
                            <th style="width:100px;"><?php echo lang('ride_type'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
                            <th style="width:100px;"><?php echo lang('action'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
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
                            
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
