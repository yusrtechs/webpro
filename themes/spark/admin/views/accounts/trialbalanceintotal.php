<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */



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
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('Trial_balance_in_total'); ?>   <?php
            if ($this->input->post('start_date')) {
                echo lang('From').' '. substr($this->input->post('start_date'),0,10) .' '.lang('to').' ' . substr($this->input->post('end_date'),0,10);
            }?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
	<div class="row">
	<div class="col-sm-12">

	<div id="form">

                    <?php echo admin_form_open('accounts/trialbalanceintotal',' autocomplete="off"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>





                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
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


        <div class="row">
	<table class="table table-striped table-bordered table-hover">
		<thead class="thead-dark">
			<tr class="text-center">
				<th><?= lang('Account') ?></th>
				<th><?= lang('Debit') ?></th>
				<th><?= lang('Credit') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php


			$totalDebitAccount  = 0;
			$totalCreditAccount = 0;

			$accounts = array();

			asort($accountList);
			foreach ($accountList as $account){
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])){
					$accounts[$account['accountCategory']] = array($account);
				}
				else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}

			$accountCategories = array_keys($accounts);
			$accountOrder = 0;
			foreach ($accounts as $accountCategory){
				echo '
				<tr>
				<td><strong>'.$accountCategories[$accountOrder].':</strong></td>
				<td></td>
				<td></td>
				</tr>
				';
				$accountOrder += 1;

				$categoryCount = 0;
				$textDecor = 'none';

				$moneySign = 'TK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				foreach ($accountCategory as $account){
					//echo $account['accountID'];
					$categoryCount++;
					if ($categoryCount == count($accountCategory)){
						//$textDecor = 'underline';
					}

					if (!empty($_POST)){
						$accountTotal = $this->accounts_model->getAccountTotalDebitCredit($account['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));
					}
					else {
						$accountTotal = $this->accounts_model->getAccountTotalDebitCredit($account['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));
					}



					// if ($accountTotal == 0){
					// 	continue;
					// }

					if($accountTotal['debitTotal']==0 && $accountTotal['creditTotal']==0){
					}else{

					$totalDebitAccount += $accountTotal['debitTotal'];
					$totalCreditAccount += $accountTotal['creditTotal'];
					echo '
					<tr>
					<td>'.$account['accountName'].'</td>
					<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotal['debitTotal'] != abs($accountTotal['debitTotal'])){
						echo '('.$this->sma->formatMoney(abs($accountTotal['debitTotal'])).')';
					}
					else {
						echo $this->sma->formatMoney($accountTotal['debitTotal']);
					}
					echo '<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotal['creditTotal'] != abs($accountTotal['creditTotal'])){
						echo '('.$this->sma->formatMoney(abs($accountTotal['creditTotal'])).')';

					}
					else {
						echo $this->sma->formatMoney($accountTotal['creditTotal']);
					}
					echo '</td>
					</tr>
					';
					}

					// if ($account['accountSide'] == 'Left (Debit)'){
					// 	$totalDebitAccount += $accountTotal;
					// 	echo '
					// 	<tr>
					// 	<td>'.$account['accountName'].'</td>
					// 	<td style="text-decoration: '.$textDecor.';" class="text-right">'.$moneySign;
					// 	if ($accountTotal != abs($accountTotal)){
					// 		echo '('.number_format(abs($accountTotal), 0).')';
					// 	}
					// 	else {
					// 		echo number_format($accountTotal, 0);
					// 	}
					// 	echo '</td>
					// 	<td></td>
					// 	</tr>
					// 	';
					// }
					// else {
					// 	$totalCreditAccount += $accountTotal;
					// 	echo '
					// 	<tr>
					// 	<td>'.$account['accountName'].'</td>
					// 	<td></td>
					// 	<td style="text-decoration: '.$textDecor.';" class="text-right">'.$moneySign;
					// 	if ($accountTotal != abs($accountTotal)){
					// 		echo '('.number_format($accountTotal, 0).')';
					// 	}
					// 	else {
					// 		echo number_format($accountTotal, 0);
					// 	}
					// 	echo '</td>
					// 	</tr>
					// 	';
					// }

					$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

				}
			}









			echo '
			<tr>
			<td class="text-center"><strong>'.lang("total").'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($totalDebitAccount).'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($totalCreditAccount).'</strong></td>
			</tr>
			';
			?>
		</tbody>
	</table>
</div>
</div>


