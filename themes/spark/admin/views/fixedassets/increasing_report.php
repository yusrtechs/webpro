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
        <?php if ($this->input->post('start_date') || $this->input->post('end_date') || $this->input->post('fixed_asset')) { ?>
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


<div class="spark-fixedassets-increasing-report-page">
    <div class="spark-fixedassets-increasing-report-header">
        <div class="spark-fixedassets-increasing-report-header-start">
            <div class="spark-fixedassets-increasing-report-header-icon">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="spark-fixedassets-increasing-report-header-info">
                <h1 class="spark-fixedassets-increasing-report-title">
                    <?= lang('Increasing_report'); ?>
                    <?php
                    if ($this->input->post('start_date')) {
                        echo ' ' . (lang('From') ? lang('From') : 'From') . ' ' . $this->sma->hrsd($this->input->post('start_date')) . ' ' . (lang('to') ? lang('to') : 'to') . ' ' . $this->sma->hrsd($this->input->post('end_date'));
                    }
                    ?>
                </h1>
                <p class="spark-fixedassets-increasing-report-subtitle">
                    <?= lang('manage_fixedassets_increasing_report_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-fixedassets-increasing-report-header-actions">
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

                    <?php echo admin_form_open('fixedassets/increasing_report',' autocomplete="off"'); ?>
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


                       <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="increasing_type"><?= lang('Type_of_fixed_asset_increase'); ?></label>
                                <?php
                                $levels_arr[''] = lang('all');
                                $levels_arr['1'] = lang('Maintenance');
                                $levels_arr['2'] = lang('Repair');
                                $levels_arr['3'] = lang('Upgraded');
                                $levels_arr['4'] = lang('Purchase_spare_parts');


                                echo form_dropdown('increasing_type', $levels_arr, (isset($_POST['increasing_type']) ? $_POST['increasing_type'] : ''), 'class="form-control" id="increasing_type" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Type_of_fixed_asset_increase') . '"');
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', ($this->input->post('start_date') ? $this->sma->hrsd($_POST['start_date']) : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Increasing_report'); ?></font></b></td>
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
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Increasing_report') .' '. lang('From') .' '. $this->sma->hrsd($this->input->post('start_date')) . ' ' . lang('to') .' '. $this->sma->hrsd($this->input->post('end_date'))  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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










                $accountEntries=$this->fixedassets_model->getIncreasingByFAId($account['id'], (isset($_POST['increasing_type']) ? $_POST['increasing_type'] : ''), (isset($_POST['start_date']) ? $_POST['start_date'] : ''), (isset($_POST['end_date']) ? $_POST['end_date'] : ''));


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
					<th>'.lang("Start_date").'</th>
                    <th>'.lang("End_date").'</th>
                    <th>'.lang("supplier").'</th>
					<th>'.lang("Type_of_fixed_asset_increase").'</th>
					<th>'.lang("The_effect_of_increasing_on").'</th>
					<th>'.lang("cost").'</th>
					<th>'.lang("Useful_life").'</th>
					<th>'.lang("description").' </th>

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
					<td class="text-left align-middle">'.$this->sma->hrsd(($entry['start_date'])).'</td>';

					echo '<td class="text-left align-middle">'.$this->sma->hrsd(($entry['end_date'])).'</td><td class="align-middle">';
					echo $this->site->getCompanyByID($entry['supplier_id'])->company.'';
					echo '</td>
					<td class="align-middle">';
					echo $entry['increasing_type']==1 ? lang('Maintenance') : ($entry['increasing_type']==2 ? lang('Repair') : ($entry['increasing_type']==3 ? lang('Upgraded') : lang('Purchase_spare_parts') )).'';
					echo '</td>;
					<td class="align-middle">';
					echo $entry['effect_type']==1 ? lang('cost') : lang('Useful_life');
					echo '</td>;
					<td class="align-middle">';
					echo $this->sma->formatDecimal($entry['cost'],4);
					echo '</td>;
					<td class="align-middle">';
					echo $entry['useful_life'];
					echo '</td><td class="align-middle">';
					echo '<strong>'.$this->sma->decode_html($entry['description']).'</strong>';
					echo '</td>
					</tr>
					';









				}
				$total=0;
				$diffDebit=0;
				$diffCredit=0;



				echo '
				</tbody>
				</table>
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
        PrPDF('pr_pdf','<?= lang('Increasing_report'); ?>','L');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('fixedassets/increasing_report/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>

