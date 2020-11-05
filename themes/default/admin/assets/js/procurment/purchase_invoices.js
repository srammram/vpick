$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    if($('#pi_supplier').val()==''){
	$('#pi_supplier').select2('focus');
	$('#pi_supplier').select2('open');
    }else{
	$('#add_item').focus();
    }
}
// Order level shipping and discoutn localStorage
if (pi_discount = localStorage.getItem('pi_discount')) {
    $('#pi_discount').val(pi_discount);
}
$('#ptax2').change(function (e) {
    localStorage.setItem('p_tax2', $(this).val());
});
if (ptax1= localStorage.getItem('p_tax1')) {
    $('#ptax1').select2("val", ptax1);
}
$('#postatus').change(function (e) {
    localStorage.setItem('pi_status', $(this).val());
});
if (postatus = localStorage.getItem('pi_status')) {
    $('#postatus').select2("val", postatus);
}
$('#tax_method').change(function (e) {
    localStorage.setItem('tax_method', $(this).val());
    loadItems();
});
if (taxmethod = localStorage.getItem('tax_method')) {
    $('#tax_method').select2("val", taxmethod);
}

/*$(document).on('change', '#tax_method', function(){
    localStorage.setItem('tax_method', $('#tax_method').val());
    alert($('#tax_method').val());
});*/

var old_shipping;
$('#poshipping').focus(function () {
    old_shipping = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_shipping);
        bootbox.alert(lang.unexpected_value);
        return;
    }
    shipping = parseFloat(posh);
    localStorage.setItem('pi_shipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
});
if (poshipping = localStorage.getItem('pi_shipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').change(function (e) {
    localStorage.setItem('pi_payment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('pi_payment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('pi_items')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('pi_items')) {
                    localStorage.removeItem('pi_items');
                }
                if (localStorage.getItem('pi_discount')) {
                    localStorage.removeItem('pi_discount');
                }
                if (localStorage.getItem('pi_tax2')) {
                    localStorage.removeItem('pi_tax2');
                }
                if (localStorage.getItem('pi_shipping')) {
                    localStorage.removeItem('pi_shipping');
                }
                if (localStorage.getItem('pi_ref')) {
                    localStorage.removeItem('pi_ref');
                }
                if (localStorage.getItem('pi_warehouse')) {
                    localStorage.removeItem('pi_warehouse');
                }
                if (localStorage.getItem('pi_note')) {
                    localStorage.removeItem('pi_note');
                }
                if (localStorage.getItem('pi_supplier')) {
                    localStorage.removeItem('pi_supplier');
                }
                if (localStorage.getItem('pi_currency')) {
                    localStorage.removeItem('pi_currency');
                }
                if (localStorage.getItem('pi_extras')) {
                    localStorage.removeItem('pi_extras');
                }
                if (localStorage.getItem('pi_date')) {
                    localStorage.removeItem('pi_date');
                }
                if (localStorage.getItem('pi_status')) {
                    localStorage.removeItem('pi_status');
                }
                if (localStorage.getItem('pi_payment_term')) {
                    localStorage.removeItem('pi_payment_term');
                }
		if (localStorage.getItem('pi_requestnumber')) {
                    localStorage.removeItem('pi_requestnumber');
                }
                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#pi_supplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('pi_ref', $(this).val());
});

if (poref = localStorage.getItem('pi_ref')) {
    $('#poref').val(poref);
}

$('#invoice_no').change(function (e) {
    localStorage.setItem('pi_invoice_no', $(this).val());
});

if (pi_invoice_no = localStorage.getItem('pi_invoice_no')) {
    $('#invoice_no').val(pi_invoice_no);
}


$('#powarehouse').change(function (e) {
    localStorage.setItem('pi_warehouse', $(this).val());
});
$('#pi_supplier').change(function (e) {
    localStorage.setItem('pi_supplier', $(this).val());
});

/*if (powarehouse = localStorage.getItem('pi_warehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}
*/

        if (ponote = localStorage.getItem('pi_note')) {
            // $('#ponote').redactor('set', ponote);
        }
        /*$supplier.change(function (e) {            
            localStorage.setItem('pi_supplier', $(this).val());            
        });*/

/*        if (pi_supplier = localStorage.getItem('pi_supplier')) {

            $supplier.val(pi_supplier).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url+"suppliers/getSupplier/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "suppliers/suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });

} else {
    nsSupplier();
}*/

    /*$('.rexpiry').change(function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        pi_items[item_id].row.expiry = $(this).val();
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
    });*/
if (localStorage.getItem('pi_extras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('pi_extras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("pi_extras");
    $('#extras-con').slideUp();
});
$(document).on('change', '.rexpiry', function () {
	
	var row = $(this).attr('data-id');

    var item_id = $(this).closest('tr').attr('data-item-id');
    pi_items[item_id].row.expiry = $(this).val();
    localStorage.setItem('pi_items', JSON.stringify(pi_items));
});

$(document).on('change', '.rdays', function () {
	
	var row = $(this).attr('data-id');
	var day_val = parseInt($(this).val());
	 var item_id = $(this).closest('tr').attr('data-item-id');
	var mfg = $("#mfg_"+row).val();
	var date = new Date(mfg);
	date.setDate(date.getDate() + day_val);
	var finalDate = date.getFullYear()+'-'+ (date.getMonth() < 9 ? '0': '') + (date.getMonth()+1) +'-'+ (date.getDate() < 9 ? '0': '') + date.getDate();
	$("#expiry_"+row).val(finalDate);
	pi_items[item_id].row.expiry = $("#expiry_"+row).val(finalDate);
    pi_items[item_id].row.days = $(this).val();
    localStorage.setItem('pi_items', JSON.stringify(pi_items));
});



		
$(document).on('click', '.rmfg', function () {
	
	var row = $(this).attr('data-id');
	var day_val = parseInt($("#days_"+row).val());
	 var item_id = $(this).closest('tr').attr('data-item-id');
	$("#mfg_"+row).datepicker({
		numberOfMonths: 1,
		maxDate: '0',  
		dateFormat: "yy-mm-dd",
		onSelect: function(selected) {
			var date = $(this).datepicker('getDate');
			date.setDate(date.getDate() + day_val); // Add 7 days
			date = moment(date).format('YYYY-MM-DD');
			$("#expiry_"+row).val(date);
			pi_items[item_id].row.expiry = $("#expiry_"+row).val(date);
		}
	}).datepicker('widget').wrap('<div class="ll-skin-nigran"/>');
	
	
	
   
    pi_items[item_id].row.mfg = $(this).val();
	 pi_items[item_id].row.days = day_val;
    localStorage.setItem('pi_items', JSON.stringify(pi_items));
});

/*$(document).on('change', '.rmfg', function () {
	alert('a');
    var item_id = $(this).closest('tr').attr('data-item-id');
	var total_expiry = $(this).closest('tr').attr('data-total-expiry');
	
	var mfg_date = $(this).val().split('-');  
	mfg_date = 	mfg_date[2] +'-'+ mfg_date[1] +'-'+ mfg_date[0]; 

	var today = new Date(mfg_date);
	var tomorrow = new Date();
	tomorrow.setDate(today.getDate() + parseInt(total_expiry) );
	
	var dd = tomorrow.getDate();
	var mm = tomorrow.getMonth()+1; //January is 0!
	
	var yyyy = tomorrow.getFullYear();
	if(dd<10){
		dd='0'+dd;
	} 
	if(mm<10){
		mm='0'+mm;
	} 
	
	var expiry_date = dd+'-'+mm+'-'+yyyy;

	$('.rexpiry[data-item='+item_id+']').val(expiry_date);
	
    pi_items[item_id].row.mfg = $(this).val();
	pi_items[item_id].row.expiry = expiry_date;
    localStorage.setItem('poitems', JSON.stringify(pi_items));
});*/

$(document).on('change', '.rbatch_no', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    pi_items[item_id].row.batch_no = $(this).val();
    localStorage.setItem('pi_items', JSON.stringify(pi_items));
});




// prevent default action upon enter
$('body').bind('keypress', function (e) {
    if ($(e.target).hasClass('redactor_editor')) {
        return true;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calcuation
if (site.settings.tax2 != 0) {
    $('#potax2').change(function () {
        localStorage.setItem('pi_tax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_pi_discount;
$('#pi_discount').focus(function () {
    old_pi_discount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('pi_discount');
        localStorage.setItem('pi_discount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_pi_discount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete pi_items[item_id];
        row.remove();
        if(pi_items.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('pi_items', JSON.stringify(pi_items));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.edit', function () {

        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = pi_items[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_cost = formatDecimal(row.children().children('.rucost').val()),
        discount = row.children().children('.rdiscount').val();
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        var real_unit_cost = item.row.real_unit_cost;
        var net_cost = real_unit_cost;
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
			$('#ptax_method').select2('val', item.row.tax_method);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_cost -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (pi_items[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_cost -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function () {
                if(o == 1) {
                    if(product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
                }
                $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                o++;
            });
        }

        uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        $.each(item.units, function () {
            if(this.id == item.row.unit) {
                $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
            } else {
                $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
            }
        });

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pcost').val(unit_cost);
        $('#punit_cost').val(formatDecimal(parseFloat(unit_cost)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_cost').val(unit_cost);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pmfg').val(row.children().children('.rmfg').val());
		$('#pexpiry').val(row.children().children('.rexpiry').val());
		$('#pbatch_no').val(row.children().children('.rbatch_no').val());
        $('#pdiscount').val(discount);
        $('#net_cost').text(formatMoney(net_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#psubtotal').val('');
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pcost, #ptax, #ptax_method, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_cost = parseFloat($('#pcost').val());
        var item = pi_items[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_cost) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_cost -= item_discount;
        var pr_tax = $('#ptax').val(), item_tax_method = ($('#ptax_method').val()) ? $('#ptax_method').val() : item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_cost -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_cost').text(formatMoney(unit_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = pi_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != pi_items[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    $('#pcost').val(formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 4)).change();
                }
            });
        } else {
            $('#pcost').val(formatDecimal(item.row.base_unit_cost)).change();
        }
    });

    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = pi_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
        qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal/qty), 4)).change();
        return false;
    });

	$(document).on('click', '.delivery_store', function () {

        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
		
		var quote_id = localStorage.getItem('pi_requestnumber');
		
        item = pi_items[item_id];
        var qty = row.children().children('.rquantity').val();
		var product_id = row.children().children('.rid').val();
		$('#dsquantity').val(qty);
		$('#dsrow_id').val(row_id);
		$('#dsproduct_id').val(product_id);
		$('#dsquote_id').val(quote_id);
        $('#DSModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        
      	$.ajax({
			type: "get", 
			url: site.base_url+"purchase_invoices/getProductStores/?product_id="+product_id+"&row="+row_id+"&qty="+qty+"&quote_id="+quote_id,
			dataType: "html",
			success: function (data) {
				$('.ds_addon').html(data);
			}
		});
        
        $('#DSModal').appendTo("body").modal('show');

    });
	
	
    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {}, new_pr_tax_method = $('#ptax_method').val();
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != pi_items[item_id].row.base_unit) {
            $.each(pi_items[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        pi_items[item_id].row.fup = 1,
        pi_items[item_id].row.qty = parseFloat($('#pquantity').val()),
        pi_items[item_id].row.base_quantity = parseFloat(base_quantity),
        pi_items[item_id].row.unit = unit,
        pi_items[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        pi_items[item_id].row.tax_rate = new_pr_tax,
		pi_items[item_id].row.tax_method = new_pr_tax_method,
        pi_items[item_id].tax_rate = new_pr_tax_rate,
        pi_items[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        pi_items[item_id].row.option = $('#poption').val(),		
		pi_items[item_id].row.tax1 = $('#ptax1').val(),
		pi_items[item_id].row.tax2 = $('#ptax2').val(),		
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        $('#prModal').modal('hide');               
        loadItems();
        return;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
    var old_row_qty;
    $(document).on("focus", '.rquantity', function () {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        pi_items[item_id].row.base_quantity = new_qty;
        if(pi_items[item_id].row.unit != pi_items[item_id].row.base_unit) {
            $.each(pi_items[item_id].units, function(){
                if (this.id == pi_items[item_id].row.unit) {
                    pi_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        pi_items[item_id].row.qty = new_qty;
        pi_items[item_id].row.received = new_qty;
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });
    
    $(document).on("click", '.item-dis-type', function () {
        var row = $(this).closest('tr');
        $dis_type = $(this).val();
        item_id = row.attr('data-item-id');
        $('.rdiscount').val('');
	pi_items[item_id].row.item_discount_percent = '';
        pi_items[item_id].row.item_dis_type = $dis_type;
       
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });
    
	
	$(document).on("change", '.rtax1', function () {
		var row = $(this).closest('tr');
		current_tax_method = $(this).val();
		item_id = row.attr('data-item-id');
		pi_items[item_id].row.tax_method = current_tax_method;
		localStorage.setItem('pi_items', JSON.stringify(pi_items));
		loadItems();
	});
	
	$(document).on("change", '.rtax2', function () {
		var row = $(this).closest('tr');
		current_tax = $(this).val();
		current_tax_rate = $(this).find('option:selected').attr('data-value');
		item_id = row.attr('data-item-id');
		pi_items[item_id].tax_rate = current_tax_rate;
		pi_items[item_id].tax_rate_id = current_tax;
		pi_items[item_id].tax_rate_val = current_tax_rate;
		localStorage.setItem('pi_items', JSON.stringify(pi_items));
		loadItems();
	});
	$(document).on("change", '.tax_method', function () {
		$('.rtax2').trigger('change');
	});

    var old_received;
     $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_received = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        if (new_received > pi_items[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(pi_items[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        pi_items[item_id].row.unit_received = new_received;
        pi_items[item_id].row.received = qty_received;
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });

    $(document).on("change", '.rucost', function () {
        var row = $(this).closest('tr');
        current_ucost = $(this).val();
        item_id = row.attr('data-item-id');        
        pi_items[item_id].row.unit_cost = current_ucost;
	
	pi_items[item_id].row.real_unit_cost = current_ucost;
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });

    $(document).on("change", '.rdiscount', function () {
	$obj = $(this);
        var row = $(this).closest('tr');        
        current_discount = $(this).val();
        item_id = row.attr('data-item-id');
	
	$row_gross = parseFloat(row.find('.rugross').val());
	$disType  = row.find('.item-dis-type:checked').val();
	item_ds_amt = parseFloat(calculateDiscount(current_discount, $row_gross));

	if ($disType=="f") {
	    
	    if(item_ds_amt>$row_gross){
		alert('Discount amount greater than Gross');
		pi_items[item_id].row.item_discount_percent = '';
		localStorage.setItem('pi_items', JSON.stringify(pi_items));
	    }else{
		pi_items[item_id].row.item_discount_percent  = current_discount;
		localStorage.setItem('pi_items', JSON.stringify(pi_items));
	    }
	}else{
	    pi_items[item_id].row.item_discount_percent = current_discount;
	    localStorage.setItem('pi_items', JSON.stringify(pi_items));
	  
	}
        loadItems();
    });
    
    $(document).on("change", '#currency', function () {
        var row = $(this).closest('tr');        
        currency = $(this).val();
        localStorage.setItem('currency',currency);
    });
    $(document).on("change", '#tax_method', function () {
        var row = $(this).closest('tr');        
        $tax_method = $(this).val();
        localStorage.setItem('tax_method',$tax_method);
    });
    $(document).on("change", '#pi_status', function () {
        var row = $(this).closest('tr');        
        status = $(this).val();
        localStorage.setItem('pi_status',status);
    });
    $(document).on("blur", '.ru_sellingprice', function () {
	$obj = $(this);
        var row = $(this).closest('tr');        
        sellingprice = parseFloat($(this).val());
        item_id = row.attr('data-item-id');
	$landcost = parseFloat(row.find('.landcost').val());
	
	if (sellingprice < $landcost) {
	    
            bootbox.alert({ 
		size: "small",
		message: 'Selling price should not be less than landing cost', 
		callback: function(){		    
		    $("html, body").
		    animate({ scrollTop: $obj.offset().top }, 1000)
		    $("html, body").animate({scrollLeft: $obj.offset().left},"slow");
		    $obj.addClass('procurment-input-error');
		    
		}
	    });
	}
	
    });
    $(document).on("change", '.ru_sellingprice', function () {
        var row = $(this).closest('tr');        
        sellingprice = $(this).val();
        item_id = row.attr('data-item-id');
        pi_items[item_id].row.item_selling_price = sellingprice;
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });

    

    $(document).on("change", '.bill_disc', function () {
	
	bill_disc = $(this).val();
	if (bill_disc.indexOf("%") !== -1) {
	    bill_disc_per = bill_disc;
	}else{
	    $bil_dis_from = parseFloat($('#final_gross_amt').val())-parseFloat( $('#item_disc').val());
	    $bill_disc_per = (bill_disc*100)/$bil_dis_from;
	    bill_disc_per = $bill_disc_per+('%');
	}

        localStorage.setItem('bill_disc',bill_disc);
	localStorage.setItem('bill_disc_percentage',bill_disc_per);
        loadItems();
    }); 


    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
     var old_cost;
     $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        pi_items[item_id].row.cost = new_cost;
        localStorage.setItem('pi_items', JSON.stringify(pi_items));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#pi_supplier').select2('readonly', false);
     return false;
 });

    if (purchase_invoices_edit) {
        $('#pi_supplier').select2("readonly", true);
    }
   $('#feright_chargers_shipping').keyup(function(){
	$charge = $(this).val();
	$charge = ($charge=='') ?0:$charge;
	$charge  = parseFloat($charge).toFixed(2);
	$('#feright_chargers_shipping').val($charge);
	$("#feright_chargers_shipping").trigger('change');
	localStorage.setItem('freight',$charge);	
   });
   $('#round_off_amt').keyup(function(){
    $charge = $(this).val();
    $charge = ($charge=='') ?0:$charge;
    $charge  = parseFloat($charge).toFixed(2);
    $('#round_off').val($charge);
    $("#round_off").trigger('change');
    localStorage.setItem('round_off',$charge);		
   });
   $('#pi_note').keyup(function(){
    $charge = $(this).val();    
    localStorage.setItem('pi_note',$charge);		
   });
   $('#invoice_amt').keyup(function(){
    $charge = $(this).val();    
    localStorage.setItem('invoice_amt',$charge);		
   });
   
   $('#final_gross_amt,#item_disc,#bill_disc_val,#sub_total,#tax,#freight,#round_off').change(function(){
    
    //$f_gross_amt = $('#final_gross_amt').val();
    //$f_item_disc = $('#item_disc').val();
    //$f_bill_disc = $('#bill_disc_val').val();
    //$f_sub_total = $('#sub_total').val();
    //$f_tax = $('#tax').val();
    //$f_freight = $('#freight').val();
    //$f_round_off = $('#round_off').val();
    //$f_total = parseFloat($f_gross_amt)-parseFloat($f_item_disc)-parseFloat($f_bill_disc)+parseFloat($f_sub_total)+parseFloat($f_tax)+parseFloat($f_freight)+parseFloat($f_round_off);
    //$('.net_amt').val($f_total);
   });
   $('#freight,#round_off').change(function(){
    loadItems();
   });
   
   $('#add-purchase-invoice input[type="submit"]').click(function(e){
    $(window).unbind('beforeunload');
    $net_amt = parseFloat($('.net_amt').val());
    $inv_amt = parseFloat($('#invoice_amt').val());
    $error = false;
   /* else*/
  
   $('#s2id_pi_supplier').removeClass('procurment-input-error');  
    if($('#purchase_invoicesTable tbody tr').length==0){
	bootbox.alert('Add Items');
	return false;
    }else if ($net_amt==0) {
	bootbox.alert('net amount should be more than zero');
	return false;
    }else if ($net_amt!=$inv_amt) {
	bootbox.alert('net amount and invoice amount is mismatched');
	return false;
    }
    else if ($('#pi_supplier').val()=='') {
	$error = true;
	$('#s2id_pi_supplier').addClass('procurment-input-error');       
    }else{
	
	$('.required').each(function(){
	    $(this).removeClass('procurment-input-error');
	   
	    if($(this).val()==''){
		$error=true;
		$(this).addClass('procurment-input-error');
	    }
	    
	});
	
	$('.ru_sellingprice').each(function(){
	    $(this).removeClass('procurment-input-error');
	    var row = $(this).closest('tr');        
	    sellingprice = parseFloat($(this).val());
	    item_id = row.attr('data-item-id');
	    $landcost = parseFloat(row.find('.landcost').val());
	    if (sellingprice < $landcost) {
		$error=true;
		$(this).addClass('procurment-input-error');
	    }
	});
	
	$(".rquantity").each(function(n,v){
	    if($(this).val()=='' || $(this).val()==0){
		$error=true;
		$(this).addClass('procurment-input-error');
	    }
	});
	
    }
    if ($error) {	   
	    e.preventDefault();
	    $("html, body").animate({ scrollTop: $('.procurment-input-error:eq(0)').offset().top }, 1000);
	    return false;   
    }else{
	$('#add-purchase-invoice').submit();
    }
   });
   $(document).on("focus", ".datepicker", function(){
        $(this).datepicker({
	dateFormat: "yy-mm-dd" ,  
	minDate: 0
    });
    });
   
    $('#po_number').change(function(){
	
    });
   
});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
	
    $('#pi_supplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
				
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}



function loadItems() {
    if (localStorage.getItem('pi_items')) {
        
        total = 0;
        gross_total = 0;
        bill_disc = 0;
        item_discount_total = 0;
        bill_discount_total = 0;
        subtotal_total = 0;
        netamount_total = 0;
        tax_total = 0;
        count = 0;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        
        $("#purchase_invoicesTable tbody").empty();
		
        pi_items = JSON.parse(localStorage.getItem('pi_items'));

		sortedItems = pi_items;       
        var order_no = new Date().getTime();
		$i = 0;
		$row_cnt = 0;
	var $total_no_items=0;
	var $total_no_qty=0;
        $.each(sortedItems, function () {			
			$i++;
            var item = this;
			            
			 var item_id = item.item_id;
			
            item.order = item.order ? item.order : order_no++;
			
            var item_days= item.row.days ? parseInt(item.row.days) : 1; 
			
			if(item.row.mfg != ''){
				
				var item_mfgs = item.row.mfg;
				
				var mfgdate = new Date(item_mfgs);
				mfgdate.setDate(mfgdate.getDate() + item_days);
				var mfgfinalDate = mfgdate.getFullYear()+'-'+ (mfgdate.getMonth() < 9 ? '0': '') + (mfgdate.getMonth()+1) +'-'+ (mfgdate.getDate() < 9 ? '0': '') + mfgdate.getDate();
				var item_mfg = mfgfinalDate;
				
				var expirydate = new Date(mfgfinalDate);
				expirydate.setDate(expirydate.getDate() + item_days);
				var expiryfinalDate = expirydate.getFullYear()+'-'+ (expirydate.getMonth() < 9 ? '0': '') + (expirydate.getMonth()+1) +'-'+ (expirydate.getDate() < 9 ? '0': '') + expirydate.getDate();
				var item_expiry = expiryfinalDate;

				
			}else{
				
				var mfgdate = new Date();
				mfgdate.setDate(mfgdate.getDate() + item_days);
				var mfgfinalDate = mfgdate.getFullYear()+'-'+ (mfgdate.getMonth() < 9 ? '0': '') + (mfgdate.getMonth()+1) +'-'+ (mfgdate.getDate() < 9 ? '0': '') + mfgdate.getDate();
				var item_mfg = mfgfinalDate;
				
				var expirydate = new Date(mfgfinalDate);
				expirydate.setDate(expirydate.getDate() + item_days);
				var expiryfinalDate = expirydate.getFullYear()+'-'+ (expirydate.getMonth() < 9 ? '0': '') + (expirydate.getMonth()+1) +'-'+ (expirydate.getDate() < 9 ? '0': '') + expirydate.getDate();
				var item_expiry = expiryfinalDate;
				
			}
			
			
			var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, net_item_cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_batch_no = item.row.batch_no,  item_tax1 = item.row.tax1, item_tax2 = item.row.tax2, item_ds = item.row.item_discount_percent,item_ds_amt = item.row.item_discount_amt, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;"),selected_taxincl,selected_taxexcl,selected_taxrate,item_bill_dis = item.row.item_bill_discount, item_expiry = item.row.expiry,item_selling = item.row.item_selling_price;  
             item_ds =  item_ds ? item_ds :0
	     item_expiry = (item_expiry!='0000-00-00')?item_expiry:'';
	     
            if (item_ds) {
                
		$disType = item.row.item_dis_type;
		
		var itemdiscount = item_ds+(($disType=="p")?'%':'');
		
		item_ds_amt = calculateDiscount(itemdiscount, ((parseFloat(item.row.unit_cost)) * parseFloat(item_qty))); 
		item_ds_amt = format2Decimals(item_ds_amt);
              }
 
			var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
            var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            if (item.row.new_entry == 1) { item_bqty = 0;//item_qty;
	    item_oqty = item_qty; }
            var unit_cost = item.row.unit_cost;

            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('pi_supplier'), belong = false;
            var item_tax_method = localStorage.getItem('tax_method');
            bill_disc = localStorage.getItem('bill_disc') ? localStorage.getItem('bill_disc') : 0;
	    bill_disc_percentage = localStorage.getItem('bill_disc_percentage') ? localStorage.getItem('bill_disc_percentage') : 0;
			
			item_tax_method = (item_tax_method) ? item_tax_method : 0;

                if (supplier == item.row.supplier1) {
                    belong = true;
                } else
                if (supplier == item.row.supplier2) {
                    belong = true;
                } else
                if (supplier == item.row.supplier3) {
                    belong = true;
                } else
                if (supplier == item.row.supplier4) {
                    belong = true;
                } else
                if (supplier == item.row.supplier5) {
                    belong = true;
                }
                var unit_qty_received = qty_received;
                
                var ds = item_ds ? item_ds : '0';
                item_discount = calculateDiscount(ds, unit_cost);
                product_discount += parseFloat(item_discount * item_qty);                
                
                

                var sel_opt = '';
                $.each(item.options, function () {
                    if(this.id == item_option) {
                        sel_opt = this.name;
                    }
                });

               if(localStorage.getItem('bill_disc_percentage')){ 

                if (bill_disc_percentage.indexOf("%") !== -1) {            
                    var pds = bill_disc_percentage.split("%");
                    if (!isNaN(pds[0])) {
                        item_bill_dis = ((((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)) * parseFloat(pds[0])) / 100);
                    } else {
                         $per =  ((bill_disc_percentage /(parseFloat(unit_cost)* parseFloat(item_qty)))*100);                          
                         item_bill_dis = formatDecimal(((total * parseFloat($per)) / 100), 4);                         
                        }
                    }
                else{
		
		
		 
                }
                }else{
                    item_bill_dis =0;
                }                
 
            var row_no = (new Date).getTime();
	    $store_id= default_store_warehouse;
            if (item.store_id) {
                $store_id = item.store_id;
            }
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id +'_'+$store_id + '"></tr>');
			tr_html = '<td class="text-center">' + $i + '</td>';

             tr_html += '<td><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><span>' + item_code +' <span class="label label-default">'+item_supplier_part_no+'</span></td>';

            tr_html += '<td><input name="store_id[]" type="hidden" class="store-id" value="' + $store_id + '"><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '"><span class="sname" id="name_' + row_no + '">'+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+' <span class="label label-default">'+item_supplier_part_no+'</span></span></td>';
			//tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'"
            tr_html += '<td><input name="quantity_balance[]" type="hidden" class="rbqty" value="' + item_bqty + '"><input class="form-control text-center rquantity numberonly" name="quantity[]" type="text"  value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';
	  
			$batch_required = (item.row.batch_required==1)?' required':'';
			$mfg_required = (item.row.expiry_date_required==1)?' required':'';
			$batch_readonly= (item.row.batch_required==0)?' readonly':'';
			$mfg_readonly = (item.row.expiry_date_required==0)?' readonly':'';
			tr_html += '<td><input class="form-control rbatch_no'+$batch_required+'" '+$batch_readonly+'  name="batch_no[]" type="text" value="' + item_batch_no + '" data-id="' + row_no + '" data-item="' + item_id + '" id="batch_no_' + row_no + '" style="width:100px!important"></td>';
	    $expiryClass = 'numberonly';
	    
	    $expiry_type = (item.row.expiry_type==null || item.row.expiry_type==undefined)?"":item.row.expiry_type;
            if (item.row.expiry_type=='year') {
		$expiryClass="datepicker";
		$expiry_type ='';
		
	    }
            tr_html += '<td><label class="expiry-val-container"><input name="expiry_type[]" type="hidden" value="' + $expiry_type + '"><input class="'+$expiryClass+' form-control  rexpiry'+$mfg_required+'" '+$mfg_readonly+' name="expiry[]" type="text" value="' + item_expiry + '" data-id="' + row_no + '" data-item="' + item_id + '" id="expiry_' + row_no + '"  ><span>'+$expiry_type+'</span></label></td>';
            
			
			tr_html += '<td class="text-right"><input class="form-control text-right rucost numberonly" name="unit_cost[]" value="' + format2Decimals(unit_cost) + '" style="width:100px!important" id="rucost_' + row_no + '" ></td>';

            tr_html += '<td class="text-right"><input class="form-control text-right rugross" readonly tabindex=-1 name="unit_gross[]" value="' + format2Decimals(((parseFloat(unit_cost)) * parseFloat(item_qty))) + '" style="width:100px!important" id="rugross_' + row_no + '" ></td>';

			if (site.settings.product_discount == 1) {

			$p_checked = (item.row.item_dis_type=="p")?'checked="checked"':'';
			$f_checked = (item.row.item_dis_type=="f")?'checked="checked"':'';
                tr_html += '<td class="text-right"><div style="width:100px !important"><input class="form-control text-right input-sm rdiscount numberonly" name="item_dis[]" maxlength="20" type="text" id="discount_' + row_no + '" value="' + (item_ds != 0 ? item_ds :  '') + '" style="width:60px !important"><input type="radio" class="item-dis-type skip" name="item_dis_type['+$row_cnt+']" id="item-dis-type-p-'+$row_cnt+'" value="p" '+$p_checked+'><label for="item-dis-type-p-'+$row_cnt+'">%</label>'+'<input type="radio" class="item-dis-type skip" name="item_dis_type['+$row_cnt+']" id="item-dis-type-f-'+$row_cnt+'" value="f" '+$f_checked+'><label for="item-dis-type-f-'+$row_cnt+'">F</label></div></td>';
                tr_html += '<td class="text-right"><input class="form-control text-right input-sm rudisamt" name="item_disc_amt[]" readonly tabindex=-1 value="'+ (item_ds_amt != 0 ? item_ds_amt :  '') +  '" style="width:100px!important" id="discount_' + row_no + '" ></td>';
            }
	    
            tr_html += '<td class="text-right"><input  type="hidden" class="form-control text-right input-sm subtotal" name="subtotal[]" value="'+ format2Decimals(((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt))) +  '"  id="subtotal_' + row_no + '" ><span class="text-right rsubtotal" id="ru_subtotal_' + row_no + '">' + format2Decimals(((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt))) + '</span></td>';

            tr_html += '<td class="text-right"><input class="form-control text-right rubilldis" readonly tabindex=-1 id="ru_billdis_' + row_no + '" name="item_bill_disc_amt[]" value="' + format2Decimals(item_bill_dis) + '" style="width:100px!important"></td>';

			tr_html += '<td class="text-right"><input  type="hidden" class="form-control text-right input-sm real_unit_price" name="total[]" value="'+ formatDecimal(((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis))) +  '"  id="real_unit_price_' + row_no + '" ><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatDecimal(((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis))) + ' </span></td>';

			if(item_tax_method == 1) {
				selected_taxincl = "";
				selected_taxexcl = "selected";
			} else {
				selected_taxincl = "selected";
				selected_taxexcl = "";
			}			

              unit_cost = formatDecimal(((parseFloat(unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)));

                var pr_tax = item.tax_rate_id;
		
		console.log('pr_tax='+item.tax_rate_id+'||purchase_tax_rate='+item.tax_rate_val)
		
		purchase_tax_rate = item.tax_rate_val;
		
                var pr_tax_val = pr_tax_rate = 0;
                if ($('#tax_method').val() == 0 && (ptax = calculateTax(pr_tax, unit_cost, item_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    product_tax += pr_tax_val * item_qty;		
                }else{
		  
		    pr_tax_val = unit_cost*(purchase_tax_rate/100);
		}

                item_cost = item_tax_method == 0 ? formatDecimal(unit_cost-pr_tax_val, 4) : formatDecimal(unit_cost);
                unit_cost = formatDecimal(unit_cost+item_discount, 4);


             tr_html += '<td><select class="form-control  rtax2" name="tax2[]" value="' + item_tax2 + '" data-id="' + row_no + '" data-item="' + item_id + '" id="tax2_' + row_no + '" style="width:100px!important">';
             $.each(tax_rates, function () {
                 if(pr_tax == this.id){
                     selected_taxrate = "selected";
                 } else {
                     selected_taxrate = "";
                 }
                 tr_html += '<option value="'+ this.id +'" '+ selected_taxrate +' data-value="'+ this.rate +'"> '+ this.name +'</option>';
             });
             tr_html += '</select></td>';

            tr_html += '<td class="text-right"><input  type="hidden" class="form-control text-right input-sm item_tax" name="item_tax[]" value="'+ parseFloat(pr_tax_val) +  '"  id="item_tax_' + row_no + '" ><span class="text-right ru_taxamt" id="ru_taxamt_' + row_no + '" style="width:100px!important">' + formatDecimal(parseFloat(pr_tax_val)) + '</span></td>';
	    $landingCost = formatDecimal(((parseFloat(item.row.real_unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)+parseFloat(pr_tax_val))/parseFloat(item_qty));
	    
            tr_html += '<td class="text-right"><input  type="hidden" class="form-control text-right input-sm landcost" name="landing_cost[]" value="'+ $landingCost+  '"  id="landcost_' + row_no + '" ><span class="text-right ru_landcost" id="ru_landcost_' + row_no + '">' + formatMoney($landingCost) + '</span></td>';
$selling_price_required = (item_type=='standard')?' required':'';
            tr_html += '<td><input class="form-control ru_sellingprice numberonly'+$selling_price_required+'" name="selling_price[]" type="text" value="' + item_selling + '" data-id="' + row_no + '" data-item="' + item_id + '" id="ru_sellingprice_' + row_no + '" style="width:100px!important"></td>';  
	   
	    $margin_dif = item_selling - $landingCost;
            margin = formatDecimal(($margin_dif*100)/$landingCost);
            tr_html += '<td class="text-right"><input class="form-control input-sm text-right nmargin" name="margin[]" type="hidden" id="nmargin_' + row_no + '" value="' + margin + '"><span class="text-right ru_margin" id="ru_margin_' + row_no + '">' + margin + '</span></td>';       			
			
			tr_html += '<td class="text-right"><input class="form-control input-sm text-right ncost" name="net_cost[]" type="hidden" id="ncost_' + row_no + '" value="' + formatDecimal(((parseFloat(item.row.real_unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)+parseFloat(pr_tax_val))) + '"><input class="netucost" name="net_unit_cost[]" type="hidden" value="' + item.row.real_unit_cost + '"><span class="text-right netcost" id="netcost_' + row_no + '">' + formatMoney(((parseFloat(item.row.real_unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)+parseFloat(pr_tax_val))) + '</span></td>';          
            
            tr_html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.appendTo("#purchase_invoicesTable");
	    $('#total_no_items').val(++$total_no_items);
	    $total_no_qty = parseFloat($total_no_qty)+parseFloat(item_qty);
	    $('#total_no_qty').val($total_no_qty);
            total += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 4);

            gross_total += formatDecimal(((parseFloat(item.row.real_unit_cost)) * parseFloat(item_qty)), 4);
            item_discount_total += formatDecimal(((parseFloat(item_ds_amt))), 4);
            bill_discount_total += formatDecimal(((parseFloat(item_bill_dis))), 4);
            subtotal_total += formatDecimal(((parseFloat(item.row.real_unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)), 4);
            netamount_total += formatDecimal(((parseFloat(item.row.real_unit_cost)* parseFloat(item_qty))-parseFloat(item_ds_amt)-parseFloat(item_bill_dis)+parseFloat(pr_tax_val)), 4);

            tax_total += formatDecimal((parseFloat(pr_tax_val)), 4);                 
            an++;            
            if(!belong)
                $('#row_' + row_no).addClass('warning');
		
		
	    $row_cnt++;
        });
        var col = 6;        

        // Order level discount calculations
        if (pi_discount = localStorage.getItem('pi_discount')) {
            var ds = pi_discount;
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    order_discount = formatDecimal(((total * parseFloat(pds[0])) / 100), 4);
                } else {
                    order_discount = formatDecimal(ds);
                }
            } else {
                order_discount = formatDecimal(ds);
            }
        }

        // Order level tax calculations
        if (site.settings.tax2 != 0) {
            if (potax2 = localStorage.getItem('pi_tax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        }
                    }
                });
            }
        }
        total_discount = parseFloat(order_discount + product_discount);
        // Totals calculations after item addition
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#total').text(formatMoney(total));        
        // $('#total_no_items').val(formatQty($i));
         // $('#total_no_qty').val(formatQty(an-1));
	$zero = parseInt('0').toFixed(2);
	
	gross_total = (gross_total==0)?$zero:format2Decimals(gross_total);
	
	item_discount_total = (item_discount_total==0)?$zero:format2Decimals(item_discount_total);
	bill_discount_total = (bill_discount_total==0)?$zero:format2Decimals(bill_discount_total);
	subtotal_total = (subtotal_total==0)?$zero:format2Decimals(subtotal_total);
	tax_total = (tax_total==0)?$zero:format2Decimals(tax_total);
	//bill_disc = (bill_disc==0)?$zero:format2Decimals(bill_disc);
	$freight_val = $('#feright_chargers_shipping').val();
	$freight_val = ($freight_val==0 || $freight_val=='')?$zero:format2Decimals($freight_val);
	$round_off_val = $('#round_off_amt').val();
	$round_off_val = ($round_off_val==0 || $round_off_val=='')?$zero:format2Decimals($round_off_val);
	netamount_total +=parseFloat($round_off_val);
	netamount_total +=parseFloat($freight_val);
	netamount_total = (netamount_total==0)?$zero:format2Decimals(netamount_total);
        $('#final_gross_amt').val(gross_total);
        $('#item_disc').val(item_discount_total);
        $('.bill_disc').val(bill_disc);
	
	
        $('.bill_disc_val').val(bill_discount_total);
        $('#sub_total').val(subtotal_total);
        $('#tax').val(tax_total);

        $('#freight').val($freight_val);
        $('#round_off').val($round_off_val);

        $('.net_amt').val(netamount_total);

        $('#titems').text((an-1)+' ('+(formatQty(parseFloat(count) - 1))+')');
        $('#tds').text(formatMoney(order_discount));
        if (site.settings.tax1) {
            $('#ttax1').text(formatMoney(product_tax));
        }
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(invoice_tax));
        }
        $('#gtotal').text(formatMoney(gtotal));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        // set_page_focus();
    }
}


/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_purchase_invoices_item(item) {
    if ($('#purchase_invoicesTable tbody tr').length==0) {
	pi_items = {};
    }
    
    if ($('#pi_supplier').val()) {
	 
	$('#pi_supplier').select2("readonly", true);
    } else {
	bootbox.alert('Select Supplier');
	item = null;
	return;
    }
    
    
    if (item == null)
        return;
    
   // var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
   var item_id = item.item_id+'_'+default_store_warehouse;
   
    if (pi_items[item_id]) {
		
		bootbox.confirm('This item is already added. Do you want to add it again?', function (result) {
			
			if (result) {
				var new_qty = parseFloat(pi_items[item_id].row.qty) + 1;
				pi_items[item_id].row.base_quantity = new_qty;
				if(pi_items[item_id].row.unit != pi_items[item_id].row.base_unit) {
					$.each(pi_items[item_id].units, function(){
						if (this.id == pi_items[item_id].row.unit) {
							pi_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
						}
					});
				}
				pi_items[item_id].row.qty = new_qty;
				//pi_items[item_id].row.batch_no = '';
				//pi_items[item_id].row.mfg = '';
				//pi_items[item_id].row.expiry = '';
				pi_items[item_id].row.batch_required = item.row.batch_required;
				pi_items[item_id].row.expiry_date_required = item.row.expiry_date_required;
				pi_items[item_id].order = new Date().getTime();
				pi_items[item_id].row.item_dis_type = 'p';
				localStorage.setItem('pi_items', JSON.stringify(pi_items));
				loadItems();
				return true;
				
			}
		});
		
		

    } else {
        item.tax_rate = item.row.purchase_tax;
	item.tax_rate_id = item.row.purchase_tax;
	item.tax_rate_val = item.row.purchase_tax_rate;
	console.log('tax_rate='+item.tax_rate+'||tax_rate_id='+item.tax_rate_id+'||tax_val='+item.tax_rate_val)
        pi_items[item_id] = item;
		pi_items[item_id].row.batch_no = '';
		pi_items[item_id].row.mfg = '';
		pi_items[item_id].row.expiry = '';
		pi_items[item_id].row.batch_required = item.row.batch_required;
		pi_items[item_id].row.expiry_date_required = item.row.expiry_date_required;
		pi_items[item_id].row.tax_rate = item.purchase_tax;
		pi_items[item_id].row.expiry_type = item.row.type_expiry;
		
		pi_items[item_id].order = new Date().getTime();
		pi_items[item_id].row.item_dis_type = 'p';
		localStorage.setItem('pi_items', JSON.stringify(pi_items));
		loadItems();
		return true;
    }
	

}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
$(document).on('keypress',".numberonly",function (event){    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});

$(".number_percentage_only").keypress(function (event){
    if (event.which!=37 && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});
$(".number_minus").keypress(function (event){
    if (event.which!=45 && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }  
});

