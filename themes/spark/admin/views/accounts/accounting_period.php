<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.accounting-period td {
    text-align: center !important;
}
.accounting_period_link {
    cursor: pointer;
}
</style>

<script>
    $(document).ready(function () {


		function accounting_period_status(x) {
	//alert(lang.approved)
	//alert(lang)
	/*$.each(lang, function () {
            alert(this);
	    });*/

	if (x == null) {
        return '';
    } else if (x == '2') {
        return '<div class="text-center"><span class="row_status label label-info">' + "<?= lang('Pending') ?>" + '</span></div>';
    } else if (x == '1') {
        return '<div class="text-center"><span class="row_status label label-success">' + "<?= lang('Open') ?>" + '</span></div>';
    } else if (x == '0') {
        return '<div class="text-center"><span class="row_status label label-danger">' + "<?= lang('Close') ?>" + '</span></div>';
    } else {
        return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
    }
}




		function get_lang(x) {
			var y=x;
			if(x=="cash")
				y="<?= lang('cash') ?>";
			if(x=="CC")
				y="<?= lang('CC') ?>";
			if(x=="Cheque")
				y="<?= lang('cheque') ?>";
			if(x=="bank_transfer")
				y="<?= lang('Bank_transfers') ?>";

            return "<div>"+y+"</div>"; // onclick=\"this.select();\"
        }


        oTable = $('#UnitTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100,500, -1], [10, 25, 50, 100,500, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('accounts/getAccountingPeriod') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null,null, null, {"mRender": accounting_period_status}],
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "accounting_period_link";
                return nRow;
            },
		});
    });
</script>
<?= admin_form_open('system_settings/unit_actions', 'id="action-form"') ?>

<div class="spark-accounting-period-page">
    <div class="spark-accounting-period-header">
        <div class="spark-accounting-period-header-start">
            <div class="spark-accounting-period-header-icon">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="spark-accounting-period-header-info">
                <h1 class="spark-accounting-period-title">
                    <?= lang('Accounting_Period'); ?>
                </h1>
                <p class="spark-accounting-period-subtitle">
                    <?= lang('manage_accounting_period_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-accounting-period-header-actions">
            <?php
            $count = $this->accounts_model->getAllAccountingPeriod()->count;
            if ($Owner || $Admin) {
                if ($count == 1) {
                    ?>
                    <a href="<?php echo admin_url('accounts/add_accounting_period'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('Add_accounting_period'); ?></span>
                    </a>
                    <?php
                } else {
                    ?>
                    <div class="dropdown">
                        <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                            <span><?= lang('actions'); ?></span>
                            <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                            <?php if (isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset == 1 && $accounts_settings->isAutoDepreciation == 0) { ?>
                                <li>
                                    <a href="<?php echo admin_url('accounts/depreciationoffixedassets'); ?>" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-lock"></i> <?= lang('Depreciation_of_fixed_assets'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="<?php echo admin_url('accounts/closingaccountingperiods'); ?>" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-lock"></i> <?= lang('Closing_accounting_periods'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url('accounts/closeincomestatement'); ?>" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-lock"></i> <?= lang('Fiscal_year_closing'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url('accounts/fiscal_year_closing'); ?>" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-unlock"></i> <?= lang('Open_a_new_fiscal_year'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="UnitTable" class="table table-striped jambo_table bulk_action reports-table accounting-period">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th><?= lang('code'); ?></th>
                                <th><?= lang('Year'); ?></th>
                                <th><?= lang('Start_Period'); ?></th>
                                <th><?= lang('End_Period'); ?></th>
                                <th><?= lang('Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="dataTables_empty">
                                    <?= lang('loading_data_from_server') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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

    $(document).on('click', '.row_status', function(e) {
        e.preventDefault;
        var row = $(this).closest('tr');
        var id = row.attr('id');
            $('#myModal').modal({ remote: site.base_url + 'accounts/update_accounting_period_status/' + id });
            $('#myModal').modal('show');
        return false;
    });

	$(document).on('click', '.accounting_period_link td:not(:first-child, :nth-last-child(3), :last-child)', function() {
        $('#myModal').modal({
            remote:
                site.base_url +
                'accounts/view_accounting_period/' +
                $(this)
                    .parent('.accounting_period_link')
                    .attr('id'),
        });
        $('#myModal').modal('show');
    });


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

