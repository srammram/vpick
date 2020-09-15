<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-brand')
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
	    var form = $('#add-brand')[0];
	    var data = new FormData(form);
	    file = $('#image')[0].files[0];
	    data.append('target_file', file);
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
                        }else if (data.brands) {
			  var newStateLabel = data.brands.name;
			  var newStateVal =data.brands.id
			  var newState = new Option(newStateLabel,newStateVal, true, true);
			  // Append it to the select
			  $("select.select-brand").append(newState).trigger('change');
			  $("#myModal .close").click();
                        }
                       return false;
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_brand'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        //echo admin_form_open_multipart("system_settings/add_brand", $attrib); ?>
	<form  data-toggle="validator" data-url="<?=admin_url('system_settings/add_brand')?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-brand">
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
                <?= lang('name', 'name'); ?>
                <?= form_input('name', '', 'class="form-control gen_slug" id="name" required="required"'); ?>
		<input type="hidden" name="add_brand" value="add brand">
            </div>

           

            <div class="form-group">
                <?= lang("image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_brand', lang('add_brand'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
   $(document).ready(function(){

$(document).on('click', '#random_num', function(event){
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