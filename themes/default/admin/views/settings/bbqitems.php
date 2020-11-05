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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= $bbq->name; ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('BBQ_Items'); ?></p>
				<?php 
                        echo admin_form_open("system_settings/bbqitems/" . $id); ?>
                 <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('filter') ?></legend>
                            
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="category_id"><?= lang("category"); ?></label>
        
                                    <div class="controls"> <?php
										$ca[] = 'All';
										if(!empty($items)){
											 foreach($items as $category){
												 $ca[$category->id] = $category->name;
											 }
										}
                                        echo form_dropdown('category_id', $ca, '', 'class="form-control tip" id="category_id" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                   			
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("sale_items", "sale_items"); ?>
                                    <?= form_input('recipe_search', '', 'class="form-control tip" id="recipe_search"  required="required"'); ?>
                                </div>
                            </div>
                            
                            <!--<div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                    	<br>
                                        <?= form_submit('search', lang("search"), 'class="btn btn-primary"'); ?>
                                    </div>
                                </div>
                    		</div>-->
                    </fieldset>
                    <?= form_close(); ?>
                <?php 
                        echo admin_form_open("system_settings/bbqitems/" . $id); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                               
                                <tr>
                                    <th rowspan="1" class="text-center"><?= lang("category"); ?></th>
                                    <th rowspan="1" class="text-center"><?= lang("subcategory"); ?></th>
                                    <th colspan="5" class="text-center"><?= lang("items"); ?></th>
                                </tr>
                                
                                </thead>
                                <tbody>
                               
                             <?php
							 if(!empty($items)){
								 foreach($items as $category){
							 ?>  
				         	<tr class="items">
                            	
                                    <td><?= $category->name; ?></td>
                                    <?php
									if(!empty($category->subcategory)){
										foreach($category->subcategory as $subcategory){
									?>
                                    <td><?= $subcategory->name; ?></td>
                                    <td>
                                    <?php
										if(!empty($subcategory->recipe)){
										?>
                                        <ul>
                                            <?php
											foreach($subcategory->recipe as $recipe){
												if(in_array($recipe->id, explode(',',$bbq->items))){
													$checked = 'checked';
												}else{
													$checked = '';
												}
											?>
                                            <li>
                                            <span style="inline-block">
                                            <input type="checkbox" value="<?= $recipe->id ?>" class="checkbox" name="items[]" <?php echo $checked;?>>
                                            <label for="items" class="padding05"><?= $recipe->name ?></label>
                                            </span>
                                            </li>
                                           <?php
											}
										   ?> 
                                            
                                        </ul>
                                        <?php
										}
										?>
                                    </td>
                                    <?php
										}
									}
									?>
                                </tr>
                              <?php
								 }
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
$("input[type=checkbox]").each(function() {
  var name = $(this).attr('name');

  if (localStorage.getItem(name) == "true") {
    $(this).prop('checked', true);
  }
});

$("input[type=checkbox]").change(function() {

  var name = $(this).attr('name'),
    value = $(this).is(':checked');
  localStorage.setItem(name, value);
});
</script>
