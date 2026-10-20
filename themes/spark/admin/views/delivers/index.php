<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#QUData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getDelivers' . ($warehouse_id ? '/' . $warehouse_id : '')) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "delivery_link";
                return nRow;
            },
            "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null, null, null, null, {"mRender": currencyFormat}, {"mRender": row_status}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('biller');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('note');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('total');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");
        oTable.fnSetColumnVis( 5, false );
        <?php if ($this->session->userdata('remove_dols')) { ?>
        if (localStorage.getItem('doitems')) {
            localStorage.removeItem('doitems');
        }
        if (localStorage.getItem('dodiscount')) {
            localStorage.removeItem('dodiscount');
        }
        if (localStorage.getItem('dotax2')) {
            localStorage.removeItem('dotax2');
        }
        if (localStorage.getItem('doshipping')) {
            localStorage.removeItem('doshipping');
        }
        if (localStorage.getItem('doref')) {
            localStorage.removeItem('doref');
        }
        if (localStorage.getItem('dowarehouse')) {
            localStorage.removeItem('dowarehouse');
        }
        if (localStorage.getItem('donote')) {
            localStorage.removeItem('donote');
        }
        if (localStorage.getItem('docustomer')) {
            localStorage.removeItem('docustomer');
        }
        if (localStorage.getItem('dobiller')) {
            localStorage.removeItem('dobiller');
        }
        if (localStorage.getItem('docurrency')) {
            localStorage.removeItem('docurrency');
        }
        if (localStorage.getItem('dodate')) {
            localStorage.removeItem('dodate');
        }
        if (localStorage.getItem('dostatus')) {
            localStorage.removeItem('dostatus');
        }
        <?php $this->sma->unset_data('remove_dols'); } ?>
    });
</script>

<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('sales/deliver_actions', 'id="action-form"');
} ?>

<div class="spark-delivers-page">
    <div class="spark-delivers-header">
        <div class="spark-delivers-header-start">
            <div class="spark-delivers-header-icon">
                <i class="fa fa-truck"></i>
            </div>
            <div class="spark-delivers-header-info">
                <h1 class="spark-delivers-title">
                    <?= lang('deliveries'); ?><?= $warehouse_id ? ' (' . $warehouse->name . ')' : ''; ?>
                </h1>
                <p class="spark-delivers-subtitle">
                    <?= lang('manage_deliveries_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-delivers-header-actions">
            <a href="<?= admin_url('sales/add_deliver'); ?>" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_deliver'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="#" id="excel" data-action="export_excel">
                            <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" class="bpo" title="<?= $this->lang->line('delete_delivers'); ?>"
                            data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></a> <button class='btn bpo-close'><?= lang('no'); ?></button>"
                            data-html="true" data-placement="left">
                            <i class="fa fa-trash-o"></i> <?= lang('delete_delivers'); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <?php if (!empty($warehouses)) { ?>
                <div class="dropdown">
                    <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-building-o"></i>
                        <span><?= $warehouse_id ? $warehouse->name : lang('all_warehouses'); ?></span>
                        <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right tasks-menus" role="menu">
                        <li><a href="<?= admin_url('sales/delivers'); ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses'); ?></a></li>
                        <li class="divider"></li>
                        <?php foreach ($warehouses as $wh) {
                            echo '<li><a href="' . admin_url('sales/delivers/' . $wh->id) . '"><i class="fa fa-building"></i>' . $wh->name . '</a></li>';
                        } ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
    </div>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="QUData" class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>
                            <th><?= lang('reference_no'); ?></th>
                            <th><?= lang('biller'); ?></th>
                            <th><?= lang('customer'); ?></th>
                            <th><?= lang('note'); ?></th>
                            <th><?= lang('total'); ?></th>
                            <th><?= lang('Status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:115px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10"
                                class="dataTables_empty"><?= lang('loading_data'); ?></td>
                        </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:115px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php if ($Owner || $GP['bulk_actions']) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            PrPDF('pr_pdf','<?= lang('deliveries'); ?>');
		    return false;
        });
    });
</script>
