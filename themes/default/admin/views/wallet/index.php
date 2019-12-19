<style>
	.box{border: none;box-shadow: none;}
</style>

<div class="box">
   
   
    <div class="box-content">
    
        <div class="row">
      		<div class="col-sm-12">
				<div class="col-sm-3 col-xs-6">
					<div class="small-box col_green crm_das ">
					   <div class="outer-inner">
							<h3><?= lang('owner') ?></h3>
					   </div>
						<div class="inner text-center">
							<h1> <?= $wallet['owner'] ?></h1>
						</div>
						<a href="<?= admin_url('wallet/owner'); ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="small-box col_blue crm_das ">
					   <div class="outer-inner">
							<h3><?= lang('customer') ?></h3>
					   </div>
						<div class="inner text-center">
						 	<h1> <?= $wallet['customer'] ?></h1>
						</div>
						<a href="<?= admin_url('wallet/customer'); ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="small-box col_darkbrown crm_das ">
					   <div class="outer-inner">
							<h3><?= lang('vendor') ?></h3>
					   </div>
						<div class="inner text-center">
						 	<h1> <?= $wallet['vendor'] ?></h1>
						</div>
						<a href="<?= admin_url('wallet/vendor'); ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="small-box col_darkblue crm_das ">
					   <div class="outer-inner">
							<h3><?= lang('driver') ?></h3>
					   </div>
						<div class="inner text-center">
						 	<h1> <?= $wallet['driver'] ?></h1>
						</div>
						<a href="<?= admin_url('wallet/driver'); ?>" class="small-box-footer"><?= lang('see_detail') ?> </a>
				  	</div>
				</div>
      		</div>
       </div>
    </div>
</div>

