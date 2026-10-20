<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>



.fa-circle1 {
  border-radius: 50%;
  background: linear-gradient(Brown);

	border: 3px solid #fff;
    color: #fff;border-radius: 50%;width:30px;height: 30px;text-align: center;line-height: 25px;vertical-align: middle;
	-webkit-print-color-adjust: exact;
}
th {
  font-size: 14px;
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
        background-color: #a52a2a !important;
        -webkit-print-color-adjust: exact;
    }
}

@media print {
    .vendorListFootering font {
        color: #fff !important;
    }
}



@media print {
.pr_color{
-webkit-print-color-adjust:exact !important;
}

.fa-circle1 {
  border-radius: 50%;
  background: linear-gradient(Brown);

	border: 3px solid #fff;
    color: #fff !important;border-radius: 50%;width:30px;height: 30px;text-align: center;line-height: 25px;vertical-align: middle;
	-webkit-print-color-adjust: exact;
}

	.table-striped>thead>tr:nth-child(odd)>th {
  background: brown !important;
  color: #fff  !important;

    }
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
			<button type="button"
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                            </a>

            </button>
			</div>
           </div>
	<div id="pr_pdf">
		  <div class="col-xs-12" style="border:10px solid #990033;"></div>

		  <div class="row bold"  <?= $Settings->user_language == 'arabic' ? '' :'dir="rtl"' ?>>
           <div class="col-xs-4 " style=" margin-bottom:10px;">
			<?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <center><span valign="top"><h1  style=" font-size: 16px;"><?= $branch->branch_name ? $branch->branch_name : $Settings->site_name; ?></h1><span>
                        <?php
                 if (!empty($warehouse->vat_reg_no)) { ?>
                    <b> الرقم الضريبي : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    <?php if (!empty($warehouse->cr_no)) { ?>
                    <b> السجل التجاري : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    </center>
                <?php
                    }else{
                ?>
				   <center><span valign="top"><h1  style=" font-size: 16px;"><?= $Settings->site_name; ?></h1><span>
                    <?php
                 if (!empty($warehouse->vat_reg_no)) { ?>
                    <b> الرقم الضريبي : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    <?php if (!empty($warehouse->cr_no)) { ?>
                    <b> السجل التجاري : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                   </center>
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
                    <img src="<?= base_url() . 'assets/uploads/' . $logo; ?>"  style="max-width:270px;"
                         alt="<?= $Settings->site_name; ?>">
						 </center>
                </div>
            <?php
} ?>
			</div>
			<div class="col-xs-4 " style=" margin-bottom:10px;">

				<?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <center><span valign="top"><h1  style=" font-size: 16px;"><?= $branch->branch_nameEn ? $branch->branch_nameEn : $Settings->site_ename; ?></h1><span>
                        <?php if (!empty($warehouse->vat_reg_no)) { ?>
                        <b> VAT No : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    <?php if (!empty($warehouse->cr_no)) { ?>
                        <b> CR No : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    </center>
                <?php
                    }else{
                ?>
                <center><span valign="top"><h1  style=" font-size: 16px;"><?= $Settings->site_ename; ?></h1><span>
                    <?php if (!empty($warehouse->vat_reg_no)) { ?>
                        <b> VAT No : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                    <?php if (!empty($warehouse->cr_no)) { ?>
                        <b> CR No : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>
                    <?php } ?>
                </center>
                <?php
                    }
                ?>

			</div>

			</div>
			<div class="row bold">
			<div class="col-xs-12" >

			<div class="row bold"    <?= $Settings->user_language == 'arabic' ? '' :'dir="rtl"' ?>>
           <!--<div class="col-xs-3" style=" margin-bottom:10px;">
						<table >
						<tr >
						<td class=" well2 col-xs-1" >
						<center><span><font  style=" font-size: 11px;"> H/هـ </font><span></center>

						</td>
						<td class=" well2 col-xs-2">
						<center><span><font  style=" font-size: 11px;"> SR / ريال </font><span></center>

						</td>
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

			-->
			<div class="col-xs-4 " style=" margin-bottom:10px;margin-top:50px;">

                    <p style="text-align:right;font-weight:bold;">
                     <span style=" font-size: 16px;">   التاريخ  <?= $this->sma->hrld($receipt->date); ?></span><br>
                    </p>

			</div>
			<div class="col-xs-4" style=" margin-bottom:10px;">

				    <center><span valign="center"><h1 class="vendorListHeading" style=" font-size: 16px;"> <font color="#D22B2B" style="-webkit-print-color-adjust:exact;"> سند قبض </font><br/> Cash Recepit <br> </h1><span></center>

			</div>



			<div class="col-xs-4" style=" margin-bottom:10px;margin-top:50px;">
					 <p style="text-align:left;font-weight:bold;">
                      <span style=" font-size: 16px;">   <font  class="vendorListHeading" color="#D22B2B" style="-webkit-print-color-adjust:exact;"> No:  <?= $receipt->bond_no; ?></font></span>

                    </p>

			</div>













			</div>














			<div class="row">
			<!--<div class="col-xs-1 divbg">
			</div>-->
			<div class="col-xs-12">





            <div>
                <div class="row bold "  <?= $Settings->user_language == 'arabic' ? '' :'dir="rtl"' ?>>
                    <!--<div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                     <span style=" font-size: 16px;">   تاريخ السند </span><br>
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
					-->

					<div class="col-xs-3" >
                    <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  أستلمت من السيد  </span><br>
                    </p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $receipt->receipt_name; ?><br>
                    </p>
                    </div>
					<div class="col-xs-3" >
                     <p style="text-align:left;font-weight:bold;" >
                	<span style=" font-size: 16px;">	Received from Mr </span><br>
					</p></div>

					<div class="col-xs-3" >
                     <p style="text-align:right;font-weight:bold;">
                     <span style=" font-size: 16px;">  مبلغ قدره </span><br>
					</p>
                    </div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divline" style="direction:ltr"' ?> >
					<?=  ' ( '.$this->sma->formatMoney($receipt->total_amount, $receipt->currency_code).' ) '; ?>
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
					<div class="col-xs-3" >
                     <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  نقدا / شيك رقم </span><br>
					</p>
					</div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $receipt->paid_by=='Cheque' ? lang('cheque').' / '.$fromAccountName : lang($receipt->paid_by).' / '.$fromAccountName; ?><br>
					</p>
					</div>
					<div class="col-xs-3" >
					<p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size:16px;">   Cash / Check number </span> <br>
					</p>
					</div>
					<div class="col-xs-3" >
                     <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  على بنك </span><br>
					</p>
					</div>
					<div class="col-xs-6" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <?= $receipt->paid_by=='bank_transfer' ? $fromAccountName : ""; ?><br>
					</p>
					</div>
					<div class="col-xs-3" >
					<p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size:16px;">   On a Bank </span> <br>
					</p>
					</div>
					<div class="col-xs-2" >
                     <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  المستلم </span><br>
					</p>
					</div>
					<div class="col-xs-2" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <br>
					</p>
					</div>
					<div class="col-xs-2" >
					<p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size:16px;">   Recived by </span> <br>
					</p>
					</div>
					<div class="col-xs-2" >
                     <p style="text-align:right;font-weight:bold;">
                      <span style=" font-size: 16px;">  المحاسب </span><br>
					</p>
					</div>
					<div class="col-xs-2" >
                    <p  <?= $Settings->user_language == 'arabic' ? 'class="divline"' :'class="divlineen"' ?> >
                      <br>
					</p>
					</div>
					<div class="col-xs-2" >
					<p  style="text-align:left;font-weight:bold;">
                     <span style=" font-size:16px;">   Accountant </span> <br>
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
            <!-- <div class="row">
                <div class="col-xs-6">
                   <p style="text-align:center;"> المحاسب / Accountant</p>
                </div>

                <div class="col-xs-6">
                    <p style="text-align:center;"> المستلم / Receiver</p>
                </div>

			</div> -->
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
	<!-- vendorListFootering -->
	         	 <div  class=" col-lg-12 vendorListFootering" style="  background: linear-gradient(brown,brown);">
				   <div class="table-responsive">
                                <table id=""
                                class=" table-striped jambo_table bulk_action table-condensed reports-table " >
                                <thead >
                                    <tr style="-webkit-print-color-adjust: exact; color:#ddd;">
                                      	<?php
								$address=$warehouse->address;
								$address=str_replace("<p>","",$warehouse->address);
								$address=str_replace("</p>","",$address);
								//echo $address ;
								?>
								<th > <i class="fa fa-map-marker fa-circle1"></i> <?=  $address   ?></th>
                                        <th> <i class="fa fa-phone fa-circle1"></i></i><?= (' ' . $warehouse->phone ) ; ?></th>
                                        <th style="text-align: right;"> <i class="fa fa-envelope-o fa-circle1"></i><?= ( ' ' .$warehouse->email); ?></th>

                                    </tr>
                                </thead>
								</table>
								</div>
			<!--
				<div><center style="font-size:10pt;"><font>
							<div class="input-group margin-bottom-sm">

							  <span class="fa-stack fa-2x">
							  <i class="fa fa-circle fa-stack-2x" ></i>
							  <i class="fa fa-map-marker fa-stack-1x fa-inverse" style="color: #fff;background: #990033;border-radius: 50%;border: 1px sold #fff; width: 50px;
								height: 50px;text-align: center;line-height: 50px;"></i>
								</span>
								<?php
								$address=$warehouse->address;
								$address=str_replace("<p>","",$warehouse->address);
								$address=str_replace("</p>","",$address);
								echo $address ;
								?>
							<!--<?= ($warehouse->address ?  $this->sma->decode_html($address) . '  ' : '') ?> -->
						<!--	<span class="fa-stack fa-2x top">
							  <i class="fa fa-circle fa-stack-2x" ></i>
							  <i class="fa fa-phone fa-stack-1x fa-inverse" style="color: #fff;background: #990033;border-radius: 50%;border:#eee; width: 50px;
								height: 50px;text-align: center;line-height: 50px;vertical-align: middle;"></i>
							</span>
							<?= ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '  ' : '') ; ?>
							<span class="fa-stack fa-2x">
							  <i class="fa fa-circle fa-stack-2x" ></i>
							  <i class="fa fa-envelope fa-stack-1x fa-inverse" style="color: #fff;background: #990033;border-radius: 50%;border-color:#eee; width: 50px;
								height: 50px;text-align: center;line-height: 50px;vertical-align: middle;"></i>
							</span>

							<?= $warehouse->email ?>


						</div>
						</font></center></div>

                     </div>




				</div>-->
				</div>

      </div>
	  </div><!-- end pr_pdf -->
      </div>
    </div>



</div>


             <div class="row inv-footer">


				</div>


<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });

        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('catch_receipts'. $inv->id); ?>');
		    return false;
        });

</script>
