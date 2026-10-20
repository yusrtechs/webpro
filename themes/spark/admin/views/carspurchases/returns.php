<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--<script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>-->

<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>



<script>
    $(document).ready(function () {
		function get_lang(x) {
			var y=x;
			if(x=="purchase_returned")
				y="<?= lang('purchase_returned') ?>";
			if(x=="purchase_partial")
				y="<?= lang('purchase_partial') ?>";
			if(x=="purchase_receive")
				y="<?= lang('purchase_receive') ?>";
			if(x=="Revaluation")
				y="<?= lang('Revaluation') ?>";
			if(x=="Adjustment")
				y="<?= lang('Adjustment') ?>";
			if(x=="Destruction")
				y="<?= lang('Destruction') ?>";
			if(x=="Transfer")
				y="<?= lang('Transfer') ?>";
			if(x=="Automated_Accounting_Entry")
				y="<?= lang('Automated_Accounting_Entry') ?>";
			if(x=="Adjusting")
				y="<?= lang('Adjusting') ?>";

            return "<div>"+y+"</div>"; // onclick=\"this.select();\"
        }





        oTable = $('#UnitTable').DataTable({
            "aaSorting": [[3, "asc"]],
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
                nRow.className = "purchase_link";
                return nRow;
            },
        });





    });
</script>

<?= admin_form_open('system_settings/unit_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('return_purchase'); ?></h2>

    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">


					<table id="UnitTable"  class="table table-striped jambo_table bulk_action reports-table">
						<thead>
                            <tr>
                                <th><?= lang('Invoice_No'); ?></th>
                                <th><?= lang('warehouse'); ?></th>
                                <th><?= lang('product_code'); ?></th>
                                <th><?= lang('product_name'); ?></th>
                                <th><?= lang('unit'); ?></th>
                                <th><?= lang('quantity'); ?></th>
                                <th><?= lang('actions'); ?></th>
                            </tr>
                        </thead>


						<tfoot>
							<tr>
                                <th><?= lang('Invoice_No'); ?></th>
                                <th><?= lang('warehouse'); ?></th>
                                <th><?= lang('product_code'); ?></th>
                                <th><?= lang('product_name'); ?></th>
                                <th><?= lang('unit'); ?></th>
                                <th><?= lang('quantity'); ?></th>
                                <th><?= lang('actions'); ?></th>
                            </tr>
                        </tfoot>

        <tbody>



		<?php
						foreach ($purchase_items as $purchase_item){
							$purchase = $this->purchases_model->getPurchaseByID($purchase_item->purchase_id);
							/*if ($purchase->return_id) {
							}else*/{

							?>

			<tr>
                <td><?= $purchase_item->purchase_id; ?></td>
                <td><?= $purchase_item->warehouse_name; ?></td>
                <td><?= $purchase_item->product_code; ?></td>
				<td><?= $purchase_item->product_name; ?></td>




                <td>
				<?php

				            	echo $this->site->getUnitByID($this->purchases_model->getProductByCode($purchase_item->product_code)->unit)->name;

				?>
				</td>
				<td><?= $this->sma->formatQuantity($purchase_item->quantity_received); ?></td>


			<td>
			<div class="text-center"><a href="<?= admin_url('carspurchases/return_purchase/'.$purchase_item->purchase_id.'/'.$purchase_item->product_code) ?>"  title="<?= lang('return_purchase') ?>"><i class="fa fa-mail-reply"></i></a></div>
			</td>

			</tr>

			<?php
							}

						}
						?>

        </tbody>
    </table>

                </div>
            </div>
        </div>
    </div>
</div>



<!-- Data Tables Plugins

<script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>


<script src="<?= $assets ?>account/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $assets ?>account/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="<?= $assets ?>account/js/demo/datatables-demo.js"></script>
-->



<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>


