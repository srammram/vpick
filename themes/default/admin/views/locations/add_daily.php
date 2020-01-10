<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
  <div class="box-content">
    <div class="row">
      <div class="col-lg-12">
        <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("locations/add_daily", $attrib);
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
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
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
					echo form_dropdown('taxi_type', $t, '', 'class="form-control select"  id="taxi_type" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang('tons', 'tons'); ?>
                <?php
				
				
				echo form_dropdown('tons', '', '', 'class="form-control select"  id="tons" required="required"'); ?>
			</div>
            <div class="form-group col-md-3 col-xs-12">
										<?= lang('accessing', 'accessing'); ?>
                                        <?php echo form_input('shift_name', '', 'class="form-control " id="shift_name" readonly required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-xs-12">
										<?= lang('load_status', 'load_status'); ?>
                                        <?php
										 	
                                        $f['0'] = 'Full Load';
                                        $f['1'] = 'Single Load';
                                        $f['2'] = 'Single Time';
                                        
                                        echo form_dropdown('load_status', $f, '', 'class="form-control select"  id="load_status" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('work_per_load', '', 'class="form-control" id="work_per_load" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-xs-12">
										<?= lang('commision_percentage', 'commision_percentage'); ?>
                                        <?php echo form_input('commision_percentage', '', 'class="form-control " id="commision_percentage" onkeyup="checkNum(this)" required="required"'); ?>
                                    </div>
                                    
                                    <div class="clearfix"></div>
                                    
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
                                        echo form_dropdown('zone_id', '', '', 'class="form-control select-local-zone select" id="zone_id" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("state", "state"); ?>
                <?php
                                        echo form_dropdown('state_id', '', '', 'class="form-control select-local-state select" id="state_id" required="required"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("city", "city"); ?>
                <?php
                                        echo form_dropdown('city_id', '', '', 'class="form-control select-local-city select" id="city_id" required="required"'); ?>
              </div>
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("area", "area"); ?>
                <?php
                                        echo form_dropdown('area_id', '', '', 'class="form-control select-local-area select" id="area_id" required="required"'); ?>
              </div>
            </div>
          </div>
          <div class="col-md-12 hidden">
            <div class="col-md-12 box_he_de"><b><?= lang('peek_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_peak" class="skip" name="is_peak" <?php echo (@$fare->is_peak==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_peak">OFF</label>
                <input type="radio" value="1" id="switch_right_is_peak" class="skip" name="is_peak" <?php echo (@$fare->is_peak==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_peak">ON</label>
              </div>
              <button type="button" id="peek_fare" class="btn btn-primary add_se_btn center-block" style="margin:0px;"><i class="fa fa-plus-circle"></i>
              <?= lang("add"); ?>
              </button>
            </div>
            <div class="col-md-12" id="field">
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
                    <input type="text" name="peak_waiting_price[]" class="form-control"/>
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
            </div>
          </div>
          <div class="col-md-12 hidden">
            <div class="col-md-12 box_he_de"><b><?= lang('night_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_night" class="skip" name="is_night" <?php echo (@$fare->is_night==0) ? "checked" : ''; ?>>
                <label for="switch_left_is_night">OFF</label>
                <input type="radio" value="1"  id="switch_right_is_night" class="skip" name="is_night" <?php echo (@$fare->is_night==1) ? "checked" : ''; ?>>
                <label for="switch_right_is_night">ON</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls">
                  <select name="night_price_type" id="night_price_type" class="form-control">
                    <option value="0">Fixed</option>
                    <option value="1">Percentage</option>
                  </select>
                </div>
              </div>
             
             
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="night_min_distance_price" name="night_min_distance_price" class="form-control"/>
                </div>
              </div>
              <input type="hidden" id="night_per_distance" name="night_per_distance" value="1" class="form-control"/>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="night_price_value" name="night_price_value" maxlength="3" class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('waiting_Fare', 'waiting_Fare'); ?>
                  <div class="controls">
                    <input type="text" name="night_waiting_price" class="form-control"/>
                  </div>
                </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="night_start_hours" id="night_start_hours" class="form-control">
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
                  <select name="night_start_minutes" id="night_start_minutes" class="form-control">
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
                  <select name="night_end_hours" id="night_end_hours" class="form-control">
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
                  <select name="night_end_minutes" id="night_end_minutes" class="form-control">
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
          </div>
          <div class="col-md-12">
            <div class="col-md-12 box_he_de"><b><?= lang('base_fare') ?></b>
              <div class="switch-field pull-right">
                <input type="radio" value="0" id="switch_left_is_base" class="skip" name="is_base" >
                <label for="switch_left_is_base">OFF</label>
                <input type="radio" value="1" id="switch_right_is_base" class="skip" name="is_base" checked>
                <label for="switch_right_is_base">ON</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('Minimum Distance', 'Minimum Distance'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance" name="base_min_distance" class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
                <div class="controls">
                  <input type="text" id="base_min_distance_price" name="base_min_distance_price" class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('price_type', 'price_type'); ?>
                <div class="controls">
                  <select name="base_price_type" id="base_price_type" class="form-control">
                    <option value="0">Fixed</option>
                    <option value="1">Percentage</option>
                  </select>
                </div>
              </div>
              <input type="hidden" id="base_per_distance" name="base_per_distance" value="1" class="form-control"/>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
                <div class="controls">
                  <input type="text" id="base_price_value" name="base_price_value" maxlength="3" class="form-control"/>
                </div>
              </div>
             
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('wait_dur_min', 'Waiting Duration (minutes)'); ?>
              <div class="controls">
                <select name="base_waiting_minute"  id="base_waiting_minute" class="form-control">
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
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('Waiting Charges', 'Waiting Charges'); ?>
                <div class="controls">
                  <input type="text" id="base_waiting_price" name="base_waiting_price" class="form-control"/>
                </div>
              </div>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="labour_charge" name="labour_charge" class="form-control"/>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_daily', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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
        var removeBtn = '<div class="col-sm-3 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div><div class="clearfix"></div><div id="field">';
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
<script type="text/javascript">
    $(document).ready(function() {
    $('form[data-toggle="validator"]').bootstrapValidator({
       
        fields: {
            name: {
                validators: {
                    notEmpty: {
                            message: 'Please Enter the Name'
                        },
                    stringLength: {
                        min: 0,
                        max: 30,
                        message: 'The username must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'The username can only consist of alphabetical'
                    }
                }
            },
            model: {
                validators: {
                    notEmpty: {
                            message: 'Please Enter the Model'
                        },
                   
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'The model can only consist of alphabetical, number and underscore'
                    }
                }
            },
            number: {
                validators: {
                    notEmpty: {
                            message: 'The phone number is required'
                        },
                    stringLength: {
                        min: 0,
                        max: 10,
                        message: 'The phone number 10 characters long'
                    },
                     regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The phone can only consist of number'
                    }
                }
            }
        }
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
});
</script>
<script>
$(document).ready(function(){
	$('.is_country').change(function(){
		
		var site = '<?php echo site_url() ?>';
		var id = '<?= $id ?>';
		var is_country = $(this).val();
		window.location.href = site+"admin/locations/add_daily/?is_country="+is_country;
		
	});
});
</script>
