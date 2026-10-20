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

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('create_user'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('create_user'); ?></p>

                <?php $attrib = ['class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open('auth/create_user', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('first_name', '', 'class="form-control" id="first_name" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('last_name', '', 'class="form-control" id="last_name" required="required"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = ['male' => lang('male'), 'female' => lang('female')];
                                echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang('select') . ' ' . lang('gender') . '" required="required"');
                                ?>
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
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                    }else{
                                        $grp[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : $this->session->userdata('grpId')), 'class="form-control select" id="grpId"   style="width:100%"')
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
                                        if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        if(($this->Settings->enable_grp==1)){}else{
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }
                                }else{
                                    $co[$this->session->userdata('coId')] = $this->site->getMainCompaniesByID($this->session->userdata('coId'))->name;
                                }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $this->session->userdata('coId')), 'class="form-control select" id="coId"   style="width:100%"')
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
                                            if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                            if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }
                                    }else{
                                        $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>


                            <div class="form-group">
                                <?php echo lang('company', 'company'); ?>
                                <div class="controls">
                                    <?php echo form_input('company', '', 'class="form-control" id="company" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('phone', 'phone'); ?>
                                <div class="controls">
                                    <?php echo form_input('phone', '', 'class="form-control" id="phone" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="email" id="email" name="email" class="form-control"
                                           required="required"/>
                                    <?php /* echo form_input('email', '', 'class="form-control" id="email" required="required"'); */ ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('username', 'username'); ?>
                                <div class="controls">
                                    <input type="text" id="username" name="username" class="form-control"
                                           required="required" pattern=".{4,20}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('password', 'password'); ?>
                                <div class="controls">
                                    <?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" '); ?>
                                    <span class="help-block"><?= lang('pasword_hint') ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('confirm_password', 'confirm_password'); ?>
                                <div class="controls">
                                    <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5 col-md-offset-1">

                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = [1 => lang('active'), 0 => lang('inactive')];
                                echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?= lang('group', 'group'); ?>
                                <?php
                                foreach ($groups as $group) {
                                    if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                                        $gp[$group['id']] = $group['name'];
                                    }
                                }
                                echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : ''), 'id="group" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="clearfix"></div>
                            <div class="no">
                                <div class="form-group">
                                    <?= lang('biller', 'biller'); ?>
                                    <?php
                                    $bl[''] = lang('select') . ' ' . lang('biller');
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'id="biller" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>



                              <?php  if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){ ?>
                                <div class="form-group">
                                    <?= lang('Showroom', 'warehouse'); ?>
                                    <?php
                                    $wh[''] = lang('select') . ' ' . lang('Showroom');
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control select" style="width:100%;" ');
                                    ?>
                                </div>
                                <?php }else{?>

                                 <div class="form-group">
                                    <?= lang('warehouse', 'warehouse'); ?>
                                    <?php
                                    $wh[''] = lang('select') . ' ' . lang('warehouse');
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control select" style="width:100%;" ');
                                    ?>
                                </div>

                                    <?php
                                      } ?>


                                <div class="form-group">
                                    <?= lang('Salesman', 'Salesman'); ?>
                                    <?php
                                $salM[""] = lang('select').' '.lang('Salesman');
                                foreach ($allSalesMan as $salesman) {
                                    $salM[$salesman->id] = $salesman->company;
                                }
                                echo form_dropdown('salesman_id', $salM, (isset($_POST['salesman_id']) ? $_POST['salesman_id'] : ""), 'class="form-control select" id="salesman_id" style="width:100%;"');
                                ?>
                                </div>

                                <?php if ($Settings->IsResturant) {
                                ?>
                                <div class="form-group">
                                    <?= lang('Waiter', 'waiter_id'); ?>
                                    <?php
                                    $wat[""] = lang('select').' '.lang('Waiter');
                                    foreach ($allWaiters as $waiter) {
                                        $wat[$waiter->id] = $waiter->name;
                                    }
                                    echo form_dropdown('waiter_id', $wat, (isset($_POST['waiter_id']) ? $_POST['waiter_id'] : $user->waiter_id), 'class="form-control select" id="waiter_id" style="width:100%;"');
                                    ?>
                                </div>
                                <?php }
                                ?>

                                <div class="form-group">
                                    <?= lang('view_right', 'view_right'); ?>
                                    <?php
                                    $vropts = [1 => lang('all_records'), 0 => lang('own_records')];
                                    echo form_dropdown('view_right', $vropts, (isset($_POST['view_right']) ? $_POST['view_right'] : 1), 'id="view_right" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('edit_right', 'edit_right'); ?>
                                    <?php
                                    $opts = [1 => lang('yes'), 0 => lang('no')];
                                    echo form_dropdown('edit_right', $opts, (isset($_POST['edit_right']) ? $_POST['edit_right'] : 0), 'id="edit_right" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Show_price', 'Show_price'); ?>
                                    <?php
                                    $opts = [1 => lang('yes'), 0 => lang('no')];
                                    echo form_dropdown('show_price', $opts, (isset($_POST['show_price']) ? $_POST['show_price'] : 0), 'id="show_price" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('allow_discount', 'allow_discount'); ?>
                                    <?= form_dropdown('allow_discount', $opts, (isset($_POST['allow_discount']) ? $_POST['allow_discount'] : 0), 'id="allow_discount" class="form-control select" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Edit_price', 'Edit_price'); ?>
                                    <?= form_dropdown('edit_price', $opts, (isset($_POST['edit_price']) ? $_POST['edit_price'] : 0), 'id="edit_price" class="form-control select" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Show_cost', 'show_cost'); ?>
                                    <?= form_dropdown('show_cost', $opts, (isset($_POST['show_cost']) ? $_POST['show_cost'] : 0), 'id="show_cost" class="form-control select" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Max_discount', 'Max_discount').' %'; ?>
                                    <?= form_input('max_discount', set_value('max_discount', '0'), 'class="form-control tip" id="max_discount"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Receptionist', 'Receptionist'); ?>
                                    <?php
                                    $opts = [1 => lang('yes'), 0 => lang('no')];
                                    echo form_dropdown('isRecepion', $opts, (isset($_POST['isRecepion']) ? $_POST['isRecepion'] : 0), 'id="isRecepion" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('Print_invoices', 'Print_invoices'); ?>
                                    <?php
                                    $optsprint = [1 => lang('yes'), 0 => lang('no')];
                                    echo form_dropdown('isPrintInvoices', $optsprint, (isset($_POST['isPrintInvoices']) ? $_POST['isPrintInvoices'] : 1), 'id="isPrintInvoices" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('Supervisor', 'Supervisor'); ?>
                                    <?php
                                    $optsvisor = [0 => lang('no'),1 => lang('yes')];
                                    echo form_dropdown('isSupervisor', $optsvisor, (isset($_POST['isSupervisor']) ? $_POST['isSupervisor'] : 1), 'id="isSupervisor" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('Print_close_register', 'CanPrintCashierClose'); ?>
                                    <?php
                                    $optsprintcashclose = [0 => lang('no'),1 => lang('yes')];
                                    echo form_dropdown('CanPrintCashierClose', $optsprintcashclose, (isset($_POST['CanPrintCashierClose']) ? $_POST['CanPrintCashierClose'] : 1), 'id="CanPrintCashierClose" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('Show_close_register', 'CanSeeCashierClose'); ?>
                                    <?php
                                    $optsshowcashclose = [0 => lang('no'),1 => lang('yes')];
                                    echo form_dropdown('CanSeeCashierClose', $optsshowcashclose, (isset($_POST['CanSeeCashierClose']) ? $_POST['CanSeeCashierClose'] : 1), 'id="CanSeeCashierClose" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label class="checkbox" for="notify">
                                        <input type="checkbox" name="notify" value="1" id="notify" checked="checked"/>
                                        <?= lang('notify_user_by_email') ?>
                                    </label>
                                </div>
                                <div class="clearfix"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <p><?php echo form_submit('add_user', lang('add_user'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.no').slideUp();
        $('#group').change(function (event) {
            var group = $(this).val();
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
    });
</script>
