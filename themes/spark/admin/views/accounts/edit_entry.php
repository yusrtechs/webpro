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
$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($entry[0]->start_date);
		$_POST['end_date']=$this->sma->hrsd($entry[0]->end_date);
	}else{

		$_POST['start_date']=$this->sma->hrsd($entry[0]->start_date);
		$_POST['end_date']=$this->sma->hrsd($entry[0]->end_date);
	}
?>
<div class="modal-dialog modal-xl">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_entry'); ?></h4>
        </div>



		<div class="modal-body">
			<?=form_open(current_url(), 'enctype="multipart/form-data"  autocomplete="off"');?>
			<fieldset>
			<div class="row">
				<div class="col-2">

					<div class="form-group">
					<?= lang('Entry_number', 'Entry_number') ?>
					<input class="form-control" placeholder="<?= lang('Entry_number') ?>" name="entryID" type="text" value="<?= $entry[0]->entryID ?>" required readonly/>
					</div>
				</div>
				<?php
				if($entry[0]->entryType =='Adjusting'){
				?>
				<div class="col-2">
					<div class="form-group">
						<?= lang('Entry_type', 'Entry_type') ?>
						<select class="form-control select" style="width:100%" name="entryType" id="entryType" title="Entry Type">
							<option value="Adjusting" selected><?= lang('Daily_entry') ?></option>
						</select>
                    </div>
				</div>
				<?php
				}else if($entry[0]->entryType =='Recurring_entry'){
				?>
				<div class="col-2">
					<div class="form-group">
						<?= lang('Entry_type', 'Entry_type') ?>
						<select class="form-control select" style="width:100%" name="entryType" id="entryType" title="Entry Type">
							<option value="Recurring_entry" selected><?= lang('Recurring_entry') ?></option>
						</select>
                    </div>
				</div>
				<?php
				}else{
				?>
				<div class="form-group" style="display:none;">
					<select class="form-control select" style="width:100%" name="entryType" title="Entry Type">
						<option>Initial</option>
						<option selected>Adjusting</option>
						<option>Closing</option>
					</select>
				</div>

				<?php
				}
				?>

				<div class="col-2 recurring">
					<div class="form-group">
						<?= lang('Time_period', 'Time_period') ?>
						<select class="form-control select" style="width:100%" name="timePeriod" title="Time Period">
							<?php
							if($entry[0]->timePeriod =='daily'){
								echo '<option value="daily" selected>'.lang('Daily').'</option>';
							}else if($entry[0]->timePeriod =='weekly'){
								echo '<option value="weekly" selected>'.lang('Weekly').'</option>';
							}else  if($entry[0]->timePeriod =='monthly'){
								echo '<option value="monthly" selected>'.lang('Monthly').'</option>';
							}
							?>
						</select>
                    </div>
				</div>

				<div class="col-2 recurring">
                    <div class="form-group">
                        <?= lang('Start_date', 'Start_date'); ?>
                        <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control " readonly id="start_date"'); ?>
                    </div>
                </div>
                <div class="col-2 recurring">
                    <div class="form-group">
                        <?= lang('End_date', 'End_date'); ?>
                        <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control " readonly id="end_date"'); ?>
                    </div>
                </div>

				<div class="col-2 adjusting">

					<div class="form-group">
					<?= lang('Entry_date', 'Entry_date') ?>
					    <?php echo form_input('entryDate', (isset($_POST['entryDate']) ? $_POST['entryDate'] : $this->sma->hrsd($entry[0]->entryDate)), 'readonly class="form-control input-tip date" id="entryDate"'); ?>
                    </div>
				</div>

			</div>

				<div class="form-group" <?php if ($entry[0]->entryType!='Adjusting') { echo 'style="display:none;"';} ?> >
					<input class="form-control" placeholder="<?= lang('description') ?>" name="entryDescription" type="text" value="<?= lang($this->sma->clear_tags($entry[0]->entryDescription)) ?>" required />
				</div>

				<div class="form-group" id="debitAccounts">
				<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryDebitAccount'] = json_decode($entry[0]->entryDebitAccount);
						$entry['entryDebitBalance'] = json_decode($entry[0]->entryDebitBalance);
						$entry['entryDebitDescription'] = json_decode($entry[0]->entryDebitDescription);
						$entry['costCenterDebitId'] = json_decode($entry[0]->costCenterDebitId);
						$entry['costCenterDebitAmount'] = json_decode($entry[0]->costCenterDebitAmount);


					//print_r($entry['costCenterDebitId']);
						$dt_index=0;
						$dt_row_index=1;
						$debitCountTemp=0;
							foreach ($entry['entryDebitAccount'] as $Debit){

								if($dt_index==0){
								?>

								<div class="row" id="<?= 'debitRow'.$dt_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]" id="entryDebitAccount" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($accountsList as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected>".lang('Debit').": {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'>".lang('Debit').": {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Entry_Debit_Balance') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" value="<?= $entry['entryDebitBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" value="<?= $entry['entryDebitDescription'][$dt_index] ?>" type="text" />
						</div>
						<div class="col-2">
							<button id="addDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_Debit') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $dt_row_index ?>" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
																<div class="row" id="<?= 'debit'.$dt_row_index ?>">
			</div>


					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debit'.$dt_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterDebitId"  name="<?= 'costCenterDebitId_0[]' ?>"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costDebit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterDebitAmount_0[]' ?>" id="costCenterDebitAmount" value="<?= $entry['costCenterDebitAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'debitRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						//$dt_row_index++;
						}


					}



								}else{
									?>
									<div class="row" id="<?= 'debitRow'.$dt_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]" id="entryDebitAccount" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($accountsList as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected>".lang('Debit').": {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'>".lang('Debit').": {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Entry_Debit_Balance') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" value="<?= $entry['entryDebitBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" value="<?= $entry['entryDebitDescription'][$dt_index] ?>" type="text" />
						</div>
						<div class="col-2">
							<button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="dtRemove('<?= 'debitRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $dt_row_index ?>" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

																		<div class="row" id="<?= 'debit'.$dt_row_index ?>">
			</div>

					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						//$dt_row_index++;
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debit'.$dt_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterDebitId"  name="<?= 'costCenterDebitId_'.$debitCountTemp.'[]' ?>"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costDebit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterDebitAmount_'.$debitCountTemp.'[]' ?>" id="costCenterDebitAmount" value="<?= $entry['costCenterDebitAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'debitRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
}


					}

								}

								$dt_index++;
								$debitCountTemp++;
						        $dt_row_index++;




							}


					?>


				</div>



				<div class="form-group" id="creditAccounts">







					<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryCreditAccount'] = json_decode($entry[0]->entryCreditAccount);
						$entry['entryCreditBalance'] = json_decode($entry[0]->entryCreditBalance);
						$entry['entryCreditDescription'] = json_decode($entry[0]->entryCreditDescription);
						$entry['costCenterCreditId'] = json_decode($entry[0]->costCenterCreditId);
						$entry['costCenterCreditAmount'] = json_decode($entry[0]->costCenterCreditAmount);


					//print_r($entry['costCenterCreditId']);
						$dt_index=0;
						$cr_row_index=1;
						$creditCountTemp=0;
							foreach ($entry['entryCreditAccount'] as $Credit){

								if($dt_index==0){
								?>

								<div class="row" id="<?= 'creditRow'.$cr_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">
								<?php
								foreach ($accountsList as $account) {
									$account = (array) $account;
									if($Credit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected>".lang('Credit').": {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'>".lang('Credit').": {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Entry_Credit_Balance') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" value="<?= $entry['entryCreditBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" value="<?= $entry['entryCreditDescription'][$dt_index] ?>" type="text" />
						</div>
						<div class="col-2">
							<button id="addCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_Credit') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $cr_row_index ?>" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
														<div class="row" id="<?= 'credit'.$cr_row_index ?>">
			</div>


					<?php
					if($entry['costCenterCreditId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'credit'.$cr_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterCreditId"  name="<?= 'costCenterCreditId_0[]' ?>"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costCredit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterCreditAmount_0[]' ?>" id="costCenterCreditAmount" value="<?= $entry['costCenterCreditAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'creditRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						//
						}


					}



								}else{
									?>
									<div class="row" id="<?= 'creditRow'.$cr_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">
								<?php
								foreach ($accountsList as $account) {
									$account = (array) $account;
									if($Credit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected>".lang('Credit').": {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'>".lang('Credit').": {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Entry_Credit_Balance') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" value="<?= $entry['entryCreditBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>
						<div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" value="<?= $entry['entryCreditDescription'][$dt_index] ?>" type="text" />
						</div>
						<div class="col-2">
							<button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="crRemove('<?= 'creditRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $cr_row_index ?>" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

																<div class="row" id="<?= 'credit'.$cr_row_index ?>">
			</div>

					<?php
					//$cr_row_index=0;

					if($entry['costCenterCreditId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'credit'.$cr_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterCreditId"  name="<?= 'costCenterCreditId_'.$creditCountTemp.'[]' ?>"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costCredit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterCreditAmount_'.$creditCountTemp.'[]' ?>" id="costCenterCreditAmount" value="<?= $entry['costCenterCreditAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'creditRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						//$cr_row_index++;
						}


					}

								}

								$dt_index++;
								$creditCountTemp++;
								$cr_row_index++;



							}
							//$cr_row_index--;

					?>





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
					<input class="form-control"  name="tatalDebit" id="tatalDebit" type="text" readonly/>
						</div>
				</div>


				<div class="col-4">

				<div class="form-group">

                <?= lang('Credit', 'Credit') ?>
					<input class="form-control"  name="tatalCredit" id="tatalCredit" type="text" readonly/>
				</div>
				</div>

				<div class="col-4">

				<div class="form-group">

                <?= lang('The_difference', 'The_difference') ?>
					<input class="form-control"  name="tatalDiff" id="tatalDiff" type="text" readonly/>
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
					<?php echo form_submit('edit_entry', lang('Edit_entry'), 'id="edit_entry" class="btn btn-primary"'); ?>
                </div>

			</fieldset>
		</form>
	</div>
</div>
</div>

<script type="text/javascript">

var dt=0,ct=0;

		var debitCount  = <?= $dt_row_index ?>;
		var debitCountTemp  = <?= $debitCountTemp ?>;
		var costdebitCount  = 0;


		var creditCount = <?= $cr_row_index ?>;
		var creditCountTemp = <?= $creditCountTemp ?>;
		var costcreditCount = 0;



function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});

}

function costRemove(id){


	$('#'+id).remove();
}

function dtRemove(id){
debitCountTemp--;

				$('#'+id).remove();
				getDebit();
}

function crRemove(id){
creditCountTemp--;

				$('#'+id).remove();
				getCredit();
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
		$("#edit_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#edit_entry").prop('disabled',true);
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
		$("#edit_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#edit_entry").prop('disabled',true);

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




		$(document).on('click', '#edit_entry', function(e) {

		var formData_entryDebitAccount = JSON.stringify($(".entryDebitAccount").serializeArray());
		$('#jsonArr_entryDebitAccount').val(formData_entryDebitAccount);
		var formData_entryDebitBalance = JSON.stringify($(".entryDebitBalance").serializeArray());
		$('#jsonArr_entryDebitBalance').val(formData_entryDebitBalance);
		var formData_entryDebitDescription = JSON.stringify($(".entryDebitDescription").serializeArray());
		$('#jsonArr_entryDebitDescription').val(formData_entryDebitDescription);

		var formData_costCenterDebitId_0 = JSON.stringify($(".costCenterDebitId_0").serializeArray());
		$('#jsonArr_costCenterDebitId_0').val(formData_costCenterDebitId_0);
		var formData_costCenterDebitAmount_0 = JSON.stringify($(".costCenterDebitAmount_0").serializeArray());
		$('#jsonArr_costCenterDebitAmount_0').val(formData_costCenterDebitAmount_0);

		var formData_costCenterDebitId = JSON.stringify($(".costCenterDebitId").serializeArray());
		$('#jsonArr_costCenterDebitId').val(formData_costCenterDebitId);
		var formData_costCenterDebitAmount = JSON.stringify($(".costCenterDebitAmount").serializeArray());
		$('#jsonArr_costCenterDebitAmount').val(formData_costCenterDebitAmount);


		var formData_entryCreditAccount = JSON.stringify($(".entryCreditAccount").serializeArray());
		$('#jsonArr_entryCreditAccount').val(formData_entryCreditAccount);
		var formData_entryCreditBalance = JSON.stringify($(".entryCreditBalance").serializeArray());
		$('#jsonArr_entryCreditBalance').val(formData_entryCreditBalance);
		var formData_entryCreditDescription = JSON.stringify($(".entryCreditDescription").serializeArray());
		$('#jsonArr_entryCreditDescription').val(formData_entryCreditDescription);

		var formData_costCenterCreditId_0 = JSON.stringify($(".costCenterCreditId_0").serializeArray());
		$('#jsonArr_costCenterCreditId_0').val(formData_costCenterCreditId_0);
		var formData_costCenterCreditAmount_0 = JSON.stringify($(".costCenterCreditAmount_0").serializeArray());
		$('#jsonArr_costCenterCreditAmount_0').val(formData_costCenterCreditAmount_0);

		var formData_costCenterCreditId = JSON.stringify($(".costCenterCreditId").serializeArray());
		$('#jsonArr_costCenterCreditId').val(formData_costCenterCreditId);
		var formData_costCenterCreditAmount = JSON.stringify($(".costCenterCreditAmount").serializeArray());
		$('#jsonArr_costCenterCreditAmount').val(formData_costCenterCreditAmount);




        });







		    $('button#addDebit').click(function(){
				costdebitCount=debitCount;
			var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-3"><select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]"  id="entryDebitAccount" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($accountsList as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\">".lang('Debit').": {$account['accountName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryDebitBalance" placeholder="<?= lang('Entry_Debit_Balance') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (debitCount) + '"><button id="addCostCenterDebit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


			buttonOption.click(function() {
				debitCountTemp--;

				$(this).parent().remove();
				getDebit();
			});
			buttonOption1.click(function() {


					var row = $(this).closest('div');
        //console.log(row)
		var creditDT=(row.attr('data-item-id'));
		//alert(creditCC)

			var divOption    = '<div class="row" id="debit'+creditDT+'">';

			//var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+creditDT+'[]"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+creditDT+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+creditDT).append(selectOption);
			$('#debitRow'+creditDT).append(inputOption);
			$('#debitRow'+creditDT).append(buttonOption);
			//debitCount += 1;
			return false;



			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(inputOption1);
			$('#debitRow'+debitCount).append(buttonOption);
			$('#debitRow'+debitCount).append(buttonOption1);
			$('#debitAccounts').append('<div class="row" id="debit'+(debitCount)+'"></div>');
			debitCount += 1;
			debitCountTemp += 1;

			search_select()

		});


		$('button#addCostCenterDebit').click(function() {



						var row = $(this).closest('div');
        //console.log(row)
		var debitCC=(row.attr('data-item-id'));
		//alert(creditCC)
			var divOption    = '<div class="row" id="debit'+debitCC+'">';
		//var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+(debitCC-1)+'[]" id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+(debitCC-1)+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCC).append(selectOption);
			$('#debitRow'+debitCC).append(inputOption);
			$('#debitRow'+debitCC).append(buttonOption);
			//debitCount += 1;

		search_select()

			});



		$('button#addCredit').click(function(){
				costcreditCount=creditCount;

			var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
			var selectOption = '<div class="col-3"><select class="form-control entryCreditAccount" style="padding:1px;"  name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">';
			<?php
			foreach ($accountsList as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\">".lang('Credit').": {$account['accountName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryCreditBalance" placeholder="<?= lang('Entry_Credit_Balance') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeCredit'+creditCount+'" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (creditCount) + '"><button id="addCostCenterCredit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


			buttonOption.click(function() {
				creditCountTemp--;
				$(this).parent().remove();
				getCredit();
			});

			buttonOption1.click(function() {
					var row = $(this).closest('div');
        //console.log(row)
		var creditCC=(row.attr('data-item-id'));
		//alert(creditCC)

			var divOption    = '<div class="row" id="credit'+creditCC+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+creditCC+'[]" id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+creditCC+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#creditAccounts').append(divOption);
			$('#credit'+creditCC).append(selectOption);
			$('#credit'+creditCC).append(inputOption);
			$('#credit'+creditCC).append(buttonOption);
			//creditCount += 1;
			return false;



			});




			$('#creditAccounts').append(divOption);
			$('#creditRow'+creditCount).append(selectOption);
			$('#creditRow'+creditCount).append(inputOption);
			$('#creditRow'+creditCount).append(inputOption1);
			$('#creditRow'+creditCount).append(buttonOption);
			$('#creditRow'+creditCount).append(buttonOption1);
			$('#creditAccounts').append('<div class="row" id="credit'+(creditCount)+'"></div>');
			creditCount += 1;
			creditCountTemp += 1;

			search_select()

		});

		$('button#addCostCenterCredit').click(function() {
					var row = $(this).closest('div');
        //console.log(row)
		var creditCC=(row.attr('data-item-id'));
		//alert(creditCC)
			var divOption    = '<div class="row" id="credit'+creditCC+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+(creditCC-1)+'[]" id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+(creditCC-1)+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#creditAccounts').append(divOption);
			$('#credit'+creditCC).append(selectOption);
			$('#credit'+creditCC).append(inputOption);
			$('#credit'+creditCC).append(buttonOption);
			//creditCount += 1;


			search_select()
			});


			getDebit();
			getCredit();

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




	});
</script>
