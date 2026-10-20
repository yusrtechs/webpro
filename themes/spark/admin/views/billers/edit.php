<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
        <link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
        <script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>

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
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_biller'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('billers/edit/' . $biller->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('logo', 'biller_logo'); ?>
                        <?php
                        $biller_logos[''] = '';
                        foreach ($logos as $key => $value) {
                            $biller_logos[$value] = $value;
                        }
                        echo form_dropdown('logo', $biller_logos, $biller->logo, 'class="form-control select" id="biller_logo"  '); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div id="logo-con" class="text-center"><img
                            src="<?= base_url('assets/uploads/logos/' . $biller->logo) ?>" alt=""></div>
                </div>
            </div>
            <div class="row">
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
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                    }else{
                                        $grp[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : $biller->grpId), 'class="form-control select" id="grpId"   style="width:100%"')
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

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $biller->coId), 'class="form-control select" id="coId"   style="width:100%"')
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $biller->brId), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>

                    <div class="form-group company">
                        <?= lang('company', 'company'); ?>
                        <?php echo form_input('company', $biller->company, 'class="form-control tip" id="company" required="required"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang('name', 'name'); ?>
                        <?php echo form_input('name', $biller->name, 'class="form-control tip" id="name" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('VAT_Regesteration_Number', 'VAT_Regesteration_Number'); ?>
                        <?php echo form_input('vat_no', $biller->vat_no, 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('gst_no', 'gst_no'); ?>
                        <?php echo form_input('gst_no', $biller->gst_no, 'class="form-control" id="gst_no"'); ?>
                    </div>
                    <!--<div class="form-group company">
                    <?= lang('contact_person', 'contact_person'); ?>
                    <?php //echo form_input('contact_person', $biller->contact_person, 'class="form-control" id="contact_person" required="required"');?>
                </div> -->
                    <div class="form-group">
                        <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"
                               value="<?= $biller->email ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"
                               value="<?= $biller->phone ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang('address', 'address'); ?>
                        <?php echo form_input('address', $biller->address, 'class="form-control" id="address" '); ?>
                    </div>

                </div>
                <div class="col-md-6">


                    <div class="form-group">
                        <?= lang('city', 'city'); ?>
                        <?php echo form_input('city', $biller->city, 'class="form-control" id="city" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('state', 'state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $states = $this->gst->getIndianStates();
                            echo form_dropdown('state', $states, $biller->state, 'class="form-control select" id="state" ');
                        } else {
                            echo form_input('state', $biller->state, 'class="form-control" id="state"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang('country', 'country'); ?>
                        <?php echo form_input('country', $biller->country, 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('postal_code', 'postal_code'); ?>
                        <?php echo form_input('postal_code', $biller->postal_code, 'class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('bcf1', 'cf1'); ?>
                        <?php echo form_input('cf1', $biller->cf1, 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('bcf2', 'cf2'); ?>
                        <?php echo form_input('cf2', $biller->cf2, 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('bcf3', 'cf3'); ?>
                        <?php echo form_input('cf3', $biller->cf3, 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('bcf4', 'cf4'); ?>
                        <?php echo form_input('cf4', $biller->cf4, 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('bcf5', 'cf5'); ?>
                        <?php echo form_input('cf5', $biller->cf5, 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('bcf6', 'cf6'); ?>
                        <?php echo form_input('cf6', $biller->cf6, 'class="form-control" id="cf6"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('invoice_footer', 'invoice_footer'); ?>
                        <?php echo form_textarea('invoice_footer', $biller->invoice_footer, 'class="form-control content" id="invoice_footer"'); ?>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_biller', lang('edit_biller'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('#biller_logo').change(function (event) {
            var biller_logo = $(this).val();
            $('#logo-con').html('<img src="<?=base_url('assets/uploads/logos')?>/' + biller_logo + '" alt="">');
        });
    });
</script>


<script>
    $(document).ready(function () {

        $('#grpId').select2('readonly', true);
        $('#coId').select2('readonly', true);
        $('#brId').select2('readonly', true);

        $('.content').richText();

    }
    );

</script>
