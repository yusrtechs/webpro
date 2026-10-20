<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('update_status'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('accounts/update_status/' . $inv->entryID, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= lang('Entry_details'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed table-striped table-borderless" style="margin-bottom:0;">
                        <tbody>
                            <tr>
                                <td><?= lang('reference_no'); ?></td>
                                <td><?= $inv->reference_no; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Entry_number'); ?></td>
                                <td><?= $inv->entryID; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Entry_type'); ?></td>
                                <td><?= lang($inv->entryType); ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('approve'); ?></td>
                                <td><strong><?= $inv->entryStatus==1 ? lang('approved') : lang('unapproved'); ?></strong></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($returned) {
            ?>
                <h4><?= lang('sale_x_action'); ?></h4>
            <?php
        } else {
            ?>
            <div class="form-group">
                <?= lang('approve', 'approve'); ?>
                <?php
                $opts = ['1' => lang('approved'), '0' => lang('unapproved')];
				//$opts = ['1' => lang('approved')];

				?>
                <?= form_dropdown('entryStatus', $opts, $inv->entryStatus, 'class="form-control" id="entryStatus" required="required" style="width:100%;"'); ?>
            </div>

            <?php if ($inv->entryType=='Adjusting') {
            ?>
            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('entryDescription', (isset($_POST['entryDescription']) ? $_POST['entryDescription'] : $this->sma->decode_html(lang($inv->entryDescription))), 'class="form-control" id="entryDescription"'); ?>
            </div>
            <?php }
            ?>
            <?php
        } ?>

        </div>
        <?php if (!$returned) {
            ?>
        <div class="modal-footer">
            <?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
        </div>
        <?php
        } ?>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
