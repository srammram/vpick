
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

    <div class="box-content" style="padding: 30px 15px;">
       <div class="row">
      		<div class="col-sm-12">
      			<div class="col-sm-3 col-xs-6">
					<div class="small-box col_green crm_das ">
					   <div class="outer-inner">
							<h3>Close</h3>
					   </div>
						<div class="inner">
						 <h3>1000</h3>
						</div>
						<div class="icon">
						  <div class="kappclose"></div>
						</div>
						<a href="#" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
     			<div class="col-sm-3 col-xs-6">
					<div class="small-box col_blue crm_das">
					   <div class="outer-inner">
							<h3>Open</h3>
					   </div>
						<div class="inner">
						 <h3>2000</h3>
						</div>
						<div class="icon">
						  <div class="kappopen"></div>
						</div>
						<a href="#" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
     			<div class="col-sm-3 col-xs-6">
					<div class="small-box col_darkbrown crm_das">
					   <div class="outer-inner">
							<h3>Process</h3>
					   </div>
						<div class="inner">
						 <h3>2580</h3>
						</div>
						<div class="icon">
						  <div class="kappprocess"></div>
						</div>
						<a href="#" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
     			<div class="col-sm-3 col-xs-6">
					<div class="small-box col_darkblue crm_das">
					   <div class="outer-inner">
							<h3>Reopen</h3>
					   </div>
						<div class="inner">
						 <h3>8655</h3>
						</div>
						<div class="icon">
						  <div class="kappreopen"></div>
						</div>
						<a href="#" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
     		<div class="col-sm-3 col-xs-6">
					<div class="small-box col_violet crm_das">
					   <div class="outer-inner">
							<h3>Transfer</h3>
					   </div>
						<div class="inner">
						 <h3>1000</h3>
						</div>
						<div class="icon">
						  <div class="kapptransfer"></div>
						</div>
						<a href="#" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
      		</div>
       </div>
        <div class="row">
            <div class="col-lg-12 text-center">
				<a href="<?= admin_url('enquiry/create_ticket') ?>" class="btn btn-primary ">Create Ticket</a>
                <a  href="<?= admin_url('enquiry/existing_ticket'); ?>" class="btn btn-primary" data-toggle="modal" data-target="#myModal" >Existing Ticket</a>
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