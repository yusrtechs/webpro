<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script type="text/javascript">
    var count = 1, count_cost = 1, count_old_cost = 1, an = 1;
    var type_opt = {'addition': '<?= lang('addition'); ?>', 'subtraction': '<?= lang('subtraction'); ?>'};
    $(document).ready(function () {
        if (localStorage.getItem('remove_rvls')) {
            if (localStorage.getItem('rvitems')) {
                localStorage.removeItem('rvitems');
            }
            if (localStorage.getItem('approve_note')) {
                localStorage.removeItem('approve_note');
            }
            if (localStorage.getItem('rvref')) {
                localStorage.removeItem('rvref');
            }
            if (localStorage.getItem('rvwarehouse')) {
                localStorage.removeItem('rvwarehouse');
            }
            if (localStorage.getItem('rvnote')) {
                localStorage.removeItem('rvnote');
            }
            if (localStorage.getItem('rvdate')) {
                localStorage.removeItem('rvdate');
            }
            localStorage.removeItem('remove_rvls');
        }
        <?php if ($revaluation) {
    ?>
        localStorage.setItem('rvdate', '<?= $this->sma->hrld($revaluation->date); ?>');
        localStorage.setItem('rvref', '<?= $revaluation->reference_no; ?>');
        localStorage.setItem('rvwarehouse', '<?= $revaluation->warehouse_id; ?>');
        localStorage.setItem('rvnote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($revaluation->note)); ?>');
        localStorage.setItem('rvitems', JSON.stringify(<?= $revaluation_items; ?>));
        localStorage.setItem('remove_rvls', '1');
        <?php
} ?>

        $("#add_item").autocomplete({
            source: function (request, response) {

                if (!$('#rvwarehouse').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_warehouse');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }



                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('products/rv_suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#rvwarehouse").val()
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
                    var row = add_revaluation_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
    });



	$(document).on('click', '#edit_revaluation', function(e) {
		var formData_product_id = JSON.stringify($(".rid").serializeArray());
		$('#jsonArr_product_id').val(formData_product_id);


        var formData_old_cost = JSON.stringify($(".rold_cost").serializeArray());
		$('#jsonArr_old_cost').val(formData_old_cost);

		var formData_cost = JSON.stringify($(".rcost").serializeArray());
		$('#jsonArr_cost').val(formData_cost);



        });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit_revaluation_of_cost_prices'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'myForm'];
                echo admin_form_open_multipart('products/edit_revaluation/' . $revaluation->id, $attrib);
                ?>
				<div class="row">
				<input type="hidden" name="jsonArr_product_id" id="jsonArr_product_id"  value='1'/>
				<input type="hidden" name="jsonArr_old_cost" id="jsonArr_old_cost"  value='1'/>
				<input type="hidden" name="jsonArr_cost" id="jsonArr_cost"  value='1'/>


                    <div class="col-lg-12">

                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        //$grp[''] = lang('select') . ' ' . lang('Main_group');
                                       foreach ($mainGroups as $mainGroup) {
                                            if($revaluation->grpId==$mainGroup->id)
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        //$co[''] = lang('select') . ' ' . lang('Main_company');

                                            foreach ($mainCompanies as $mainCompany) {
                                                if($revaluation->coId==$mainCompany->id)
                                                $co[$mainCompany->id] = $mainCompany->name;
                                            }


                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            //$br[''] = lang('select') . ' ' . lang('Main_branch');
                                            foreach ($mainBranches as $mainBranche) {
                                                if($revaluation->brId==$mainBranche->id)
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $branch->brId), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'rvdate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($revaluation->date)), 'class="form-control input-tip datetime" id="rvdate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'rvref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $revaluation->reference_no), 'class="form-control input-tip" id="rvref"'); ?>
                            </div>
                        </div>


                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('warehouse', 'rvwarehouse'); ?>
                                    <?php
                                    $wh[''] = '';
                    foreach ($warehouses as $warehouse) {
                        if($revaluation->warehouse_id==$warehouse->id)
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $revaluation->warehouse_id), 'id="rvwarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); ?>
                                </div>
                            </div>
                            <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'rvwarehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('document', 'document') ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>





                        <div class="clearfix"></div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang('add_product_to_order') . '"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('products'); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="rvTable" class="table items table-striped  table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th><?= lang('product_name') . ' (' . lang('product_code') . ')'; ?></th>
                                            <th class="col-md-1"><?= lang('Previous_cost'); ?></th>
                                            <th class="col-md-1"><?= lang('New_cost'); ?></th>
                                            <th class="col-md-1"><?= lang('unit'); ?></th>

                                            <th style="max-width: 30px !important; text-align: center;">
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
                                    <?= lang('note', 'rvnote'); ?>
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="rvnote" style="margin-top: 10px; height: 100px;"'); ?>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_revaluation', lang('submit'), 'id="edit_revaluation" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>

				    <!--<div class="fprom-group">
					           <button type="button" class="btn btn-danger" id="edit_revaluation_new"><?= lang('reset') ?></div>
                        </div>-->
                <?php echo form_close();
				?>

            </div>

        </div>
    </div>
</div>

