<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>$(document).ready(function () {
        CURI = '<?= admin_url('sync/jisr_sync'); ?>';
    });</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-database"></i><?php echo lang('Synchronize_with_Jisr'); ?></h2>
        <div class="box-icon">
            <div class="hidden-xs">
                <div class="controls">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" value="<?= $date; ?>" id="log-date" class="form-control" style="z-index:unset;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

            <div class="well">
                <p><h2><?php echo lang('auth_token'); ?></h2></p>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="auth_token"><?php echo lang('access_token'); ?></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>


                </div>

            <div class="well">
                        <p><h2><?php echo lang('Attendance'); ?></h2></p>

                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="import_presence"><?php echo lang('Presence'); ?>
                                    </a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="actions">
                                <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="import_departure"><?php echo lang('Departure'); ?>
                                    </a></div>
                                <div class="performing" style="padding-top:7px;display:none;"><i
                                        class="fa fa-refresh fa-spin"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>




                <div class="well">
                <p><h2><?php echo lang('Accounts'); ?></h2></p>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-3"><a href="#" class="btn btn-primary btn-block" id="update_payroll"><?php echo lang('Payroll'); ?></a></div>
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


        $('#auth_token').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/auth_token')?>',
                type: 'GET',
                success: function (data) {
                    if(data.msg){
                        //console.log(data.msg)
                            perf.html(data.msg);




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



        $('#import_presence').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_presence/'.$date)?>',
                type: 'GET',
                success: function (data) {
                    if(data.msg){
                        //console.log(data.msg)
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_presence').trigger('click');
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


        $('#import_departure').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/import_departure/'.$date)?>',
                type: 'GET',
                success: function (data) {
                    if(data.msg){
                        //console.log(data.msg)
                        if(data.total!=0){
                            perf.html(data.msg+' '+Math.ceil(data.to * 100 / data.total)+' %');

                        }else{
                            perf.html(data.msg);

                        }


                     if(data.to!=data.total){
                        $('#import_departure').trigger('click');
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