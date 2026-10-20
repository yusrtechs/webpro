<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }
</style>

<script>
    $(document).ready(function () {
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
        PrPDF('pr_pdf','<?= lang('Car_Owners_report'); ?>','L');
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

        oTable = $('#CusData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('carsreports/getSuppliers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, null, null, null, {
                "mRender": decimalFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {
                "mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var purchases = 0, total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    purchases += parseFloat(aaData[aiDisplay[i]][4]);
                    total += parseFloat(aaData[aiDisplay[i]][5]);
                    paid += parseFloat(aaData[aiDisplay[i]][6]);
                    balance += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = decimalFormat(parseFloat(purchases));
                nCells[5].innerHTML = currencyFormat(parseFloat(total));
                nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                nCells[7].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('company');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<?=$content_report?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('Car_Owners'); ?>
        <?php
        if ($this->input->post('start_date')) {
                //echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
                echo lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date');

            }
            ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>"><i class="icon fa fa-file-excel-o"></i></a></li>
               <li class="dropdown"><a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>"><i class="icon fa fa-file-pdf-o"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('View_report_Car_Owner'); ?></p>
                <div id="pr_pdf">
                <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('Car_Owners_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Car_Owners_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table  table-condensed table-hover table-striped reports-table tableData">
                        <thead>
                        <tr class="primary">
                            <th><?= lang('company'); ?></th>
                            <th><?= lang('name'); ?></th>
                            <th><?= lang('phone'); ?></th>
                            <th><?= lang('email_address'); ?></th>
                            <th><?= lang('total_purchases'); ?></th>
                            <th><?= lang('Total_amount_purchases'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th style="width:85px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-center"><?= lang('total_purchases'); ?></th>
                            <th class="text-center"><?= lang('total_amount'); ?></th>
                            <th class="text-center"><?= lang('paid'); ?></th>
                            <th class="text-center"><?= lang('balance'); ?></th>
                            <th style="width:85px; text-align: center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('carsreports/getSuppliers/0/xls')?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>