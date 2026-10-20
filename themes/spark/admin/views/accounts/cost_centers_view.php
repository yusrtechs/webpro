<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
				
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $cost_details->costID. ' - ' .$cost_details->costName. ' / ' .$cost_details->costNameEn; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('Cost_center_No'); ?></strong></td>
                        <td><?= $cost_details->costID; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Cost_center_name'); ?></strong></td>
                        <td><?= $cost_details->costName; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('English_name'); ?></strong></td>
                        <td><?= $cost_details->costNameEn; ?></strong></td>
                    </tr>
                   
                    
                   
                    <?php
					$cost_type="";
					if($cost_details->master_cost=="0")
						$cost_type=lang('Detailed_cost_center');
					if($cost_details->master_cost=="1")
						$cost_type=lang('Master_cost_center');
					?>
					<tr>
                        <td rowspan="3"><strong><?= lang('Cost_center_type'); ?></strong></td>
                        <td><?= $cost_type; ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                
                <?php if ($Owner || $Admin) {
                ?>
                    <a href="<?=admin_url('accounts/edit_cost_center/' . $cost_details->costID); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('Edit_cost_center'); ?></a>
					<?php
					} ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
