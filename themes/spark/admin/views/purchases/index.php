<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v='';
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
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('supplier')) {
    $v .= "&supplier=" . $this->input->post('supplier');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
		$warehouse_id= $this->input->post('warehouse');

}else if($this->session->userdata('warehouse_id') &&  !$Owner && !$Admin){
$v .= "&warehouse=" . $this->session->userdata('warehouse_id');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('status')) {
    $v .= "&status=" . $this->input->post('status');
}
if ($this->input->post('payment_status')) {
    $v .= "&payment_status=" . $this->input->post('payment_status');
}

?>


<script>
    $(document).ready(function () {

		function p_status(x) {
	//alert(lang.approved)
	//alert(lang)
	/*$.each(lang, function () {
            alert(this);
	    });*/

	if (x == null) {
        return '';
    } else if (x == 'pending') {

		x='purchase_pending';
        return '<div class="text-center"><span class="row_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'unapproved' || x == 'غير معتمد') {
        return '<div class="text-center"><span class="row_status label label-warning">' + x + '</span></div>';
    } else if (x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang[x] + '</span></div>';
    } else if (x == 'approved' || x == 'معتمد') {
        return '<div class="text-center"><span class="row_status label label-success">' + x + '</span></div>';
    } else if (x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="row_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'due' || x == 'returned') {
        return '<div class="text-center"><span class="row_status label label-danger">' + lang[x] + '</span></div>';
    } else {
        return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
    }
}

		function decimalFormatInPurchases(x){
			//alert(x)
			    var d = site.settings.decimals;
				var result= parseFloat(x).toFixed(d);
				//alert(result);
				if(result==(-0.01) || result==(-0.00))
					result=parseFloat(0).toFixed(d);
				return '<div class="text-center">' + result + '</div>';


		}
		function decimalFormatInPurchases2(x){
			//alert(x)
			    var d = site.settings.decimals;
				var result= parseFloat(x).toFixed(d);
				//alert(result);
				if(result==(-0.01) || result==(-0.00))
					result=parseFloat(0).toFixed(d);
				//return '<div class="text-center">' + result + '</div>';
				return result;


		}



        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('pogrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('pocoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('pogrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('pocoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('pobrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>

        oTable = $('#POData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?=admin_url('purchases/getPurchases/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null, null, {"mRender": p_status}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormatInPurchases}, {"mRender": pay_status}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "purchase_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, discount_earned = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][5]);
                    paid += parseFloat(aaData[aiDisplay[i]][6]);
					discount_earned += parseFloat(aaData[aiDisplay[i]][7]);
                    balance += parseFloat(decimalFormatInPurchases2(aaData[aiDisplay[i]][8]));
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = decimalFormat(total);
                nCells[6].innerHTML = decimalFormat(paid);
                nCells[7].innerHTML = decimalFormat(discount_earned);
                nCells[8].innerHTML = decimalFormat(balance);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('ref_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('purchase_status');?>]", filter_type: "text", data: []},
            {column_number: 9, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
        ], "footer");

        <?php if ($this->session->userdata('remove_pols')) {
    ?>
        if (localStorage.getItem('poitems')) {
            localStorage.removeItem('poitems');
        }
        if (localStorage.getItem('podiscount')) {
            localStorage.removeItem('podiscount');
        }
        if (localStorage.getItem('potax2')) {
            localStorage.removeItem('potax2');
        }
        if (localStorage.getItem('poshipping')) {
            localStorage.removeItem('poshipping');
        }
        if (localStorage.getItem('poref')) {
            localStorage.removeItem('poref');
        }
        if (localStorage.getItem('powarehouse')) {
            localStorage.removeItem('powarehouse');
        }
        if (localStorage.getItem('pogrpId')) {
            localStorage.removeItem('pogrpId');
        }
        if (localStorage.getItem('pocoId')) {
            localStorage.removeItem('pocoId');
        }
        if (localStorage.getItem('pobrId')) {
            localStorage.removeItem('pobrId');
        }
        if (localStorage.getItem('ponote')) {
            localStorage.removeItem('ponote');
        }
        if (localStorage.getItem('posupplier')) {
            localStorage.removeItem('posupplier');
        }
        if (localStorage.getItem('pocurrency')) {
            localStorage.removeItem('pocurrency');
        }
        if (localStorage.getItem('poextras')) {
            localStorage.removeItem('poextras');
        }
        if (localStorage.getItem('podate')) {
            localStorage.removeItem('podate');
        }
        if (localStorage.getItem('postatus')) {
            localStorage.removeItem('postatus');
        }
        if (localStorage.getItem('popayment_term')) {
            localStorage.removeItem('popayment_term');
        }
        <?php $this->sma->unset_data('remove_pols');
}
        ?>
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('start_date') && !$this->input->post('end_date') && !$this->input->post('reference_no') && !$this->input->post('supplier') && !$this->input->post('warehouse') && !$this->input->post('product') && !$this->input->post('user') && !$this->input->post('serial') && !$this->input->post('status') && !$this->input->post('payment_status') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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

<?php echo admin_form_open("purchases/index", 'id="action-form"'); ?>

<div class="spark-purchases-page">
    <div class="spark-purchases-header">
        <div class="spark-purchases-header-start">
            <div class="spark-purchases-header-icon">
                <i class="fa fa-cart-arrow-down"></i>
            </div>
            <div class="spark-purchases-header-info">
                <h1 class="spark-purchases-title">
                    <?= lang('purchases') . ($warehouse_id ? ' (' . $warehouse->name . ')' : ''); ?>
                    <?php if ($this->input->post('start_date')) {
                        echo " " . lang('From') . " " . $this->input->post('start_date') . " " . lang('to') . " " . $this->input->post('end_date');
                    } ?>
                </h1>
                <p class="spark-purchases-subtitle">
                    <?= lang('manage_purchases_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-purchases-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <a href="<?= admin_url('purchases/add'); ?>" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_purchase'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="<?= admin_url('purchases/purchase_by_csv'); ?>">
                            <i class="fa fa-file-text-o"></i> <?= lang('add_purchase_by_csv'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= admin_url('purchases/purchase_by_excel'); ?>">
                            <i class="fa fa-file-excel-o"></i> <?= lang('add_purchase_by_excel'); ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" id="excel" data-action="export_excel">
                            <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="combine" data-action="combine">
                            <i class="fa fa-file-pdf-o"></i> <?= lang('combine_to_pdf'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>
				<div id="form">

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
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
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
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>


                         <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('reference_no', 'poref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>




                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('supplier', 'supplier'); ?>
                                <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="posupplier"'); ?>
								</div>
                        </div>
                       <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $wh[""] = lang('select').' '.lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>
                           <div class="col-sm-3">
                            <div class="form-group">

                                <?= lang('purchase_status', 'postatus'); ?>
                                <?php
                                $post = ['0' => lang('all'),'received' => lang('received'), 'partial' => lang('partial'), 'pending' => lang('purchase_pending'), 'ordered' => lang('ordered'), 'returned' => lang('returned')];
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('status') . '" style="width:100%;" ');

                                ?>

                            </div>
                            </div>
                              <?php if ($Owner || $Admin || $GP['sales-payments']) {
                                    ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('payment_status', 'slpayment_status'); ?>
                                <?php $pst = ['0' => lang('all'),'pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid')];
                                    echo form_dropdown('payment_status', $pst, (isset($_POST['payment_status']) ? $_POST['payment_status'] : ""), 'class="form-control input-tip"  id="slpayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'pending');
                                }
                        ?>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>


                </div>
                <div class="clearfix"></div>


                <div class="table-responsive">
                    <table id="POData" class="table table-striped jambo_table bulk_action" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>
                            <th><?= lang('ref_no'); ?></th>
                            <th><?= lang('supplier'); ?></th>
                            <th><?= lang('purchase_status'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Earned'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th><?= lang('payment_status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?=lang('loading_data_from_server');?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Earned'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px; text-align: center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php if ($Owner || $Admin || $GP['bulk_actions']) {
                    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php
                }
?>

<script>
$(document).ready(function () {

$('#submit_report').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('purchases/index') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#excel').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('purchases/purchase_actions') ?>');

        });
$('#combine').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('purchases/purchase_actions') ?>');

        });
	});

</script>
