<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
/*
.qrimg{
	padding-bottom:20px;
}
*/

.qrimg{
	padding-bottom:20px;
}

    table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;}

 </style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header" style="min-height:50px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="fa fa-2x">&times;</i>
        </button>
        <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
            <i class="fa fa-print"></i> <?= lang('print'); ?>
        </button>
         <button type="button "
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                <i  class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                 </a>

            </button>
        <h4 class="modal-title" id="myModalLabel"><?= $product->name . (SHOP && $product->hide != 1 ? ' (' . lang('shop_views') . ': ' . $product->views . ')' : ''); ?></h4>
    </div>
        <div class="modal-body">
        <div id="pr_pdf">
            <div class="row">
                <div class="col-xs-5">
                    <img id="pr-image" src="<?= base_url() ?>assets/uploads/<?= $vehicle->image ?>"
                    alt="<?= $product->name ?>" class="img-responsive img-thumbnail"/>

                    <div id="multiimages" class="padding10">
                        <?php if (!empty($images)) {
    echo '<a class="img-thumbnail change_img" href="' . base_url() . 'assets/uploads/' . $vehicle->image . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $vehicle->image . '" alt="' . $vehicle->image . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
    foreach ($images as $ph) {
        echo '<div class="gallery-image"><a class="img-thumbnail change_img" href="' . base_url() . 'assets/uploads/' . $ph->photo . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $ph->photo . '" alt="' . $ph->photo . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
        if ($Owner || $Admin || $GP['products-edit']) {
            echo '<a href="#" class="delimg" data-item-id="' . $ph->id . '"><i class="fa fa-times"></i></a>';
        }
        echo '</div>';
    }
}
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-xs-7">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped dfTable table-right-left">
                            <tbody>
                                        <tr>
                                            <td colspan="2" style="background-color:#FFF;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:30%;"><?= lang('barcode_qrcode'); ?></td>
                                            <td style="width:70%;">
                                            <img style="padding-bottom:20px;" src="<?= admin_url('misc/barcode/' . $vehicle->chesis_no . '/' . $vehicle->barcode_symbology . '/74/0'); ?>" alt="<?= $vehicle->chesis_no; ?>" class="bcimg" />
                                                <?= $this->sma->qrcode('link', urlencode(admin_url('cars/vehicle_details/' . $vehicle->vehicle_id)), 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Showroom'); ?></td>
                                            <td><?php echo $this->cars_model->getShowroomByID($vehicle->showroom_id)->name ;?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Chesis_no'); ?></td>
                                            <td><?= $vehicle->chesis_no ;?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Panel_no'); ?></td>
                                            <td><?= $vehicle->panel_no ;?></td>
                                        </tr>
                                        <tr>
                                            <td ><?= lang('Car_Type'); ?></td>
                                            <td><?= $vehicle->name;?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Model_or_Registration_Year'); ?></td>
                                            <td><?= $vehicle->registration_year; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Car_status'); ?></td>
                                            <td><?= lang($vehicle->car_status);?></td>
                                        </tr>

                                        <?php if ($vehicle->available_place) {
										        ?>
                                         <tr>
                                            <td><?= lang('Car_Place'); ?></td>
                                            <td><?= $vehicle->available_place;?></td>
                                          </tr>
                                         <?php } ?>


                                        <tr>
                                            <td><?= lang('Car_Owner'); ?></td>
                                            <td><?=  $carowner->company ? $carowner->company : $carowner->name ;?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('selling_price'); ?></td>
                                            <td><?php echo $this->sma->formatMoney($vehicle->buying_price)	; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('cost'); ?></td>
                                            <td><?php echo $this->sma->formatMoney($vehicle->cost)	; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Color'); ?></td>
                                            <td><?= $vehicle->color; ?></td>
                                        </tr>

                                        <?php if ($vehicle->car_status=='old'){ ?>
                                        <tr>
                                            <td><?= lang('Mileage'); ?></td>
                                            <td><?= $vehicle->mileage; ?></td>
                                        </tr>
                                        <?php } ?>

                                       <tr>
                                            <td><?= lang('Added_on'); ?></td>

                                            <td><?php echo $this->sma->hrsd($vehicle->date); ?></td>
                                        </tr>

                                         <tr>
                                            <td><?= lang('created_by'); ?></td>
                                            <td><?= $created_by->first_name . ' ' . $created_by->last_name.' ( '. $this->sma->hrld($vehicle->created_date).' ) '; ?></td>
                                        </tr>
                                        <?php if ($vehicle->updated_by) {
										?>

                                        <tr>
                                            <td><?= lang('updated_by'); ?></td>
                                            <td><?= $updated_by->first_name . ' ' . $updated_by->last_name.' ( '. $this->sma->hrld($vehicle->updated_date).' ) '; ?></td>
                                        </tr>
                                        <?php
										} ?>
                                          <tr>
                                            <td><?= lang('Status'); ?></td>
                                            <td><span class="label  <?php if($vehicle->status == "available"){ echo "label-success"; } else { echo "label-warning"; } ?> "><?php echo lang($vehicle->status); ?></span></td>

                                        </tr>

                                    <?php if ($product->tax_rate) {
                                        ?>
                                    <tr>
                                        <td><?= lang('tax_rate'); ?></td>
                                        <td><?= $tax_rate->name; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tax_method'); ?></td>
                                        <td><?= $product->tax_method == 0 ? lang('inclusive') : lang('exclusive'); ?></td>
                                    </tr>
                                    <?php
                                    } ?>
                                    <?php if ($product->alert_quantity != 0) {
                                        ?>
                                    <tr>
                                        <td><?= lang('alert_quantity'); ?></td>
                                        <td><?= $this->sma->formatQuantity($product->alert_quantity); ?></td>
                                    </tr>
                                    <?php
                                    } ?>
                                    <?php if ($variants) {
                                        ?>
                                    <tr>
                                        <td><?= lang('product_variants'); ?></td>
                                        <td><?php foreach ($variants as $variant) {
                                            echo '<span class="label label-primary">' . $variant->name . '</span> ';
                                        } ?></td>
                                    </tr>
                                    <?php
                                    } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-5">

								<?php if (!empty($product_price)) {
                                            ?>
                                            <h3 class="bold"><?= lang('price_groups'); ?></h3>
                                            <div class="table-responsive">
                                                <table
                                                    class="table  table-striped table-condensed dfTable">
                                                    <thead>
                                                    <tr>
                                                        <th><?= lang('name') ?></th>
                                                        <th><?= lang('unit'); ?></th>
                                                        <th><?= lang('price'); ?></th>
                                                        <th><?= lang('barcode'); ?></th>


                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    foreach ($product_price as $pr) {

                                                            echo '<tr><td>' . $pr->price_groups_name . '</td><td>' . $pr->unit_name . '</td><td class="text-center">' . $this->sma->formatDecimal($pr->product_price) . '</td><td class="text-center">' . $pr->product_code . '</td>';

                                                            echo '</tr>';

                                                    } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php
                                        } ?>


                                <?php if ($product->cf1 || $product->cf2 || $product->cf3 || $product->cf4 || $product->cf5 || $product->cf6) {
                                        ?>
                                <h3 class="bold"><?= lang('custom_fields') ?></h3>
                                <div class="table-responsive">
                                    <table
                                    class="table  table-striped table-condensed dfTable two-columns">
                                    <thead>
                                        <tr>
                                            <th><?= lang('custom_field') ?></th>
                                            <th><?= lang('value') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($product->cf1) {
                                            echo '<tr><td>' . lang('pcf1') . '</td><td>' . $product->cf1 . '</td></tr>';
                                        }
                                        if ($product->cf2) {
                                            echo '<tr><td>' . lang('pcf2') . '</td><td>' . $product->cf2 . '</td></tr>';
                                        }
                                        if ($product->cf3) {
                                            echo '<tr><td>' . lang('pcf3') . '</td><td>' . $product->cf3 . '</td></tr>';
                                        }
                                        if ($product->cf4) {
                                            echo '<tr><td>' . lang('pcf4') . '</td><td>' . $product->cf4 . '</td></tr>';
                                        }
                                        if ($product->cf5) {
                                            echo '<tr><td>' . lang('pcf5') . '</td><td>' . $product->cf5 . '</td></tr>';
                                        }
                                        if ($product->cf6) {
                                            echo '<tr><td>' . lang('pcf6') . '</td><td>' . $product->cf6 . '</td></tr>';
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                                    } ?>

                            <?php
                            $product->type = 'standard';

                            if ((!$Supplier || !$Customer) && !empty($warehouses) && ($product->type == 'standard' || $product->type == 'manufacturing' || $product->type == 'combo'))
                            {
                                        ?>
                            <!-- <h3 class="bold"><?= lang('warehouse_quantity') ?></h3>-->
                            <div class="table-responsive">
                                <table
                                class="table  table-striped table-condensed dfTable one-columns">
                                <thead>
                                    <tr>
                                        <th><?= lang('Showroom') ?></th>
                                        <?php if($product->type != 'combo'){ ?><th><?= lang('quantity'); ?></th><?php } ?>
										<th><?= lang('avg_cost')  ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($warehouses as $warehouse) {
														if($product->type != 'combo'){

                                                if ($warehouse->quantity != 0) {
													if($warehouse->quantity >0)
														$avg_cost=$this->sma->formatDecimal($warehouse->avg_cost,4) ;
													else
														$avg_cost=0;

														echo '<tr><td>' . $warehouse->name . ' (' . $warehouse->code . ')</td><td><strong>' . $this->sma->formatQuantity($warehouse->quantity) . '</strong>' . ($warehouse->rack ? ' (' . $warehouse->rack . ')' : '') . '</td><td>'. $avg_cost .'</td></tr>';
													}
														}else{
														$avg_cost=0;
														foreach ($combo_items_avg_cost as $combo_item_avg_cost){
															if($combo_item_avg_cost->warehouse_id == $warehouse->id)
																$avg_cost=$this->sma->formatDecimal($combo_item_avg_cost->totalcost,4);


														}
														echo '<tr><td>' . $warehouse->name . ' (' . $warehouse->code . ')</td><td>'.$avg_cost.'</td></tr>';
														}
                                            } ?>
                                                </tbody>
                            </table>
                        </div>
                        <?php
                                    } ?>
                    </div>
                    <div class="col-xs-7">
                        <?php if ($product->type == 'combo') {
                                        ?>
                        <h3 class="bold"><?= lang('combo_products') ?></h3>
                        <div class="table-responsive">
                        <table
                                                    class="table  table-striped table-condensed dfTable one-columns">
                                                    <thead>
                                                    <tr>
                                                        <th class="col-sm-3"><?= lang('product_name') ?></th>
                                                        <th class="col-sm-2"><?= lang('quantity') ?></th>

                                                    </tr>
                                                    </thead>
                            <tbody>
                                <?php foreach ($combo_items as $combo_item) {
                                            echo '<tr><td>' . $combo_item->name . ' (' . $combo_item->code . ') </td><td>' . $this->sma->formatQuantity($combo_item->qty) . '</td></tr>';
                                        } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                                    } ?>
                    <?php if (!empty($options)) {
                                        ?>
                    <h3 class="bold"><?= lang('product_variants_quantity'); ?></h3>
                    <div class="table-responsive">
                        <table
                        class="table  table-striped table-condensed dfTable">
                        <thead>
                            <tr>
                                <th><?= lang('warehouse_name') ?></th>
                                <th><?= lang('product_variant'); ?></th>
                                <th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th>
                                <?php if ($Owner || $Admin) {
                                            echo '<th>' . lang('price_addition') . '</th>';
                                        } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($options as $option) {
                                if ($option->wh_qty != 0) {
                                    echo '<tr><td>' . $option->wh_name . '</td><td>' . $option->name . '</td><td class="text-center">' . $this->sma->formatQuantity($option->wh_qty) . '</td>';
                                    if ($Owner || $Admin && (!$Customer || $this->session->userdata('show_cost'))) {
                                        echo '<td class="text-right">' . $this->sma->formatMoney($option->price) . '</td>';
                                    }
                                    echo '</tr>';
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                                    } ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12">

        <?= $product->details ? '<div class="panel panel-success"><div class="panel-heading">' . lang('product_details_for_invoice') . '</div><div class="panel-body">' . $product->details . '</div></div>' : ''; ?>
        <?= $product->product_details ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('product_details') . '</div><div class="panel-body">' . $product->product_details . '</div></div>' : ''; ?>

    </div>
    </div><!--  end"pr_pdf"-->
</div>
<?php if (!$Supplier || !$Customer) {
                                        ?>
    <div class="buttons">
        <div class="btn-group btn-group-justified">
            <div class="btn-group" style="display:none;>
                <a href="<?= admin_url('products/print_barcodes/' . $product->id) ?>" class="tip btn btn-primary" title="<?= lang('print_barcode_label') ?>">
                    <i class="fa fa-print"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('print_barcode_label') ?></span>
                </a>
            </div>
            <div class="btn-group" style="display:none;">
                <a href="<?= admin_url('products/pdf/' . $product->id) ?>" class="tip btn btn-primary" title="<?= lang('pdf') ?>">
                    <i class="fa fa-download"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="<?= admin_url('cars/edit_vehicle/' . $vehicle->vehicle_id) ?>" class="tip btn btn-warning tip" title="<?= lang('Edit_Vehicle') ?>">
                    <i class="fa fa-edit"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="#" class="tip btn btn-danger bpo" title="<b><?= lang('Delete_Vehicle') ?></b>"
                    data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('cars/delete_vehicle/' . $vehicle->vehicle_id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                    data-html="true" data-placement="top">
                    <i class="fa fa-trash-o"></i>
                    <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                </a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function () {
        $('.tip').tooltip();

    });

    </script>
<?php
                                    } ?>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.change_img').click(function(event) {
        event.preventDefault();
        var img_src = $(this).attr('href');
        $('#pr-image').attr('src', img_src);
        return false;
    });
    });
    $('#pdf').click(function (event) {
                event.preventDefault();
            PrPDF('pr_pdf','<?php echo $this->cars_model->getBrandByID($vehicle->brand_id)->brand_name. ' - ' .$this->cars_model->getModelByID($vehicle->model_id)->model_name. ' - '  .$this->cars_model->getCategoryByID($vehicle->category_id)->categ_name; ?>');
		        return false;
            });

</script>
