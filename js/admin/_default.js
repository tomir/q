$(document).ready(function() {

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs_new li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs_new li").click(function() {
		$("ul.tabs_new li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).show(); //Fade in the active content
		return false;
	});
	
	$('#search_customer').click(function(){
		var customer = $('#customer_value').val();
	
		$.ajax({
			type: "POST",
			url:  "/admin/index_ajax.php?action=ajax_searchCustomer",
			data: "customer="+customer,
			beforeSend: function() {
				$("#customer_results").html('<img src="/images/5.gif" style="margin:0; paddin: 0; border:0;" />');
			},
			success: function(data){
				$("#customer_results").html(data);
				
			}
		});
		return false;
	});
	
	$('a[rel="select_customer"]').live("click", function () {
		
		var customer_id = $(this).attr("id");
	
		$.ajax({
			type: "POST",
			url:  "/admin/index_ajax.php?action=ajax_getUser",
			data: "id_user="+customer_id,
			beforeSend: function() {
				$("#customer_results").html('<img src="/images/5.gif" style="margin:0; paddin: 0; border:0;" />');
			},
			success: function(data){
				
				
				$("#customer_results").html("")
				var x = eval('(' + data + ')');
				
				$("#user_id").val(x.ID);
				$("#o_customer_name").val(x.FirstName);
				$("#o_customer_surname").val(x.LastName);
				$("#o_adress1").val(x.Street);
				$("#o_hause_number").val(x.Adress);
				$("#o_room_number").val(x.Adress2);
				$("#o_city").val(x.City);
				$('#o_country').selectmenu('value', x.IdCountry-1); 
				$("#o_zip").val(x.Zip);
				$("#o_email").val(x.Email);
				$("#o_phone").val(x.Phone);
				
			}
		});
		return true;
		

	})
});