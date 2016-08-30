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
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<thead>
				<tr>
				{section name=id2 loop=$aColumns}
					<td style="font-size: 12px; padding: 5px 8px; {if $aColumns[id2].wysrodkowana}text-align: center;{/if}" class="header">{$aColumns[id2].nazwa_pola}</td>
				{/section}
					{if $visibleColumn}<td class="header" style="font-size: 12px; text-align: center;">Widoczny</td>{/if}
					<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
				</tr>
			</thead>
			{section loop=$aResult name=id max=10 start=$start}
			<tbody>
				<tr class="{cycle values="odd,"}">
					{section loop=$aColumns name=id2}	
						{assign var=column value=$aColumns[id2].column_s}
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; {if $aColumns[id2].wysrodkowana}text-align: center;{/if}">
							{if $aColumns[id2].powiazana_tabela != ''}
								{if $aResult[id].powColumn_rec == ''}<span style="color: #9C938B;">brak powiązania</span>{else}{$aResult[id].powColumn_rec[id2]}{/if}
							{else}{if $aColumns[id2].typ_pola == 'file'}
									{if $aResult[id].$column != 'jpg'}
									<script type='text/javascript' src='{$wwwPatch}images/mediaplayer-5.2/swfobject.js'></script>

									<div id='mediaspace{$aResult[id].$idColumn}'></div>

									<script type='text/javascript'>
									  var so = new SWFObject('{$wwwPatch}images/mediaplayer-5.2/player.swf','ply','210','120','9','#000000');
									  so.addParam('allowfullscreen','true');
									  so.addParam('allowscriptaccess','always');
									  so.addParam('wmode','opaque');
									  so.addVariable('file','{$wwwPatch}pliki/{$aResult[id].$idColumn}.{$aResult[id].video_filename}');
									  so.write('mediaspace{$aResult[id].$idColumn}');
									</script>
									{else}<img src="{$aColumns[id2].file_miejsce_www}{$aResult[id].$idColumn}_thumb.{$aResult[id].media_filename}" alt="" style="padding: 2px; border: 1px solid #E6EEEE;" />{/if}{else}{$aResult[id].$column|stripslashes|truncate:150}{/if}{/if}
						</td>
					{/section}
					{if $visibleColumn}<td style="text-align: center; width: 55px;"><a style="display: block; height: 25px;" rel="show_record" href="javascript:void(0);" title="Zmień status" id="{$aResult[id].$idColumn}" ><img style="padding: 3px;border: 0;" src="../images/admin/icons/main_{if $aResult[id].$visibleColumn}on{else}off{/if}.jpg" alt="Zmień status" /></a></td>{/if}
					<td style="text-align: center; width: 70px;">
						<a href="{$wwwPatchPanel}{$menu_url},edit,{$idForm},{$aResult[id].$idColumn}.html" title="Edytuj"><img src="../images/admin/icons/edit.jpg" alt="Edytuj" style="border: 0;" /></a>
						{section loop=$dodActions name=id3}
							<a href="{if $dodActions[id3].pow_link}{$dodActions[id3].pow_link}{else}{$wwwPatchPanel}{$dodActions[id3].nazwa_url},add,{$dodActions[id3].pow_form},{$aResult[id].$idColumn}{/if}.html" title="{$dodActions[id3].nazwa_formularza}"><img src="../images/admin/icons/{$dodActions[id3].image}" alt="{$dodActions[id3].nazwa_formularza}" style="border: 0;" /></a>
						{/section}
						<a href="javascript:void(0);" rel="delete_record" id="{$aResult[id].$idColumn}" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					</td>
				</tr>
			</tbody>
			{sectionelse}
			<tbody>
				<tr class="odd"><td colspan="7" style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">Brak rekordów w bazie</td></tr>
			</tbody>
			{/section}
		</table>
	</div>
	{section loop=$aResult name=id2 step=10}
		{* $strona_akt = $smarty.section.id2.index *}
		{if $smarty.section.id2.first}
			<table class="header" cellspacing="1" cellpadding="0" style="background-color: #fff; min-width: 60px; margin: 5px auto;">
				<tr><td style="border: 0px;"><span style="font-weight: bold;">strony: </span></td>
		{/if}
					<td style="background: #EFEFEF; padding: 0; text-align: center; border: 1px solid #CDCDCD;"><a href="{$wwwPatchPanel}{$menu_url},{$smarty.section.id2.index}.html" style="{if $strona == $smarty.section.id2.index}font-weight: bold; text-decoration: underline;{/if}display: block; width: 7px; height: 15px; padding: 4px;" title="Zobacz następne">{$smarty.section.id2.iteration}</a></td>
		{if $smarty.section.id2.last}
				</tr>
			</table>
		{/if}
	{/section}
</div>