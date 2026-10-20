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

$('#coId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('no_model_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_model_id') ?>",
                                data: [{id: '', text: '<?= lang('no_model_id') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
    });
</script>

<div class="row">

    <div class="col-sm-2">
        <div class="row">
            <div class="col-sm-12 text-center">
                <div style="max-width:200px; margin: 0 auto;">
                    <?=
                    $user->avatar ? '<img alt="" src="' . base_url() . 'assets/uploads/avatars/thumbs/' . $user->avatar . '" class="avatar">' :
                        '<img alt="" src="' . base_url() . 'assets/images/' . $user->gender . '.png" class="avatar">';
                    ?>
                </div>
                <h4><?= lang('login_email'); ?></h4>

                <p><i class="fa fa-envelope"></i> <?= $user->email; ?></p>
            </div>
        </div>
    </div>

    <div class="col-sm-10">

        <ul id="myTab" class="nav nav-tabs">
            <li class=""><a href="#edit" class="tab-grey"><?= lang('edit') ?></a></li>
            <li class=""><a href="#cpassword" class="tab-grey"><?= lang('change_password') ?></a></li>
            <li class=""><a href="#avatar" class="tab-grey"><?= lang('avatar') ?></a></li>
        </ul>

        <div class="tab-content">
            <div id="edit" class="tab-pane fade in">

                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-edit nb"></i><?= lang('edit_profile'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">

                                <?php $attrib = ['class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form'];
                                echo admin_form_open('auth/edit_user/' . $user->id, $attrib);
                                ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php echo lang('first_name', 'first_name'); ?>
                                                <div class="controls">
                                                    <?php echo form_input('first_name', $user->first_name, 'class="form-control" id="first_name" required="required"'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <?php echo lang('last_name', 'last_name'); ?>

                                                <div class="controls">
                                                    <?php echo form_input('last_name', $user->last_name, 'class="form-control" id="last_name" required="required"'); ?>
                                                </div>
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
                                        if ($Owner || $Admin){
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                    }else{
                                        $grp[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : $user->grpId), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        //$co[''] = lang('select') . ' ' . lang('Main_company');
                                        if ($Owner || $Admin){

                                        foreach ($mainCompanies as $mainCompany) {
                                            if($user->coId==$mainCompany->id)
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }

                                }else{
                                    $co[$this->session->userdata('coId')] = $this->site->getMainCompaniesByID($this->session->userdata('coId'))->name;
                                }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $user->coId), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            //$br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if ($Owner || $Admin){
                                             foreach ($mainBranches as $mainBranche) {
                                                if($user->brId==$mainBranche->id)
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }

                                    }else{
                                        $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $user->brId), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>


                                         <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) {
                                    ?>
                                                <div class="form-group">
                                                    <?php echo lang('company', 'company'); ?>
                                                    <div class="controls">
                                                        <?php echo form_input('company', $user->company, 'class="form-control" id="company" required="required"'); ?>
                                                    </div>
                                                </div>
                                            <?php
                                } else {
                                    echo form_hidden('company', $user->company);
                                } ?>
                                            <div class="form-group">

                                                <?php echo lang('phone', 'phone'); ?>
                                                <div class="controls">
                                                    <input type="tel" name="phone" class="form-control" id="phone"
                                                           required="required" value="<?= $user->phone ?>"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <?= lang('gender', 'gender'); ?>
                                                <div class="controls">  <?php
                                                    $ge[''] = ['male' => lang('male'), 'female' => lang('female')];
                                                    echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : $user->gender), 'class="tip form-control" id="gender" required="required"');
                                                    ?>
                                                </div>
                                            </div>
                                            <?php if (($Owner || $Admin) && $id != $this->session->userdata('user_id')) {
                                                        ?>
                                            <div class="form-group">
                                                <?= lang('award_points', 'award_points'); ?>
                                                <?= form_input('award_points', set_value('award_points', $user->award_points), 'class="form-control tip" id="award_points"  required="required"'); ?>
                                            </div>
                                            <?php
                                                    } ?>

                                            <?php if ($Owner && $id != $this->session->userdata('user_id')) {
                                                        ?>
                                                <div class="form-group">
                                                    <?php echo lang('username', 'username'); ?>
                                                    <input type="text" name="username" class="form-control"
                                                           id="username" value="<?= $user->username ?>"
                                                           required="required"/>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo lang('email', 'email'); ?>

                                                    <input type="email" name="email" class="form-control" id="email"
                                                           value="<?= $user->email ?>" required="required"/>
                                                </div>
                                                <div class="row">
                                                    <div class="panel panel-warning">
                                                        <div
                                                            class="panel-heading" style="font-size: 11px;"><?= lang('if_you_need_to_rest_password_for_user') ?></div>
                                                        <div class="panel-body" style="padding: 5px;">
                                                            <div class="col-md-12">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <?php echo lang('password', 'password'); ?>
                                                                        <?php echo form_input($password); ?>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <?php echo lang('confirm_password', 'password_confirm'); ?>
                                                                        <?php echo form_input($password_confirm); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php
                                                    } ?>

                                        </div>
                                        <div class="col-md-8 col-md-offset-1x">
                                            <?php if ($Owner && $id != $this->session->userdata('user_id')) {
                                                        ?>

                                                    <div class="col-md-12">
                                                        <div class="panel panel-warning">
                                                            <div class="panel-heading"><?= lang('user_options') ?></div>
                                                            <div class="panel-body" style="padding: 5px;">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <?= lang('Status', 'status'); ?>
                                                                            <?php
                                                                            $opt = [1 => lang('active'), 0 => lang('inactive')];
                                                        echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : $user->active), 'id="status" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                                                        </div>
                                                                        <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) {
                                                            ?>
                                                                        <div class="form-group">
                                                                            <?= lang('group', 'group'); ?>
                                                                            <?php
                                                                            $gp[''] = '';
                                                            foreach ($groups as $group) {
                                                                if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                                                                    $gp[$group['id']] = $group['name'];
                                                                }
                                                            }
                                                            echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : $user->group_id), 'id="group" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('group') . '" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="row">
                                                                        <div class="no col-md-6">
                                                                            <div class="form-group">
                                                                                <?= lang('biller', 'biller'); ?>
                                                                                <?php
                                                                                $bl[''] = lang('select') . ' ' . lang('biller');
                                                            foreach ($billers as $biller) {
                                                                $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                                            }
                                                            echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $user->biller_id), 'id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                              <?php  if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){ ?>

                                                                <div class="form-group">
                                                                <?= lang('Showroom', 'warehouse'); ?>
                                                                <?php
                                                                $wh[''] = lang('select') . ' ' . lang('Showroom');
                                                            foreach ($warehouses as $warehouse) {
                                                                $wh[$warehouse->id] = $warehouse->name;
                                                            }
                                                            echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $user->warehouse_id), 'id="warehouse" class="form-control select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Showroom') . '" style="width:90%;" '); ?>
                                                                            </div>

                                                            <?php }else{?>
                                                                            <div class="form-group">
                                                                                <?= lang('warehouse', 'warehouse'); ?>
                                                                                <?php
                                                                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                                            foreach ($warehouses as $warehouse) {
                                                                $wh[$warehouse->id] = $warehouse->name;
                                                            }
                                                            echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $user->warehouse_id), 'id="warehouse" class="form-control select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '" style="width:90%;" '); ?>
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
                                                                echo form_dropdown('salesman_id', $salM, (isset($_POST['salesman_id']) ? $_POST['salesman_id'] : $user->salesman_id), 'class="form-control select" id="salesman_id" style="width:90%;"');
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
                                                                echo form_dropdown('waiter_id', $wat, (isset($_POST['waiter_id']) ? $_POST['waiter_id'] : $user->waiter_id), 'class="form-control select" id="waiter_id" style="width:90%;"');
                                                                ?>
                                                            </div>
                                                            <?php }
                                                            ?>

                                                                            <div class="form-group">
                                                                                <?= lang('view_right', 'view_right'); ?>
                                                                                <?php
                                                                                $vropts = [1 => lang('all_records'), 0 => lang('own_records')];
                                                            echo form_dropdown('view_right', $vropts, (isset($_POST['view_right']) ? $_POST['view_right'] : $user->view_right), 'id="view_right" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang('edit_right', 'edit_right'); ?>
                                                                                <?php
                                                                                $opts = [1 => lang('yes'), 0 => lang('no')];
                                                            echo form_dropdown('edit_right', $opts, (isset($_POST['edit_right']) ? $_POST['edit_right'] : $user->edit_right), 'id="edit_right" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                            <?= lang('Show_price', 'Show_price'); ?>
                                                                                <?php
                                                                                $opts = [1 => lang('yes'), 0 => lang('no')];
                                                            echo form_dropdown('show_price', $opts, (isset($_POST['show_price']) ? $_POST['show_price'] : $user->show_price), 'id="show_price" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang('allow_discount', 'allow_discount'); ?>
                                                                                <?= form_dropdown('allow_discount', $opts, (isset($_POST['allow_discount']) ? $_POST['allow_discount'] : $user->allow_discount), 'id="allow_discount" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
																			<div class="form-group">
                                                                                <?= lang('Edit_price', 'Edit_price'); ?>
                                                                                <?= form_dropdown('edit_price', $opts, (isset($_POST['edit_price']) ? $_POST['edit_price'] : $user->edit_price), 'id="edit_price" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang('Show_sales', 'Show_sales'); ?>
                                                                                <?php
                                                                                    $optsshowsales = [0 => lang('no'), 1 => lang('yes')];
                                                                                    echo form_dropdown('show_sales', $optsshowsales, (isset($_POST['show_sales']) ? $_POST['show_sales'] : $user->show_sales), 'id="show_sales" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="no col-md-6">
                                                                            <div class="form-group">
                                                                                <?= lang('Show_cost', 'Show_cost'); ?>
                                                                                <?= form_dropdown('show_cost', $opts, (isset($_POST['show_cost']) ? $_POST['show_cost'] : $user->show_cost), 'id="show_cost" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang('Max_discount', 'Max_discount').' %'; ?>
                                                                                <?= form_input('max_discount', set_value('max_discount', $user->max_discount), 'class="form-control tip" id="max_discount"  required="required"'); ?>
                                                                             </div>
                                                                             <div class="form-group">
                                                                            <?= lang('Receptionist', 'Receptionist'); ?>
                                                                                <?php
                                                                                $opts = [1 => lang('yes'), 0 => lang('no')];
                                                                                echo form_dropdown('isRecepion', $opts, (isset($_POST['isRecepion']) ? $_POST['isRecepion'] : $user->isRecepion), 'id="isRecepion" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                            <?= lang('Print_invoices', 'Print_invoices'); ?>
                                                                                <?php
                                                                                $optsprint = [1 => lang('yes'), 0 => lang('no')];
                                                                                echo form_dropdown('isPrintInvoices', $optsprint, (isset($_POST['isPrintInvoices']) ? $_POST['isPrintInvoices'] : $user->isPrintInvoices), 'id="isPrintInvoices" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>

                                                                            <div class="form-group">
                                                                            <?= lang('Supervisor', 'Supervisor'); ?>
                                                                                <?php
                                                                                $optsvisor = [0 => lang('no'), 1 => lang('yes')];
                                                                                echo form_dropdown('isSupervisor', $optsvisor, (isset($_POST['isSupervisor']) ? $_POST['isSupervisor'] : $user->isSupervisor), 'id="isSupervisor" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>

                                                                            <div class="form-group">
                                                                            <?= lang('Print_close_register', 'CanPrintCashierClose'); ?>
                                                                                <?php
                                                                                $optsprintcashclose = [0 => lang('no'), 1 => lang('yes')];
                                                                                echo form_dropdown('CanPrintCashierClose', $optsprintcashclose, (isset($_POST['CanPrintCashierClose']) ? $_POST['CanPrintCashierClose'] : $user->CanPrintCashierClose), 'id="CanPrintCashierClose" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>

                                                                            <div class="form-group">
                                                                            <?= lang('Show_close_register', 'CanSeeCashierClose'); ?>
                                                                                <?php
                                                                                $optsshowcashclose = [0 => lang('no'), 1 => lang('yes')];
                                                                                echo form_dropdown('CanSeeCashierClose', $optsshowcashclose, (isset($_POST['CanSeeCashierClose']) ? $_POST['CanSeeCashierClose'] : $user->CanSeeCashierClose), 'id="CanPrintCashierClose" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>

                                                                            <div class="form-group">
                                                                            <?= lang('Show_pos_dashboard', 'showpos_dashboard'); ?>
                                                                                <?php
                                                                                $optsshowposdashboard = [0 => lang('no'), 1 => lang('yes')];
                                                                                echo form_dropdown('showpos_dashboard', $optsshowposdashboard, (isset($_POST['showpos_dashboard']) ? $_POST['showpos_dashboard'] : $user->showpos_dashboard), 'id="showpos_dashboard" class="form-control select" style="width:90%;"'); ?>
                                                                            </div>
                                                                        </div>

                                                                            <?php
                                                        } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                    } ?>
                                            <?php echo form_hidden('id', $id); ?>
                                            <?php echo form_hidden($csrf); ?>
                                        </div>
                                    </div>
                                </div>
                                <p><?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?></p>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cpassword" class="tab-pane fade">
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-key nb"></i><?= lang('change_password'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo admin_form_open('auth/change_password', 'id="change-password-form"'); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <?php echo lang('old_password', 'curr_password'); ?> <br/>
                                                <?php echo form_password('old_password', '', 'class="form-control" id="curr_password" required="required"'); ?>
                                            </div>

                                            <div class="form-group">
                                                <label
                                                    for="new_password"><?php echo sprintf(lang('new_password'), $min_password_length); ?></label>
                                                <br/>
                                                <?php echo form_password('new_password', '', 'class="form-control" id="new_password" required="required"  data-bv-regexp-message="' . lang('pasword_hint') . '"'); ?>
                                                <span class="help-block"><?= lang('pasword_hint') ?></span>
                                            </div>

                                            <div class="form-group">
                                                <?php echo lang('confirm_password', 'new_password_confirm'); ?> <br/>
                                                <?php echo form_password('new_password_confirm', '', 'class="form-control" id="new_password_confirm" required="required" data-bv-identical="true" data-bv-identical-field="new_password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>

                                            </div>
                                            <?php echo form_input($user_id); ?>
                                            <p><?php echo form_submit('change_password', lang('change_password'), 'class="btn btn-primary"'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="avatar" class="tab-pane fade">
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-file-picture-o nb"></i><?= lang('change_avatar'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-md-5">
                                    <div style="position: relative;">
                                        <?php if ($user->avatar) {
                                                        ?>
                                            <img alt=""
                                                 src="<?= base_url() ?>assets/uploads/avatars/<?= $user->avatar ?>"
                                                 class="profile-image img-thumbnail">
                                            <a href="#" class="btn btn-danger btn-xs po"
                                               style="position: absolute; top: 0;" title="<?= lang('delete_avatar') ?>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-block btn-danger po-delete' href='<?= admin_url('auth/delete_avatar/' . $id . '/' . $user->avatar) ?>'> <?= lang('i_m_sure') ?></a> <button class='btn btn-block po-close'> <?= lang('no') ?></button>"
                                               data-html="true" rel="popover"><i class="fa fa-trash-o"></i></a><br>
                                            <br><?php
                                                    } ?>
                                    </div>
                                    <?php echo admin_form_open_multipart('auth/update_avatar'); ?>
                                    <div class="form-group">
                                        <?= lang('change_avatar', 'change_avatar'); ?>
                                        <input type="file" data-browse-label="<?= lang('browse'); ?>" name="avatar" id="product_image" required="required"
                                               data-show-upload="false" data-show-preview="false" accept="image/*"
                                               class="form-control file"/>
                                    </div>
                                    <div class="form-group">
                                        <?php echo form_hidden('id', $id); ?>
                                        <?php echo form_hidden($csrf); ?>
                                        <?php echo form_submit('update_avatar', lang('update_avatar'), 'class="btn btn-primary"'); ?>
                                        <?php echo form_close(); ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#change-password-form').bootstrapValidator({
                message: 'Please enter/select a value',
                submitButtons: 'input[type="submit"]'
            });
        });
    </script>
    <?php if ($Owner && $id != $this->session->userdata('user_id')) {
                                                        ?>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            $('#group').change(function (event) {
                var group = $(this).val();
                if (group == 1 || group == 2) {
                    $('.no').slideUp();
                } else {
                    $('.no').slideDown();
                }
            });
            var group = <?=$user->group_id?>;
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
    </script>
<?php
                                                    } ?>

