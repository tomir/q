$(document).ready(function(){

	$("#nicemenu img.arrow").click(function(){ 
								
		$("span.head_menu").removeClass('active');
		
		submenu = $(this).parent().parent().find("div.sub_menu");
		
		if(submenu.css('display')=="block"){
			$(this).parent().removeClass("active"); 	
			submenu.hide(); 		
			$(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow_hover.png');
		}else{
			$(this).parent().addClass("active"); 	
			submenu.fadeIn(); 		
			$(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow_select.png');
		}
		
		$("div.sub_menu:visible").not(submenu).hide();
		$("#nicemenu img.arrow").not(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow.png');
						
	})
	.mouseover(function(){ $(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow_hover.png'); })
	.mouseout(function(){ 
		if($(this).parent().parent().find("div.sub_menu").css('display')!="block"){
			$(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow.png');
		}else{
			$(this).attr('src','http://gfx.tvs.pl/sklep/images/arrow_select.png');
		}
	});

	$("#nicemenu span.head_menu").mouseover(function(){ $(this).addClass('over')})
								 .mouseout(function(){ $(this).removeClass('over') });
	
	$("#nicemenu div.sub_menu").mouseover(function(){ $(this).fadeIn(); })
							   .blur(function(){ 
							   		$(this).hide();
									$("span.head_menu").removeClass('active');
								});		
								
	$(document).click(function(event){ 		
			var target = $(event.target);
			if (target.parents("#nicemenu").length == 0) {				
				$("#nicemenu span.head_menu").removeClass('active');
				$("#nicemenu div.sub_menu").hide();
				$("#nicemenu img.arrow").attr('src','http://gfx.tvs.pl/sklep/images/arrow.png');
			}
	});

	$.extend(DateInput.DEFAULT_OPTS, {
		stringToDate: function(string) {
			var matches;
			if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
			  return new Date(matches[1], matches[2] - 1, matches[3]);
			} else {
			  return null;
			}
		 },

		dateToString: function(date) {
			var month = (date.getMonth() + 1).toString();
			var dom = date.getDate().toString();
			if (month.length == 1) month = "0" + month;
			if (dom.length == 1) dom = "0" + dom;
			return date.getFullYear() + "-" + month + "-" + dom;
		}
	});
	$('input.date_picker').date_input();
							   
								   
});