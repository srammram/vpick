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
                        
                        <div class="form-group  col-xs-3">
										<?= lang('cab_type', 'taxi_type'); ?>
                                        <?php
                                        $t[''] = 'Select Taxi Type';
                                        foreach ($taxi_types as $taxi_type) {
                                            $t[$taxi_type->id] = $taxi_type->name;
                                        }
                                        echo form_dropdown('taxi_type', $t,  $result->taxi_type, 'class="form-control select"  id="taxi_type" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-sm-3 col-xs-12">
									<?php echo lang('package_name', 'package_name'); ?>
                                    <div class="controls">
                                        <input type="text" id="package_name" name="package_name" value="<?= $result->package_name ?>" class="form-control"  onkeyup="inputFirstUpper(this)"/>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-12">  
                        	<h2 class="box_he_de"><?= lang('location_details') ?></h2>  	
                                <div class="col-md-12">
                                	
                                	<div class="form-group  col-xs-3">
										<?= lang('continent', 'continent'); ?>
                                        <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, $result->continent_id, 'class="form-control select-local-continent select"  id="continent_id" required="required"'); ?>
                                    </div>
                                   
                                    <div class="form-group  col-xs-3">
                                        <?= lang("country", "country"); ?>
                                       <?php
									   $lcou[''] = 'Select Country';
										foreach ($countrys as $country) {
											$lcou[$country->id] = $country->name;
										}
                                        echo form_dropdown('country_id', $lcou, $result->country_id, 'class="form-control select-local-country select" id="country_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group  col-xs-3">
                                        <?= lang("zone", "zone"); ?>
                                       <?php
									    $lz[''] = 'Select Zone';
										foreach ($zones as $zone) {
											$lz[$zone->id] = $zone->name;
										}
                                        echo form_dropdown('zone_id', $lz, $result->zone_id, 'class="form-control select-local-zone select" id="zone_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group  col-xs-3">
                                        <?= lang("state", "state"); ?>
                                       <?php
									   $ls[''] = 'Select State';
										foreach ($states as $state) {
											$ls[$state->id] = $state->name;
										}
                                        echo form_dropdown('state_id', $ls, $result->state_id, 'class="form-control select-local-state select" id="state_id" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group  col-xs-3">
                                        <?= lang("city", "city"); ?>
                                       <?php
									   $lci[''] = 'Select City';
										foreach ($citys as $city) {
											$lci[$city->id] = $city->name;
										}
                                        echo form_dropdown('city_id', $lci, $result->city_id, 'class="form-control select-local-city select" id="city_id" required="required"'); ?>
                                    </div>
                                    
                                    
                   
              <div class="form-group col-xs-3">
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
                    
                        <div class="col-md-4">   
                            <h2 class="box_he_de">Pick Fare 
                                <div class="switch-field pull-right">
                    <input type="radio" value="0" id="switch_left_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==0) ? "checked" : ''; ?>>
                    <label for="switch_left_is_peak">OFF</label>
                    <input type="radio" value="1" id="switch_right_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==1) ? "checked" : ''; ?>>
                    <label for="switch_right_is_peak">ON</label>
                  </div>
                  
                                <button type="button" id="peek_fare" class="btn  btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
                                <?= lang("add"); ?>
                                </button>
                            </h2> 	
                                <div class="col-md-12 peek">
                                
                                	
                                    <?php
											if($result->is_peak == 1 && !empty($peek_slot)){
												$i=0;
												foreach($peek_slot as $pslot){
											?>                
											<div class="row well <?= $i > 0 ? 'peek_remove_div' : '' ?>">
											
											<button type="button" class="peek_remove <?= $i > 0 ? '' : 'hidden' ?> btn btn-danger" style="position:absolute; right:-35px;">X</button>
                                                                    
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <?php echo lang('package_fare', 'package_fare'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $pslot->include_fare ?>" id="peak_package_price" name="peak_package_price[]" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <?php echo lang('package_type', 'package_type'); ?>
                                        <div class="controls">
                                            <?php
                                            $opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
                                            echo form_dropdown('peak_package_type[]', $opt, $pslot->package_type, 'id="peak_package_type" class="form-control select" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <?php echo lang('pre_distance_fare(1Km)', 'pre_distance_fare(1Km)'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $pslot->extra_fare ?>" id="peak_pre_distance_price" name="peak_pre_distance_price[]" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-xs-12">
                                        <?php echo lang('per_time_fare(1Minutes)', 'per_time_fare(1Minutes)'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $pslot->extra_time_fare ?>" id="peak_pre_time_price" name="peak_pre_time_price[]" class="form-control"/>
                                        </div>
                                    </div>
                                    
                                     <?php $pstime = explode(':', $pslot->start_time); $petime = explode(':', $pslot->end_time) ?>
                                    <div class="form-group  col-xs-6"> <?php echo lang('start_hours', 'start_hours'); ?>
                                      <div class="controls">
                                        <select name="peak_start_hours[]"  class="form-control">
                                          <option value="00" <?= $pstime[0] == '00' ? 'selected' : '' ?>>00</option>
                                        <option value="01" <?= $pstime[0] == '01' ? 'selected' : '' ?>>01</option>
                                        <option value="02" <?= $pstime[0] == '02' ? 'selected' : '' ?>>02</option>
                                        <option value="03" <?= $pstime[0] == '03' ? 'selected' : '' ?>>03</option>
                                        <option value="04" <?= $pstime[0] == '04' ? 'selected' : '' ?>>04</option>
                                        <option value="05" <?= $pstime[0] == '05' ? 'selected' : '' ?>>05</option>
                                        <option value="06" <?= $pstime[0] == '06' ? 'selected' : '' ?>>06</option>
                                        <option value="07" <?= $pstime[0] == '07' ? 'selected' : '' ?>>07</option>
                                        <option value="08" <?= $pstime[0] == '08' ? 'selected' : '' ?>>08</option>
                                        <option value="09" <?= $pstime[0] == '09' ? 'selected' : '' ?>>09</option>
                                        <option value="10" <?= $pstime[0] == '10' ? 'selected' : '' ?>>10</option>
                                        <option value="11" <?= $pstime[0] == '11' ? 'selected' : '' ?>>11</option>
                                        <option value="12" <?= $pstime[0] == '12' ? 'selected' : '' ?>>12</option>
                                        <option value="13" <?= $pstime[0] == '13' ? 'selected' : '' ?>>13</option>
                                        <option value="14" <?= $pstime[0] == '14' ? 'selected' : '' ?>>14</option>
                                        <option value="15" <?= $pstime[0] == '15' ? 'selected' : '' ?>>15</option>
                                        <option value="16" <?= $pstime[0] == '16' ? 'selected' : '' ?>>16</option>
                                        <option value="17" <?= $pstime[0] == '17' ? 'selected' : '' ?>>17</option>
                                        <option value="18" <?= $pstime[0] == '18' ? 'selected' : '' ?>>18</option>
                                        <option value="19" <?= $pstime[0] == '19' ? 'selected' : '' ?>>19</option>
                                        <option value="20" <?= $pstime[0] == '20' ? 'selected' : '' ?>>20</option>
                                        <option value="21" <?= $pstime[0] == '21' ? 'selected' : '' ?>>21</option>
                                        <option value="22" <?= $pstime[0] == '22' ? 'selected' : '' ?>>22</option>
                                        <option value="23" <?= $pstime[0] == '23' ? 'selected' : '' ?>>23</option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group  col-xs-6"> <?php echo lang('start_minutes', 'start_minutes'); ?>
                                      <div class="controls">
                                        <select name="peak_start_minutes[]" class="form-control">
                                          <option value="00" <?= $pstime[1] == '00' ? 'selected' : '' ?>>00</option>
                                        <option value="05" <?= $pstime[1] == '05' ? 'selected' : '' ?>>05</option>
                                        <option value="10" <?= $pstime[1] == '10' ? 'selected' : '' ?>>10</option>
                                        <option value="15" <?= $pstime[1] == '15' ? 'selected' : '' ?>>15</option>
                                        <option value="20" <?= $pstime[1] == '20' ? 'selected' : '' ?>>20</option>
                                        <option value="25" <?= $pstime[1] == '25' ? 'selected' : '' ?>>25</option>
                                        <option value="30" <?= $pstime[1] == '30' ? 'selected' : '' ?>>30</option>
                                        <option value="35" <?= $pstime[1] == '35' ? 'selected' : '' ?>>35</option>
                                        <option value="40" <?= $pstime[1] == '40' ? 'selected' : '' ?>>40</option>
                                        <option value="45" <?= $pstime[1] == '45' ? 'selected' : '' ?>>45</option>
                                        <option value="50" <?= $pstime[1] == '50' ? 'selected' : '' ?>>50</option>
                                        <option value="55" <?= $pstime[1] == '55' ? 'selected' : '' ?>>55</option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group  col-xs-6"> <?php echo lang('end_hours', 'end_hours'); ?>
                                      <div class="controls">
                                        <select name="peak_end_hours[]"  class="form-control">
                                          <option value="00" <?= $petime[0] == '00' ? 'selected' : '' ?>>00</option>
                                        <option value="01" <?= $petime[0] == '01' ? 'selected' : '' ?>>01</option>
                                        <option value="02" <?= $petime[0] == '02' ? 'selected' : '' ?>>02</option>
                                        <option value="03" <?= $petime[0] == '03' ? 'selected' : '' ?>>03</option>
                                        <option value="04" <?= $petime[0] == '04' ? 'selected' : '' ?>>04</option>
                                        <option value="05" <?= $petime[0] == '05' ? 'selected' : '' ?>>05</option>
                                        <option value="06" <?= $petime[0] == '06' ? 'selected' : '' ?>>06</option>
                                        <option value="07" <?= $petime[0] == '07' ? 'selected' : '' ?>>07</option>
                                        <option value="08" <?= $petime[0] == '08' ? 'selected' : '' ?>>08</option>
                                        <option value="09" <?= $petime[0] == '09' ? 'selected' : '' ?>>09</option>
                                        <option value="10" <?= $petime[0] == '10' ? 'selected' : '' ?>>10</option>
                                        <option value="11" <?= $petime[0] == '11' ? 'selected' : '' ?>>11</option>
                                        <option value="12" <?= $petime[0] == '12' ? 'selected' : '' ?>>12</option>
                                        <option value="13" <?= $petime[0] == '13' ? 'selected' : '' ?>>13</option>
                                        <option value="14" <?= $petime[0] == '14' ? 'selected' : '' ?>>14</option>
                                        <option value="15" <?= $petime[0] == '15' ? 'selected' : '' ?>>15</option>
                                        <option value="16" <?= $petime[0] == '16' ? 'selected' : '' ?>>16</option>
                                        <option value="17" <?= $petime[0] == '17' ? 'selected' : '' ?>>17</option>
                                        <option value="18" <?= $petime[0] == '18' ? 'selected' : '' ?>>18</option>
                                        <option value="19" <?= $petime[0] == '19' ? 'selected' : '' ?>>19</option>
                                        <option value="20" <?= $petime[0] == '20' ? 'selected' : '' ?>>20</option>
                                        <option value="21" <?= $petime[0] == '21' ? 'selected' : '' ?>>21</option>
                                        <option value="22" <?= $petime[0] == '22' ? 'selected' : '' ?>>22</option>
                                        <option value="23" <?= $petime[0] == '23' ? 'selected' : '' ?>>23</option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group  col-xs-6"> <?php echo lang('end_minutes', 'end_minutes'); ?>
                                      <div class="controls">
                                        <select name="peak_end_minutes[]" class="form-control">
                                          <option value="00" <?= $petime[1] == '00' ? 'selected' : '' ?>>00</option>
                                        <option value="05" <?= $petime[1] == '05' ? 'selected' : '' ?>>05</option>
                                        <option value="10" <?= $petime[1] == '10' ? 'selected' : '' ?>>10</option>
                                        <option value="15" <?= $petime[1] == '15' ? 'selected' : '' ?>>15</option>
                                        <option value="20" <?= $petime[1] == '20' ? 'selected' : '' ?>>20</option>
                                        <option value="25" <?= $petime[1] == '25' ? 'selected' : '' ?>>25</option>
                                        <option value="30" <?= $petime[1] == '30' ? 'selected' : '' ?>>30</option>
                                        <option value="35" <?= $petime[1] == '35' ? 'selected' : '' ?>>35</option>
                                        <option value="40" <?= $petime[1] == '40' ? 'selected' : '' ?>>40</option>
                                        <option value="45" <?= $petime[1] == '45' ? 'selected' : '' ?>>45</option>
                                        <option value="50" <?= $petime[1] == '50' ? 'selected' : '' ?>>50</option>
                                        <option value="55" <?= $petime[1] == '55' ? 'selected' : '' ?>>55</option>
                                        </select>
                                      </div>
                                    </div>
            
                                    
                                    </div> 
                                    
                                    <?php
										$i++;
										}
									}else{
									?>                              
                                 	<div class="row well">
                                    	<div class="form-group col-sm-6 col-xs-12">
                                        <?php echo lang('package_fare', 'package_fare'); ?>
                                        <div class="controls">
                                            <input type="text" id="peak_package_price" name="peak_package_price[]" class="form-control"/>
                                        </div>
                                    </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <?php echo lang('package_type', 'package_type'); ?>
                                            <div class="controls">
                                                <?php
                                                $opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
                                                echo form_dropdown('peak_package_type[]', $opt, '', 'id="peak_package_type" class="form-control select" style="width:100%;"');
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <?php echo lang('pre_distance_fare(1Km)', 'pre_distance_fare(1Km)'); ?>
                                            <div class="controls">
                                                <input type="text" id="peak_pre_distance_price" name="peak_pre_distance_price[]" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-xs-12">
                                            <?php echo lang('per_time_fare(1Minutes)', 'per_time_fare(1Minutes)'); ?>
                                            <div class="controls">
                                                <input type="text" id="peak_pre_time_price" name="peak_pre_time_price[]" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group  col-xs-6"> <?php echo lang('start_hours', 'start_hours'); ?>
                          <div class="controls">
                            <select name="peak_start_hours[]"  class="form-control">
                              <option value="00">00</option>
                              <option value="01">01</option>
                              <option value="02">02</option>
                              <option value="03">03</option>
                              <option value="04">04</option>
                              <option value="05">05</option>
                              <option value="06">06</option>
                              <option value="07">07</option>
                              <option value="08">08</option>
                              <option value="09">09</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                              <option value="13">13</option>
                              <option value="14">14</option>
                              <option value="15">15</option>
                              <option value="16">16</option>
                              <option value="17">17</option>
                              <option value="18">18</option>
                              <option value="19">19</option>
                              <option value="20">20</option>
                              <option value="21">21</option>
                              <option value="22">22</option>
                              <option value="23">23</option>
                            </select>
                          </div>
                        </div>
                                        <div class="form-group  col-xs-6"> <?php echo lang('start_minutes', 'start_minutes'); ?>
                                          <div class="controls">
                                            <select name="peak_start_minutes[]" class="form-control">
                                              <option value="00">00</option>
                                              <option value="05">05</option>
                                              <option value="10">10</option>
                                              <option value="15">15</option>
                                              <option value="20">20</option>
                                              <option value="25">25</option>
                                              <option value="30">30</option>
                                              <option value="35">35</option>
                                              <option value="40">40</option>
                                              <option value="45">45</option>
                                              <option value="50">50</option>
                                              <option value="55">55</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group  col-xs-6"> <?php echo lang('end_hours', 'end_hours'); ?>
                                          <div class="controls">
                                            <select name="peak_end_hours[]"  class="form-control">
                                              <option value="00">00</option>
                                              <option value="01">01</option>
                                              <option value="02">02</option>
                                              <option value="03">03</option>
                                              <option value="04">04</option>
                                              <option value="05">05</option>
                                              <option value="06">06</option>
                                              <option value="07">07</option>
                                              <option value="08">08</option>
                                              <option value="09">09</option>
                                              <option value="10">10</option>
                                              <option value="11">11</option>
                                              <option value="12">12</option>
                                              <option value="13">13</option>
                                              <option value="14">14</option>
                                              <option value="15">15</option>
                                              <option value="16">16</option>
                                              <option value="17">17</option>
                                              <option value="18">18</option>
                                              <option value="19">19</option>
                                              <option value="20">20</option>
                                              <option value="21">21</option>
                                              <option value="22">22</option>
                                              <option value="23">23</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group  col-xs-6"> <?php echo lang('end_minutes', 'end_minutes'); ?>
                                          <div class="controls">
                                            <select name="peak_end_minutes[]" class="form-control">
                                              <option value="00">00</option>
                                              <option value="05">05</option>
                                              <option value="10">10</option>
                                              <option value="15">15</option>
                                              <option value="20">20</option>
                                              <option value="25">25</option>
                                              <option value="30">30</option>
                                              <option value="35">35</option>
                                              <option value="40">40</option>
                                              <option value="45">45</option>
                                              <option value="50">50</option>
                                              <option value="55">55</option>
                                            </select>
                                          </div>
                                        </div>
                                    </div>
                                 	<?php
									}
									?>
                                </div>
                            </div>
                        
                        <div class="col-md-4">   
                        <h2 class="box_he_de">Night Fare
                        <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_night">OFF</label>
                <input type="radio" value="1" id="switch_right_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_night">ON</label>
              </div>
              <button type="button" id="night_fare" class="btn  btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
              <?= lang("add"); ?>
              </button>
                        </h2> 	
                            <div class="col-md-12 night">
                            
                            	<?php
				if($result->is_night == 1 && !empty($night_slot)){
					$i=0;
					foreach($night_slot as $nslot){
				?>
               <div class="row well <?= $i > 0 ? 'night_remove_div' : '' ?>">
<button type="button" class="night_remove <?= $i > 0 ? '' : 'hidden' ?> btn btn-danger" style="position:absolute; right:-35px;">X</button>
                                                                
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('package_fare', 'package_fare'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $nslot->include_fare ?>" id="night_package_price" name="night_package_price[]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('package_type', 'package_type'); ?>
                                    <div class="controls">
                                        <?php
                                        $opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
                                        echo form_dropdown('night_package_type[]', $opt, $nslot->package_type, 'id="night_package_type" class="form-control select" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('pre_distance_fare(1Km)', 'pre_distance_fare(1Km)'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $nslot->extra_fare ?>" id="night_pre_distance_price" name="night_pre_distance_price[]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('per_time_fare(1Minutes)', 'per_time_fare(1Minutes)'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $nslot->extra_time_fare ?>" id="night_pre_time_price" name="night_pre_time_price[]" class="form-control"/>
                                    </div>
                                </div>
                                
                                
                <?php $nstime = explode(':', $nslot->start_time); $netime = explode(':', $nslot->end_time) ?>
              <div class="form-group  col-xs-6"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="night_start_hours[]" id="night_start_hours" class="form-control">
                     <option value="00" <?= $nstime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $nstime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $nstime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $nstime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $nstime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $nstime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $nstime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $nstime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $nstime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $nstime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $nstime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $nstime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $nstime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $nstime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $nstime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $nstime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $nstime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $nstime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $nstime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $nstime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $nstime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $nstime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $nstime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $nstime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('start_minutes', 'start_minutes'); ?>
                <div class="controls">
                  <select name="night_start_minutes[]" id="night_start_minutes" class="form-control">
                    <option value="00" <?= $nstime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $nstime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $nstime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $nstime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $nstime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $nstime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $nstime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $nstime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $nstime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $nstime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $nstime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $nstime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('end_hours', 'end_hours'); ?>
                <div class="controls">
                  <select name="night_end_hours[]" id="night_end_hours" class="form-control">
                    <option value="00" <?= $netime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $netime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $netime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $netime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $netime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $netime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $netime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $netime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $netime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $netime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $netime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $netime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $netime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $netime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $netime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $netime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $netime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $netime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $netime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $netime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $netime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $netime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $netime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $netime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('end_minutes', 'end_minutes'); ?>
                <div class="controls">
                  <select name="night_end_minutes[]" id="night_end_minutes" class="form-control">
                    <option value="00" <?= $netime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $netime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $netime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $netime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $netime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $netime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $netime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $netime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $netime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $netime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $netime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $netime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
                                
                                </div> 
                                <?php
									$i++;
									}
								}else{
								?>
                                <div class="row well">
                                                                
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('package_fare', 'package_fare'); ?>
                                    <div class="controls">
                                        <input type="text" id="night_package_price" name="night_package_price[]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('package_type', 'package_type'); ?>
                                    <div class="controls">
                                        <?php
                                        $opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
                                        echo form_dropdown('night_package_type[]', $opt, '', 'id="night_package_type" class="form-control select" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('pre_distance_fare(1Km)', 'pre_distance_fare(1Km)'); ?>
                                    <div class="controls">
                                        <input type="text" id="night_pre_distance_price" name="night_pre_distance_price[]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
                                    <?php echo lang('per_time_fare(1Minutes)', 'per_time_fare(1Minutes)'); ?>
                                    <div class="controls">
                                        <input type="text" id="night_pre_time_price" name="night_pre_time_price[]" class="form-control"/>
                                    </div>
                                </div>
                                
                                 <?php $nstime = explode(':', $nslot->start_time); $netime = explode(':', $nslot->end_time) ?>
              <div class="form-group  col-xs-6"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="night_start_hours[]" id="night_start_hours" class="form-control">
                     <option value="00" <?= $nstime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $nstime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $nstime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $nstime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $nstime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $nstime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $nstime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $nstime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $nstime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $nstime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $nstime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $nstime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $nstime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $nstime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $nstime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $nstime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $nstime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $nstime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $nstime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $nstime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $nstime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $nstime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $nstime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $nstime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('start_minutes', 'start_minutes'); ?>
                <div class="controls">
                  <select name="night_start_minutes[]" id="night_start_minutes" class="form-control">
                    <option value="00" <?= $nstime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $nstime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $nstime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $nstime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $nstime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $nstime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $nstime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $nstime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $nstime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $nstime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $nstime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $nstime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('end_hours', 'end_hours'); ?>
                <div class="controls">
                  <select name="night_end_hours[]" id="night_end_hours" class="form-control">
                    <option value="00" <?= $netime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $netime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $netime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $netime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $netime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $netime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $netime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $netime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $netime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $netime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $netime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $netime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $netime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $netime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $netime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $netime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $netime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $netime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $netime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $netime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $netime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $netime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $netime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $netime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('end_minutes', 'end_minutes'); ?>
                <div class="controls">
                  <select name="night_end_minutes[]" id="night_end_minutes" class="form-control">
                    <option value="00" <?= $netime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $netime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $netime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $netime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $netime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $netime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $netime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $netime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $netime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $netime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $netime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $netime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
                                
                                </div>   
                                <?php
								}
								?>                             
                               
                             
                            </div>
                        </div>
                        
                        <div class="col-md-4">   
                        <h2 class="box_he_de">Base Fare</h2> 	
                            <div class="col-md-12">
                            	<div class="row well">
                                <input type="hidden" value="1" name="is_base">
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('package_fare', 'package_fare'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->package_price ?>"  id="package_price" name="package_price" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('package_type', 'package_type'); ?>
                                    <div class="controls">
                                        <?php
										$opt = array(0 => lang('all'), 1 => lang('distance'), 2 => lang('hour'));
										echo form_dropdown('option_type', $opt, $result->option_type, 'id="option_type" class="form-control select" style="width:100%;"');
										?>
                                    </div>
                                </div>
                                
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('Package Distance', 'Package Distance'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->package_distance ?>"  id="package_distance" placeholder="Km" name="package_distance" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('Package Duration ', 'Package Duration(hours)'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->package_time ?>"  id="package_time" placeholder="hours" name="package_time" class="form-control time"/>
                                    </div>
                                </div>
                                
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('pre_distance_fare(1Km)', 'pre_distance_fare(1Km)'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->per_distance_price ?>"  id="per_distance_price" name="per_distance_price" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-12">
									<?php echo lang('per_time_fare(1Minutes)', 'per_time_fare(1Minutes)'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->per_time_price ?>" id="per_time_price" name="per_time_price" class="form-control"/>
                                    </div>
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





</div>

<script>
$('#peek_fare').click(function(){
	is_peak = $("input[name='is_peak']:checked").val();
	if(is_peak == 1){
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('locations/getrentalPeek')?>',
			dataType: "html",
			cache: false,
			success: function (res) {
				$(".peek").prepend(res);
			}
		})
	}else{
		alert('Please select peek value');
	}
});
$(document).on('click', '.peek_remove', function(){
	$(this).parent().remove();
})

$('#night_fare').click(function(){
	is_night = $("input[name='is_night']:checked").val();
	if(is_night == 1){
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('locations/getrentalNight')?>',
			dataType: "html",
			cache: false,
			success: function (res) {
				$(".night").prepend(res);
			}
		})
	}else{
		alert('Please select night value');
	}
});
$(document).on('click', '.night_remove', function(){
	$(this).parent().remove();
})

</script> 
<script>
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