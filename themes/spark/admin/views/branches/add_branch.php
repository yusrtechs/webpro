<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        /*$("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_model_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_model_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_model_to_load') ?>'}
            ]
        });*/
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

$('#coId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                //data: scdata


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
    });
</script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Branch'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('branches/add_branch', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

        <div class="row">
          <div class="col-md-6">
            <?php
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
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
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
                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"  required="required" style="width:100%"')
                ?>
            </div>
            <?php } ?>
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('Code_Branch'); ?></label>
                <?php echo form_input('code', $branch_code, 'class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="nameEn"><?php echo $this->lang->line('English_name'); ?></label>
                <?php echo form_input('nameEn', '', 'class="form-control" id="nameEn" '); ?>
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
                <label class="control-label" for="branch_name"><?php echo $this->lang->line('The_name_appearing_on_the_invoice'); ?></label>
                <?php echo form_input('branch_name', '', 'class="form-control" id="branch_name"'); ?>
            </div>

            <div class="form-group">
                <label class="control-label" for="branch_nameEn"><?php echo $this->lang->line('The_name_appearing_on_the_invoice_is_in_English'); ?></label>
                <?php echo form_input('branch_nameEn', '', 'class="form-control" id="branch_nameEn"'); ?>
            </div>



            </div>


		  <div class="col-md-6">



          <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
                <?php echo form_textarea('address', '', 'class="form-control" id="address" required="required"'); ?>
            </div>

			<div class="form-group">
                <label class="control-label" for="addressEn"><?php echo $this->lang->line('English_address'); ?></label>
                <?php echo form_textarea('addressEn', '', 'class="form-control" id="addressEn"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="branch_prefix"><?php echo $this->lang->line('prefix'); ?></label>
                <?php echo form_input('branch_prefix', $branch_prefix, 'class="form-control" id="branch_prefix" required="required"'); ?>
            </div>


            <p class="text-primary"><?= lang('Recommended')." : ".lang('logo_image_tip'); ?></p>

<div class="form-group">
    <?= lang('Logo', 'Logo') ?>
    <input id="branch_logo" type="file" data-browse-label="<?= lang('browse'); ?>" name="branch_logo" data-show-upload="false" data-show-preview="false" class="form-control file">
</div>




          </div>
		</div>




        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_branch', lang('Add_Branch'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
