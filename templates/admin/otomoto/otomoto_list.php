<style type="text/css">
	.ui-state-highlight { height: 92px; width: 122px;}
	
	input.ui-state-default {
		background: url("../images/admin/button_highlight.png") repeat-x scroll 0 0 #6D859C;
		border-color: #8E9CAA #66798C #5A6E83 #7B8C9C;
		border-style: solid;
		border-width: 1px;
		color: #FFFFFF;
	}
	
	input.ui-state-hover {
		background: url("../images/admin/button_highlight_selected.png") repeat-x scroll 0 0 #69909E;
		border-color: #8CA2AB #537481 #456977 #527480;
		border-style: solid;
		border-width: 1px;
		color: #FFFFFF;
	}
	
</style>
<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
<script type="text/javascript">

	$(document).ready( function() {
		$("input:submit, input:reset, button").button();
		
		$('A[rel="delete_record"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć rekord i wszystkie możliwe powiązania?");
    		if (answer == 1) {
    			var url;
				var id = $(this).attr('id');
				url = "<?php echo $wwwPanelPatch; ?>otomoto,delete," + id + "<?php if($atr2 > 1) echo ','.$atr2; ?>.html";
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
			  url: "<?php echo $wwwPanelPatch; ?>otomoto,activate,"+id+".html",

			  success: function(transport) {
					this_a.find("img").attr({src: transport});
				}
			})
		});
		
		$('#items_all').click(function() {
			$('.items').attr('checked', this.checked);
		});


	});
</script>	
<div style="margin: 0 auto;">
   	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;padding-left: 10px; margin-top:1px;">Lista aukcji</div>
   		<br style="clear: both;" />
   	</h4>
	<form name="" id="form22" action="/admin/otomoto,wystaw_grupowo.html" method="post">
		<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px; padding-bottom: 0;">
			<table class="table-long">
				<thead>
					<tr>
						<td class="header"><?php if(strlen($content[0]['otomoto-id']) < 3 && $content[0]['date_to_send'] == '') { ?><input type="checkbox" checked="checked" id="items_all" name="items_all" value="1" /><?php } ?></td>
						<td class="header" colspan="2">Nazwa aukcji</td>
						<td class="header">Rocznik</td>
						<td class="header">Ilość odw.</td>
						<td class="header">Kwota</td>
						<td class="header">Data zak.</td>
						<td class="header" style="text-align: center;">Aktywna</td>
						<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
					</tr>
				</thead>
				<?php if(is_array($content) && count($content) > 0) { ?>
				<tbody>
					<?php $class = 'odd'; foreach($content as $row) { ?>
						<tr class="<?php echo $class; ?>">
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;"><?php if(strlen($row['otomoto-id']) < 3) { ?><input type="checkbox" class="items" checked="checked" name="item[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>" /><?php } ?></td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php if(is_array($row['photos']) && count($row['photos']) > 0) { ?>
								<img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="/pliki/cars/<?php echo $row['car_id'].'/'.$row['photos'][0]['photo_id'].'_thumb.jpg'; ?>" />
								<?php } ?>
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php echo $row['producer_name']." ".$row['model_name']; ?><?php if(strlen($row['otomoto-id']) < 3) { if($row['date_to_send'] != '') { ?><br /><span style="color: #629B4F; line-height: 16px; font-size: 11px"><strong>(wystawienie zaplanowane)</strong><br /><?php echo $row['date_to_send']; ?><br />(+15 min)</span><?php } else { 
									
								if(is_array($row['errors']) && !empty($row['errors'])) { echo '<br /><span style="color: orange; line-height: 16px; font-size: 12px"><Strong>Wystąpiły błędy</strong></span>'; foreach($row['errors'] as $error) { echo '<br /><span style="color: orange; line-height: 16px; font-size: 11px">'.$error['error'].'</span>'; } } else { ?><br /><span style="color: red; font-size: 10px">(auckja niewystawiona)</span><?php } } } ?>
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php echo $row['build-year']; ?>	
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php echo $row['ogladalnosc']; ?>	
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php echo $row['price']; ?> PLN		
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
								<?php echo $row['sale_date']; ?>		
							</td>
							<td style="vertical-align: middle; text-align: center; width: 55px;"><?php if(strlen($row['otomoto-id']) > 3) { ?><a rel="show_record" href="javascript:void(0);" title="Zmień status" id="<?php echo $row['id']; ?>" ><img style="border: 0;" src="../images/admin/icons/main_<?php if($row['active'] == 1) echo 'on'; else echo 'off'; ?>.png" alt="Zmień status" /></a><?php } ?></td>
							<td style="vertical-align: middle; text-align: center; width: 70px;">
								<a href="<?php echo $wwwPatchPanel.'otomoto,edit,'.$row['id'].','.$row['car_id']; ?>.html" title="Edytuj"><img src="../images/admin/icons/edit.png" alt="Edytuj" style="border: 0;" /></a>
								<a href="javascript:void(0);" rel="delete_record" id="<?php echo $row['id']; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
							</td>
						</tr>
					<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
						<? if($atr2 == 'do_wystawienia') { ?>
						<tr style="background: #E8E8DD;">
							<td colspan="9" style="padding-left: 0;">
								<input style="cursor: pointer; padding: 5px 12px; margin: 4px; margin-left: 0;" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" name="zapisz" value="Wystaw aukcje" style="margin:5px"/>
								<input style="cursor: pointer; padding: 5px 12px; margin: 4px; margin-left: 0;" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" onclick="$('#form22').attr('action', '/admin/otomoto,delete_grupowo.html'); $('#form22').submit(); return false;" name="usun" value="Usuń" style="margin:5px"/>
							
							</td>
						</tr>
						<?php } ?>
				</tbody>
				<?php } else { ?>
				<tbody>
					<tr class="odd"><td colspan="7" style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">Brak rekordów w bazie</td></tr>
				</tbody>
				<?php } ?>
			</table>

			<?php if($show_pages > 1) {
				$pages_limit = $show_page + 5;
				$start_limit = $show_page - 5;
			?>

			<div class="pagination right">
				<a href="<?php echo $wwwPatchPanel.'otomoto,'.($show_page-1); ?>.html"<?php if($show_page == 1) { ?> style="Visibility:Hidden;"<?php } ?>>&laquo;</a>
				<?php
				if($pages_limit > $show_pages)
					$pages_limit = $show_pages+1;
				if($start_limit <= 0)
					$start_limit = 1;

			for($i = $start_limit;$i<$pages_limit; $i++) { ?>
				<?php echo '<a '; if($show_page == $i) { echo ' class="active" '; } echo 'href="'.$wwwPatchPanel.'otomoto,'.$i.'.html">'.$i.'</a></td>'; ?>
			<?php } ?>
			<?php if($show_page != $show_pages) { ?><a href="<?php echo $wwwPatchPanel.'otomoto,'.($show_page+1); ?>.html">&raquo;</a><?php } ?>
			</div>
			<div style="clear:both;"></div>
			<?php } ?>
		</div>
	</form>
	
	
</div>