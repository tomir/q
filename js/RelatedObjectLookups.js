// Handles related-objects functionality: lookup link for raw_id_fields
// and Add Another links.
var items;
var id_order;

function html_unescape(text) {
    // Unescape a string that was escaped using django.utils.html.escape.
    text = text.replace(/&lt;/g, '<');
    text = text.replace(/&gt;/g, '>');
    text = text.replace(/&quot;/g, '"');
    text = text.replace(/&#39;/g, "'");
    text = text.replace(/&amp;/g, '&');
    return text;
}

function showRelatedObjectLookupPopup(triggeringLink) {
    var name = triggeringLink.id.replace(/^lookup_/, '');
    // IE doesn't like periods in the window name, so convert temporarily.
    name = name.replace(/\./g, '___');
    var href;
    if (triggeringLink.href.search(/\?/) >= 0) {
        href = triggeringLink.href + '&pop=1';
    } else {
        href = triggeringLink.href + '?pop=1';
    }
    var win = window.open(href, name, 'height=500,width=800,resizable=yes,scrollbars=yes');
    win.focus();
    return false;
}

function dismissRelatedLookupPopup(win, val1, val2, val3, items) {
    var name = win.name.replace(/___/g, '.');
    

	if(document.getElementById('order_id').value == 0) {
		
		$.ajax({
			 url: '/admin/index_ajax.php?action=addOrder_ajax',
			 success: function(data_o) {
				$("#order_id").val(data_o);
				id_order = data_o;
				document.getElementById("zapisz").disabled=false;

			 },
			 complete: function() {
				 if($("#empty_box").html() == 'brak produktów') {
					 $.ajax({
						data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
						type: "POST",
						url: '/admin/index_ajax.php?action=addOrderItem_ajax',
						success: function() {
							$("#empty_box").html('<div class="table" style="padding-left: 0;"><table><thead><tr><th style="border-top: 1px solid #CDCDCD; border-left: 1px solid #CDCDCD;">Produkt</th><th style="border-top: 1px solid #CDCDCD;">Ilość</th><th style="border-top: 1px solid #CDCDCD;">Cena</th></tr></thead><tbody><tr><td class="price" style="background: #fff; border-left: 1px solid #CDCDCD;">' + val2 + '</td><td  class="price" style="background: #fff;">' + items + '</td><td class="price" style="background: #fff;">' + val3 + ' PLN</td></tr></tbody></table></div>');
						}
					});
				}
			}
		});

	} else {
		
		id_order = $("#order_id").val();
		
		if($("#empty_box").html() == 'brak produktów') {
			 $.ajax({
				data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
				type: "POST",
				url: '/admin/index_ajax.php?action=addOrderItem_ajax',
				success: function() {
					$("#empty_box").html('<div class="table" style="padding-left: 0;"><table><thead><tr><th style="border-top: 1px solid #CDCDCD; border-left: 1px solid #CDCDCD;">Produkt</th><th style="border-top: 1px solid #CDCDCD;">Ilość</th><th style="border-top: 1px solid #CDCDCD;">Cena</th></tr></thead><tbody><tr><td class="price" style="background: #fff; border-left: 1px solid #CDCDCD;">' + val2 + '</td><td  class="price" style="background: #fff; border-left: 1px solid #CDCDCD;">' + items + '</td><td class="price" style="background: #fff;">' + val3 + ' PLN</td></tr></tbody></table></div>');
				}
			});
		}
		else {

			$.ajax({
				data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
				type: "POST",
				url: '/admin/index_ajax.php?action=addOrderItem_ajax',
				success: function(data) {
					$("#empty_box > div > table > tbody").append('<tr><td class="price" style="background: #fff; border-left: 1px solid #CDCDCD;">' + val2 + '</td><td  class="price" style="background: #fff;">' + items + '</td><td class="price" style="background: #fff;">' + val3 + ' PLN</td></tr>');
				}
			});

		}


	}

    win.close();
}

function showAddAnotherPopup(triggeringLink) {
    var name = triggeringLink.id.replace(/^add_/, '');
    name = name.replace(/\./g, '___');
    href = triggeringLink.href
    if (href.indexOf('?') == -1) {
        href += '?_popup=1';
    } else {
        href  += '&_popup=1';
    }
    var win = window.open(href, name, 'height=500,width=800,resizable=yes,scrollbars=yes');
    win.focus();
    return false;
}

function dismissAddAnotherPopup(win, newId, newRepr) {
    // newId and newRepr are expected to have previously been escaped by
    // django.utils.html.escape.
    newId = html_unescape(newId);
    newRepr = html_unescape(newRepr);
    var name = win.name.replace(/___/g, '.');
    var elem = document.getElementById(name);
    if (elem) {
        if (elem.nodeName == 'SELECT') {
            var o = new Option(newRepr, newId);
            elem.options[elem.options.length] = o;
            o.selected = true;
        } else if (elem.nodeName == 'INPUT') {
            elem.value = newId;
        }
    } else {
        var toId = name + "_to";
        elem = document.getElementById(toId);
        var o = new Option(newRepr, newId);
        SelectBox.add_to_cache(toId, o);
        SelectBox.redisplay(toId);
    }
    win.close();
}
