<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .ob {
        list-style: none;
        padding: 0;
        margin: 0;
        margin-top: 10px;
    }

    .ob li {
        width: 49%;
        margin: 0 10px 10px 0;
        float: left;
    }

    .ob li .btn {
        width: 100%;
    }

    .ob li:nth-child(2n+2) {
        margin-right: 0;
    }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header modal-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="ajaxModalLabel">
                <?= lang('return_sales') ?>
            </h4>
        </div>
		<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos-return-form'];
                    echo admin_form_open('pos/return_check', $attrib);?>
                    
        <div class="modal-body">
                <!--<p><?=lang('type_reference_note');?></p>-->

                
                <div class="form-group">
                    <?=lang('reference_no', 'reference_no');?>
                    <?= form_input('reference_no', (!empty($reference_no) ? $reference_no : ''), 'class="form-control kb-text" id="reference_no"'); ?>
                </div>
				

            </div>
            <div class="modal-footer">
                <button type="button" id="return_sale" class="btn btn-primary"><?=lang('submit')?></button>
            </div>
			<?php echo form_close(); ?>
                    
        
    </div>
</div>


<script>
    $(document).ready(function () {
        $('.pagination a').attr('data-toggle', 'ajax');
        // $('.pagination').on('click', 'a', function (e) {
        //     e.preventDefault();
        //     var pg = $(this).attr("href");
        //     $.get(pg, function (data) {
        //         $('#myModal').find('.modal-dialog').html(data);
        //     });
        //     return false;
        // });
	
        $('#return_sale').on('click', function (e) {
            var rid = $('#reference_no').val();
			
			if (protect_delete == 1) {
            var boxd = bootbox.dialog({
                title: "<i class='fa fa-key'></i> Pin Code",
                message: '<input id="pos_pin" name="pos_pin" type="password" placeholder="Pin Code" class="form-control"> ',
                buttons: {
                    success: {
                        label: "<i class='fa fa-tick'></i> OK",
                        className: 'btn-success verify_pin',
                        callback: function() {
                            var pos_pin = md5($('#pos_pin').val());
                            if (pos_pin == pos_settings.pin_code) {
                               //window.location.href = "<?= admin_url('pos/return_check') ?>/" + rid; 
							   $('#pos-return-form').submit();

							   
                            } else {
                                bootbox.alert('Wrong Pin Code');
                            }
                        },
                    },
                },
            });
            boxd.on('shown.bs.modal', function() {
                $('#pos_pin')
                    .focus()
                    .keypress(function(e) {
                        if (e.keyCode == 13) {
                            e.preventDefault();
                            $('.verify_pin').trigger('click');
                            return false;
                        }
                    });
            });
        }else
			$('#pos-return-form').submit();

            
			
			
			
			
			
			
			
			
			
			
			
        });
    });
</script>
