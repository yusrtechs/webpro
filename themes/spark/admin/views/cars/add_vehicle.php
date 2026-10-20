<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.pr_link{
    cursor: pointer;
}
</style>

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





        <?php if ($this->input->get('supplier')) {
        ?>

            localStorage.setItem('cvowner_id', <?=$this->input->get('supplier'); ?>);

        <?php
    } ?>




       $('#car_status').change(function () {
        localStorage.setItem('cvcar_status', $(this).val());

            var v = $(this).val();
            if(v=="old"){
            $("#div_tax_rate").css("display", "none");
            $("#div_tax_method").css("display", "none");
            }else{
            $("#div_tax_rate").css("display", "block");
            $("#div_tax_method").css("display", "block");
            }

        });


        $('#car_status').change(function () {
            var v = $(this).val();
            if(v=="new")
            $("#div_mileage").css("display", "none");
            else
            $("#div_mileage").css("display", "block");


        });
            $('#brand_id').change(function () {
            var v = $(this).val();
            var uopt = $("#model_id");
//            console.log(uopt);
            localStorage.setItem('cvbrand_id', $(this).val());

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('cars/getModelsByBrandId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;

                        $('#model_id').val('').trigger('change.select2');
                        $('#category_id')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                            .val('')
                        ;

                        $('#category_id').val('').trigger('change.select2');

                            $.each(scdata, function () {
                     $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

                      });


                        } else {

                            $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;


                               $('#model_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value=""><?= lang('select_model_to_load') ?></option>')
                    .val('')
                        ;
                            $('#model_id').val('').trigger('change.select2');
                        $('#category_id')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                            .val('')
                        ;

                        $('#category_id').val('').trigger('change.select2');
                            /*$("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_model_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_model_id') ?>",
                                data: [{id: '', text: '<?= lang('no_model_id') ?>'}]
                            });*/
                        }
                        },
                        error: function () {
                            bootbox.alert('<?= lang('ajax_error') ?>');
                        }
                    });
                } else {
                    $("#model_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                    });
                }
            });


              $('#model_id').change(function () {
                localStorage.setItem('cvmodel_id', $(this).val());

                        var v = $(this).val();
                        var b = $("#brand_id").val();
                        var uopt = $("#category_id");
                    //     console.log(uopt);

                        if (v && b) {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?= admin_url('cars/getCategoryByBrandIdModelId') ?>/" + b + "/" +v,
                                dataType: "json",
                                success: function (scdata) {
                                    //console.log(scdata);
                                    if (scdata != null) {
                                        $('#category_id')
                .find('option')
                .remove()
                .end()
                .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                .val('')
            ;

            $('#category_id').val('').trigger('change.select2');

                                        $.each(scdata, function () {
                            $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

                    });


                        } else {

                            $('#category_id')
                .find('option')
                .remove()
                .end()
                .append('<option value=""><?= lang('select_category_to_load') ?></option>')
                .val('')
            ;

            $('#category_id').val('').trigger('change.select2');


                           /* $("#category_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_category_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_category_id') ?>",
                                data: [{id: '', text: '<?= lang('no_category_id') ?>'}]
                            });*/
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#category_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
           });


			$(document).on('click', '.pr_link', function(e) {
                var row = $(this).closest('tr');
                var itemid = row.find('.rid').val();
                $('#myModal').modal({ remote: site.base_url + 'products/modal_view/' + itemid });
                $('#myModal').modal('show');
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



            $("#crdate").datetimepicker({
                format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma',  todayBtn: 1, autoclose: 1, minView: 2
            }).datetimepicker('update', new Date());
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add_Vehicle'); ?></h2>
    </div>
    <div class="box-content">
								<div class="alert alert-danger" id="mError-conBOM" style="display: none;">
                    			<span id="mErrorBOM"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-conBOM" style="display: none;">
                    			<span id="mSuccessBOM"></span>
								</div>

        <div class="row">
            <div class="col-lg-12">
            <input type="hidden" id="vehicle_id" />
            <form id="addCarForm" method="post"  enctype="multipart/form-data">

				<div class="row">

                <div class="col-sm-3">

                <div class="form-group">
                                <?= lang('Chesis_no', 'chesis_no'); ?>
                                <?= form_input('chesis_no', $cars_vehicles->chesis_no, 'class="form-control gen_slug" id="chesis_no" required="required"'); ?>
                 </div>
             <!--   <div class="form-group all">
                        <?= lang('code', 'code') ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                    </div> -->

                <div class="form-group standard_combo">
                        <?= lang('Showroom', 'showroom_id'); ?>
                    <div class="input-group">
					<?php
                    //print_r($showroom);
                        $sh[''] = lang('select') . ' ' . lang('Showroom');
                        foreach ($showroom as $bu) {
                            $sh[$bu->id] = $bu->name;
                        }
                        ?>
                        <?= form_dropdown('showroom_id', $sh, '', 'class="form-control tip" id="showroom_id" required="required" style="width:100%;"'); ?>

					<!--		<?php if ($Owner || $Admin ) {
                         ?>  -->
                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('cars/add_showroom'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                                </div>
                     <!--   <?php
                        } ?>  -->

                    </div>
                    </div>


                 <div class="form-group standard_combo">
                      <label class="control-label" for="brand_id"><?= lang('brand'); ?></label>

                    <div class="input-group">
                    <?php
                    $br['']='';
                    foreach ($brands as $brand) {
                        $br[$brand->id] = $brand->brand_name;
                    }
                    echo form_dropdown('brand_id', $br, '', 'id="brand_id"  class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('brand') . '" required="required" style="width:100%;" '); ?>

                            <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('cars/add_brand'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                            </div>


                     </div>

                </div>
                <div class="form-group standard_combo">
                <label class="control-label" for="model_id"><?= lang('Car_Type'); ?></label>
                  <!--  <?= lang('Car_Type', 'model_name'); ?> -->


                    <div class="input-group">
                    <?php
                    $mod=[];
                    echo form_dropdown('model_id', $mod, '', 'id="model_id"  class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('model') . '" required="required" style="width:100%;" '); ?>
                            <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('cars/add_model'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                            </div>

                </div>
                </div>

                <div class="form-group">
                <label class="control-label" for="category_id"><?= lang('category_name'); ?></label>
                    <div class="input-group">
                    <?php
                    $cat=[];
                    echo form_dropdown('category_id', $cat, '', 'id="category_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('category') . '" required="required" style="width:100%;" '); ?>
                            <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('cars/add_category'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                            </div>

                    </div>
                </div>


          <!--  <div class="form-group">

                <?= lang('status','status'); ?>

                <?php $status_options =  ['available' => lang('Available'), 'sold' => lang('sold')]; ?>
                <?= form_dropdown('status', $status_options, $cars_vehicles->status, 'class="form-control tip" id="status" '); ?>
              </div> -->




            <div class="form-group">
                <?= lang('selling_price', 'buying_price'); ?>
                <?= form_input('buying_price', $cars_vehicles->buying_price, 'class="form-control gen_slug" id="buying_price"  '); ?>
            </div>

            <div class="form-group">
                <?= lang('cost', 'cost'); ?>
                <?= form_input('cost', $cars_vehicles->cost, 'class="form-control gen_slug" id="cost" '); ?>
            </div>
            <div class="form-group">
                    <?= lang('Model_or_Registration_Year', 'registration_year'); ?>
                    <input class="form-control "  name="registration_year" id="registration_year"  type="number" min="1950" step="01" value="<?php echo $cars_vehicles->registration_year; ?>"  />

                   <!-- <?= form_input('registration_year', $cars_vehicles->registration_year, 'class="form-control gen_slug" id="registration_year" '); ?> -->
            </div>

           </div>

           <div class="col-sm-3" >
                <div class="form-group">
                        <?= lang('Panel_no', 'panel_no'); ?>
                        <?= form_input('panel_no', $cars_vehicles->panel_no, 'class="form-control gen_slug" id="panel_no" '); ?>
                </div>


                <div class="form-group">
                        <?= lang('Car_status','car_status'); ?>

                        <?php $car_status_options =  ['new' => lang('New'), 'old' => lang('old')]; ?>
                        <?= form_dropdown('car_status', $car_status_options, $cars_vehicles->car_status, 'class="form-control tip" id="car_status" '); ?>
                </div>
                    <div class="form-group" id="div_tax_rate" style="display:block;">
                                <?= lang('Tax', 'tax_rate') ?>
                                <?php
                                $tr_default=$tax_rates[0]->id;
                                $tr[''] = '';
                                foreach ($tax_rates as $tax) {
                                    if($tax->tax_default==1)
									$tr_default=$tax->id;

                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('tax_rate', $tr, $tr_default, 'id="tax_rate"  required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                        </div>
                        <div class="form-group all" id="div_tax_method" style="display:block;">
                                <?= lang('tax_method', 'tax_method') ?>
                                <?php
                                $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                                echo form_dropdown('tax_method', $tm, '', 'class="form-control select" id="tax_method" required="required" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
                        </div>

              <div class="form-group" id="div_mileage" style="display:none;">
                <?= lang('Mileage', 'mileage'); ?>
                <?= form_input('mileage', $cars_vehicles->mileage, 'class="form-control gen_slug" id="mileage" '); ?>
            </div>



            <div class="form-group">
                <?= lang('Wd_Type', 'wd_type'); ?>
                <!--<input class="form-control "  name="wd_type" id="wd_type"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->wd_type; ?>"  />
                -->
                <?= form_input('wd_type', $cars_vehicles->wd_type, 'class="form-control gen_slug" id="wd_type" '); ?>
            </div>


            <div class="form-group">
                <?= lang('Cylinder', 'cylinder'); ?>
                <input class="form-control "  name="cylinder" id="cylinder"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->cylinder; ?>"  />

                <!-- <?= form_input('cylinder', $cars_vehicles->cylinder, 'class="form-control gen_slug" id="cylinder" '); ?> -->
            </div>

            <div class="form-group">
                <?= lang('Engine_Capacity', 'engine_capacity'); ?>
                <input class="form-control "  name="engine_capacity" id="engine_capacity"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->engine_capacity; ?>"  />

              <!--  <?= form_input('engine_capacity', $cars_vehicles->engine_capacity, 'class="form-control gen_slug" id="engine_capacity" '); ?> -->
            </div>

            <div class="form-group">
                <?= lang('Fuel_Type', 'fuel_type'); ?>
                 <?php $fuel_type_options =  ['gasoline' => lang('Gasoline'), 'diesel' => lang('Diesel'),'hybird' => lang('Hybird'),'electricity' => lang('Electricity')]; ?>
                <?= form_dropdown('fuel_type', $fuel_type_options, $cars_vehicles->fuel_type, 'class="form-control tip" id="fuel_type" '); ?>

            </div>




            </div>

            <div class="col-sm-3">
                        <div class="form-group">
                            <?= lang('Added_on', 'crdate'); ?>
                            <?= form_input('date', '', 'readonly class="form-control input-tip date" id="crdate" '); ?>
                        </div>

                        <div class="form-group">

                           <?= lang('Car_Place','available_place'); ?>

                           <?= form_input('available_place', $cars_vehicles->available_place, 'class="form-control gen_slug" id="available_place" '); ?>

                       </div>

                        <div class="form-group">
                            <?= lang('Color', 'color'); ?>
                            <?= form_input('color', $cars_vehicles->color, 'class="form-control gen_slug" id="color" '); ?>
                        </div>

                        <div class="form-group">
                                <?= lang('Shape', 'shape'); ?>
                                <?= form_input('shape', $cars_vehicles->shape, 'class="form-control gen_slug" id="shape" '); ?>
                          </div>

                        <div class="form-group">
                                <?= lang('Gear', 'gear'); ?>
                                <?= form_input('gear', $cars_vehicles->gear, 'class="form-control gen_slug" id="gear"'); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Doors', 'doors'); ?>
                                <input class="form-control "  name="doors" id="doors"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->doors; ?>"  />

                               <!-- <?= form_input('doors', $cars_vehicles->doors, 'class="form-control gen_slug" id="doors" '); ?> -->
                          </div>
                          <div class="form-group">
                                <?= lang('Seats', 'seats'); ?>
                                <input class="form-control "  name="seats" id="seats"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->seats; ?>"  />

                              <!--  <?= form_input('seats', $cars_vehicles->seats, 'class="form-control gen_slug" id="seats" '); ?> -->
                          </div>
                          <div class="form-group">
                                <?= lang('Tank', 'tank'); ?>
                                <input class="form-control "  name="tank" id="tank"  type="number" min="0" step="01" value="<?php echo $cars_vehicles->tank; ?>"  />

                              <!--  <?= form_input('tank', $cars_vehicles->tank, 'class="form-control gen_slug" id="tank" '); ?> -->
                          </div>



                          </div> <!-- end div col-sm-3 -->
                          <div class="col-sm-3">

                          <div class="form-group">
                                <?= lang('Serial_no', 'serial_no'); ?>
                                <?= form_input('serial_no', $cars_vehicles->serial_no, 'class="form-control gen_slug" id="serial_no" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Card_no', 'card_no'); ?>

                                <?= form_input('card_no', $cars_vehicles->card_no, 'class="form-control gen_slug" id="card_no" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Customs_Statment', 'customs_statment'); ?>
                                <?= form_input('customs_statment', $cars_vehicles->customs_statment, 'class="form-control gen_slug" id="customs_statment" '); ?>
                          </div>

                          <div class="form-group">
                                <?= lang('Insurance_id', 'insurance_id'); ?>
                                <?= form_input('insurance_id', $cars_vehicles->insurance_id, 'class="form-control gen_slug" id="insurance_id" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Port_Entry', 'port_entry'); ?>
                                <?= form_input('port_entry', $cars_vehicles->port_entry, 'class="form-control gen_slug" id="port_entry" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Country_Manufacturing', 'country_manufacturing'); ?>
                                <?= form_input('country_manufacturing', $cars_vehicles->country_manufacturing, 'class="form-control gen_slug" id="country_manufacturing" '); ?>
                          </div>


                            <div class="form-group">
                                <?= lang('Specifications', 'specification'); ?>
                                <?= form_input('specification', $cars_vehicles->specification, 'class="form-control gen_slug" id="specification" '); ?>
                            </div>


                          </div> <!-- end div col-sm-3 -->
                          <div class="col-sm-12">
                                 <!-- <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('Car_Owner', 'owner_id'); ?>
                                            <div class="input-group">
                                                <?php
                                                echo form_input('owner_id', (isset($_GET['owner_id']) ? $_GET['owner_id'] : ''), 'id="owner_id" data-placeholder="' . lang('select') . ' ' . lang('owner_id') . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                                    <a href="#" id="toogle-customer-read-attr" class="external">
                                                        <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                                    <a href="#" id="view-carowner" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php if ($Owner || $Admin) {
                                                    ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                    <a href="<?= admin_url('cars/addcarowner'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>

                                     </div>
                                   -->

                                   <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('Car_Owner', 'owner_id'); ?>
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) {
                                                    ?><div class="input-group"><?php
                                                } ?>
                                                <input type="hidden" name="supplier" value="" id="owner_id"
                                                       class="form-control" required="required" style="width:100%;"
                                                       placeholder="<?= lang('select') . ' ' . lang('Car_Owner') ?>">
                                                <input type="hidden" name="supplier_id" value="" id="supplier_id"
                                                       class="form-control">

                                                <?php if ($Owner || $Admin || $GP['suppliers-index']) {
                                                      ?>
                                                    <div class="input-group-addon no-print" style="padding: 2px 5px; border-left: 0;">
                                                        <a href="#" id="view-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-2x fa-user" id="addIcon"></i>
                                                        </a>
                                                    </div>
                                                <?php
                                                   } ?>
                                                <?php if ($Owner || $Admin || $GP['suppliers-add']) {
                                                 ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="<?= admin_url('carssuppliers/add'); ?>" id="add-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                                    </a>
                                                </div>
                                            <?php
                                             } ?>
                                            <?php if ($Owner || $Admin || $GP['suppliers-add'] || $GP['suppliers-index']) {
                                    ?></div><?php
                                } ?>
                                </div>
                                </div>


                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <?= lang('image', 'image') ?>
                                                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
                                            </div>
                                         </div> <!-- end div col-sm-6 -->
                                        </div> <!-- end div col-sm-12 -->
                            <div class="col-sm-12">

                                <div class="form-group all">
                                    <?= lang('description', 'description'); ?>
                                    <?= form_textarea('description', '', 'class="form-control" id="description"'); ?>
                                </div>

                            </div>
						</div>

							<div class="col-sm-12 " style="padding-bottom:30px;">
								<button type="button" class="btn btn-primary" id="add_vehicle"><?= lang('Add_Vehicle') ?></button>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>

							</div>
                            </form>




							<div class="clearfix"></div>





							<div id="div_Features"  style="display:none;">
							<ul id="myTab" class="nav nav-tabs no-print">
							<li class=""><a href="#features-con" class="tab-grey"><?= lang('Features') ?></a></li>
    						<!--<li class=""><a href="#others-con" class="tab-grey"><?= lang('Others') ?></a></li>-->
    						</ul>



							<div class="tab-content">
								<div id="features-con" class="tab-pane fade in">

								<div class="alert alert-danger" id="mError-con" style="display: none;">
                    			<span id="mError"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-con" style="display: none;">
                    			<span id="mSuccess"></span>
								</div>




						<div class="row">
                        <div class="col-sm-12">
                        <div class="col-sm-3">
                                <div id="externalequipment">
                                    <div class="form-group">
                                            <input type="checkbox" class="checkbox" name="rmr_ee" <?php if($req_items) { echo "checked"; } ?>
                                           id="rmr_ee"><label
                                        for="rmr_ee"
                                        class="padding05"><?= lang('External_Equipment'); ?></label>
                                    </div>
                                </div>
                                <div id="req_ee" <?php if(!$req_items) { echo "style='display:none;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addExternalEquipment" class="btn btn-primary " type="button"><?= lang('Add_Feature') ?></button>
                                                </div>
		                                    </div>
                                        </div>
                                        <div id="ex-combo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div id="comfort">
                                    <div class="form-group">
                                            <input type="checkbox" class="checkbox" name="rmr_c" <?php if($req_items) { echo "checked"; } ?>
                                           id="rmr_c"><label
                                        for="rmr_c"
                                        class="padding05"><?= lang('Comfort'); ?></label>
                                    </div>
                                </div>
                                <div id="req_c" <?php if(!$req_items) { echo "style='display:none;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addComfort" class="btn btn-primary " type="button"><?= lang('Add_Feature') ?></button>
                                                </div>
		                                    </div>
                                        </div>
                                        <div id="ex-combo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div id="techniques">
                                    <div class="form-group">
                                            <input type="checkbox" class="checkbox" name="rmr_t" <?php if($req_items) { echo "checked"; } ?>
                                           id="rmr_t"><label
                                        for="rmr_t"
                                        class="padding05"><?= lang('Techniques'); ?></label>
                                    </div>
                                </div>
                                <div id="req_t" <?php if(!$req_items) { echo "style='display:none;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addTechniques" class="btn btn-primary " type="button"><?= lang('Add_Feature') ?></button>
                                                </div>
		                                    </div>
                                        </div>
                                        <div id="ex-combo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div id="CostCenters">
                                    <div class="form-group">
                                            <input type="checkbox" class="checkbox" name="rmr" <?php if($req_items) { echo "checked"; } ?>
                                           id="rmr"><label
                                        for="rmr"
                                        class="padding05"><?= lang('Safty'); ?></label>
                                    </div>
                                </div>
                                <div id="req_mr" <?php if(!$req_items) { echo "style='display:none;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addFeature" class="btn btn-primary " type="button"><?= lang('Add_Feature') ?></button>
                                                </div>
		                                    </div>
                                        </div>
                                        <div id="ex-combo"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
						</div>

						<div class="col-sm-12 " id="div_addnewItemManually">
							<button type="button" class="btn btn-primary" id="save_feature"><?= lang('save') ?></button>
							<button type="button" class="btn btn-danger" id="back" ><?= lang('Back') ?></button>

						</div>
						<div class="clearfix"></div>


                        </div>












							<div id="others-con" class="tab-pane fade in">


							<div class="alert alert-danger" id="mError-conMR" style="display: none;">
                    			<span id="mErrorMR"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-conMR" style="display: none;">
                    			<span id="mSuccessMR"></span>
								</div>


                        <div class="row">

                      <div class="col-sm-12">


						<div class="combo1" >

					<!--	<div class="col-md-4" >

							<div class="form-group">
                            <?= lang('Add_Raw_materials', 'add_item') ; ?>
                            <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
							</div>
                        </div>
                        <div class="col-sm-12">
						<div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang('Raw_materials'); ?></label>

                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped  table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('cost'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('Total'); ?></th>
                                        <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
									<tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
						    <div id="ex-combo"></div>

							<div class="col-sm-12">
                            <h3 class="bold"><?= lang('warehouse_quantity') ?></h3>
                            <div class="table-responsive">
                                <table
                                class="table  table-striped table-condensed dfTable one-columns">
                                <thead>
                                    <tr>
                                        <th class="col-xs-6"><?= lang('warehouse_name') ?></th>
                                        <th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th>
										<th><?= lang('avg_cost')  ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
									//foreach ($warehouses as $warehouse) {


                                                //if ($warehouse->quantity != 0) {
														$avg_cost=$this->sma->formatDecimal($warehouse->avg_cost,4) ;

														echo '<tr><td>' . $bom_details->wh_complete_name  . '</td><td><strong>' . $this->sma->formatQuantity($bom_details->quantity) . '</strong>' . ($warehouse->rack ? ' (' . $warehouse->rack . ')' : '') . '</td><td><input disabled class="form-control text-center" name="wavg_cost" id="wavg_cost" type="text" ></td></tr>';
													//}

                                            //}
											?>
                                                </tbody>
                            </table>
                        </div>
                        </div>
						-->
						<div class="col-sm-12 " id="div_addnewItemManually">
							<button type="button" class="btn btn-primary" id="addnewFeature"><?= lang('save') ?></button>
							<button type="button" class="btn btn-danger" id="backMR" ><?= lang('Back') ?></button>

						</div>


                    </div>



                                    </div>
                                </div>
                                </div>


								</div>
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


        var zero_tax_rate='<?= $zero_tax_rate ?>';
        //alert(zero_tax_rate)


       //
       function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});

}
//search_select();


        var $owner_id_var = $('#owner_id');



    //if ((owner_id = localStorage.getItem('owner_id'))) {
        $owner_id_var.val(owner_id).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: 'get',
                    async: false,
                    url: site.base_url + 'carssuppliers/getSupplier/' + $(element).val(),
                    dataType: 'json',
                    success: function(data) {
                        callback(data[0]);
                    },
                });
            },
            ajax: {
                url: site.base_url + 'carssuppliers/suggestions',
                dataType: 'json',
                quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        limit: 10,
                    };
                },
                results: function(data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                },
            },
        });
   // } else {
    //    nsCustomer();
   // }



		$(document).on('click', '#add_vehicle', function (e) {



            if (!$('#chesis_no').val()) {
                $('#mErrorBOM').text('<?= lang('Chesis_no_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
         /*   if (!$('#code').val()) {
                $('#mErrorBOM').text('<?= lang('code_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            } */

            if (!$('#showroom_id').val()) {
                $('#mErrorBOM').text('<?= lang('Showroom_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

			if (!$('#brand_id').val()) {
                $('#mErrorBOM').text('<?= lang('Brand_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#model_id').val()) {
                $('#mErrorBOM').text('<?= lang('Car_type_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#category_id').val()) {
                $('#mErrorBOM').text('<?= lang('Category_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

          /*
            if (!$('#car_status').val()) {
                $('#mErrorBOM').text('<?= lang('car_status_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

            if (!$('#buying_price').val()) {
                $('#mErrorBOM').text('<?= lang('buying_price_is_required') ?>');
                $('#mError-conBOM').show();
                return false;

            }
             if (!$('#color').val()) {
                $('#mErrorBOM').text('<?= lang('color_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#image').val()) {
                $('#mErrorBOM').text('<?= lang('image_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#mileage').val()) {
                $('#mErrorBOM').text('<?= lang('mileage_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#wd_type').val()) {
                $('#mErrorBOM').text('<?= lang('wd_type_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#registration_year').val()) {
                $('#mErrorBOM').text('<?= lang('registration_year_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#cylinder').val()) {
                $('#mErrorBOM').text('<?= lang('cylinder_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#engine_capacity').val()) {
                $('#mErrorBOM').text('<?= lang('engine_capacity_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#fuel_type').val()) {
                $('#mErrorBOM').text('<?= lang('fuel_type_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#specification').val()) {
                $('#mErrorBOM').text('<?= lang('specification_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#shape').val()) {
                $('#mErrorBOM').text('<?= lang('shape_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#gear').val()) {
                $('#mErrorBOM').text('<?= lang('gear_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#doors').val()) {
                $('#mErrorBOM').text('<?= lang('doors_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#seats').val()) {
                $('#mErrorBOM').text('<?= lang('seats_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#tank').val()) {
                $('#mErrorBOM').text('<?= lang('tank_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

            if (!$('#panel_no').val()) {
                $('#mErrorBOM').text('<?= lang('panel_no_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#serial_no').val()) {
                $('#mErrorBOM').text('<?= lang('serial_no_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#card_no').val()) {
                $('#mErrorBOM').text('<?= lang('card_no_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#customs_statment').val()) {
                $('#mErrorBOM').text('<?= lang('customs_statment_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#port_entry').val()) {
                $('#mErrorBOM').text('<?= lang('port_entry_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#country_manufacturing').val()) {
                $('#mErrorBOM').text('<?= lang('country_manufacturing_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

            if (!$('#description').val()) {
                $('#mErrorBOM').text('<?= lang('description_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#available_place').val()) {
                $('#mErrorBOM').text('<?= lang('Car_place_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#insurance_id').val()) {
                $('#mErrorBOM').text('<?= lang('insurance_id_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }*/
            if (!$('#owner_id').val()) {
                $('#mErrorBOM').text('<?= lang('Car_owner_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#tax_rate').val()) {
                $('#mErrorBOM').text('<?= lang('tax_rate_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

            if (!$('#tax_method').val()) {
                $('#mErrorBOM').text('<?= lang('tax_method_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }


            if($('#car_status').val()=="old"){

                $('#tax_rate').val(zero_tax_rate);
                $('#tax_method').val(1);



            }
            //alert($('#tax_rate').val());
            //alert($('#tax_method').val());

         //   var code=$('#code').val();
         var vehicle_id=$('#vehicle_id').val();
            var showroom_id=$('#showroom_id').val();
            var brand_id=$('#brand_id').val();
            var model_id=$('#model_id').val();
            var category_id=$('#category_id').val();
            var car_status=$('#car_status').val();
            var color=$('#color').val();
            var image=$('#image').val();
            var mileage=$('#mileage').val();
            var buying_price=$('#buying_price').val();
            var cost=$('#cost').val();

            var wd_type=$('#wd_type').val();
            var registration_year=$('#registration_year').val();
            var cylinder=$('#cylinder').val();
            var engine_capacity=$('#engine_capacity').val();
            var fuel_type=$('#fuel_type').val();
            var specification=$('#specification').val();
            var shape=$('#shape').val();
            var gear=$('#gear').val();
            var doors=$('#doors').val();
            var seats=$('#seats').val();
            var tank=$('#tank').val();
            var chesis_no=$('#chesis_no').val();
            var panel_no=$('#panel_no').val();
            var serial_no=$('#serial_no').val();
            var card_no=$('#card_no').val();
            var customs_statment=$('#customs_statment').val();
            var port_entry=$('#port_entry').val();
            var country_manufacturing=$('#country_manufacturing').val();
            var owner_id=$('#owner_id').val();
            var description=$('#description').val();
            var available_place=$('#available_place').val();
            var insurance_id=$('#insurance_id').val();
            var tax_rate=$('#tax_rate').val();
            var mtax_method=$('#tax_method').val();

            var url;
            var success_msg;
				url="cars/addVehicleByAjax";
				success_msg='<?= lang('Vehicle_added_successfully') ?>';
			//alert(success_msg)

			var msg;


            var mts = $('#image').get(0).files[0];

			var formData = new FormData();

				var data = $('#addCarForm').serializeArray();
				var obj = {};
				for (var i = 0; i < data.length; i++) {
    					//obj[data[i].name] = data[i].value;
    					formData.append([data[i].name], data[i].value);
				}

                formData.append('owner_id', owner_id);
				formData.append('userfile', mts);
				formData.append('token', "<?= $csrf; ?>");






                //type: "post", async: false,
                //url: site.base_url +""+url,
                //data: {token: "<?= $csrf; ?>", showroom_id:showroom_id,brand_id: brand_id, model_id: model_id, category_id: category_id, car_status: car_status, color: color, image: image, mileage: mileage, buying_price: buying_price,cost:cost, wd_type: wd_type, registration_year: registration_year, cylinder: cylinder, engine_capacity: engine_capacity, fuel_type: fuel_type, specification: specification, shape: shape, gear: gear, doors: doors, seats:seats, tank: tank, chesis_no: chesis_no, panel_no: panel_no, serial_no: serial_no, card_no: card_no, customs_statment: customs_statment, port_entry: port_entry, country_manufacturing: country_manufacturing, owner_id: owner_id, description: description , available_place: available_place , insurance_id: insurance_id, tax_rate: tax_rate, mtax_method:mtax_method },
                //dataType: "json",

			try{
            $.ajax({
                type: 'POST',
				data: formData,
					contentType: false,
					cache: false,
					processData:false,
                url: site.base_url +""+url,
                success: function (data) {
                    //console.log(data);

                    if (data.msg == 'success') {
						$("#vehicle_id").val(data.vehicle_id);
						//$('input#add_item').autocomplete("search");

						$('#mSuccessBOM').text(success_msg);

						$('#mSuccess-conBOM').show();
						$("#div_Features").css("display", "block");


                    } else {
                        msg = data.msg;
						$('#mErrorBOM').text(msg);
						$('#mError-conBOM').show();

                    }
                }
            });
        }catch(err){alert(err)}
            return false;

        });


		$(document).on('click', '#save_feature', function (e) {
			var featureIdsE = JSON.stringify($(".featureIdsE").serializeArray());
			var featureIdsC= JSON.stringify($(".featureIdsC").serializeArray());
			var featureIdsT = JSON.stringify($(".featureIdsT").serializeArray());
			var featureIds = JSON.stringify($(".featureIds").serializeArray());
		//$('#jsonArr_product_id').val(formData_product_id);

        var vehicle_id=$("#vehicle_id").val();
        //console.log(jQuery.parseJSON(featureIds));
        //vehicle_id=7;
        //console.log(vehicle_id);

        var url;
            var success_msg;
				url="cars/updateVehicleFeatureByAjax";
				success_msg='<?= lang('Features_added_successfully') ?>';
			//alert(success_msg)

			var msg;


			try{
            $.ajax({
                type: "post", async: false,
                url: site.base_url +""+url,
                data: {token: "<?= $csrf; ?>", vehicle_id: vehicle_id, featureIdsE: jQuery.parseJSON(featureIdsE), featureIdsC: jQuery.parseJSON(featureIdsC), featureIdsT: jQuery.parseJSON(featureIdsT), featureIds: jQuery.parseJSON(featureIds)},
                dataType: "json",
                success: function (data) {
                   // console.log(data);

                    if (data.msg == 'success') {

						$('#mSuccess').text(success_msg);

						$('#mSuccess-con').show();
						//$("#div_addnewItemManually").css("display", "none");


                    } else {
                        msg = data.msg;
						$('#mError').text(msg);
						$('#mError-con').show();

                    }
                }
            });
        }catch(err){alert(err)}
            return false;


		});



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
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('Showroom') ?></label>
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
                <button type="button" class="btn btn-primary" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {


        $('#chesis_no').change(function () {
            localStorage.setItem('cvchesis_no', $(this).val());

        });
        $('#showroom_id').change(function () {
            localStorage.setItem('cvshowroom_id', $(this).val());

        });
        $('#color').change(function () {
            localStorage.setItem('cvcolor', $(this).val());

        });
        $('#mileage').change(function () {
            localStorage.setItem('cvmileage', $(this).val());

        });
        $('#car_status').change(function () {
            localStorage.setItem('cvcar_status', $(this).val());

        });
        $('#buying_price').change(function () {
            localStorage.setItem('cvbuying_price', $(this).val());

        });
        $('#cost').change(function () {
            localStorage.setItem('cvcost', $(this).val());

        });
        $('#wd_type').change(function () {
            localStorage.setItem('cvwd_type', $(this).val());

        });
        $('#registration_year').change(function () {
            localStorage.setItem('cvregistration_year', $(this).val());

        });
        $('#cylinder').change(function () {
            localStorage.setItem('cvcylinder', $(this).val());

        });
        $('#engine_capacity').change(function () {
            localStorage.setItem('cvengine_capacity', $(this).val());

        });
        $('#fuel_type').change(function () {
            localStorage.setItem('cvfuel_type', $(this).val());

        });
        $('#specification').change(function () {
            localStorage.setItem('cvspecification', $(this).val());

        });
        $('#shape').change(function () {
            localStorage.setItem('cvshape', $(this).val());

        });
        $('#gear').change(function () {
            localStorage.setItem('cvgear', $(this).val());

        });
        $('#doors').change(function () {
            localStorage.setItem('cvdoors', $(this).val());

        });
        $('#seats').change(function () {
            localStorage.setItem('cvseats', $(this).val());

        });
        $('#tank').change(function () {
            localStorage.setItem('cvtank', $(this).val());
        });
        $('#panel_no').change(function () {
            localStorage.setItem('cvpanel_no', $(this).val());
        });
        $('#serial_no').change(function () {
            localStorage.setItem('cvserial_no', $(this).val());
        });
        $('#card_no').change(function () {
            localStorage.setItem('cvcard_no', $(this).val());
        });
        $('#customs_statment').change(function () {
            localStorage.setItem('cvcustoms_statment', $(this).val());
        });
        $('#port_entry').change(function () {
            localStorage.setItem('cvport_entry', $(this).val());
        });
        $('#country_manufacturing').change(function () {
            localStorage.setItem('cvcountry_manufacturing', $(this).val());
        });
        $('#owner_id').change(function () {
            localStorage.setItem('cvowner_id', $(this).val());

        });
        $('#description').change(function () {
            localStorage.setItem('cvdescription', $(this).val());

        });

        $('#available_place').change(function () {
            localStorage.setItem('cvavailable_place', $(this).val());

        });
        $('#insurance_id').change(function () {
            localStorage.setItem('cvinsurance_id', $(this).val());

        });
        $('#category_id').change(function () {
            localStorage.setItem('cvcategory_id', $(this).val());

        });

        $('#tax_method').change(function () {
            localStorage.setItem('cvtax_method', $(this).val());

        });



        $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {

                if (localStorage.getItem('cvchesis_no')) {
                    localStorage.removeItem('cvchesis_no');
                }

                if (localStorage.getItem('cvshowroom_id')) {
                    localStorage.removeItem('cvshowroom_id');
                }
                if (localStorage.getItem('cvbrand_id')) {
                    localStorage.removeItem('cvbrand_id');
                }
                if (localStorage.getItem('cvmodel_id')) {
                    localStorage.removeItem('cvmodel_id');
                }
                if (localStorage.getItem('cvcategory_id')) {
                    localStorage.removeItem('cvcategory_id');
                }
                if (localStorage.getItem('cvimage')) {
                    localStorage.removeItem('cvimage');
                }

                if (localStorage.getItem('cvcar_status')) {
                    localStorage.removeItem('cvcar_status');
                }
                if (localStorage.getItem('cvcolor')) {
                    localStorage.removeItem('cvcolor');
                }
                if (localStorage.getItem('cvmileage')) {
                    localStorage.removeItem('cvmileage');
                }
                if (localStorage.getItem('cvbuying_price')) {
                    localStorage.removeItem('cvbuying_price');
                }
                if (localStorage.getItem('cvcost')) {
                    localStorage.removeItem('cvcost');
                }
                if (localStorage.getItem('cvwd_type')) {
                    localStorage.removeItem('cvwd_type');
                }
                if (localStorage.getItem('cvregistration_year')) {
                    localStorage.removeItem('cvregistration_year');
                }
                if (localStorage.getItem('cvcylinder')) {
                    localStorage.removeItem('cvcylinder');
                }
                if (localStorage.getItem('cvengine_capacity')) {
                    localStorage.removeItem('cvengine_capacity');
                }
                if (localStorage.getItem('cvfuel_type')) {
                    localStorage.removeItem('cvfuel_type');
                }
                if (localStorage.getItem('cvspecification')) {
                    localStorage.removeItem('cvspecification');
                }
                if (localStorage.getItem('cvshape')) {
                    localStorage.removeItem('cvshape');
                }
                if (localStorage.getItem('cvgear')) {
                    localStorage.removeItem('cvgear');
                }
                if (localStorage.getItem('cvdoors')) {
                    localStorage.removeItem('cvdoors');
                }
                if (localStorage.getItem('cvseats')) {
                    localStorage.removeItem('cvseats');
                }
                if (localStorage.getItem('cvtank')) {
                    localStorage.removeItem('cvtank');
                }
                if (localStorage.getItem('cvpanel_no')) {
                    localStorage.removeItem('cvpanel_no');
                }
                if (localStorage.getItem('cvserial_no')) {
                    localStorage.removeItem('cvserial_no');
                }
                if (localStorage.getItem('cvcard_no')) {
                    localStorage.removeItem('cvcard_no');
                }
                if (localStorage.getItem('cvcustoms_statment')) {
                    localStorage.removeItem('cvcustoms_statment');
                }
                if (localStorage.getItem('cvport_entry')) {
                    localStorage.removeItem('cvport_entry');
                }
                if (localStorage.getItem('cvcountry_manufacturing')) {
                    localStorage.removeItem('cvcountry_manufacturing');
                }
                if (localStorage.getItem('cvowner_id')) {
                    localStorage.removeItem('cvowner_id');
                }
                if (localStorage.getItem('cvdescription')) {
                    localStorage.removeItem('cvdescription');
                }
                 if (localStorage.getItem('cvavailable_place')) {
                    localStorage.removeItem('cvavailable_place');
                }
                if (localStorage.getItem('cvinsurance_id')) {
                    localStorage.removeItem('cvinsurance_id');
                }
                if (localStorage.getItem('cvtax_rate')) {
                    localStorage.removeItem('cvtax_rate');
                }
                if (localStorage.getItem('cvtax_method')) {
                    localStorage.removeItem('cvtax_method');
                }



                $('#modal-loading').show();
                //location.reload();
                document.location.href=site.base_url + 'cars/add_vehicle';
            }
        });
    });

        $('#tax_rate').change(function () {

            localStorage.setItem('cvtax_rate', $(this).val());


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


		var pi = 'amount_1', pa = 2;//,KB = <?=$pos_settings->keyboard?>;
        $('#random_num').click(function() {
            $(this)
                .parent('.input-group')
                .children('input')
                .val(generateCardNo(8));
        });
        function generateCardNo(x) {
        if (!x) {
            x = 16;
        }
        chars = '1234567890';
        no = '';
        for (var i = 0; i < x; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            no += chars.substring(rnum, rnum + 1);
        }
        return no;
        }


		var costCount  = 0;
		 $('#externalequipment').slideDown();
        $('button#addExternalEquipment').click(function(){
			var divOption    = '<div  id="costRow'+costCount+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div class="row" ><select class="form-control col-8 featureIdsE"  name="featureIdsE[]"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($features_external_equipment as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['feature_name']}</option>';
				";
			}
			?>
			selectOption += '</select><div class="col-1 text-center"> <span id="removeDebit[]" <i class="fa fa-times delAttr"></i></span></div></div>';                               //       fa fa-times attr-remove-all  <td class="text-center"><i class="fa fa-times delAttr"></i></td> fa fa-trash-o

			//var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');
			var buttonOption  = $('</div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#externalequipment').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(buttonOption);

			costCount += 1;

		});

		var costCount  = 0;
			        $('#comfort').slideDown();
        $('button#addComfort').click(function(){
			var divOption    = '<div  id="costRow'+costCount+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div class="row" ><select class="form-control col-8 featureIdsC"  name="featureIdsC[]"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($featured_comfort as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['feature_name']}</option>';
				";
			}
			?>
			selectOption += '</select><div class="col-1 text-center"> <span id="removeDebit[]" <i class="fa fa-times delAttr"></i></span></div></div>';

			//var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');
			var buttonOption  = $('</div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#comfort').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(buttonOption);

			costCount += 1;

		});

		var costCount  = 0;
	  $('#techniques').slideDown();
        $('button#addTechniques').click(function(){
			var divOption    = '<div  id="costRow'+costCount+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div class="row"><select class="form-control col-8 featureIdsT"  name="featureIdsT[]"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($featured_techniques as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['feature_name']}</option>';
				";
			}
			?>
			selectOption += '</select><div class="col-1 text-center"> <span id="removeDebit[]" <i class="fa fa-times delAttr"></i></span></div></div>';                               //       fa fa-times attr-remove-all  <td class="text-center"><i class="fa fa-times delAttr"></i></td> fa fa-trash-o

			//var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');
			var buttonOption  = $('</div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#techniques').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(buttonOption);

			costCount += 1;

		});

        var costCount  = 0;
	      $('#CostCenters').slideDown();

		$('button#addFeature').click(function(){
			var divOption    = '<div  id="costRow'+costCount+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div class="row" ><select class="form-control col-8 featureIds"  name="featureIds[]"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($features_safty as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['feature_name']}</option>';
				";
			}
			?>
			selectOption += '</select><div class="col-1 text-center"> <span id="removeDebit[]" <i class="fa fa-times delAttr"></i></span></div></div>';                               //       fa fa-times attr-remove-all  <td class="text-center"><i class="fa fa-times delAttr"></i></td> fa fa-trash-o

			//var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');
			var buttonOption  = $('</div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCenters').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(buttonOption);

			costCount += 1;
            //divCol += '</div>';
		});


        $(document).on('ifChecked', '#rmr', function (e) {
            $('#req_mr').slideDown();
        });
        $(document).on('ifUnchecked', '#rmr', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#req_mr').slideUp();
        });

		$(document).on('ifChecked', '#rmr_ee', function (e) {
            $('#req_ee').slideDown();
        });
        $(document).on('ifUnchecked', '#rmr_ee', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#req_ee').slideUp();
        });
        $(document).on('ifChecked', '#rmr_c', function (e) {
            $('#req_c').slideDown();
        });
        $(document).on('ifUnchecked', '#rmr_c', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#req_c').slideUp();
        });
        $(document).on('ifChecked', '#rmr_t', function (e) {
            $('#req_t').slideDown();
        });
        $(document).on('ifUnchecked', '#rmr_t', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#req_t').slideUp();
        });
		$(document).on('ifChecked', '#dmr', function (e) {
            $('#dest_mr').slideDown();
        });
        $(document).on('ifUnchecked', '#dmr', function (e) {
            //$(".select-tags").select2("val", "");
            //$('.attr-remove-all').trigger('click');
            $('#dest_mr').slideUp();
        });





        if (cvchesis_no = localStorage.getItem('cvchesis_no')) {
            $('#chesis_no').val(cvchesis_no);
        }
        if (cvshowroom_id = localStorage.getItem('cvshowroom_id')) {
            $('#showroom_id').val(cvshowroom_id);
        }
        if (cvbrand_id = localStorage.getItem('cvbrand_id')) {
            $('#brand_id').val(cvbrand_id).trigger('change');
        }
        if (cvmodel_id = localStorage.getItem('cvmodel_id')) {
            $('#model_id').val(cvmodel_id).trigger('change');
        }
        if (cvcategory_id = localStorage.getItem('cvcategory_id')) {
            $('#category_id').val(cvcategory_id);
        }
        if (cvcolor = localStorage.getItem('cvcolor')) {
            $('#color').val(cvcolor);
        }


        if (cvcar_status = localStorage.getItem('cvcar_status')) {
            $('#car_status').val(cvcar_status);
        }
        if (cvmileage = localStorage.getItem('cvmileage')) {
            $('#mileage').val(cvmileage);
        }
        if (cvbuying_price = localStorage.getItem('cvbuying_price')) {
            $('#buying_price').val(cvbuying_price);
        }
        if (cvcost = localStorage.getItem('cvcost')) {
            $('#cost').val(cvcost);
        }
        if (cvwd_type = localStorage.getItem('cvwd_type')) {
            $('#wd_type').val(cvwd_type);
        }
        if (cvregistration_year = localStorage.getItem('cvregistration_year')) {
            $('#registration_year').val(cvregistration_year);
        }
        if (cvcylinder = localStorage.getItem('cvcylinder')) {
            $('#cylinder').val(cvcylinder);
        }
        if (cvengine_capacity = localStorage.getItem('cvengine_capacity')) {
            $('#engine_capacity').val(cvengine_capacity);
        }
        if (cvfuel_type = localStorage.getItem('cvfuel_type')) {
            $('#fuel_type').val(cvfuel_type);
        }
        if (cvspecification = localStorage.getItem('cvspecification')) {
            $('#specification').val(cvspecification);
        }
        if (cvshape = localStorage.getItem('cvshape')) {
            $('#shape').val(cvshape);
        }
        if (cvgear = localStorage.getItem('cvgear')) {
            $('#gear').val(cvgear);
        }
        if (cvdoors = localStorage.getItem('cvdoors')) {
            $('#doors').val(cvdoors);
        }
        if (cvseats = localStorage.getItem('cvseats')) {
            $('#seats').val(cvseats);
        }
        if (cvtank = localStorage.getItem('cvtank')) {
            $('#tank').val(cvtank);
        }
        if (cvpanel_no = localStorage.getItem('cvpanel_no')) {
            $('#panel_no').val(cvpanel_no);
        }
        if (cvserial_no = localStorage.getItem('cvserial_no')) {
            $('#serial_no').val(cvserial_no);
        }
        if (cvcard_no = localStorage.getItem('cvcard_no')) {
            $('#card_no').val(cvcard_no);
        }
        if (cvcustoms_statment = localStorage.getItem('cvcustoms_statment')) {
            $('#customs_statment').val(cvcustoms_statment);
        }
        if (cvport_entry = localStorage.getItem('cvport_entry')) {
            $('#port_entry').val(cvport_entry);
        }
        if (cvcountry_manufacturing = localStorage.getItem('cvcountry_manufacturing')) {
            $('#country_manufacturing').val(cvcountry_manufacturing);
        }

        if (cvowner_id = localStorage.getItem('cvowner_id')) {
            $('#owner_id').val(cvowner_id).trigger('change');
        }
        if (cvdescription = localStorage.getItem('cvdescription')) {
            $('#description').val(cvdescription);
        }
        if (cvavailable_place = localStorage.getItem('cvavailable_place')) {
            $('#available_place').val(cvavailable_place);
        }
        if (cvinsurance_id = localStorage.getItem('cvinsurance_id')) {
            $('#insurance_id').val(cvinsurance_id);
        }
        if (cvtax_rate = localStorage.getItem('cvtax_rate')) {
            $('#tax_rate').val(cvtax_rate);
        }
        if (cvtax_method = localStorage.getItem('cvtax_method')) {
            $('#tax_method').val(cvtax_method);
        }




    });
    </script>