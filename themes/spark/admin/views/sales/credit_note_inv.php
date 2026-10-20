<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var isTobacco =0,currRate = 1,count = 1,  sl_edit = false,an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
        product_tax = 0, invoice_tax = 0, product_discount = 0, order_discount = 0, total_discount = 0, total = 0, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        tax_rates = <?php echo json_encode($tax_rates); ?>,base_units = <?php echo json_encode($base_units); ?>;

    //var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    //var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {

							<?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>

        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('slcredit_id')) {
                localStorage.removeItem('slcredit_id');
            }
            if (localStorage.getItem('stax_method_type')) {
                localStorage.removeItem('stax_method_type');
            }
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }

			if (localStorage.getItem('pcash_account_1')) {
                localStorage.removeItem('pcash_account_1');
            }
			if (localStorage.getItem('pcc_account_1')) {
                localStorage.removeItem('pcc_account_1');
            }

			if (localStorage.getItem('pcheque_account_1')) {
                localStorage.removeItem('pcheque_account_1');
            }

			if (localStorage.getItem('ptransfer_account_1')) {
                localStorage.removeItem('ptransfer_account_1');
            }


            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('bank_transfer_no_1')) {
                localStorage.removeItem('bank_transfer_no_1');
            }
            if (localStorage.getItem('payment_note_1')) {
                localStorage.removeItem('payment_note_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }
        <?php if ($credit_id) {
    ?>
        // localStorage.setItem('sldate', '<?= $this->sma->hrld($credit->date) ?>');

        localStorage.setItem('sgrpId', '<?= $this->site->getWarehouseByID($credit->warehouse_id)->grpId ?>');
        localStorage.setItem('scoId', '<?= $this->site->getWarehouseByID($credit->warehouse_id)->coId ?>');
        localStorage.setItem('sbrId', '<?= $this->site->getWarehouseByID($credit->warehouse_id)->brId ?>');
        localStorage.setItem('slcustomer', '<?= $credit->customer_id ?>');
        localStorage.setItem('slbiller', '<?= $credit->biller_id ?>');
        localStorage.setItem('slwarehouse', '<?= $credit->warehouse_id ?>');
        localStorage.setItem('slnote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($credit->note)); ?>');
        localStorage.setItem('sldiscount', '<?= $credit->order_discount_id ?>');
        localStorage.setItem('sltax2', '<?= $credit->order_tax_id ?>');
        localStorage.setItem('slshipping', '<?= $credit->shipping ?>');
        localStorage.setItem('slcurrency', '<?= $credit->currency_code ?>');
        //localStorage.setItem('slitems', JSON.stringify(<?= $credit_items; ?>));
        //localStorage.setItem('slitems', JSON.stringify({}));
        localStorage.setItem('slcredit_id', '<?= $credit_id ?>');

        <?php
        if($credit->product_tax>0){
            ?>
            localStorage.setItem('stax_method_type', '2');
            <?php
        }else{
            ?>
            localStorage.setItem('stax_method_type', '1');
            <?php
        }

} ?>


        <?php if ($this->input->get('customer')) {
        ?>
        if (!localStorage.getItem('slitems')) {
            localStorage.setItem('slcustomer', <?=$this->input->get('customer'); ?>);
        }
        <?php
    } ?>
        <?php
		//if ($Owner || $Admin) {
        ?>
        if (!localStorage.getItem('sldate')) {
            $("#sldate").datetimepicker({
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
        $(document).on('change', '#sldate', function (e) {
            localStorage.setItem('sldate', $(this).val());
        });
        if (sldate = localStorage.getItem('sldate')) {
            $('#sldate').val(sldate);
        }
        <?php
    //}
	?>
        $(document).on('change', '#slbiller', function (e) {
            localStorage.setItem('slbiller', $(this).val());
        });
        if (slbiller = localStorage.getItem('slbiller')) {
            $('#slbiller').val(slbiller);
        }
        if (!localStorage.getItem('slref')) {
            localStorage.setItem('slref', '<?=$slnumber?>');
        }
        if (!localStorage.getItem('sltax2')) {
            localStorage.setItem('sltax2', <?=$Settings->default_tax_rate2;?>);
        }
        ItemnTotals();
        $('.bootbox').on('hidden.bs.modal', function (e) {
            $('#add_item').focus();
        });
        $("#add_item").autocomplete({
            source: function (request, response) {

                if (!$('#slwarehouse').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_warehouse');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }

                if (!$('#slcustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_customer');?>');
                    //bootbox.alert('<?=lang('Taxes_are_not_uniform_Please_change_the_tax_system_type');?>');
                    $('#add_item').focus();
                    return false;
                }

                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('sales/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#slwarehouse").val(),
                        customer_id: $("#slcustomer").val()
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
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
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
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var wh = $("#slwarehouse").val();
                    var qtyDis=0;
                    try{
                        qtyDis=slitems[ui.item.row.id].row.qty;
                    }catch(err){}
                    qtyDis++;

                    if(qtyDis>1){

                    $.ajax({
                        type: "get",
                        url: "<?=admin_url('sales/getProductPromoRDiscountWithQty');?>",
                        data: {product_id: ui.item.row.id, warehouse_id: wh, item_quantity: qtyDis},
                        dataType: "json",
                        cache: false,
                        async : false,
                        success: function (data) {
                            if (data) {
                                //ui.item.row.discount=data.discount;
                                slitems[ui.item.row.id].row.discount=data.discount;

                                //data.free = true;
                                //data.parent = ui.item.row.id;

                                //add_invoice_item(data);
                            }
                            $("#add_item").removeClass('ui-autocomplete-loading');
                        }
                    }).done(function () {
                        $('#modal-loading').hide();
                    });
                    //console.log(slitems[ui.item.row.id]);
                    }

                    var row = add_invoice_item(ui.item);
                     var qty=1;
                    try{
                        qty=slitems[ui.item.row.id].row.qty;
                    }catch(err){}

					$.ajax({
                        type: "get",
                        url: "<?=admin_url('sales/getProductPromo');?>",
                        data: {product_id: ui.item.row.id, warehouse_id: wh, item_quantity: qty},
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                data.free = true;
                                data.parent = ui.item.row.id;

                                add_invoice_item(data);
                            }
                            $("#add_item").removeClass('ui-autocomplete-loading');
                        }
                    }).done(function () {
                        $('#modal-loading').hide();
                    });

                    $.ajax({
                        type: "get",
                        url: "<?=admin_url('sales/getProductPromo');?>",
                        data: {product_id: ui.item.row.id, warehouse_id: wh, item_quantity: qty},
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                data.free = true;
                                data.parent = ui.item.row.id;

                                add_invoice_item(data);
                            }
                            $("#add_item").removeClass('ui-autocomplete-loading');
                        }
                    }).done(function () {
                        $('#modal-loading').hide();
                    });

                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
        $(document).on('change', '#gift_card_no', function () {
            var cn = $(this).val() ? $(this).val() : '';
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#gift_card_no').parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('incorrect_gift_card')?>');
                        } else if (data.customer_id !== null && data.customer_id !== $('#slcustomer').val()) {
                            $('#gift_card_no').parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('gift_card_not_for_customer')?>');

                        } else {
                            $('#gc_details').html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + ' - Balance: ' + data.balance + '</small>');
                            $('#gift_card_no').parent('.form-group').removeClass('has-error');
                        }
                    }
                });
            }
        });



    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Credit_note'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('sales/credit_note_inv', $attrib);
                if ($credit_id) {
                    echo form_hidden('credit_id', $credit_id);
                }

                ?>
                <div class="row">
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                    </div>
                                        <?php }
                                        } ?>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'sldate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'readonly class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                                </div>
                            </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'slref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" id="slref"'); ?>
                            </div>
                        </div>
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id') || $this->session->userdata('isSupervisor')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('biller', 'slbiller'); ?>
                                    <?php
                                    $bl[''] = lang('select') . ' ' . lang('biller');
                                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {}else{
                    foreach ($billers as $biller) {
                        $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                    }
                }
                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="slbiller" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                } else {
                    $biller_input = [
                        'type'  => 'hidden',
                        'name'  => 'biller',
                        'id'    => 'slbiller',
                        'value' => $this->session->userdata('biller_id'),
                    ];

                    echo form_input($biller_input);
                } ?>

					<?php if ($Settings->IsAutomotiveServices) {
                    ?>
					    <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Exit_date', 'Exit_date'); ?>
                                    <?php echo form_input('exit_date', (isset($credit_id) ? $this->sma->hrld($credit->exit_date) : ''), 'class="form-control input-tip datetime" id="sldate" '); ?>
                                </div>
                            </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Plate_Number', 'Plate_Number'); ?>
                                <?php echo form_input('plate_no', (isset($credit_id) ? $credit->plate_no : $plate_no), 'class="form-control input-tip" id="plate_no"'); ?>
                            </div>
                        </div>
						<?php
						} ?>


                        <?php if ($Settings->IsRental) {
                    ?>
					    <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Rent_start_date', 'Rent_start_date'); ?>
                                    <?php echo form_input('rent_start_date', (isset($credit_id->rent_start_date) ? $this->sma->hrsd($credit->rent_start_date) : ''), 'readonly class="form-control input-tip date" id="rent_start_date" '); ?>
                                </div>
                            </div>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Rent_end_date', 'Rent_end_date'); ?>
                                    <?php echo form_input('rent_end_date', (isset($credit_id->rent_end_date) ? $this->sma->hrsd($credit->rent_end_date) : ''), 'readonly class="form-control input-tip date" id="rent_end_date" '); ?>
                                </div>
                            </div>

						<?php
						} ?>


                        	<?php if ($Settings->IsConstruction) {
                    ?>
					    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Contract_Number', 'Contract_Number'); ?>
                                <?php echo form_input('manual_payment', (isset($_POST['manual_payment']) ? $_POST['manual_payment'] : ''), 'class="form-control input-tip" id="manual_payment"'); ?>
                            </div>
                        </div>
					    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('PO_Number', 'PO_Number'); ?>
                                <?php echo form_input('po_no', (isset($_POST['po_no']) ? $_POST['po_no'] : ''), 'class="form-control input-tip" id="po_no"'); ?>
                            </div>
                        </div>



						<?php
						} ?>
                     <?php if ($Settings->isLogistic) {
                        ?>
					    <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Final_bayan_date', 'Final_bayan_date'); ?>
                                    <?php echo form_input('final_bayan_date', (isset($_POST['final_bayan_date']) ? $_POST['final_bayan_date'] : $final_bayan_date), 'class="form-control input-tip datetime" id="final_bayan_date" '); ?>
                                </div>
                            </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Bill_lading_no', 'Bill_lading_no'); ?>
                                <?php echo form_input('bill_lading_no', (isset($_POST['bill_lading_no']) ? $_POST['bill_lading_no'] : $bill_lading_no), 'class="form-control input-tip" id="bill_lading_no"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Bayan_no', 'Bayan_no'); ?>
                                <?php echo form_input('bayan_no', (isset($_POST['bayan_no']) ? $_POST['bayan_no'] : $bayan_no), 'class="form-control input-tip" id="bayan_no"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Declaration_no', 'Declaration_no'); ?>
                                <?php echo form_input('declaration_no', (isset($_POST['declaration_no']) ? $_POST['declaration_no'] : $declaration_no), 'class="form-control input-tip" id="declaration_no"'); ?>
                            </div>
                        </div>
						<?php
						} ?>

                        <?php if ($Settings->isShipping) {
                        ?>
					        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Delivery_note_number', 'Delivery_note_number'); ?>
                                    <?php echo form_input('delivery_note_number', (isset($_POST['delivery_note_number']) ? $_POST['delivery_note_number'] : $delivery_note_number), 'class="form-control input-tip" id="delivery_note_number" '); ?>
                                </div>
                            </div>

						<?php
						} ?>

                        <?php if ($Settings->invoice_view==11) {
                        ?>
					        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Invoice_type', 'inv_type'); ?>
                                    <?php
                                    $opt_inv = [ 1 => lang('credit'), 2 => lang('Cash')];
                                    echo form_dropdown('inv_type', $opt_inv, '', 'class="form-control tip" required="required" id="inv_type" style="width:100%;"');
                                    ?>
                                </div>
                            </div>

						<?php
						} ?>

                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang('warehouse', 'slwarehouse'); ?>
                                                <?php
                                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                            {}else{

                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                }
                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ($Settings->default_warehouse ? $Settings->default_warehouse : '')), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" ');

                    ?>
                                            </div>
                                        </div>
                                    <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'slwarehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <?= lang('customer', 'slcustomer'); ?>
                                            <div class="input-group">
                                                <?php
                                                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'id="slcustomer" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                                    <a href="#" id="toogle-customer-read-attr" class="external">
                                                        <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                                    <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php if ($Owner || $Admin || $GP['customers-add']) {
                                                    ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                    <a href="<?= admin_url('customers/add'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
									<div class="col-md-4">
									<div class="form-group">
										<?= lang('tax_method_type', 'tax_method_type'); ?>
										<?php
										$type = ['' => '', '1' => lang('tax_invoices_type'), '2' => lang('tax_products_type')];
										echo form_dropdown('tax_method_type', $type, '', 'id="tax_method_type" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('tax_method_type') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>

									<div class="col-md-4">
									<div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
                                         $curr[''] = lang('select') . ' ' . lang('currency');
                                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {}else{
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
                                    }
											echo form_dropdown('currency', $curr, $Settings->default_currency, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>

                                    <div class="col-md-4">
									<div class="form-group">
										<?= lang('Salesman', 'Salesman'); ?>
										<?php
                                         $salM[''] = lang('select') . ' ' . lang('Salesman');

										 foreach ($allSalesMan as $salesman) {
										$salM[$salesman->id] = $salesman->company;
										}

											echo form_dropdown('salesman', $salM, '', 'id="slsalesman" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Salesman') . '"  style="width:100%;" '); ?>
									</div>
									</div>

                                </div>
                            </div>

                        </div>

                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang('add_product_to_order') . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) {
                                                    ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addnItemManually" class="tip" title="<?= lang('add_product_manually') ?>">
                                                <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                        <?php
                                                } if ($Owner || $Admin || $GP['sales-add_gift_card']) {
                                                    ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;display:none;">
                                            <a href="#" id="sellGiftCard" class="tip" title="<?= lang('sell_gift_card') ?>">
                                               <i class="fa fa-2x fa-credit-card addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                        <?php
                                                } ?>

                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addnItemPromo" class="tip" title="<?= lang('promos') ?>">
                                               <i class="fa fa-2x fa-bullhorn addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('order_items'); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="slTable" class="table items table-striped  table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="<?= ($Settings->IsCurtains || $Settings->IsEquipmentRental) ? 'col-md-4' : 'col-md-5' ?>"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                            <?php
                                            //if ($Settings->product_serial) {
                                                echo '<th class="col-md-1">' . lang('unit_price') . '</th>';
                                            //}
                                            ?>
                                            <th class="col-md-1"><?= lang('net_unit_price'); ?></th>

                                            <?php
                                            if ($Settings->IsCurtains || $Settings->IsEquipmentRental) {
                                                echo '<th class="col-md-1">' . ( $Settings->IsCurtains ? lang('Area') : lang('No._days')) . '</th>';
                                            }
                                            ?>

                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <?php
                                            if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                                                echo '<th class="col-md-1" id="div_th_discount2">' . lang('discount') . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1" id="div_th_tax2">' . lang('product_tax') . '</th>';
                                            }
                                            ?>
                                            <th>
                                                <?= lang('subtotal'); ?>
                                             <!--   (<span class="currency"><?= $default_currency->code ?></span>) -->
                                            </th>
                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
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
						<div class="row">

                        <?php if ($Settings->tax2) {
                                                ?>
                            <div class="col-md-4"  id="div_invoice_tax">
                                <div class="form-group">
                                    <?= lang('order_tax', 'sltax2'); ?>
                                    <?php
                                    $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . lang('select') . ' ' . lang('order_tax') . '" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                                            } ?>

                        <?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) {
                                                ?>
                            <div class="col-md-4"  id="div_invoice_discount">
                                <div class="form-group">
                                    <?= lang('order_discount', 'sldiscount'); ?>
                                    <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sldiscount"'); ?>
                                </div>
                            </div>
                        <?php
                                            } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('shipping', 'slshipping'); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="slshipping"'); ?>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('document', 'document') ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('sale_status', 'slsale_status'); ?>
                                <?php $sst = ['completed' => lang('completed')];
                                echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('payment_term', 'slpayment_term'); ?>
                                <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); ?>

                            </div>
                        </div>
                        <?php if ($Owner || $Admin || $GP['sales-payments']) {
                                    ?>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('payment_status', 'slpayment_status'); ?>
                                <?php $pst = ['pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid')];
                                    echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="slpayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'pending');
                                }
                        ?>
                        <?php if ($Settings->IsInsuranceBusiness) {
                    ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Advance_payment_discount', 'Advance_payment_discount').' %'; ?>
                                <?php echo form_input('advance_payment_discount', (isset($_POST['advance_payment_discount']) ? $_POST['advance_payment_discount'] : ''), 'class="form-control input-tip" id="advance_payment_discount"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Insurance_business', 'Insurance_business').' ( '.lang('An_amount_or_percentage').' ) '; ?>
                                <?php echo form_input('insurance_business', (isset($_POST['insurance_business']) ? $_POST['insurance_business'] : ''), 'class="form-control input-tip" id="insurance_business"'); ?>
                            </div>
                        </div>
                        <?php
                        }
                    ?>


                        <div class="clearfix"></div>
						</div>


                        <div id="CostCenters" style="display: none;">
						<?php
						if($Settings->accounting){
						?>

                            <div class="col-md-12">
                            	<div class="row">
									<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
										<button id="addCostCenter" class="btn btn-primary btn-block" type="button"><?= lang('Add_cost_center') ?></button>
									</div>
								</div>
							</div>
						<?php
						}
						?>
						</div>






					   <div id="payments" style="display: none;">
                            <div class="col-md-12">
                                <div class="well well-sm well_1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php
							                    if($Settings->isDonation){
                                            ?>
                                            <div class="col-md-3">
                                            <?php
							                    }else{
                                            ?>
                                            <div class="col-md-4">
                                            <?php
							                    }
                                            ?>

                                                <div class="form-group">
                                                    <?= lang('payment_reference_no', 'payment_reference_no'); ?>
                                                    <?= form_input('payment_reference_no', (isset($_POST['payment_reference_no']) ? $_POST['payment_reference_no'] : $payment_ref), 'class="form-control tip" id="payment_reference_no"'); ?>
                                                </div>
                                            </div>
                                            <?php
							                    if($Settings->isDonation){
                                            ?>
                                            <div class="col-md-3">
                                            <?php
							                    }else{
                                            ?>
                                            <div class="col-md-4">
                                            <?php
							                    }
                                            ?>
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
                                            <?php
							                    if($Settings->isDonation){
                                            ?>
                                            <div class="col-sm-3">
                                            <?php
							                    }else{
                                            ?>
                                            <div class="col-sm-4" style="display:none;">
                                            <?php
							                    }
                                            ?>

                                                <div class="payment">
                                                    <div class="form-group ngc">
                                                        <?= lang('Donation', 'donation_1'); ?>
                                                        <input name="donation" type="text" id="donation_1"
                                                               class="pa form-control kb-pad amount"/>
                                                    </div>

                                                </div>
                                            </div>


                                            <?php
							                    if($Settings->isDonation){
                                            ?>
                                            <div class="col-md-3">
                                            <?php
							                    }else{
                                            ?>
                                            <div class="col-md-4">
                                            <?php
							                    }
                                            ?>
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
                                                    <?php
                $pts= [];

				foreach ($paymentType as $pt) {

                     $pts[$pt->code] = $pt->name;
                 }

				echo form_dropdown('pcc_type', $pts, '', 'class="form-control tip" id="pcc_type_1" data-placeholder="' . lang('select') . ' ' . lang('card_type') . '"  style="width:100%;"');
                if(empty($pts)){
				?>

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
                                                        <?php
                                                            }
                                                            ?>
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
                                        <input type="hidden" name="donation[]" id="donation_val_<?=$i?>" value=""/>
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

                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('Credit_Notice_Notes', 'slnote'); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="slnote" style="margin-top: 10px; height: 100px;" required="required"'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('staff_note', 'slinnote'); ?>
                                        <?php echo form_textarea('staff_note', (isset($_POST['staff_note']) ? $_POST['staff_note'] : ''), 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>


                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_sale', lang('submit'), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table  table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) {
                            ?>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                            <?php
                        }?>
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
 <?php
										if ($Settings->isOpen_invoice==1) {
											?>

                        <div class="form-group">
                        <label for="ptax_method" class="col-sm-4 control-label"><?= lang('tax_method') ?></label>
                        <div class="col-sm-8">
                            <div id="ptax_method-div"></div>
                            </div>
                            </div>
                        <?php
										}
											?>
                    <?php if ($Settings->product_serial) {
                            ?>
                        <div class="form-group">
                            <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pserial">
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
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                            ?>
                        <div class="form-group" id="div_product_discount">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php
                        }

						?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pprice" <?= ($Owner || $Admin || $this->session->userdata('edit_price')) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                            ?>
                        <div class="form-group">
                            <label for="psubt"
                                   class="col-sm-4 control-label"><?= lang('subtotal') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="psubt" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('adjust_subtotal') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="padiscount" placeholder="<?= lang('nearest_subtotal'); ?>">
                            </div>
                        </div>
                    <?php
                        } ?>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<!--
<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) {
                            ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                            <div class="col-sm-8">
                                <?php
                                $tr[''] = '';
                            foreach ($tax_rates as $tax) {
                                $tr[$tax->id] = $tax->name;
                            }
                            echo form_dropdown('mtax', $tr, '', 'id="mtax" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                        </div>
                    <?php
                        } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="munit" class="col-sm-4 control-label"><?= lang('unit') ?> *</label>

                        <div class="col-sm-8">
                            <?php
                            $uts[''] = '';
                            foreach ($units as $unit) {
                                $uts[$unit->id] = $unit->name;
                            }
                            echo form_dropdown('munit', $uts, '', 'id="munit" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                                ?>
                        <div class="form-group">
                            <label for="mdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount">
                            </div>
                        </div>
                    <?php
                            } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
-->
<div class="modal" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?= lang('card_no', 'gccard_no'); ?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#"
                                                                                                           id="genNo"><i
                                    class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname"/>

                <div class="form-group">
                    <?= lang('value', 'gcvalue'); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?= lang('price', 'gcprice'); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?= lang('customer', 'gccustomer'); ?>
                    <?php echo form_input('gccustomer', '', 'class="form-control" id="gccustomer"'); ?>
                </div>
                <div class="form-group">
                    <?= lang('expiry_date', 'gcexpiry'); ?>
                    <?php echo form_input('gcexpiry', $this->sma->hrsd(date('Y-m-d', strtotime('+2 year'))), 'class="form-control date" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="nmModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
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
                                <?= lang('subcategory', 'msubcategory') ?>
                                <div class="controls" id="subcat_data"> <?php
                                    echo form_input('subcategory', (isset($_POST['subcategory']) ? $_POST['subcategory'] : ''), 'class="form-control" id="msubcategory"  placeholder="' . lang('select_category_to_load') . '"');
                                    ?>
                                </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addnewItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="npModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('promos') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="pError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="pError"></span>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">



						<div class="form-group">
                                <?= lang('Promos_list', 'promo_id') ?> *
                                <?php
                                $ps[''] = '';
                                foreach ($promosSales as $promosSale) {
                                    $ps[$promosSale->id] = $promosSale->promo_name;
                                }
                                echo form_dropdown('promo_id', $ps, '', 'id="promo_id" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>

                    </div>
                    <div class="col-md-12 col-sm-12" style="display:none;">

                        <div class="form-group">
                            <?= lang('quantity', 'prquantity') ?> *
                            <input type="text" class="form-control" id="prquantity" value="1">
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addnewItemPromo"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {


		var pi = 'amount_1', di = 'donation_1', pa = 2;//,KB = <?=$pos_settings->keyboard?>;


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
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');

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
                    di = 'donation_' + pa;

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
        $(document).on('change', '#donation_<?=$i?>', function (e) {
            $('#donation_val_<?=$i?>').val($(this).val());
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


        $('#gccustomer').select2({
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
        $('#genNo').click(function () {
            var no = generateCardNo();
            $(this).parent().parent('.input-group').children('input').val(no);
            return false;
        });

		 $(document).on('click', '#addnItemManually', function (e) {
	if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val() && $('#tax_method_type').val()) {
                $('#slcustomer').select2('readonly', true);
                $('#slwarehouse').select2('readonly', true);
                $('#tax_method_type').select2('readonly', true);
                $('#grpId').select2('readonly', true);
                $('#coId').select2('readonly', true);
                $('#brId').select2('readonly', true);

	 } else {
        if(!$('#slwarehouse').val()){
                bootbox.alert(lang.Select_warehouse);
            }else if(!$('#slcustomer').val()){
                bootbox.alert(lang.Select_customer);
            }else{
                bootbox.alert(lang.Select_tax_method_type);
            }
                item = null;
                return false;
            }
        }
        $('#nmModal').appendTo("body").modal('show');
        return false;
    });


    $(document).on('click', '#addnItemPromo', function (e) {
	if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val() && $('#tax_method_type').val()) {
                $('#slcustomer').select2('readonly', true);
                $('#slwarehouse').select2('readonly', true);
                $('#tax_method_type').select2('readonly', true);
                $('#grpId').select2('readonly', true);
                $('#coId').select2('readonly', true);
                $('#brId').select2('readonly', true);

	 } else {
        if(!$('#slwarehouse').val()){
                bootbox.alert(lang.Select_warehouse);
            }else if(!$('#slcustomer').val()){
                bootbox.alert(lang.Select_customer);
            }else{
                bootbox.alert(lang.Select_tax_method_type);
            }
                item = null;
                return false;
            }
        }
        $('#npModal').appendTo("body").modal('show');
        return false;
    });


$(document).on('click', '#addnOpenItemManually', function (e) {

			try{
	if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val() && $('#tax_method_type').val()) {
                $('#slcustomer').select2('readonly', true);
                $('#slwarehouse').select2('readonly', true);
                $('#tax_method_type').select2('readonly', true);
                $('#grpId').select2('readonly', true);
                $('#coId').select2('readonly', true);
                $('#brId').select2('readonly', true);
	 } else {
        if($('#slwarehouse').val()){
                bootbox.alert(lang.Select_warehouse);
            }else if($('#slcustomer').val()){
                bootbox.alert(lang.Select_customer);
            }else{
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
        //if (typeof slitems === "undefined") {
        //    var slitems = {};
        //}
slitems[mid] = {
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
                            area: 1,
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


                    localStorage.setItem('slitems', JSON.stringify(slitems));
                    loadItems(1);
		//add_invoice_item(data)
        //$('#nmModal').appendTo("body").modal('show');
        //return false;
			}catch(err){alert(err)}
    });







        $("#msubcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#mcategory').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#msubcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#msubcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#msubcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) {
    ?>
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#msubcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: scdata
                    });
                }
            }
        });
        <?php
} ?>





		$(document).on('click', '#addnewItemManually', function (e) {

			var pr_type="<?= ($this->Settings->isOpen_invoice==0) ? 'standard' : 'service'; ?>";
			pr_type='standard';
			//alert(pr_type);




			$('#mcost').val('0');


			if (!$('#mcode').val()) {
                $('#mError').text('<?= lang('product_code_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?= lang('product_name_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?= lang('product_category_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?= lang('product_unit_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?= lang('product_cost_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?= lang('product_price_is_required') ?>');
                $('#mError-con').show();
                return false;
            }



            var msg, row = null, product = {
                type: pr_type,
                type2: 'primary',
                warehouse: $("#slwarehouse").val(),
                quantity: '',
                code: $('#mcode').val(),
                name: $('#mname').val(),
                tax_rate: $('#mtax').val(),
                tax_method: $('#mtax_method').val(),
                category_id: $('#mcategory').val(),
                subcategory_id: $('#msubcategory').val(),
                unit: $('#munit').val(),
                sale_unit: $('#munit').val(),
                purchase_unit: $('#munit').val(),
                cost: '',
                price: $('#mprice').val(),
				expiry: '0000-00-00',
				second_name: $('#mname2').val()


            };

            $.ajax({
                type: "get", async: false,
                url: site.base_url + "products/addAjaxProductFromSales_new",
                data: {token: "<?= $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {

                    if (data.msg == 'success') {
						$('#mcode').val('');
						$('#mname').val('');
						$('#mcategory').val('').trigger('change.select2');
						$('#munit').val('').trigger('change.select2');
						$('#mprice').val('');
						$('#mname2').val('');


                        row = add_invoice_item(data.result);
						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#nmModal').modal('hide');
                //audio_success.play();
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

        });



        $(document).on('click', '#addnewItemPromo', function (e) {


			if (!$('#promo_id').val()) {
                $('#pError').text('<?= lang('Promo_is_required') ?>');
                $('#pError-con').show();
                return false;
            }
            if (!$('#prquantity').val() || $('#prquantity').val()=="0" ) {
                $('#pError').text('<?= lang('Quantity_is_required') ?>');
                $('#pError-con').show();
                return false;
            }






            var msg, row = null;





            $.ajax({
                type: "get", async: false,
                url: site.base_url + "sales/addAjaxPromoFromSales_new",
                data: {token: "<?= $csrf; ?>", warehouse: $("#slwarehouse").val(),promo_id: $('#promo_id').val(),prquantity: $('#prquantity').val()},
                dataType: "json",
                success: function (data) {

                    if (data.msg == 'success') {

						$('#promo_id').val('').trigger('change.select2');

                        //console.log(data.result);

                        $.each(data.result, function(){
                            var item = this;
                            //console.log(item);
                            row = add_invoice_item(item);
                        });

                        //return false;
                        //row = add_invoice_item(data.result);
						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#npModal').modal('hide');
                //audio_success.play();
            } else {
                $('#pError').text(msg);
                $('#pError-con').show();
            }
            return false;

        });
        if (stax_method_type = localStorage.getItem('stax_method_type')) {
            //alert(localStorage.getItem('stax_method_type'))
                	$('#tax_method_type').val( stax_method_type).trigger('change');

                }
        $('.panel-warning').hide();


    });
</script>
