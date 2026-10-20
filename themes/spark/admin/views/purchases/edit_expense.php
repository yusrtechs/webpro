<?php defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('display_errors', 0);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_expense'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('purchases/edit_expense/' . $expense->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
          <div class="col-md-6">


            <?php if ($Owner || $Admin) {
            ?>

                <div class="form-group">
                    <?= lang('date', 'date'); ?>
                    <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($expense->date)), 'class="form-control datetime" id="date" required="required"'); ?>
                </div>
            <?php
        } ?>

            <div class="form-group">
                <?= lang('reference', 'reference'); ?>
                <?= form_input('reference', (isset($_POST['reference']) ? $_POST['reference'] : $expense->reference), 'class="form-control tip" id="reference" required="required"'); ?>
            </div>


			<?php
				if($Settings->accounting){
			?>

			<div class="form-group">
                <?= lang('Account', 'Account'); ?>
                <?php
                $ct[''] = lang('select') . ' ' . lang('Account');
                if ($categories) {
                    foreach ($expenses_accounts as $category) {
                        $ct[$category->id] = $category->accountName;
                    }
                }
                ?>
                <?= form_dropdown('account_id', $ct, set_value('category', $expense->account_id), 'class="form-control tip" id="account_id"'); ?>
            </div>

            <div class="form-group" style="display:none;">
                <?= lang('Cost_center', 'Cost_center'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('Cost_center');
                foreach ($cost_centers as $cost_center) {
                    $wh[$cost_center->id] = $cost_center->costName;
                }
                echo form_dropdown('cost_id', $wh, set_value('category', $expense->cost_id), 'id="cost_id" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>

			<?php
				}else{
			?>


            <div class="form-group">
                <?= lang('category', 'category'); ?>
                <?php
                $ct[''] = lang('select') . ' ' . lang('category');
                foreach ($categories as $category) {
                    $ct[$category->id] = $category->name;
                }
                ?>
                <?= form_dropdown('category', $ct, set_value('category', $expense->category_id), 'class="form-control tip" id="category"'); ?>
            </div>

            <div class="form-group">
                <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('select') . ' ' . lang('warehouse');
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse', $wh, set_value('warehouse', $expense->warehouse_id), 'id="warehouse" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>

			<?php
				}
			?>

                <div class="form-group">
                <?= lang('attachment', 'attachment') ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

            <div id="CostCenters" style="display: none;">
						<?php
						if($Settings->accounting)
                        {
						?>

                                <div class="form-group">
									<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;width:50%;" >
										<button id="addCostCenter" class="btn btn-primary btn-block" type="button"><?= lang('Add_cost_center') ?></button>
									</div>
                                </div>
						<?php
						}
						?>
                        </div>

            </div>



            <div class="col-md-6">

            <?php
                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
                    if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
                            <div class="form-group">
                                <?= lang('Main_group', 'Main_group') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $expense->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                ?>
            </div>
            ?>
                            </div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="form-group">
                                <?= lang('Main_company', 'Main_company') ?>
                                <?php
                                $co[''] = lang('select') . ' ' . lang('Main_company');
                                if(($this->Settings->enable_grp==1)){}else{
                                    foreach ($mainCompanies as $mainCompany) {
                                        $co[$mainCompany->id] = $mainCompany->name;
                                    }
                                }

                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $expense->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
                ?>
            </div>
            <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php
                                $br[''] = lang('select') . ' ' . lang('Main_branch');
                                if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                    foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                }
                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $expense->brId), 'class="form-control select" id="brId"  required="required" style="width:100%"')
                    ?>
                </div>
                <?php } } ?>


            <div class="form-group">
                <?= lang('amount', 'amount'); ?>
                <input name="amount" type="text" id="amount" value="<?= $this->sma->formatDecimal($expense->total); ?>"
                       class="pa form-control kb-pad amount" required="required"/>
            </div>
			<?php if ($Settings->tax1) {
                                                ?>
            <div class="form-group">
				<?= lang('order_tax', 'potax2') ?>
				<?php
				$tr[''] = '';
				foreach ($tax_rates as $tax) {
				$tr[$tax->id] = $tax->name;
				}
				echo form_dropdown('order_tax', $tr, set_value('order_tax', $expense->order_tax_id), 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
            </div>

            <div class="form-group all">
                                <?= lang('tax_method', 'mtax_method') ?>
                                <?php
                                $tm = [ '1' => lang('exclusive'),'0' => lang('inclusive')];
                                echo form_dropdown('tax_method', $tm, $expense->tax_method, 'class="form-control select" id="mtax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
            </div>


			<?php
            } ?>
			<div class="form-group">
                <?= lang('tax', 'potax2'); ?>
                <input name="total_tax" type="text" id="total_tax" value="<?= $this->sma->formatDecimal($expense->total_tax); ?>" class="pa form-control kb-pad total_tax"
                       required="required" readonly/>
            </div>

			<div class="form-group">
                <?= lang('grand_total', 'potax2'); ?>
                <input name="grand_total" type="text" id="grand_total" value="<?= $this->sma->formatDecimal($expense->grand_total); ?>" class="pa form-control kb-pad grand_total"
                       required="required" readonly/>
            </div>

            </div>
                       </div>




            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $expense->note), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_expense', lang('edit_expense'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        var accounting  = <?=$Settings->accounting?>;

        if(accounting){
        var costCount  = 0;
			        //$('#CostCenters').slideDown();

		$('button#addCostCenter').click(function(){
			var divOption    = '<div class="row" id="costRow'+costCount+'">';
			var selectOption = '<div class="col-5" style="padding-left:30px;padding-right:25px;"><select class="form-control"  name="costCenterId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
            if($Settings->accounting){
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
        }
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-4" dir="ltr"><input class="form-control" placeholder="<?= lang('Amount') ?>" name="costCenterAmount[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCenters').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(inputOption);
			$('#costRow'+costCount).append(buttonOption);
			costCount += 1;

		});

        $('#CostCenters').slideDown();
    }

        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
		localStorage.setItem('potax2', $('#potax2').val());

		var invoice_tax = 0,
		total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;

		if (site.settings.tax2 != 0) {
			$('#potax2,#mtax_method').change(function () {
			localStorage.setItem('potax2', $('#potax2').val());

			total = $('#amount').val();
			try{
			if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            if($('#mtax_method').val()==1)
                                invoice_tax = formatDecimal((((total) * this.rate) / 100), 4);
                            else
                                invoice_tax = formatDecimal((((total) * this.rate) / 115), 4);
                        }
                    }
                });
            }
			$('#total_tax').val(invoice_tax);
            if($('#mtax_method').val()==1)
			$('#grand_total').val(formatDecimal(((parseFloat(total) + parseFloat(invoice_tax))), 4));
            else
            $('#grand_total').val(formatDecimal(total, 4));

			}catch(err){alert(err);}

			return;
		});
		}

		$('#amount').change(function () {
			//localStorage.setItem('potax2', $(this).val());

			total = $('#amount').val();
			try{
			if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            if($('#mtax_method').val()==1)
                                invoice_tax = formatDecimal((((total) * this.rate) / 100), 4);
                            else
                                invoice_tax = formatDecimal((((total) * this.rate) / 115), 4);
                        }
                    }
                });
            }
			$('#total_tax').val(invoice_tax);
            if($('#mtax_method').val()==1)
			$('#grand_total').val(formatDecimal(((parseFloat(total) + parseFloat(invoice_tax))), 4));
            else
            $('#grand_total').val(formatDecimal(total, 4));
			}catch(err){alert(err);}

			return;
		});

    });
</script>

<script>
    $(document).ready(function () {

       $('#grpId').select2('readonly', true);
       $('#coId').select2('readonly', true);
       $('#brId').select2('readonly', true);
    }
    );

</script>
