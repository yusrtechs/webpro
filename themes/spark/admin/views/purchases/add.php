<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .icheckbox_square-blue{
        background-position: -24px 0;
    }
</style>
<script type="text/javascript">

<?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('pogrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('pocoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('pogrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('pocoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('pobrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>

    <?php

	if ($this->session->userdata('remove_pols')) {
	?>

    if (localStorage.getItem('ptax_method_type')) {
        localStorage.removeItem('ptax_method_type');

    }
    if (localStorage.getItem('poitems')) {
        localStorage.removeItem('poitems');
    }
    if (localStorage.getItem('podiscount')) {
        localStorage.removeItem('podiscount');
    }
    if (localStorage.getItem('potax2')) {
        localStorage.removeItem('potax2');
    }
    if (localStorage.getItem('poshipping')) {
        localStorage.removeItem('poshipping');
    }
    if (localStorage.getItem('poref')) {
        localStorage.removeItem('poref');
    }
    if (localStorage.getItem('powarehouse')) {
        localStorage.removeItem('powarehouse');
    }
    if (localStorage.getItem('pogrpId')) {
        localStorage.removeItem('pogrpId');
    }
    if (localStorage.getItem('pocoId')) {
        localStorage.removeItem('pocoId');
    }
    if (localStorage.getItem('pobrId')) {
        localStorage.removeItem('pobrId');
    }

    if (localStorage.getItem('ponote')) {
        localStorage.removeItem('ponote');
    }
    if (localStorage.getItem('posupplier')) {
        localStorage.removeItem('posupplier');
    }
    if (localStorage.getItem('pocurrency')) {
        localStorage.removeItem('pocurrency');
    }
    if (localStorage.getItem('poextras')) {
        localStorage.removeItem('poextras');
    }
    if (localStorage.getItem('podate')) {
        localStorage.removeItem('podate');
    }
    if (localStorage.getItem('postatus')) {
        localStorage.removeItem('postatus');
    }
    if (localStorage.getItem('popayment_term')) {
        localStorage.removeItem('popayment_term');
    }
    if (localStorage.getItem('popayment_status')) {
                localStorage.removeItem('popayment_status');
    }
    <?php $this->sma->unset_data('remove_pols');
} ?>
    <?php if ($quote_id) {
    ?>
    localStorage.setItem('pogrpId', '<?= $quote->grpId ?>');
    localStorage.setItem('pocoId', '<?= $quote->coId ?>');
    localStorage.setItem('pobrId', '<?= $quote->brId ?>');
    localStorage.setItem('powarehouse', '<?= $quote->warehouse_id ?>');
    localStorage.setItem('ponote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($quote->note)); ?>');
    localStorage.setItem('podiscount', '<?= $quote->order_discount_id ?>');
    localStorage.setItem('potax2', '<?= $quote->order_tax_id ?>');
    localStorage.setItem('poshipping', '<?= $quote->shipping ?>');
    localStorage.setItem('pocurrency', '<?= $quote->currency_code ?>');
    localStorage.setItem('postatus', 'ordered');
    <?php if ($quote->supplier_id) {
        ?>
        localStorage.setItem('posupplier', '<?= $quote->supplier_id ?>');
    <?php
    } ?>
    localStorage.setItem('poitems', JSON.stringify(<?= $quote_items; ?>));
    <?php
} ?>

    var currRate = 1,count = 1, an = 1, po_edit = false, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {},
        base_units = <?php echo json_encode($base_units); ?>,
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');



        function baseunits(ele) {
        $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'products/suggestionsV4',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10,
                };
            },
            results: function(data, page) {

                if (data != null) {
                    return { results: data };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            },
        },
    });
    }


    $(document).ready(function () {




        <?php if ($this->input->get('supplier')) {
        ?>
        if (!localStorage.getItem('poitems')) {
            localStorage.setItem('posupplier', <?=$this->input->get('supplier'); ?>);
        }
        <?php
    } ?>

        if (!localStorage.getItem('podate')) {
            $("#podate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }

        if (!localStorage.getItem('potax2')) {
            localStorage.setItem('potax2', <?=$Settings->default_tax_rate2;?>);
            setTimeout(function(){ $('#extras').iCheck('check'); }, 1000);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            // source: '<?= admin_url('purchases/suggestions'); ?>',
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('purchases/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#posupplier").val(),
                        warehouse_id: $("#powarehouse").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found_pur') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();

					bootbox.confirm('<?= lang('no_match_found_pur') ?> </br> <?= lang('Do_you_want_add_this_product') ?>', function(result){
						$('#add_item').focus();
						if(result)
                    $( "#addManually" ).click();
                   });
                    //bootbox.alert('<?= lang('no_match_found_pur') ?>', function () {
                    //    $('#add_item').focus();
                    //});
                    $(this).removeClass('ui-autocomplete-loading');
                    //$(this).val('');
					var mcode=$(this).val();
					$("#mcode").val(mcode);
					$(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_purchase_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found_pur') ?>');
                }
            }
        });

        $(document).on('click', '#genPurcCode2', function (e) {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
        });

        $(document).on('click', '#genPurcCode3', function (e) {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
        });

        $(document).on('click', '#genPurcCode4', function (e) {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
        });

        $(document).on('click', '#genPurcCode5', function (e) {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
        });


        $(document).on('click', '#addnOpenItemManually', function (e) {

try{
if (count == 1) {
poitems = {};
if ( $('#posupplier').val() && $('#tax_method_type').val()) {
    $('#posupplier').select2('readonly', true);
    $('#tax_method_type').select2('readonly', true);
    $('#grpId').select2('readonly', true);
    $('#coId').select2('readonly', true);
    $('#pobrId').select2('readonly', true);
} else {
    if($('#posupplier').val()){
                bootbox.alert(lang.Select_supplier);
            }else if($('#tax_method_type').val()){
                bootbox.alert(lang.Select_tax_method_type);
            }
    item = null;
    return false;
}
}

var mid = new Date().getTime(),
gccode = '',
gcname = '',
gcvalue = 0,
gccustomer = 'gccustomer',
gcexpiry = '',
gcprice = parseFloat(0);
if (gccode == '' || gcvalue == '' || gcprice == '' || gcvalue == 0 || gcprice == 0) {
//$('#gcerror').text('Please fill the required fields');
//$('.gcerror-con').show();
//return false;
}

var gc_data = new Array();
gc_data[0] = gccode;
gc_data[1] = gcvalue;
gc_data[2] = gccustomer;
gc_data[3] = gcexpiry;
//if (typeof poitems === "undefined") {
//    var poitems = {};
//}
poitems[mid] = {
            id: mid,
            item_id: mid,
            label: gcname + ' (' + gccode + ')',
            row: {
                id: mid,
                code: gccode,
                name: gcname,
                base_unit: 1,
                unit: 1,
                tax_method: 0,
                quantity: 1,
                new_entry: 1,
                base_quantity: 1,
                price: gcprice,
                real_unit_price: gcprice,
                tax_rate: 0,
                qty: 1,
                type: 'manual',
                discount: '0',
                expiry: '',
                quantity_balance: '',
                serial: '',

            },
            tax_rate: false,
            code: gccode,
            qty: 1,
            options: false,
            units: false,
        };


        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems(1);
//add_invoice_item(data)
//$('#nmModal').appendTo("body").modal('show');
//return false;
}catch(err){alert(err)}
});


        $(document).on('click', '#addItemManually', function (e) {
			$('#mcost').val('0');





			if (!$('#mcode').val()) {
                $('#mError').text('<?= lang('product_code_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);

                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?= lang('product_name_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);

                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?= lang('product_category_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?= lang('product_unit_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?= lang('product_cost_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?= lang('product_price_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                return false;
            }

            if( $('#price_groups').is(':checked') ){
                if (!$('#product_unit_id').val()) {
                    $('#mError').text('<?= lang('Pricing_unit_is_required') ?>');
                    $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                    return false;
                }

                if (!$('#factor').val()) {
                    $('#mError').text('<?= lang('Conversion_factor_is_required') ?>');
                    $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                    return false;
                }

                if (!$('#pricing_code').val()) {
                    $('#mError').text('<?= lang('Pricing_code_is_required') ?>');
                    $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                    return false;
                }

                if (!$('#selling_price').val()) {
                    $('#mError').text('<?= lang('Selling_price_is_required') ?>');
                    $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                    return false;
                }

                if (!$('#purchasing_price').val()) {
                    $('#mError').text('<?= lang('Purchasing_price_is_required') ?>');
                    $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                    return false;
                }

            }








            var price_groups_check=0, msg, row = null, product = {
                type: 'standard',
                type2: 'primary',
				quantity: '',
                code: $('#mcode').val(),
                name: $('#mname').val(),
                tax_rate: $('#mtax').val(),
                tax_method: $('#mtax_method').val(),
                category_id: $('#mcategory').val(),
                unit: $('#munit').val(),
                sale_unit: $('#munit').val(),
                purchase_unit: $('#munit').val(),
                cost: '',
                price: $('#mprice').val(),
				expiry: '0000-00-00',
				second_name: $('#mname2').val(),
                brId: localStorage.getItem('pobrId')


            }, price_groups = {
                unit: $('#munit').val(),
                product_unit_id: $('#product_unit_id').val(),
                factor: $('#factor').val(),
                pricing_code: $('#pricing_code').val(),
                selling_price: $('#selling_price').val(),
                purchasing_price: $('#purchasing_price').val(),
                price_group_id: $('#price_group_id').val()


            };

            if( $('#price_groups').is(':checked') ){
                price_groups_check=1;
            }


            $.ajax({
                type: "get", async: false,
                url: site.base_url + "products/addByAjax",
                data: {token: "<?= $csrf; ?>", product: product, price_groups: price_groups, price_groups_check: price_groups_check},
                dataType: "json",
                success: function (data) {

                    if (data.msg == 'success') {
                        //console.log(data);
						$('#mcode').val('');
						$('#mname').val('');
						$('#mcategory').val('').trigger('change.select2');
						$('#munit').val('').trigger('change.select2');
						$('#mprice').val('');
						$('#mname2').val('');
                        $('#product_unit_id').val('').trigger('change.select2');
                        $('#factor').val('');
                        $('#pricing_code').val('');
                        $('#selling_price').val('');
                        $('#purchasing_price').val('');
                        $('#price_groups').iCheck('uncheck');

                        row = add_purchase_item(data.result);


						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#mModal').modal('hide');
                //audio_success.play();
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
            }
            return false;

        });


        var su = 2;
        $('#addPriceGroups').click(function () {
            if (su <= 5) {
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-lg-12"><div class="col-sm-4"><div class="form-group"><input type="hidden" name="product_unit_id2_' + su + '", class="form-control" id="product_unit_id2_' + su + '" placeholder="<?= lang('select') . ' ' . lang('Pricing_unit') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-sm-4"><div class="form-group"><input  type="number" min="1" step="1" name="factor2_' + su + '" class="form-control tip" id="factor2_' + su + '"  placeholder="<?= lang('Conversion_factor') ?>" /></div></div><div class="col-sm-4"><div class="form-group"><div class="input-group"><input type="text" name="pricing_code2_' + su + '" class="form-control tip" id="pricing_code2_' + su + '" placeholder="<?= lang('Pricing_code') ?>" /><div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genPurcCode' + su + '" ><i class="fa fa-cogs"></i></a></div></div></div></div><div class="col-sm-4"><div class="form-group"><input  type="number" min=".01" step=".01" name="selling_price2_' + su + '" class="form-control tip" id="selling_price2_' + su + '"  placeholder="<?= lang('selling_price') ?>" /></div></div><div class="col-sm-4"><div class="form-group"><input  type="number" min=".01" step=".01" name="purchasing_price2_' + su + '" class="form-control tip" id="purchasing_price2_' + su + '"  placeholder="<?= lang('purchasing_price') ?>" /></div></div><div class="col-sm-4"><div class="form-group"><input type="checkbox" class="checkbox" name="add_checkbox2_' + su + '" id="add_checkbox2_' + su + '" value="1"/><label for="add_checkbox2_' + su + '" class="padding05"><?= lang('Add_to_the_invoice') ?></label></div></div></div></div>';
                $('#ex-products').append(html);
                var sup = $('#product_unit_id2_' + su);
                baseunits(sup);
                $('#su_count').val(su);
                $('#add_checkbox2_' + su)
        .not('.skip')
        .iCheck({
            checkboxClass: 'icheckbox_square-blue ',
            increaseArea: '20%',
        });
        $('#add_checkbox2_' + su).iCheck('check');
                $('#factor2_' + su + '').focus();
                su++;
            } else {
                alert('<?= lang('max_reached') ?>');
                return false;
            }
        });



    });


	$(document).on('click', '#add_pruchase', function(e) {

		var formData_product_id = JSON.stringify($(".rid").serializeArray());
		$('#jsonArr_product_id').val(formData_product_id);

		var formData_product = JSON.stringify($(".rcode").serializeArray());
		$('#jsonArr_product').val(formData_product);

		var formData_product_name = JSON.stringify($(".rname").serializeArray());
		$('#jsonArr_product_name').val(formData_product_name);

		var formData_product_option = JSON.stringify($(".roption").serializeArray());
		$('#jsonArr_product_option').val(formData_product_option);

		var formData_part_no = JSON.stringify($(".rpart_no").serializeArray());
		$('#jsonArr_part_no').val(formData_part_no);


		var formData_expiry = JSON.stringify($(".rexpiry").serializeArray());
		$('#jsonArr_expiry').val(formData_expiry);

		var formData_net_cost = JSON.stringify($(".rcost").serializeArray());
		$('#jsonArr_net_cost').val(formData_net_cost);


		var formData_unit_cost = JSON.stringify($(".rucost").serializeArray());
		$('#jsonArr_unit_cost').val(formData_unit_cost);

		var formData_real_unit_cost = JSON.stringify($(".realucost").serializeArray());
		$('#jsonArr_real_unit_cost').val(formData_real_unit_cost);

		var formData_quantity_balance = JSON.stringify($(".rbqty").serializeArray());
		$('#jsonArr_quantity_balance').val(formData_quantity_balance);

		var formData_quantity = JSON.stringify($(".rquantity").serializeArray());
		$('#jsonArr_quantity').val(formData_quantity);

		var formData_product_unit = JSON.stringify($(".runit").serializeArray());
		$('#jsonArr_product_unit').val(formData_product_unit);

		var formData_product_base_quantity = JSON.stringify($(".rbase_quantity").serializeArray());
		$('#jsonArr_product_base_quantity').val(formData_product_base_quantity);

		var formData_product_discount = JSON.stringify($(".rdiscount").serializeArray());
		$('#jsonArr_product_discount').val(formData_product_discount);

		var formData_product_tax = JSON.stringify($(".rproduct_tax").serializeArray());
		$('#jsonArr_product_tax').val(formData_product_tax);





        });

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('purchases/add', $attrib)
                ?>


                <div class="row">

				<input type="hidden" name="jsonArr_product_id" id="jsonArr_product_id"  value='1'/>
				<input type="hidden" name="jsonArr_product" id="jsonArr_product"  value='1'/>
				<input type="hidden" name="jsonArr_product_name" id="jsonArr_product_name"  value='1'/>
				<input type="hidden" name="jsonArr_product_option" id="jsonArr_product_option"  value='1'/>
				<input type="hidden" name="jsonArr_part_no" id="jsonArr_part_no"  value='1'/>
				<input type="hidden" name="jsonArr_expiry" id="jsonArr_expiry"  value='1'/>
				<input type="hidden" name="jsonArr_net_cost" id="jsonArr_net_cost"  value='1'/>
				<input type="hidden" name="jsonArr_unit_cost" id="jsonArr_unit_cost"  value='1'/>
				<input type="hidden" name="jsonArr_real_unit_cost" id="jsonArr_real_unit_cost"  value='1'/>
				<input type="hidden" name="jsonArr_quantity_balance" id="jsonArr_quantity_balance"  value='1'/>
				<input type="hidden" name="jsonArr_quantity" id="jsonArr_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_product_unit" id="jsonArr_product_unit"  value='1'/>
				<input type="hidden" name="jsonArr_product_base_quantity" id="jsonArr_product_base_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_product_discount" id="jsonArr_product_discount"  value='1'/>
				<input type="hidden" name="jsonArr_product_tax" id="jsonArr_product_tax"  value='1'/>



                    <div class="col-lg-12">
                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                         <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $cat[''] = lang('select') . ' ' . lang('Main_group');
                                        if ($Owner || $Admin){
                                        foreach ($mainGroups as $mainGroup) {
                                            $cat[$mainGroup->id] = $mainGroup->name;
                                        }

                                    }else{
                                        $cat[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                        </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if ($Owner || $Admin){
                                        if(($this->Settings->enable_grp==1)){}else{
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }
                                }else{
                                    $co[$this->session->userdata('coId')] = $this->site->getMainCompaniesByID($this->session->userdata('coId'))->name;
                                }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                        </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {

                                    if($Owner || $Admin){
                                            ?>
                                            <div class="col-md-4">
                                            <?php
                                        }else{
                                            ?>
                                            <div class="col-md-4" style="display:none;">
                                            <?php
                                        }
                                        ?>
                                        <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if ($Owner || $Admin){
                                            /*if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }*/
                                    }else{
                                        $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="pobrId"   style="width:100%"')
                                            ?>
                                        </div>
                                    </div>
                                        <?php }
                                        } ?>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'podate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'readonly class="form-control input-tip datetime" id="podate" required="required"'); ?>
                                </div>
                            </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'poref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>
                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('warehouse', 'powarehouse'); ?>
                                    <?php
                                     $wh[''] = lang('select') . ' ' . lang('warehouse');
                                     if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                     {}else{
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                }
                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); ?>
                                </div>
                            </div>
                        <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'powarehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('status', 'postatus'); ?>
                                <?php
                                $post = ['received' => lang('Purchase_invoice_received'),'received1' => lang('Purchase_invoice_pendding'), 'pending' => lang('purchase_pending'), 'ordered' => lang('ordered')];
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('status') . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('document', 'document') ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= lang('supplier', 'posupplier'); ?>
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) {
                                    ?><div class="input-group"><?php
                                } ?>
                                                <input type="hidden" name="supplier" value="" id="posupplier"
                                                       class="form-control" style="width:100%;"
                                                       placeholder="<?= lang('select') . ' ' . lang('supplier') ?>">
                                                <input type="hidden" name="supplier_id" value="" id="supplier_id"
                                                       class="form-control">
                                                <?php if ($Owner || $Admin || $GP['suppliers-index']) {
                                    ?>
                                                    <div class="input-group-addon no-print" style="border-left: 0;">
                                                        <a href="#" id="view-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-2x fa-user" id="addIcon"></i>
                                                        </a>
                                                    </div>
                                                <?php
                                } ?>
                                                <?php if ($Owner || $Admin || $GP['suppliers-add']) {
                                    ?>
                                                <div class="input-group-addon no-print">
                                                    <a href="<?= admin_url('suppliers/add'); ?>" id="add-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                                    </a>
                                                </div>
                                            <?php
                                } ?>
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) {
                                    ?></div><?php
                                } ?>
                                        </div>
                                    </div>

								<!--<div class="col-md-4">
									<div class="form-group">
									<?= lang('tax_invoices_type', 'tax_invoices_type'); ?>


									<?php echo form_radio('tax_method_type', '0',true,'id="tax_invoices_type"'); ?>

									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">

								<?= lang('tax_products_type', 'tax_products_type'); ?>


									<?php echo form_radio('tax_method_type', '1',false,'id="tax_products_type"'); ?>
									</div>

								</div>-->

								<div class="col-md-3">
									<div class="form-group">
										<?= lang('tax_method_type', 'tax_method_type'); ?>
										<?php
										$type = ['' => '', '1' => lang('tax_invoices_type'), '2' => lang('tax_products_type')];
										echo form_dropdown('tax_method_type', $type, '', 'id="tax_method_type" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('tax_method_type') . '" required="required" style="width:100%;" '); ?>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, $Settings->default_currency, 'id="pocurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>




                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line('add_product_to_order') . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) {
                                    ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;display:none">
                                            <a href="<?= admin_url('products/add') ?>" id="addManually"><i
                                                    class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i></a></div>
                                        <?php
                                } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('order_items'); ?></label>

                                <div class="controls table-controls">
                                    <table id="poTable"
                                           class="table items table-striped  table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                            <?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-2">' . $this->lang->line('expiry_date') . '</th>';
                                            }
                                            ?>
                                            <th class="col-md-1"><?= lang('net_unit_cost'); ?></th>
                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <?php
                                            if ($Settings->product_discount) {
                                                echo '<th class="col-md-1" id="div_th_discount">' . $this->lang->line('discount') . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1" id="div_th_tax">' . $this->lang->line('product_tax') . '</th>';
                                            }
                                            ?>
                                            <th><?= lang('subtotal'); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
										<?php
										if ($Settings->isOpen_invoice==1) {
											?>
										<tr id="tfoot" class="tfoot active">
										<th> <button id="addnOpenItemManually" class="tip" style="border-width:0px;"><i class="fa fa-plus"></i></button> </th>
										</tr>
										<?php
										}
										?>

										</tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

						<div id="CostCenters" style="display: none;">
						<?php
						if($Settings->accounting){
						?>

                            <div class="col-md-12">
                            	<div class="row">
									<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
										<button id="addCostCenter" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
									</div>
								</div>
							</div>
						<?php
						}
						?>
						</div>


                        <div class="col-md-12" id="more_options">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                <?php if ($Settings->tax1) {
                                                ?>
                                    <div class="col-md-4"  id="div_invoice_tax">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'potax2') ?>
                                            <?php
                                            $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('order_tax', $tr, '', 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
                                        </div>
                                    </div>
                                <?php
                                            } ?>

								<div class="col-md-4" id="div_customs_fees">
                                    <div class="form-group">
                                        <?= lang('customs_fees', 'customs_fees'); ?>
                                        <?php echo form_input('customs_fees', '', 'class="form-control input-tip" id="customs_fees"'); ?>
                                    </div>
                                </div>

								<div class="col-md-4" id="div_Other_fees">
                                    <div class="form-group">
                                        <?= lang('Other_fees', 'Other_fees'); ?>
                                        <?php echo form_input('Other_fees', '', 'class="form-control input-tip" id="Other_fees"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4"  id="div_invoice_discount">
                                    <div class="form-group">
                                        <?= lang('discount_label', 'podiscount'); ?>
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('shipping', 'poshipping'); ?>
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('payment_term', 'popayment_term'); ?>
                                        <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="popayment_term"'); ?>
                                    </div>
                                </div>
								<?php
								if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){

								?>

								<div class="col-md-4" id="receiving_date_div">
                                    <div class="form-group">
                                       <?= lang('Receiving_date','Receiving_date'); ?>
                                     <?php echo form_input('receiving_date', '', 'class="form-control input-tip datetime" id="receiving_date"'); ?>
                                    </div>
                                </div>

								<?php
								}
								?>

                        <?php if ($Owner || $Admin) {
                                    ?>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('payment_status', 'popayment_status'); ?>
                                <?php $pst = ['pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid')];
                                   echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="popayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'pending');
                                }
                        ?>


                            </div>
                            <div class="clearfix"></div>









                            <div id="payments" style="display: none;">
                            <div class="col-md-12">
                                <div class="well well-sm well_1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('payment_reference_no', 'payment_reference_no'); ?>
                                                    <?= form_input('payment_reference_no', (isset($_POST['payment_reference_no']) ? $_POST['payment_reference_no'] : $payment_ref), 'class="form-control tip" id="payment_reference_no"'); ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="payment">
                                                    <div class="form-group ngc">
                                                        <?= lang('amount', 'amount_1'); ?>
                                                        <input name="amount" type="text" id="amount_1"
                                                               class="pa form-control kb-pad amount"/>
                                                    </div>
                                                    <div class="form-group gc_1" style="display: none;">
                                                        <?= lang('gift_card_no', 'gift_card_no_1'); ?>
                                                        <input name="paying_gift_card_no" type="text" id="gift_card_no_1"
                                                               class="pa form-control kb-pad"/>

                                                        <div id="gc_details"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by">
														<?= $Settings->accounting ? $this->sma->paid_opts_accounts(false, true) : $this->sma->paid_opts(false, true); ?>
													</select>
                                                </div>
                                            </div>
											<?php
						if($Settings->accounting){
						?>
						<div class="col-md-4 pcash_1" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				$acc[''] = lang('select') . ' ' . lang('Account');

				foreach ($payment_types as $account) {
					if($account->payment_type=='cash')
                     $acc[$account->id] = $account->accountName;
                 }
				 $acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;

                echo form_dropdown('cash_account', $acc, '', 'class="form-control tip" id="pcash_account_1" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>

                                        </div>
                                        <div class="clearfix"></div>




                                        <div class="pcc_1" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="cc_no" type="text" id="pcc_no_1"
                                                               class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="cc_holer" type="text" id="pcc_holder_1"
                                                               class="form-control"
                                                               placeholder="<?= lang('cc_holder') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <select name="cc_type" id="pcc_type_1"
                                                                class="form-control pcc_type"
                                                                placeholder="<?= lang('card_type') ?>">
                                                            <option value="Mada"><?= lang('Mada'); ?></option>
                                                            <option value="Visa"><?= lang('Visa'); ?></option>
                                                            <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                                            <option value="Amex"><?= lang('Amex'); ?></option>
                                                            <option value="Discover"><?= lang('Discover'); ?></option>
                                                            <option value="STC Pay"><?= lang('STC_Pay'); ?></option>
                                                        </select>
                                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="cc_month" type="text" id="pcc_month_1"
                                                               class="form-control" placeholder="<?= lang('month') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="cc_year" type="text" id="pcc_year_1"
                                                               class="form-control" placeholder="<?= lang('year') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="cc_ccv" type="text" id="pcc_cvv2_1"
                                                               class="form-control" placeholder="<?= lang('cvv2') ?>"/>
                                                    </div>
                                                </div>

												<?php
							if($Settings->accounting){
						?>
						<div class="col-md-4" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				$acc[''] = lang('select') . ' ' . lang('Account');


				foreach ($payment_types as $account) {
					if($account->payment_type=='CC')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;

				echo form_dropdown('cc_account', $acc, '', 'class="form-control tip" id="pcc_account_1" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');

				?>
                            </div>
                        </div>
						<?php
						}
						?>
                                            </div>
                                        </div>
                                        <div class="pcheque_1" style="display:none;">
                                            <div class="col-md-8" >
											<div class="form-group"><?= lang('cheque_no', 'cheque_no_1'); ?>
                                                <input name="cheque_no" type="text" id="cheque_no_1"
                                                       class="form-control cheque_no"/>
                                            </div>
											</div>
											<?php
						if($Settings->accounting){
						?>
						<div class="col-md-4" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				$acc[''] = lang('select') . ' ' . lang('Account');

				foreach ($payment_types as $account) {
					if($account->payment_type=='Cheque')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('cheque_account', $acc, '', 'class="form-control tip" id="pcheque_account_1" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>
                                        </div>

										<div class="ptransfer_1" style="display:none;">
										<div class="row">


							<?php
						if($Settings->accounting){
						?>
						<div class="col-md-4" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				$acc[''] = lang('select') . ' ' . lang('Account');

				foreach ($payment_types as $account) {
					if($account->payment_type=='bank_transfer')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('transfer_account', $acc, '', 'class="form-control tip" id="ptransfer_account_1" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}else{
						?>
						<div class="col-md-8" >
						<div class="form-group"><?= lang('bank_details', 'bank_details'); ?>
                                                <input name="cheque_no" type="text" id="cheque_no_1"
                                                       class="form-control cheque_no"/>
                        </div>
						</div>

						<?php
						}
						?>
                        </div>
						</div>



                                        <div class="form-group">
                                            <?= lang('payment_note', 'payment_note_1'); ?>
                                            <textarea name="payment_note" id="payment_note_1"
                                                      class="pa form-control kb-text payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
						<div id="multi-payment"></div>
						<div id="payment-button"  style="display:none;">
						<button type="button" class="btn btn-primary col-md-12 addButton"><i
                                class="fa fa-plus"></i> <?=lang('add_more_payments')?></button>
                        <div style="clear:both; height:15px;"></div>
                        <div class="font16">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td width="50%"><?=lang("total_paying");?></td>
                                    <td width="50%" class="text-right"><span id="total_paying">0.00</span></td>
                                </tr>
                               </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>

                        </div>

						<div id="payment-con">
                                    <?php for ($i = 1; $i <= 5; $i++) {?>
                                        <input type="hidden" name="amount[]" id="amount_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="balance_amount[]" id="balance_amount_<?=$i?>" value=""/>
                                        <input type="hidden" name="paid_by[]" id="paid_by_val_<?=$i?>" value="cash"/>
                                        <input type="hidden" name="cc_no[]" id="cc_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="paying_gift_card_no[]" id="paying_gift_card_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_holder[]" id="cc_holder_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cheque_no[]" id="cheque_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="bank_transfer_no[]" id="bank_transfer_no_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_month[]" id="cc_month_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_year[]" id="cc_year_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_type[]" id="cc_type_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cash_account[]" id="cash_account_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_account[]" id="cc_account_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cheque_account[]" id="cheque_account_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="transfer_account[]" id="transfer_account_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_<?=$i?>" value=""/>
                                        <input type="hidden" name="payment_note[]" id="payment_note_val_<?=$i?>" value=""/>
                                    <?php }
                                    ?>
									<input type="hidden" name="rpaidby" id="rpaidby" value="cash" style="display: none;"/>

                        </div>








                        </div>
						<div class="col-md-12">
						 <div class="form-group">
                                <?= lang('note', 'ponote'); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>
						</div>

                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_pruchase', $this->lang->line('submit'), 'id="add_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table  table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>

							 <td id="div_tcustoms_fees"><?= lang('customs_fees') ?> <span class="totals_val pull-right" id="tcustoms_fees">0.00</span></td>
                             <td id="div_tOther_fees"><?= lang('Other_fees') ?> <span class="totals_val pull-right" id="tOther_fees">0.00</span></td>

							<?php if ($Settings->tax2) {
                                                ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php
                                            } ?>

                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <?php if ($Settings->tax1) {
                                                ?>
                        <div class="form-group" id="div_product_tax">
                            <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                            <div class="col-sm-8">
                                <?php
                                $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('ptax', $tr, '', 'id="ptax" class="form-control pos-input-tip" style="width:100%;"'); ?>
                            </div>
                        </div>
                    <?php
                                            } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_expiry) {
                                                ?>
                        <div class="form-group">
                            <label for="pexpiry" class="col-sm-4 control-label"><?= lang('product_expiry') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pexpiry">
                            </div>
                        </div>
                    <?php
                                            } ?>
                    <div class="form-group">
                        <label for="punit" class="col-sm-4 control-label"><?= lang('product_unit') ?></label>
                        <div class="col-sm-8">
                            <div id="punits-div"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>
                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount) {
                                                ?>
                        <div class="form-group" id="div_product_discount">
                            <label for="pdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php
                                            } ?>
                    <div class="form-group">
                        <label for="pcost" class="col-sm-4 control-label"><?= lang('unit_cost') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pcost">
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_cost'); ?></th>
                            <th style="width:25%;"><span id="net_cost"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>


                    <div class="alert alert-danger" id="mError2-con" style="display: none;">

                        <span id="mError2"></span>
                    </div>


                    <div class="form-group col-sm-12">
                    <?= form_checkbox('price_groups2', '1','', 'class="form-control tip" id="price_groups2"'); ?>
				    <?= lang('price_groups', 'price_groups'); ?>

                    <div class=" well-sm" id="div_price_groups2" style="display:none;">
                    <input type="hidden" name="su_count" id="su_count" value="1" />
				        <div class="row">
                            <div class="col-lg-12">

					            <div class="col-sm-4">

                                    <div class="form-group">
                                        <?= lang('Pricing_unit','product_unit_id2') ?>
                                        <?php
                                        $pun[''] = '';
                                        foreach ($base_units as $unit) {
                                            $pun[$unit->id] = $unit->name;
                                        }
                                        echo form_dropdown('product_unit_id2', $pun, '', 'id="product_unit_id2" class="form-control input-tip select" style="width:100%;"'); ?>
                                    </div>
                                </div>

			                    <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('Conversion_factor', 'factor2') ?>
                                        <input type="text" class="form-control" id="factor2">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('Pricing_code', 'pricing_code2'); ?>
                                        <div class="input-group">
                                            <?php echo form_input('pricing_code2', '', 'class="form-control" id="pricing_code2"'); ?>
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <a href="#" id="genPurcCode">
                                                    <i class="fa fa-cogs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('selling_price', 'selling_price2') ?>
                                        <input type="text" class="form-control" id="selling_price2">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('purchasing_price', 'purchasing_price2') ?>
                                        <input type="text" class="form-control" id="purchasing_price2">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('price_group','price_group_id2') ?>
                                        <?php
                                        foreach ($price_groups as $price_group) {
                                            $cgs[$price_group->id] = $price_group->name;
                                        }
                                        echo form_dropdown('price_group_id2', $cgs, $Settings->price_group, 'id="price_group_id2" class="form-control input-tip select" style="width:100%;"'); ?>
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= form_checkbox('add_checkbox2', '1','1', 'class="form-control tip" id="add_checkbox2"'); ?>
				                        <?= lang('Add_to_the_invoice', 'Add_to_the_invoice'); ?>
                                    </div>

                                </div>



                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('price_groups', 'price_groups') ?>
                                        <button type="button" class="btn btn-primary btn-xs" id="addPriceGroups"><i class="fa fa-plus"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div id="ex-products"></div>
                    </div>
                </div>

                    <div class="panel panel-default">
                        <div class="panel-heading"><?= lang('calculate_unit_cost'); ?></div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label for="pcost" class="col-sm-4 control-label"><?= lang('subtotal') ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="psubtotal">
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="calculate_unit_price" class="tip" title="<?= lang('calculate_unit_cost'); ?>">
                                                <i class="fa fa-calculator"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="punit_cost" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_cost" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">

                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('product_code', 'mcode') ?> *
                            <input type="text" class="form-control" id="mcode">
                        </div>
                        <div class="form-group">
                            <?= lang('product_name', 'mname') ?> *
                            <input type="text" class="form-control" id="mname">
                        </div>
                        <div class="form-group">
                            <?= lang('category', 'mcategory') ?> *
                            <?php
                            $cat[''] = '';
                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
                            echo form_dropdown('category', $cat, '', 'class="form-control select" id="mcategory" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%"')
                            ?>
                        </div>
						<div class="form-group">
                                <?= lang('unit', 'munit') ?> *
                                <?php
                                $un[''] = '';
                                foreach ($base_units as $unit) {
                                    $un[$unit->id] = $unit->name;
                                }
                                echo form_dropdown('munit', $un, '', 'id="munit" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>

                    </div>
                    <div class="col-md-6 col-sm-6" >
                        <div class="form-group">
                             <?= lang('second_name', 'mname2'); ?>
                            <input type="text" class="form-control" id="mname2">
                        </div>
                        <div class="form-group" style="display:none;">
                            <?= lang('cost', 'mcost') ?> *
                            <input type="text" class="form-control" id="mcost">
                        </div>
                        <div class="form-group">
                            <?= lang('price', 'mprice') ?> *
                            <input type="text" class="form-control" id="mprice">
                        </div>

                        <?php if ($Settings->tax1) {
                                ?>
                            <div class="form-group">
                                <?= lang('product_tax', 'mtax') ?>
                                <?php
                                $tr[''] = '';
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, '', 'id="mtax" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                            <div class="form-group all">
                                <?= lang('tax_method', 'mtax_method') ?>
                                <?php
                                $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                                echo form_dropdown('tax_method', $tm, '', 'class="form-control select" id="mtax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
                            </div>
                        <?php
                            } ?>
                    </div>
                </div>
                <div class="form-group all">
                    <?= form_checkbox('price_groups', '1','', 'class="form-control tip" id="price_groups"'); ?>
				    <?= lang('price_groups', 'price_groups'); ?>

                    <div class="well well-sm" id="div_price_groups" style="display:none;">

				        <div class="row">
                            <div class="col-lg-12">

					            <div class="col-sm-4">

                                    <div class="form-group">
                                        <?= lang('Pricing_unit','product_unit_id') ?>
                                        <?php
                                        $pun[''] = '';
                                        foreach ($base_units as $unit) {
                                            $pun[$unit->id] = $unit->name;
                                        }
                                        echo form_dropdown('product_unit_id', $pun, '', 'id="product_unit_id" class="form-control input-tip select" style="width:100%;"'); ?>
                                    </div>
                                </div>

			                    <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('Conversion_factor', 'factor') ?>
                                        <input type="text" class="form-control" id="factor">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('Pricing_code', 'pricing_code'); ?>
                                        <div class="input-group">
                                            <?php echo form_input('pricing_code', '', 'class="form-control" id="pricing_code"'); ?>
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <a href="#" id="genPurcCode1">
                                                    <i class="fa fa-cogs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('selling_price', 'selling_price') ?>
                                        <input type="text" class="form-control" id="selling_price">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('purchasing_price', 'purchasing_price') ?>
                                        <input type="text" class="form-control" id="purchasing_price">
                                    </div>

                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('price_group','price_group_id') ?>
                                        <?php
                                        foreach ($price_groups as $price_group) {
                                            $cgs[$price_group->id] = $price_group->name;
                                        }
                                        echo form_dropdown('price_group_id', $cgs, $Settings->price_group, 'id="price_group_id" class="form-control input-tip select" style="width:100%;"'); ?>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        var pi = 'amount_1', pa = 2;//,KB = <?=$pos_settings->keyboard?>;



		var costCount  = 0;
			        $('#CostCenters').slideDown();

		$('button#addCostCenter').click(function(){
			var divOption    = '<div class="row" id="costRow'+costCount+'">';
			var selectOption = '<div class="col-5" style="padding-left:30px;padding-right:25px;"><select class="form-control"  name="costCenterId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-4" dir="ltr"><input class="form-control" placeholder="<?= lang('Amount') ?>" name="costCenterAmount[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCenters').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(inputOption);
			$('#costRow'+costCount).append(buttonOption);
			costCount += 1;

		});



		$(document).on('click', '.addButton', function () {
			try{
            if (pa <= 5) {
                $('#paid_by_1, #pcc_type_1,#pcash_account_1,#pcc_account_1,#pcheque_account_1,#ptransfer_account_1').select2('destroy');
                var phtml = $('#payments').html(),
                    update_html = phtml.replace(/_1/g, '_' + pa);

                pi = 'amount_' + pa;

                $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fa fa-2x">&times;</i></button>' + update_html);
                $('#paid_by_1, #pcc_type_1, #pcash_account_1, #pcc_account_1, #pcheque_account_1, #ptransfer_account_1, #paid_by_' + pa + ', #pcc_type_' + pa + ', #pcash_account_' + pa + ', #pcc_account_' + pa + ', #pcheque_account_' + pa + ', #ptransfer_account_' + pa).select2({minimumResultsForSearch: 7});

				read_card();

                pa++;

            } else {
                bootbox.alert('<?=lang('max_reached')?>');
                return false;
            }

            //$(document).css('overflow-y', 'scroll');
			}catch(err){alert(err)}
        });
        $(document).on('click', '.close-payment', function () {
            $(this).next().remove();
            $(this).remove();
            pa--;
        });

		       $(document).on('focus', '.amount', function () {
            pi = $(this).attr('id');
            calculateTotals();
        }).on('blur', '.amount', function () {
            calculateTotals();
        });

        function calculateTotals() {

            var total_paying = 0;
            var ia = $(".amount");
			$.each(ia, function (i) {
                var this_amount = formatDecimal($(this).val() ? $(this).val() : 0);
                total_paying += parseFloat(this_amount);
            });

            $('#total_paying').text(formatDecimal(total_paying));



        }



		<?php for ($i = 1; $i <= 5; $i++) {?>
        $(document).on('change', '#amount_<?=$i?>', function (e) {
            $('#amount_val_<?=$i?>').val($(this).val());
        });
        $(document).on('blur', '#amount_<?=$i?>', function (e) {
            $('#amount_val_<?=$i?>').val($(this).val());
        });
        $(document).on('select2-close', '#paid_by_<?=$i?>', function (e) {
            $('#paid_by_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_no_<?=$i?>', function (e) {
            $('#cc_no_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_holder_<?=$i?>', function (e) {
            $('#cc_holder_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#gift_card_no_<?=$i?>', function (e) {
            $('#paying_gift_card_no_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_month_<?=$i?>', function (e) {
            $('#cc_month_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_year_<?=$i?>', function (e) {
            $('#cc_year_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_type_<?=$i?>', function (e) {
            $('#cc_type_val_<?=$i?>').val($(this).val());
        });
         $(document).on('change', '#pcash_account_<?=$i?>', function (e) {
            $('#cash_account_val_<?=$i?>').val($(this).val());
        });
         $(document).on('change', '#pcc_account_<?=$i?>', function (e) {
            $('#cc_account_val_<?=$i?>').val($(this).val());
        });
         $(document).on('change', '#pcheque_account_<?=$i?>', function (e) {
            $('#cheque_account_val_<?=$i?>').val($(this).val());
        });
         $(document).on('change', '#ptransfer_account_<?=$i?>', function (e) {
            $('#transfer_account_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#pcc_cvv2_<?=$i?>', function (e) {
            $('#cc_cvv2_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#cheque_no_<?=$i?>', function (e) {
            $('#cheque_no_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#bank_transfer_no_<?=$i?>', function (e) {
            $('#bank_transfer_no_val_<?=$i?>').val($(this).val());
        });
        $(document).on('change', '#payment_note_<?=$i?>', function (e) {
            $('#payment_note_val_<?=$i?>').val($(this).val());
        });
        <?php }
        ?>


function read_card() {



	try{

    var typingTimer;

    $('.swipe').keyup(function(e) {

        //e.preventDefault();
        var self = $(this);

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function() {
            var payid = self.attr('id');

            var id = payid.substr(payid.length - 1);
            var v = self.val();
            var p = new SwipeParserObj(v);




            if (p.hasTrack1) {
                var CardType = null;
                var ccn1 = p.account.charAt(0);
                if (ccn1 == 4) CardType = 'Visa';
                else if (ccn1 == 5) CardType = 'MasterCard';
                else if (ccn1 == 3) CardType = 'Amex';
                else if (ccn1 == 6) CardType = 'Discover';
                else CardType = 'Visa';

                $('#pcc_no_' + id)
                    .val(p.account)
                    .change();
                $('#pcc_holder_' + id)
                    .val(p.account_name)
                    .change();
                $('#pcc_month_' + id)
                    .val(p.exp_month)
                    .change();
                $('#pcc_year_' + id)
                    .val(p.exp_year)
                    .change();
                $('#pcc_cvv2_' + id).val('');
                $('#pcc_type_' + id)
                    .val(CardType)
                    .change();
                self.val('');
                $('#pcc_cvv2_' + id).focus();
            } else {
                $('#pcc_no_' + id).val('');
                $('#pcc_holder_' + id).val('');
                $('#pcc_month_' + id).val('');
                $('#pcc_year_' + id).val('');
                $('#pcc_cvv2_' + id).val('');
                $('#pcc_type_' + id).val('');
            }
        }, 100);
    });

    $('.swipe').keydown(function(e) {
        clearTimeout(typingTimer);
    });
	}catch(err){alert(err)}
}



$('#genPurcCode').click(function() {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
    });

	$('#genPurcCode1').click(function() {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
    });



	});

    $(document).on('ifChecked', '#price_groups', function (event) {

            if (!$('#munit').val()) {
                $('#mError').text('<?= lang('product_unit_is_required') ?>');
                $('#mError-con').show();setTimeout(function () {$('#mError-con').hide();}, 3000);
                $('#price_groups').iCheck('uncheck');
                return false;
            }

            $('#div_price_groups').slideDown();
    });
    $(document).on('ifUnchecked', '#price_groups', function (event) {
            $('#div_price_groups').slideUp();
    });

    $(document).on('ifChecked', '#price_groups2', function (event) {



            $('#div_price_groups2').slideDown();
    });
    $(document).on('ifUnchecked', '#price_groups2', function (event) {
            $('#div_price_groups2').slideUp();
    });


</script>
<?php if ($Owner || $Admin) {}else{

     if(!$this->site->IsPermissions('purchases-ordered')){
                                                ?>
<script>
    $(document).ready(function() {
        $("#postatus option[value='ordered']").attr('disabled', 'disabled');

    });
</script>
<?php
    }else{
        ?>
<script>
    $(document).ready(function() {
        $('#postatus').val('ordered').trigger('change.select2');
        $('#CostCenters').slideUp();
        $("#more_options").css("display", "none");

    });
</script>
        <?php
    }
     if(!$this->site->IsPermissions('purchases-pending')){
                                                ?>
<script>
    $(document).ready(function() {
        $("#postatus option[value='pending']").attr('disabled', 'disabled');
    });
</script>
<?php
    }else{
        ?>
        <script>
    $(document).ready(function() {
        $('#postatus').val('pending').trigger('change.select2');
        $('#CostCenters').slideUp();
		$("#more_options").css("display", "block");
		$("#receiving_date_div").css("display", "none");
		$('#podiscount').attr('readonly', false);
        $('#poshipping').attr('readonly', false);
		$('#customs_fees').attr('readonly', false);
		$('#Other_fees').attr('readonly', false);

    });
</script>
        <?php

    }
    if(!$this->site->IsPermissions('purchases-received')){
                                                ?>
<script>
    $(document).ready(function() {
        $("#postatus option[value='received']").attr('disabled', 'disabled');
        $("#postatus option[value='received1']").attr('disabled', 'disabled');
    });
</script>
<?php
    }else{
        ?>
        <script>
    $(document).ready(function() {
        $('#postatus').val('received').trigger('change.select2');
        $('#CostCenters').slideDown();
        $("#more_options").css("display", "block");
		$("#receiving_date_div").css("display", "block");
        $('#podiscount').attr('readonly', false);
        $('#poshipping').attr('readonly', false);
        $('#customs_fees').attr('readonly', false);
		$('#Other_fees').attr('readonly', false);

    });
</script>
        <?php

    }
    } ?>