<?php if (!isset($spark_ui_enabled) || $spark_ui_enabled !== false) { ?>
        </div> <!-- /.spark-content -->
        <footer class="spark-footer">
            <div class="spark-footer-inner">
                <p class="spark-footer-copyright">
                    &copy; <?= date('Y') . ' ' . lang('Arkan_Pro_Company'); ?>. <?= (isset($Settings->user_language) && $Settings->user_language == 'arabic') ? 'جميع الحقوق محفوظة.' : (lang('all_rights_reserved') ?? 'All rights reserved.'); ?>
                </p>
            </div>
        </footer>
    </main> <!-- /.spark-main -->
</div> <!-- /.spark-app -->
<a href="#" id="toTop" class="blue" style="position: fixed; bottom: 30px; right: 30px; font-size: 30px; display: none;">
    <i class="fa fa-chevron-circle-up"></i>
</a>
<?php } else { ?>
<div class="clearfix"></div>
<?= '</div></td></tr></table></div></div>'; ?>
<div class="clearfix"></div>

</div>
</div>
</div>
</body>
</div>
</body>

<footer>
<?php $assets=site_url().'themes/default/admin/assets/'; ?>

 <?php if ($Settings->logo) {
	 if ($Settings->user_language == 'arabic') {
    echo '<img class="imgfooter_ar" src="' . base_url('assets/uploads/logos/' . $Settings->logo2) . '" alt="' . $Settings->site_name . '" style="margin-bottom:10px;max-width:50%;height:auto;" />';
	 }else {
	echo '<img class="imgfooter" src="' . base_url('assets/uploads/logos/' . $Settings->logo2) . '" alt="' . $Settings->site_name . '" style="margin-bottom:10px;max-width:50%;height:auto;" />';
	}
} ?>

<a href="#" id="toTop" class="blue" style="position: fixed; bottom: 30px; right: 30px; font-size: 30px; display: none;">
    <i class="fa fa-chevron-circle-up"></i>
</a>

    <p style="text-align:center;">&copy; <?= date('Y') . ' ' . $Settings->site_name; ?></p>
</footer>
<?php } ?>
<?= '</div>'; ?>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="spark-dropdown-portal" class="spark-dropdown-portal spark-ui" aria-hidden="true"></div>
<div id="modal-loading" class="spark-modal-loader" style="display: none;" role="status" aria-live="polite">
    <div class="blackbg spark-modal-loader-backdrop"></div>
    <div class="loader spark-modal-loader-card">
        <span class="spark-spinner spark-spinner-md"></span>
        <span class="spark-modal-loader-text"><?= (isset($Settings->user_language) && $Settings->user_language == 'arabic') ? 'جاري التحميل...' : 'Loading...'; ?></span>
    </div>
</div>
<div id="ajaxCall" class="spark-ajax-loader" role="status" aria-live="polite" aria-busy="false" style="display: none;">
    <span class="spark-spinner spark-spinner-xs"></span>
    <span class="spark-ajax-loader-text"><?= (isset($Settings->user_language) && $Settings->user_language == 'arabic') ? 'جاري المعالجة...' : 'Processing...'; ?></span>
</div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code); ?>
<script type="text/javascript">
var dt_lang = <?=$dt_lang?>, dp_lang = <?=$dp_lang?>, site = <?=json_encode(['url' => base_url(), 'base_url' => admin_url(), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats])?>;
//var lang = {produce: '<?=lang('produce');?>', confirmed: '<?=lang('confirmed');?>', done: '<?=lang('done');?>', Paid: '<?=lang('Paid');?>', Free_of_charge: '<?=lang('Free_of_charge');?>', paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', purchase_pending: '<?=lang('purchase_pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>', required_invalid: '<?=lang('required_invalid');?>'};
var lang = {You_cannot_select_this_item_because_its_price_is_negative: '<?=lang('You_cannot_select_this_item_because_its_price_is_negative');?>',menu_item: '<?=lang('menu_item');?>',sub_menu: '<?=lang('sub_menu');?>',Exceeded_the_minimum_selling_price: '<?=lang('Exceeded_the_minimum_selling_price');?>',debit_note: '<?=lang('Debit_note');?>',credit_note: '<?=lang('Credit_note');?>',exit: '<?=lang('exit');?>',Purchasing_price_is_required: '<?=lang('Purchasing_price_is_required');?>',Selling_price_is_required: '<?=lang('Selling_price_is_required');?>',Pricing_code_is_required: '<?=lang('Pricing_code_is_required');?>',Conversion_factor_is_required: '<?=lang('Conversion_factor_is_required');?>',Pricing_unit_is_required: '<?=lang('Pricing_unit_is_required');?>',Select_supplier: '<?=lang('Select_supplier');?>',taxes_are_not_uniform_Please_change_the_tax_system_type: '<?=lang('taxes_are_not_uniform_Please_change_the_tax_system_type');?>',Select_customer: '<?=lang('Select_customer');?>',Select_warehouse: '<?=lang('Select_warehouse');?>',Select_tax_method_type: '<?=lang('Select_tax_method_type');?>',Select_currency: '<?=lang('Select_currency');?>',Select_product_tax: '<?=lang('Select_product_tax');?>',Select_order_tax: '<?=lang('Select_order_tax');?>',Select_biller: '<?=lang('Select_biller');?>',select_unit_first: '<?=lang('select_unit_first');?>',Select_unit: '<?=lang('Select_unit');?>',select_category_to_load: '<?=lang('select_category_to_load');?>',no_subcategory: '<?=lang('no_subcategory');?>',select_subcategory: '<?=lang('select_subcategory');?>',Select_user: '<?=lang('Select_user');?>',Select_main_company: '<?=lang('Select_main_company');?>',Select_main_branch: '<?=lang('Select_main_branch');?>',Select_Showroom: '<?=lang('Select_Showroom');?>',Select_category: '<?=lang('Select_category');?>',Select_brand: '<?=lang('Select_brand');?>',inclusive: '<?=lang('inclusive');?>',exclusive: '<?=lang('exclusive');?>',No_Sold: '<?=lang('No_Sold');?>',canceled: '<?=lang('canceled');?>', available: '<?=lang('available');?>', sold: '<?=lang('sold');?>', produce: '<?=lang('produce');?>', confirmed: '<?=lang('confirmed');?>', done: '<?=lang('done');?>', Paid: '<?=lang('Paid');?>', Free_of_charge: '<?=lang('Free_of_charge');?>', paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', purchase_pending: '<?=lang('purchase_pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', transfer_ordered: '<?=lang('transfer_ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>', required_invalid: '<?=lang('required_invalid');?>',The_owner_of_all_vehicles_must_be_the_same: '<?=lang('The_owner_of_all_vehicles_must_be_the_same');?>'};

</script>
<?php
$s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
foreach (lang('select2_lang') as $s2_key => $s2_line) {
    $s2_data[$s2_key] = str_replace(['{', '}'], ['"+', '+"'], $s2_line);
}
$s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
<script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.calculator.min.js"></script>
<?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>

<script type="text/javascript" src="<?= $assets ?>js/carscore.js<?= '?'.time() ?>"></script>
<?php
                }else{

?>
<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
<?php
                }
?>
<script type="text/javascript" src="<?= $assets ?>js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/html2pdf/dist/html2pdf.bundle.min.js"></script>

<?= ($m == 'purchases' && ($v == 'add' || $v == 'edit' || $v == 'purchase_by_csv' || $v == 'purchase_by_excel')) ? '<script type="text/javascript" src="' . $assets . 'js/purchases.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'carspurchases' && ($v == 'add' || $v == 'edit' || $v == 'purchase_by_csv' || $v == 'purchase_by_excel')) ? '<script type="text/javascript" src="' . $assets . 'js/carspurchases.js"></script>' : ''; ?>
<?= ($m == 'transfers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/transfers.js"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'transfers_add' || $v == 'transfers_edit')) ? '<script type="text/javascript" src="' . $assets . 'js/transfers.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'transfers_add' || $v == 'transfers_edit')) ? '<script type="text/javascript" src="' . $assets . 'js/transfers.js"></script>' : ''; ?>
<?= ($m == 'carstransfers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carstransfers.js"></script>' : ''; ?>
<?= ($m == 'cars' && ($v == 'transfers_add' || $v == 'transfers_edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carstransfers.js"></script>' : ''; ?>
<?= ($m == 'carstransfers' && ($v == 'transfers_add' || $v == 'transfers_edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carstransfers.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add' || $v == 'edit' || $v == 'debit_note' || $v == 'credit_note_inv')) ? '<script type="text/javascript" src="' . $assets . 'js/sales.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'edit_discount')) ? '<script type="text/javascript" src="' . $assets . 'js/sales_discount.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carssales.js"></script>' : ''; ?>
<?= ($m == 'returns' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_return' || $v == 'edit_return')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_return' || $v == 'edit_return')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'quotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js"></script>' : ''; ?>
<?= ($m == 'carsquotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carsquotes.js"></script>' : ''; ?>
<?= ($m == 'carsoffers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carsoffers.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_quote' || $v == 'edit_quote')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_deliver' || $v == 'edit_deliver')) ? '<script type="text/javascript" src="' . $assets . 'js/delivers.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_quote' || $v == 'edit_quote')) ? '<script type="text/javascript" src="' . $assets . 'js/carsquotes.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_offer' || $v == 'edit_offer')) ? '<script type="text/javascript" src="' . $assets . 'js/carsoffers.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'accounts' && ($v == 'add_opening_balance' || $v == 'edit_opening_balance')) ? '<script type="text/javascript" src="' . $assets . 'js/opening_balance.js?' . time() . '"></script>' : ''; ?>

<?= ($m == 'products' && ($v == 'add_adjustment_count')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments_count.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment_store')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments_store.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'destruction_inventory' || $v == 'edit_destruction')) ? '<script type="text/javascript" src="' . $assets . 'js/destruction_inventory.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'destruction_inventory' || $v == 'edit_destruction')) ? '<script type="text/javascript" src="' . $assets . 'js/destruction_inventory.js?' . time() . '"></script>' : ''; ?>

<?= ($m == 'products' && ($v == 'add_adjust' || $v == 'edit_adjust')) ? '<script type="text/javascript" src="' . $assets . 'js/quantity_adjustments.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_revaluation' || $v == 'edit_revaluation')) ? '<script type="text/javascript" src="' . $assets . 'js/revaluations.js"></script>' : ''; ?>

<script type="text/javascript" charset="UTF-8">


var isFullscreen = false;
var elem = document.getElementById("controls");

            function fullscreen(){



  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) { /* Safari */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE11 */
    elem.msRequestFullscreen();
  }

				/*var d = {};
                var speed = 300;
                if(!isFullscreen){ // MAXIMIZATION
                    d.width = "100%";
                    d.height = "100%";
                    isFullscreen = true;
                    $("#mainnav2").slideUp(speed);
                    $("#mainnav").slideUp(speed);
                    $("#mainnav1").slideUp(speed);
                }
                else{ // MINIMIZATION
                    d.width = "300px";
                    d.height = "100px";
                    isFullscreen = false;
                    $("#mainnav2").slideDown(speed);
                    $("#mainnav").slideDown(speed);
                    $("#mainnav1").slideDown(speed);
                }
                $("#controls").animate(d,speed);  */
            }

var oTable = '', r_u_sure = "<?=lang('r_u_sure')?>";
    <?=$s2_file_date?>
    $.extend(true, $.fn.dataTable.defaults, {"oLanguage":<?=$dt_lang?>});
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;

    $(window).load(larg);
    $(window).resize(larg);
    function larg(){
    var x = window.matchMedia("(max-width: 991px)");

		if(!x.matches){

        $('#mm_welcome').css("display", "none");
        $('#mm_products').css("display", "none");
        $('#mm_sales').css("display", "none");
        $('#mm_carssales').css("display", "none");
        $('#mm_cars').css("display", "none");
        $('#mm_purchases').css("display", "none");
        $('#mm_carspurchases').css("display", "none");
        $('#mm_auth').css("display", "none");
        $('#mm_reports').css("display", "none");
        $('#mm_carsreports').css("display", "none");
        $('#mm_accounts').css("display", "none");
        $('#mm_manufacturing').css("display", "none");
        $('#mm_fixedassets').css("display", "none");
        $('#mm_tailor').css("display", "none");
        $('#mm_branches').css("display", "none");
        $('#mm_sync').css("display", "none");
        $('#mm_shop_settings').css("display", "none");
        $('#mm_hr').css("display", "none");
        $('#mm_crm').css("display", "none");

		if('<?=$m?>'=='shop_settings'){
		//$('#mm_auth').css("display", "block");
		//$('#mm_shop_settings').css("display", "block");
		}



		if('<?=$m?>'=='system_settings' || '<?=$m?>'=='billers')
		$('#mm_auth').css("display", "block");
		if('<?=$m?>'=='pos' && '<?=$v?>'=='sales'){
		$('#mm_sales').css("display", "block");
		}
		if('<?=$m?>'=='pos' && '<?=$v?>'=='printers'){
		$('#mm_auth').css("display", "block");
		}
		if('<?=$m?>'=='pos' && '<?=$v?>'=='carssales'){
		$('#mm_carssales').css("display", "block");
		}
		if('<?=$m?>'=='pos' && '<?=$v?>'=='settings'){
		$('#mm_auth').css("display", "block");
		}
		if('<?=$m?>'=='promos' || '<?=$m?>'=='promo' || '<?=$m?>'=='customers' || '<?=$m?>'=='salesman')
		$('#mm_sales').css("display", "block");

        if('<?=$m?>'=='promos' || '<?=$m?>'=='promo' || '<?=$m?>'=='customers' || '<?=$m?>'=='salesman')
		$('#mm_carssales').css("display", "block");

		if('<?=$m?>'=='suppliers')
		$('#mm_purchases').css("display", "block");

		if('<?=$m?>'=='carssuppliers')
		$('#mm_carspurchases').css("display", "block");

        if('<?=$m?>'=='carstransfers')
		$('#mm_cars').css("display", "block");








        $('#mm_<?=$m?>').css("display", "block");
        }else{

        $('#mm_welcome').css("display", "block");
        $('#mm_products').css("display", "block");
        $('#mm_sales').css("display", "block");
        $('#mm_carssales').css("display", "block");
        $('#mm_cars').css("display", "block");
        $('#mm_purchases').css("display", "block");
        $('#mm_carspurchases').css("display", "block");
        $('#mm_auth').css("display", "block");
        $('#mm_reports').css("display", "block");
        $('#mm_carsreports').css("display", "block");
        $('#mm_accounts').css("display", "block");
        $('#mm_manufacturing').css("display", "block");
        $('#mm_fixedassets').css("display", "block");
        $('#mm_tailor').css("display", "block");
        $('#mm_branches').css("display", "block");
        $('#mm_sync').css("display", "block");
        $('#mm_shop_settings').css("display", "block");
        }


		$('.mm_<?=$m?>').addClass('active');
        $('.mm_<?=$m?>').find("ul").first().slideToggle();
        $('#<?=$m?>_<?=$v?>').addClass('active');
        $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");


        if('<?=$m?>'=='system_settings' && '<?=$v?>'=='expense_categories'){
        $('#mm_purchases').css("display", "block");
        $('.mm_purchases').addClass('active');
        $('#mm_auth').css("display", "none");
        $('.mm_auth').removeClass('active');
		}
    }

</script>
<?php

if($m == 'suppliers' && ($v == 'supplier_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'pos' && ($v == 'sales')){
	?>
<script>

    $(window).load(function () {
        $('.mm_pos1').addClass('active');
        $('.mm_pos').removeClass('active');
        //$('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'customers' && ($v == 'customers_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'auth' && ($v == 'users_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'promo' && ($v == 'promo_type')){
	?>
<script>

    $(window).load(function () {
        $('.mm_sales_promo_<?=$v?>').addClass('active');
        $('.mm_sales_promo_<?=$v?>').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'purchases' && ($v == 'daily_purchases' || $v == 'monthly_purchases' || $v == 'purchases')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'purchases' && ($v == 'expenses_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_expenses_report').addClass('active');
        $('.mm_<?=$m?>_expenses_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'sales' && ($v == 'daily_sales' || $v == 'monthly_sales' || $v == 'sales' || $v == 'best_sellers')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'sales' && ($v == 'returns_index' || $v == 'add_return')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_return').addClass('active');
        $('.mm_<?=$m?>_return').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'sales' && ($v == 'quotes_index' || $v == 'add_quote')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_quote').addClass('active');
        $('.mm_<?=$m?>_quote').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>

<?php

if($m == 'carssales' && ($v == 'offers_index' || $v == 'add_offer')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_offer').addClass('active');
        $('.mm_<?=$m?>_offer').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'products' && ($v == 'add' || $v == 'units' || $v == 'import_csv' || $v == 'import_ecxel' || $v == 'print_barcodes' || $v == 'categories')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_definitions').addClass('active');
        $('.mm_<?=$m?>_definitions').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'products' && ($v == 'manual_inventory' || $v == 'count_stock' || $v == 'stock_counts')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_inventory').addClass('active');
        $('.mm_<?=$m?>_inventory').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>

<?php

if($m == 'products' && ($v == 'destruction_inventory' || $v == 'destruction')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_destruction').addClass('active');
        $('.mm_<?=$m?>_destruction').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>

<?php

if($m == 'products' && ($v == 'transfers' || $v == 'transfers_add' || $v == 'transfer_by_csv')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_transfers').addClass('active');
        $('.mm_<?=$m?>_transfers').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>

<?php

if($m == 'products' && ($v == 'warehouse_stock'  ||  $v == 'adjustments_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_report').addClass('active');
        $('.mm_<?=$m?>_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>
<?php

if($m == 'products' && ($v == 'quantity_alerts'  ||  $v == 'expiry_alerts'  ||  $v == 'products_report')){
	?>
<script>

    $(window).load(function () {
        $('.mm_<?=$m?>_product').addClass('active');
        $('.mm_<?=$m?>_product').find("ul").first().slideToggle();
        $('.mm_<?=$m?>_product_report').addClass('active');
        $('.mm_<?=$m?>_product_report').find("ul").first().slideToggle();


    });
</script>
<?php

}
?>

<!--
<?= (DEMO) ? '<script src="' . $assets . 'js/ppp_ad.min.js"></script>' : ''; ?>

-->

<?php




$notif=null;
$otherdb = $this->load->database('info', true);
            $check = $otherdb->where('company',$this->session->userdata('company'))->get('client')->row();

            //print_r($check->company);
        $effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($check->renewal_date)));
		$period_from_date = $effectiveEndDate;
        //$period_from_date = $check->created_at;//$effectiveEndDate;
        $period_to_date = date('Y-m-d');

        $ts1 = strtotime($period_from_date);
        $ts2 = strtotime($period_to_date);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1 + 1);
        $date1 = new DateTime($period_from_date);
        $date2 = new DateTime($period_to_date);
        $interval = $date1->diff($date2);
        //echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ";
        //echo "difference " . $interval->days . " days ";
					if(($date1 < $date2) && $check->company!='leen'){

                    //noti extends
                   // $notif='You have exceeded the subscription period '.$interval->days;
                   // $notif=$lang['Notify_after_end_supscription'] . ' : ' . $interval->days;
                    $notif= lang('Dear_customer,_you_have_exceeded_the_subscription_period_for') .' ' . $interval->days .' '. lang('Day') .' , '. lang('Since_your_subscription_has_expired_at') . ' ' .$this->sma->hrsd($effectiveEndDate) .' , '. lang('To_renew_your_subscription_and_ensure_the_continuation_of_the_service_please_contact_Arkan_Pro_for_Information_Technology_Company_on_the_phone_numbers_0534277747_-_0560531961_or_email_info_@_companypro.com.sa');
                    ?>
					<script>
const myNotification = window.createNotification({
      // options here
     // close on click
  closeOnClick: false,
  // displays close button
  displayCloseButton: false,
  // nfc-top-left
  // nfc-bottom-right
  // nfc-bottom-left
  positionClass: 'nfc-top-right',
  // callback
  onclick: false,
  // timeout in milliseconds
  showDuration: 0,
  // success, info, warning, error, and none
  theme: 'info'
});

myNotification({
  title: '<?= lang('Important_notice') ?>',
  message: '<?= $notif ?>'
});

</script>

					<?php
					}else{
                    if($interval->days<=15  && $m == 'welcome'){
                             //("<script type='text/javascript'> alert('') ;</script>");
                    // $notif='Keep your subscription '.$interval->days.' The day your subscription ends '.$this->sma->hrsd($effectiveEndDate);
                     $notif=lang('Dear_customer,_the_date_of_your_subscription_with_us_remains') .' ' . $interval->days .' '. lang('Day') .' '. lang('Your_subscription_will_expire_on') . ' ' .$this->sma->hrsd($effectiveEndDate) .' '. lang('To_renew_your_subscription_and_ensure_the_continuation_of_the_service_please_contact_Arkan_Pro_for_Information_Technology_Company_on_the_phone_number_0534277747_or_email_info_@_companypro.com.sa');
                    ?>
					<script>
const myNotification = window.createNotification({
      // options here
     // close on click
  closeOnClick: true,
  // displays close button
  displayCloseButton: true,
  // nfc-top-left
  // nfc-bottom-right
  // nfc-bottom-left
  positionClass: 'nfc-top-right',
  // callback
  onclick: false,
  // timeout in milliseconds
  showDuration: 0,
  // success, info, warning, error, and none
  theme: 'info'
});

myNotification({
  title: '<?= lang('Important_notice') ?>',
  message: '<?= $notif ?>'
});

</script>

					<?php
					  //$notif=$text;
                    }

                    }
?>



	<!--
	<script src="<?= $assets ?>template1/vendors/jquery/dist/jquery.min.js"></script>
                -->
	<!-- Bootstrap
	<script src="<?= $assets ?>template1/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    -->
	<!-- FastClick -->
    <script src="<?= $assets ?>template1/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?= $assets ?>template1/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js
    <script src="<?= $assets ?>template1/vendors/Chart.js/dist/Chart.min.js"></script>


	-->
    <!-- gauge.js -->
    <script src="<?= $assets ?>template1/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="<?= $assets ?>template1/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="<?= $assets ?>template1/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="<?= $assets ?>template1/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="<?= $assets ?>template1/vendors/Flot/jquery.flot.js"></script>
    <script src="<?= $assets ?>template1/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="<?= $assets ?>template1/vendors/Flot/jquery.flot.time.js"></script>
    <script src="<?= $assets ?>template1/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="<?= $assets ?>template1/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="<?= $assets ?>template1/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="<?= $assets ?>template1/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="<?= $assets ?>template1/vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="<?= $assets ?>template1/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="<?= $assets ?>template1/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="<?= $assets ?>template1/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="<?= $assets ?>template1/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="<?= $assets ?>template1/vendors/moment/min/moment.min.js"></script>
    <script src="<?= $assets ?>template1/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts
    <script src="<?= $assets ?>template1/build/js/custom.min.js"></script>
	 -->

	 <script>

	 /**
 * Resize function without multiple trigger
 *
 * Usage:
 * $(window).smartresize(function(){
 *     // code here
 * });
 */


(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null;
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100);
        };
    };

    // smartresize
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');


$(document).ready(function () {

    var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer');
	// Sidebar
try{
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.outerHeight(),
            footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
            leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + footerHeight;

        $RIGHT_COL.css('min-height', contentHeight);
    };

    var openUpMenu = function () {
        $SIDEBAR_MENU.find('li').removeClass('active active-sm');
        $SIDEBAR_MENU.find('li ul').slideUp();
    }

    $SIDEBAR_MENU.find('a').on('click', function (ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function () {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if ($li.parent().is('.child_menu')) {
			if (!$li.parent().is('.child_menu')) {
                openUpMenu();
            } else {
                if ($BODY.is('nav-sm')) {
                    if (!$li.parent().is('child_menu')) {
                        openUpMenu();
                    }
                }
            }
			}else{
			if (!$li.parent().is('.child_menu_ar')) {
                openUpMenu();
            } else {
                if ($BODY.is('nav-sm')) {
                    if (!$li.parent().is('child_menu_ar')) {
                        openUpMenu();
                    }
                }
            }
			}

            $li.addClass('active');

            $('ul:first', $li).slideDown(function () {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function () {
        if ($BODY.hasClass('nav-md')) {
            $SIDEBAR_MENU.find('li.active ul').hide();
            $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $SIDEBAR_MENU.find('li.active-sm ul').show();
            $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $BODY.toggleClass('nav-md nav-sm');

        setContentHeight();

        $('.dataTable').each(function () { $(this).dataTable().fnDraw(); });
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function () {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function () {
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel: { preventDefault: true }
        });
    }
}catch(err){alert(err);}
// /Sidebar

    function updateCategories() {


    $('#ajaxCall').hide();

    $.ajax({
        url: '<?= admin_url("shop_settings/update_categories") ?>',
        type: 'GET',
        success: function (data) {

            if (data.msg) {

                if (data.to != data.total) {
                    updateCategories();
                } else {
                    bttn.classList.remove("loading");
                }

            } else {
            }
        },
        error: function () {
        }
    });
}

    $(document).on('click', '#startTrialBtn', function (e) {
    e.preventDefault();

    let btn = this;



    btn.classList.add("loading");

    let newWindow = window.open('', '_blank');
    if (newWindow) {
    }

    $.ajax({
        url: '<?= admin_url("shop_settings/get_store_link_notif") ?>',
        type: 'GET',
        dataType: 'json',
                beforeSend: function () {
            $('#startTrialBtn').text('جاري التحويل...');
        },
        success: function (res) {
            btn.classList.remove("loading");

            if (res && res.url) {
                $(btn).data('url', res.url);

                if (newWindow) {
                    newWindow.location.href = res.url;
                } else {
                    window.location.href = res.url;
                }

                $('.promo-banner').hide();
                updateCategories();
            } else {
                if (newWindow) newWindow.close();
                $('#startTrialBtn').text('ابدأ متجرك الإلكتروني');

            }
        },
        error: function (xhr) {
            btn.classList.remove("loading");
            if (newWindow) newWindow.close();
        }
    });
    });

});


	 </script>

     <?php
        $dbname = str_replace("cpesoft_", "", $this->db->database);
        if ($dbname == "spark" || $dbname == "aic") {
        $is_arabic = ($Settings->user_language == 'arabic'); ?>

<!-- ============================================================
     Smart Accountant — floating avatar + Insight Console
     Console is a command-bar + single result card (deliberately
     NOT a chat transcript: one question, one answer surface).
     ============================================================ -->
<style>
/* ---------- floating avatar (80% smaller than the old 170px) ---------- */
#cpFloatWidget {
    position: fixed; z-index: 999996;
    box-sizing: border-box; width: 136px; height: 136px;
    top: 78px; <?= $is_arabic ? 'right: 24px;' : 'left: 24px;' ?>
    border-radius: 50%;
    background: radial-gradient(circle at 35% 28%, #818cf8 0%, #6366f1 45%, #4338ca 100%);
    border: 2px solid #6366f1;
    box-shadow: 0 6px 18px rgba(99,102,241,.45), inset 0 0 12px rgba(255,255,255,.18);
    cursor: grab; user-select: none; touch-action: none;
    transition: transform .2s ease, box-shadow .2s ease;
}
#cpFloatWidget:hover { transform: scale(1.08); box-shadow: 0 10px 24px rgba(99,102,241,.7); }
#cpFloatWidget:active { cursor: grabbing; transform: scale(1.02); }
#cpFloatWidget.cp-open { box-shadow: 0 10px 26px rgba(99,102,241,.8), 0 0 0 3px rgba(99,102,241,.25); }
/* avatar centred in the circle: framed on the head/torso */
#cpFloatWidget .cp-av {
    position: absolute; inset: 0; border-radius: 50%; overflow: hidden;
}
#cpFloatWidget model-viewer {
    width: 100%; height: 100%;
    pointer-events: none; background-color: transparent;
}
#cpFloatWidget model-viewer::part(default-progress-bar) { display: none; }
/* online badge — aligned to the 68px circle edge */
#cpFloatWidget .cp-online-badge {
    position: absolute; bottom: 13px; <?= $is_arabic ? 'left: 13px;' : 'right: 13px;' ?> /* on the 136px circumference */
    width: 14px; height: 14px; border-radius: 50%;
    background: #34d399; border: 2px solid #4338ca;
    box-shadow: 0 0 6px #34d399; pointer-events: none; z-index: 3;
}
/* close / dismiss */
/* close: white circle, blue X, centred — lives at the end of the chip */
#cpFloatWidget .cp-hide {
    flex: 0 0 18px; width: 18px; height: 18px; border-radius: 50%;
    background: #fff; color: #6366f1; border: 0; padding: 0; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s ease, color .15s ease;
}
#cpFloatWidget .cp-hide svg { display: block; width: 8px; height: 8px; }
#cpFloatWidget .cp-hide:hover { background: #ef4444; color: #fff; }
#cpFloatWidget .cp-tooltip {
    position: absolute; bottom: calc(100% + 8px); left: 50%;
    transform: translateX(-50%);
    display: flex; align-items: center; gap: 5px;   /* label + close, 5px apart */
    background: #6366f1; color: #fff;
    border: 1px solid rgba(255,255,255,.28);
    padding: 5px 7px 5px 11px; border-radius: 10px; font-size: 11.5px; white-space: nowrap;
    box-shadow: 0 6px 16px rgba(99,102,241,.4);
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity .2s ease; font-family: 'Cairo', sans-serif;
}
#cpFloatWidget:hover .cp-tooltip { opacity: 1; visibility: visible; pointer-events: auto; }

/* restore pill, shown once the widget is dismissed */
#cpRestore {
    position: fixed; z-index: 999996; bottom: 18px; <?= $is_arabic ? 'right: 18px;' : 'left: 18px;' ?>
    display: none; align-items: center; gap: 7px; cursor: pointer;
    background: #fff; color: #4338ca; border: 1px solid #e2e8f0;
    border-radius: 999px; padding: 7px 13px; font-size: 12px; font-weight: 600;
    box-shadow: 0 4px 14px rgba(15,23,42,.14); font-family: 'Cairo', sans-serif;
}
#cpRestore:hover { border-color: #818cf8; }

/* ---------- Insight Console ---------- */
#cpConsole {
    position: fixed; z-index: 999997; width: 380px; max-width: calc(100vw - 28px);
    background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 20px 50px rgba(15,23,42,.20), 0 2px 6px rgba(15,23,42,.06);
    font-family: 'Cairo', 'Inter', sans-serif; direction: <?= $is_arabic ? 'rtl' : 'ltr' ?>;
    display: none; opacity: 0; transform: translateY(8px) scale(.985);
    transition: opacity .18s ease, transform .18s cubic-bezier(.2,.9,.3,1.2);
    overflow: hidden;
}
#cpConsole.active { display: block; opacity: 1; transform: translateY(0) scale(1); }
/* command bar first — the console opens focused on input, not on a message log */
.cpc-bar { display: flex; align-items: center; gap: 8px; padding: 12px 14px; border-bottom: 1px solid #f1f5f9; }
.cpc-spark { width: 26px; height: 26px; border-radius: 8px; flex: 0 0 26px; display: grid; place-items: center;
    background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff; font-size: 13px; }
.cpc-bar input { flex: 1; border: 0; outline: 0; font: inherit; font-size: 13.5px; color: #0f172a; background: transparent; }
.cpc-bar input::placeholder { color: #94a3b8; }
.cpc-go { border: 0; background: #eef2ff; color: #4338ca; width: 28px; height: 28px; border-radius: 8px; cursor: pointer; display: grid; place-items: center; }
.cpc-go:hover { background: #6366f1; color: #fff; }
.cpc-x { border: 0; background: transparent; color: #94a3b8; cursor: pointer; font-size: 17px; line-height: 1; padding: 2px 4px; }
.cpc-x:hover { color: #ef4444; }
/* quick chips */
.cpc-chips { display: flex; gap: 6px; padding: 10px 14px; flex-wrap: wrap; }
.cpc-chip { font-size: 11.5px; padding: 5px 11px; border-radius: 999px; cursor: pointer;
    background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; transition: .15s; }
.cpc-chip:hover { background: #eef2ff; border-color: #818cf8; color: #4338ca; }
/* single result card — replaced in place, never stacked */
.cpc-body { padding: 0 14px 14px; max-height: 46vh; overflow-y: auto; }
.cpc-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #fff; animation: cpcIn .22s ease; }
@keyframes cpcIn { from { opacity: 0; transform: translateY(6px); } }
.cpc-head { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.cpc-head i { width: 26px; height: 26px; border-radius: 8px; display: grid; place-items: center;
    background: #eef2ff; color: #6366f1; font-size: 14px; }
.cpc-head h4 { margin: 0; font-size: 13px; font-weight: 700; color: #0f172a; }
.cpc-dates { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 8px; }
.cpc-date { font-size: 10.5px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 999px; padding: 2px 8px; }
/* figures as a compact stat grid, not paragraphs */
.cpc-pills { display: grid; grid-template-columns: repeat(auto-fit, minmax(96px,1fr)); gap: 7px; margin-bottom: 9px; }
.cpc-pill { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; padding: 7px 9px; }
.cpc-pill span { display: block; font-size: 10.5px; color: #64748b; margin-bottom: 2px; }
.cpc-pill b { font-size: 13.5px; color: #4338ca; font-weight: 800; }
.cpc-text { margin: 0; font-size: 12px; line-height: 1.7; color: #475569; }
.cpc-link { display: inline-flex; align-items: center; gap: 5px; margin-top: 9px; font-size: 11.5px;
    font-weight: 600; color: #4338ca; text-decoration: none; }
.cpc-link:hover { text-decoration: underline; }
.cpc-foot { display: flex; justify-content: space-between; align-items: center;
    padding: 9px 14px; border-top: 1px solid #f1f5f9; background: #f8fafc; font-size: 11px; color: #94a3b8; }
.cpc-foot a { color: #4338ca; font-weight: 600; text-decoration: none; }
.cpc-load { display: flex; gap: 4px; padding: 14px; justify-content: center; }
.cpc-load i { width: 6px; height: 6px; border-radius: 50%; background: #818cf8; animation: cpcB .9s infinite ease-in-out; }
.cpc-load i:nth-child(2) { animation-delay: .15s; } .cpc-load i:nth-child(3) { animation-delay: .3s; }
@keyframes cpcB { 0%,80%,100% { opacity: .25; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-5px); } }
.cp-riyal { width: .72em; height: .8em; display: inline-block; vertical-align: -.08em; fill: currentColor; margin-inline-start: 2px; }
/* invoice rows — a list of documents, not a data grid: each row is one tap
   target that opens the invoice list's own modal (Print / PDF included). */
.cpc-invs { display: flex; flex-direction: column; gap: 6px; margin-top: 9px; }
.cpc-inv { display: flex; align-items: center; gap: 8px; text-decoration: none;
    border: 1px solid #e2e8f0; border-radius: 9px; padding: 7px 9px; background: #fff;
    transition: border-color .15s ease, background .15s ease; }
.cpc-inv:hover { border-color: #818cf8; background: #f8faff; text-decoration: none; }
.cpc-inv-main { flex: 1 1 auto; min-width: 0; }
.cpc-inv-main b { display: block; font-size: 12px; color: #0f172a; font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cpc-inv-sub { display: block; font-size: 10.5px; color: #64748b; font-style: normal;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cpc-inv-side { flex: 0 0 auto; text-align: <?= $is_arabic ? 'left' : 'right' ?>; }
.cpc-inv-side b { display: block; font-size: 12px; color: #4338ca; font-weight: 800; white-space: nowrap; }
.cpc-tag { display: inline-block; font-size: 9.5px; font-style: normal; border-radius: 999px;
    padding: 1px 7px; margin-top: 2px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.cpc-tag.is-paid { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
.cpc-tag.is-partial { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.cpc-tag.is-pending { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.cpc-more { display: block; text-align: center; font-size: 11px; font-weight: 600;
    color: #4338ca; text-decoration: none; padding: 5px; border-radius: 8px; background: #f8fafc; }
.cpc-more:hover { background: #eef2ff; text-decoration: none; }
/* the copilot sits above everything, which would also put it above a bootstrap
   modal — step aside while one is open so invoices stay reachable. */
body.cp-modal-open #cpFloatWidget,
body.cp-modal-open #cpRestore,
body.cp-modal-open #cpConsole { z-index: 1039 !important; }
@media (max-width: 575px) { #cpConsole { width: calc(100vw - 24px); } }
</style>

<!-- Saudi Riyal glyph sprite. copilotMoney() emits <use href="#cpRiyal">, so the
     symbol has to exist wherever an answer is rendered — the console runs on
     every admin page, not just the 3D deck that used to define it. -->
<svg width="0" height="0" style="position:absolute;overflow:hidden" aria-hidden="true" focusable="false"><symbol id="cpRiyal" viewBox="0 0 1124.14 1256.39"><path d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></symbol></svg>

<div id="cpFloatWidget">
    <div class="cp-av">
        <model-viewer src="<?= base_url('assets/glb/saudi_businessman_web.glb') ?>" camera-controls="false" disable-zoom interaction-prompt="none" camera-orbit="0deg 82deg 2.3m"></model-viewer>
    </div>
    <div class="cp-online-badge"></div>
    <div class="cp-tooltip">
        <span><?= $is_arabic ? 'المحاسب الذكي' : 'Smart Accountant' ?></span>
        <button type="button" class="cp-hide" id="cpHide" title="<?= $is_arabic ? 'إخفاء' : 'Hide' ?>"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l8 8M9 1l-8 8"/></svg></button>
    </div>
</div>

<div id="cpRestore"><i class="fa fa-diamond"></i> <?= $is_arabic ? 'المحاسب الذكي' : 'Smart Accountant' ?></div>

<div id="cpConsole">
    <div class="cpc-bar">
        <span class="cpc-spark"><i class="fa fa-diamond"></i></span>
        <input type="text" id="cpcInput" autocomplete="off" placeholder="<?= $is_arabic ? 'اسأل… مثال: مبيعات هذا الشهر' : 'Ask… e.g. sales this month' ?>">
        <button type="button" class="cpc-go" id="cpcGo" aria-label="ask">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
        </button>
        <button type="button" class="cpc-x" id="cpcClose">&times;</button>
    </div>
    <div class="cpc-chips" id="cpcChips">
        <span class="cpc-chip"><?= $is_arabic ? 'مبيعات هذا الشهر' : 'Sales this month' ?></span>
        <span class="cpc-chip"><?= $is_arabic ? 'فواتير هذا الشهر' : 'Invoices this month' ?></span>
        <span class="cpc-chip"><?= $is_arabic ? 'الفواتير غير المدفوعة' : 'Unpaid invoices' ?></span>
        <span class="cpc-chip"><?= $is_arabic ? 'ضريبة الربع الثاني' : 'VAT for Q2' ?></span>
    </div>
    <div class="cpc-body" id="cpcBody"></div>
    <div class="cpc-foot">
        <span><?= $is_arabic ? 'اضغط Esc للإغلاق' : 'Press Esc to close' ?></span>
        <a href="<?= admin_url('welcome/copilot') ?>"><?= $is_arabic ? 'العرض ثلاثي الأبعاد ↗' : '3D view ↗' ?></a>
    </div>
</div>

<script>
(function () {
    var W = document.getElementById('cpFloatWidget'),
        C = document.getElementById('cpConsole'),
        B = document.getElementById('cpcBody'),
        I = document.getElementById('cpcInput'),
        R = document.getElementById('cpRestore'),
        AR = <?= $is_arabic ? 'true' : 'false' ?>,
        URL = '<?= admin_url('welcome/copilot_ask') ?>';

    /* ---- dismiss / restore ---- */
    function hideW() { W.style.display = 'none'; R.style.display = 'inline-flex'; close(); try { localStorage.setItem('cpWidgetHidden', '1'); } catch (e) {} }
    function showW() { W.style.display = 'block'; R.style.display = 'none'; try { localStorage.removeItem('cpWidgetHidden'); } catch (e) {} }
    var HB = document.getElementById('cpHide');
    HB.addEventListener('mousedown', function (e) { e.stopPropagation(); });
    HB.addEventListener('touchstart', function (e) { e.stopPropagation(); }, { passive: true });
    HB.addEventListener('click', function (e) { e.stopPropagation(); hideW(); });
    R.addEventListener('click', showW);
    try { if (localStorage.getItem('cpWidgetHidden') === '1') { W.style.display = 'none'; R.style.display = 'inline-flex'; } } catch (e) {}

    /* ---- restore saved position ---- */
    try {
        var L = localStorage.getItem('cpWidgetLeft'), T = localStorage.getItem('cpWidgetTop');
        if (L !== null && T !== null) { W.style.left = L + 'px'; W.style.top = T + 'px'; W.style.right = 'auto'; }
    } catch (e) {}

    /* ---- drag (click = open, drag = move) ---- */
    var dragging = false, sx = 0, sy = 0, il = 0, it = 0;
    function down(e) {
        dragging = false;
        var x = e.touches ? e.touches[0].clientX : e.clientX, y = e.touches ? e.touches[0].clientY : e.clientY;
        sx = x; sy = y;
        var r = W.getBoundingClientRect(); il = r.left; it = r.top;
        document.addEventListener('mousemove', move); document.addEventListener('mouseup', up);
        document.addEventListener('touchmove', move, { passive: false }); document.addEventListener('touchend', up);
    }
    function move(e) {
        var x = e.touches ? e.touches[0].clientX : e.clientX, y = e.touches ? e.touches[0].clientY : e.clientY,
            dx = x - sx, dy = y - sy;
        if (Math.sqrt(dx * dx + dy * dy) > 6) {
            dragging = true; if (e.cancelable) e.preventDefault();
            W.style.left = Math.max(6, Math.min(innerWidth - W.offsetWidth - 6, il + dx)) + 'px';
            W.style.top = Math.max(6, Math.min(innerHeight - W.offsetHeight - 6, it + dy)) + 'px';
            W.style.right = 'auto';
            if (C.classList.contains('active')) place();
        }
    }
    function up() {
        document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', up);
        document.removeEventListener('touchmove', move); document.removeEventListener('touchend', up);
        if (dragging) { try { localStorage.setItem('cpWidgetLeft', parseInt(W.style.left)); localStorage.setItem('cpWidgetTop', parseInt(W.style.top)); } catch (e) {} }
        else { C.classList.contains('active') ? close() : open(); }
    }
    W.addEventListener('mousedown', down);
    W.addEventListener('touchstart', down, { passive: true });

    /* ---- console anchored beside the avatar, kept on-screen ---- */
    function place() {
        var r = W.getBoundingClientRect(), w = C.offsetWidth || 380, h = C.offsetHeight || 320, gap = 10, l, t;
        l = AR ? r.left - w - gap : r.right + gap;
        if (l < 8) l = r.right + gap; if (l + w > innerWidth - 8) l = Math.max(8, r.left - w - gap);
        if (l < 8) l = 8;
        t = Math.min(Math.max(8, r.top), innerHeight - h - 8); if (t < 8) t = 8;
        C.style.left = l + 'px'; C.style.top = t + 'px';
    }
    function open() { C.classList.add('active'); W.classList.add('cp-open'); place(); setTimeout(place, 20); I.focus(); if (!B.innerHTML) greet(); }
    function close() { C.classList.remove('active'); W.classList.remove('cp-open'); }
    document.getElementById('cpcClose').addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    document.addEventListener('mousedown', function (e) {
        if (C.classList.contains('active') && !C.contains(e.target) && !W.contains(e.target)) close();
    });
    addEventListener('resize', function () { if (C.classList.contains('active')) place(); });

    /* ---- rendering: ONE result card, replaced in place ---- */
    function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
    function greet() {
        B.innerHTML = '<div class="cpc-card"><div class="cpc-head"><i class="fa fa-diamond"></i><h4>' +
            (AR ? 'أهلاً بك' : 'Welcome') + '</h4></div><p class="cpc-text">' +
            (AR ? 'اسأل عن المبيعات أو المشتريات أو الضريبة لأي فترة.' : 'Ask about sales, purchases or VAT for any period.') + '</p></div>';
    }
    function render(d) {
        var h = '<div class="cpc-card"><div class="cpc-head"><i class="' + esc(d.icon || 'fa fa-diamond') + '"></i><h4>' + esc(d.title || '') + '</h4></div>';
        if (d.dates && d.dates.length) {
            h += '<div class="cpc-dates">';
            d.dates.forEach(function (x) { h += '<span class="cpc-date">' + esc(typeof x === 'string' ? x : (x.k || '') + ' ' + (x.v || '')) + '</span>'; });
            h += '</div>';
        }
        if (d.pills && d.pills.length) {
            h += '<div class="cpc-pills">';
            /* pill values carry the Riyal <svg>, so they are injected as markup by design */
            d.pills.forEach(function (p) { h += '<div class="cpc-pill"><span>' + esc(p.k) + '</span><b>' + (p.v || '') + '</b></div>'; });
            h += '</div>';
        }
        if (d.text) h += '<p class="cpc-text">' + d.text + '</p>';
        if (d.rows && d.rows.length) h += invoiceRows(d);
        if (d.link) h += '<a class="cpc-link" href="' + esc(d.link) + '">' + (AR ? 'فتح التقرير الكامل ↗' : 'Open full report ↗') + '</a>';
        B.innerHTML = h + '</div>';
    }
    /* Invoice rows. Only the money fields are markup (they may carry the Riyal
       glyph) — every other value comes from the database and is escaped. */
    function invoiceRows(d) {
        var h = '<div class="cpc-invs">';
        d.rows.forEach(function (r) {
            var sub = [r.date, r.ref2, r.party].filter(Boolean).map(esc).join(' · ');
            h += '<a class="cpc-inv" href="' + esc(r.url) + '" data-modal="' + esc(r.modal) + '">' +
                     '<span class="cpc-inv-main"><b>' + esc(r.ref) + '</b><i class="cpc-inv-sub">' + sub + '</i></span>' +
                     '<span class="cpc-inv-side"><b>' + (r.total || '') + '</b>' +
                        (r.pay ? '<em class="cpc-tag is-' + esc(r.pay_key) + '">' + esc(r.pay) + '</em>' : '') +
                     '</span></a>';
        });
        if (d.more > 0 && d.link) {
            h += '<a class="cpc-more" href="' + esc(d.link) + '">+' + esc(String(d.more)) +
                 ' ' + (AR ? 'فاتورة أخرى' : 'more') + ' →</a>';
        }
        return h + '</div>';
    }
    /* A row opens the invoice list's OWN modal, so the invoice arrives with its
       existing Print / PDF buttons. The href stays a real link: middle-click,
       "open in new tab" and a missing jQuery all still work. */
    B.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('.cpc-inv') : null;
        if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
        var url = a.getAttribute('data-modal');
        if (!url || !window.jQuery || !jQuery('#myModal').length) return;
        e.preventDefault();
        close();
        jQuery('#myModal').removeData('bs.modal').modal({ remote: url, show: true });
    });
    function ask(q) {
        if (!q) return;
        I.value = q;
        B.innerHTML = '<div class="cpc-load"><i></i><i></i><i></i></div>';
        fetch(URL, {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ q: q })
        }).then(function (r) { return r.json(); })
          .then(render)
          .catch(function () {
              B.innerHTML = '<div class="cpc-card"><p class="cpc-text">' +
                  (AR ? 'تعذّر الاتصال. حاول مرة أخرى.' : 'Connection failed. Please try again.') + '</p></div>';
          });
    }
    document.getElementById('cpcGo').addEventListener('click', function () { ask(I.value.trim()); });
    I.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); ask(I.value.trim()); } });
    document.getElementById('cpcChips').addEventListener('click', function (e) {
        if (e.target.classList.contains('cpc-chip')) ask(e.target.textContent.trim());
    });

    /* The copilot is pinned above the whole UI, so any bootstrap modal — this
       feature's invoice view included — has to outrank it while it is open. */
    if (window.jQuery) {
        jQuery(document)
            .on('show.bs.modal', function () { document.body.classList.add('cp-modal-open'); })
            .on('hidden.bs.modal', function () {
                if (!jQuery('.modal.in').length) { document.body.classList.remove('cp-modal-open'); }
            });
    }
})();
</script>
<?php } ?>
</body>
</html>

