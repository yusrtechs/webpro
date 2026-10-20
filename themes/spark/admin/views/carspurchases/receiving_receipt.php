<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}

@media print {
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
				
				
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
            <div class="col-xs-6 pull-left" style=" margin-bottom:10px;"> 
					<span valign="top"><h1  style=" font-size: 25px;">Receiving Receipt / إيصال إستلام</h1><span>
						
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
                    <div class="col-xs-5">
                    <p class="bold">
                       
					   <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
                       
                        <?= lang('status'); ?>: <?= $inv->status=="returned" ? lang('Notice_creditor') : lang($inv->status); ?><br>
                    </p>
                    </div>
                    <div class="col-xs-7 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <!--<?= $this->sma->qrcode('link', urlencode(admin_url('carspurchases/view/' . $inv->id)), 2); ?>-->
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-6">
                    <?php echo $this->lang->line('from'); ?>:
                    <h2 style="margin-top:10px;"><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></h2>
                    <?= $supplier->company                              && $supplier->company != '-' ? '' : 'Attn: ' . $supplier->name ?>

                    
                </div>
                <div class="col-xs-6">
                    <?php echo $this->lang->line('to'); ?>:<br/>
                    <h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>
                    <!--<?= $warehouse->name ?>-->

                   
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table">

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th><?= lang('no_sales'); ?></th>
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th class="col-xs-10"><?= lang('product_name_sales'); ?></th>
                        <th><?= lang('product_unit_sales'); ?></th>
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <?php
                                echo '<th>' . lang('quantity_sales') . '</th>';
								echo '<th class="col-xs-2">' . lang('Receiving_purchases') . '</th>';
                            
                        
						
						?>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r     = 1;
                    $tax_summary = [];
					$tquantity=0;
					$tquantity_received=0;
					
                    foreach ($rows as $row):
					if($row->quantity_received==0)
                    $tquantity=$tquantity+$row->quantity;
                    else
					$tquantity=$tquantity+$row->quantity_received;
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <!--<td style="vertical-align:middle;">
                                <?= $row->product_code?>
                            </td>-->
							<td style="vertical-align:middle;">
                                <?= lang('code'). ':' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) {
                        ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
                    } ?>
                            <?php
							    $product_unit_code="";
							    $quantity=0;
							    $quantity=$row->quantity;
								$product_unit_code=$row->product_unit_code;
								$quantity_received=0;
							    $quantity_received=$row->quantity_received;
								$real_unit_cost=0;
							    $real_unit_cost=$row->real_unit_cost;
								if($inv->status=="partial" || $inv->status=="received"){
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$product_unit_code=$unit->name;
										if($unit->operator=='*'){
											$quantity=$quantity/$unit->operation_value;
											$quantity_received=$quantity_received/$unit->operation_value;
											$real_unit_cost=$real_unit_cost*$unit->operation_value;
										}
										if($unit->operator=='/'){
											$quantity=$quantity*$unit->operation_value;
											$quantity_received=$quantity_received*$unit->operation_value;
											$real_unit_cost=$real_unit_cost/$unit->operation_value;
										}	
									}
								}
								}
								
							?>
							
							
							<td style="width: 80px; text-align:center; vertical-align:middle;"><?= $product_unit_code?></td>
                            <?php
                            	/*$product_unit_code="";
								$product_unit_code=$row->product_unit_code;
								$product_unit_base='';
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id)
										$product_unit_base=$unit->base_unit;
								}
								if($product_unit_base){
								foreach ($units as $unit){
									if($unit->id == $product_unit_base)
										$product_unit_code=$unit->name;
								}
									
								}*/
								
								
								
								
								
                                echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity($quantity_received) . '</td>';
                            ?>
							<td style="min-width: 80px; text-align:center; vertical-align:middle;"><?= $row->receiving_date ? $this->sma->hrld($row->receiving_date) : ''; ?></td>
                            
                        </tr>
                        <?php
                        $r++;
						$tquantity_received=$tquantity_received+$quantity_received;
                    endforeach;
                    ?>
					<tr>
                            <td colspan="3" style="text-align:center; width:40px; vertical-align:middle;"><b>Total Quantity / اجمالي الكمية</b></td>
							<td style=" font-weight:bold;"><?= $this->sma->formatDecimal(abs($tquantity_received),2); ?></td>
							<td style=" font-weight:bold;"></td>
						
                    </tr>
                    </tbody>
					
                    
                </table>
            </div>

            
            <?php
			$tax_active_rate=0;
			if($inv->order_tax>0)
			$tax_active_rate=$inv->tax_active_rate;
				
			?>
			<div class="row inv-details">
			
				<div class="clearfix"></div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Receiver_Signature'); ?>:<br>  </p>
                </div>
				<div class="col-xs-4">
                    <p></p>
                </div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p></p>
                </div>
            	
				<div class="clearfix"></div>
			    <div class="col-xs-12">
                    <?php
                        if ($inv->note || $inv->note != '') {
                            ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang('note'); ?>:</p>
                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        ?>
                </div>
				<div class="clearfix"></div>

                
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
