$(document).ready(function () {
    if (!localStorage.getItem('rvref')) {
        localStorage.setItem('rvref', '');
    }

    ItemnTotals();
    $('.bootbox').on('hidden.bs.modal', function (e) {
        $('#add_item').focus();
    });
    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();
    if (site.settings.set_focus != 1) {
        $('#add_item').focus();
    }

    //localStorage.clear();
    // If there is any item in localStorage
    if (localStorage.getItem('rvitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('rvitems')) {
                    localStorage.removeItem('rvitems');
                }
                if (localStorage.getItem('rvref')) {
                    localStorage.removeItem('rvref');
                }
                if (localStorage.getItem('rvwarehouse')) {
                    localStorage.removeItem('rvwarehouse');
                }
                if (localStorage.getItem('rvnote')) {
                    localStorage.removeItem('rvnote');
                }
                if (localStorage.getItem('approve_note')) {
                    localStorage.removeItem('approve_note');
                }
                if (localStorage.getItem('rvdate')) {
                    localStorage.removeItem('rvdate');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

    // save and load the fields in and/or from localStorage
    $('#rvref').change(function (e) {
        localStorage.setItem('rvref', $(this).val());
    });
    if (rvref = localStorage.getItem('rvref')) {
        $('#rvref').val(rvref);
    }
    $('#rvwarehouse').change(function (e) {
        localStorage.setItem('rvwarehouse', $(this).val());
    });
    if (rvwarehouse = localStorage.getItem('rvwarehouse')) {
        $('#rvwarehouse').select2("val", rvwarehouse);
    }

    //$(document).on('change', '#rvnote', function (e) {
        $('#rvnote').redactor('destroy');
        $('#rvnote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('rvnote', v);
            }
        });
        if (rvnote = localStorage.getItem('rvnote')) {
            $('#rvnote').redactor('set', rvnote);
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

    $(document).on('click', '.rvdel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete rvitems[item_id];
        row.remove();
        if(rvitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('rvitems', JSON.stringify(rvitems));
            loadItems();
            return;
        }
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */

    $(document).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        rvitems[item_id].row.qty = parseFloat(new_qty);

        rvitems[item_id].row.value = new_qty*parseFloat(rvitems[item_id].row.cost);
        localStorage.setItem('rvitems', JSON.stringify(rvitems));
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
        rvitems[item_id].row.cost = new_cost;
        rvitems[item_id].row.value = new_cost*parseFloat(rvitems[item_id].row.qty);
        localStorage.setItem('rvitems', JSON.stringify(rvitems));
        loadItems();
    });
$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    rvitems[item_id].row.expiry = $(this).val();
    localStorage.setItem('rvitems', JSON.stringify(rvitems));
});


    $(document).on("change", '.rtype', function () {
        var row = $(this).closest('tr');
        var new_type = $(this).val(),
        item_id = row.attr('data-item-id');
        rvitems[item_id].row.type = new_type;
        localStorage.setItem('rvitems', JSON.stringify(rvitems));
    });

    $(document).on("change", '.rvariant', function () {
        var row = $(this).closest('tr');
        var new_opt = $(this).val(),
        item_id = row.attr('data-item-id');
        rvitems[item_id].row.option = new_opt;
        localStorage.setItem('rvitems', JSON.stringify(rvitems));
    });


});


/* -----------------------
 * Load Items to table
 ----------------------- */

function loadItems() {

    if (localStorage.getItem('rvitems')) {
        count = 1;
        count_cost = 1;

        an = 1;
        $("#rvTable tbody").empty();
        rvitems = JSON.parse(localStorage.getItem('rvitems'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(rvitems, function(o){return [parseInt(o.order)];}) : rvitems;
        $.each(sortedItems, function () {

            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : new Date().getTime();
            var product_id = item.row.id,item_expiry = item.row.expiry, item_qty = item.row.qty,item_unit_id = item.row.unit.id,item_unit_name = item.row.unit.name, old_cost = item.row.avg_cost, item_cost = item.row.cost, item_value = item.row.value, item_option = item.row.option, item_code = item.row.code, item_serial = item.row.serial, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var type = item.row.type ? item.row.type : '';


            var opt = $("<select id=\"poption\"  class=\"form-control select rvariant\" />");
            if(item.options !== false) {
                $.each(item.options, function () {
                    if (item.row.option == this.id)
                        $("<option />", {value: this.id, text: this.name, selected: 'selected'}).appendTo(opt);
                    else
                        $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                });
            } else {
                $("<option />", {value: 0, text: 'n/a'}).appendTo(opt);
                opt = opt.hide();
            }


            var row_no = item.id;
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - ' + item_name +'</span></td>';
            tr_html += '<td style="display:none;"><input class="form-control text-center rquantity" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="quantity[]" type="text" value="' + formatDecimal(item_qty,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input class="form-control text-center rold_cost" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'"  name="old_cost[]" type="text" readonly value="' + formatDecimal(old_cost,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="old_cost' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input class="form-control text-center rcost" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" name="cost[]" type="text" value="' + formatDecimal(item_cost,4) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="cost' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input  name="unit[]" type="hidden" value="' + item_unit_id + '"><span class="sname" id="unit_' + row_no + '">' + item_unit_name +'</span></td>';

            tr_html += '<td class="text-center"><i class="fa fa-times tip rvdel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#rvTable");
            count += parseFloat(item_qty);
            count_cost += parseFloat(old_cost);

            an++;
			if(count)
			{
				//$('#rvwarehouse').select2('readonly', true);
			}


        });

        //var col = 1;
        //var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatDecimal((count) - 1,4) + '</th><th class="text-center">' + formatDecimal((count_cost) - 1,4) + '</th><th class="text-center">' + formatDecimal((count_value) - 1,4) + '</th><th></th>';

        //tfoot += '<th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        //$('#rvTable tfoot').html(tfoot);
        //$('select.select').select2({minimumResultsForSearch: 7});
        //if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
        //    $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
        //    $(window).scrollTop($(window).scrollTop() + 1);
        //}
        //set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
function add_revaluation_item(item) {

	if($('#rvwarehouse').val()){
	 //$('#tax_method_type').select2('readonly', true);
	 }else{
	        bootbox.alert(lang.Select_warehouse);
            item = null;
            return;
       }


    if (count == 1) {
        rvitems = {};
    }
    if (item == null)
        return;

    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;

    if (rvitems[item_id]) {

        var new_qty = parseFloat(rvitems[item_id].row.qty) + 1;

        try{
        rvitems[item_id].row.base_quantity = new_qty;

		if(rvitems[item_id].row.unit != rvitems[item_id].row.base_unit) {
            $.each(rvitems[item_id].units, function(){
                if (this.id == rvitems[item_id].row.unit) {
                    rvitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }

        }catch(err){
		//alert(err);
		}
		rvitems[item_id].row.qty = new_qty;


    } else {
        rvitems[item_id] = item;
    }
    rvitems[item_id].order = new Date().getTime();
    localStorage.setItem('rvitems', JSON.stringify(rvitems));
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
