$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
if (store_rec_discount = localStorage.getItem('store_rec_discount')) {
    $('#store_rec_discount').val(store_rec_discount);
}
$('#ptax2').change(function (e) {
    localStorage.setItem('p_tax2', $(this).val());
});
if (ptax1= localStorage.getItem('p_tax1')) {
    $('#ptax1').select2("val", ptax1);
}
$('#postatus').change(function (e) {
    localStorage.setItem('store_rec_status', $(this).val());
});
if (postatus = localStorage.getItem('store_rec_status')) {
    $('#postatus').select2("val", postatus);
}
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
    localStorage.setItem('store_rec_shipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
});
if (poshipping = localStorage.getItem('store_rec_shipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').change(function (e) {
    localStorage.setItem('store_rec_payment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('store_rec_payment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('store_rec_items')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('store_rec_items')) {
                    localStorage.removeItem('store_rec_items');
                }
                if (localStorage.getItem('store_rec_discount')) {
                    localStorage.removeItem('store_rec_discount');
                }
                if (localStorage.getItem('store_rec_tax2')) {
                    localStorage.removeItem('store_rec_tax2');
                }
                if (localStorage.getItem('store_rec_shipping')) {
                    localStorage.removeItem('store_rec_shipping');
                }
                if (localStorage.getItem('store_rec_ref')) {
                    localStorage.removeItem('store_rec_ref');
                }
                if (localStorage.getItem('store_rec_warehouse')) {
                    localStorage.removeItem('store_rec_warehouse');
                }
                if (localStorage.getItem('store_rec_note')) {
                    localStorage.removeItem('store_rec_note');
                }
                if (localStorage.getItem('store_rec_supplier')) {
                    localStorage.removeItem('store_rec_supplier');
                }
                if (localStorage.getItem('store_rec_currency')) {
                    localStorage.removeItem('store_rec_currency');
                }
                if (localStorage.getItem('store_rec_extras')) {
                    localStorage.removeItem('store_rec_extras');
                }
                if (localStorage.getItem('store_rec_date')) {
                    localStorage.removeItem('store_rec_date');
                }
                if (localStorage.getItem('store_rec_status')) {
                    localStorage.removeItem('store_rec_status');
                }
				
				if (localStorage.getItem('store_rec_from_store_id')) {
                    localStorage.removeItem('store_rec_from_store_id');
                }
				if (localStorage.getItem('store_rec_to_store_id')) {
                    localStorage.removeItem('store_rec_to_store_id');
                }
				
                if (localStorage.getItem('store_rec_payment_term')) {
                    localStorage.removeItem('store_rec_payment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#store_rec_supplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('store_rec_ref', $(this).val());
});

if (poref = localStorage.getItem('store_rec_ref')) {
    $('#poref').val(poref);
}

$('#invoice_no').change(function (e) {
    localStorage.setItem('store_rec_invoice_no', $(this).val());
});

if (store_rec_invoice_no = localStorage.getItem('store_rec_invoice_no')) {
    $('#invoice_no').val(store_rec_invoice_no);
}


$('#powarehouse').change(function (e) {
    localStorage.setItem('store_rec_warehouse', $(this).val());
});
if (powarehouse = localStorage.getItem('store_rec_warehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}

        $('#ponote').redactor('destroy');
        $('#ponote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('store_rec_note', v);
            }
        });
        if (ponote = localStorage.getItem('store_rec_note')) {
            $('#ponote').redactor('set', ponote);
        }
        $supplier.change(function (e) {
            localStorage.setItem('store_rec_supplier', $(this).val());
            $('#supplier_id').val($(this).val());
        });
        if (store_rec_supplier = localStorage.getItem('store_rec_supplier')) {
            $supplier.val(store_rec_supplier).select2({
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
}

    /*$('.rexpiry').change(function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        store_rec_items[item_id].row.expiry = $(this).val();
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
    });*/
if (localStorage.getItem('store_rec_extras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('store_rec_extras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("store_rec_extras");
    $('#extras-con').slideUp();
});
/*$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.expiry = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
});
$(document).on('change', '.rmfg', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.mfg = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
});
*/


$(document).on('change', '.rbatch_no', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    store_rec_items[item_id].row.batch_no = $(this).val();
    localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
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
        localStorage.setItem('store_rec_tax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_store_rec_discount;
$('#store_rec_discount').focus(function () {
    old_store_rec_discount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('store_rec_discount');
        localStorage.setItem('store_rec_discount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_store_rec_discount);
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
        delete store_rec_items[item_id];
        row.remove();
        if(store_rec_items.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
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
        item = store_rec_items[item_id];
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

                            if (store_rec_items[item_id].row.tax_method == 0) {
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
        var item = store_rec_items[item_id];
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
        var item = store_rec_items[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != store_rec_items[item_id].row.base_unit) {
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
        var item = store_rec_items[item_id];
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
        if(unit != store_rec_items[item_id].row.base_unit) {
            $.each(store_rec_items[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        store_rec_items[item_id].row.fup = 1,
        store_rec_items[item_id].row.qty = parseFloat($('#pquantity').val()),
        store_rec_items[item_id].row.base_quantity = parseFloat(base_quantity),
        store_rec_items[item_id].row.unit = unit,
        store_rec_items[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        store_rec_items[item_id].row.tax_rate = new_pr_tax,
		store_rec_items[item_id].row.tax_method = new_pr_tax_method,
        store_rec_items[item_id].tax_rate = new_pr_tax_rate,
        store_rec_items[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        store_rec_items[item_id].row.option = $('#poption').val(),		
		store_rec_items[item_id].row.tax1 = $('#ptax1').val(),
		store_rec_items[item_id].row.tax2 = $('#ptax2').val(),		
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
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
        store_rec_items[item_id].row.base_quantity = new_qty;
        if(store_rec_items[item_id].row.unit != store_rec_items[item_id].row.base_unit) {
            $.each(store_rec_items[item_id].units, function(){
                if (this.id == store_rec_items[item_id].row.unit) {
                    store_rec_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_rec_items[item_id].row.qty = new_qty;
        store_rec_items[item_id].row.received = new_qty;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });
	
	$(document).on("change", '.rtax1', function () {
		var row = $(this).closest('tr');
		current_tax_method = $(this).val();
		item_id = row.attr('data-item-id');
		store_rec_items[item_id].row.tax_method = current_tax_method;
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
		loadItems();
	});
	
	$(document).on("change", '.rtax2', function () {
		var row = $(this).closest('tr');
		current_tax = $(this).val();
		item_id = row.attr('data-item-id');
		store_rec_items[item_id].tax_rate = current_tax;
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
		loadItems();
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
        if (new_received > store_rec_items[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(store_rec_items[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        store_rec_items[item_id].row.unit_received = new_received;
        store_rec_items[item_id].row.received = qty_received;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("change", '.rucost', function () {
        var row = $(this).closest('tr');
        current_ucost = $(this).val();
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.real_unit_cost = current_ucost;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("change", '.rdiscount', function () {
        var row = $(this).closest('tr');
        current_discount = $(this).val();
        item_id = row.attr('data-item-id');
        store_rec_items[item_id].row.discount = current_discount;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
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
        store_rec_items[item_id].row.cost = new_cost;
        localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#store_rec_supplier').select2('readonly', false);
     return false;
 });

    if (store_receivers_edit) {
        $('#store_rec_supplier').select2("readonly", true);
    }

});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
	
    $('#store_rec_supplier').select2({
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
	
    if (localStorage.getItem('store_rec_items')) {
        
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#store_receiversTable tbody").empty();
		
        store_rec_items = JSON.parse(localStorage.getItem('store_rec_items'));

		sortedItems = store_rec_items;
       // sortedItems = (site.settings.item_addition == 1) ? _.sortBy(store_rec_items, function(o){return [parseInt(o.order)];}) : store_rec_items;
        var order_no = new Date().getTime();
		
        $.each(sortedItems, function () {			
			
            var item = this;
            //var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
			 var item_id = item.item_id;
			 
			 var item_available_qty = item.row.current_quantity ? item.row.current_quantity : 0;
			 var item_transfer_qty = item.row.transfer_quantity ? item.row.transfer_quantity : 0;
			 var item_pending_qty = item.row.pending_quantity ? item.row.pending_quantity : 0;
			
            item.order = item.order ? item.order : order_no++;
            
			var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, net_item_cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_batch_no = item.row.batch_no, item_mfg = item.row.mfg, item_expiry = item.row.expiry, item_tax_method = item.row.tax_method, item_tax1 = item.row.tax1, item_tax2 = item.row.tax2, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;"),selected_taxincl,selected_taxexcl,selected_taxrate;
            
			var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
            var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            if (item.row.new_entry == 1) { item_bqty = item_qty; item_oqty = item_qty; }
            var unit_cost = item.row.real_unit_cost;

            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('store_rec_supplier'), belong = false;
			
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
                /*if(item.row.fup != 1 && product_unit != item.row.base_unit) {
                    $.each(item.units, function(){
                        if (this.id == product_unit) {
                            base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                            unit_qty_received = item.row.unit_received ? item.row.unit_received : formatDecimal(baseToUnitQty(qty_received, this), 4);
                            unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 4);
                        }
                    });
                }*/
                var ds = item_ds ? item_ds : '0';
                item_discount = calculateDiscount(ds, unit_cost);
                product_discount += parseFloat(item_discount * item_qty);
                
                
                unit_cost = formatDecimal(unit_cost-item_discount);
                var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, unit_cost, item_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    product_tax += pr_tax_val * item_qty;
                }
               item_cost = item_tax_method == 0 ? formatDecimal(unit_cost-pr_tax_val, 4) : formatDecimal(unit_cost);
               unit_cost = formatDecimal(unit_cost+item_discount, 4);
                var sel_opt = '';
                $.each(item.options, function () {
                    if(this.id == item_option) {
                        sel_opt = this.name;
                    }
                });

            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
			
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+' <span class="label label-default">'+item_supplier_part_no+'</span></span></td>';
			
			/* 
			<i class="pull-right fa fa-edit tip edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i>
			tr_html += '<td><input class="form-control rbatch_no" name="batch_no[]" type="text" value="' + item_batch_no + '" data-id="' + row_no + '" data-item="' + item_id + '" id="batch_no_' + row_no + '"></td>';
			 
            if (site.settings.product_expiry == 1) {
				 tr_html += '<td><input class="form-control date rmfg" name="mfg[]" type="text" value="' + item_mfg + '" data-id="' + row_no + '" data-item="' + item_id + '" id="mfg_' + row_no + '"></td>';
                tr_html += '<td><input class="form-control date rexpiry" name="expiry[]" type="text" value="' + item_expiry + '" data-id="' + row_no + '" data-item="' + item_id + '" id="expiry_' + row_no + '"></td>';
            }*/
			
			//tr_html += '<td class="text-right"><input class="form-control rucost" name="unit_cost[]" value="' + unit_cost + '"></td>';
			tr_html += '<td><input name="quantity_balance[]" type="hidden" class="rbqty" value="' + item_bqty + '"><input class="form-control text-center rquantity" readonly name="quantity[]" type="text" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';
			
			
			if(item_tax_method == 1) {
				selected_taxincl = "";
				selected_taxexcl = "selected";
			} else {
				selected_taxincl = "selected";
				selected_taxexcl = "";
			}
			
			 //tr_html += '<td><select class="form-control  rtax1" name="tax1[]" value="' + item_tax1 + '" data-id="' + row_no + '" data-item="' + item_id + '" id="tax1_' + row_no + '"><option value="0" '+selected_taxincl+'>Inclusive</option><option value="1" '+selected_taxexcl+'>Exclusive</option></select></td>';
				 
			 // tr_html += '<td><select class="form-control  rtax2" name="tax2[]" value="' + item_tax2 + '" data-id="' + row_no + '" data-item="' + item_id + '" id="tax2_' + row_no + '">';
			 $.each(tax_rates, function () {
				 if(pr_tax == this.id){
					 selected_taxrate = "selected";
				 } else {
					 selected_taxrate = "";
				 }
				 tr_html += '<option value="'+ this.id +'" '+ selected_taxrate +'> '+ this.name +'</option>';
			 });
			 
			 //tr_html += '</select></td>';
			
            
			
           /* if (store_rec_edit) {
                tr_html += '<td class="rec_con"><input name="ordered_quantity[]" type="hidden" class="oqty" value="' + item_oqty + '"><input class="form-control text-center received" name="received[]" type="text" value="' + formatDecimal(unit_qty_received) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="received_' + row_no + '" onClick="this.select();"><input name="received_base_quantity[]" type="hidden" class="rrbase_quantity" value="' + qty_received + '"></td>';
            }*/
            if (site.settings.product_discount == 1) {
               // tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="text" id="discount_' + row_no + '" value="' + (item_ds != 0 ? item_ds :  '') + '"></td>';
            }
			
			//tr_html += '<td class="text-right"><input class="form-control input-sm text-right rcost" name="net_cost[]" type="hidden" id="cost_' + row_no + '" value="' + item_cost + '"><input class="realucost" name="real_unit_cost[]" type="hidden" value="' + item.row.real_unit_cost + '"><span class="text-right scost" id="scost_' + row_no + '">' + formatMoney(item_cost) + '</span></td>';
			
			if (site.settings.tax1 == 1) {
               // tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + (pr_tax == '[object Object]' ? item.tax_rate.id : pr_tax  ) + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (pr_tax_rate ? '(' + pr_tax_rate + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty) + '</span></td>';
            }
           
            //tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty))) + '</span></td>';
			
			
			 tr_html += '<td><input class="form-control date ravailable_qty" readonly name="available_qty[]" type="text" value="' + item_available_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="available_qty_' + row_no + '"></td>';
			 
			  tr_html += '<td><input class="form-control date rtransfer_qty" readonly name="transfer_qty[]" type="text" value="' + item_transfer_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="transfer_qty_' + row_no + '"></td>';
			  
			  tr_html += '<td><input class="form-control date rpending_qty" readonly name="pending_qty[]" type="text" value="' + item_pending_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="pending_qty_' + row_no + '"></td>';
			
            tr_html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.appendTo("#store_receiversTable");
            total += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 4);
            count += parseFloat(item_qty);
            an++;
            if(!belong)
                $('#row_' + row_no).addClass('warning');
        });
		

        var col = 1;
        // if (site.settings.product_expiry == 1) { col++; }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatQty(parseFloat(count) - 1) + '</th>';
        if (store_receivers_edit) {
            //tfoot += '<th  class="rec_con"></th>';
        }
        if (site.settings.product_discount == 1) {
           // tfoot += '<th colspan="3" class="text-right">'+formatMoney(product_discount)+'</th>';
        }
        if (site.settings.tax1 == 1) {
            tfoot += '<th colspan="3"  class="text-right"></th>';
        }
        tfoot += '<th class="text-center" colspan="3"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#store_receiversTable tfoot').html(tfoot);

        // Order level discount calculations
        if (store_rec_discount = localStorage.getItem('store_rec_discount')) {
            var ds = store_rec_discount;
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
            if (potax2 = localStorage.getItem('store_rec_tax2')) {
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
        set_page_focus();
    }
}


/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_store_receivers_item(item) {
    if (count == 1) {
        store_rec_items = {};
        if ($('#store_rec_supplier').val()) {
             
            $('#store_rec_supplier').select2("readonly", true);
        } else {
            bootbox.alert(lang.select_above);
            item = null;
            return;
        }
    }
    if (item == null)
        return;

   // var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
   var item_id = item.item_id;
   
    if (store_rec_items[item_id]) {
		
		bootbox.confirm('This item is already added. Do you want to add it again?', function (result) {
			
			if (result) {
				var new_qty = parseFloat(store_rec_items[item_id].row.qty) + 1;
				store_rec_items[item_id].row.base_quantity = new_qty;
				if(store_rec_items[item_id].row.unit != store_rec_items[item_id].row.base_unit) {
					$.each(store_rec_items[item_id].units, function(){
						if (this.id == store_rec_items[item_id].row.unit) {
							store_rec_items[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
						}
					});
				}
				store_rec_items[item_id].row.qty = new_qty;
				store_rec_items[item_id].row.batch_no = '';
				store_rec_items[item_id].row.mfg = '';
				store_rec_items[item_id].order = new Date().getTime();
				localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
				loadItems();
				return true;
				
			}
		});
		
		

    } else {
        store_rec_items[item_id] = item;
		store_rec_items[item_id].row.batch_no = '';
		store_rec_items[item_id].row.mfg = '';
		store_rec_items[item_id].order = new Date().getTime();
		localStorage.setItem('store_rec_items', JSON.stringify(store_rec_items));
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
