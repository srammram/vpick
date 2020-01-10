
<script>
$('form[class="add_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                   
                }
            },
            
          myClass: {
            selector: '.country_instance',
            validators: {
                notEmpty: {
                    message: 'Select Instance of country'
                }
            }
        },
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>











<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_cab_type'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from','role' => 'form');
        echo admin_form_open_multipart("masters/add_taxi_type", $attrib); ?>
        <div class="modal-body">
		<div class="col-md-12">
            	<h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <?php echo lang('instance_of_country', 'instance_of_country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country country_instance"  name="is_country" id="is_country">
                <option value="">Select Country</option>
                <?php
                foreach($AllCountrys as $AllCountry){
                ?>
                <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                <?php
                }
                ?>
            </select>
            </div>
            
            
             <div class="form-group col-sm-6 col-xs-12">
                <?= lang('category', 'category_id'); ?>
				<?php
                $t[''] = 'Select Category';
                foreach ($parent as $type) {
                    $t[$type->id] = $type->name;
                }
                echo form_dropdown('category_id', $t, '', 'class="form-control select"  id="category_id" required="required"'); ?>
            </div>
            
            
            
            <div class="form-group col-sm-6 col-xs-12">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
            </div>
            <div class="form-group col-sm-6">
            <?php echo lang('image', 'image'); ?>
            <select  required class="form-control select taxi_image_id"  name="taxi_image_id" id="taxi_image_id">
                <option value="">Select Image</option>
                <?php
                foreach($typeimage as $timage){
                ?>
                <option value="<?= $timage->id ?>"><?= $timage->name ?></option>
                <?php
                }
                ?>
            </select>
            </div>
            
            <div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('tons_details') ?></h2>
						<div id="field">
                            <div id="field0">
                            
                            
                                <div class="form-group col-md-4 col-xs-12">
                                    <?= lang('tons', 'tons'); ?>
                                    <?php echo form_input('tons[]', '', 'class="form-control num" id="tons" onkeyup="checkNum(this)" required="required"'); ?>
                                </div>
                               
                                
                                <div class="form-group col-md-4 col-xs-12">
                                    <?= lang('shift', 'shift'); ?>
                                    <?php
                                    $f['Both Shift'] = 'Both Shift';
                                    $f['Day Shift'] = 'Day Shift';
									$f['Night Shift'] = 'Night Shift';
									
                                    echo form_dropdown('shift[]', $f, '', 'class="form-control select"  id="shift" required="required"'); ?>
                                </div>
                                
                                
                                <div class="form-group col-md-4 col-xs-12">
                                    
                                    <button type="button" style="margin-top:30px;" id="add-more" name="add-more" class="btn btn-primary btn-block">Add More</button>
                                </div>
                       			
                            
                            </div>
                            <div class="clearfix"></div>
                        </div>
            			</div>
            
            
           
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_taxi_type', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script>
$(document).ready(function(){
	$(".country_instance").select2();
	$(".taxi_image_id").select2();
});
</script>
<script>
$(document).ready(function(){
	
	$('.country_instance').change(function(){
		$('#category_id').select2('destroy');
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getTaxicategory_byCountry')?>',
			data: {is_country: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option1 = '<option value="">Select Category</option>';
				if (scdata.category && scdata.category.length > 0) {
					$.each(scdata.category,function(n,v){
						$option1 += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				$("#category_id").html($option1);
				$("#category_id").select2();
				
				
			}
		})
	});
});

$(document).ready(function () {
    var next = 0;
	$(document).on('click', "#add-more", function(e){
 
        e.preventDefault();
        var addto = "#field";
       // var addRemove = "#field" + (next);
        next = next + 1;
        var newIn = ' <div id="field'+ next +'" name="field'+ next +'"><div class="form-group col-md-4 col-xs-12"><label>Tons</label><input type="text" name="tons[]" class="form-control num"  onkeyup="checkNum(this)" id="tons" ></div><div class="form-group col-md-4 col-xs-12"><label>Shift</label><select name="shift[]" class="form-control select"><option value="Both Shift">Both Shift</option><option value="Day Shift">Day Shift</option><option value="Night Shift">Night Shift</option></select></div><div class="form-group col-md-4 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div></div><div class="clearfix"></div>';
        var newInput = $(newIn);
        //var removeBtn = '';
       // var removeButton = $(removeBtn);
        $(addto).before(newInput);
       // $(addRemove).before(removeButton);
        $("#field" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = parseInt(this.id.charAt(this.id.length-1));
				fieldNum = parseInt(fieldNum + 1);
                var fieldID = "#field" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });


});



function checkNum(input) {
	input.value = input.value.match(/^\d+\.?\d{0,1}/);  
}

/*$(".num").keyup(function (e){
	this.value = this.value.match(/^\d+\.?\d{0,1}/);  
});*/
</script>