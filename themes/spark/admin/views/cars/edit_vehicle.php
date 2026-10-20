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

function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});

}

    $(document).ready(function () {
      //  search_select();

      <?php if ($this->input->get('supplier')) {
        ?>
        if (!localStorage.getItem('poitems')) {
            localStorage.setItem('posupplier', <?=$this->input->get('supplier'); ?>);
        }
        <?php
    } ?>


       $('#car_status').change(function () {
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
        $('#brand_id').val('<?= $vehicle->brand_id ?>').trigger('change');
		$('#model_id').val('<?= $vehicle->model_id ?>').trigger('change');
		$('#category_id').val('<?= $vehicle->category_id ?>').trigger('change');

    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file-text-o nb"></i><?= lang('Edit_Vehicle'); ?></h2>
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
            <input type="hidden" id="vehicle_id" value="<?= $vehicle->vehicle_id ?>"/>
            <form id="editCarForm" method="post"  enctype="multipart/form-data">

				<div class="row">

                <div class="col-sm-3">
            <!--     <div class="form-group all">
                      <?= lang('code', 'code') ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($vehicle ? $vehicle->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        	</div> -->


                 <div class="form-group">
                                <?= lang('Chesis_no', 'chesis_no'); ?>
                                <?= form_input('chesis_no', $vehicle->chesis_no, 'class="form-control gen_slug" id="chesis_no" required="required"'); ?>
                 </div>
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
                        <?= form_dropdown('showroom_id', $sh, $vehicle->showroom_id, 'class="form-control tip" id="showroom_id" required="required" style="width:100%;"'); ?>

							<?php if ($Owner || $Admin ) {
                         ?>
                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('cars/add_showroom'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                                </div>
                        <?php
                        } ?>

                    </div>
                    </div>

                 <div class="form-group">
                      <label class="control-label" for="brand_id"><?= lang('brand'); ?></label>

                      <div class="input-group">
                            <?php
                            $br['']='';
                            foreach ($brands as $brand) {
                                $br[$brand->id] = $brand->brand_name;
                            }

                            echo form_dropdown('brand_id', $br, $vehicle->brand_id, 'id="brand_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('brand') . '" required="required" style="width:100%;" '); ?>

                                    <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                            <a href="<?= admin_url('cars/add_brand'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                                <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                            </a>
                                    </div>

                         </div>

                 </div>
                <div class="form-group">
                <label class="control-label" for="model_id"><?= lang('Car_Type'); ?></label>
                  <!--  <?= lang('Car_Type', 'model_name'); ?> -->
                    <div class="input-group">
                        <?php
                        $mod=[];
                        echo form_dropdown('model_id', $mod, '$vehicle->model_id', 'id="model_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('model') . '" required="required" style="width:100%;" '); ?>
                             <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                            <a href="<?= admin_url('cars/add_model'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                                <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                            </a>
                            </div>
                     </div>
                </div>

                <div class="form-group">
                <label class="control-label" for="category_id"><?= lang('category_name'); ?></label>
                 <!--   <?= lang('category_name', 'categ_name'); ?> -->
                    <div class="input-group">
                        <?php
                        $cat=[];
                        echo form_dropdown('category_id', $cat, '$vehicle->category_id', 'id="category_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('category') . '" required="required" style="width:100%;" '); ?>
                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                <a href="<?= admin_url('cars/add_category'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                                    <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                </a>
                        </div>
                    </div>
                </div>


            <div class="form-group">
                <?= lang('selling_price', 'buying_price'); ?>
                <?= form_input('buying_price', $vehicle->buying_price, 'class="form-control gen_slug" id="buying_price"  '); ?>
            </div>


            <div class="form-group">
                <?= lang('cost', 'cost'); ?>
                <?= form_input('cost', $vehicle->cost, 'class="form-control gen_slug" id="cost" '); ?>

            </div>



            <div class="form-group">
                <?= lang('Model_or_Registration_Year', 'registration_year'); ?>
                <input class="form-control "  name="registration_year" id="registration_year"  type="number" min="1950" step="01" value="<?php echo $vehicle->registration_year; ?>"  />

                <!--<?= form_input('registration_year', $vehicle->registration_year, 'class="form-control gen_slug" id="registration_year" '); ?> -->
            </div>

            <div class="form-group" style="display:none;">

                           <?= lang('Status','status'); ?>

                           <?php $status_options =  ['available' => lang('Available'), 'sold' => lang('sold')]; ?>
                           <?= form_dropdown('status', $status_options, $vehicle->status, 'class="form-control tip" id="status" '); ?>
            </div>

           </div>
              <div class="col-sm-3">
                       <div class="form-group">
                                <?= lang('Panel_no', 'panel_no'); ?>
                                <?= form_input('panel_no', $vehicle->panel_no, 'class="form-control gen_slug" id="panel_no" '); ?>
                          </div>

                         <div class="form-group">
                        <?= lang('Car_status','car_status'); ?>

                        <?php $car_status_options =  ['new' => lang('New'), 'old' => lang('old')]; ?>
                        <?= form_dropdown('car_status', $car_status_options, $vehicle->car_status, 'class="form-control tip" id="car_status" '); ?>
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
                                echo form_dropdown('tax_rate', $tr, $tr_default, 'id="tax_rate"   class="form-control input-tip select" style="width:100%;"'); ?>
                        </div>
                        <div class="form-group all" id="div_tax_method" style="display:block;">
                                <?= lang('tax_method', 'tax_method') ?>
                                <?php
                                $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                                echo form_dropdown('tax_method', $tm, '$vehicle->tax_method', 'class="form-control select" id="tax_method"  placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
                        </div>

              <div class="form-group" id="div_mileage" style="display:none;">
                <?= lang('Mileage', 'mileage'); ?>
                <?= form_input('mileage', $vehicle->mileage, 'class="form-control gen_slug" id="mileage" '); ?>
            </div>



            <div class="form-group">
                <?= lang('Wd_Type', 'wd_type'); ?>
                  <!-- <input class="form-control "  name="wd_type" id="wd_type"  type="number" min="0" step="01" value="<?php echo $vehicle->wd_type; ?>"  /> -->
                 <?= form_input('wd_type', $vehicle->wd_type, 'class="form-control gen_slug" id="wd_type" '); ?>



            </div>


            <div class="form-group">
                <?= lang('Cylinder', 'cylinder'); ?>
                <input class="form-control "  name="cylinder" id="cylinder"  type="number" min="0" step="01" value="<?php echo $vehicle->cylinder; ?>"  />

                <!-- <?= form_input('cylinder', $vehicle->cylinder, 'class="form-control gen_slug" id="cylinder" '); ?> -->
            </div>

            <div class="form-group">
                <?= lang('Engine_Capacity', 'engine_capacity'); ?>
                <input class="form-control "  name="engine_capacity" id="engine_capacity"  type="number" min="0" step="01" value="<?php echo $vehicle->engine_capacity; ?>"  />

               <!-- <?= form_input('engine_capacity', $vehicle->engine_capacity, 'class="form-control gen_slug" id="engine_capacity" '); ?> -->
            </div>

            <div class="form-group">
                <?= lang('Fuel_Type', 'fuel_type'); ?>
                 <?php $fuel_type_options =  ['gasoline' => lang('Gasoline'), 'diesel' => lang('Diesel'),'hybird' => lang('Hybird'),'electricity' => lang('Electricity')]; ?>
                <?= form_dropdown('fuel_type', $fuel_type_options, $vehicle->fuel_type, 'class="form-control tip" id="fuel_type" '); ?>

            </div>



            </div>

            <div class="col-sm-3">
                        <div class="form-group">
                            <?= lang('Added_on', 'crdate'); ?>
                            <?= form_input('date', $this->sma->hrsd($vehicle->date), 'readonly class="form-control input-tip date" id="crdate" '); ?>
                        </div>
                        <div class="form-group">

                           <?= lang('Car_Place','available_place'); ?>

                           <?= form_input('available_place', $vehicle->available_place, 'class="form-control gen_slug" id="available_place" '); ?>

                       </div>

                        <div class="form-group">
                            <?= lang('Color', 'color'); ?>
                            <?= form_input('color', $vehicle->color, 'class="form-control gen_slug" id="color" '); ?>
                        </div>

                        <div class="form-group">
                                <?= lang('Shape', 'shape'); ?>
                                <?= form_input('shape', $vehicle->shape, 'class="form-control gen_slug" id="shape" '); ?>
                          </div>

                        <div class="form-group">
                                <?= lang('Gear', 'gear'); ?>
                                <?= form_input('gear', $vehicle->gear, 'class="form-control gen_slug" id="gear"'); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Doors', 'doors'); ?>
                                <input class="form-control "  name="doors" id="doors"  type="number" min="0" step="01" value="<?php echo $vehicle->doors; ?>"  />

                                <!--<?= form_input('doors', $vehicle->doors, 'class="form-control gen_slug" id="doors" '); ?> -->
                          </div>
                          <div class="form-group">
                                <?= lang('Seats', 'seats'); ?>
                                <input class="form-control "  name="seats" id="seats"  type="number" min="0" step="01" value="<?php echo $vehicle->seats; ?>"  />

                              <!--  <?= form_input('seats', $vehicle->seats, 'class="form-control gen_slug" id="seats" '); ?> -->
                          </div>
                          <div class="form-group">
                                <?= lang('Tank', 'tank'); ?>
                                <input class="form-control "  name="tank" id="tank"  type="number" min="0" step="01" value="<?php echo $vehicle->tank; ?>"  />

                              <!--  <?= form_input('tank', $vehicle->tank, 'class="form-control gen_slug" id="tank" '); ?> -->
                          </div>



                          </div> <!-- end div col-sm-3 -->
                          <div class="col-sm-3">

                          <div class="form-group">
                                <?= lang('Serial_no', 'serial_no'); ?>
                                <?= form_input('serial_no', $vehicle->serial_no, 'class="form-control gen_slug" id="serial_no" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Card_no', 'card_no'); ?>

                                <?= form_input('card_no', $vehicle->card_no, 'class="form-control gen_slug" id="card_no" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Customs_Statment', 'customs_statment'); ?>
                                <?= form_input('customs_statment', $vehicle->customs_statment, 'class="form-control gen_slug" id="customs_statment" '); ?>
                          </div>

                          <div class="form-group">
                                <?= lang('Insurance_id', 'insurance_id'); ?>
                                <?= form_input('insurance_id', $vehicle->insurance_id, 'class="form-control gen_slug" id="insurance_id" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Port_Entry', 'port_entry'); ?>
                                <?= form_input('port_entry', $vehicle->port_entry, 'class="form-control gen_slug" id="port_entry" '); ?>
                          </div>
                          <div class="form-group">
                                <?= lang('Country_Manufacturing', 'country_manufacturing'); ?>
                                <?= form_input('country_manufacturing', $vehicle->country_manufacturing, 'class="form-control gen_slug" id="country_manufacturing" '); ?>
                          </div>

                             <div class="form-group">
                                 <?= lang('Specifications', 'specification'); ?>
                                <?= form_input('specification', $vehicle->specification, 'class="form-control gen_slug" id="specification" '); ?>
                            </div>


                          </div> <!-- end div col-sm-4 -->
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
                                                       class="form-control" style="width:100%;"
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
                                    <?= form_textarea('description', $vehicle->description, 'class="form-control" id="description"'); ?>
                                </div>

                            </div>
						</div>


							<div class="col-sm-12 " style="padding-bottom:30px;">
								<button type="button" class="btn btn-primary" id="edit_vehicle"><?= lang('Edit_Vehicle') ?></button>

							</div>

                            </form>


							<div class="clearfix"></div>





							<div id="div_Features"  style="display:block;">
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

                                    <div class="form-group ">
                                            <input type="checkbox" class="checkbox" name="rmr_ee" <?php  { echo "checked"; } ?>
                                           id="rmr_ee"><label for="rmr_ee" class="padding05"><?= lang('External_Equipment'); ?></label>


                                        <?php
                                      $vehicle_e = (array) $vehicle_e;
                                        $vehicle_e['featured_external_equipment'] = json_decode($vehicle->featured_external_equipment);

                                        $featuredE_row_index=0;
                                            foreach ($vehicle_e['featured_external_equipment'] as $Debit){

								?>

								<div class="row " id="<?= 'costRowE'.$featuredE_row_index ?>" >





							<select class="form-control col-8 featureIdsE" name="featureIdsE[]" id="<?= 'costRowE'.$featuredE_row_index ?>" data-toggle="tooltip" data-placement="left" title="Debit Account ">
								<?php

								foreach ($features_external_equipment as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['feature_name']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['feature_name']}</option>
									";
									}

								}
								?>
							</select>
						<div class="col-sm-1 text-center"> <span id="removefeatureIdsE[]"  > <i class="fa fa-times delAttr" onclick="costRemove('<?= 'costRowE'.$featuredE_row_index ?>')"></i></span></div>
					</div>
			<?php

                           $featuredE_row_index++;
                        }
                            ?>





                                    </div>
                                </div>
                                <div id="req_ee" <?php  { echo "style='display:block;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-md-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addExternalEquipment" class="btn btn-primary" type="button"><?= lang('Add_Feature') ?></button>
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
                                            <input type="checkbox" class="checkbox" name="rmr_c" <?php  { echo "checked"; } ?>
                                           id="rmr_c"><label for="rmr_c" class="padding05"><?= lang('Comfort'); ?></label>


                                        <?php
                                      $vehicle_c = (array) $vehicle_c;
                                        $vehicle_c['featured_comfort'] = json_decode($vehicle->featured_comfort);

                                        $featuredC_row_index=0;
                                            foreach ($vehicle_c['featured_comfort'] as $Debit){

								?>

								<div class="row " id="<?= 'costRowC'.$featuredC_row_index ?>" >





							<select class="form-control col-8 featureIdsC"  name="featureIdsC[]" id="<?= 'costRowC'.$featuredC_row_index ?>" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($featured_comfort as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['feature_name']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['feature_name']}</option>
									";
									}

								}
								?>
							</select>
						<div class="col-sm-1 text-center"> <span id="removefeatureIdsC[]"  > <i class="fa fa-times delAttr" onclick="costRemove('<?= 'costRowC'.$featuredC_row_index ?>')"></i></span></div>
					</div>
        			<?php

                           $featuredC_row_index++;
                        }
                            ?>





                                    </div>
                                </div>
                                <div id="req_c" <?php  { echo "style='display:block;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">
                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button id="addComfort" class="btn btn-primary" type="button"><?= lang('Add_Feature') ?></button>
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
                                            <input type="checkbox" class="checkbox" name="rmr_t" <?php  { echo "checked"; } ?>
                                           id="rmr_t"><label for="rmr_t" class="padding05"><?= lang('Techniques'); ?></label>


                                        <?php
                                      $vehicle_t = (array) $vehicle_t;
                                        $vehicle_t['featured_techniques'] = json_decode($vehicle->featured_techniques);

                                        $featuredT_row_index=0;
                                            foreach ($vehicle_t['featured_techniques'] as $Debit){

								?>

								<div class="row " id="<?= 'costRowT'.$featuredT_row_index ?>" >





							<select class="form-control col-8 featureIdsT" name="featureIdsT[]" id="<?= 'costRowT'.$featuredT_row_index ?>" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($featured_techniques as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['feature_name']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['feature_name']}</option>
									";
									}

								}
								?>
							</select>
						<div class="col-sm-1 text-center"> <span id="removefeatureIdsT[]"  > <i class="fa fa-times delAttr" onclick="costRemove('<?= 'costRowT'.$featuredT_row_index ?>')"></i></span></div>
					</div>
			        <?php

                           $featuredT_row_index++;
                        }
                            ?>





                                    </div>
                                </div>
                                <div id="req_t" <?php  { echo "style='display:block;'"; } ?>>
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







                            <div class="col-sm-3" >
                                <div id="safty">

                                    <div class="form-group">
                                            <input type="checkbox" class="checkbox" name="rmr_s" <?php  { echo "checked"; } ?>
                                           id="rmr_s"><label for="rmr_s" class="padding05"><?= lang('Safty'); ?></label>


                                        <?php
                                      $vehicle_s = (array) $vehicle_s;
                                        $vehicle_s['featured'] = json_decode($vehicle->featured);

                                        $featuredS_row_index=0;
                                            foreach ($vehicle_s['featured'] as $Debit){

								?>

								<div class="row " id="<?= 'costRowS'.$featuredS_row_index ?>" >





							<select class="form-control col-8 featureIds"  name="featureIds[]" id="<?= 'costRowS'.$featuredS_row_index ?>" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($features_safty as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['feature_name']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['feature_name']}</option>
									";
									}

								}
								?>
							</select>
						<div class="col-sm-1 text-center"> <span id="removefeatureIds[]"  > <i class="fa fa-times delAttr" onclick="costRemove('<?= 'costRowS'.$featuredS_row_index ?>')"></i></span></div>
					</div>
			        <?php

                           $featuredS_row_index++;
                        }
                            ?>





                                    </div>
                                </div>
                                <div id="req_s" <?php  { echo "style='display:block;'"; } ?>>
                                    <div class="combo1" >
                                        <div class="col-sm-8" >
                                            <div class="form-group">

                                                <div  style="padding-top:15px;padding-left:30px;padding-bottom:10px;padding-right:25px;" >

                                                    <button id="addSafty" class="btn btn-primary " type="button"><?= lang('Add_Feature') ?></button>
                                                    <!-- btn-block -->

                                                </div>
		                                    </div>
                                        </div>
                                        <div id="ex-combo"></div>
                                    </div>
                                </div>
                            </div>


                            </div> <!-- col-sm-12 -->











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
function costRemove(id){

	//alert(id)
	$('#'+id).remove();
}

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


		$(document).on('click', '#edit_vehicle', function (e) {

            if (!$('#chesis_no').val()) {
                $('#mErrorBOM').text('<?= lang('chesis_no_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
         /*   if (!$('#code').val()) {
                $('#mErrorBOM').text('<?= lang('code_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            } */

            if (!$('#showroom_id').val()) {
                $('#mErrorBOM').text('<?= lang('showroom_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }

			if (!$('#brand_id').val()) {
                $('#mErrorBOM').text('<?= lang('brand_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#model_id').val()) {
                $('#mErrorBOM').text('<?= lang('Car_type_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#category_id').val()) {
                $('#mErrorBOM').text('<?= lang('category_name_is_required') ?>');
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
            var tax_method=$('#tax_method').val();
             var url;
            var success_msg;
				url="cars/editVehicleByAjax";
				success_msg='<?= lang('Vehicle_updated_successfully') ?>';
			//alert(success_msg)

			var msg;
			//	console.log({token: "<?= $csrf; ?>", showroom_id:showroom_id,brand_id: brand_id, model_id: model_id, category_id: category_id, car_status: car_status, color: color, image: image, mileage: mileage, buying_price: buying_price, wd_type: wd_type, registration_year: registration_year, cylinder: cylinder, engine_capacity: engine_capacity, fuel_type: fuel_type, specification: specification, shape: shape, gear: gear, doors: doors, seats:seats, tank: tank, chesis_no: chesis_no, panel_no: panel_no, serial_no: serial_no, card_no: card_no, customs_statment: customs_statment, port_entry: port_entry, country_manufacturing: country_manufacturing, owner_id: owner_id, description: description , available_place: available_place , insurance_id: insurance_id});
            //return false;


            var mts = $('#image').get(0).files[0];

			var formData = new FormData();

				var data = $('#editCarForm').serializeArray();
				var obj = {};
				for (var i = 0; i < data.length; i++) {
    					//obj[data[i].name] = data[i].value;
    					formData.append([data[i].name], data[i].value);
				}

                formData.append('vehicle_id', vehicle_id);
				formData.append('owner_id', owner_id);
				formData.append('userfile', mts);
				formData.append('token', "<?= $csrf; ?>");

                //type: "post", async: false,
                //url: site.base_url +""+url,
                //data: {token: "<?= $csrf; ?>", vehicle_id: vehicle_id, showroom_id:showroom_id,brand_id: brand_id, model_id: model_id, category_id: category_id, car_status: car_status, color: color, image: image, mileage: mileage, buying_price: buying_price,cost:cost, wd_type: wd_type, registration_year: registration_year, cylinder: cylinder, engine_capacity: engine_capacity, fuel_type: fuel_type, specification: specification, shape: shape, gear: gear, doors: doors, seats:seats, tank: tank, chesis_no: chesis_no, panel_no: panel_no, serial_no: serial_no, card_no: card_no, customs_statment: customs_statment, port_entry: port_entry, country_manufacturing: country_manufacturing, owner_id: owner_id, description: description , available_place: available_place , insurance_id: insurance_id, tax_rate: tax_rate, tax_method:tax_method },

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
                   // console.log(data);

                    if (data.msg == 'success') {
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
        //console.log((featureIdsE));
        //return false;
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

        $('#tax_rate').change(function () {



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



        var costCountE  = <?= $featuredE_row_index ?>;
        //alert(costCountE)
		 //$('#externalequipment').slideDown();
        $('button#addExternalEquipment').click(function(){
//            alert('');
			var divOption    = '<div  id="costRowE'+costCountE+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div  class="row"><select class="form-control col-8 featureIdsE"  name="featureIdsE[]"   id="featureIdsE'+costCountE+'"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
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
			$('#costRowE'+costCountE).append(selectOption);
			$('#costRowE'+costCountE).append(buttonOption);

			costCountE += 1;

		});




        var costCountC  = <?= $featuredC_row_index ?>;
        //alert(costCountC)
		 //$('#externalequipment').slideDown();
        $('button#addComfort').click(function(){
            //alert('');
			var divOption    = '<div id="costRowC'+costCountC+'">';

			var selectOption = '<div  class="row" ><select class="form-control col-8 featureIdsC"  name="featureIdsC[]"   id="featureIdsC'+costCountC+'"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($featured_comfort as $account) {
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

			$('#comfort').append(divOption);
			$('#costRowC'+costCountC).append(selectOption);
			$('#costRowC'+costCountC).append(buttonOption);

			costCountC += 1;

		});


        var costCountT  = <?= $featuredT_row_index ?>;


        //alert(costCountT)
		 //$('#externalequipment').slideDown();
        $('button#addTechniques').click(function(){
            //alert('');
			var divOption    = '<div id="costRowT'+costCountT+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div  class="row" ><select class="form-control col-8 featureIdsT"  name="featureIdsT[]"   id="featureIdsT'+costCountT+'"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($featured_techniques as $account) {
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

			$('#techniques').append(divOption);
			$('#costRowT'+costCountT).append(selectOption);
			$('#costRowT'+costCountT).append(buttonOption);

			costCountT += 1;

		});


        var costCountS  = <?= $featuredS_row_index ?>;
        //alert(costCountS)
		 //$('#externalequipment').slideDown();
        $('button#addSafty').click(function(){
            //alert('');
			var divOption    = '<div  id="costRowS'+costCountS+'">';

            //var divCol ='<div class="col-4" >';
			var selectOption = '<div class="row" ><select class="form-control col-8 featureIds"  name="featureIds[]"   id="featureId'+costCountS+'"   data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($features_safty as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['feature_name']}</option>';
				";
			}
			?>
			selectOption += '</select> <div class="col-1 text-center"> <span id="removeDebit[]" <i class="fa fa-times delAttr"></i></span></div></div>';

			//var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');
			var buttonOption  = $('</div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#safty').append(divOption);
			$('#costRowS'+costCountS).append(selectOption);
			$('#costRowS'+costCountS).append(buttonOption);

			costCountS += 1;

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
        $('#owner_id').val('<?= $vehicle->owner_id ?>').trigger('change');
		$("#vehicle_id").val('<?= $vehicle->vehicle_id ?>');


    });
    </script>

