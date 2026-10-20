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

	.pa td{
border: 1px solid gray;
    }		

@media print {
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
			
			#myModal .modal-content {
            display: none !important;
        }
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
<div class="modal-dialog modal-lg no-modal-header pa">
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
            <div class="col-xs-6 pull-left" style=" margin-bottom:10px;"> 
					<span valign="top"><h1  style=" font-size: 25px;">Payment Application / طلب الدفع</h1><span>
						
			</div>
					
             <div class="col-xs-6" >
					
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
                <div class="row bold">
                    <div class="col-xs-4">
                    <p class="bold">
                        <?= $inv->status=="ordered" ? lang('date') : lang('Inv_date'); ?>: <?= $this->sma->hrsd($inv->date); ?><br>
                       
						<?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
                       
                        <?= lang('status'); ?>: <?= $inv->status=="returned" ? lang('Notice_creditor') : lang($inv->status); ?><br>
                        <?= lang('payment_status'); ?>: <?= lang($inv->payment_status); ?>
                    </p>
                    </div>
					
					
					
					<div class="col-xs-4">
                    <h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>
                    <!--<?= $warehouse->name ?>-->

                    <?php
                    /*$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;*/
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ': ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?>
                    </div>
					
					
					
                    <div class="col-xs-4 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <!--<?= $this->sma->qrcode('link', urlencode(admin_url('carspurchases/view/' . $inv->id)), 2); ?>-->
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

           

            <div>
                <table class="table">

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th colspan="4" ><?= lang('Payment_application'); ?></th>
                       
                    </tr>

                    </thead>

                    <tbody>
					
					
					<tr>
                            <td><?= lang('code'); ?></td>
							<td style="vertical-align:middle;"> <?= $payment_application->code; ?></td>
                            <td><?= lang('date'); ?></td>
							<td style="vertical-align:middle;"> <?= $this->sma->hrsd($payment_application->created_date); ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('to'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('No'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $inv->id; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('BANK_NAME'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $supplier->cf1; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('IBAN'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $supplier->cf2; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('address'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $supplier->address . '<br />' . $supplier->city . ' ' . $supplier->postal_code . ' ' . $supplier->state . ' ' . $supplier->country; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('Purpose'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $this->sma->decode_html($payment_application->purpose); ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('Project'); ?></td>
							<td colspan="3" style="vertical-align:middle;"> <?= $warehouse->name; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('Amount'); ?></td>
							<td style="vertical-align:middle;"> <?= $this->sma->formatMoney($inv->grand_total); ?></td>
                            <td><?= lang('Payment_schedule'); ?></td>
							<td style="vertical-align:middle;"> <?= $payment_application->payment_schedule; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('Applied_amount'); ?></td>
							<td style="vertical-align:middle;"> <?= $this->sma->formatMoney($payment_application->applied_amount); ?></td>
                            <td><?= lang('Applied_schedule'); ?></td>
							<td style="vertical-align:middle;"> <?= $payment_application->applied_schedule; ?></td>
                            
                    </tr>
                    <tr>
                            <td><?= lang('Remarks'); ?></td>
							<td style="vertical-align:middle;"> <?= $payment_application->remarks; ?></td>
                            <td><?= lang('Expected_payment_date'); ?></td>
							<td style="vertical-align:middle;"> <?= $this->sma->hrsd($payment_application->expected_payment_date); ?></td>
                            
                    </tr>
                    <tr class="thcol">
                        <td colspan="4" style="vertical-align:middle;"><?= lang('Cash_in_advance').' ( '.lang('Cashier_Fill').' ) '; ?></td>
                       
                    </tr>
					<tr>
                            <td><?= lang('No'); ?></td>
							<td><?= lang('date'); ?></td>
							<td><?= lang('Purpose'); ?></td>
							<td><?= lang('Amount'); ?></td>
							
                    </tr>
                    <tr>
                            <td>1</td>
							<td></td>
							<td></td>
							<td></td>
							
                    </tr>
                    <tr>
                            <td>2</td>
							<td></td>
							<td></td>
							<td></td>
							
                    </tr>
                    <tr   class="thcol">
                        <td colspan="3" style="vertical-align:middle;"><?= lang('Total_advance'); ?></td>
                    		<td></td>
					   
                    </tr>
					<tr   class="thcol">
                        <td  colspan="3" style="vertical-align:middle;"><?= lang('Amount_payable').' ( '.lang('Cashier_Fill').' ) '; ?></td>
                    		<td></td>
					   
                    </tr>
					
					
    

                  
                    </tbody>
                    
                </table>
            </div>

            
			<div class="row inv-details">
			
				<div class="clearfix"></div>
				<div class="col-xs-3" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Applied_By'); ?>:<br>  </p>
                </div>
				<div class="col-xs-3" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Finance_Manager'); ?>:<br>  </p>
                </div>
				<div class="col-xs-3" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Factory_Manager'); ?>:<br>  </p>
                </div>
				<div class="col-xs-3" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('General_Manager'); ?>:<br>  </p>
                </div>
				
				
            </div>
			
			<table class="table">

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th style="vertical-align:middle;"><?= lang('SR'); ?></th>
                        <th style="vertical-align:middle;">1</th>
                        <th style="vertical-align:middle;">2</th>
                        <th style="vertical-align:middle;">3</th>
                        <th style="vertical-align:middle;">4</th>
                        <th style="vertical-align:middle;">5</th>
                        <th style="vertical-align:middle;">6</th>
                        <th style="vertical-align:middle;">7</th>
                        <th style="vertical-align:middle;"><?= lang('Total'); ?></th>
                       
                    </tr>

                    </thead>

                    <tbody>
				<tr   >
                        <td   style="vertical-align:middle;">INV OR PO</td>
                    	<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						
					   
                    </tr>
				<tr   >
                        <td   style="vertical-align:middle;"><?= lang('Remarks'); ?></td>
                    	<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						
					   
                    </tr>
					
					</tbody>
                    
                </table>
            </div>
			
			</div>
            </div>
			
            
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
</script>
