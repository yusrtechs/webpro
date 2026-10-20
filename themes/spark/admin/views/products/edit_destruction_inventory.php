<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1;
    var type_opt = {'addition': '<?= lang('addition'); ?>', 'subtraction': '<?= lang('subtraction'); ?>'};
    $(document).ready(function () {

        if (localStorage.getItem('remove_qals')) {
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
            localStorage.removeItem('remove_qals');
        }


        <?php if ($adjustment_items) {
    ?>
        localStorage.setItem('qadate', '<?= $this->sma->hrld($adjustment->date) ?>');
        localStorage.setItem('qaitems', JSON.stringify(<?= $adjustment_items; ?>));
        <?php
} ?>
        <?php if ($adjustment->warehouse_id) {
        ?>
        localStorage.setItem('qawarehouse', '<?= $adjustment->warehouse_id; ?>');
        $('#qawarehouse').select2('readonly', true);
        <?php
    } ?>

        <?php if ($Owner || $Admin) {
        ?>
        if (!localStorage.getItem('qadate')) {
            $("#qadate").datetimepicker({
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
        $(document).on('change', '#qadate', function (e) {
            localStorage.setItem('qadate', $(this).val());
        });
        if (qadate = localStorage.getItem('qadate')) {
            $('#qadate').val(qadate);
        }
        <?php
    } ?>

        $("#add_item").autocomplete({

            //source: '<?= admin_url('products/de_suggestions'); ?>',
			source: function (request, response) {
                if (!$('#qawarehouse').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('products/de_suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#qawarehouse").val()
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
                    var row = add_adjustment_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit_inventory_operations'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('products/edit_destruction' . ($count_id ? '/' . $count_id : ''), $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'qadate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($adjustment->date)), 'class="form-control input-tip datetime" id="qadate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'qaref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control input-tip" id="qaref"'); ?>
                            </div>
                        </div>
                        <?= form_hidden('count_id', $count_id); ?>

                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('warehouse', 'qawarehouse'); ?>
                                    <?php
                                    $wh[''] = '';
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse', $wh, $adjustment->warehouse_id, 'id="qawarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" ' . ($warehouse_id ? 'readonly' : '') . ' style="width:100%;"'); ?>
                                </div>
                            </div>
                            <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'qawarehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Operation_type', 'op_type'); ?>
                                <?php
                                    $tropts = [1 => lang('Destruction'), 2 => lang('Gifts_and_donations')];
                                    echo form_dropdown('op_type', $tropts, (isset($_POST['op_type']) ? $_POST['op_type'] : $adjustment->op_type), 'class="tip form-control" required="required" id="op_type" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <?php
			            	if($Settings->accounting){
			            ?>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="op_acct"><?= lang('Account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('op_acct', $acc, ($adjustment->op_acct ? $adjustment->op_acct : $accounts_settings->warehouses_destruction), 'class="form-control tip" id="op_acct" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>
                        <?php
			            	}
			            ?>
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
                                    <table id="qaTable" class="table items table-striped  table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th><?= lang('product_name') . ' (' . lang('product_code') . ')'; ?></th>
											<th class="col-md-1"><?= lang('quantity'); ?></th>
											<th class="col-md-1"><?= lang('unit'); ?></th>
                                            <th class="col-md-1"><?= lang('cost'); ?></th>
                                            <th class="col-md-1"><?= lang('value'); ?></th>
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
                                    <?= lang('note', 'qanote'); ?>
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $this->sma->decode_html($adjustment->note)), 'class="form-control" id="qanote" style="margin-top: 10px; height: 100px;"'); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_adjustment', lang('submit'), 'id="edit_adjustment" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
