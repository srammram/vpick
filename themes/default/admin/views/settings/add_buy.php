<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
  <div class="box-header">
    <h2 class="blue"><i class="fa-fw fa fa-plus"></i>
      <?= lang('add_buy'); ?>
    </h2>
  </div>
  <div class="box-content">
    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_buy", $attrib); ?>
    <div class="row">
      <div class="col-lg-12">
        <p class="introtext"><?php echo lang('enter_info'); ?></p>
        <div class="col-md-7">
          <div class="form-group">
            <label for="name"><?php echo $this->lang->line("name"); ?></label>
            <div
							class="controls"> <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?> </div>
          </div>
         
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Buy Method"); ?></label>
            <div class="controls">
              <select name="buy_method" id="type" class="form-control select" >
                <option value="buy_x_get_x">Buy X Get X</option>
                <option value="buy_x_get_y">Buy X Get Y</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("Date & Time"); ?></label>
            
           </div>
           <div class="row">
          <div class="form-group  col-lg-6 date_div">
           
           
                  <div class="controls ">
                    <input type="text" name="start_date" class="form-control date" placeholder="From Date " id="start_date" required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 date_div">
                  <div class="controls">
                    <input type="text" name="end_date" class="form-control date" placeholder="To Date " id="end_date" required="required">
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls ">
                    <input type="text" name="start_time" class="form-control time" placeholder="From  Time" id="start_time" required="required">
                  </div>
                </div>
                <div class="form-group  col-lg-6 time_div">
                  <div class="controls">
                    <input type="text" name="end_time" class="form-control time" placeholder="To  Time" id="end_time" required="required">
                  </div>
                </div>
            </div>
                          
      
            <div class="form-group quantity">
                <label for="value"><?php echo $this->lang->line("Buy Quantity"); ?></label>
        
                <div class="controls"> <?php echo form_input('buy_quantity', '', 'class="form-control numberonly" maxlength="2" id="buy_quantity" required="required"'); ?> </div>
            </div>
            
            
            <div class="form-group quantity">
                <label for="value"><?php echo $this->lang->line("Get Quantity"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('get_quantity', '', 'class="form-control numberonly" maxlength="2" id="get_quantity" required="required"'); ?> </div>
            </div>
            
           
       
        
        
        
        <fieldset class="buy_buy_x_get_x">
            <legend>Item:
            <button type="button" class="btn btn-primary btn-xs" id="addItemx"><i class="fa fa-plus"></i></button>
            </legend>
            <div id="itemx">
              <div class="well col-lg-12">
                <div class="form-group col-lg-12">
                  <div class="controls">
                    <select name="buy_type[]"  class="form-control select buy_type" id="buy_type_0" >
                      <option value="Sale Items">Sale Items</option>
                    </select>
                  </div>
                </div>
               
              
              	<div class="form-group col-lg-6 recipe">
                  <div class="controls">
                   <select name="buy_item[]" class="form-control buy_item select"  id="buy_item_0"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Buy") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option>
                      <?php
						}
					  ?>
                    </select>
                    
                   </div>
                </div>
                
                
                
                <div class="form-group col-lg-6 recipe_get">
                  <div class="controls">
                   <select name="get_item[]" id="get_item_0" class="form-control get_item select"   placeholder="<?= lang("select") . ' ' . lang("Sale Item Get") ?>" >
                   <option value=""></option>
                   		<?php
						foreach($recipe as $recipe_row){
						?>
                      <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option>
                      <?php
						}
					  ?>
                    </select>
                   </div>
                </div>
                               
              </div>
            </div>
          </fieldset>
        
          
          
        </div>
      </div>
    </div>
    <div class="box-footer"> <?php echo form_submit('add_buy', lang('add_buy'), 'class="btn btn-primary"'); ?> </div>
  </div>
  
  <?php echo form_close(); ?> </div>
<script>

$(document).ready(function(e) {
    

var c=1;
$('#addItemx').click(function () {
	
	
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-12"> <div class="controls"> <select name="buy_type[]" id="buy_type_'+c+'" class="form-control select buy_type" > <option value="Sale Items">Sale Items</option> <option value="Sale Groups">Sale Groups</option> </select> </div> </div>  <div class="form-group col-lg-6 recipe"> <div class="controls"> <select name="buy_item[]" id="buy_item_'+c+'" class="form-control buy_item select"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Buy") ?>" > <option value=""></option> <?php foreach($recipe as $recipe_row){ ?> <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option> <?php } ?> </select> </div> </div> <div class="form-group col-lg-6 recipe_get"> <div class="controls"> <select name="get_item[]" id="get_item_'+c+'" class="form-control get_item select"  placeholder="<?= lang("select") . ' ' . lang("Sale Item Get") ?>" ><option value=""></option> <?php foreach($recipe as $recipe_row){ ?> <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option> <?php } ?> </select> </div> </div> <button type="button" class="btn btn-primary pull-right btn-xs deleteItemx"><i class="fa fa-trash-o"></i></button></div>';
		
		
		$('#itemx').append(html);
		$('#buy_type_'+c).select2();
		$('#in_list_'+c).select2();
		$('#get_item_'+c).select2();
		$('#buy_item_'+c).select2();
		
		c++;
		
});

$("body").on('click','.deleteItemx', function(){
	$(this).closest('.well').remove();
});

$(".numberonly").keypress(function (e){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
	});
</script>