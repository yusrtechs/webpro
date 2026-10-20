<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css" media="screen">
    #PRData td:nth-child(7) {
        text-align: right;
    }
    <?php if ($Owner || $Admin || $this->session->userdata('show_cost')) {
    ?>
    #PRData td:nth-child(9) {
        text-align: right;
    }
    <?php
} if ($Owner || $Admin || $this->session->userdata('show_price')) {
        ?>
    #PRData td:nth-child(8) {
        text-align: right;
    }
    <?php
    } ?>
</style>
<?php
$v = '';

if ($this->input->post('product')) {
    $v .= '&product=' . $this->input->post('product');
}
if ($this->input->post('category')) {
    $v .= '&category=' . $this->input->post('category');
}
if ($this->input->post('product_type')) {
    $v .= '&product_type=' . $this->input->post('product_type');
}
if ($this->input->post('brand')) {
    $v .= '&brand=' . $this->input->post('brand');
}
if ($this->input->post('subcategory')) {
    $v .= '&subcategory=' . $this->input->post('subcategory');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
	$warehouse_id= $this->input->post('warehouse');
}
if ($supplier) {
    $v .= '&supplier=' . $supplier->id;
}

//'sAjaxSource': '<?= admin_url('products/getProducts' . ($warehouse_id ? '/' . $warehouse_id : '') . ($supplier ? '?supplier=' . $supplier->id : ''))

?>
<script>
    var oTable;
    $(document).ready(function () {

	    oTable = $('#PRData').dataTable({
            "aaSorting": [ [0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('manufacturing/getProgress/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				//var oSettings = oTable.fnSettings();
                //aData[2]=(iDisplayIndex+1);
				//alert(iDisplayIndex)
                var oSettings = oTable.fnSettings();
				$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);

                nRow.id = aData[0];
                //nRow.className = "product_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [
                null, {"mRender": fld},null , {"mRender": formatQuantity}, {"mRender": fsd},{"mRender": row_status},null, {"bSortable": false}
            ]
        });

    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) {
    ?>
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: scdata
                    });
                }
            }
        });
        <?php
} ?>
    });
</script>
   <!-- <?php if ($Owner || $GP['bulk_actions']) {
                                        echo admin_form_open('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : ''), 'id="action-form"');
                                    } ?>
                                    -->
<?php echo admin_form_open('products/index', 'id="action-form"'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
            class="fa-fw fa fa-barcode"></i><?= lang('In_progress_products') ; ?>
        </h2>

    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

		        <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="PRData" class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th><?= lang('Sr#') ?></th>
                            <th style="min-width:150px; text-align:center;"><?= lang('date') ?></th>
							<th style="min-width:150px; text-align:center;"><?= lang('Bill_Of_Material'); ?></th>
                            <th><?= lang('Bill_quantity'); ?></th>
                            <th><?= lang('Deadline_Start'); ?></th>
                            <th><?= lang('Status') ?></th>
							<th style="min-width:150px; text-align:center;"><?= lang('created_by') ?></th>
							<th style="min-width:250px; text-align:center;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) {
                                ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php
                            } ?>


<script>
$(document).ready(function () {

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

