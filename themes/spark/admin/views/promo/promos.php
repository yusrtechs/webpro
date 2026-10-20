<script>
$(document).ready(function () {
    'use strict';

    var currentStatusFilter = '';

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * Unified single source of truth for Promo Active/Inactive status computation
     * Rule:
     * - status = 0 -> inactive
     * - status = 1 and (end_date is null or 0000-00-00 or end_date >= today) -> active
     * - status = 1 and end_date < today -> inactive
     */
    function getPromoStatus(status, endDate) {
        var rawStatus = parseInt(status, 10);
        if (rawStatus === 0 || status === '0') {
            return 'inactive';
        }

        if (rawStatus === 1 || status === '1') {
            if (!endDate || endDate === '0000-00-00' || endDate === 'null' || endDate === '') {
                return 'active';
            }

            var today = new Date();
            today.setHours(0, 0, 0, 0);

            var parts = String(endDate).split('-');
            if (parts.length === 3) {
                var end = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                end.setHours(0, 0, 0, 0);
                return end >= today ? 'active' : 'inactive';
            }

            var end = new Date(endDate + 'T00:00:00');
            end.setHours(0, 0, 0, 0);
            return end >= today ? 'active' : 'inactive';
        }

        return 'inactive';
    }

    function renderPromoCards(data) {
        var $grid = $('#sparkPromoCardsGrid');
        $grid.empty();

        if (!data || data.length === 0) {
            var emptyHtml = '<div class="spark-promos-empty">' +
                '<div class="spark-promos-empty-icon">' +
                    '<i class="fa fa-gift"></i>' +
                '</div>' +
                '<h3 class="spark-promos-empty-title"><?= lang('no_promos_found'); ?></h3>' +
                '<p class="spark-promos-empty-desc"><?= lang('no_promos_category'); ?></p>' +
            '</div>';
            $grid.html(emptyHtml);
            return;
        }

        var editUrlBase = '<?= admin_url('promo/edit_promo/'); ?>';
        var deleteUrlBase = '<?= admin_url('promo/delete_promo/'); ?>';
        var deleteTitle = '<b><?= lang('delete_promo'); ?></b>';
        var deletePrompt = '<p><?= lang('r_u_sure'); ?></p>';
        var imSureText = '<?= lang('i_m_sure'); ?>';
        var noText = '<?= lang('no'); ?>';
        var activeText = '<?= lang('active'); ?>';
        var inactiveText = '<?= lang('inactive'); ?>';
        var editText = '<?= lang('edit'); ?>';
        var productLabel = '<?= lang('product'); ?>';
        var noProductText = '<em><?= lang('no_product_assigned'); ?></em>';
        var endsOnLabel = '<?= lang('ends_on'); ?>';
        var noEndDateText = '<?= lang('no_end_date'); ?>';

        var cardsHtml = '';

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            var promoId = row[0];
            var promoType = row[1] ? escapeHtml(row[1]) : '';
            var promoName = row[2] ? escapeHtml(row[2]) : '';
            var pCode = row[3] ? escapeHtml(row[3]) : '';
            var pName = row[4] ? escapeHtml(row[4]) : '';
            var rawStatus = String(row[5]).split('__')[0];
            var rawEndDate = row[6] ? String(row[6]).trim() : '';

            // Compute status using the exact rule
            var computedStatus = getPromoStatus(rawStatus, rawEndDate);
            var isActive = (computedStatus === 'active');

            var statusBadgeHtml = isActive
                ? '<span class="spark-promo-status-badge status-active"><i class="fa fa-check-circle"></i> ' + activeText + '</span>'
                : '<span class="spark-promo-status-badge status-inactive"><i class="fa fa-times-circle"></i> ' + inactiveText + '</span>';

            var popoverContent = deletePrompt +
                '<a class="btn btn-danger po-delete" href="' + deleteUrlBase + promoId + '">' + imSureText + '</a> ' +
                '<button class="btn po-close">' + noText + '</button>';

            var productContent = '';
            if (pCode || pName) {
                productContent = (pCode ? '<span class="spark-promo-product-code">' + pCode + '</span> ' : '') +
                                 (pName ? '<span class="spark-promo-product-name">' + pName + '</span>' : '');
            } else {
                productContent = '<span class="spark-promo-product-name">' + noProductText + '</span>';
            }

            var hasEndDate = (rawEndDate && rawEndDate !== '0000-00-00' && rawEndDate !== 'null');
            var endDateDisplay = hasEndDate ? rawEndDate : noEndDateText;
            var endDateClass = hasEndDate ? (isActive ? '' : 'is-expired') : 'is-none';

            cardsHtml += '<div class="spark-promo-card" data-id="' + promoId + '" data-status="' + computedStatus + '">' +
                '<div class="spark-promo-card-header">' +
                    '<div class="spark-promo-type-wrap">' +
                        '<div class="spark-promo-type-icon">' +
                            '<i class="fa fa-gift"></i>' +
                        '</div>' +
                        (promoType ? '<span class="spark-promo-type-badge">' + promoType + '</span>' : '') +
                    '</div>' +
                    '<div class="spark-promo-status-wrap">' +
                        statusBadgeHtml +
                    '</div>' +
                '</div>' +

                '<div class="spark-promo-card-body">' +
                    '<h3 class="spark-promo-card-title" title="' + promoName + '">' + promoName + '</h3>' +
                    '<div class="spark-promo-product-box">' +
                        '<div class="spark-promo-product-label">' +
                            '<i class="fa fa-cube"></i> ' + productLabel +
                        '</div>' +
                        '<div class="spark-promo-product-content">' +
                            productContent +
                        '</div>' +
                    '</div>' +
                    '<div class="spark-promo-date-row">' +
                        '<div class="spark-promo-date-item">' +
                            '<span class="spark-promo-date-label"><i class="fa fa-calendar-o"></i> ' + endsOnLabel + ':</span>' +
                            '<span class="spark-promo-date-val ' + endDateClass + '">' + escapeHtml(endDateDisplay) + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +

                '<div class="spark-promo-card-footer">' +
                    '<a href="' + editUrlBase + promoId + '" class="spark-promo-btn-edit tip" title="' + editText + '">' +
                        '<i class="fa fa-pencil-square-o"></i> <span>' + editText + '</span>' +
                    '</a>' +
                    '<a href="#" class="spark-promo-btn-delete po tip" title="' + deleteTitle + '" data-content="' + popoverContent.replace(/"/g, '&quot;') + '" rel="popover">' +
                        '<i class="fa fa-trash-o"></i>' +
                    '</a>' +
                '</div>' +
            '</div>';
        }

        $grid.html(cardsHtml);

        // Re-bind popovers and tooltips for the dynamically injected cards
        if ($.fn.popover) {
            $('.spark-promos-page .po').popover({
                html: true,
                placement: '<?= $Settings->user_language == 'arabic' ? 'right' : 'left'; ?>',
                trigger: 'manual'
            });
        }
    }

    // Initialize DataTables as the server-side data driver
    oTable = $('#UsrTable').dataTable({
        "aaSorting": [[2, "asc"], [3, "asc"]],
        "aLengthMenu": [[12, 24, 48, 96, -1], [12, 24, 48, 96, "<?= lang('all') ?>"]],
        "iDisplayLength": 12,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": '<?= admin_url('promo/getPromos') ?>',
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?= $this->security->get_csrf_token_name() ?>",
                "value": "<?= $this->security->get_csrf_hash() ?>"
            });

            // Pass server-side active/inactive computed filter
            if (currentStatusFilter) {
                aoData.push({
                    "name": "status_filter",
                    "value": currentStatusFilter
                });
            }

            $('#sparkPromoCardsGrid').html(
                '<div class="spark-promos-loading">' +
                    '<div class="spark-promos-spinner"></div>' +
                    '<span><?= lang('loading_data_from_server'); ?></span>' +
                '</div>'
            );

            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': function (json) {
                    fnCallback(json);
                }
            });
        },
        "aoColumns": [null, null, null, null, null, null, null, {"bSortable": false}],
        "fnDrawCallback": function () {
            var data = oTable.fnGetData();
            renderPromoCards(data);

            // Sync DataTables pagination and info into our custom pagination container
            var $dtPaginate = $('#UsrTable_paginate');
            var $dtInfo = $('#UsrTable_info');

            if ($dtPaginate.length) {
                $('#sparkPromoPaginationWrap').empty().append($dtPaginate.contents());
                // Re-bind click handlers for cloned pagination
                $('#sparkPromoPaginationWrap .paginate_button').on('click', function (e) {
                    e.preventDefault();
                    if ($(this).hasClass('disabled') || $(this).hasClass('current')) return;
                    if ($(this).hasClass('next')) {
                        oTable.fnPageChange('next');
                    } else if ($(this).hasClass('previous')) {
                        oTable.fnPageChange('previous');
                    } else {
                        var pageNum = parseInt($(this).text(), 10) - 1;
                        if (!isNaN(pageNum)) {
                            oTable.fnPageChange(pageNum);
                        }
                    }
                });
            }

            if ($dtInfo.length) {
                $('#sparkPromoInfo').text($dtInfo.text());
            }

            // Update Tab Count dynamically from the response
            var oSettings = oTable.fnSettings();
            var totalRecords = oSettings.fnRecordsDisplay();
            $('#sparkTabCount').text(totalRecords);
        }
    });

    // Server-Side Tab Navigation Handling
    $('.spark-promos-tab').on('click', function (e) {
        e.preventDefault();
        $('.spark-promos-tab').removeClass('active');
        $(this).addClass('active');

        var status = $(this).attr('data-status');
        currentStatusFilter = status || '';

        // Trigger Server-Side redraw with the new status_filter
        oTable.fnDraw();
    });

    // Real-time Search Box with Debounce
    var searchTimer = null;
    $('#sparkPromoSearch').on('input keyup', function () {
        var query = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            oTable.fnFilter(query);
        }, 300);
    });

    // Page Length Selector
    $('#sparkPromoPageSize').on('change', function () {
        var size = parseInt($(this).val(), 10);
        oTable.fnSettings()._iDisplayLength = size;
        oTable.fnDraw();
    });
});
</script>

<?php if ($Owner) {
    echo admin_form_open('prmo/promos', 'id="action-form"');
} ?>

<div class="spark-promos-page">

  <!-- 1. Page Header -->
  <div class="spark-promos-header">
    <div class="spark-promos-header-start">
      <div class="spark-promos-header-icon">
        <i class="fa fa-gift"></i>
      </div>
      <div class="spark-promos-header-info">
        <h1 class="spark-promos-title"><?= lang('Promos_list'); ?></h1>
        <p class="spark-promos-subtitle"><?= lang('manage_promos_desc'); ?></p>
      </div>
    </div>

    <div class="spark-promos-header-actions">
      <?php if ($Owner) { ?>
      <a href="<?= admin_url('promo/add_promo'); ?>" class="spark-btn-primary-action">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_promo'); ?></span>
      </a>
      <?php } ?>
    </div>
  </div>

  <!-- 2. Controls & Tabs Card -->
  <div class="spark-promos-controls-card">
    <div class="spark-promos-controls-row">
      <!-- Tabs (All, Active, Inactive) -->
      <div class="spark-promos-tabs" role="tablist">
        <button type="button" class="spark-promos-tab active" data-tab="all" data-status="">
          <i class="fa fa-tags"></i>
          <span><?= lang('all_promos'); ?></span>
          <span class="spark-promos-tab-badge" id="sparkTabCount">0</span>
        </button>
        <button type="button" class="spark-promos-tab" data-tab="active" data-status="active">
          <i class="fa fa-check-circle"></i>
          <span><?= lang('active_promos'); ?></span>
        </button>
        <button type="button" class="spark-promos-tab" data-tab="inactive" data-status="inactive">
          <i class="fa fa-times-circle"></i>
          <span><?= lang('inactive_promos'); ?></span>
        </button>
      </div>

      <!-- Search & Page Size Controls -->
      <div class="spark-promos-search-actions">
        <div class="spark-promos-search-wrap">
          <i class="fa fa-search"></i>
          <input type="text" id="sparkPromoSearch" class="spark-promos-search-input" placeholder="<?= lang('search_promos_placeholder'); ?>">
        </div>

        <select id="sparkPromoPageSize" class="spark-promos-length-select" title="<?= lang('rows_per_page'); ?>">
          <option value="12" selected>12</option>
          <option value="24">24</option>
          <option value="48">48</option>
          <option value="96">96</option>
        </select>
      </div>
    </div>
  </div>

  <!-- 3. Promotional Cards Grid -->
  <div id="sparkPromoCardsGrid" class="spark-promos-grid">
    <div class="spark-promos-loading">
      <div class="spark-promos-spinner"></div>
      <span><?= lang('loading_data_from_server'); ?></span>
    </div>
  </div>

  <!-- 4. Pagination & Info Bar -->
  <div class="spark-promos-pagination-card">
    <div class="spark-promos-info-text" id="sparkPromoInfo">
      <?= lang('loading_data_from_server'); ?>
    </div>
    <div class="spark-promos-pagination-wrap" id="sparkPromoPaginationWrap">
    </div>
  </div>

  <!-- 5. Hidden DataTables Driver (Preserves 100% of Backend Data & AJAX Pipeline) -->
  <div style="display: none !important;">
    <table id="UsrTable" class="table">
      <thead>
        <tr>
          <th><?= lang('Sr#'); ?></th>
          <th><?= lang('Promo_type'); ?></th>
          <th><?= lang('Promo_name'); ?></th>
          <th><?= lang('product_code'); ?></th>
          <th><?= lang('product_name'); ?></th>
          <th><?= lang('Status'); ?></th>
          <th><?= lang('end_date'); ?></th>
          <th><?= lang('actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="8"><?= lang('loading_data_from_server'); ?></td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>

    <script language="javascript">
        $(document).ready(function () {
            $('#set_admin').click(function () {
                $('#usr-form-btn').trigger('click');
            });
        });
    </script>
<?php } ?>