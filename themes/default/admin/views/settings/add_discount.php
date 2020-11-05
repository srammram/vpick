<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
  <div class="box-header">
    <h2 class="blue"><i class="fa-fw fa fa-plus"></i>
      <?= lang('add_discount'); ?>
    </h2>
  </div>
  <div class="box-content">
    <?php $attrib = array('id'=>'discount-form','role' => 'form');
        echo admin_form_open("system_settings/add_discount", $attrib); ?>
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
            <label for="description"><?php echo $this->lang->line("description"); ?></label>
            <div
							class="controls"> <?php echo form_input('description', '', 'class="form-control" id="description" required="required"'); ?> </div>
          </div>
          <div class="form-group">
            <label for="method"><?php echo $this->lang->line("discount_method"); ?></label>
            <div class="controls">
              <select name="discount_method" id="discount_method" class="form-control select" >
                <optgroup label="Discount">
                <option value="discount_simple">Simple Discount</option>
                <option value="discount_on_total">Discount on Total</option>
                </optgroup>
              </select>
            </div>
          </div>
          
        <div class="well">
            <div class="form-group quantity" style="display:none;">
                <label for="value"><?php echo $this->lang->line("Buy Quantity"); ?></label>
        
                <div class="controls"> <?php echo form_input('buy_quantity', '', 'class="form-control" id="buy_quantity" required="required"'); ?> </div>
            </div>
            <div class="form-group amount" style="display:none;">
                <label for="value"><?php echo $this->lang->line("amount"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('amount', '', 'class="form-control" id="amount" required="required"'); ?> </div>
            </div>
            
            <div class="form-group quantity" style="display:none;">
                <label for="value"><?php echo $this->lang->line("Get Quantity"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('get_quantity', '', 'class="form-control" id="get_quantity" required="required"'); ?> </div>
            </div>
            
            <div class="form-group method">
                <label for="method"><?php echo $this->lang->line("discount_type"); ?></label>
        
                <div class="controls"> 
                    <select name="discount_type" id="discount_type" class="form-control select">
                        
                        <optgroup label="Discount">
                            <option value="percentage_discount">Percentage Discount</option>
                            <option value="fixed_discount">Fixed Discount</option>
                         </optgroup>
                         
                            
                     </select>
                </div>
            </div>
            <div class="form-group method">
                <label for="value"><?php echo $this->lang->line("Discount_Value"); ?></label>
        
                <div
                    class="controls"> <?php echo form_input('discount', '', 'class="form-control" id="discount" required="required"'); ?> </div>
            </div>
        </div>
        
        <fieldset class="discount_simple">
            <legend><?= lang('item') ?>:
            <button type="button" class="btn btn-primary btn-xs" id="addItem"><i class="fa fa-plus"></i></button>
            </legend>
            <div id="item">
              <div class="well col-lg-12">
                <div class="form-group col-lg-6">
                  <div class="controls">
                    <select name="item[0][item_method]"  class="form-control select item_method" >
                      <option value="item_product"><?= lang('recipe') ?></option>
                      <option value="item_category"><?= lang('recipe_category') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-6">
                  <div class="controls">
                    <select name="item[0][item_type]" id="item_type" class="form-control select" >
                      <option value="in_list"><?= lang('in_list') ?></option>
                      <option value="not_in_list"><?= lang('not_in_list') ?></option>
                    </select>
                  </div>
                </div> 
                 
                <div class="form-group col-lg-12 recipe">
                  <!--<div class="controls">-->
		    
                   <select name="item[0][recipe_item][]" id="recipe_item" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("recipe_item") ?>" required="required">
                   		<?php
                        foreach($recipe as $recipe_row){
                        ?>
                          <option value="<?php echo $recipe_row->id; ?>"><?php echo $recipe_row->name; ?></option>
                          <?php
                        }
                      ?>
                    </select>
                  <!-- </div>-->
                </div>
                
                <div class="form-group col-lg-12 recipe_category" style="display:none;">
                  <div class="controls">
                  
                  <select name="item[0][recipe_category_item][]" id="recipe_category_item" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("recipe_category") ?>" >
                  	<?php
                  foreach($recipe_category as $recipe_category_row){
                  ?>
                      <option value="<?php echo $recipe_category_row->id; ?>">
                      <?php echo $recipe_category_row->name; ?></option>
                  <?php
                  }
                  ?>
                  </select>
                </div>
                </div>        
              </div>
            </div>
          </fieldset>
        
        <fieldset class="discount_buy_x_get_x" style="display:none;">
            <legend><?= lang('item') ?>:
            <button type="button" class="btn btn-primary btn-xs" id="addItemx"><i class="fa fa-plus"></i></button>
            </legend>
            <div id="itemx">
              <div class="well col-lg-12">
                <div class="form-group col-lg-6">
                  <div class="controls">
                    <select name="item[0][item_methodx]"  class="form-control select item_method" >
                      <option value="item_product"><?= lang('recipe') ?></option>
                      <option value="item_category"><?= lang('recipe_category') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-6">
                  <div class="controls">
                    <select name="item[0][item_typex]" id="item_type" class="form-control select" >
                      <option value="in_list"><?= lang('in_list') ?></option>
                      <option value="not_in_list"><?= lang('not_in_list') ?></option>
                    </select>
                  </div>
                </div> 
              
              	<div class="form-group col-lg-12 recipe">
                  <div class="controls">
                   <select name="item[0][recipe_itemx][]" id="recipe_itemx" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("recipe_item_buy") ?>" >
                    <?php
                    foreach($recipe as $recipe_row){
                    ?>
                        <option value="<?php echo $recipe_row->id; ?>">
                        <?php echo $recipe_row->name; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  </div>
                </div>
                
                <div class="form-group col-lg-12 recipe_category" style="display:none;">
                  <div class="controls">
                  <select name="item[0][recipe_category_itemx][]" id="recipe_category_itemx" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("recipe_item_buy") ?>" >
                   	<?php
                      foreach($recipe_category as $recipe_category_row){
                      ?>
                        <option value="<?php echo $recipe_category_row->id; ?>">
                        <?php echo $recipe_category_row->name; ?></option>
                        <?php
                      }
					         ?>
                    </select>
                   </div>
                </div>
                
                <div class="form-group col-lg-12 recipe_get">
                  <div class="controls">
                   <select name="item[0][recipe_item_getx][]" id="recipe_item_getx" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("recipe_item_get") ?>" >
                    		<?php
                    foreach($recipe as $recipe_row){
                    ?>
                      <option value="<?php echo $recipe_row->id; ?>">
                      <?php echo $recipe_row->name; ?></option>
                      <?php
                    }
                    ?>
                    </select>
                   </div>
                </div>         
              </div>
            </div>
          </fieldset>
        
          <fieldset>
            <legend><?= lang('conditions') ?>:
            <button type="button" class="btn btn-primary btn-xs" id="addCondition"><i class="fa fa-plus"></i></button>
            </legend>
            <div id="condition">
              <div class="well col-lg-12">
                <div class="form-group col-lg-12">
                  <div class="controls">
                    <select name="condition[0][condition_method]"  class="form-control select condition_method" >
                      <option value="condition_date"><?= lang('date') ?></option>
                      <option value="condition_time"><?= lang('time') ?></option>
                      <option value="condition_days"><?= lang('day_of_weak') ?></option>
                    </select>
                  </div>
                </div>
                <!-- <div class="form-group col-lg-6">
                  <div class="controls">
                    <select name="condition[0][condition_type]" id="condition_type" class="form-control select" >
                      <option value="in_list">In List</option>
                      <option value="not_in_list">Not In List</option>
                    </select>
                  </div>
                </div> -->
                <div class="form-group  col-lg-6 date_div condition_date_div">
                  <div class="controls ">
                    <input type="text" name="condition[0][from_date]" class="form-control totay_date" placeholder="From Date " id="from_date" required="required" autocomplete=off>
                  </div>
                </div>
                <div class="form-group  col-lg-6 date_div condition_date_div">
                  <div class="controls">
                    <input type="text" name="condition[0][to_date]" class="form-control totay_date" placeholder="To Date " id="to_date" required="required" autocomplete=off>
                  </div>
                </div>
                
                <div class="form-group  col-lg-6 time_div condition_time_div" style="display: none">
                  <div class="controls ">
                    <input type="text" name="condition[0][from_time]" class="form-control time" placeholder="From  Time" id="from_timel" required="required" autocomplete=off>
                  </div>
                </div>
                <div class="form-group  col-lg-6 time_div condition_time_div" style="display: none">
                  <div class="controls">
                    <input type="text" name="condition[0][to_time]" class="form-control time" placeholder="To  Time" id="to_time" required="required" autocomplete=off>
                  </div>
                </div>
                
                <div class="form-group col-lg-12 condition_days condition_days_div" style="display:none">
                  <div class="controls">
                    <select name="condition[0][condition_days][]" id="condition_days" multiple class="form-control select"  placeholder="Choose Multiple days" autocomplete=off>
                      <option value=""><?= lang('choose_days') ?></option>
                      <option value="monday"><?= lang('monday') ?></option>
                      <option value="tuesday"><?= lang('tuesday') ?></option>
                      <option value="wednesday"><?= lang('wednesday') ?></option>
                      <option value="thursday"><?= lang('thursday') ?></option>
                      <option value="friday"><?= lang('friday') ?></option>
                      <option value="saturday"><?= lang('saturday') ?></option>
                      <option value="sunday"><?= lang('sunday') ?></option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          
        </div>
	<div class="col-md-5">
	  <div class="form-group">
            <label for="Unique_Discount"><?php echo lang("Unique_Discount"); ?></label>
            <div class="controls"> <input type="checkbox" name="unique_discount" value="1" <?php if(isset($_POST['unique_discount'])){ echo 'checked="checked"';}?>></div>
          </div>
	</div>
      </div>
    </div>
    <div class="box-footer"> <?php echo form_submit('add_discount', lang('add_discount'), 'class="discount-submit btn btn-primary"'); ?> </div>
  </div>
  
  <?php echo form_close(); ?> </div>
<script>
$(document).on('change', '#discount_method', function(){
	var method = $(this).val();
	if(method == 'discount_simple'){
		$(".quantity").hide();
		$(".amount").hide();
		$(".discount_buy_x_get_x").hide();
    $(".discount_simple").show();   
		$(".method").show();		
	}else if(method == 'discount_on_total'){
		$(".quantity").hide();
		$(".amount").show();
		$(".discount_buy_x_get_x").hide();
		$(".discount_simple").hide();
	}else if(method == 'discount_buy_x_get_x'){
    $(".amount").hide();
    $(".quantity").hide();
    $(".method").hide();
		$(".discount_buy_x_get_x").hide();
		$(".discount_simple").show();
	}else if(method == 'discount_buy_x_get_y'){
		$(".amount").hide();
    $(".method").hide();
		$(".quantity").show();
		$(".discount_simple").hide();
		$(".discount_buy_x_get_x").show();
	}
});
$(document).on('change', '.condition_method', function(){
	$index = $('select.condition_method').index($(this));
	var condition_method = $(this).val();
	if(condition_method == 'condition_date'){
		$(this).parent().parent().parent().children('.condition_days').hide();
		$(this).parent().parent().parent().children('.time_div').hide();
		$(this).parent().parent().parent().children('.date_div').show();
	}else if(condition_method == 'condition_time'){
		$(this).parent().parent().parent().children('.date_div').hide();
		$(this).parent().parent().parent().children('.condition_days').hide();
		$(this).parent().parent().parent().children('.time_div').show();
	}else if(condition_method == 'condition_days'){
		$(this).parent().parent().parent().children('.time_div').hide();
		$(this).parent().parent().parent().children('.date_div').hide();
		$(this).parent().parent().parent().children('.condition_days').show();
	}
	
	$thisval = condition_method;
	$methods = ['condition_date','condition_days','condition_time'];
	$selectmethods = [];
	$('select.condition_method').each(function(n,v){
	  $selectmethods.push($(this).find('option:selected').val());
	  if (n!=$index) {
	    $('select[name="condition['+n+'][condition_method]"] option[value='+$thisval+']').attr("disabled",true);
	  }
	  
	});
	var array3 = $methods.filter(function(obj) { return $selectmethods.indexOf(obj) == -1; });
	$.each(array3,function(n,v){
	  console.log(v
)
	  $("select.condition_method option[value=" + v + "]").removeAttr('disabled');
	})
		
		
});



$(document).on('change', '.item_method', function(){
	var item_method = $(this).val();
	if(item_method == 'item_product'){
		$(this).parent().parent().parent().children('.recipe_category').hide();
		$(this).parent().parent().parent().children('.recipe').show();
	}else if(item_method == 'item_category'){
		$(this).parent().parent().parent().children('.recipe').hide();
		$(this).parent().parent().parent().children('.recipe_category').show();
	}
});


var aCon = 1;
$('#addCondition').click(function () {
  
  $obj = $(this);
  $select_method = [];
  $('select.condition_method').each(function(){
    //console.log($(this).find('option:selected').val())
    $select_method.push($(this).val());
  });
  console.log($select_method);
  $day_disabled ='';$date_disabled='';$time_disabled='';
  if ($select_method.indexOf("condition_date")!=-1) {
    $date_disabled = 'disabled="disabled"';
  }
  if ($select_method.indexOf("condition_time")!=-1) {
    $time_disabled = 'disabled="disabled"';
  }
  if ($select_method.indexOf("condition_days")!=-1) {
    $day_disabled = 'disabled="disabled"';
  }
  console.log($('select.condition_method').length)
	  if ($('select.condition_method').length==3) {	   
	    return false;
	  }else if ($('select.condition_method').length==3) {
	     $obj.hide();
	  }
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-12"> <div class="controls"> <select name="condition['+aCon+'][condition_method]" id="condition_method_' + aCon + '" class="form-control select condition_method" > <option value="condition_date" '+$date_disabled+'>Date</option><option value="condition_time" '+$time_disabled+'>Time</option> <option value="condition_days" '+$day_disabled+'>Days of Week</option> </select> </div></div><div class="form-group col-lg-6 date_div condition_date_div"> <div class="controls "> <input type="text" name="condition['+aCon+'][from_date]" class="form-control date_picker" placeholder="From Date" id="from_date_'+aCon+'" required="required"> </div></div><div class="form-group col-lg-6 date_div condition_date_div"> <div class="controls"> <input type="text" name="condition['+aCon+'][to_date]" class="form-control date_picker" placeholder="To Date" id="to_date_'+aCon+'" required="required"> </div></div><div class="form-group  col-lg-6 time_div condition_time_div" style="display: none"><div class="controls "><input type="text" name="condition['+aCon+'][from_time]" class="form-control timepicker" placeholder="From  Time" id="from_time1" required="required"></div></div><div class="form-group  col-lg-6 time_div condition_time_div" style="display: none"><div class="controls"><input type="text" name="condition['+aCon+'][to_time]" class="form-control timepicker" placeholder="To  Time" id="to_time" required="required"></div></div><div class="form-group col-lg-12 condition_days condition_days_div" style="display:none"> <div class="controls"> <select name="condition['+aCon+'][condition_days][]" id="condition_days_'+aCon+'" multiple class="form-control select" placeholder="Choose Multiple days"> <option value="monday">Monday</option> <option value="tuesday">Tuesday</option> <option value="wednesday">Wednesday</option> <option value="thursday">Thursday</option> <option value="friday">Friday</option> <option value="saturday">Saturday</option> <option value="sunday">Sunday</option> </select> </div></div><button type="button" class="btn btn-primary pull-right btn-xs deleteCondition"><i class="fa fa-trash-o"></i></button></div>';
		
		$('#condition').append(html);
		
		$('#condition_method_' + aCon).select2();
		$('#condition_type_' + aCon).select2();
		$('#condition_days_' + aCon).select2();
		$thisval = $('select[name="condition['+aCon+'][condition_method]"]').val();
		
		
		$('select.condition_method option[value='+$thisval+']').attr("disabled",true);
		console.log(aCon +'--'+$thisval)
		$('select[name="condition['+aCon+'][condition_method]"]').closest('.well').find('.condition_date_div,.condition_time_div,.condition_days_div').hide();
		$('select[name="condition['+aCon+'][condition_method]"]').closest('.well').find('.'+$thisval+'_div').show();
		$('select[name="condition['+aCon+'][condition_method]"] option[value='+$thisval+']').attr("disabled",false);
		
		aCon++;
});




$("body").on('click','.deleteCondition', function(){
  $obj = $(this);
  $thisval = $obj.closest('div.well').find('select.condition_method option:selected').val();
	$obj.closest('.well').remove();
	
	if ($('select.condition_method').length==2) {
	     $('#addCondition').show();
	}
	$("select.condition_method option[value=" + $thisval + "]").removeAttr('disabled');
})


var b=1;
$('#addItem').click(function () {
	
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-6"> <div class="controls"> <select name="item['+b+'][item_method]" id="item_method_'+b+'" class="form-control select item_method" > <option value="item_product"><?= lang('recipe') ?></option> <option value="item_category"><?= lang('recipe_category') ?></option> </select> </div></div><div class="form-group col-lg-6"> <div class="controls"> <select name="item['+b+'][item_type]" id="item_type_'+b+'" class="form-control select" > <option value="in_list">In List</option> <option value="not_in_list">Not In List</option> </select> </div></div><div class="form-group col-lg-12 recipe"> <div class="controls"><select name="item['+b+'][recipe_item][]" id="recipe_item_'+b+'" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("Recipe Item") ?>" ><?php foreach($recipe as $recipe_row){ ?><option value="<?php echo $recipe_row->id; ?>"><?php echo str_replace("'","\'",$recipe_row->name) ?></option><?php } ?></select></div></div><div class="form-group col-lg-12 recipe_category" style="display:none;"> <div class="controls"><select name="item['+b+'][recipe_category_item][]" id="recipe_category_item_'+b+'" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("Recipe Category") ?>" ><?php foreach($recipe_category as $recipe_category_row){ ?><option value="<?php echo $recipe_category_row->id; ?>"><?php echo $recipe_category_row->name; ?></option><?php } ?></select></div></div><button type="button" class="btn btn-primary pull-right btn-xs deleteItem"><i class="fa fa-trash-o"></i></button></div>';		
		
	
		$('#item').append(html);
	    $('#item_method_' + b).select2();
		$('#item_type_' + b).select2();
		$('#recipe_item_' + b).select2();
		$('#recipe_category_item_' + b).select2();
		b++;
});



$("body").on('click','.deleteItem', function(){
	$(this).closest('.well').remove();
})

var c=1;
$('#addItemx').click(function () {
	
	
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-6"> <div class="controls"> <select name="item['+c+'][item_methodx]" id="item_methodx_'+c+'" class="form-control select item_method" > <option value="item_product">Recipe</option> <option value="item_category">Recipe Category</option> </select> </div></div><div class="form-group col-lg-6"> <div class="controls"> <select name="item['+c+'][item_typex]" id="item_typex_'+c+'" class="form-control select" > <option value="in_list">In List</option> <option value="not_in_list">Not In List</option> </select> </div></div><div class="form-group col-lg-12 recipe"> <div class="controls"><select name="item['+c+'][recipe_itemx][]" id="recipe_itemx_'+c+'" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("Recipe Item Buy") ?>" ><?php foreach($recipe as $recipe_row){ ?><option value="<?php echo $recipe_row->id; ?>"><?php echo str_replace("'","\'",$recipe_row->name); ?></option><?php } ?></select></div></div><div class="form-group col-lg-12 recipe_category" style="display:none;"> <div class="controls"> <select name="item['+c+'][recipe_category_itemx][]" id="recipe_category_itemx_'+c+'" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("Recipe Category Buy") ?>" ><?php foreach($recipe_category as $recipe_category_row){ ?><option value="<?php echo $recipe_category_row->id; ?>"><?php echo '$recipe_category_row->name;' ?></option><?php } ?></select></div></div><div class="form-group col-lg-12 recipe_get"> <div class="controls"> <select name="item['+c+'][recipe_item_getx][]" id="recipe_item_getx_'+c+'" class="form-control select"  multiple placeholder="<?= lang("select") . ' ' . lang("Recipe Item Get") ?>" ><?php foreach($recipe as $recipe_row){ ?> <option value="<?php echo $recipe_row->id; ?>"><?php echo str_replace("'","\'",$recipe_row->name); ?></option><?php } ?></select></div></div><button type="button" class="btn btn-primary pull-right btn-xs deleteItemx"><i class="fa fa-trash-o"></i></button></div>';
		
		
		$('#itemx').append(html);
		$('#item_methodx_' + c).select2();
		$('#item_typex_' + c).select2();
		$('#recipe_itemx_' + c).select2();
		$('#recipe_category_itemx_' + c).select2();
		$('#recipe_itemg_' + c).select2();
		
		c++;
		
});

$("body").on('click','.deleteItemx', function(){
	$(this).closest('.well').remove();
});
$(document).ready(function(){
  $("#discount-form").validate({});
});
</script>
<!--<script>
 $("#discount-form").validate({
ignore: [""],
     highlight: function (element, errorClass, validClass) {
       var elem = $(element);
       if (elem.hasClass("select2-hidden-accessible")) {
           $("#select2-" + elem.attr("id") + "-container").parent().addClass(errorClass); 
       } else {
           elem.addClass(errorClass);
       }
     },    
     unhighlight: function (element, errorClass, validClass) {
         var elem = $(element);
         if (elem.hasClass("select2-hidden-accessible")) {
              $("#select2-" + elem.attr("id") + "-container").parent().removeClass(errorClass);
         } else {
             elem.removeClass(errorClass);
         }
     },
     errorPlacement: function(error, element) {
       var elem = $(element);
       if (elem.hasClass("select2-hidden-accessible")) {
           element = $("#select2-" + elem.attr("id") + "-container").parent(); 
           error.insertAfter(element);
       } else {
           error.insertAfter(element);
       }
     }
   });


</script>-->
