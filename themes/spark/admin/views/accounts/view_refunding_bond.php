<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}

.t-left{
	text-align:left;font-weight:bold !important;
}

.divline {
  display: flex;
  width: 100%;
}
.divline:after {
  border-bottom: 2px dotted gray;
  content: '';
  flex: 1;
}
.divlineen {
  display: flex;
  width: 100%;
}
.divlineen:before {
  border-bottom: 2px dotted gray;
  content: '';
  flex: 1;
}

.inv-footer    { display: none;}
    .well1{
		border: 1px solid #ddd;
    background-color: #f6f6f6;
    box-shadow: none;
    border-radius: 0px;
	padding: 9px;
	 -webkit-print-color-adjust: exact !important;
	}

.well2{
		border: no-border;
    background-color: #ffffff;
    box-shadow: none;
    border-radius: 0px;
	padding: 9px;
	-webkit-print-color-adjust:exact;
	}



@media print {
    tr.vendorListHeading {
        background-color: #f6f6f6 !important;
        -webkit-print-color-adjust: exact;
    }
}

@media print {
    .vendorListHeading font {
        color: #D22B2B !important;
    }
}

@media print {
    div.vendorListFootering {
        background-color: #2A3F54 !important;
        -webkit-print-color-adjust: exact;
    }
}

@media print {
    .vendorListFootering font {
        color: #ddd !important;
    }
}



@media print {


	.t-left{
	text-align:left;font-weight:bold !important;
}
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}




				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
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
<!--
<div id="text9" style="position:absolute; overflow:hidden; left:35%; top:<?php echo ($top+230); ?>px; width:200px; height:200px; z-index:20;">
<img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>" style="opacity:0.2;" width="100%" height="100%"/>
</div>
-->

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
           <div class="col-xs-4 pull-left" style=" margin-bottom:10px;">
                <?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <center><span valign="top"><h1  style=" font-size: 18px;"><?= $branch->branch_name ? $branch->branch_name : $Settings->site_name; ?></h1><span></center>
                <?php
                    }else{
                ?>
				   <center><span valign="top"><h1  style=" font-size: 18px;"><?= $Settings->site_name; ?></h1><span></center>
                <?php
                    }
                ?>


			</div>

             <div class="col-xs-4" >

            <?php
            $logo=($branch->branch_logo) ? $branch->branch_logo : 'logos/'.$Settings->logo;
			//if ($logo)
			{
    ?>
                <div  style="margin-top:10px;">
				<center>
                    <img src="<?= base_url() . 'assets/uploads/' . $logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
						 </center>
                </div>
            <?php
} ?>
			</div>
			<div class="col-xs-4 pull-right" style=" margin-bottom:10px;">

				<?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <center><span valign="top"><h1  style=" font-size: 18px;"><?= $branch->branch_nameEn ? $branch->branch_nameEn : $Settings->site_ename; ?></h1><span></center>
                <?php
                    }else{
                ?>
                <center><span valign="top"><h1  style=" font-size: 18px;"><?= $Settings->site_ename; ?></h1><span></center>
                <?php
                    }
                ?>
			</div>

			</div>
			<div class="row bold">
			<div class="col-xs-12" style="border:2px solid #2A3F54;margin:10px;">

			<div class="row bold"  >
           <div class="col-xs-3" style=" margin-bottom:10px;">
						<table >
						<tr >
                            <?php
                            if($receipt->currency_code==$Settings->default_currency){
                                ?>

                        <td class=" well2 col-xs-1" >

						<center><span><font  style=" font-size: 11px;"> H/هـ </font><span></center>

						</td>
						<td class=" well2 col-xs-2">
						<center><span><font  style=" font-size: 11px;"> SR / ريال </font><span></center>

						</td>
                        <?php
                            }else{
                            ?>

                        <td class=" well2 col-xs-1" >

						<center><span><font  style=" font-size: 11px;"></font><span></center>

						</td>
						<td class=" well2 col-xs-2">
						<center><span><font  style=" font-size: 11px;"> <?=$receipt->currency_code?></font><span></center>

						</td>
						<?php
                            }
                            ?>

						</tr>

						<tr class="vendorListHeading">
						<td class=" well1 col-xs-1">
						<center><span style=" font-size: 12px;"> <?= floor(($receipt->total_amount-floor($this->sma->formatDecimal($receipt->total_amount)))*100); ?><span></center>

						</td>
						<td class=" well1 col-xs-2">
						<center><span style=" font-size: 12px;"> <?= floor($this->sma->formatDecimal($receipt->total_amount)); ?><span></center>

						</td>
						</tr>
						</table>


			</div>



			<div class="col-xs-6" style=" margin-bottom:10px;">

				    <center><span valign="center"><h1 class="vendorListHeading" style=" font-size: 20px;">  سند تحويل <br/> REFUNDING BOND<br> <font color="#D22B2B" style="-webkit-print-color-adjust:exact;"> <?= $receipt->bond_no; ?></font></h1><span></center>

			</div>



			<div class="col-xs-3" style=" margin-bottom:10px;">


			</div>













			</div>














			<div class="row">
			<!--<div class="col-xs-1 divbg">
			</div>-->
			<div class="col-xs-12">





            <div>
                <div class="row bold "  <?= $Settings->user_language == 'arabic' ? '' :'dir="rtl"' ?>>
                    <div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                     <span style=" font-size: 16px;">   تاريخ السند </span> <br>
                    </p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $this->sma->hrld($receipt->date); ?><br>
                    </p>
                    </div>
					<div class="col-xs-3" >
                     <p  style="text-align:left;font-weight:bold;">
                    <span style=" font-size: 16px;">    Date </span> <br>
                    </p>
				    </div>


					<div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  الحساب المحول منه </span> <br>
                    </p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $accountNameFrom; ?><br>
                    </p>
                    </div>
					<div class="col-xs-3" >
                     <p style="text-align:left;font-weight:bold;" >
                	<span style=" font-size: 16px;">	Transferred From </span><br>
					</p></div>

					<div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  الحساب المحول له </span> <br>
                    </p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                       <?= $accountNameTo; ?><br>
                    </p>
                    </div>
					<div class="col-xs-3" >
                     <p style="text-align:left;font-weight:bold;" >
                	<span style=" font-size: 16px;">	Transferred To </span><br>
					</p></div>

					<div class="col-xs-3" >
                     <p style="text-align:right;font-weight:bold;">
                     <span style=" font-size: 16px;">  مبلغ وقدره </span><br>
					</p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                       <?=  $FRACTION; ?><br>
					</p>
                    </div>
					<div class="col-xs-3" >
                     <p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size: 16px;">   Amount </span> <br>
				    </p>
					</div>

					<div class="col-xs-3" >
                     <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  طريقة التحويل </span><br>
					</p>
					</div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= lang($receipt->paid_by); ?><br>
					</p>
					</div>
					<div class="col-xs-3" >
					<p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size:16px;">   Transferred Type  </span> <br>
					</p>
					</div>

					<div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                       <span style=" font-size: 16px;">  وذلك مقابل </span><br>
					</p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $this->sma->decode_html($receipt->note); ?><br>
					</p>
                    </div>
					<div class="col-xs-3" >
                    <p  style="text-align:left;font-weight:bold;">
                      <span style=" font-size: 16px;">  Being </span> <br>
					</p>
					</div>

                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>



















            <div class="clearfix"></div>

            <?php //if ($delivery->status == 'delivered')
			{
                        ?>
            <div class="row" style="display:none;">
                <div class="col-xs-6">
                    <p> المحاسب / Accountant</p>
                </div>

                <div class="col-xs-6">
                    <p   style="text-align:left;">المستلم / Receiver</p>
                </div>

			</div>
            <?php
                    }  ?>




					<div class="clearfix">
					</br>
					</div>
					<div class="clearfix">
					</br>
					</div>



					<div class="row inv-details">


				</div>

         </div>
        </div>
      </div>
	  <div class="col-xs-12" >



                            <div class="vendorListFootering" style="background-color: #2A3F54;color:#ddd;-webkit-print-color-adjust:exact  !important;">
							<?php
							$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","",$address);
					?>


                                <div><center style="font-size:10pt;"><font><?= ($warehouse->address ?  $this->sma->decode_html($address) . '  ' : '') . ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '  ' : '') ; ?></font></center></div>
                            </div>

                </div>
      </div>
      </div>
    </div>



</div>


             <div class="row inv-footer">


				</div>


