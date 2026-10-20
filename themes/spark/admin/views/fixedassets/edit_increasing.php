<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_increasing'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('fixedassets/edit_increasing/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>


            <div class="row">
            <div class="col-sm-4">
                        <div class="form-group">
                            <?= lang('Start_date', 'start_date'); ?>
                            <?= form_input('start_date', $this->sma->hrsd($increasing->start_date), 'readonly class="form-control date" id="start_date" required="required"'); ?>
                        </div>
            </div>

            <div class="col-sm-4">
                        <div class="form-group">
                            <?= lang('End_date', 'end_date'); ?>
                            <?= form_input('end_date', $this->sma->hrsd($increasing->end_date), 'readonly class="form-control date" id="end_date" required="required"'); ?>
                        </div>
            </div>

            <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="reference_no"><?php echo $this->lang->line('reference_no'); ?></label>
                <?php echo form_input('reference_no', $increasing->reference_no, ' readonly class="form-control" id="reference_no" required="required"'); ?>
            </div>
            </div>

            </div>

            <div class="row">
            <div class="col-md-6">

            <div class="form-group">
                <label class="control-label" for="fixed_asset_id"><?= lang('Fixed_asset'); ?></label>
                                <?php
                                $ast[''] = lang('select') . ' ' . lang('Fixed_asset');
                                foreach ($fixedAssetList as $fixedAsset) {
									$fixedAsset = (array) $fixedAsset;

										$ast[$fixedAsset['id']] = $fixedAsset['code']." - ".$fixedAsset['name'];


                                }
                                echo form_dropdown('fixed_asset_id', $ast, $increasing->fixed_asset_id, 'class="form-control  input-tip select" id="fixed_asset_id"  required="required"  style="width:100%"')
                                ?>
            </div>

            <div class="form-group">
                <?= lang('Type_of_fixed_asset_increase', 'increasing_type'); ?>
                <?php

                $increasing_type_options =  ['' => lang('select') . ' ' . lang('Type_of_fixed_asset_increase'),1 => lang('Maintenance'), 2 => lang('Repair'), 3 => lang('Upgraded'), 4 => lang('Purchase_spare_parts')]; ?>
                <?= form_dropdown('increasing_type', $increasing_type_options, $increasing->increasing_type, 'class="form-control" id="increasing_type"data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Type_of_fixed_asset_increase') . '"  required="required"'); ?>
            </div>

            <div class="form-group effect_type_cost" style="display:none;">
                        <?= lang('cost'); ?>
                        <?= form_input('cost',$this->sma->formatDecimal($increasing->cost,4), ' class="form-control tip" id="cost"'); ?>
			</div>

            <div class="form-group effect_type_cost" style="display:none;">
                        <?= lang('tax'); ?>
                        <?= form_input('tax',$this->sma->formatDecimal($increasing->tax,4), 'readonly class="form-control tip" id="tax"'); ?>
			</div>


            <div class="form-group effect_type_useful_life" style="display:none;">
                        <?= lang('Useful_life'); ?> ( <?= lang('Per_year'); ?> )
                        <?= form_input('useful_life',$increasing->useful_life, ' class="form-control tip" id="useful_life"'); ?>
			</div>


            </div>
            <div class="col-md-6">

            <div class="form-group">
            <?= lang('supplier', 'supplier_id'); ?>

                <?php

                $sup[''] = lang('select') . ' ' . lang('supplier');
                foreach ($suppliers as $supplier) {
                    $supplier = (array) $supplier;
                                    if ($this->Settings->user_language == 'arabic' || $supplier['companyEn']==''){
				$sup[$supplier['id']] = $supplier['company'];
				}else{
				$sup[$supplier['id']] = $supplier['companyEn'];

				}
                                }
                                echo form_dropdown('supplier_id', $sup, $increasing->supplier_id, 'class="form-control" id="supplier_id" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('supplier') . '" required="required"  ');
                                ?>
            </div>

            <div class="form-group">
                <?= lang('The_effect_of_increasing_on', 'effect_type'); ?>
                <?php

                $effect_type_options =  ['' => lang('select') . ' ' . lang('The_effect_of_increasing_on'),1 => lang('cost'), 2 => lang('Useful_life')]; ?>
                <?= form_dropdown('effect_type', $effect_type_options, $increasing->effect_type, 'class="form-control" id="effect_type" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('The_effect_of_increasing_on') . '"  required="required"'); ?>
            </div>

            <div class="form-group effect_type_cost" style="display:none;">
                                            <?= lang('order_tax') ?>
                                            <?php
                                            $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('tax_id', $tr, $increasing->tax_id ? $increasing->tax_id : '1', 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
             </div>

             <div class="form-group effect_type_cost" style="display:none;">
                        <?= lang('Net'); ?>
                        <?= form_input('net',$this->sma->formatDecimal($increasing->net,4), 'readonly class="form-control tip" id="net"'); ?>
			</div>



            </div>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_textarea('description', $this->sma->decode_html($increasing->description), 'class="form-control" id="description"'); ?>
            </div>






        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_increasing', lang('Edit_increasing'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
$(document).ready(function() {


    if($("#effect_type").val()==1){
        $('.effect_type_cost').show();
    }else{
        $('.effect_type_cost').hide();
    }

    if($("#effect_type").val()==2){
        $('.effect_type_useful_life').show();
    }else{
        $('.effect_type_useful_life').hide();
    }

});

$(document).on('change', '#effect_type', function () {
    if($("#effect_type").val()==1){
        $('.effect_type_cost').show();
    }else{
        $('.effect_type_cost').hide();
    }

    if($("#effect_type").val()==2){
        $('.effect_type_useful_life').show();
    }else{
        $('.effect_type_useful_life').hide();
    }



});

function calculate_price() {

            var tax_rates =<?php echo json_encode($tax_rates); ?>;

            var pp = 0,invoice_tax=0;

            if($('#cost').val()){

            pp=formatDecimal($('#cost').val(),4);

                    $.each(tax_rates, function () {
                    if (this.id == $('#potax2').val()) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((pp) * this.rate) / 100), 12);
                        }
                    }
                });
                    $('#tax').val(formatDecimal(invoice_tax,4));
					$('#net').val(formatDecimal(pp+invoice_tax,4));

            }

            return true;
        }

$('#potax2,#cost').change(function (e) {
            calculate_price();
        });
</script>


