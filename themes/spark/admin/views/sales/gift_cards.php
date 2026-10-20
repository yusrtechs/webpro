<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Calculate real summary statistics from database
$total_cards   = $this->db->where('date >=', date('Y-m-').'01 00:00:00')->count_all_results('gift_cards');
$active_cards  = $this->db->where('status', 1)->count_all_results('gift_cards');
$expired_cards = $this->db->where('expiry <', date('Y-m-d'))->count_all_results('gift_cards');

$this->db->select_sum('value');
$q_val = $this->db->get('gift_cards')->row();
$total_val = $q_val ? ($q_val->value ?? 0) : 0;

$this->db->select_sum('balance');
$q_bal = $this->db->get('gift_cards')->row();
$total_bal = $q_bal ? ($q_bal->balance ?? 0) : 0;

$total_used = max(0, $total_val - $total_bal);

// Real template performance statistics fallback
if (!isset($gift_card_type_stats) || empty($gift_card_type_stats)) {
    $gift_card_type_stats = [
        'classic' => ['count' => 0, 'value' => 0, 'percentage' => 0],
        'golden'  => ['count' => 0, 'value' => 0, 'percentage' => 0],
        'elegant' => ['count' => 0, 'value' => 0, 'percentage' => 0],
        'modern'  => ['count' => 0, 'value' => 0, 'percentage' => 0],
    ];
}
?>

<script>
    $(document).ready(function () {
        function p_status(x) {
            if (x == '0') {
                return '<div class="text-center"><span class="row_status label label-warning" style="border-radius: 6px; padding: 4px 10px; font-weight: 700;">' + lang['Free_of_charge'] + '</span></div>';
            } else if (x == '1') {
                return '<div class="text-center"><span class="row_status label label-success" style="border-radius: 6px; padding: 4px 10px; font-weight: 700; background: #10b981;">' + lang['Paid'] + '</span></div>';
            }
        }

        function s_status(x) {
            if (x == '0') {
                return '<div class="text-center"><span class="row_status label label-danger" style="border-radius: 6px; padding: 4px 10px; font-weight: 700;">' + lang['inactive'] + '</span></div>';
            } else if (x == '1') {
                return '<div class="text-center"><span class="row_status label label-success" style="border-radius: 6px; padding: 4px 10px; font-weight: 700; background: #10b981;">' + lang['active'] + '</span></div>';
            }
        }

        function card_no_format(n) {
            if (!n) return '';
            return '<span style="font-family: monospace; font-weight: 700; color: var(--spark-text-primary, #0f172a); direction: ltr; display: inline-block;">' + n + '</span>';
        }

        $('#GCData').dataTable({
            "aaSorting": [[3, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getGiftCards') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [
                {"bSortable": false, "mRender": checkbox},
                {"mRender": card_no_format},
                {"mRender": currencyFormat},
                {"mRender": currencyFormat},
                null,
                null,
                {"mRender": p_status},
                {"mRender": decimalFormat},
                {"mRender": s_status},
                {"mRender": fsd},
                {"bSortable": false}
            ]
        });
    });
</script>

<?= admin_form_open('sales/gift_card_actions', 'id="action-form"') ?>

<div class="spark-giftcard-module">

    <!-- Emerald Header Banner (Matching React Screenshot) -->
    <div class="spark-giftcard-banner">
        <div class="spark-giftcard-banner-content">
            <div class="spark-giftcard-banner-badge">
                <i class="fa fa-gift"></i>
            </div>
            <div>
                <h1 class="spark-giftcard-banner-title"><?= lang('gift_cards'); ?></h1>
                <p class="spark-giftcard-banner-subtitle"><?= lang('gift_cards_list'); ?></p>
            </div>
        </div>

        <!-- Action Buttons in Banner -->
        <div class="flex items-center gap-2" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="<?= admin_url('sales/add_gift_card') ?>" data-toggle="modal" data-target="#myModal" class="btn btn-default" style="background: rgba(255,255,255,0.2); color: #fff; font-weight: 700; border: none; border-radius: 12px; padding: 8px 16px;">
                <i class="fa fa-plus-circle" style="margin-inline-end: 4px;"></i> <?= lang('add_gift_card') ?>
            </a>

            <a href="#" id="excel" data-action="export_excel" class="btn btn-default" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); color: #fff; border-radius: 12px; padding: 8px 14px; font-weight: 700;">
                <i class="fa fa-download" style="margin-inline-end: 4px;"></i> <?= lang('export_report'); ?>
            </a>

            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); color: #fff; border-radius: 12px; padding: 8px 14px; font-weight: 700;">
                    <i class="fa fa-tasks"></i> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>
                        <a href="<?php echo admin_url('sales/add_gift_card'); ?>" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-plus"></i> <?= lang('add_gift_card') ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="excel-opt" data-action="export_excel" onclick="$('#excel').trigger('click'); return false;">
                            <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" id="delete" data-action="delete">
                            <i class="fa fa-trash-o text-danger"></i> <?= lang('delete_gift_cards') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <div class="spark-giftcard-body">

        <!-- 4 KPI Summary Cards (Matching React Screenshot Exactly) -->
        <div class="spark-giftcard-stats">

            <!-- Card 1: New / Total Gift Cards (Emerald) -->
            <div class="spark-giftcard-stat-card">
                <div class="spark-giftcard-stat-left">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span class="spark-card-tag tag-emerald"><?= lang('Cards_NO'); ?></span>
                    </div>
                    <h3 class="spark-giftcard-stat-value" style="margin-top: 8px;"><?= number_format($total_cards); ?></h3>
                    <p class="spark-giftcard-stat-label"><?= lang('gift_cards'); ?></p>
                </div>
                <div class="spark-giftcard-stat-icon icon-teal">
                    <i class="fa fa-gift"></i>
                </div>
            </div>

            <!-- Card 2: Total Sales Value (Blue) -->
            <div class="spark-giftcard-stat-card">
                <div class="spark-giftcard-stat-left">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span class="spark-card-tag tag-blue"><?= lang('total'); ?></span>
                    </div>
                    <h3 class="spark-giftcard-stat-value" style="margin-top: 8px;"><?= $this->sma->formatMoney($total_val); ?></h3>
                    <p class="spark-giftcard-stat-label"> <?= lang('card_sales'); ?></p>
                </div>
                <div class="spark-giftcard-stat-icon icon-blue">
                    <i class="fa fa-credit-card"></i>
                </div>
            </div>

            <!-- Card 3: Used Balance (Amber) -->
            <div class="spark-giftcard-stat-card">
                <div class="spark-giftcard-stat-left">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span class="spark-card-tag tag-amber"><?= lang('used'); ?></span>
                    </div>
                    <h3 class="spark-giftcard-stat-value" style="margin-top: 8px;"><?= $this->sma->formatMoney($total_used); ?></h3>
                    <p class="spark-giftcard-stat-label"> <?= lang('amount_used'); ?></p>
                </div>
                <div class="spark-giftcard-stat-icon icon-amber">
                    <i class="fa fa-money"></i>
                </div>
            </div>

            <!-- Card 4: Expired Cards (Rose) -->
            <div class="spark-giftcard-stat-card">
                <div class="spark-giftcard-stat-left">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <span class="spark-card-tag tag-rose"><?= lang('expired'); ?></span>
                    </div>
                    <h3 class="spark-giftcard-stat-value" style="margin-top: 8px;"><?= number_format($expired_cards); ?></h3>
                    <p class="spark-giftcard-stat-label"><?= lang('expired_cards'); ?></p>
                </div>
                <div class="spark-giftcard-stat-icon" style="background: rgba(244, 63, 94, 0.15); color: #f43f5e;">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>

        </div>

        <!-- Performance by Card Type Section (Matching React Screenshot Exactly) -->
        <div class="spark-giftcard-perf-card">
            <h4 class="spark-perf-header">
                <i class="fa fa-line-chart text-success"></i> <?= lang('cards_performance_by_type'); ?>
            </h4>

            <!-- Row 1: Classic -->
            <div class="spark-perf-row">
                <div class="spark-perf-label-group">
                    <span class="spark-perf-dot" style="background: #0f3460;"></span>
                    <span class="spark-perf-name"><?= lang('classic'); ?></span>
                </div>
                <div class="spark-perf-track">
                    <div class="spark-perf-fill" style="width: <?= $gift_card_type_stats['classic']['percentage']; ?>%; background: linear-gradient(90deg, #1a1a2e, #0f3460);"></div>
                </div>
                <div class="spark-perf-metrics">
                    <span class="spark-perf-count"><?= sprintf('%02d', $gift_card_type_stats['classic']['count']); ?> <?= lang('card_unit'); ?></span>
                    <span class="spark-perf-amount"><?= $this->sma->formatMoney($gift_card_type_stats['classic']['value']); ?> </span>
                </div>
            </div>

            <!-- Row 2: Gold -->
            <div class="spark-perf-row">
                <div class="spark-perf-label-group">
                    <span class="spark-perf-dot" style="background: #d4a853;"></span>
                    <span class="spark-perf-name"><?= lang('gold'); ?></span>
                </div>
                <div class="spark-perf-track">
                    <div class="spark-perf-fill" style="width: <?= $gift_card_type_stats['golden']['percentage']; ?>%; background: linear-gradient(90deg, #2c1810, #d4a853);"></div>
                </div>
                <div class="spark-perf-metrics">
                    <span class="spark-perf-count"><?= sprintf('%02d', $gift_card_type_stats['golden']['count']); ?> <?= lang('card_unit'); ?></span>
                    <span class="spark-perf-amount"><?= $this->sma->formatMoney($gift_card_type_stats['golden']['value']); ?> </span>
                </div>
            </div>

            <!-- Row 3: Emerald -->
            <div class="spark-perf-row">
                <div class="spark-perf-label-group">
                    <span class="spark-perf-dot" style="background: #10b981;"></span>
                    <span class="spark-perf-name"><?= lang('elegant'); ?></span>
                </div>
                <div class="spark-perf-track">
                    <div class="spark-perf-fill" style="width: <?= $gift_card_type_stats['elegant']['percentage']; ?>%; background: linear-gradient(90deg, #1b4332, #10b981);"></div>
                </div>
                <div class="spark-perf-metrics">
                    <span class="spark-perf-count"><?= sprintf('%02d', $gift_card_type_stats['elegant']['count']); ?> <?= lang('card_unit'); ?></span>
                    <span class="spark-perf-amount"><?= $this->sma->formatMoney($gift_card_type_stats['elegant']['value']); ?> </span>
                </div>
            </div>

            <!-- Row 4: Modern -->
            <div class="spark-perf-row">
                <div class="spark-perf-label-group">
                    <span class="spark-perf-dot" style="background: #2d2d2d;"></span>
                    <span class="spark-perf-name"><?= lang('modern'); ?></span>
                </div>
                <div class="spark-perf-track">
                    <div class="spark-perf-fill" style="width: <?= $gift_card_type_stats['modern']['percentage']; ?>%; background: linear-gradient(90deg, #3d3d3d, #1a1a1a);"></div>
                </div>
                <div class="spark-perf-metrics">
                    <span class="spark-perf-count"><?= sprintf('%02d', $gift_card_type_stats['modern']['count']); ?> <?= lang('card_unit'); ?></span>
                    <span class="spark-perf-amount"><?= $this->sma->formatMoney($gift_card_type_stats['modern']['value']); ?> </span>
                </div>
            </div>

        </div>

        <!-- DataTable Container -->
        <div class="spark-giftcard-datatable-card" style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--spark-text-primary, #0f172a); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-list-alt text-success"></i> <?= lang('gift_cards_list'); ?>
                </h4>
            </div>

            <div class="table-responsive">
                <table id="GCData" class="table table-striped jambo_table bulk_action" style="width: 100%;">
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
                        <th style="width:65px; text-align: center;"><?php echo $this->lang->line('actions'); ?></th>
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
