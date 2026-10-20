<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>



	 .tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
<?php

if (empty($_POST)){

$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($check_period->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($check_period->period_to_date);
	}else{

		$_POST['start_date']=$this->sma->hrsd($max_min_period_year->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($max_min_period_year->period_to_date);
	}
}



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
}else{
    $_POST['start_date']=$this->sma->hrsd($max_min_period_year->period_from_date);

    $month = date("m",strtotime($this->sma->fld($_POST['start_date'])));
	$year = date("Y",strtotime($this->sma->fld($_POST['start_date'])));

	$_POST['start_date']=$year."-".$month."-01";

	$v .= '&start_date=' . $_POST['start_date'];
}

if ($this->input->post('end_date')) {

	$a_date = $this->sma->fld($this->input->post('end_date'));




	$_POST['end_date']=date("Y-m-t", strtotime($a_date));

    $v .= '&end_date=' . $this->input->post('end_date');
}else{
    $_POST['end_date']=$this->sma->hrsd($max_min_period_year->period_to_date);

    $a_date = $this->sma->fld($_POST['end_date']);




	$_POST['end_date']=date("Y-m-t", strtotime($a_date));
	$v .= '&end_date=' . $_POST['end_date'];
}
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


<div class="spark-fixedassets-valuation-report-page">
    <div class="spark-fixedassets-valuation-report-header">
        <div class="spark-fixedassets-valuation-report-header-start">
            <div class="spark-fixedassets-valuation-report-header-icon">
                <i class="fa fa-bar-chart"></i>
            </div>
            <div class="spark-fixedassets-valuation-report-header-info">
                <h1 class="spark-fixedassets-valuation-report-title">
                    <?= lang('Valuation_report'); ?>
                    <?php
                    if ($this->input->post('start_date')) {
                        echo ' ' . (lang('From') ? lang('From') : 'From') . ' ' . $this->sma->hrsd($this->input->post('start_date')) . ' ' . (lang('to') ? lang('to') : 'to') . ' ' . $this->sma->hrsd($this->input->post('end_date'));
                    }
                    ?>
                </h1>
                <p class="spark-fixedassets-valuation-report-subtitle">
                    <?= lang('manage_fixedassets_valuation_report_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-fixedassets-valuation-report-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
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

                    <?php echo admin_form_open('fixedassets/valuation_report',' autocomplete="off" id="valuationForm"'); ?>
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

                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Year', 'Year'); ?>
                                <?php
								 foreach ($period_year as $account) {
									$account = (array) $account;


				        $ye[$account['period_year']] = $account['period_year'];


                                }
								 echo form_dropdown('period_year', $ye, (isset($_POST['period_year']) ? $_POST['period_year'] : date('Y')), 'class="form-control" id="period_year" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Year') . '"');
								 ?>
                            </div>
                        </div>


                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', ($this->input->post('start_date') ? $this->sma->hrsd($_POST['start_date']) : ''), 'class="form-control date" readonly id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', ($this->input->post('end_date') ? $this->sma->hrsd($_POST['end_date']) : ''), 'class="form-control date" readonly id="end_date"'); ?>
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Valuation_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>

					<?php
						if($this->input->post('group_id')){
					?>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo  $groupName;  ?></font></b></td>

					</tr>
					<?php
						}
					?>


					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Valuation_report') .' '. lang('From') .' '. $this->sma->hrsd($this->input->post('start_date')) . ' ' . lang('to') .' '. $this->sma->hrsd($this->input->post('end_date'))  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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




                    echo '
					<table class="table table-striped table-bordered table-hover">
					<thead class="thead-dark">
					<tr class="text-center">
					<th>'.lang("Fixed_asset").' </th>
					<th>'.lang("The_book_value").' - '.lang("Start_Period").'</th>
					<th>'.lang("Depreciation_of_fixed_asset").'</th>
					<th>'.lang("The_book_value_of_the_asset").'</th>

					</tr>
					</thead>
					<tbody class="searchable text-center">
					';


        $end_value=0;
		foreach ($fixedAssetList as $account){

			$account = (array) $account;




			$id      = $account['id'];







			{










                $accountEntries=$this->fixedassets_model->getValuationsByFAId($account['id'], (isset($_POST['start_date']) ? $_POST['start_date'] : ''), (isset($_POST['end_date']) ? $_POST['end_date'] : ''));


				if($accountEntries){


















				foreach ($accountEntries as $entry){
					$entry = (array) $entry;










					echo '
					<tr>
					<td class="text-left align-middle"><strong><a href="'.admin_url('fixedassets/fixed_asset_details/'.$account['id']).' ">'.$account['name'].' ( '.$account['code'].' ) </a></strong></td>';

					echo '<td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($entry['start_value']).'</strong><br />';
					echo '</td><td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($entry['accumulated_value']).'</strong><br />';
					echo '</td><td class="align-middle">';
					echo '<strong>'.$this->sma->formatMoney($entry['end_value']).'</strong><br />';
					echo '</td>
					</tr>
					';







					$end_value= $end_value + $entry['end_value'];

				}


			}

			}
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
				<td colspan="3"><strong>'.lang("The_book_value_of_the_asset").'</strong></td>';


				echo '

				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($end_value).'</strong></td>

				';

				echo '
				</tr>
				';





				echo '
				</tfoot></table>
				';


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
        PrPDF('pr_pdf','<?= lang('Valuation_report'); ?>');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('fixedassets/valuation_report/0/xls/?v=' . $v)?>";
            return false;
        });

        $("#start_date").datetimepicker({

format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma',  todayBtn: 1, autoclose: 1, minView: 2,
startDate: "<?= $max_min_period_year->period_from_date; ?>",
endDate: "<?= $max_min_period_year->period_to_date; ?>",

});
$("#end_date").datetimepicker({

format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma',  todayBtn: 1, autoclose: 1, minView: 2,
startDate: "<?= $max_min_period_year->period_from_date; ?>",
endDate: "<?= $max_min_period_year->period_to_date; ?>",

});


$('#period_year').change(function (e) {

//alert($(this).val());
$("#start_date").val('');
$("#end_date").val('');
$("#valuationForm").submit();

});


    });
</script>

