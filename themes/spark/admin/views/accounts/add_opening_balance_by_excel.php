<?php defined('BASEPATH') or exit('No direct script access allowed');
ini_set('display_errors', 0);
 ?>
<script type="text/javascript">

    $(document).ready(function () {

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

    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add_opening_balance_by_excel'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/add_opening_balance_by_excel', $attrib);
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
												if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
											}else{
												$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
											}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="obbrId"   style="width:100%"')
                                            ?>
                            </div>
							</div>
                           <?php
                        }} ?>
                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('date', 'qadate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'class="form-control input-tip datetime" id="qadate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                <div class="col-sm-3" >
                    <div class="form-group">
						<?= lang('currency', 'currency'); ?>
						<?php
						foreach ($currencies as $currency) {
							$curr[$currency->code] = $currency->name;
						}
						echo form_dropdown('currency', $curr, $Settings->default_currency, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
					</div>
                </div>




                        <div class="clearfix"></div>



                        <div class="col-md-12">

                            <div class="well well-small">
                                <a href="<?php echo base_url(); ?>assets/csv/sample_opening_balance.xlsx"
                                 class="btn btn-primary pull-right"><i
                                 class="fa fa-download"></i> <?= lang('download_sample_file') ?></a>
                                 <span class="text-warning"><?= lang('xlsx1'); ?></span><br/><?= lang('xlsx2'); ?> <span
                                 class="text-info">(<?= lang('Account_code') . ', ' . lang('Account_name') . ', ' . lang('Debit') . ', ' . lang('Credit') . ', ' . lang('Cost_center_No') . ', ' . lang('Cost_center_name') . ', ' . lang('Cost_center_amount'); ?>
                                 )</span> <?= lang('xlsx3'); ?>
                                <br>
                             </div>

                             <div class="form-group">
                                <label for="xlsx_file"><?= lang('upload_file'); ?></label>
                                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="xlsx_file" class="form-control file" data-show-upload="false" data-show-preview="false" id="xlsx_file" required="required"/>
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
