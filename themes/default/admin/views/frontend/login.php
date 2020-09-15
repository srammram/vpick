<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= "Login " . $Settings->site_name;?></title>
    <script type="text/javascript">if(parent.frames.length !== 0){top.location = '<?=admin_url('pos')?>';}</script>
    <base href="<?=base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>
     <link rel="stylesheet" href="<?=$assets?>styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
    <link rel="stylesheet" href="<?=$assets?>styles/frontend.css" type="text/css">
	<link rel="stylesheet" href="<?=$assets?>pos/css/posajax.css" type="text/css">
	<link rel="stylesheet" href="<?=$assets?>styles/frontend.css" type="text/css">
    <script type="text/javascript" src="<?=$assets?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?=$assets?>js/jquery.js"></script>
    <![endif]-->
</head>
<body>

<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div class="col-md-12 col-sm-12 col-xs-12 login_screen">
	<div class="container-fluid">
		<div class="row">
		<div class="col-md-3 col-md-offset-7 col-sm-3 col-xs-12 left">
        		<div class="main"> 
                
				<h1><?=lang('enter_your_passcode')?></h1>
<?php
if ($error) {
	?>
	<div class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<ul class="list-group"><?= $error; ?></ul>
	</div>
	<?php
}
if ($message) {
	?>
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<ul class="list-group"><?= $message; ?></ul>
	</div>
	<?php
}
?>


<div><button class="btn btn-success btn-sm pull-right"><?php echo date('Y-m-d'); ?></button></div>
<?php echo frontend_form_open("login", 'class="login" data-toggle="validator"'); ?>

<div class="form-group">
<input type="password" name="user_number" id="user_number" class="form-control kb-pad" placeholder="<?=lang('password')?>" required="required" maxLength="4">
</div>
<div class="form-group">
<?php
$ug[''] = lang('select_groups');
	foreach ($groups as $group) {
		$ug[$group->id] = $group->name;
	}
	echo form_dropdown('user_group', $ug, (isset($_POST['user_group']) ? $_POST['user_group'] : ''), 'id="posuser_group" class="form-control pos-input-tip" data-placeholder="'.lang('select_groups').'" required="required" style="width:100%;" ');
?>
</div>
<div class="form-group">
<?php
$wh[''] = lang('select_branch');
	foreach ($warehouses as $warehouse) {
		$wh[$warehouse->id] = $warehouse->name;
	}
	echo form_dropdown('user_warehouse', $wh, (isset($_POST['user_warehouse']) ? $_POST['user_warehouse'] : ''), 'id="posuser_warehouse" class="form-control pos-input-tip" data-placeholder="Select Branch" required="required" style="width:100%;" ');
?>
</div>

<button type="reset" class="btn  btn-danger pull-left"><?= lang('reset') ?> &nbsp; <i class="fa fa-sign-in"></i></button>

<button type="submit" class="btn btn-success  pull-right"><?= lang('login') ?> &nbsp; <i class="fa fa-sign-in"></i></button>

<?php echo form_close(); ?>


		</div>
        </div>

</div>
</div>
</div>

<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/plugins.min.js"></script>
<script type="text/javascript" src="<?=$assets?>pos/js/parse-track-data.js"></script>
<?php /*<script type="text/javascript" src="<?=$assets?>pos/js/pos.ajax.js"></script>*/?>
<script type="text/javascript" src="<?=$assets?>js/dragscrollable.js"></script>
<script type="text/javascript" src="<?=$assets?>js/dragscroll.js"></script>
<script>
		$('#posuser_group').select2({
		placeholder: 'Select Group'
			});
		$('#posuser_warehouse').select2({
		placeholder: 'Select Warehouse'
			});
		$('.kb-pad').keyboard({
        restrictInput: true,
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
		maxLength: 4,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',

            ' {accept} {cancel}'
            ]
        }
    });
			
			
	</script>

</body>
</html>
