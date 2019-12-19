<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="box">
  <div class="box-content">
    <div class="row">
      <div class="col-lg-12">
        <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("incentive/edit_incentive/".$id, $attrib);
                ?>
        <div class="row">
          <div class="col-md-12">
            <h2 class="box_he_de"><?= lang('location_details') ?></h2>
            <div class="col-md-12">
              
              
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang('continent', 'continent');  ?>
                <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, $result->continent_id, 'class="form-control select-group-continent select"  id="continent_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("country", "country"); ?>
                <?php
				$lcou[''] = 'Select Country';
										foreach ($countrys as $country) {
											$lcou[$country->id] = $country->name;
										}
                                        echo form_dropdown('country_id', $lcou, $result->country_id, 'class="form-control select-group-country select" id="country_id"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("zone", "zone"); ?>
                <?php
				$lz[''] = 'Select Zone';
										foreach ($zones as $zone) {
											$lz[$zone->id] = $zone->name;
										}
                                        echo form_dropdown('zone_id', $lz, $result->zone_id, 'class="form-control select-group-zone select" id="zone_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("state", "state"); ?>
                <?php
				$ls[''] = 'Select State';
										foreach ($states as $state) {
											$ls[$state->id] = $state->name;
										}
                                        echo form_dropdown('state_id', $ls, $result->state_id, 'class="form-control select-group-state select" id="state_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("city", "city"); ?>
                <?php
				$lc[''] = 'Select City';
										foreach ($citys as $city) {
											$lc[$city->id] = $city->name;
										}
                                        echo form_dropdown('city_id', $lc, $result->city_id, 'class="form-control select-group-city select" id="city_id" '); ?>
              </div>
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("area", "area"); ?>
                <?php
				 $a[''] = 'Select Area';
                                       foreach ($areas as $area) {
                                            $a[$area->id] = $area->name;
                                        }
                                        echo form_dropdown('area_id', $a, $result->area_id, 'class="form-control select-group-area select" id="area_id" '); ?>
              </div>
              
              
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("group_id", "group_id"); ?>
                <?php
				
				$g['0'] = 'Select Group';
                                        foreach ($groups as $group) {
                                            $g[$group->id] = $group->name;
                                        }
										
					echo form_dropdown('group_id[]', $g, explode(',',$result->group_id), 'class="form-control select-group select" multiple id="group_id" required="required"'); ?>
                    
              </div>
              
            </div>
          </div>
		 	
            <div class="col-md-12">
            <div class="col-md-12 box_he_de"><b><?= lang('incentive') ?></b>
             
            </div>
            <div class="col-md-12">
            
            <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('type', 'type'); ?>
                <div class="controls">
                  <select name="type" id="type" class="form-control">
                    <option value="1" <?= $result->type == 1 ? 'selected' : '' ?>>Fare</option>
                    <option value="2" <?= $result->type == 2 ? 'selected' : '' ?>>Ride</option>
                    <option value="3" <?= $result->type == 3 ? 'selected' : '' ?>>Fare and Ride</option>
                  </select>
                </div>
              </div>
            
             
              
              <div class="form-group col-sm-3 col-xs-12 fare"> <?php echo lang('target_fare', 'target_fare'); ?>
                <div class="controls">
                  <input type="text" id="target_fare" name="target_fare" value="<?= $result->target_fare ?>" class="form-control "/>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12 ride"> <?php echo lang('target_ride', 'target_ride'); ?>
                <div class="controls">
                  <input type="text" id="target_ride" name="target_ride" value="<?= $result->target_ride ?>" class="form-control "/>
                </div>
              </div>
              
               <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('date_type', 'date_type'); ?>
                <div class="controls">
                  <select name="date_type" id="date_type" class="form-control ">
                    <option value="0" <?= $result->date_type == 0 ? 'selected' : '' ?>>Days</option>
                    <option value="1" <?= $result->date_type == 1 ? 'selected' : '' ?>>Dates</option>
                  </select>
                </div>
              </div>
             
             <?php
			 $days = [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
			];
			 ?>
             <div class="form-group col-sm-3 col-xs-12 days"> <?php echo lang('days', 'days'); ?>
                <div class="controls">
                  <select name="days" id="days" class="form-control ">
                    <option value="">Select Days</option>
                    <?php
					foreach($days as $day){
					?>
                    <option value="<?= $day ?>" <?= $result->days == 3 ? 'selected' : '' ?>><?= $day ?></option>
                    <?php
					}
					?>
                  </select>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12 dates"> <?php echo lang('start_date', 'start_date'); ?>
                <div class="controls">
                  <input type="text" id="start_date" name="start_date" value="<?= $result->start_date ?>" class="form-control "/>
                </div>
              </div>


			<div class="form-group col-sm-3 col-xs-12 dates"> <?php echo lang('end_date', 'end_date'); ?>
                <div class="controls">
                  <input type="text" id="end_date" name="end_date" value="<?= $result->end_date ?>" class="form-control "/>
                </div>
              </div>
			<?php $stime = explode(':', $result->start_time); $etime = explode(':', $result->end_time) ?>
             <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('start_hours', 'start_hours'); ?>
                <div class="controls">
                  <select name="start_hours" id="start_hours" class="form-control">
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
                  <select name="start_minutes" id="start_minutes" class="form-control">
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
                  <select name="end_hours" id="end_hours" class="form-control">
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
                  <select name="end_minutes" id="end_minutes" class="form-control">
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
              
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('fare_type', 'fare_type'); ?>
                <div class="controls">
                  <select name="fare_type" id="fare_type" class="form-control ">
                    <option value="0" <?= $result->fare_type == 0 ? 'selected' : '' ?>>Fixed</option>
                    <option value="1" <?= $result->fare_type == 1 ? 'selected' : '' ?>>Percentage</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('fare_amount', 'fare_amount'); ?>
                <div class="controls">
                  <input type="text" id="fare_amount" name="fare_amount" value="<?= $result->fare_amount ?>" class="form-control"/>
                </div>
              </div>
              
            </div>
          </div>
        </div>

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
$(document).ready(function(){
	
	var date_type = $('#date_type').val();
	var type = $('#type').val();
	
	if(type == 1){
		$('.ride').hide();
		$('.fare').show();
	}else if(type == 2){
		$('.fare').hide();
		$('.ride').show();
	}else{
		$('.fare').show();
		$('.ride').show();
	}
	
	if(date_type == 0){
		$('.dates').hide();
		$('.days').show();
	}else{
		$('.days').hide();
		$('.dates').show();
	}
	
	
	
	var m_new = new Date();
	var month_new = m_new.getMonth() - <?= $due_month ?>;
	m_new.setMonth(month_new);
	
	var yearRangeMin =  '-<?= $due_year ?>:+0';
	var yearRangeMax =  '-0:+<?= $due_year ?>';
	
	function getDate(element) {
     var date;
	 
     try {
       date = element.value;
     } catch (error) {
       date = null;
     }

     return date;
   }

	var dateFormat =  "dd/mm/yy";
		
	var start_date = $("#start_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		minDate: 0,
		numberOfMonths: 1,
		yearRange: '-100:+0',
		
	})
	.on("change", function() {
		end_date.datepicker("option", "minDate", getDate(this));
	});
	
	var end_date = $("#end_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		minDate: 0,
		numberOfMonths: 1
	})
	.on("change", function() {
		start_date.datepicker("option", "minDate", getDate(this));
	});
	
	

});
$(document).on('change', '#type', function(){
	var ctype = this.value;
	if(ctype == 1){
		$('.ride').hide();
		$('.fare').show();
	}else if(ctype == 2){
		$('.fare').hide();
		$('.ride').show();
	}else{
		$('.fare').show();
		$('.ride').show();
	}

});

$(document).on('change', '#date_type', function(){
	var cdate_type = this.value;
	if(cdate_type == 0){
		$('.dates').hide();
		$('.days').show();
	}else{
		$('.days').hide();
		$('.dates').show();
	}

});



$('.select-group-continent').change(function(){
		
		$(".select-group-country").select2("destroy");
		$(".select-group-zone").select2("destroy");
		$(".select-group-state").select2("destroy");
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		$(".select-group").select2("destroy");
		
		var id = $(this).val();
	
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getCountry_bycontinent')?>',
			data: {continent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				
				
				$(".select-group-country").html(option);
				$(".select-group").html(option1);
				
				$(".select-group-zone").html('<option value="">Select Zone</option>');
				$(".select-group-state").html('<option value="">Select State</option>');
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				
				$(".select-group-country").select2();
				$(".select-group-zone").select2();
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				$(".select-group").select2();
			}
		})
	});
	
	$('.select-group-country').change(function(){
		$(".select-group-zone").select2("destroy");
		$(".select-group-state").select2("destroy");
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		$(".select-group").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				
				$(".select-group-zone").html(option);
				$(".select-group-state").html('<option value="">Select State</option>');
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				$(".select-group").html(option1);
				$(".select-group-zone").select2();
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				$(".select-group").select2();
			}
		})
	});
	
	$('.select-group-zone').change(function(){
		$(".select-group-state").select2("destroy");
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		$(".select-group").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getState_byzone')?>',
			data: {zone_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				
				$(".select-group-state").html(option);
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				$(".select-group").html(option1);
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				$(".select-group").select2();
			}
		})
	});
	
	$('.select-group-state').change(function(){
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		$(".select-group").select2("destroy");
		var id = $(this).val();
		var state = $('.select-group-state option:selected').text();
		$('#license_issuing_authority').val(state);
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getCity_bystate')?>',
			data: {state_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				$(".select-group-city").html(option);
				$(".select-group-area").html('<option value="">Select Area</option>');
				$(".select-group").html(option1);
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				$(".select-group").select2();
			}
		})
	});
	
	$('.select-group-city').change(function(){
		
		$(".select-group-area").select2("destroy");
		$(".select-group").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getArea_bycity')?>',
			data: {city_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				$(".select-group-area").html(option);
				$(".select-group").html(option1);
				$(".select-group-area").select2();
				$(".select-group").select2();
			}
		})
	});
	
	$('.select-group-area').change(function(){
		
		$(".select-group").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('incentive/getGroup_byarea')?>',
			data: {area_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				
				
				var option1 = '<option value="">Select Group</option>';
				
				if (scdata.group !== undefined)
				{
				$.each(scdata.group,function(n1,v1){
					option1 += '<option value="'+v1.id+'">'+v1.text+'</option>';
				});
				}
				$(".select-group").html(option1);
				$(".select-group").select2();
			}
		})
	});
</script>