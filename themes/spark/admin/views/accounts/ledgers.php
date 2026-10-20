<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php


if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
	//
$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($check_period->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($check_period->period_to_date);
	}
}


$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}

if ($this->input->post('account')) {
    $v .= '&account=' . $this->input->post('account');
}


if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


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
        <?php if (!$this->input->post('start_date') && !$this->input->post('submit_report') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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

<script>
    $(document).ready(function () {
       (function ($) {

        $('#filter').keyup(function () {


            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();

        })

    }(jQuery));
    });
</script>

<div class="spark-ledgers-page">
    <div class="spark-ledgers-header">
        <div class="spark-ledgers-header-start">
            <div class="spark-ledgers-header-icon">
                <i class="fa fa-book"></i>
            </div>
            <div class="spark-ledgers-header-info">
                <h1 class="spark-ledgers-title">
                    <?= lang('List_of_ledgers') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-ledgers-subtitle">
                    <?= lang('manage_ledgers_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-ledgers-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('download_xls'); ?></span>
            </button>

            <button type="button" id="pdf" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                <i class="fa fa-file-pdf-o"></i>
                <span><?= lang('download_pdf'); ?></span>
            </button>
        </div>
    </div>

<div class="box">

    <div class="box-content">

	<div class="row">
	<div class="col-sm-12">

	<div id="form">

                    <?php echo admin_form_open('accounts/ledgers',' autocomplete="off"'); ?>
                    <div class="col-sm-12">


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
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="account"><?= lang('Account'); ?></label>
                                <?php
                                $wh[''] = lang('all');
                                foreach ($accounts as $account) {
									$account = (array) $account;
                                    if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
										$wh[$account['id']] = $account['accountName'];
										}else{
										$wh[$account['id']] = $account['accountNameEn'];

									}
                                }
                                echo form_dropdown('account', $wh, (isset($_POST['account']) ? $_POST['account'] : ''), 'class="form-control" id="account" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Account') . '"');
                                ?>
                            </div>
                        </div>


                       <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="levels"><?= lang('Levels'); ?></label>
                                <?php
                                $levels_arr['0'] = lang('all');
                                $levels_arr['1'] = lang('Level_1');
                                $levels_arr['2'] = lang('Level_2');
                                $levels_arr['3'] = lang('Level_3');
                                $levels_arr['4'] = lang('Level_4');
                                $levels_arr['5'] = lang('Level_5');
                                $levels_arr['6'] = lang('Level_6');
                                $levels_arr['7'] = lang('Level_7');

                                echo form_dropdown('levels', $levels_arr, (isset($_POST['levels']) ? $_POST['levels'] : ''), 'class="form-control" id="levels" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Levels') . '"');
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
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






	</div>
	</div>
	<!--
	<div class="row">
				<div class="col-lg-8">
		<p>
			<?= lang('list_results'); ?>

		</p>
		</div>


		<div class="col-md-4" >
                                    <div class="form-group">
                                        <?php echo form_input('filter', '', 'class="form-control input-tip" id="filter" placeholder="' . lang('Search') . '"  '); ?>
                                    </div>
        </div>

	</div>-->

	<div class="row"  id="pr_pdf">
		<div class="col-sm-12 col-md-12">

		<div class="table-responsive">


		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Ledgers'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Ledgers') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
				</div>
			</div>
		</div>


        <div class="row">
            <div class="col-lg-12">








		<?php
		if(isset($_POST['account']) ){
			$accountList=$this->accounts_model->getAccounts($_POST['account']);
		}







		foreach ($accountList as $account){

			$account = (array) $account;
			$accountEntries = array();
			$prev_accountEntries = array();


			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];






			foreach($entryList as $entry){
				$entry = (array) $entry;
				if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
					array_push($accountEntries, $entry);
				}
			}
			//

			//if (!empty($accountEntries))
			{
				echo '<h2 class="pull-left">';
				?>

				<?php
				if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				echo $account['accountName'].'</h2>';
				}else{
				echo $account['accountNameEn'].'</a></h2>';

				}

				echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Entry_number").'</th>
				<th>'.lang("date").'</th>
				<th>'.lang("reference_no").'</th>
				<th>'.lang("Detailed_account").'</th>
				<th>'.lang("Debit").'</th>
				<th>'.lang("Credit").'</th>

				</tr>
				</thead>
				<tbody class="searchable text-center">
				';





				$balanceDebit=0;
				$balanceCredit=0;




			/*foreach ($accountList as $account){

			$account = (array) $account;

			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];
			*/

		foreach($prev_entryList as $prev_entry){
				$prev_entry = (array) $prev_entry;
//
			if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))){
					array_push($prev_accountEntries, $prev_entry);
				}
			}








			//if (!empty($prev_accountEntries))
			{




				$prev_fbalance=0;
				$sr=0;
				foreach ($prev_accountEntries as $entry){
					$entry = (array) $entry;

					$entryDebitAccounts = array();
					$entryDebitBalances = 0;

					$entryCreditAccounts = array();
					$entryCreditBalances = 0;

					foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
						$entryCreditBalances += 1;
					}





					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance+$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance-$balance;




							$y++;
						}
					}







				}


				}
		//}
		//echo $prev_fbalance;






				if($prev_fbalance){
					$sr++;
					echo '
					<tr>
					<td class="text-left align-middle">'.$sr.'</td>




					<td></td>
					<td class="text-left align-middle">'.lang('Carry-over_balance').' '.lang('Before').' '.(isset($_POST['start_date']) ? $_POST['start_date'] : '').'</td>
					<td></td>
					<td></td>
					<td class="align-middle">';


						if ($prev_fbalance >= 0){

							echo '<strong>'.$this->sma->formatMoney($prev_fbalance).'</strong><br />';
							$balanceDebit=$balanceDebit+$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



						}
					echo '			</td>
					<td class="align-middle">';
						if ($prev_fbalance < 0){
							echo '<strong>'.$this->sma->formatMoney(abs($prev_fbalance)).'</strong><br />';
							$balanceCredit=$balanceCredit-$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;





						}




					echo '			</td>




					</tr>
					';

					}





				foreach ($accountEntries as $entry){
					$entry = (array) $entry;

					$entryDebitAccounts = array();
					$entryDebitBalances = 0;

					$entryCreditAccounts = array();
					$entryCreditBalances = 0;

					foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
						$entryCreditBalances += 1;
					}





					$sr++;
					echo '
					<tr>
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';
					?>
				<a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
                                <?= '#'.($entry['entry_no'] ? $entry['entry_no'] : $entry['entryID']) ?>
                            </a>

				<?php
					echo '</td>
					<td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td><td class="text-left align-middle">'.$entry['reference_no'].'</td>';
					$entryDescriptionText=$entry['entryDescription'];

						$entryDescription= str_replace("<p>","",($entryDescriptionText)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
						$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
					echo '
					<td class="text-left align-middle">'.lang($entryDescription).'</td>';

					echo '<td class="align-middle">';
					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance).'</strong><br />';
							$balanceDebit=$balanceDebit+$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



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
							echo '<strong>'.$this->sma->formatMoney($balance).'</strong><br />';
							$balanceCredit=$balanceCredit+$balance;



							$y++;
						}
					}

				echo '</td>
					</tr>
					';







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
						$diffDebit=$this->sma->formatMoney($diffDebit);
					else
						$diffDebit='';

				if($diffCredit!=0)
						$diffCredit=$this->sma->formatMoney($diffCredit);
					else
						$diffCredit='';

					echo '
				<tr class="text-center">
				<td colspan="5"><strong>'.lang("total").'</strong></td>';
				if($diffDebit!=''){

				echo '

				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($total).'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;"> </strong></td>
				';
				}else{
				echo '
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;"></strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;"> '.$this->sma->formatMoney($total).'</strong></td>
				';
				}
				echo '
				</tr>
				';

				echo '
				<tr class="text-center">
				<td colspan="5"><strong>'.lang("balance").'</strong></td>';

				if($diffDebit!=''){

				echo '
				<td style="text-decoration: underline;"><strong>'.$diffDebit.'</strong></td>
				<td style="text-decoration: underline;"><strong><font color="green"><i class="fa fa-arrow-right"></i></font>'.$diffDebit.'</strong></td>';
				}else{
				echo '
				<td style="text-decoration: underline;"><strong>'.$diffCredit.'<font color="green"><i class="fa fa-arrow-left"></i></font></strong></td>
				<td style="text-decoration: underline;"><strong>'.$diffCredit.'</strong></td>';

				}


				echo '
				</tr>';

				}

				echo '
				</tfoot></table>
				';

			}
		}


		?>
	</div>




        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Ledgers'); ?>','L');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/ledgers/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>

