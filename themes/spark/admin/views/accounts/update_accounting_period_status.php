<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('update_status'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('accounts/update_accounting_period_status/' . $inv->id, $attrib); ?>
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
                                <td><?= lang('Year'); ?></td>
                                <td><?= $inv->period_year; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('code'); ?></td>
                                <td><?= $inv->period_code; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Start_Period'); ?></td>
                                <td><?= $this->sma->hrsd($inv->period_from_date); ?></strong></td>
							</tr>
                            <tr>
                                <td><?= lang('End_Period'); ?></td>
                                <td><?= $this->sma->hrsd($inv->period_to_date); ?></strong></td>
							</tr>
                            <tr>
                                <td><?= lang('Status'); ?></td>
                                <td><?= lang($inv->period_status== 0 ? 'Close' : ($inv->period_status== 1 ? 'Open' : 'Pending' )); ?></strong></td>
							</tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <?php 
			if ($Owner || $Admin) {
            ?>
            <div class="form-group">
                <?= lang('Status', 'Status'); ?>
                <?php
                $opts = ['1' => lang('Open'), '0' => lang('Close'), '2' => lang('Pending')]; 
				//$opts = ['1' => lang('approved')]; 
				
				?>
                <?= form_dropdown('period_status', $opts, $inv->period_status, 'class="form-control" id="period_status" required="required" style="width:100%;"'); ?>
            </div>

            <?php
			}
        ?>

        </div>
        <?php 
		if ($Owner || $Admin) {
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
