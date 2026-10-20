<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = [];
}
?>
<script type="text/javascript">
    $(document).ready(function () {

		$(document).on('click', '#back,#backMR', function (e) {

				    window.location.href = site.base_url + 'manufacturing/confirmed';

		});


		$('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
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
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
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
<div class="box spark-manufacturing-page spark-manufacturing-checkstock">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file-text-o nb"></i><?= lang('Check_stock').' ( ' .$bom_details->name.' ) ' ; ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">




				<div class="row">
                            <div class="col-sm-5">
                                <img src="<?= base_url() ?>assets/uploads/<?= $bom_details->image ?>"
                                     alt="<?= $bom_details->name ?>" class="img-responsive img-thumbnail"/>


                            </div>
                            <div class="col-sm-7">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped dfTable table-right-left">
                                        <tbody>
                                        <tr>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Bill_Of_Material'); ?></td>
                                            <td><?php echo $bom_details->name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('date'); ?></td>
                                            <td><?php echo $this->sma->hrld($man_details->created_date); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Raw_materials_warehouse'); ?></td>
                                            <td><?= $bom_details->wh_raw_name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('quantity'); ?></td>
                                            <td><?php echo $this->sma->formatQuantity($man_details->quantity,4); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('Deadline_Start'); ?></td>
                                            <td><?php echo $this->sma->hrsd($man_details->deadline_start); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang('created_by'); ?></td>
                                            <td><?= $created_by->first_name . ' ' . $created_by->last_name; ?></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>


							<div class="col-sm-12">

                                <?= $man_details->description ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('description') . '</div><div class="panel-body">' . $bom_details->description . '</div></div>' : ''; ?>

                            </div>
                            </div>
							<div class="clearfix"></div>

							<div class="col-sm-12">

							<div class="alert alert-danger" id="mError-con" style="display: none;">
                    			<span id="mError"></span>
							</div>

								<div class="card1">
                            <div class="card-body">
                                <h2 class="header-title"><i class="fa fa-cubes"></i> <?= lang('Raw_materials') ?> ( <?php echo $bom_details->name; ?> )</h2>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase">
                                                <tr class="table-active">
                                                    <th scope="col"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                                    <th scope="col"><?= lang('unit') ?></th>
                                                    <th scope="col"><?= lang('To_Consume') ?></th>
                                                    <th scope="col"><?= lang('stock_value') ?></th>
                                                    <th scope="col"><?= lang('Status') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php
												$consumedFlage=1;
												foreach ($bom_detail as $key => $value) : ?>
                                            	<?php $row_no=$value->id; $qty = $man_details->quantity; $bom_qty = $value->qty; $consumed = $qty*$bom_qty; $sp=$this->manufacturing_model->getWarehousesWithPQ($value->id,$bom_details->wh_raw)[0]->quantity; ?>
                                                <tr id="row_<?= $row_no ?>" class="<?php if($consumed <= $sp) { echo "table-success"; } else { echo "table-danger"; } ?>" data-item-id="<?= $row_no ?>">
                                                    <th scope="row"><?php echo $value->code.' - '.$value->name; ?></th>
                                                    <td><?php echo $value->unit_name; ?></td>
                                                    <td style="max-width:20px;"><input class="form-control text-center rquantity1" name="combo_item_quantity[]" type="text" value="<?= $this->sma->formatQuantity($consumed,4); ?>" data-id="<?= $row_no ?>" data-item="<?= $row_no ?>" id="quantity_<?= $row_no ?>" onClick="this.select();"></td>
                                                    <td><input class="form-control text-center " name="combo_item_stock[]" type="hidden" value="<?= $sp; ?>" data-id="<?= $row_no ?>" data-item="<?= $row_no ?>" id="stock_<?= $row_no ?>" onClick="this.select();"><?php echo $this->sma->formatDecimal($sp); ?></td>
                                                    <td><?php if($consumed <= $sp) { echo '<span class="label label-success"><i class="fa fa-check"></i> ' . lang('Available') . '</span>'; } else { $consumedFlage=0; echo '<span class="label label-danger"><i class="fa fa-times"></i> ' . lang('Not_Available') . '</span>'; } ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </div>

							<div class="col-sm-12 " id="div_addProduce">
								<button type="button" class="btn btn-primary spark-btn-primary-action" id="addProduce" <?php if($consumedFlage== 1) { } else { echo "disabled"; } ?> ><i class="fa fa-cogs"></i> <?= lang('Produce') ?></button>
								<button type="button" class="btn btn-primary spark-btn-secondary-action" id="addPR" <?php if($consumedFlage== 0) { } else { echo "disabled"; } ?> ><i class="fa fa-shopping-cart"></i> <?= lang('ordered') ?></button>
								<button type="button" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" id="back" ><i class="fa fa-arrow-left"></i> <?= lang('Back') ?></button>
							</div>



































                <div class="col-md-5" style="display:none;">
                    <div class="form-group">
                        <?= lang('product', 'type') ?>
                        <?php
                        $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service')];
                        echo form_dropdown('type', $opts, 'combo', 'class="form-control" id="type" required="required"');
                        ?>
                    </div>


                </div>





            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {



		$(document).on('click', '#addProduce', function (e) {



			$.each(items, function () {

			var row_no = this.id;
			var qty = $('#quantity_'+row_no).val();
			items[row_no].qty=formatDecimal(qty,4);
			//alert(items[item_id].qty);
			//alert(qty);
             });
			 var man_id='<?= $man_details->id; ?>';
			 var bom_id='<?= $bom_details->id; ?>';
			//var product_id=items[item_id].id;
			//var product_code=items[item_id].code;
			//var product_quantity=items[item_id].qty;
			//var product_unit=items[item_id].unit_id;
			//var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();


					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addManufacturingDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, man_id : man_id, items : items},
                success: function(data, page) {
					if(data==true){
					    window.location.href = site.base_url + 'manufacturing/progress';
				}else{
					$('#mError').text('<?= lang('failed_to_add_product') ?>');
					$('#mError-con').show();
					}

                },
            });




            return false;

        });

		$(document).on('click', '#addPR', function (e) {



			 var man_id='<?= $man_details->id; ?>';
			//var product_id=items[item_id].id;
			//var product_code=items[item_id].code;
			//var product_quantity=items[item_id].qty;
			//var product_unit=items[item_id].unit_id;
			//var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();
            window.location.href = site.base_url + 'purchases/add/'+man_id;





            return false;

        });


		$('#tax_rate').change(function () {

			$("#div_tax_method").css("display", "block");


			try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'products/getTax_Rate/' + $('#tax_rate').val(),
                dataType: 'json',
                data: { id : $('#tax_rate').val()},
                success: function(data, page) {

					if (data>0) {
                        $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
				        $('#tax_method').select2('readonly', false);



                    } else {
                    	$('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
				        $('#tax_method').select2('readonly', true);



                    }

                },
            });

		}catch(err){alert(err);}

        });

        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        <?php
        if ($bom_items) {
            foreach ($bom_items as $item) {
                //echo 'ietms['.$item->id.'] = '.$item.';';
                if ($item->code) {
                    echo 'add_product_item(' . json_encode($item) . ',0);';
                }
            }
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");' : '' ?>
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
                $('#cost').attr('disabled', true);
                //$('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideDown();
                //$('#track_quantity').iCheck('check');
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
				$('#cost').attr('disabled', false);
			} else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('disabled', true);
            //$('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
           // $('#track_quantity').iCheck('check');
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

            source: '<?= admin_url('manufacturing/suggestions?warehouse_id='.$bom_details->wh_raw); ?>' ,
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {

                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
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
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {

                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item,1);
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });



		function add_product_item(item,flage) {
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
				var bom_id='<?= $bom_details->id; ?>';
				updateBomDetail(bom_id,item_id,items[item_id].qty);
                //items[item_id].avg_cost = (parseFloat(items[item_id].avg_cost) * parseFloat(items[item_id].qty)).toFixed(2);
            } else {
                items[item_id] = item;
				if(flage==1){
			var bom_id='<?= $bom_details->id; ?>';
			var product_id=items[item_id].id;
			var product_code=items[item_id].code;
			var product_quantity=items[item_id].qty;
			var product_unit=items[item_id].unit_id;
			var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();



					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_code : product_code, product_quantity : product_quantity, product_unit : product_unit, product_avg_cost : product_avg_cost},
                success: function(data, page) {


                },
            });


				}

			}
            var pp = 0;
            var total = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
				var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input name="combo_item_unit[]" type="hidden" value="' + this.unit_id + '"><span id="unit_' + row_no + '">' + this.unit_name + '</span></td>';
                tr_html += '<td><input disabled class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input disabled class="form-control text-center trprice" name="total_combo_item_price[]" type="text" value="' + formatDecimal(this.qty * this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                pp += formatDecimal(parseFloat(this.avg_cost)*parseFloat(this.qty),4);
            });
            $('.item_' + item_id).addClass('warning');
			var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="4"><?= lang('total') ?></th><th class="text-center total"><input disabled class="form-control text-center" name="total" id="total" type="text" value="' + formatDecimal(pp,4) + '"></th><tr>';
				$("#prTable tfoot").html(tfoot);


            $('#total').val(formatDecimal(pp,4));
            $('#wavg_cost').val(formatDecimal(pp/<?= $bom_details->quantity ?>,4));
            return true;
        }



        function updateBomDetail(bom_id,product_id,product_quantity){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/updateBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_quantity : product_quantity},
                success: function(data, page) {


                },
            });
		}
        function deleteBomDetail(bom_id,product_id){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/deleteBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id},
                success: function(data, page) {


                },
            });
		}
        function calculate_price() {
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
            });
                        $('#total').val(formatDecimal(pp,4));
						$('#wavg_cost').val(formatDecimal(pp/<?= $bom_details->quantity ?>,4));

            return true;
        }

        $(document).on('change', '.rquantity', function () {
			var row = $(this).closest('tr');
			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
        	//row.find('.trprice').val(formatDecimal(parseFloat(row.find('.rprice').val())*parseFloat(row.find('.rquantity').val()),4));
			//var bom_id='<?= $bom_details->id; ?>';
			//items[item_id].qty=formatDecimal(row.find('.rquantity').val(),4);
				//updateBomDetail(bom_id,item_id,row.find('.rquantity').val());
        //alert(items[item_id].qty)

            //calculate_price();
        });

        $(document).on('click', '.del', function () {


			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
			var bom_id='<?= $bom_details->id; ?>';
				deleteBomDetail(bom_id,item_id);

            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();




            calculate_price();
        });

        var su = 2;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang('select') . ' ' . lang('supplier') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
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
                    <?php if (!empty($warehouses)) {
            foreach ($warehouses as $warehouse) {
                echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
                // echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span>0</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
            }
        } else {
            ?>
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                        // $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php
        } ?>
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
            // $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        // $('#aModal').on('shown.bs.modal', function () {
        //     $('#aquantity').focus();
        //     $(this).keypress(function( e ) {
        //         if ( e.which == 13 ) {
        //             $('#updateAttr').click();
        //         }
        //     });
        // });
        $(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            // row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            // row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            $('#aModal').modal('hide');
        });

		$('#pwarehouse').val('<?= $warehouses[0]->id ?>').trigger('change.select2');

			$("#pwarehouse > option").each(function(){

				$("#prTable"+ this.value).hide();
                $("#title"+ this.value).hide();


			});


    });

    <?php if ($product) {
            ?>
    $(document).ready(function () {

        var t = "<?=$product->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            //$('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            //$('#track_quantity').iCheck('check');
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
        $("#product_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $product->category_id ?>,
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
        <?php if ($product->supplier1) {
                ?>
        select_supplier('supplier1', "<?= $product->supplier1; ?>");
        $('#supplier_price').val("<?= $product->supplier1price == 0 ? '' : $this->sma->formatDecimal($product->supplier1price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier2) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_2', "<?= $product->supplier2; ?>");
        $('#supplier_2_price').val("<?= $product->supplier2price == 0 ? '' : $this->sma->formatDecimal($product->supplier2price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier3) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_3', "<?= $product->supplier3; ?>");
        $('#supplier_3_price').val("<?= $product->supplier3price == 0 ? '' : $this->sma->formatDecimal($product->supplier3price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier4) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_4', "<?= $product->supplier4; ?>");
        $('#supplier_4_price').val("<?= $product->supplier4price == 0 ? '' : $this->sma->formatDecimal($product->supplier4price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier5) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_5', "<?= $product->supplier5; ?>");
        $('#supplier_5_price').val("<?= $product->supplier5price == 0 ? '' : $this->sma->formatDecimal($product->supplier5price); ?>");
        <?php
            } ?>
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
            });//.select2("val", "<?= $product->supplier1; ?>");
        }

        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#r' + $(this).attr('id')).text());
        });
    });
    <?php
        } ?>
    $(document).ready(function() {

        $('#unit').change(function(e) {

            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        });


			$("#div_tax_method").css("display", "block");


    });


</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_product_manually') ?></h4>
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
                    <!-- <div class="form-group">
                        <label for="aquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aquantity">
                        </div>
                    </div> -->
                    <input type="hidden" id="aquantity" value="0">
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price_addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aprice">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary spark-btn-primary-action" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
