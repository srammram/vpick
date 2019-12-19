<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="box">
  <div class="box-content">
    <div class="row">
      <div class="col-lg-12">
        <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("locations/edit_daily/".$id, $attrib);
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
                                        echo form_dropdown('taxi_type', $t, $result->taxi_type, 'class="form-control select"  id="taxi_type" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang('continent', 'continent'); ?>
                <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, $result->continent_id, 'class="form-control select-local-continent select"  id="continent_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("country", "country"); ?>
                <?php
				$lcou[''] = 'Select Country';
										foreach ($countrys as $country) {
											$lcou[$country->id] = $country->name;
										}
                                        echo form_dropdown('country_id', $lcou, $result->country_id, 'class="form-control select-local-country select" id="country_id"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("zone", "zone"); ?>
                <?php
				$lz[''] = 'Select Zone';
										foreach ($zones as $zone) {
											$lz[$zone->id] = $zone->name;
										}
                                        echo form_dropdown('zone_id', $lz, $result->zone_id, 'class="form-control select-local-zone select" id="zone_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("state", "state"); ?>
                <?php
				$ls[''] = 'Select State';
										foreach ($states as $state) {
											$ls[$state->id] = $state->name;
										}
                                        echo form_dropdown('state_id', $ls, $result->state_id, 'class="form-control select-local-state select" id="state_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("city", "city"); ?>
                <?php
				$lc[''] = 'Select City';
										foreach ($citys as $city) {
											$lc[$city->id] = $city->name;
										}
                                        echo form_dropdown('city_id', $lc, $result->city_id, 'class="form-control select-local-city select" id="city_id" '); ?>
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
            <div class="col-md-12 box_he_de"><b><?= lang('peek_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_peak">OFF</label>
                <input type="radio" value="1" id="switch_right_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_peak">ON</label>
              </div>
              <button type="button" id="peek_fare" class="btn btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
              <?= lang("add"); ?>
              </button>
            </div>
            <div class="col-md-12" id="field">
             <?php
			 if($result->is_peak == 1 && !empty($peek_slot)){
				 $i=0;
				foreach($peek_slot as $pslot){
			 ?>
              <div id="field<?= $i ?>">
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                  <div class="controls">
                    <select name="peak_price_type[]"  class="form-control">
                      <option value="0" <?= $pslot->price_type == 0 ? 'selected' : '' ?>>Fixed</option>
                      <option value="1" <?= $pslot->price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                  <div class="controls">
                    <input type="text"  name="peak_min_distance_price[]" value="<?= $pslot->min_fare ?>" class="form-control"/>
                  </div>
                </div>
                <input type="hidden" name="peak_per_distance[]" value="1" class="form-control"/>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                  <div class="controls">
                    <input type="text" name="peak_price_value[]" maxlength="3" value="<?= $pslot->per_fare ?>"  class="form-control"/>
                  </div>
                </div>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="peak_waiting_price[]" value="<?= $pslot->waiting_price ?>" class="form-control"/>
                  </div>
                </div>
                <?php $pstime = explode(':', $pslot->start_time); $petime = explode(':', $pslot->end_time) ?>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_hours', 'start_hours'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_minutes', 'start_minutes'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_hours', 'end_hours'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_minutes', 'end_minutes'); ?>
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
                <?php
				if($i > 0){
				?>
                <div class="col-sm-3 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div>
                <?php
				}
				?>
                <div class="clearfix"></div>
              </div>
              <?php
			  	$i++;
				}
			 }else{
			  ?>
              <div id="field0">
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                  <div class="controls">
                    <select name="peak_price_type[]"  class="form-control">
                      <option value="0">Fixed</option>
                      <option value="1">Percentage</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                  <div class="controls">
                    <input type="text"  name="peak_min_distance_price[]" class="form-control"/>
                  </div>
                </div>
                <input type="hidden" name="peak_per_distance[]" value="1" class="form-control"/>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                  <div class="controls">
                    <input type="text" name="peak_price_value[]" maxlength="3" class="form-control"/>
                  </div>
                </div>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="peak_waiting_price[]"  class="form-control"/>
                  </div>
                </div>
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_hours', 'start_hours'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_minutes', 'start_minutes'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_hours', 'end_hours'); ?>
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
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_minutes', 'end_minutes'); ?>
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
          <div class="col-md-12">
            <div class="col-md-12 box_he_de"><b><?= lang('night_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_night">OFF</label>
                <input type="radio" value="1" id="switch_right_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_night">ON</label>
              </div>
            </div>
           
            <div class="col-md-12">
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls">
                  <select name="night_price_type" id="night_price_type" class="form-control">
                    <option value="0" <?= !empty($night_slot) && $night_slot[0]->price_type == 0 ? 'selected' : '' ?>>Fixed</option>
                    <option value="1"  <?= !empty($night_slot) && $night_slot[0]->price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                  </select>
                </div>
              </div>
             
             
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="night_min_distance_price" name="night_min_distance_price" value="<?= !empty($night_slot) ? $night_slot[0]->min_fare : '' ?>" class="form-control"/>
                </div>
              </div>
              <input type="hidden" id="night_per_distance" name="night_per_distance" value="1" class="form-control"/>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="night_price_value" name="night_price_value" maxlength="3" value="<?= !empty($night_slot) ? $night_slot[0]->per_fare : '' ?>"  class="form-control"/>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="night_waiting_price" value="<?= $night_slot[0]->waiting_price ?>" class="form-control"/>
                  </div>
                </div>
              
              <?php $stime = explode(':', $night_slot[0]->start_time); $etime = explode(':', $night_slot[0]->end_time) ?>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="night_start_hours" id="night_start_hours" class="form-control">
                    <option value="00" <?= $stime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $stime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $stime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $stime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $stime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $stime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $stime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $stime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $stime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $stime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $stime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $stime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $stime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $stime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $stime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $stime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $stime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $stime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $stime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $stime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $stime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $stime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $stime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $stime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_minutes', 'start_minutes'); ?>
                <div class="controls">
                  <select name="night_start_minutes" id="night_start_minutes" class="form-control">
                    <option value="00" <?= $stime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $stime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $stime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $stime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $stime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $stime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $stime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $stime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $stime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $stime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $stime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $stime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_hours', 'end_hours'); ?>
                <div class="controls">
                  <select name="night_end_hours" id="night_end_hours" class="form-control">
                    <option value="00" <?= $etime[0] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="01" <?= $etime[0] == '01' ? 'selected' : '' ?>>01</option>
                    <option value="02" <?= $etime[0] == '02' ? 'selected' : '' ?>>02</option>
                    <option value="03" <?= $etime[0] == '03' ? 'selected' : '' ?>>03</option>
                    <option value="04" <?= $etime[0] == '04' ? 'selected' : '' ?>>04</option>
                    <option value="05" <?= $etime[0] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="06" <?= $etime[0] == '06' ? 'selected' : '' ?>>06</option>
                    <option value="07" <?= $etime[0] == '07' ? 'selected' : '' ?>>07</option>
                    <option value="08" <?= $etime[0] == '08' ? 'selected' : '' ?>>08</option>
                    <option value="09" <?= $etime[0] == '09' ? 'selected' : '' ?>>09</option>
                    <option value="10" <?= $etime[0] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $etime[0] == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $etime[0] == '12' ? 'selected' : '' ?>>12</option>
                    <option value="13" <?= $etime[0] == '13' ? 'selected' : '' ?>>13</option>
                    <option value="14" <?= $etime[0] == '14' ? 'selected' : '' ?>>14</option>
                    <option value="15" <?= $etime[0] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="16" <?= $etime[0] == '16' ? 'selected' : '' ?>>16</option>
                    <option value="17" <?= $etime[0] == '17' ? 'selected' : '' ?>>17</option>
                    <option value="18" <?= $etime[0] == '18' ? 'selected' : '' ?>>18</option>
                    <option value="19" <?= $etime[0] == '19' ? 'selected' : '' ?>>19</option>
                    <option value="20" <?= $etime[0] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="21" <?= $etime[0] == '21' ? 'selected' : '' ?>>21</option>
                    <option value="22" <?= $etime[0] == '22' ? 'selected' : '' ?>>22</option>
                    <option value="23" <?= $etime[0] == '23' ? 'selected' : '' ?>>23</option>
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_minutes', 'end_minutes'); ?>
                <div class="controls">
                  <select name="night_end_minutes" id="night_end_minutes" class="form-control">
                    <option value="00" <?= $etime[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $etime[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $etime[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $etime[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $etime[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $etime[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $etime[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $etime[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $etime[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $etime[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $etime[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $etime[1] == '55' ? 'selected' : '' ?>>55</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="col-md-12 box_he_de"><b><?= lang('base_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_base" class="skip" name="is_base" <?php echo (@$result->is_base==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_base">OFF</label>
                <input type="radio" value="1" id="switch_right_is_base" class="skip" name="is_base" <?php echo (@$result->is_base==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_base">ON</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('Minimum Distance', 'Minimum Distance'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance" name="base_min_distance" value="<?= $result->base_min_distance ?>" class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance_price" name="base_min_distance_price" value="<?= $result->base_min_distance_price ?>"  class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls">
                  <select name="base_price_type" id="base_price_type" class="form-control">
                    <option value="0" <?= $result->base_price_type == 0 ? 'selected' : '' ?> >Fixed</option>
                    <option value="1" <?= $result->base_price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                  </select>
                </div>
              </div>
              <input type="hidden" id="base_per_distance" name="base_per_distance" value="1" class="form-control"/>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="base_price_value" name="base_price_value" maxlength="3" value="<?= $result->base_price_value ?>"  class="form-control"/>
                </div>
              </div>
             
               <?php $waiting = explode(':', $result->base_waiting_minute);  ?>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('wait_dur_min', 'Waiting Duration (minutes)'); ?>
              <div class="controls">
                <select name="base_waiting_minute"  id="base_waiting_minute" class="form-control">
                    <option value="00" <?= $waiting[1] == '00' ? 'selected' : '' ?>>00</option>
                    <option value="05" <?= $waiting[1] == '05' ? 'selected' : '' ?>>05</option>
                    <option value="10" <?= $waiting[1] == '10' ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $waiting[1] == '15' ? 'selected' : '' ?>>15</option>
                    <option value="20" <?= $waiting[1] == '20' ? 'selected' : '' ?>>20</option>
                    <option value="25" <?= $waiting[1] == '25' ? 'selected' : '' ?>>25</option>
                    <option value="30" <?= $waiting[1] == '30' ? 'selected' : '' ?>>30</option>
                    <option value="35" <?= $waiting[1] == '35' ? 'selected' : '' ?>>35</option>
                    <option value="40" <?= $waiting[1] == '40' ? 'selected' : '' ?>>40</option>
                    <option value="45" <?= $waiting[1] == '45' ? 'selected' : '' ?>>45</option>
                    <option value="50" <?= $waiting[1] == '50' ? 'selected' : '' ?>>50</option>
                    <option value="55" <?= $waiting[1] == '55' ? 'selected' : '' ?>>55</option>
                </select>
              </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('Waiting Charges', 'Waiting Charges'); ?>
                <div class="controls">
                  <input type="text" id="base_waiting_price" name="base_waiting_price" value="<?= $result->base_waiting_price ?>"  class="form-control"/>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="labour_charge" name="labour_charge" value="<?= $result->labour_charge ?>"   class="form-control"/>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        <div class="col-sm-12 last_sa_se"><?php echo form_submit('edit_daily', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
        <?php echo form_close(); ?> </div>
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
<script>
$(document).ready(function () {
    //@naresh action dynamic childs
    var next = 0;
    $("#peek_fare").click(function(e){
        e.preventDefault();
        var addto = "#field" + next;
        var addRemove = "#field" + (next);
        next = next + 1;
	   var newIn = ' <div id="field'+ next +'" name="field'+ next +'"> <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>  <div class="controls"><select name="peak_price_type[]"  class="form-control">  <option value="0">Fixed</option> <option value="1">Percentage</option>  </select>	 </div> </div>   <div class="form-group col-sm-3 col-xs-12">  <?php echo lang('minimum_fare', 'minimum_fare'); ?>  <div class="controls"> <input type="text"  name="peak_min_distance_price[]" class="form-control"/>  </div> </div> <input type="hidden" name="peak_per_distance[]" value="1" class="form-control"/><div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>  <div class="controls">	  <input type="text" name="peak_price_value[]" maxlength="3" class="form-control"/></div> </div><div class="form-group col-sm-3 col-xs-12"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?><div class="controls"> <input type="text" name="peak_waiting_price[]" class="form-control"/></div></div><div class="form-group col-sm-3 col-xs-12">  <?php echo lang('start_hours', 'start_hours'); ?>  <div class="controls">  <select name="peak_start_hours[]"  class="form-control"> <option value="00">00</option> <option value="01">01</option> <option value="02">02</option> <option value="03">03</option> <option value="04">04</option> <option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>  </select>	</div> </div> <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_minutes', 'start_minutes'); ?> <div class="controls">  <select name="peak_start_minutes[]" class="form-control">  <option value="00">00</option><option value="05">05</option><option value="10">10</option><option value="15">15</option><option value="20">20</option><option value="25">25</option><option value="30">30</option><option value="35">35</option><option value="40">40</option><option value="45">45</option><option value="50">50</option><option value="55">55</option> </select>	 </div></div><div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_hours', 'end_hours'); ?> <div class="controls">	  <select name="peak_end_hours[]"  class="form-control"> <option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option>  <option value="23">23</option></select>	 </div> </div> <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('end_minutes', 'end_minutes'); ?> <div class="controls"> <select name="peak_end_minutes[]" class="form-control"><option value="00">00</option><option value="05">05</option><option value="10">10</option><option value="15">15</option><option value="20">20</option><option value="25">25</option><option value="30">30</option><option value="35">35</option><option value="40">40</option><option value="45">45</option><option value="50">50</option><option value="55">55</option></select>	</div> </div></div>';
        var newInput = $(newIn);
        var removeBtn = '<div class="col-sm-3 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div></div><div class="clearfix"></div><div id="field">';
        var removeButton = $(removeBtn);
        $(addto).before(newInput);
        $(addRemove).before(removeButton);
        $("#field" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#field" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });

});
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


