<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<?php

$v = "";
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
if (isset($_POST['start_date'])) {
	$v .= '&start_date=' . $this->input->post('start_date');
}
if (isset($_POST['end_date'])) {
	$v .= '&end_date=' . $this->input->post('end_date');
}
if (isset($_POST['bond_no'])) {
	$v .= '&bond_no=' . $this->input->post('bond_no');
}
if (isset($_POST['reference_no'])) {
	$v .= '&reference_no=' . $this->input->post('reference_no');
}

?>
<script>
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

		function get_lang(x) {
			var y=x;
			if(x=="cash")
				y="<?= lang('cash') ?>";
			if(x=="bank_transfer")
				y="<?= lang('bank_transfer') ?>";
			if(x=="CC")
				y="<?= lang('CC') ?>";
			if(x=="Cheque")
				y="<?= lang('cheque') ?>";


            return "<div>"+y+"</div>"; // onclick=\"this.select();\"
        }

        oTable = $('#dmpData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100,500, -1], [10, 25, 50, 100,500, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('accounts/getRefundingBonds?1' . $v); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null, {"mRender": fld}, null, null, {"mRender": currencyFormat}, {"bSortable": false, "mRender": get_lang}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "refunding_bonds_link";
                return nRow;
            },
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('Bond_No');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
			{column_number: 3, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},


        ], "footer");

        if (localStorage.getItem('remove_qals')) {
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
            localStorage.removeItem('remove_qals');
        }

        <?php if ($this->session->userdata('remove_qals')) {
    ?>
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
        <?php $this->sma->unset_data('remove_qals');
}
        ?>
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId') && !$this->input->post('warehouse') && !$this->input->post('start_date')) { ?>
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

<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('products/adjustment_actions', 'id="action-form"');
} ?>

<div class="spark-refunding-bonds-page">
    <div class="spark-refunding-bonds-header">
        <div class="spark-refunding-bonds-header-start">
            <div class="spark-refunding-bonds-header-icon">
                <i class="fa fa-exchange"></i>
            </div>
            <div class="spark-refunding-bonds-header-info">
                <h1 class="spark-refunding-bonds-title">
                    <?= lang('Refunding_bonds'); ?>
                </h1>
                <p class="spark-refunding-bonds-subtitle">
                    <?= lang('manage_refunding_bonds_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-refunding-bonds-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <a href="<?= admin_url('accounts/add_refunding_bonds'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('Add_refunding_bonds'); ?></span>
            </a>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

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

                        <div class="col-sm-2">
							<div class="form-group">
								<?= lang('Start_date', 'Start_date'); ?>
								<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'readonly class="form-control date" id="start_date"'); ?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<?= lang('End_date', 'End_date'); ?>
								<?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'readonly class="form-control date" id="end_date"'); ?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<?= lang('Bond_No', 'Bond_No'); ?>
								<?php echo form_input('bond_no', (isset($_POST['bond_no']) ? $_POST['bond_no'] : ''), 'class="form-control " id="bond_no"'); ?>
							</div>
						</div>
                        <div class="col-sm-2">
							<div class="form-group">
								<?= lang('reference_no', 'reference_no'); ?>
								<?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control " id="reference_no"'); ?>
							</div>
						</div>



                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>

                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="dmpData" class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-1"><?= lang('Bond_No'); ?></th>
                            <th class="col-xs-1"><?= lang('reference_no'); ?></th>
                            <th class="col-xs-1"><?= lang('date'); ?></th>
                            <th style="min-width:75px; text-align:center;"><?= lang('The_account_transferred_from'); ?></th>
                            <th style="min-width:75px; text-align:center;"><?= lang('The_account_transferred_to'); ?></th>
                            <th class="col-xs-1"><?= lang('Amount'); ?></th>
                            <th class="col-xs-1"><?= lang('Payment_method'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="min-width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                            <th style="width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php if ($Owner || $GP['bulk_actions']) {
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
	$(document).ready(function() {

		$('#submit_report').click(function(e) {
			e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('accounts/refunding_bonds') ?>');
			$('#action-form-submit').trigger('click');

		});
	});
</script>
