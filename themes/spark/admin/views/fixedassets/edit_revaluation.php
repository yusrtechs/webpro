<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_revaluation'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('fixedassets/edit_revaluation/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>


            <div class="row">
            <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang('date', 'date'); ?>
                            <?= form_input('date', $this->sma->hrld($revaluation->date), 'readonly class="form-control datetime" id="date" required="required"'); ?>
                        </div>
            </div>

            <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label" for="reference_no"><?php echo $this->lang->line('reference_no'); ?></label>
                <?php echo form_input('reference_no', $revaluation->reference_no, ' readonly class="form-control" id="reference_no" required="required"'); ?>
            </div>
            </div>

            </div>

            <div class="form-group">
                <label class="control-label" for="fixed_asset_id"><?= lang('Fixed_asset'); ?></label>
                                <?php
                                $ast[''] = lang('select') . ' ' . lang('Fixed_asset');
                                foreach ($fixedAssetList as $fixedAsset) {
									$fixedAsset = (array) $fixedAsset;

										$ast[$fixedAsset['id']] = $fixedAsset['code']." - ".$fixedAsset['name'];


                                }
                                echo form_dropdown('fixed_asset_id', $ast, $revaluation->fixed_asset_id, 'class="form-control  input-tip select" id="fixed_asset_id"  required="required"  style="width:100%"')
                                ?>
                            </div>

            <div class="form-group">
                <?= lang('Fixed_asset_account', 'accountID') ?>

                <?php
                $acc[''] = lang('select') . ' ' . lang('Fixed_asset_account');
                foreach ($accountsList as $account) {
                    $account = (array) $account;
                                    if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				$acc[$account['id']] = $account['accountID']." - ".$account['accountName'];
				}else{
				$acc[$account['id']] = $account['accountID']." - ".$account['accountNameEn'];

				}
                                }
                                echo form_dropdown('accountID', $acc, $revaluation->accountID, 'class="form-control" id="accountID" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Account') . '" required="required"  ');
                                ?>
            </div>

            <div class="form-group">
                <label class="control-label" for="book_value"><?php echo $this->lang->line('The_book_value_of_the_asset'); ?></label>
                <input class="form-control" placeholder="<?= lang('The_book_value_of_the_asset') ?>" name="book_value" id="book_value" readonly  type="number" min="0" step=".01" value="<?= $this->sma->formatDecimal($revaluation->book_value) ?>" required />
            </div>

            <div class="form-group">
                <label class="control-label" for="revaluation_value"><?php echo $this->lang->line('Revaluation_value'); ?></label>
                <input class="form-control" placeholder="<?= lang('Revaluation_value') ?>" name="revaluation_value" id="revaluation_value" type="number" min="0" step=".01" value="<?= $this->sma->formatDecimal($revaluation->revaluation_value) ?>"  required />
            </div>






        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_revaluation', lang('Edit_revaluation'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>


<script>
$(document).ready(function() {

    $('#fixed_asset_id, #date').change(function () {

			$("#div_tax_method").css("display", "block");


			try{
		$.ajax({
                type: 'post',
                async: false,
                url: site.base_url + 'fixedassets/getBookValue',
                dataType: 'json',
                data: { fixed_asset_id : $('#fixed_asset_id').val(), date : $('#date').val() },
                success: function(data, page) {



					if (data>0) {
                        $('#book_value').val(formatDecimal(data,2));




                    } else {
                    	$('#book_value').val('');



                    }

                },
            });

		}catch(err){$('#book_value').val('');}

        });

        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());

});
</script>
