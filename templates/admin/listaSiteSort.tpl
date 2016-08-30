<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
{literal}
<script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	} 

	$(document).ready( function() {
		$('A[rel="delete_record"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć rekord i wszystkie możliwe powiązania?");
    		if (answer == 1) {
    			var url;
				var id = $(this).attr('id');
				url = "{/literal}{$wwwPanelPatch}{$menu_url}{literal},delete," + id + ".html";
				window.location = url; // pozostale
	        	href_remove.href = url; // explorer
			}
			return false;
		});
		
		$('A[rel="show_record"]').click(function() {
			var id = $(this).attr('id');
			var this_a = $(this);
			
			$.ajax({  
			  type: "GET",  
			  url: "{/literal}{$wwwPanelPatch}{$menu_url}{literal},change,"+id+".html",   

			  success: function(transport) {
					this_a.find("img").attr({src: transport});
				}
			})
		});

		$("#test-list").sortable({
      		handle : '.handle',
      		update : function () {
				var order = $('#test-list').sortable('toArray');
				var dataString = "order="+order;
				
				$.ajax({  
			  		type: "POST",
			  		data: dataString,
					url: "{/literal}{$wwwPanelPatch}{$menu_url}{literal},sort.html"
				});
      		}
    	});
		$("#test-list").disableSelection();

	});
	{/literal}
</script>	
<div style="margin: 0 auto;">
	{if $filtr.table != ''}
	<span style="font-weight: bold;font-size: 12px;">Filtruj według:</span> <select onchange="MM_jumpMenu('parent',this,0)" id="filtr" name="filtr" style="margin-bottom: 5px;">
   		<option value="{$wwwPatchPanel}{$menu_url}.html"></option>
   		{section name=id loop=$aPow}
			<option {if $aPow[id].$filtr.value == $filtr.value}selected="selected"{/if} value="{$wwwPatchPanel}{$menu_url},{$aPow[id].$filtr.value}.html">{$aPow[id].$filtr.name}</option>
   		{/section}
   	</select>
   	{/if}
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;">
   			<a title="{$button}" href="{$wwwPatchPanel}{$menu_url},add.html"><img style="border: 1px solid #E0E0E0;" alt="" src="../images/admin/icons/add.gif"/></a>
   		</div>
   		<div style="float: left;padding-left: 10px; margin-top:1px;"><a style="color: #E0E0E0; text-decoration: none;" title="{$button}" href="{$wwwPatchPanel}{$menu_url},add.html">{$button}</a></div>
   		<br style="clear: both;" />
   	</h4>
	<table class="table-long" style="background-color: #fff;">
		<thead>
			<tr>
				{section name=id2 loop=$aColumns}
					{assign var="ile" value=$smarty.section.id2.total}
					<td class="header" style="{if $aColumns[id2].wysrodkowana}text-align: center;{/if}width: {math equation="x / y" x=700 y=$ile format="%.2f"}px;{$aColumns[id2].styl}">{$width}{$aColumns[id2].nazwa_pola}</td>
				{/section}
					{if $visibleColumn}<td class="header" style="text-align: center; width: 80px;">Widoczny</td>{/if}
					<td class="header" style="text-align: center; width: 80px;">Akcje</td>
			</tr>
		</thead>
	</table>
	<ul id="test-list" style="list-style: none; padding-left: 0;">
		{section loop=$aResult name=id}
			
			<li id="{$aResult[id].$idColumn}">
				<table class="handle table-long" style="cursor: move; background-color: #fff;">
					<tbody>
						<tr>
							{section loop=$aColumns name=id2}	
								{assign var=column value=$aColumns[id2].column_s}
								<td style="{if $aColumns[id2].wysrodkowana}text-align: center; {/if}width: {math equation="x / y" x=700 y=$ile format="%.2f"}px; vertical-align: middle; font-size: 12px; padding: 5px 8px;">
									{if $aColumns[id2].powiazana_tabela != ''}
										{if $aResult[id].powColumn_rec[0] == ''}<span style="color: #9C938B;">brak powiązania</span>{else}{$aResult[id].powColumn_rec[0]}{/if}
									{else}{if $aColumns[id2].typ_pola == 'file'}
											{if $aColumns[id2].file_rozszerzenia == '*.jpg'}
												<img src="{$aColumns[id2].file_miejsce_www}{$aResult[id].$idColumn}_thumb.{$aColumns[id2].image_main_roz}" alt="" style="padding: 2px; border: 1px solid #E6EEEE;" />
											{else}{if $aColumns[id2].file_rozszerzenia == '*.mp3'}
												<object width="17" height="17" data="../js/musicplayer.swf?song_url={$aColumns[id2].file_miejsce_www}{$aResult[id].$idColumn}.mp3" type="application/x-shockwave-flash">
													<param value="../js/musicplayer.swf?song_url={$aColumns[id2].file_miejsce_www}{$aResult[id].$idColumn}.mp3" name="movie">
													<img width="17" height="17" alt="" src="noflash.gif">
												</object>
											{/if}{/if}
										  {else}{$aResult[id].$column|stripslashes}
										  {/if}
									{/if}
								</td>
							{/section}
							{if $visibleColumn}<td style="text-align: center; width: 80px;"><a style="display: block; height: 25px;" rel="show_record" href="javascript:void(0);" title="Zmień status" id="{$aResult[id].$idColumn}" ><img style="padding: 3px;border: 0;" src="../images/admin/icons/main_{if $aResult[id].$visibleColumn}on{else}off{/if}.jpg" alt="Zmień status" /></a></td>{/if}
							<td style="text-align: center; width: 80px;">
								<a href="{$wwwPatchPanel}{$menu_url},edit,{$idForm},{$aResult[id].$idColumn}.html" title="Edytuj"><img src="../images/admin/icons/edit.jpg" alt="Edytuj" style="border: 0;" /></a>
								{section loop=$dodActions name=id3}
									<a href="{if $dodActions[id3].pow_link}{$dodActions[id3].pow_link}{else}{$wwwPatchPanel}{$dodActions[id3].nazwa_url},add,{$dodActions[id3].pow_form},{$aResult[id].$idColumn}{/if}.html" title="{$dodActions[id3].nazwa_formularza}"><img src="../images/admin/icons/{$dodActions[id3].image}" alt="{$dodActions[id3].nazwa_formularza}" style="border: 0;" /></a>
								{/section}
								<a href="javascript:void(0);" rel="delete_record" id="{$aResult[id].$idColumn}" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
							</td>
						</tr>
					</tbody>
				</table>
				
			</li>
		{sectionelse}
			<table class="table-long">
				<tr class="odd"><td>Brak rekordów w bazie</td></tr>
			</table>
		{/section}
	</ul>
	<div style="color: #23AAAA; font-size: 12px; margin-top: 10px;">Aby zmienić kolejność "złap" za element i przeciągnij go w dół lub górę</div>
</div>