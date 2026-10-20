<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <div class="modal-header">
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('stock_count'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="row"  style="overflow:auto !important;">
                <div style="width:100%; height:400px; overflow:auto;" class="col-xs-12">
                <table style="overflow:auto !important;" class="table  table-condensed">
                        <tbody>
                            <tr>
                                <td><?= lang('warehouse'); ?></td>
                                <td><?= $warehouse->name . ' ( ' . $warehouse->code . ' )'; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('start_date'); ?></td>
                                <td><?= $this->sma->hrld($stock_count->date); ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('end_date'); ?></td>
                                <td><?= $this->sma->hrld($stock_count->updated_at); ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('reference'); ?></td>
                                <td><?= $stock_count->reference_no; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('type'); ?></td>
                                <td><?= lang($stock_count->type); ?></td>
                            </tr>
                            <?php if ($stock_count->type == 'partial') { ?>
                                <tr>
                                    <td><?= lang('categories'); ?></td>
                                    <td><?= $stock_count->category_names; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('brands'); ?></td>
                                    <td><?= $stock_count->brand_names; ?></td>
                                </tr>
                            <?php
							} ?>
                            <tr>
                                <td><?= lang('files'); ?></td>
                                <td>
                                    <?= anchor('admin/welcome/download/' . $stock_count->initial_file, '<i class="fa fa-download"></i> ' . lang('initial_file'), 'class="btn btn-primary btn-xs"'); ?>
                                    <?= anchor('admin/welcome/download/' . $stock_count->final_file, '<i class="fa fa-download"></i> ' . lang('final_file'), 'class="btn btn-primary btn-xs"'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if (!empty($stock_count_items)) {
        ?>
                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action order-table">
                            <thead>
                            <tr>
                                <th style="text-align:center; vertical-align:middle;"><?= lang('Sr#'); ?></th>
                                <th style="vertical-align:middle;"><?= lang('product_name') . ' (' . lang('product_code') . ')'; ?></th>
                                <th style="text-align:center; vertical-align:middle;"><?= lang('quantity'); ?></th>
                                <th style="text-align:center; vertical-align:middle;"><?= lang('system_quantity'); ?></th>
                                <th style="text-align:center; vertical-align:middle;"><?= lang('difference'); ?></th>
                                <th style="text-align:center; vertical-align:middle;"><?= lang('unit'); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $r = 1;
        $total                       = 0;
        $items                       = 0;
        foreach ($stock_count_items as $row): ?>
                                <tr>
                                    <td style="text-align:center; width:25px;"><?= $r; ?></td>
                                    <td style="text-align:left;">
                                        <?= $row->product_code . ' - ' . $row->product_name . ($row->product_variant ? ' (' . $row->product_variant . ')' : ''); ?>
                                    </td>
                                    <td style="text-align:center; width:100px;">
                                        <?= $this->sma->formatQuantity($row->counted); ?>
                                    </td>
                                    <td style="text-align:center; width:100px;">
                                        <?= $this->sma->formatQuantity($row->expected); ?>
                                    </td>
                                    <td style="text-align:right; width:100px;">
                                        <?= $this->sma->formatQuantity($row->counted - $row->expected); ?>
                                    </td>
                                    <td style="text-align:right; width:120px;">
                                        <?= $row->unit_name; ?>
                                    </td>
                                <!--<td style="text-align:right; width:120px;">
                                        <?= $this->sma->formatMoney($row->cost * ($row->counted - $row->expected)); ?>
                                    </td>-->
                                </tr>
                                <?php $r++;
        $items += $row->counted - $row->expected;
        $total += $row->cost * ($row->counted - $row->expected);
        endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4"><?= lang('total'); ?></th>
                                    <th style="text-align:right; width:80px;">
                                    <?= $this->sma->formatQuantity($items); ?>
                                    </th>
                                    <!--<th style="text-align:right; width:100px;">
                                        <?= $this->sma->formatMoney($total); ?>
                                    </th>-->
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php
    } else {
        echo '<div class="well well-sm">' . lang('no_mismatch_found') . '</div>';
    }
                    ?>
                    <?php
                    if ($adjustment) {
                        echo '<a href="' . admin_url('products/view_adjustment_count/' . $adjustment->id) . '" class="btn btn-primary btn-block no-print" data-toggle="modal" data-target="#myModal2">' . lang('view_adjustment') . '</a>';
                    } elseif ($adjustment1) {
                        echo '<a href="' . admin_url('products/view_adjustment_store/' . $adjustment1->id) . '" class="btn btn-primary btn-block no-print" data-toggle="modal" data-target="#myModal2">' . lang('view_adjustment_store') . '</a>';
                    } elseif (!empty($stock_count_items)) {
                        echo '<div class="col-md-6"><a href="' . admin_url('products/add_adjustment_count/' . $stock_count->id) . '" class="btn btn-primary btn-block no-print">' . lang('effective_warehouse_only') . '</a></div>';
                        echo '<div class="col-md-6"><a href="' . admin_url('products/add_adjustment_store/' . $stock_count->id) . '" class="btn btn-primary btn-block no-print">' . lang('effective_warehouse_and_store') . '</a></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
