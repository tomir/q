<script type="text/javascript">
	{literal}
	$(document).ready( function() {
		$('A[rel="delete_form"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć ten formularz?");
    		if (answer == 1) {
    			var url;
				var id = $(this).find('img').attr('id');
				url = "{/literal}{$tffPatch}{literal}usun," + id + ".html";
				window.location = url; // pozostale
	        	href_remove.href = url; // explorer
			}
			return false;
		});
		
		$('A[rel="details_form"]').click(function() {
			var id = $(this).attr('id');
			var tr = $(this).closest("tr");
			var this_a = $(this);
			var arrive = $("#arrive"+id).attr('id');

			if(!arrive) {
				$.ajax({  
				  type: "POST",  
				  url: "{/literal}{$tffPatch}{literal}pobierzFormDetailsJS,"+id+".html",   
	
				  success: function(transport) {
						tr.after(transport);
						this_a.html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Schowaj formularz">schowaj</a>');
						
					}
				})
			} else {
				tr.find('A[rel="details_form"]').html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Rozwiń formularz">rozwiń</a>');
				$("#arrive"+id).remove();
			}
		
		});
		
		$('A[rel="hide_form"]').click(function() {
			tr.find('A[rel="hide_form"]').html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Rozwiń formularz">rozwiń</a>');
			$("#arrive").remove();
		
		});
	});
	
	$(document).hover( function() {
		$('A[rel="edit_form"]').click(function() {
			var id = $(this).find('img').attr('id');
			var tr = $(this).closest("tr");
			$.ajax({  
			  type: "POST",  
			  url: "{/literal}{$tffPatch}{literal}pobierzFormEditJS,"+id+".html",   
			  beforeSend: function() {
					tr.html('<td colspan="8"><img alt="" style="padding-left: 5px; margin-top: 5px;" src="http://gfx.tvs.pl/img/35.gif" /></td>');
					$(document).find('A[rel="edit_form"]').css({'display' : 'none'});
				},
			  success: function(transport) {
					tr.html(transport);	
				}
			})
		
		});
		
		$("#zapisz_form").click(function() {
			var tr = $(this).closest("tr");
			var id = $("#edited_formid").val();
			var name = $("#edited_name").val();
			var stan = 0;
			if($("#edited_active:checked").val()) {
				stan = $("#edited_active:checked").val();
			}

			var dataString = "name="+name+"&stan="+stan; 
			
			$.ajax({  
			  type: "POST",  
			  url: "{/literal}{$tffPatch}{literal}zapiszFormEditJS,"+id+".html",   
			  data: dataString,
			  beforeSend: function() {
					tr.html('<td colspan="8"><img alt="" style="padding-left: 5px; margin-top: 5px;" src="http://gfx.tvs.pl/img/35.gif" /></td>');
				},
			  success: function(transport) {
					tr.html(transport);
					$(document).find('A[rel="edit_form"]').css({'display' : 'inline'});
				}
			})
		
		});

	});
	
	
	{/literal}
</script>
<div style="margin: 0 auto; width: 990px;">    	
    {if $aForms}
    <h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;">
   			<a title="{$button}" href="{$tffPatch}stworz.html"><img style="border: 1px solid #E0E0E0;" alt="" src="../images/admin/icons/add.gif"/></a>
   		</div>
   		<div style="float: left;padding-left: 10px; margin-top:1px;"><a style="color: #E0E0E0; text-decoration: none;" title="dodaj nowy formularz" href="{$tffPatch}stworz.html">dodaj nowy formularz</a></div>
   		<br style="clear: both;" />
   	</h4>
    <div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long" style="width: 990px;">
			<thead>
				<tr>
					<td class="header">Id</td>
					<td class="header">Nazwa formularza</td>
					<td class="header">Nazwa tabeli</td>
					<td class="header">Aktywny</td>
					<td class="header" style="text-align: center; width: 170px;">Data stworzenia</td>
					<td class="header" style="text-align: center; width: 170px;">Ostatnia modyfikacja</td>
					<td class="header" style="text-align: center; width: 90px;">Akcje</td>
					<td class="header">Rozwiń</td>
				</tr>
			</thead>
   		{section name=id loop=$aForms}
   			<tbody>
				<tr class="{cycle values="odd,"}">
	   				<td>{$aForms[id].id_formularza}</td>
	   				<td>{$aForms[id].nazwa_formularza}</td>
	   				<td>{$aForms[id].nazwa_tabeli}</td>
	   				<td style="text-align: center;">{if $aForms[id].active}tak{else}nie{/if}</td>
	   				<td style="text-align: center;">{$aForms[id].data_stworzenia}</td>
	   				<td style="text-align: center;">{$aForms[id].data_modyfikacji}</td>
	   				<td style="text-align: center; width: 90px;">
	   					<a href="{$wwwPatch}podglad,{$aForms[id].id_formularza},html" title="Podgląd"><img src="{$wwwPatch}images/admin/icons/lupa.gif" alt="Pogląd formularza" style="border: 0;" /></a>
	   					<a href="javascript:void(0);" rel="edit_form" title="Edytuj"><img src="{$wwwPatch}images/admin/icons/edit.jpg" id="{$aForms[id].id_formularza}" alt="Edytuj" style="border: 0;" /></a>
						<a href="javascript:void(0);" rel="delete_form" title="Usuń"><img src="{$wwwPatch}images/admin/icons/delete.gif" id="{$aForms[id].id_formularza}" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					</td>
	   				<td style="text-align: center;"><a href="javascript:void(0);" rel="details_form" id="{$aForms[id].id_formularza}" title="Rozwiń formularz">rozwiń</a></td>
   				</tr>
   			</tbody>
   		{/section}
   		</table>
   	</div>
   	{/if}
</div>
