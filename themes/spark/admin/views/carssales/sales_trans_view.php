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
					
				    <span valign="top"><h1  style=" font-size: 25px;">Sales Transaction / حركة المبيعات</h1><span>
						
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
                        <?= lang('from'); ?>: <?= $start_date; ?><br>
                        <?= lang('to'); ?>: <?= $end_date; ?>
                        
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
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th class="col-xs-6"><?= lang('product_name'); ?></th>
                        <th class="col-xs-2"><?= lang('date'); ?></th>
                        
                        <th><?= lang('sales_quantity'); ?></th>
                       
                        <th class="col-xs-1"><?= lang('sold_total'); ?></th>
						 
                        <?php
						//if ($Setting->product_discount && $inv->product_discount != 0) {
                            echo '<th class="col-xs-1">' . lang('VAT_Amount') . '</th>';
                        //}
						//echo '<th class="col-xs-2">' . lang('item_price_sales') . '</th>';
                        //if ($Setting->tax1 && $inv->product_tax > 0) 
						{
							 echo '<th class="col-xs-1">' . lang('grand_total') . '</th>';
                            echo '<th class="col-xs-1">' . lang('goods_cost') . '</th>';
                        }
                        
                        ?>
                        <th class="col-xs-1"><?= lang('profit_loss'); ?></th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
					$tquantity=0;
					$tsold=0;
					$tvat=0;
					$ttotal=0;
					$tcost=0;
					$tprofit=0;
					
					foreach ($rows as $row):
					
					$tquantity+=$row->sales_quantity;
					$tsold+=$row->sold;
					$tvat+=$row->sales_tax;
					$ttotal+=$row->Final_Total;
					$tcost+=$row->totalGoodsCost;
					$tprofit+=$row->Profit;
					
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <!--<td style="vertical-align:middle;">
                                <?= $row->product_code ?>
                            </td>-->
							<td style="vertical-align:middle;">
                                <?=  lang('code'). ':' .$row->code. '<br>' . $row->name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <!--<?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>-->
                            </td>
							
							
							<td><?=$row->sales_date; ?></td>
                           
							 	
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->sales_quantity); ?></td>
                           
							<td style="text-align:right; width:100px;">
                            	<?= $this->sma->formatMoney($row->sold); ?>
                            </td>
							<?php
							    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney($row->sales_tax) . '</td>';
                            {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .$this->sma->formatMoney($row->Final_Total) . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Setting->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->totalGoodsCost) . '</td>';
                            }
                            
                            ?>
                            <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->Profit); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    
                    ?>
                    </tbody>
                    <tfoot>
                   
                        <tr>
                            <td colspan="3"
                                ><?= lang('total'); ?>
                                
                            </td>
							
                          
                            <td ><?= $this->sma->formatQuantity($tquantity); ?></td>
                            <td ><?= $this->sma->formatMoney($tsold); ?></td>
                            <td ><?= $this->sma->formatMoney($tvat); ?></td>
                            <td ><?= $this->sma->formatMoney($ttotal); ?></td>
                            <td ><?= $this->sma->formatMoney($tcost); ?></td>
                            <td ><?= $this->sma->formatMoney($tprofit); ?></td>
                        </tr>
                    
                    

                    </tfoot>
                </table>
            </div>

			
			</div>
            </div>
            
			
			
    </div>
</div>
