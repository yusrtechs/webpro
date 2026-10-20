<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php


if ($this->Settings->user_language == 'arabic' || $warehouse->nameEn==""){
	$warehouse_name=$warehouse->name;
}else{
    $warehouse_name=$warehouse->nameEn;
}
if ($this->Settings->user_language == 'arabic' || $biller_details->companyEn==""){
	$biller_name=$biller_details->company;
}else{
    $biller_name=$biller_details->companyEn;
}

    $_POST['open_date']=$posReg->date;
    $_POST['close_date']=$posReg->closed_at;

$v = '';


    $v .= '&wh=' . $warehouse->id;


if ($this->input->post('open_date')) {
    $v .= '&open_date=' . $this->input->post('open_date');
}
if ($this->input->post('close_date')) {
    $v .= '&close_date=' . $this->input->post('close_date');
}

?>
<script>$(document).ready(function () {
        CURI = '<?= admin_url('reports/profit_loss/?v=1' . $v); ?>';
    });</script>

<script>$(document).ready(function () {



        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
			if (localStorage.getItem('pcash_account_1')) {
                localStorage.removeItem('pcash_account_1');
            }
			if (localStorage.getItem('pcc_account_1')) {
                localStorage.removeItem('pcc_account_1');
            }

			if (localStorage.getItem('pcheque_account_1')) {
                localStorage.removeItem('pcheque_account_1');
            }

			if (localStorage.getItem('ptransfer_account_1')) {
                localStorage.removeItem('ptransfer_account_1');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }
            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }

        <?php if ($this->session->userdata('remove_slls')) {
    ?>
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        if (localStorage.getItem('sldiscount')) {
            localStorage.removeItem('sldiscount');
        }
        if (localStorage.getItem('sltax2')) {
            localStorage.removeItem('sltax2');
        }
        if (localStorage.getItem('slref')) {
            localStorage.removeItem('slref');
        }
        if (localStorage.getItem('slshipping')) {
            localStorage.removeItem('slshipping');
        }
        if (localStorage.getItem('slwarehouse')) {
            localStorage.removeItem('slwarehouse');
        }
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
        }
        if (localStorage.getItem('slnote')) {
            localStorage.removeItem('slnote');
        }
        if (localStorage.getItem('slinnote')) {
            localStorage.removeItem('slinnote');
        }
        if (localStorage.getItem('slcustomer')) {
            localStorage.removeItem('slcustomer');
        }
        if (localStorage.getItem('slbiller')) {
            localStorage.removeItem('slbiller');
        }
        if (localStorage.getItem('slcurrency')) {
            localStorage.removeItem('slcurrency');
        }
        if (localStorage.getItem('sldate')) {
            localStorage.removeItem('sldate');
        }
        if (localStorage.getItem('slsale_status')) {
            localStorage.removeItem('slsale_status');
        }
        if (localStorage.getItem('slpayment_status')) {
            localStorage.removeItem('slpayment_status');
        }
        if (localStorage.getItem('paid_by')) {
            localStorage.removeItem('paid_by');
        }
        if (localStorage.getItem('amount_1')) {
            localStorage.removeItem('amount_1');
        }
        if (localStorage.getItem('paid_by_1')) {
            localStorage.removeItem('paid_by_1');
        }
        if (localStorage.getItem('pcc_holder_1')) {
            localStorage.removeItem('pcc_holder_1');
        }
        if (localStorage.getItem('pcc_type_1')) {
            localStorage.removeItem('pcc_type_1');
        }
        if (localStorage.getItem('pcc_month_1')) {
            localStorage.removeItem('pcc_month_1');
        }
        if (localStorage.getItem('pcc_year_1')) {
            localStorage.removeItem('pcc_year_1');
        }
        if (localStorage.getItem('pcc_no_1')) {
            localStorage.removeItem('pcc_no_1');
        }
        if (localStorage.getItem('cheque_no_1')) {
            localStorage.removeItem('cheque_no_1');
        }
        if (localStorage.getItem('slpayment_term')) {
            localStorage.removeItem('slpayment_term');
        }
        <?php $this->sma->unset_data('remove_slls');
}
        ?>



    });
</script>
<style>@media print {
        .fa {
            color: #EEE;
            display: none;
        }

        .small-box {
            border: 1px solid #CCC;
        }
    }
	/*.pltable{padding:0.5em;}
	.pltable1{padding-left:0.2em;padding-right:0.2em;}*/


	</style>

	<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>


<div class="box">
    <!--<div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-bars"></i><?= lang('profit_loss'); ?></h2>

        <div class="box-icon">
            <div class="form-group choose-date hidden-xs">
                <div class="controls">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text"
                               value="<?= ($start ? $this->sma->hrld($start) : '') . ' - ' . ($end ? $this->sma->hrld($end) : ''); ?>"
                               id="daterange" class="form-control">
                        <span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">

            </ul>
        </div>
    </div>-->
	<div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= lang('Cashier_statistics'); ?>
        </h2>


        <div class="box-icon">
            <ul class="btn-tasks">
                    <li class="dropdown">
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>


            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-sm-12 col-md-12">



				<div class="row">
				<div class="col-sm-12 col-md-12">

    <div id="pr_pdf">

	<div><table border="0" cellpadding="0" cellspacing="0" class="table">

	<tbody width="100%">
	<thead style="border: 1px solid #00ace6;">
	<tr>
		<td style="background-color:#dee2e6;color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('Cashier_statistics'); ?></font></b></td>
		</tr>


                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;-webkit-print-color-adjust:exact !important;"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo $Settings->site_name;  ?></font></b></td>

					</tr>






	</thead>


	<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


    <tr>
		<td  style="background-color:#dee2e6;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;border-bottom: 1px solid #00ace6; " colspan="8" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('Register_data'); ?></font></b></td>

	</tr>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('warehouse') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable1"><b><font  size="2"><?= $warehouse_name ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('biller') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable1"><b><font  size="2"><?= $biller_name ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('open_date') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable1"><b><font  size="2"><?= $this->sma->hrld($this->input->post('open_date')) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('close_date') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"   class="pltable1"><b><font  size="2"><?= $this->sma->hrld($this->input->post('close_date')) ?></font></b></td>

	</tr>

    <tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('note') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"   class="pltable1"><b><font  size="2"><?= $this->sma->decode_html($posReg->note); ?></font></b></td>

	</tr>
    <tr>
		<td  style="background-color:#dee2e6;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;border-bottom: 1px solid #00ace6; " colspan="8" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('Close_register_statistics'); ?></font></b></td>

	</tr>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Sales_Invoices') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatDecimal(($allSales->total+$rSales->total),0) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('The_amount_when_opening_the_cashier') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney($posReg->cash_in_hand) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Cash_payments') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(($paymentcash->amount + $re_paymentcash->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Bank_transfers') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(($paymentbank_transfer->amount)) ?></font></b></td>

	</tr>
    <tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('gift_cards') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(($paymentgift_card->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('award_points') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(($paymentgift_card_points->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Credit_sales') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney($allSales->due) ?></font></b></td>

	</tr>
    <?php
    $ccTotal=0;
    foreach($paymentCC as $cc){
        ?>
        <tr>
            <td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= $cc->cc_type ?></font></b></td>
		    <td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney($cc->amount) ?></font></b></td>
        </tr>
        <?php
        $ccTotal=$ccTotal+$cc->amount;
    }

    $re_ccTotalBefRet=0;
    foreach($re_paymentCC as $re_cc){

        $re_ccTotalBefRet=$re_ccTotalBefRet+$re_cc->amount;
    }

    $ccTotal=$ccTotal+$re_ccTotalBefRet+$allSales->due+$rSales->due+$paymentgift_card->amount+$paymentbank_transfer->amount + $re_paymentbank_transfer->amount+$paymentcash->amount + $re_paymentcash->amount;
    ?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="3"><?= lang('sold_total') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="3"><?= $this->sma->formatMoney($ccTotal) ?></font></b></td>

	</tr>

	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="2" color="#212529"><?= lang('returns') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable" align="center"><b><font  size="2" color="#212529"></font></b></td>

	</tr>
    <tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Cash_payments').' ( '.lang('returned').' ) ' ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($re_paymentcash->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Bank_transfers').' ( '.lang('returned').' ) '; ?> </font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($re_paymentbank_transfer->amount)) ?></font></b></td>

	</tr>
    <tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('gift_cards').' ( '.lang('returned').' ) ' ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($re_paymentgift_card->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('award_points').' ( '.lang('returned').' ) ' ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($re_paymentgift_card_points->amount)) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= lang('Credit_sales').' ( '.lang('returned').' ) '; ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($rSales->due)) ?></font></b></td>

	</tr>
    <?php
    $re_ccTotal=0;
    foreach($re_paymentCC as $re_cc){
        ?>
        <tr>
            <td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="2"><?= $re_cc->cc_type.' ( '.lang('returned').' ) '; ?></font></b></td>
		    <td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="2"><?= $this->sma->formatMoney(abs($re_cc->amount)) ?></font></b></td>
        </tr>
        <?php
        $re_ccTotal=$re_ccTotal+$re_cc->amount;
    }
    $re_ccTotal=$re_ccTotal+$rSales->due+$re_paymentbank_transfer->amount+$re_paymentcash->amount+$re_paymentgift_card->amount;
    ?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><b><font  size="3"><?= lang('return_total') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="3"><?= $this->sma->formatMoney(abs($re_ccTotal)) ?></font></b></td>

	</tr>

	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="3"><?= lang('Total_cash_on_hand'); ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><b><font  size="3"><?= $this->sma->formatMoney(($posReg->cash_in_hand+$paymentcash->amount+$re_paymentcash->amount)) ?></font></b></td>
	</tr>
    <tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="3"><?= lang('Deficit_and_Excess'); ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable"   ><b><font  size="3" <?php if($posReg->Deficitandexcess > 0){ echo 'color="green" <i class="fa fa-arrow-up"></i>'; }else if($posReg->Deficitandexcess<0) echo 'color="red" <i class="fa fa-arrow-down"></i>'; ?> </font> <?= $this->sma->formatMoney(abs($posReg->Deficitandexcess)) ?></b></td>

	</tr>



</tbody></table>

            </div>
			</div>
			</div>
            </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Cashier_statistics'); ?>');
		    return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/profit_loss_xls/0/xls/?v=1' . $v)?>";
            return false;
        });


        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
