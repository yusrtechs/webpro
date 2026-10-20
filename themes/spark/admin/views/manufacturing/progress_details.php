<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.pr_link{
    cursor: pointer;
}
</style>

<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = [];
}
?>
<script type="text/javascript">
    $(document).ready(function () {

				$(document).on('click', '.pr_link', function(e) {
        var row = $(this).closest('tr');
        var itemid = row.find('.rid').val();
        $('#myModal').modal({ remote: site.base_url + 'products/modal_view/' + itemid });
        $('#myModal').modal('show');
    });

		$('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<div class="box spark-manufacturing-page spark-manufacturing-details">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file-text-o nb"></i><?= lang('manufacturing').' ( ' .$bom_details->name.' ) ' ; ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">




				<div class="row">
                            <div class="col-sm-5">
                                <img src="<?= base_url() ?>assets/uploads/<?= $bom_details->image ?>"
                                     alt="<?= $bom_details->name ?>" class="img-responsive img-thumbnail"/>


                            </div>
                            <div class="col-sm-7">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped dfTable table-right-left">
                                        <tbody>
                                        <tr>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Bill_Of_Material'); ?></td>
                                            <td><?php echo $bom_details->name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('date'); ?></td>
                                            <td><?php echo $this->sma->hrld($man_details->date_produce); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Raw_materials_warehouse'); ?></td>
                                            <td><?= $bom_details->wh_raw_name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Manufacturing_warehouse'); ?></td>
                                            <td><?php echo $bom_details->wh_manufacturing_name	; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Destruction_warehouse'); ?></td>
                                            <td><?= $bom_details->wh_destruction_name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('quantity'); ?></td>
                                            <td><?php echo $this->sma->formatQuantity($man_details->quantity,4); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Deadline_Start'); ?></td>
                                            <td><?php echo $this->sma->hrsd($man_details->deadline_start); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('created_by'); ?></td>
                                            <td><?= $created_by->first_name . ' ' . $created_by->last_name; ?></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>


							<div class="col-sm-12">

                                <?= $man_details->description ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('description') . '</div><div class="panel-body">' . $bom_details->description . '</div></div>' : ''; ?>

                            </div>
                            </div>
							<div class="clearfix"></div>

							<div class="col-sm-12">

							<div class="alert alert-danger" id="mError-con" style="display: none;">
                    			<span id="mError"></span>
							</div>

								<div class="card1">
                            <div class="card-body">
                                <h2 class="header-title"><i class="fa fa-cubes"></i> <?= lang('Raw_materials') ?> ( <?php echo $bom_details->name; ?> )</h2>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase">
                                                <tr class="table-active">
                                                    <th scope="col"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                                    <th scope="col"><?= lang('unit') ?></th>
                                                    <th scope="col"><?= lang('cost') ?></th>
                                                    <th scope="col"><?= lang('Standard_quantity') ?></th>
                                                    <th style="max-width:100px;"><?= lang('Actual_quantity') ?></th>
                                                    <th scope="col" style="width:200px;"><?= lang('Total') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php
												$gTotal=0;
                                                foreach ($bom_detail as $key => $value) : ?>
                                            	<?php $row_no=$value->id; $qty = $value->qty;$actual_qty = $value->actual_qty;$cost = $value->avg_cost; $total = $actual_qty*$cost;  ?>
                                                <tr id="row_<?= $row_no ?>"  data-item-id="<?= $row_no ?>">
                                                    <input type="hidden" class="rid" value="<?php echo $value->id; ?>"><td scope="row"  class="pr_link"><?php echo $value->code.' - '.$value->name; ?></td>
                                                    <td><?php echo $value->unit_name; ?></td>
                                                    <td><input class="form-control text-center " name="combo_item_stock[]" type="hidden" value="<?= $cost; ?>" data-id="<?= $row_no ?>" data-item="<?= $row_no ?>" id="stock_<?= $row_no ?>" onClick="this.select();"><?php echo $this->sma->formatDecimal($cost,4); ?></td>
                                                    <td><?php  echo  $this->sma->formatQuantity($qty,4); ?></td>
                                                    <td  style="max-width:100px;"><input class="form-control" name="actual_qty_<?php echo $value->id; ?>" id="actual_qty_<?php echo $value->id; ?>" value="<?php  echo  ($actual_qty); ?>" readonly type="text" step=".00" onchange="actual_qty('<?php echo $value->id; ?>')" required /></td>
                                                    <td  style="max-width:100px;"><input class="form-control" name="actual_qty_total_<?php echo $value->id; ?>" id="actual_qty_total_<?php echo $value->id; ?>" value="<?php  echo  $this->sma->formatDecimal($total); ?>" readonly type="text"  required /></td>
                                                </tr>
                                            <?php
											$gTotal=$gTotal+$total;
											endforeach;
											?>
                                            </tbody>
                                            <tfoot>
												<tr id="tfoot" class="tfoot active"><th colspan="5"><?= lang('Total_cost_of_raw_materials') ?></th><th><input class="form-control" name="gTotal_row" id="gTotal_row" value="<?php  echo  $this->sma->formatDecimal($gTotal,4); ?>" readonly type="text"  style="border:none;" required /></th><tr>
												<tr id="tfoot" class="tfoot active" style="display:none;"><th colspan="4"><?= lang('avg_cost') ?></th><th><?php  echo  $this->sma->formatDecimal($gTotal/($man_details->quantity*$bom_details->quantity),4); ?></th><tr>
											</tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <h2 class="header-title"><i class="fa fa-users"></i> <?= lang('Labor_cost') ?> ( <?php echo $bom_details->name; ?> )</h2>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase">
                                                <tr class="table-active">
                                                    <th scope="col"><?= lang('Declaration'); ?></th>
                                                    <th scope="col"><?= lang('Hourly_wage') ?></th>
                                                    <th scope="col"><?= lang('Standard_working_hours') ?></th>
                                                    <th scope="col" style="width:200px;"><?= lang('Actual_working_hours') ?></th>
                                                    <th scope="col" style="width:200px;"><?= lang('Total') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php
												$gTotal_labors=0;
												foreach ($bom_labors as $key => $value) : ?>
                                            	<?php $row_no=$value->id; $hours_no = $value->actual_hours_no;$hours_wage = $value->hours_wage; $total_labors = $hours_no*$hours_wage;  ?>
                                                <tr id="row_<?= $row_no ?>"  data-item-id="<?= $row_no ?>">
                                                    <input type="hidden" class="rid" value="<?php echo $value->id; ?>"><td scope="row"  ><?php echo $value->labor_desc; ?></td>
                                                    <td><input class="form-control text-center " name="combo_item_stock[]" type="hidden" value="<?= $hours_wage; ?>" data-id="<?= $row_no ?>" data-item="<?= $row_no ?>" id="hours_wage_<?= $row_no ?>" onClick="this.select();"><?php echo $this->sma->formatDecimal($hours_wage,4); ?></td>
                                                    <td><?php echo $this->sma->formatDecimal($value->hours_no,4); ?></td>
                                                    <td  style="max-width:100px;"><input class="form-control" name="actual_hours_no_<?php echo $value->id; ?>" id="actual_hours_no_<?php echo $value->id; ?>" value="<?php  echo  ($value->actual_hours_no); ?>" readonly type="text" step=".00" onchange="actual_hours_no('<?php echo $value->id; ?>')" required /></td>
                                                    <td  style="max-width:100px;"><input class="form-control" name="actual_hours_no_total_<?php echo $value->id; ?>" id="actual_hours_no_total_<?php echo $value->id; ?>" value="<?php  echo  $this->sma->formatDecimal($total_labors,4); ?>" readonly type="text"  required /></td>

                                                </tr>
                                            <?php
											$gTotal_labors=$gTotal_labors+$total_labors;
											endforeach;
                                            $gTotal=$gTotal+$gTotal_labors;
											?>
                                            </tbody>
                                            <tfoot>
												<tr id="tfoot" class="tfoot active"><th colspan="4"><?= lang('Total_labor_cost') ?></th><th><input class="form-control" name="gTotal_labors" id="gTotal_labors" value="<?php  echo  $this->sma->formatDecimal($gTotal_labors,4); ?>" readonly type="text" style="border:none;" required /></th><tr>
												<tr id="tfoot" class="tfoot active" style="display:none;"><th colspan="4"><?= lang('avg_cost') ?></th><th><?php  echo  $this->sma->formatDecimal($gTotal/($man_details->quantity*$bom_details->quantity),4); ?></th><tr>
											</tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <h2 class="header-title"><i class="fa fa-industry"></i> <?= lang('Industrial_expenses') ?> ( <?php echo $bom_details->name; ?> )</h2>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase">
                                                <tr class="table-active">
                                                    <th scope="col"><?= lang('Account'); ?></th>
                                                    <th scope="col"><?= lang('Standard_cost') ?></th>
                                                    <th scope="col" style="width:200px;"><?= lang('Actual_cost') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php
												$gTotal_expenses=0;
												foreach ($bom_expenses as $key => $value) : ?>
                                            	<?php $row_no=$value->id; $actual_cost = $value->actual_cost; $total_expenses = $actual_cost;  ?>
                                                <tr id="row_<?= $row_no ?>"  data-item-id="<?= $row_no ?>">
                                                    <input type="hidden" class="rid" value="<?php echo $value->id; ?>"><td scope="row"  ><?php echo $this->accounts_model->getAccountByID($value->expenses_acct)->accountName; ?></td>
                                                    <td><?php echo $this->sma->formatDecimal($value->cost,4); ?></td>
                                                    <td  style="max-width:100px;"><input class="form-control" name="actual_cost_<?php echo $value->id; ?>" id="actual_cost_<?php echo $value->id; ?>" value="<?php  echo  ($value->actual_cost); ?>" readonly type="text" step=".00" onchange="actual_cost('<?php echo $value->id; ?>')" required /></td>
                                                </tr>
                                            <?php
											$gTotal_expenses=$gTotal_expenses+$total_expenses;
											endforeach;
                                            $gTotal=$gTotal+$gTotal_expenses;
											?>
                                            </tbody>
                                            <tfoot>
												<tr id="tfoot" class="tfoot active"><th colspan="2"><?= lang('Total_industrial_expenses') ?></th><th><input class="form-control" name="gTotal_expenses" id="gTotal_expenses" value="<?php  echo  $this->sma->formatDecimal($gTotal_expenses,4); ?>" readonly type="text" style="border:none;" required /></th><tr>
												<tr id="tfoot" class="tfoot active" style="display:none;"><th colspan="4"><?= lang('avg_cost') ?></th><th><?php  echo  $this->sma->formatDecimal($gTotal/($man_details->quantity*$bom_details->quantity),4); ?></th><tr>
											</tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                            </div>








						<div class="row">

                            <div class="col-sm-12">
								<div class="form-group">
									<input type="checkbox" class="checkbox" name="rmr" <?php if($req_items) { echo "checked"; } ?>
                                   id="rmr"><label
                                for="rmr"
                                class="padding05"><?= lang('Request_for_raw_materials'); ?></label>
								</div>
							</div>
                                    <div class="col-sm-12" id="req_mr" <?php if(!$req_items) { echo "style='display:none;'"; } ?>>


						<div class="combo1" >
						<div class="col-md-4" >

							<div class="form-group">
                            <?= lang('Add_Raw_materials', 'add_item') ; ?>
                            <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
							</div>
                        </div>
                        <div class="col-sm-12">
						<div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang('Raw_materials'); ?></label>

                            <div class="controls table-controls">
                                <table id="rmrTable"
                                       class="table items table-striped  table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('cost'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('Total'); ?></th>
                                        <!--<th class="col-md-3 col-sm-3 col-xs-3"><?= lang('price'); ?></th>-->
                                        <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
									<tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
						    <div id="ex-combo"></div>



                    </div>



                                    </div>






									<div class="col-sm-12">
								<div class="form-group">
									<input type="checkbox" class="checkbox" name="dmr" <?php if($dest_items) { echo "checked"; } ?>
                                   id="dmr"><label
                                for="dmr"
                                class="padding05"><?= lang('destruction'); ?></label>
								</div>
							</div>
                                    <div class="col-sm-12" id="dest_mr" <?php if(!$dest_items) { echo "style='display:none;'"; } ?>>


						<div class="combo1" >
						<div class="col-md-4" >

							<div class="form-group">
                            <?= lang('Destruction_Raw_materials', 'add_item') ; ?>
                            <?php echo form_input('add_item_dest', '', 'class="form-control ttip" id="add_item_dest" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
							</div>
                        </div>
                        <div class="col-sm-12">
						<div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang('Raw_materials'); ?></label>

                            <div class="controls table-controls">
                                <table id="dmrTable"
                                       class="table items_dest table-striped  table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('cost'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('Total'); ?></th>
                                        <!--<th class="col-md-3 col-sm-3 col-xs-3"><?= lang('price'); ?></th>-->
                                        <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
									<tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
						    <div id="ex-combo"></div>



                    </div>



                                    </div>


                                </div>


								<div class="col-sm-6">
                            <h3 class="bold"><?= lang('Quantity_manufactured_based_on_BOM') ?></h3>
                            <div class="table-responsive">
                                <table
                                class="table  table-striped table-condensed dfTable one-columns">
                                <thead>
                                    <tr>
                                        <th><?= lang('quantity'); ?></th>
										<th><?= lang('unit')  ?></th>
                                    	<th><?= lang('avg_cost')  ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
									//foreach ($warehouses as $warehouse) {


                                                //if ($warehouse->quantity != 0) {
														$avg_cost=$this->sma->formatDecimal($warehouse->avg_cost,4) ;

														echo '<tr><td><strong>' . $this->sma->formatQuantity($man_details->quantity*$bom_details->quantity,4) . '</strong>' . ($warehouse->rack ? ' (' . $warehouse->rack . ')' : '') . '</td><td class="text-center"><strong>'.$this->site->getUnitByID($this->site->getProductByID($bom_details->product_id)->unit)->name.'</strong></td><td class="text-center"><input disabled class="form-control text-center" name="nwavg_cost" id="nwavg_cost" type="hidden" value="'.($gTotal/($man_details->quantity*$bom_details->quantity)).'" >'.'<input disabled class="form-control text-center" name="gTotal" id="gTotal" type="hidden" value="'.($gTotal).'" >'.'<input disabled class="form-control text-center" name="nwavg_cost_show" id="nwavg_cost_show" type="text" value="'.$this->sma->formatDecimal($gTotal/($man_details->quantity*$bom_details->quantity),4).'" ></td></tr>';
													//}

                                            //}
											?>
                                                </tbody>
                            </table>
                        </div>
                        </div>

							<div class="col-sm-6">
                            <h3 class="bold"><?= lang('Confirm_the_manufactured_quantity') ?></h3>
                            <div class="table-responsive">
                                <table
                                class="table  table-striped table-condensed dfTable one-columns">
                                <thead>
                                    <tr>
                                        <th class="col-xs-6"><?= lang('quantity'); ?></th>
										<th><?= lang('avg_cost')  ?></th>
									</tr>
                                </thead>
                                <tbody>
                                    <?php
									//echo $man_details->confirm_qty;
									$confirm_qty=$man_details->confirm_qty ? $man_details->confirm_qty : ($man_details->quantity*$bom_details->quantity);
									//echo $confirm_qty;
									//foreach ($warehouses as $warehouse) {


                                                //if ($warehouse->quantity != 0) {
														$avg_cost=$this->sma->formatDecimal($warehouse->avg_cost,4) ;

														echo '<tr><td><input  class="form-control text-center rconfirm_qty" name="confirm_qty" id="confirm_qty" disabled type="text"  value="' . $this->sma->formatDecimal($confirm_qty,4) . '"></td><td><input disabled class="form-control text-center" name="fwavg_cost" id="fwavg_cost" type="text" ></td></tr>';
													//}

                                            //}
											?>
                                                </tbody>
                            </table>
                        </div>
                        </div>

						<input disabled class="form-control text-center" name="wavg_cost" id="wavg_cost" type="hidden" value="0">
						<input disabled class="form-control text-center" name="wavg_cost_dest" id="wavg_cost_dest" type="hidden" value="0">

							<div class="col-sm-12 " id="div_addProduce">
								<button type="button" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" id="back" ><i class="fa fa-arrow-left"></i> <?= lang('Back') ?></button>
							</div>



































                <div class="col-md-5" style="display:none;">
                    <div class="form-group">
                        <?= lang('product', 'type') ?>
                        <?php
                        $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service')];
                        echo form_dropdown('type', $opts, 'combo', 'class="form-control" id="type" required="required"');
                        ?>
                    </div>


                </div>





            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    function actual_qty(row_no){

        var bom_detail='<?= $bom_detail ?>';
        var gtotal=0;
        <?php
            foreach ($bom_detail as $key => $value){
        ?>
                var row_no = '<?= $value->id; ?>';
                var stock_cost = $('#stock_'+row_no).val();
                var actual_qty = $('#actual_qty_'+row_no).val();

                $('#actual_qty_total_'+row_no).val(formatDecimal((parseFloat(stock_cost)*parseFloat(actual_qty)),4));
                var actual_qty_total = $('#actual_qty_total_'+row_no).val();
                gtotal=parseFloat(gtotal)+parseFloat(actual_qty_total);

        <?php
        }
        ?>


    $('#gTotal_row').val(formatDecimal((parseFloat(gtotal)),4));
    var gTotal_row = $('#gTotal_row').val();
    var gTotal_labors = $('#gTotal_labors').val();
    var gTotal_expenses = $('#gTotal_expenses').val();

    var real_qty='<?= ($man_details->quantity*$bom_details->quantity) ?>';
    var real_gTotal=(parseFloat(gTotal_row)+parseFloat(gTotal_labors)+parseFloat(gTotal_expenses));

    $('#gTotal').val((parseFloat(real_gTotal)));


    $('#nwavg_cost').val((parseFloat(real_gTotal)/parseFloat(real_qty)));
    $('#nwavg_cost_show').val(formatDecimal((parseFloat(real_gTotal)/parseFloat(real_qty)),4));
    $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));

    //alert($('#nwavg_cost').val())
    }

    function actual_hours_no(row_no){

        var bom_labors='<?= $bom_labors ?>';
        var gtotal=0;
        <?php
            foreach ($bom_labors as $key => $value){
        ?>
                var row_no = '<?= $value->id; ?>';
                var hours_wage_cost = $('#hours_wage_'+row_no).val();
                var actual_hours_no = $('#actual_hours_no_'+row_no).val();

                $('#actual_hours_no_total_'+row_no).val(formatDecimal((parseFloat(hours_wage_cost)*parseFloat(actual_hours_no)),4));
                var actual_hours_no_total = $('#actual_hours_no_total_'+row_no).val();
                gtotal=parseFloat(gtotal)+parseFloat(actual_hours_no_total);

        <?php
        }
        ?>


    $('#gTotal_labors').val(formatDecimal((parseFloat(gtotal)),4));
    var gTotal_row = $('#gTotal_row').val();
    var gTotal_labors = $('#gTotal_labors').val();
    var gTotal_expenses = $('#gTotal_expenses').val();

    var real_qty='<?= ($man_details->quantity*$bom_details->quantity) ?>';
    var real_gTotal=(parseFloat(gTotal_row)+parseFloat(gTotal_labors)+parseFloat(gTotal_expenses));

    $('#gTotal').val((parseFloat(real_gTotal)));


    $('#nwavg_cost').val((parseFloat(real_gTotal)/parseFloat(real_qty)));
    $('#nwavg_cost_show').val(formatDecimal((parseFloat(real_gTotal)/parseFloat(real_qty)),4));
    $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));

    //alert($('#nwavg_cost').val())
    }

    function actual_cost(row_no){

        var bom_expenses='<?= $bom_expenses ?>';
        var gtotal=0;
        <?php
            foreach ($bom_expenses as $key => $value){
        ?>
                var row_no = '<?= $value->id; ?>';
                var actual_cost = $('#actual_cost_'+row_no).val();

                gtotal=parseFloat(gtotal)+parseFloat(actual_cost);

        <?php
        }
        ?>


    $('#gTotal_expenses').val(formatDecimal((parseFloat(gtotal)),4));
    var gTotal_row = $('#gTotal_row').val();
    var gTotal_labors = $('#gTotal_labors').val();
    var gTotal_expenses = $('#gTotal_expenses').val();

    var real_qty='<?= ($man_details->quantity*$bom_details->quantity) ?>';
    var real_gTotal=(parseFloat(gTotal_row)+parseFloat(gTotal_labors)+parseFloat(gTotal_expenses));

    $('#gTotal').val((parseFloat(real_gTotal)));


    $('#nwavg_cost').val((parseFloat(real_gTotal)/parseFloat(real_qty)));
    $('#nwavg_cost_show').val(formatDecimal((parseFloat(real_gTotal)/parseFloat(real_qty)),4));
    $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));

    //alert($('#nwavg_cost').val())
    }

    $(document).ready(function () {


		$('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));


		$(document).on('click', '#back', function (e) {

				    window.location.href = site.base_url + 'manufacturing/progress';

		});
		$(document).on('click', '#addProduce', function (e) {



			if (!$('#confirm_qty').val()) {
                $('#mError').text('<?= lang('Confirm_the_manufactured_quantity_is_required') ?>');
                $('#mError-con').show();
                return false;
            }

			$.each(items, function () {

			var row_no = this.id;
			var qty = $('#quantity_'+row_no).val();
			items[row_no].qty=qty;
			//alert(items[item_id].qty);
			//alert(qty);
             });

			$.each(items_dest, function () {

			var row_no = this.id;
			var qty = $('#quantity_dest_'+row_no).val();
			items_dest[row_no].qty=qty;
			//alert(items[item_id].qty);
			//alert(qty);
             });
			 var man_id='<?= $man_details->id; ?>';
			 var bom_id='<?= $bom_details->id; ?>';
			 var confirm_qty=$('#confirm_qty').val();
			//var product_code=items[item_id].code;
			//var product_quantity=items[item_id].qty;
			//var product_unit=items[item_id].unit_id;
			//var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();

            var actual_qty_arr2 = {};
            var i=0;
            <?php
            foreach ($bom_detail as $key => $value){
            ?>
                var actual_qty_arr = {};
                var row_no = '<?= $value->id; ?>';
			    var qty = $('#actual_qty_'+row_no).val();
			    actual_qty_arr['actual_qty']=qty;
                actual_qty_arr['product_id']=row_no;
                actual_qty_arr2[i] = actual_qty_arr;
                i++;
                //console.log(qty);

            <?php
            }
            ?>


            var actual_hours_no_arr2 = {};
            var i=0;
            <?php
            foreach ($bom_labors as $key => $value){
            ?>
                var actual_hours_no_arr = {};
                var row_no = '<?= $value->id; ?>';
			    var qty = $('#actual_hours_no_'+row_no).val();
			    actual_hours_no_arr['actual_hours_no']=qty;
                actual_hours_no_arr['id']=row_no;
                actual_hours_no_arr2[i] = actual_hours_no_arr;
                i++;
                //console.log(qty);

            <?php
            }
            ?>


            var actual_cost_arr2 = {};
            var i=0;
            <?php
            foreach ($bom_expenses as $key => $value){
            ?>
                var actual_cost_arr = {};
                var row_no = '<?= $value->id; ?>';
			    var qty = $('#actual_cost_'+row_no).val();
			    actual_cost_arr['actual_cost']=qty;
                actual_cost_arr['id']=row_no;
                actual_cost_arr2[i] = actual_cost_arr;
                i++;
                //console.log(qty);

            <?php
            }
            ?>

            //bom_detail='<?= json_encode($bom_detail); ?>';
            //console.log(actual_qty_arr2);
            //return false;

					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addManufacturingRequestDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, man_id : man_id, items : items, items_dest : items_dest, confirm_qty:confirm_qty,actual_qty_arr2:actual_qty_arr2,actual_hours_no_arr2:actual_hours_no_arr2,actual_cost_arr2:actual_cost_arr2},
                success: function(data, page) {
                    //console.log(data);
                    //return false;
					if(data==true){
					    window.location.href = site.base_url + 'manufacturing/progress';
				}else{
					$('#mError').text('<?= lang('failed_to_save') ?>');
					$('#mError-con').show();
					}

                },
            });




            return false;

        });


		$('#tax_rate').change(function () {

			$("#div_tax_method").css("display", "block");


			try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'products/getTax_Rate/' + $('#tax_rate').val(),
                dataType: 'json',
                data: { id : $('#tax_rate').val()},
                success: function(data, page) {

					if (data>0) {
                        $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
				        $('#tax_method').select2('readonly', false);



                    } else {
                    	$('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
				        $('#tax_method').select2('readonly', true);



                    }

                },
            });

		}catch(err){alert(err);}

        });

        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        var items_dest = {};
        <?php
        if ($req_items) {
            foreach ($req_items as $item) {
                //echo 'ietms['.$item->id.'] = '.$item.';';
                if ($item->code) {
                    echo 'add_product_item(' . json_encode($item) . ',0);';
                }
            }
        }
        ?>
        <?php
        if ($dest_items) {
            foreach ($dest_items as $item) {
                //echo 'ietms['.$item->id.'] = '.$item.';';
                if ($item->code) {
                    echo 'add_product_item_dest(' . json_encode($item) . ',0);';
                }
            }
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");' : '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });
        //$('#cost').removeAttr('required');
        $('#digital_file').change(function () {
            if ($(this).val()) {
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
        });
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'standard') {
                $('.standard').slideUp();
                $('#cost').attr('disabled', true);
                //$('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideDown();
                //$('#track_quantity').iCheck('check');
                $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('.digital').slideDown();
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
            if (t == 'standard' || t == 'combo') {
                $('.standard_combo').slideDown();
				$('#cost').attr('disabled', false);
			} else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('disabled', true);
            //$('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
           // $('#track_quantity').iCheck('check');
            $('#cost').attr('disabled', false);
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
        } else {
            $('.standard_combo').slideUp();
        }




        $("#add_item").autocomplete({

            source: '<?= admin_url('manufacturing/suggestions?warehouse_id='.$bom_details->wh_raw); ?>' ,
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {

                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {

                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item,1);
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });

       $("#add_item_dest").autocomplete({

            source: '<?= admin_url('manufacturing/suggestions_dist?warehouse_id='.$bom_details->wh_manufacturing.'&man_id='.$man_details->id); ?>' ,
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {

                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {

                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item_dest(ui.item,1);
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });



		function add_product_item(item,flage) {
			flage=0;
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
				var bom_id='<?= $bom_details->id; ?>';
				//updateBomDetail(bom_id,item_id,items[item_id].qty);
                //items[item_id].avg_cost = (parseFloat(items[item_id].avg_cost) * parseFloat(items[item_id].qty)).toFixed(2);
            } else {
                items[item_id] = item;
				if(flage==1){
			var bom_id='<?= $bom_details->id; ?>';
			var product_id=items[item_id].id;
			var product_code=items[item_id].code;
			var product_quantity=items[item_id].qty;
			var product_unit=items[item_id].unit_id;
			var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();



					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_code : product_code, product_quantity : product_quantity, product_unit : product_unit, product_avg_cost : product_avg_cost},
                success: function(data, page) {


                },
            });


				}

			}
            var pp = 0;
            var total = 0;
            $("#rmrTable tbody").empty();
            $.each(items, function () {
				var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" class="rid" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span class="pr_link" id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input name="combo_item_unit[]" type="hidden" value="' + this.unit_id + '"><span id="unit_' + row_no + '">' + this.unit_name + '</span></td>';
                tr_html += '<td><input disabled class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" disabled type="text" value="' + formatDecimal(this.qty,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input disabled class="form-control text-center trprice" name="total_combo_item_price[]" type="text" value="' + formatDecimal(this.qty * this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				//tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#rmrTable");
                pp += formatDecimal(parseFloat(this.avg_cost)*parseFloat(this.qty),4);
            });
            $('.item_' + item_id).addClass('warning');
			var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="4"><?= lang('Total_cost_of_materials_converted_to_manufacture') ?></th><th class="text-center total"><input disabled class="form-control text-center" name="total" id="total" type="text" value="' + formatDecimal(pp,4) + '"></th><tr>';
				$("#rmrTable tfoot").html(tfoot);


            $('#total').val(formatDecimal(pp,4));
            $('#wavg_cost').val(formatDecimal(pp/<?= ($man_details->quantity*$bom_details->quantity) ?>,4));
			$('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));

            return true;
        }

		function add_product_item_dest(item,flage) {
			flage=0;
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items_dest[item_id]) {
                items_dest[item_id].qty = (parseFloat(items_dest[item_id].qty) + 1).toFixed(2);
				var bom_id='<?= $bom_details->id; ?>';
				//updateBomDetail(bom_id,item_id,items_dest[item_id].qty);
                //items_dest[item_id].avg_cost = (parseFloat(items_dest[item_id].avg_cost) * parseFloat(items_dest[item_id].qty)).toFixed(2);
            } else {
                items_dest[item_id] = item;
				if(flage==1){
			var bom_id='<?= $bom_details->id; ?>';
			var product_id=items_dest[item_id].id;
			var product_code=items_dest[item_id].code;
			var product_quantity=items_dest[item_id].qty;
			var product_unit=items_dest[item_id].unit_id;
			var product_avg_cost=items_dest[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();



					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_code : product_code, product_quantity : product_quantity, product_unit : product_unit, product_avg_cost : product_avg_cost},
                success: function(data, page) {


                },
            });


				}

			}
            var pp = 0;
            var total = 0;
            $("#dmrTable tbody").empty();
            $.each(items_dest, function () {
				var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" class="rid" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span class="pr_link" id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input name="combo_item_unit[]" type="hidden" value="' + this.unit_id + '"><span id="unit_' + row_no + '">' + this.unit_name + '</span></td>';
                tr_html += '<td><input disabled class="form-control text-center rprice_dest" name="combo_item_price[]" type="text" value="' + formatDecimal(this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td><input class="form-control text-center rquantity_dest" name="combo_item_quantity[]" disabled type="text" value="' + formatDecimal(this.qty,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_dest_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input disabled class="form-control text-center trprice_dest" name="total_combo_item_price[]" type="text" value="' + formatDecimal(this.qty * this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				//tr_html += '<td class="text-center"><i class="fa fa-times tip del_dest" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#dmrTable");
                pp += formatDecimal(parseFloat(this.avg_cost)*parseFloat(this.qty),4);
            });
            $('.item_' + item_id).addClass('warning');
			var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="4"><?= lang('Total_cost_for_destroyed_materials') ?></th><th class="text-center total_dest"><input disabled class="form-control text-center" name="total_dest" id="total_dest" type="text" value="' + formatDecimal(pp,4) + '"></th><tr>';
				$("#dmrTable tfoot").html(tfoot);


            $('#total_dest').val(formatDecimal(pp,4));
            $('#wavg_cost_dest').val(formatDecimal(pp/<?= ($man_details->quantity*$bom_details->quantity) ?>,4));
            $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));
			return true;
        }



        function updateBomDetail(bom_id,product_id,product_quantity){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/updateBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_quantity : product_quantity},
                success: function(data, page) {


                },
            });
		}
        function deleteBomDetail(bom_id,product_id){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/deleteBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id},
                success: function(data, page) {


                },
            });
		}
        function calculate_price_confirm() {
			var total=parseFloat($('#gTotal').val());
            //$('#gTotal').val((parseFloat(real_gTotal)));
            //alert(total)
			var mv_total_req=0,mv_total_dest=0;
			try{
				if($('#total').val()){
				mv_total_req=$('#total').val();
			}
			}catch(err){}
			if($('#total_dest').val())
			mv_total_dest=$('#total_dest').val();
			//alert(parseFloat($('#confirm_qty').val()))
			$('#fwavg_cost').val(formatDecimal((parseFloat(total)+parseFloat(mv_total_req)-parseFloat(mv_total_dest))/parseFloat($('#confirm_qty').val()),4));
		return true;
		}
        function calculate_price() {
            var rows = $('#rmrTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
            });
                        $('#total').val(formatDecimal(pp,4));
						$('#wavg_cost').val(formatDecimal(pp/<?= ($man_details->quantity*$bom_details->quantity) ?>,4));
            $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));
			calculate_price_confirm();
            return true;
        }

        function calculate_price_dest() {
            var rows = $('#dmrTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice_dest').val())*parseFloat($(this).find('.rquantity_dest').val()),4);
            });
                        $('#total_dest').val(formatDecimal(pp,4));
						$('#wavg_cost_dest').val(formatDecimal(pp/<?= ($man_details->quantity*$bom_details->quantity) ?>,4));
            $('#fwavg_cost').val(formatDecimal((parseFloat($('#nwavg_cost').val())+parseFloat($('#wavg_cost').val())-parseFloat($('#wavg_cost_dest').val())),4));

            return true;
        }



        $(document).on('change', '.rquantity, .rprice', function () {
			var row = $(this).closest('tr');
			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');

			row.find('.trprice').val(formatDecimal(parseFloat(row.find('.rprice').val())*parseFloat(row.find('.rquantity').val()),4));
			var bom_id='<?= $bom_details->id; ?>';
			items[item_id].qty=formatDecimal(row.find('.rquantity').val(),4);
				//updateBomDetail(bom_id,item_id,row.find('.rquantity').val());

            calculate_price();
        });

        $(document).on('change', '.rconfirm_qty', function () {
			calculate_price_confirm();
	});

        $(document).on('change', '.rquantity_dest, .rprice_dest', function () {
			var row = $(this).closest('tr');
			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');

			row.find('.trprice_dest').val(formatDecimal(parseFloat(row.find('.rprice_dest').val())*parseFloat(row.find('.rquantity_dest').val()),4));
			var bom_id='<?= $bom_details->id; ?>';
			items_dest[item_id].qty=formatDecimal(row.find('.rquantity_dest').val(),4);
				//updateBomDetail(bom_id,item_id,row.find('.rquantity').val());

            calculate_price_dest();
        });

        $(document).on('click', '.del', function () {


			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
			var bom_id='<?= $bom_details->id; ?>';
				//deleteBomDetail(bom_id,item_id);

            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();




            calculate_price();
        });

		$(document).on('click', '.del_dest', function () {


			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
			var bom_id='<?= $bom_details->id; ?>';
				//deleteBomDetail(bom_id,item_id);

            var id = $(this).attr('id');
            delete items_dest[id];
            $(this).closest('#row_' + id).remove();




            calculate_price_dest();
        });

        var su = 2;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang('select') . ' ' . lang('supplier') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);
                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?=json_encode($vars);?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#rmr', function (e) {
            $('#req_mr').slideDown();
        });
        $(document).on('ifUnchecked', '#rmr', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#req_mr').slideUp();
        });

		$(document).on('ifChecked', '#dmr', function (e) {
            $('#dest_mr').slideDown();
        });
        $(document).on('ifUnchecked', '#dmr', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#dest_mr').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            for (var i in attrs) {
                if (attrs[i] !== '') {
                    <?php if (!empty($warehouses)) {
            foreach ($warehouses as $warehouse) {
                echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
                // echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span>0</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
            }
        } else {
            ?>
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                        // $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php
        } ?>
                }
            }
        });
//$('#attributesInput').on('select2-blur', function(){
//    $('#addAttributes').click();
//});
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
            // $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        // $('#aModal').on('shown.bs.modal', function () {
        //     $('#aquantity').focus();
        //     $(this).keypress(function( e ) {
        //         if ( e.which == 13 ) {
        //             $('#updateAttr').click();
        //         }
        //     });
        // });
        $(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            // row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            // row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            $('#aModal').modal('hide');
        });

		$('#pwarehouse').val('<?= $warehouses[0]->id ?>').trigger('change.select2');

			$("#pwarehouse > option").each(function(){

				$("#rmrTable"+ this.value).hide();
                $("#title"+ this.value).hide();


			});

			calculate_price_confirm();



    });

    <?php if ($product) {
            ?>
    $(document).ready(function () {

        var t = "<?=$product->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            //$('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            //$('#track_quantity').iCheck('check');
            $('#cost').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
            //$('#add_item').removeAttr('required');
            //$('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
        } else {
            $('.combo').slideDown();
            //$('#add_item').attr('required', 'required');
            //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'add_item');
        }
        $("#code").parent('.form-group').addClass("has-error");
        $("#code").focus();
        $("#product_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $product->category_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: scdata
                    });
                } else {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });
        <?php if ($product->supplier1) {
                ?>
        select_supplier('supplier1', "<?= $product->supplier1; ?>");
        $('#supplier_price').val("<?= $product->supplier1price == 0 ? '' : $this->sma->formatDecimal($product->supplier1price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier2) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_2', "<?= $product->supplier2; ?>");
        $('#supplier_2_price').val("<?= $product->supplier2price == 0 ? '' : $this->sma->formatDecimal($product->supplier2price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier3) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_3', "<?= $product->supplier3; ?>");
        $('#supplier_3_price').val("<?= $product->supplier3price == 0 ? '' : $this->sma->formatDecimal($product->supplier3price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier4) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_4', "<?= $product->supplier4; ?>");
        $('#supplier_4_price').val("<?= $product->supplier4price == 0 ? '' : $this->sma->formatDecimal($product->supplier4price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier5) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_5', "<?= $product->supplier5; ?>");
        $('#supplier_5_price').val("<?= $product->supplier5price == 0 ? '' : $this->sma->formatDecimal($product->supplier5price); ?>");
        <?php
            } ?>
        function select_supplier(id, v) {
            $('#' + id).val(v).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: "<?= admin_url('suppliers/getSupplier') ?>/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "suppliers/suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });//.select2("val", "<?= $product->supplier1; ?>");
        }

        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#r' + $(this).attr('id')).text());
        });
    });
    <?php
        } ?>
    $(document).ready(function() {

        $('#unit').change(function(e) {

            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        });


			$("#div_tax_method").css("display", "block");


    });


</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('warehouse') ?></label>
                        <div class="col-sm-8">
                            <?php
                            $wh[''] = '';
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, '', 'id="awarehouse" class="form-control"');
                            ?>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="aquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aquantity">
                        </div>
                    </div> -->
                    <input type="hidden" id="aquantity" value="0">
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price_addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aprice">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary spark-btn-primary-action" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
