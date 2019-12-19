<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("locations/edit_rental/".$id, $attrib);
                ?>
                
                <div class="row">
                <div class="instance_country col-sm-12">
                <div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $result->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        </div>
                        <div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('location_details') ?></h2>  	
                                <div class="col-md-12">
                                	<div class="form-group col-sm-3 col-xs-12">
										<?= lang('cab_type', 'taxi_type'); ?>
                                        <?php
                                        $t[''] = 'Select Taxi Type';
                                        foreach ($taxi_types as $taxi_type) {
                                            $t[$taxi_type->id] = $taxi_type->name;
                                        }
                                        echo form_dropdown('taxi_type', $t,  $result->taxi_type, 'class="form-control select"  id="taxi_type" required="required"'); ?>
                                    </div>
                                	<div class="form-group col-sm-3 col-xs-12">
										<?= lang('continent', 'continent'); ?>
                                        <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, $result->continent_id, 'class="form-control select-local-continent select"  id="continent_id" required="required"'); ?>
                                    </div>
                                   
                                    <div class="form-group col-sm-3 col-xs-12">
                                        <?= lang("country", "country"); ?>
                                       <?php
									   $lcou[''] = 'Select Country';
										foreach ($countrys as $country) {
											$lcou[$country->id] = $country->name;
										}
                                        echo form_dropdown('country_id', $lcou, $result->country_id, 'class="form-control select-local-country select" id="country_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-sm-3 col-xs-12">
                                        <?= lang("zone", "zone"); ?>
                                       <?php
									    $lz[''] = 'Select Zone';
										foreach ($zones as $zone) {
											$lz[$zone->id] = $zone->name;
										}
                                        echo form_dropdown('zone_id', $lz, $result->zone_id, 'class="form-control select-local-zone select" id="zone_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-sm-3 col-xs-12">
                                        <?= lang("state", "state"); ?>
                                       <?php
									   $ls[''] = 'Select State';
										foreach ($states as $state) {
											$ls[$state->id] = $state->name;
										}
                                        echo form_dropdown('state_id', $ls, $result->state_id, 'class="form-control select-local-state select" id="state_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-sm-3 col-xs-12">
                                        <?= lang("city", "city"); ?>
                                       <?php
									   $lci[''] = 'Select City';
										foreach ($citys as $city) {
											$lci[$city->id] = $city->name;
										}
                                        echo form_dropdown('city_id', $lci, $result->city_id, 'class="form-control select-local-city select" id="city_id" required="required"'); ?>
                                    </div>
                                    
                                    
                   
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("area", "area"); ?>
                <?php
				 $a[''] = 'Select Area';
                                       foreach ($areas as $area) {
                                            $a[$area->id] = $area->name;
                                        }
                                        echo form_dropdown('area_id', $a, $result->area_id, 'class="form-control select-local-area select" id="area_id" '); ?>
              </div>
                                    
                                </div>
                            </div>
                    
                        <div class="col-md-12">  
                        <h2 class="box_he_de">Rental Time</h2>  	
                
                            <div class="col-md-12">
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('package_name', 'package_name'); ?>
                                    <div class="controls">
                                        <input type="text" id="package_name" name="package_name" value="<?= $result->package_name ?>" class="form-control"  onkeyup="inputFirstUpper(this)"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('package_fare', 'package_fare'); ?>
                                    <div class="controls">
                                        <input type="text" id="package_price" name="package_price" value="<?= $result->package_price ?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('option_type', 'option_type'); ?>
                                    <div class="controls">
                                        <?php
										$opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
										echo form_dropdown('option_type', $opt, $result->option_type, 'id="option_type" class="form-control select" style="width:100%;"');
										?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('option_price', 'option_price'); ?>
                                    <div class="controls">
                                        <?php
										$opp = array(1 => lang('lower'), 2 => lang('higher'));
										echo form_dropdown('option_price', $opp, $result->option_price, 'id="option_price"  class="form-control select" style="width:100%;"');
										?>
                                    </div>
                                </div>
                                
                                
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('included_distance', 'Included Distance'); ?>
                                    <div class="controls">
                                        <input type="text" id="package_distance" name="package_distance"  value="<?= $result->package_distance ?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('included_duration ', 'Included Duration '); ?>
                                    <div class="controls">
                                        <input type="text" id="package_time" name="package_time"  value="<?= $result->package_time ?>" class="form-control time"/>
                                    </div>
                                </div>
                                
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('extra_distance', 'Extra Distance'); ?>
                                    <div class="controls">
                                        <input type="text" id="per_distance" name="per_distance" value="<?= $result->per_distance ?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('extra_distance_fare', 'Extra Distance_fare'); ?>
                                    <div class="controls">
                                        <input type="text" id="per_distance_price" name="per_distance_price" value="<?= $result->per_distance_price ?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('time_type', 'time_type'); ?>
                                    <div class="controls">
                                        <?php
										$ott = array(0 => lang('minutes'), 1 => lang('hours'));
										echo form_dropdown('time_type', $ott, $result->time_type, 'id="time_type"  class="form-control select" style="width:100%;"');
										?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('extra_duration', 'Extra Duration'); ?>
                                    <div class="controls">
                                        <input type="text" id="per_time" name="per_time" value="<?= $result->per_time ?>" class="form-control time"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo llang('extra_duration_fare', 'Extra Duration Fare'); ?>
                                    <div class="controls">
                                        <input type="text" id="per_time_price" name="per_time_price" value="<?= $result->per_time_price ?>" class="form-control"/>
                                    </div>
                                </div>
                                
                                <!--<div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('day_allowance', 'day_allowance'); ?>
                                    <div class="controls">
                                        <input type="text" id="day_allowance" name="day_allowance" value="<?= $result->day_allowance ?>"  class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('overnight_allowance', 'overnight_allowance'); ?>
                                    <div class="controls">
                                        <input type="text" id="overnight_allowance" name="overnight_allowance" value="<?= $result->overnight_allowance ?>"  class="form-control"/>
                                    </div>
                                </div>-->
                                
                                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                                    <div class="controls">
                                      <input type="text" id="labour_charge" name="labour_charge" value="<?= $result->labour_charge ?>"   class="form-control"/>
                                    </div>
                                  </div>
                                
                                
                            </div>
                        
                        
                 </div>
               <div class="col-sm-12 last_sa_se"> <?php echo form_submit('edit_rental', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
<style>
.switch-field {
  position: absolute;
  display: inline;
}

.switch-title {
  margin-bottom: 6px;
}

.switch-field input {
    position: absolute !important;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    width: 1px;
    border: 0;
    overflow: hidden;
}

.switch-field label {
  float: left;
}

.switch-field label {
  display: inline-block;
  width: 35px;
  background-color: #fffff;
  color: #000000;
  font-size: 14px;
  font-weight: normal;
  text-align: center;
  text-shadow: none;
  padding: 3px 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition:    all 0.1s ease-in-out;
  -ms-transition:     all 0.1s ease-in-out;
  -o-transition:      all 0.1s ease-in-out;
  transition:         all 0.1s ease-in-out;
}

.switch-field label:hover {
	cursor: pointer;
}

.switch-field input:checked + label {
  background-color: #2489c5;
  -webkit-box-shadow: none;
  box-shadow: none;
  color: #fff;
}

.switch-field label:first-of-type {
  border-radius: 13px 0 0 13px;
}

.switch-field label:last-of-type {
  border-radius: 0 13px 13px 0;
}
</style>





</div><script>
$(document).ready(function(){
	$('.is_country').change(function(){
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getTaxitype_byCountry')?>',
			data: {is_country: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option1 = '<option value="">Select Type</option>';
				$.each(scdata.type,function(n,v){
					$option1 += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$("#taxi_type").html($option1);
				
				
			}
		})
	});
});
</script>