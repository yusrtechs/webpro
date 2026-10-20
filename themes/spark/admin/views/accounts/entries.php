<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
			if(x=="Fixed_asset_depreciation")
				y="<?= lang('Fixed_asset_depreciation') ?>";

            return "<div>"+y+"</div>"; // onclick=\"this.select();\"
        }

		var accounts_map = <?php
			$all_accs = $this->accounts_model->getAllDetailsAccounts();
			$acc_names = [];
			if (!empty($all_accs)) {
				foreach ($all_accs as $a) {
					$acc_names[$a->id] = $a->accountName;
				}
			}
			echo json_encode($acc_names);
		?>;

		function get_account(x) {
			if (!x) return '';
			var clean = (x + '').replace(/["\[\]]/g, '');
			var parts = clean.split('_');
			var accounts = parts[0] ? parts[0].split(',') : [];
			var balances = parts[1] ? parts[1].split(',') : [];
			var content = '';
			for (var r = 0; r < accounts.length; r++) {
				var accId = accounts[r].trim();
				if (accId === '') continue;
				var accName = accounts_map[accId] || accId;
				var bal = balances[r] !== undefined ? formatMoney(balances[r]) : '';
				content += accName + " <br>" + bal + " <br>";
			}
			return content;
		}

        oTable = $('#UnitTable').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('accounts/getEntries') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {

                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });

                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});


            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null, null, {"bSortable": false, "mRender": get_account}, {"bSortable": false, "mRender": get_account}, {"mRender": row_status}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				//alert(aData[0])
                nRow.id = aData[0];
                nRow.className = "entries_link";
                return nRow;
            },
        });
    });
</script>
<?= admin_form_open('system_settings/unit_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('Entries'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?php echo admin_url('accounts/add_entry'); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus"></i> <?= lang('Add_entry') ?>
                            </a>
                        </li>
                        <!--<li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" id="delete" data-action="delete">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_units') ?>
                            </a>
                        </li>-->
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="UnitTable" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th><?= lang('Entry_number'); ?></th>
                                <th><?= lang('Entry_date'); ?></th>
                                <th><?= lang('Entry_type'); ?></th>
                                <th class="col-xs-2"><?= lang('Debit'); ?></th>
                                <th class="col-xs-2"><?= lang('Credit'); ?></th>
                                <th><?= lang('approved'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty">
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

