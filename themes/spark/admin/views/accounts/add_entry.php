<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
	@media (min-width: 1200px) {
    .modal-xl {
        width:1140px
    }
}
</style>
<?php
$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
if ($check_period && isset($check_period->period_status) && $check_period->period_status == 1) {
	$_POST['start_date'] = $this->sma->hrsd(date('Y-m-d'));
	$_POST['end_date']   = $this->sma->hrsd($check_period->period_to_date);
} else {
	$_POST['start_date'] = $this->sma->hrsd(date('Y-m-d'));
	$_POST['end_date']   = $this->sma->hrsd(isset($max_min_period_year->period_to_date) ? $max_min_period_year->period_to_date : date('Y-12-31'));
}
?>
<script type="text/javascript">
    $(document).ready(function () {

        $('#grpId').change(function () {
            var v = $(this).val();
            var uopt = $("#coId");

            if (v) {
                $.ajax({
                    type: "get",
                    url: "<?= admin_url('branches/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        $('#coId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>').val('');
                        $('#glbrId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('');

                        if (scdata != null) {
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);
                            });
                        }
                        $('#coId').trigger('change.select2');
                        $('#glbrId').trigger('change.select2');
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#coId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>').val('').trigger('change.select2');
                $('#glbrId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('').trigger('change.select2');
            }
        });

        $('#coId').change(function () {
            var v = $(this).val();
            var uopt = $("#glbrId");

            if (v) {
                $.ajax({
                    type: "get",
                    url: "<?= admin_url('branches/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        $('#glbrId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('');

                        if (scdata != null) {
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);
                            });
                        }
                        $('#glbrId').trigger('change.select2');
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#glbrId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('').trigger('change.select2');
            }
        });
    });
</script>
<div class="modal-dialog modal-xl">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_entry'); ?></h4>
        </div>



		<div class="modal-body">
			<?=form_open(current_url(), 'enctype="multipart/form-data"  autocomplete="off"');?>
			<fieldset>
			<div class="row">
			<?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
							<div class="col-3">
                            <div class="form-group">
                                <?= lang('Main_group', 'Main_group') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                ?>
                            </div>
							</div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="col-3">
								<div class="form-group">
                                <?= lang('Main_company', 'Main_company') ?>
                                <?php
                                $co[''] = lang('select') . ' ' . lang('Main_company');
                                if(($this->Settings->enable_grp==1)){}else{
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
							<div class="col-3">
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="glbrId"   style="width:100%"')
                                            ?>
                            </div>
							</div>
                           <?php
                        }} ?>
						<?php
						$col=2;
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
							$col=3;
						}
						?>
				<div class="col-<?= $col ?>">

					<div class="form-group">
					<?= lang('Entry_number', 'Entry_number') ?>
					<input class="form-control" placeholder="<?= lang('Entry_number') ?>" name="entryID" type="text" value="<?= $entryID ?>" required readonly/>
					</div>
				</div>

				<div class="col-<?= $col ?>">
					<div class="form-group">
						<?= lang('Entry_type', 'Entry_type') ?>
						<select class="form-control select" style="width:100%" name="entryType" id="entryType" title="Entry Type">
							<option value="Adjusting" selected><?= lang('Daily_entry') ?></option>
							<option value="Recurring_entry"><?= lang('Recurring_entry') ?></option>
						</select>
                    </div>
				</div>

				<div class="col-<?= $col ?> recurring">
					<div class="form-group">
						<?= lang('Time_period', 'Time_period') ?>
						<select class="form-control select" style="width:100%" name="timePeriod" title="Time Period">
							<option value="daily"><?= lang('Daily') ?></option>
							<option value="weekly"><?= lang('Weekly') ?></option>
							<option value="monthly" selected><?= lang('Monthly') ?></option>
						</select>
                    </div>
				</div>

				<div class="col-<?= $col ?> recurring">
                    <div class="form-group">
                        <?= lang('Start_date', 'Start_date'); ?>
                        <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" readonly id="start_date"'); ?>
                    </div>
                </div>
                <div class="col-<?= $col ?> recurring">
                    <div class="form-group">
                        <?= lang('End_date', 'End_date'); ?>
                        <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" readonly id="end_date"'); ?>
                    </div>
                </div>

				<div class="col-<?= $col ?> adjusting">

					<div class="form-group">
					<?= lang('Entry_date', 'Entry_date') ?>
					    <?php echo form_input('entryDate', (isset($_POST['entryDate']) ? $_POST['entryDate'] : ''), 'readonly class="form-control input-tip date" id="entryDate"'); ?>
                    </div>
				</div>

			</div>

				<div class="form-group">
					<input class="form-control" placeholder="<?= lang('description') ?>" name="entryDescription" id="entryDescription" type="text" required />
				</div>
				<div class="form-group" id="debitAccounts">
					<div class="row">
						<div class="col-3">
							<select class="form-control select entryDebitAccount" style="width:100%" name="entryDebitAccount[]" id="entryDebitAccount" placeholder="<?= lang('Select_Debit_Account') ?>" required >
								<?php
								echo "
								<option></option>
								";
								foreach ($accountsList as $account) {
									$account = (array) $account;
									echo "
									<option value='{$account['id']}'>".lang('Debit').": {$account['accountID']} - {$account['accountName']}</option>
									";
								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Entry_Debit_Balance') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" type="text" />
						</div>
						<div class="col-2">
							<button id="addDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_Debit') ?></button>
						</div>
						<div  class="col-2">
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
					<div class="row" id="debit0">

					</div>
				</div>



				<div class="form-group" id="creditAccounts">
					<div class="row">
						<div class="col-3">
							<select class="form-control select entryCreditAccount" style="width:100%" name="entryCreditAccount[]" id="entryCreditAccount" placeholder="<?= lang('Select_Credit_Account') ?>" required >
								<?php
								echo "
								<option></option>
								";
								foreach ($accountsList as $account) {
									$account = (array) $account;
									echo "
									<option value='{$account['id']}'>".lang('Credit').": {$account['accountID']} - {$account['accountName']}</option>
									";
								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Entry_Credit_Balance') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" />
						</div>
						<div class="col-2">
							<button id="addCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_Credit') ?></button>
						</div>
						<div  class="col-2">
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
					<div class="row" id="credit0">

					</div>
				</div>
				<div class="form-group">
                <?= lang('document', 'document') ?>
				<input id="entryFile" type="file" data-browse-label="<?= lang('browse'); ?>" name="entryFile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
                </div>

				<div class="row">
				<div class="col-4">

				<div class="form-group">

                <?= lang('Debit', 'Debit') ?>
					<input class="form-control"  name="tatalDebit" id="tatalDebit" type="text" readonly id="tatalDebit"/>
						</div>
				</div>


				<div class="col-4">

				<div class="form-group">

                <?= lang('Credit', 'Credit') ?>
					<input class="form-control"  name="tatalCredit" id="tatalCredit" type="text" readonly id="tatalDebit"/>
				</div>
				</div>

				<div class="col-4">

				<div class="form-group">

                <?= lang('The_difference', 'The_difference') ?>
					<input class="form-control"  name="tatalDiff" id="tatalDiff" type="text" readonly id="tatalDiff"/>
				</div>
				</div>

				</div>

				<input type="hidden" name="jsonArr_entryDebitAccount" id="jsonArr_entryDebitAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitBalance" id="jsonArr_entryDebitBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitDescription" id="jsonArr_entryDebitDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId_0" id="jsonArr_costCenterDebitId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount_0" id="jsonArr_costCenterDebitAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId" id="jsonArr_costCenterDebitId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount" id="jsonArr_costCenterDebitAmount"  value=''/>

				<input type="hidden" name="jsonArr_entryCreditAccount" id="jsonArr_entryCreditAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditBalance" id="jsonArr_entryCreditBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditDescription" id="jsonArr_entryCreditDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId_0" id="jsonArr_costCenterCreditId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount_0" id="jsonArr_costCenterCreditAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId" id="jsonArr_costCenterCreditId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount" id="jsonArr_costCenterCreditAmount"  value=''/>


				<div class="modal-footer">
					<?php echo form_submit('add_entry', lang('Add_entry'), 'id="add_entry" class="btn btn-primary"'); ?>
                </div>

			</fieldset>
		</form>
	</div>
</div>
</div>


<script type="text/javascript">

var dt=0,ct=0;

function search_select(context){
	var $scope = context ? $(context) : $('.modal-body');
	$scope.find('select').each(function() {
		var $el = $(this);
		if (!$el.hasClass('select2-offscreen') && !$el.data('select2')) {
			$el.addClass('select');
			var placeholderText = $el.attr('placeholder') || $el.attr('title') || '';
			$el.select2({
				width: '100%',
				placeholder: placeholderText || undefined,
				allowClear: placeholderText ? true : false,
				minimumResultsForSearch: 7,
				matcher: function(term, text, option) {
					if (!term) return true;
					var termUpper = term.toUpperCase();
					var textUpper = (text || '').toUpperCase();
					var valUpper = (option ? String(option.val() || '') : '').toUpperCase();
					return textUpper.indexOf(termUpper) >= 0 || valUpper.indexOf(termUpper) >= 0;
				}
			});
		}
	});
}


function getDebit(){
	var p = ($(".entryDebitBalance"));
		//alert(p.length);
		dt=0;
		try{
jQuery.each(p, function(i, val) {
	if(val.value==''){
		val.value=0;
	}
	dt=parseFloat(dt)+parseFloat(val.value);
	if(val.value==0){
		val.value='';
	}
  //alert(val.value+" "+i);
});
		}catch(err){}

	$("#tatalDebit").val(formatDecimal(dt));

	if($("#tatalDebit").val() == $("#tatalCredit").val()){
		$("#add_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#add_entry").prop('disabled',true);
		var dt=$("#tatalDebit").val();
		if(dt==''){
			dt=0;
		}
		var cr=$("#tatalCredit").val();
		if(cr==''){
			cr=0;
		}
		$("#tatalDiff").val(formatDecimal(Math.abs(parseFloat(dt) - parseFloat(cr))));

	}

}


function getCredit(){
	var p = ($(".entryCreditBalance"));
		//alert(p.length);
		ct=0;
		try{
jQuery.each(p, function(i, val) {
	if(val.value==''){
		val.value=0;
	}

	ct=parseFloat(ct)+parseFloat(val.value);
	if(val.value==0){
		val.value='';
	}
  //alert(val.value+" "+i);
});
		}catch(err){}

	$("#tatalCredit").val(formatDecimal(ct));

	if($("#tatalDebit").val() == $("#tatalCredit").val()){
		$("#add_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#add_entry").prop('disabled',true);
		var dt=$("#tatalDebit").val();
		if(dt==''){
			dt=0;
		}
		var cr=$("#tatalCredit").val();
		if(cr==''){
			cr=0;
		}
		$("#tatalDiff").val(formatDecimal(Math.abs(parseFloat(dt) - parseFloat(cr))));

	}
}


	$(document).ready(function() {

		search_select();

		var debitCount  = 0;
		var debitCountTemp  = 0;
		var costdebitCount  = 0;
		var isSubmitting = false;

		function serializeAddEntry() {
			$('#jsonArr_entryDebitAccount').val(JSON.stringify($(".entryDebitAccount").serializeArray()));
			$('#jsonArr_entryDebitBalance').val(JSON.stringify($(".entryDebitBalance").serializeArray()));
			$('#jsonArr_entryDebitDescription').val(JSON.stringify($(".entryDebitDescription").serializeArray()));

			$('#jsonArr_costCenterDebitId_0').val(JSON.stringify($(".costCenterDebitId_0").serializeArray()));
			$('#jsonArr_costCenterDebitAmount_0').val(JSON.stringify($(".costCenterDebitAmount_0").serializeArray()));

			$('#jsonArr_costCenterDebitId').val(JSON.stringify($(".costCenterDebitId").serializeArray()));
			$('#jsonArr_costCenterDebitAmount').val(JSON.stringify($(".costCenterDebitAmount").serializeArray()));

			$('#jsonArr_entryCreditAccount').val(JSON.stringify($(".entryCreditAccount").serializeArray()));
			$('#jsonArr_entryCreditBalance').val(JSON.stringify($(".entryCreditBalance").serializeArray()));
			$('#jsonArr_entryCreditDescription').val(JSON.stringify($(".entryCreditDescription").serializeArray()));

			$('#jsonArr_costCenterCreditId_0').val(JSON.stringify($(".costCenterCreditId_0").serializeArray()));
			$('#jsonArr_costCenterCreditAmount_0').val(JSON.stringify($(".costCenterCreditAmount_0").serializeArray()));

			$('#jsonArr_costCenterCreditId').val(JSON.stringify($(".costCenterCreditId").serializeArray()));
			$('#jsonArr_costCenterCreditAmount').val(JSON.stringify($(".costCenterCreditAmount").serializeArray()));
		}

		$(document).off('click.sparkAddEntry', '#add_entry').on('click.sparkAddEntry', '#add_entry', function(e) {
			if (isSubmitting) {
				e.preventDefault();
				return false;
			}
			serializeAddEntry();
			isSubmitting = true;
			var $btn = $(this);
			setTimeout(function() {
				isSubmitting = false;
				$btn.removeClass('disabled');
			}, 3000);
		});



		    $('button#addDebit').click(function(){
				costdebitCount=debitCount;
			var divOption    = $('<div class="row" id="debitRow'+debitCount+'"></div>');
			var selectOption = $('<div class="col-3"><select class="form-control select entryDebitAccount" style="width:100%" name="entryDebitAccount[]" id="entryDebitAccount" placeholder="<?= lang('Select_Debit_Account') ?>" required ><?php
			echo "<option></option>";
			foreach ($accountsList as $account) {
				$account = (array) $account;
				echo "<option value=\"{$account['id']}\">".lang('Debit').": {$account['accountID']} - {$account['accountName']}</option>";
			}
			?></select></div>');
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryDebitBalance" placeholder="<?= lang('Entry_Debit_Balance') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (debitCount+1) + '"><button id="addCostCenterDebit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div>');

			buttonOption.click(function() {
				debitCountTemp--;
				$(this).closest('.row').remove();
				getDebit();
			});
			buttonOption1.click(function() {
				var row = $(this).closest('div');
				var debitCC=(row.attr('data-item-id'));
				var divCC = $('<div class="row" id="debit'+debitCount+'"></div>');
				var selectCC = $('<div class="col-5"><select class="form-control select costCenterDebitId" style="width:100%" name="costCenterDebitId_'+debitCC+'[]" id="costCenterDebitId" title="Debit Account"><?php
				foreach ($cost_centers as $account) {
					$account = (array) $account;
					echo "<option value=\"{$account['id']}\"> {$account['costName']}</option>";
				}
				?></select></div>');
				var inputCC = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+debitCC+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
				var buttonCC = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');

				buttonCC.click(function() {
					$(this).closest('.row').remove();
				});

				$('#debitAccounts').append(divCC);
				$('#debit'+debitCC).append(selectCC).append(inputCC).append(buttonCC);
				search_select($('#debit'+debitCC));
			});

			divOption.append(selectOption).append(inputOption).append(inputOption1).append(buttonOption).append(buttonOption1);
			$('#debitAccounts').append(divOption);
			$('#debitAccounts').append('<div class="row" id="debit'+(debitCount+1)+'"></div>');
			debitCount += 1;
			debitCountTemp += 1;

			search_select(divOption);
		});


		$('button#addCostCenterDebit').click(function() {
			var divOption    = $('<div class="row" id="debit0"></div>');
			var selectOption = $('<div class="col-5"><select class="form-control select costCenterDebitId_0" style="width:100%" name="costCenterDebitId_0[]" id="costCenterDebitId_0" title="Debit Account"><?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "<option value=\"{$account['id']}\"> {$account['costName']}</option>";
			}
			?></select></div>');
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_0[]" id="costCenterDebitAmount_0" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');

			buttonOption.click(function() {
				$(this).closest('.row').remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debit0').append(selectOption).append(inputOption).append(buttonOption);
			search_select($('#debit0'));
		});


		var creditCount = 0;
		var creditCountTemp = 0;
		var costcreditCount = 0;

		$('button#addCredit').click(function(){
			costcreditCount=creditCount;

			var divOption    = $('<div class="row" id="creditRow'+creditCount+'"></div>');
			var selectOption = $('<div class="col-3"><select class="form-control select entryCreditAccount" style="width:100%" name="entryCreditAccount[]" id="entryCreditAccount" placeholder="<?= lang('Select_Credit_Account') ?>" required ><?php
			echo "<option></option>";
			foreach ($accountsList as $account) {
				$account = (array) $account;
				echo "<option value=\"{$account['id']}\">".lang('Credit').": {$account['accountID']} - {$account['accountName']}</option>";
			}
			?></select></div>');
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryCreditBalance" placeholder="<?= lang('Entry_Credit_Balance') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (creditCount+1) + '"><button id="addCostCenterCredit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div>');

			buttonOption.click(function() {
				creditCountTemp--;
				$(this).closest('.row').remove();
				getCredit();
			});

			buttonOption1.click(function() {
				var row = $(this).closest('div');
				var creditCC=(row.attr('data-item-id'));
				var divCC = $('<div class="row" id="credit'+creditCount+'"></div>');
				var selectCC = $('<div class="col-5"><select class="form-control select costCenterCreditId" style="width:100%" name="costCenterCreditId_'+creditCC+'[]" id="costCenterCreditId" title="Debit Account"><?php
				foreach ($cost_centers as $account) {
					$account = (array) $account;
					echo "<option value=\"{$account['id']}\"> {$account['costName']}</option>";
				}
				?></select></div>');
				var inputCC = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+creditCC+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
				var buttonCC = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');

				buttonCC.click(function() {
					$(this).closest('.row').remove();
				});

				$('#creditAccounts').append(divCC);
				$('#credit'+creditCC).append(selectCC).append(inputCC).append(buttonCC);
				search_select($('#credit'+creditCC));
			});

			divOption.append(selectOption).append(inputOption).append(inputOption1).append(buttonOption).append(buttonOption1);
			$('#creditAccounts').append(divOption);
			$('#creditAccounts').append('<div class="row" id="credit'+(creditCount+1)+'"></div>');
			creditCount += 1;
			creditCountTemp += 1;

			search_select(divOption);
		});

		$('button#addCostCenterCredit').click(function() {
			var divOption    = $('<div class="row" id="credit0"></div>');
			var selectOption = $('<div class="col-5"><select class="form-control select costCenterCreditId_0" style="width:100%" name="costCenterCreditId_0[]" id="costCenterCreditId_0" title="Debit Account"><?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "<option value=\"{$account['id']}\"> {$account['costName']}</option>";
			}
			?></select></div>');
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_0[]" id="costCenterCreditAmount_0" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div>');

			buttonOption.click(function() {
				$(this).closest('.row').remove();
			});

			$('#creditAccounts').append(divOption);
			$('#credit0').append(selectOption).append(inputOption).append(buttonOption);
			search_select($('#credit0'));
		});


			$('#entryType').change(function(e) {
            if ($(this).val() == 'Recurring_entry') {
                $('.recurring').show();
                $('.adjusting').hide();
            } else {
                $('.recurring').hide();
				$('.adjusting').show();
            }
        	});
        	if ($('#entryType').val() == 'Recurring_entry') {
                $('.recurring').show();
				$('.adjusting').hide();
            } else {
                $('.recurring').hide();
				$('.adjusting').show();
        	}

		$("#entryDate").datetimepicker({
			format: site.dateFormats.js_sdate,
			fontAwesome: true,
			language: 'sma',
			todayBtn: 1,
			autoclose: 1,
			minView: 2
		});

		$("#start_date").datetimepicker({
			format: site.dateFormats.js_sdate,
			fontAwesome: true,
			language: 'sma',
			todayBtn: 1,
			autoclose: 1,
			minView: 2,
			startDate: "<?= isset($max_min_period_year->period_from_date) ? $max_min_period_year->period_from_date : date('Y-01-01'); ?>",
			endDate: "<?= isset($max_min_period_year->period_to_date) ? $max_min_period_year->period_to_date : date('Y-12-31'); ?>"
		});

		$("#end_date").datetimepicker({
			format: site.dateFormats.js_sdate,
			fontAwesome: true,
			language: 'sma',
			todayBtn: 1,
			autoclose: 1,
			minView: 2,
			startDate: "<?= isset($max_min_period_year->period_from_date) ? $max_min_period_year->period_from_date : date('Y-01-01'); ?>",
			endDate: "<?= isset($max_min_period_year->period_to_date) ? $max_min_period_year->period_to_date : date('Y-12-31'); ?>"
		});






	});
</script>

