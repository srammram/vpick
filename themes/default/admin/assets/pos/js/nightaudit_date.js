$(document).ready(function(){
$('.kb-pad-usernum').keyboard({
        restrictInput: true,
	css: {
		container: 'custom_keyboard'
	},
        preventPaste: true,
        autoAccept: true,
        alwaysOpen: false,
        openOn: 'click',
        usePreview: false,
        layout: 'custom',
		maxLength: 4,
        display: {
            'b': '\u2190:Backspace',
        },
        customLayout: {
            'default': [
            '1 2 3 4',
            '5 6 7 8  ',
            ' 9 0 {b}',

            ' {accept} {cancel}'
            ]
        }
    });

$.ajax({
    url:siteurl+"frontend/create_nightauditDate",
    type:'post',
    success:function(res){
	
    }
});

check_nightauditDate();

 setInterval(function(){
    d_c = new Date();
    console.log(d_c)
    change_cur_date = [d_c.getDate()]+'-'+ [d_c.getMonth()+1]+'-'+ d_c.getFullYear();
    console.log('change_cur_date'+change_cur_date)
    if(transaction_date=='' || cur_date!=change_cur_date){
	popup = 1;
	check_nightauditDate();
	
    }
 },5000);
});
var transaction_date = '';
var d = new Date();
var cur_date = [d.getDate()]+'-'+ [d.getMonth()+1]+'-'+ d.getFullYear();
console.log('cur_date'+cur_date)
var popup=1;
$(document).ready(function(){
    $('.set-transaction-date').click(function(){
	$thisObj = $(this);
	$transactionDay = $thisObj.attr('data-item');
	$entered_pass = $('.user-num').val();
	$user_pass = $('.cur-user-num').val();
	console.log($user_pass);
	$('.user-num-container .error').remove();
	if ($entered_pass!=$user_pass) {			
	    $('<small class="error" style="color: red;">Wrong password!</small>').insertAfter('.user-num');
	    return false;
	}
	
	
			    
	$.ajax({
	    url:siteurl+"frontend/update_nightauditDate",
	    type:'post',
	    data:{day:$transactionDay},
	    success:function(res){
		alert('Billing date set as '+res);
		if ($transactionDay=='today') {
		    window.location.href = siteurl+"frontend/logout";
		}else{
		    location.reload();
		}
	    }
	});
    });
})
function check_nightauditDate(){
    $.ajax({
    url:siteurl+"frontend/check_nightauditDate",
    dataType:'json',
    type:'post',    
    success:function(res){
	console.log(res)
	console.log(res.status)
	if (!res.status) {
	    var d = new Date();
	    $curdate = [d.getDate()-1]+'-'+ [d.getMonth()+1]+'-'+ d.getFullYear();
	    var isShown = ($(".bootbox.modal.transaction-date-confirm").data('bs.modal') || {}).isShown;
	    if(popup==1 && isShown==undefined){
		$msg = "<div>Do you want to continue with last transaction date["+res.date+"] as todays's transaction date?</div><input type='hidden' class='cur-user-num' value='"+res.user+"'>";
	    $("#transaction-date-popup .date-alert-text").html($msg);
	    $("#transaction-date-popup").show();
	}
	}else{
	    transaction_date = res.date;
	}
    }
});
}