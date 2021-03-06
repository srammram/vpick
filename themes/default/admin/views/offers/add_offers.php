<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
  <div class="box-content">
    <div class="row">
      <div class="col-lg-12">
        <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("offers/add_offers", $attrib);
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
            <h2 class="box_he_de"><?= lang('location_details') ?></h2>
            <div class="col-md-12">
             
             
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang('continent', 'continent'); ?>
                <?php
                                        $c[''] = 'Select Continents';
                                        foreach ($continents as $continents) {
                                            $c[$continents->id] = $continents->name;
                                        }
                                        echo form_dropdown('continent_id', $c, '', 'class="form-control select-group-continent select"  id="continent_id"'); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("country", "country"); ?>
                <?php
                                        echo form_dropdown('country_id', '', '', 'class="form-control select-group-country select" id="country_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("zone", "zone"); ?>
                <?php
                                        echo form_dropdown('zone_id', '', '', 'class="form-control select-group-zone select" id="zone_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("state", "state"); ?>
                <?php
                                        echo form_dropdown('state_id', '', '', 'class="form-control select-group-state select" id="state_id" '); ?>
              </div>
              <div class="form-group col-sm-3 col-xs-12">
                <?= lang("city", "city"); ?>
                <?php
                                        echo form_dropdown('city_id', '', '', 'class="form-control select-group-city select" id="city_id" '); ?>
              </div>
              
              <div class="form-group col-md-3 col-xs-12">
                <?= lang("area", "area"); ?>
                <?php
                                        echo form_dropdown('area_id', '', '', 'class="form-control select-group-area select" id="area_id" '); ?>
              </div>
              <input type="hidden" name="group_id" id="group_id" value="5">
              <!--<div class="form-group col-md-3 col-xs-12">
                <?= lang("group_id", "group_id"); ?>
                <?php
					$g[''] = 'Select Group';
					foreach ($groups as $group) {
						$g[$group->id] = $group->name;
					}
					echo form_dropdown('group_id', $g, '', 'class="form-control select-group select" id="group_id" required="required"'); ?>
              </div>-->
              
            </div>
          </div>
          <div class="col-md-12">
            <div class="col-md-12 box_he_de"><b><?= lang('offers') ?></b>
             
            </div>
            <div class="col-md-12">
            
            <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('name', 'name'); ?>
                <div class="controls">
                  <input type="text" id="offer_name" required name="offer_name" class="form-control "/>
                </div>
              </div>
              
              <?php $refer_code = $this->site->promocode('O');  ?>
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('promo_code', 'promo_code'); ?>
                <div class="controls">
                  <input type="text" id="offer_code" name="offer_code" value="<?= $refer_code ?>" readonly class="form-control "/>
                </div>
              </div>
              
              <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('description', 'description'); ?>
                <div class="controls">
                  
                  <textarea class="form-control" name="offer_description" id="offer_description"></textarea>
                </div>
              </div>
            	
                <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('maximum_limit', 'maximum_limit'); ?>
                    <div class="controls">
                      <input type="text" id="offer_limit" name="offer_limit" class="form-control "/>
                    </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('minimum_amount', 'minimum_amount'); ?>
                    <div class="controls">
                      <input type="text" id="maximum_amount" name="maximum_amount" required class="form-control "/>
                    </div>
              </div>
              
            <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('fare_type', 'fare_type'); ?>
                <div class="controls">
                  <select name="offer_fare_type" id="offer_fare_type" class="form-control">
                    <option value="0" selected>Fixed</option>
                    <option value="1">Percentage</option>
                    <option value="2" >Others</option>
                  </select>
                </div>
              </div>
            
             
              
              <div class="form-group col-sm-3 col-xs-12 fare"> <?php echo lang('offer_fare', 'offer_fare'); ?>
                <div class="controls">
                  <input type="text" id="offer_fare" name="offer_fare" required class="form-control "/>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12 other"> <?php echo lang('other_product', 'other_product'); ?>
                <div class="controls">
                  <input type="text" id="other_product" name="other_product" class="form-control "/>
                </div>
              </div>
              
               <div class="form-group col-sm-3 col-xs-12"> <?php echo lang('date_type', 'date_type'); ?>
                <div class="controls">
                  <select name="date_type" id="date_type" class="form-control ">
                    <option value="0">Days</option>
                    <option value="1">Dates</option>
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
                    <option value="<?= $day ?>"><?= $day ?></option>
                    <?php
					}
					?>
                  </select>
                </div>
              </div>
              
              <div class="form-group col-sm-3 col-xs-12 dates"> <?php echo lang('start_date', 'start_date'); ?>
                <div class="controls">
                  <input type="text" id="start_date" name="start_date" class="form-control "/>
                </div>
              </div>


			<div class="form-group col-sm-3 col-xs-12 dates"> <?php echo lang('end_date', 'end_date'); ?>
                <div class="controls">
                  <input type="text" id="end_date" name="end_date" class="form-control "/>
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
        </div>
        <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_offers', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>
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
	var offer_fare_type = $('#offer_fare_type').val();
	
	if(offer_fare_type == 2){
		$('.fare').hide();
		$('.other').show();
	}else{
		$('.other').hide();
		$('.fare').show();
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
		start_date.datepicker("option", "maxDate", getDate(this));
	});
	
	

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

$(document).on('change', '#offer_fare_type', function(){
	var offer_fare_type = this.value;
	if(offer_fare_type == 2){
		$('.fare').hide();
		$('.other').show();
	}else{
		$('.other').hide();
		$('.fare').show();
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
			url: '<?=admin_url('offers/getCountry_bycontinent')?>',
			data: {continent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				
				
				
				$(".select-group-country").html(option);
				
				
				$(".select-group-zone").html('<option value="">Select Zone</option>');
				$(".select-group-state").html('<option value="">Select State</option>');
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				
				$(".select-group-country").select2();
				$(".select-group-zone").select2();
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				
			}
		})
	});
	
	$('.select-group-country').change(function(){
		$(".select-group-zone").select2("destroy");
		$(".select-group-state").select2("destroy");
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('offers/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				
				
				$(".select-group-zone").html(option);
				$(".select-group-state").html('<option value="">Select State</option>');
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				
				$(".select-group-zone").select2();
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				
			}
		})
	});
	
	$('.select-group-zone').change(function(){
		$(".select-group-state").select2("destroy");
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('offers/getState_byzone')?>',
			data: {zone_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				
				
				$(".select-group-state").html(option);
				$(".select-group-city").html('<option value="">Select City</option>');
				$(".select-group-area").html('<option value="">Select Area</option>');
				
				$(".select-group-state").select2();
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				
			}
		})
	});
	
	$('.select-group-state').change(function(){
		$(".select-group-city").select2("destroy");
		$(".select-group-area").select2("destroy");
		
		var id = $(this).val();
		var state = $('.select-group-state option:selected').text();
		$('#license_issuing_authority').val(state);
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('offers/getCity_bystate')?>',
			data: {state_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';
				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				
				$(".select-group-city").html(option);
				$(".select-group-area").html('<option value="">Select Area</option>');
				
				$(".select-group-city").select2();
				$(".select-group-area").select2();
				
			}
		})
	});
	
	$('.select-group-city').change(function(){
		
		$(".select-group-area").select2("destroy");
		
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('offers/getArea_bycity')?>',
			data: {city_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var option = '<option value="">Select Country</option>';

				
				if (scdata.location !== undefined)
				{
					$.each(scdata.location,function(n,v){
						option += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				

				$(".select-group-area").html(option);

				$(".select-group-area").select2();

			}
		})
	});
	
	
</script>
