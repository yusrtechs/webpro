<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>



	 .tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
<?php





$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

if ($this->input->post('group_id')) {
    $v .= '&group_id=' . $this->input->post('group_id');
}

if ($this->input->post('fixed_asset_id')) {
    $v .= '&fixed_asset_id=' . $this->input->post('fixed_asset_id');
}

if ($this->input->post('start_date')) {


	$month = date("m",strtotime($this->sma->fld($this->input->post('start_date'))));
	$year = date("Y",strtotime($this->sma->fld($this->input->post('start_date'))));

	$_POST['start_date']=$year."-".$month."-01";

    $v .= '&start_date=' . $this->input->post('start_date');
}

if ($this->input->post('end_date')) {

	$a_date = $this->sma->fld($this->input->post('end_date'));




	$_POST['end_date']=date("Y-m-t", strtotime($a_date));

    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        <?php if ($this->input->post('start_date') || $this->input->post('end_date') || $this->input->post('period_year')) { ?>
            $('#form').show();
        <?php } else { ?>
            $('#form').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideToggle();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
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


<div class="spark-fixedassets-depreciation-report-page">
    <div class="spark-fixedassets-depreciation-report-header">
        <div class="spark-fixedassets-depreciation-report-header-start">
            <div class="spark-fixedassets-depreciation-report-header-icon">
                <i class="fa fa-calculator"></i>
            </div>
            <div class="spark-fixedassets-depreciation-report-header-info">
                <h1 class="spark-fixedassets-depreciation-report-title">
                    <?= lang('Depreciation_report'); ?>
                    <?php
                    if ($this->input->post('start_date')) {
                        echo ' ' . (lang('From') ? lang('From') : 'From') . ' ' . $this->sma->hrsd($this->input->post('start_date')) . ' ' . (lang('to') ? lang('to') : 'to') . ' ' . $this->sma->hrsd($this->input->post('end_date'));
                    }
                    ?>
                </h1>
                <p class="spark-fixedassets-depreciation-report-subtitle">
                    <?= lang('manage_fixedassets_depreciation_report_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-fixedassets-depreciation-report-header-actions">
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

                    <?php echo admin_form_open('fixedassets/depreciation_report',' autocomplete="off"'); ?>
                    <div class="col-sm-12">




                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="group_id"><?= lang('Fixed_assets_group'); ?></label>
                                <?php
                                $gr[''] = lang('all');
                                foreach ($groups as $group) {
									$group = (array) $group;

										$gr[$group['id']] = $group['name'];


                                }
                                echo form_dropdown('group_id', $gr, (isset($_POST['group_id']) ? $_POST['group_id'] : ''), 'class="form-control  input-tip select" id="group_id"   style="width:100%"')
                                ?>
                            </div>
                        </div>

						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="fixed_asset_id"><?= lang('Fixed_asset'); ?></label>
                                <?php
                                $wh[''] = lang('all');
                                foreach ($fixedAssets as $fixedAsset) {
									$fixedAsset = (array) $fixedAsset;

										$wh[$fixedAsset['id']] = $fixedAsset['code']." - ".$fixedAsset['name'];


                                }
                                echo form_dropdown('fixed_asset_id', $wh, (isset($_POST['fixed_asset_id']) ? $_POST['fixed_asset_id'] : ''), 'class="form-control  input-tip select" id="fixed_asset_id"   style="width:100%"')
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
                                <?php echo form_input('start_date', ($this->input->post('start_date') ? $this->sma->hrsd($_POST['start_date']) : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', ($this->input->post('end_date') ? $this->sma->hrsd($_POST['end_date']) : ''), 'class="form-control date" id="end_date"'); ?>
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Depreciation_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>

					<?php
						if($this->input->post('group_id')){
					?>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo $groupName;  ?></font></b></td>

					</tr>
					<?php
						}
					?>


					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Depreciation_report') .' '. lang('From') .' '. $this->sma->hrsd($this->input->post('start_date')) . ' ' . lang('to') .' '. $this->sma->hrsd($this->input->post('end_date'))  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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








		foreach ($fixedAssetList as $account){

			$account = (array) $account;




			$id      = $account['id'];







			{










                $accountEntries=$this->fixedassets_model->getDepreciationsByFAId($account['id'], (isset($_POST['start_date']) ? $_POST['start_date'] : ''), (isset($_POST['end_date']) ? $_POST['end_date'] : ''));


				if($accountEntries){

					echo '<h2 class="pull-left">';
					?>
					<a href="<?= admin_url('fixedassets/fixed_asset_details/'.$account['id']); ?> ">
					<?php

					echo $account['name'].' ( '.$account['code'].' ) </a></h2>';



					echo '
					<table class="table table-striped table-bordered table-hover">
					<thead class="thead-dark">
					<tr class="text-center">
					<th>'.lang("Period").' ( '.lang("month").' ) </th>
					<th>'.lang("The_book_value").' - '.lang("Start_Period").'</th>
					<th>'.lang("Depreciation_percentage").'</th>
					<th>'.lang("Depreciation_of_fixed_asset").'</th>
					<th>'.lang("Accumulated_depreciation").'</th>
					<th>'.lang("The_book_value").' - '.lang("End_Period").'</th>

					</tr>
					</thead>
					<tbody class="searchable text-center">
					';





					$end_value=0;
					$accumulated_value=0;





				foreach ($accountEntries as $entry){
					$entry = (array) $entry;




					$accumulated_value=$accumulated_value + $entry['depreciation_amount'];



					echo '
					<tr>
					<td class="text-left align-middle">'.date('Y-m', strtotime($entry['period_date'])).'</td>';

					echo '<td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($entry['start_value']).'</strong><br />';
					echo '</td><td class="align-middle">';
					echo $this->sma->formatDecimal($entry['depreciation_ratio'],2).'%<br />';
					echo '</td>
					<td class="align-middle">';
					echo $this->sma->formatMoney($entry['depreciation_amount']).'<br />';
					echo '</td><td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($accumulated_value).'</strong><br />';
					echo '</td><td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($entry['end_value']).'</strong><br />';
					echo '</td>
					</tr>
					';







					$end_value=$entry['end_value'];

				}
				$total=0;
				$diffDebit=0;
				$diffCredit=0;



				echo '
				</tbody>
				<tfoot class="thead-dark">';

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
				<td colspan="5"><strong>'.lang("The_book_value").' - '.lang("End_Period").'</strong></td>';


				echo '

				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($end_value).'</strong></td>

				';

				echo '
				</tr>
				';





				echo '
				</tfoot></table>
				';

			}

			}
		}


		?>
	</div>




        </div>
    </div>
</div>
</div>
</div>



<script type="text/javascript">


    $(document).ready(function () {

        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Depreciation_report'); ?>');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('fixedassets/depreciation_report/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>

