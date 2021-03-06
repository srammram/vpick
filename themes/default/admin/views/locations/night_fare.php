<div class="row well night_remove_div">
<button type="button" class="night_remove btn btn-danger" style="position:absolute; right:-35px;">X</button>
       <input type="hidden" id="night_per_distance" name="night_per_distance[]" value="1" class="form-control"/>
      <div class="form-group  col-xs-6"> <?php echo lang('price_type', 'price_type'); ?>
        <div class="controls" data-count="<?= $night_fare_count ?>">
          <select name="night_price_type[]" id="night_price_type" class="form-control night_price_type">
            <option value="0">Fixed</option>
            <option value="1">Percentage</option>
          </select>
        </div>
      </div>
      <div class="form-group  col-xs-6"> <?php echo lang('percentage_value', 'percentage_value'); ?>
      <div class="controls">
        <input type="text" id="night_percentage_value<?= $night_fare_count ?>"  name="night_percentage_value[]"  readonly class="form-control night_percentage_value"/>
      </div>
    </div>
      <div class="form-group  col-xs-6"> <?php echo lang('minimum_fare', 'minimum_fare'); ?>
        <div class="controls">
          <input type="text" id="night_min_distance_price<?= $night_fare_count ?>"  name="night_min_distance_price[]" class="form-control"/>
        </div>
      </div>
      <div class="form-group  col-xs-6"> <?php echo lang('rate_per_km', 'rate_per_km'); ?>
        <div class="controls">
          <input type="text" id="night_per_distance_price<?= $night_fare_count ?>"  name="night_per_distance_price[]"  class="form-control"/>
        </div>
      </div>
	   <div class="form-group col-sm-6 col-xs-12"> <?php echo lang('labour_charge', 'labour_charge'); ?>
                <div class="controls">
                  <input type="text" id="night_labour_charge<?= $night_fare_count ?>" name="night_labour_charge[]" class="form-control"/>
                </div>
              </div>
			  
			  <div class="form-group col-md-6 col-xs-12">
										<?= lang('work_per_load', 'work_per_load'); ?>
                                        <?php echo form_input('night_work_per_load[]', '', 'class="form-control" id="night_work_per_load".$night_fare_count."" onkeyup="checkNum(this)" required="required"'); ?>
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