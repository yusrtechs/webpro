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



    });
</script>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_warehouse'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/add_warehouse', $attrib); ?>
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
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
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
                                        <?php }
                                        } ?>



            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('code'); ?></label>
                <?php echo form_input('code', $warehouse_code, 'class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('Warehouse_name'); ?></label>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="nameEn"><?php echo $this->lang->line('English_warehouse_name'); ?></label>
                <?php echo form_input('nameEn', '', 'class="form-control" id="nameEn" '); ?>
            </div>
            <div class="form-group" <?php if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) echo "style='display:none;'"; ?>>
                <label class="control-label" for="branch_name"><?php echo $this->lang->line('Branch_name'); ?></label>
                <?php echo form_input('branch_name', '', 'class="form-control" id="branch_name" '); ?>
            </div>
            <div class="form-group" <?php if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) echo "style='display:none;'"; ?>>
                <label class="control-label" for="branch_nameEn"><?php echo $this->lang->line('English_branch_name'); ?></label>
                <?php echo form_input('branch_nameEn', '', 'class="form-control" id="branch_nameEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="vat_reg_no"><?php echo $this->lang->line('VAT_Regesteration_Number').' ( '.$this->lang->line('Required_fifteen_digits').' ) '; ?></label>
                <?php echo form_input('vat_reg_no', '', 'type="number" pattern="[0-9]{15}" maxlength=15" class="form-control" id="vat_reg_no"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="cr_no"><?php echo $this->lang->line('CR_Number').' ( '.$this->lang->line('Required_ten_digits').' ) '; ?></label>
                <?php echo form_input('cr_no', '', 'type="number" pattern="[0-9]{6}" maxlength=10" class="form-control" id="cr_no"  '); ?>
            </div>


			<div class="form-group">
                                <label class="control-label" for="tax_method"><?= lang('tax_method'); ?></label>

                                <div class="controls">
                                    <?php
                                    $opt_wh = [ 'products' => lang('From').' '.lang('products'), 'exclusive' => lang('exclusive'), 'inclusive' => lang('inclusive')];
                                    echo form_dropdown('tax_method', $opt_wh, 'products', 'class="form-control tip" required="required" id="tax_method" style="width:100%;"');
                                    ?>
                                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="phone"><?php echo $this->lang->line('phone'); ?></label>
                <?php echo form_input('phone', '', 'class="form-control" id="phone"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="email"><?php echo $this->lang->line('email'); ?></label>
                <?php echo form_input('email', '', 'class="form-control" id="email"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="postal_code"><?php echo $this->lang->line('postal_code').' ( '.$this->lang->line('Required_five_digits').' ) '; ?></label>
                <?php echo form_input('postal_code', '', 'type="number" pattern="[0-9]{5}" maxlength="5" class="form-control" id="postal_code" required="required"'); ?>
            </div>
			<div class="form-group">
                <label class="control-label" for="additional_no"><?php echo $this->lang->line('Additional_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                <?php echo form_input('additional_no', '', 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="additional_no" required="required"'); ?>
            </div>



		  </div>


		  <div class="col-md-6">

            <div class="form-group">
                <label class="control-label" for="price_group"><?php echo $this->lang->line('price_group'); ?></label>
                <?php
                $pgs[''] = lang('select') . ' ' . lang('price_group');
                foreach ($price_groups as $price_group) {
                    $pgs[$price_group->id] = $price_group->name;
                }
                echo form_dropdown('price_group', $pgs, $Settings->price_group, 'class="form-control tip select" id="price_group" style="width:100%;"');
                ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="building_no"><?php echo $this->lang->line('Building_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                <?php echo form_input('building_no', '', 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="building_no" required="required"'); ?>
            </div>

            <div class="form-group">
                <label class="control-label" for="street_name"><?php echo $this->lang->line('Street_name'); ?></label>
                <?php echo form_input('street_name', '', 'class="form-control" id="street_name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="street_nameEn"><?php echo $this->lang->line('English_street_name'); ?></label>
                <?php echo form_input('street_nameEn', '', 'class="form-control" id="street_nameEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="district_name"><?php echo $this->lang->line('District_name'); ?></label>
                <?php echo form_input('district_name', '', 'class="form-control" id="district_name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="district_nameEn"><?php echo $this->lang->line('English_district_name'); ?></label>
                <?php echo form_input('district_nameEn', '', 'class="form-control" id="district_nameEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="city"><?php echo $this->lang->line('city'); ?></label>
                <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="cityEn"><?php echo $this->lang->line('English_city'); ?></label>
                <?php echo form_input('cityEn', '', 'class="form-control" id="cityEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="state"><?php echo $this->lang->line('state'); ?></label>
                <?php echo form_input('state', '', 'class="form-control" id="state" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="stateEn"><?php echo $this->lang->line('English_state'); ?></label>
                <?php echo form_input('stateEn', '', 'class="form-control" id="stateEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="country"><?php echo $this->lang->line('country'); ?></label>
                <?php echo form_input('country', '', 'class="form-control" id="country" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="countryEn"><?php echo $this->lang->line('English_country'); ?></label>
                <?php echo form_input('countryEn', '', 'class="form-control" id="countryEn" '); ?>
            </div>





          </div>
		</div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
                <?php echo form_textarea('address', '', 'class="form-control" id="address" required="required"'); ?>
            </div>
            </div>
            <div class="col-md-6">
			<div class="form-group">
                <label class="control-label" for="addressEn"><?php echo $this->lang->line('English_address'); ?></label>
                <?php echo form_textarea('addressEn', '', 'class="form-control" id="addressEn"'); ?>
            </div>
            </div>
			<p class="text-primary"><?= lang('Recommended')." : ".lang('logo_image_tip'); ?></p>

            <div class="form-group">
                <?= lang('Warehouse_logo', 'Warehouse_logo') ?>
                <input id="warehouse_logo" type="file" data-browse-label="<?= lang('browse'); ?>" name="warehouse_logo" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            <div class="form-group">
                <?= lang('warehouse_map', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
            <div class="form-group all">
                <?= form_checkbox('isLogistic', '1','', 'class="form-control tip" id="isLogistic" '); ?>
				<?= lang('Logistic_Template', 'Logistic_Template'); ?>
            </div>
            <?php
			if($Settings->isTobacco==1){

                ?>
            <div class="form-group all">
                <?= form_checkbox('isTobacco', '1','', 'class="form-control tip" id="isTobacco" '); ?>
				<?= lang('A_store_that_provides_tobacco_products', 'A_store_that_provides_tobacco_products'); ?>
            </div>
            <?php
            }
            ?>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_warehouse', lang('add_warehouse'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>