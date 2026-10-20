<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}
		
			?>

<style>

.qrimg{
	padding-bottom:20px;
}

    table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }
	
	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;}

   

@media print {
	
	.modal-content th,td {
  font-size: 10px;
}
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
				.well1 {margin-top:-15px;padding:1px;}
				
				.table td, .table th {padding: 0.2rem !important;}
				
				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}
		td {
    border: 1px solid #ddd;
    
}

.from,.to{margin-bottom:15px;margin-top:15px !important;}		
  
  
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
  height: 99%;
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
<div class="modal-dialog modal-lg no-modal-header ">
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
             <button type="button" 
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">
                                
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                               
                            </a>
                           
            </button>
			</div>
           </div>
	<div id="pr_pdf">
	
		   <div class="row bold" style="border:none;border-bottom:solid windowtext 1.5pt !important;padding:0in 0in 1.0pt 0in;margin-left:0in;margin-right:-.05in;">
		   
		   
		   
            <div class="col-xs-4 pull-left"> 
					<?php
					if($customer->vat_no!=""){
					?>	
				    <span valign="top"><h1  style=" font-size: 20px;display: inline-block;"> فاتورة&nbsp;ضريبية&nbsp;/&nbsp;Tax&nbsp;Invoice</h1><span></br>
					<?php
					}else{
					?>	
				    <span valign="top"><h1  style=" font-size: 20px;display: inline-block;">Simplified&nbsp;Tax&nbsp;Invoice&nbsp;/&nbsp;فاتورة&nbsp;ضريبية&nbsp;مبسطة</h1><span></br>
					<?php
					}
					?>	
				    <b><span><?= lang('Inv_date'); ?> : <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang('Invoice_No'); ?> : <?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".$inv->id : $inv->id; ?><br>
                        <?= lang('reference_no'); ?> : <?= $inv->reference_no; ?><br>
					<?php if ($Settings->IsConstruction && !empty($inv->manual_payment)) { ?>
                        <?= lang('Contract_Number'); ?> : <?= $inv->manual_payment; ?><br>
						<?php } ?>
                    <!--<?php if (!empty($inv->supply_date)) { ?>
                        <?= lang('Supply_date'); ?> : <?= $this->sma->hrld($inv->supply_date); ?><br>
						<?php } ?>-->
                        <?php if (!empty($inv->due_date)) { ?>
                        <?= lang('Due_date'); ?> : <?= $this->sma->hrsd($inv->due_date); ?><br>
						<?php } ?>
                        <?php if (!empty($inv->return_sale_ref)) {
        echo lang('return_ref') . ' : ' . $inv->return_sale_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
                        <?= lang('sale_status'); ?> : <?= $inv->sale_status=="returned" ? lang('Notice_creditor') : lang($inv->sale_status); ?> - <?= lang($inv->payment_status); ?><br>
                        
						
                        <?php 
						/*
						if ($inv->payment_status != 'paid') {
        echo '<br>' . lang('due_date') . ': ' . $this->sma->hrsd($inv->due_date); 
		
    } 
	*/
	?>  </span></b>
						
			</div>
					
             <div class="col-xs-8" >
					
            <?php 
			//if ($logo) 
			{
    ?>
                <div class="pull-right" style="margin-top:10px;">
                    
						 <?php
					$company = $this->session->userdata('company');
					?>
                    <div class="text-right order_barcodes">
					<img style="margin-left:20px;margin-bottom:2px;" src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>"><br/>
						 
		
                        <!--<?= $this->sma->qrcode('link', urlencode(site_url('SalesDetails/view/' . $company . '/' . $inv->hash )), 2); ?>-->
                        <?php
						$QR=$this->sma->barcodeZATCA($Settings->site_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
						?>
						<?= $this->sma->qrcode('', $QR, 2); ?>
						<?php 
						//if($inv->sale_status == 'returned' || $inv->sale_status == 'completed')
						//$this->sma->xmlInvoice($Settings->site_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2),$inv->id,$rows,$warehouse,$customer,$inv->uuid,$QR,$this->sma->formatDecimal($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid ))); 
						//$this->sma->barcodeZATCA($Settings->site_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2))
						?>
						<img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg"/>
						
						
                    </div>
                </div>
            <?php
            } ?>
			</div>
			</div>
			
			
			
			<div class="row">
			<!--<?php 
			if ($Settings->IsServices) {
				?>
			<div class="col-xs-1">
			<?php
            }else{ ?>
			<div class="col-xs-1 divbg">
			
			<?php
            }?>
				
			</div>-->
			<div class="col-xs-12">
			


				
           <!-- <div class="well well-sm">
                <div class="row bold"  >
                    <div class="col-xs-4">
                    <p class="bold">
                        <?= lang('Inv_date'); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang('Invoice_No'); ?>: <?= $inv->id; ?><br>
                        <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
						<?php if ($Settings->IsConstruction && !empty($inv->manual_payment)) { ?>
                        <?= lang('Contract_Number'); ?>: <?= $inv->manual_payment; ?><br>
						<?php } ?>
                    <?php if (!empty($inv->supply_date)) { ?>
                        <?= lang('Supply_date'); ?>: <?= $this->sma->hrld($inv->supply_date); ?><br>
						<?php } ?>
                        <?php if (!empty($inv->due_date)) { ?>
                        <?= lang('Due_date'); ?>: <?= $this->sma->hrsd($inv->due_date); ?><br>
						<?php } ?>
                    <?php if (!empty($inv->return_sale_ref)) {
        echo lang('return_ref') . ': ' . $inv->return_sale_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
                        <?= lang('sale_status'); ?>: <?= lang($inv->sale_status); ?><br>
                        <?= lang('payment_status'); ?>: <?= lang($inv->payment_status); ?>
						
                        <?php 
						/*
						if ($inv->payment_status != 'paid') {
        echo '<br>' . lang('due_date') . ': ' . $this->sma->hrsd($inv->due_date); 
		
    } 
	*/
	?>
                    </p>
                    </div>
					<?php
					$company = $this->session->userdata('company');
					?>
                    <div class="col-xs-8 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(site_url('SalesDetails/view/' . $company . '/' . $inv->hash )), 2); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>-->

            <div class="row" style="margin-bottom:15px;margin-top:15px;">

                <?php 
				//if ($Settings->invoice_view == 1) 
				{
        ?>
                    <!--<div class="col-xs-4 text-center">
                    	
                    </div>
                    <div class="col-xs-4 text-center">
                        <h1 style="min-height: 20px;padding: 10px;margin-top: -5px;background-color: #f5f5f5;border: 1px solid #e3e3e3;border-radius: 20px;-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);    box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%); font-size: 25px;">Tax Invoice <br>فاتوره ضريبية</h1>
						
                    </div>
                    <div class="col-xs-4 text-center">
                    	
                    </div>-->
                <?php
    } ?>

                

                <div class="col-xs-6 from">
                    <b><?php echo $this->lang->line('from'); ?> : <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>
                    
					<?= $warehouse->name ?>-->

                    <?php
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ' : ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? lang('CR_Number') . ' : ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : '');
                    ?>
                </div>
				
				
				<div class="col-xs-6 to">
                   <b> <?php echo $this->lang->line('to'); ?> : <?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></b><br/>
                    <!--<h2 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>-->
                    <?= $customer->company                              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>

                    <?php
                    echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country . '<br>';

                    /*echo '<p>';

                    if ($customer->vat_no != '-' && $customer->vat_no != '') {
                        echo '<br>' . lang('vat_no') . ': ' . $customer->vat_no;
                    }
                    if ($customer->gst_no != '-' && $customer->gst_no != '') {
                        echo '<br>' . lang('gst_no') . ': ' . $customer->gst_no;
                    }
                    if ($customer->cf1 != '-' && $customer->cf1 != '') {
                        echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                    }
                    if ($customer->cf2 != '-' && $customer->cf2 != '') {
                        echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                    }
                    if ($customer->cf3 != '-' && $customer->cf3 != '') {
                        echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                    }
                    if ($customer->cf4 != '-' && $customer->cf4 != '') {
                        echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                    }
                    if ($customer->cf5 != '-' && $customer->cf5 != '') {
                        echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                    }
                    if ($customer->cf6 != '-' && $customer->cf6 != '') {
                        echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                    }

                    echo '</p>';*/
                    echo ($customer->vat_no ? lang('VAT_Regesteration_Number') . ' : ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR_Number') . ' : ' . $customer->cr_no . '<br>' : '<br>') ;
					?>
					<?php if ($Settings->IsConstruction && (!empty($customer->cf1) || !empty($inv->po_no) )) { ?>
					<?php if ($Settings->user_language == 'arabic') {
						?>
	
                       <font dir="ltr"> <?= $inv->po_no ? $inv->po_no : $customer->cf1; ?> : <?= lang('PO_Number'); ?></font><br>
						<?php }else{ ?>
                    	<font> <?= lang('PO_Number'); ?> : <?= $inv->po_no ? $inv->po_no : $customer->cf1; ?></font><br>
						<?php } ?>
                    	<?php } ?>
						<?php
                    echo lang('tel') . ' : ' . $customer->phone . '<br>' . lang('email') . ' : ' . $customer->email;
                    ?>
                </div>

            </div>
            <?php
                    $col = $Settings->indian_gst ? 9 : 8;
                    //if ($Settings->product_discount && $inv->product_discount != 0) {
                    //    $col++;
                    //}
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 2;
                    //} elseif ($Settings->product_discount && $inv->product_discount != 0) {
                    //    $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table pdftable" >
				

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th><?= lang('no_sales'); ?></th>
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th class="col-xs-12"><?=  $Settings->IsServices ? lang('service_name_sales') : lang('product_name_sales'); ?></th>
                        <th><?= lang('product_unit_sales'); ?></th>
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <th><?= lang('quantity_sales'); ?></th>
                       
                        <th class="col-xs-2"><?= lang('unit_price_sales'); ?></th>
						 <!--<th><?= lang('value'); ?></th>-->
                        <?php
						if($inv->order_type==1){
						//if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th class="col-xs-2">' . lang('discount_sales') . '</th>';
                        //}
						//echo '<th class="col-xs-2">' . lang('item_price_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
							 echo '<th class="col-xs-2">' . lang('tax_percent_sales') . '</th>';
                            echo '<th class="col-xs-2">' . lang('VAT_amount_sales') . '</th>';
                        }
						}
                        
                        ?>
                        <th><?= lang('total_price_sales'); ?></th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
					$tquantity=0;
					foreach ($rows as $row):
					if($row->quantity_received==0)
                    $tquantity=$tquantity+$row->quantity;
                    else
					$tquantity=$tquantity+$row->quantity_received;
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <!--<td style="vertical-align:middle;">
                                <?= $row->product_code ?>
                            </td>-->
							<td style="vertical-align:middle;">
							<?php 
							if($row->promo_free==1){
								?>
							<img src="<?= base_url() . 'assets/images/free.jpg' ?>" width="10%">
							<?php
							}
							?>
							<?php if ($Settings->show_product_code) {
							?>
                             <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?>
                               <?php
							} 
							if($row->details){
							$details= str_replace("<p>","",($row->details)."");
							$details= str_replace("</p>","",($details)."");
							}
						
							?>
							
								<?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? ' - ' . $row->second_name : ''; ?>
                                <?= $row->details ? ' - ' . $details : ''; ?>
                                <?= $row->cf1 ? ' - ' . $row->cf1. ' ' .$row->cf2. ' ' .$row->cf3 : ''; ?>
                            </td>
							<?php
								$base_unit_code=$row->base_unit_code;
								$product_unit_code=$row->product_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
										$product_unit_code=$unit->name;
									}
								}
								?>
							
							<td><?=$product_unit_code?></td>
                            <?php if ($Settings->indian_gst) {
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
							} ?>
							 	
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . ($inv->sale_status == 'returned' ? $base_unit_code : ''); ?></td>
                           
							<td style="text-align:right; width:100px;">
                                <!--<?= $row->unit_price != $row->real_unit_price && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_price) . '</del>' : ''; ?>
                                -->
								<?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?>
                            </td>
							<!--<td><?=($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity))?></td>-->
                            <?php
							if($inv->order_type==1){
							//if ($Settings->product_discount && $inv->product_discount != 0) {
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney($row->item_discount,' ') . '</td>';
                            //}
							// echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->net_unit_price * $row->quantity). '</td>';
                            
						
							if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                            }
                            echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->subtotal,' ') . '</td>';
                            
							}else{
							    echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatDecimal(($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity))*$row->unit_quantity) . '</td>';
                            }
                            ?>
                           <!-- <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td> -->
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
						$tquantity=$tquantity+$row->unit_quantity;
                        ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <!--<td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>-->
                                <td style="vertical-align:middle;">
                                    <?php if ($Settings->show_product_code) {
							?>
                             <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?>
                               <?php
							} 
							if($row->details){
							$details= str_replace("<p>","",($row->details)."");
							$details= str_replace("</p>","",($details)."");
							}
						
							?>
							
								<?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                            <?= $row->second_name ? ' - ' . $row->second_name : ''; ?>
                                    <?= $row->details ? ' - ' . $details : ''; ?>
                                    <?= $row->serial_no ? ' - ' . $row->serial_no : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) {
                            ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php
                        } ?>
                                <?php
								$base_unit_code=$row->base_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
									}
								}
								?>
								
								<td style="width: 80px; text-align:center; vertical-align:middle;"><?= $base_unit_code; ?></td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($row->quantity)); ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price,' '); ?></td>
                                <?php
                                if($inv->order_type==1){
								if ($Settings->tax1 && $inv->product_tax > 0) 
								{
                                //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount)) . '</td>';
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . abs($row->tax) . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';
                            
								}else{
                        //if ($Settings->product_discount && $inv->product_discount != 0) {
                          //  echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $this->sma->formatMoney($row->discount) . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                             echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                           }
						   	  echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->subtotal),' ') . '</td>';
                           
								}else{
									
									  echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->unit_price) * $this->sma->formatQuantity(abs($row->quantity)),' ') . '</td>';
                           
								}
						   
						?>
                                <!--<td style="text-align:right; width:120px;"><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>-->
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <!--<tfoot>
                    <?php if ($inv->grand_total != $inv->total) {
                        ?>
                        <tr>
                            <td colspan="<?= $tcol-2; ?>"
                                style="text-align:right; padding-right:10px;"><?= lang('total'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
							
                            <?php
                            
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount + $return_sale->product_discount) : $inv->product_discount) . '</td>';
                        } ?>
						<td></td>	
						<td></td>	
						<?php 
						if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax + $return_sale->product_tax) : $inv->product_tax) . '</td>';
                            }
						?>
                            <td style="text-align:right; padding-right:10px;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax) + ($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php
                    } ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('return_total') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('return_surcharge') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->shipping - ($return_sale && $return_sale->shipping ? $return_sale->shipping : 0)) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
						
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>-->
                </table>
            </div>
			<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}
		
			?>
			
			

            <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>

            <div class="row inv-details" >
			   <div class="col-xs-6 pull-right">
                    <div class="well well-sm">
                        <p>
                            <table class="table table-striped jambo_table bulk_action print-table order-table pdftable">
				        <tfoot style="-webkit-print-color-adjust:exact;">
						<tr style="-webkit-print-color-adjust:exact;">
						<?php if ($Settings->user_language == 'arabic') {
						?>
	
						
						<?php if($inv->total < 0){ ?>
						
					    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;"><?=  $Settings->IsServices ? 'عدد الخدمات المرتجعة' : 'عدد الأصناف المرتجعة'; ?>
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;"> <?=  $Settings->IsServices ? 'عدد الخدمات المقدمة' : 'عدد الأصناف المباعة'; ?>
                            
                        </td>
						<?php } ?>
					    
                        <td style="text-align:left; font-weight:bold;"><?= (int)$this->sma->formatDecimal(abs($r-1),2); ?></td>
						
						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:left;font-weight:bold;"><?=  $Settings->IsServices ? 'Services Return Total' : 'Item Return Total'; ?>
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;"><?=  $Settings->IsServices ? 'Services Provided Total' : 'Item Sold Total'; ?>
                            
                        </td>
						<?php } ?>
					    <?php }else{ ?>
						<?php if($inv->total < 0){ ?>
						
					    <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;"><?=  $Settings->IsServices ? 'Services Return Total' : 'Item Return Total'; ?>
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;"><?=  $Settings->IsServices ? 'Services Provided Total' : 'Item Sold Total'; ?>
                            
                        </td>
						<?php } ?>
					    
                        <td style="text-align:left; font-weight:bold;"><?= (int)$this->sma->formatDecimal(abs($r-1),2); ?></td>
						
						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:right;font-weight:bold;"><?=  $Settings->IsServices ? 'عدد الخدمات المرتجعة' : 'عدد الأصناف المرتجعة'; ?>
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;"><?=  $Settings->IsServices ? 'عدد الخدمات المقدمة' : 'عدد الأصناف المباعة'; ?>
                            
                        </td>
						<?php } ?>
					    
					    <?php } ?>
					    
                    </tr>
                    <tr>
					
					
					
					
					
					
					   <?php if ($Settings->user_language == 'arabic') {
						?>
	
						
						
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">إجمالي خصم العروض
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Discounts
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Discounts
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">إجمالي خصم العروض
                            
                        </td>
						
					    <?php } ?>
					    
					
					
				<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('discount_total'); ?>
                            
                        </td>
						
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    -->
					</tr>
                    <tr>
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الإجمالي قبل الضريبة
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Before VAT
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Before VAT
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الإجمالي قبل الضريبة
                            
                        </td>
						
					    <?php } ?>
					   
                       


					   <!--
					   <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT_before_total'); ?>
                           
                        </td>
						
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
						
						-->
                    </tr>
                    <tr>
                        
						<?php
						$VAT="";
					if($inv->total_tax!=0)
					$VAT="(".$this->sma->formatDecimal($active_tax,0)."%)";
				    
					?>
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">ضريبة القيمة المضافة <?= $VAT ?>
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">VAT <?= $VAT ?>
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">VAT <?= $VAT ?>
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">ضريبة القيمة المضافة <?= $VAT ?>
                            
                        </td>
						
					    <?php } ?>
					   <!--
						
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
						-->
                    </tr>
						<tr>
                        
						
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">المبلغ الكلي
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Amount
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Amount
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">المبلغ الكلي
                            
                        </td>
						
					    <?php } ?>
					   
						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>
                          
                        </td>
						
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
						-->
                    </tr>
                    

                    </tfoot>
                </table>
						</p>
                    </div>
                </div>
                <div class="col-xs-6 pull-left">
                    <div class="well well-sm">
                        <p>
                        <table class="table table-striped jambo_table bulk_action print-table order-table pdftable">
				        <tfoot style="-webkit-print-color-adjust:exact;">
					<tr style="-webkit-print-color-adjust:exact;">
                        
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid ),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid ),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع
                            
                        </td>
						
					    <?php } ?>
					   
						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('paid_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid)); ?></td>
						-->
                    </tr>
					<?php
					if(abs($inv->paid)!=0){
						foreach ($payments as $payment) {
                    
						?>
                    <tr>
					
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع بواسطة
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?php
							 if ($payment->paid_by == 'cash' || $payment->paid_by == 'gift_card'  || $payment->paid_by == 'deposit'){
								 ?>
								 
								  (<?= lang($payment->paid_by) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($payment->paid_by == 'CC' || $payment->paid_by == 'span'){
								 ?>
								 
								  (<?= lang($payment->cc_type) ?>)
							<?php	  
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'Cheque'){
								 ?>
								 
								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)
								  
								  
							<?php	  
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>
								 
								  (<?= lang('bank_transfer') ?>)<br/><?= lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)
								  
								  
							<?php	  
							 }
                            ?>
							<?= $this->sma->formatMoney(abs($payment->amount ),$payment->currency); ?>
						
						</td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(abs($payment->amount ),$payment->currency); ?>
						<?php
							 if ($payment->paid_by == 'cash' || $payment->paid_by == 'gift_card'  || $payment->paid_by == 'deposit'){
								 ?>
								 
								  (<?= lang($payment->paid_by) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($payment->paid_by == 'CC' || $payment->paid_by == 'span'){
								 ?>
								 
								  (<?= lang($payment->cc_type) ?>)
							<?php	  
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'Cheque'){
								 ?>
								 
								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)
								  
								  
							<?php	  
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>
								 
								  <br/>(<?= lang('bank_transfer') ?>)<br/><?= lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)
								  
								  
							<?php	  
							 }
                            ?>
							</td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع بواسطة
                            
                        </td>
						
					    <?php } ?>
					   
                        
						
                    </tr>
					
					
					
					
					
					
					
					
					
					
					
					
					
					<tr>
					
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">تاريخ الدفع
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>
                    	
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid Date
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid Date
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">تاريخ الدفع
                            
                        </td>
						
					    <?php } ?>
					   
                        
						
                    </tr>
					
					
                    
					<?php
						}
					}
						?>
						
							<?php
							if(abs($return_sale->paid)!=0){
					        if ($return_sale) {
                    
						?>
                    <tr>
					
					
					
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع بواسطة

                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;">
						<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>
								 
								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>
								 
								  (<?= lang($return_payments[0]->cc_type) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>
								 
								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php	  
							 }
                            ?>
						<?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?>
						<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>
								 
								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>
								 
								  (<?= lang($return_payments[0]->cc_type) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>
								 
								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php	  
							 }
                            ?>
						</td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع بواسطة

                            
                        </td>
						
					    <?php } ?>
					   
					
					
					<!--
                        <td colspan="<?= $col; ?>"
                            style="font-weight:bold;"><?= lang('paid_by_tax_invoice'); ?>
							<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>
								 
								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>
								 
								  (<?= lang($return_payments[0]->cc_type) ?>)
							<?php	  
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>
								 
								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php	  
							 }
                            ?>
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_payments[0]->amount)); ?></td>
						-->
                    </tr>
                    
					<?php
						}
						}
						
						
						
						
						
                    ?>
					
					<?php
					if($inv->discount_allowance!=0){
					?>
					<tr>
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">خصم مسموح به
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->discount_allowance  - abs($return_sale->discount_allowance)) : $inv->discount_allowance),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Discount Allowance
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Discount Allowance
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->discount_allowance - abs($return_sale->discount_allowance)) : $inv->discount_allowance),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">خصم مسموح به
                            
                        </td>
						
					    <?php } ?>
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('shipping_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>
					<tr>
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الرصيد
                            
                        </td>
						
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Balance
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Balance
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الرصيد
                            
                        </td>
						
					    <?php } ?>
						
						
						
					   <!--
                        <td colspan="<?= $col; ?>"
                            style="font-weight:bold;"><?= lang('balance_tax_invoice'); ?>
                          
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>
						-->
                    </tr>
					<?php
					if($inv->shipping!=0){
					?>
					<tr>
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الشحن
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Shipping
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Shipping
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الشحن
                            
                        </td>
						
					    <?php } ?>
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('shipping_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>
					

                    </tfoot>
                </table>
            
						</p>
                    </div>
                </div>
				
				<div class="clearfix"></div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Prepared_by'); ?> : <br>  </p>
                </div>
				<div class="col-xs-4">
                    <p></p>
                </div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Receiver_Signature'); ?> : <br>  </p>
                </div>
            	
				
				<div class="clearfix"></div>
			    
            
                <div class="col-xs-12">
                    <?php
                        if ($inv->note || $inv->note != '') {
                            ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang('note'); ?> : </p>
                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        if ($inv->staff_note || $inv->staff_note != '') {
                            ?>
                            <div class="well well-sm staff_note">
                                <p class="bold"><?= lang('staff_note'); ?>:</p>
                                <div><?= $this->sma->decode_html($inv->staff_note); ?></div>
                            </div>
                        <?php
                        } ?>
                </div>

                <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) {
                            ?>
                <div class="col-xs-5 pull-left">
                    <div class="well well-sm">
                        <?=
                        '<p>' . lang('this_sale') . ' : ' . floor(($inv->grand_total / $Settings->each_spent) * $Settings->ca_point)
                        . '<br>' .
                        lang('total') . ' ' . lang('award_points') . ': ' . $customer->award_points . '</p>'; ?>
                    </div>
                </div>
                <?php
                        } ?>

                 <div class="col-xs-5 pull">
				    <div class="well1 well-sm">
                        <p>
                            <?= lang('created_by'); ?> : <?= $inv->created_by ? $created_by->first_name . ' ' . $created_by->last_name : $customer->name; ?> <br>
                            <?= lang('date'); ?> : <?= $this->sma->hrld($inv->date); ?>
                        </p>
                        <?php if ($inv->updated_by) {
                            ?>
                        <p>
                            <?= lang('updated_by'); ?> : <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                            <?= lang('update_at'); ?> : <?= $this->sma->hrld($inv->updated_at); ?>
                        </p>
                        <?php
                        } ?>
                    </div>
                </div>
				
				<?php if ($Settings->IsServices) {
                            ?>
				    <div class="col-xs-5 pull-left">
                    <div class="well well-sm">
                        <p>
                            <?= lang('Plate_Number'); ?> : <?= $inv->plate_no; ?> <br>
                            <?= lang('Exit_date'); ?> : <?= $this->sma->hrld($inv->exit_date); ?>
                        </p>
                        
                       
                        
                    </div>
                    </div>
					
					<?php
                        } ?>
						
	</div><!--  end"pr_pdf"-->						
					<div class="col-xs-12 pull-left">
                           <?= $biller->invoice_footer ? '<p class="text-center notorder"><center>' . $this->sma->decode_html($biller->invoice_footer) . '</center></p>' : ''; ?>
					</div>
            
				
            </div>
			
			</div>
            </div>
            
			
			<?php if (!$Supplier || !$Customer) {/*
                            ?>
                <div class="buttons">
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_payment/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_payment') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-dollar"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('payment') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_delivery/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_delivery') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-truck"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delivery') ?></span>
                            </a>
                        </div>
                        <?php if ($inv->attachment) {
                                ?>
                            <div class="btn-group">
                                <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>" class="tip btn btn-primary" title="<?= lang('attachment') ?>">
                                    <i class="fa fa-chain"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('attachment') ?></span>
                                </a>
                            </div>
                        <?php
                            } ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('email') ?>">
                                <i class="fa fa-envelope-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                            </a>
                        </div>
                        <!--<div class="btn-group">
                            <a href="<?= admin_url('sales/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                            </a>
                        </div>-->
                        <?php if (!$inv->sale_id) {
                                ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/edit/' . $inv->id) ?>" class="tip btn btn-warning sledit" title="<?= lang('edit') ?>">
                                <i class="fa fa-edit"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="#" class="tip btn btn-danger bpo" title="<b><?= $this->lang->line('delete_sale') ?></b>"
                                data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('sales/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                                data-html="true" data-placement="top">
                                <i class="fa fa-trash-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                            </a>
                        </div>
                        <?php
                            } ?>
                    </div>
                </div>
            <?php
                        */} ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
     $('#pdf').click(function (event) {
     try{

               // $(".pdftable td").css("padding", "0.2rem");
                //$(".pdftable th").css("padding", "0.2rem");
               // $(".table").css("padding: 0.2rem !important");
                }catch(err){alert(err)}
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('sales'. $inv->id); ?>');
		    return false;
        });

</script>
