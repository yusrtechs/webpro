<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('#grpId').change(function () {
            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#coId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;

$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;
$('#warehouse')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('warehouse') ?></option>')
    .val('')
;

$('#coId').val('').trigger('change.select2');
$('#brId').val('').trigger('change.select2');
$('#biller').val('').trigger('change.select2');
$('#warehouse').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });


        $('#coId').change(function () {
            var v = $(this).val();
            var uopt = $("#brId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

                            $('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;

$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;

$('#warehouse')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('warehouse') ?></option>')
    .val('')
;


$('#brId').val('').trigger('change.select2');
$('#biller').val('').trigger('change.select2');
$('#warehouse').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });



        $('#brId').change(function () {
            var v = $(this).val();
            var uopt = $("#biller");
            var uopt1 = $("#warehouse");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBillersBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {



$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;





$('#biller').val('').trigger('change.select2');


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                });
            }


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {





$('#warehouse')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('warehouse') ?></option>')
    .val('')
;




$('#warehouse').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });

                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }
        });
    });
</script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_expense'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('purchases/add_expense', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">
          <div class="col-md-6">

            <?php if ($Owner || $Admin) {
            ?>

                <div class="form-group">
                    <?= lang('date', 'date'); ?>
                    <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'class="form-control datetime" id="date" required="required"'); ?>
                </div>
            <?php
        } ?>

            <div class="form-group">
                <?= lang('reference', 'reference'); ?>
                <?= form_input('reference', (isset($_POST['reference']) ? $_POST['reference'] : $exnumber), 'class="form-control tip" id="reference"'); ?>
            </div>


			<?php
				if($Settings->accounting){
			?>

			<div class="form-group">
                <?= lang('Account', 'Account'); ?>
                <?php
                $ct[''] = lang('select') . ' ' . lang('Account');
                if ($categories) {
                    foreach ($expenses_accounts as $category) {
                        $ct[$category->id] = $category->accountName;
                    }
                }
                ?>
                <?= form_dropdown('account_id', $ct, set_value('Account'), 'class="form-control tip" id="account_id"'); ?>
            </div>

            <div class="form-group" style="display:none;">
                <?= lang('Cost_center', 'Cost_center'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('Cost_center');
                foreach ($cost_centers as $cost_center) {
                    $wh[$cost_center->id] = $cost_center->costName;
                }
                echo form_dropdown('cost_id', $wh, (isset($_POST['cost_id']) ? $_POST['cost_id'] : ''), 'id="cost_id" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>

			<?php
				}else{
			?>

			<div class="form-group">
                <?= lang('category', 'category'); ?>
                <?php
                $ct[''] = lang('select') . ' ' . lang('category');
                if ($categories) {
                    foreach ($categories as $category) {
                        $ct[$category->id] = $category->name;
                    }
                }
                ?>
                <?= form_dropdown('category', $ct, set_value('category'), 'class="form-control tip" id="category"'); ?>
            </div>

            <div class="form-group">
                <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('warehouse');
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>


			<?php
				}
			?>

            <div class="form-group">
                <?= lang('attachment', 'attachment') ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
                       </div>



                       <div id="CostCenters" style="display: none;">
						<?php
						if($Settings->accounting)
                        {
						?>

                                <div class="form-group">
									<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;width:50%;" >
										<button id="addCostCenter" class="btn btn-primary btn-block" type="button"><?= lang('Add_cost_center') ?></button>
									</div>
                                </div>
						<?php
						}
						?>
                        </div>

            </div>



            <div class="col-md-6">

            <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
                            <div class="form-group">
                                <?= lang('Main_group', 'Main_group') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                ?>
                            </div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="form-group">
                                <?= lang('Main_company', 'Main_company') ?>
                                <?php
                                $co[''] = lang('select') . ' ' . lang('Main_company');
                                if(($this->Settings->enable_grp==1)){}else{
                                    foreach ($mainCompanies as $mainCompany) {
                                        $co[$mainCompany->id] = $mainCompany->name;
                                    }
                                }

                                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                ?>
                            </div>
                            <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php
                                $br[''] = lang('select') . ' ' . lang('Main_branch');
                                if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                    if ($Owner || $Admin){
                                        foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                }else{
                                    $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                }
                                }
                                echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                ?>
                            </div>
                           <?php
                        }} ?>

            <div class="form-group">
                <?= lang('amount', 'amount'); ?>
                <input name="amount" type="text" id="amount" value="" class="pa form-control kb-pad amount"
                       required="required"/>
            </div>



			<?php if ($Settings->tax1) {
                                                ?>
            <div class="form-group">
				<?= lang('order_tax', 'potax2') ?>
				<?php
				$tr[''] = '';
                $tr_default = !empty($tax_rates) ? $tax_rates[0]->id : '';
				foreach ($tax_rates as $tax) {
                    if ($tax->tax_default == 1) {
                        $tr_default = $tax->id;
                    }
				    $tr[$tax->id] = $tax->name;
				}
				echo form_dropdown('order_tax', $tr, $tr_default, 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
            </div>

            <div class="form-group all">
                                <?= lang('tax_method', 'mtax_method') ?>
                                <?php
                                $tm = [ '1' => lang('exclusive'),'0' => lang('inclusive')];
                                echo form_dropdown('tax_method', $tm, $Settings->default_tax_method, 'class="form-control select" id="mtax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
            </div>


			<?php
            } ?>
			<div class="form-group">
                <?= lang('tax', 'potax2'); ?>
                <input name="total_tax" type="text" id="total_tax" value="0" class="pa form-control kb-pad total_tax"
                       required="required" readonly/>
            </div>

			<div class="form-group">
                <?= lang('grand_total', 'potax2'); ?>
                <input name="grand_total" type="text" id="grand_total" value="0" class="pa form-control kb-pad grand_total"
                       required="required" readonly/>
            </div>




                       </div>
                       <?php if ($Owner || $Admin) {
                                    ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('payment_status', 'exppayment_status'); ?>
                                <?php $pst = ['pending' => lang('pending'), 'paid' => lang('paid')];
                                   echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="exppayment_status"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-6 exppaid_by">
                                <div class="form-group">
                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
                                        <?= $Settings->accounting ? $this->sma->paid_opts_accounts(false, true) : $this->sma->paid_opts(false, true); ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                                }
                        ?>


                       </div>

                       <div class="col-md-12 exppaid_by">
                        <div class="row">

                        <?php
						if($Settings->accounting){
						?>
						<div class="col-md-6 pcash_1" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='cash')
                     $acc[$account->id] = $account->accountName;
                 }
				  if(empty($acc))
				$acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;

                echo form_dropdown('cash_account', $acc, '', 'class="form-control tip" id="cash_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>


						<div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                               placeholder="<?= lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control"
                                               placeholder="<?= lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?= lang('card_type') ?>">
                                            <option value="Mada"><?= lang('Mada'); ?></option>
                                            <option value="Visa"><?= lang('Visa'); ?></option>
                                            <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                            <option value="Amex"><?= lang('Amex'); ?></option>
                                            <option value="Discover"><?= lang('Discover'); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder="<?= lang('year') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control"
                                               placeholder="<?= lang('cvv2') ?>"/>
                                    </div>
                                </div>
                            </div>
							<?php
						if($Settings->accounting){
						?>
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='CC')
                     $acc[$account->id] = $account->accountName;
                 }
                 if(empty($acc))
				$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;

				echo form_dropdown('cc_account', $acc, '', 'class="form-control tip" id="cc_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');

				?>
                            </div>
                        </div>
						<?php
						}
						?>
                        </div>
                        <div class="pcheque_1 col-md-6" style="display:none;">
                            <div class="form-group"><?= lang('cheque_no', 'cheque_no_1'); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
								<?php
						if($Settings->accounting){
						?>
						<div >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='Cheque')
                     $acc[$account->id] = $account->accountName;
                 }
                 if(empty($acc))
				$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('cheque_account', $acc, '', 'class="form-control tip" id="cheque_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>

                        </div>
						<div class="ptransfer_1 col-md-6" style="display:none;">

							<?php
						if($Settings->accounting){
						?>
						<div >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='bank_transfer')
                     $acc[$account->id] = $account->accountName;
                 }
                 if(empty($acc))
				$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('transfer_account', $acc, '', 'class="form-control tip" id="transfer_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}else{

						?>
						<div class="form-group"><?= lang('bank_details', 'bank_details'); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>

							<?php
						}
						?>

                        </div>

                    </div>
                    </div>
                    <div class="clearfix"></div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_expense', lang('add_expense'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {

       var accounting  = <?=$Settings->accounting?>;

       if(accounting){

        var costCount  = 0;
			        $('#CostCenters').slideDown();

		$('button#addCostCenter').click(function(){
			var divOption    = '<div class="row" id="costRow'+costCount+'">';
			var selectOption = '<div class="col-5" style="padding-left:30px;padding-right:25px;"><select class="form-control"  name="costCenterId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
            if($Settings->accounting){
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
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

}


        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
		if (localStorage.getItem('potax2')) {
                    localStorage.removeItem('potax2');
         }

		var invoice_tax = 0,
		total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;

		//if (site.settings.tax2 != 0) {
		$('#potax2,#mtax_method').change(function () {
			localStorage.setItem('potax2', $('#potax2').val());

			total = $('#amount').val();
			try{
			if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {

                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            if($('#mtax_method').val()==1)
                                invoice_tax = formatDecimal((((total) * this.rate) / 100), 4);
                            else
                                invoice_tax = formatDecimal((((total) * this.rate) / 115), 4);

                        }
                    }
                });
            }


			$('#total_tax').val(invoice_tax);
            if($('#mtax_method').val()==1)
			$('#grand_total').val(formatDecimal(((parseFloat(total) + parseFloat(invoice_tax))), 4));
            else
            $('#grand_total').val(formatDecimal(total, 4));

			}catch(err){alert(err);}

			return;
		});
		//}

		$('#amount').change(function () {
			localStorage.setItem('potax2', $('#potax2').val());

			total = $('#amount').val();
			try{
			if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            if($('#mtax_method').val()==1)
                                invoice_tax = formatDecimal((((total) * this.rate) / 100), 4);
                            else
                                invoice_tax = formatDecimal((((total) * this.rate) / 115), 4);
                        }
                    }
                });
            }
			$('#total_tax').val(invoice_tax);
            if($('#mtax_method').val()==1)
			$('#grand_total').val(formatDecimal(((parseFloat(total) + parseFloat(invoice_tax))), 4));
            else
            $('#grand_total').val(formatDecimal(total, 4));
			}catch(err){alert(err);}

			return;
		});


        $("#date").datetimepicker({
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

        $(".exppaid_by").hide();
        $('#exppayment_status').change(function () {
            if($('#exppayment_status').val()=="pending"){
                $(".exppaid_by").hide();
            }else{
                $(".exppaid_by").show();
            }
        });

        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
            localStorage.setItem('paid_by', p_val);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
				$('.ptransfer_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
				$('.ptransfer_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('#pcc_no_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.ptransfer_1').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else if (p_val == 'bank_transfer') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.pcheque_1').hide();
                $('.ptransfer_1').show();

            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.ptransfer_1').hide();
            }
        });
        $('#pcc_no_1').change(function (e) {
            var pcc_no = $(this).val();
            localStorage.setItem('pcc_no_1', pcc_no);
            var CardType = null;
            var ccn1 = pcc_no.charAt(0);
            if (ccn1 == 4)
                CardType = 'Visa';
            else if (ccn1 == 5)
                CardType = 'MasterCard';
            else if (ccn1 == 3)
                CardType = 'Amex';
            else if (ccn1 == 6)
                CardType = 'Discover';
            else
                CardType = 'Mada';

            $('#pcc_type_1').select2("val", CardType);
        });

    });
</script>
