<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    var apis = ['-', '<?= lang('read'); ?>', '<?= lang('read_write_delete'); ?>'];
    $(document).ready(function () {
        function alevel(n) {
            return '<div class="text-center"><span class="label label-'+(n == 1 ? 'info' : (n == 2 ? 'primary' : 'default'))+'">'+apis[n]+'</span></div>';
        }
        function alimit(n) {
            return '<div class="text-center">'+(n > 0 ? n : '')+'</span></div>';
        }
        oTable = $('#ApiTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('api_settings/getApis') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": alevel}, {"mRender": alimit}, {"mRender": empty_status}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('api_settings/actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="ApiTable" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th class="col-xs-2"><?= lang("reference"); ?></th>
                                <th class="col-xs-3"><?= lang("key"); ?></th>
                                <th class="col-xs-1"><?= lang("level"); ?></th>
                                <th class="col-xs-1"><?= lang("ignore_limit"); ?></th>
                                <th class="col-xs-4"><?= lang("ip_addresses"); ?></th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty">
                                    <?= lang('loading_data_from_server') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>
<script language="javascript">
    $(document).ready(function () {

        $('#delete').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#excel').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#pdf').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

    });
</script>

