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
<script type="text/javascript">
    $(document).ready(function () {

        $('#grpId').change(function () {
            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    url: "<?= admin_url('branches/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#coId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#glbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;

$('#coId').val('').trigger('change.select2');
$('#glbrId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select_category_to_load') ?>",
                                //data: scdata


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
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

                        if (scdata != null) {

$('#glbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#glbrId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                //data: scdata


                        } else {
                            $("#glbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#glbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });
    });
</script>
<div class="modal-dialog modal-xl">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Reverse_entry'); ?></h4>
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
							<div class="col-4">
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

                            <div class="col-4">
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
							<div class="col-4">
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
				<div class="col-4">

					<div class="form-group">
					<?= lang('Entry_number', 'Entry_number') ?>
					<input class="form-control" placeholder="<?= lang('Entry_number') ?>" name="entryID" type="text" value="<?= $entryID ?>" required readonly/>
					</div>
				</div>
				<div class="col-4">

					<div class="form-group">
					<?= lang('Entry_date', 'Entry_date') ?>
					    <?php echo form_input('entryDate', (isset($_POST['entryDate']) ? $_POST['entryDate'] : ''), 'readonly class="form-control input-tip date" id="entryDate"'); ?>
                    </div>
				</div>
			</div>

				<div class="form-group">
					<input class="form-control" placeholder="<?= lang('description') ?>" name="entryDescription" id="entryDescription" type="text" required />
				</div>
				<div class="form-group" style="display:none;">
					<select class="form-control" name="entryType" data-toggle="tooltip" data-placement="left" title="Entry Type">
						<option>Initial</option>
						<option selected>Adjusting</option>
						<option>Closing</option>
					</select>
				</div>
				<div class="form-group" id="debitAccounts">
				<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryDebitAccount'] = json_decode($entry[0]->entryCreditAccount);
						$entry['entryDebitBalance'] = json_decode($entry[0]->entryCreditBalance);
						$entry['entryDebitDescription'] = json_decode($entry[0]->entryCreditDescription);
						$entry['costCenterDebitId'] = json_decode($entry[0]->costCenterCreditId);
						$entry['costCenterDebitAmount'] = json_decode($entry[0]->costCenterCreditAmount);




					//print_r($entry['costCenterDebitId']);
						$dt_index=0;
						$dt_row_index=1;
						$debitCountTemp=0;
							foreach ($entry['entryDebitAccount'] as $Debit){

								if($dt_index==0){
								?>

								<div class="row">

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
						<div  class="col-2">
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debitRow'.$dt_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterDebitId_0"  name="<?= 'costCenterDebitId_0[]' ?>"  id="costCenterDebitId_0" data-toggle="tooltip" data-placement="left" title="Debit Account">
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
			<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount_0" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterDebitAmount_0[]' ?>" id="costCenterDebitAmount_0" value="<?= $entry['costCenterDebitAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'debitRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						$dt_row_index++;
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
						<div  class="col-2">
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>


					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						$dt_row_index++;
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debitRow'.$dt_row_index ?>">
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
						$dt_row_index++;
						}


					}

								}

								$dt_index++;
								$debitCountTemp++;



							}


					?>


				</div>



				<div class="form-group" id="creditAccounts">



					<!--<div class="row">
						<div class="col-3">
							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">
								<?php
								foreach ($accountsList as $account) {
									$account = (array) $account;
									echo "
									<option value='{$account['id']}'>".lang('Credit').": {$account['accountName']}</option>
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
					</div>-->



					<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryCreditAccount'] = json_decode($entry[0]->entryDebitAccount);
						$entry['entryCreditBalance'] = json_decode($entry[0]->entryDebitBalance);
						$entry['entryCreditDescription'] = json_decode($entry[0]->entryDebitDescription);
						$entry['costCenterCreditId'] = json_decode($entry[0]->costCenterDebitId);
						$entry['costCenterCreditAmount'] = json_decode($entry[0]->costCenterDebitAmount);


					//print_r($entry['costCenterCreditId']);
						$dt_index=0;
						$cr_row_index=1;
						$creditCountTemp=0;
							foreach ($entry['entryCreditAccount'] as $Credit){

								if($dt_index==0){
								?>

								<div class="row">

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
						<div  class="col-2">
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

					<?php
					if($entry['costCenterCreditId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'creditRow'.$cr_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterCreditId_0"  name="<?= 'costCenterCreditId_0[]' ?>"  id="costCenterCreditId_0" data-toggle="tooltip" data-placement="left" title="Credit Account">
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
			<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount_0" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterCreditAmount_0[]' ?>" id="costCenterCreditAmount_0" value="<?= $entry['costCenterCreditAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'creditRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						$cr_row_index++;
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
						<div  class="col-2">
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>


					<?php
					if($entry['costCenterCreditId'][$dt_index]){
						$cr_row_index++;
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'creditRow'.$cr_row_index ?>">
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
						$cr_row_index++;
						}


					}

								}

								$dt_index++;
								$creditCountTemp++;



							}


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
					<?php echo form_submit('reverse_entry', lang('Reverse_entry'), 'id="reverse_entry" class="btn btn-primary"'); ?>
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
		$("#reverse_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#reverse_entry").prop('disabled',true);
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
		$("#reverse_entry").prop('disabled',false);
		$("#tatalDiff").val('0');
	}else{
		$("#reverse_entry").prop('disabled',true);
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
		var isSubmitting = false;

		function serializeReverseEntry() {
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

		$(document).off('click.sparkReverseEntry', '#reverse_entry').on('click.sparkReverseEntry', '#reverse_entry', function(e) {
			if (isSubmitting) {
				e.preventDefault();
				return false;
			}
			serializeReverseEntry();
			isSubmitting = true;
			var $btn = $(this);
			setTimeout(function() {
				isSubmitting = false;
				$btn.removeClass('disabled');
			}, 3000);
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
			var buttonOption1  = $('<div class="col-2"><button id="addCostCenterDebit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


			buttonOption.click(function() {
				debitCountTemp--;

				$(this).parent().remove();
				getDebit();
			});
			buttonOption1.click(function() {



			var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+debitCountTemp+'[]"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+debitCountTemp+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(buttonOption);
			debitCount += 1;



			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(inputOption1);
			$('#debitRow'+debitCount).append(buttonOption);
			$('#debitRow'+debitCount).append(buttonOption1);
			debitCount += 1;
			debitCountTemp += 1;

			search_select()

		});


		$('button#addCostCenterDebit').click(function() {



			var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId_0"  name="costCenterDebitId_0[]" id="costCenterDebitId_0" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_0[]" id="costCenterDebitAmount_0" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(buttonOption);
			debitCount += 1;

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
			var buttonOption1  = $('<div class="col-2"><button id="addCostCenterCredit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


			buttonOption.click(function() {
				creditCountTemp--;
				$(this).parent().remove();
				getCredit();
			});

			buttonOption1.click(function() {

			var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+creditCountTemp+'[]" id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+creditCountTemp+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#creditAccounts').append(divOption);
			$('#creditRow'+creditCount).append(selectOption);
			$('#creditRow'+creditCount).append(inputOption);
			$('#creditRow'+creditCount).append(buttonOption);
			creditCount += 1;



			});




			$('#creditAccounts').append(divOption);
			$('#creditRow'+creditCount).append(selectOption);
			$('#creditRow'+creditCount).append(inputOption);
			$('#creditRow'+creditCount).append(inputOption1);
			$('#creditRow'+creditCount).append(buttonOption);
			$('#creditRow'+creditCount).append(buttonOption1);
			creditCount += 1;
			creditCountTemp += 1;

			search_select()

		});

		$('button#addCostCenterCredit').click(function() {

			var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId_0"  name="costCenterCreditId_0[]" id="costCenterCreditId_0" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_0[]" id="costCenterCreditAmount_0" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#creditAccounts').append(divOption);
			$('#creditRow'+creditCount).append(selectOption);
			$('#creditRow'+creditCount).append(inputOption);
			$('#creditRow'+creditCount).append(buttonOption);
			creditCount += 1;


			search_select()
			});


			$("#entryDate").datetimepicker({
				format: site.dateFormats.js_sdate,
				fontAwesome: true,
				language: 'sma',
				todayBtn: 1,
				autoclose: 1,
				minView: 2
			});

			getDebit();
			getCredit();


	});
</script>
