<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_warehouse'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("system_settings/edit_warehouse/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
	    <div class="form-group">				
                <label><?=lang('type')?>:</label><label for="suki" class="padding03" style="font-weight:bold"><?= ($warehouse->type==0)?lang('warehouse'): lang('outlet')?></label>
            </div>
	    <div class="form-group show-warehouse" style="display:<?=($warehouse->type==0)?'none':'block';?>;">
		<label><?=lang('select_warehouse')?></label></br>
		<?php foreach($warehouses as $k => $row) : ?>		
		    <?php $parent_warehouse = explode(',',$warehouse->parent_warehouses); ?>
		    <label for="warehouse-<?=$row->id?>" class="padding03">
			<input type="checkbox" value="<?=$row->id?>" name="parent_warehouses[]" class="map-warehouse checkbox"  id="warehouse-<?=$row->id?>" <?php if(in_array($row->id,$parent_warehouse)){ echo 'checked="checked"';}?>><?=$row->name ?>
		    </label>		  
		
		<?php endforeach; ?>
	    </div>
           <div class="form-group">
                <?= lang('code', 'code'); ?>
                <div class="input-group col-md-12">
                	<?= form_input('code', $warehouse->code, 'class="form-control numberonly" id="code" maxlength="9" required="required"' ); ?>
                     <span class="" id="random_num" style="    padding: 6px 10px;
    background: #efefef;
    position: relative;
    margin-top: -34px;
    border: 1px solid #ccc;
    float: right;
    z-index: 99;
    cursor: pointer;">
                        <i class="fa fa-random"></i>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line("name"); ?></label>
                <?php echo form_input('name', $warehouse->name, 'class="form-control" id="name" required="required"'); ?>
            </div>
            <!--<div class="form-group">
                <label class="control-label" for="price_group"><?php echo $this->lang->line("price_group"); ?></label>
                <?php
                //$pgs[''] = lang('select').' '.lang('price_group');
//                foreach ($price_groups as $price_group) {
//                    $pgs[$price_group->id] = $price_group->name;
//                }
//                echo form_dropdown('price_group', $pgs, $warehouse->price_group_id, 'class="form-control tip select" id="price_group" style="width:100%;"');
                ?>
            </div>-->
            <div class="form-group">
                <label class="control-label" for="phone"><?php echo $this->lang->line("phone"); ?></label>
                <?php echo form_input('phone', $warehouse->phone, 'class="form-control numberonly" id="phone" required="required" onkeydown="return(event.which == 8 && event.which == 0 || (event.charCode >= 48 || event.which <= 57))" maxlength="20"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="email"><?php echo $this->lang->line("email"); ?></label>
                <?php echo form_email('email', $warehouse->email, 'class="form-control" id="email" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line("address"); ?></label>
                <?php echo form_textarea('address', $warehouse->address, 'class="form-control" id="address" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang("warehouse_map", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
	    
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_warehouse', lang('edit_warehouse'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script>
$(document).ready(function(){

$('#random_num').click(function(event){
	event.preventDefault();
			$(this).parent('.input-group').children('input').val(generateCardNo(8));
			 $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $('#code'));
			 
			
		});
		$(".numberonly").keypress(function (event){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
});
</script>
<?= $modal_js ?>