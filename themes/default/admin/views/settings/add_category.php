<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-category')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                                //submitButtons: 'input[type="submit"]',
                                
                            }
                        )
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
	    $('.counter-form-error').remove();
            $obj = $(this);
            $url = $obj.attr('data-url');
            $formData = $obj.serialize();
	    var form = $('#add-category')[0];
	    var data = new FormData(form);
	    file = $('#image')[0].files[0];
	    data.append('target_file', file);
	    $parentCategory = $('.select-parent-category').select2('data');
	    //console.log($parentCategory);
	    $.ajax({
                    url: $url,
                    type: "POST",
                    data: data,//$formData+'&userfile='+data+'&add_brand=Add Brand',
                    cache: false,
		    dataType: 'json',
		    processData: false, // Don't process the files
		    contentType: false,
                    success:function(data){
			 
                        if (data.error) {
                            $('<div class="counter-form-error">'+data.error+'</div>').insertAfter($('.modal-body p:eq(0)'));
                            $obj.find('input[type="submit"]').attr('disabled',false);//$('#add-counter').live('submit');
                        }else if (data.category) {
			    $("#myModal .close").trigger('click');
			    
			    if ($parentCategory.id=='') {
				var newStateLabel = data.category.name;
				var newStateVal =data.category.id				
			    }else{
				$Category = $('.select-category').select2('data');
				var newStateLabel = $parentCategory.text;
				var newStateVal = $parentCategory.id
				$('#subcategory-hidden').val([data.category.id]);
			    }
			    var newState = new Option(newStateLabel,newStateVal, false, true);
				// Append it to the select
			    $("select.select-category").append(newState).trigger('change');
			    
			    return false;
                        }
                       
                    },
                   
                });
        });
        
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_category'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        //echo admin_form_open_multipart("system_settings/add_category", $attrib); ?>
	<form  data-toggle="validator" data-url="<?=admin_url('system_settings/add_category')?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-category">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            
          <div class="form-group">
                <?= lang('code', 'code'); ?>
               
                <div class="input-group col-md-12">
                	<?= form_input('code', '', 'class="form-control numberonly" id="code" required="required" maxlength="9" '); ?>
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
                <?= lang('category_name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
		
        	<div class="form-group">
                <?= lang('khmer_name', 'khmer_name'); ?>
                <?= form_input('khmer_name', set_value('khmer_name'), 'class="form-control" id="khmer_name" required="required"'); ?>
            </div>
           

            <div class="form-group">
                <?= lang("category_image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
            <div class="form-group">
                <?= lang("parent_category", "parent") ?>
                <?php
                $cat[''] = lang('select').' '.lang('parent_category');
                foreach ($categories as $pcat) {
                    $cat[$pcat->id] = $pcat->name;
                }
                echo form_dropdown('parent', $cat, (isset($_POST['parent']) ? $_POST['parent'] : ''), 'class="form-control select select-parent-category" id="parent" style="width:100%"')
                ?>
            </div>

        </div>
        <div class="modal-footer">
	    <input type="hidden" name="add_category" value="<?=lang('add_category')?>">
            <?php echo form_submit('add_category', lang('add_category'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
  $(document).ready(function(){
$(document).on('click', '#random_num', function(event){
//$('#random_num').click(function(event){
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