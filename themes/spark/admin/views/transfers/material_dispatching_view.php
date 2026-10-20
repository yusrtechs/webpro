<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.pa th {
	font-size: 16px;
}

.thcol {
	font-size: 16px;
    background-color: #dee2e6;
    color: #212529;
    text-align: center;
	
}

	.pa td,.no-modal-header tr{
border: 1px solid gray;
    }		
	.pa td{
border: 1px solid gray;
font-weight: bold;
    }	
.material h3{
font-weight: bold;
}	

@media print {
                /*h1 {padding:1px;}*/
				
			
.thcol {
	font-size: 16px;
    background-color: #dee2e6;
    color: #212529;
    text-align: center;
	-webkit-print-color-adjust:exact !important;
}
	.pa td{
border: 1px solid gray !important;
-webkit-print-color-adjust:exact !important;
    }		
				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}
		td {
    border: 1px solid #ddd;
    
}

.from{margin-bottom:15px;}		
  
  
  div.divFooter {
    position: fixed;
    bottom: 0;
  } 


      table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   
	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt; }
                
            }
			
			
   		
 .divbg:after {
  content: '';
  width: 100%;
  height: 100%;
  position: absolute;
  background-color: #2A3F54;
  top: 0;
  right: 0;
  -webkit-print-color-adjust:exact;
}

.col-xs-1 {
    width: 4.33333333%;
}
</style>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
          <div class="row bold">
                    <div class="col-xs-12 no-print" >
                
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
			</div>
           </div>
		    <div class="row bold">
            <div class="col-xs-8 pull-left" style=" margin-bottom:10px;"> 
					<span valign="top"><h1  style=" font-size: 20px;"> Material Dispatching Form / نموذج إرسال المواد </h1><span>
						
			</div>
					
             <div class="col-xs-4" >
					
            <?php 
			if ($logo) 
			{
    ?>
                <div class="pull-right" style="margin-top:10px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
            <?php
} ?>
			</div>
			</div>
			
			<div class="row">
			<div class="col-xs-12">
			
			
            <div class="well well-sm">
                <div class="row bold" style="-webkit-print-color-adjust:exact;">
                    <div class="col-xs-6"><?= lang('date'); ?>: <?= $this->sma->hrld($transfer->date); ?>
                        <br><?= lang('ref'); ?>: <?= $transfer->transfer_no; ?>
                        <br><?= lang('status'); ?>: <?= lang($transfer->status); ?>
                    </div>
                    <div class="col-xs-6 pull-right text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($transfer->transfer_no) . '/code128/74/0/1'); ?>" alt="<?= $transfer->transfer_no; ?>" class="bcimg" />
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="row material">
                <div class="col-xs-6">
                    
                    <h3 style="margin-top:10px;"><?= lang('Dispatching_From'); ?> : <?= $from_warehouse->name; ?></h3>
                    <h3 style="margin-top:10px;"><?= lang('Requested_by'); ?> : <?= $transfer->requested_by; ?></h3>
                </div>
                <div class="col-xs-6">
                    <h3 style="margin-top:10px;"><?= lang('Dispatching_To'); ?> : <?= $to_warehouse->name; ?></h3>
                    <h3 style="margin-top:10px;"><?= lang('Project'); ?> : <?= $transfer->project_area; ?></h3>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action order-table">
                    <thead>
                        <tr style="-webkit-print-color-adjust:exact;">
                        <th style="text-align:center; vertical-align:middle;"><?= lang('no.'); ?></th>
                            <th style="vertical-align:middle;"><?= lang('description'); ?></th>
                        
                            <th style="text-align:center; vertical-align:middle;"><?= lang('quantity'); ?></th>
                            <th style="text-align:center; vertical-align:middle;"><?= lang('unit'); ?></th>
                     </thead>

                    <tbody>
                        <?php $r = 1;
                        foreach ($rows as $row): ?>
                        <tr>
                            <td style="text-align:center; width:25px;"><?= $r; ?></td>
                            <td style="text-align:left;">
                                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                            </td>
                            <td style="text-align:center;"><?= $this->sma->formatQuantity($row->unit_quantity); ?></td>
                            <td style="text-align:right; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                           </tr>
                        <?php $r++;
                        endforeach; ?>
                    </tbody>
                   
                </table>
            </div>
			
			<div class="row">
                <div class="col-xs-12">
                    <?php if ($transfer->note || $transfer->note != '') {
                            ?>
                    <div class="well well-sm">
                        <p class="bold"><?= lang('note'); ?>:</p>

                        <div><?= $this->sma->decode_html($transfer->note); ?></div>
                    </div>
                    <?php
                        } ?>
                </div>
                
            </div>
			    <div class="clearfix"></div>
                
			
			<div class="pa">
                <table class="table">

                    
                    <tbody>
					
					
					<tr>
                            <td style="width:50%;"><?= lang('Driver_name'); ?> : <?= $transfer->driver_name; ?></td>
							<td style="width:50%;"><?= lang('Approved_by'); ?> : <?= $transfer->approved_by; ?></td>
					</tr>
                    <tr>
                            <td style="width:50%;"><?= lang('Driver_mobile'); ?> : <?= $transfer->driver_mobile_no; ?></td>
							<td style="width:50%;"><?= lang('Receiver_name'); ?> : <?= $transfer->receiver_name; ?></td>
					</tr>
                    <tr>
                            <td style="width:50%;"><?= lang('Vehcile_no'); ?> : <?= $transfer->driver_vehcile_no; ?></td>
							<td style="width:50%;"><?= lang('Receiver_no'); ?> : <?= $transfer->receiver_no; ?></td>
					</tr>
                    <tr>
                            <td style="width:50%;"><?= lang('Iqama_no'); ?> : <?= $transfer->driver_iqama_no; ?></td>
							<td style="width:50%;"><?= $transfer->receiver_sign==1 ? lang('Receiver_sign').' : ' : ''; ?></td>
					</tr>
                    
    

                  
                    </tbody>
                    
                </table>
            </div>

                <div class="clearfix"></div>
                
			
			<div class="pa">
                <table class="table">

                    
                    <tbody>
					
					
					<tr>
                            <td style="width:50%;"><?= lang('Signature'); ?> : </td>
							<td style="width:50%;"><?= lang('Store_Incharge_Signature'); ?> : </td>
					</tr>
                   
                    
    

                  
                    </tbody>
                    
                </table>
            </div>

            
           
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
</script>
