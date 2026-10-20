<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--<script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>-->

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
?>

<script>
	var sma_lang = <?php echo json_encode($this->lang->language); ?>;
	var user_language = <?php echo json_encode($this->Settings->user_language); ?>;
	var accounts_gl = <?php echo json_encode($accounts_gl); ?>;
</script>
<!--
<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
-->
<script>
	$(document).ready(function() {


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
			var y = x;
			if (x == "purchase_returned")
				y = "<?= lang('purchase_returned') ?>";
			if (x == "purchase_partial")
				y = "<?= lang('purchase_partial') ?>";
			if (x == "purchase_receive")
				y = "<?= lang('purchase_receive') ?>";
			if (x == "Revaluation")
				y = "<?= lang('Revaluation') ?>";
			if (x == "Adjustment")
				y = "<?= lang('Adjustment') ?>";
			if (x == "Destruction")
				y = "<?= lang('Destruction') ?>";
			if (x == "Transfer")
				y = "<?= lang('Transfer') ?>";
			if (x == "Automated_Accounting_Entry")
				y = "<?= lang('Automated_Accounting_Entry') ?>";
			if (x == "Adjusting")
				y = "<?= lang('Adjusting') ?>";

			return "<div>" + y + "</div>"; // onclick=\"this.select();\"
		}

		oTable = $('#UnitTable').dataTable({
			"aaSorting": [
				[0, "desc"]
			],
			"aLengthMenu": [
				[10, 25, 50, 100,500, -1],
				[10, 25, 50, 100,500, "<?= lang('all') ?>"]
			],
			"iDisplayLength": <?= $Settings->rows_per_page ?>,
			'bProcessing': true,
			'bServerSide': true,
			<?php
				$active_year = isset($selected_year) && !empty($selected_year) ? $selected_year : (isset($_POST['period_year']) ? $_POST['period_year'] : (isset($period_year[0]->period_year) ? $period_year[0]->period_year : date('Y')));
			?>
			'sAjaxSource': '<?= admin_url('accounts/getEntriesDTable') ?>?year=<?= $active_year . $v; ?>',
			'fnServerData': function(sSource, aoData, fnCallback) {
				aoData.push({
					"name": "<?= $this->security->get_csrf_token_name() ?>",
					"value": "<?= $this->security->get_csrf_hash() ?>"
				});
				$.ajax({
					'dataType': 'json',
					'type': 'POST',
					'url': sSource,
					'data': aoData,
					'success': fnCallback
				});
			},
			'fnRowCallback': function (nRow, aData, iDisplayIndex) {
               nRow.id = aData[0];
                nRow.className = "entries_link";
                return nRow;
            },
			"aoColumns": [{
					"mRender": function(value, type, row, meta) {
						return checkbox(row[0]);
						return (row[0]);
					}
				}
				, {
					"mRender": function(value, type, row, meta) {
						var entry_no=row[1];
						if(entry_no==null)
							entry_no=row[0];
						return '<div style="min-width:100px;">'+entry_no+'</div>';

					}
				}, {
					"mRender": function(value, type, row, meta) {
						return moment(row[2]).format('DD/MM/YYYY');
					}
				}, {
					"mRender": function(value, type, row, meta) {
						//if(user_language=='arabic'){

						if ($.trim(row[3]) != "Adjusting" && $.trim(row[3]) != "Recurring_entry")
							return sma_lang[row[3]];
						else if (!isNaN(sma_lang[row[4]]))
							return sma_lang[row[4]];
						else
							return `<div style="white-space: normal; word-break: break-word;">${row[4]}</div>`;

					//}else{
					//	if ($.trim(row[2]) != "Adjusting")
					//		return row[2];
					//	else
					//		return row[3];
					//}
					}
				},null, {
					"mRender": function(value, type, row, meta) {
						$debit = (JSON.parse(row[5]))
						$balance = (JSON.parse(row[6]))
						$html = '';
						for ($i = 0; $i < $debit.length; $i++) {
							if (row[3] == "Fiscal_year_closing" && accounts_gl[$debit[$i]] == null) {
								$html += sma_lang['Net_Income'];
							} else {
								$html += accounts_gl[$debit[$i]] + `<br /><strong>` + formatMoney($balance[$i], 0) + `</strong><br />`;
							}
						}
						return $html;
					}
				},
				{
					"mRender": function(value, type, row, meta) {
						$credit = (JSON.parse(row[7]))
						$balance = (JSON.parse(row[8]))
						$html = '';
						for ($i = 0; $i < $credit.length; $i++) {
							if (row[3] == "Fiscal_year_closing" && accounts_gl[$credit[$i]] == null) {
								$html += sma_lang['Net_Income'];
							} else {
								$html += accounts_gl[$credit[$i]] + `<br /><strong>` + formatMoney($balance[$i], 0) + `</strong><br />`;
							}
						}
						return $html;
					}
				},null,null,
				{
					"mRender": function(value, type, row, meta) {
						return row[9] == null ? '' : `
							<div class="text-center"><a href="<?= admin_url('welcome/download/'); ?>/` + row[9] + `" class="tip" title="` + sma_lang['download'] + `"><i class="fa fa-file"></i></a></div>
						`;
					}
				},

				{
					"mRender": function(value, type, row, meta) {
						if (row[10] == '1')
							return '<div class="text-center"><span class="row_status label label-success"><?= lang('approved'); ?></span></div>';
						else
							return '<div class="text-center"><span class="row_status label label-warning"><?= lang('unapproved'); ?></span></div>';

					}
				}, {
					"bSortable": false,
					"mRender": function(value, type, row, meta) {
						if ($.trim(row[3]) != "Recurring_entry"){
						return `
							<div class="text-center">
								<a href="<?= admin_url('accounts/duplicate_entry/') ?>` + row[0] + `" data-toggle="modal" data-target="#myModal" class="tip" title="<?= lang('Duplicate_entry') ?>">
									<i style="padding:2px;" class="fa fa-clone"></i>
								</a>
								<a href="<?= admin_url('accounts/reverse_entry/') ?>` + row[0] + `" data-toggle="modal" data-target="#myModal" class="tip" title="<?= lang('Reverse_entry') ?>">
									<i style="padding:2px;" class="fa fa-exchange"></i>
								</a>
								<a href="<?= admin_url('accounts/edit_entry/') ?>` + row[0] + `" data-toggle="modal" data-target="#myModal" class="tip" title="<?= lang('Edit_entry') ?>">
									<i style="padding:2px;" class="fa fa-edit"></i>
								</a>
							</div>
						`;
						}else{
						return `
							<div class="text-center">
								<a href="<?= admin_url('accounts/edit_entry/') ?>` + row[0] + `" data-toggle="modal" data-target="#myModal" class="tip" title="<?= lang('Edit_entry') ?>">
									<i style="padding:2px;" class="fa fa-edit"></i>
								</a>
							</div>
						`;
						}
					}

				}
			],
		});
		oTable.fnSetColumnVis( 4, false );
		oTable.fnSetColumnVis( 7, false );
		oTable.fnSetColumnVis( 8, false );
	});
</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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

<?= admin_form_open('accounts/entries', 'id="action-form"') ?>

<div class="spark-entries-page">
    <div class="spark-entries-header">
        <div class="spark-entries-header-start">
            <div class="spark-entries-header-icon">
                <i class="fa fa-book"></i>
            </div>
            <div class="spark-entries-header-info">
                <h1 class="spark-entries-title">
                    <?= lang('Entries'); ?>
                </h1>
                <p class="spark-entries-subtitle">
                    <?= lang('manage_entries_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-entries-header-actions">
            <?php if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) { ?>
                <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>
            <?php } ?>

            <a href="<?= admin_url('accounts/add_entry'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('Add_entry'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="#" id="status" data-action="update_status">
                            <i class="fa fa-thumbs-o-up"></i> <?= lang('approve'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="status_unapprove" data-action="update_status_unapprove">
                            <i class="fa fa-thumbs-o-down"></i> <?= lang('unapprove'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

<div class="box">

	<div class="box-content">

		<div class="clearfix"></div>
		<div class="row">
			<div class="col-lg-12">


			<p class="introtext"><?=lang('list_results');?></p>
			<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
											?>
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



                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>

                </div>
				<?php
                            }
							?>
                <div class="clearfix"></div>

				<div class="table-responsive">
					<table id="UnitTable" class="table table-striped jambo_table bulk_action reports-table">
						<thead>
							<tr>
								<th style="min-width:30px; width: 30px; text-align: center;">
									<input class="checkbox checkft" type="checkbox" name="check" />
								</th>
								<th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Entry_number'); ?></th>
								<th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Entry_date'); ?></th>
								<th><?= lang('Entry_type'); ?></th>
								<th><?= lang('Entry_Description'); ?></th>
								<th class="col-xs-2"><?= lang('Debit'); ?></th>
								<th class="col-xs-2"><?= lang('Credit'); ?></th>
								<th><?= lang('actions1'); ?></th>
								<th><?= lang('actions2'); ?></th>
								<th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
								<th><?= lang('approved'); ?></th>
								<th><?= lang('actions'); ?></th>

							</tr>
						</thead>
						<tfoot>
							<tr>
								<th></th>
								<th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Entry_number'); ?></th>
								<th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Entry_date'); ?></th>
								<th><?= lang('Entry_type'); ?></th>
								<th><?= lang('Entry_Description'); ?></th>
								<th class="col-xs-2"><?= lang('Debit'); ?></th>
								<th class="col-xs-2"><?= lang('Credit'); ?></th>
								<th><?= lang('actions1'); ?></th>
								<th><?= lang('actions2'); ?></th>
								<th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
								<th><?= lang('approved'); ?></th>
								<th style="min-width:100px; width: 100px; text-align: center;"><?= lang('actions'); ?></th>

							</tr>
						</tfoot>
						<tbody>
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>
<div style="display: none;">
	<input type="hidden" name="form_action" value="" id="form_action" />
	<?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>

<script>
	$(document).ready(function() {

		$('#submit_report').click(function(e) {
			e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('accounts/entries') ?>');
			$('#action-form-submit').trigger('click');

		});
		$('#status').click(function(e) {

			e.preventDefault();
			$('#action-form').attr('action', '<?= admin_url('accounts/account_actions') ?>');

			$('#form_action').val($(this).attr('data-action'));
			$('#action-form-submit').trigger('click');

		});

		$('#status_unapprove').click(function(e) {

e.preventDefault();
$('#action-form').attr('action', '<?= admin_url('accounts/account_actions') ?>');

$('#form_action').val($(this).attr('data-action'));
$('#action-form-submit').trigger('click');

});
	});
</script>