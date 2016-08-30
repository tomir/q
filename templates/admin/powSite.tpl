<script type="text/javascript" src="../js/jquery-ui-1.7.2.custom.min.js" ></script>
{literal}
<script type="text/javascript">
	$(document).ready( function() {
		$('A[rel="delete_record"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć rekord i wszystkie możliwe powiązania?");
    		if (answer == 1) {
    			var url;
				var id = $(this).attr('id');
				url = "{/literal}{$wwwPatch}admin/{$menu_url}{literal},delete," + id + ".html";
				window.location = url; // pozostale
	        	href_remove.href = url; // explorer
			}
			return false;
		});
	});
	{/literal}
</script>	
<div class="box">
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		 Dodane rekordy  		
	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
	{section name="id" loop=$aWyniki}
		<div style="margin-left: 20px; width: auto; float: left;">
		{section name="id2" loop=$aColumns}
			{assign var="column" value=$aColumns[id2].column_s}
			{if $aColumns[id2].typ_pola == 'file'}
				{if $aColumns[id2].image_main_roz == 'jpg'}
					<div style="padding: 2px; border: 1px solid #DFDAD1; background: url({$aColumns[id2].file_miejsce_www}{$aWyniki[id].$idColumn}_thumb.{$aColumns[id2].image_main_roz}); width: {$aColumns[id2].thumb_scalex}px;height: {if $aColumns[id2].thumb_scaley == 0}80{else}{$aColumns[id2].thumb_scaley}{/if}px; background-repeat: no-repeat; "><a href="javascript:void(0);" id="{$aWyniki[id].$idColumn}" rel="delete_record" title="Usuń rekord"><img src="/img/admin/icons/delete.png" alt="" style="border: 0;"/></a></div>
				{else}
					<div id="player_{$aWyniki[id].$idColumn}"></div><div style="font-size: 12px;float: left;padding-left: 3px; border: 1px solid #AFAFAF; padding-top: 3px;"><a href="javascript:void(0);" id="{$aWyniki[id].$idColumn}" rel="delete_record" title="Usuń rekord"><img src="/img/admin/icons/delete.gif" alt="" style="border: 0;"/> usuń</a></div><div style="margin-left: 5px;font-size: 12px;float: left;padding-left: 3px; border: 1px solid #AFAFAF;"><a href="{$wwwPatch}admin/{$menu_url},edit,{$id_form},{$aWyniki[id].$idColumn}.html" title="Edytuj rekord"><img style="border: 0pt none ;" alt="Edytuj" src="/img/admin/edit.jpg"/> edytuj</a></div>
					<script type="text/javascript">
						var so = new SWFObject('/js/player-licensed.swf','mpl','190','140','9');
						so.addParam('allowscriptaccess','always');
						so.addParam('allowfullscreen','true');
						so.addParam('wmode','transparent');
						so.addParam('flashvars','&amp;file={$aColumns[id2].file_miejsce_www}{$aWyniki[id].$idColumn}.{$aWyniki[id].$column}&amp;autostart=false&amp;volume=50&amp;image=/images/zaslepka_video.jpg');
						so.write('player_{$aWyniki[id].$idColumn}');
					</script>
				{/if}
			{elseif $aColumns[id2].typ_pola == 'text'}
				<div style="font-size: 12px; float: left; width: 190px;">{$aWyniki[id].$column}</div>
			{/if}
		{/section}
		</div>
	{sectionelse}
		<p style="padding-left: 20px; margin-top: 5px;"><b>Brak rekordów</b></p>
	{/section}
	<br style="clear: both;" />
	</div>	
</div>