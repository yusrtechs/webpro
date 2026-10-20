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
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_salesman'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-salesman-form'];
        echo admin_form_open_multipart('salesman/edit/' . $salesman->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>



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
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $salesman->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                ?>
            </div>
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

                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $salesman->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
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
                                    foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                }
                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $salesman->brId), 'class="form-control select" id="brId"  required="required" style="width:100%"')
                    ?>
                </div>
                <?php } } ?>




            <div class="form-group person">
                        <?= lang('name', 'name'); ?>
                        <?php echo form_input('name', $salesman->name, 'class="form-control tip" id="name" required="required"'); ?>
                    </div>
                          <div class="form-group">
                    <?= lang('Commission_calculation', 'cf5'); ?>
                    <?php $types = ['1' => lang('sales'), '2' => lang('profit')];
                    echo form_dropdown('cf5', $types, $salesman->cf5, 'class="form-control select" id="cf5" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('Commission_%', 'cf6'); ?>
                         <input class="form-control"  name="cf6" id="cf6"  type="number" min="0" step=".01" value="<?= $salesman->cf6; ?>" required />

                    </div>

                    <div class="form-group">
                            <?= lang('Address_in_Arabic', 'Address_in_Arabic'); ?>
                            <?php echo form_input('address', $salesman->address, 'class="form-control" id="address" '); ?>
                        </div>

                        <div class="form-group">
                        <?= lang('English_address', 'English_address'); ?>
                        <?php echo form_input('addressEn', $salesman->addressEn, 'class="form-control" id="addressEn" '); ?>
                    </div>



            <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone" value="<?= $salesman->phone ?>"/>
                    </div>


                    <div class="form-group">
                            <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address" value="<?= $salesman->email ?>"/>
                    </div>


                </div>
                <div class="col-md-6">
                <div class="form-group">
                        <?= lang('ID_Number', 'cf3'); ?>
                        <?php echo form_input('cf3', $salesman->cf3, 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('Nationality', 'cf4'); ?>
                        <?php echo form_input('cf4', $salesman->cf4, 'class="form-control" id="cf4"'); ?>

                    </div>

                <div class="form-group">
                        <?= lang('city', 'city'); ?>
                        <?php echo form_input('city', $salesman->city, 'class="form-control" id="city" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('state', 'state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $states = $this->gst->getIndianStates(true);
                            echo form_dropdown('state', $states, $salesman->state, 'class="form-control select" id="state" ');
                        } else {
                            echo form_input('state', $salesman->state, 'class="form-control" id="state"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang('country', 'country'); ?>
                        <?php echo form_input('country', $salesman->country, 'class="form-control" id="country"'); ?>
                    </div>

                    <div class="form-group person" style="display:none;">
                        <?= lang('nameEn', 'nameEn'); ?>
                        <?php echo form_input('nameEn', $salesman->nameEn, 'class="form-control tip" id="nameEn" '); ?>
                    </div>





                    <div class="form-group" dir="ltr">

                        <?=  lang('cf1', 'cf1'); ?>
                        <?php echo form_input('cf1', $salesman->cf1, 'class="form-control" id="cf1"'); ?>
                    </div>



					<div class="form-group">
                        <?= lang('cf2', 'cf2'); ?>
                        <?php echo form_input('cf2', $salesman->cf2, 'class="form-control" id="cf2"'); ?>

                    </div>
                </div>


			</div>






        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_salesman', lang('edit_salesman'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>



<script>
    $(document).ready(function () {

       $('#grpId').select2('readonly', true);
       $('#coId').select2('readonly', true);
       $('#brId').select2('readonly', true);
    }
    );

</script>
