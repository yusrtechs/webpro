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

$('#coId').val('').trigger('change.select2');
$('#brId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select_category_to_load') ?>",
                                //data: scdata


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


$('#brId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                //data: scdata


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
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_deposit') . ' (' . $company->name . ')'; ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('customers/add_deposit/' . $company->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-sm-12">
                    <?php if ($Owner || $Admin) {

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
                        }}
                        ?>


                    <div class="form-group">
                        <?php echo lang('date', 'date'); ?>
                        <div class="controls">
                            <?php echo form_input('date', set_value('date', date($dateFormats['php_ldate'])), 'class="form-control datetime" id="date" required="required"'); ?>
                        </div>
                    </div>
                    <?php
        } ?>

                    <div class="form-group">
                        <?php echo lang('amount', 'amount_1'); ?>
                        <div class="controls">
                            <?php echo form_input('amount', set_value('amount'), 'class="form-control" id="amount_1" required="required"'); ?>
                        </div>
                    </div>

                    <!--<div class="form-group">
                        <?php echo lang('paid_by', 'paid_by'); ?>
                        <div class="controls">
                            <?php echo form_input('paid_by', set_value('paid_by'), 'class="form-control" id="paid_by"'); ?>
                        </div>
                    </div>-->


                                <div class="form-group">
                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
                                       <?= $Settings->accounting ? $this->sma->paid_opts_accounts(false, true) : $this->sma->paid_opts(false, true); ?>
                                     </select>
                                </div>


								<?php
						if($Settings->accounting){
						?>
						<div class="row">
						<div class="col-md-6 pcash_1" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='cash')
                     $acc[$account->id] = $account->accountName;
                 }
				 $acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;

                echo form_dropdown('cash_account', $acc, '', 'class="form-control tip" id="cash_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
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
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder="<?= lang('year') ?>"/>
                                    </div>
                                </div>
                                <!--<div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control" placeholder="<?= lang('cvv2') ?>" />
                                    </div>
                                </div>-->
                            </div>
							<?php
							if($Settings->accounting){
						?>
						<div class="row">

						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='CC')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;

				echo form_dropdown('cc_account', $acc, '', 'class="form-control tip" id="cc_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');

				?>
                            </div>
                        </div>
						</div>
						<?php
						}
						?>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><?= lang('cheque_no', 'cheque_no_1'); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
							<?php
						if($Settings->accounting){
						?>
						<div class="row">
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='Cheque')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('cheque_account', $acc, '', 'class="form-control tip" id="cheque_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
                        </div>
						<?php
						}
						?>
                        </div>


						<div class="ptransfer_1" style="display:none;">

							<?php
						if($Settings->accounting){
						?>
						<div class="row">
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];

				foreach ($payment_types as $account) {
					if($account->payment_type=='bank_transfer')
                     $acc[$account->id] = $account->accountName;
                 }
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('transfer_account', $acc, '', 'class="form-control tip" id="transfer_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
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




                    <div class="form-group">
                        <?php echo lang('note', 'note'); ?>
                        <div class="controls">
                            <?php echo form_textarea('note', set_value('note'), 'class="form-control" id="note"'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_deposit', lang('add_deposit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {

        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
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
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
                $('.gc').hide();
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
    });
</script>

