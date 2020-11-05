<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_customer_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'edit-cus-dis-form');
        echo admin_form_open("system_settings/edit_customer_discount/".$customer_discounts->id, $attrib); ?>

                    <p><?= lang('enter_info'); ?></p>
            <div class="form-group col-lg-12">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', set_value('name', $customer_discounts->name), 'class="form-control tip" id="name" required="required"'); ?>
                <input type="hidden" name="id" value="<?=$customer_discounts->id?>">
            </div>
              <div class="form-group col-lg-6">
                <?= lang('from_date', 'from_date'); ?>
                    <div class="controls ">
                        <?php $from_date='';if($customer_discounts->from_date!='0000-00-00 00:00:00'){$from_date = trim($customer_discounts->from_date,' 00:00:00');} ?>
                      <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required" value="<?=@$from_date?>" autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6">
                <?= lang('to_date', 'to_date'); ?>
                <?php $to_date='';if($customer_discounts->to_date!='0000-00-00 00:00:00'){$to_date = trim($customer_discounts->to_date,' 00:00:00');} ?>
              <div class="controls">
                <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date" required="required" value="<?=@$to_date?>" autocomplete="off">
              </div>
            </div>
             <div class="form-group col-lg-6">
                <?= lang('from_time', 'from_time'); ?>
                <?php $from_time='';if($customer_discounts->from_time!='00:00:00'){$from_time = trim(trim($customer_discounts->from_time,'00'),':');}else{$from_time = '00:00';} ?>
                    <div class="controls ">
                      <input type="text" data-format="hh:mm" name="from_time" class="form-control" placeholder="From Time " id="from_time" required="required" value="<?=@$from_time?>" autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6">
                <?= lang('to_time', 'to_time'); ?>
                <?php $to_time='';if($customer_discounts->to_time!='00:00:00'){$to_time = trim(trim($customer_discounts->to_time,'00'),':');}else{$to_time = '00:00';} ?>
              <div class="controls">
                <input type="text" name="to_time" class="form-control" placeholder="To Time " id="to_time" required="required" value="<?=@$to_time?>" autocomplete="off">
              </div>
            </div>
             <div class="form-group col-lg-12">
               
              <div class="controls">
                <?php $weekdays = explode(',',$customer_discounts->week_days); ?>
                <?= lang('Apply_on', 'Apply_on'); ?> : 
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="mon" value="Monday" autocomplete="off" <?php if(in_array('Monday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Monday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="tue" value="Tuesday" autocomplete="off" <?php if(in_array('Tuesday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Tuesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="wed" value="Wednesday" autocomplete="off" <?php if(in_array('Wednesday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Wednesday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="thu" value="Thursday" autocomplete="off" <?php if(in_array('Thursday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Thursday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="fri" value="Friday" autocomplete="off" <?php if(in_array('Friday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Friday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sat" value="Saturday" autocomplete="off" <?php if(in_array('Saturday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Saturday</label>
                <label><input type="checkbox" name="weekdays[]" class="form-control r_weekdays" required="required" data-code="sun" value="Sunday" autocomplete="off" <?php if(in_array('Sunday',$weekdays)) { echo 'checked="checked"';} ?>>&nbsp;&nbsp;Sunday</label>
              </div>
            </div>
            
            <!--<fieldset>
            <legend><?= lang('conditions') ?>:
            <button type="button" class="btn btn-primary btn-xs" id="addCondition"><i class="fa fa-plus"></i></button>
            </legend>
            <div id="condition">
                <?php foreach ($customer_discounts->conditions as $c => $c_val) : ?>
                <div class="well col-lg-12">
                  <div class="form-group col-lg-12">
                    <div class="controls">
                      <select name="condition[0][condition_method]"  class="form-control select condition_method" >
                        <option value="condition_date" <?php if($c_val->condition_method=="condition_date") { echo 'selected="selected"';} ?>><?= lang('date') ?></option>
                        <option value="condition_time" <?php if($c_val->condition_method=="condition_time") { echo 'selected="selected"';} ?>><?= lang('time') ?></option>
                        <option value="condition_days" <?php if($c_val->condition_method=="condition_days") { echo 'selected="selected"';} ?>><?= lang('day_of_weak') ?></option>
                      </select>
                    </div>
                  </div>
                  <!-- <div class="form-group col-lg-6">
                    <div class="controls">
                      <select name="condition[0][condition_type]" id="condition_type" class="form-control select" >
                        <option value="in_list">In List</option>
                        <option value="not_in_list">Not In List</option>
                      </select>
                    </div>
                  </div> 
                  <div class="form-group  col-lg-6 date_div" style="display: <?=($c_val->condition_method=="condition_date")?"block":"none";?>">
                    <div class="controls ">
                      <input type="text" name="condition[0][from_date]" class="form-control totay_date" placeholder="From Date " id="from_date" required="required" value="<?=@$c_val->from_date?>">
                    </div>
                  </div>
                  <div class="form-group  col-lg-6 date_div" style="display: <?=($c_val->condition_method=="condition_date")?"block":"none";?>">
                    <div class="controls">
                      <input type="text" name="condition[0][to_date]" class="form-control totay_date" placeholder="To Date " id="to_date" required="required" value="<?=@$c_val->to_date?>">
                    </div>
                  </div>
                  
                  <div class="form-group  col-lg-6 time_div" style="display: <?=($c_val->condition_method=="condition_time")?"block":"none";?>">
                    <div class="controls ">
                      <input type="text" name="condition[0][from_time]" class="form-control time" placeholder="From  Time" id="from_time" required="required"  value="<?=@$c_val->from_time?>">
                    </div>
                  </div>
                  <div class="form-group  col-lg-6 time_div" style="display: <?=($c_val->condition_method=="condition_time")?"block":"none";?>">
                    <div class="controls">
                      <input type="text" name="condition[0][to_time]" class="form-control time" placeholder="To  Time" id="to_time" required="required"  value="<?=@$c_val->to_time?>">
                    </div>
                  </div>
                  
                  <div class="form-group col-lg-12 condition_days" style="display: <?=($c_val->condition_method=="condition_days")?"block":"none";?>">
                    <div class="controls">
                        <?php $days = ($c_val->days!='')? explode(',',$c_val->days):array();?>
                      <select name="condition[0][condition_days][]" id="condition_days" multiple class="form-control select"  placeholder="Choose Multiple days" >
                        <option value=""><?= lang('choose_days') ?></option>
                        <option value="monday" <?php if(in_array('monday',$days)) { echo 'selected="selected"';} ?>><?= lang('monday') ?></option>
                        <option value="tuesday" <?php if(in_array('tuesday',$days)) { echo 'selected="selected"';} ?>><?= lang('tuesday') ?></option>
                        <option value="wednesday" <?php if(in_array('wednesday',$days)) { echo 'selected="selected"';} ?>><?= lang('wednesday') ?></option>
                        <option value="thursday" <?php if(in_array('thursday',$days)) { echo 'selected="selected"';} ?>><?= lang('thursday') ?></option>
                        <option value="friday" <?php if(in_array('friday',$days)) { echo 'selected="selected"';} ?>><?= lang('friday') ?></option>
                        <option value="saturday" <?php if(in_array('saturday',$days)) { echo 'selected="selected"';} ?>><?= lang('saturday') ?></option>
                        <option value="sunday" <?php if(in_array('sunday',$days)) { echo 'selected="selected"';} ?>><?= lang('sunday') ?></option>
                      </select>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
            </div>
          </fieldset>-->
            
            
            
            
            
            <!--<div class="form-group">
                <?= lang('discount', 'discount'); ?>
                <?= form_input('discount', set_value('discount', $customer_discounts->value), 'class="form-control tip numberonly" id="discount" required="required"'); ?>
            </div>-->
            <?php $index=0;if(!empty($customer_discounts->group)) :
            foreach($customer_discounts->group as $dis_val => $row) : ?>
            <div class="discount-container existing-discount col-lg-12">
                <div class="dis-row">
                    <div class="col-md-2" style="width:13%;top: 25px;position: relative;">
                        <div class="form-group">
                        <label style="padding:7px;">
                                <input type="checkbox" name="group[<?=$index?>][status]" id="activate-discount" <?php if($row->status==1){echo 'checked="checked"';} ?>>&nbsp&nbspActive
                            </label>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount', 'discount'); ?>
                        <input type="text" name="group[<?=$index?>][discount]" value="<?=$row->discount_val?>" class="form-control tip numberonly discount">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount_type', 'discount_type'); ?></br>
                        <select name="group[<?=$index?>][discount_type]" class="select">
                            <option value="percentage" <?php if($row->discount_type=="percentage") { echo 'selected="selected"';} ?>><?=lang('percentage')?></option>
                            <option value="amount"<?php if($row->discount_type=="amount") { echo 'selected="selected"';} ?>><?=lang('amount')?></option>
                        </select>
                    </div>
                </div>
                
                <?php if($index==0) : ?>
                    <!--<div class="col-md-2">
                    <div class="form-group" style="top: 27px;position: relative;"> 
                        
                        <label class="padding05">
                            <input type="checkbox" name="apply_all" id="apply-all" <?php if($customer_discounts->apply_all==1){echo 'checked="checked"';} ?>>Apply all
                        </label>
                        
                    </div>
                    </div>-->
                <?php endif; ?>
                <div class="<?php if($index!=0) {  ?>col-md-3<?php } else{ ?>col-md-3<?php }?>">
                    <div class="form-group" style="top: 34px;position: relative;">
                    <a href="" class="list-group">Select Groups</a>
                    <?php if($index==0) : ?>
                    <label class="padding05">
                            <!--<input type="checkbox" name="apply_all" id="apply-all" <?php if($customer_discounts->apply_all==1){echo 'checked="checked"';} ?>>&nbsp&nbspApply all-->
                    </label>
                     <?php endif; ?>
                    <?php if($index!=0) : ?>&nbsp&nbsp&nbsp<a href="" class="remove-discount" data-index="<?=$index?>">Remove</a><?php endif; ?>
                    
                    </div>
                </div>
                </div>
                <div class="dis-group-row">
                    <div class="recipe-group-list"  style="display:none;">
                        <ul class="level-1-menu">
                        <?php foreach($recipe_groups as $kk => $row_1) : ?>
                            <li class="level-1-menu-li">
                                <div class="level-1-menu-div">
                                <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>">
                                
                                <div class="category-name-container">
                                    <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][all]" value="<?=@$row_1->id?>" class="recipe-group" data-index="<?=$index?>">
                                  
                                    <label for="pos-door_delivery_bils" class="category-name padding05">
                                          &nbsp;<?=@$row_1->name?></label><span  class="subgroup_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></div>
                                
                                
                                <?php if(!empty($row_1->sub_category)) : ?>
                                    <ul class="level-2-menu" style="display: none;">
                                        <label class="subgroup-title">subgroups</label>
                                        <?php foreach($row_1->sub_category as $sk => $row_2) : ?>
                                        <li class="level-2-menu-li">
                                            <?php $subgroup_value = (isset($row->recipe_groups[$row_2->id]))?$row->recipe_groups[$row_2->id]:false; ?>
                                            <?php //echo '<pre>';print_R($subgroup_value); echo $subgroup_value->recipe_id;?>
                                            <?php
                                                $subgroup_recipes = @unserialize($subgroup_value->recipe_id);
                                                //echo '<pre>';print_R($subgroup_recipes);exit;
                                                //$item['id'] = $subgroup_recipes[0]['id'];
                                                //$item['days'] = unserialize($subgroup_recipes[217]['days']);
                                                //echo '<pre>';print_R($item);
                                            ?>
                                            <?php $sub_checked = ($subgroup_value && $subgroup_value->recipe_id===0 && $row_2->id==$subgroup_value->recipe_subgroup_id)?'checked="checked"':'';
                                            $sub_type_checked = ($subgroup_value && $subgroup_value->type=='excluded' && $row_2->id==$subgroup_value->recipe_subgroup_id)?'checked="checked"':'';
                                            ?>
                                            <div class="subgroup-strip">
                                            <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][id]" value="<?=@$row_2->id?>">
                                            <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][all]" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="<?=$index?>" <?=$sub_checked?>>
                                        <label for="pos-door_delivery_bils" class="subgroup-name padding05"><?=@$row_2->name?></label><span class="recipe_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span><label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][type]" value="excluded" class="subgroup-item-excluded skip" data-index="<?=$index?>" <?=$sub_type_checked?>>
                                        <?=@lang('excluded')?></label>
                                            </div>
                                            <?php if(!empty($row_2->recipes)) : ?>
                                                <ul class="level-3-menu" style="display: none;">
                                                    <div class="items-title">items</div>
                                                    <?php foreach($row_2->recipes as $rk => $row_3) : ?>
                                                    <?php $subgroup_value = (isset($row->recipe_groups[$row_2->id]))?$row->recipe_groups[$row_2->id]:false; ?>
                                                    <?php $subgroup_recipe_id = @array_column($subgroup_recipes, 'id'); $recipe_checked = ($subgroup_value && $row_2->id==$subgroup_value->recipe_subgroup_id && ($subgroup_value->recipe_id===0 || in_array($row_3->id,$subgroup_recipe_id)) )?'checked="checked"':'';?>
                                                    <li>
                                                        <label for="pos-door_delivery_bils" class="padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][recipes][]" value="<?=@$row_3->id?>" class="recipe-item" data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>" <?=$recipe_checked?>> <?=@$row_3->name?></label>
                                                        <?php $days = array();$days = (isset($subgroup_recipes[$row_3->id]['days']))?@unserialize($subgroup_recipes[$row_3->id]['days']):array();  ?>
                                                        <div class="recipe-item-days-div weekdays-selector">
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>" value="mon" class="recipe-item-days weekday skip" <?php if(isset($days['mon'])) {echo 'checked="checked"';} ?>  data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>">M</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>" value="tue" class="recipe-item-days weekday skip" <?php if(isset($days['tue'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>">T</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>" value="wed" class="recipe-item-days weekday skip" <?php if(isset($days['wed'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>">W</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>" value="thu" class="recipe-item-days weekday skip" <?php if(isset($days['thu'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>">T</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>" value="fri" class="recipe-item-days weekday skip" <?php if(isset($days['fri'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>">F</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>" value="sat" class="recipe-item-days weekday skip" <?php if(isset($days['sat'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>">S</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>" value="sun" class="recipe-item-days weekday skip" <?php if(isset($days['sun'])) {echo 'checked="checked"';} ?>data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>">S</label>
</div>
                                                   
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <?php $index++;endforeach; else : ?>
            
                <div class="discount-container existing-discount col-lg-12">
                <div class="dis-row">
                    <div class="col-md-2" style="width:13%;top: 25px;position: relative;">
                        <div class="form-group">
                        <label style="padding:7px;">
                                <input type="checkbox" name="group[<?=$index?>][status]" id="activate-discount">&nbsp&nbspActive
                            </label>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount', 'discount'); ?>
                        <input type="text" name="group[<?=$index?>][discount]" value="" class="form-control tip numberonly discount">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('discount_type', 'discount_type'); ?></br>
                        <select name="group[<?=$index?>][discount_type]" class="select">
                            <option value="percentage"><?=lang('percentage')?></option>
                            <option value="amount"><?=lang('amount')?></option>
                        </select>
                    </div>
                </div>
                
                <?php if($index==0) : ?>
                    <!--<div class="col-md-2">
                    <div class="form-group" style="top: 27px;position: relative;"> 
                        
                        <label class="padding05">
                            <input type="checkbox" name="apply_all" id="apply-all" <?php if($customer_discounts->apply_all==1){echo 'checked="checked"';} ?>>Apply all
                        </label>
                        
                    </div>
                    </div>-->
                <?php endif; ?>
                <div class="<?php if($index!=0) {  ?>col-md-3<?php } else{ ?>col-md-3<?php }?>">
                    <div class="form-group" style="top: 34px;position: relative;">
                    <a href="" class="list-group">Select Groups</a>
                    <?php if($index==0) : ?>
                   <!-- <label class="padding05">
                            <input type="checkbox" name="apply_all" id="apply-all" >&nbsp&nbspApply all
                    </label>-->
                     <?php endif; ?>
                    <?php if($index!=0) : ?>&nbsp&nbsp&nbsp<a href="" class="remove-discount" data-index="<?=$index?>">Remove</a><?php endif; ?>
                    
                    </div>
                </div>
                </div>
                <div class="dis-group-row">
                    <div class="recipe-group-list"  style="display:none;">
                        <ul class="level-1-menu">
                        <?php foreach($recipe_groups as $kk => $row_1) : ?>
                            <li class="level-1-menu-li">
                                <div class="level-1-menu-div">
                               <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>">
                                <div class="category-name-container">
                                    <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>" class="recipe-group" data-index="<?=$index?>">
                                    <label for="pos-door_delivery_bils" class="category-name padding05">
                                        &nbsp;<?=@$row_1->name?></label><span class="subgroup_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></div>
                              
                                <?php if(!empty($row_1->sub_category)) : ?>
                                    <ul class="level-2-menu">
                                        <label class="subgroup-title">subgroups</label>
                                        <?php foreach($row_1->sub_category as $sk => $row_2) : ?>
                                        <li  class="level-2-menu-li">
                                            <div class="subgroup-strip">
                                            <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][id]" value="<?=@$row_2->id?>">
                                            <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][all]" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="<?=$index?>">
                                            <label for="pos-door_delivery_bils" class="subgroup-name padding05"><?=@$row_2->name?></label><span class="recipe_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span><label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05" style="display: none;"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][type]" value="excluded" class="subgroup-item-excluded skip" data-index="<?=$index?>">
                                        <?=@lang('excluded')?></label>
                                            </div>
                                            
                                            
                                            <?php if(!empty($row_2->recipes)) : ?>
                                                <ul class="level-3-menu" style="display: none;">
                                                    <div class="items-title">items</div>
                                                    <?php foreach($row_2->recipes as $rk => $row_3) : ?>
                                                    <li>
                                                        <label for="pos-door_delivery_bils" class="padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][recipes][]" value="<?=@$row_3->id?>" class="recipe-item" data-index="<?=$index?>">
                                                    <?=@$row_3->name?></label>
                                                        <div class="recipe-item-days-div weekdays-selector">
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>" value="mon" class="recipe-item-days weekday skip" <?php if(isset($days['mon'])) {echo 'checked="checked"';} ?>  data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-mon'?>">M</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>" value="tue" class="recipe-item-days weekday skip" <?php if(isset($days['tue'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-tue'?>">T</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>" value="wed" class="recipe-item-days weekday skip" <?php if(isset($days['wed'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-wed'?>">W</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>" value="thu" class="recipe-item-days weekday skip" <?php if(isset($days['thu'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-thu'?>">T</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>" value="fri" class="recipe-item-days weekday skip" <?php if(isset($days['fri'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-fri'?>">F</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>" value="sat" class="recipe-item-days weekday skip" <?php if(isset($days['sat'])) {echo 'checked="checked"';} ?> data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sat'?>">S</label>
  <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][days][<?=@$row_3->id?>][]" id="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>" value="sun" class="recipe-item-days weekday skip" <?php if(isset($days['sun'])) {echo 'checked="checked"';} ?>data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>" />
  <label for="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id.'-sun'?>">S</label>
</div>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
                <?php endif; ?>
            
            
            
            
            
            <div style="clear: both;height: 10px;"></div>

                    <div class="form-group col-lg-12">
                        <button type="button" id="add-more" class="btn btn-primary">Add More</button>
                        <?php echo form_submit('update_customer_discount', $this->lang->line("update_customer_discount"), 'class="btn btn-primary"'); ?>
                    </div>

                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>





<script>
    $(document).ready(function(){
        $appliedGroup = '';//'<?=@json_encode($customer_discounts->r_group)?>';
        $apply_all = '<?=$customer_discounts->apply_all?>';
        $dis_applied = {};$onloadExistingDis = {};
        $disindex = 0;
        //if($apply_all!=1){
        //  $.each($appliedGroup,function(i,v){
        //  
        //    $.each(v,function($i,$v){
        //        $onloadExistingDis[$v] = $disindex;
        //        $dis_applied[$v] = $disindex;
        //    });
        //    $disindex++;
        //  });  
        //}else{
        //    $('#add-more').hide();
        //    $('.recipe-group-list').show();
        //}
        
        $('.level-2-menu-li').each(function(n,v){
            $obj1 = $(this);
            $checkbox = $obj1.find('.level-3-menu .recipe-item').length;
            $checkbox_checked = $obj1.find('.level-3-menu .recipe-item:checked').length;
            if ($checkbox!=0 && $checkbox == $checkbox_checked) {
               $obj1.find('.recipe-subgroup').iCheck('check');
            }
        })
        $('.recipe-item-days:checked').each(function(n,v){
            $obj1 = $(this);
            $d_val = $obj1.closest('li').find('.recipe-item').val();
            $day_val = $obj1.val();
            $d_index = $obj1.attr('data-index');
            $array_key = $d_val+'-'+$day_val;
            $dis_applied[$array_key] = $d_index;
        });
        
        
        
        console.log($dis_applied)

       
      $recipeGroups = <?=$recipe_groups_json?>;
      
     // $(document).on('ifChanged','.icheckbox_square-blue input.recipe-group', function (e) {
     // //$(".icheckbox_square-blue input").on('ifChanged', function (e) {
     //   $this = $(this);
     //   if (($this).is(':checked')) {
     //       $val = $this.val();
     //       $index = $this.attr('data-index');
     //       $dis_applied[$val] = $index;
     //   }else{
     //       $val = $this.val();
     //       delete $dis_applied[$val];
     //   }
     //   console.log($(".discount-container:first .recipe-group").length);
     //   console.log('/'+$(".discount-container:first .recipe-group:checked").length);
     //   if ($(".discount-container:first .recipe-group").length==$(".discount-container:first .recipe-group:checked").length) {
     //       $('#apply-all').iCheck('check');
     //   }else{
     //       $('#apply-all').iCheck('uncheck');
     //   }
     //   
     //});
    
    $(document).on('ifChanged','.icheckbox_square-blue input.recipe-group', function (e) {
        $obj = $(this);
        console.log($obj)
        var isChecked = e.currentTarget.checked;
                            
        if (isChecked == true) {
            if (!$obj.closest('.level-1-menu-li').find('.level-2-menu').is(':visible')) {
                $obj.closest('.level-1-menu-li').find('.level-2-menu').show();
                $obj.closest('.level-1-menu-li').find('.subgroup_hide_show i').addClass('fa-minus-circle');
            }
            $obj.closest('li').find('.recipe-subgroup,.recipe-item').iCheck('check');
            
            //setTimeout(function(){
                //$obj.closest('li').find('input.recipe-item-days').attr('checked','checked');
            //},200);
            //alert($obj.closest('li .level-2-menu li').find('.level-3-menu input.recipe-item-days)').length);
            
            
        }else{
            $all_r_len = $obj.closest('li').find('.recipe-subgroup').length;
            $c_r_len = $obj.closest('li').find('.recipe-subgroup:checked').length;
            if ($all_r_len==$c_r_len) {
                
                $obj.closest('li').find('.recipe-subgroup,.recipe-item').iCheck('uncheck');
               // setTimeout(function(){
               // $obj.closest('li').find('input.recipe-item-days').attr('checked',false);
                //},200);
            }
        }
    });
    $(document).on('ifChanged','.icheckbox_square-blue input.recipe-subgroup', function (e) {
        $obj = $(this);
        //console.log($obj)
        var isChecked = e.currentTarget.checked;
                           // alert(6)
        if (isChecked == true) {
            if (!$obj.closest('.level-2-menu-li').find('.level-3-menu').is(':visible')) {
                $obj.closest('.level-2-menu-li').find('.level-3-menu').show();
                $obj.closest('.level-2-menu-li').find('.recipe_hide_show i').addClass('fa-minus-circle');
            }
            $obj.closest('li').find('input.recipe-item-days:not(.disabled-day)').attr('checked','checked');
            $obj.closest('li').find('.recipe-item').iCheck('check');
            
        }else{
            $all_len = $obj.closest('li').find('.recipe-item').length;
            $c_len = $obj.closest('li').find('.recipe-item:checked').length;
            //console.log('sub:'+$all_len+'=='+$c_len)
            if ($all_len==$c_len) {
                $obj.closest('li').find('.recipe-item').iCheck('uncheck');
                //$obj.closest('li').find('input.recipe-item-days').attr('checked',false);
            }
            
        }
        $obj.closest('li').find('input.recipe-item-days').each(function(){
            applied_dis_days($(this));
        });
        $checkbox = $obj.closest('li.level-1-menu-li ul').find('input[type="checkbox"]:not(".skip")').length;
        $checked =false;
        $checked = $obj.closest('li.level-1-menu-li ul').find('input[type="checkbox"]:checked:not(".skip")').length;
        console.log('group:'+$checkbox+'=='+$checked)
        if ($checkbox == $checked) {
            $obj.closest('li.level-1-menu-li').find('.recipe-group').iCheck('check');
        }else{
            $obj.closest('li.level-1-menu-li').find('.recipe-group').iCheck('uncheck');
        }        
    });
     function applied_dis_days($dayobj) {
        $this = $dayobj;
        $d_val = $this.closest('li').find('.recipe-item').val();
        $day_val = $this.val();
        $val = $d_val+'-'+$day_val;
        if (($this).is(':checked')) {
            //$val = $this.val();
            $this.next('label').css({background: '#2AD705',color: '#ffffff'});
            $index = $this.attr('data-index');
            $dis_applied[$val] = $index;
        }else{
            //$val = $this.val();
            $this.next('label').css({background: '#dddddd',color: '#000000'});
            delete $dis_applied[$val];
        }
    }
    $(document).on('ifChanged','.icheckbox_square-blue input.recipe-item', function (e) {
        $obj = $(this);
        var isChecked = e.currentTarget.checked;
        
        //alert(5)
        if (isChecked) {
           $obj.closest('li').find('input.recipe-item-days:not(.disabled-day)').attr('checked','checked');
            $obj.closest('li').find('input.recipe-item-days:not(.disabled-day)').attr('disabled',false);
        }else{
            $obj.closest('li').find('input.recipe-item-days:not(.disabled-day)').attr('checked',false);
            $obj.closest('li').find('input.recipe-item-days:not(.disabled-day)').attr('disabled',true);
        }
        $obj.closest('li').find('input.recipe-item-days').each(function(){
            applied_dis_days($(this));
        });
        //$all =  $obj.closest('li>ul.level-3-menu').find('input[type="checkbox"]').length;              
       // $checked = $obj.closest('li>ul.level-3-menu').find('input[type="checkbox"]:checked').length;
       $all = $obj.closest('li.level-2-menu-li').find('.level-3-menu input[type="checkbox"]:not(.recipe-item-days)').length;
        $checked = $obj.closest('li.level-2-menu-li').find('.level-3-menu input[type="checkbox"]:checked:not(.recipe-item-days)').length;
        console.log('length:'+$all+'=='+$checked)
        if ($all==$checked) {
           // $obj.closest('li').find('input.recipe-item-days').attr('checked','checked');
            $obj.closest('li.level-2-menu-li').find('.recipe-subgroup').iCheck('check');
            
        }else{
            // $obj.closest('li').find('input.recipe-item-days').attr('checked',false);
            $obj.closest('li.level-2-menu-li').find('.recipe-subgroup').iCheck('uncheck');
        }  
    });
     
    $(document).on('click','.recipe_hide_show',function(){
        $obj = $(this);
        console.log(55)
        $obj.find('i').toggleClass('fa-minus-circle');
        $obj.closest('li.level-2-menu-li').find('.level-3-menu').toggle();
    });
    
    $(document).on('click','.subgroup_hide_show',function(){
        $obj = $(this);
        //$(this).find('i').toggleClass('fa-minus-circle');
        //$(this).closest('li').find('.level-2-menu').toggle();
        $(this).toggleClass('opened');
        $(this).find('i').toggleClass('fa-minus-circle');
        $(this).closest('li').find('.level-2-menu').toggle();
        if ($(this).closest('li').find('.level-2-menu').is(':visible')) {
            $('.subgroup_hide_show.opened').not(this).closest('li').find('.level-2-menu').hide();
            $('.subgroup_hide_show.opened').not(this).find('i').removeClass('fa-minus-circle');
        }
        
    });
    $(document).on('ifChanged','.icheckbox_square-blue input#apply-all', function (e) {
        $this = $(this);
        if (($this).is(':checked')) {
            $(".discount-container").not(":first,.existing-discount").remove();
            $(".discount-container.existing-discount").not(":first").hide();
            $('#add-more').hide();
            $('.recipe-group-list:first .icheckbox_square-blue').each(function(e){
                 $(this).iCheck('enable');$(this).iCheck('check');
            });
            $(".recipe-group-list").hide();
            $(".discount-container:first .recipe-group-list").show();
        }else{
            //$(".discount-container").show();
            $(".discount-container.existing-discount").not(":first").show();
            $('#add-more').show();
            if ($(".discount-container:first .recipe-group").length==$(".discount-container:first .recipe-group:checked").length) {
                $('.recipe-group-list:first input').iCheck('uncheck');
            }
            $('.recipe-group-list:first input').each(function(){
                 $val = $this.val();
                 $index = $this.attr('data-index');
                 if($onloadExistingDis[$val] != undefined && $onloadExistingDis[$val]==$index){
                 $(this).iCheck('check');
                }
            });
        }
    });

    $discontainerLength = $('.discount-container').length;
    $('#add-more').click(function(e){
        e.preventDefault();
        $('.recipe-group-list').hide();
        $i = $discontainerLength++;
        $html = '<div class="discount-container col-lg-12">'+
                '<div class="dis-row">'+
                '<div class="col-md-2" style="width:13%"></div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<?= lang('discount', 'discount'); ?>'+
                        '<input type="text" name="group['+$i+'][discount]" value="" class="numberonly form-control tip numberonly discount">'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<div class="form-group">'+
                        '<?= lang('discount_type', 'discount_type'); ?></br>'+
                        '<select name="group['+$i+'][discount_type]" class="select discount_type">'+
                            '<option value="percentage"><?=lang('percentage')?></option>'+
                            '<option value="amount"><?=lang('amount')?></option>'+
                        '</select>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-3">'+
                '<div class="form-group" style="top: 27px;position: relative;"> '+
                    '<a href="" class="list-group">Select Groups</a>'+
                    '&nbsp&nbsp&nbsp<a href="" class="remove-discount" data-index="'+$i+'">Remove</a>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="dis-group-row">'+
                    '<div class="recipe-group-list"  style="display:none;">'+
                       '<ul class="level-1-menu">';
                        $.each($recipeGroups,function(n,val){
                            
                            $html += '<li class="level-1-menu-li">'+
                            '<div class="level-1-menu-div">'+
                            '<div class="category-name-container">'+
                                    
                                    ' <input type="hidden" name="group['+$i+'][recipe_group_id]['+n+'][id]" value="<?=@$row_1->id?>">'+
                                    '<input type="checkbox" name="group['+$i+'][recipe_group_id]['+n+'][all]" value="'+val.id+'" class="recipe-group" data-index="'+$i+'">&nbsp;'+
                                '<label class="padding05 category-name">'+val.name+
                                '</label>';
                                $html +='<span  class="subgroup_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span></div>';
                                $html +='<ul class="level-2-menu" style="display:none;">';
                                $html +='<label class="subgroup-title">subgroups</label>';
                                console.log()
                                    $.each(val.sub_category,function(sn,sval){
                                        $index = $i;
                                        $html += '<li  class="level-2-menu-li">'+
                                        '<div class="subgroup-strip">'+
                                            '<input type="hidden" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][id]" value="'+sval.id+'">'+
                                            
                                            '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][all]" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="'+$index+'">&nbsp;'+
                                        '<label for="pos-door_delivery_bils" class="subgroup-name padding05">'+sval.name+
                                        '</label>'+
                                        '<span class="recipe_hide_show"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>'+
                                        '<label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05">'+
                                            '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][type]" value="excluded" class="subgroup-item-excluded skip" data-index="'+$index+'">'+
                                        '<?=@lang('excluded')?>'+
                                        '</label>'+'</div>'+
                                                '<ul class="level-3-menu" style="display: none;"><div class="items-title">items</div>';
                                                    $.each(sval.recipes,function(rn,rval){
                                                        $dataIndex = $i+'-'+val.id+'-'+sval.id+'-'+rval.id;
                                                    $html += '<li>'+
                                                        '<label for="pos-door_delivery_bils" class="padding05">'+
                                                        '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][recipes][]" value="'+rval.id+'" class="recipe-item" data-index="'+$dataIndex+'">'+
                                                    rval.name+'</label>'+
                                                      '<div class="recipe-item-days-div weekdays-selector">'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-mon" value="mon" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-mon"/>'+
  '<label for="'+$dataIndex+'-mon">M</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-tue" value="tue" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-tue"/>'+
  '<label for="'+$dataIndex+'-tue">T</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-wed" value="wed" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-wed"/>'+
  '<label for="'+$dataIndex+'-wed">W</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-thu" value="thu" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-thu"/>'+
  '<label for="'+$dataIndex+'-thu">T</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-fri" value="fri" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-fri"/>'+
  '<label for="'+$dataIndex+'-fri">F</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-sat" value="sat" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-sat"/>'+
  '<label for="'+$dataIndex+'-sat">S</label>'+
  '<input type="checkbox" name="group['+$index+'][recipe_group_id]['+n+'][sub_category]['+sn+'][days]['+rval.id+'][]" id="'+$dataIndex+'-sun" value="sun" class="recipe-item-days weekday skip" data-index="'+$dataIndex+'-sun"/>'+
  '<label for="'+$dataIndex+'-sun">S</label>'+
'</div>'+
                                                    '</li>';
                                                    });
                                               $html += '</ul>'+
                                        '</li>';
                                    });
                                $html +='</ul>';
                                
                                
                            $html +='</div></li>';
                        });
                        $html += '</ul>'+
                    '</div>'+
                '</div>'+
            '</div>';
            
        //$($html).insertAfter('.discount-container').last();
        $('.discount-container:last').after($html);
        setTimeout(function() {
            $('.discount-container:last input[type="checkbox"]:not(.skip)').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
            
        },100);
        $('.discount-container:last select.discount_type').select2({});
    });
    //$('.recipe-item-days').iCheck({
    //        checkboxClass: 'icheckbox_square-blue',
    //        radioClass: 'iradio_square-blue',
    //        increaseArea: '10%'
    //    });
     $(document).on('click','#edit-cus-dis-form input[type="submit"]',function(e){
        $index = 1;
        $dis_error = [];
        $group_error = [];
        $('.discount-container:visible').each(function(i,v){
            if($(this).find('input[type="text"].discount').val()=='' || $(this).find('input[type="text"].discount').val()==0){
                $dis_error.push($index);
            }
            if ($(this).find('.recipe-group:checked').length==0) {
                $group_error.push($index);
            }
            $index++;
        });return true;
        if ($dis_error.length>0 || $group_error.length>0) {
            //e.preventDefault(); e.stopImmediatePropagation();
            //bootbox.alert('Discount field should not be empty. Please select atleast one group or else remove');
            //$('#edit-cus-dis-form input[type="submit"]').attr('disabled',false);
            //return false;
        }else{
            //console.log(55)
            //$('#edit-cus-dis-form').submit();
            return true;
        }
        return false;
        
    });
    //$('input[type="checkbox"]').iCheck({
    //    default_insert: 'Press me!',
    //})
     var a = '<?=(isset($customer_discounts->conditions))?count($customer_discounts->conditions):1?>';
$('#addCondition').click(function () {
	    if (a==3) {
                return false;
           }
		var html = '<div class="well col-lg-12"> <div class="form-group col-lg-12"> <div class="controls"> <select name="condition['+a+'][condition_method]" id="condition_method_' + a + '" class="form-control select condition_method" > <option value="condition_date">Date</option><option value="condition_time">Time</option> <option value="condition_days">Days of Week</option> </select> </div></div><div class="form-group col-lg-6 date_div"> <div class="controls "> <input type="text" name="condition['+a+'][from_date]" class="form-control datetime" placeholder="From Date & Time" id="from_date_'+a+'" required="required"> </div></div><div class="form-group col-lg-6 date_div"> <div class="controls"> <input type="text" name="condition['+a+'][to_date]" class="form-control datetime" placeholder="To Date & Time" id="to_date_'+a+'" required="required"> </div></div><div class="form-group  col-lg-6 time_div" style="display: none"><div class="controls "><input type="text" name="condition['+a+'][from_time]" class="form-control time" placeholder="From  Time" id="from_time" required="required"></div></div><div class="form-group  col-lg-6 time_div" style="display: none"><div class="controls"><input type="text" name="condition['+a+'][to_time]" class="form-control time" placeholder="To  Time" id="to_time" required="required"></div></div><div class="form-group col-lg-12 condition_days" style="display:none"> <div class="controls"> <select name="condition['+a+'][condition_days][]" id="condition_days_'+a+'" multiple class="form-control select" > <option value="monday">Monday</option> <option value="tuesday">Tuesday</option> <option value="wednesday">Wednesday</option> <option value="thursday">Thursday</option> <option value="friday">Friday</option> <option value="saturday">Saturday</option> <option value="sunday">Sunday</option> </select> </div></div><button type="button" class="btn btn-primary pull-right btn-xs deleteCondition"><i class="fa fa-trash-o"></i></button></div>';
		
		$('#condition').append(html);
		
		$('#condition_method_' + a).select2();
		$('#condition_type_' + a).select2();
		$('#condition_days_' + a).select2();
		
		a++;
});
$("body").on('click','.deleteCondition', function(){
	$(this).closest('.well').remove();
})
$('.recipe-group').each(function(){
    $obj = $(this);
     $checkbox = $obj.closest('li').find('.recipe-subgroup').length + $obj.closest('li').find('.recipe-item').length;
     console.log($checkbox)
        $checked =false;
        //$checked = $obj.closest('li.level-1-menu-li ul').find('input[type="checkbox"]:checked:not(".skip")').length;
        $checked = $obj.closest('li').find('.recipe-subgroup:checked').length + $obj.closest('li').find('.recipe-item:checked').length;
        console.log('group:'+$checkbox+'=='+$checked)
        if ($checkbox == $checked) {
            $obj.closest('li.level-1-menu-li').find('.recipe-group').iCheck('check');
        }else{
            $obj.closest('li.level-1-menu-li').find('.recipe-group').iCheck('uncheck');
        }      
});

    });
</script>
<style>
.recipe-group-list ul.level-1-menu li,.recipe-group-list ul.level-2-menu li {
    list-style: none;
   /* float: left;*/
    position: relative;
    /*margin-right: 20px;*/
    /*width: 200px;*/
}
.recipe-group-list ul.level-3-menu li {
    list-style: none;
    float: left !important;
    position: relative;
    margin-right: 20px;
    /*min-width: 200px !important;*/
    width:47%
}
.recipe-group-list ul.level-1-menu>li , .recipe-group-list ul.level-2-menu>li{
  clear: both;
}
.level-2-menu li{
    text-indent:15px;
}
.level-3-menu li{
    text-indent:25px;
}
.level-1-menu-li{
    padding: 5px;
    /*min-height: 130px;*/
}
.level-1-menu-div{
    /*min-height: 130px;*/
    background-color: #f8f6f6;
    border-radius: 10px;
    overflow: hidden;
    /*padding: 10px 10px 10px 10px;*/
   /* padding: 5px;*/
    position: relative;
    box-shadow: inset 0 3px 3px -3px rgba(0, 0, 0, 0.3);
    background: linear-gradient(181deg, #ffffff 0%, #ececec 100%);
}
/*.weekdays-selector input {
  display: none!important;
}

.weekdays-selector input[type=checkbox] + label {
  display: inline-block;
  border-radius: 6px;
  background: #dddddd;
  height: 20px;
  width: 20px;
  line-height:40px;
  
  text-align: center;
  cursor: pointer;
  font-size:10px
}
*/
.weekdays-selector{
    width:198px;
    float: right;
    text-indent: 1px;

}
.weekdays-selector input {
  display: none!important;
  margin-right: 3px;
}

.weekdays-selector label {
      display: inline-block;
    border-radius: 6px;
    background: #dddddd;
    height: 21px;
    width: 17px;
    margin-right: 3px;
    line-height: 23px;
    text-align: center;
    cursor: pointer;
}

.weekdays-selector input[type=checkbox]:checked + label {
  background: #2AD705;
  color: #ffffff;
}
/*.weekdays-selector input[type=checkbox]:checked + label {
  background: #2AD705;
  color: #ffffff;
}*/
.subgroup_hide_show{
   float: right;
    position: relative;
    /* background: grey; */
    /* float: left; */
    /* top: 24px; */
    right: 0.5%;
    top: 3px;
    font-size: 20px;
}
.level-1-menu-div .category-name-container{background: grey;    padding: 4px; cursor: pointer;
/*        width: 100%;
    
    height: 32px;
    top: -4px;
    position: relative;*/
}
.subgroup-item-excluded-label{
    display: none;
}
.disabled-day + label{
    background: #d31919 !important;
  color: #ffffff !important;
}
.subgroup-title{
    text-indent: 5px;
    font-weight: bold;
    text-transform: uppercase;
}
.items-title{
   padding-left: 2%;
    font-weight: bold;
    text-transform: uppercase;
}
.subgroup-strip{
    padding: 4px;
        margin: 9px;
     background: linear-gradient(181deg, #ffffff 0%, #a0a0a0fc 100%);
}
.recipe_hide_show{
    float: right;
    font-size: 20px;
}
.weekday-disabled + label{
    background: #817e7a !important;
    color: #ffffff !important;
}
/*.datetimepicker:nth-child(3) .table-condensed thead .switch,.datetimepicker:nth-child(3) .table-condensed thead  .prev, .datetimepicker:nth-of-type(3) .table-condensed thead .next
{     
     visibility:  hidden !important; 
}*/
</style>

