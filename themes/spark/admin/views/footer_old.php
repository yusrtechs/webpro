<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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

    <p style="text-align:center;">&copy; <?= date('Y') . ' ' . $Settings->site_name; ?> (<a href="<?= base_url('documentation.pdf'); ?>" target="_blank">v<?= $Settings->version; ?></a>
        ) <?php if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    echo ' - Page rendered in <strong>{elapsed_time}</strong> seconds';
} ?></p>
</footer>
<?= '</div>'; ?>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code); ?>
<script type="text/javascript">
var dt_lang = <?=$dt_lang?>, dp_lang = <?=$dp_lang?>, site = <?=json_encode(['url' => base_url(), 'base_url' => admin_url(), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats])?>;
//var lang = {produce: '<?=lang('produce');?>', confirmed: '<?=lang('confirmed');?>', done: '<?=lang('done');?>', Paid: '<?=lang('Paid');?>', Free_of_charge: '<?=lang('Free_of_charge');?>', paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', purchase_pending: '<?=lang('purchase_pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>', required_invalid: '<?=lang('required_invalid');?>'};
var lang = {exit: '<?=lang('exit');?>',Purchasing_price_is_required: '<?=lang('Purchasing_price_is_required');?>',Selling_price_is_required: '<?=lang('Selling_price_is_required');?>',Pricing_code_is_required: '<?=lang('Pricing_code_is_required');?>',Conversion_factor_is_required: '<?=lang('Conversion_factor_is_required');?>',Pricing_unit_is_required: '<?=lang('Pricing_unit_is_required');?>',Select_supplier: '<?=lang('Select_supplier');?>',taxes_are_not_uniform_Please_change_the_tax_system_type: '<?=lang('taxes_are_not_uniform_Please_change_the_tax_system_type');?>',Select_customer: '<?=lang('Select_customer');?>',Select_warehouse: '<?=lang('Select_warehouse');?>',Select_tax_method_type: '<?=lang('Select_tax_method_type');?>',Select_currency: '<?=lang('Select_currency');?>',Select_product_tax: '<?=lang('Select_product_tax');?>',Select_order_tax: '<?=lang('Select_order_tax');?>',Select_biller: '<?=lang('Select_biller');?>',select_unit_first: '<?=lang('select_unit_first');?>',Select_unit: '<?=lang('Select_unit');?>',select_category_to_load: '<?=lang('select_category_to_load');?>',no_subcategory: '<?=lang('no_subcategory');?>',select_subcategory: '<?=lang('select_subcategory');?>',Select_user: '<?=lang('Select_user');?>',Select_main_company: '<?=lang('Select_main_company');?>',Select_main_branch: '<?=lang('Select_main_branch');?>',Select_Showroom: '<?=lang('Select_Showroom');?>',Select_category: '<?=lang('Select_category');?>',Select_brand: '<?=lang('Select_brand');?>',inclusive: '<?=lang('inclusive');?>',exclusive: '<?=lang('exclusive');?>',No_Sold: '<?=lang('No_Sold');?>',canceled: '<?=lang('canceled');?>', available: '<?=lang('available');?>', sold: '<?=lang('sold');?>', produce: '<?=lang('produce');?>', confirmed: '<?=lang('confirmed');?>', done: '<?=lang('done');?>', Paid: '<?=lang('Paid');?>', Free_of_charge: '<?=lang('Free_of_charge');?>', paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', purchase_pending: '<?=lang('purchase_pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', transfer_ordered: '<?=lang('transfer_ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>', required_invalid: '<?=lang('required_invalid');?>',The_owner_of_all_vehicles_must_be_the_same: '<?=lang('The_owner_of_all_vehicles_must_be_the_same');?>'};

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
<?= ($m == 'sales' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/sales.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'edit_discount')) ? '<script type="text/javascript" src="' . $assets . 'js/sales_discount.js?' . time() . '"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carssales.js"></script>' : ''; ?>
<?= ($m == 'returns' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_return' || $v == 'edit_return')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_return' || $v == 'edit_return')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'quotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js"></script>' : ''; ?>
<?= ($m == 'carsquotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carsquotes.js"></script>' : ''; ?>
<?= ($m == 'carsoffers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/carsoffers.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_quote' || $v == 'edit_quote')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add_deliver' || $v == 'edit_deliver')) ? '<script type="text/javascript" src="' . $assets . 'js/delivers.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_quote' || $v == 'edit_quote')) ? '<script type="text/javascript" src="' . $assets . 'js/carsquotes.js"></script>' : ''; ?>
<?= ($m == 'carssales' && ($v == 'add_offer' || $v == 'edit_offer')) ? '<script type="text/javascript" src="' . $assets . 'js/carsoffers.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'accounts' && ($v == 'add_opening_balance' || $v == 'edit_opening_balance')) ? '<script type="text/javascript" src="' . $assets . 'js/opening_balance.js"></script>' : ''; ?>

<?= ($m == 'products' && ($v == 'add_adjustment_count')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments_count.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment_store')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments_store.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'destruction_inventory' || $v == 'edit_destruction')) ? '<script type="text/javascript" src="' . $assets . 'js/destruction_inventory.js"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'destruction_inventory' || $v == 'edit_destruction')) ? '<script type="text/javascript" src="' . $assets . 'js/destruction_inventory.js"></script>' : ''; ?>

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

		if('<?=$m?>'=='shop_settings'){
		$('#mm_auth').css("display", "block");
		//$('#mm_shop_settings').css("display", "block");
		}



		if('<?=$m?>'=='system_settings' || '<?=$m?>'=='billers')
		$('#mm_auth').css("display", "block");
		if('<?=$m?>'=='pos' && '<?=$v?>'=='sales'){
		$('#mm_sales').css("display", "block");
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

            //print_r($check->created_at);
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
					if(($date1 < $date2)){

                    //noti extends
                   // $notif='You have exceeded the subscription period '.$interval->days;
                   // $notif=$lang['Notify_after_end_supscription'] . ' : ' . $interval->days;
                    $notif= lang('Dear_customer,_you_have_exceeded_the_subscription_period_for') .' ' . $interval->days .' '. lang('Day') .' , '. lang('Since_your_subscription_has_expired_at') . ' ' .$this->sma->hrsd($effectiveEndDate) .' , '. lang('To_renew_your_subscription_and_ensure_the_continuation_of_the_service_please_contact_Arkan_Pro_for_Information_Technology_Company_on_the_phone_numbers_0534277747_-_0560531961_or_email_info_@_companypro.com');
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
                     $notif=lang('Dear_customer,_the_date_of_your_subscription_with_us_remains') .' ' . $interval->days .' '. lang('Day') .' '. lang('Your_subscription_will_expire_on') . ' ' .$this->sma->hrsd($effectiveEndDate) .' '. lang('To_renew_your_subscription_and_ensure_the_continuation_of_the_service_please_contact_Arkan_Pro_for_Information_Technology_Company_on_the_phone_number_0534277747_or_email_info_@_companypro.com');
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



});


	 </script>
     <!--
<script type="text/javascript" src="<?= base_url('assets/custom/custom.js') ?>"></script>
  <script src="<?=$assets?>/js/spectrum.js"></script>
-->
</body>
</html>

