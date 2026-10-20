<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
th {
  font-size: 12px;
}
td {
  font-size: 12px;
}

@media print {

th {
  font-size: 8px;
}
td {
  font-size: 8px;
}


	
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
	  tr td    { font-size:8pt; }

   
	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt; }
	
	div.container {
  min-width: 100%;
  overflow:hidden;
  border:no-border;
}
	div. content {
 
  overflow:hidden;
}



div{
	overflow:visible !important;
	
}
                
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

<div class="box ">
    
    <div class="box-content">
       <div class="row bold">
        <div class="col-xs-12 no-print" >
                
           
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
			</div>
           </div>
		   <div class="row bold">
            <div class="col-xs-8 pull-left" style=" margin-bottom:10px;"> 
					
				    <span valign="top"><h1  style=" font-size: 25px;">Product Purchases Details / تفاصيل مشتريات المنتج</h1><span>
						
			</div>
					
             <div class="col-xs-4" >
					
            <?php 
			//if ($logo) 
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
			<div class="col-xs-1 divbg">
			</div>
			<div class="col-xs-11">
			


				
            <div class="well well-sm">
                <div class="row bold"  >
                    <div class="col-xs-12">
                    <p class="bold">
                        <?= lang('code'); ?>: <?= $product->code; ?><br>
                        <?= lang('description'); ?>: <?= $product->second_name ? '<br>' . $product->name . ' / ' .$product->second_name : $product->name; ?><br>
						<?= lang('warehouse'); ?>: <?= $warehouse; ?>
                        
                                
                        
                    </p>
                    </div>
					
                    
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            
           
            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table" >
				

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th><?= lang('Sr'); ?></th>
                        <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('biller'); ?></th>
                                    <th><?= lang('customer'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('Unit_Price'); ?></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
									<th><?= lang('Total_Value'); ?></th>
                                    
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
					$item_discount=0;
					$item_tax=0;
					$subtotal=0;
					
					foreach ($rows as $row):
					
					$item_discount+=$row->item_discount;
					$item_tax+=$row->item_tax;
					$subtotal+=$row->subtotal;
					
					
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                           
							
							<td><?=$this->sma->hrld($row->date); ?></td>
                            <td><?=$row->reference_no; ?></td>
							<td><?=$row->biller; ?></td>
							<td><?=$row->customer; ?></td>
							<td><?=$this->sma->formatQuantity($row->iname); ?></td>
							<td><?=$row->product_unit_code; ?></td>
							<td><?=$this->sma->formatMoney($row->unit_price); ?></td>
							<td><?=$this->sma->formatMoney($row->item_discount); ?></td>
							<td><?=$this->sma->formatMoney($row->item_tax); ?></td>
							<td><?=$this->sma->formatMoney($row->subtotal); ?></td>
							
							
                           
							 	
                            
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    
                    ?>
					<tr>
                            <td colspan="8"><?= lang('total'); ?>
                                
                            </td>
							
                          
                            <td ><?= $this->sma->formatMoney($item_discount); ?></td>
                            <td ><?= $this->sma->formatMoney($item_tax); ?></td>
                            <td ><?= $this->sma->formatMoney($subtotal); ?></td>
                            
                            
                        </tr>
                    
                    </tbody>
                    <!--<tfoot>
                   
                        <tr>
                            <td colspan="8"
                                ><?= lang('total'); ?>
                                
                            </td>
							
                          
                            <td ><?= $this->sma->formatMoney($item_discount); ?></td>
                            <td ><?= $this->sma->formatMoney($item_tax); ?></td>
                            <td ><?= $this->sma->formatMoney($subtotal); ?></td>
                            <td ></td>
                            
                        </tr>
                    
                    

                    </tfoot>-->
                </table>
            </div>

			
			</div>
            </div>
            
			
			
    </div>
</div>
