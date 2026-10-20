<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<aside class="spark-sidebar" id="sparkSidebar">
  <div class="spark-sidebar-brand">
    <?php if ($Settings->logo2) { ?>
      <img src="<?= base_url('assets/uploads/logos/' . $Settings->logo2); ?>" alt="<?= $site_name ?>" class="spark-sidebar-logo" />
    <?php } ?>
    <span class="spark-sidebar-brand-text"><?= $site_name ?></span>
  </div>
  <nav class="spark-sidebar-nav">
    <ul class="spark-sidebar-menu">
				    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'welcome') ? 'is-active is-expanded' : '' ?>" id="mm_welcome">
                    <ul class=" spark-sidebar-submenu_ar">
          <li class="spark-sidebar-submenu-item"><a class="spark-sidebar-submenu-link" href="<?= admin_url() ?>"><?= lang('dashboard'); ?></a></li>
                  </li>
                  <?php
                    if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                    {
                    ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'cars') ? 'is-active is-expanded' : '' ?>" id="mm_cars">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('cars/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('cars/add_vehicle'); ?>"><?= lang('Add_Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars'); ?>"><?= lang('Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars/showrooms'); ?>"><?= lang('Showroom'); ?></a></li>
                      <li><a href="<?= admin_url('cars/services'); ?>"><?= lang('Services'); ?></a></li>
                      <li><a href="<?= admin_url('cars/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>


        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('cars/brands') ?>"><?= lang('brands'); ?></a></li>
                            <li><a href="<?= admin_url('cars/models'); ?>"><?= lang('Car_Type'); ?></a></li>
                            <li><a href="<?= admin_url('cars/categories'); ?>"><?= lang('categories'); ?></a></li>
                            <li><a href="<?= admin_url('cars/features'); ?>"><?= lang('Features'); ?></a></li>

                      </li>


					</ul>
                  </li>
                  <?php
                }else{
                ?>
      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'products') ? 'is-active is-expanded' : '' ?>" id="mm_products">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('products/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('products/warehouses') ?>"><?= lang('warehouses'); ?></a></li>
                      <li><a href="<?= admin_url('products'); ?>"><?= lang('list_products'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjustments'); ?>"><?= lang('inventory_opening_balance'); ?></a></li>
                      <li><a href="<?= admin_url('products/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>
                      <li><a href="<?= admin_url('products/destruction_inventory') ?>"><?= lang('Inventory_operations'); ?></a></li>
                      <li><a href="<?= admin_url('products/manual_inventory'); ?>"><?= lang('manual_inventory'); ?></a></li>
                      <li><a href="<?= admin_url('products/stock_counts'); ?>"><?= lang('stock_counts'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjust'); ?>"><?= lang('Quantity_adjustments'); ?></a></li>
                      <li><a href="<?= admin_url('products/revaluation'); ?>"><?= lang('Re-evaluation_of_cost_prices'); ?></a></li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('products/add'); ?>"><?= lang('add_product'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_csv'); ?>"><?= lang('import_csv'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_excel'); ?>"><?= lang('import_excel'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/print_barcodes'); ?>"><?= lang('print_barcode_label'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/units'); ?>"><?= lang('units'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/categories'); ?>"><?= lang('categories'); ?></a>
                            </li>

							<?php if ($Owner) {
                                ?>
							<li><a href="<?= admin_url('products/brands'); ?>"  ><?= lang('brands'); ?></a>
                            </li>
							<?php
							//$dbname=str_replace("cpesoft_","",$this->db->database);
								//if($dbname=="test" || $dbname=="spark")
								{
									?>
							<li><a href="<?= admin_url('products/variants'); ?>"  ><?= lang('variants'); ?></a>
                            </li>

                            <li><a href="<?= admin_url('products/modifiers'); ?>"  ><?= lang('Modifiers'); ?></a>
                            </li>
								<?php
                            } ?>

								<?php
                            } ?>


                        </li>


					   <li><a href="<?= admin_url('products/reports'); ?>"><?= lang('reports'); ?></a></li>





					</ul>
                  </li>
                  <?php
                }
                ?>

                  <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>



      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'carssales') ? 'is-active is-expanded' : '' ?>" id="mm_carssales">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('carssales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('carssales') ?>"><?= lang('list_sales'); ?></a></li>

                      <!--    <?php if (POS) {
                                ?>

					  <li class="sub_menu"><a href="<?= admin_url('pos/carssales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_sales_return spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('pos_sales'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                      </li>

                          <?php
                            } ?> -->
					  <li><a href="<?= admin_url('carssales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/quotes_index'); ?>"><?= lang('List_reserved_vehicle'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/offers_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                     <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_sales_promo spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('promos'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>

                      </li>
					  -->
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('carssales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<!--<li><a href="<?= admin_url('carssales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            -->

                      </li>
					  <li><a href="<?= admin_url('carssales/reports'); ?>"><?= lang('reports'); ?></a></li>




					</ul>
                  </li>

                <?php
                }else{
                ?>



      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'sales') ? 'is-active is-expanded' : '' ?>" id="mm_sales">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('sales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('sales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('sales') ?>"><?= lang('list_sales'); ?></a></li>

                          <?php if (POS) {
                                ?>

					  <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_return spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('pos_sales'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                      </li>-->

                          <?php
                            } ?>
                      <?php if ($this->Settings->isDeliveryFromSales==1) {
                                ?>
					  <li><a href="<?= admin_url('sales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        }else{ ?>
                      <li><a href="<?= admin_url('sales/delivers'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        } ?>
                      <li><a href="<?= admin_url('sales/coupons'); ?>"><?= lang('Coupons'); ?></a></li>
                      <li><a href="<?= admin_url('sales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('sales/quotes_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                      <li><a href="<?= admin_url('salesman'); ?>"><?= lang('list_salesman'); ?></a></li>
                     <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>-->
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_promo spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('promos'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>

                      </li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('sales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<li><a href="<?= admin_url('sales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            <li><a href="<?= admin_url('sales/terms') ?>"><?= lang('Terms_and_conditions'); ?></a></li>
                            <?php
                            if(isset($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions==1)
                            {
                            ?>
                                <li><a href="<?= admin_url('sales/subscriptions') ?>"><?= lang('Subscriptions'); ?></a></li>
                            <?php
                            }
                            ?>

                      </li>
					  <li><a href="<?= admin_url('sales/reports'); ?>"><?= lang('reports'); ?></a></li>




					</ul>
                  </li>

                  <?php
                }
                ?>



                <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>


      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'carspurchases') ? 'is-active is-expanded' : '' ?>" id="mm_carspurchases">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('carspurchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases') ?>"><?= lang('list_purchases'); ?></a></li>
					 <!-- <li><a href="<?= admin_url('carspurchases/returns') ?>"><?= lang('return_purchase'); ?></a></li> -->

                      <li><a href="<?= admin_url('carspurchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('carssuppliers'); ?>"><?= lang('list_cars_owners'); ?></a></li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_purchases_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                      </li>
					   <li><a href="<?= admin_url('carspurchases/reports') ?>"><?= lang('reports'); ?></a></li>






					</ul>
                  </li>



                 <?php
                }else{
                ?>





      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'purchases') ? 'is-active is-expanded' : '' ?>" id="mm_purchases">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('purchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('purchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('purchases') ?>"><?= lang('list_purchases'); ?></a></li>
					  <li><a href="<?= admin_url('purchases/returns') ?>"><?= lang('return_purchase'); ?></a></li>

                      <li><a href="<?= admin_url('purchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('suppliers'); ?>"><?= lang('list_suppliers'); ?></a></li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                      </li>
					   <li><a href="<?= admin_url('purchases/reports') ?>"><?= lang('reports'); ?></a></li>






					</ul>
                  </li>


				      <?php
						}
						?>

<?php
                }
                ?>

                    <?php
            if ($Owner || $Admin) {
			  if(isset($this->Settings->isHR) && $this->Settings->isHR==1)
              {
				  ?>
      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'hr') ? 'is-active is-expanded' : '' ?>" id="mm_hr">

                  </li>
			  <?php
			  }
            }
			  ?>

              <?php
				$dbname=str_replace("cpesoft_","",$this->db->database);
				if($dbname=="arkanpro" || $dbname=="spark")
					{
				?>
      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'crm') ? 'is-active is-expanded' : '' ?>" id="mm_crm">
                    </li>
			  <?php
			  }
			  ?>
                      <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'branches') ? 'is-active is-expanded' : '' ?>" id="mm_branches">
                    <ul class=" spark-sidebar-submenu_ar">
                    <?php if ($Owner) {

                    ?>

                            <li><a href="<?= admin_url('branches/dashboard') ?>"><?= lang('dashboard'); ?></a></li>

                          <?php
                          if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                            ?>

                            <li class="sub_menu"><a href="<?= admin_url('branches/groups_settings'); ?>"><?= lang('Groups_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/companies_settings'); ?>"><?= lang('Companies_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/branches_list'); ?>"><?= lang('Branches_list'); ?></a>
                            </li>
                            <?php
                            }
                            ?>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_settings spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('settings'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
								<ul class=" spark-sidebar-submenu_ar">
                                    <li class="sub_menu"><a href="<?= admin_url('branches/main_settings'); ?>"><?= lang('Data_sharing'); ?></a>
                                    </li>
                                    <li class="sub_menu"><a href="<?= admin_url('branches/branches_settings'); ?>"><?= lang('Branches_settings'); ?></a>
                                    </li>
								</ul>
							</li>




                    <?php
                    }
                            ?>




                    </li>
                    <?php
                    }
                            ?>



      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'auth') ? 'is-active is-expanded' : '' ?>" id="mm_auth">
                    <ul class=" spark-sidebar-submenu_ar">



        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_system_settings_system_settings spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Settings'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">


						          <?php if ($Owner) {

                                    ?>
									    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('system_settings'); ?>"><?= lang('system_settings'); ?></a>


                            </li>



							     <?php if (POS) {
                                    ?>
                                    <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
                }else{
				  ?>


					  <li><a href="<?= admin_url('pos/settings') ?>"><?= lang('pos_settings'); ?></a></li>
					  <li><a href="<?= admin_url('pos/printers') ?>"><?= lang('list_printers'); ?></a></li>
					<?php
                            }
                        }
                        ?>
                    <?php
                            } ?>


                      <li><a href="<?= admin_url('system_settings/change_logo') ?>"  data-toggle="modal" data-target="#myModal"><?= lang('change_logo'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tax_rates') ?>"><?= lang('tax_rates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/currencies') ?>"><?= lang('currencies'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/credit_cards') ?>"><?= lang('Credit_Cards'); ?></a></li>
					  <?php if ($Settings->IsResturant) {
                                    ?>

					  <li><a href="<?= admin_url('system_settings/departments') ?>"><?= lang('Departments'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tabels') ?>"><?= lang('Tabels'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/stuff') ?>"><?= lang('Stuff'); ?></a></li>
					  <?php
                            } ?>

					  <li><a href="<?= admin_url('system_settings/email_templates') ?>"><?= lang('email_templates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/user_groups') ?>"><?= lang('group_permissions'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/backups') ?>"><?= lang('backups'); ?></a></li>

							<?php
                            } ?>


                      </li>


        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_system_settings_sms_settings spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('SMS'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sms_settings'); ?>"><?= lang('sms_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_sms'); ?>"><?= lang('send_sms'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/sms_log'); ?>"><?= lang('sms_log'); ?></a>
                            </li>
                      </li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_system_settings_whatsapp_settings spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Whatsapp'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_settings'); ?>"><?= lang('Whatsapp_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_whatsapp'); ?>"><?= lang('send_whatsapp'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_log'); ?>"><?= lang('Whatsapp_log'); ?></a>
                            </li>
                        	<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_templates'); ?>"><?= lang('Whatsapp_templates'); ?></a>
                            </li>
                      </li>
                    <?php
							$dbname=str_replace("cpesoft_","",$this->db->database);
								if($dbname=="arkanpro" || $dbname=="test2" || $dbname=="spark")
								{
									?>
                      <li><a><i class="mm_system_settings_lectronic_signature"></i><?= lang('Electronic_signature'); ?><span class="fa fa-chevron-down"></span><span class="pc-micon" style="background-color:rgb(4 169 245);border-radius:12px;float:left;padding:3px;font-size:10px;"><?= lang('new'); ?></span></a>
                          <ul class=" spark-sidebar-submenu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sign_document'); ?>"><?= lang('Sign_a_document'); ?></a>
                            </li>
						  </ul>
                      </li>
                      <?php
							}
					    ?>









        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_system_settings_people spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('users'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">

						          <?php if ($Owner) {
                                    ?>

                            <li class="sub_menu"><a href="<?= admin_url('users'); ?>"><?= lang('list_users'); ?></a>
                            </li>
							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

						    <li><a href="<?= admin_url('billers'); ?>"><?= lang('list_billers'); ?></a>
                            </li>


							<?php
                            }
							?>
                    <?php
                            }
							?>


                      </li>

        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_system_settings_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">

						          <?php if ($Owner) {
                                    ?>

							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

							<li><a href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal"><?= lang('add_biller'); ?></a>
                            </li>


							      <?php if (POS) {
                                    ?>

                            <li><a href="<?= admin_url('pos/add_printer'); ?>"><?= lang('add_printer'); ?></a>
                            </li>
							<?php
                            } ?>

							<?php
                            }
                            } ?>


                      </li>






					</ul>
                  </li>

                      <?php
			  if($this->Settings->accountingOnly==0)
              {
                if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                    ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'carsreports') ? 'is-active is-expanded' : '' ?>" id="mm_carsreports">
                <ul class=" spark-sidebar-submenu_ar">
                  <li><a href="<?= admin_url('carsreports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                  <li class="sub_menu"><a href="<?= admin_url('carsreports/tax') ?>"><?= lang('tax_report'); ?></a></li>
                                    <?php
                            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
                            {
                            }else{
                                ?>

                                    <li><a href="<?= admin_url('carsreports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
                                <?php
                            }
                            ?>

                                    <?php if(!DEMO){
                            ?>
                            <li><a href="<?= admin_url('carsreports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                                </li>

                        <?php
                        }
                        ?>





                 </li>

        <?php
            }else{
                        ?>
      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'reports') ? 'is-active is-expanded' : '' ?>" id="mm_reports">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('reports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('reports/tax') ?>"><?= lang('tax_report'); ?></a></li>
					  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
			  }else{
				  ?>

                      <li><a href="<?= admin_url('reports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
				<?php
			  }
			  ?>

					  <?php if(!DEMO){
						  ?>
                          <li><a href="<?= admin_url('reports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                            </li>

					  <?php
					  }
					  ?>





					</ul>
                  </li>
                  <?php
            }
			  }
			  ?>
				  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'accounts') ? 'is-active is-expanded' : '' ?>" id="mm_accounts">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('accounts/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('accounts/accounts_tree') ?>"><?= lang('Accounts_tree'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/opening_balance'); ?>"><?= lang('Opening_balance'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/entries'); ?>"><?= lang('Entries'); ?></a></li>
					  <?php
						//if($this->session->userdata('company') =='spark' || $this->session->userdata('company') =='test' || $this->session->userdata('company') =='eqrar')
						{
						?>
                      <li><a href="<?= admin_url('accounts/accounting_period'); ?>"><?= lang('Accounting_Period'); ?></a></li>




						<?php
						}
						?>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_bond spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Bond'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar">

							<li><a href="<?= admin_url('accounts/receipts'); ?>"><?= lang('Receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/catch_receipts'); ?>"><?= lang('Catch_receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/refunding_bonds'); ?>"><?= lang('Refunding_bonds'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/notice_creditor'); ?>"><?= lang('Notice_creditor'); ?></a></li>

                      </li>
					  <li><a href="<?= admin_url('accounts/payment_types'); ?>"><?= lang('Payment_types'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/cost_centers'); ?>"><?= lang('Cost_centers'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/settings'); ?>"><?= lang('Account_system_settings'); ?></a></li>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_report spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('reports'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
                          <ul class=" spark-sidebar-submenu_ar" style="padding-bottom:20px;">

							<li><a href="<?= admin_url('accounts/accountsguide'); ?>"><?= lang('Accounts_guide'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ledgers'); ?>"><?= lang('Ledgers'); ?></a></li>
							<li><a href="<?= admin_url('accounts/trialbalance'); ?>"><?= lang('Trial_balance'); ?></a></li>
							<li><a href="<?= admin_url('accounts/entriesreport'); ?>"><?= lang('Entries_Report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/incomestatement'); ?>"><?= lang('Income_Statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ownersequity'); ?>"><?= lang('Statement_of_owners_equity'); ?></a></li>
							<li><a href="<?= admin_url('accounts/balancesheet'); ?>"><?= lang('Balance_Sheet'); ?></a></li>
							<li><a href="<?= admin_url('accounts/accountstatement'); ?>"><?= lang('Account_statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/tax') ?>"><?= lang('tax_report'); ?></a></li>
					        <li><a href="<?= admin_url('accounts/agedopeninvoices'); ?>"><?= lang('Aged_Open_Invoices'); ?></a></li>
							<li><a href="<?= admin_url('accounts/agedopeninvoicessupplier'); ?>"><?= lang('Supplier_debt_aging_report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/debtliability'); ?>"><?= lang('Debt_and_Liability'); ?></a></li>
							<li><a href="<?= admin_url('accounts/distributioncostcenters'); ?>"><?= lang('Cost_centers_Report'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/collection_report'); ?>"><?= lang('Collection_report'); ?></a></li>




                      </li>





					</ul>
                  </li>

				  <?php
					  }
					  ?>











                <?php
			  if(isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset==1)
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'fixedassets') ? 'is-active is-expanded' : '' ?>" id="mm_fixedassets">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('fixedassets/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  	<li><a href="<?= admin_url('fixedassets'); ?>"><?= lang('Fixed_assets_list'); ?></a></li>


        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Definitions'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
								<ul class=" spark-sidebar-submenu_ar">
									<li><a href="<?= admin_url('fixedassets/group'); ?>"><?= lang('Fixed_assets_group'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/parent_classes') ?>"><?= lang('Parent_classes'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/sub_classes') ?>"><?= lang('Sub_classes'); ?></a></li>
									<!--<li><a href="<?= admin_url('fixedassets/add_fixed_asset') ?>"><?= lang('Definition_of_fixed_asset'); ?></a></li>-->
								</ul>
							</li>
                            <li><a href="<?= admin_url('fixedassets/accounts_setting'); ?>"><?= lang('Accounts_settings'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/revaluation'); ?>"><?= lang('Revaluation_of_fixed_asset'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/increasing'); ?>"><?= lang('Increasing_the_fixed_asset'); ?></a></li>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="_reports spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('reports'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
								<ul class=" spark-sidebar-submenu_ar">
                                <li><a href="<?= admin_url('fixedassets/valuation_report'); ?>"><?= lang('Valuation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/depreciation_report'); ?>"><?= lang('Depreciation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/increasing_report'); ?>"><?= lang('Increasing_report'); ?></a></li>
							</li>










					</ul>
                  </li>

				  <?php
					  }
					  ?>


				  <?php
			  if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'manufacturing') ? 'is-active is-expanded' : '' ?>" id="mm_manufacturing">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('manufacturing/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('manufacturing/bom') ?>"><?= lang('Bill_Of_Material'); ?></a></li>
					  	<li><a href="<?= admin_url('manufacturing'); ?>"><?= lang('Manufacturing_list'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/confirmed'); ?>"><?= lang('Confirmed_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/progress'); ?>"><?= lang('In_progress_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/done'); ?>"><?= lang('Done_products'); ?></a></li>
        <a class="spark-sidebar-link" href="javascript:void(0);">
          <i class="mm_products_definitions spark-sidebar-icon"></i>
          <span class="spark-sidebar-text"><?= lang('Archive'); ?></span>
          <span class="spark-sidebar-chevron fa fa-chevron-down"></span>
        </a>
								<ul class=" spark-sidebar-submenu_ar">
									<li><a href="<?= admin_url('manufacturing/transfers'); ?>"><?= lang('transfers'); ?></a></li>
									<li><a href="<?= admin_url('manufacturing/destructions') ?>"><?= lang('destruction_inventory'); ?></a></li>
								</ul>
							</li>








					</ul>
                  </li>

				  <?php
					  }
					  ?>


                   <?php
			  if(isset($this->Settings->isStore) && $this->Settings->isStore==1 && ($Owner || $Admin))
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'shop_settings') ? 'is-active is-expanded' : '' ?>" id="mm_shop_settings">
                    <!--<ul class=" spark-sidebar-submenu_ar">
                        <li><a href="<?= admin_url('shop_settings/shop_sync') ?>"><?= lang('Synchronize_Data'); ?></a></li>


					</ul>-->
                  </li>

				  <?php
					  }else{
                        ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'shop_settings') ? 'is-active is-expanded' : '' ?>" id="mm_shop_settings">

                  </li>

				  <?php
                      }
					  ?>


<?php
			  if(isset($this->Settings->isTailer) && $this->Settings->isTailer==1)
              {
				  ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'tailor') ? 'is-active is-expanded' : '' ?>" id="mm_tailor">
                    <ul class=" spark-sidebar-submenu_ar">
                      <li><a href="<?= admin_url('tailor') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/tailor') ?>"><?= lang('Tailors'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/collar') ?>"><?= lang('Collar'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/cuff') ?>"><?= lang('Cuff'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/sleeve') ?>"><?= lang('Sleeve'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/filling') ?>"><?= lang('Filling'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/model') ?>"><?= lang('Model'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/embroidery') ?>"><?= lang('Embroidery'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/pocket') ?>"><?= lang('Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket') ?>"><?= lang('Chest_Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket_padding') ?>"><?= lang('Chest_Pocket_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper') ?>"><?= lang('Zipper'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper_padding') ?>"><?= lang('Zipper_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/buttons') ?>"><?= lang('Buttons'); ?></a></li>








					</ul>
                  </li>

				  <?php
					  }
					  ?>




            <?php
                if(isset($this->Settings->isElectronicLinking) && $this->Settings->isElectronicLinking==1)
                {
                    ?>

      <li class="spark-sidebar-item spark-sidebar-item-expandable <?= ($m == 'sync') ? 'is-active is-expanded' : '' ?>" id="mm_sync">
                    <ul class=" spark-sidebar-submenu_ar">
                    <?php if ($Owner) {


				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/dashboard'); ?>"><?= lang('dashboard'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('sync/electronic_linking'); ?>"><?= lang('Supported_linking_services'); ?></a>
                            </li>

                            <?php
                            if(isset($this->Settings->isFoodics) && $this->Settings->isFoodics==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/foodics_sync'); ?>"><?= lang('Synchronize_with_Foodics'); ?></a>
                            </li>





                      <?php

                            }
                            ?>

                        <?php
                            if(isset($this->Settings->isJisr) && $this->Settings->isJisr==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/jisr_sync'); ?>"><?= lang('Synchronize_with_Jisr'); ?></a>
                            </li>





                      <?php

                            }
                            ?>

                            <?php
                        }
							?>
                      </li>

                      <?php
                    }
                            ?>






























    </ul>
  </nav>
  <div class="spark-sidebar-footer">
    <div class="spark-sidebar-user">
      <img src="<?= $this->session->userdata('avatar') ? base_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>" alt="Avatar" class="spark-sidebar-avatar" />
      <span class="spark-sidebar-username"><?= $this->session->userdata('username') ?></span>
    </div>
  </div>
</aside>
