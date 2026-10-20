<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
   <script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
<style>
 .badge{
    display:initial;
 }
 .cars_link{
     cursor: pointer;
 }
</style>    
<div class="box">
    <div class="box-header">
          <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('Cars_list') ; ?>
        
         <?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?></h2>

        
        <div class="box-icon">
            <ul class="btn-tasks">
                  <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    
                        <!--<i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                        <i class="btn  btn-sm btn-def-action "style="font-weight: bold;" ><?=lang('actions') ?>&nbsp;&nbsp<span class="caret"></span>  </i>-->
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('cars/add_vehicle') ?>" >
                                <i class="fa fa-plus-circle"></i> <?= lang('Add_Vehicle') ?>
                            </a>
                
                    </ul>
                </li>
                <?php if (!empty($warehouses)) {
                ?>
                    <li class="dropdown" style="display:none;">
                    
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang('warehouses') ?>"></i></a>
                        
                        <!--<i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang('warehouses') ?>"></i>
                         <i class="btn  btn-sm btn-def " ><?=lang('warehouses')?>&nbsp;&nbsp<span class="icon fa fa-filter " data-placement="left"></span></i>-->
                         </a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?= admin_url('products') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                            <li class="divider"></li>
                            <?php
                            foreach ($warehouses as $warehouse) {
                                echo '<li><a href="' . admin_url('products/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                            } ?>
                        </ul>
                    </li>
                <?php
            } ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
            
				<!-- Dark table start -->
                    <table id="vehiclesTable"  class="table table-striped jambo_table bulk_action reports-table">
										<thead class="text-capitalize">
                                            <tr>
                                                <th><?= lang('Sr#') ?></th>
                                                <th><?= lang('image') ?></th>
                                                <th><?= lang('Added_on') ?></th>
												<th><?= lang('Showroom'); ?></th>
                                                <th><?= lang('brand'); ?></th>
                                                <th><?= lang('Car_Type'); ?></th>
                                                <th><?= lang('category'); ?></th>
                                                <th><?= lang('Status') ?></th>
                                                <th><?= lang('Car_Place') ?></th>
												<th><?= lang('price') ?></th>
												<th><?= lang('Sold_on') ?></th>
                                                <!--<th><?= lang('action') ?></th> -->
                                                <th style="width:100px;"><?= lang('actions'); ?></th>
                                                
											</tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; foreach ($vehicles as $key => $value) : ?>
                                            <tr id="<?php echo $value->vehicle_id; ?>">
                                            <td><?php echo $i; ?></td>
                                                <td width="15%"><img class="img-responsive" src="<?php echo base_url()."assets/uploads/".$value->image; ?>"></td>
                                                <td><?php echo $this->sma->hrld($value->created_date); ?></td>
                                                <td><?php echo $value->showroom_name; ?></td>
                                                <td><?php echo $value->brand_name; ?></td>
                                                <td><?php echo $value->model_name; ?></td>
                                                <td><?php echo $value->categ_name; ?></td>
                                                <td><span class="label  <?php if($value->status == "available"){ echo "badge progress-bar-success"; } else { echo "badge progress-bar-warning"; } ?> "><?php echo lang($value->status); ?></span></td>
                                                <?php if($value->status == "available"){     ?>
                                                   <td> <?= lang($value->available_place); ?> </td>
                                                   <?php
                                                    }   else  {?> <td><?= lang(" "); ?> </td> <?php
                                                    }
                                                     ?>
                                                <td><?php echo $this->sma->formatDecimal($value->buying_price); ?></td>
                                                <td><?php if($value->sold_date){ echo $this->sma->hrsd($value->sold_date);}else echo lang('No_Sold'); ?></td>
                                                <!--<td><a href="<?= admin_url('cars/vehicle_details/'.$value->vehicle_id) ?>" ><span class="label  <?php if($value->status == "produce") { echo "label-primary"; } else if($value->status == "done") { echo "label-success"; } else { echo "label-warning"; } ?> "><?php echo lang($value->status); ?></span></a></td> -->
                                               <td><span class="label  label-info"><a href="<?= admin_url('cars/vehicle_details/'.$value->vehicle_id) ?>" ><?php echo lang("Details"); ?></span></a><span class="label  label-warning"><a href="<?= admin_url('cars/edit_vehicle/'.$value->vehicle_id) ?>" ><?php echo lang("edit"); ?></span></a> 
                                                <span class="label  label-warning"><a href="<?= admin_url('cars/delete_vehicle/'.$value->vehicle_id) ?>" ><?php echo lang("Delete"); ?></span></a></span></td>
                                              <!-- <span class="label  label-warning"><a href="#" class="tip po"  data-content="<p> <?php lang('r_u_sure') ; ?> </p><a class='btn btn-danger po-delete' href='<?= admin_url('cars/delete_vehicle/'.$value->vehicle_id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>  rel='popover'><i class="fa fa-trash-o"></i></a></span></td>-->
                                               <!--  <td><a href="<?= admin_url('cars/view_sold/'.$value->vehicle_id) ?>" ><span class="label  <?php if($value->status == "available"){ echo "label-success"; } else { echo "label-warning"; } ?> "><?php echo lang($value->status); ?></span></td> -->
                                               <!-- <td><?php if ($value->status == "available") { echo '<span class="badge progress-bar-success" style=""><?php echo lang("edit"); ?></span>';} else if ( $value->status == 'sold' ) { echo '<span class="badge progress-bar-danger">Sold</span>';} else { echo '<span class="badge badge-primary">None</span>';} ?></td>-->
                                                
                                            </tr>
                                            <?php $i++; endforeach; ?>
                                    </table>
                                
				</div>
            
                            


                                                    
            
        </div>
    </div>
</div>
</div>



<script>
$(document).ready(function () {


    $('body').on('click', '.cars_link td:not(:first-child, :last-child)', function() {
        $('#myModal').modal({
            remote:
                site.base_url +
                'cars/modal_view/' +
                $(this)
                    .parent('.cars_link')
                    .attr('id'),
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'products/view/' + $(this).parent('.product_link').attr('id');
    });
    

	        var rownumber = 0;
		var tableajax = $('#vehiclesTable').DataTable({
          responsive: true,
		  "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            
            
			columnDefs: [{
    "defaultContent": "-",
    "targets": "_all"
  }],
           
		   
		   
		   
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				//alert(aData[0])
				//$("td:eq(4)", nRow).text(get_account(aData[4]));
				//var ent=aData[4];
				
				
				
                //$("td:eq(4)", nRow).text(ent);
                //nRow.id = aData[0];
               // alert(aData[0]);
                nRow.className = "cars_link";
                return nRow;
            },
      });

            
$('#submit_report').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('products/index') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#labelProducts').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
            
        });
$('#sync_quantity').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : '').'') ?>');
            
        });
	});
			
</script>
    

