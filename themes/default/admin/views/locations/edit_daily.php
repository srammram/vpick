<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="box">
  <div class="box-content">
    <div class="row">
      <div class="col-lg-12">
        <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from1','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
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
                <?= lang('tons', 'tons'); ?>
                <?php
				$ton[''] = 'Select Tons';
                                        foreach ($tons['type'] as $trow) {
                                            $ton[$trow->tons] = $trow->tons;
                                        }
				
				echo form_dropdown('tons', $ton, $result->tons, 'class="form-control select"  id="tons" required="required"'); ?>
			</div>
			<div class="form-group col-md-3 col-xs-12">
										<?= lang('accessing', 'accessing'); ?>
                                        <?php echo form_input('shift_name', $result->shift_name, 'class="form-control " id="shift_name" readonly required="required"'); ?>
                                    </div>
									<div class="form-group col-md-3 col-xs-12">
										<?= lang('commision_percentage', 'commision_percentage'); ?>
                                        <?php echo form_input('commision_percentage', $result->commision_percentage, 'class="form-control " id="commision_percentage" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
									<div class="form-group col-md-3 col-xs-12">
										<?= lang('load_status', 'load_status'); ?>
                                        <?php
										 	
                                        $f['0'] = 'Full Load';
                                        $f['1'] = 'Single Load';
                                       
                                        
                                        echo form_dropdown('load_status', $f, $result->load_status, 'class="form-control select"  id="load_status" required="required"'); ?>
                                    </div>
			  
                        </div>
          <div class="col-md-12">
            <h2 class="box_he_de"><?= lang('location_details') ?></h2>
            <div class="col-md-12">
              
              
              
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
          
          <div class="col-md-4">
            <div class="col-md-12 box_he_de"><b><?= lang('peek_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_peak">OFF</label>
                <input type="radio" value="1" id="switch_right_is_peak" class="skip" name="is_peak" <?php echo (@$result->is_peak==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_peak">ON</label>
              </div>
              <button type="button" id="peek_fare" class="btn  btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
              <?= lang("add"); ?>
              </button>
            </div>
            <div class="col-md-12 peek">
            	
                <?php
				if($result->is_peak == 1 && !empty($peek_slot)){
					$i=0;
					$peak_count = count($peek_slot);
					foreach($peek_slot as $pslot){
				?>                
            	<div class="row well <?= $i > 0 ? 'peek_remove_div' : '' ?>">
                
                <button type="button" class="peek_remove <?= $i > 0 ? '' : 'hidden' ?> btn btn-danger" style="position:absolute; right:-35px;">X</button>
              	<input type="hidden" name="peak_per_distance[]" value="1" class="form-control"/>
                <div class="form-group col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
                  <div class="controls" data-count="<?= $i ?>">
                    <select name="peak_price_type[]"  class="form-control peak_price_type">
                      <option value="0" <?= $pslot->price_type == 0 ? 'selected' : '' ?>>Fixed</option>
                      <option value="1" <?= $pslot->price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                    </select>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
                  <div class="controls">
                    <input type="text" id="peak_percentage_value<?= $i ?>"  value="<?= $pslot->min_fare ?>" name="peak_percentage_value[]" readonly class="form-control peak_percentage_value"/>
                  </div>
                </div>
                
                <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                  <div class="controls">
                    <input type="text" id="peak_min_distance_price<?= $i ?>"  name="peak_min_distance_price[]" value="<?= $pslot->include_fare ?>"  class="form-control"/>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                  <div class="controls">
                    <input type="text" id="peek_per_distance_price<?= $i ?>" name="peek_per_distance_price[]"  value="<?= $pslot->extra_fare ?>" class="form-control"/>
                  </div>
                </div>
				<div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="peak_labour_charge<?= $i ?>" value="<?= $pslot->labour_charge ?>" name="peak_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('peak_work_per_load[]', $pslot->work_per_load, 'class="form-control" id="peak_work_per_load".$i."" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
                <div class="form-group  col-xs-6"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="peak_waiting_price[]" value="<?= $pslot->waiting_price ?>" class="form-control"/>
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
					$peak_count = 1;
				?>
                <div class="row well">
              	<input type="hidden" name="peak_per_distance[]" value="1" class="form-control"/>
                <div class="form-group col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
                  <div class="controls" data-count="0">
                    <select name="peak_price_type[]"  class="form-control peak_price_type">
                      <option value="0">Fixed</option>
                      <option value="1">Percentage</option>
                    </select>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
                  <div class="controls" data-count="0">
                    <input type="text" id="peak_percentage_value0"   name="peak_percentage_value[]" readonly class="form-control peak_percentage_value"/>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                  <div class="controls">
                    <input type="text" id="peak_min_distance_price0"  name="peak_min_distance_price[]"  class="form-control"/>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                  <div class="controls">
                    <input type="text" id="peek_per_distance_price0" name="peek_per_distance_price[]"  class="form-control"/>
                  </div>
                </div>
				<div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="peak_labour_charge0" name="peak_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('peak_work_per_load[]', '', 'class="form-control" id="peak_work_per_load0" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
                <div class="form-group  col-xs-6"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="peak_waiting_price[]" class="form-control"/>
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
            <div class="col-md-12 box_he_de"><b><?= lang('night_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_night">OFF</label>
                <input type="radio" value="1" id="switch_right_is_night" class="skip" name="is_night" <?php echo (@$result->is_night==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_night">ON</label>
              </div>
              <button type="button" id="night_fare" class="btn  btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
              <?= lang("add"); ?>
              </button>
            </div>
            <div class="col-md-12 night">
            	<?php
				if($result->is_night == 1 && !empty($night_slot)){
					$i=0;
					$night_count = count($night_slot);
					foreach($night_slot as $nslot){
				?>
               <div class="row well <?= $i > 0 ? 'night_remove_div' : '' ?>">
<button type="button" class="night_remove <?= $i > 0 ? '' : 'hidden' ?> btn btn-danger" style="position:absolute; right:-35px;">X</button>
               <input type="hidden" id="night_per_distance" name="night_per_distance[]" value="1" class="form-control"/>
              <div class="form-group  col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls" data-count="<?= $i ?>">
                  <select name="night_price_type[]" id="night_price_type" class="form-control night_price_type">
                    <option value="0" <?= $nslot->price_type == 0 ? 'selected' : '' ?>>Fixed</option>
                      <option value="1" <?= $nslot->price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
                  <div class="controls">
                    <input type="text" id="night_percentage_value<?= $i ?>" value="<?= $nslot->min_fare ?>"  name="night_percentage_value[]" readonly class="form-control night_percentage_value"/>
                  </div>
                </div>
              <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="night_min_distance_price<?= $i ?>" name="night_min_distance_price[]" value="<?= $nslot->include_fare ?>" class="form-control"/>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="night_per_distance_price<?= $i ?>" name="night_per_distance_price[]"  value="<?= $nslot->extra_fare ?>" class="form-control"/>
                </div>
              </div>
			  <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="night_labour_charge<?= $i ?>" value="<?= $nslot->labour_charge ?>" name="night_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('night_work_per_load[]', $nslot->work_per_load, 'class="form-control" id="night_work_per_load".$i."" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
              <div class="form-group  col-xs-6"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="night_waiting_price[]" value="<?= $nslot->waiting_price ?>"  class="form-control"/>
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
					$night_count = 1;
			  ?>
              <div class="row well">
               <input type="hidden" id="night_per_distance" name="night_per_distance[]" value="1" class="form-control"/>
              <div class="form-group  col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls" data-count="0">
                  <select name="night_price_type[]" id="night_price_type" class="form-control night_price_type">
                    <option value="0">Fixed</option>
                    <option value="1">Percentage</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
                  <div class="controls">
                    <input type="text" id="night_percentage_value0"  name="night_percentage_value[]" readonly class="form-control night_percentage_value"/>
                  </div>
                </div>
              <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="night_min_distance_price0" name="night_min_distance_price[]" class="form-control"/>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="night_per_distance_price0" name="night_per_distance_price[]" class="form-control"/>
                </div>
              </div>
			  <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="night_labour_charge0" name="night_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('night_work_per_load[]', '', 'class="form-control" id="night_work_per_load0" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
              <div class="form-group  col-xs-6"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="night_waiting_price[]" class="form-control"/>
                  </div>
                </div>
              <div class="form-group  col-xs-6"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="night_start_hours[]" id="night_start_hours" class="form-control">
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
                  <select name="night_start_minutes[]" id="night_start_minutes" class="form-control">
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
                  <select name="night_end_hours[]" id="night_end_hours" class="form-control">
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
                  <select name="night_end_minutes[]" id="night_end_minutes" class="form-control">
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
            <div class="col-md-12 box_he_de"><b><?= lang('base_fare') ?></b>
              <input type="hidden" name="is_base" value="1">
            </div>
            <div class="col-md-12">
            	<div class="row well">
            	<input type="hidden" id="base_per_distance" name="base_per_distance" value="1" class="form-control"/>
              <div class="form-group  col-xs-6"> <?php echo lang('Minimum Distance', 'Minimum Distance'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance" name="base_min_distance" value="<?= $result->base_min_distance ?>" class="form-control"/>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance_price" value="<?= $result->base_min_distance_price ?>" name="base_min_distance_price" class="form-control"/>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls">
                  <select name="base_price_type" id="base_price_type" class="form-control">
                    <option value="0" <?= $result->base_price_type == 0 ? 'selected' : '' ?> >Fixed</option>
                    <option value="1" <?= $result->base_price_type == 1 ? 'selected' : '' ?>>Percentage</option>
                  </select>
                </div>
              </div>
              <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
                  <div class="controls">
                    <input type="text" id="base_percentage_value" value="<?= $result->base_price_value ?>"  name="base_percentage_value" <?= $result->base_price_type == 0 ? 'readonly' : '' ?>  class="form-control base_percentage_value"/>
                  </div>
                </div>
              <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="base_per_distance_price" value="<?= $result->base_per_distance_price ?>" name="base_per_distance_price"  class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="base_labour_charge" name="base_labour_charge" value="<?= $result->labour_charge ?>" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('base_work_per_load', $result->work_per_load, 'class="form-control" id="base_work_per_load" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
			  
              <div class="form-group  col-xs-6"> <?php echo lang('Waiting (minutes)', 'Waiting Duration (minutes)'); ?>
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
              <div class="form-group  col-xs-6"> <?php echo lang('Waiting Charges', 'Waiting Charges'); ?>
                <div class="controls">
                  <input type="text" id="base_waiting_price" value="<?= $result->base_waiting_price ?>" name="base_waiting_price" class="form-control"/>
                </div>
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
var peek_fare_count = <?= $peak_count ?>;
$('#peek_fare').click(function(){
	is_peak = $("input[name='is_peak']:checked").val();
	if(is_peak == 1){
		peek_fare_count++;
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('locations/getPeek')?>',
			dataType: "html",
			cache: false,
			data: {peek_fare_count: peek_fare_count},
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

$(document).on('change', '.peak_price_type', function(){
	var price_type = $(this).val();
	var id = $(this).parent().attr('data-count');
	if(price_type == 1){
		$('#peak_percentage_value'+id).attr('readonly', false); 
		$('#peak_min_distance_price'+id).attr('readonly', true); 
		$('#peek_per_distance_price'+id).attr('readonly', true); 
		$('#peak_percentage_value'+id).val('');
		$('#peak_min_distance_price'+id).val('');
		$('#peek_per_distance_price'+id).val('');
		
	}else{
		$('#peak_percentage_value'+id).attr('readonly', true); 
		$('#peak_min_distance_price'+id).attr('readonly', false); 
		$('#peek_per_distance_price'+id).attr('readonly', false); 
		$('#peak_percentage_value'+id).val('');
		$('#peak_min_distance_price'+id).val('');
		$('#peek_per_distance_price'+id).val('');
	}
});

$(document).on('change', '.peak_percentage_value', function(){
	var id = $(this).attr('id').slice(-1);
	var base_min_distance_price = $('#base_min_distance_price').val();
	var base_per_distance_price = $('#base_per_distance_price').val();
	var peak_percentage_value = $(this).val();
	if(base_min_distance_price != '' && base_per_distance_price != ''){
		var peak_min_distance_price = base_min_distance_price * peak_percentage_value / 100;
		var peek_per_distance_price = base_per_distance_price * peak_percentage_value / 100;
		$('#peak_min_distance_price'+id).val(peak_min_distance_price);
		$('#peek_per_distance_price'+id).val(peek_per_distance_price);
	}else{
		alert('Please select min and per rate amount');
		$('#peak_percentage_value'+id).val(''); 
	}
});

var night_fare_count = <?= $night_count ?>;
$('#night_fare').click(function(){
	is_night = $("input[name='is_night']:checked").val();
	if(is_night == 1){
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('locations/getNight')?>',
			dataType: "html",
			cache: false,
			data: {night_fare_count: night_fare_count},
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

$(document).on('change', '.night_price_type', function(){
	var price_type = $(this).val();
	var id = $(this).parent().attr('data-count');
	if(price_type == 1){
		$('#night_percentage_value'+id).attr('readonly', false); 
		$('#night_min_distance_price'+id).attr('readonly', true); 
		$('#night_per_distance_price'+id).attr('readonly', true); 
		$('#night_percentage_value'+id).val('');
		$('#night_min_distance_price'+id).val('');
		$('#night_per_distance_price'+id).val('');
		
	}else{
		$('#night_percentage_value'+id).attr('readonly', true); 
		$('#night_min_distance_price'+id).attr('readonly', false); 
		$('#night_per_distance_price'+id).attr('readonly', false); 
		$('#night_percentage_value'+id).val('');
		$('#night_min_distance_price'+id).val('');
		$('#night_per_distance_price'+id).val('');
	}
});

$(document).on('change', '.night_percentage_value', function(){
	var id = $(this).attr('id').slice(-1);
	var base_min_distance_price = $('#base_min_distance_price').val();
	var base_per_distance_price = $('#base_per_distance_price').val();
	var night_percentage_value = $(this).val();
	if(base_min_distance_price != '' && base_per_distance_price != ''){
		var night_min_distance_price = base_min_distance_price * night_percentage_value / 100;
		var night_per_distance_price = base_per_distance_price * night_percentage_value / 100;
		$('#night_min_distance_price'+id).val(night_min_distance_price);
		$('#night_per_distance_price'+id).val(night_per_distance_price);
	}else{
		alert('Please select min and per rate amount');
		$('#night_percentage_value'+id).val(''); 
	}
});

$(document).on('change', '.base_price_type', function(){
	var price_type = $(this).val();
	if(price_type == 1){
		$('#base_percentage_value').attr('readonly', false); 
		$('#base_per_distance_price').attr('readonly', true); 
		$('#base_percentage_value').val('');
		$('#base_per_distance_price').val('');
	}else{
		$('#base_percentage_value').attr('readonly', true); 
		$('#base_per_distance_price').attr('readonly', false); 
		$('#base_percentage_value').val('');
		$('#base_per_distance_price').val('');
	}
});

$(document).on('change', '.base_percentage_value', function(){
	
	var base_percentage_value = $(this).val();
	var base_min_distance_price = $('#base_min_distance_price').val();
	if(base_min_distance_price != '' && base_percentage_value != ''){
		var base_per_distance_price = base_min_distance_price * base_percentage_value / 100;
		$('#base_per_distance_price').val(base_per_distance_price);
	}else{
		$('#base_per_distance_price').val(''); 
	}
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


