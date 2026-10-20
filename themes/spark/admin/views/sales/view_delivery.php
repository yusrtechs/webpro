<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}


.inv-footer    { display: none;}



    table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;}



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


	.inv-footer    { display: block; position: fixed;
    bottom: 0; page-break-inside:avoid; page-break-after:auto }
	.inv-details    { page-break-inside:avoid; page-break-after:auto }
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
                        <button type="button "
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                <i  class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                 </a>

            </button>
			</div>
           </div>

	<div id="pr_pdf">
		  <div class="row bold">
           <div class="col-xs-6 pull-left" style=" margin-bottom:10px;">

				    <span valign="top"><h1  style=" font-size: 25px;">Delivery&nbsp;Note&nbsp;/&nbsp;ملاحظة&nbsp;التسليم</h1><span>

			</div>

             <div class="col-xs-6" >

            <?php

			if ($logo)
			{
    ?>
                <div class="pull-right" style="margin-bottom:10px;">
				<span valign="top"><h1  style=" font-size: 25px;"><?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></h1><span>

                </div>
            <?php
} ?>
			</div>
			</div>









			<div class="row">
			<!--<div class="col-xs-1 divbg">
			</div>
			<div class="col-xs-11">
			-->
            <div class="col-xs-12">


            <div class="well well-sm">
                <div class="row bold"  >
                    <div class="col-xs-6">
                    <p class="bold">
                        <?= lang('Delivery_date'); ?> : <?= $this->sma->hrld($delivery->date); ?><br>
                        <?= lang('Delivery_no'); ?> : <?= $delivery->id; ?><br>
                        <?= lang('do_reference_no'); ?> : <?= $delivery->do_reference_no ? $delivery->do_reference_no : $delivery->reference_no; ?><br>
						<?= lang('sale_reference_no'); ?> : <?= $delivery->sale_reference_no; ?><br>
						<?= lang('Status'); ?> : <?= lang($delivery->status); ?><br>


                    </p>
                    </div>
					<div class="col-xs-6" >

            <?php
            $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;
			if ($logo)
			{
    ?>
                <div class="pull-right" style="margin-top:10px;">

                    <img src="<?= base_url() . 'assets/uploads/' . $logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
            <?php
} ?>
			</div>

                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>




			<div class="row" style="margin-bottom:15px;margin-top:-10px;">

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
                    <?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <?php echo $this->lang->line('from'); ?>  <br/><b><span style="margin-top:10px;"><?= $branch->branch_name ? $branch->branch_name : $Settings->site_name; ?></span></b><br/>
                <?php
                    }else{
                ?>
                    <?php echo $this->lang->line('from'); ?> <br/><b><span style="margin-top:10px;"><?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></span></b><br/>
                <?php
                    }
                ?>

                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>

					<?= $warehouse->name ?>-->

                    <?php
                    if(isset($Settings->preAccounting) && $Settings->preAccounting==1 && $Settings->isDisplayWarehouseOnInvoice==1)
                    {
                        echo lang('warehouse') . ' : ' . $warehouse->name . '<br>';
                    }
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ' : ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? lang('CR_Number') . ' : ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : '');
                    ?>
                </div>
                <?php

                if ($this->Settings->isDeliveryFromSales==1) {

                ?>

				<div class="col-xs-6">
                    <?php echo $this->lang->line('to'); ?><br/>
                    <h2 style="margin-top:10px;"><?= $delivery->customer; ?></h2>

                    <?php
                    echo $delivery->address;


                    ?>
                </div>

                <?php
                }else{
                    ?>



                <div class="col-xs-6 to">
                    <?php echo $this->lang->line('to'); ?> <br/><b> <span  dir="ltr"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></span></b><br/>
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
                    echo ($accountID ? lang('Account_No') . ' : ' . $accountID . '<br>' : '') . ($customer->vat_no ? lang('VAT_Regesteration_Number') . ' : ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR_Number') . ' : ' . $customer->cr_no . '<br>' : '<br>') ;
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


                <?php
                }
                ?>



            </div>









            <h3><?= lang('items'); ?></h3>
            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action pdftable">
                    <thead>
                        <tr>
                            <th style="text-align:center; vertical-align:middle;"><?= lang('no_sales'); ?></th>
                            <th style="vertical-align:middle;"><?= lang('product_name_sales'); ?></th>
                            <th style="text-align:center; vertical-align:middle;"><?= lang('product_unit_sales'); ?></th>
                            <th style="text-align:center; vertical-align:middle;"><?= lang('quantity_sales'); ?></th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php $r = 1;
                    foreach ($rows as $row): ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;"  dir="ltr">
                                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : '');
                                if ($row->details) {
                                    echo '<br><strong>' . lang('product_details') . '</strong> ' .
                                    html_entity_decode($row->details);
                                }
                                ?>
                            </td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </div>





            <div class="clearfix"></div>

            <?php if ($delivery->status == 'delivered') {
                        ?>
            <div class="row">
                <div class="col-xs-4">
                    <p><?= lang('prepared_by'); ?> : <br> <?= $user->first_name . ' ' . $user->last_name; ?> </p>
                </div>
				<?php
				if($delivery->delivered_by){
					?>
                <div class="col-xs-4">
                    <p><?= lang('delivered_by'); ?> : <br> <?= $delivery->delivered_by; ?></p>
                </div>
				<?php
				}else{
				?>
                <div class="col-xs-4">
                    <p></p>
                </div>
				<?php
				}
				?>
                <div class="col-xs-4">
                    <p><?= lang('received_by'); ?> : <br> <?= $delivery->received_by; ?></p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
            </div>
            <?php
                    } else {
                        ?>

            <div class="row">
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('prepared_by'); ?>
                        : <?= $user->first_name . ' ' . $user->last_name; ?> </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('delivered_by'); ?>: </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('received_by'); ?>: </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
            </div>

            <?php
                    } ?>




					<div class="clearfix">
					</br>
					</div>
					<div class="clearfix">
					</br>
					</div>



					<div class="row inv-details">

					<div class="col-xs-12">


                    <?php
                        if ($delivery->note) {
                            ?>
                            <div class="well well-sm">

                                <div><?= $this->sma->decode_html($delivery->note); ?></div>
                            </div>
                        <?php
                        }
                         ?>
                </div>
				</div>

         </div>
        </div>
      </div>

      </div><!--  end"pr_pdf"-->
    </div>



</div>


             <div class="row inv-footer">

					<div class="col-xs-12">



                            <div class="well well-sm">

                                <div><center><?= ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '  ' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : ''); ?></center></div>
                            </div>

                    </div>
			</div>



 <script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
     $('#pdf').click(function (event) {
                event.preventDefault();
        PrPDF('pr_pdf','<?= lang('delivery'. $delivery->id); ?>');
		        return false;
            });
</script>
