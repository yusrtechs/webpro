<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        /*$("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_model_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_model_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_model_to_load') ?>'}
            ]
        });*/
        $('#brand_id1').change(function () {
            var v = $(this).val();
            var uopt = $("#model_id1");
           // console.log(uopt);
                            
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('cars/getModelsByBrandId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#model_id1')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
    .val('')
;

$('#model_id1').val('').trigger('change.select2');
				
                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);
            
        });

                            //$("#model_id1").select2("destroy").empty().attr("placeholder", "<?= lang('select_model_id1') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select_category_to_load') ?>",
                                //data: scdata
                            
                        
                        } else {
                            $("#model_id1").select2("destroy").empty().attr("placeholder", "<?= lang('no_model_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_model_id') ?>",
                                data: [{id: '', text: '<?= lang('no_model_id') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#model_id1").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
    });
</script>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_category'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/add_category', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', '', 'class="form-control" id="code"'); ?>
            </div>
            <div class="form-group">
                <?= lang('Brand_name', 'brand_id') ?>
                
                <?php
                $br[''] = lang('select') . ' ' . lang('Brand_name');
                foreach ($brands as $brand) {
                    $br[$brand->id] = $brand->brand_name;
                }
                echo form_dropdown('brand_id', $br, '', 'class="form-control select" id="brand_id1" style="width:100%"')
                ?>
             
            </div>
            
            <div class="form-group">
                <?= lang('Car_Type', 'model_id') ?>
                <?php
                $br[''] = lang('select') . ' ' . lang('Car_Type');
                foreach ($models as $model) {
                    $br[$model->id] = $model->model_name;
                }
                echo form_dropdown('model_id', '', '', 'class="form-control select" id="model_id1" style="width:100%"')
                ?>
            </div> 
                       
			
            <div class="form-group">
                <?= lang('category_name', 'categ_name'); ?>
                <?= form_input('categ_name', '', 'class="form-control gen_slug" id="categ_name" required="required"'); ?>
            </div>

          
            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description'), 'class="form-control tip" id="description" '); ?>
            </div>
           
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_category', lang('add_category'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        
    });
</script>
