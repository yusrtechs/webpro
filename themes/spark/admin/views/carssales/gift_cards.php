<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
	function p_status(x) {
	if (x == '0') {
        return '<div class="text-center"><span class="row_status label label-warning">' + lang['Free_of_charge'] + '</span></div>';
    } else if (x == '1') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang['Paid'] + '</span></div>';
    }
	}
    
	function s_status(x) {
	if (x == '0') {
        return '<div class="text-center"><span class="row_status label label-danger">' + lang['inactive'] + '</span></div>';
    } else if (x == '1') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang['active'] + '</span></div>';
    }
	}

        $('#GCData').dataTable({
            "aaSorting": [[3, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('carssales/getGiftCards') ?>',
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
            }, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, null, null, {"mRender": p_status}, {"mRender": decimalFormat}, {"mRender": s_status}, {"mRender": fsd}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('carssales/gift_card_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-gift"></i><?= lang('gift_cards') ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip"  data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?php echo admin_url('carssales/add_gift_card'); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus"></i> <?= lang('add_gift_card') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" id="delete" data-action="delete">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_gift_cards') ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo $this->lang->line('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="GCData" class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo $this->lang->line('card_no'); ?></th>
                            <th><?php echo $this->lang->line('value'); ?></th>
                            <th><?php echo $this->lang->line('balance'); ?></th>
                            <th><?php echo $this->lang->line('created_by'); ?></th>
                            <th><?php echo $this->lang->line('customer'); ?></th>
                            <th><?php echo $this->lang->line('Paid_Status'); ?></th>
                            <th><?php echo $this->lang->line('Amount'); ?></th>
                            <th><?php echo $this->lang->line('Status'); ?></th>
                            <th><?php echo $this->lang->line('expiry'); ?></th>
                            <th style="width:65px;"><?php echo $this->lang->line('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>
<script language="javascript">
    $(document).ready(function () {

        $('#delete').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#excel').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#pdf').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

    });
</script>
