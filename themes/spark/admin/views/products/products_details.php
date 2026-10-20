<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= $assets ?>/styles/multi.min.css" />
<script src="<?= $assets ?>/js/multi.min.js"></script>

<style>

            .multicontainer {
                box-sizing: border-box;
                margin: 0 auto;
                max-width: 100%;
                padding: 0 20px;
                width: 100%;
            }
        </style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Branches_list').' / '.lang('warehouses') . ' (' . ($product ? $product->name : lang("all").' '.lang("products"))  . ')'; ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/products_details/' . $product->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
            <div class="form-group multicontainer">

<?php
if(!$product){
    ?>







                   <div class="col-md-6 col-sm-6">
                    <div class="controls " >
                                                <?= lang('from_warehouse', 'One_unit'); ?>

                                                    <?php
                                                        $pu[''] = lang('select') . ' ' . lang('warehouse');
                                                        foreach ($this->site->getAllWarehouses() as $bu) {
                                                            $pu[$bu->id] = $bu->name ;
                                                        }

                                                        ?>
                                                        <?= form_dropdown('from_warehouse', $pu, set_value('warehouse', ($product ? $product->unit : '')), '  class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>



                                                        </div>
                                                        </div>
                    <div class="col-md-6 col-sm-6">
                    </div>

                    <?php

                    }
                    ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo lang('Main_branch', 'Main_branch').' / '.lang('warehouse','warehouse') ; ?>
                        <div class="controls">





                        <select
                    multiple="multiple"
                    name="wh_option[]"
                    id="products_details"
                >


                    <?php

        foreach ($mainBranches as $mainBranch) {





                    ?>

                            <optgroup label="<?= $mainBranch->name ?>">

                            <?php
                            foreach ($this->site->getWarehousesBybrId($mainBranch->id) as $warehous) {

                                $flage=0;
                        foreach ($warehousBranches as $warehousBranch) {

                            if($warehous->id==$warehousBranch->warehouse_id )
                            $flage=1;

                        }
                                ?>

                    <option <?= $flage==1 ?  'selected="selected" disabled="disabled"' : ''; ?> value="<?= $warehous->id ?>"><?= $warehous->name ?></option>
                    <?php
                    }
                    ?>
                </optgroup>
                    <?php
                    }
                    ?>

                </select>
                        </div>
                    </div>


                </div>

            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('products_details', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script>
            var select = document.getElementById("products_details");
            multi(select, {
                non_selected_header: "<?= lang('From_branches').' / '.lang('warehouses'); ?>",
                selected_header: "<?= lang('To_branches').' / '.lang('warehouses'); ?>"
            });

            $("#s2id_products_details").css("display", "none");
        </script>

