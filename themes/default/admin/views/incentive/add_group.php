
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("incentive/add_group", $attrib);
                ?>
                <div class="row">
                <div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
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
					</div>
					  <div class="col-md-12">  
						<h2 class="box_he_de">Group Details</h2>
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('name', 'name'); ?>
							<div class="controls">
								<input type="text" id="name" onkeyup="inputFirstUpper(this)" name="name"  class="form-control"/>
							</div>
						</div>
						
                        <div class="form-group col-sm-3 col-xs-12">
                <?= lang('continent', 'continent'); ?>
                <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, '', 'class="form-control select-local-continent select"  id="continent_id" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("country", "country"); ?>
                <?php
                                        echo form_dropdown('country_id', '', '', 'class="form-control select-local-country select" id="country_id" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("zone", "zone"); ?>
                <?php
                                        echo form_dropdown('zone_id', '', '', 'class="form-control select-local-zone select" id="zone_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("state", "state"); ?>
                <?php
                                        echo form_dropdown('state_id', '', '', 'class="form-control select-local-state select" id="state_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("city", "city"); ?>
                <?php
                                        echo form_dropdown('city_id', '', '', 'class="form-control select-local-city select" id="city_id" '); ?>
              </div>
              
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("area", "area"); ?>
                <?php
                                        echo form_dropdown('area_id', '', '', 'class="form-control select-local-area select" id="area_id" '); ?>
              </div>

						<!--<div class="form-group col-sm-3 col-xs-12" style="margin-top: 20px;">
							<input type="checkbox" class="checkbox" id="is_default" name="is_default" value="1"/>
							<label for="extras" class="padding05"><?= lang('is_default') ?></label>
						</div>-->

					</div> 
                    <div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('driver_details') ?>
                        <div class="pull-right">
							<input type="checkbox" class="checkbox" name="all_driver" id="all_driver" value="All"/>
							<label for="extras" class="padding05">All</label>
						</div>
                        </h2>
                        
                        <div class="clearfix"></div>
                        <?php
						foreach($drivers as $driver){
						?>
                        <div class="form-group col-sm-3 col-xs-12" style="margin-top: 20px;">
							<input type="checkbox" class="checkbox all_group" name="user_id[]" value="<?= $driver->id ?>"/>
							<label for="extras" class="padding05"><?= $driver->first_name.'('.$driver->mobile.')'; ?></label>
						</div>
                       <?php
						}
					   ?> 
                    </div>        
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_group', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function(){
	$(document).on('ifChecked', '#all_driver', function (event) {
    $('.checkth, .checkft, .all_group').iCheck('check');
    
});
$(document).on('ifUnchecked', '#all_driver', function (event) {
    $('.checkth, .checkft, .all_group').iCheck('uncheck');
   
});

});
</script>