<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('update_prices_excel') . ' (' . $group->name . ')'; ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('sales/update_prices_excel/' . $group->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-12">
                    <div class="well well-small">
                        <a href="<?php echo base_url(); ?>assets/csv/sample_product_price.xlsx" class="btn btn-primary pull-right">
                            <i class="fa fa-download"></i> <?= lang('download_sample_file') ?>
                        </a>
                        <span class="text-warning"><?= lang('xlsx1'); ?></span><br/><?= lang('xlsx2'); ?> 
                        <span class="text-info">(<?= lang('product_code') . ', ' . lang('product_price') . ', ' . lang('unit') . ', ' . lang('Barcode'); ?>)</span>
                        <?= lang('xlsx3'); ?>
                    </div>

                    <div class="form-group">
                         <label for="xlsx_file"><?= lang('upload_file'); ?></label>
                         <input type="file" data-browse-label="<?= lang('browse'); ?>" name="xlsx_file" class="form-control file" data-show-upload="false" data-show-preview="false" id="xlsx_file" required="required"/>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('update_price', lang('update_price'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>