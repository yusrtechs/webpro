<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<style>
    .label {
    display: inline;
    padding: .2em .6em .3em !important;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
</style>
<script>
    $(document).ready(function () {

        function op_status(x) {
            if (x == null) {
                return '';
            } else if (x == '1') {
                return '<div class="text-center"><span class="payment_status label label-danger"><?= lang("Destruction") ?></span></div>';
            } else if (x == '2') {
                return '<div class="text-center"><span class="payment_status label label-primary"><?= lang("Gifts_and_donations") ?></i></span></div>';
            } else {
                return '<div class="text-center"><span class="payment_status label label-default">' + x + '</span></div>';
            }
        }

        oTable = $('#dmpData2').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getdestruction/' . ($warehouse ? $warehouse->id : '')); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"mRender": fld}, null, null,  {"mRender": op_status},null, {"mRender": decode_html}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "destruction_link2";
                return nRow;
            },
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('warehouse');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Operation_type');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('created_by');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('note');?>]", filter_type: "text", data: []},
        ], "footer");

        if (localStorage.getItem('remove_qals')) {
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
            localStorage.removeItem('remove_qals');
        }

        <?php if ($this->session->userdata('remove_qals')) {
    ?>
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
        <?php $this->sma->unset_data('remove_qals');
}
        ?>
    });
</script>

<?php if ($Owner || $GP['bulk_actions']) {
            echo admin_form_open('products/adjustment_actions', 'id="action-form"');
        }
?>
<?=$content_report?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('Inventory_operations') . ' (' . ($warehouse ? $warehouse->name : lang('all_warehouses')) . ')'; ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">

                <li class="dropdown"><a href="<?=admin_url('products/destruction_inventory')?>"class="tip"  title="<?= lang('Inventory_operations') ?>"><i class="icon fa fa-plus"></i></a></li>

                <?php if (!empty($warehouses)) {
    ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang('warehouses') ?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?= admin_url('products/destruction') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                            <li class="divider"></li>
                            <?php
                            foreach ($warehouses as $warehouse) {
                                echo '<li><a href="' . admin_url('products/destruction/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                            } ?>
                        </ul>
                    </li>
                <?php
} ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="dmpData2" class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-2"><?= lang('date'); ?></th>
                            <th><?= lang('reference_no'); ?></th>
                            <th class="col-xs-2"><?= lang('warehouse'); ?></th>
                            <th><?= lang('Operation_type'); ?></th>
                            <th><?= lang('created_by'); ?></th>
                            <th class="col-xs-3"><?= lang('note'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="min-width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th><th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:75px; text-align:center;"><?= lang('actions'); ?></th>
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
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php
                            }
?>
