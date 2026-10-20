<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.qrimg{
	padding:20px;
}
.performing pre{
	text-align:left;
}

.invoice-download-actions .btn{
    align-items:center;
    margin:6px;
    padding:6px 14px;
    font-weight:600;
}
</style>

<div class="modal-dialog modal-lg no-modal-header " >
    <div class="modal-content" dir="ltr">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Synchronize_with_ZATCA'); ?></h4>
        </div>

		<input type="hidden" name="stamp_hash" id="stamp_hash" value="<?= $invDoc->stamp_hash; ?>">
		<input type="hidden" name="uuid" id="uuid" value="<?= $inv->uuid; ?>">
		<input type="hidden" name="synch_hash" id="synch_hash" value="<?= $invDoc->synch_hash; ?>">

                <div class="col-xs-12">
                    <p><b><?= lang('Invoice_No'); ?>: <?= $inv->id; ?></b></p>
                </div>
                    <?php
                    if ($org_return_sale==1) {
                    ?>
                    <div class="col-xs-12">
                        <p style="direction: rtl;color:red;" ><b><?= lang('The_original_invoice_must_be_synchronized_first'); ?></b></p>
                    </div>

                    <div class="form-group">
                        <div class="actions">

                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <?php
                    }else if ($inv->synch==1) {
                    ?>
                    <div class="">
    <p style="direction: rtl; padding-left:15px;padding-right:15px;">
        <b><?= lang('Status'); ?> : <?= $inv->synch_status; ?></b>
    </p>
</div>

<?php
if ($inv->synch_status=="CLEARED") {

    $xml_download = anchor(
        'admin/welcome/downloadXMLUBL/' . $inv->xml_file_path.'clearedInvoice.xml',
        '<i class="fa fa-download"></i> XML Cleared Invoice',
        'class="btn btn-primary btn-sm"'
    );

    $xml_download_pdf = anchor(
        'admin/sales/pdf/' . $inv->id,
        '<i class="fa fa-file-pdf-o"></i> PDF/A-3',
        'class="btn btn-primary btn-sm download-pdf" data-url="'.site_url('admin/sales/pdf/'.$inv->id).'"'
    );
?>


<?php } ?>
<div class="form-group">
    <div class="actions invoice-download-actions">
        <?= $xml_download ?>
        <?= $xml_download_pdf ?>
    </div>
</div>

<?php
                    } else {
                    ?>

                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-5"><a href="#" class="btn btn-primary btn-block" id="generate_xml"><strong>(Step 1) Generate
                                    XML UBL 2.1</strong></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <div class="actions">
                            <div class="col-xs-5"><a href="#" class="btn btn-primary btn-block" id="digital_signature"><strong>(Step 2) Digital
                                    Signature and Stamp</strong></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <?php
                    if($customer->vat_no!=""){// || abs($inv->grand_total)>=1000){
                        ?>

					<div class="form-group">
                        <div class="actions">
                            <div class="col-xs-5"><a href="#" class="btn btn-primary btn-block" id="integration_zatca_clearance"><strong>(Step 3) Clearance API Integration
                                    with ZATCA</strong></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    }else{
                        ?>

					<div class="form-group">
                        <div class="actions">
                            <div class="col-xs-5"><a href="#" class="btn btn-primary btn-block" id="integration_zatca_reporting"><strong>(Step 3) Reporting API Integration
                                    with ZATCA</strong></a></div>
                            <div class="performing" style="padding-top:7px;display:none;"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    }
                        ?>

                    <?php
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
  </div><!-- end modal-dialog div -->


<iframe id="download_iframe" style="display:none;"></iframe>

<script type="text/javascript">
    $(document).on('click', '.download-pdf', function(e){

    e.preventDefault();

    var btn = $(this);
    var url = btn.data('url');
    var original = btn.html();

    btn.prop('disabled', true);
    btn.html('<i class="fa fa-spinner fa-spin"></i> <?php echo lang('Loading'); ?>');

    $('#download_iframe').attr('src', url);

    var checkCookie = setInterval(function(){

        if(document.cookie.indexOf('fileDownload=true') !== -1){

            clearInterval(checkCookie);

            btn.prop('disabled', false);
            btn.html(original);

            document.cookie = "fileDownload=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        }

    }, 500);

});
    $('document').ready(function () {

		var inv_id='<?= $inv->id ?>';
		var username='<?= $zatcaConfig->binarySecurityToken ?>';
		var password='<?= $zatcaConfig->secret ?>';
        var api_type='<?= $zatcaConfig->api_type ?>';


//console.log(btoa(username + ":" + password))

        $('#generate_xml').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/generate_xml/')?>'+inv_id,
                type: 'GET',
                success: function (data) {
                    perf.html(data.msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });



		$('#digital_signature').click(function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();
            $.ajax({
                url: '<?=admin_url('sync/digital_signature/')?>'+inv_id,
                type: 'GET',
                success: function (data) {
					//console.log(data);
                    perf.html(data.msg);
					$('#stamp_hash').val(data.stamp_hash);
					$('#uuid').val(data.uuid);
					$('#synch_hash').val(data.synch_hash);

                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
        });



		$('#integration_zatca_clearance').click(function (e) {
			var res='Not Action';

			var stamp_hash=$('#stamp_hash').val();
			var uuid=$('#uuid').val();
			var synch_hash=$('#synch_hash').val();

			//var url = "https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/compliance/invoices";
            var url = "https://gw-fatoora.zatca.gov.sa/e-invoicing/"+api_type+"/invoices/clearance/single";




            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();

/*var xhr = new XMLHttpRequest();
xhr.open("POST", url);


xhr.setRequestHeader("accept", "application/json");
xhr.setRequestHeader("Content-Type", "application/json");
xhr.setRequestHeader("Accept-Version", "V2");
xhr.setRequestHeader("Accept-Language", "en");
xhr.setRequestHeader ("Authorization", "Basic " + btoa(username + ":" + password));


xhr.onreadystatechange = function () {
   if (xhr.readyState === 4) {
      //console.log(xhr.status);
      //console.log(xhr);

     //otp->otp_token //access_token=

						if(xhr.status=="200"){


							try{
	  //res=JSON.parse(xhr.responseText);
	  //res=(xhr.responseText);

	  //console.log(res);
					//	$('#mokafaa_OTPToken').val(otp_token);

						//$("#div_mokafaa_otp").css("display", "block");




	}catch(err){
		//$('#mError').text('Failed');
					//$('#mError-con').show();

	  }


						}




	  $.ajax({
                url: '<?=admin_url('sync/integration_zatca_clearance_temp/')?>'+inv_id,
                type: 'post',
				data: {res: JSON.parse(xhr.responseText)},

                success: function (msg) {
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
		//perf.html(xhr);
		//btn.attr('disabled', false);


   }
   };

//var data = "grant_type=client_credentials&scope=customer-authorization otp-validation redemption-transaction-reversal redemption-transactions&client_id=483020386674213ca6539348c95a02f0&client_secret=cd3a599408d5b4b9a96af37660289081";
var data = '{"invoiceHash":"'+stamp_hash+'","uuid":"'+uuid+'","invoice":"'+synch_hash+'"}';

xhr.send(data);
*/
            $.ajax({
                url: '<?=admin_url('sync/integration_zatca_clearance/')?>'+inv_id,
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

        $('#integration_zatca_reporting').click(function (e) {
            var res='Not Action';

			var stamp_hash=$('#stamp_hash').val();
			var uuid=$('#uuid').val();
			var synch_hash=$('#synch_hash').val();

			var url = "https://gw-fatoora.zatca.gov.sa/e-invoicing/"+api_type+"/invoices/reporting/single";




            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();

            $.ajax({
                url: '<?=admin_url('sync/integration_zatca_clearance/')?>'+inv_id,
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

/*var xhr = new XMLHttpRequest();
xhr.open("POST", url);


xhr.setRequestHeader("accept", "application/json");
xhr.setRequestHeader("Content-Type", "application/json");
xhr.setRequestHeader("Accept-Version", "V2");
xhr.setRequestHeader("Accept-Language", "en");
xhr.setRequestHeader("Clearance-Status", "0");
xhr.setRequestHeader ("Authorization", "Basic " + btoa(username + ":" + password));


xhr.onreadystatechange = function () {
   if (xhr.readyState === 4) {
      //console.log(xhr.status);
      //console.log(xhr);

     //otp->otp_token //access_token=






	  $.ajax({
                url: '<?=admin_url('sync/integration_zatca_reporting_temp/')?>'+inv_id,
                type: 'post',
				data: {res: JSON.parse(xhr.responseText)},

                success: function (msg) {
                    perf.html(msg);
                    btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });
		//perf.html(xhr);
		//btn.attr('disabled', false);


   }
   };

var data = '{"invoiceHash":"'+stamp_hash+'","uuid":"'+uuid+'","invoice":"'+synch_hash+'"}';

xhr.send(data);
*/



        });



    });

</script>