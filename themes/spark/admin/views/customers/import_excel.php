<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('import_by_excel'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('customers/import_excel', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="well well-small">
                <a href="<?php echo base_url(); ?>assets/csv/sample_customer.xlsx" class="btn btn-primary pull-right"><i
                        class="fa fa-download"></i> <?= lang('download_sample_file') ?></a>
                <span class="text-warning"><?= lang('xlsx1'); ?></span><br/><?= lang('xlsx2'); ?> <span class="text-info">(<?= lang('company') . ', ' . lang('name') . ', ' . lang('email') . ', ' . lang('phone') . ', ' . lang('address') . ', ' . lang('city'); ?>
                    ,  <?= lang('state') . ', ' . lang('postal_code') . ', ' . lang('country') . ', ' . lang('vat_no') . ', ' . lang('gst_no') . ', ' . lang('ccf1') . ', ' . lang('ccf2') . ', ' . lang('ccf3') . ', ' . lang('ccf4') . ', ' . lang('ccf5') . ', ' . lang('ccf6'); ?>
                    )</span> <?= lang('xlsx3'); ?><br>
                <p class="text-success"><?= lang('first_6_required'); ?></p>
                <span class="text-primary"><?= lang('csv_update_tip'); ?></span>
            </div>
            <div class="form-group">
                <label for="xlsx_file"><?= lang('upload_file'); ?></label>
                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="xlsx_file" class="form-control file" data-show-upload="false" data-show-preview="false" id="xlsx_file" required="required"/>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('import', lang('import'), 'class="btn btn-primary"'); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
