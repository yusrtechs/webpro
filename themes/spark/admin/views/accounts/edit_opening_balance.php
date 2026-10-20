<?php defined('BASEPATH') or exit('No direct script access allowed');
ini_set('display_errors', 0);
?>
<script type="text/javascript">
    var count = 1, tdebit = 1, count_cost = 1, count_value = 1, an = 1;
    var type_opt = {'addition': '<?= lang('addition'); ?>', 'subtraction': '<?= lang('subtraction'); ?>'};
	localStorage.setItem('cost_centers', JSON.stringify(<?= $cost_centers; ?>));

    $(document).ready(function () {
        var isGroup= '<?= $this->Settings->isGrouping ?>';
		localStorage.setItem('cost_centers', JSON.stringify(<?= $cost_centers; ?>));

        if (localStorage.getItem('remove_obls')) {

            if (localStorage.getItem('obnote')) {
                localStorage.removeItem('obnote');
            }
            if (localStorage.getItem('obdate')) {
                localStorage.removeItem('obdate');
            }
            if (localStorage.getItem('obbrId')) {
                localStorage.removeItem('obbrId');
            }
            localStorage.removeItem('remove_obls');
        }

        <?php if ($opening_balance_items) {
        ?>
        localStorage.setItem('obitems', JSON.stringify(<?= $opening_balance_items; ?>));
        <?php
        } ?>
        <?php if ($opening_balance) {
        ?>
        localStorage.setItem('obdate', '<?= $this->sma->hrld($opening_balance->openingDate) ?>');
        <?php
        } ?>
        <?php if ($warehouse_id) {
        ?>
        localStorage.setItem('obwarehouse', '<?= $warehouse_id; ?>');
        $('#obwarehouse').select2('readonly', true);
        <?php
    } ?>

$(document).on('change', '#obbrId', function (e) {

localStorage.setItem('obbrId', $(this).val());

});

if (obbrId = localStorage.getItem('obbrId')) {
$('#obbrId').val(obbrId);

}else{

localStorage.setItem('obbrId', '<?= $opening_balance->brId; ?>');
}

        <?php if ($Owner || $Admin) {
        ?>
        if (!localStorage.getItem('obdate')) {
            $("#obdate").datetimepicker({
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
        $(document).on('change', '#obdate', function (e) {
            localStorage.setItem('obdate', $(this).val());
        });
        if (obdate = localStorage.getItem('obdate')) {
            $('#obdate').val(obdate);
        }
        <?php
    } ?>




		$("#add_item").autocomplete({
            source: function (request, response) {

                if (!$('#obbrId').val() && isGroup==1) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select') . ' ' . lang('Main_branch');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }



                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('accounts/qa_suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,

                        brId: $("#obbrId").val()
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
                    bootbox.alert('<?= lang('Account_no_match_found') ?>', function () {
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
                    bootbox.alert('<?= lang('Account_no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_opening_balance_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('Account_no_match_found') ?>');
                }
            }
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit_opening_balance'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/edit_opening_balance' . ($count_id ? '/' . $count_id : ''), $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">

                    <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
							<div class="col-md-3">
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
							</div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="col-md-3">
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
							</div>
                            <?php }

                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
							<div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{

                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }

                                        }
                                            echo form_dropdown('brId', $br, $opening_balance->brId, 'class="form-control select" id="obbrId"   style="width:100%"')
                                            ?>
                            </div>
							</div>
                           <?php
                        }} ?>

                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('date', 'obdate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($opening_balance->openingDate)), 'readonly class="form-control input-tip datetime" id="obdate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                <div class="col-sm-2" >
                    <div class="form-group">
						<?= lang('currency', 'currency'); ?>
						<?php
						foreach ($currencies as $currency) {
							$curr[$currency->code] = $currency->name;
						}
						echo form_dropdown('currency', $curr, $opening_balance->currency_code, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
					</div>
                </div>


				<div class="col-md-1" style="display:none;">
                                <div class="form-group">
                               <label style="padding-top: 12px;"></label>
									<div class="input-group-addon" style="padding-left: 10px; padding-right: 10px; ">
                                            <a href="<?= admin_url('accounts/add_opening_balance/all') ?>" id="addManually">

											<i class="fa fa-2x 	fa-arrow-circle-o-down addIcon" id="addIcon"></i>
									</a>
								    </div>

								</div>

                            </div>

							<div class="col-md-7" style="display:none;">
                                <div class="form-group" style="padding-top:25px;margin-right:-20px;">

                                  <?= lang('Import_all_accounts', 'Import_all_accounts'); ?>

                                </div>
                            </div>




                        <div class="clearfix"></div>


						<div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang('Add_accounts_to_order') . '"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>




                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('Accounts'); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="obTable" class="table items table-striped  table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th><?= lang('Account_name') . ' (' . lang('Account_code') . ')'; ?></th>
                                            <th class="col-md-2"><?= lang('Debit'); ?></th>
                                            <th class="col-md-2"><?= lang('Credit'); ?></th>
                                            <th class="col-md-2"><?= lang('Cost_center'); ?></th>
                                            <th class="col-md-2"><?= lang('Amount'); ?></th>

                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('note', 'obnote'); ?>
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="obnote" style="margin-top: 10px; height: 100px;"'); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>


                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_opening_balance', lang('submit'), 'id="add_opening_balance" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
