
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
            
            symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },
			
			unicode_symbol:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The symbol'
                    }
                }
            },
			
            iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The iso_code'
                    }
                }
            },

            numeric_iso_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The numeric_iso_code'
                    }
                }
            },
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
  
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_currency", $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                    	<div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'is_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
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
                        <div class="col-md-12">                                   
                           	<?php
							$phonecode = $this->site->getPhonecode($_GET['is_country']);
							?>
                            <div class="form-group">
                                <?php echo lang('phonecode', 'phonecode'); ?>
                                <div class="controls">
                                    <input type="text" id="phonecode" name="phonecode" value="<?= $phonecode ? $phonecode : 0 ?>" readonly class="form-control"                                            />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('mobile', 'booking_phone'); ?>
                                <div class="controls">
                                    <input type="text" id="booking_phone" name="booking_phone" required class="form-control"                                            />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('name', 'booking_name'); ?>
                                <div class="controls">
                                    <input type="text" id="booking_name" name="booking_name" required class="form-control"                                           />
                                </div>
                            </div>
                            
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_currency', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

$(document).on('change', '#is_country', function(){
	
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/booking_crm/create_customer?is_country="+is_country;
		
    
})
</script>


