<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <?php
			    $logo=($branch->branch_logo) ? $branch->branch_logo : 'logos/'.$Settings->logo;
            ?>
            <div class="text-center" style="margin-bottom:20px;">
                <img src="<?= base_url() . 'assets/uploads/' . $logo; ?>" alt="<?= $Settings->site_name; ?>">
            </div>
            <div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-5">
                    <p class="bold">
                        <?= lang('Entry_number'); ?>: <?= $inv->entry_no ? $inv->entry_no : $inv->entryID; ?><br>
                        <?= lang('Entry_date'); ?>: <?= $this->sma->hrld($inv->entryDate); ?><br>
                        <?= lang('Entry_type'); ?>: <?= lang($inv->entryType); ?><br>
                        <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
						<?php
						$entryDescription=$inv->entryDescription;
						$entryDescription= str_replace("<p>","",($entryDescription)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
							$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);

						?>
                        <?= $inv->entryDescription=="Automated_Accounting_Entry" ? '' : lang('description')." : <font style='white-space: normal; word-break: break-word;'>". $entryDescription."</font>"; ?>
                    </p>
                    </div>
                    <div class="col-xs-7 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->entryID) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(admin_url('accounts/view_entries/' . $inv->entryID)), 2); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>


            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table">

                    <thead>

                    <tr>
                        <th><?= lang('Sr#'); ?></th>
                        <th><?= lang('Account'); ?></th>
                        <th><?= lang('Debit'); ?></th>
                        <th><?= lang('Credit'); ?></th>
                        <th><?= lang('Cost_centers'); ?></th>
                        <th><?= lang('description'); ?></th>

                    </tr>

                    </thead>

					<?php

							$sr=1;
							$dt=0;
							$cr=0;



							$entry = (array) $entry;
							$entry['entryDebitAccount'] = json_decode($inv->entryDebitAccount);
							$entry['entryDebitBalance'] = json_decode($inv->entryDebitBalance);
							$entry['entryDebitDescription'] = json_decode($inv->entryDebitDescription);
							$entry['entryCreditAccount'] = json_decode($inv->entryCreditAccount);
							$entry['entryCreditBalance'] = json_decode($inv->entryCreditBalance);
							$entry['entryCreditDescription'] = json_decode($inv->entryCreditDescription);
							$entry['costCenterDebitId'] = json_decode($inv->costCenterDebitId);
							$entry['costCenterDebitAmount'] = json_decode($inv->costCenterDebitAmount);
							$entry['costCenterCreditId'] = json_decode($inv->costCenterCreditId);
							$entry['costCenterCreditAmount'] = json_decode($inv->costCenterCreditAmount);



							$dt_index=0;
							foreach ($entry['entryDebitAccount'] as $debitAccount){
								echo '
							<tr>
							<td>'.$sr.'</td>
							<td>';
								$accObj = isset($accounts_map[$debitAccount]) ? $accounts_map[$debitAccount] : $this->accounts_model->getAccountByID($debitAccount);
								$accName = $accObj ? $accObj->accountName : '';
								echo ($inv->entryType =="Fiscal_year_closing" && !$accObj ? lang('Net_Income') : $accName).'</td><td>';
								echo $this->sma->formatMoney($entry['entryDebitBalance'][$dt_index]).'</td><td>';
								echo '0</td><td>';
								$dtc_index=0;
									foreach ((Array)$entry['costCenterDebitId'][$dt_index] as $costDebitId){
                                        if(isset($entry['costCenterDebitAmount'][$dt_index][$dtc_index]) && $entry['costCenterDebitAmount'][$dt_index][$dtc_index]>0) {
											$ccId = $entry['costCenterDebitId'][$dt_index][$dtc_index];
											$ccObj = isset($cost_centers_map[$ccId]) ? $cost_centers_map[$ccId] : $this->accounts_model->getCostCentersByID($ccId);
											$ccName = $ccObj ? $ccObj->costName : '';
											echo $ccName.'</br><strong>'.$this->sma->formatMoney($entry['costCenterDebitAmount'][$dt_index][$dtc_index]).'</strong></br>';
										}
								$dtc_index++;
									}
								echo '</td><td style="white-space: normal; word-break: break-word;">'.$entry['entryDebitDescription'][$dt_index].'</td>';

								echo '</tr><tr>';
								$sr++;
								$dt=$dt+$entry['entryDebitBalance'][$dt_index];
								$dt_index++;


							}
							$cr_index=0;
							foreach ($entry['entryCreditAccount'] as $creditAccount){
								echo '
							<tr>
							<td>'.$sr.'</td>
							<td>';
								$accObj = isset($accounts_map[$creditAccount]) ? $accounts_map[$creditAccount] : $this->accounts_model->getAccountByID($creditAccount);
								$accName = $accObj ? $accObj->accountName : '';
								echo ($inv->entryType =="Fiscal_year_closing" && !$accObj ? lang('Net_Income') : $accName).'</td><td>';
								echo '0</td><td>';
								echo $this->sma->formatMoney($entry['entryCreditBalance'][$cr_index]).'</td><td>';
								$crc_index=0;
									foreach ((Array)$entry['costCenterCreditId'][$cr_index] as $costCreditId){
                                    if(isset($entry['costCenterCreditAmount'][$cr_index][$crc_index]) && $entry['costCenterCreditAmount'][$cr_index][$crc_index]>0) {
										$ccId = $entry['costCenterCreditId'][$cr_index][$crc_index];
										$ccObj = isset($cost_centers_map[$ccId]) ? $cost_centers_map[$ccId] : $this->accounts_model->getCostCentersByID($ccId);
										$ccName = $ccObj ? $ccObj->costName : '';
										echo $ccName.'</br><strong>'.$this->sma->formatMoney($entry['costCenterCreditAmount'][$cr_index][$crc_index]).'</strong></br>';
									}
								$crc_index++;
									}

                                $entryDescription=$entry['entryCreditDescription'][$cr_index];
        						$entryDescription= str_replace("<p>","",($entryDescription)."");
        						$entryDescription= str_replace("</p>","",($entryDescription)."");
		    					$iof=strpos(trim($entryDescription), "#");
			        			if($iof>-1)
					        		$entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);

								echo '</td><td style="white-space: normal; word-break: break-word;">'.($iof>-1 ? $entryDescription : $entry['entryCreditDescription'][$cr_index]).'</td>';
								echo '</tr><tr>';
								$sr++;
								$cr=$cr+$entry['entryCreditBalance'][$cr_index];
								$cr_index++;



							}


						?>
					</tbody>
					<tfoot>
					<tr>
                            <td colspan="2"><?= lang('total'); ?>

                            </td>
					        <td>
							<?= $this->sma->formatMoney($dt); ?>

                            </td>
					        <td>
							<?= $this->sma->formatMoney($cr); ?>

                            </td>
							<td>

                            </td>
								<td>

                            </td>

					</tr>

					</tfoot>


                </table>
            </div>

            <div class="row" style="display:none">
                <div class="col-xs-6">
                    <?php if ($inv->note || $inv->note != '') {
                        ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('note'); ?>:</p>
                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                    <?php
                    } ?>
                </div>
				<div class="col-xs-6">
                    <?php if ($inv->approve_note || $inv->approve_note != '') {
                        ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('approve_note'); ?>:</p>
                            <div><?= $this->sma->decode_html($inv->approve_note); ?></div>
                        </div>
                    <?php
                    } ?>
                </div>


                <div class="col-xs-5 pull-right">
                    <div class="well well-sm">
                        <p>
                            <?= lang('created_by'); ?>: <?= $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                            <?= lang('date'); ?>: <?= $this->sma->hrld($inv->openingDate); ?>
                        </p>
                        <?php if ($inv->updated_by) {
                        ?>
                        <p>
                            <?= lang('updated_by'); ?>: <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                            <?= lang('update_at'); ?>: <?= $this->sma->hrld($inv->updated_at); ?>
                        </p>
                        <?php
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
