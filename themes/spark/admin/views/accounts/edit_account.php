<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(document).ready(function() {
		var costCount  = 0;
		$('button#addCostCenter').click(function(){
			var divOption    = '<div class="row" id="costRow'+costCount+'">';
			var selectOption = '<div class="col-5" style="padding-left:30px;padding-right:25px;"><select class="form-control"  id="costId'+costCount+'" name="costCenterId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-4" dir="ltr"><input class="form-control" placeholder="<?= lang('%') ?>" id="costPercentage'+costCount+'" name="costCenterPercentage[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCenters').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(inputOption);
			$('#costRow'+costCount).append(buttonOption);
			costCount += 1;

		});





        $('#base_account_type').change(function(e) {
            var bu = $(this).val();
            if(bu > 0)
                $('#measuring').slideDown();
            else
                $('#measuring').slideUp();
        });
        var obu = <?= !empty($account_type->base_account_type) ? $account_type->base_account_type : 0; ?>;
        if(obu > 0)
            $('#measuring').slideDown();
        else
            $('#measuring').slideUp();
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_account'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/edit_account/' . $account_details->accountID, $attrib); ?>
        <div class="modal-body col-md-12">
            <p><?= lang('enter_info'); ?></p>


			<div class="col-md-4">
            <div class="form-group">
                <?= lang('Account_No', 'Account_No'); ?>
                <?= form_input('accountID', $account_details->accountID, 'class="form-control tip" id="accountID" required="required" readonly'); ?>
            </div>
            <div class="form-group">
               <?= lang('Account_type', 'Account_type'); ?>
                <?php
                $opts[0] = lang('Detailed_account');
				$opts[1] = lang('Master_account');


                ?>
                <?= form_dropdown('master_account', $opts, set_value('master_account', $account_details->master_account), 'class="form-control tip" id="master_account" style="width:100%;"'); ?>
            </div>
            <div class="form-group">
             <?= lang('Account_type', 'Account_type'); ?>
                <?php
                $opts[0] = lang('Debit');
				$opts[1] = lang('Credit');


                ?>
                <?= form_dropdown('accountSide', $opts, set_value('accountSide', $account_details->accountSide==='Debit' ? 0 : 1), 'class="form-control tip" id="accountSide" style="width:100%;"'); ?>
            </div>

			</div>


        	<div class="col-md-8">
            <div class="form-group">
                <?= lang('Account_name', 'Account_name'); ?>
                <?= form_input('accountName', set_value('accountName', $account_details->accountName), 'class="form-control tip" id="accountName" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('accountNameEn', set_value('accountNameEn', $account_details->accountNameEn), 'class="form-control tip" id="accountName" '); ?>
            </div>


			<?php
			if($account_type=="warehouses"){
			?>

                            <?php
                            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                                ?>
                           <div class="form-group">
             <?= lang('Showroom', 'Showroom'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('Showroom');

                foreach ($warehouses as $warehouse) {
                $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" style="width:100%;" ');
				?>
            </div>


                                   <?php
                            }else{
                                ?>
                            <div class="form-group">
             <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('warehouse');

                foreach ($warehouses as $warehouse) {
                $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" style="width:100%;" ');
				?>
            </div>

                           <?php
                            }
                                ?>
			<?php
			}else if($account_type=="sales"){
			?>
			<div class="form-group">
             <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('warehouse');

                foreach ($warehouses as $warehouse) {
                $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="return_sales"){
			?>
			<div class="form-group">
             <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('warehouse');

                foreach ($warehouses as $warehouse) {
                $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="local_suppliers"){
			?>
			<div class="form-group">
             <?= lang('supplier', 'supplier'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('supplier');

                foreach ($local_suppliers as $supplier) {
                $wh[$supplier->id] = $supplier->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('supplier') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="international_suppliers"){
			?>
			<div class="form-group">
             <?= lang('supplier', 'supplier'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('supplier');

                foreach ($international_suppliers as $supplier) {
                $wh[$supplier->id] = $supplier->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('supplier') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="local_customers"){
			?>
			<div class="form-group">
             <?= lang('customer', 'customer'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('customer');

                foreach ($local_customers as $customer) {
                $wh[$customer->id] = $customer->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="international_customers"){
			?>
			<div class="form-group">
             <?= lang('customer', 'customer'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('customer');

                foreach ($international_customers as $customer) {
                $wh[$customer->id] = $customer->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" style="width:100%;" ');
				?>
            </div>
			<?php
			}else if($account_type=="insurance"){
			?>
            <input type="hidden" name="in" id="in" value="1" />
			<div class="form-group">
             <?= lang('customer', 'customer'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('customer');

                foreach ($local_customers as $customer) {
                    $wh[$customer->id] = $customer->company;
                }
                foreach ($international_customers as $customer) {
                    $wh[$customer->id] = $customer->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" style="width:100%;" ');
				?>
            </div>


			<?php
			}else if($account_type=="advance"){
			?>
            <input type="hidden" name="ad" id="ad" value="1" />
			<div class="form-group">
             <?= lang('customer', 'customer'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('customer');

                foreach ($local_customers as $customer) {
                    $wh[$customer->id] = $customer->company;
                }
                foreach ($international_customers as $customer) {
                    $wh[$customer->id] = $customer->company;
                }
                echo form_dropdown('account_ref_id', $wh, $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" style="width:100%;" ');
				?>
            </div>


			<?php
			}else if($account_type=="billers"){
			?>

            <input type="hidden" name="bi" id="bi" value="<?= $bi; ?>" />
			<div class="form-group">
             <?= lang('biller', 'biller'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('biller');

                foreach ($billers as $biller) {
                $wh[$biller->id] = $biller->company;
                }
                echo form_dropdown('account_ref_id', $wh,  $account_details->account_ref_id, 'id="account_ref_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" style="width:100%;" ');
				?>
            </div>

            <?php
            if($account_details->account_ref_id2){
                ?>
            <div class="form-group">
             <?= lang('biller', 'biller'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('biller');

                foreach ($billers as $biller) {
                $wh[$biller->id] = $biller->company;
                }
                echo form_dropdown('account_ref_id2', $wh,  $account_details->account_ref_id2, 'id="account_ref_id2" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" style="width:100%;" ');
				?>
            </div>
                <?php
            }

            ?>

             <?php
            if($account_details->account_ref_id3){
                ?>
            <div class="form-group">
             <?= lang('biller', 'biller'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('biller');

                foreach ($billers as $biller) {
                $wh[$biller->id] = $biller->company;
                }
                echo form_dropdown('account_ref_id3', $wh,  $account_details->account_ref_id3, 'id="account_ref_id3" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" style="width:100%;" ');
				?>
            </div>
                <?php
            }

            ?>

            <?php
            if($account_details->account_ref_id4){
                ?>
            <div class="form-group">
             <?= lang('biller', 'biller'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('biller');

                foreach ($billers as $biller) {
                $wh[$biller->id] = $biller->company;
                }
                echo form_dropdown('account_ref_id4', $wh,  $account_details->account_ref_id4, 'id="account_ref_id4" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" style="width:100%;" ');
				?>
            </div>
                <?php
            }

            ?>

            <?php
            if($account_details->account_ref_id5){
                ?>
            <div class="form-group">
             <?= lang('biller', 'biller'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('biller');

                foreach ($billers as $biller) {
                $wh[$biller->id] = $biller->company;
                }
                echo form_dropdown('account_ref_id5', $wh,  $account_details->account_ref_id5, 'id="account_ref_id5" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" style="width:100%;" ');
				?>
            </div>
                <?php
            }

            ?>


            <div class="form-group">
                <?= lang('biller', 'biller') ?>
                <button type="button" class="btn btn-primary btn-xs" id="addAccount"><i class="fa fa-plus"></i>
                </button>
            </div>

            <div id="ex-billers"></div>


			<?php
			}
			?>

			</div>

            <div class="col-md-12">
			<div class="form-group" id="CostCenters">
					<div class="row">

						<div  style="padding-left:15px;padding-bottom:10px;padding-right:10px;padding-top:10px;" >
							<button id="addCostCenter" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
                    </div>
                    </div>

				<?php

				if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['costCenterId'] = json_decode($account_details->costCenterId);
						$entry['costCenterPercentage'] = json_decode($account_details->costCenterPercentage);

						$cost_index=0;
							foreach ($entry['costCenterId'] as $cost_center){



								 ?>
								 <script>
								 $( "#addCostCenter" ).trigger( "click" );
								 $('#costId'+'<?= $cost_index; ?>').val('<?= $cost_center; ?>')
								 $('#costPercentage'+'<?= $cost_index; ?>').val('<?= $entry["costCenterPercentage"][$cost_index]; ?>')
								 </script>
								 <?php

								$cost_index++;


							}

					} ?>




        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_account', lang('Edit_account'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript">

    function billers(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'accounts/suggestionsBillers',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10,
                };
            },
            results: function(data, page) {
                console.log(data)
                if (data != null) {
                    return { results: data };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            },
        },
    });
}
    $(document).ready(function() {
        var su = '<?= ($bi+1); ?>';
        $('#addAccount').click(function () {
            if (su <= 5) {
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="account_ref_id' + su + '", class="form-control" id="account_ref_id' + su + '" placeholder="<?= lang('select') . ' ' . lang('biller') ?>" style="width:100%;display: block !important;" /></div></div></div>';
                $('#ex-billers').append(html);
                var sup = $('#account_ref_id' + su);
                billers(sup);
                $('#bi').val(su);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });
    });
</script>

