<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
   <script type="text/javascript" src="<?= $assets ?>js/core.js"></script>

<div class="spark-manufacturing-page">
    <div class="spark-manufacturing-header">
        <div class="spark-manufacturing-header-start">
            <div class="spark-manufacturing-header-icon">
                <i class="fa fa-cogs"></i>
            </div>
            <div class="spark-manufacturing-header-info">
                <h1 class="spark-manufacturing-title">
                    <?= lang('Manufacturing_list'); ?>
                    <?php
                    if ($this->input->post('start_date')) {
                        echo ' ' . (lang('from') ? lang('from') : 'From') . ' ' . $this->input->post('start_date') . ' ' . (lang('to') ? lang('to') : 'to') . ' ' . $this->input->post('end_date');
                    }
                    ?>
                </h1>
                <p class="spark-manufacturing-subtitle">
                    <?= lang('manage_manufacturing_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-manufacturing-header-actions">
            <a href="<?= admin_url('manufacturing/add_manufacturing') ?>" data-toggle="modal" data-target="#myModal" id="add" class="spark-btn-primary-action">
                <i class="fa fa-plus-circle"></i>
                <span><?= lang('Add_a_manufacturing_product'); ?></span>
            </a>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('export_to_excel') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel'); ?></span>
            </button>
        </div>
    </div>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">

				<!-- Dark table start -->
                    <table id="manufacturingTable"  class="table table-striped jambo_table bulk_action reports-table">
										<thead class="text-capitalize">
                                            <tr>
                                                <th><?= lang('Sr#') ?></th>
                                                <th><?= lang('date') ?></th>
												<th><?= lang('Bill_Of_Material'); ?></th>
                                                <th><?= lang('Bill_quantity'); ?></th>
                                                <th><?= lang('Deadline_Start'); ?></th>
                                                <th><?= lang('created_by') ?></th>
												<th><?= lang('description') ?></th>
												<th><?= lang('Status') ?></th>
											</tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; foreach ($manufacturing as $key => $value) : ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $this->sma->hrld($value->created_date); ?></td>
                                                <td><?php echo $value->name; ?></td>
                                                <td><?php echo $value->quantity; ?></td>
                                                <td><?php echo $this->sma->hrsd($value->deadline_start); ?></td>
                                                <td><?php echo $value->created_by; ?></td>
                                                <td><?php echo $value->description; ?></td>
                                                <td><span class="label  <?php if($value->status == "produce") { echo "label-primary"; } else if($value->status == "done") { echo "label-success"; } else { echo "label-warning"; } ?> "><?php echo lang($value->status); ?></span></td>
                                            </tr>
                                            <?php $i++; endforeach; ?>
                                    </table>

				</div>











        </div>
    </div>
</div>
</div>
</div>



<script>
$(document).ready(function () {


    $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('manufacturing/index/0/xls')?>";
            return false;
        });

	        var rownumber = 0;
		var tableajax = $('#manufacturingTable').DataTable({
          responsive: true,
		  "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,

			columnDefs: [{
    "defaultContent": "-",
    "targets": "_all"
  }],




            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				//alert(aData[0])
				//$("td:eq(4)", nRow).text(get_account(aData[4]));
				//var ent=aData[4];



                //$("td:eq(4)", nRow).text(ent);
                nRow.id = aData[0];
                //nRow.className = "entries_link";
                return nRow;
            },
      });


$('#submit_report').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('products/index') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#labelProducts').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');

        });
$('#sync_quantity').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');

        });



	});

</script>


