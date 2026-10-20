<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
        <script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_fixed_asset'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('fixedassets/add_fixed_asset', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', $fixed_asset_code, 'class="form-control" id="code" required="required"'); ?>
            </div>


            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', '', 'class="form-control " id="name" required="required"'); ?>
            </div>

            <div class="form-group">
                        <?= lang('unit', 'unit_id'); ?>
							<?php
                        $pu[''] = lang('select') . ' ' . lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                        }
                        ?>
                        <?= form_dropdown('unit_id', $pu,  '', 'class="form-control tip" id="unit_id" required="required" style="width:100%;"'); ?>
			</div>

            <div class="form-group all">
                <?= lang('Measurement', 'Measurement'); ?>
                <?= form_input('measurement', '', 'class="form-control tip" id="measurement"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('The_manufacture_company', 'The_manufacture_company'); ?>
                <?= form_input('man_company', '', 'class="form-control tip" id="man_company"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('Country_of_origin', 'Country_of_origin'); ?>
                <?= form_input('country_origin', '', 'class="form-control tip" id="country_origin"'); ?>
            </div>

            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
		</div>
	    <div class="col-md-6">

			    <div class="form-group">
                    <?= lang('Fixed_assets_group', 'Fixed_assets_group'); ?>
                    <?php

                    foreach ($groups as $group) {
                        $wh[$group->id] = $group->name;
                    }
                    echo form_dropdown('group_id', $wh, (isset($_POST['group_id']) ? $_POST['group_id'] : ''), 'id="group_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Fixed_assets_group') . '" required="required" style="width:100%;" '); ?>
                </div>

                <div class="form-group">
                    <?= lang('Location', 'Location'); ?>
                    <?= form_input('location', '', 'class="form-control " id="location" '); ?>
                </div>

				<div class="form-group">
                    <?= lang('Color', 'Color'); ?>
                    <?= form_input('color', '', 'class="form-control " id="color" '); ?>
                </div>

                <div class="form-group">
                    <?= lang('Quality', 'Quality'); ?>
                    <?= form_input('quality', '', 'class="form-control " id="quality" '); ?>
                </div>

                <div class="form-group">
                    <?= lang('Style', 'Style'); ?>
                    <?= form_input('style', '', 'class="form-control " id="style" '); ?>
                </div>

				<div class="form-group">
                    <?= lang('Status', 'Status'); ?>
                    <?php

                    $status_options =  [1 => lang('new'), 2 => lang('Old')];
                    echo form_dropdown('status', $status_options, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Status') . '" required="required" style="width:100%;" '); ?>
                </div>

                <div class="form-group all">
                            <?= lang('status', 'active') ?>
                            <?php
                            $tm = [ '0' => lang('inactive'),'1' => lang('active')];
                            echo form_dropdown('active', $tm, (isset($_POST['active']) ? $_POST['active'] : 1), 'class="form-control select" id="active" placeholder="' . lang('select') . ' ' . lang('status') . '" required="required" style="width:100%"'); ?>
                        </div>



		</div>
	</div>


			<div class="form-group all">
                <?= lang('Specifications', 'Specifications'); ?>
                <?= form_textarea('specifications', '', 'class="form-control content" id="specifications"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_fixed_asset', lang('Add_fixed_asset'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>

<script>
    $(document).ready(function() {

        $('.content').richText();
    });
</script>
