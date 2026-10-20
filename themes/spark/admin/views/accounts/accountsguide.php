<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>

	/*#pr_pdf table td,#pr_pdf table th{
		padding: 0.2rem;
		font-size: 13px;
	}*/



    /*  table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
*/

	 table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
<?php



$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

if ($this->input->post('account')) {
    $v .= '&account=' . $this->input->post('account');
}

if ($this->input->post('levels')) {
    $v .= '&levels=' . $this->input->post('levels');
}

$levels=$this->input->post('levels') ? $this->input->post('levels') : NULL;
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('levels') && !$this->input->post('submit_report')) { ?>
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

<div class="spark-accountsguide-page">
    <div class="spark-accountsguide-header">
        <div class="spark-accountsguide-header-start">
            <div class="spark-accountsguide-header-icon">
                <i class="fa fa-list-alt"></i>
            </div>
            <div class="spark-accountsguide-header-info">
                <h1 class="spark-accountsguide-title">
                    <?= lang('Accounts_guide'); ?>
                </h1>
                <p class="spark-accountsguide-subtitle">
                    <?= lang('manage_accountsguide_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-accountsguide-header-actions">
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

                    <?php echo admin_form_open('accounts/accountsguide',' autocomplete="off"'); ?>
                    <div class="col-sm-12">



                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="account"><?= lang('Master_account'); ?></label>
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


                       <div class="col-sm-3" >
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Accounts_guide'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>

					</thead>
					</table>
				</div>
			</div>
		</div>


        <div class="row">
            <div class="col-lg-12">


            <table class="table table-striped table-bordered table-hover">
		<thead class="thead-dark">
			<tr class="text-center">
				<th><?= lang('Account_code') ?></th>
				<th><?= lang('Account_name') ?></th>
				<th><?= lang('English_name') ?></th>
				<th><?= lang('Account_type') ?></th>
			</tr>
		</thead>
				<tbody >





		<?php
		if(isset($_POST['account']) && $_POST['account']!=''){

			$accountChilds = $this->accounts_model->getAccounts($_POST['account']);
			$accountChilds = (array) $accountChilds[0];

			$accountList=$this->accounts_model->getAccountChildsGuide($accountChilds['accountID'],$levels);


			//$accountList=$this->accounts_model->getAccounts($_POST['account'],$levels);
		}else{
			$accountList=$this->accounts_model->getAccounts(null,$levels);
		}



			$accounts = array();

	foreach ($accountList as $account){
		$account = (array) $account;

		if (!isset($accounts[$account['accountCategory']])){
			$accounts[$account['accountCategory']] = array($account);
		}
		else {
			array_push($accounts[$account['accountCategory']], $account);
		}
	}
	//$this->sma->print_arrays($accounts);


	$accountCategories = array_keys($accounts);
	$accountOrder = 0;
			foreach ($accounts as $accountCategory){
				/*echo '
				<tr>
				<td><strong>'.$accountCategories[$accountOrder].':</strong></td>
				<td></td>
				<td></td>
				<td></td>
				</tr>
				';*/
				$moneySign = '';
				$accountSubCategory = NULL;
				$subTotal = 0;




				foreach ($accountCategory as $account){

			//$accountChilds = $this->accounts_model->getAccounts($account['id']);
			//$accountChilds_levels=$accountChilds;

			//$accountChilds = (array) $accountChilds[0];



					$accountStatement="";
					if($account['accountStatement']==="Income Statement")
						$accountStatement=lang('Income_Statement');
					else
						$accountStatement=lang('Balance_Sheet');
					echo '
					<tr>
					<td>'. $account['accountID'] .'</td>
					<td>'. $account['accountName'] .'</td>
					<td>'.($account['accountNameEn'] ? $account['accountNameEn'] : $account['accountName']).'</td>';
					if($account['level']==1){
						echo '
						<td>'.$account['accountCategory'] .'</td>';
					}else{
						echo '
						<td>'. $accountStatement .' - '. $account['accountCategory'] .'</td>';
					}

					echo '
					</tr>
					';

				}

				/*else{
					if ($equityTotal != abs($equityTotal)){
						$accountAmount = '('.number_format(abs($equityTotal), 0).')';
					}
					else {
						$accountAmount = number_format($equityTotal, 0);
					}
					echo '
					<tr>
					<td class="text-center"><strong>Total '.$accountCategories[$accountOrder].'</strong></td>
					<td class="text-right" style="text-decoration: underline; border-top: 2px solid #000000;"><strong>TK&nbsp;&nbsp;&nbsp;'.$accountAmount.'</strong></td>
					</tr>
					';
				}*/
				$accountOrder += 1;
			}

		//






		?>
				</tbody>
	</table>

	</div>




        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Accounts_guide'); ?>');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/accountsguide/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>

