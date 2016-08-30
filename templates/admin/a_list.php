<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
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
				url = "<?php echo $wwwPanelPatch.$menu_url; ?>,delete," + id + ".html";
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
			  url: "<?php echo $wwwPanelPatch.$menu_url; ?>,change,"+id+".html",

			  success: function(transport) {
					this_a.find("img").attr({src: transport});
				}
			})
		});


	});
</script>	
<div style="margin: 0 auto;">
	<?php if($filtr['table'] != '') { ?>
	<span style="font-weight: bold;font-size: 12px;">Filtruj według:</span> <select onchange="MM_jumpMenu('parent',this,0)" id="filtr" name="filtr" style="margin-bottom: 5px;">
   		<option value="<?php echo $wwwPatchPanel.$menu_url; ?>.html"></option>
		<?php foreach($aPow as $row_p) {  ?>
			<option <?php if($row_p[$filtr]['value'] == $filtr['value']) echo 'selected="selected"'; ?> value="<?php echo $wwwPatchPanel.$menu_url.','.$row_p[$filtr]['value']; ?>.html"><?php echo $row_p[$filtr]['name']; ?></option>
   		<?php } ?>
   	</select>
   	<?php } ?>
   	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;">
   			<a title="<?php echo $button; ?>" href="<?php echo $wwwPatchPanel.$menu_url; ?>,add.html"><img style="border: 1px solid #E0E0E0;" alt="" src="../images/admin/icons/add.gif"/></a>
   		</div>
   		<div style="float: left;padding-left: 10px; margin-top:1px;"><a style="color: #E0E0E0; text-decoration: none;" title="<?php echo $button; ?>" href="<?php echo $wwwPatchPanel.$menu_url; ?>,add.html"><?php echo $button; ?></a></div>
   		<br style="clear: both;" />
   	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<thead>
				<tr>
					<?php if($menu_url == "pojazdy") { ?>
						<td class="header" style="font-size: 12px;">Zdjęcie</td>
					<?php } ?>
				<?php foreach($aColumns as $row_c) { ?>
					<td style="<?php if($row_c['wysrodkowana']) { ?>text-align: center;<?php } ?>" class="header"><?php echo $row_c['nazwa_pola']; ?></td>
				<?php } ?>
					<?php if($visibleColumn) { ?><td class="header" style="text-align: center;">Widoczny</td><?php } ?>
					<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
				</tr>
			</thead>
			<?php if(is_array($aResult) && count($aResult) > 0) { ?>
			<?php $class = 'odd'; foreach($aResult as $row) { $kk=0;?>
			<tbody>
				<tr class="<?php echo $class; ?>">
					<?php if($menu_url == "pojazdy") { ?>
						<td style="vertical-align: middle;"><?php if(is_array($row['photos'])) { ?><img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="/pliki/cars/<?php echo $row['photos'][0]['car_id'].'/'.$row['photos'][0]['photo_id'].'_thumb.jpg'; ?>" /><?php } ?></td>
					<?php } ?>
					<?php $i = 0; foreach($aColumns as $row_c) { ?>
						<?php $column = $row_c['column_s']; ?>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; <?php if($row_c['wysrodkowana'] == 1) { ?>text-align: center;<?php } ?>">
							<?php if($row_c['powiazana_tabela'] != '') { ?>
								<?php if($row['powColumn_rec'][$kk] == '') { ?><span style="color: #9C938B;">brak powiązania</span><?php } else { echo $row['powColumn_rec'][$kk]; } ?>
									<?php $kk++;} else { if($row_c['typ_pola'] == 'file') { ?>
									<?php if(substr($row[$column], -3, 3) == 'mov' || substr($row[$column], -3, 3) == 'mp4' || substr($row[$column], -3, 3) == 'flv') { ?>
									<script type='text/javascript' src='<?php echo $wwwPatch; ?>images/mediaplayer-5.2/swfobject.js'></script>

									<div id='mediaspace<?php echo $row[$idColumn]; ?>'></div>

									<script type='text/javascript'>
									  var so = new SWFObject('<?php echo $wwwPatch; ?>images/mediaplayer-5.2/player.swf','ply','210','120','9','#000000');
									  so.addParam('allowfullscreen','true');
									  so.addParam('allowscriptaccess','always');
									  so.addParam('wmode','opaque');
									  so.addVariable('file','<?php echo $row_c['file_miejsce_www'] ?><?php echo $row['video_filename']; ?>');
									  so.write('mediaspace<?php echo $row[$idColumn]; ?>');
									</script>
									<?php } elseif($row[$column] == 'mp3') { ?>
										<object width="17" height="17" data="../js/musicplayer.swf?song_url=<?php echo $row_c['file_miejsce_www'].$row['video_filename']; ?>" type="application/x-shockwave-flash">
											<param value="../js/musicplayer.swf?song_url=<?php echo $row_c['file_miejsce_www'].$row['video_filename']; ?>" name="movie">
											<img width="17" height="17" alt="" src="noflash.gif">
										</object>
									<?php } else { ?>
										<img src="<?php echo $row_c['file_miejsce_www'].$row[$idColumn].'_thumb.'.$row[$column]; ?>" alt="" style="padding: 2px; border: 1px solid #E6EEEE;" />
									<?php } } else { ?>
										<?php echo substr(stripslashes($row[$column]),0,150); ?>
									<?php } } ?>
						</td>
						<?php $i++; } ?>
					<?php if($visibleColumn) { ?><td style="text-align: center; width: 55px; vertical-align: middle;"><a rel="show_record" href="javascript:void(0);" title="Zmień status" id="<?php echo $row[$idColumn]; ?>" ><img style="border: 0;" src="../images/admin/icons/main_<?php if($row[$visibleColumn]) echo 'on'; else echo 'off'; ?>.png" alt="Zmień status" /></a></td><?php } ?>
					<td style="text-align: center; width: 70px; vertical-align: middle;">
						<a href="<?php echo $wwwPatchPanel.$menu_url.',view,'.$idForm.','.$row[$idColumn]; ?>.html" title="Edytuj"><img src="../images/admin/icons/edit.png" alt="Edytuj" style="border: 0;" /></a>
						<?php foreach($dodActions as $row_d) { ?>
							<a href="<?php if($row_d['pow_link']) { echo $row_d['pow_link'].',0,'.$row[$idColumn]; } else { echo $wwwPatchPanel.$row_d['nazwa_url'].',add,'.$row_d['pow_form'].','.$row[$idColumn]; } ?>.html" title="<?php echo $row_d['nazwa_formularza']; ?>"><img src="../images/admin/icons/<?php echo $row_d['image']; ?>" alt="<?php echo $row_d['nazwa_formularza']; ?>" style="border: 0;" /></a>
						<?php } ?>
						<a href="javascript:void(0);" rel="delete_record" id="<?php echo $row[$idColumn]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					</td>
				</tr>
			</tbody>
			<?php if($class == 'odd') $class = ''; else $class = 'odd'; } } else { ?>
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
			<a href="<?php echo $wwwPatchPanel.$menu_url.','.($show_page-1); ?>.html"<?php if($show_page == 1) { ?> style="Visibility:Hidden;"<?php } ?>>&laquo;</a>
			<?php
			if($pages_limit > $show_pages)
				$pages_limit = $show_pages+1;
			if($start_limit <= 0)
				$start_limit = 1;

		for($i = $start_limit;$i<$pages_limit; $i++) { ?>
			<?php echo '<a '; if($show_page == $i) { echo ' class="active" '; } echo 'href="'.$wwwPatchPanel.$menu_url.','.$i.'.html">'.$i.'</a></td>'; ?>
		<?php } ?>
		<?php if($show_page != $show_pages) { ?><a href="<?php echo $wwwPatchPanel.$menu_url.','.($show_page+1); ?>.html">&raquo;</a><?php } ?>
		</div>
		<div style="clear:both;"></div>
		<?php } ?>
	</div>

	
	
</div>