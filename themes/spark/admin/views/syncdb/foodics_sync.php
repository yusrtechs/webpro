<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-database"></i><?php echo lang('Synchronize_with_Foodics'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

            <div class="well">
                        <p><h2><?php echo lang('Synchronize_General_Data'); ?></h2></p>

                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="import_settings"><?php echo lang('settings'); ?>
                                    </a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="import_branches"><?php echo lang('Branches')." / ".lang('warehouses'); ?>
                                    </a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="import_Payment_types"><?php echo lang('Payment_types'); ?>
                                        </a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block"
                                                         id="import_customers"><?php echo lang('customers'); ?></a></div>
                                <div class="performing" style="padding-top:7px;display:none;">


                                <i
                                        class="fa fa-refresh fa-spin"></i>

                                    </div>


                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block"
                                                         id="import_suppliers"><?php echo lang('suppliers'); ?></a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>



                <div class="well">
                <p><h2><?php echo lang('Synchronize_Baisc_Data'); ?></h2></p>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_sales"><?php echo lang('sales'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_deposits"><?php echo lang('Customer_deposits'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_purchases"><?php echo lang('purchases'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_transfers"><?php echo lang('transfers'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_destruction_inventory"><?php echo lang('destruction_inventory').' & '.lang('Quantity_adjustment'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_inventory_counts"><?php echo lang('count_stock'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('document').ready(function () {




        $('#delete_extra_tables').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Please make sure you have perfomr all above actions before deleting tables. Procced?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/delete_extra_tables')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#user_groups').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/user_groups')?>',
                type: 'GET',
                success: function (msg) {
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#import_billers').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_billers')?>',
                type: 'GET',
                success: function (msg) {
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#import_customers').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_customers')?>',
                type: 'GET',
                success: function (data) {
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_customers').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#import_suppliers').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_suppliers')?>',
                type: 'GET',
                success: function (data) {
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_suppliers').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#reset_sales').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the sales and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_sales')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_quotes').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the quotations and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_quotes')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_purchases').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the purchases and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_purchases')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_transfers').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the transfers and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_transfers')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_deliveries').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the deliveries and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_deliveries')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_products').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the products and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_products')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#reset_damage_products').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            bootbox.confirm("Are you sure to delete all the damage products and reset the table?", function (res) {
                if (res == true) {
                    var perf = btn.parent().parent('.actions').children('.performing');
                    perf.show();
                    $.ajax({
                        url: '<?=admin_url('sync/reset_damage_products')?>',
                        type: 'GET',
                        success: function (msg) {
                            perf.html(msg);
                            btn.attr('disabled', false);
                        },
                        error: function () {
                            perf.html('Request Failed!').show();
                            btn.attr('disabled', false);
                        }
                    });
                }
            });
        });

        $('#update_sales').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_sales')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_sales').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#update_quotes').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_quotes')?>',
                type: 'GET',
                success: function (msg) {
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#update_purchases').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_purchases')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_purchases').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });



        $('#update_deposits').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_deposits')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(data.msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_deposits').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#update_transfers').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_transfers')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_transfers').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#update_inventory_counts').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_inventory_counts')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_inventory_counts').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });


        $('#update_destruction_inventory').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/update_destruction_inventory')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#update_destruction_inventory').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

        $('#import_branches').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_branches')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_branches').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });



        $('#import_settings').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_settings')?>',
                type: 'GET',
                success: function (msg) {
                    //console.log(msg);
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });


        $('#import_Payment_types').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_Payment_types')?>',
                type: 'GET',
                success: function (data) {
                    //console.log(msg);
                    if(data.msg){
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_Payment_types').trigger('click');
                     }else{
                        btn.attr('disabled', false);
                     }

                    //btn.attr('disabled', false);
                }else{
                    perf.html(data);
                    btn.attr('disabled', false);
                }
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });

    });



</script>