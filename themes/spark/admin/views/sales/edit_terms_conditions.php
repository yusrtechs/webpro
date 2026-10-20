<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
        <script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_terms_and_conditions'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('sales/edit_terms_conditions/' . $terms->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row" >
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Name', 'name'); ?>
                        <?php echo form_input('name', $terms->name, 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                                <?= lang('Status', 'status'); ?>
                                <?php
                                $opt = [1 => lang('active'), 0 => lang('inactive')];
                                echo form_dropdown('status', $opt, $terms->status, 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('description', 'description'); ?>
                        <?php echo form_textarea('description', $terms->description, 'class="form-control content" id="description"'); ?>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_terms_conditions', lang('Edit_terms_and_conditions'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {

        $('.content').richText();
    });
</script>

