<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('group_permissions'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row group-permission">
            <div class="col-lg-12">

                <p class="introtext"><?= lang("set_permissions"); ?></p>

                <?php if (!empty($p)) {
                    if ($p->group_id != 1) {

                        echo admin_form_open("system_settings/permissions/" . $id); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                                <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("group_permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center"><?= lang("module_name"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th class="text-center"><?= lang("view"); ?></th>
                                    <th class="text-center"><?= lang("add"); ?></th>
                                    <th class="text-center"><?= lang("edit"); ?></th>
                                    <th class="text-center"><?= lang("delete"); ?></th>
                                    <th class="text-center"><?= lang("misc"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= lang("products"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="products-index" <?php echo $p->{'products-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="products-add" <?php echo $p->{'products-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="products-edit" <?php echo $p->{'products-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="products-delete" <?php echo $p->{'products-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
					<ul>
					<li>
					<input type="checkbox" value="1" id="products-import_csv" class="checkbox" name="products-import_csv" <?php echo $p->{'products-import_csv'} ? "checked" : ''; ?>>
                                        <label for="products-import_csv" class="padding05"><?= lang('import_csv') ?></label>
					</li>
					<li>
                                        <input type="checkbox" value="1" id="products-cost" class="checkbox" name="products-cost" <?php echo $p->{'products-cost'} ? "checked" : ''; ?>>
                                        <label for="products-cost" class="padding05"><?= lang('product_cost') ?></label>
                                        </li>
					<li>
					<input type="checkbox" value="1" id="products-price" class="checkbox" name="products-price" <?php echo $p->{'products-price'} ? "checked" : ''; ?>>
                                        <label for="products-price" class="padding05"><?= lang('product_price') ?></label>
					</li>
					<li>
                                        <input type="checkbox" value="1" id="products-adjustments" class="checkbox" name="products-adjustments" <?php echo $p->{'products-adjustments'} ? "checked" : ''; ?>>
                                        <label for="products-adjustments" class="padding05"><?= lang('adjustments') ?></label>
					</li>
					<li>
                                        <input type="checkbox" value="1" id="products-barcode" class="checkbox" name="products-barcode" <?php echo $p->{'products-barcode'} ? "checked" : ''; ?>>
                                        <label for="products-barcode" class="padding05"><?= lang('print_barcodes') ?></label>
					</li>
					<li>
                                        <input type="checkbox" value="1" id="products-stock_count" class="checkbox" name="products-stock_count" <?php echo $p->{'products-stock_count'} ? "checked" : ''; ?>>
                                        <label for="products-stock_count" class="padding05"><?= lang('stock_counts') ?></label>
					</li>
					</ul>
				    </td>
                                </tr>
				<tr>
				    <td><?= lang("sale_items"); ?></td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="recipe-index" <?php echo $p->{'recipe-index'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="recipe-add" <?php echo $p->{'recipe-add'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="recipe-edit" <?php echo $p->{'recipe-edit'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="recipe-delete" <?php echo $p->{'recipe-delete'} ? "checked" : ''; ?>>
				    </td>
				    <td>
					<input type="checkbox" value="1" id="recipe-csv" class="checkbox" name="recipe-csv" <?php echo $p->{'recipe-csv'} ? "checked" : ''; ?>>
					<label for="recipe-csv" class="padding05"><?= lang('csv') ?></label>
				    </td>
				</tr>
                                
				 <tr>
				    <td><?= lang("production"); ?></td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="production-index" <?php echo $p->{'production-index'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="production-add" <?php echo $p->{'production-add'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="production-edit" <?php echo $p->{'production-edit'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="production-delete" <?php echo $p->{'production-delete'} ? "checked" : ''; ?>>
				    </td>
				    <td>
					<ul>
					<li>
					    <input type="checkbox" value="1" id="production-balance" class="checkbox" name="production-balance" <?php echo $p->{'production-balance'} ? "checked" : ''; ?>>
					    <label for="production-balance" class="padding05"><?= lang('balance_production') ?></label>
					</li>
					<li>
					    <input type="checkbox" value="1" id="production-balance_edit" class="checkbox" name="production-balance_edit" <?php echo $p->{'production-balance_edit'} ? "checked" : ''; ?>>
					    <label for="production-balance_edit" class="padding05"><?= lang('balance_edit') ?></label>
					</li>
					</ul>
				    </td>
				 </tr>
				 <tr>
				    <td><?= lang("bill_of_material"); ?></td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="saleitem_to_purchasesitem-index" <?php echo $p->{'saleitem_to_purchasesitem-index'} ? "checked" : ''; ?>>
				    </td>
				 </tr>
				<tr>
                                    <td><?= lang("sales"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-index" <?php echo $p->{'sales-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-add" <?php echo $p->{'sales-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-edit" <?php echo $p->{'sales-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-delete" <?php echo $p->{'sales-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="sales-email" class="checkbox" name="sales-email" <?php echo $p->{'sales-email'} ? "checked" : ''; ?>>
                                        <label for="sales-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="sales-pdf" class="checkbox" name="sales-pdf" <?php echo $p->{'sales-pdf'} ? "checked" : ''; ?>>
                                        <label for="sales-pdf" class="padding05"><?= lang('pdf') ?></label>
                                        <?php if (POS) { ?>
                                            <input type="checkbox" value="1" id="pos-index" class="checkbox" name="pos-index" <?php echo $p->{'pos-index'} ? "checked" : ''; ?>>
                                            <label for="pos-index" class="padding05"><?= lang('pos') ?></label>
                                        <?php } ?>
										
										
										
										
										
										
                                        <input type="checkbox" value="1" id="sales-payments" class="checkbox" name="sales-payments" <?php echo $p->{'sales-payments'} ? "checked" : ''; ?>>
                                        <label for="sales-payments" class="padding05"><?= lang('payments') ?></label>
                                        <input type="checkbox" value="1" id="sales-return_sales" class="checkbox" name="sales-return_sales" <?php echo $p->{'sales-return_sales'} ? "checked" : ''; ?>>
                                        <label for="sales-return_sales" class="padding05"><?= lang('return_sales') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("deliveries"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-deliveries" <?php echo $p->{'sales-deliveries'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-add_delivery" <?php echo $p->{'sales-add_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-edit_delivery" <?php echo $p->{'sales-edit_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-delete_delivery" <?php echo $p->{'sales-delete_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <!--<input type="checkbox" value="1" id="sales-email" class="checkbox" name="sales-email_delivery" <?php echo $p->{'sales-email_delivery'} ? "checked" : ''; ?>><label for="sales-email_delivery" class="padding05"><?= lang('email') ?></label>-->
                                        <input type="checkbox" value="1" id="sales-pdf" class="checkbox" name="sales-pdf_delivery" <?php echo $p->{'sales-pdf_delivery'} ? "checked" : ''; ?>>
                                        <label for="sales-pdf_delivery" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>
                                 <tr class="restaurants-group-permission">
				    <td><?= lang("Restaurants"); ?></td>
				    <td colspan="4"></td>
				    <td class="text-center">
					<ul>
					    <li><?=lang('warehouses')?></li>
					    <li>
						<input type="checkbox" value="1" id="system_settings-warehouses" class="checkbox" name="system_settings-warehouses" <?php echo $p->{'system_settings-warehouses'} ? "checked" : ''; ?>>
						<label for="brances-view" class="padding05"><?= lang('view') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="system_settings-add_warehouse" class="checkbox" name="system_settings-add_warehouse" <?php echo $p->{'system_settings-add_warehouse'} ? "checked" : ''; ?>>
						<label for="warehouse-add" class="padding05"><?= lang('add') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="system_settings-edit_warehouse" class="checkbox" name="system_settings-edit_warehouse" <?php echo $p->{'system_settings-edit_warehouse'} ? "checked" : ''; ?>>
						<label for="warehouse-edit" class="padding05"><?= lang('edit') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="system_settings-delete_warehouse" class="checkbox" name="system_settings-delete_warehouse" <?php echo $p->{'system_settings-delete_warehouse'} ? "checked" : ''; ?>>
						<label for="warehouse-delete" class="padding05"><?= lang('Delete') ?></label>
					    </li>
					</ul>
					<ul>
					    <li><?=lang('table_types')?></li>
					    <li>
						<input type="checkbox" value="1" id="tables-areas" class="checkbox" name="tables-areas" <?php echo $p->{'tables-areas'} ? "checked" : ''; ?>>
						<label for="tables-areas" class="padding05"><?= lang('view') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-add_area" class="checkbox" name="tables-add_area" <?php echo $p->{'tables-add_area'} ? "checked" : ''; ?>>
						<label for="tables-add_area" class="padding05"><?= lang('add') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-edit_area" class="checkbox" name="tables-edit_area" <?php echo $p->{'tables-edit_area'} ? "checked" : ''; ?>>
						<label for="tables-edit_area" class="padding05"><?= lang('edit') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-delete_area" class="checkbox" name="tables-delete_area" <?php echo $p->{'tables-delete_area'} ? "checked" : ''; ?>>
						<label for="tables-delete_area" class="padding05"><?= lang('delete') ?></label>
					    </li>
					    
					</ul>
					<ul>
					    <li><?=lang('tables')?></li>
					    <li>
						<input type="checkbox" value="1" id="tables-index" class="checkbox" name="tables-index" <?php echo $p->{'tables-index'} ? "checked" : ''; ?>>
						<label for="tables-index" class="padding05"><?= lang('view') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-add" class="checkbox" name="tables-add" <?php echo $p->{'tables-add'} ? "checked" : ''; ?>>
						<label for="tables-add" class="padding05"><?= lang('add') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-edit" class="checkbox" name="tables-edit" <?php echo $p->{'tables-edit'} ? "checked" : ''; ?>>
						<label for="tables-edit" class="padding05"><?= lang('edit') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-delete" class="checkbox" name="tables-delete" <?php echo $p->{'tables-delete'} ? "checked" : ''; ?>>
						<label for="tables-delete" class="padding05"><?= lang('delete') ?></label>
					    </li>
					    
					</ul>
					<ul>
					    <li><?=lang('Kitchens')?></li>
					    <li>
						<input type="checkbox" value="1" id="tables-kitchens" class="checkbox" name="tables-kitchens" <?php echo $p->{'tables-kitchens'} ? "checked" : ''; ?>>
						<label for="tables-kitchens" class="padding05"><?= lang('view') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-add_kitchen" class="checkbox" name="tables-add_kitchen" <?php echo $p->{'tables-add_kitchen'} ? "checked" : ''; ?>>
						<label for="tables-add_kitchen" class="padding05"><?= lang('add') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-edit_kitchen" class="checkbox" name="tables-edit_kitchen" <?php echo $p->{'tables-edit_kitchen'} ? "checked" : ''; ?>>
						<label for="tables-edit_kitchen" class="padding05"><?= lang('edit') ?></label>
					    </li>
					    <li>
						<input type="checkbox" value="1" id="tables-delete_kitchen" class="checkbox" name="tables-delete_kitchen" <?php echo $p->{'tables-delete_kitchen'} ? "checked" : ''; ?>>
						<label for="tables-delete_kitchen" class="padding05"><?= lang('delete') ?></label>
					    </li>
					    
					</ul>
				    </td>
				 </tr>
				 <tr>
                                    <td><?= lang("gift_cards"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-gift_cards" <?php echo $p->{'sales-gift_cards'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-add_gift_card" <?php echo $p->{'sales-add_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-edit_gift_card" <?php echo $p->{'sales-edit_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="sales-delete_gift_card" <?php echo $p->{'sales-delete_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("quotes"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="quotes-index" <?php echo $p->{'quotes-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="quotes-add" <?php echo $p->{'quotes-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="quotes-edit" <?php echo $p->{'quotes-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="quotes-delete" <?php echo $p->{'quotes-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="quotes-email" class="checkbox" name="quotes-email" <?php echo $p->{'quotes-email'} ? "checked" : ''; ?>>
                                        <label for="quotes-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="quotes-pdf" class="checkbox" name="quotes-pdf" <?php echo $p->{'quotes-pdf'} ? "checked" : ''; ?>>
                                        <label for="quotes-pdf" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("purchases_order"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases_order-index" <?php echo $p->{'purchases_order-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases_order-add" <?php echo $p->{'purchases_order-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases_order-edit" <?php echo $p->{'purchases_order-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases_order-delete" <?php echo $p->{'purchases_order-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="purchases-order-email" class="checkbox" name="purchases_order-email" <?php echo $p->{'purchases_order-email'} ? "checked" : ''; ?>>
                                        <label for="purchases-order-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="purchases-order-pdf" class="checkbox" name="purchases_order-pdf" <?php echo $p->{'purchases_order-pdf'} ? "checked" : ''; ?>>
                                        <label for="purchases-order-pdf" class="padding05"><?= lang('pdf') ?></label>
                                        <input type="checkbox" value="1" id="purchases-order-payments" class="checkbox" name="purchases_order-payments" <?php echo $p->{'purchases-order-payments'} ? "checked" : ''; ?>>
                                        <label for="purchases-order-payments" class="padding05"><?= lang('payments') ?></label>
                                        <input type="checkbox" value="1" id="purchases-order-expenses" class="checkbox" name="purchases_order-expenses" <?php echo $p->{'purchases_order-expenses'} ? "checked" : ''; ?>>
                                        <label for="purchases-order-expenses" class="padding05"><?= lang('expenses') ?></label>
                                        <input type="checkbox" value="1" id="purchases-order-return" class="checkbox" name="purchases_order-return" <?php echo $p->{'purchases_order-return'} ? "checked" : ''; ?>>
                                        <label for="purchases-order-return" class="padding05"><?= lang('purchases-order-return') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("purchases"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases-index" <?php echo $p->{'purchases-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases-add" <?php echo $p->{'purchases-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases-edit" <?php echo $p->{'purchases-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="purchases-delete" <?php echo $p->{'purchases-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="purchases-email" class="checkbox" name="purchases-email" <?php echo $p->{'purchases-email'} ? "checked" : ''; ?>>
                                        <label for="purchases-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="purchases-pdf" class="checkbox" name="purchases-pdf" <?php echo $p->{'purchases-pdf'} ? "checked" : ''; ?>>
                                        <label for="purchases-pdf" class="padding05"><?= lang('pdf') ?></label>
                                        <input type="checkbox" value="1" id="purchases-payments" class="checkbox" name="purchases-payments" <?php echo $p->{'purchases-payments'} ? "checked" : ''; ?>>
                                        <label for="purchases-payments" class="padding05"><?= lang('payments') ?></label>
                                        <input type="checkbox" value="1" id="purchases-expenses" class="checkbox" name="purchases-expenses" <?php echo $p->{'purchases-expenses'} ? "checked" : ''; ?>>
                                        <label for="purchases-expenses" class="padding05"><?= lang('expenses') ?></label>
                                        <input type="checkbox" value="1" id="purchases-return_purchases" class="checkbox" name="purchases-return_purchases" <?php echo $p->{'purchases-return_purchases'} ? "checked" : ''; ?>>
                                        <label for="purchases-return_purchases" class="padding05"><?= lang('return_purchases') ?></label>
                                    </td>
                                </tr>
                                
                                

                                <tr>
                                    <td><?= lang("transfers"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="transfers-index" <?php echo $p->{'transfers-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="transfers-add" <?php echo $p->{'transfers-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="transfers-edit" <?php echo $p->{'transfers-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="transfers-delete" <?php echo $p->{'transfers-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="transfers-email" class="checkbox" name="transfers-email" <?php echo $p->{'transfers-email'} ? "checked" : ''; ?>>
                                        <label for="transfers-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="transfers-pdf" class="checkbox" name="transfers-pdf" <?php echo $p->{'transfers-pdf'} ? "checked" : ''; ?>>
                                        <label for="transfers-pdf" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("customers"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="customers-index" <?php echo $p->{'customers-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="customers-add" <?php echo $p->{'customers-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="customers-edit" <?php echo $p->{'customers-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="customers-delete" <?php echo $p->{'customers-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="customers-deposits" class="checkbox" name="customers-deposits" <?php echo $p->{'customers-deposits'} ? "checked" : ''; ?>>
                                        <label for="customers-deposits" class="padding05"><?= lang('deposits') ?></label>
                                        <input type="checkbox" value="1" id="customers-delete_deposit" class="checkbox" name="customers-delete_deposit" <?php echo $p->{'customers-delete_deposit'} ? "checked" : ''; ?>>
                                        <label for="customers-delete_deposit" class="padding05"><?= lang('delete_deposit') ?></label>
                                    </td>
                                </tr>

                                <tr>
				    <td><?= lang("Users"); ?></td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="auth-users" <?php echo $p->{'auth-users'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="auth-create_user" <?php echo $p->{'auth-create_user'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="auth-profile" <?php echo $p->{'auth-profile'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="auth-delete" <?php echo $p->{'auth-delete'} ? "checked" : ''; ?>>
				    </td>
				    <td>
                                        <input type="checkbox" value="1" id="auth-excel" class="checkbox" name="auth-excel" <?php echo $p->{'auth-excel'} ? "checked" : ''; ?>>
                                        <label for="auth-excel" class="padding05"><?= lang('export_to_excel') ?></label>
				    </td>
				</tr>
				 <tr>
				    <td><?= lang("Counters"); ?></td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="billers-index" <?php echo $p->{'billers-index'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="billers-add" <?php echo $p->{'billers-add'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="billers-edit" <?php echo $p->{'billers-edit'} ? "checked" : ''; ?>>
				    </td>
				    <td class="text-center">
					<input type="checkbox" value="1" class="checkbox" name="billers-delete" <?php echo $p->{'billers-delete'} ? "checked" : ''; ?>>
				    </td>
				     <td>
					<input type="checkbox" value="1" id="billers-excel" class="checkbox" name="billers-excel" <?php echo $p->{'billers-excel'} ? "checked" : ''; ?>>
					<label for="billers-excel" class="padding05"><?= lang('export_to_excel') ?></label>
				    </td>
				</tr>
				<tr>
                                    <td><?= lang("suppliers"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="suppliers-index" <?php echo $p->{'suppliers-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="suppliers-add" <?php echo $p->{'suppliers-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="suppliers-edit" <?php echo $p->{'suppliers-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="suppliers-delete" <?php echo $p->{'suppliers-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                    </td>
                                </tr>

                                <tr>
								    <td><?= lang("material_request"); ?></td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="material_request-index" <?php echo $p->{'material_request-index'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="material_request-add" <?php echo $p->{'material_request-add'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="material_request-edit" <?php echo $p->{'material_request-edit'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="material_request-delete" <?php echo $p->{'material_request-delete'} ? "checked" : ''; ?>>
								    </td>
								</tr>

								 <tr>
								    <td><?= lang("categories"); ?></td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="system_settings_categories" <?php echo $p->{'system_settings_categories'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="system_settings_categories_add" <?php echo $p->{'system_settings_categories_add-add'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="system_settings_categories_edit" <?php echo $p->{'system_settings_categories_edit'} ? "checked" : ''; ?>>
								    </td>
								    <td class="text-center">
									<input type="checkbox" value="1" class="checkbox" name="system_settings_categories_delete" <?php echo $p->{'system_settings_categories_delete'} ? "checked" : ''; ?>>
								    </td>
								</tr>
				         <tr class="reports">
                                    <td><?= lang("reports"); ?></td>
                                    <td colspan="5">
					<ul>
					    
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="warehouse_stock" name="reports-warehouse_stock" <?php echo $p->{'reports-warehouse_stock'} ? "checked" : ''; ?>>
						<label for="warehouse_stock" class="padding05"><?= lang('warehouse_stock') ?></label>
					    </span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="tax_reports" name="reports-tax_reports" <?php echo $p->{'reports-tax_reports'} ? "checked" : ''; ?>>
						    <label for="tax_reports" class="padding05"><?= lang('tax_reports') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="best_sellers" name="reports-best_sellers" <?php echo $p->{'reports-best_sellers'} ? "checked" : ''; ?>>
						    <label for="best_sellers" class="padding05"><?= lang('best_sellers') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="recipe" name="reports-recipe" <?php echo $p->{'reports-recipe'} ? "checked" : ''; ?>>
						    <label for="recipe" class="padding05"><?= lang('recipe') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="pos_settlement" name="reports-pos_settlement" <?php echo $p->{'reports-pos_settlement'} ? "checked" : ''; ?>>
						    <label for="pos_settlement" class="padding05"><?= lang('pos_settlement') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="kot_details" name="reports-kot_details" <?php echo $p->{'reports-kot_details'} ? "checked" : ''; ?>>
						    <label for="kot_details" class="padding05"><?= lang('kot_details') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="user_reports" name="reports-user_reports" <?php echo $p->{'reports-user_reports'} ? "checked" : ''; ?>>
						    <label for="user_reports" class="padding05"><?= lang('user_reports') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="home_delivery" name="reports-home_delivery" <?php echo $p->{'reports-home_delivery'} ? "checked" : ''; ?>>
						    <label for="home_delivery" class="padding05"><?= lang('home_delivery') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="takeaway" name="reports-take_away" <?php echo $p->{'reports-take_away'} ? "checked" : ''; ?>>
						    <label for="takeaway" class="padding05"><?= lang('takeaway') ?></label>
						</span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="daily_sales" name="reports-daily_sales" <?php echo $p->{'reports-daily_sales'} ? "checked" : ''; ?>>
						<label for="daily_sales" class="padding05"><?= lang('daily_sales') ?></label>
					    </span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="bill_details" name="reports-bill_details" <?php echo $p->{'reports-bill_details'} ? "checked" : ''; ?>>
						    <label for="bill_details" class="padding05"><?= lang('bill_details') ?></label>
						</span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="monthly_sales" name="reports-monthly_sales" <?php echo $p->{'reports-monthly_sales'} ? "checked" : ''; ?>>
						<label for="monthly_sales" class="padding05"><?= lang('monthly_sales') ?></label>
					    </span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="hourly_wise" name="reports-hourly_wise" <?php echo $p->{'reports-hourly_wise'} ? "checked" : ''; ?>>
						    <label for="hourly_wise" class="padding05"><?= lang('hourly_sales') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="discount_summary" name="reports-discount_summary" <?php echo $p->{'reports-discount_summary'} ? "checked" : ''; ?>>
						    <label for="discount_summary" class="padding05"><?= lang('discount_summary') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="void_bills" name="reports-void_bills" <?php echo $p->{'reports-void_bills'} ? "checked" : ''; ?>>
						    <label for="void_bills" class="padding05"><?= lang('void_bills') ?></label>
						</span>
					    </li>
					    <li>
						<span style="inline-block">
						    <input type="checkbox" value="1" class="checkbox" id="popular_analysis" name="reports-popular_analysis" <?php echo $p->{'reports-popular_analysis'} ? "checked" : ''; ?>>
						    <label for="popular_analysis" class="padding05"><?= lang('popular_analysis') ?></label>
						</span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="product_quantity_alerts" name="reports-quantity_alerts" <?php echo $p->{'reports-quantity_alerts'} ? "checked" : ''; ?>>
						<label for="product_quantity_alerts" class="padding05"><?= lang('product_quantity_alerts') ?></label>
					    </span>
					    </li>					    
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="Product_expiry_alerts" name="reports-expiry_alerts" <?php echo $p->{'reports-expiry_alerts'} ? "checked" : ''; ?>>
						<label for="Product_expiry_alerts" class="padding05"><?= lang('product_expiry_alerts') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="products"
						name="reports-products" <?php echo $p->{'reports-products'} ? "checked" : ''; ?>><label for="products" class="padding05"><?= lang('products') ?></label>
					    </span>
					    </li>
					    
					    
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="sales" name="reports-sales" <?php echo $p->{'reports-sales'} ? "checked" : ''; ?>>
						<label for="sales" class="padding05"><?= lang('sales') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="payments" name="reports-payments" <?php echo $p->{'reports-payments'} ? "checked" : ''; ?>>
						<label for="payments" class="padding05"><?= lang('payments') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="expenses" name="reports-expenses" <?php echo $p->{'reports-expenses'} ? "checked" : ''; ?>>
						<label for="expenses" class="padding05"><?= lang('expenses') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="daily_purchases" name="reports-daily_purchases" <?php echo $p->{'reports-daily_purchases'} ? "checked" : ''; ?>>
						<label for="daily_purchases" class="padding05"><?= lang('daily_purchases') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="monthly_purchases" name="reports-monthly_purchases" <?php echo $p->{'reports-monthly_purchases'} ? "checked" : ''; ?>>
						<label for="monthly_purchases" class="padding05"><?= lang('monthly_purchases') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="purchases" name="reports-purchases" <?php echo $p->{'reports-purchases'} ? "checked" : ''; ?>>
						<label for="purchases" class="padding05"><?= lang('purchases') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="customers" name="reports-customers" <?php echo $p->{'reports-customers'} ? "checked" : ''; ?>>
						<label for="customers" class="padding05"><?= lang('customers') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="suppliers" name="reports-suppliers" <?php echo $p->{'reports-suppliers'} ? "checked" : ''; ?>>
						<label for="suppliers" class="padding05"><?= lang('suppliers') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="staff_report" name="reports-users" <?php echo $p->{'reports-users'} ? "checked" : ''; ?>>
						<label for="staff_report" class="padding05"><?= lang('staff_report') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="profit_loss" name="reports-profit_loss" <?php echo $p->{'reports-profit_loss'} ? "checked" : ''; ?>>
						<label for="profit_loss" class="padding05"><?= lang('profit_loss') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="brands" name="reports-brands" <?php echo $p->{'reports-brands'} ? "checked" : ''; ?>>
						<label for="brands" class="padding05"><?= lang('brands') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="categories" name="reports-categories" <?php echo $p->{'reports-categories'} ? "checked" : ''; ?>>
						<label for="categories" class="padding05"><?= lang('categories') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="adjustments" name="reports-adjustments" <?php echo $p->{'reports-adjustments'} ? "checked" : ''; ?>>
						<label for="adjustments" class="padding05"><?= lang('adjustments') ?></label>
					    </span>
					    </li>
					    
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="stock_audit" name="reports-stock_audit" <?php echo $p->{'reports-stock_audit'} ? "checked" : ''; ?>>
						<label for="stock_audit" class="padding05"><?= lang('stock_audit') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="cover_analysis" name="reports-cover_analysis" <?php echo $p->{'reports-cover_analysis'} ? "checked" : ''; ?>>
						<label for="cover_analysis" class="padding05"><?= lang('cover_analysis') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="postpaid_bills" name="reports-postpaid_bills" <?php echo $p->{'reports-postpaid_bills'} ? "checked" : ''; ?>>
						<label for="postpaid_bills" class="padding05"><?= lang('postpaid_bills') ?></label>
					    </span>
					    </li>
					    <li>
					    <span style="inline-block">
						<input type="checkbox" value="1" class="checkbox" id="feedback" name="reports-feedback" <?php echo $p->{'reports-feedback'} ? "checked" : ''; ?>>
						<label for="feedback" class="padding05"><?= lang('feedback') ?></label>
					    </span>
					    </li>
					</ul>
				    </td>
                                </tr>
                                
                                

                                <tr>
                                    <td><?= lang("misc"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="bulk_actions"
                                            name="bulk_actions" <?php echo $p->bulk_actions ? "checked" : ''; ?>>
                                            <label for="bulk_actions" class="padding05"><?= lang('bulk_actions') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="edit_price"
                                            name="edit_price" <?php echo $p->edit_price ? "checked" : ''; ?>>
                                            <label for="edit_price" class="padding05"><?= lang('edit_price_on_sale') ?></label>
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("nightaudit"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="nightaudit-index" <?php echo $p->{'nightaudit-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="nightaudit-add" <?php echo $p->{'nightaudit-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="nightaudit-edit" <?php echo $p->{'nightaudit-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="nightaudit-delete" <?php echo $p->{'nightaudit-delete'} ? "checked" : ''; ?>>
                                    </td>
									<td>
										<input type="checkbox" value="1" id="nightaudit-pdf" class="checkbox" name="nightaudit-pdf" <?php echo $p->{'nightaudit-pdf'} ? "checked" : ''; ?>>
										<label for="nightaudit-pdf" class="padding05"><?= lang('nightaudit_pdf') ?></label>
									
										<input type="checkbox" value="1" id="blind_night_audit" class="checkbox" name="blind_night_audit" <?php echo $p->{'blind_night_audit'} ? "checked" : ''; ?>>
										<label for="blind_night_audit" class="padding05"><?= lang('blind_night_audit') ?></label>
									</td>
				</tr>
				</tbody>
                            </table>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                                <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("group_permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center"><?= lang("pos_access"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("pos_privillege"); ?></th>
                                </tr>
                                <tr>
                                   
                                    <th class="text-center"><?= lang("misc"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                            
								<tr>
                                    <td><?= lang("pos_type"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                        
                                            <input type="checkbox" value="1" class="checkbox" id="pos-dinein"
                                            name="pos-dinein" <?php echo $p->{'pos-dinein'} ? "checked" : ''; ?>>
                                            <label for="pos-dinein" class="padding05"><?= lang('dinein') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-takeaway"
                                            name="pos-takeaway" <?php echo $p->{'pos-takeaway'} ? "checked" : ''; ?>>
                                            <label for="pos-takeaway" class="padding05"><?= lang('takeaway') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-door_delivery"
                                            name="pos-door_delivery" <?php echo $p->{'pos-takeaway'} ? "checked" : ''; ?>>
                                            <label for="pos-door_delivery" class="padding05"><?= lang('door_delivery') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-orders"
                                            name="pos-orders" <?php echo $p->{'pos-orders'} ? "checked" : ''; ?>>
                                            <label for="pos-orders" class="padding05"><?= lang('orders') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kitchens"
                                            name="pos-kitchens" <?php echo $p->{'pos-kitchens'} ? "checked" : ''; ?>>
                                            <label for="pos-kitchens" class="padding05"><?= lang('kitchens') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-billing"
                                            name="pos-billing" <?php echo $p->{'pos-billing'} ? "checked" : ''; ?>>
                                            <label for="pos-billing" class="padding05"><?= lang('billing') ?></label>
                                        </span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td><?= lang("item_process"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-table_view"
                                            name="pos-table_view" <?php echo $p->{'pos-table_view'} ? "checked" : ''; ?>>
                                            <label for="pos-table_view" class="padding05"><?= lang('table_view') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-table_add"
                                            name="pos-table_add" <?php echo $p->{'pos-table_add'} ? "checked" : ''; ?>>
                                            <label for="pos-table_add" class="padding05"><?= lang('table_add') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-table_edit"
                                            name="pos-table_edit" <?php echo $p->{'pos-table_edit'} ? "checked" : ''; ?>>
                                            <label for="pos-table_edit" class="padding05"><?= lang('table_edit') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-quantity_edit"
                                            name="pos-quantity_edit" <?php echo $p->{'pos-quantity_edit'} ? "checked" : ''; ?>>
                                            <label for="pos-quantity_edit" class="padding05"><?= lang('quantity_edit') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-orders_cancel"
                                            name="pos-orders_cancel" <?php echo $p->{'pos-orders_cancel'} ? "checked" : ''; ?>>
                                            <label for="pos-orders_cancel" class="padding05"><?= lang('orders_cancel') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-sendtokitchen"
                                            name="pos-sendtokitchen" <?php echo $p->{'pos-sendtokitchen'} ? "checked" : ''; ?>>
                                            <label for="pos-sendtokitchen" class="padding05"><?= lang('send_to_kitchen') ?></label>
                                        </span>
                                       
                                    </td>
                                </tr>
                                
                                 <tr class="orders-settings">
                                    <td><?= lang("orders"); ?></td>
                                    <td colspan="5">
					<ul>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-dinein_orders"
                                            name="pos-dinein_orders" <?php echo $p->{'pos-dinein_orders'} ? "checked" : ''; ?>>
                                            <label for="pos-dinein_orders" class="padding05"><?= lang('dinein_orders') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-takeaway_orders"
                                            name="pos-takeaway_orders" <?php echo $p->{'pos-takeaway_orders'} ? "checked" : ''; ?>>
                                            <label for="pos-takeaway_orders" class="padding05"><?= lang('takeaway_orders') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-door_delivery_orders"
                                            name="pos-door_delivery_orders" <?php echo $p->{'pos-door_delivery_orders'} ? "checked" : ''; ?>>
                                            <label for="pos-door_delivery_orders" class="padding05"><?= lang('door_delivery_orders') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-change_single_status"
                                            name="pos-change_single_status" <?php echo $p->{'pos-change_single_status'} ? "checked" : ''; ?>>
                                            <label for="pos-change_single_status" class="padding05"><?= lang('change_single_status') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-change_multiple_status"
                                            name="pos-change_multiple_status" <?php echo $p->{'pos-change_multiple_status'} ? "checked" : ''; ?>>
                                            <label for="pos-change_multiple_status" class="padding05"><?= lang('change_multiple_status') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-cancel_order_items"
                                            name="pos-cancel_order_items" <?php echo $p->{'pos-cancel_order_items'} ? "checked" : ''; ?>>
                                            <label for="pos-cancel_order_items" class="padding05"><?= lang('cancel_order_items') ?></label>
                                        </span>
                                        </li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-cancel_order_remarks"
                                            name="pos-cancel_order_remarks" <?php echo $p->{'pos-cancel_order_remarks'} ? "checked" : ''; ?>>
                                            <label for="pos-cancel_order_remarks" class="padding05"><?= lang('cancel_order_remarks') ?></label>
                                        </span>
                                        </li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-new_order_create"
                                            name="pos-new_order_create" <?php echo $p->{'pos-new_order_create'} ? "checked" : ''; ?>>
                                            <label for="pos-new_order_create" class="padding05"><?= lang('new_order_create') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-new_split_create"
                                            name="pos-new_split_create" <?php echo $p->{'pos-new_split_create'} ? "checked" : ''; ?>>
                                            <label for="pos-new_split_create" class="padding05"><?= lang('new_split_create') ?></label>
                                        </span>
					</li>
					<li>
                                       <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-bil_generator"
                                            name="pos-bil_generator" <?php echo $p->{'pos-bil_generator'} ? "checked" : ''; ?>>
                                            <label for="pos-bil_generator" class="padding05"><?= lang('bil_generator') ?></label>
                                        </span>
				       </li>
					<li>
                                         <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-auto_bil"
                                            name="pos-auto_bil" <?php echo $p->{'pos-auto_bil'} ? "checked" : ''; ?>>
                                            <label for="pos-auto_bil" class="padding05"><?= lang('auto_bil') ?></label>
                                        </span>
					 </li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-view_allusers_orders"
                                            name="pos-view_allusers_orders" <?php echo $p->{'pos-view_allusers_orders'} ? "checked" : ''; ?>>
                                            <label for="pos-view_allusers_orders" class="padding05"><?= lang('view_allusers_orders') ?></label>
                                        </span>
                                        </li>
					</ul>
				    </td>
				 </tr>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-no_discount"
                                            name="pos-no_discount" <?php echo $p->{'pos-no_discount'} ? "checked" : ''; ?>>
                                            <label for="pos-no_discount" class="padding05"><?= lang('no_discount') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-no_tax"
                                            name="pos-no_tax" <?php echo $p->{'pos-no_tax'} ? "checked" : ''; ?>>
                                            <label for="pos-no_tax" class="padding05"><?= lang('no_tax') ?></label>
                                        </span>
                                    </td>
                                </tr>
                                
                                 <tr>
                                    <td><?= lang("kitchen"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kitchen_view"
                                            name="pos-kitchen_view" <?php echo $p->{'pos-kitchen_view'} ? "checked" : ''; ?>>
                                            <label for="pos-kitchen_view" class="padding05"><?= lang('kitchen_view') ?></label>
                                        </span>
                                        
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kitchen_change_single_status"
                                            name="pos-kitchen_change_single_status" <?php echo $p->{'pos-kitchen_change_single_status'} ? "checked" : ''; ?>>
                                            <label for="pos-kitchen_change_single_status" class="padding05"><?= lang('change_single_status') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kitchen_change_multiple_status"
                                            name="pos-kitchen_change_multiple_status" <?php echo $p->{'pos-kitchen_change_multiple_status'} ? "checked" : ''; ?>>
                                            <label for="pos-kitchen_change_multiple_status" class="padding05"><?= lang('change_multiple_status') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kitchen_cancel_order_items"
                                            name="pos-kitchen_cancel_order_items" <?php echo $p->{'pos-kitchen_cancel_order_items'} ? "checked" : ''; ?>>
                                            <label for="pos-kitchen_cancel_order_items" class="padding05"><?= lang('cancel_order_items') ?></label>
                                        </span>
                                         <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-kot_print"
                                            name="pos-kot_print" <?php echo $p->{'pos-kot_print'} ? "checked" : ''; ?>>
                                            <label for="pos-kot_print" class="padding05"><?= lang('kot_print') ?></label>
                                        </span>
                                        
                                        
                                    </td>
                                </tr>
                                
                                 <tr class="billing-settings">
                                    <td><?= lang("billing"); ?></td>
                                    <td colspan="5">
					<ul>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-dinein_bils"
                                            name="pos-dinein_bils" <?php echo $p->{'pos-dinein_bils'} ? "checked" : ''; ?>>
                                            <label for="pos-dinein_bils" class="padding05"><?= lang('dinein_bils') ?></label>
                                        </span>
                                        </li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-takeaway_bils"
                                            name="pos-takeaway_bils" <?php echo $p->{'pos-takeaway_bils'} ? "checked" : ''; ?>>
                                            <label for="pos-takeaway_bils" class="padding05"><?= lang('takeaway_bils') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-door_delivery_bils"
                                            name="pos-door_delivery_bils" <?php echo $p->{'pos-door_delivery_bils'} ? "checked" : ''; ?>>
                                            <label for="pos-door_delivery_bils" class="padding05"><?= lang('door_delivery_bils') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-bil_cancel"
                                            name="pos-bil_cancel" <?php echo $p->{'pos-bil_cancel'} ? "checked" : ''; ?>>
                                            <label for="pos-bil_cancel" class="padding05"><?= lang('bil_cancel') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-bil_payment"
                                            name="pos-bil_payment" <?php echo $p->{'pos-bil_payment'} ? "checked" : ''; ?>>
                                            <label for="pos-bil_payment" class="padding05"><?= lang('bil_payment') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-bil_print"
                                            name="pos-bil_print" <?php echo $p->{'pos-bil_print'} ? "checked" : ''; ?>>
                                            <label for="pos-bil_print" class="padding05"><?= lang('bil_print') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-report_view"
                                            name="pos-report_view" <?php echo $p->{'pos-report_view'} ? "checked" : ''; ?>>
                                            <label for="pos-report_view" class="padding05"><?= lang('report_view') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-today_item_report"
                                            name="pos-today_item_report" <?php echo $p->{'pos-today_item_report'} ? "checked" : ''; ?>>
                                            <label for="pos-today_item_report" class="padding05"><?= lang('today_item_report') ?></label>
                                        </span>
					</li>
					<li>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-daywise_report"
                                            name="pos-daywise_report" <?php echo $p->{'pos-daywise_report'} ? "checked" : ''; ?>>
                                            <label for="pos-daywise_report" class="padding05"><?= lang('daywise_report') ?></label>
                                        </span>
					</li>
					<li>
                        <span style="inline-block">
                            <input type="checkbox" value="1" class="checkbox" id="pos-cashierwise_report"
                            name="pos-cashierwise_report" <?php echo $p->{'pos-cashierwise_report'} ? "checked" : ''; ?>>
                            <label for="pos-cashierwise_report" class="padding05"><?= lang('cashierwise_report') ?></label>
                        </span>
					</li>
					<li>
                        <span style="inline-block">
                            <input type="checkbox" value="1" class="checkbox" id="pos-open_sale_register"
                            name="pos-open_sale_register" <?php echo $p->{'pos-open_sale_register'} ? "checked" : ''; ?>>
                            <label for="pos-open_sale_register" class="padding05"><?= lang('open_sale_register') ?></label>
                        </span>
					</li>
                                       </ul>
                                        
                                    </td>
                                </tr>
                                
                                </tbody>
                            </table>
                        </div>
			 <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped settings-table">

                                <thead>
                               <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("settings"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
				    <tr>
                                    <td><?= lang("printers"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                        
                                            <input type="checkbox" value="1" class="checkbox" id="pos-printers"
                                            name="pos-printers" <?php echo $p->{'pos-printers'} ? "checked" : ''; ?>>
                                            <label for="pos-printers" class="padding05"><?= lang('view') ?></label>
                                        </span>
					<span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-add_printer"
                                            name="pos-add_printer" <?php echo $p->{'pos-add_printer'} ? "checked" : ''; ?>>
                                            <label for="pos-add_printer" class="padding05"><?= lang('add') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-edit_printer"
                                            name="pos-edit_printer" <?php echo $p->{'pos-edit_printer'} ? "checked" : ''; ?>>
                                            <label for="pos-edit_printer" class="padding05"><?= lang('edit') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="pos-delete_printer"
                                            name="pos-delete_printer" <?php echo $p->{'pos-delete_printer'} ? "checked" : ''; ?>>
                                            <label for="pos-delete_printer" class="padding05"><?= lang('delete') ?></label>
                                        </span>                                       
                                    </td>
                                </tr>
				    <tr>
                                    <td><?= lang("tender_type"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                        
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-payment_methods"
                                            name="system_settings-payment_methods" <?php echo $p->{'system_settings-payment_methods'} ? "checked" : ''; ?>>
                                            <label for="system_settings-payment_methods" class="padding05"><?= lang('view') ?></label>
                                        </span>
					<span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-add_payment_method"
                                            name="system_settings-add_payment_method" <?php echo $p->{'system_settings-add_payment_method'} ? "checked" : ''; ?>>
                                            <label for="system_settings-add_payment_method" class="padding05"><?= lang('add') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-tender_type_status"
                                            name="system_settings-tender_type_status" <?php echo $p->{'system_settings-tender_type_status'} ? "checked" : ''; ?>>
                                            <label for="system_settings-tender_type_status" class="padding05"><?= lang('status') ?></label>
                                        </span>                                     
                                    </td>
                                </tr>
				    <tr>
                                    <td><?= lang("custom_feedback"); ?></td>
                                    <td colspan="5">
                                        <span style="inline-block">
                                        
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-customfeedback"
                                            name="system_settings-customfeedback" <?php echo $p->{'system_settings-customfeedback'} ? "checked" : ''; ?>>
                                            <label for="system_settings-customfeedback" class="padding05"><?= lang('view') ?></label>
                                        </span>
					<span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-add_customfeedback"
                                            name="system_settings-add_customfeedback" <?php echo $p->{'system_settings-add_customfeedback'} ? "checked" : ''; ?>>
                                            <label for="system_settings-add_customfeedback" class="padding05"><?= lang('add') ?></label>
                                        </span>
                                        <span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-edit_customfeedback"
                                            name="system_settings-edit_customfeedback" <?php echo $p->{'system_settings-edit_customfeedback'} ? "checked" : ''; ?>>
                                            <label for="system_settings-edit_customfeedback" class="padding05"><?= lang('edit') ?></label>
                                        </span>
					<span style="inline-block">
                                            <input type="checkbox" value="1" class="checkbox" id="system_settings-delete_customfeedback"
                                            name="system_settings-delete_customfeedback" <?php echo $p->{'system_settings-delete_customfeedback'} ? "checked" : ''; ?>>
                                            <label for="system_settings-delete_customfeedback" class="padding05"><?= lang('delete') ?></label>
                                        </span> 
                                    </td>
                                </tr>
				    <tr>
					<td><?= lang("logo"); ?></td>
					<td colspan="5">
					    <span style="inline-block">
					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-change_logo"
						name="system_settings-change_logo" <?php echo $p->{'system_settings-change_logo'} ? "checked" : ''; ?>>
						<label for="system_settings-change_logo" class="padding05"><?= lang('change') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("currencies"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-currencies"
						name="system_settings-currencies" <?php echo $p->{'system_settings-currencies'} ? "checked" : ''; ?>>
						<label for="system_settings-currencies" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_currency"
						name="system_settings-add_currency" <?php echo $p->{'system_settings-add_currency'} ? "checked" : ''; ?>>
						<label for="system_settings-add_currency" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_currency"
						name="system_settings-edit_currency" <?php echo $p->{'system_settings-edit_currency'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_currency" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_currency"
						name="system_settings-delete_currency" <?php echo $p->{'system_settings-delete_currency'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_currency" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("customer_groups"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-customer_groups"
						name="system_settings-customer_groups" <?php echo $p->{'system_settings-customer_groups'} ? "checked" : ''; ?>>
						<label for="system_settings-customer_groups" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_customer_group"
						name="system_settings-add_customer_group" <?php echo $p->{'system_settings-add_customer_group'} ? "checked" : ''; ?>>
						<label for="system_settings-add_customer_group" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_customer_group"
						name="system_settings-edit_customer_group" <?php echo $p->{'system_settings-edit_customer_group'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_customer_group" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_customer_group"
						name="system_settings-delete_customer_group" <?php echo $p->{'system_settings-delete_customer_group'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_customer_group" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("categories"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-categories"
						name="system_settings-categories" <?php echo $p->{'system_settings-categories'} ? "checked" : ''; ?>>
						<label for="system_settings-categories" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_category"
						name="system_settings-add_category" <?php echo $p->{'system_settings-add_category'} ? "checked" : ''; ?>>
						<label for="system_settings-add_category" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_category"
						name="system_settings-edit_category" <?php echo $p->{'system_settings-edit_category'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_category" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_category"
						name="system_settings-delete_category" <?php echo $p->{'system_settings-delete_category'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_category" class="padding05"><?= lang('delete') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="products-barcode"
						name="products-barcode" <?php echo $p->{'products-barcode'} ? "checked" : ''; ?>>
						<label for="products-barcode" class="padding05"><?= lang('print_barcodes') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("recipe_groups"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-recipecategories"
						name="system_settings-recipecategories" <?php echo $p->{'system_settings-recipecategories'} ? "checked" : ''; ?>>
						<label for="system_settings-recipecategories" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_recipecategory"
						name="system_settings-add_recipecategory" <?php echo $p->{'system_settings-add_recipecategory'} ? "checked" : ''; ?>>
						<label for="system_settings-add_recipecategory" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_recipecategory"
						name="system_settings-edit_recipecategory" <?php echo $p->{'system_settings-edit_recipecategory'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_recipecategory" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_recipecategory"
						name="system_settings-delete_recipecategory" <?php echo $p->{'system_settings-delete_recipecategory'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_recipecategory" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("expense_categories"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-expense_categories"
						name="system_settings-expense_categories" <?php echo $p->{'system_settings-expense_categories'} ? "checked" : ''; ?>>
						<label for="system_settings-expense_categories" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_expense_category"
						name="system_settings-add_expense_category" <?php echo $p->{'system_settings-add_expense_category'} ? "checked" : ''; ?>>
						<label for="system_settings-add_expense_category" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_expense_category"
						name="system_settings-edit_expense_category" <?php echo $p->{'system_settings-edit_expense_category'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_expense_category" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_expense_category"
						name="system_settings-delete_expense_category" <?php echo $p->{'system_settings-delete_expense_category'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_expense_category" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("units"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-units"
						name="system_settings-units" <?php echo $p->{'system_settings-units'} ? "checked" : ''; ?>>
						<label for="system_settings-units" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_unit"
						name="system_settings-add_unit" <?php echo $p->{'system_settings-add_unit'} ? "checked" : ''; ?>>
						<label for="system_settings-add_unit" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_unit"
						name="system_settings-edit_unit" <?php echo $p->{'system_settings-edit_unit'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_unit" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_unit"
						name="system_settings-delete_unit" <?php echo $p->{'system_settings-delete_unit'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_unit" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("brands"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-brands"
						name="system_settings-brands" <?php echo $p->{'system_settings-brands'} ? "checked" : ''; ?>>
						<label for="system_settings-brands" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_brand"
						name="system_settings-add_brand" <?php echo $p->{'system_settings-add_brand'} ? "checked" : ''; ?>>
						<label for="system_settings-add_brand" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_brand"
						name="system_settings-edit_brand" <?php echo $p->{'system_settings-edit_brand'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_brand" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_brand"
						name="system_settings-delete_brand" <?php echo $p->{'system_settings-delete_brand'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_brand" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("sales_type"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-sales_type"
						name="system_settings-sales_type" <?php echo $p->{'system_settings-sales_type'} ? "checked" : ''; ?>>
						<label for="system_settings-sales_type" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_sales_type"
						name="system_settings-add_sales_type" <?php echo $p->{'system_settings-add_sales_type'} ? "checked" : ''; ?>>
						<label for="system_settings-add_sales_type" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_sales_type"
						name="system_settings-edit_sales_type" <?php echo $p->{'system_settings-edit_sales_type'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_sales_type" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_sales_type"
						name="system_settings-delete_sales_type" <?php echo $p->{'system_settings-delete_sales_type'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_sales_type" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("tax_rates"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-tax_rates"
						name="system_settings-tax_rates" <?php echo $p->{'system_settings-tax_rates'} ? "checked" : ''; ?>>
						<label for="system_settings-tax_rates" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_tax_rate"
						name="system_settings-add_tax_rate" <?php echo $p->{'system_settings-add_tax_rate'} ? "checked" : ''; ?>>
						<label for="system_settings-add_tax_rate" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_tax_rate"
						name="system_settings-edit_tax_rate" <?php echo $p->{'system_settings-edit_tax_rate'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_tax_rate" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_tax_rate"
						name="system_settings-delete_tax_rate" <?php echo $p->{'system_settings-delete_tax_rate'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_tax_rate" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("discounts"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-discounts"
						name="system_settings-discounts" <?php echo $p->{'system_settings-discounts'} ? "checked" : ''; ?>>
						<label for="system_settings-discounts" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_discount"
						name="system_settings-add_discount" <?php echo $p->{'system_settings-add_discount'} ? "checked" : ''; ?>>
						<label for="system_settings-add_discount" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_discount"
						name="system_settings-delete_discount" <?php echo $p->{'system_settings-delete_discount'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_discount" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("customer_discounts"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-customer_discounts"
						name="system_settings-customer_discounts" <?php echo $p->{'system_settings-customer_discounts'} ? "checked" : ''; ?>>
						<label for="system_settings-customer_discounts" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_customer_discounts"
						name="system_settings-add_customer_discounts" <?php echo $p->{'system_settings-add_customer_discounts'} ? "checked" : ''; ?>>
						<label for="system_settings-add_customer_discounts" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_customer_discount"
						name="system_settings-edit_customer_discount" <?php echo $p->{'system_settings-edit_customer_discount'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_customer_discount" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_customer_discount"
						name="system_settings-delete_customer_discount" <?php echo $p->{'system_settings-delete_customer_discount'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_customer_discount" class="padding05"><?= lang('delete') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-cus_dis_status"
						name="system_settings-cus_dis_status" <?php echo $p->{'system_settings-cus_dis_status'} ? "checked" : ''; ?>>
						<label for="system_settings-cus_dis_status" class="padding05"><?= lang('status') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("buy_get"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-buy_get"
						name="system_settings-buy_get" <?php echo $p->{'system_settings-buy_get'} ? "checked" : ''; ?>>
						<label for="system_settings-buy_get" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-add_buy"
						name="system_settings-add_buy" <?php echo $p->{'system_settings-add_buy'} ? "checked" : ''; ?>>
						<label for="system_settings-add_buy" class="padding05"><?= lang('add') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-edit_buy"
						name="system_settings-edit_buy" <?php echo $p->{'system_settings-edit_buy'} ? "checked" : ''; ?>>
						<label for="system_settings-edit_buy" class="padding05"><?= lang('edit') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_buy"
						name="system_settings-delete_buy" <?php echo $p->{'system_settings-delete_buy'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_buy" class="padding05"><?= lang('delete') ?></label>
					    </span>
					</td>
				    </tr>
				    <tr>
					<td><?= lang("email_templates"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-email_templates"
						name="system_settings-email_templates" <?php echo $p->{'system_settings-email_templates'} ? "checked" : ''; ?>>
						<label for="system_settings-email_templates" class="padding05"><?= lang('edit') ?></label>
					    </span>					    
					</td>
				    </tr>
				    <tr>
					<td><?= lang("backups"); ?></td>
					<td colspan="5">
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-backups"
						name="system_settings-backups" <?php echo $p->{'system_settings-backups'} ? "checked" : ''; ?>>
						<label for="system_settings-backups" class="padding05"><?= lang('view') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-backup_database"
						name="system_settings-backup_database" <?php echo $p->{'system_settings-backup_database'} ? "checked" : ''; ?>>
						<label for="system_settings-backup_database" class="padding05"><?= lang('backup') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-download_database"
						name="system_settings-download_database" <?php echo $p->{'system_settings-download_database'} ? "checked" : ''; ?>>
						<label for="system_settings-download_database" class="padding05"><?= lang('download_database') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-restore_database"
						name="system_settings-restore_database" <?php echo $p->{'system_settings-restore_database'} ? "checked" : ''; ?>>
						<label for="system_settings-restore_database" class="padding05"><?= lang('restore_database') ?></label>
					    </span>
					    <span style="inline-block">					    
						<input type="checkbox" value="1" class="checkbox" id="system_settings-delete_database"
						name="system_settings-delete_database" <?php echo $p->{'system_settings-delete_database'} ? "checked" : ''; ?>>
						<label for="system_settings-delete_database" class="padding05"><?= lang('delete_database') ?></label>
					    </span>
					</td>
				    </tr>
				</tbody>
			    </table>
			 </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();
                    } else {
                        echo $this->lang->line("group_x_allowed");
                    }
                } else {
                    echo $this->lang->line("group_x_allowed");
                } ?>


            </div>
        </div>
    </div>
</div>
<style>
    .group-permission ul{
	list-style: none;
	
    }
    .reports ul{
    -moz-column-count: 4 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 4 !important;
    -webkit-column-gap: 23px;
    column-count: 4 !important;
    column-gap: 0px;/*23px;*/
    }
    .orders-settings ul,.billing-settings ul,.group-permission ul{
    -moz-column-count: 3;
    -moz-column-gap: 23px;
    -webkit-column-count: 3;
    -webkit-column-gap: 23px;
    column-count: 3;
    column-gap: 0px;/*23px;*/
    }
    .restaurants-group-permission ul li{
	 /*-moz-column-count: 1 !important;
    -moz-column-gap: 23px;
    -webkit-column-count: 1 !important;
    -webkit-column-gap: 23px;
    column-count: 1 !important;
    column-gap: 0px;*//*23px;*/
     display: block;
    float: left;
    width:45%
    }
</style>
