</td></tr></table></div></div><div class="clearfix"></div>
<footer>
<a href="#" id="toTop" class="blue" style="position: fixed; bottom: 30px; right: 30px; font-size: 30px; display: none;">
    <i class="fa fa-chevron-circle-up"></i>
</a>

    <p style="text-align:center;">&copy; <?= date('Y') ?> Heyy CAB </p>
</footer>
</div><div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<script type="text/javascript">
var dt_lang = {"sEmptyTable":"No data available in table","sInfo":"Showing _START_ to _END_ of _TOTAL_ entries","sInfoEmpty":"Showing 0 to 0 of 0 entries","sInfoFiltered":"(filtered from _MAX_ total entries)","sInfoPostFix":"","sInfoThousands":",","sLengthMenu":"Show _MENU_ ","sLoadingRecords":"Loading...","sProcessing":"Processing...","sSearch":"Search","sZeroRecords":"No matching records found","oAria":{"sSortAscending":": activate to sort column ascending","sSortDescending":": activate to sort column descending"},"oPaginate":{"sFirst":"<< First","sLast":"Last >>","sNext":"Next >","sPrevious":"< Previous"}}, dp_lang = {"days":["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],"daysShort":["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"],"daysMin":["Su","Mo","Tu","We","Th","Fr","Sa","Su"],"months":["January","February","March","April","May","June","July","August","September","October","November","December"],"monthsShort":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],"today":" Today","suffix":[],"meridiem":[]}; 

var aurl = '<?=admin_url()?>'; 
var siteurl = '<?=site_url()?>'; 
var sUrl = '<?=site_url()?>'; 

var site = {"url":''+siteurl+'',"base_url":''+aurl+'',"assets":"http:\/\/localhost\/kapp\/themes\/default\/admin\/assets\/","settings":{"logo":"logo2.png","logo2":"logo3.png","site_name":"kapp","language":"en","default_warehouse":"1","accounting_method":"2","default_currency":"2","default_tax_rate":"1","rows_per_page":"25","version":"3.2.10","default_tax_rate2":"1","dateformat":"4","sales_prefix":"SALE","quote_prefix":"QUOTE","purchase_prefix":"PO","transfer_prefix":"TR","delivery_prefix":"DO","payment_prefix":"IPAY","return_prefix":"SR","returnp_prefix":"PR","expense_prefix":"","item_addition":"0","theme":"default","product_serial":"1","default_discount":"1","product_discount":"1","discount_method":"1","tax1":"1","tax2":"1","overselling":"1","timezone_gmt":"GMT+07:00","iwidth":"800","iheight":"800","twidth":"150","theight":"150","watermark":"0","smtp_host":"pop.gmail.com","bc_fix":"4","auto_detect_barcode":"1","captcha":"0","reference_format":"3","racks":"0","attributes":"0","product_expiry":"1","decimals":"2","qty_decimals":"2","decimals_sep":".","thousands_sep":",","invoice_view":"0","default_biller":"12","rtl":"0","each_spent":null,"ca_point":null,"each_sale":null,"sa_point":null,"sac":"0","display_all_products":"0","display_symbol":"1","symbol":"$","remove_expired":"0","barcode_separator":"_","set_focus":"0","price_group":"1","barcode_img":"1","ppayment_prefix":"POP","disable_editing":"90","qa_prefix":"","update_cost":"0","apis":"1","state":"AN","pdf_lib":"dompdf","dine_in":"1","take_away":"1","door_delivery":"1","first_level":"5","second_level":"5","qsr":"0","customer_discount_request":"1","nagative_stock_production":"0","excel_header_color":"d28f16","excel_footer_color":"ffc000","installed_date":"2018-03-01 00:00:00","site_expiry_date":"0000-00-00","bill_reset":"0","billnumber_reset":"0","recipe_time_management":"1","bill_number_start_from":"010001","enable_qrcode":"0","enable_barcode":"0","default_preparation_time":"600","night_audit_rights":"1","user_language":"en","user_rtl":"0","indian_gst":false},"dateFormats":{"js_sdate":"dd-mm-yyyy","php_sdate":"d-m-Y","mysq_sdate":"%d-%m-%Y","js_ldate":"dd-mm-yyyy hh:ii","php_ldate":"d-m-Y H:i","mysql_ldate":"%d-%m-%Y %H:%i"}};

var lang = {paid: ' Paid', pending: ' Pending', completed: ' Completed', ordered: ' Ordered', received: ' Received', partial: ' Partial', sent: ' Sent', r_u_sure: 'Are You Sure?', due: ' Due', returned: ' Returned', transferring: ' Transferring', active: ' Active', inactive: ' Inactive', unexpected_value: ' Unexpected Value Provided!', select_above: ' Please Select Above First', download: ' Download'};
</script>

   <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">

<script type="text/javascript" src="<?=$assets?>js/bootstrap.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
<link rel="stylesheet" href="<?=$assets?>styles/jquery-ui.css">
<script src="<?=$assets?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.dataTables.dtFilter.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/select2.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/custom.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/core.js"></script>
<script type="text/javascript" src="<?=$assets?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/jquery.table2excel.min.js"></script>

<script type="text/javascript" src="<?=$assets?>js/jquery.magnific-popup.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/event.js"></script>
<script type="text/javascript" src="<?=$assets?>js/magnifier.js"></script>
<script type="text/javascript">
var evt = new Event(),
    m = new Magnifier(evt);
</script>
<script>
	m.attach({
    thumb: '.img',
});
	</script>
 <script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>

 <script>
	$('#navigation_container').mCustomScrollbar({ 
        theme:"dark-3",
		mouseWheelPixels:100,
		
	});
	 $('#navigation_container').mCustomScrollbar('scrollTo','0%');
	  $('#navigation_container').mCustomScrollbar('scrollTo',$('#navigation_container').find('.mCSB_container').find('.mainmenu .active'));
	
	</script>
<script>
$('#dashboard_container').mCustomScrollbar({ 
	theme:"dark-3",
	mouseWheelPixels:100 //change this to a value, that fits your needs
});
</script>

<script type="text/javascript" charset="UTF-8">var oTable = '';
    
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
    $(window).load(function () {
        var mm = '<?=$m?>';
        var vv = '<?=$m?>_<?=$v?>';
	if(mm == 'rides'){
	    console.log(mm)
            $('.mm_<?=$m?>').addClass('active');
            $('.mm_<?=$m?>').find("ul").first().slideToggle();
	    $type  = '<?=(isset($_GET['type']))?$_GET['type']:''?>';
            $('#'+$type+'_<?=$m?>_<?=$v?>').addClass('active');
	    console.log('#'+$type+'<?=$m?>_<?=$v?>')
            $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
	    $('#'+$type+'_<?=$m?>_<?=$v?>').parent('li ul:first').slideToggle();
         }
        else if(mm != 'system_settings'){
            $('.mm_<?=$m?>').addClass('active');
            $('.mm_<?=$m?>').find("ul").first().slideToggle();
            $('#<?=$m?>_<?=$v?>').addClass('active');
            $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
	    $('#<?=$m?>_<?=$v?>').parent('li.mm_<?=$m?>_submenu ul:first').slideToggle();
         }
         else
         {
            if(vv == 'system_settings_index')
            {                
                $('.mm_system_settings,.mm_pos').addClass('active');
                $('.mm_pos').find("ul").first().slideToggle();
                $('.mm_tables').removeClass('active');
                $('#system_settings_index').addClass('active');
            }
            if(vv == 'system_settings_warehouses')
            {
                $('.mm_tables,.mm_system_settings').addClass('active');
                $('.mm_system_settings').find("ul").first().slideToggle();
                $('.mm_system_settings').removeClass('active');
                $('#system_settings_warehouses').addClass('active');
            }
         }
    });
</script>
<script>
$(document).ready(function(){
	
	$('.currency').change(function(){
		var symbol = $('option:selected', this).attr('data-symbol');
		var unicode = $('option:selected', this).attr('data-unicode');
		$('#unicode_symbol').val(unicode);
		$('#symbol').val(symbol);
		
	});
});
</script>
<script type="text/javascript">
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
</script>
<script type="text/javascript">
$(document).ready(function(){
	
	
	$('.select-license-country').change(function(){
		$(".select-license-type").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCountry_bylicensetype')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select License Type</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-license-type").html($option);
				
				$(".select-license-type").select2();
			}
		})
	});
	/*Local address*/
	$('.select-local-continent').change(function(){
		
		$(".select-local-country").select2("destroy");
		$(".select-local-zone").select2("destroy");
		$(".select-local-state").select2("destroy");
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		
		var id = $(this).val();
	
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCountry_bycontinent')?>',
			data: {continent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Country</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				
				$(".select-local-country").html($option);
				$(".select-local-zone").html('<option value="">Select Zone</option>');
				$(".select-local-state").html('<option value="">Select State</option>');
				$(".select-local-city").html('<option value="">Select City</option>');
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-country").select2();
				$(".select-local-zone").select2();
				$(".select-local-state").select2();
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	$('.select-local-country').change(function(){
		$(".select-local-zone").select2("destroy");
		$(".select-local-state").select2("destroy");
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Zone</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-local-zone").html($option);
				$(".select-local-state").html('<option value="">Select State</option>');
				$(".select-local-city").html('<option value="">Select City</option>');
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-zone").select2();
				$(".select-local-state").select2();
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	$('.select-local-zone').change(function(){
		$(".select-local-state").select2("destroy");
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getState_byzone')?>',
			data: {zone_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select State</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-local-state").html($option);
				$(".select-local-city").html('<option value="">Select City</option>');
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-state").select2();
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	$('.select-local-state').change(function(){
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		var id = $(this).val();
		var state = $('.select-local-state option:selected').text();
		$('#license_issuing_authority').val(state);
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCity_bystate')?>',
			data: {state_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select City</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-local-city").html($option);
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	$('.select-local-city').change(function(){
		
		$(".select-local-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getArea_bycity')?>',
			data: {city_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Area</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-local-area").html($option);
				$(".select-local-area").select2();
			}
		})
	});
	
	
	$('.select-permanent-continent').change(function(){
		$(".select-permanent-country").select2("destroy");
		$(".select-permanent-zone").select2("destroy");
		$(".select-permanent-state").select2("destroy");
		$(".select-permanent-city").select2("destroy");
		$(".select-permanent-area").select2("destroy");
		
		var id = $(this).val();
	
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCountry_bycontinent')?>',
			data: {continent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Country</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				
				$(".select-permanent-country").html($option);
				$(".select-permanent-zone").html('<option value="">Select Zone</option>');
				$(".select-permanent-state").html('<option value="">Select State</option>');
				$(".select-permanent-city").html('<option value="">Select City</option>');
				$(".select-permanent-area").html('<option value="">Select Area</option>');
				$(".select-permanent-country").select2();
				$(".select-permanent-zone").select2();
				$(".select-permanent-state").select2();
				$(".select-permanent-city").select2();
				$(".select-permanent-area").select2();
			}
		})
	});
	
	$('.select-permanent-country').change(function(){
		$(".select-permanent-zone").select2("destroy");
		$(".select-permanent-state").select2("destroy");
		$(".select-permanent-city").select2("destroy");
		$(".select-permanent-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Zone</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-permanent-zone").html($option);
				$(".select-permanent-state").html('<option value="">Select State</option>');
				$(".select-permanent-city").html('<option value="">Select City</option>');
				$(".select-permanent-area").html('<option value="">Select Area</option>');
				$(".select-permanent-zone").select2();
				$(".select-permanent-state").select2();
				$(".select-permanent-city").select2();
				$(".select-permanent-area").select2();
			}
		})
	});
	
	$('.select-permanent-zone').change(function(){
		$(".select-permanent-state").select2("destroy");
		$(".select-permanent-city").select2("destroy");
		$(".select-permanent-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getState_byzone')?>',
			data: {zone_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select State</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-permanent-state").html($option);
				$(".select-permanent-city").html('<option value="">Select City</option>');
				$(".select-permanent-area").html('<option value="">Select Area</option>');
				$(".select-permanent-state").select2();
				$(".select-permanent-city").select2();
				$(".select-permanent-area").select2();
			}
		})
	});
	
	$('.select-permanent-state').change(function(){
		$(".select-permanent-city").select2("destroy");
		$(".select-permanent-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCity_bystate')?>',
			data: {state_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select City</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-permanent-city").html($option);
				$(".select-permanent-area").html('<option value="">Select Area</option>');
				$(".select-permanent-city").select2();
				$(".select-permanent-area").select2();
			}
		})
	});
	
	$('.select-permanent-city').change(function(){
		$(".select-permanent-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getArea_bycity')?>',
			data: {city_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Area</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-permanent-area").html($option);
				$(".select-permanent-area").select2();
			}
		})
	});
	
});
</script>
<script type="text/javascript">
$('.without-caption').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: false,
		mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
		image: {
			verticalFit: true
		},
		zoom: {
			enabled: true,
			duration: 300,
			easing: 'ease-in-out', // don't foget to change the duration also in CSS
		}
	});


function checkValue(str, max) {
  if (str.charAt(0) !== '0' || str == '00') {
    var num = parseInt(str);
    if (isNaN(num) || num <= 0 || num > max) num = 1;
    str = num > parseInt(max.toString().charAt(0)) && num.toString().length == 1 ? '0' + num : num.toString();
  };
  return str;
};

function dateCheck(date){
	
	date.addEventListener('input', function(e) {
	  this.type = 'text';
	  var input = this.value;
	  if (/\D\/$/.test(input)) input = input.substr(0, input.length - 3);
	  var values = input.split('/').map(function(v) {
		return v.replace(/\D/g, '')
	  });
	  if (values[0]) values[0] = checkValue(values[0], 31);
	  if (values[1]) values[1] = checkValue(values[1], 12);
	  var output = values.map(function(v, i) {
		return v.length == 2 && i < 2 ? v + '/' : v;
	  });
	  this.value = output.join('').substr(0, 10);
	});

	date.addEventListener('blur', function(e) {
	  this.type = 'text';
	  var input = this.value;
	  var values = input.split('/').map(function(v, i) {
		return v.replace(/\D/g, '')
	  });
	  var output = '';
	  
	  if (values.length == 3) {
		var year = values[2].length !== 4 ? parseInt(values[2]) + 2000 : parseInt(values[2]);
		var month = parseInt(values[1]) ;
		var day = parseInt(values[0])- 1;
		var d = new Date(year, month, day);
		
		
		
		
		if (!isNaN(d)) {
		  var dates = [d.getMonth() + 1, d.getDate(), d.getFullYear()];
		  output = dates.map(function(v) {
			v = v.toString();
			return v.length == 1 ? '0' + v : v;
		  }).join('/');
		};
	  };
	  this.value = output;
	});
}


function inputUpper(input) {
	input.value = input.value.toUpperCase();
}
function inputlower(input) {
	input.value = input.value.toLowerCase();
}

function inputFirstUpper(input) {
	input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
}


</script>

<script type="text/javascript">
var due_month = <?= $due_month ?>;
var m_new = '+'+due_month+'m';
var yearRangeMin =  '-<?= $due_year ?>:+0';
var yearRangeMax =  '-0:+<?= $due_year ?>';
var d_age = new Date();
var year_d_age = d_age.getFullYear() - 18;
d_age.setFullYear(year_d_age);

var CurrentDate = new Date();

function getDate(element) {
	var date;
	try {
		date = $.datepicker.parseDate(dateFormat, element.value);
	} catch (error) {
		date = null;
	}
	return date;
}


$('.common_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	maxDate: 0,
	changeMonth: true,
	changeYear: true,
	yearRange: '-100:+0'    
});


$('#dob').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: d_age,
	changeMonth: true,
	changeYear: true,
	maxDate: d_age,
	yearRange: '-100:+0',
	   
});

$('#license_issued_on').datepicker({
	dateFormat: "dd/mm/yy" ,
	changeMonth: true,
	changeYear: true,
	maxDate: 0,
	yearRange: yearRangeMin, 
	onSelect: function(dateText, instance) {
		date = $.datepicker.parseDate(instance.settings.dateFormat, dateText, instance.settings);
		date.setMonth(date.getMonth() + due_month);
		$("#license_validity").datepicker("setDate", date);
	}
});

$('#license_validity').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	changeMonth: true,
	changeYear: true,
	minDate:m_new,
	yearRange: yearRangeMax,
});

$('#license_dob').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: d_age,
	changeMonth: true,
	changeYear: true,
	maxDate: d_age,
	yearRange: '-100:+0',
});

$('#police_on').datepicker({
	dateFormat: "dd/mm/yy" ,
	changeMonth: true,
	changeYear: true,
	maxDate: 0,
	yearRange: yearRangeMin, 
	onSelect: function(dateText, instance) {
		date = $.datepicker.parseDate(instance.settings.dateFormat, dateText, instance.settings);
		date.setMonth(date.getMonth() + due_month);
		$("#police_til").datepicker("setDate", date);
	}
});

$('#police_til').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	changeMonth: true,
	changeYear: true,
	minDate:m_new,
	yearRange: yearRangeMax,
	 
});

$('#reg_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	changeMonth: true,
	changeYear: true,
	maxDate: 0,
	yearRange: yearRangeMin, 
	onSelect: function(dateText, instance) {
		date = $.datepicker.parseDate(instance.settings.dateFormat, dateText, instance.settings);
		date.setMonth(date.getMonth() + due_month);
		$("#reg_due_date").datepicker("setDate", date);
	}
});

$('#reg_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	changeMonth: true,
	changeYear: true,
	minDate:m_new,
	yearRange: yearRangeMax,
	 
});

$('#taxation_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax  
});

$('#insurance_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax  
});

$('#permit_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax      
});

$('#authorisation_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax     
});

$('#fitness_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax    
});

$('#speed_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax   
});

$('#puc_due_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	minDate:m_new,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMax    
});

$('#payment_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	maxDate:0,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMin    
});

$('#deposit_date').datepicker({
	dateFormat: "dd/mm/yy" ,
	defaultDate: m_new,
	maxDate:0,
	changeMonth: true,
	changeYear: true,
	yearRange: yearRangeMin    
});

function hideDaysFromCalendar() {
	var thisCalendar = $(this);
	$('.ui-datepicker-calendar').detach();
	// Also fix the click event on the Done button.
	$('.ui-datepicker-close').unbind("click").click(function() {
		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
		thisCalendar.datepicker('setDate', new Date(year, month, 1));
	});
}

$('#manufacture_year').datepicker({
	dateFormat: "mm yy" ,
	//defaultDate: m_new,
	maxDate:0,
	
    viewMode: "months", 
    minViewMode: "months",
	
	changeMonth: true,
	changeYear: true,
	showButtonPanel: true,
        showAnim: "",
	yearRange: yearRangeMin    
}).focus(hideDaysFromCalendar);

$(document).on('change', '#capacity_number', function(){
	var capacity_number = parseInt($(this).val());
	capacity_number = parseInt(capacity_number + 1);
	$('#capacity').val(capacity_number);
});


</script>

<script type="text/javascript">
  var tmpAnimation = 0;
  $("button").click(function(){
	  var imagevalue = $(this).parent().find(".image-link").find('img');
	  
    var element = imagevalue;
    tmpAnimation = tmpAnimation + 90;
    
    $({degrees: tmpAnimation - 90}).animate({degrees: tmpAnimation}, {
        duration: 3000,
        step: function(now) {
            element.css({
                transform: 'rotate(' + now + 'deg)'
            });
        }
    });
  });
</script>


</body>
</html>
