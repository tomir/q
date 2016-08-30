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
		
		$("#test-list").sortable({
		    handle : '.handle',
		    update : function () {
				    var order = $('#test-list').sortable('toArray');
				    var dataString = "order="+order;

				    $.ajax({
					    type: "POST",
					    data: dataString,
					    url: "<?php echo $wwwPanelPatch.$menu_url ?>,sort.html"
				    });
		    }
		});
		$("#test-list").disableSelection();


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
				<?php foreach($aColumns as $row_c) { ?>
					<td style="font-size: 12px; width: <?php echo (600/count($aColumns)); ?>px; padding: 5px 8px; <?php if($row_c['wysrodkowana']) { ?>text-align: center;<?php } ?>" class="header"><?php echo $row_c['nazwa_pola']; ?></td>
				<?php } ?>
					<?php if($visibleColumn) { ?><td class="header" style="font-size: 12px; text-align: center;">Widoczny</td><?php } ?>
					<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
				</tr>
			</thead>
		</table>
			<?php if(is_array($aResult) && count($aResult) > 0) { ?>
		<ul id="test-list" style="list-style: none; padding-left: 0;">
			<?php $class = 'odd'; foreach($aResult as $row) { ?>
			<li id="<?php echo $row[$idColumn]; ?>">
				<table class="handle table-long" style="cursor: move; background-color: #fff;">
					<tbody>
						<tr class="<?php echo $class; ?>">
							<?php $i = 0; foreach($aColumns as $row_c) { ?>
								<?php $column = $row_c['column_s']; ?>
								<td style="vertical-align: middle; font-size: 12px; width: <?php echo (700/count($aColumns)); ?>px; padding: 5px 8px; <?php if($row_c['wysrodkowana'] == 1) { ?>text-align: center;<?php } ?>">
									<?php if($row_c['powiazana_tabela'] != '') { ?>
										<?php if(!is_array($row['powColumn_rec'])) { ?><span style="color: #9C938B;">brak powiązania</span><?php } else { echo $row['powColumn_rec'][$i]; } ?>
											<?php } else { if($row_c['typ_pola'] == 'file') { ?>
											<?php if($row[$column] == 'mov' || $row[$column] == 'mp4' || $row[$column] == 'flv') { ?>
											<script type='text/javascript' src='{$wwwPatch}images/mediaplayer-5.2/swfobject.js'></script>

											<div id='mediaspace<?php echo $row[$idColumn]; ?>'></div>

											<script type='text/javascript'>
											  var so = new SWFObject('<?php echo $wwwPatch; ?>images/mediaplayer-5.2/player.swf','ply','210','120','9','#000000');
											  so.addParam('allowfullscreen','true');
											  so.addParam('allowscriptaccess','always');
											  so.addParam('wmode','opaque');
											  so.addVariable('file','<?php echo $wwwPatch; ?>pliki/<?php echo $row[$idColumn]; ?>.<?php echo $row['video_filename']; ?>');
											  so.write('mediaspace<?php echo $row[$idColumn]; ?>');
											</script>
											<?php } elseif($row[$column] == 'mp3') { ?>
												<object width="17" height="17" data="../js/musicplayer.swf?song_url=<?php echo $row_c['file_miejsce_www'].$row[$idColumn].'.'.$row['video_filename']; ?>" type="application/x-shockwave-flash">
													<param value="../js/musicplayer.swf?song_url=<?php echo $row_c['file_miejsce_www'].$row[$idColumn].'.'.$row['video_filename']; ?>" name="movie">
													<img width="17" height="17" alt="" src="noflash.gif">
												</object>
											<?php } else { ?>
												<img src="<?php echo $row_c['file_miejsce_www'].$row[$idColumn].'_thumb.'.$row[$column]; ?>" alt="" style="padding: 2px; border: 1px solid #E6EEEE;" />
											<?php } } else { ?>
												<?php echo substr(stripslashes($row[$column]),0,150); ?>
											<?php } } ?>
								</td>
								<?php  } $i++; ?>
							<?php if($visibleColumn) { ?><td style="text-align: center; width: 55px;"><a style="display: block; height: 25px;" rel="show_record" href="javascript:void(0);" title="Zmień status" id="<?php echo $row[$idColumn]; ?>" ><img style="padding: 3px;border: 0;" src="../images/admin/icons/main_<?php if($row[$visibleColumn]) echo 'on'; else echo 'off'; ?>.png" alt="Zmień status" /></a></td><?php } ?>
							<td style="text-align: center; width: 70px;">
								<a href="<?php echo $wwwPatchPanel.$menu_url.',view,'.$idForm.','.$row[$idColumn]; ?>.html" title="Edytuj"><img src="../images/admin/icons/edit.png" alt="Edytuj" style="border: 0;" /></a>
								<?php foreach($dodActions as $row_d) { ?>
									<a href="<?php if($row_d['pow_link']) { echo $row_d['pow_link']; } else { echo $wwwPatchPanel.$row_d['nazwa_url'].',add,'.$row_d['pow_form'].','.$row[$idColumn]; } ?>.html" title="<?php echo $row_d['nazwa_formularza']; ?>"><img src="../images/admin/icons/<?php echo $row_d['image']; ?>" alt="<?php echo $row_d['nazwa_formularza']; ?>" style="border: 0;" /></a>
								<?php } ?>
								<a href="javascript:void(0);" rel="delete_record" id="<?php echo $row[$idColumn]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<?php if($class == 'odd') $class = ''; else $class = 'odd'; } } else { ?>
			<table class="table-long">
				<tr class="odd"><td colspan="7" style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">Brak rekordów w bazie</td></tr>
			</table>
			<?php } ?>
		</ul>
		<div style="color: #23AAAA; font-size: 12px; margin-top: 10px; margin-left: 10px;">Aby zmienić kolejność "złap" za element i przeciągnij go w dół lub górę</div>
	</div>

	
	<?php  /*
		$pages_limit = $show_page + 5;
		$start_limit = $show_page - 5;
		if($show_pages > 1) { ?>
			<table class="header" cellspacing="1" cellpadding="0" style="background-color: #fff; min-width: 60px; margin: 5px auto;">
				<tr><td style="border: 0px;"><span style="font-weight: bold;">strony: </span></td>
				<?php if($start != 0) { ?><td style="background: #EFEFEF; padding: 0; text-align: center; border: 1px solid #CDCDCD;"><a href="<?php echo $wwwPatchPanel.$menu_url.','.$start-1; ?>"<?php if($start == 1) { ?> style="visibility: hidden;"<?php } ?>>&laquo;</a></td><?php } ?>
		<?php
		if($pages_limit > $show_pages)
			$pages_limit = $show_pages+1;
		if($start_limit <= 0)
			$start_limit = 1;

	for($i = $start_limit;$i<$pages_limit; $i++) { ?>
		<?php echo '<td style="background: #EFEFEF; padding: 0; text-align: center; border: 1px solid #CDCDCD;"><a '; echo 'style="'; if($start == $i) { echo 'font-weight: bold; text-decoration: underline;'; } echo 'display: block; width: 7px; height: 15px; padding: 4px;" href="'.$wwwPatchPanel.$menu_url.','.$i.'.html">'.$i.'</a></td>'; ?>
	<?php } ?>
	<?php if($start != $show_pages) { ?><td style="background: #EFEFEF; padding: 0; text-align: center; border: 1px solid #CDCDCD;"><a href="<?php echo $wwwPatchPanel.$menu_url.','.$start+1; ?>.html">&raquo;</a></td><?php } ?>
		</tr>
	</table>
	<?php } */ ?>
</div>