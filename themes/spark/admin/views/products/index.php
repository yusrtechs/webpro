<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css" media="screen">
    #PRData td:nth-child(7) {
        text-align: right;
    }
    <?php if ($Owner || $Admin || $this->session->userdata('show_cost')) {
    ?>
    #PRData td:nth-child(9) {
        text-align: right;
    }
    <?php
} if ($Owner || $Admin || $this->session->userdata('show_price')) {
        ?>
    #PRData td:nth-child(8) {
        text-align: right;
    }
    <?php
    } ?>
</style>
<?php
$v = '';
if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}
if ($this->input->post('product')) {
    $v .= '&product=' . $this->input->post('product');
}
if ($this->input->post('category')) {
    $v .= '&category=' . $this->input->post('category');
}
if ($this->input->post('product_type')) {
    $v .= '&product_type=' . $this->input->post('product_type');
}
if ($this->input->post('brand')) {
    $v .= '&brand=' . $this->input->post('brand');
}
if ($this->input->post('subcategory')) {
    $v .= '&subcategory=' . $this->input->post('subcategory');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
	$warehouse_id= $this->input->post('warehouse');
}else if($this->session->userdata('warehouse_id') &&  !$Owner && !$Admin){
$v .= "&warehouse=" . $this->session->userdata('warehouse_id');
}
if ($supplier) {
    $v .= '&supplier=' . $supplier->id;
}

//'sAjaxSource': '<?= admin_url('products/getProducts' . ($warehouse_id ? '/' . $warehouse_id : '') . ($supplier ? '?supplier=' . $supplier->id : ''))

?>
<script>
    var oTable;
    $(document).ready(function () {
        oTable = $('#PRData').dataTable({
            "aaSorting": [ [0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getProducts/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				aData[2]=(iDisplayIndex+1);
				//alert(iDisplayIndex)
                var oSettings = oTable.fnSettings();
				$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);

                nRow.id = aData[0];
                nRow.className = "product_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [
                null,{"bSortable": false, "mRender": checkbox}, {"bSortable": false,"mRender": img_hl},null,null, null, null, null, <?php if ($Owner || $Admin) {
        echo '{"mRender": currencyFormat}, {"mRender": currencyFormat},';
    } else {
        if ($this->session->userdata('show_cost')) {
            echo '{"mRender": currencyFormat},';
        }
        if ($this->session->userdata('show_price')) {
            echo '{"mRender": currencyFormat},';
        }
    } ?> {"mRender": formatQuantity}, null, <?php if (!$warehouse_id || !$Settings->racks) {
        echo '{"bVisible": false},';
    } else {
        echo '{"bSortable": true},';
    } ?> {"mRender": formatQuantity}, {"bSortable": false}
            ]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Sr#');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('code');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Item_no');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
            <?php $col = 7;
            if ($Owner || $Admin) {
                echo '{column_number : 8, filter_default_label: "[' . lang('cost') . ']", filter_type: "text", data: [] },';
                echo '{column_number : 9, filter_default_label: "[' . lang('price') . ']", filter_type: "text", data: [] },';
                $col += 2;
            } else {
                if ($this->session->userdata('show_cost')) {
                    $col++;
                    echo '{column_number : ' . $col . ', filter_default_label: "[' . lang('cost') . ']", filter_type: "text", data: [] },';
                }
                if ($this->session->userdata('show_price')) {
                    $col++;
                    echo '{column_number : ' . $col . ', filter_default_label: "[' . lang('price') . ']", filter_type: "text, data: []" },';
                }
            }
            ?>
            {column_number: <?php $col++; echo $col; ?>, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
            {column_number: <?php $col++; echo $col; ?>, filter_default_label: "[<?=lang('unit');?>]", filter_type: "text", data: []},
            <?php $col++; if ($warehouse_id && $Settings->racks) {
                echo '{column_number : ' . $col . ', filter_default_label: "[' . lang('rack') . ']", filter_type: "text", data: [] },';
            } ?>
            {column_number: <?php $col++; echo $col; ?>, filter_default_label: "[<?=lang('alert_quantity');?>]", filter_type: "text", data: []},
        ], "footer");

    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();
        $(document).on('click', '.toggle_down, #toggle-filter-btn', function (e) {
            e.preventDefault();
            $("#form").slideToggle();
            return false;
        });
        $(document).on('click', '.toggle_up', function (e) {
            e.preventDefault();
            $("#form").slideUp();
            return false;
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) {
    ?>
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: scdata
                    });
                }
            }
        });
        <?php
} ?>
    });
</script>
   <!-- <?php if ($Owner || $GP['bulk_actions']) {
                                        echo admin_form_open('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : ''), 'id="action-form"');
                                    } ?>
                                    -->
<div class="spark-branches-page spark-customers-page spark-products-page">

  <!-- 1. Page Header (Matching Customers / Branches / Warehouses Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-products-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-products-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-products-header-icon">
        <i class="fa fa-cubes"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-products-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-products-title">
          <?= lang('products') . ($warehouse_id ? ' (' . $warehouse->name . ')' : '') . ($supplier ? ' (' . lang('supplier') . ': ' . ($supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name) . ')' : ''); ?>
        </h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-products-subtitle">
          <?= lang('manage_products_desc'); ?>
        </p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-products-header-actions">
      <!-- Filter Toggle Button -->
      <button type="button" class="spark-btn-secondary-action toggle_down" id="toggle-filter-btn" title="<?= lang('filter'); ?>">
        <i class="fa fa-filter"></i>
        <span><?= lang('filter'); ?></span>
      </button>

      <!-- Add Product Primary Button -->
      <?php if ($Owner || $Admin || $GP['products-add']) { ?>
      <a href="<?= admin_url('products/add'); ?>" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_product'); ?></span>
      </a>
      <?php } ?>

      <!-- Actions Dropdown Button -->
      <div class="dropdown" style="display: inline-block;">
        <button class="spark-btn-secondary-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-tasks"></i>
          <span><?= lang('actions'); ?></span>
          <i class="fa fa-angle-down" style="font-size: 11px; margin-inline-start: 4px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
          <?php if ($Owner || $Admin || $GP['products-add']) { ?>
          <li>
            <a href="<?= admin_url('products/add'); ?>">
              <i class="fa fa-plus-circle"></i> <?= lang('add_product'); ?>
            </a>
          </li>
          <?php } ?>
          <?php if (!$warehouse_id) { ?>
          <li>
            <a href="<?= admin_url('products/update_price'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-file-excel-o"></i> <?= lang('update_price'); ?>
            </a>
          </li>
          <?php } ?>
          <li>
            <a href="#" id="labelProducts" data-action="labels">
              <i class="fa fa-print"></i> <?= lang('print_barcode_label'); ?>
            </a>
          </li>
          <li>
            <a href="#" id="sync_quantity" data-action="sync_quantity">
              <i class="fa fa-arrows-v"></i> <?= lang('sync_quantity'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_products'); ?></b>"
               data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></button> <button class='btn bpo-close'><?= lang('no'); ?></button>" data-html="true" data-placement="left">
              <i class="fa fa-trash-o"></i> <?= lang('delete_products'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

<?php echo admin_form_open('products/index', 'id="action-form"'); ?>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>


                <div id="form">
                       <!-- <?php if ($Owner || $GP['bulk_actions']) {
                                        echo admin_form_open('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : ''), 'id="action-form"');
                                    } ?>
 -->
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
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
                                                if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }else{
                                            $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                        }
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                        </div>
                                        <?php }
                                        } ?>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('product', 'suggest_product'); ?>
                                <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ''), 'class="form-control" id="suggest_product"'); ?>
                                <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : '' ?>" id="report_product_id"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('category', 'category') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('category');
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ''), 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('subcategory', 'subcategory') ?>
                                <div class="controls" id="subcat_data"> <?php
                                    echo form_input('subcategory', (isset($_POST['subcategory']) ? $_POST['subcategory'] : ''), 'class="form-control" id="subcategory"  placeholder="' . lang('select_category_to_load') . '"');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('brand', 'brand') ?>
                                <?php
                                $bt[''] = lang('select') . ' ' . lang('brand');
                                foreach ($brands as $brand) {
                                    $bt[$brand->id] = $brand->name;
                                }
                                echo form_dropdown('brand', $bt, (isset($_POST['brand']) ? $_POST['brand'] : ''), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

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

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="product_type"><?= lang('product_type'); ?></label>
                            <?php
                                $opts = ['' => lang('select') . ' ' . lang('product_type'), 'standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service'), 'manufacturing' => lang('manufacturing')];
                                echo form_dropdown('product_type', $opts, (isset($_POST['product_type']) ? $_POST['product_type'] :  ''), 'class="form-control" id="product_type" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('product_type') . '"');
							?>
                            </div>
                        </div>



                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>

                </div>

		        <div class="clearfix"></div>









                <div class="table-responsive">
                    <table id="PRData" class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Sr#') ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line('image'); ?></th>
                            <th><?= lang('code') ?></th>
                            <th><?= lang('Item_no') ?></th>
                            <th style="min-width:250px;"><?= lang('name') ?></th>
                            <th><?= lang('brand') ?></th>
                            <th><?= lang('category') ?></th>
                            <?php
                            if ($Owner || $Admin) {

                                echo '<th>' . lang('cost') . '</th>';
                                echo '<th>' . lang('price') . '</th>';
                            } else {
                                if ($this->session->userdata('show_cost')) {
                                    echo '<th>' . lang('cost') . '</th>';
                                }
                                if ($this->session->userdata('show_price')) {
                                    echo '<th>' . lang('price') . '</th>';
                                }
                            }
                            ?>
                            <th><?= lang('quantity') ?></th>
                            <th><?= lang('unit') ?></th>
                            <th><?= lang('rack') ?></th>
                            <th style="min-width:100px;"><?= lang('alert_quantity') ?></th>
                            <th style="min-width:65px; text-align:center;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;"></th>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line('image'); ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php
                            if ($Owner || $Admin) {
                                echo '<th></th>';
                                echo '<th></th>';
                            } else {
                                if ($this->session->userdata('show_cost')) {
                                    echo '<th></th>';
                                }
                                if ($this->session->userdata('show_price')) {
                                    echo '<th></th>';
                                }
                            }
                            ?>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style="width:65px; text-align:center;"><?= lang('actions') ?></th>
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
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php
                            } ?>
</div>
<?php if(!$warehouse_id) { ?>
<style>
#PRData td:nth-child(8) {
        display: none;
    }

#PRData th:nth-child(8) {
        display: none;
    }
</style>
<?php } ?>


<script>
$(document).ready(function () {

$('#submit_report').click(function (e) {
    $('#action-form').attr('action', '<?= admin_url('products/index') ?>');
    $('#action-form-submit').trigger('click');
});
$('#labelProducts').click(function (e) {
    e.preventDefault();
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
    $('#action-form-submit').trigger('click');
});
$('#sync_quantity').click(function (e) {
    e.preventDefault();
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
    $('#action-form-submit').trigger('click');
});
$('#excel').click(function (e) {
    e.preventDefault();
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
    $('#action-form-submit').trigger('click');
});
$('#pdf').click(function (e) {
    e.preventDefault();
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
    $('#action-form-submit').trigger('click');
});
$(document).on('click', '#delete', function (e) {
    e.preventDefault();
    $('#form_action').val($(this).attr('data-action'));
    $('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
    $('#action-form-submit').trigger('click');
});
});

</script>

