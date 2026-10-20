<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
.tableData tr    { page-break-inside:avoid; page-break-after:auto }
.tableData  tfoot    { page-break-inside:avoid; page-break-after:auto }

.car_link{
    cursor: pointer;
}
</style>
<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
  if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}
if ($this->input->post('chesis_no')) {
    $v .= "&chesis_no=" . $this->input->post('chesis_no');
}
if ($this->input->post('panel_no')) {
    $v .= "&panel_no=" . $this->input->post('panel_no');
}
if ($this->input->post('supplier')) {
    $v .= "&supplier=" . $this->input->post('supplier');
}

if ($this->input->post('fuel_type')) {
    $v .= "&fuel_type=" . $this->input->post('fuel_type');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
		$warehouse_id= $this->input->post('warehouse');

}
if ($this->input->post('brand_id')) {
    $v .= "&brand_id=" . $this->input->post('brand_id');
}
if ($this->input->post('model_id')) {
    $v .= "&model_id=" . $this->input->post('model_id');
}
if ($this->input->post('category_id')) {
    $v .= "&category_id=" . $this->input->post('category_id');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('status')) {
    $v .= "&status=" . $this->input->post('status');
}
if ($this->input->post('car_status')) {
    $v .= "&car_status=" . $this->input->post('car_status');
}
if ($this->input->post('payment_status')) {
    $v .= "&payment_status=" . $this->input->post('payment_status');
}
   //'sAjaxSource': '<?=admin_url('carssales/getSales' . ($warehouse_id ? '/' . $warehouse_id : '') . '?v=1' . $v' . ($this->input->get('shop') ? '&shop=' . $this->input->get('shop') : '') . ($this->input->get('attachment') ? '&attachment=' . $this->input->get('attachment') : '') . ($this->input->get('delivery') ? '&delivery=' . $this->input->get('delivery') : '')); ',
    //echo    $v;
?>

<script>
    $(document).ready(function () {



            $('#brand_id').change(function () {
            var v = $(this).val();
            var uopt = $("#model_id");
//            console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('cars/getModelsByBrandId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;

                        $('#model_id').val('').trigger('change.select2');
                        $('#category_id')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                            .val('')
                        ;

                        $('#category_id').val('').trigger('change.select2');

                            $.each(scdata, function () {
                     $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

                      });


                        } else {

                            $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;


                               $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;
                            $('#model_id').val('').trigger('change.select2');
                        $('#category_id')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                            .val('')
                        ;

                        $('#category_id').val('').trigger('change.select2');
                            /*$("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_model_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_model_id') ?>",
                                data: [{id: '', text: '<?= lang('no_model_id') ?>'}]
                            });*/
                        }
                        },
                        error: function () {
                            bootbox.alert('<?= lang('ajax_error') ?>');
                        }
                    });
                } else {
                    $("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                    });
                }
            });


              $('#model_id').change(function () {
                        var v = $(this).val();
                        var b = $("#brand_id").val();
                        var uopt = $("#category_id");
                    //     console.log(uopt);

                        if (v && b) {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?= admin_url('cars/getCategoryByBrandIdModelId') ?>/" + b + "/" +v,
                                dataType: "json",
                                success: function (scdata) {
                                    //console.log(scdata);
                                    if (scdata != null) {
                                        $('#category_id')
                .find('option')
                .remove()
                .end()
                .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                .val('')
            ;

            $('#category_id').val('').trigger('change.select2');

                                        $.each(scdata, function () {
                            $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

                    });


                        } else {

                            $('#category_id')
                .find('option')
                .remove()
                .end()
                .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                .val('')
            ;

            $('#category_id').val('').trigger('change.select2');


                           /* $("#category_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_category_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_category_id') ?>",
                                data: [{id: '', text: '<?= lang('no_category_id') ?>'}]
                            });*/
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#category_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
           });




        function resolveAfter2Seconds() {
			$('.dataTables_length').hide();
			$('.dataTables_filter').hide();
            $('.dataTables_paginate').hide();


			var oSettings = oTable.fnSettings();
        oSettings._iDisplayLength = -1;


   oTable.fnDraw();
  return new Promise(resolve => {
    setTimeout(() => {

      resolve('resolved');
    }, 2000);
  });
}

async function asyncCall() {
  //console.log('calling');
  const result = await resolveAfter2Seconds();
  PrPDF('pr_pdf','<?= lang('Cars_list'); ?>','L');
  $('.dataTables_length').show();
  $('.dataTables_filter').show();
  $('.dataTables_paginate').show();

  //console.log(result);
  // expected output: "resolved"
}


		$('#pdf').click(function (event) {


asyncCall();


		    return false;

        });


        function getLang(x){
           if(x=="No_Sold")
           return lang[x];
           else
           return fld(x);

        }
        oTable = $('#SLData').dataTable({
            "aaSorting": [[2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('cars/getVehicles/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                //$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
                nRow.id = aData[0];
                nRow.setAttribute('data-return-id', aData[12]);
                nRow.className = "car_link ";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
           // "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null, null, null, {"mRender": row_status}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": pay_status}, {"bSortable": false,"mRender": attachment}, {"bVisible": false}, {"bSortable": false}],
           "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"bSortable": false,"mRender": img_cars}, {"mRender": fld}, null,null, null, null, null, {"mRender": row_status} , null, null, {"mRender": getLang}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var  price = 0;
                for (var i = 0; i < aaData.length; i++) {

                    price += parseFloat(aaData[aiDisplay[i]][10]);
                }
                var nCells = nRow.getElementsByTagName('th');

                nCells[10].innerHTML = decimalFormat(parseFloat(price));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 2, filter_default_label: "[<?=lang('Added_on');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Chesis_no');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Showroom');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('Car_Type');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
            {column_number: 8, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
            {column_number: 9, filter_default_label: "[<?=lang('Car_Place');?>]", filter_type: "text", data: []},
            {column_number: 11, filter_default_label: "[<?=lang('Sold_on');?>]", filter_type: "text", data: []},

        ], "footer");

        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
			if (localStorage.getItem('pcash_account_1')) {
                localStorage.removeItem('pcash_account_1');
            }
			if (localStorage.getItem('pcc_account_1')) {
                localStorage.removeItem('pcc_account_1');
            }

			if (localStorage.getItem('pcheque_account_1')) {
                localStorage.removeItem('pcheque_account_1');
            }

			if (localStorage.getItem('ptransfer_account_1')) {
                localStorage.removeItem('ptransfer_account_1');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }
            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }

        <?php if ($this->session->userdata('remove_slls')) {
    ?>
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        if (localStorage.getItem('sldiscount')) {
            localStorage.removeItem('sldiscount');
        }
        if (localStorage.getItem('sltax2')) {
            localStorage.removeItem('sltax2');
        }
        if (localStorage.getItem('slref')) {
            localStorage.removeItem('slref');
        }
        if (localStorage.getItem('slshipping')) {
            localStorage.removeItem('slshipping');
        }
        if (localStorage.getItem('slwarehouse')) {
            localStorage.removeItem('slwarehouse');
        }
        if (localStorage.getItem('slnote')) {
            localStorage.removeItem('slnote');
        }
        if (localStorage.getItem('slinnote')) {
            localStorage.removeItem('slinnote');
        }
        if (localStorage.getItem('slcustomer')) {
            localStorage.removeItem('slcustomer');
        }
        if (localStorage.getItem('slbiller')) {
            localStorage.removeItem('slbiller');
        }
        if (localStorage.getItem('slcurrency')) {
            localStorage.removeItem('slcurrency');
        }
        if (localStorage.getItem('sldate')) {
            localStorage.removeItem('sldate');
        }
        if (localStorage.getItem('slsale_status')) {
            localStorage.removeItem('slsale_status');
        }
        if (localStorage.getItem('slpayment_status')) {
            localStorage.removeItem('slpayment_status');
        }
        if (localStorage.getItem('paid_by')) {
            localStorage.removeItem('paid_by');
        }
        if (localStorage.getItem('amount_1')) {
            localStorage.removeItem('amount_1');
        }
        if (localStorage.getItem('paid_by_1')) {
            localStorage.removeItem('paid_by_1');
        }
        if (localStorage.getItem('pcc_holder_1')) {
            localStorage.removeItem('pcc_holder_1');
        }
        if (localStorage.getItem('pcc_type_1')) {
            localStorage.removeItem('pcc_type_1');
        }
        if (localStorage.getItem('pcc_month_1')) {
            localStorage.removeItem('pcc_month_1');
        }
        if (localStorage.getItem('pcc_year_1')) {
            localStorage.removeItem('pcc_year_1');
        }
        if (localStorage.getItem('pcc_no_1')) {
            localStorage.removeItem('pcc_no_1');
        }
        if (localStorage.getItem('cheque_no_1')) {
            localStorage.removeItem('cheque_no_1');
        }
        if (localStorage.getItem('slpayment_term')) {
            localStorage.removeItem('slpayment_term');
        }
        <?php $this->sma->unset_data('remove_slls');
}
        ?>

        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });
        $(document).on('click', '.slduplicate', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });

    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();


        <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
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
        });

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>
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

<!--
<?php

if ($Owner || $GP['bulk_actions']) {
            echo admin_form_open('cars/sale_actions', 'id="action-form"');
        }
?>
-->
<?php echo admin_form_open("cars/index", 'id="action-form"'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-car"></i><?= lang('Cars_list'); ?>

        </h2>
		<div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>

            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang('actions')?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('cars/add_vehicle') ?>" >
                                <i class="fa fa-plus-circle"></i> <?= lang('Add_Vehicle') ?>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </div>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="#" id="excel" class="tip" data-action="export_excel" title="<?= lang('download_xls') ?>"><i class="icon fa fa-file-excel-o"></i></a></li>

				<li class="dropdown">
						<a href="#" id="pdf" class="tip"  title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
			</ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>
                <div id="form">

                    <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <?= lang("Start_date", "Start_date"); ?>
                                        <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <?= lang("End_date", "End_date"); ?>
                                        <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                                    </div>
                                </div>
                                 <div class="col-sm-3" style="display:none;">
                                    <div class="form-group">
                                        <?= lang("product", "suggest_product"); ?>
                                        <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                        <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : "" ?>" id="report_product_id"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="chesis_no"><?= lang("Chesis_no"); ?></label>
                                        <?php echo form_input('chesis_no', (isset($_POST['chesis_no']) ? $_POST['chesis_no'] : ""), 'class="form-control tip" id="chesis_no"'); ?>

                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="panel_no"><?= lang("Panel_no"); ?></label>
                                        <?php echo form_input('panel_no', (isset($_POST['panel_no']) ? $_POST['panel_no'] : ""), 'class="form-control tip" id="panel_no"'); ?>

                                    </div>
                                  </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="fuel_type"><?= lang("Fuel_Type"); ?></label>
                                        <?php
                                            $fuel_type_options =  ['0' => lang('all'),'gasoline' => lang('Gasoline'), 'diesel' => lang('Diesel'),'hybird' => lang('Hybird'),'electricity' => lang('Electricity')];
                                            echo form_dropdown('fuel_type', $fuel_type_options, (isset($_POST['fuel_type']) ? $_POST['fuel_type'] : ""), 'class="form-control" id="fuel_type" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Fuel_Type") . '"');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <?= lang('Car_Owner', 'supplier'); ?>
                                        <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="supplier"'); ?>
                                        </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="warehouse"><?= lang("Showroom"); ?></label>
                                        <?php
                                        $wh[""] = lang('select').' '.lang('Showroom');
                                        foreach ($warehouses as $warehouse) {
                                            $wh[$warehouse->id] = $warehouse->name;
                                        }
                                        echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <div class="form-group ">
                                    <label class="control-label" for="brand_id"><?= lang('brand'); ?></label>

                                    <?php
                                    $br['']= lang('select').' '.lang('brand');
                                    foreach ($brands as $brand) {
                                        $br[$brand->id] = $brand->brand_name;
                                    }
                                         echo form_dropdown('brand_id', $br, (isset($_POST['brand_id']) ? $_POST['brand_id'] : ""), 'class="form-control" id="brand_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("brand") . '"'); ?>

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <div class="form-group ">
                                    <label class="control-label" for="model_id"><?= lang('Car_Type'); ?></label>

                                    <?php
                                    $mod['']= lang('select').' '.lang('Car_Type');

                                         echo form_dropdown('model_id', $mod, (isset($_POST['model_id']) ? $_POST['model_id'] : ""), 'class="form-control" id="model_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Car_Type") . '"'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <div class="form-group ">
                                    <label class="control-label" for="category_id"><?= lang('category'); ?></label>

                                    <?php
                                    $cat['']= lang('select').' '.lang('category');

                                        echo form_dropdown('category_id', $cat, '', 'id="category_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('category') . '"  style="width:100%;" '); ?>
                                        <!-- echo form_dropdown('category_id', $cat, (isset($_POST['category_id']) ? $_POST['category_id'] : ""), 'class="form-control" id="category_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("category") . '"'); ?>
                                        -->

                                    </div>
                                </div>

                                <?php if($Settings->product_serial) { ?>
                                    <div class="col-sm-3" style="display:none;">
                                        <div class="form-group">
                                            <?= lang('serial_no', 'serial'); ?>
                                            <?= form_input('serial', '', 'class="form-control tip" id="serial"'); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="status"><?= lang("Status"); ?></label>
                                        <?php
                                        $status_options =  ['0' => lang('all'),'available' => lang('Available'), 'sold' => lang('sold'), 'reserved' => lang('Reserved')];
                                            echo form_dropdown('status', $status_options, (isset($_POST['status']) ? $_POST['status'] : ""), 'class="form-control" id="status" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("status") . '"');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="car_status"><?= lang("Car_status"); ?></label>
                                        <?php
                                        $car_status_options =  ['0' => lang('all'),'new' => lang('New'), 'old' => lang('old')];
                                            echo form_dropdown('car_status', $car_status_options, (isset($_POST['car_status']) ? $_POST['car_status'] : ""), 'class="form-control" id="car_status" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Car_status") . '"');
                                        ?>
                                    </div>
                                </div>



                    </div> <!-- row  -->
                        <div class="form-group">
                            <div
                                class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                        </div>

                </div> <!-- form -->
                <div class="clearfix"></div>
                <div id="pr_pdf">

                <!-- <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('Cars_list'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Invoice_profit_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table> -->

                <div class="table-responsive">
                <table id="SLData" class="table table-striped jambo_table bulk_action tableData " cellpadding="0" cellspacing="0" border="0">

                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                                                <th><?= lang('image') ?></th>
                                                <th><?= lang('Added_on') ?></th>
                                                <th><?= lang('Chesis_no') ?></th>
												<th><?= lang('Showroom'); ?></th>
                                                <th><?= lang('brand'); ?></th>
                                                <th><?= lang('Car_Type'); ?></th>
                                                <th><?= lang('category'); ?></th>
                                                <th><?= lang('Status') ?></th>

                                                <th><?= lang('Car_Place') ?></th>
                                                <th><?= lang('price') ?></th>
												<th ><?= lang('Sold_on') ?></th>
                            <th style="width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>

                            <th><?= lang('price'); ?></th>
                            <th ><?= lang('Sold_on') ?></th>
                            <th style="width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                    </div>
                 </div>
                 </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) {
                    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php
                }
?>

<script>
$(document).ready(function () {

$('#submit_report').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('cars/index') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#excel').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('cars/sale_actions') ?>');

        });
$('#combine').click(function (e) {
            e.preventDefault();

			//$('#action-form').attr('action', '<?= admin_url('cars/sale_actions') ?>');

        });
	});

</script>
