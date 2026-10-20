<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
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
            'sAjaxSource': '<?= admin_url('carspurchases/getExpenses'); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, {"mRender": fld}, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat},{"mRender": currencyFormat}, {"mRender": pay_status}, {
                "bSortable": false,
                "mRender": attachment
            }, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "expense_link1";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][4]);
					paid += parseFloat(aaData[aiDisplay[i]][5]);
                    balance += parseFloat(aaData[aiDisplay[i]][6]);
                
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(total);
                nCells[5].innerHTML = currencyFormat(paid);
                nCells[6].innerHTML = currencyFormat(balance);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference');?>]", filter_type: "text", data: []},
            <?php  if ($this->Settings->accounting){?>
            {column_number: 3, filter_default_label: "[<?=lang('Account_type');?>]", filter_type: "text", data: []},
            <?php }else {?>
                {column_number: 3, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
           <?php
             } ?>
            {column_number: 7, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
        ], "footer");

    });

</script>

<?php if ($Owner) {
    echo admin_form_open('carspurchases/expense_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-dollar"></i><?= lang('expenses'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('carspurchases/add_expense') ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_expense') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_expenses') ?></b>"
                                data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>"
                                data-html="true" data-placement="left">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_expenses') ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">

                        <li class="dropdown"><a href="<?=admin_url('carspurchases/add_expense')?>" data-toggle="modal" data-target="#myModal"  title="<?= lang('add_expense') ?>"><i class="icon fa fa-plus"></i></a></li>

			</ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="EXPData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-2"><?= lang('date'); ?></th>
                            <th><?= lang('reference'); ?></th>
                          <?php  if ($this->Settings->accounting){ ?>
                                <th><?= lang('Account_type'); ?></th>
                             <?php }else { ?>
                                <th><?= lang('category'); ?></th>
                                <?php
                                } ?>
                            <th><?= lang('amount'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th class="col-xs-1"><?= lang('payment_status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i>
                            </th>
                            <th style="width:100px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i>
                            </th>
                            <th style="width:100px; text-align: center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner) {
    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php
} ?>
