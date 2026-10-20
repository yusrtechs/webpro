<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_object'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('modules/edit_object/' . $object->id, $attrib); ?>

        <div class="modal-body">

            <p><?= lang('update_info'); ?></p>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $object->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'name_en'); ?>
                <?= form_input('name_en', $object->name_en, 'class="form-control gen_slug" id="name_en" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $object->description), 'class="form-control tip" id="description"'); ?>
            </div>
            <div class="form-group sort_ord">
                <?= lang('sort', 'sort'); ?>
                <?= form_input('sort_ord', $object->sort_ord, 'class="form-control tip" id="sort_ord" required="required"'); ?>
            </div>
            <div class="form-group sub_menu">
                <?= lang('sub_menu', 'sub_menu'); ?>
                <?php
                $sort_ord[''] = lang('select') . ' ' . lang('sub_menu');
                foreach ($sub_menu as $menu) {
					$sort_ord[$menu->sort_ord] = $menu->name;
                }
                echo form_dropdown('sort_ord2', $sort_ord, $object->sort_ord, 'class="form-control  input-tip select" id="sort_ord2"  style="width:100%"')
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('Directory', 'directory'); ?>
                <?= form_input('directory', $object->directory, 'class="form-control" id="directory"'); ?>
            </div>
            <div class="form-group all">
                <?= lang('type', 'type'); ?>
                <?php
                $t_opt = [1 => lang('menu_item'), 2 => lang('sub_menu')];
                echo form_dropdown('type', $t_opt, $object->type, 'id="type"" class="form-control input-tip select" style="width:100%;"'); ?>
            </div>
            <div class="form-group">
                <?= lang('Status', 'Status'); ?>
                <?php
                $opt = [1 => lang('active'), 0 => lang('inactive')];
                echo form_dropdown('status', $opt, $object->status, 'id="status" " class="form-control input-tip select" style="width:100%;"'); ?>
            </div>
            <div class="form-group sub_menu">
                <?= lang('Rank_sub_menu', 'Rank_sub_menu'); ?>
                <?= form_input('rnk', $object->rnk, 'class="form-control" id="rnk"'); ?>
            </div>



        </div>
        </div>
            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

            <?php echo form_hidden('id', $object->id); ?>
        </div>

        <div class="modal-footer">
            <?php echo form_submit('edit_object', lang('Edit_object'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        var rnk= '<?= $object->rnk ?>';
        if(rnk){
            $('.sort_ord').hide();
            $('.sub_menu').show();
        }else{
            $('.sub_menu').hide();
            $('.sort_ord').show();
        }
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'object');
        });
    });
</script>
