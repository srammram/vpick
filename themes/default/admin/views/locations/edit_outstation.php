    <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("locations/edit_outstation/".$id, $attrib);
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
                                    
                                    <div class="form-group  col-xs-3">
									<?php echo lang('package_name', 'package_name'); ?>
                                    <div class="controls">
                                        <input type="text" id="package_name" name="package_name" value="<?= $result->package_name ?>" class="form-control" onkeyup="inputFirstUpper(this)"/>
                                    </div>
                                    
                        </div>
                        	<div class="col-md-12">
                        	
                                <div class="col-md-6">
                                	
                                    
                                    <h2 class="box_he_de"><?= lang('from_location') ?></h2>  
                                    
                                	<div class="form-group   col-xs-6">
                                <?= lang("continent", "continent"); ?>
                                
                               <?php
							   $lcon[''] = 'Select Continent';
								foreach ($lcontinents as $lcontinent) {
									$lcon[$lcontinent->id] = $lcontinent->name;
								}
                                echo form_dropdown('local_continent_id', $lcon, $result->local_continent_id, 'class="form-control select-local-continent " id="local_continent_id" '); ?>
                            </div>
                                    <div class="form-group   col-xs-6">
                                        <?= lang("country", "country"); ?>
                                       <?php
									   $lcou[''] = 'Select Country';
										foreach ($lcountrys as $lcountry) {
											$lcou[$lcountry->id] = $lcountry->name;
										}
                                        echo form_dropdown('local_country_id', $lcou, $result->local_country_id, 'class="form-control select-local-country " id="local_country_id" '); ?>
                                    </div>
                                    <div class="form-group   col-xs-6">
                                        <?= lang("zone", "zone"); ?>
                                       <?php
									   $lz[''] = 'Select Zone';
										foreach ($lzones as $lzone) {
											$lz[$lzone->id] = $lzone->name;
										}
                                        echo form_dropdown('local_zone_id', $lz, $result->local_zone_id, 'class="form-control select-local-zone " id="local_zone_id" '); ?>
                                    </div>
                                    <div class="form-group   col-xs-6">
                                        <?= lang("state", "state"); ?>
                                       <?php
									   $ls[''] = 'Select State';
										foreach ($lstates as $lstate) {
											$ls[$lstate->id] = $lstate->name;
										}
                                        echo form_dropdown('local_state_id', $ls, $result->local_state_id, 'class="form-control select-local-state " id="local_state_id" '); ?>
                                    </div>
                                    <div class="form-group   col-xs-6">
                                        <?= lang("city", "city"); ?>
                                       <?php
									   $lci[''] = 'Select City';
										foreach ($lcitys as $lcity) {
											$lci[$lcity->id] = $lcity->name;
										}
                                        echo form_dropdown('from_city_id', $lci, $result->from_city_id, 'class="form-control select-local-city " id="local_city_id" '); ?>
                                    </div>
                                </div>    
                                <div class="col-md-6">   
                                    <div class="fixed_package common_package">
                                    <h2 class="box_he_de"><?= lang('to_location') ?></h2>
                                    
                                    <div class="form-group col-xs-6">
                                <?= lang("continent", "continent"); ?>
                                
                               <?php
							   $pcon[''] = 'Select Continent';
								foreach ($pcontinents as $pcontinent) {
									$pcon[$pcontinent->id] = $pcontinent->name;
								}
                                echo form_dropdown('permanent_continent_id', $pcon, $result->permanent_continent_id, 'class="form-control select-permanent-continent " id="permanent_continent_id" '); ?>
                            </div>
                                    <div class="form-group  col-xs-6">
                                        <?= lang("country", "country"); ?>
                                       <?php
									   $pcou[''] = 'Select Country';
										foreach ($pcountrys as $pcountry) {
											$pcou[$pcountry->id] = $pcountry->name;
										}
                                        echo form_dropdown('permanent_country_id', $pcou, $result->permanent_country_id, 'class="form-control select-permanent-country " id="permanent_country_id" '); ?>
                                    </div>
                                    <div class="form-group  col-xs-6">
                                        <?= lang("zone", "zone"); ?>
                                       <?php
									   $pz[''] = 'Select Zone';
										foreach ($pzones as $pzone) {
											$pz[$pzone->id] = $pzone->name;
										}
                                        echo form_dropdown('permanent_zone_id', $pz, $result->permanent_zone_id, 'class="form-control select-permanent-zone " id="permanent_zone_id" '); ?>
                                    </div>
                                    <div class="form-group col-xs-6">
                                        <?= lang("state", "state"); ?>
                                       <?php
									   $ps[''] = 'Select State';
										foreach ($pstates as $pstate) {
											$ps[$pstate->id] = $pstate->name;
										}
                                        echo form_dropdown('permanent_state_id', $ps, $result->permanent_state_id, 'class="form-control select-permanent-state " id="permanent_state_id" '); ?>
                                    </div>
                                    <div class="form-group  col-xs-6">
                                        <?= lang("city", "city"); ?>
                                       <?php
									   $pci[''] = 'Select City';
										foreach ($pcitys as $pcity) {
											$pci[$pcity->id] = $pcity->name;
										}
                                        echo form_dropdown('to_city_id', $pci, $result->to_city_id, 'class="form-control select-permanent-city " id="permanent_city_id" '); ?>
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
                  <div class="controls">
                    <input type="text" id="peak_percentage_value0"   name="peak_percentage_value[]" readonly class="form-control peak_percentage_value"/>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                  <div class="controls">
                    <input type="text"  id="peak_min_distance_price0" name="peak_min_distance_price[]"  class="form-control"/>
                  </div>
                </div>
                <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                  <div class="controls">
                    <input type="text" id="peek_per_distance_price0" name="peek_per_distance_price[]"  class="form-control"/>
                  </div>
                </div>
				<div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="peak_labour_charge0" value="<?= $pslot->labour_charge ?>" name="peak_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('peak_work_per_load[]', $pslot->work_per_load, 'class="form-control" id="peak_work_per_load0" onkeyup="checkNum(this)" required="required"'); ?>
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
                                <input type="hidden" name="is_base" value="1">
                                	<h2 class="box_he_de"><?= lang('one_way_trip') ?> <span class="pull-right"><input class="pull-right" type="radio" name="is_way" value="1" <?= $result->is_oneway == 1 ? 'checked' : '' ?>></span></h2>   
                                    
                              <div class="form-group oneway_package_price  col-xs-6">
                                    <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->oneway_package_price ?>" id="oneway_package_price" name="oneway_package_price" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group oneway_package_price  col-xs-6">
                                    <?php echo lang('minimum_distance', 'minimum_distance'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->oneway_distance ?>" id="oneway_distance" name="oneway_distance" class="form-control"/>
                                    </div>
                                </div>  
                                <input type="hidden" id="oneway_per_distance" value="1" name="oneway_per_distance" class="form-control"/>
                                
                                <div class="form-group  col-xs-6">
									<?php echo lang('extra_fare', 'extra_fare'); ?>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->is_oneway == 1 ? $result->per_distance_price : '' ?>"  id="oneway_per_distance_price" name="oneway_per_distance_price" class="form-control"/>
                                    </div>
                                </div>  
                                   
								   <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="oneway_labour_charge" name="oneway_labour_charge" value="<?= $result->is_oneway == 1 ? $result->labour_charge : '' ?>" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('oneway_work_per_load', $result->is_oneway == 1 ? $result->work_per_load : '', 'class="form-control" id="oneway_work_per_load" onkeyup="checkNum(this)" '); ?>
                                    </div>
								   
                                    <div class="form-group  col-xs-6">
                                        <?php echo lang('driver_allowance', 'driver_allowance'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->is_oneway == 1 ? $result->driver_allowance_per_day : '' ?>" id="oneway_driver_allowance_per_day" name="oneway_driver_allowance_per_day" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group  col-xs-6">
                                        <?php echo lang('night_allowance', 'night_allowance'); ?>
                                        <div class="controls">
                                            <input type="text"  value="<?= $result->is_oneway == 1 ? $result->driver_night_per_day : '' ?>"id="oneway_driver_night_per_day" name="oneway_driver_night_per_day" class="form-control"/>
                                        </div>
                                    </div> 
                                
                                   
                                    
                                        
                                </div>
                                
                                <div class="col-md-4">
                                	<h2 class="box_he_de"><?= lang('round_trip') ?> <span class="pull-right"><input class="pull-right" type="radio" name="is_way" value="2" <?= $result->is_twoway == 1 ? 'checked' : '' ?> ></span></h2>   
                                    
                                    
                                    <div class="form-group twoway_package_price  col-xs-6">
                                        <?php echo lang('round_trip_fare', 'round_trip_fare'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->twoway_package_price ?>" id="twoway_package_price" name="twoway_package_price" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group twoway_package_price  col-xs-6">
                                        <?php echo lang('round_trip_distance', 'round_trip_distance'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->twoway_distance ?>" id="twoway_distance" name="twoway_distance" class="form-control"/>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" id="twoway_per_distance" value="1" name="twoway_per_distance" class="form-control"/>
                                    
                                    <div class="form-group  col-xs-6">
                                        <?php echo lang('extra_fare', 'extra_fare'); ?>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->is_twoway == 1 ? $result->per_distance_price : '' ?>" id="twoway_per_distance_price" name="twoway_per_distance_price" class="form-control"/>
                                        </div>
                                    </div>  
									<div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="twoway_labour_charge" name="twoway_labour_charge" value="<?= $result->is_twoway == 1 ? $result->labour_charge : '' ?>" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('twoway_work_per_load', $result->is_twoway == 1 ? $result->work_per_load : '', 'class="form-control" id="twoway_work_per_load" onkeyup="checkNum(this)" '); ?>
                                    </div>
                                    
                                    <div class="form-group  col-xs-6">
                                        <?php echo lang('driver_allowance', 'driver_allowance'); ?>
                                        <div class="controls">
                                            <input type="text" id="twoway_driver_allowance_per_day" value="<?= $result->is_twoway == 1 ? $result->driver_allowance_per_day : '' ?>" name="twoway_driver_allowance_per_day" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group  col-xs-6">
                                        <?php echo lang('night_allowance', 'night_allowance'); ?>
                                        <div class="controls">
                                            <input type="text" id="twoway_driver_night_per_day" value="<?= $result->is_twoway == 1 ? $result->driver_night_per_day : '' ?>" name="twoway_driver_night_per_day" class="form-control"/>
                                        </div>
                                    </div>
                                
                                   
                                        
                                </div>
                                
                                
                                </div>
                                
                                
                            </div>
                            
                     	    
                      
                        
                        
                        
                 </div>
                <div class="col-sm-12 last_sa_se"><?php echo form_submit('edit_outstation', lang('edit_outstation'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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

<script>
$('#taxi_type').change(function(){
		
		$("#tons").select2("destroy");
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getTons_byTaxi_type')?>',
			data: {taxi_type_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				
				
				console.log(scdata);
				$option1 = '<option value="">Select Tons</option>';
				if(scdata.length != 0){
				$.each(scdata.type,function(n,v){
					$option1 += '<option data-shift="'+v.shift_name+'" value="'+v.tons+'">'+v.tons+'</option>';
				});
				}
				$("#tons").html($option1);
				$("#tons").select2();
				
			}
		})
	});
	
	$('#tons').change(function(){
		var shift_name = $(this).find(':selected').attr('data-shift');
		$('#shift_name').val(shift_name);
		
	});
	
var peek_fare_count = <?= $peak_count ?>;
$('#peek_fare').click(function(){
	is_peak = $("input[name='is_peak']:checked").val();
	if(is_peak == 1){
		peek_fare_count++;
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('locations/getoutstationPeek')?>',
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
		$('#peak_work_per_load'+id).attr('readonly', true); 
		$('#peak_labour_charge'+id).attr('readonly', true); 
		$('#peak_percentage_value'+id).val('');
		$('#peak_min_distance_price'+id).val('');
		$('#peek_per_distance_price'+id).val('');
		$('#peak_labour_charge'+id).val('');
		$('#peak_work_per_load'+id).val('');
		
	}else{
		$('#peak_percentage_value'+id).attr('readonly', true); 
		$('#peak_min_distance_price'+id).attr('readonly', false); 
		$('#peek_per_distance_price'+id).attr('readonly', false);
		$('#peak_work_per_load'+id).attr('readonly', false); 
		$('#peak_labour_charge'+id).attr('readonly', false); 
		$('#peak_percentage_value'+id).val('');
		$('#peak_min_distance_price'+id).val('');
		$('#peek_per_distance_price'+id).val('');
		$('#peak_labour_charge'+id).val('');
		$('#peak_work_per_load'+id).val('');
	}
});

$(document).on('change', '.peak_percentage_value', function(){
	var id = $(this).attr('id').slice(-1);
	
	var is_way = $('input[name="is_way"]:checked').val();
	var peak_percentage_value = $(this).val();
	if(is_way == 2){
		var base_min_distance_price = $('#twoway_package_price').val();
		var base_per_distance_price = $('#twoway_per_distance_price').val();
		var base_labour_charge = $('#twoway_labour_charge').val();
		var base_work_per_load = $('#twoway_work_per_load').val();
		
		if(base_min_distance_price != '' && base_per_distance_price != '' && base_labour_charge != '' && base_work_per_load != ''){
			var peak_min_distance_price = base_min_distance_price * peak_percentage_value / 100;
			var peek_per_distance_price = base_per_distance_price * peak_percentage_value / 100;
			var peak_labour_charge = base_labour_charge * peak_percentage_value / 100;
			var peak_work_per_load = base_work_per_load * peak_percentage_value / 100;
			$('#peak_min_distance_price'+id).val(peak_min_distance_price);
			$('#peek_per_distance_price'+id).val(peek_per_distance_price);
			$('#peak_labour_charge'+id).val(peak_labour_charge);
			$('#peak_work_per_load'+id).val(peak_work_per_load);
		}else{
			alert('Please select package and per rate amount');
			$('#peak_percentage_value'+id).val(''); 
		}
	}else if(is_way == 1){
		var base_min_distance_price = $('#oneway_package_price').val();
		var base_per_distance_price = $('#oneway_per_distance_price').val();
		var base_labour_charge = $('#oneway_labour_charge').val();
		var base_work_per_load = $('#oneway_work_per_load').val();
		if(base_min_distance_price != '' && base_per_distance_price != '' && base_labour_charge != '' && base_work_per_load != ''){
			var peak_min_distance_price = base_min_distance_price * peak_percentage_value / 100;
			var peek_per_distance_price = base_per_distance_price * peak_percentage_value / 100;
			var peak_labour_charge = base_labour_charge * peak_percentage_value / 100;
			var peak_work_per_load = base_work_per_load * peak_percentage_value / 100;
			$('#peak_min_distance_price'+id).val(peak_min_distance_price);
			$('#peek_per_distance_price'+id).val(peek_per_distance_price);
			$('#peak_labour_charge'+id).val(peak_labour_charge);
			$('#peak_work_per_load'+id).val(peak_work_per_load);
		}else{
			alert('Please select package and per rate amount');
			$('#peak_percentage_value'+id).val(''); 
		}
	}
});
</script>

<script>

$(document).on('change', '#type', function(){
	if($('#type').val() == 'variable_package') {
		$('.common_package').hide();
		$('.variable_package').show();
	} else {
		$('.common_package').hide();
		$('.fixed_package').show();
	} 
});

$(document).on('ifChecked', '#is_oneway', function (event) {
    
    $('#is_oneway').each(function () {
        $(this).iCheck('check');
		$('.oneway_package_price').show();
    });
});
$(document).on('ifUnchecked', '#is_oneway', function (event) {
    
    $('#is_oneway').each(function () {
        $('.oneway_package_price').hide();
    });
});

$(document).on('ifChecked', '#is_twoway', function (event) {
    
    $('#is_twoway').each(function () {
        $(this).iCheck('check');
		$('.twoway_package_price').show();
    });
});
$(document).on('ifUnchecked', '#is_twoway', function (event) {
    
    $('#is_twoway').each(function () {
        $('.twoway_package_price').hide();
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



