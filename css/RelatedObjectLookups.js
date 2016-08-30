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
			 url: '/sklep/admin/addOrder_ajax',
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
						url: '/sklep/admin/addOrderItem_ajax',
						success: function() {
							$("#empty_box").html('<table style="width:100%;"><tr><td style="width: 255px; font-size: 12px;"><b>Tytuł</b></td><td style="width: 71px; font-size: 12px;"><b>Ilość</b></td><td style="width: 71px; font-size: 12px;"><b>Cena</b></td></tr><tr><td style="width: 255px; font-size: 12px;">' + val2 + '</td><td style="width: 71px; font-size: 12px;">' + items + '</td><td style="width: 71px; font-size: 12px;">' + val3 + ' PLN</td></tr></table>');
							$("#empty_box").attr("id", "");
						}
					});
				}
				else {

					$.ajax({
						data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
						type: "POST",
						url: '/sklep/admin/addOrderItem_ajax',
						success: function(data) {
							$("#main_table").append('<tr class=""><td></td><td style="font-size: 12px;"><table style="width: 100%;"><tr><td style="width: 255px; font-size: 12px;">' + val2 + '</td><td style="width: 71px; font-size: 12px;">' + items + '</td><td style="width: 71px; font-size: 12px;">' + val3 + ' PLN</td></tr></table></td></tr>');
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
				url: '/sklep/admin/addOrderItem_ajax',
				success: function() {
					$("#empty_box").html('<table style="width:100%;"><tr><td style="width: 255px; font-size: 12px;"><b>Tytuł</b></td><td style="width: 71px; font-size: 12px;"><b>Ilość</b></td><td style="width: 71px; font-size: 12px;"><b>Cena</b></td></tr><tr><td style="width: 255px; font-size: 12px;">' + val2 + '</td><td style="width: 71px; font-size: 12px;">' + items + '</td><td style="width: 71px; font-size: 12px;">' + val3 + ' PLN</td></tr></table>');
					$("#empty_box").attr("id", "");
				}
			});
		}
		else {

			$.ajax({
				data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
				type: "POST",
				url: '/sklep/admin/addOrderItem_ajax',
				success: function(data) {
					$("#main_table").append('<tr class=""><td></td><td style="font-size: 12px;"><table style="width: 100%;"><tr><td style="width: 255px; font-size: 12px;">' + val2 + '</td><td style="width: 71px; font-size: 12px;">' + items + '</td><td style="width: 71px; font-size: 12px;">' + val3 + ' PLN</td></tr></table></td></tr>');
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
