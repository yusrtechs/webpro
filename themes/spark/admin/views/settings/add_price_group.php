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
    .append('<option value=""><?= lang('all') ?></option>')
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
    .append('<option value=""><?= lang('all_warehouses') ?></option>')
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
    .append('<option value=""><?= lang('all') ?></option>')
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
    .append('<option value=""><?= lang('all_warehouses') ?></option>')
    .val('')
;


$('#brId').val('').trigger('change.select2');
$('#biller').val('').trigger('change.select2');
$('#warehouse').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('all') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('all') ?>",
                                scdata: [{id: '', text: '<?= lang('all') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('all') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('all') ?>",
                    scdata: [{id: '', text: '<?= lang('all') ?>'}]
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
    .append('<option value=""><?= lang('all_warehouses') ?></option>')
    .val('')
;




$('#warehouse').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });

                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('all_warehouses') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('all_warehouses') ?>",
                                scdata: [{id: '', text: '<?= lang('all_warehouses') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('all_warehouses') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('all_warehouses') ?>",
                    scdata: [{id: '', text: '<?= lang('all_warehouses') ?>'}]
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_price_group'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/add_price_group', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label for="name"><?php echo $this->lang->line('group_name'); ?></label>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>

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

                                if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                    if ($Owner || $Admin){
                                        $br['0'] = lang('all');
                                        foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"');

                                }else{
                                    $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"');

                                }
                                }
                                ?>
                            </div>
                           <?php
                        }} ?>

                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>

            <div class="form-group">
                <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh['0'] = lang('all_warehouses');
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>

            <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'warehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_price_group', lang('add_price_group'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>