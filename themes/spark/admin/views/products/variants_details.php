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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Branches_list') . ' (' . ($variant ? $variant->name : lang("all").' '.lang("variants"))  . ')'; ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/variants_details/' . $variant->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">


                <div class="col-md-12 col-sm-12">


                    <div class="form-group multicontainer">


                        <?php echo lang('Main_branch', 'Main_branch'); ?>
                        <div class="controls">



                        <select
                    multiple="multiple"
                    name="br_option[]"
                    id="variants_details"
                >

                    >
                    <?php
                    foreach ($mainBranches as $mainBranch) {
                        $flage=0;
                        foreach ($variantBranches as $variantBranch) {
                            if($mainBranch->id==$variantBranch->brId )
                            $flage=1;

                        }


                    ?>
                    <option <?= $flage==1 ?  'selected="selected" disabled="disabled"' : ''; ?> value="<?= $mainBranch->id ?>"><?= $mainBranch->name ?></option>
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
            <?php echo form_submit('variants_details', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script>
            var select = document.getElementById("variants_details");
            multi(select, {
                non_selected_header: "<?= lang('From_branches'); ?>",
                selected_header: "<?= lang('To_branches'); ?>"
            });

            $("#s2id_variants_details").css("display", "none");
        </script>

