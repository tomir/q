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
		
    id_order = $("#order_id").val();
    $.ajax({
	    data: 'id_order=' + id_order +'&id_item=' + val1 + '&items=' + items,
	    type: "POST",
	    url: '/sklep/admin/addOrderItem_ajax_edit',
	    success: function(data) {
		    var myMsg = eval(data);
		    $("#pod_list").before('<tr class="order-list" id="order_item_'+myMsg[0]['p_id']+'"><td><input type="checkbox" name="product[]" value="'+myMsg[0]['p_id']+'" /></td><td><a title="" href="http://www.tvs.pl/sklep/produkt/przykladowy,'+myMsg[0]['p_id']+'.html"><img width="70px" alt="" title="" src="http://gfx.tvs.pl/sklep/okladki/make_image.php?id='+myMsg[0]['m_i']+'&action=thumb_order"/></a></td><td><p><a title="" href="http://www.tvs.pl/sklep/produkt/przykladowy,'+myMsg[0]['p_id']+'.html"><b class="nameme">'+myMsg[0]['p_name']+'</b></a><br/><b>'+myMsg[0]['author_name']+'</b></p></td><td><span><span id="price_'+myMsg[0]['i_id']+'">'+myMsg[0]['i_price']+'</span> PLN <a href="javascript:void(0)" rel="edit_price" id="'+myMsg[0]['i_id']+'"><img src="http://gfx.tvs.pl/sklep/images/edit_icon.png" /></a> </span></td><td class="cou"><p class="count"><span id="piece_'+myMsg[0]['i_id']+'">'+myMsg[0]['i_pieces']+'</span> <a href="javascript:void(0)" rel="edit_pieces" id="'+myMsg[0]['i_id']+'"><img src="http://gfx.tvs.pl/sklep/images/edit_icon.png" /></a></p></td><td class="sum"><span><span id="isum_'+myMsg[0]['i_id']+'">'+myMsg[0]['i_sum']+'</span> PLN</span></td></tr>');

		    $.get('http://www.tvs.pl/sklep/admin/index_ajax.php?action=calc_order&id='+myMsg[0]['id_order'], function(data2) { $("#osum_"+myMsg[0]['id_order']).html(data2); });
		    $.get('http://www.tvs.pl/sklep/admin/index_ajax.php?action=calc_order&id='+myMsg[0]['id_order']+'&fee=1', function(data3) { $("#psum_"+myMsg[0]['id_order']).html(data3); });
	    }
    });

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
