<?php defined('BASEPATH') or exit('No direct script access allowed');
ini_set('display_errors', 0);
?>
<script type="text/javascript">
    var count = 1, count_cost = 1, count_value = 1, an = 1;
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
        localStorage.setItem('qaitems', JSON.stringify(<?= $adjustment_items; ?>));
        <?php
} ?>
        <?php if ($warehouse_id) {
        ?>
        localStorage.setItem('qawarehouse', '<?= $warehouse_id; ?>');
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
            source: '<?= admin_url('products/qa_suggestions'); ?>',
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



        $('#grpId').change(function () {
            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getCompaniesBygrpId') ?>/" + v,
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

$('#qawarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                            $('#qawarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
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
                    url: "<?= admin_url('branches/getBranchesBycoId') ?>/" + v,
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

$('#qawarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                            $('#qawarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
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

            var uopt1 = $("#qawarehouse");




            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {


//console.log(scdata[0])
$('#qawarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});




                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });
        $('#qawarehouse').val(scdata[0].id).trigger('change.select2');
                        } else {
                            $("#qawarehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
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
                $("#qawarehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }












        });
    });


	$(document).on('click', '#add_adjustment', function(e) {
		var formData_product_id = JSON.stringify($(".rid").serializeArray());
		$('#jsonArr_product_id').val(formData_product_id);

		var formData_quantity = JSON.stringify($(".rquantity").serializeArray());
		$('#jsonArr_quantity').val(formData_quantity);

		var formData_cost = JSON.stringify($(".rcost").serializeArray());
		$('#jsonArr_cost').val(formData_cost);

		var formData_expiry = JSON.stringify($(".rexpiry").serializeArray());
		$('#jsonArr_expiry').val(formData_expiry);

        });

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_adjustment'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('products/add_adjustment' . ($count_id ? '/' . $count_id : ''), $attrib);
                ?>
                <div class="row">
				<input type="hidden" name="jsonArr_product_id" id="jsonArr_product_id"  value='1'/>
				<input type="hidden" name="jsonArr_quantity" id="jsonArr_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_cost" id="jsonArr_cost"  value='1'/>
				<input type="hidden" name="jsonArr_expiry" id="jsonArr_expiry"  value='1'/>



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
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
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
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1 && !$this->input->post('grpId'))){}else{
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
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) && !$this->input->post('coId'))){}else{
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
                                        </div>
                                        <?php }
                                        } ?>


                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'qadate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'class="form-control input-tip datetime" id="qadate" required="required"'); ?>
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

                                    $wh[''] = lang('select') . ' ' . lang('warehouse');
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }

                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ($warehouse_id ? $warehouse_id : '')), 'id="qawarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" ' . ($warehouse_id ? 'readonly' : '') . ' style="width:100%;"'); ?>
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
                                            <th class="col-md-2"><?= lang('variant'); ?></th>
                                            <th class="col-md-1" style="display:none;"><?= lang('type'); ?></th>
                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <th class="col-md-1"><?= lang('cost'); ?></th>
                                            <th class="col-md-1"><?= lang('value'); ?></th>
                                            <th class="col-md-1"><?= lang('unit'); ?></th>
                                            <?php
                                            if ($Settings->product_serial) {
                                                echo '<th class="col-md-2" style="display:none;">' . lang('serial_no') . '</th>';
                                            }
                                            ?>
											<?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-2">' . $this->lang->line('expiry_date') . '</th>';
                                            }
                                            ?>

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
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="qanote" style="margin-top: 10px; height: 100px;"'); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_adjustment', lang('submit'), 'id="add_adjustment" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
