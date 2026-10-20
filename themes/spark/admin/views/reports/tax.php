<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
	<style>
		@media print {
                .no-print { display: none !important; }
				.tbl table  {
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        padding: 0.1in !important;


    }
	.tbl table td{
		-webkit-print-color-adjust:exact !important;
	}


    }
	</style>
<?php
if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
}
$v = '';
if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . implode(",",$this->input->post('brId'));
}
if ($this->input->post('biller')) {
    $v .= '&biller=' . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}

?>
<div class="spark-tax-page">
    <div class="spark-tax-header">
        <div class="spark-tax-header-start">
            <div class="spark-tax-header-icon">
                <i class="fa fa-percent"></i>
            </div>
            <div class="spark-tax-header-info">
                <h1 class="spark-tax-title">
                    <?= lang('Tax') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-tax-subtitle">
                    <?= lang('manage_tax_desc'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-content">
            <ul id="myTab" class="nav nav-tabs no-print spark-nav-tabs" style="margin-bottom: 20px;">
                <li class="active"><a href="#sales-con" data-toggle="tab" class="tab-grey"><?= lang('sales') ?></a></li>
                <li class=""><a href="#purchases-con" data-toggle="tab" class="tab-grey"><?= lang('purchases') ?></a></li>
                <li class=""><a href="#tax-con" data-toggle="tab" class="tab-grey"><?= lang('Tax_Declaration') ?></a></li>
            </ul>

            <div class="tab-content">
                <div id="sales-con" class="tab-pane fade in active">
<script>
    $(document).ready(function () {
                 <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		                localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
			        	<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

	                	localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				        <?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

                        localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
                        localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
                        localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
                        <?php
                            }
                        }
					?>
        oTable = $('#TAXData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/get_multi_sale_taxes/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

			   nRow.id = aData[0];
                nRow.className = (aData[5] == 'completed') ? "invoice_link2" : "invoice_link2 warning";
                return nRow;
            },
            //"aoColumns": [{"mRender": fld}, null, {"mRender": row_status}, null, null,{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
			"aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");


        $('#grpId').change(function () {
            localStorage.setItem('sgrpId', $(this).val());

            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#coId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;



$('#coId').val('').trigger('change.select2');
$('#brId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });



        $('#coId').change(function () {
            localStorage.setItem('scoId', $(this).val());



            var v = $(this).val();
            var uopt = $("#brId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

                            $('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#brId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });




        $('#brId').change(function () {
            localStorage.setItem('sbrId', $(this).val());


            var v = $(this).val();
            var uopt = $("#biller");

            var uopt1 = $("#warehouse");






            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {


//console.log(scdata[0])
$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});




                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });
        $('#warehouse').val(scdata[0].id).trigger('change.select2');
                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBillersBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {



$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;





$('#biller').val('').trigger('change.select2');


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                });
            }












        });


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
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
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
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
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


		oTable = $('#TAXDataCN').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/get_multi_sale_taxesCN/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

			   nRow.id = aData[0];
                nRow.className = (aData[5] == 'completed') ? "invoice_link2" : "invoice_link2 warning";
                return nRow;
            },
            //"aoColumns": [{"mRender": fld}, null, {"mRender": row_status}, null, null,{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
			"aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");






		oTable = $('#TAXDataCNOut').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/get_multi_sale_taxesCNOut/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

			   nRow.id = aData[0];
                nRow.className = (aData[5] == 'completed') ? "invoice_link2" : "invoice_link2 warning";
                return nRow;
            },
            //"aoColumns": [{"mRender": fld}, null, {"mRender": row_status}, null, null,{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
			"aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId') && !$this->input->post('warehouse')) { ?>
            $('#salesform').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#salesform").slideToggle(200);
            return false;
        });
        $('.toggle_up').click(function () {
            $("#salesform").slideUp(200);
            return false;
        });




        $(document).on('ifChecked', '#ch_brId1', function (e) {
            $("#brId1 > option").prop("selected","selected");
            $("#brId1").trigger("change");
        });
        $(document).on('ifUnchecked', '#ch_brId1', function (e) {
            $("#brId1 > option").removeAttr("selected");
            $('#brId1').val('<?= $this->session->userdata('brId') ?>');
            $("#brId1").trigger("change");

        });

    });
</script>


<div class="box box1">
    <div class="spark-tax-header" style="margin-bottom: 16px;">
        <div class="spark-tax-header-start">
            <div class="spark-tax-header-icon">
                <i class="fa fa-percent"></i>
            </div>
            <div class="spark-tax-header-info">
                <h2 class="spark-tax-title">
                    <?= lang('VAT_on_sales'); ?>
                </h2>
            </div>
        </div>

        <div class="spark-tax-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('download_xls'); ?></span>
            </button>

            <button type="button" id="pdf1" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                <i class="fa fa-file-pdf-o"></i>
                <span><?= lang('download_pdf'); ?></span>
            </button>
        </div>
    </div>
    <div class="box-content">



		<div class="row">
			<div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="salesform">

                    <?php echo admin_form_open('reports/tax'); ?>
                    <div class="row">
                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-5">
                                        <?= lang('Main_branch', 'Main_branch') ?>
                                        <div class="input-group">

                                            <?php
                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
                                            if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }else{
                                            $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                        }
                                        }
                                            echo form_multiselect('brId[]', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId1"   style="width:100%;overflow:hidden;height:auto;"')
                                            ?>
                                            <span class="input-group-addon pointer"  style="background-color:inherit;border:0;padding: 1px 10px;">
                                                <input type="checkbox" class="checkbox" name="ch_brId1" id="ch_brId1" >
                                                    <label for="ch_brId1" class="padding05"><?= lang('all') ?></label>
                                            </span>
                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

                        <!--<div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>






		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="10" align="center"  class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId1')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('warehouse')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('warehouse').' : '.($this->site->getWarehouseByID($this->input->post('warehouse'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10" align="center" class="pltable" ><b><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_sales') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></b></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="salesdiv" id="salesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>

	</tr>

	<tr>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>
	<?php
	$total_sales_amount=0;
	$total_sales_amount_returned=0;
	$total_sales_tax=0;




	foreach ($sale_tax_local_basic_details as $sale_tax_local_basic_detail) {

		if($start_date && $end_date){
        if(str_replace("cpesoft_","",$this->db->database)=="hamed")
            $sale_tax_local_basic_details_returned     = $this->reports_model->getMultiSalesTaxLocalBasicDetailsReturnedV2($start_date, $end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$sale_tax_local_basic_detail->rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));
        else
        $sale_tax_local_basic_details_returned     = $this->reports_model->getMultiSalesTaxLocalBasicDetailsReturned($start_date, $end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$sale_tax_local_basic_detail->rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));


        $ret_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($sale_tax_local_basic_details_returned as $sale_tax_local_basic_details_return) {
            $sdate=$this->site->getSaleByid($sale_tax_local_basic_details_return->sale_id)->date;
			if (($sdate > $start_date && $sdate < $end_date) || (!isset($sale_tax_local_basic_details_return->sale_id)))
				{
					$qTotal=$qTotal+$sale_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$sale_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$sale_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$sale_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_data['total']= $qTotal;
					$ret_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		//$this->data['sale_tax_local_basic_details_returned']=$ret_data_amendment;
		}
		//echo $ret_data_amendment['total']."  ".$ret_data_amendment['total_tax']."<br>";
		//echo $sale_tax_local_basic_detail->total."  ".$sale_tax_local_basic_detail->total_tax."<br>";
		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;


	if($ret_data_amendment['total']){
		$Tgrand_total=($sale_tax_local_basic_detail->total-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=(($sale_tax_local_basic_detail->total_tax));
	}else{
		$Tgrand_total=($sale_tax_local_basic_detail->total);//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($sale_tax_local_basic_detail->total_tax);

	}
		//echo $Tret_grand_total."  ".$Tgrand_total."<br>";


		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Sales_subject_to_the_standard')." ".(int)$this->sma->formatNumber($sale_tax_local_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Tgrand_total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($Tret_grand_total)) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Ttotal_tax) ?></font></td>

	</tr>
	<?php
	$total_sales_amount=$total_sales_amount+($Tgrand_total);
	$total_sales_amount_returned=$total_sales_amount_returned+abs($Tret_grand_total);
	$total_sales_tax=$total_sales_tax+($Ttotal_tax);
	//$total_sales_tax_completed=$total_sales_tax_completed+$sale_tax_local_basic_details_completed[$index]->total_tax;
	//$total_sales_tax_returned=$total_sales_tax_returned+abs($sale_tax_local_basic_details_returned[$index]->total_tax);
	//$index++;
	}



	$qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

	if($start_date && $end_date){
		foreach ($sale_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->site->getSaleByid($sale_tax_local_zero_return->sale_id)->date;
		//print_r($sale_tax_local_zero_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				//	$qTotal_Zero=$qTotal_Zero+abs($sale_tax_local_zero_return->total);
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}



	if($start_date && $end_date){
		foreach ($sale_tax_international_returned as $sale_tax_international_return) {
        $sdate=$this->site->getSaleByid($sale_tax_international_return->sale_id)->date;
		//print_r($sale_tax_international_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentinternational=$qTotal_amendmentinternational+abs($sale_tax_international_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

	if($start_date && $end_date){
		foreach ($sale_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->site->getSaleByid($sale_tax_local_nt_return->sale_id)->date;
		//print_r($sale_tax_local_nt_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}


		?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Zero-rated_domestic_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_local_zero->total) ? $this->sma->formatDecimal(($sale_tax_local_zero->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Exports') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_international->total) ? $this->sma->formatDecimal(($sale_tax_international->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentinternational) ? abs($this->sma->formatDecimal($qTotal_amendmentinternational)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Tax_free_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_local_nt->total) ? $this->sma->formatDecimal(($sale_tax_local_nt->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<?php
	$total_sales_amount=$total_sales_amount+(($sale_tax_local_zero->total) )+(($sale_tax_international->total))+(($sale_tax_local_nt->total));
	$total_sales_amount_returned=$total_sales_amount_returned+abs($qTotal_amendmentZero)+abs($qTotal_amendmentinternational)+abs($qTotal_amendmentnt);
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_sales') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>

		<ul id="myTab2" class="nav nav-tabs no-print spark-nav-tabs" style="margin-bottom: 20px;">
			<li class="active"><a href="#allsales-con" data-toggle="tab" class="tab-grey"><?= lang('sales') ?></a></li>
			<li class=""><a href="#creditnotes-con" data-toggle="tab" class="tab-grey"><?= lang('Credit_Notes') ?></a></li>
			<li class=""><a href="#creditnotesout-con" data-toggle="tab" class="tab-grey"><?= lang('Previous_period_credit_notes') ?></a></li>
		</ul>

		<div class="tab-content">
		<div id="allsales-con" class="tab-pane fade in active">



		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXData"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>



                    </table>
                </div>

				</div>
				</div>
		</div>


		<div id="creditnotes-con" class="tab-pane fade in">



		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXDataCN"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>



                    </table>
                </div>

				</div>
				</div>
		</div>






		<div id="creditnotesout-con" class="tab-pane fade in">



		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXDataCNOut"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>



                    </table>
                </div>

				</div>
				</div>
		</div>


		</div>


	</div>
	</div>












    </div>
</div>
</div>

<div id="purchases-con" class="tab-pane fade in">

    <script type="text/javascript">
        $(document).ready(function () {
            <?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId') && !$this->input->post('warehouse')) { ?>
                $('#purchaseform').hide();
            <?php } ?>
            $('.purchasetoggle_down').click(function () {
                $("#purchaseform").slideToggle(200);
                return false;
            });
            $('.purchasetoggle_up').click(function () {
                $("#purchaseform").slideUp(200);
                return false;
            });

            $(document).on('ifChecked', '#ch_pubrId1', function (e) {
                $("#pubrId1 > option").prop("selected","selected");
                $("#pubrId1").trigger("change");
            });
            $(document).on('ifUnchecked', '#ch_pubrId1', function (e) {
                $("#pubrId1 > option").removeAttr("selected");
                $('#pubrId1').val('<?= $this->session->userdata('brId') ?>');
                $("#pubrId1").trigger("change");
            });
        });
    </script>

    <script>
        $(document).ready(function () {
             /* <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		                localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
			        	<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

	                	localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				        <?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

                        localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
                        localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
                        localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
                        <?php
                            }
                        }
					?> */

            oTable = $('#PTAXData').dataTable({
                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('reports/get_multi_purchase_taxes/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    nRow.id = aData[0];
                    nRow.className = (aData[5] == 'received') ? "purchase_link" : "purchase_link warning";
                    return nRow;
                },
                "aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
				{column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
                {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
            ], "footer");


            $('#pugrpId').change(function () {
            localStorage.setItem('sgrpId', $(this).val());

            var v = $(this).val();
            var uopt = $("#pucoId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#pucoId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#pubrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;



$('#pucoId').val('').trigger('change.select2');
$('#pubrId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#pucoId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#pucoId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });



        $('#pucoId').change(function () {
            localStorage.setItem('scoId', $(this).val());



            var v = $(this).val();
            var uopt = $("#pubrId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

                            $('#pubrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#pubrId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#pubrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#pubrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });




        $('#pubrId').change(function () {
            localStorage.setItem('sbrId', $(this).val());


            var v = $(this).val();
            var uopt = $("#biller");

            var uopt1 = $("#warehouse");






            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {


//console.log(scdata[0])
$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});




                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });
        $('#warehouse').val(scdata[0].id).trigger('change.select2');
                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBillersBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {



$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;





$('#biller').val('').trigger('change.select2');


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                });
            }












        });


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
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
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
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
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

			oTable = $('#PTAXDataDN').dataTable({
                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('reports/get_multi_purchase_taxesDN/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    nRow.id = aData[0];
                    nRow.className = (aData[5] == 'received') ? "purchase_link" : "purchase_link warning";
                    return nRow;
                },
                "aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
				{column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
                {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
            ], "footer");




			oTable = $('#PTAXDataDNOut').dataTable({
                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('reports/get_multi_purchase_taxesDNOut/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    nRow.id = aData[0];
                    nRow.className = (aData[5] == 'received') ? "purchase_link" : "purchase_link warning";
                    return nRow;
                },
                "aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
				{column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
                {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
            ], "footer");



			function attachment(x) {
            if (x != null) {
                return '<a href="' + site.url + 'assets/uploads/' + x + '" target="_blank"><i class="fa fa-chain"></i></a>';
            }
            return x;
        }

        oTable = $('#EXPData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getExpensesTax/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, {"mRender": fld}, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "expense_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][4]);
					paid += parseFloat(aaData[aiDisplay[i]][5]);

                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(total);
                nCells[5].innerHTML = currencyFormat(paid);
               }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Expense_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
        ], "footer");


        $('#tgrpId').change(function () {
            localStorage.setItem('sgrpId', $(this).val());

            var v = $(this).val();
            var uopt = $("tcoId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#tcoId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#tbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;



$('#tcoId').val('').trigger('change.select2');
$('#tbrId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#tcoId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#tcoId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });



        $('#tcoId').change(function () {
            localStorage.setItem('scoId', $(this).val());



            var v = $(this).val();
            var uopt = $("#tbrId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

                            $('#tbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#tbrId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#tbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#tbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });




        $('#tbrId').change(function () {
            localStorage.setItem('sbrId', $(this).val());


            var v = $(this).val();
            var uopt = $("#biller");

            var uopt1 = $("#warehouse");






            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {


//console.log(scdata[0])
$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});




                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });
        $('#warehouse').val(scdata[0].id).trigger('change.select2');
                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBillersBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {



$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;





$('#biller').val('').trigger('change.select2');


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                });
            }












        });

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
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
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
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
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



        });
    </script>
    <div class="box box2">
        <div class="spark-tax-header" style="margin-bottom: 16px;">
            <div class="spark-tax-header-start">
                <div class="spark-tax-header-icon">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="spark-tax-header-info">
                    <h2 class="spark-tax-title">
                        <?= lang('VAT_on_purchases'); ?>
                    </h2>
                </div>
            </div>

            <div class="spark-tax-header-actions">
                <button type="button" class="spark-btn-secondary-action purchasetoggle_down tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>

                <button type="button" id="xls1" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                    <i class="fa fa-file-excel-o"></i>
                    <span><?= lang('download_xls'); ?></span>
                </button>

                <button type="button" id="pdf2" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                    <i class="fa fa-file-pdf-o"></i>
                    <span><?= lang('download_pdf'); ?></span>
                </button>
            </div>
        </div>
    <div class="box-content">



        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="purchaseform">

                    <?php echo admin_form_open('reports/tax'); ?>
                    <div class="row">
                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="pugrpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="pucoId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-5">
                                        <?= lang('Main_branch', 'Main_branch') ?>
                                        <div class="input-group">
                                            <?php
                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
                                            if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }else{
                                            $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                        }
                                        }
                                            echo form_multiselect('brId[]', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="pubrId1"   style="width:100%;overflow:hidden;height:auto;"')
                                            ?>
                                            <span class="input-group-addon pointer"  style="background-color:inherit;border:0;padding: 1px 10px;">
                                                <input type="checkbox" class="checkbox" name="ch_pubrId1" id="ch_pubrId1" >
                                                    <label for="ch_pubrId1" class="padding05"><?= lang('all') ?></label>
                                            </span>
                                        </div>
                                        </div>
                                        <?php }
                               } ?>
                        <!--<div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>






				<div class="row">
			<div class="col-sm-12 col-md-12">

				<div  id="pr_pdf2">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId1')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('warehouse')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('warehouse').' : '.($this->site->getWarehouseByID($this->input->post('warehouse'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10" align="center" class="pltable" ><b><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_purchases') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></b></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="purchasesdiv" id="purchasesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>

	</tr>

	<tr>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>
	<?php
	$total_purchases_amount=0;
	$total_purchases_amount_returned=0;
	$total_purchases_tax=0;
	foreach ($purchase_tax_local_basic_details as $purchase_tax_local_basic_detail) {

		if($start_date && $end_date){
		$purchase_tax_local_basic_details_returned     = $this->reports_model->getMultiPurchasesTaxLocalBasicDetailsReturned($start_date, $end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$purchase_tax_local_basic_detail->rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));
        $ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_local_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}

		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;




		//$Tgrand_total=($purchase_tax_local_basic_detail->total+$purchase_tax_local_basic_detail->total_tax);//-$Tret_grand_total;

	if($ret_data_amendment['total']){
		$Tgrand_total=($purchase_tax_local_basic_detail->total-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=(($purchase_tax_local_basic_detail->total_tax));
	}else{
		$Tgrand_total=($purchase_tax_local_basic_detail->total);//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($purchase_tax_local_basic_detail->total_tax);

	}

		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Purchases_subject_to_the_standard')." ".(int)$this->sma->formatNumber($purchase_tax_local_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Tgrand_total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($Tret_grand_total)) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($Ttotal_tax) ?></font></td>

	</tr>
	<?php
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_basic_detail->total;
	//$total_purchases_tax=$total_purchases_tax+$purchase_tax_local_basic_detail->total_tax;
	/*
	$total_purchases_amount=$total_purchases_amount+($purchase_tax_local_basic_detail->total );
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($ret_data_amendment['total']);
	$total_purchases_tax=$total_purchases_tax+(($purchase_tax_local_basic_detail->total_tax  + ($ret_data_amendment['total_tax'])));
	*/
	$total_purchases_amount=$total_purchases_amount+($Tgrand_total);
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($Tret_grand_total);
	$total_purchases_tax=$total_purchases_tax+($Ttotal_tax);

	}
		?>


	<?php
	foreach ($purchase_tax_international_basic_details as $purchase_tax_international_basic_detail) {
		if($start_date && $end_date){
		$purchase_tax_international_basic_details_returned     = $this->reports_model->getMultiPurchasesTaxInternationalBasicDetailsReturned($start_date, $end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$purchase_tax_local_basic_details->rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));
        //print_r($purchase_tax_international_basic_details_returned);
		$ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_international_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}


		$Tret_grand_total=0;
		$Tgrand_total=0;
		$Ttotal_tax=0;






	if($ret_data_amendment['total']){
		$Tgrand_total=((($purchase_tax_international_basic_detail->total)+($purchase_tax_international_basic_detail->customs_fees ) +($purchase_tax_international_basic_detail->Other_fees ))-$ret_data_amendment['total']);//-$Tret_grand_total;
		$Tret_grand_total=($ret_data_amendment['total']);
		$Ttotal_tax=($purchase_tax_international_basic_detail->total_tax)+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);
		}else{
		$Tgrand_total=(($purchase_tax_international_basic_detail->total)+($purchase_tax_international_basic_detail->customs_fees ) +($purchase_tax_international_basic_detail->Other_fees ));//-$Tret_grand_total;
		$Tret_grand_total=0;
		$Ttotal_tax=($purchase_tax_international_basic_detail->total_tax)+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);

	}
		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Imports_subject_to_value_added_tax_and_paid_to_the_customs_authority')." ".(int)$this->sma->formatNumber($purchase_tax_international_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($ret_data_amendment['total'])) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100)+$ret_data_amendment['total_tax']) ?></font></td>

	</tr>
	<?php
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees - abs($ret_data_amendment['total']);
	//$total_purchases_tax=$total_purchases_tax+$purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);


	/*$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees ;
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($ret_data_amendment['total']);
	$total_purchases_tax=$total_purchases_tax+($purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100)+$ret_data_amendment['total_tax']);
	*/
	$total_purchases_amount=$total_purchases_amount+($Tgrand_total);
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($Tret_grand_total);
	$total_purchases_tax=$total_purchases_tax+($Ttotal_tax);

	}





		?>




<?php
$purchase_tax_international_zero_ret_var=0;
if($start_date && $end_date){
    foreach ($purchase_tax_international_zero_returned as $purchase_tax_international_zero_return) {
        $sdate=$this->site->getPurchaseByID($purchase_tax_international_zero_return->purchase_id)->date;

        if ($sdate > $start_date && $sdate < $end_date)
		{}else{
            $purchase_tax_international_zero_ret_var=$purchase_tax_international_zero_ret_var + $purchase_tax_international_zero_return->total;
        }
    }
}


$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_zero->total;
$numItems = count($purchase_tax_international_zero_details);
$i = 0;

	$ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;
	foreach ($purchase_tax_international_zero_details as $purchase_tax_international_zero_detail) {

		if($start_date && $end_date){
		$purchase_tax_international_zero_details_returned     = $this->reports_model->getMultiPurchasesTaxInternationalZeroDetailsReturned($start_date, $end_date ,(isset($_POST['warehouse']) ? $_POST['warehouse'] : ''),$purchase_tax_international_zero_detail->tax_active_rate,(isset($_POST['grpId']) ? $_POST['grpId'] : ''),(isset($_POST['coId']) ? $_POST['coId'] : ''),(isset($_POST['brId']) ? $_POST['brId'] : ''));
        //print_r($purchase_tax_international_zero_details_returned);
		foreach ($purchase_tax_international_zero_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
        }
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}

		//echo $ret_data_amendment['total']."p";

		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Imports_subject_to_value_added_tax_to_which_the_reverse_charge_mechanism_is_applied')." ".(int)$this->sma->formatNumber($purchase_tax_international_zero_detail->tax_active_rate)."%" ?></font></td>
		<?php
		if($i==0){
			?>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" rowspan="<?= $numItems ?>" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_international_zero->total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" rowspan="<?= $numItems ?>" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_international_zero_ret_var) ? $this->sma->formatDecimal(abs($purchase_tax_international_zero_ret_var)) : '0.00' ?></font></td>
		<?php
		}
		?>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal((((($purchase_tax_international_zero_detail->order_tax!=0 || $purchase_tax_international_zero_detail->product_tax!=0) ? $purchase_tax_international_zero_detail->total : 0)+($purchase_tax_international_zero_ret_var)) * ($purchase_tax_international_zero_detail->tax_active_rate) / 100)) ?></font></td>

	</tr>
	<?php
	$i++;
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_zero_detail->total;

	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($purchase_tax_international_zero_ret_var);
	$total_purchases_tax=$total_purchases_tax+(((($purchase_tax_international_zero_detail->order_tax!=0 || $purchase_tax_international_zero_detail->product_tax!=0) ? $purchase_tax_international_zero_detail->total : 0)+($purchase_tax_international_zero_ret_var)) * ($purchase_tax_international_zero_detail->tax_active_rate) / 100);

	}
		?>

		<?php
		$qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

	if($start_date && $end_date){
		foreach ($purchase_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->site->getPurchaseByID($sale_tax_local_zero_return->sale_id)->date;
		//print_r($sale_tax_local_zero_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				//	$qTotal_Zero=$qTotal_Zero+abs($sale_tax_local_zero_return->total);
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}


	if($start_date && $end_date){
		foreach ($purchase_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->site->getPurchaseByID($sale_tax_local_nt_return->sale_id)->date;
		//print_r($sale_tax_local_nt_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Zero-rated_domestic_purchases') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_local_zero->total) ? $this->sma->formatDecimal(($purchase_tax_local_zero->total) ) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>



	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Tax_free_purchases') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_local_nt->total) ? $this->sma->formatDecimal(($purchase_tax_local_nt->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<?php
	$total_purchases_amount_returned=$total_purchases_amount_returned+$qTotal_amendmentZero+$qTotal_amendmentnt;
	$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_zero->total+$purchase_tax_local_nt->total;
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_purchases') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>

		<ul id="myTab3" class="nav nav-tabs no-print spark-nav-tabs" style="margin-bottom: 20px;">
			<li class="active"><a href="#allpurchases-con" data-toggle="tab" class="tab-grey"><?= lang('purchases')." - ".lang('expenses') ?></a></li>
			<li class=""><a href="#debitnotes-con" data-toggle="tab" class="tab-grey"><?= lang('Debit_Notes') ?></a></li>
			<li class=""><a href="#debitnotesout-con" data-toggle="tab" class="tab-grey"><?= lang('Previous_period_debit_notes') ?></a></li>
		</ul>

		<div class="tab-content">
		<div id="allpurchases-con" class="tab-pane fade in active">

		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXData"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>




				 <div class="row">
            <div class="col-lg-12">


                <div class="table-responsive">
                    <table id="EXPData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr class="active">
                            <th style="width:100px !important;"><?= lang('Expense_No'); ?></th>
                            <th><?= lang('date'); ?></th>
                            <th class="col-xs-2"><?= lang('reference'); ?></th>
                            <th><?= lang('category'); ?></th>
                            <th><?= lang('Total_before_VAT'); ?></th>
                            <th><?= lang('total_tax'); ?></th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
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

                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


		</div>

		<div id="debitnotes-con" class="tab-pane fade in">

		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXDataDN"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>
				</div>




		<div id="debitnotesout-con" class="tab-pane fade in">

		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXDataDNOut"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>
				</div>


		</div>




	</div>
	</div>








            </div>





        </div>



    </div>



<!-----tax-------->

<div id="tax-con" class="tab-pane fade in">
<script type="text/javascript">
	$(document).ready(function () {
        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		                localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
			        	<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

	                	localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				        <?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

                        localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
                        localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
                        localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
                        <?php
                            }
                        }
					?>

		<?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId') && !$this->input->post('warehouse')) { ?>
			$('#purchaseform_tax').hide();
		<?php } ?>
		$('.purchasetoggle_down_tax').click(function () {
			$("#purchaseform_tax").slideToggle(200);
			return false;
		});
		$('.purchasetoggle_up_tax').click(function () {
			$("#purchaseform_tax").slideUp(200);
			return false;
		});

        $(document).on('ifChecked', '#ch_tbrId1', function (e) {
            $("#tbrId1 > option").prop("selected","selected");
            $("#tbrId1").trigger("change");
        });
        $(document).on('ifUnchecked', '#ch_tbrId1', function (e) {
            $("#tbrId1 > option").removeAttr("selected");
            $('#tbrId1').val('<?= $this->session->userdata('brId') ?>');
            $("#tbrId1").trigger("change");
        });
	});
</script>
	<div class="box box2">
        <div class="spark-tax-header" style="margin-bottom: 16px;">
            <div class="spark-tax-header-start">
                <div class="spark-tax-header-icon">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="spark-tax-header-info">
                    <h2 class="spark-tax-title">
                        <?= lang('Tax_Declaration'); ?>
                    </h2>
                </div>
            </div>

			<div class="spark-tax-header-actions">
                <button type="button" class="spark-btn-secondary-action purchasetoggle_down_tax tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>

                <button type="button" id="pdf3" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                    <i class="fa fa-file-pdf-o"></i>
                    <span><?= lang('download_pdf'); ?></span>
                </button>

                <button type="button" id="print" class="spark-btn-secondary-action tip" title="<?= lang('print') ?>">
                    <i class="fa fa-print"></i>
                    <span><?= lang('print'); ?></span>
                </button>
			</div>
		</div>

		<div class="box-content">
			<p class="introtext"><?= lang('Value_added_tax_for_the_current_period_if_it_is_positive_on_you_and_if_it_is_negative_for_you'); ?></p>
			<div id="pr_pdf3">

			<div class="no-print" id="purchaseform_tax">


				<?php echo admin_form_open('reports/tax'); ?>
				<div class="row">
                <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="tgrpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="tcoId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-5">
                                        <?= lang('Main_branch', 'Main_branch') ?>
                                        <div class="input-group">
                                            <?php
                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
                                            if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }else{
                                            $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                        }
                                        }
                                            echo form_multiselect('brId[]', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="tbrId1"   style="width:100%;overflow:hidden;height:auto;"')
                                            ?>
                                            <span class="input-group-addon pointer"  style="background-color:inherit;border:0;padding: 1px 10px;">
                                                <input type="checkbox" class="checkbox" name="ch_tbrId1" id="ch_tbrId1" >
                                                    <label for="ch_tbrId1" class="padding05"><?= lang('all') ?></label>
                                            </span>
                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

					<!--<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="biller"><?= lang('biller'); ?></label>
							<?php
							$bl[''] = lang('select') . ' ' . lang('biller');
							foreach ($billers as $biller) {
								$bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
							}
							echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
							?>
						</div>
					</div>-->
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
							<?php
							$wh[''] = lang('select') . ' ' . lang('warehouse');
							foreach ($warehouses as $warehouse) {
								$wh[$warehouse->id] = $warehouse->name;
							}
							echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
							?>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<?= lang('start_date', 'start_date'); ?>
							<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<?= lang('end_date', 'end_date'); ?>
							<?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div
						class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
				</div>
				<?php echo form_close(); ?>

			</div>

			<div class="clearfix"></div>





			<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl">
					<table border="0" cellpadding="0" cellspacing="0" class="table">


					<thead  style="width:100%;border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('Tax_Declaration'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"  align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId1')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('warehouse')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10" align="center"   class="pltable" ><b><font size="3" color="#212529"><?php echo lang('warehouse').' : '.($this->site->getWarehouseByID($this->input->post('warehouse'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;-webkit-print-color-adjust:exact !important;"  colspan="10" align="center" class="pltable" ><b><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Tax_Declaration') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></b></font></td>

					</tr>
					</thead>
					<tbody  width="100%" class="taxsalesdiv">



					</tbody>
					<tr>
					<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="3"><?= lang('Total_VAT_due_for_the_current_period') ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_amount-$total_purchases_amount) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal(abs($total_sales_amount_returned)-abs($total_purchases_amount_returned)) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_tax-$total_purchases_tax) ?></font></b></td>

					</tr>

					</table>





				</div>


			</div>




		</div>









			<div class="clearfix"></div>
			<!-----------end --->
		</div>
	</div>
	</div>


</div>
<!---------------->

</div>
</div>
</div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {




		$(function(){

			var htmlsales = document.getElementById("salesdiv").innerHTML;
            var htmlpurchases = document.getElementById("purchasesdiv").innerHTML;
            $('.taxsalesdiv').html(htmlsales+' '+htmlpurchases);
			//htmlString = document.getElementById("purchasesdiv").innerHTML;
            //$('.taxsalesdiv').appendTo(htmlString);


		});

		$('#pdf1').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf1','<?= lang('sales'); ?>');
		    return false;
        });

		$('#pdf2').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf2','<?= lang('purchases'); ?>');
		    return false;
        });

        $('#pdf3').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf3','<?= lang('Tax_Declaration'); ?>');
		    return false;
        });


        $('#print').click(function (event) {
            window.print();
			return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_multi_sale_taxes/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#xls1').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_multi_purchase_taxes/0/xls/?v=1' . $v)?>";
            return false;
        });

        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box1'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
        $('#image1').click(function (event) {
            event.preventDefault();
            html2canvas($('.box2'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
