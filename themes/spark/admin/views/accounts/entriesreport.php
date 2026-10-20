<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = '';
$q = [];

if (isset($_POST['grpId'])) {
    $v .= '&grpId=' . $this->input->post('grpId');
	$q['grpId'] = $this->input->post('grpId');
}
if (isset($_POST['coId'])) {
    $v .= '&coId=' . $this->input->post('coId');
	$q['coId'] = $this->input->post('coId');
}
if (isset($_POST['brId'])) {
    $v .= '&brId=' . $this->input->post('brId');
	$q['brId'] = $this->input->post('brId');
}
if (isset($_POST['start_date'])) {
	$v .= '&start_date=' . $this->input->post('start_date');
	$q['start_date'] = $this->input->post('start_date');
}
if (isset($_POST['end_date'])) {
	$v .= '&end_date=' . $this->input->post('end_date');
	$q['end_date'] = $this->input->post('end_date');
}
if (isset($_POST['entryID'])) {
	$v .= '&entryID=' . $this->input->post('entryID');
	$q['entryID'] = $this->input->post('entryID');
}
if (isset($_POST['entryStatus'])) {
	$v .= '&entryStatus=' . $this->input->post('entryStatus');
	$q['entryStatus'] = $this->input->post('entryStatus');
}
if (isset($_POST['entryType'])) {
	$v .= '&entryType=' . $this->input->post('entryType');
	$q['entryType'] = $this->input->post('entryType');
}
//$this->sma->print_arrays($q);
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script>
	var sma_lang = <?php echo json_encode($this->lang->language); ?>;
	var user_language = <?php echo json_encode($this->Settings->user_language); ?>;
	var accounts_gl = <?php echo json_encode($accounts_gl); ?>;
</script>
<!--<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>-->
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
			"aaSorting": [[0, "desc"]],
			"aLengthMenu": [
				[10, 25, 50, 100,500, -1],
				[10, 25, 50, 100,500, "<?= lang('all') ?>"]
			],
			"iDisplayLength": <?= $Settings->rows_per_page ?>,
			'bProcessing': true,
			'bServerSide': true,
			'sAjaxSource': '<?= admin_url('accounts/getEntriesDTable') . '/?' . http_build_query($q); ?>',
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
			'fnRowCallback': function(nRow, aData, iDisplayIndex) {
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
					}

				}
			],
		});
		oTable.fnSetColumnVis( 0, false );
		oTable.fnSetColumnVis( 4, false );
		oTable.fnSetColumnVis( 7, false );
		oTable.fnSetColumnVis( 8, false );
		oTable.fnSetColumnVis( 9, false );
		oTable.fnSetColumnVis( 11, false );
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
        <?php if (!$this->input->post('start_date') && !$this->input->post('submit_report') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
            $('#form').hide();
        <?php } ?>
		$('.toggle_down').click(function() {
			$("#form").slideToggle(200);
			return false;
		});
		$('.toggle_up').click(function() {
			$("#form").slideUp(200);
			return false;
		});
		$('#filter').keyup(function() {
			var rex = new RegExp($(this).val(), 'i');
			$('.searchable tr').hide();
			$('.searchable tr').filter(function() {
				return rex.test($(this).text());
			}).show();

		})
	});
</script>

<div class="spark-entriesreport-page">
    <div class="spark-entriesreport-header">
        <div class="spark-entriesreport-header-start">
            <div class="spark-entriesreport-header-icon">
                <i class="fa fa-list-alt"></i>
            </div>
            <div class="spark-entriesreport-header-info">
                <h1 class="spark-entriesreport-title">
                    <?= lang('Entries') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'), 0, 10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'), 0, 10) . ')' : ''); ?>
                </h1>
                <p class="spark-entriesreport-subtitle">
                    <?= lang('manage_entriesreport_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-entriesreport-header-actions">
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
					<?php echo admin_form_open('accounts/entriesreport', ' autocomplete="off"'); ?>
					<div class="col-sm-12">

					<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-2">
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

                                    <div class="col-sm-2">
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
                                        <div class="col-sm-2">
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
						<div class="col-sm-2">
							<div class="form-group">
								<?= lang('Start_date', 'Start_date'); ?>
								<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<?= lang('End_date', 'End_date'); ?>
								<?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<?= lang('Entry_number', 'Entry_number'); ?>
								<?php echo form_input('entryID', (isset($_POST['entryID']) ? $_POST['entryID'] : ''), 'class="form-control " id="entryID"'); ?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label class="control-label" for="entryType"><?= lang('Entry_type'); ?></label>
								<?php
								$ss[''] = lang('all');
								$ss['Adjusting'] = lang('Manual_Accounting_Entry');
								$ss['Automated_Accounting_Entry'] = lang('Automated_Accounting_Entry');
								$ss['Recurring_entry'] = lang('Recurring_Accounting_Entry');

								echo form_dropdown('entryType', $ss, (isset($_POST['entryType']) ? $_POST['entryType'] : ''), 'class="form-control" id="entryType" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Entry_type') . '"');
								?>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
								<label class="control-label" for="entryStatus"><?= lang('approved'); ?></label>
								<?php
								$aa[''] = lang('all');
								$aa[1] = lang('approved');
								$aa[0] = lang('unapproved');
								echo form_dropdown('entryStatus', $aa, (isset($_POST['entryStatus']) ? $_POST['entryStatus'] : ''), 'class="form-control" id="entryStatus" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('entryStatus') . '"');
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<div class="row" id="pr_pdf">
			<div class="col-sm-12 col-md-12">
				<div class="table-responsive">
					<div class="row">
						<div class="col-sm-12 col-md-12">

							<div class="tbl" id="pr_pdf1">
								<table border="0" cellpadding="0" cellspacing="0" class="table">



									<thead style="border: 1px solid #00ace6;">
										<tr>
											<td style="color:#ECF0F1;border:0;" colspan="8" align="center" class="pltable"><b>
													<font size="6" color="#212529"><?= lang('Entries_Report'); ?></font>
												</b></td>
										</tr>
										<tr>
											<td style="color:#ECF0F1;border:0;" colspan="8" align="center" class="pltable"><b>
													<font size="5" color="#212529"><?php echo ($wh->name ?  $wh->name  : $Settings->site_name);  ?></font>
												</b></td>

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
											<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="8" align="center" class="pltable"><b>
													<font size="3" color="#212529">



														<?php
														echo ($this->input->post('start_date') ?  lang('period') . ' ' . lang('Entries_Report') . ' ' . lang('From') . ' ' . $this->input->post('start_date') . ' ' . lang('to') . ' ' . $this->input->post('end_date')  : ''); // . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
														?></font>
												</b></td>

										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
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
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#pdf').click(function(event) {
			event.preventDefault();
			PrPDF('pr_pdf', '<?= lang('Entries_Report'); ?>', '');
			return false;
		});

		$('#xls').click(function(event) {
			event.preventDefault();
			window.location.href = "<?= admin_url('accounts/entriesreport/0/xls/?v=' . $v) ?>";
			return false;
		});
	});
</script>