<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?= $assets ?>styles/jquery-ui.css">
  <script src="<?= $assets ?>js/jquery-ui.js"></script>
<?php
$wm = array('0' => lang('no'), '1' => lang('yes'));
$ps = array('0' => lang("disable"), '1' => lang("enable"));
?>

<script>
    $(document).ready(function () {
        <?php if(isset($message)) { echo 'localStorage.clear();'; } ?>
        var timezones = <?= json_encode(DateTimeZone::listIdentifiers(DateTimeZone::ALL)); ?>;
        $('#timezone').autocomplete({
            source: timezones
        });
        if ($('#protocol').val() == 'smtp') {
            $('#smtp_config').slideDown();
        } else if ($('#protocol').val() == 'sendmail') {
            $('#sendmail_config').slideDown();
        }
        $('#protocol').change(function () {
            if ($(this).val() == 'smtp') {
                $('#sendmail_config').slideUp();
                $('#smtp_config').slideDown();
            } else if ($(this).val() == 'sendmail') {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideDown();
            } else {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideUp();
            }
        });
        $('#overselling').change(function () {
            if ($(this).val() == 1) {
                if ($('#accounting_method').select2("val") != 2) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#accounting_method').select2("val", '2');
                }
            }
        });
        $('#accounting_method').change(function () {
            var oam = <?=$Settings->accounting_method?>, nam = $(this).val();
            if (oam != nam) {
                bootbox.alert('<?=lang('accounting_method_change_alert')?>');
            }
        });
        $('#accounting_method').change(function () {
            if ($(this).val() != 2) {
                if ($('#overselling').select2("val") == 1) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#overselling').select2("val", 0);
                }
            }
        });
        $('#item_addition').change(function () {
            if ($(this).val() == 1) {
                bootbox.alert('<?=lang('product_variants_feature_x')?>');
            }
        });
        var sac = $('#sac').val()
        if(sac == 1) {
            $('.nsac').slideUp();
        } else {
            $('.nsac').slideDown();
        }
        $('#sac').change(function () {
            if ($(this).val() == 1) {
                $('.nsac').slideUp();
            } else {
                $('.nsac').slideDown();
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('system_settings'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="<?= admin_url('system_settings/paypal') ?>" class="toggle_up"><i
                            class="icon fa fa-paypal"></i><span
                            class="padding-right-10"><?= lang('paypal'); ?></span></a></li>
                <li class="dropdown"><a href="<?= admin_url('system_settings/skrill') ?>" class="toggle_down"><i
                            class="icon fa fa-bank"></i><span class="padding-right-10"><?= lang('skrill'); ?></span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("system_settings", $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('site_config') ?></legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("site_name", "site_name"); ?>
                                    <?= form_input('site_name', $Settings->site_name, 'class="form-control tip" id="site_name"  required="required"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("language", "language"); ?>
                                    <?php
                                    $lang = array(
                                        //'arabic'                    => 'Arabic',
                                        'english'                   => 'English',
                                        /*'german'                    => 'German',
                                        'portuguese-brazilian'      => 'Portuguese (Brazil)',
                                        'simplified-chinese'        => 'Simplified Chinese',
                                        'spanish'                   => 'Spanish',
                                        'thai'                      => 'Thai',
                                        'traditional-chinese'       => 'Traditional Chinese',
                                        'turkish'                   => 'Turkish',
                                        'vietnamese'                => 'Vietnamese',*/
										'khmer'						=> 'Khmer',
                                    );
                                    echo form_dropdown('language', $lang, $Settings->language, 'class="form-control tip" id="language" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="captcha"><?= lang("QSR_mode"); ?></label>
        
                                    <div class="controls">
                                        <?php
                                        echo form_dropdown('qsr', $ps, $Settings->qsr, 'id="qsr" class="form-control tip" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="currency"><?= lang("default_currency"); ?></label>

                                    <div class="controls"> <?php
                                        foreach ($currencies as $currency) {
                                            $cu[$currency->id] = $currency->code;
                                        }
                                        echo form_dropdown('currency', $cu, $Settings->default_currency, 'class="form-control tip" id="currency" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="first_level"><?= lang("escalation_level_one"); ?></label>

                                    <div class="controls"> <?php
                                        foreach ($level as $first_level) {
                                            $fl[$first_level->id] = $first_level->name;
                                        }
                                        echo form_dropdown('first_level', $fl, $Settings->first_level, 'class="form-control tip" id="first_level" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="second_level"><?= lang("escalation_level_two"); ?></label>

                                    <div class="controls"> <?php
                                        foreach ($level as $second_level) {
                                            $sl[$second_level->id] = $second_level->name;
                                        }
                                        echo form_dropdown('second_level', $sl, $Settings->second_level, 'class="form-control tip" id="second_level" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("accounting_method", "accounting_method"); ?>
                                    <?php
                                    $am = array(0 => 'FIFO (First In First Out)', 1 => 'LIFO (Last In First Out)', 2 => 'AVCO (Average Cost Method)');
                                    echo form_dropdown('accounting_method', $am, $Settings->accounting_method, 'class="form-control tip" id="accounting_method" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="email"><?= lang("default_email"); ?></label>

                                    <?= form_email('email', $Settings->default_email, 'class="form-control tip" required="required" id="email"'); ?>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="customer_group"><?= lang("default_customer_group"); ?></label>
                            <?php
                                foreach ($customer_groups as $customer_group) {
                                    $pgs[$customer_group->id] = $customer_group->name;
                                }
                                echo form_dropdown('customer_group', $pgs, $Settings->customer_group, 'class="form-control tip" id="customer_group" style="width:100%;" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="price_group"><?= lang("default_price_group"); ?></label>
                            <?php
                                foreach ($price_groups as $price_group) {
                                    $cgs[$price_group->id] = $price_group->name;
                                }
                                echo form_dropdown('price_group', $cgs, $Settings->price_group, 'class="form-control tip" id="price_group" style="width:100%;" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('maintenance_mode', 'mmode'); ?>
                            <div class="controls">  <?php
                                echo form_dropdown('mmode', $wm, (isset($_POST['mmode']) ? $_POST['mmode'] : $Settings->mmode), 'class="tip form-control" required="required" id="mmode" style="width:100%;"');
                                ?> </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="theme"><?= lang("theme"); ?></label>

                            <div class="controls">
                                <?php
                                $themes = array(
                                    'default' => 'Default'
                                );
                                echo form_dropdown('theme', $themes, $Settings->theme, 'id="theme" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="rtl"><?= lang("rtl_support"); ?></label>

                            <div class="controls">
                                <?php
                                echo form_dropdown('rtl', $ps, $Settings->rtl, 'id="rtl" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="captcha"><?= lang("login_captcha"); ?></label>

                            <div class="controls">
                                <?php
                                echo form_dropdown('captcha', $ps, $Settings->captcha, 'id="captcha" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="disable_editing"><?= lang("disable_editing"); ?></label>
                            <?= form_input('disable_editing', $Settings->disable_editing, 'class="form-control tip" id="disable_editing" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="rows_per_page"><?= lang("rows_per_page"); ?></label>
                            <?= form_input('rows_per_page', $Settings->rows_per_page, 'class="form-control tip" id="rows_per_page" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="dateformat"><?= lang("dateformat"); ?></label>

                            <div class="controls">
                                <?php
                                foreach ($date_formats as $date_format) {
                                    $dt[$date_format->id] = $date_format->js;
                                }
                                echo form_dropdown('dateformat', $dt, $Settings->dateformat, 'id="dateformat" class="form-control tip" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="timezone"><?= lang("timezone"); ?></label>
                            <?php
							$timezones = array(
			''					=> "Please select",
			'Pacific/Midway'       => "GMT-11:00",
			'US/Samoa'             => "GMT-11:00",
			'US/Hawaii'            => "GMT-10:00",
			'US/Alaska'            => "GMT-09:00",
			'US/Pacific'           => "GMT-08:00",
			'America/Tijuana'      => "GMT-08:00",
			'US/Arizona'           => "GMT-07:00",
			'US/Mountain'          => "GMT-07:00",
			'America/Chihuahua'    => "GMT-07:00",
			'America/Mazatlan'     => "GMT-07:00",
			'America/Mexico_City'  => "GMT-06:00",
			'America/Monterrey'    => "GMT-06:00",
			'Canada/Saskatchewan'  => "GMT-06:00",
			'US/Central'           => "GMT-06:00",
			'US/Eastern'           => "GMT-05:00",
			'US/East-Indiana'      => "GMT-05:00",
			'America/Bogota'       => "GMT-05:00",
			'America/Lima'         => "GMT-05:00",
			'America/Caracas'      => "GMT-04:30",
			'Canada/Atlantic'      => "GMT-04:00",
			'America/La_Paz'       => "GMT-04:00",
			'America/Santiago'     => "GMT-04:00",
			'Canada/Newfoundland'  => "GMT-03:30",
			'America/Buenos_Aires' => "GMT-03:00",
			'Greenland'            => "GMT-03:00",
			'Atlantic/Stanley'     => "GMT-02:00",
			'Atlantic/Azores'      => "GMT-01:00",
			'Atlantic/Cape_Verde'  => "GMT-01:00",
			'Africa/Casablanca'    => "GMT",
			'Europe/Dublin'        => "GMT",
			'Europe/Lisbon'        => "GMT",
			'Europe/London'        => "GMT",
			'Africa/Monrovia'      => "GMT",
			'Europe/Amsterdam'     => "GMT+01:00",
			'Europe/Belgrade'      => "GMT+01:00",
			'Europe/Berlin'        => "GMT+01:00",
			'Europe/Bratislava'    => "GMT+01:00",
			'Europe/Brussels'      => "GMT+01:00",
			'Europe/Budapest'      => "GMT+01:00",
			'Europe/Copenhagen'    => "GMT+01:00",
			'Europe/Ljubljana'     => "GMT+01:00",
			'Europe/Madrid'        => "GMT+01:00",
			'Europe/Paris'         => "GMT+01:00",
			'Europe/Prague'        => "GMT+01:00",
			'Europe/Rome'          => "GMT+01:00",
			'Europe/Sarajevo'      => "GMT+01:00",
			'Europe/Skopje'        => "GMT+01:00",
			'Europe/Stockholm'     => "GMT+01:00",
			'Europe/Vienna'        => "GMT+01:00",
			'Europe/Warsaw'        => "GMT+01:00",
			'Europe/Zagreb'        => "GMT+01:00",
			'Europe/Athens'        => "GMT+02:00",
			'Europe/Bucharest'     => "GMT+02:00",
			'Africa/Cairo'         => "GMT+02:00",
			'Africa/Harare'        => "GMT+02:00",
			'Europe/Helsinki'      => "GMT+02:00",
			'Europe/Istanbul'      => "GMT+02:00",
			'Asia/Jerusalem'       => "GMT+02:00",
			'Europe/Kiev'          => "GMT+02:00",
			'Europe/Minsk'         => "GMT+02:00",
			'Europe/Riga'          => "GMT+02:00",
			'Europe/Sofia'         => "GMT+02:00",
			'Europe/Tallinn'       => "GMT+02:00",
			'Europe/Vilnius'       => "GMT+02:00",
			'Asia/Baghdad'         => "GMT+03:00",
			'Asia/Kuwait'          => "GMT+03:00",
			'Africa/Nairobi'       => "GMT+03:00",
			'Asia/Riyadh'          => "GMT+03:00",
			'Asia/Tehran'          => "GMT+03:30",
			'Europe/Moscow'        => "GMT+04:00",
			'Asia/Baku'            => "GMT+04:00",
			'Europe/Volgograd'     => "GMT+04:00",
			'Asia/Muscat'          => "GMT+04:00",
			'Asia/Tbilisi'         => "GMT+04:00",
			'Asia/Yerevan'         => "GMT+04:00",
			'Asia/Kabul'           => "GMT+04:30",
			'Asia/Karachi'         => "GMT+05:00",
			'Asia/Tashkent'        => "GMT+05:00",
			'Asia/Kolkata'         => "GMT+05:30",
			'Asia/Kathmandu'       => "GMT+05:45",
			'Asia/Yekaterinburg'   => "GMT+06:00",
			'Asia/Almaty'          => "GMT+06:00",
			'Asia/Dhaka'           => "GMT+06:00",
			'Asia/Novosibirsk'     => "GMT+07:00",
			'Asia/Bangkok'         => "GMT+07:00",
			'Asia/Jakarta'         => "GMT+07:00",
			'Asia/Krasnoyarsk'     => "GMT+08:00",
			'Asia/Chongqing'       => "GMT+08:00",
			'Asia/Hong_Kong'       => "GMT+08:00",
			'Asia/Kuala_Lumpur'    => "GMT+08:00",
			'Australia/Perth'      => "GMT+08:00",
			'Asia/Singapore'       => "GMT+08:00",
			'Asia/Taipei'          => "GMT+08:00",
			'Asia/Ulaanbaatar'     => "GMT+08:00",
			'Asia/Urumqi'          => "GMT+08:00",
			'Asia/Irkutsk'         => "GMT+09:00",
			'Asia/Seoul'           => "GMT+09:00",
			'Asia/Tokyo'           => "GMT+09:00",
			'Australia/Adelaide'   => "GMT+09:30",
			'Australia/Darwin'     => "GMT+09:30",
			'Asia/Yakutsk'         => "GMT+10:00",
			'Australia/Brisbane'   => "GMT+10:00",
			'Australia/Canberra'   => "GMT+10:00",
			'Pacific/Guam'         => "GMT+10:00",
			'Australia/Hobart'     => "GMT+10:00",
			'Australia/Melbourne'  => "GMT+10:00",
			'Pacific/Port_Moresby' => "GMT+10:00",
			'Australia/Sydney'     => "GMT+10:00",
			'Asia/Vladivostok'     => "GMT+11:00",
			'Asia/Magadan'         => "GMT+12:00",
			'Pacific/Auckland'     => "GMT+12:00",
			'Pacific/Fiji'         => "GMT+12:00",
		);
		
							?>
                            <select name="timezone" class="form-control" id="timezone" required>
                            	<?php
								foreach($timezones as $key => $val){
									if(!empty($Settings->timezone) && $Settings->timezone == $key){
										$selected = "selected";
									}else{
										$selected = "";
									}
								?>
                                <option value="<?php echo $key.','.$val; ?>" <?php echo $selected; ?> ><?php echo $key; ?></option>
                                <?php
								}
								?>
                            </select>
                           
                            <?php /*?><?php
							
                           $timezone_identifiers = DateTimeZone::listIdentifiers();
                            foreach ($timezone_identifiers as $tzi) {
                                $tz[$tzi] = $tzi;
                            }
                            ?>
                            <?= form_dropdown('timezone', $tz, TIMEZONE, 'class="form-control tip" id="timezone" required="required"'); ?><?php */?>
                        </div>
                    </div>
                    <!--<div class="col-md-4">
                        <div class="form-group">
                            <?= lang('reg_ver', 'reg_ver'); ?>
                            <div class="controls">  <?php
                                echo form_dropdown('reg_ver', $wm, (isset($_POST['reg_ver']) ? $_POST['reg_ver'] : $Settings->reg_ver), 'class="tip form-control" required="required" id="reg_ver" style="width:100%;"');
                                ?> </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('allow_reg', 'allow_reg'); ?>
                            <div class="controls">  <?php
                                echo form_dropdown('allow_reg', $wm, (isset($_POST['allow_reg']) ? $_POST['allow_reg'] : $Settings->allow_reg), 'class="tip form-control" required="required" id="allow_reg" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('reg_notification', 'reg_notification'); ?>
                            <div class="controls">  <?php
                                echo form_dropdown('reg_notification', $wm, (isset($_POST['reg_notification']) ? $_POST['reg_notification'] : $Settings->reg_notification), 'class="tip form-control" required="required" id="reg_notification" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>-->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="restrict_calendar"><?= lang("calendar"); ?></label>

                            <div class="controls">
                                <?php
                                $opt_cal = array(1 => lang('private'), 0 => lang('shared'));
                                echo form_dropdown('restrict_calendar', $opt_cal, $Settings->restrict_calendar, 'class="form-control tip" required="required" id="restrict_calendar" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="warehouse"><?= lang("default_warehouse"); ?></label>

                            <div class="controls"> <?php
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name . ' (' . $warehouse->code . ')';
                                }
                                echo form_dropdown('warehouse', $wh, $Settings->default_warehouse, 'class="form-control tip" id="warehouse" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="default-store"><?= lang("default_store"); ?></label>

                            <div class="controls"> <?php
                                foreach ($stores as $store) {
                                    $sh[$store->id] = $store->name . ' (' . $store->code . ')';
                                }
                                echo form_dropdown('store', $sh, $Settings->default_store, 'class="form-control tip" id="default-store" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang("default_biller", "biller"); ?>
                            <?php
                            $bl[""] = "";
                            foreach ($billers as $biller) {
                                $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                            }
                            echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="biller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('pdf_lib', 'pdf_lib'); ?>
                            <?php $pdflibs = ['mpdf' => 'mPDF', 'dompdf' => 'Dompdf']; ?>
                            <?= form_dropdown('pdf_lib', $pdflibs, $Settings->pdf_lib, 'class="form-control tip" id="pdf_lib" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <?= lang('dine_in', 'dine_in'); ?>
                            
                         <?php
                           /*$opt = array(1 => lang('yes'), 0 => lang('no'));*/
                             echo form_dropdown('dine_in', $ps, $Settings->dine_in, 'class="form-control tip" id="dine_in" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                  
                    <div class="col-md-4">
                        <div class="form-group">
                        <?= lang('take_away', 'take_away'); ?>
                            
                         <?php
                           /*$opt = array(1 => lang('yes'), 0 => lang('no'));*/
                             echo form_dropdown('take_away', $ps, $Settings->take_away, 'class="form-control tip" id="take_away" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <?= lang('door_delivery', 'door_delivery'); ?>
                            
                         <?php
                          /* $opt = array(1 => lang('yes'), 0 => lang('no'));*/
                             echo form_dropdown('door_delivery', $ps, $Settings->door_delivery, 'class="form-control tip" id="door_delivery" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <?php //if (SHOP) { ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('apis_feature', 'apis'); ?>
                            <?= form_dropdown('apis', $ps, $Settings->apis, 'class="form-control tip" id="apis" required="required"'); ?>
                        </div>
                    </div>
                    <?php //} ?>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('night_audit_rights', 'night_audit_rights'); ?>
                            <?= form_dropdown('night_audit_rights', $ps, $Settings->night_audit_rights, 'class="form-control tip" id="night_audit_rights" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="excel_header_color"><?= lang("excel_header_color"); ?></label>
                            <?= form_input('excel_header_color', $Settings->excel_header_color, 'class="form-control tip" id="excel_header_color" '); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="excel_footer_color"><?= lang("excel_footer_color"); ?></label>
                            <?= form_input('excel_footer_color', $Settings->excel_footer_color, 'class="form-control tip" id="excel_footer_color"'); ?>
                        </div>
                    </div>
                    
                    <div style="clear: both;height: 10px;"></div>
                    
                    
                    
                    
                    <div class="clearfix"></div>
                   
                    
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                    
                        
                    
                    
                   
                    
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('bbq_configuration') ?></legend>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="bbq_enable" style="padding: 4px 7px 4px 0px;"><?= lang("BBQ_enable"); ?></label>
                                        <div class="switch-field">
                                        
                                            <input type="radio" value="0"  id="bbq-switch-left" class="switch_left skip" name="bbq_enable" <?php echo ($Settings->bbq_enable==0) ? "checked" : ''; ?>>
                                            <label for="bbq-switch-left">OFF</label>
                                            <input type="radio" value="1" id="bbq-switch-right" class="switch_right skip" name="bbq_enable" <?php echo ($Settings->bbq_enable==1) ? "checked" : ''; ?>>
                                            <label for="bbq-switch-right">ON</label>
                                        </div>
                                    </div>
                                </div>
                        
                        
                        
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="bbq_covers_limit"><?= lang("bbq_covers_limit")?></label>
                                    <?= form_input('bbq_covers_limit', ($Settings->bbq_covers_limit!='')?$Settings->bbq_covers_limit:'', 'class="form-control " id="bbq_covers_limit"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                     <label class="control-label" for="bbq_display_items"><?= lang("BBQ_display_items")?></label>
                                     <?= form_input('bbq_display_items', ($Settings->bbq_display_items!='')?$Settings->bbq_display_items:'', 'class="form-control " id="bbq_display_items"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('BBQ_discount', 'bbq_discount'); ?>
                                </br>
                                <input type="radio" value="none" id="bbq_discount-none" class="checkbox" name="bbq_discount" <?php echo ($Settings->bbq_discount=="none") ? "checked" : ''; ?>>
                                <label for="bbq_discount-none" class="padding03"><?= lang('none') ?></label>   
                                 <input type="radio" value="manual" id="manual-discount" class="checkbox" name="bbq_discount" <?php echo ($Settings->bbq_discount=="manual") ? "checked" : ''; ?>>
                                <label for="manual-discount" class="padding03"><?= lang('manual') ?></label>
                                <input type="radio" value="bbq" id="customer-discount" class="checkbox" name="bbq_discount" <?php echo ($Settings->bbq_discount=="bbq") ? "checked" : ''; ?>>
                                <label for="customer-discount" class="padding03"><?= lang('BBQ') ?></label>   
                            </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="bbq_adult_price"><?= lang("bbq_adult_price")?></label>
                                    <?= form_input('bbq_adult_price', ($Settings->bbq_adult_price!='')?$Settings->bbq_adult_price:'', 'class="form-control " id="bbq_adult_price"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="bbq_child_price"><?= lang("bbq_child_price")?></label>
                                    <?= form_input('bbq_child_price', ($Settings->bbq_child_price!='')?$Settings->bbq_child_price:'', 'class="form-control " id="bbq_child_price"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="bbq_kids_price"><?= lang("bbq_kids_price")?></label>
                                    <?= form_input('bbq_kids_price', ($Settings->bbq_kids_price!='')?$Settings->bbq_kids_price:'', 'class="form-control " id="bbq_kids_price"'); ?>
                                </div>
                            </div>
                            
                        
                       
                        
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('scan_settings') ?></legend>
                            <div class="col-md-12">
                                <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="enable_barcode" style="padding: 4px 7px;"><?= lang("barcode_on_bill"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0"  id="barcode-switch-left" class="switch_left skip" name="enable_barcode" <?php echo ($Settings->enable_barcode==0) ? "checked" : ''; ?>>
                            <label for="barcode-switch-left">OFF</label>
                            <input type="radio" value="1" id="barcode-switch-right" class="switch_right skip" name="enable_barcode" <?php echo ($Settings->enable_barcode==1) ? "checked" : ''; ?>>
                            <label for="barcode-switch-right">ON</label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="enable_qrcode" style="padding: 4px 7px;"><?= lang("QRcode_on_bill"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0"  id="qrcode-switch-left" class="switch_left skip" name="enable_qrcode" <?php echo ($Settings->enable_qrcode==0) ? "checked" : ''; ?>>
                            <label for="qrcode-switch-left">OFF</label>
                            <input type="radio" value="1" id="qrcode-switch-right" class="switch_right skip" name="enable_qrcode" <?php echo ($Settings->enable_qrcode==1) ? "checked" : ''; ?>>
                            <label for="qrcode-switch-right">ON</label>
                            </div>
                        </div>
                        
                    </div>
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('dine_in_discount_settings') ?></legend>
                            <div class="col-md-12">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('Dine_in_discount', 'dine_in_discount'); ?> : 
                                        <input type="radio" value="none" id="customer-discount-none" class="checkbox" name="customer_discount" <?php echo ($Settings->customer_discount=="none") ? "checked" : ''; ?>>
                                        <label for="customer-discount-none" class="padding03"><?= lang('none') ?></label>   
                                         <input type="radio" value="manual" id="manual-discount" class="checkbox" name="customer_discount" <?php echo ($Settings->customer_discount=="manual") ? "checked" : ''; ?>>
                                        <label for="manual-discount" class="padding03"><?= lang('manual') ?></label>
                                        <input type="radio" value="customer" id="customer-discount" class="checkbox" name="customer_discount" <?php echo ($Settings->customer_discount=="customer") ? "checked" : ''; ?>>
                                        <label for="customer-discount" class="padding03"><?= lang('customer') ?></label>   
                                    </div>    
                                </div>
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('recipe_time_management') ?></legend>
                            <div class="col-md-12">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="recipe_time_management"><?= lang("recipe_time_management"); ?></label>
                                        <div class="switch-field">
                                        
                                        <input type="radio" value="0" id="switch_left" class="skip" name="recipe_time_management" <?php echo ($Settings->recipe_time_management==0) ? "checked" : ''; ?>>
                                        <label for="switch_left">OFF</label>
                                        <input type="radio" value="1" id="switch_right" class="skip" name="recipe_time_management" <?php echo ($Settings->recipe_time_management==1) ? "checked" : ''; ?>>
                                        <label for="switch_right">ON</label>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group default_preparation_time" style="display:<?=($Settings->recipe_time_management==1)?'block':'none'?>">
                           
                                        <label class="control-label" for="default_preparation_time"><?= lang("default_preparation_time")?> [in secs]</label>
                                        <?= form_input('default_preparation_time', ($Settings->default_preparation_time!=0)?$Settings->default_preparation_time:'', 'class="form-control numberonly" id="default_preparation_time"'); ?>
                           
                                    </div>
                                </div>
                            </div>
                     <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("procurment", "procurment"); ?>
                                <?php
                                $procurment = array(0 => lang('disabled'), 1 => lang('enabled'));
                                echo form_dropdown('procurment', $procurment, (isset($_POST['procurment']) ? $_POST['procurment'] : $Settings->procurment), 'id="set_focus" data-placeholder="' . lang("select") . ' ' . lang("procurment") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('app_settings') ?></legend>
                            <div class="col-md-12">
                                 <div class="col-md-4">                    
                                    <div class="form-group">
                                        <label class="control-label" for="order_request_stewardapp" style="padding: 4px 7px 4px 0px;"><?= lang("Bil_Request_Stewardapp"); ?></label>
                                        <div class="switch-field">
                                        
                                        <input type="radio" value="0"  id="stewardapp-switch-left" class="switch_left skip" name="order_request_stewardapp" <?php echo ($Settings->order_request_stewardapp==0) ? "checked" : ''; ?>>
                                        <label for="stewardapp-switch-left">OFF</label>
                                        <input type="radio" value="1" id="stewardapp-switch-right" class="switch_right skip" name="order_request_stewardapp" <?php echo ($Settings->order_request_stewardapp==1) ? "checked" : ''; ?>>
                                        <label for="stewardapp-switch-right">ON</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('products') ?></legend>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("product_tax", "tax_rate"); ?>
                                <?php
                                echo form_dropdown('tax_rate', $ps, $Settings->default_tax_rate, 'class="form-control tip" id="tax_rate" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="racks"><?= lang("racks"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('racks', $ps, $Settings->racks, 'id="racks" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="attributes"><?= lang("attributes"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('attributes', $ps, $Settings->attributes, 'id="attributes" class="form-control tip"  required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="product_expiry"><?= lang("product_expiry"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('product_expiry', $ps, $Settings->product_expiry, 'id="product_expiry" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="remove_expired"><?= lang("remove_expired"); ?></label>

                                <div class="controls">
                                    <?php
                                    $re_opts = array(0 => lang('no').', '.lang('i_ll_remove'), 1 => lang('yes').', '.lang('remove_automatically'));
                                    echo form_dropdown('remove_expired', $re_opts, $Settings->remove_expired, 'id="remove_expired" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="image_size"><?= lang("image_size"); ?> (Width :
                                    Height) *</label>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= form_input('iwidth', $Settings->iwidth, 'class="form-control tip" id="iwidth" placeholder="image width" required="required"'); ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?= form_input('iheight', $Settings->iheight, 'class="form-control tip" id="iheight" placeholder="image height" required="required"'); ?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="thumbnail_size"><?= lang("thumbnail_size"); ?>
                                    (Width : Height) *</label>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= form_input('twidth', $Settings->twidth, 'class="form-control tip" id="twidth" placeholder="thumbnail width" required="required"'); ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?= form_input('theight', $Settings->theight, 'class="form-control tip" id="theight" placeholder="thumbnail height" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('watermark', 'watermark'); ?>
                                <?php
                                    echo form_dropdown('watermark', $wm, (isset($_POST['watermark']) ? $_POST['watermark'] : $Settings->watermark), 'class="tip form-control" required="required" id="watermark" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('display_all_products', 'display_all_products'); ?>
                                <?php
                                    $dopts = array(0 => lang('hide_with_0_qty'), 1 => lang('show_with_0_qty'));
                                    echo form_dropdown('display_all_products', $dopts, (isset($_POST['display_all_products']) ? $_POST['display_all_products'] : $Settings->display_all_products), 'class="tip form-control" required="required" id="display_all_products" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('barcode_separator', 'barcode_separator'); ?>
                                <?php
                                    $bcopts = array('-' => lang('-'), '.' => lang('.'), '~' => lang('~'), '_' => lang('_'));
                                    echo form_dropdown('barcode_separator', $bcopts, (isset($_POST['barcode_separator']) ? $_POST['barcode_separator'] : $Settings->barcode_separator), 'class="tip form-control" required="required" id="barcode_separator" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('barcode_renderer', 'barcode_renderer'); ?>
                                <?php
                                    $bcropts = array(1 => lang('image'), 0 => lang('svg'));
                                    echo form_dropdown('barcode_renderer', $bcropts, (isset($_POST['barcode_renderer']) ? $_POST['barcode_renderer'] : $Settings->barcode_img), 'class="tip form-control" required="required" id="barcode_renderer" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('update_cost_with_purchase', 'update_cost'); ?>
                                <?= form_dropdown('update_cost', $wm, $Settings->update_cost, 'class="form-control" id="update_cost" required="required"'); ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('sales') ?></legend>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="overselling"><?= lang("over_selling"); ?></label>

                                <div class="controls">
                                    <?php
                                    $opt = array(1 => lang('yes'), 0 => lang('no'));
                                    echo form_dropdown('restrict_sale', $opt, $Settings->overselling, 'class="form-control tip" id="overselling" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="reference_format"><?= lang("reference_format"); ?></label>

                                <div class="controls">
                                    <?php
                                    $ref = array(1 => lang('prefix_year_no'), 2 => lang('prefix_month_year_no'), 3 => lang('sequence_number'), 4 => lang('random_number'));
                                    echo form_dropdown('reference_format', $ref, $Settings->reference_format, 'class="form-control tip" required="required" id="reference_format" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("invoice_tax", "tax_rate2"); ?>
                                <?php $tr['0'] = lang("disable");
                                foreach ($tax_rates as $rate) {
                                    $tr[$rate->id] = $rate->name;
                                }
                                echo form_dropdown('tax_rate2', $tr, $Settings->default_tax_rate2, 'id="tax_rate2" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="product_discount"><?= lang("product_level_discount"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('product_discount', $ps, $Settings->product_discount, 'id="product_discount" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="product_serial"><?= lang("product_serial"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('product_serial', $ps, $Settings->product_serial, 'id="product_serial" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="detect_barcode"><?= lang("auto_detect_barcode"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('detect_barcode', $ps, $Settings->auto_detect_barcode, 'id="detect_barcode" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="bc_fix"><?= lang("bc_fix"); ?></label>


                                <?= form_input('bc_fix', $Settings->bc_fix, 'class="form-control tip" required="required" id="bc_fix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="item_addition"><?= lang("item_addition"); ?></label>

                                <div class="controls">
                                    <?php
                                    $ia = array(0 => lang('add_new_item'), 1 => lang('increase_quantity_if_item_exist'));
                                    echo form_dropdown('item_addition', $ia, $Settings->item_addition, 'id="item_addition" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("set_focus", "set_focus"); ?>
                                <?php
                                $sfopts = array(0 => lang('add_item_input'), 1 => lang('last_order_item'));
                                echo form_dropdown('set_focus', $sfopts, (isset($_POST['set_focus']) ? $_POST['set_focus'] : $Settings->set_focus), 'id="set_focus" data-placeholder="' . lang("select") . ' ' . lang("set_focus") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="customer_discount_request"><?= lang("customer_discount_request"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('customer_discount_request', $ps, $Settings->customer_discount_request, 'id="customer_discount_request" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="nagative_stock_production"><?= lang("nagative_stock_production"); ?></label>

                                <div class="controls">
                                    <?php
                                    echo form_dropdown('nagative_stock_production', $ps, $Settings->nagative_stock_production, 'id="nagative_stock_production" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="nagative_stock_sale"><?= lang("nagative_stock_sale"); ?></label>
                                <div class="controls">
                                    <?php
                                    echo form_dropdown('nagative_stock_sale', $ps, $Settings->nagative_stock_sale, 'id="nagative_stock_sale" class="form-control tip" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="invoice_view"><?= lang("invoice_view"); ?></label>

                                <div class="controls">
                                    <?php
                                    $opt_inv = array(1 => lang('tax_invoice'), 0 => lang('standard'), 2 => lang('indian_gst'));
                                    echo form_dropdown('invoice_view', $opt_inv, $Settings->invoice_view, 'class="form-control tip" required="required" id="invoice_view" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="states" style="display: none;">
                            <div class="form-group">
                                <label class="control-label" for="state"><?= lang("biz_state"); ?></label>
                                <div class="controls">
                                    <?php
                                    $states = $this->gst->getIndianStates();
                                    echo form_dropdown('state', $states, $Settings->state, 'class="form-control tip" required="required" id="state" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('prefix') ?></legend>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="sales_prefix"><?= lang("sales_prefix"); ?></label>

                                <?= form_input('sales_prefix', $Settings->sales_prefix, 'class="form-control tip" id="sales_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="return_prefix"><?= lang("return_prefix"); ?></label>

                                <?= form_input('return_prefix', $Settings->return_prefix, 'class="form-control tip" id="return_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="payment_prefix"><?= lang("payment_prefix"); ?></label>
                                <?= form_input('payment_prefix', $Settings->payment_prefix, 'class="form-control tip" id="payment_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="ppayment_prefix"><?= lang("ppayment_prefix"); ?></label>
                                <?= form_input('ppayment_prefix', $Settings->ppayment_prefix, 'class="form-control tip" id="ppayment_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="delivery_prefix"><?= lang("delivery_prefix"); ?></label>

                                <?= form_input('delivery_prefix', $Settings->delivery_prefix, 'class="form-control tip" id="delivery_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="quote_prefix"><?= lang("quote_prefix"); ?></label>

                                <?= form_input('quote_prefix', $Settings->quote_prefix, 'class="form-control tip" id="quote_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="purchase_prefix"><?= lang("purchase_prefix"); ?></label>

                                <?= form_input('purchase_prefix', $Settings->purchase_prefix, 'class="form-control tip" id="purchase_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="returnp_prefix"><?= lang("returnp_prefix"); ?></label>

                                <?= form_input('returnp_prefix', $Settings->returnp_prefix, 'class="form-control tip" id="returnp_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="transfer_prefix"><?= lang("transfer_prefix"); ?></label>
                                <?= form_input('transfer_prefix', $Settings->transfer_prefix, 'class="form-control tip" id="transfer_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('expense_prefix', 'expense_prefix'); ?>
                                <?= form_input('expense_prefix', $Settings->expense_prefix, 'class="form-control tip" id="expense_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('qa_prefix', 'qa_prefix'); ?>
                                <?= form_input('qa_prefix', $Settings->qa_prefix, 'class="form-control tip" id="qa_prefix"'); ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('money_number_format') ?></legend>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="decimals"><?= lang("decimals"); ?></label>

                                <div class="controls"> <?php
                                    $decimals = array(0 => lang('disable'), 1 => '1', 2 => '2', 3 => '3', 4 => '4');
                                    echo form_dropdown('decimals', $decimals, $Settings->decimals, 'class="form-control tip" id="decimals"  style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="qty_decimals"><?= lang("qty_decimals"); ?></label>

                                <div class="controls"> <?php
                                    $qty_decimals = array(0 => lang('disable'), 1 => '1', 2 => '2', 3 => '3', 4 => '4');
                                    echo form_dropdown('qty_decimals', $qty_decimals, $Settings->qty_decimals, 'class="form-control tip" id="qty_decimals"  style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('sac', 'sac'); ?>
                                <?= form_dropdown('sac', $ps, set_value('sac', $Settings->sac), 'class="form-control tip" id="sac"  required="required"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="nsac">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="decimals_sep"><?= lang("decimals_sep"); ?></label>

                                    <div class="controls"> <?php
                                        $dec_point = array('.' => lang('dot'), ',' => lang('comma'));
                                        echo form_dropdown('decimals_sep', $dec_point, $Settings->decimals_sep, 'class="form-control tip" id="decimals_sep"  style="width:100%;" required="required"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="thousands_sep"><?= lang("thousands_sep"); ?></label>
                                    <div class="controls"> <?php
                                        $thousands_sep = array('.' => lang('dot'), ',' => lang('comma'), '0' => lang('space'));
                                        echo form_dropdown('thousands_sep', $thousands_sep, $Settings->thousands_sep, 'class="form-control tip" id="thousands_sep"  style="width:100%;" required="required"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('display_currency_symbol', 'display_symbol'); ?>
                                <?php $opts = array(0 => lang('disable'), 1 => lang('before'), 2 => lang('after')); ?>
                                <?= form_dropdown('display_symbol', $opts, $Settings->display_symbol, 'class="form-control" id="display_symbol" style="width:100%;" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('currency_symbol', 'symbol'); ?>
                                <?= form_input('symbol', $Settings->symbol, 'class="form-control" id="symbol" style="width:100%;"'); ?>
                            </div>
                        </div>
                       
                    </fieldset>

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('email') ?></legend>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="protocol"><?= lang("email_protocol"); ?></label>

                                <div class="controls"> <?php
                                    $popt = array('mail' => 'PHP Mail Function', 'sendmail' => 'Send Mail', 'smtp' => 'SMTP');
                                    echo form_dropdown('protocol', $popt, $Settings->protocol, 'class="form-control tip" id="protocol"  style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="sendmail_config" style="display: none;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="mailpath"><?= lang("mailpath"); ?></label>

                                        <?= form_input('mailpath', $Settings->mailpath, 'class="form-control tip" id="mailpath"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="smtp_config" style="display: none;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="smtp_host"><?= lang("smtp_host"); ?></label>

                                        <?= form_input('smtp_host', $Settings->smtp_host, 'class="form-control tip" id="smtp_host"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="smtp_user"><?= lang("smtp_user"); ?></label>

                                        <?= form_input('smtp_user', $Settings->smtp_user, 'class="form-control tip" id="smtp_user"'); ?> </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="smtp_pass"><?= lang("smtp_pass"); ?></label>

                                        <?= form_password('smtp_pass', $Settings->smtp_pass, 'class="form-control tip" id="smtp_pass"'); ?> </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="smtp_port"><?= lang("smtp_port"); ?></label>

                                        <?= form_input('smtp_port', $Settings->smtp_port, 'class="form-control tip" id="smtp_port"'); ?> </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="smtp_crypto"><?= lang("smtp_crypto"); ?></label>

                                        <div class="controls"> <?php
                                            $crypto_opt = array('' => lang('none'), 'tls' => 'TLS', 'ssl' => 'SSL');
                                            echo form_dropdown('smtp_crypto', $crypto_opt, $Settings->smtp_crypto, 'class="form-control tip" id="smtp_crypto"');
                                            ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('Social_media') ?></legend>
                       
                        <div class="row" id="sendmail_config">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="fb_app_id"><?= lang("fb_app_id"); ?></label>

                                        <?= form_input('fb_app_id', $Settings->fb_app_id, 'class="form-control tip" id="fb_app_id"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="sendmail_config">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="fb_secret_token"><?= lang("fb_secret_token"); ?></label>

                                        <?= form_input('fb_secret_token', $Settings->fb_secret_token, 'class="form-control tip" id="fb_secret_token"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="row" id="sendmail_config">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="fb_page_id"><?= lang("fb_page_id"); ?></label>

                                        <?= form_input('fb_page_id', $Settings->fb_page_id, 'class="form-control tip" id="fb_page_id"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="sendmail_config">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="fb_page_access_token"><?= lang("fb_page_access_token"); ?></label>

                                        <?= form_input('fb_page_access_token', $Settings->fb_page_access_token, 'class="form-control tip" id="fb_page_access_token"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('Socket') ?></legend>
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="control-label" for="socket_enable" style="padding: 4px 7px 4px 0px;"><?= lang("socket_enable"); ?></label>
                            <div class="switch-field">
                            
                            <input type="radio" value="0"  id="socket-switch-left" class="switch_left skip" name="socket_enable" <?php echo ($Settings->socket_enable==0) ? "checked" : ''; ?>>
                            <label for="socket-switch-left">OFF</label>
                            <input type="radio" value="1" id="socket-switch-right" class="switch_right skip" name="socket_enable" <?php echo ($Settings->socket_enable==1) ? "checked" : ''; ?>>
                            <label for="socket-switch-right">ON</label>
                            </div>
                        </div>
                            <div class="col-md-6">
                                 <div class="form-group socket_port">
                               
                                    <label class="control-label" for="socket_port"><?= lang("socket_port")?></label>
                                    <?= form_input('socket_port', ($Settings->socket_port!=0)?$Settings->socket_port:'', 'class="form-control numberonly" id="socket_port"'); ?>
                               
                                 </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group socket_host">
                                  
                                       <label class="control-label" for="socket_host"><?= lang("socket_host")?></label>
                                       <?= form_input('socket_host', $Settings->socket_host, 'class="form-control" id="socket_host"'); ?>
                                  
                               </div>
                            </div>
                        </div>
                     </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('Notification') ?></legend>
                         <div class="col-md-12">
                            <div class="col-md-3">
                                <div class="form-group notification_time_interval">
                               
                                    <label class="control-label" for="notification_time_interval"><?= lang("notification_time_interval")?> <span style="font-size:10px;">[in secs]</span></label>
                                    <?= form_input('notification_time_interval', ($Settings->notification_time_interval!=0)?$Settings->notification_time_interval:'', 'class="form-control numberonly" id="notification_time_interval"'); ?>
                               
                                </div>
                            </div>
                         
                           
                            <div class="col-md-3">
                                 <div class="form-group bbq_notify_no_of_times">
                               
                                    <label class="control-label" for="bbq_notify_no_of_times"><?= lang("bbq_cover_notify_no_of_times")?></label>
                                    <?= form_input('bbq_notify_no_of_times', ($Settings->bbq_notify_no_of_times!=0)?$Settings->bbq_notify_no_of_times:'', 'class="form-control numberonly" id="bbq_notify_no_of_times"'); ?>
                               
                                 </div>
                            </div>
                            <div class="col-md-3">
                                 <div class="form-group bbq_return_notify_no_of_times">
                               
                                    <label class="control-label" for="bbq_return_notify_no_of_times"><?= lang("bbq_return_notify_no_of_times")?></label>
                                    <?= form_input('bbq_return_notify_no_of_times', ($Settings->bbq_return_notify_no_of_times!=0)?$Settings->bbq_return_notify_no_of_times:'', 'class="form-control numberonly" id="bbq_return_notify_no_of_times"'); ?>
                               
                                 </div>
                            </div>
                            <div class="col-md-3">
                                 <div class="form-group bill_request_notify_no_of_times">
                               
                                    <label class="control-label" for="bill_request_notify_no_of_times"><?= lang("bill_request_notify_no_of_times")?></label>
                                    <?= form_input('bill_request_notify_no_of_times', ($Settings->bill_request_notify_no_of_times!=0)?$Settings->bill_request_notify_no_of_times:'', 'class="form-control numberonly" id="bill_request_notify_no_of_times"'); ?>
                               
                                 </div>
                            </div>
                            
                        </div>
                     </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('bill_number_reset') ?></legend>
                            <div class="col-md-12">
                            <div class="col-md-4 bill_number_start_from">
                                <div class="form-group">
                                    <label class="control-label" for="bill_number_start_from"><?= lang("bill_number_start_from")?></label>
                                    <?= form_input('bill_number_start_from', ($Settings->bill_number_start_from!='')?$Settings->bill_number_start_from:'', 'class="form-control numberonly" id="bill_number_start_from"'); ?>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('bill_number_reset', 'bill_number_reset'); ?>
                                        </br>
                                        <input type="radio" value="0" id="bill-reset-none" class="checkbox billnumber_reset" name="billnumber_reset" <?php echo ($Settings->billnumber_reset==0) ? "checked" : ''; ?>>
                                        <label for="bill-reset-none" class="padding03"><?= lang('None') ?></label>
                                        <input type="radio" value="1" id="bill-reset-daily" class="checkbox billnumber_reset" name="billnumber_reset" <?php echo ($Settings->billnumber_reset==1) ? "checked" : ''; ?>>
                                        <label for="bill-reset-daily" class="padding03"><?= lang('daily') ?></label>
                                        <input type="radio" value="2" id="bill-reset-daily" class="checkbox" name="billnumber_reset" <?php echo ($Settings->billnumber_reset==2) ? "checked" : ''; ?>>
                                        <label for="bill-reset-weekly" class="padding03"><?= lang('weekly') ?></label>
                                        <input type="radio" value="3" id="bill-reset-daily" class="checkbox billnumber_reset" name="billnumber_reset" <?php echo ($Settings->billnumber_reset==3) ? "checked" : ''; ?>>
                                        <label for="bill-reset-monthly" class="padding03"><?= lang('monthly') ?></label>
                                        <input type="radio" value="4" id="bill-reset-yearly" class="checkbox billnumber_reset" name="billnumber_reset" <?php echo ($Settings->billnumber_reset==4) ? "checked" : ''; ?>>
                                        <label for="bill-reset-monthly" class="padding03"><?= lang('yearly') ?></label>
                                    </div>
                                </div>
                            </div>
                            
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('fnancial_year') ?></legend>
                        <div class="col-md-12 financial_yr_settings" style="display: <?=($Settings->billnumber_reset==4)?'block':'none';?>">
                            
                            <div class="col-md-6 ">
                                 <div class="form-group financial_yr_from">
                               
                                    <label class="control-label" for="financial_yr_from"><?= lang("financial_year_from")?></label>
                                   
                                    <?php $required='required="required"';if($Settings->billnumber_reset==4){ $required = ' required="required"';} ?>
                                    <?= form_input('financial_yr_from',$Settings->financial_yr_from, 'class="form-control month-date-picker" id="financial_yr_from" readonly="readonly"'.$required); ?>
                               
                                 </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group financial_yr_to">
                               
                                    <label class="control-label" for="financial_yr_to"><?= lang("financial_year_to")?></label>
                                    
                                    <?= form_input('financial_yr_to', $Settings->financial_yr_to, 'class="form-control month-date-picker" id="financial_yr_to" readonly="readonly"'.$required); ?>
                               
                                 </div>
                            </div>
                            
                            
                        </div>
                     </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('backup_path') ?></legend>
                        <div class="col-md-12">
                           
                            <div class="col-md-6">
                                 <div class="form-group backup_path">
                               
                                    <label class="control-label" for="backup_path"><?= lang("backup_path")?></label>
                                    <?= form_input('backup_path', $Settings->backup_path, 'class="form-control" id="backup_path"'); ?>
                               
                                 </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6" style="padding-top: 3%;">
                            <div class="form-group">
                                <label class="control-label" for="ftp_autobackup_enable" style="padding: 4px 7px 4px 0px;"><?= lang("ftp_autobackup_enable"); ?></label>
                                <div class="switch-field">
                                
                                <input type="radio" value="0"  id="ftp-switch-left" class="switch_left skip" name="ftp_autobackup_enable" <?php echo ($Settings->ftp_autobackup_enable==0) ? "checked" : ''; ?>>
                                <label for="ftp-switch-left">OFF</label>
                                <input type="radio" value="1" id="ftp-switch-right" class="switch_right skip" name="ftp_autobackup_enable" <?php echo ($Settings->ftp_autobackup_enable==1) ? "checked" : ''; ?>>
                                <label for="ftp-switch-right">ON</label>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group ftp_instance_name">
                               
                                    <label class="control-label" for="ftp_instance_name"><?= lang("ftp_instance_name")?></label>
                                    <?= form_input('ftp_instance_name', $Settings->ftp_instance_name, 'class="form-control" id="ftp_instance_name"'); ?>
                               
                                 </div>
                            </div>
                            
                        </div>
                     </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('award_points') ?></legend>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"><?= lang("customer_award_points"); ?></label>

                                <div class="row">
                                    <div class="col-sm-4 col-xs-6">
                                        <?= lang('each_spent'); ?><br>
                                        <?= form_input('each_spent', $this->sma->formatDecimal($Settings->each_spent), 'class="form-control"'); ?>
                                    </div>
                                    <div class="col-sm-1 col-xs-1 text-center"><i class="fa fa-arrow-right"></i>
                                    </div>
                                    <div class="col-sm-4 col-xs-5">
                                        <?= lang('award_points'); ?><br>
                                        <?= form_input('ca_point', $Settings->ca_point, 'class="form-control"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"><?= lang("staff_award_points"); ?></label>

                                <div class="row">
                                    <div class="col-sm-4 col-xs-6">
                                        <?= lang('each_in_sale'); ?><br>
                                        <?= form_input('each_sale', $this->sma->formatDecimal($Settings->each_sale), 'class="form-control"'); ?>
                                    </div>
                                    <div class="col-sm-1 col-xs-1 text-center"><i class="fa fa-arrow-right"></i>
                                    </div>
                                    <div class="col-sm-4 col-xs-5">
                                        <?= lang('award_points'); ?><br>
                                        <?= form_input('sa_point', $Settings->sa_point, 'class="form-control"'); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </fieldset>

                </div>
            </div>
            <div class="cleafix"></div>
            <div class="form-group">
                <div class="controls">
                    <?= form_submit('update_settings', lang("update_settings"), 'class="btn btn-primary btn-lg"'); ?>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <div class="alert alert-info" role="alert"><p>
        <?php if (!DEMO) { ?>
            <a class="btn btn-primary btn-xs pull-right" target="_blank" href="<?= admin_url('cron/run'); ?>">Run cron job now</a>
        <?php } ?>
        <p><strong>Cron Job</strong> (run at 1:00 AM daily):</p>
        <pre>0 1 * * * wget -qO- <?= admin_url('cron/run'); ?> &gt;/dev/null 2&gt;&amp;1</pre>
        OR
        <pre>0 1 * * * <?= (defined('PHP_BINDIR') ? PHP_BINDIR.DIRECTORY_SEPARATOR :'').'php '.FCPATH.SELF.' admin/cron run'; ?> >/dev/null 2>&1</pre>
        For CLI: <code>schedule path/to/php path/to/index.php controller method</code>
    </p></div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('#invoice_view').change(function(e) {
            if ($(this).val() == 2) {
                $('#states').show();
            } else {
                $('#states').hide();
            }
        });
        if ($('#invoice_view').val() == 2) {
            $('#states').show();
        } else {
            $('#states').hide();
        }
        
    });
</script>
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
label[for="recipe_time_management"]{
    width: 180px;
    top: 4px;
    position: relative;
}
</style>
<script>
    $(document).ready(function(){
       $('input[name="recipe_time_management"]').click(function(){
         if($(this).val()==1){
            $('.default_preparation_time').show();
         }else{
            $('.default_preparation_time').hide();
         }
       });
	//$('.month-date-picker').datepicker("setDate", new Date(2008,9,03) );
	$('.month-date-picker').datepicker({
		dateFormat: "dd/mm/yy" ,
            changeYear: false,autoclose:true,
            dateFormat:'d/mm',
            
        }).focus(function () {
            $(".ui-datepicker-year").hide();
        });
	
        $(".billnumber_reset").on("ifChanged",function(){
            if ($(this).val()==4) {
                //$('#financial_yr_from,#financial_yr_to').attr('required','required');
                $('.financial_yr_settings').show();
                
            }else{
                $('.financial_yr_settings').hide();
                //$('#financial_yr_from,#financial_yr_to').attr('required',false);
            }
            
        })
    });
</script>

