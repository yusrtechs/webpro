<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
	<style>
		@media print {
                .no-print { display: none !important; }
				.tbl table  {
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        padding: 0.1in !important;


    }
	.tbl table td{
		-webkit-print-color-adjust:exact !important;
	}


    }
	</style>
<?php

$v = '';

$v .= '&bilshow_Opening_balanceler=1';
$v .= '&show_style=1';
$v .= '&Carry_over_balance=0';
if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}

?>
<div class="spark-tax-page">
    <div class="spark-tax-header">
        <div class="spark-tax-header-start">
            <div class="spark-tax-header-icon">
                <i class="fa fa-percent"></i>
            </div>
            <div class="spark-tax-header-info">
                <h1 class="spark-tax-title">
                    <?= lang('Tax') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-tax-subtitle">
                    <?= lang('manage_tax_desc'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-content">
            <ul id="myTab" class="nav nav-tabs no-print spark-nav-tabs" style="margin-bottom: 20px;">
                <li class="active"><a href="#sales-con" data-toggle="tab" class="tab-grey"><?= lang('sales') ?></a></li>
                <li class=""><a href="#purchases-con" data-toggle="tab" class="tab-grey"><?= lang('purchases') ?></a></li>
                <li class=""><a href="#tax-con" data-toggle="tab" class="tab-grey"><?= lang('Tax_Declaration') ?></a></li>
            </ul>

            <div class="tab-content">
                <div id="sales-con" class="tab-pane fade in active">

<script type="text/javascript">
    $(document).ready(function () {

        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('glbrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>
        <?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
            $('#form').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideToggle(200);
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp(200);
            return false;
        });
    });
</script>

<div class="box box1">
    <div class="spark-tax-header" style="margin-bottom: 16px;">
        <div class="spark-tax-header-start">
            <div class="spark-tax-header-icon">
                <i class="fa fa-percent"></i>
            </div>
            <div class="spark-tax-header-info">
                <h2 class="spark-tax-title">
                    <?= lang('VAT_on_sales'); ?>
                </h2>
            </div>
        </div>

        <div class="spark-tax-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('download_xls'); ?></span>
            </button>

            <button type="button" id="pdf1" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                <i class="fa fa-file-pdf-o"></i>
                <span><?= lang('download_pdf'); ?></span>
            </button>
        </div>
    </div>

    <div class="box-content">




		<div class="row">
			<div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('accounts/tax'); ?>
                    <div class="row">

                        <!--<div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>-->

                        <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
                                                    $br['']=lang('all');
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>






		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_sales') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="salesdiv" id="salesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>

	</tr>

	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>
<?php
$Tgrand_total=($accountVatTotal / $active_tax * 100);
$Tret_grand_total=0;
?>
    <tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Sales_subject_to_the_standard')." ".(int)$this->sma->formatNumber($active_tax)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Tgrand_total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($Tret_grand_total)) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($accountVatTotal) ?></font></td>

	</tr>

	<?php
	$total_sales_amount=$Tgrand_total;
	$total_sales_amount_returned=$Tret_grand_total;
	$total_sales_tax=$accountVatTotal;
    ?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_sales') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>
        <?php

$accountSide="";

$accountInfo = $this->accounts_model->getAccountByID($sales_vat_acct);
$accountSide=$accountInfo->accountSide;
if ($this->Settings->user_language == 'arabic' || $accountInfo->accountNameEn==''){
    $accountName= $accountInfo->accountName;
    }else{
    $accountName=$accountInfo->accountNameEn;

    }

?>
		<ul id="myTab2" class="nav nav-tabs no-print">
			<li class=""><a href="#allsales-con" class="tab-grey"><?= lang('Account_statement') .' - '. $accountName ?></a></li>
			<li style="display:none;"><a href="#creditnotes-con" class="tab-grey"><?= lang('Credit_Notes') ?></a></li>
			<li style="display:none;"><a href="#creditnotesout-con" class="tab-grey"><?= lang('Previous_period_credit_notes') ?></a></li>
		</ul>

		<div class="tab-content">
		<div id="allsales-con" class="tab-pane fade in">



		<div class="row">
			<div class="col-sm-12 col-md-12">

            <?php

//if($this->input->post('show_style')==1)
{









    //print_r($accountListSales);
    foreach ($accountListSales as $account1){





    $account1 = (array) $account1;
    $accountSide="";

    if($accountSide==""){
        $accountInfo = $this->accounts_model->getAccountByID($account1['id']);
        $accountSide=$accountInfo->accountSide;
    }
    //if($accountSide==""){
    if($account1['id']==1)
        $accountSide="Debit";
    if($account1['id']==2)
        $accountSide="Credit";
    if($account1['id']==3)
        $accountSide="Debit";
    if($account1['id']==4)
        $accountSide="Credit";
    //}

    //echo $account1['id']." ".$accountSide."<br/>";


    //$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);

    $accountList1=$this->accounts_model->getAccountChildsWithLevels($account1['accountID'],$levels);

    //$accountList1 = $this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID);//




        echo '
        <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
        <tr class="text-center">
        <th>'.lang("Sr#").'</th>
        <th>'.lang("Entry_number").'</th>
        <th>'.lang("date").'</th>
        <th>'.lang("Detailed_account").'</th>
        <th>'.lang("Debit").' ('.$this->Settings->symbol.')</th>
        <th>'.lang("Credit").' ('.$this->Settings->symbol.')</th>
        <th>'.lang("balance").' ('.$this->Settings->symbol.')</th>

        </tr>
        </thead>
        <tbody class="searchable text-center">
        ';





        $balanceDebit=0;
        $balanceCredit=0;
        $fbalance=0;
        $sr=0;



//$this->sma->print_arrays($prev_fbalance);
//$this->data['prev_fbalance']  = $prev_fbalance;






    foreach ($accountList1 as $account){

    $account = (array) $account;
    $accountEntries = array();


    //if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
    {



    $id      = $account['id'];
    $accountBalance = $account['accountDebit'] - $account['accountCredit'];


    foreach($entryList as $entry){
        $entry = (array) $entry;
        if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
            array_push($accountEntries, $entry);
        }
    }
    //


    if (!empty($accountEntries))
    {
        /*echo '<h2 class="pull-left">';
        ?>
        <a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
        <?php
        if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
        echo $account['accountName'].'</a></h2>';
        }else{
        echo $account['accountNameEn'].'</a></h2>';

        }*/





        foreach ($accountEntries as $entry){
            $entry = (array) $entry;

            $entryDebitAccounts = array();
            $entryDebitBalances = 0;




            $entryCreditAccounts = array();
            $entryCreditBalances = 0;

            $entryDescriptionText = "";


            foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
                array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
                if(json_decode($entry['entryDebitAccount'])[$entryDebitBalances]== $id){
                    if($entryDescriptionText=="")
                        $entryDescriptionText = json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
                    else
                    $entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryDebitDescription'])[$entryDebitBalances];

                }
                $entryDebitBalances += 1;
            }

            foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
                array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
                if(json_decode($entry['entryCreditAccount'])[$entryCreditBalances]== $id){
                    if($entryDescriptionText=="")
                        $entryDescriptionText = json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
                    else
                    $entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryCreditDescription'])[$entryCreditBalances];

                }
                $entryCreditBalances += 1;
            }






            $sr++;

                echo '
                <tr>';

            echo '
            <td class="text-left align-middle">'.$sr.'</td>

            <td class="align-middle">';
            ?>
        <a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
        <?= '#'.($entry['entry_no'] ? $entry['entry_no'] : $entry['entryID']) ?>
                    </a>

        <?php

        echo '</td>

            <td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>';
            if($entry['entryType']!="Opening_balance"){
                if($entryDescriptionText=="")
                $entryDescriptionText=$entry['entryDescription'];

                $entryDescription= str_replace("<p>","",($entryDescriptionText)."");
                $entryDescription= str_replace("</p>","",($entryDescription)."");
                $iof=strpos(trim($entryDescription), "#");
                if($iof>-1)
                    $entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
            echo '
            <td class="text-left align-middle">'.lang($entryDescription).'</td>
            <td class="align-middle">';
        }else{
            echo '
            <td class="text-left align-middle">'.lang($entry['entryType']).'</td>
            <td class="align-middle">';

        }
            foreach ($entryDebitAccounts as $account){
                $balance = $account[key($account)];
                $account = key($account);
                $accountInfo = $this->accounts_model->getAccountByID($account);

                $x = 0;
                if ($accountInfo->id == $id){
                    echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
                    $balanceDebit=$balanceDebit+$balance;
                    if($accountSide=="Debit")
                        $fbalance=$fbalance+$balance;
                    else
                        $fbalance=$fbalance-$balance;



                    $x++;
                }
            }
            echo '			</td>
            <td class="align-middle">';
            foreach ($entryCreditAccounts as $account){
                $balance = $account[key($account)];
                $account = key($account);
                $accountInfo = $this->accounts_model->getAccountByID($account);

                $y = 0;
                if ($accountInfo->id == $id){
                    echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
                    $balanceCredit=$balanceCredit+$balance;
                    if($accountSide=="Credit")
                        $fbalance=$fbalance+$balance;
                    else
                        $fbalance=$fbalance-$balance;


                    $y++;
                }
            }

            if($accountSide=="Debit"){
                if($fbalance>0){

            echo '			</td>

            <td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';
                }else{
            echo '			</td>

            <td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';

                }
            }

            if($accountSide=="Credit"){
                if($fbalance>0){

            echo '			</td>

            <td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';
                }else{
            echo '			</td>

            <td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';

                }
            }






        }

    }


    }
    }

    $total=0;
        $diffDebit=0;
        $diffCredit=0;
        if($balanceDebit>$balanceCredit){
            $total=$balanceDebit;
            $diffDebit=($balanceDebit-$balanceCredit);
        }else{
            $total=$balanceCredit;
            if($balanceCredit>$balanceDebit)
            $diffCredit=($balanceCredit-$balanceDebit);
        }


        echo '
        </tbody>
        <tfoot class="thead-dark">';
        if($diffDebit!=0 || $diffCredit!=0){
            if($diffDebit!=0)
                $diffDebit=$this->sma->formatMoney($diffDebit,' ');
            else
                $diffDebit='';

        if($diffCredit!=0)
                $diffCredit=$this->sma->formatMoney($diffCredit,' ');
            else
                $diffCredit='';





        echo '
        <td></td>

        </tr>';

        }


        echo '
        <tr class="text-center">
        <td colspan="4"><strong style="font-size:18px;">'.lang("total").'</strong></td>
        <td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceDebit,' ').'</strong></td>
        <td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceCredit,' ').'</strong></td>';


        if($accountSide=="Debit"){
                if($fbalance>0){

        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
                }else{
        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

                }
            }

            if($accountSide=="Credit"){
                if($fbalance>0){

        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
                }else{
            echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

                }
            }




        echo '
        </tr>
        </tfoot></table>
        ';
}












}
?>

				</div>
				</div>
		</div>


		<div id="creditnotes-con" class="tab-pane fade in">



		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXDataCN"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>



                    </table>
                </div>

				</div>
				</div>
		</div>






		<div id="creditnotesout-con" class="tab-pane fade in">



		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXDataCNOut"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>



                    </table>
                </div>

				</div>
				</div>
		</div>


		</div>


	</div>
	</div>












    </div>
</div>
</div>

<div id="purchases-con" class="tab-pane fade in">

    <script type="text/javascript">
        $(document).ready(function () {
            <?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
                $('#purchaseform').hide();
            <?php } ?>
            $('.purchasetoggle_down').click(function () {
                $("#purchaseform").slideToggle(200);
                return false;
            });
            $('.purchasetoggle_up').click(function () {
                $("#purchaseform").slideUp(200);
                return false;
            });
        });
    </script>

    <div class="box box2">
        <div class="spark-tax-header" style="margin-bottom: 16px;">
            <div class="spark-tax-header-start">
                <div class="spark-tax-header-icon">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="spark-tax-header-info">
                    <h2 class="spark-tax-title">
                        <?= lang('VAT_on_purchases'); ?>
                    </h2>
                </div>
            </div>

            <div class="spark-tax-header-actions">
                <button type="button" class="spark-btn-secondary-action purchasetoggle_down tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>

                <button type="button" id="xls1" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                    <i class="fa fa-file-excel-o"></i>
                    <span><?= lang('download_xls'); ?></span>
                </button>

                <button type="button" id="pdf2" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                    <i class="fa fa-file-pdf-o"></i>
                    <span><?= lang('download_pdf'); ?></span>
                </button>
            </div>
        </div>

    <div class="box-content">



        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="purchaseform">

                    <?php echo admin_form_open('accounts/tax'); ?>
                    <div class="row">

                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
                                                    $br['']=lang('all');
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>






				<div class="row">
			<div class="col-sm-12 col-md-12">

				<div  id="pr_pdf2">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_purchases') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="purchasesdiv" id="purchasesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>

	</tr>

	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>

<?php
$Tgrand_total=($accountPuVatTotal / $active_tax * 100);
$Tret_grand_total=0;
?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Purchases_subject_to_the_standard')." ".(int)$this->sma->formatNumber($active_tax)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Tgrand_total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($Tret_grand_total)) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($accountPuVatTotal) ?></font></td>

	</tr>
	<?php
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_basic_detail->total;
	//$total_purchases_tax=$total_purchases_tax+$purchase_tax_local_basic_detail->total_tax;
	/*
	$total_purchases_amount=$total_purchases_amount+($purchase_tax_local_basic_detail->total );
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($ret_data_amendment['total']);
	$total_purchases_tax=$total_purchases_tax+(($purchase_tax_local_basic_detail->total_tax  + ($ret_data_amendment['total_tax'])));
	*/
	$total_purchases_amount=$Tgrand_total;
	$total_purchases_amount_returned=$Tret_grand_total;
	$total_purchases_tax=$accountPuVatTotal;


		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_purchases') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>
        <?php

$accountSide="";

$accountInfo = $this->accounts_model->getAccountByID($purchases_vat_acct);
$accountSide=$accountInfo->accountSide;
if ($this->Settings->user_language == 'arabic' || $accountInfo->accountNameEn==''){
    $accountName= $accountInfo->accountName;
    }else{
    $accountName=$accountInfo->accountNameEn;

    }

?>

		<ul id="myTab3" class="nav nav-tabs no-print">
			<li class=""><a href="#allpurchases-con" class="tab-grey"><?= lang('Account_statement') .' - '. $accountName ?></a></li>
			<li style="display:none;"><a href="#debitnotes-con" class="tab-grey"><?= lang('Debit_Notes') ?></a></li>
			<li style="display:none;"><a href="#debitnotesout-con" class="tab-grey"><?= lang('Previous_period_debit_notes') ?></a></li>
		</ul>

		<div class="tab-content">
		<div id="allpurchases-con" class="tab-pane fade in">

		<div class="row">
			<div class="col-sm-12 col-md-12">

            <?php

//if($this->input->post('show_style')==1)
{









    //print_r($accountListPurchases);
    foreach ($accountListPurchases as $account1){





    $account1 = (array) $account1;
    $accountSide="";

    if($accountSide==""){
        $accountInfo = $this->accounts_model->getAccountByID($account1['id']);
        $accountSide=$accountInfo->accountSide;
    }
    //if($accountSide==""){
    if($account1['id']==1)
        $accountSide="Debit";
    if($account1['id']==2)
        $accountSide="Credit";
    if($account1['id']==3)
        $accountSide="Debit";
    if($account1['id']==4)
        $accountSide="Credit";
    //}

    //echo $account1['id']." ".$accountSide."<br/>";


    //$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);

    $accountList1=$this->accounts_model->getAccountChildsWithLevels($account1['accountID'],$levels);

    //$accountList1 = $this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID);//




        echo '
        <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
        <tr class="text-center">
        <th>'.lang("Sr#").'</th>
        <th>'.lang("Entry_number").'</th>
        <th>'.lang("date").'</th>
        <th>'.lang("Detailed_account").'</th>
        <th>'.lang("Debit").' ('.$this->Settings->symbol.')</th>
        <th>'.lang("Credit").' ('.$this->Settings->symbol.')</th>
        <th>'.lang("balance").' ('.$this->Settings->symbol.')</th>

        </tr>
        </thead>
        <tbody class="searchable text-center">
        ';





        $balanceDebit=0;
        $balanceCredit=0;
        $fbalance=0;
        $sr=0;



//$this->sma->print_arrays($prev_fbalance);
//$this->data['prev_fbalance']  = $prev_fbalance;






    foreach ($accountList1 as $account){

    $account = (array) $account;
    $accountEntries = array();


    //if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
    {



    $id      = $account['id'];
    $accountBalance = $account['accountDebit'] - $account['accountCredit'];


    foreach($entryList as $entry){
        $entry = (array) $entry;
        if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
            array_push($accountEntries, $entry);
        }
    }
    //


    if (!empty($accountEntries))
    {
        /*echo '<h2 class="pull-left">';
        ?>
        <a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
        <?php
        if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
        echo $account['accountName'].'</a></h2>';
        }else{
        echo $account['accountNameEn'].'</a></h2>';

        }*/





        foreach ($accountEntries as $entry){
            $entry = (array) $entry;

            $entryDebitAccounts = array();
            $entryDebitBalances = 0;




            $entryCreditAccounts = array();
            $entryCreditBalances = 0;

            $entryDescriptionText = "";


            foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
                array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
                if(json_decode($entry['entryDebitAccount'])[$entryDebitBalances]== $id){
                    if($entryDescriptionText=="")
                        $entryDescriptionText = json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
                    else
                    $entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryDebitDescription'])[$entryDebitBalances];

                }
                $entryDebitBalances += 1;
            }

            foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
                array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
                if(json_decode($entry['entryCreditAccount'])[$entryCreditBalances]== $id){
                    if($entryDescriptionText=="")
                        $entryDescriptionText = json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
                    else
                    $entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryCreditDescription'])[$entryCreditBalances];

                }
                $entryCreditBalances += 1;
            }






            $sr++;

                echo '
                <tr>';

            echo '
            <td class="text-left align-middle">'.$sr.'</td>

            <td class="align-middle">';
            ?>
        <a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
        <?= '#'.($entry['entry_no'] ? $entry['entry_no'] : $entry['entryID']) ?>
                    </a>

        <?php

        echo '</td>

            <td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>';
            if($entry['entryType']!="Opening_balance"){
                if($entryDescriptionText=="")
                $entryDescriptionText=$entry['entryDescription'];

                $entryDescription= str_replace("<p>","",($entryDescriptionText)."");
                $entryDescription= str_replace("</p>","",($entryDescription)."");
                $iof=strpos(trim($entryDescription), "#");
                if($iof>-1)
                    $entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
            echo '
            <td class="text-left align-middle">'.lang($entryDescription).'</td>
            <td class="align-middle">';
        }else{
            echo '
            <td class="text-left align-middle">'.lang($entry['entryType']).'</td>
            <td class="align-middle">';

        }
            foreach ($entryDebitAccounts as $account){
                $balance = $account[key($account)];
                $account = key($account);
                $accountInfo = $this->accounts_model->getAccountByID($account);

                $x = 0;
                if ($accountInfo->id == $id){
                    echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
                    $balanceDebit=$balanceDebit+$balance;
                    if($accountSide=="Debit")
                        $fbalance=$fbalance+$balance;
                    else
                        $fbalance=$fbalance-$balance;



                    $x++;
                }
            }
            echo '			</td>
            <td class="align-middle">';
            foreach ($entryCreditAccounts as $account){
                $balance = $account[key($account)];
                $account = key($account);
                $accountInfo = $this->accounts_model->getAccountByID($account);

                $y = 0;
                if ($accountInfo->id == $id){
                    echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
                    $balanceCredit=$balanceCredit+$balance;
                    if($accountSide=="Credit")
                        $fbalance=$fbalance+$balance;
                    else
                        $fbalance=$fbalance-$balance;


                    $y++;
                }
            }

            if($accountSide=="Debit"){
                if($fbalance>0){

            echo '			</td>

            <td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';
                }else{
            echo '			</td>

            <td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';

                }
            }

            if($accountSide=="Credit"){
                if($fbalance>0){

            echo '			</td>

            <td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';
                }else{
            echo '			</td>

            <td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



            </tr>
            ';

                }
            }






        }

    }


    }
    }

    $total=0;
        $diffDebit=0;
        $diffCredit=0;
        if($balanceDebit>$balanceCredit){
            $total=$balanceDebit;
            $diffDebit=($balanceDebit-$balanceCredit);
        }else{
            $total=$balanceCredit;
            if($balanceCredit>$balanceDebit)
            $diffCredit=($balanceCredit-$balanceDebit);
        }


        echo '
        </tbody>
        <tfoot class="thead-dark">';
        if($diffDebit!=0 || $diffCredit!=0){
            if($diffDebit!=0)
                $diffDebit=$this->sma->formatMoney($diffDebit,' ');
            else
                $diffDebit='';

        if($diffCredit!=0)
                $diffCredit=$this->sma->formatMoney($diffCredit,' ');
            else
                $diffCredit='';





        echo '
        <td></td>

        </tr>';

        }


        echo '
        <tr class="text-center">
        <td colspan="4"><strong style="font-size:18px;">'.lang("total").'</strong></td>
        <td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceDebit,' ').'</strong></td>
        <td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceCredit,' ').'</strong></td>';


        if($accountSide=="Debit"){
                if($fbalance>0){

        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
                }else{
        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

                }
            }

            if($accountSide=="Credit"){
                if($fbalance>0){

        echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
                }else{
            echo '
        <td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

                }
            }




        echo '
        </tr>
        </tfoot></table>
        ';
}












}
?>

				</div>
				</div>







		</div>

		<div id="debitnotes-con" class="tab-pane fade in">

		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXDataDN"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>
				</div>




		<div id="debitnotesout-con" class="tab-pane fade in">

		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXDataDNOut"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>
				</div>


		</div>




	</div>
	</div>








            </div>





        </div>



    </div>



<!-----tax-------->
<div id="tax-con" class="tab-pane fade in">
<script type="text/javascript">
		$(document).ready(function () {
			<?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
				$('#purchaseform_tax').hide();
			<?php } ?>
			$('.purchasetoggle_down_tax').click(function () {
				$("#purchaseform_tax").slideToggle(200);
				return false;
			});
			$('.purchasetoggle_up_tax').click(function () {
				$("#purchaseform_tax").slideUp(200);
				return false;
			});
		});
	</script>
	<div class="box box2">
        <div class="spark-tax-header" style="margin-bottom: 16px;">
            <div class="spark-tax-header-start">
                <div class="spark-tax-header-icon">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="spark-tax-header-info">
                    <h2 class="spark-tax-title">
                        <?= lang('Tax_Declaration'); ?>
                    </h2>
                </div>
            </div>

			<div class="spark-tax-header-actions">
                <button type="button" class="spark-btn-secondary-action purchasetoggle_down_tax tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>

                <button type="button" id="pdf3" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                    <i class="fa fa-file-pdf-o"></i>
                    <span><?= lang('download_pdf'); ?></span>
                </button>

                <button type="button" id="print" class="spark-btn-secondary-action tip" title="<?= lang('print') ?>">
                    <i class="fa fa-print"></i>
                    <span><?= lang('print'); ?></span>
                </button>
			</div>
		</div>


		<div class="box-content">
			<p class="introtext"><?= lang('Value_added_tax_for_the_current_period_if_it_is_positive_on_you_and_if_it_is_negative_for_you'); ?></p>
			<div id="pr_pdf3">

			<div class="no-print" id="purchaseform_tax">

            <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
                     echo admin_form_open('accounts/tax');
                }else{
                     echo admin_form_open('accounts/tax');

                        }
                        ?>


				<div class="row">

					<!--<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="biller"><?= lang('biller'); ?></label>
							<?php
							$bl[''] = lang('select') . ' ' . lang('biller');
							foreach ($billers as $biller) {
								$bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
							}
							echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
							?>
						</div>
					</div>-->
					<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
                                                    $br['']=lang('all');
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>
					<div class="col-sm-3">
						<div class="form-group">
							<?= lang('start_date', 'start_date'); ?>
							<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<?= lang('end_date', 'end_date'); ?>
							<?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div
						class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
				</div>
				<?php echo form_close(); ?>

			</div>

			<div class="clearfix"></div>





			<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl">
					<table border="0" cellpadding="0" cellspacing="0" class="table">


					<thead  style="width:100%;border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('Tax_Declaration'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"    class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;-webkit-print-color-adjust:exact !important;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Tax_Declaration') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody  width="100%" class="taxsalesdiv">



					</tbody>
					<tr>
					<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="3"><?= lang('Total_VAT_due_for_the_current_period') ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_amount-$total_purchases_amount) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal(abs($total_sales_amount_returned)-abs($total_purchases_amount_returned)) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_tax-$total_purchases_tax) ?></font></b></td>

					</tr>

					</table>





				</div>


			</div>




		</div>









			<div class="clearfix"></div>
			<!-----------end --->
		</div>
	</div>
	</div>


</div>
<!---------------->

</div>
</div>
</div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js">
</script>
<script type="text/javascript">
    $(document).ready(function () {


		$(function(){

			var htmlsales = document.getElementById("salesdiv").innerHTML;
            var htmlpurchases = document.getElementById("purchasesdiv").innerHTML;
            $('.taxsalesdiv').html(htmlsales+' '+htmlpurchases);
			//htmlString = document.getElementById("purchasesdiv").innerHTML;
            //$('.taxsalesdiv').appendTo(htmlString);


		});

		$('#pdf1').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf1','<?= lang('sales'); ?>');
		    return false;
        });

		$('#pdf2').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf2','<?= lang('purchases'); ?>');
		    return false;
        });

        $('#pdf3').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf3','<?= lang('Tax_Declaration'); ?>');
		    return false;
        });


        $('#print').click(function (event) {
            window.print();
			return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/accountstatement/0/xls/?v=1&account=' . $sales_vat_acct.''. $v)?>";
            return false;
        });
        $('#xls1').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/accountstatement/0/xls/?v=1&account=' . $purchases_vat_acct.''. $v)?>";
            return false;
        });

        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box1'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
        $('#image1').click(function (event) {
            event.preventDefault();
            html2canvas($('.box2'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
