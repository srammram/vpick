$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
    if (store_reqdiscount = localStorage.getItem('store_reqdiscount')) {
        $('#store_reqdiscount').val(store_reqdiscount);
    }
    $('#store_reqtax2').change(function (e) {
        localStorage.setItem('store_reqtax2', $(this).val());
        $('#store_reqtax2').val($(this).val());
    });
    if (store_reqtax2 = localStorage.getItem('store_reqtax2')) {
        $('#store_reqtax2').select2("val", store_reqtax2);
    }
    $('#store_reqstatus').change(function (e) {
        localStorage.setItem('store_reqstatus', $(this).val());
    });
    if (store_reqstatus = localStorage.getItem('store_reqstatus')) {
        $('#store_reqstatus').select2("val", store_reqstatus);
    }
    var old_shipping;
    $('#store_reqshipping').focus(function () {
        old_shipping = $(this).val();
    }).change(function () {
        if (!is_numeric($(this).val())) {
            $(this).val(old_shipping);
            bootbox.alert(lang.unexpected_value);
            return;
        } else {
            shipping = $(this).val() ? parseFloat($(this).val()) : '0';
        }
        localStorage.setItem('store_reqshipping', shipping);
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#gtotal').text(formatMoney(gtotal));
        $('#tship').text(formatMoney(shipping));
    });
    if (store_reqshipping = localStorage.getItem('store_reqshipping')) {
        shipping = parseFloat(store_reqshipping);
        $('#store_reqshipping').val(shipping);
    } else {
        shipping = 0;
    }

    $('#store_reqsupplier').change(function (e) {
        localStorage.setItem('store_reqsupplier', $(this).val());
        $('#supplier_id').val($(this).val());
    });
    if (store_reqsupplier = localStorage.getItem('store_reqsupplier')) {
        $('#store_reqsupplier').val(store_reqsupplier).select2({
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
                reqietMillis: 15,
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

    // If there is any item in localStorage
    if (localStorage.getItem('store_reqitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (localStorage.getItem('store_reqitems')) {
                        localStorage.removeItem('store_reqitems');
                    }
                    if (localStorage.getItem('store_reqdiscount')) {
                        localStorage.removeItem('store_reqdiscount');
                    }
                    if (localStorage.getItem('store_reqtax2')) {
                        localStorage.removeItem('store_reqtax2');
                    }
                    if (localStorage.getItem('store_reqshipping')) {
                        localStorage.removeItem('store_reqshipping');
                    }
                    if (localStorage.getItem('store_reqref')) {
                        localStorage.removeItem('store_reqref');
                    }
                    if (localStorage.getItem('store_reqwarehouse')) {
                        localStorage.removeItem('store_reqwarehouse');
                    }
					if (localStorage.getItem('store_reqfrom_store_id')) {
                        localStorage.removeItem('store_reqfrom_store_id');
                    }
					if (localStorage.getItem('store_reqto_store_id')) {
                        localStorage.removeItem('store_reqto_store_id');
                    }
					if (localStorage.getItem('store_reqstore')) {
                        localStorage.removeItem('store_reqstore');
                    }
                    if (localStorage.getItem('store_reqnote')) {
                        localStorage.removeItem('store_reqnote');
                    }
                    if (localStorage.getItem('store_reqinnote')) {
                        localStorage.removeItem('store_reqinnote');
                    }
                    if (localStorage.getItem('store_reqcustomer')) {
                        localStorage.removeItem('store_reqcustomer');
                    }
                    if (localStorage.getItem('store_reqcurrency')) {
                        localStorage.removeItem('store_reqcurrency');
                    }
                    if (localStorage.getItem('store_reqdate')) {
                        localStorage.removeItem('store_reqdate');
                    }
                    if (localStorage.getItem('store_reqstatus')) {
                        localStorage.removeItem('store_reqstatus');
                    }
                    if (localStorage.getItem('store_reqbiller')) {
                        localStorage.removeItem('store_reqbiller');
                    }

                    $('#modal-loading').show();
                    location.reload();
                }
            });
    });

// save and load the fields in and/or from localStorage

    $('#store_reqref').change(function (e) {
        localStorage.setItem('store_reqref', $(this).val());
    });
    if (store_reqref = localStorage.getItem('store_reqref')) {
        $('#store_reqref').val(store_reqref);
    }
	
    $('#store_reqwarehouse').change(function (e) {
        localStorage.setItem('store_reqwarehouse', $(this).val());
    });
    if (store_reqwarehouse = localStorage.getItem('store_reqwarehouse')) {
        $('#store_reqwarehouse').select2("val", store_reqwarehouse);
    }
	
	 $('#store_reqfrom_store_id').change(function (e) {
        localStorage.setItem('store_reqfrom_store_id', $(this).val());
	
	$('#store_reqto_store_id option').attr('disabled',false)
	$('#store_reqto_store_id option[value="'+$(this).val()+'"]').attr('disabled',true)
    });
    if (store_reqfrom_store_id = localStorage.getItem('store_reqfrom_store_id')) {
        $('#store_reqfrom_store_id').select2("val", store_reqfrom_store_id);
	$('#store_reqto_store_id option').attr('disabled',false)
	$('#store_reqto_store_id option[value="'+store_reqfrom_store_id+'"]').attr('disabled',true)
    }
	
	 $('#store_reqto_store_id').change(function (e) {
        localStorage.setItem('store_reqto_store_id', $(this).val());
    });
    if (store_reqto_store_id = localStorage.getItem('store_reqto_store_id')) {
        $('#store_reqto_store_id').select2("val", store_reqto_store_id);
    }
	
	$('#store_reqstore').change(function (e) {
        localStorage.setItem('store_reqstore', $(this).val());
    });
    if (store_reqstore = localStorage.getItem('store_reqstore')) {
        $('#store_reqstore').select2("val", store_reqstore);
    }

    $('#store_reqnote').redactor('destroy');
    $('#store_reqnote').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function (e) {
            var v = this.get();
            localStorage.setItem('store_reqnote', v);
        }
    });
    if (store_reqnote = localStorage.getItem('store_reqnote')) {
        $('#store_reqnote').redactor('set', store_reqnote);
    }
    var $customer = $('#store_reqcustomer');
    $customer.change(function (e) {
        localStorage.setItem('store_reqcustomer', $(this).val());
    });
    if (store_reqcustomer = localStorage.getItem('store_reqcustomer')) {
        $customer.val(store_reqcustomer).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url+"customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
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
        nsCustomer();
    }


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

// Order tax calculation
if (site.settings.tax2 != 0) {
    $('#store_reqtax2').change(function () {
        localStorage.setItem('store_reqtax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calculation
var old_store_reqdiscount;
$('#store_reqdiscount').focus(function () {
    old_store_reqdiscount = $(this).val();
}).change(function () {
    var new_discount = $(this).val() ? $(this).val() : '0';
    if (is_valid_discount(new_discount)) {
        localStorage.removeItem('store_reqdiscount');
        localStorage.setItem('store_reqdiscount', new_discount);
        loadItems();
        return;
    } else {
        $(this).val(old_store_reqdiscount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});

/* ----------------------
 * Delete Row Method
 * ---------------------- */

 $(document).on('click', '.store_reqdel', function () {
    var row = $(this).closest('tr');
    var item_id = row.attr('data-item-id');    
    delete store_reqitems[item_id];
    row.remove();
    if(store_reqitems.hasOwnProperty(item_id)) { } else {
        localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
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
        item = store_reqitems[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_price = formatDecimal(row.children().children('.ruprice').val()),
        discount = row.children().children('.rdiscount').val();
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price)+parseFloat(this.price);
                }
            });
        }
        var real_unit_price = item.row.real_unit_price;
        var net_price = unit_price;
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal(parseFloat(((unit_price) * parseFloat(pds[0])) / 100), 4);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_price -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                /*$.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (store_reqitems[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_price -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });*/
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
        } else {
            product_variant = 0;
        }

        uopt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if (item.units) {
            uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
            $.each(item.units, function () {
                if(this.id == item.row.unit) {
                    $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
                } else {
                    $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
                }
            });
        }

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pprice').val(unit_price);
        $('#punit_price').val(formatDecimal(parseFloat(unit_price)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_price').val(unit_price);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pserial').val(row.children().children('.rserial').val());
        $('#pdiscount').val(discount);
        $('#net_price').text(formatMoney(net_price-item_discount));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pprice, #ptax, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_price = parseFloat($('#pprice').val());
        var item = store_reqitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#ptax').val(), item_tax_method = item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            /*$.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });*/
        }

        $('#net_price').text(formatMoney(unit_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = store_reqitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var opt = $('#poption').val(), unit = $('#punit').val(), base_quantity = $('#pquantity').val(), aprice = 0;
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    aprice = parseFloat(this.price);
                }
            });
        }
        if(item.units && unit != store_reqitems[item_id].row.base_unit) {
            $.each(item.units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                    $('#pprice').val(formatDecimal(((parseFloat(item.row.base_unit_price+aprice))*unitToBaseQty(1, this)), 4)).change();
                }
            });
        } else {
            $('#pprice').val(formatDecimal(item.row.base_unit_price+aprice)).change();
        }
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = false;
        if (new_pr_tax) {
            /*$.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });*/
        }
        var price = parseFloat($('#pprice').val());
        if(item.options !== false) {
            var opt = $('#poption').val();
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    price = price-parseFloat(this.price);
                }
            });
        }
        if (site.settings.product_discount == 1 && $('#pdiscount').val()) {
            if(!is_valid_discount($('#pdiscount').val()) || $('#pdiscount').val() > price) {
                bootbox.alert(lang.unexpected_value);
                return false;
            }
        }
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        store_reqitems[item_id].row.fup = 1,
        store_reqitems[item_id].row.qty = parseFloat($('#pquantity').val()),
        store_reqitems[item_id].row.base_quantity = parseFloat(base_quantity),
        store_reqitems[item_id].row.real_unit_price = price,
        store_reqitems[item_id].row.unit = unit,
        store_reqitems[item_id].row.tax_rate = new_pr_tax,
        store_reqitems[item_id].tax_rate = new_pr_tax_rate,
        store_reqitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '',
        store_reqitems[item_id].row.option = $('#poption').val() ? $('#poption').val() : '',
        store_reqitems[item_id].row.serial = $('#pserial').val();
        localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
        $('#prModal').modal('hide');

        loadItems();
        return;
    });

    /* -----------------------
     * Product option change
     ----------------------- */
     $(document).on('change', '#poption', function () {
        var row = $('#' + $('#row_id').val()), opt = $(this).val();
        var item_id = row.attr('data-item-id');
        var item = store_reqitems[item_id];
        var unit = $('#punit').val(), base_quantity = parseFloat($('#pquantity').val()), base_unit_price = item.row.base_unit_price;
        if(unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
                if (this.id == unit) {
                    base_unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this))), 4)
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        if(item.options !== false) {
            $.each(item.options, function () {
                if(this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    $('#pprice').val(parseFloat(base_unit_price)+(parseFloat(this.price))).trigger('change');
                }
            });
        }
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        if (count == 1) {
            store_reqitems = {};
            if ($('#store_reqwarehouse').val()) {
                $('#store_reqcustomer').select2("readonly", true);
                $('#store_reqwarehouse').select2("readonly", true);
				$('#store_reqfrom_store_id').select2("readonly", true);
				$('#store_reqto_store_id').select2("readonly", true);
				$('#store_reqstore').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return false;
            }
        }
        $('#mnet_price').text('0.00');
        $('#mpro_tax').text('0.00');
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

     $(document).on('click', '#addItemManually', function (e) {
        var mid = (new Date).getTime(),
        mcode = $('#mcode').val(),
        mname = $('#mname').val(),
        mtax = parseInt($('#mtax').val()),
        mqty = parseFloat($('#mquantity').val()),
        mdiscount = $('#mdiscount').val() ? $('#mdiscount').val() : '0',
        unit_price = parseFloat($('#mprice').val()),
        mtax_rate = {};
        if (mcode && mname && mqty && unit_price) {
            /*$.each(tax_rates, function () {
                if (this.id == mtax) {
                    mtax_rate = this;
                }
            });*/

            store_reqitems[mid] = {"id": mid, "item_id": mid, "label": mname + ' (' + mcode + ')', "row": {"id": mid, "code": mcode, "name": mname, "quantity": mqty, "price": unit_price, "unit_price": unit_price, "real_unit_price": unit_price, "tax_rate": mtax, "tax_method": 0, "qty": mqty, "type": "manual", "discount": mdiscount, "serial": "", "option":""}, "tax_rate": mtax_rate, "options":false};
            localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
            loadItems();
        }
        $('#mModal').modal('hide');
        $('#mcode').val('');
        $('#mname').val('');
        $('#mtax').val('');
        $('#mquantity').val('');
        $('#mdiscount').val('');
        $('#mprice').val('');
        return false;
    });

    $(document).on('change', '#mprice, #mtax, #mdiscount', function () {
        var unit_price = parseFloat($('#mprice').val());
        var ds = $('#mdiscount').val() ? $('#mdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#mtax').val(), item_tax_method = 0;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            /*$.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)));
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / 100);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });*/
        }

        $('#mnet_price').text(formatMoney(unit_price));
        $('#mpro_tax').text(formatMoney(pr_tax_val));
    });

    /* --------------------------
     * Edit Row quantity Method
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
        store_reqitems[item_id].row.base_quantity = new_qty;
        if(store_reqitems[item_id].row.unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
                if (this.id == store_reqitems[item_id].row.unit) {
                    store_reqitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_reqitems[item_id].row.qty = new_qty;
        localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
        loadItems();
    });

    /* --------------------------
     * Edit Row Price Method
     -------------------------- */
    var old_price;
    $(document).on("focus", '.rprice', function () {
        old_price = $(this).val();
    }).on("change", '.rprice', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_price);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_price = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');
        store_reqitems[item_id].row.price = new_price;
        localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
       $('#store_reqcustomer').select2('readonly', false);
       //$('#store_reqwarehouse').select2('readonly', false);
       return false;
    });


});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for customer if no localStorage value
function nsCustomer() {
    $('#store_reqcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
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

function nsSupplier() {
    $('#store_reqsupplier').select2({
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

//localStorage.clear();
function loadItems() {

    if (localStorage.getItem('store_reqitems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;

        $("#store_reqTable tbody").empty();
        
        store_reqitems = JSON.parse(localStorage.getItem('store_reqitems'));
        sortedItems = store_reqitems;
        /*sortedItems = (site.settings.item_addition == 1) ? _.sortBy(store_reqitems, function(o) { return [parseInt(o.order)]; }) : store_reqitems;*/
        $('#add_sale, #edit_sale').attr('disabled', false);
        var c = 1;
        $.each(sortedItems, function () {
            var item = this;
            // var item_id = site.settings.item_addition == 1 ? item.item_id : item.item_id;
            var item_id = item.item_id;
            item.order = item.order ? item.order : new Date().getTime();
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_price = item.row.price, item_qty = item.row.qty, item_aqty = item.row.quantity, item_tax_method = item.row.tax_method, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_serial = item.row.serial, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");

            var unit_price = item.row.real_unit_price;
            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            if(item.units && item.row.fup != 1 && product_unit != item.row.base_unit) {
                $.each(item.units, function(){
                    if (this.id == product_unit) {
                        base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                        unit_price = formatDecimal((parseFloat(item.row.base_unit_price)*(unitToBaseQty(1, this))), 4);
                    }
                });
            }
            if(item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                        item_price = unit_price+(parseFloat(this.price));
                        unit_price = item_price;
                    }
                });
            }

            var ds = item_ds ? item_ds : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal((((unit_price) * parseFloat(pds[0])) / 100), 4);
                } else {
                    item_discount = formatDecimal(ds);
                }
            } else {
                 item_discount = formatDecimal(ds);
            }
            product_discount += parseFloat(item_discount * item_qty);

            unit_price = formatDecimal(unit_price-item_discount);
            var pr_tax = item.tax_rate;
            var pr_tax_val = 0, pr_tax_rate = 0;
            if (site.settings.tax1 == 1) {
                if (pr_tax !== false) {
                    if (pr_tax.type == 1) {

                        if (item_tax_method == '0') {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(pr_tax.rate)) / (100 + parseFloat(pr_tax.rate))), 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        } else {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(pr_tax.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        }

                    } else if (pr_tax.type == 2) {

                        pr_tax_val = parseFloat(pr_tax.rate);
                        pr_tax_rate = pr_tax.rate;

                    }
                    product_tax += pr_tax_val * item_qty;
                }
            }
            item_price = item_tax_method == 0 ? formatDecimal(unit_price-pr_tax_val, 4) : formatDecimal(unit_price);
            unit_price = formatDecimal(unit_price+item_discount, 4);
            var sel_opt = '';
            $.each(item.options, function () {
                if(this.id == item_option) {
                    sel_opt = this.name;
                }
            });
            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="product_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+'</span> </td>';
           // tr_html += '<td class="text-right"><input class="form-control input-sm text-right rprice" name="net_price[]" type="hidden" id="price_' + row_no + '" value="' + formatDecimal(item_price) + '"><input class="ruprice" name="unit_price[]" type="hidden" value="' + unit_price + '"><input class="realuprice" name="real_unit_price[]" type="hidden" value="' + item.row.real_unit_price + '"><span class="text-right sprice" id="sprice_' + row_no + '">' + formatMoney(item_price) + '</span></td>';
            tr_html += '<td><input class="form-control text-center rquantity" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="quantity[]" type="text" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';
            /*if ((site.settings.product_discount == 1 && allow_discount == 1) || item_discount) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '"><span class="text-right sdiscount text-danger" id="sdiscount_' + row_no + '">' + formatMoney(0 - (item_discount * item_qty)) + '</span></td>';
            }*/
            /*if (site.settings.tax1 == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (pr_tax_rate ? '(' + formatDecimal(pr_tax_rate) + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty) + '</span></td>';
            }*/
            //tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty))) + '</span></td>';
            /*tr_html += '<td class="text-center"><i class="fa fa-times tip pointer store_reqdel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';*/

            tr_html += '<td class="text-center"><i class="fa fa-trash-o tip pointer store_reqdel" id="' + row_no + '" title="Remove" style="cursor:pointer; color:red;"></i></td>';

            newTr.html(tr_html);
            newTr.appendTo("#store_reqTable");
            total += formatDecimal(((parseFloat(item_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 4);
            count += parseFloat(item_qty);
            an++;
            if (item_type == 'standard' && item.options !== false) {
                $.each(item.options, function () {
                    if(this.id == item_option && base_quantity > this.quantity) {
                        $('#row_' + row_no).addClass('danger');
                        if(site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    }
                });
            } else if(item_type == 'standard' && base_quantity > item_aqty) {
                $('#row_' + row_no).addClass('danger');
            } else if (item_type == 'combo') {
                if(combo_items === false) {
                    $('#row_' + row_no).addClass('danger');
                } else {
                    $.each(combo_items, function() {
                       if(parseFloat(this.quantity) < (parseFloat(this.qty)*base_quantity) && this.type == 'standard') {
                           $('#row_' + row_no).addClass('danger');
                       }
                   });
                }
            }

        });

        var col = 2;
		var tfoot = '';
        // tfoot +=  '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatQty(parseFloat(count) - 1) + '</th>';
        /*if ((site.settings.product_discount == 1 && allow_discount == 1) || product_discount) {
            tfoot += '<th class="text-right">'+formatMoney(product_discount)+'</th>';
        }*/
        /*if (site.settings.tax1 == 1) {
            tfoot += '<th class="text-right">'+formatMoney(product_tax)+'</th>';
        }*/
       // tfoot += '<th class="text-right">'+formatMoney(total)+'</th><th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#store_reqTable tfoot').html(tfoot);

        // Order level discount calculations
        if (store_reqdiscount = localStorage.getItem('store_reqdiscount')) {
            var ds = store_reqdiscount;
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    order_discount = formatDecimal((((total) * parseFloat(pds[0])) / 100), 4);
                } else {
                    order_discount = formatDecimal(ds);
                }
            } else {
                order_discount = formatDecimal(ds);
            }
        }

        // Order level tax calculations
        if (site.settings.tax2 != 0) {
            if (store_reqtax2 = localStorage.getItem('store_reqtax2')) {
                /*$.each(tax_rates, function () {
                    if (this.id == store_reqtax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        }
                    }
                });*/
            }
        }
        total_discount = parseFloat(order_discount + product_discount);
        // Totals calculations after item addition
        var gtotal = parseFloat(((total + invoice_tax) - order_discount) + shipping);
        $('#total').text(formatMoney(total));
        $('#titems').text((an - 1) + ' (' + formatQty(parseFloat(count) - 1) + ')');
        $('#total_items').val((parseFloat(count) - 1));
        $('#tds').text(formatMoney(order_discount));
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(invoice_tax));
        }
        $('#tship').text(formatMoney(shipping));
        $('#gtotal').text(formatMoney(gtotal));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
    }
}

/* -----------------------------
 * Add reqotation Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_invoice_item(item) {

    if (count == 1) {
        store_reqitems = {};
        if ($('#store_reqwarehouse').val()) {
            $('#store_reqcustomer').select2("readonly", true);
            $('#store_reqwarehouse').select2("readonly", true);
			$('#store_reqfrom_store_id').select2("readonly", true);
			$('#store_reqto_store_id').select2("readonly", true);
			$('#store_reqstore').select2("readonly", true);
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
    if (store_reqitems[item_id]) {

        var new_qty = parseFloat(store_reqitems[item_id].row.qty) + 1;
        store_reqitems[item_id].row.base_quantity = new_qty;
        if(store_reqitems[item_id].row.unit != store_reqitems[item_id].row.base_unit) {
            $.each(store_reqitems[item_id].units, function(){
                if (this.id == store_reqitems[item_id].row.unit) {
                    store_reqitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        store_reqitems[item_id].row.qty = new_qty;

    } else {
        store_reqitems[item_id] = item;
    }
    store_reqitems[item_id].order = new Date().getTime();
    localStorage.setItem('store_reqitems', JSON.stringify(store_reqitems));
    loadItems();
    return true;
	
	
}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}

$(document).ready(function(){
    $('#add-store-request input[type="submit"]').click(function(e){

    $(window).unbind('beforeunload');
    $error = false;
   
    
  if($('#store_reqTable tbody tr').length==0){
    bootbox.alert('Add Items');
    return false;
    }else{
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
    $('#add-store-request').submit();
    }
   });
})