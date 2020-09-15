<script>
    function payment_status(x) {
        var y = x.split("__");
        return y[0] == 1 ?
        '<a href="javascript:void(0)"><span class="label label-success">  Complete</span></a>' :
        '<a href="javascript:void(0)"><span class="label label-danger">  Pending</span><a/>';
    }
	function send_status(x) {
        var y = x.split("__");
        return y[0] == 1 ?
        '<a href="javascript:void(0)"><span class="label label-success">  Driver Paid </span></a>' :
        '<a href="javascript:void(0)"><span class="label label-danger">  Driver Not Paid</span><a/>';
    }
	function recived_status(x) {
        var y = x.split("__");
        return y[0] == 1 ?
        '<a href="javascript:void(0)"><span class="label label-success">  Vendor Received</span></a>' :
        '<a href="javascript:void(0)"><span class="label label-danger">  Vendor Not Received</span><a/>';
    }
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('monthly_payment/getMonthlyPayment?is_country='.$_GET['is_country']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": send_status}, {"mRender": recived_status}, {"mRender": payment_status}, {"mRender": empty_status}]
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
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('Monthly Driver Payment'); ?></h2>

      <!--  <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('users/allocated'); ?>"><i class="fa fa-plus-circle"></i> <?= lang("allocated"); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>-->
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
               <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <div class="form-group">
            <?php echo lang('Country', 'Country'); ?>
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
            
            <div class="col-lg-3 row">
            <div class="form-group col-lg-7">
                <?php echo lang('&nbsp;'); ?><br>
               <a href="javascript:void(0)" id="filte_ride" class="btn btn-primary btn-block">SEARCH</a>
            </div>
           
            <div class="form-group col-lg-5">            	
                <?php echo lang('&nbsp;'); ?><br>
                 <?php
				$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
				?>
               <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$uri_parts[0]; ?>" id="resetfilter"  class="btn btn-primary btn-block">RESET</a>
            </div>            
            </div>
            <div class="clearfix"></div>
            
                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-2"><?php echo lang('Row_ID'); ?></th>
                            <th class="col-xs-2"><?php echo lang('driver_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('vendor_name'); ?></th>
                            <th class="col-xs-2"><?php echo lang('amount'); ?></th>
                            <th class="col-xs-2"><?php echo lang('payment_type'); ?></th>
                            <th class="col-xs-2"><?php echo lang('date'); ?></th>
                            <th style="width:100px;"><?php echo lang('send_status'); ?></th>
                            <th style="width:100px;"><?php echo lang('recived_status'); ?></th>
                            <th style="width:100px;"><?php echo lang('status'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
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
			
			$('#filte_ride').click(function(e) {
				var site = '<?php echo site_url() ?>';
				var sdate = $('#start_date').val();
				var edate = $('#end_date').val();
				var is_country = $('#is_country').val();
				var approved = $('#approved').val();
				window.location.href = site+"admin/monthly_payment/index?sdate="+sdate+"&edate="+edate+"&approved="+approved+"&is_country="+is_country;
				
			});

        });
    </script>

<?php } ?>