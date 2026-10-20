<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_object'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('modules/add_object/'.$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', '', 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'name_en'); ?>
                <?= form_input('name_en', '', 'class="form-control gen_slug" id="name_en" required="required"'); ?>
            </div>


            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', '', 'class="form-control tip" id="description" '); ?>
            </div>

            <div class="form-group sort_ord2">
                <?= lang('sub_menu', 'sub_menu'); ?>
                <?php
                $sort_ord[''] = lang('no');
                foreach ($sub_menu as $menu) {
					$sort_ord[$menu->sort_ord] = $menu->name;
                }
                echo form_dropdown('sort_ord2', $sort_ord, $object->sort_ord, 'class="form-control  input-tip select" id="sort_ord2"  style="width:100%"')
                ?>
            </div>


         </div>

         <div class="col-md-6">

            <div class="form-group all">
                <?= lang('Directory', 'directory'); ?>
                <?= form_input('directory', '', 'class="form-control tip" id="directory" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('type', 'type'); ?>
                <?php
                $t_opt = [1 => lang('menu_item'), 2 => lang('sub_menu')];
                echo form_dropdown('type', $t_opt, (isset($_POST['type']) ? $_POST['type'] : ''), 'id="type"" class="form-control input-tip select" style="width:100%;"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Status', 'Status'); ?>
                <?php
                $opt = [1 => lang('active'), 0 => lang('inactive')];
                echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status"" class="form-control input-tip select" style="width:100%;"'); ?>
            </div>

            <div class="form-group sort_ord">
                <?= lang('sort', 'sort'); ?>
                <?= form_input('sort_ord', '', 'class="form-control tip" id="sort_ord"'); ?>
            </div>

            <div class="form-group sub_menu">
                <?= lang('Rank_sub_menu', 'Rank_sub_menu'); ?>
                <?= form_input('rnk', '', 'class="form-control tip" id="rnk"'); ?>
            </div>

        </div>
        </div>
            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_object', lang('Add_object'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.sub_menu').hide();
        $('.sort_ord').show();

        $('#sort_ord2').change(function(e) {
            if($(this).val()){
                $('.sort_ord').hide();
                $('.sub_menu').show();
            }else{
                $('.sub_menu').hide();
                $('.sort_ord').show();
            }
        });

        $('#type').change(function(e) {
            if($(this).val()==2){
                $('#sort_ord2').val('').trigger('change');
                $('.sort_ord2').hide();
            }else{
                $('.sort_ord2').show();
            }
        });
    });
</script>
