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
            <h4 class="modal-title" id="myModalLabel"><?= $accounting_period_details->period_code. ' ' .lang('From').' '.$this->sma->hrsd($accounting_period_details->period_from_date). ' ' .lang('to').' '.$this->sma->hrsd($accounting_period_details->period_to_date); ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('Year'); ?></strong></td>
                        <td><?= $accounting_period_details->period_year; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('code'); ?></strong></td>
                        <td><?= $accounting_period_details->period_code; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Start_Period'); ?></strong></td>
                        <td><?= $this->sma->hrsd($accounting_period_details->period_from_date); ?></strong></td>
                    </tr>
                    
                    
                    <tr>
                        <td><strong><?= lang('End_Period'); ?></strong></td>
                        <td><?= $this->sma->hrsd($accounting_period_details->period_to_date); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Status'); ?></strong></td>
						
                        <td><?= lang($accounting_period_details->period_status== 0 ? 'Close' : ($accounting_period_details->period_status== 1 ? 'Open' : 'Pending' )); ?></strong></td>
                    </tr>
                    
					
                    
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                
                
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
