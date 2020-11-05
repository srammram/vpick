
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
            	<?php
				if(!empty($url_data['enquiry'])){
				foreach($url_data['enquiry'] as $users){
				?>
      			<div class="col-sm-6 col-xs-6">
					<div class="small-box <?= $users['color'] ?> crm_das ">
					   <div class="outer-inner">
							<h3><?= $users['title'] ?></h3>
					   </div>
						<div class="inner">
						 <span class="box_left">
                                <p><?= $users['app_title'] ?></p>
                                <p><?= $users['app_count'] ?></p>
                              </span>
                          	
                            <span class="box_left">
                                <p><?= $users['web_title'] ?></p>
                                <p><?= $users['web_count'] ?></p>
                              </span>
                              
                              <span class="box_left">
                                <p><?= $users['telephone_title'] ?></p>
                                <p><?= $users['telephone_count'] ?></p>
                              </span>
						</div>
						<div class="icon">
						  <div class="<?= $users['icon'] ?>"></div>
						</div>
						<a href="<?= $users['link'] ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
                <?php
				}
				}
				?>
     			
      		</div>
       </div>
        <div class="row">
            <div class="col-lg-12 text-center">
				<a href="<?= admin_url('usersenquiry/create_customer') ?>" class="btn btn-primary " data-toggle="modal" data-target="#myModal">Create Ticket</a>
                <a  href="<?= admin_url('usersenquiry/listview'); ?>" class="btn btn-primary"  >Existing Ticket</a>
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