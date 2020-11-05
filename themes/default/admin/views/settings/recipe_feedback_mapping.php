<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/feedback_recipe_mapping.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('recipe_feedback_mapping'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'add-cus-dis-form');
        echo admin_form_open("system_settings/recipe_feedback_mapping/", $attrib); ?>

              
            <?php $index = 0 ; ?>
            
                <div class="discount-container col-lg-12">
                
                <div class="dis-group-row">
                    <div class="recipe-group-list">
                        <ul class="level-1-menu">
                        <?php foreach($recipe_groups as $kk => $row_1) : ?>
                            <li class="level-1-menu-li">
                                <div class="level-1-menu-div">
                               <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>">
                                <div class="category-name-container">
                                    <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][id]" value="<?=@$row_1->id?>" class="recipe-group" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="category-name padding05">
                                    &nbsp;<?=@$row_1->name?></label><span class="subgroup_hide_show"><i class="fa fa-plus-circle fa-minus-circle" aria-hidden="true"></i></span></div>
                                
                                <?php if(!empty($row_1->sub_category)) : ?>
                                    <ul class="level-2-menu">
                                        <label class="subgroup-title">subgroups</label>
                                        <?php foreach($row_1->sub_category as $sk => $row_2) : ?>
                                        <li  class="level-2-menu-li">
                                            <div class="subgroup-strip">
                                            <input type="hidden" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][id]" value="<?=@$row_2->id?>">
                                            <input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][all]" value="<?=@$row_2->id?>" class="recipe-subgroup" data-index="<?=$index?>"><label for="pos-door_delivery_bils" class="subgroup-name padding05">
                                        <?=@$row_2->name?></label><span class="recipe_hide_show"><i class="fa fa-plus-circle fa-minus-circle" aria-hidden="true"></i></span><label for="pos-door_delivery_bils" class="subgroup-item-excluded-label padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][type]" value="excluded" class="subgroup-item-excluded skip" data-index="<?=$index?>">
                                        <?=@lang('excluded')?></label>
                                            </div>
                                            <?php if(!empty($row_2->recipes)) : ?>
                                                <ul class="level-3-menu"><div class="items-title">items</div>
                                                    <?php foreach($row_2->recipes as $rk => $row_3) :
                                                    $checked = (in_array($row_3->id,$mapped_rids))?'checked="checked"':'';
                                                    
                                                    ?>
                                                    <li>
                                                        <label for="pos-door_delivery_bils" class="padding05"><input type="checkbox" name="group[<?=$index?>][recipe_group_id][<?=$kk?>][sub_category][<?=$sk?>][recipes][]" value="<?=@$row_3->id?>" class="recipe-item" data-index="<?=@$index.'-'.$row_1->id.'-'.@$row_2->id.'-'.@$row_3->id?>" <?=$checked?>>
                                                    <?=@$row_3->name?></label>
                                                        
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
               
            
            
            
            
            
            <div style="clear: both;height: 10px;"></div>

                    <div class="form-group col-lg-12" style="padding-right: 21px;">
                        <?php echo form_submit('update', $this->lang->line("update"), 'class="btn btn-primary" style="float:right"'); ?>
                    </div>

                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>



<?php //$modal_js ?>
<script>
$(".numberonly").keypress(function (event){
    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  
    });
</script>
<script>

</script>
<style>
.recipe-group-list ul.level-1-menu li,.recipe-group-list ul.level-2-menu li {
    list-style: none;
   /* float: left;*/
    position: relative;
   /* margin-right: 20px;*/
    /*width: 200px;*/
}
.recipe-group-list ul.level-3-menu li {
    list-style: none;
    float: left !important;
    position: relative;
    margin-right: 20px;
    /*min-width: 200px !important;*/
    width:30%
}
.recipe-group-list ul.level-1-menu>li , .recipe-group-list ul.level-2-menu>li{
  clear: both;
}
.level-2-menu{
    text-indent:15px;
}
.level-3-menu{
    text-indent:25px;
}
.level-1-menu-li{
    padding: 5px;
    /*min-height: 130px;*/
}
.level-1-menu-div{
    background-color: #f8f6f6;
    border-radius: 10px;
    overflow: hidden;
    /*padding: 10px 10px 10px 10px;*/
    /*padding: 5px;*/
    position: relative;
    box-shadow: inset 0 3px 3px -3px rgba(0, 0, 0, 0.3);
    background: linear-gradient(181deg, #ffffff 0%, #ececec 100%);
}
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
.level-1-menu-div .category-name-container{
    background: grey;
        padding: 4px;
        cursor: pointer;
    /*width: 100%;
    
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
</style>

