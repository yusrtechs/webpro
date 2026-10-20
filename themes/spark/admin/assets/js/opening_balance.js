$(document).ready(function () {


    //ItemnTotals();


    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();


    //localStorage.clear();
    // If there is any item in localStorage
    if (localStorage.getItem('obitems')) {

        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('obitems')) {
                    localStorage.removeItem('obitems');
                }
                if (localStorage.getItem('obref')) {
                    localStorage.removeItem('obref');
                }
                if (localStorage.getItem('obwarehouse')) {
                    localStorage.removeItem('obwarehouse');
                }
                if (localStorage.getItem('obnote')) {
                    localStorage.removeItem('obnote');
                }
                if (localStorage.getItem('approve_note')) {
                    localStorage.removeItem('approve_note');
                }
                if (localStorage.getItem('obdate')) {
                    localStorage.removeItem('obdate');
                }
                if (localStorage.getItem('obbrId')) {
                    localStorage.removeItem('obbrId');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

    // save and load the fields in and/or from localStorage
    $('#obref').change(function (e) {
        localStorage.setItem('obref', $(this).val());
    });
    if (obref = localStorage.getItem('obref')) {
        $('#obref').val(obref);
    }
    $('#obwarehouse').change(function (e) {
        localStorage.setItem('obwarehouse', $(this).val());
    });
    if (obwarehouse = localStorage.getItem('obwarehouse')) {
        $('#obwarehouse').select2("val", obwarehouse);
    }

    //$(document).on('change', '#obnote', function (e) {
        $('#obnote').redactor('destroy');
        $('#obnote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('obnote', v);
            }
        });
        if (obnote = localStorage.getItem('obnote')) {
            $('#obnote').redactor('set', obnote);
        }

		    $('#approve_note').redactor('destroy');
        $('#approve_note').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('approve_note', v);
            }
        });
        if (approve_note = localStorage.getItem('approve_note')) {
            $('#approve_note').redactor('set', approve_note);
        }



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


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

    $(document).on('click', '.obdel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete obitems[item_id];
        row.remove();
        if($('#obTable tr').length==3){

			try{
			 if(Object.keys(JSON.parse(localStorage.getItem('obitems'))).length >0)

			 $('#obbrId').select2('readonly', false);
			}catch(err){}

		}

        if(obitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('obitems', JSON.stringify(obitems));
            loadItems();
            return;
        }
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */

    $(document).on("change", '.rdebit', function () {
        var row = $(this).closest('tr');
		if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            //$(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        obitems[item_id].row.debit = parseFloat(new_qty);

        //obitems[item_id].row.value = new_qty*parseFloat(obitems[item_id].row.cost);
        localStorage.setItem('obitems', JSON.stringify(obitems));
        loadItems();
    });

	$(document).on("change", '.rcredit', function () {
        var row = $(this).closest('tr');
		if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            //$(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        obitems[item_id].row.credit = parseFloat(new_qty);

        //obitems[item_id].row.value = new_qty*parseFloat(obitems[item_id].row.cost);
        localStorage.setItem('obitems', JSON.stringify(obitems));
        loadItems();
    });

    $(document).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        obitems[item_id].row.cost = new_cost;
        obitems[item_id].row.value = new_cost*parseFloat(obitems[item_id].row.qty);
        localStorage.setItem('obitems', JSON.stringify(obitems));
        loadItems();
    });
$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    obitems[item_id].row.expiry = $(this).val();
    localStorage.setItem('obitems', JSON.stringify(obitems));
});


    $(document).on("change", '.rtype', function () {
        var row = $(this).closest('tr');
        var new_type = $(this).val(),
        item_id = row.attr('data-item-id');
        obitems[item_id].row.type = new_type;
        localStorage.setItem('obitems', JSON.stringify(obitems));
    });

    $(document).on("change", '.rcost_center', function () {
        var row = $(this).closest('tr');
        var new_opt = $(this).val(),
        item_id = row.attr('data-item-id');
			//console.log(row.attr('data-item-id'))
			$('#costcenter'+row.attr('data-item-id')).prop('readonly', false);


        obitems[item_id].row.cost_center = new_opt;
        localStorage.setItem('obitems', JSON.stringify(obitems));
    });

    $(document).on("change", '.rcost_center_amount', function () {
        var row = $(this).closest('tr');
        var new_opt = $(this).val(),
        item_id = row.attr('data-item-id');
		obitems[item_id].row.cost_center_amount = new_opt;
        localStorage.setItem('obitems', JSON.stringify(obitems));
    });


});


/* -----------------------
 * Load Items to table
 ----------------------- */

function loadItems() {


    if (localStorage.getItem('obitems')) {
        count = 1;
		tdebit=1;
        count_cost = 1;
        count_value = 1;
        an = 1;
        $("#obTable tbody").empty();
        obitems = JSON.parse(localStorage.getItem('obitems'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(obitems, function(o){return [parseInt(o.order)];}) : obitems;
        $.each(sortedItems, function () {

            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : new Date().getTime();
            var account_id = item.row.id, debit = item.row.debit, credit = item.row.credit, cost_center_amount = item.row.cost_center_amount, balance = item.row.balance, accountID = item.row.accountID,  accountName = item.row.accountName.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var type = item.row.accountSide;


			var opt = $("<select id=\"cost_center\"  name=\"cost_center[]\"  class=\"form-control select rcost_center\" />");
			//var opt=row.find('.obcost_center');
            cost_centers = JSON.parse(localStorage.getItem('cost_centers'));
        //alert(cost_centers)
			try{
					     $("<option />", {value: ''	, text: ''}).appendTo(opt);

			//if(item.cost_centers !== false) {
                $.each(cost_centers, function () {
				   //alert(this.id)
                    if (item.row.cost_center == this.id)
                        $("<option />", {value: this.id	, text: this.costName, selected: 'selected'}).appendTo(opt);
                    else
                        $("<option />", {value: this.id	, text: this.costName}).appendTo(opt);
                });
            //} else {
            //    $("<option />", {value: 0, text: 'n/a'}).appendTo(opt);
            //    opt = opt.hide();
            //}

			}catch(err){
				alert(err)
			}


            var row_no = item.id;
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="account_id[]" type="hidden" class="rid" value="' + account_id + '"><span class="obname" id="name_' + row_no + '">' + accountID +' - ' + accountName +'</span></td>';
            if(type=='Debit'){

			tr_html += '<td><input class="form-control text-center rdebit" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="debit[]" type="number" min="0" step=".01" value="' + formatDecimal(debit,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input class="form-control text-center rcredit" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="credit[]" type="number" min="0" step=".01" readonly value="' + formatDecimal(credit,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="cost' + row_no + '" onClick="this.select();"></td>';
            }else{

			tr_html += '<td><input class="form-control text-center rdebit" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="debit[]" type="number" min="0" step=".01"  readonly value="' + formatDecimal(debit,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input class="form-control text-center rcredit" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="credit[]" type="number" min="0" step=".01"  value="' + formatDecimal(credit,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="cost' + row_no + '" onClick="this.select();"></td>';



			}
			//tr_html += '<td><input class="form-control text-center " tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'"  type="text"  value="" data-id="' + row_no + '" data-item="' + item_id + '" id="value' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td>'+(opt.get(0).outerHTML)+'</td>';
            tr_html += '<td><input class="form-control text-center rcost_center_amount" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="cost_center_amount[]" type="number" min="0" step=".01" readonly value="' + formatDecimal(cost_center_amount,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="costcenter' + item_id + '" onClick="this.select();"></td>';
            //tr_html += '<td><select class="form-contol select rtype" style="width:100%;"><option value="addition"'+(type == 'addition' ? ' selected' : '')+'>'+type_opt.addition+'</option></select></td>';


            tr_html += '<td class="text-center"><i class="fa fa-times tip obdel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#obTable");
            tdebit += parseFloat(debit);
            count_cost += parseFloat(credit);
            count_value += parseFloat(balance);
			count++;
            an++;
			if(count)
			{
				//$('#obwarehouse').select2('readonly', true);
			}


        });


        var col = 1;
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatDecimal((tdebit) - 1,4) + '</th><th class="text-center">' + formatDecimal((count_cost) - 1,4) + '</th><th></th><th></th><th></th>';
        tfoot += '</tr>';
		if((tdebit-count_cost)!=0){
			tfoot += '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'"></th><th class="text-center" style="color:red;" >' + formatDecimal(Math.abs(tdebit-count_cost),4) + '</th><th></th><th></th></tr></th></tr></th></tr>';
        try{
            if(site.settings.accounting_type!='periodic')
			$('#add_opening_balance').attr('disabled', 'true');
		}catch(err){}
		}else{
			tfoot += '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'"></th><th class="text-center" >' + formatDecimal((tdebit-count_cost),4) + '</th><th></th><th></th><th></th><th></th></tr>';
        try{
			$('#add_opening_balance').removeAttr("disabled");
			}catch(err){}
		}

		$('#obTable tfoot').html(tfoot);
        $('select.select').select2({minimumResultsForSearch: 7});

        if (count > 1) {

            $('#obbrId').select2('readonly', true);
        }

        //if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
        //    $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
        //    $(window).scrollTop($(window).scrollTop() + 1);
        //}
		//alert('');
        //set_page_focus();
    }

}

/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
function add_opening_balance_item(item) {




    if (count == 1) {
        obitems = {};
        $('#obbrId').select2('readonly', false);
    }
    if (item == null)
        return;



    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;

    if (obitems[item_id]) {

        var new_qty = parseFloat(obitems[item_id].row.qty) + 1;

        try{
        obitems[item_id].row.base_quantity = new_qty;

		if(obitems[item_id].row.unit != obitems[item_id].row.base_unit) {
            $.each(obitems[item_id].units, function(){
                if (this.id == obitems[item_id].row.unit) {
                    obitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }

        }catch(err){
		//alert(err);
		}
		obitems[item_id].row.qty = new_qty;


    } else {
        obitems[item_id] = item;
    }
    obitems[item_id].order = new Date().getTime();
    localStorage.setItem('obitems', JSON.stringify(obitems));
    loadItems();
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
