<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('update_status'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/update_status/' . $inv->vehicle_id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= lang('Details_of_Vehicle'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed table-striped table-borderless" style="margin-bottom:0;">
                        <tbody>
                            <tr>
                                <td><?= lang('Showroom'); ?></td>
                                <td><?php echo $this->cars_model->getShowroomByID($inv->showroom_id)->name ;?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Chesis_no'); ?></td>
                                <td><?= $inv->chesis_no ;?></td>
                            </tr>
                            <tr>
                                <td ><?= lang('Car_Type'); ?></td>
                                <td><?= $inv->name;?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Status'); ?></td>
                                <td><strong><?= lang($inv->status); ?></strong></td>
                            </tr>
                            <tr>
                                <td><?= lang('Car_status'); ?></td>
                                <td><?= lang($inv->car_status); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($returned) {
            ?>

            <?php
        } else {
            ?>
            <div class="form-group">
                <?= lang('Status', 'status'); ?>
                <?php
                $opts = ['available' => lang('available'), 'exit' => lang('exit')]; ?>
                <?= form_dropdown('status', $opts, $inv->status, 'class="form-control" id="status" required="required" style="width:100%;"'); ?>
            </div>

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
