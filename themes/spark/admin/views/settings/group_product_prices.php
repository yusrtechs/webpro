<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var ti = 0;
        $(document).on('change', '.price', function () {
            var row = $(this).closest('tr');
            row.first('td').find('input[type="checkbox"]').iCheck('check');
        });

		$(document).on('change', '.idd', function () {

			var row = $(this).closest('tr');
            if(this.checked){

            row.first('td').find('input[type="checkbox"]').iCheck('check');

			var product_unit_id1 =  row.find('.unit').val();
			row.find('.idd').val(product_unit_id1);
			}else{
			row.first('td').find('input[type="checkbox"]').iCheck('uncheck');


			}
        });



		$(document).on('click', '.unit', function () {



            var row = $(this).closest('tr');

            row.first('td').find('input[type="checkbox"]').iCheck('check');
			var product_unit_id1 =  row.find('.unit').val();
			row.find('.idd').val(product_unit_id1);


			var product_unit_id =  row.find('.unit').val();
			//alert(product_unit_id)
			var product_id =  row.attr('id');






			//var opt = $("<select name=\"prunit[]\" id=\"prunit_" + product_id + "\"  class=\"form-control select rprunit\" />");
			opt=row.find('.unit');








                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnitsByProductId') ?>/" + product_id,
                    dataType: "json",
                    success: function (data) {

						//if(data.length>1)
						{
						opt.select2("destroy").empty();
                        //opt.select2("destroy").empty().select2({minimumResultsForSearch: 7});
						    //$("<option />", {value: "", text: ""}).appendTo(opt);

                        $.each(data, function () {
							//uopt1+='<option   value='+ this.id +'  selected>'+ this.name +'</option>';
							if (this.id == product_unit_id) {


                            $("<option />", {value: this.id, text: this.name}).appendTo(opt);
							}else{
							$("<option />", {value: this.id, text: this.name}).appendTo(opt);

							}
                        });
						//opt.val(v).change();

						//opt.select2('val', product_unit_id);

						//alert('');
						//opt.val(2).trigger('change.select2');
						//alert('');
						opt.val(product_unit_id); // Select the option with a value of '1'
                        opt.trigger('change'); // Notify any JS components that the value changed


						//alert(opt.get(0).outerHTML)

						}
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });


				//alert(opt.get(0).outerHTML)




        });





		$(document).on('change', '.unit', function () {



            var row = $(this).closest('tr');

            row.first('td').find('input[type="checkbox"]').iCheck('check');
			var product_unit_id1 =  row.find('.unit').val();
			row.find('.idd').val(product_unit_id1);


			var product_unit_id =  row.find('.unit').val();
			var product_id =  row.attr('id');






			//var product_code = row.find('.product_code').val();

			//var opt = $("<select name=\"prunit[]\" id=\"prunit_" + product_id + "\"  class=\"form-control select rprunit\" />");
			//opt=row.find('.unit');




				 $.ajax({
                type: 'post',
                url: '<?= admin_url('sales/get_product_group_price/' . $price_group->id); ?>',
                dataType: "json",
                data: {
                    <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>',
                    product_id: product_id,  product_unit_id: product_unit_id
                },
                success: function (data) {

                    if (data.status == 0){
                    var price = row.find('.price').val('');
                    var product_code = row.find('.product_code').val('');
			}else{


							var price = row.find('.price').val(data.price);
                         var product_code = row.find('.product_code').val(data.product_code);



						//btn.removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-check"></i>');
					}
                },
                error: function (data) {
                    bootbox.alert('<?= lang('ajax_error') ?>');//btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i>');
                }
            });




				//alert(opt.get(0).outerHTML)




        });


		$(document).on('click', '.random_num', function () {

			var row = $(this).closest('tr');


			row.first('td').find('input[type="checkbox"]').iCheck('check');
			var product_unit_id1 =  row.find('.unit').val();
			row.find('.idd').val(product_unit_id1);


			chars = '1234567890';
            no = '';

			for (var i = 0; i < 8; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        no += chars.substring(rnum, rnum + 1);
    }
            var product_code = row.find('.product_code').val(no);
			//alert(product_code);
            //row.first('td').find('input[type="checkbox"]').iCheck('check');
        });
        $(document).on('click', '.form-submit', function () {
            var btn = $(this);
            btn.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>');
            var row = btn.closest('tr');
            var product_id = row.attr('id');
            var price = row.find('.price').val();
            var unit = row.find('.unit').val();
			var product_code = row.find('.product_code').val();




            $.ajax({
                type: 'post',
                url: '<?= admin_url('sales/update_product_group_price/' . $price_group->id); ?>',
                dataType: "json",
                data: {
                    <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>',
                    product_id: product_id, price: price, unit: unit, product_code: product_code
                },
                success: function (data) {

                    if (data.status != 1)
                        btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i>');
                    else{
                        btn.removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-check"></i>');
						//RefreshTable('#CGData', '<?= admin_url('sales/getProductPrices/' . $price_group->id) ?>');
						//var table = $('#CGData').DataTable();
                        //table.ajax.reload();
						//$('#CGData').DataTable().ajax.reload();

					}
                },
                error: function (data) {
                    btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i>');
                }
            });
            // btn.html('<i class="fa fa-check"></i>');
        });

		function checkbox_pp(x) {
			ti = ti+1;
         //return '<div class="text-center idd"><input class="checkbox multi-select" type="checkbox" name="val[]" value="' + x + '" /></div>';
         return "<div class=\"text-center \" style=\"display:none;\"><input type=\"checkbox\" name=\"val[]\" value=\""+ x+"\" class=\"checkbox multi-select\" tabindex=\""+(ti)+"\" style=\"padding:2px;height:auto;\"></div><div class=\"text-center \" style=\"position: relative;display: table;border-collapse: separate;\"><input type=\"checkbox\" name=\"val1[]\" value=\""+x+"\" class=\"form-control text-center idd\" tabindex=\""+(ti)+"\" style=\"padding:1px;height:auto;\"></div>"; // onclick=\"this.select();\"
          }

        function product_code_input(x) {
            ti = ti+1;
            //var v = x.split('__');
            return "<div class=\"text-center \" style=\"position: relative;display: table;border-collapse: separate;\"><input type=\"text\" name=\"product_code"+x+"\" value=\""+(x != null ? x : '')+"\" class=\"form-control text-center product_code\" tabindex=\""+(ti)+"\" style=\"padding:2px;height:auto;\"><span class=\"input-group-addon pointer random_num\" id=\"random_num\" style=\"padding: 1px 10px;\"><i class=\"fa fa-random\"></i></span></div>"; // onclick=\"this.select();\"
        }

        function price_input(x) {
            ti = ti+1;
            var v = x.split('__');
            return "<div class=\"text-center\"><input type=\"text\" name=\"price"+v[0]+"\" value=\""+(v[1] != '' ? formatDecimals(v[1]) : '')+"\" class=\"form-control text-center price\" tabindex=\""+(ti)+"\" style=\"padding:2px;height:auto;\"></div>"; // onclick=\"this.select();\"
        }

		function unit_disable(x) {
            return "<div style=\"display:none\"></div>"; // onclick=\"this.select();\"
        }

        function unit_input(x) {

			var units=<?= $units ?>;



            ti = ti+1;
            var v = x.split('__');


			var row_no = v[2];

				//console.log(row_no)

				//var opt = $("<select name=\"prunit[]\" id=\"prunit_" + row_no + "\"  class=\"form-control select rprunit\" />");









			//console.log(opt.get(0).outerHTML)




			var uopt=null;
			var uopt1 = '<div  class=\"text-center\">';

			if (units) {
             uopt1+="<select   style='width:100%;'  name=\"unit"+v[0]+"\" class=\"text-center unit\" tabindex=\""+(ti)+"\" style=\"padding:7px;height:100%;\" >";
             //uopt = $('<select   name=\"unit"+v[0]+"\" class=\"form-control text-center unit\" tabindex=\""+(ti)+"\" style=\"padding:2px;height:auto;\" />');

			$.each(units, function() {
				if (this.id == (v[1] != '' ? v[1] : '') || this.id == (v[2] != '' ? v[2] : '')) {
                //    $('<option />', { value: this.id, text: this.name, selected: true }).appendTo(uopt1);
                uopt1+='<option   value='+ this.id +'  selected>'+ this.name +'</option>';

				} else {
                //    $('<option />', { value: this.id, text: this.name }).appendTo(uopt1);
				//uopt1+='<option   value='+ this.id +'  >'+ this.name +'</option>';

                }
            });
			uopt1+='</select>';

        }
		//var divu = $("<div class=\"text-center\">"+ uopt"+</div>");
		//alert(uopt)
		//divu.html(uopt);
			uopt1+='</vid>';
            return uopt1;
          //  return "<div class=\"text-center\"><input type=\"text\" name=\"unit"+v[0]+"\" value=\""+(v[1] != '' ? v[1] : '')+"\" class=\"form-control text-center unit\" tabindex=\""+(ti)+"\" style=\"padding:2px;height:auto;\"></div>"; // onclick=\"this.select();\"
        }

		function RefreshTable(tableId, urlData) {
    $.getJSON(urlData, null, function(json) {
        table = $(tableId).dataTable();
        oSettings = table.fnSettings();

        table.fnClearTable(this);

        for (var i = 0; i < json.aaData.length; i++) {
            table.oApi._fnAddData(oSettings, json.aaData[i]);
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        table.fnDraw();
    });
    }

        $('#CGData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getProductPrices/' . $price_group->id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "product_group_price_id";
                return nRow;
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox_pp}, null, null, {"bSortable": false, "mRender": unit_input}, {"bSortable": false, "mRender": price_input}, {"bSortable": false, "mRender": product_code_input}, {"bSortable": false, "mRender": unit_disable}, {"bSortable": false}]
        }).fnSetFilteringDelay();
    });
</script>
<?= admin_form_open('sales/product_group_price_actions/' . $price_group->id, 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-building"></i><?= $page_title ?> (<?= $price_group->name; ?>)</h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="#" id="update_price" data-action="update_price">
                                <i class="fa fa-dollar"></i> <?= lang('update_price') ?>
                            </a>
                        </li>
                        <!--<li>
                            <a href="<?php echo admin_url('sales/update_prices_csv/' . $price_group->id); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-upload"></i> <?= lang('update_prices_csv') ?>
                            </a>
                        </li>-->
                        <li>
                            <a href="<?php echo admin_url('sales/update_prices_excel/' . $price_group->id); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-upload"></i> <?= lang('update_prices_excel') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" id="delete" data-action="delete">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_product_group_prices') ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CGData" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-2"><?= lang('product_code'); ?></th>
                            <th class="col-xs-6"><?= lang('product_name'); ?></th>
                            <th class="col-xs-2"><?= lang('unit'); ?></th>
                            <th class="col-xs-1"><?= lang('price'); ?></th>
                            <th  style="min-width:185px;"><?= lang('product_code'); ?></th>
                            <th></th>
                            <th style="width:85px;"><?= lang('update'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

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

        $('#update_price').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

    });
</script>

