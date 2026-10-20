$(document).ready(function () {



	if(po_edit){
		if (localStorage.getItem('poitems')) {
			poitems = JSON.parse(localStorage.getItem('poitems'));
		 sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function(o){return [parseInt(o.order)];}) : poitems;
		 //console.log(poitems);

        $.each(sortedItems, function () {
            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
           //alert(poitems[item_id].row.received);
	    poitems[item_id].row.received = 0;
        //localStorage.setItem('poitems', JSON.stringify(poitems));
		});
        loadItems();
		}
	}




	try{
$('#tax_method_type').change(function (e) {

	localStorage.setItem('ptax_method_type', $(this).val());

            if($('#tax_method_type').val()==1){
				$('#potax2').val(1).trigger('change.select2');
				$('#potax2').select2('readonly', false);
				$("#div_invoice_tax").css("display", "block");
				$("#div_product_tax").css("display", "none");
				$('#ptax').val(1).trigger('change.select2');
				$('#ptax').select2('readonly', true);
				$('#podiscount').val("").trigger('change');
				$("#div_invoice_discount").css("display", "block");
				//$('#pdiscount').val("0").trigger('change');
				$("#div_product_discount").css("display", "none");
				$("#div_th_discount").css("display", "none");
				$("#div_th_tax").css("display", "none");


			}else{
				$('#potax2').val(1).trigger('change.select2');
				$('#potax2').select2('readonly', true);
				$("#div_invoice_tax").css("display", "none");
				$("#div_product_tax").css("display", "block");
				//$('#ptax').val(1).trigger('change.select2');
				$('#ptax').select2('readonly', false);
				$('#podiscount').val("").trigger('change');
				$("#div_invoice_discount").css("display", "none");
				//$('#pdiscount').val("0").trigger('change');
				$("#div_product_discount").css("display", "block");
				$("#div_th_discount").css("display", "table-cell");
				$("#div_th_tax").css("display", "table-cell");



			}
        try{
			 if(Object.keys(JSON.parse(localStorage.getItem('poitems'))).length >0)
				 $('#tax_method_type').select2('readonly', true);
			}catch(err){}

    });

	}catch(err){alert(err);}


			try{

			if (ptax_method_type = localStorage.getItem('ptax_method_type') && (Object.keys(JSON.parse(localStorage.getItem('poitems'))).length >0)) {
				$('#tax_method_type').select2("val", localStorage.getItem('ptax_method_type'));
			}
			}catch(err){}



			if($('#tax_method_type').val()==1){
				$('#potax2').val(1).trigger('change.select2');
				$('#potax2').select2('readonly', false);
				$("#div_invoice_tax").css("display", "block");
				$("#div_product_tax").css("display", "none");
				$('#ptax').val(1).trigger('change.select2');
				$('#ptax').select2('readonly', true);
				$('#podiscount').val("").trigger('change');
				$("#div_invoice_discount").css("display", "block");
				//$('#pdiscount').val("0").trigger('change');
				$("#div_product_discount").css("display", "none");
				$("#div_th_discount").css("display", "none");
				$("#div_th_tax").css("display", "none");


			}else{
				$('#potax2').val(1).trigger('change.select2');
				$('#potax2').select2('readonly', true);
				$("#div_invoice_tax").css("display", "none");
				$("#div_product_tax").css("display", "block");
				//$('#ptax').val(1).trigger('change.select2');
				$('#ptax').select2('readonly', false);
				$('#podiscount').val("").trigger('change');
				$("#div_invoice_discount").css("display", "none");
				//$('#pdiscount').val("0").trigger('change');
				$("#div_product_discount").css("display", "block");
				$("#div_th_discount").css("display", "table-cell");
				$("#div_th_tax").css("display", "table-cell");



			}

			if (!localStorage.getItem('ptax_method_type')) {
                   localStorage.setItem('ptax_method_type', '1');
                }
             try{
			 if(Object.keys(JSON.parse(localStorage.getItem('poitems'))).length >0)
				 $('#tax_method_type').select2('readonly', true);
				 $('#pocurrency').select2('readonly', true);
			}catch(err){}




$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
if (podiscount = localStorage.getItem('podiscount')) {
    $('#podiscount').val(podiscount);
}

$('#potax2').change(function (e) {
    localStorage.setItem('potax2', $(this).val());
});
if (potax2 = localStorage.getItem('potax2')) {
    $('#potax2').select2("val", potax2);
}

$('#postatus').change(function (e) {




	/*if(po_edit){
		if($('#postatus').val()=='pending' || $('#postatus').val()=='ordered'){
		if(localStorage.getItem('postatus')=='received' || localStorage.getItem('postatus')=='partial'){
			//alert(localStorage.getItem('postatus'));
			//alert(flag);
			//if(flag!=1)
				if (postatus = localStorage.getItem('postatus'))
			$('#postatus').select2("val", postatus);

			 $('#postatus').val(localStorage.getItem('postatus')).trigger('change.select2');
		     //$('#postatus').val('received').trigger('change.select2');
		if (postatus = localStorage.getItem('postatus')) {
          $('#postatus').select2("val", postatus);
	         if(localStorage.getItem('postatus')=='pending' || $(this).val()=='ordered'){
		    	$('#podiscount').attr('readonly', false);
                $('#poshipping').attr('readonly', false);
				if(po_edit){
				$('#poshipping_received').attr('readonly', true);
				}

			}else{
				$('#podiscount').attr('readonly', true);
                $('#poshipping').attr('readonly', true);
            	if(po_edit){
				$('#poshipping_received').attr('readonly', true);
				}
			}

            }
		    //else
			 //loadItems();
			 //alert(localStorage.getItem('postatus'));
			 //localStorage.setItem('postatus', $(this).val());
			 //return;
		}else
		localStorage.setItem('postatus', $(this).val());
		}else
		localStorage.setItem('postatus', $(this).val());

	}else*/


    localStorage.setItem('postatus', $(this).val());

	if($(this).val()=='ordered'){
		$('#CostCenters').slideUp();

		$("#more_options").css("display", "none");

	}else if($(this).val()=='pending'){
		$('#CostCenters').slideUp();

		$("#more_options").css("display", "block");
		$("#receiving_date_div").css("display", "none");


		    	$('#podiscount').attr('readonly', false);
                $('#poshipping').attr('readonly', false);
				$('#customs_fees').attr('readonly', false);
				$('#Other_fees').attr('readonly', false);
				if(po_edit){
				$('#poshipping_received').attr('readonly', true);
				$("#sticker").css("display", "block");


				//$('#customs_fees').attr('readonly', true);
				//$('#Other_fees').attr('readonly', true);
				}

			}else if($(this).val()=='received' || $(this).val()=='received1' || $(this).val()=='partial'){

				if($(this).val()=='received' || $(this).val()=='partial'){
					$('#CostCenters').slideDown();
				}else{
					$('#CostCenters').slideUp();
				}


				$("#more_options").css("display", "block");
				$("#receiving_date_div").css("display", "block");


				$('#podiscount').attr('readonly', true);
                $('#poshipping').attr('readonly', true);
            	$('#customs_fees').attr('readonly', true);
				$('#Other_fees').attr('readonly', true);
				if(po_edit){
					$("#sticker").css("display", "none");

				$('#poshipping_received').attr('readonly', true);
				if($(this).val()=='received' || $(this).val()=='received1'){

				$('#poshipping_amount').attr('readonly', true);

		if (localStorage.getItem('poitems')) {
			poitems = JSON.parse(localStorage.getItem('poitems'));
		 sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function(o){return [parseInt(o.order)];}) : poitems;
		 //alert(sortedItems);




        $.each(sortedItems, function () {
            var item = this;


            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;


			var quantity_received=poitems[item_id].row.quantity_received;


			quantity_received=getQuantityReceived(item_id,quantity_received);

          poitems[item_id].row.unit_received=(parseFloat(poitems[item_id].row.qty) - parseFloat(quantity_received));
	     // poitems[item_id].row.received=(parseFloat(poitems[item_id].row.qty) - parseFloat(poitems[item_id].row.quantity_received));



        $.each(poitems[item_id].units, function(){
            if (this.id == poitems[item_id].row.unit) {
                qty_received = formatDecimal(unitToBaseQty(poitems[item_id].row.unit_received, this), 12);
            }
        });
        poitems[item_id].row.received = qty_received;


	    //poitems[item_id].row.received = 0;

		});

		}


				localStorage.setItem('poitems', JSON.stringify(poitems));
				loadItems();


				}else
					loadItems();
				}

			}
});
if (postatus = localStorage.getItem('postatus')) {
    $('#postatus').select2("val", postatus);
	if(localStorage.getItem('postatus')=='ordered'){
	$("#more_options").css("display", "none");

	}else if(localStorage.getItem('postatus')=='pending'){
		    	$('#podiscount').attr('readonly', false);
                $('#poshipping').attr('readonly', false);
				$('#customs_fees').attr('readonly', false);
				$('#Other_fees').attr('readonly', false);
				if(po_edit){
				$('#poref').attr('readonly', false);
				$('#powarehouse').select2('readonly', false);


				$("#sticker").css("display", "block");

				$('#poshipping_received').attr('readonly', true);
				}

			}else{
				$('#podiscount').attr('readonly', true);
                $('#poshipping').attr('readonly', true);
            	$('#customs_fees').attr('readonly', true);
				$('#Other_fees').attr('readonly', true);
				if(po_edit){
				$('#poref').attr('readonly', true);
				$('#powarehouse').select2('readonly', true);
				$("#sticker").css("display", "none");
				$('#poshipping_received').attr('readonly', true);
				}
			}

}

if(po_edit){
	var old_shipping_amount;
$('#poshipping_amount').focus(function () {
    old_shipping_amount = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_shipping_amount);
        bootbox.alert(lang.unexpected_value);
        return;
    }


    var shipping_amount = parseFloat(posh);

	if((shipping_amount <= $('#poshipping').val()) && (shipping_amount <= ($('#poshipping').val() - $('#poshipping_received').val())) && (shipping_amount >= 0))
	{
	//alert(($('#poshipping').val() - $('#poshipping_received').val()));
}else{
	 $(this).val(old_shipping_amount);
        bootbox.alert(lang.unexpected_value);
        return;

}
    //localStorage.setItem('poshipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping_amount + parseFloat($('#customs_fees').val() ? $('#customs_fees').val() : 0) + parseFloat($('#Other_fees').val() ? $('#Other_fees').val() : 0);
    $('#gtotal').text(formatMoney(gtotal,localStorage.getItem('pocurrency')));
    $('#tship').text(formatMoney(shipping_amount,localStorage.getItem('pocurrency')));
    $('#tcustoms_fees').text(formatMoney($('#customs_fees').val(),localStorage.getItem('pocurrency')));
    $('#tOther_fees').text(formatMoney($('#Other_fees').val(),localStorage.getItem('pocurrency')));
});

}
var old_shipping;
$('#poshipping').focus(function () {
    old_shipping = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_shipping);
        bootbox.alert(lang.unexpected_value);
        return;
    }
    shipping = parseFloat(posh);
    localStorage.setItem('poshipping', shipping);

	if(po_edit){
				$('#poshipping_amount').val(shipping - parseFloat($('#poshipping_received').val()));
			   }

    var gtotal = ((total + invoice_tax) - order_discount) + shipping + parseFloat($('#customs_fees').val() ? $('#customs_fees').val() : 0) + parseFloat($('#Other_fees').val() ? $('#Other_fees').val() : 0);
    $('#gtotal').text(formatMoney(gtotal,localStorage.getItem('pocurrency')));
    $('#tship').text(formatMoney(shipping,localStorage.getItem('pocurrency')));
    $('#tcustoms_fees').text(formatMoney($('#customs_fees').val(),localStorage.getItem('pocurrency')));
    $('#tOther_fees').text(formatMoney($('#Other_fees').val(),localStorage.getItem('pocurrency')));
});

var old_Other_fees;
$('#Other_fees').focus(function () {
    old_Other_fees = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_Other_fees);
        bootbox.alert(lang.unexpected_value);
        return;
    }
    Other_fees = parseFloat(posh);
    localStorage.setItem('poOther_fees', Other_fees);
	//alert(Other_fees);
    //alert($('#Other_fees').val());
    var gtotal = ((total + invoice_tax) - order_discount) + shipping + parseFloat($('#customs_fees').val() ? $('#customs_fees').val() : 0) + parseFloat($('#Other_fees').val() ? $('#Other_fees').val() : 0);
    $('#gtotal').text(formatMoney(gtotal,localStorage.getItem('pocurrency')));
    $('#tship').text(formatMoney(shipping,localStorage.getItem('pocurrency')));
    $('#tcustoms_fees').text(formatMoney($('#customs_fees').val(),localStorage.getItem('pocurrency')));
    $('#tOther_fees').text(formatMoney($('#Other_fees').val(),localStorage.getItem('pocurrency')));
});


var old_customs_fees;
$('#customs_fees').focus(function () {
    old_customs_fees = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_customs_fees);
        bootbox.alert(lang.unexpected_value);
        return;
    }

    customs_fees = parseFloat(posh);

    localStorage.setItem('pocustoms_fees', customs_fees);

    var gtotal = ((total + invoice_tax) - order_discount) + shipping + parseFloat($('#customs_fees').val() ? $('#customs_fees').val() : 0) + parseFloat($('#Other_fees').val() ? $('#Other_fees').val() : 0);
    $('#gtotal').text(formatMoney(gtotal,localStorage.getItem('pocurrency')));
    $('#tship').text(formatMoney(shipping,localStorage.getItem('pocurrency')));
    $('#tcustoms_fees').text(formatMoney($('#customs_fees').val(),localStorage.getItem('pocurrency')));
    $('#tOther_fees').text(formatMoney($('#Other_fees').val(),localStorage.getItem('pocurrency')));
});


if (poshipping = localStorage.getItem('poshipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}
if (pocustoms_fees = localStorage.getItem('pocustoms_fees')) {

	customs_fees = parseFloat(pocustoms_fees);

    $('#customs_fees').val(customs_fees);

}
if (poOther_fees = localStorage.getItem('poOther_fees')) {
    Other_fees = parseFloat(poOther_fees);
    $('#Other_fees').val(Other_fees);

}



if(po_edit){
if (poshipping_received = localStorage.getItem('poshipping_received')) {
    shipping_received = parseFloat(poshipping_received);
    $('#poshipping_received').val(shipping_received);
}

$('#poshipping_amount').val(shipping-shipping_received);
}
$('#popayment_term').change(function (e) {
    localStorage.setItem('popayment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('popayment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('poitems')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('ptax_method_type')) {
                    localStorage.removeItem('ptax_method_type');
                }
                if (localStorage.getItem('poitems')) {
                    localStorage.removeItem('poitems');
                }
                if (localStorage.getItem('podiscount')) {
                    localStorage.removeItem('podiscount');
                }
                if (localStorage.getItem('potax2')) {
                    localStorage.removeItem('potax2');
                }
                if (localStorage.getItem('poshipping')) {
                    localStorage.removeItem('poshipping');
                }
                if (localStorage.getItem('pocustoms_fees')) {
                    localStorage.removeItem('pocustoms_fees');
                }
                if (localStorage.getItem('poOther_fees')) {
                    localStorage.removeItem('poOther_fees');
                }
                if (localStorage.getItem('poref')) {
                    localStorage.removeItem('poref');
                }
                if (localStorage.getItem('powarehouse')) {
                    localStorage.removeItem('powarehouse');
                }
                if (localStorage.getItem('ponote')) {
                    localStorage.removeItem('ponote');
                }
                if (localStorage.getItem('posupplier')) {
                    localStorage.removeItem('posupplier');
                }
                if (localStorage.getItem('pocurrency')) {
                    localStorage.removeItem('pocurrency');
                }
                if (localStorage.getItem('poextras')) {
                    localStorage.removeItem('poextras');
                }
                if (localStorage.getItem('podate')) {
                    localStorage.removeItem('podate');
                }
                if (localStorage.getItem('postatus')) {
                    localStorage.removeItem('postatus');
                }
                if (localStorage.getItem('popayment_term')) {
                    localStorage.removeItem('popayment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#posupplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('poref', $(this).val());
});
if (poref = localStorage.getItem('poref')) {
    $('#poref').val(poref);
}
$('#powarehouse').change(function (e) {
    localStorage.setItem('powarehouse', $(this).val());


});
if (powarehouse = localStorage.getItem('powarehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}

        $('#pocurrency').change(function(e) {
        localStorage.setItem('pocurrency', $(this).val());


		try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'carspurchases/getCurrencyByCode/' + localStorage.getItem('pocurrency'),
                dataType: 'json',
                data: { code : localStorage.getItem('pocurrency')},
                success: function(data, page) {
					//data.co_type
					//alert(data.rate);
					currRate=data.rate;
                },
            });

		}catch(err){alert(err);}

					//alert();


    });
    if ((pocurrency = localStorage.getItem('pocurrency'))) {
        $('#pocurrency').select2('val', pocurrency);




    }

		$('#ponote').redactor('destroy');
        $('#ponote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('ponote', v);
            }
        });
        if (ponote = localStorage.getItem('ponote')) {
            $('#ponote').redactor('set', ponote);
        }




		$supplier.change(function (e) {

            localStorage.setItem('posupplier', $(this).val());
            $('#supplier_id').val($(this).val());



			try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'customers/getCustomerco_type/' + localStorage.getItem('posupplier'),
                dataType: 'json',
                data: { id : localStorage.getItem('posupplier')},
                success: function(data, page) {
					if (data.co_type==1) {
                        $('#Other_fees').val('');
						$('#Other_fees').attr('readonly', true);
                        $('#customs_fees').val('');
						$('#customs_fees').attr('readonly', true);
						$("#div_customs_fees").css("display", "none");
				        $("#div_Other_fees").css("display", "none");
						$("#div_tcustoms_fees").css("display", "none");
						$("#div_tOther_fees").css("display", "none");



                    } else {
                    	$('#Other_fees').val('');
						$('#Other_fees').attr('readonly', false);
                        $('#customs_fees').val('');
						$('#customs_fees').attr('readonly', false);
						$("#div_customs_fees").css("display", "block");
				        $("#div_Other_fees").css("display", "block");
						$("#div_tcustoms_fees").css("display", "table-cell");
						$("#div_tOther_fees").css("display", "table-cell");



                    }

                },
            });

		}catch(err){alert(err);}
        });
		/*$('#Other_fees').val('');
		$('#Other_fees').attr('readonly', true);
        $('#customs_fees').val('');
		$('#customs_fees').attr('readonly', true);
		*/

		try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'customers/getCustomerco_type/' + localStorage.getItem('posupplier'),
                dataType: 'json',
                data: { id : localStorage.getItem('posupplier')},
                success: function(data, page) {
					if (data.co_type==1) {
                        $('#Other_fees').val('');
						$('#Other_fees').attr('readonly', true);
                        $('#customs_fees').val('');
						$('#customs_fees').attr('readonly', true);
                    	$("#div_customs_fees").css("display", "none");
				        $("#div_Other_fees").css("display", "none");
						$("#div_tcustoms_fees").css("display", "none");
						$("#div_tOther_fees").css("display", "none");

                    } else {
						if (po_edit){
                    	//$('#Other_fees').val('');
						$('#Other_fees').attr('readonly', true);
                        //$('#customs_fees').val('');
						$('#customs_fees').attr('readonly', true);
						}else{
						$('#Other_fees').val('');
						$('#Other_fees').attr('readonly', false);
                        $('#customs_fees').val('');
						$('#customs_fees').attr('readonly', false);

						}
                    	$("#div_customs_fees").css("display", "block");
				        $("#div_Other_fees").css("display", "block");
						$("#div_tcustoms_fees").css("display", "table-cell");
						$("#div_tOther_fees").css("display", "table-cell");

                    }

                },
            });
		}catch(err){alert(err);}


        if (posupplier = localStorage.getItem('posupplier')) {
            $supplier.val(posupplier).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url+"carssuppliers/getSupplier/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "carssuppliers/suggestions",
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
            });

} else {
    nsSupplier();
}

    /*$('.rexpiry').change(function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        poitems[item_id].row.expiry = $(this).val();
        localStorage.setItem('poitems', JSON.stringify(poitems));
    });*/
if (localStorage.getItem('poextras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('poextras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("poextras");
    $('#extras-con').slideUp();
});
$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    poitems[item_id].row.expiry = $(this).val();
    localStorage.setItem('poitems', JSON.stringify(poitems));
});


// prevent default action upon enter
$('body').bind('keypress', function (e) {
    if ($(e.target).hasClass('redactor_editor')) {
        return true;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calcuation
if (site.settings.tax2 != 0) {
    $('#potax2').change(function () {
        localStorage.setItem('potax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_podiscount;
$('#podiscount').focus(function () {
    old_podiscount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('podiscount');
        localStorage.setItem('podiscount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_podiscount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete poitems[item_id];
        row.remove();
		if($('#poTable tr').length==2){
			try{
			 if(Object.keys(JSON.parse(localStorage.getItem('poitems'))).length >0){
				 $('#tax_method_type').select2('readonly', false);
				 $('#pocurrency').select2('readonly', false);
				  $('#pocurrency').select2('readonly', false);


			 }
			}catch(err){}

		}
        if(poitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('poitems', JSON.stringify(poitems));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.edit', function () {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = poitems[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_cost = formatDecimal(row.children().children('.rucost').val()),
        discount = row.children().children('.rdiscount').val();
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        var real_unit_cost = item.row.real_unit_cost;
        var net_cost = real_unit_cost;
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_cost -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            /*if (poitems[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_cost -= pr_tax_val;
                            } else*/ {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / 100), 12);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function () {
                if(o == 1) {
                    if(product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
                }
                $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                o++;
            });
        }

        uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        $.each(item.units, function () {
            if(this.id == item.row.unit) {
                $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
            } else {
                $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
            }
        });

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pcost').val(unit_cost);
        $('#punit_cost').val(formatDecimal(parseFloat(unit_cost)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_cost').val(unit_cost);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pexpiry').val(row.children().children('.rexpiry').val());
        $('#pdiscount').val(discount);
        $('#net_cost').text(formatMoney(net_cost,localStorage.getItem('pocurrency')));
        $('#pro_tax').text(formatMoney(pr_tax_val,localStorage.getItem('pocurrency')));
        $('#psubtotal').val('');
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pcost, #ptax, #pdiscount', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_cost = parseFloat($('#pcost').val());
        var item = poitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_cost) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
		unit_cost -= item_discount;
        //var pr_tax = $('#ptax').val(), item_tax_method = item.row.tax_method;
        var pr_tax = $('#ptax').val(), item_tax_method = 1;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            //pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 12);
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100)), 12);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_cost -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / 100), 12);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }
		//alert(unit_cost);

        $('#net_cost').text(formatMoney(unit_cost,localStorage.getItem('pocurrency')));
        $('#pro_tax').text(formatMoney(pr_tax_val,localStorage.getItem('pocurrency')));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != poitems[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    $('#pcost').val(formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 12)).change();
                }
            });
        } else {
            $('#pcost').val(formatDecimal(item.row.base_unit_cost)).change();
        }
    });

    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
        qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal/qty), 12)).change();
        return false;
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {};
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        poitems[item_id].row.fup = 1,
        poitems[item_id].row.qty = parseFloat($('#pquantity').val()),
        poitems[item_id].row.base_quantity = parseFloat(base_quantity),
        poitems[item_id].row.unit = unit,
        poitems[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        poitems[item_id].row.tax_rate = new_pr_tax,
        poitems[item_id].tax_rate = new_pr_tax_rate,
        poitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        poitems[item_id].row.option = $('#poption').val(),
        poitems[item_id].row.expiry = $('#pexpiry').val() ? $('#pexpiry').val() : '';
        localStorage.setItem('poitems', JSON.stringify(poitems));
        $('#prModal').modal('hide');
        loadItems();
        return;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {

        //if ($('#powarehouse').val() && $('#posupplier').val()  && $('#tax_method_type').val()) {
        if ($('#powarehouse').val() && $('#tax_method_type').val()) {
                $('#mModal').appendTo("body").modal('show');
        } else {
            bootbox.alert(lang.select_above);
        }
        return false;
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
     var old_row_qty;
     $(document).on("focus", '.rquantity', function () {

        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.base_quantity = new_qty;
        if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.received = new_qty;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    var old_received;
     $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
		var new_received = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
		var quantity_received=poitems[item_id].row.quantity_received;


		if(poitems[item_id].row.unit != poitems[item_id].row.base_unit){
				$.each(poitems[item_id].units, function(){
                        if (this.id == poitems[item_id].row.unit) {
							quantity_received=formatDecimal(baseToUnitQty(quantity_received, this),12);

                        }
                    });

			}
		if (new_received > (poitems[item_id].row.qty - quantity_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(poitems[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 12);
            }
        });
        poitems[item_id].row.unit_received = new_received;
        poitems[item_id].row.received = qty_received;


        localStorage.setItem('poitems', JSON.stringify(poitems));

		var rfinaltotal=0;
		var finaltotal=0;
		if (localStorage.getItem('poitems')) {
			poitems = JSON.parse(localStorage.getItem('poitems'));
		 sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function(o){return [parseInt(o.order)];}) : poitems;
		 //alert(sortedItems);

        $.each(sortedItems, function () {
            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
           rfinaltotal=rfinaltotal+(parseFloat(poitems[item_id].row.received) + parseFloat(quantity_received));
	       finaltotal=finaltotal+( parseFloat(poitems[item_id].row.qty));
	    //poitems[item_id].row.received = 0;

		});
		if(rfinaltotal==finaltotal){

		$('#postatus').val('received').trigger('change.select2');
		$('#poshipping_amount').attr('readonly', true);
		$('#postatus').select2('readonly', true);
		$('#poshipping_amount').val(parseFloat($('#poshipping').val()) - parseFloat($('#poshipping_received').val()));


		}else{
		//$('#poshipping_amount').attr('readonly', false);
		//$('#postatus').attr('readonly', false);

		}
		}


        loadItems();
    });

    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
     var old_cost;
     $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.cost = new_cost;
		poitems[item_id].row.real_unit_cost = new_cost;
		poitems[item_id].row.fup = 1;
		var qty = poitems[item_id].row.qty;//$('.rquantity').val();

        //
		var unit = poitems[item_id].row.unit;

		var base_quantity = parseFloat(qty);
        if(unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty(qty, this);
                }
            });
        }

		poitems[item_id].row.base_quantity = parseFloat(base_quantity);
        poitems[item_id].row.unit = unit;

        //alert(qty);


		/*poitems[item_id].row.real_unit_cost = poitems[item_id].row.unit;


		 alert(poitems[item_id].row.unit);
		 alert(poitems[item_id].row.base_unit);
		 if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        */

		localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#posupplier').select2('readonly', false);
     return false;
 });

    if (po_edit) {
        $('#posupplier').select2("readonly", true);
    }



});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
    $('#posupplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "carssuppliers/suggestions",
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
    });
}

function loadItems() {

    if (localStorage.getItem('poitems')) {


		try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'carspurchases/getCurrencyByCode/' + $('#pocurrency').val(),
                dataType: 'json',
                data: { code : $('#pocurrency').val()},
                success: function(data, page) {
					//data.co_type
					//alert(data.rate);
					currRate=data.rate;
                },
            });

		}catch(err){alert(err);}


        orgtotal = 0;
        total = 0;
        ptotal = 0;
        //pflag = 1;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#poTable tbody").empty();

        poitems = JSON.parse(localStorage.getItem('poitems'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function(o){return [parseInt(o.order)];}) : poitems;

        var order_no = new Date().getTime();

        $.each(sortedItems, function () {

            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : order_no++;
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_rqty = item.row.quantity_received, item_expiry = item.row.expiry, item_tax_method = 1/*item.row.tax_method*/, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");

			//if(item.row.received >= 0)
			//item_rqty=item.row.received;

			//var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
			//alert(item.row.received);
			//alert(item_qty);
			//alert(item_rqty);
			//var qty_received = ();
			if(item.row.unit != item.row.base_unit){
				$.each(item.units, function(){
                        if (this.id == item.row.unit) {
							item_rqty=formatDecimal(baseToUnitQty(item_rqty, this),12);

                        }
                    });

			}
			var qty_received = (item.row.received >= 0) ? item.row.received : (item_qty-item_rqty);




			//var qty_received = (item.row.received >= 0) ? item.row.received : (item.row.qty-item_rqty);
            //var qty_received =  (item.row.qty);
            //alert(item_rqty);

			var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            if (item.row.new_entry == 1) { item_bqty = item_qty; item_oqty = item_qty; }
            var unit_cost = item.row.real_unit_cost;

            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('posupplier'), belong = false;

                if (supplier == item.row.supplier1) {
                    belong = true;
                } else
                if (supplier == item.row.supplier2) {
                    belong = true;
                } else
                if (supplier == item.row.supplier3) {
                    belong = true;
                } else
                if (supplier == item.row.supplier4) {
                    belong = true;
                } else
                if (supplier == item.row.supplier5) {
                    belong = true;
                }
                var unit_qty_received = qty_received;


				if(item.row.fup != 1 && product_unit != item.row.base_unit) {
                    $.each(item.units, function(){
                        if (this.id == product_unit) {
                            base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 12);
                            unit_qty_received = item.row.unit_received ? item.row.unit_received : formatDecimal(baseToUnitQty(qty_received, this), 12);
                            //if(unit_qty_received!=item_qty &&  $('#postatus').val()=='received'){
							//	unit_qty_received = qty_received;
							 //   unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 12);
							//}
							if (!po_edit)
							unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 12);
							else
							unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost)), 12);

                        }
                    });
                }
				item.row.real_unit_cost=unit_cost;
                //alert(unit_cost);
				var ds = item_ds ? item_ds : '0';
                item_discount = calculateDiscount(ds, unit_cost);
                product_discount += parseFloat(item_discount * item_qty);


                unit_cost = formatDecimal(unit_cost-item_discount, 12);



				/*new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {};
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }*/






        //poitems[item_id].row.tax_rate = new_pr_tax,
        //poitems[item_id].tax_rate = new_pr_tax_rate,



                var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
                if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, unit_cost, item_tax_method))) {
                    pr_tax_val = ptax[0];
                    pr_tax_rate = ptax[1];
                    product_tax += pr_tax_val * item_qty;
                }



                item_cost = item_tax_method == 0 ? formatDecimal(unit_cost-pr_tax_val, 12) : formatDecimal(unit_cost,12);
                unit_cost = formatDecimal(unit_cost+item_discount, 12);
                var sel_opt = '';
                $.each(item.options, function () {
                    if(this.id == item_option) {
                        sel_opt = this.name;
                    }
                });




            var row_no = item.id;
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
			if($('#tax_method_type').val()!=1)
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+' <span class="label label-default">'+item_supplier_part_no+'</span></span> <i class="pull-right fa fa-edit tip edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i></td>';
            else
			tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+' <span class="label label-default">'+item_supplier_part_no+'</span></span> </td>';
            if (site.settings.product_expiry == 1) {
                tr_html += '<td style="display:none;"><input class="form-control date rexpiry" name="expiry[]" type="text" value="' + item_expiry + '" data-id="' + row_no + '" data-item="' + item_id + '" id="expiry_' + row_no + '"></td>';
            }
            tr_html += '<td class="text-right"><input class="form-control text-center rcost" name="net_cost[]" type="text" id="cost_' + row_no + '" value="' + formatDecimal(item_cost,12) + '"><input class="rucost" name="unit_cost[]" type="hidden" value="' + unit_cost + '"><input class="realucost" name="real_unit_cost[]" type="hidden" value="' + item.row.real_unit_cost + '"><span class="text-right scost" style="display:none;" id="scost_' + row_no + '">' + formatMoney(item_cost,localStorage.getItem('pocurrency')) + '</span></td>';
            tr_html += '<td><input name="quantity_balance[]" type="hidden" class="rbqty" value="' + item_bqty + '"><input class="form-control text-center rquantity" name="quantity[]" type="text" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';

			if (po_edit && localStorage.getItem('postatus')!='pending' && localStorage.getItem('postatus')!='ordered') {
                //tr_html += '<td class="text-right"><span class="text-right" id="received_quantity' + row_no + '">' + formatDecimal(item_qty-unit_qty_received) + '</span></td>';
				tr_html += '<td class="rec_con"><input class="form-control text-center" name="prev_received[]" type="text" value="' + formatDecimal(item_rqty) + '" data-id="' + row_no + '" data-item="' + item_id + '"   readonly id="prev_received_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="rec_con"><input name="ordered_quantity[]" type="hidden" class="oqty" value="' + item_oqty + '"><input class="form-control text-center received" name="received[]" type="text" value="' + formatDecimal(unit_qty_received) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="received_' + row_no + '" onClick="this.select();"><input name="received_base_quantity[]" type="hidden" class="rrbase_quantity" value="' + qty_received + '"></td>';
            }
			if($('#tax_method_type').val()!=1)
            if (site.settings.product_discount == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '"><span class="text-right sdiscount text-danger" id="sdiscount_' + row_no + '">' + formatMoney(0 - (item_discount * item_qty),localStorage.getItem('pocurrency')) + '</span></td>';
            }
            if($('#tax_method_type').val()!=1)
            if (site.settings.tax1 == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (pr_tax_rate ? '(' + pr_tax_rate + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty,localStorage.getItem('pocurrency')) + '</span></td>';
            }
            tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)),localStorage.getItem('pocurrency')) + '</span></td>';
            tr_html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#poTable");


            if (po_edit){
			ptotal += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(unit_qty_received)), 12);
            //count += parseFloat(unit_qty_received);
            orgtotal += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 12);

			}

			total += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 12);
            count += parseFloat(item_qty);
			an++;
            if(!belong)
                $('#row_' + row_no).addClass('warning');
        });


        var col = 2;
        if (site.settings.product_expiry == 1) {  }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatQty(parseFloat(count) - 1) + '</th>';
        if (po_edit && localStorage.getItem('postatus')!='pending'  && localStorage.getItem('postatus')!='ordered') {
            tfoot += '<th class="rec_con"></th>';
            tfoot += '<th class="rec_con"></th>';
        }
        if($('#tax_method_type').val()!=1)
        if (site.settings.product_discount == 1) {
            tfoot += '<th class="text-right">'+formatMoney(product_discount,localStorage.getItem('pocurrency'))+'</th>';
        }
        if($('#tax_method_type').val()!=1)
        if (site.settings.tax1 == 1) {
            tfoot += '<th class="text-right">'+formatMoney(product_tax,localStorage.getItem('pocurrency'))+'</th>';
        }
        tfoot += '<th class="text-right">'+formatMoney(total,localStorage.getItem('pocurrency'))+'</th><th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#poTable tfoot').html(tfoot);




        // Order level discount calculations
        if (podiscount = localStorage.getItem('podiscount')) {
            var ds = podiscount;
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    order_discount = formatDecimal(((total * parseFloat(pds[0])) / 100), 12);
                } else {
                    order_discount = formatDecimal(ds);
                }
            } else {
                order_discount = formatDecimal(ds);
            }
        }
         //if(po_edit)
			//alert(ptotal);
		 if(po_edit){
			 //alert(ptotal);
			 //alert(total);
			 var odis=formatDecimal((ptotal / total * order_discount), 12);
			 //alert(odis);
			 //alert(odis);
			 //alert(odis);

		order_discount=odis;
		//$('#podiscount').val(formatDecimal(odis));
		total=ptotal;



		var shipping=parseFloat($('#poshipping').val());
		var shipping_amount=formatDecimal((ptotal / orgtotal * shipping), 12);
		$('#poshipping_amount').val(shipping_amount);

		//alert(shipping);
		//alert(ptotal);
		//alert(total);
		//alert(shipping_amount);

		}else{
			var shipping = $('#poshipping').val() ? $('#poshipping').val() : 0;
		shipping=parseFloat(shipping);
		//alert(shipping);
		}
		//alert(orgtotal);
        // Order level tax calculations
        if (site.settings.tax2 != 0) {
            if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 12);
                        }
                    }
                });
            }
        }
        total_discount = parseFloat(order_discount + product_discount);
        // Totals calculations after item addition
		var gtotal;
		if(po_edit)
         gtotal = ((total + invoice_tax) - order_discount) + formatDecimal($('#poshipping_amount').val());
        else
		 gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#total').text(formatMoney(total,localStorage.getItem('pocurrency')));
        $('#titems').text((an-1)+' ('+(formatQty(parseFloat(count) - 1))+')');
        $('#tds').text(formatMoney(order_discount,localStorage.getItem('pocurrency')));
        if (site.settings.tax1) {
            $('#ttax1').text(formatMoney(product_tax,localStorage.getItem('pocurrency')));
        }
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(invoice_tax,localStorage.getItem('pocurrency')));
        }
		$('#gtotal').text(formatMoney(gtotal,localStorage.getItem('pocurrency')));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
    }
}


 function getQuantityReceived(item_id,quantity_received){

	 if(poitems[item_id].row.unit != poitems[item_id].row.base_unit){
				$.each(poitems[item_id].units, function(){
                        if (this.id == poitems[item_id].row.unit) {
							quantity_received=formatDecimal(baseToUnitQty(quantity_received, this),12);

                        }
                    });

			}else{
			quantity_received=quantity_received;
			}

			return quantity_received;

 }
/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_purchase_item(item) {
     if($('#tax_method_type').val()){
	 $('#tax_method_type').select2('readonly', true);
	 }else{
	        bootbox.alert(lang.select_above);
            item = null;
            return;
       }
    if (count == 1) {

        poitems = {};
        if ($('#posupplier').val()) {
            $('#posupplier').select2("readonly", true);
			$('#pocurrency').select2('readonly', true);

        } else {
        //    bootbox.alert(lang.select_above);
        //    item = null;
        //    return;
        }
    }
    if (item == null)
        return;



    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    if (poitems[item_id]) {




        var new_qty = parseFloat(poitems[item_id].row.qty) + 1;
        poitems[item_id].row.base_quantity = new_qty;
        if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        poitems[item_id].row.qty = new_qty;

    } else {





        poitems[item_id] = item;

        if (count == 1){
            old_owner_id=poitems[item_id].row.owner_id;

            //localStorage.setItem('sold_item_tax',old_item_tax);
            }


            if(poitems[item_id].owner_id!=old_owner_id){

                bootbox.alert(lang.The_owner_of_all_vehicles_must_be_the_same);
                delete poitems[item_id];
                    item = null;
                    return;
                }


    }


	//console.log(poitems[item_id].owner_id);
        localStorage.setItem('posupplier',poitems[item_id].owner_id);
        $('#posupplier').val(poitems[item_id].owner_id);



	if($('#tax_method_type').val()!=1) {
            $('#ptax').select2('val', item.row.tax_rate);

            $('#old_tax').val(item.row.tax_rate);

	var  new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {};
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

	poitems[item_id].row.tax_rate = new_pr_tax;
    poitems[item_id].tax_rate = new_pr_tax_rate;
	}
    poitems[item_id].order = new Date().getTime();
    poitems[item_id].row.real_unit_cost=poitems[item_id].row.real_unit_cost / currRate;
	poitems[item_id].row.base_unit_cost=poitems[item_id].row.base_unit_cost / currRate;


	localStorage.setItem('poitems', JSON.stringify(poitems));
	try{
    loadItems();
	}catch(err){
	//	alert(err);
	}
    return true;

}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
