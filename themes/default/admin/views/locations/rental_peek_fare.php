<div class="row well peek_remove_div">
<button type="button" class="peek_remove btn btn-danger" style="position:absolute; right:-35px;">X</button>
	
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