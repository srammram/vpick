<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i>BBQ</h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('buy_x_get_x'); ?></p>

                <?php 
                        echo admin_form_open("system_settings/bbqbuyxgetx/"); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                               
                                <tr>
                                    <th rowspan="1" class="text-center" style="width:150px;"><?= lang("days"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("buy_x_get_x"); ?></th>
                                </tr>
                                
                                </thead>
                                <tbody>
                               
                             	<?php
								$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
								
								
								foreach($days as $day){
									$buyxgetx_data = $this->site->getBBQbuyxgetxDAYS($day);
									if($buyxgetx_data->days == $day){
										$checked = 'checked';
										$disabled = '';
									}else{
										$checked = '';
										$disabled = 'disabled';
									}
								?>
                               
                                 <tr class="items">
                                	<td>
                                    	<span style="inline-block">
                                        <input type="checkbox" value="<?= $day ?>" class="checkbox days" <?php echo $checked; ?> name="days[]">
                                        <label for="warehouse_stock" class="padding05"><?= $day ?></label>
                                        </span>
                                    </td>
                                    <td>
                                    	<div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("adult"); ?></label>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                    <?php 
														$adult_buy[0] = 'Buy';
														for($j=2; $j<15; $j++){
														$adult_buy[$j] = $j;
														}
													echo form_dropdown('adult_buy[]', $adult_buy, $buyxgetx_data->adult_buy, 'class="form-control adult_buy select" id="adult_buy_'.$day.'" placeholder="Buy"  '.$disabled.' required'); ?>
                                                    </div>
                                                    <div class="col-md-5">
                                                    <?php 
														$adult_get[0] = 'Get';
														for($j=1; $j<15; $j++){
														$adult_get[$j] = $j;
														}
													echo form_dropdown('adult_get[]', $adult_get, $buyxgetx_data->adult_get, 'class="form-control select" id="adult_get_'.$day.'" placeholder="Get" '.$disabled.' required'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("child"); ?></label>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                    <?php 
														$child_buy[0] = 'Buy';
														for($j=2; $j<15; $j++){
														$child_buy[$j] = $j;
														}
													echo form_dropdown('child_buy[]', $child_buy, $buyxgetx_data->child_buy, 'class="form-control child_buy select" id="child_buy_'.$day.'" placeholder="Buy" '.$disabled.' required'); ?>
                                                    </div>
                                                    <div class="col-md-5">
                                                    <?php 
														$child_get[0] = 'Get';
														for($j=1; $j<15; $j++){
														$child_get[$j] = $j;
														}
													echo form_dropdown('child_get[]', $child_get, $buyxgetx_data->child_get, 'class="form-control select" id="child_get_'.$day.'" placeholder="Get" '.$disabled.' required'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label" for="disable_editing"><?= lang("kids"); ?></label>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                    <?php 
														$kids_buy[0] = 'Buy';
														for($j=2; $j<15; $j++){
														$kids_buy[$j] = $j;
														}
													echo form_dropdown('kids_buy[]', $kids_buy, $buyxgetx_data->kids_buy, 'class="form-control kids_buy select" id="kids_buy_'.$day.'" placeholder="Buy" '.$disabled.' required'); ?>
                                                    </div>
                                                    <div class="col-md-5">
                                                    <?php 
														$kids_get[0] = 'Get';
														for($j=1; $j<15; $j++){
														$kids_get[$j] = $j;
														}
													echo form_dropdown('kids_get[]', $kids_get, $buyxgetx_data->kids_get, 'class="form-control select" id="kids_get_'.$day.'" placeholder="Get" '.$disabled.' required'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>   
                                 <?php
                                    
								}
								?>
                                
								</tbody>
                            </table>
                        </div>
                        
                        
			 

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();
                   ?>


            </div>
        </div>
    </div>
</div>
<style>
    .group-permission ul{
	list-style: none;
	
    }
    .reports ul{
    -moz-column-count: 4 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 4 !important;
    -webkit-column-gap: 23px;
    column-count: 4 !important;
    column-gap: 0px;/*23px;*/
    }
    .orders-settings ul,.billing-settings ul,.group-permission ul{
    -moz-column-count: 3;
    -moz-column-gap: 23px;
    -webkit-column-count: 3;
    -webkit-column-gap: 23px;
    column-count: 3;
    column-gap: 0px;/*23px;*/
    }
    .restaurants-group-permission ul li{
	 /*-moz-column-count: 1 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 1 !important;
    -webkit-column-gap: 23px;
    column-count: 1 !important;
    column-gap: 0px;*//*23px;*/
     display: block;
    float: left;
    width:45%
    }
</style>
<script>
$(document).on('ifChanged','.days', function (e) {
        $this = $(this);
		var days = $(this).val();
		if(($this).is(':checked')){
			$(this).iCheck('check');
			$('#adult_buy_'+days).prop("disabled", false);
			$('#adult_get_'+days).prop("disabled", false);
			$('#child_buy_'+days).prop("disabled", false);
			$('#child_get_'+days).prop("disabled", false);
			$('#kids_buy_'+days).prop("disabled", false);
			$('#kids_get_'+days).prop("disabled", false);
		}else{
			$(this).iCheck('uncheck');
			$('#adult_buy_'+days).prop("disabled", true);
			$('#adult_get_'+days).prop("disabled", true);
			$('#child_buy_'+days).prop("disabled", true);
			$('#child_get_'+days).prop("disabled", true);
			$('#kids_buy_'+days).prop("disabled", true);
			$('#kids_get_'+days).prop("disabled", true);
		}
    
    });

$(document).on('change', '.adult_buy', function(){
	
	var num = $(this).val();
	var days = $(this).attr('id');
	days = days.substring(10, 20);
	$('#adult_get_'+days).select2('data', null);
	for(i=num; i<15; i++){
		$("#adult_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
	}
});

$(document).on('change', '.child_buy', function(){
	
	var num = $(this).val();
	var days = $(this).attr('id');
	days = days.substring(10, 20);
	$('#child_get_'+days).select2('data', null);
	for(i=num; i<15; i++){
		$("#child_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
	}
});

$(document).on('change', '.kids_buy', function(){

	var num = $(this).val();
	var days = $(this).attr('id');
	days = days.substring(10, 20);
	$('#kids_get_'+days).select2('data', null);
	for(i=num; i<15; i++){
		$("#kids_get_"+days+" option:contains("+i+")").attr("disabled","disabled");
	}
});
/*$(document).ready(function(e) {
	alert('a');
  var  x = 5;
var y = 2;
var i = 12;

var r = i % (x + y);
var n = (i - r) / (x + y);
var py = max(0, r - x) + (n * y);
var px = i - py ;

x = px;
y = py;

alert(x);
alert(y);
});*/
</script>
