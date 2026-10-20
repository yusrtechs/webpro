<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

$v = '';
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('wh')) {
    $v .= '&wh=' . $this->input->post('wh');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}

?>
<script>$(document).ready(function () {
        CURI = '<?= admin_url('carsreports/profit_loss/?v=1' . $v); ?>';
    });</script>
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
        <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= lang('profit_loss'); ?> <?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?>
        </h2>

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
        <div class="box-icon">
            <ul class="btn-tasks">
                    <li class="dropdown">
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
				<li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <p class="introtext"><?= lang('view_pl_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('carsreports/profit_loss'); ?>
                    <div >

                        <div class="col-sm-4 col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $whs[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $whs[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('wh', $whs, (isset($_POST['wh']) ? $_POST['wh'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
				<div class="row">
				<div class="col-sm-12 col-md-12">

    <div id="pr_pdf">

	<div><table border="0" cellpadding="0" cellspacing="0" class="table">

	<tbody width="100%">
	<thead style="border: 1px solid #00ace6;">
	<tr>
		<td style="background-color:#dee2e6;color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('profit_loss'); ?></font></b></td>
		</tr>
		<tr>
		<td style="background-color:#dee2e6;color:#ECF0F1;border:0;"  colspan="8"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

	</tr>
	<tr>
		<td style="background-color:#dee2e6;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  class="pltable" ><font size="2" color="#212529"> <?php
                    echo($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

	</tr>
	</thead>


	<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td  style="background-color:#dee2e6;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="8" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('profit_loss'); ?></font></b></td>

	</tr>
	<tr>
		<td style="background-color:#dee2e6;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="6"  class="pltable"  ><b><font  size="2" color="#212529"><?= lang('revenues'); ?></font></b></td>
		<td style="background-color:#dee2e6;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="2" color="#212529"><?= date('Y'); ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->total_amount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('return_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney(abs($total_sales->rtotal_amount)) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('Discounts') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->total_discount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('Discount_Allowance') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->discount_allowance) ?></font></td>

	</tr>
	<?php
	$net_sales=$this->sma->formatDecimal($total_sales->total_amount+$total_sales->rtotal_amount-$total_sales->total_discount-$total_sales->discount_allowance,12);
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('net_sales') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatMoney($net_sales) ?></font></b></td>

	</tr>
	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="2" color="#212529"><?= lang('Cost_of_Goods_Sold') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable" align="center"><b><font  size="2" color="#212529"></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Total_Cost_of_Goods_Sold') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1" ><b><font  size="2" ><?= $this->sma->formatMoney($total_cost_goods->item_cost_value) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Gross_Profit') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2"><?= $this->sma->formatMoney($net_sales - $total_cost_goods->item_cost_value) ?></font></b></td>

	</tr>
	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="2" color="#212529"><?= lang('expenses') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable" align="center"  ><b><font  size="2" color="#212529"></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('expenses') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1" ><font  size="2" ><?= $this->sma->formatMoney($total_expenses->total_amount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf"  colspan="6"  class="pltable1" ><b><font  size="2"> <?= lang('Total_Expenses') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($total_expenses->total_amount) ?></font></b></td>

	</tr>
	<?php
	$Operating_profit_loss=$this->sma->formatDecimal($net_sales - $total_cost_goods->item_cost_value - $total_expenses->total_amount,12);
	?>
	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Operating_profit_loss'); ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"   ><b><font  size="2"><?= $this->sma->formatMoney($Operating_profit_loss) ?></font></b></td>

	</tr>
	<?php
	$Other_Expenses=$this->sma->formatDecimal( $total_expenses->other_expenses,12);
	?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Other_Expenses') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($Other_Expenses) ?></font></b></td>

	</tr>
	<?php
	$Other_Revenues=$this->sma->formatDecimal($total_sales->other_revenues + $total_revenues->total_amount,12);
	?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Discount_Earned') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($total_purchases->discount_earned) ?></b></font></td>

	</tr>


	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Other_Revenues') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($Other_Revenues) ?></b></font></td>

	</tr>
	<?php
	$Net_profit_loss=$this->sma->formatDecimal($Operating_profit_loss - $Other_Expenses + $Other_Revenues + $total_purchases->discount_earned,12);
	?>
	<tr style="background-color:#dee2e6;color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="3"><?= lang('Net_profit_loss'); ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable"   ><b><font  size="3" <?php if($Net_profit_loss > 0){ echo 'color="green" <i class="fa fa-arrow-up"></i>'; }else if($Net_profit_loss<0) echo 'color="red" <i class="fa fa-arrow-down"></i>'; ?> </font> <?= $this->sma->formatMoney(abs($Net_profit_loss)) ?></b></td>

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
        PrPDF('pr_pdf','<?= lang('profit_loss'); ?>');
		    return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('carsreports/profit_loss_xls/0/xls/?v=1' . $v)?>";
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
