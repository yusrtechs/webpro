<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = array();
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'promos');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('promo/getCategoryProducts') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
						    if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select offer product') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select product to load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_promo'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promo/add", $attrib)
                ?>

                <div class="col-md-5">
                    <div class="form-group">
                        <?= lang("promo_type", "type") ?>
                        <?php
                        $opts = array(1 => lang('according cart value'), 2 => lang('specified category'), 3 => lang('buy one get one free'), 4 => lang('buy two get two free'), 5 => lang('percentage discount'), 6 => lang('occasional offers'), 7 => lang('time slot offers'));
                        echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($promo ? $promo->type : '')), 'class="form-control" id="type" required="required"');
                        ?>
                    </div>
          
                    <div class="form-group all">
                        <?= lang("promo_code", "code") ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($promo ? $promo->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        <span class="help-block"><?= lang('enter promo code without space') ?></span>
                    </div>






                         <div class="form-group all">
                        <?= lang("offer category" ) ?>
                        <?php
                        $cat[''] = "";
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('offer_cat', $cat, (isset($_POST['offer_cat']) ? $_POST['offer_cat'] : ($promo ? $promo->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("offer product" ) ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($promo ? $promo->name : ''), 'class="form-control" id="subcategory"   placeholder="' . lang("select_product_to_load") . '"');
                            ?>
                        </div>
                        
                        </div>
                        <div class="form-group all">
                        <?= lang("free category" ) ?>
                        <?php
                        $cat[''] = "";
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('offer_cat', $cat, (isset($_POST['offer_cat']) ? $_POST['offer_cat'] : ($promo ? $promo->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("free product" ) ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($promo ? $promo->name : ''), 'class="form-control" id="subcategory"   placeholder="' . lang("select_product_to_load") . '"');
                            ?>
                        </div>
                        
                        </div>
                        
                
                    <div class="form-group all">
                        <?= lang("total_uses", "Total Uses") ?>
                        <?= form_input('price', (isset($_POST['total_uses']) ? $_POST['total_uses'] : ($promo ? $this->sma->formatDecimal($promo->total_uses) : '')), 'class="form-control tip" id="price" required="required"') ?>
                    </div>


  <div class="form-group all">
                        <?= lang("user_uses_limit", "Per User Uses Limit") ?>
                        <?= form_input('user_uses_limit', (isset($_POST['user_uses_limit']) ? $_POST['user_uses_limit'] : ($promo ? $this->sma->formatDecimal($promo->user_uses_limit) : '')), 'class="form-control tip" id="price" required="required"') ?>
                    </div>

                    

  <div class="form-group all">
                        <?= lang("start_date", "Start Date") ?>
                        <?= form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control tip date" id="start_date""') ?>
                    </div>
                    
                      <div class="form-group all">
                        <?= lang("end_date", "End Date") ?>
                         <?= form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control tip date" id="end_date""') ?>
                    </div>

                   

                   

                   
                    <div class="form-group standard">
                        <?= lang("alert_quantity", "alert_quantity") ?>
                        <div
                            class="input-group"> <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($promo ? $this->sma->formatQuantityDecimal($promo->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity"') ?>
                            <span class="input-group-addon">
                            <input type="checkbox" name="track_quantity" id="track_quantity"
                                   value="1" <?= ($promo ? (isset($promo->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
                        </span>
                        </div>
                    </div>

                    <div class="form-group all">
                        <?= lang("promo_image", "promo_image") ?>
                        <input id="promo_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="promo_image" data-show-upload="false"
                               data-show-preview="false" accept="image/*" class="form-control file">
                    </div>

                    

                    <div class="form-group">
                        <?php echo form_submit('add_promo', $this->lang->line("add_promo"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        <?php
        if($combo_items) {
            foreach($combo_items as $item) {
            //echo 'ietms['.$item->id.'] = '.$item.';';
                if($item->code) {
                    echo 'add_promo_item('.  json_encode($item).');';
                }
            }
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");': '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");': '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });
        //$('#cost').removeAttr('required');
        $('#digital_file').change(function () {
            if ($(this).val()) {
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
        });
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'standard') {
                $('.standard').slideUp();
                $('#unit').attr('disabled', true);
                $('#cost').attr('disabled', true);
                $('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideDown();
                $('#track_quantity').iCheck('check');
                $('#unit').attr('disabled', false);
                $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('.digital').slideDown();
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
            if (t == 'standard' || t == 'combo') {
                $('.standard_combo').slideDown();
            } else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#unit').attr('disabled', true);
            $('#cost').attr('disabled', true);
            $('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#unit').attr('disabled', false);
            $('#cost').attr('disabled', false);
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
        } else {
            $('.standard_combo').slideUp();
        }

        $("#add_item").autocomplete({
            source: '<?= admin_url('promo/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_promo_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_promo_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_promo_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_promo_found') ?>');
                }
            }
        });

        <?php
        if($this->input->post('type') == 'combo') {
            $c = isset($_POST['combo_item_code']) ? sizeof($_POST['combo_item_code']) : 0;
            for ($r = 0; $r <= $c; $r++) {
                if(isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                    $items[] = array('id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'price' => $_POST['combo_item_price'][$r]);
                }
            }
            echo '
            var ci = '.(isset($items) ? json_encode($items) : "''").';
            $.each(ci, function() { add_promo_item(this); });
            ';
        }
        ?>
        function add_promo_item(item) {
            if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
            } else {
                items[item_id] = item;
            }
            var pp = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty));
            });
            $('.item_' + item_id).addClass('warning');
            $('#price').val(pp);
            return true;
        }

        function calculate_price() {
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()));
            });
            $('#price').val(pp);
            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
            calculate_price();
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
            calculate_price();
        });
        var su = 2;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);
                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?=json_encode($vars);?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            for (var i in attrs) {
                if (attrs[i] !== '') {
                    <?php if( ! empty($warehouses)) {
                        foreach ($warehouses as $warehouse) {
                            echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="'.$warehouse->id.'"><span>'.$warehouse->name.'</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span>0</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
                        }
                    } else { ?>
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php } ?>
                }
            }
        });
//$('#attributesInput').on('select2-blur', function(){
//    $('#addAttributes').click();
//});
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
            $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        $('#aModal').on('shown.bs.modal', function () {
            $('#aquantity').focus();
            $(this).keypress(function( e ) {
                if ( e.which == 13 ) {
                    $('#updateAttr').click();
                }
            });
        });
        $(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            $('#aModal').modal('hide');
        });
    });

    <?php if ($promo) { ?>
    $(document).ready(function () {
        var t = "<?=$promo->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            $('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#cost').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
            //$('#add_item').removeAttr('required');
            //$('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
        } else {
            $('.combo').slideDown();
            //$('#add_item').attr('required', 'required');
            //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'add_item');
        }
        $("#code").parent('.form-group').addClass("has-error");
        $("#code").focus();
        $("#promo_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('promo/getSubCategories') ?>/" + <?= $promo->category_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: scdata
                    });
                } else {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });
        <?php if ($promo->supplier1) { ?>
        select_supplier('supplier1', "<?= $promo->supplier1; ?>");
        $('#supplier_price').val("<?= $promo->supplier1price == 0 ? '' : $this->sma->formatDecimal($promo->supplier1price); ?>");
        <?php } ?>
        <?php if ($promo->supplier2) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_2', "<?= $promo->supplier2; ?>");
        $('#supplier_2_price').val("<?= $promo->supplier2price == 0 ? '' : $this->sma->formatDecimal($promo->supplier2price); ?>");
        <?php } ?>
        <?php if ($promo->supplier3) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_3', "<?= $promo->supplier3; ?>");
        $('#supplier_3_price').val("<?= $promo->supplier3price == 0 ? '' : $this->sma->formatDecimal($promo->supplier3price); ?>");
        <?php } ?>
        <?php if ($promo->supplier4) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_4', "<?= $promo->supplier4; ?>");
        $('#supplier_4_price').val("<?= $promo->supplier4price == 0 ? '' : $this->sma->formatDecimal($promo->supplier4price); ?>");
        <?php } ?>
        <?php if ($promo->supplier5) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_5', "<?= $promo->supplier5; ?>");
        $('#supplier_5_price').val("<?= $promo->supplier5price == 0 ? '' : $this->sma->formatDecimal($promo->supplier5price); ?>");
        <?php } ?>
        function select_supplier(id, v) {
            $('#' + id).val(v).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: "<?= admin_url('suppliers/getSupplier') ?>/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "suppliers/suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });//.select2("val", "<?= $promo->supplier1; ?>");
        }

        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#r' + $(this).attr('id')).text());
        });
    });
    <?php } ?>
   
</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <iclass="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_promo_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('warehouse') ?></label>
                        <div class="col-sm-8">
                            <?php
                            $wh[''] = '';
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, '', 'id="awarehouse" class="form-control"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="aquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price_addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aprice">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
