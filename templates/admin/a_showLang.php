<style type="text/css">
	.ui-state-highlight { height: 92px; width: 122px;}
	
</style>
<script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	} 

	$(document).ready( function() {
		$('A[rel="delete_record"]').click(function() {
		    var answer;
		    answer = window.confirm("Napewno chcesz usunąć powiązanie?");
		    if (answer == 1) {
    			var url;
			var id = $(this).attr('id');
			var url = $(this).attr('url');
			url = "<?php echo $wwwPanelPatch; ?>" + url + ",delete," + id + ".html";
			window.location = url; // pozostale
			href_remove.href = url; // explorer
		    }
		    return false;
		});
		
		$('A[rel="show_add_form"]').click(function() {
			var id = $(this).attr('id');
			$("#form_"+id).toggle();
		});

		$("#test-list").sortable({
		    handle : '.handle',
		    update : function () {
				    var order = $('#test-list').sortable('toArray');
			var url = $(this).attr("url");
			var dataString = "order="+order;

			$.ajax({
				type: "POST",
				data: dataString,
				url: "<?php echo $wwwPanelPatch; ?>"+url+",sort.html"
			});
		    }
		 });
		


	});
</script>

<div style="margin: 0 auto;">
    <h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
	<div style="float: left;margin-top:1px;">Edytuj rekord</div>
	<br style="clear: both;" />
    </h4>
    <div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
	<table class="table-long">
	    <tbody>
		<tr>
		    <td style="border-top: 0;">
			<div style="float: right; margin-right: 10px;"><a href="<?php echo $wwwPatchPanel.$atr.',edit,'.$atr3.','.$atr4; ?>.html" style="text-decoration: none;">Edytuj dane</a></div>
		    </td>
		</tr>
		<tr>
		    <td style="border-top: 0;">
			<table style="width: 720px; margin-bottom: 15px;">
			<?php foreach($aColumns as $row_c) { $column = $row_c['column_s']; if($row_c['active']) { ?>
			<tr>
			    <td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
				<?php echo '<span style="color: #848E92; font-weight: bold;">'.$row_c['nazwa_pola'].'</span>: '; ?>
			    </td>
			    <td>
				<?php	if($row_c['lang']) echo strip_tags(stripslashes($aData[0][$column]['pl']));
					else {	if($row_c['pow_value']) {
						    foreach($row_c['powiazania'] as $row_pow) {
							if($aData[0][$column] == $row_pow['pow_value'])
							    echo strip_tags(stripslashes($row_pow['pow_name']));
						    }
						}
						else echo strip_tags(stripslashes($aData[0][$column])); }?>&nbsp;
			    </td>
			</tr>
			<?php } } ?>
			</table>
		    </td>
		</tr>
		<tr>
		    <td style="border-top: 0;">
			<div style="float: right; margin-right: 10px;"><a href="<?php echo $wwwPatchPanel.$atr.',edit,'.$atr3.','.$atr4; ?>.html" style="text-decoration: none;">Edytuj dane</a></div>
		    </td>
		</tr>
		<?php  foreach($aRelatedData as $row_r) { $column = $row_r['column_s']; ?>
		<tr>
		    <td style="border-top: none;">
			<a name="pow"></a>
			<h1 style="width: 720px; padding-bottom: 3px; border-bottom: 1px solid #DFDFDF; font-size: 14px; font-weight: bold;"><?php echo $row_r['info']['title']; ?></h1>
			<table style="width: 720px; margin-bottom: 20px;">
			    <?php // nowa kolumna w cms_formularz ktra bedzie okreslac sposób powiazania, select, checkbox albo file ?>
			    <?php if($row_r['info']['main_pow_type'] == 'checkbox') { ?><form method="post" action="" name="">
			    <?php $tr = 0; if($photo) { echo '</ul></td></tr>'; $photo = false; } foreach($row_r['values'] as $row_val) { ?>
			    <?php if ($tr == 0) { ?><tr><?php } elseif($tr != 0 && is_int($tr/3)) { ?></tr><tr><?php } ?>
				<td style="border-top: 0; width: 33%; vertical-align: top; padding: 5px 0;">
				    <?php $checked = false; foreach($row_r['data'] as $row_d) {
					    if($row_d['pl'][$row_r['info']['main_column']] == $row_val['pl'][$row_r['info']['main_column']])
						$checked = 'checked="checked"';
					    } ?>
				    <div style="float: left;"><input type="checkbox" name=<?php echo $row_r['info']['main_column_id']; ?>_field[]" <?php echo $checked; ?> value="<?php echo $row_val['pl'][$row_r['info']['main_column_id']]; ?>" /></div>
				    <div style="float: left; margin-left: 5px; margin-top: 3px; width: 200px;"><?php echo $row_val['pl'][$row_r['info']['main_column']]; ?></div>
				</td>

			    <?php $tr++; } ?>
				<tr>
				    <td colspan="3" style="text-align: right;">
					<input type="hidden" name="main_field" value="<?php echo $obMain -> idColumn; ?>" />
					<input type="hidden" name="<?php echo $obMain -> idColumn; ?>_field" value="<?php echo $atr4; ?>" />
					<input type="hidden" name="form_id" value="<?php echo $row_r['id_formularza']; ?>" />
					<input type="hidden" name="array_column" value="<?php echo $row_r['info']['main_column_id']; ?>" />
					<input type="hidden" name="table" value="<?php echo $row_r['nazwa_tabeli']; ?>" />
					<input type="submit" name="dodaj_pow_array" value="Zapisz" style="margin-top: 5px;"/>
				    </td>
				</tr>
			    </form>
			    <?php } ?>
			    <?php $k = 0; $photo = false; if(is_array($row_r['data'])) { foreach($row_r['data'] as $row_d) { ?>
			    <?php if($row_r['info']['main_pow_type'] == 'select') { if($photo) { echo '</ul></td></tr>'; $photo = false; } ?>
			    <tr>
				<td colspan="3" style="border-top: 0; border-bottom: 1px solid #DFDFDF; vertical-align: middle; background:<?php if(is_int($k/2)) echo '#E9F0F2'; else echo '#F2F2F2'; ?>">
				    <div style="float: left;"><?php echo $row_d['pl'][$row_r['info']['main_column']]; ?></div>
				    <div style="float: left; margin-left: 5px;"><a href="javascript:void(0);" rel="delete_record" url="<?php echo $row_r['nazwa_url']; ?>" id="<?php echo $row_d['pl'][$row_r['info']['id_column']]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a></div>
				</td>
			    </tr>   
			    <?php } elseif($row_r['info']['main_pow_type'] == 'file') { $foto = true; //jesli są to obrazki
				if(!$photo) { $photo = true; ?>
				<tr>
				    <td colspan="3" style="border-top: 0; border-bottom: 1px solid #DFDFDF; vertical-align: middle; background:#F2F2F2">
					<ul id="test-list" style="list-style: none; padding-left: 0; width: 150px;" url="<?php echo $row_r['nazwa_url']; ?>">
			    <?php } ?>
					    <li class="ui-state-default" id="<?php echo $row_d[$row_r['info']['id_column']]; ?>" style="width: 170px; height: 90px; border: 0;">
						<table class="handle" style="cursor: move; background-color: #fff;">
						    <tbody>
							<tr style="background:<?php if(is_int($k/2)) echo '#E9F0F2'; else echo '#F2F2F2'; ?>">
							    
								<?php if($row_r['info']['lang_table'] != '') { ?>
								<td style="width: 130px;"><img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="<?php echo $row_r['info'][$row_r['info']['main_column']]['file_miejsce_www'].$atr4.'/'.$row_d['pl'][$row_r['info']['main_column_id']].'_thumb.'.$row_r['info'][$row_r['info']['main_column']]['image_main_roz']; ?>" /></td>
								<td style="vertical-align: top;"><a href="javascript:void(0);" rel="delete_record" url="<?php echo $row_r['nazwa_url']; ?>" id="<?php echo $row_d['pl'][$row_r['info']['id_column']]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; margin-top: 5px; margin-left: 5px; border: 0;" /></a></td>
								<?php } else { ?>
								<td style="width: 130px;"><img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="<?php echo $row_r['info'][$row_r['info']['main_column']]['file_miejsce_www'].$atr4.'/'.$row_d[$row_r['info']['main_column_id']].'_thumb.'.$row_r['info'][$row_r['info']['main_column']]['image_main_roz']; ?>" /></td>
								<td style="vertical-align: top;"><a href="javascript:void(0);" rel="delete_record" url="<?php echo $row_r['nazwa_url']; ?>" id="<?php echo $row_d[$row_r['info']['id_column']]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; margin-top: 5px; border: 0; margin-left: 5px;" /></a></td>
								<?php } ?>
							    </td>
							</tr>
						    </tbody>
						</table>
					    </li>
			    <?php } ?>
			    <?php $k++; } if($photo) { echo '</ul></td></tr>'; $photo = false; } } else { ?>
				<tr>
				    <td colspan="3" style="border-top: 0; border-bottom: 1px solid #DFDFDF; vertical-align: middle; background: #F2F2F2;">brak powiązań</td>
				</tr>
				<?php } //formularz z selectami do dodawania powiazanych ?>
			    <tr id="form_<?php echo $row_r['id_formularza']; ?>" style="display: none;">
				<td colspan="3" style="padding-top: 5px;text-align: right; border-top: 0; padding-left: 0">
				    <?php if($row_r['info']['main_pow_type'] == 'select') { ?>
				    <form action="#pow" method="post">
					<select name="<?php echo $row_r['info']['main_column_id']; ?>_field" style="float: left;">
					    <?php if(is_array($row_r['values'])) {
						    foreach($row_r['values'] as $val) {
							if($row_r['info']['lang_table'] != '') {
							    if(is_array($row_r['data'])) {
								$isit = false;
								foreach($row_r['data'] as $row_d) {
								    if($row_d['pl'][$row_r['info']['main_column']] == $val['pl'][$row_r['info']['main_column']]) {
									$isit = true;
								    }
								}
								if(!$isit)
								    echo '<option value="'.$val['pl'][$row_r['info']['main_column_id']].'">'.$val['pl'][$row_r['info']['main_column']].'</option>';
							    } else {
								echo '<option value="'.$val['pl'][$row_r['info']['main_column_id']].'">'.$val['pl'][$row_r['info']['main_column']].'</option>';
							    }
							}
							else {
							    echo '<option value="'.$val[$row_r['info']['main_column_id']].'">'.$val[$row_r['info']['main_column']].'</option>';
							}
						    }
						  } ?>
					</select>
					<input type="hidden" name="form_id" value="<?php echo $row_r['id_formularza']; ?>" />
					<input type="hidden" name="<?php echo $obMain -> idColumn; ?>_field" value="<?php echo $atr4; ?>" />
					<input type="hidden" name="type" value="<?php echo $row_r['info'][$row_r['info']['main_column']]['typ_pola']; ?>" />
					<input type="hidden" name="table" value="<?php echo $row_r['nazwa_tabeli']; ?>" />
					<input type="submit" value="Dodaj" name="dodaj_pow" style="float: left; margin-left: 5px;" />
				    </form>
				    <?php } else if($row_r['info'][$row_r['info']['main_column']]['typ_pola'] == 'file') { ?>
				    
				    <link rel="stylesheet" type="text/css" href="../css/admin/SWFUpload_view.css" />
				    <style type="text/css">
					    ul#menu li a.top-level:hover span, .blue span {background-image: none;}
				    </style>

				    <script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload/swfupload.js"></script>
				    <script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload.queue.js"></script>
				    <script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/fileprogress_view.js"></script>
				    <script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/handlers.js"></script>

				    <script type="text/javascript">
					$(document).ready(function(){
					    var swfu;
					    swfu = new SWFUpload({
						    flash_url : "<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload/swfupload.swf",
						    upload_url: "<?php echo $wwwPatch; ?>tff/flash_upload.php?sciezka=<?php echo $row_r['info'][$row_r['info']['main_column']]['file_miejsce']; ?>&convert=<?php echo $row_r['info'][$row_r['info']['main_column']]['image_main_roz']; ?>&sciezka2=<?php echo $atr4; ?>&sciezka3=new",
						    file_post_name : "Filedata",
						    file_size_limit : "300 MB",
						    file_types : "<?php echo $row_r['info'][$row_r['info']['main_column']]['file_rozszerzenia']; ?>",
						    file_types_description : "Pliki graficzne (<?php echo $row_r['info'][$row_r['info']['main_column']]['file_rozszerzenia']; ?>)",
						    file_upload_limit : 10,
						    file_queue_limit : 5,
						    custom_settings : {
							    progressTarget : "fsUploadProgress",
							    cancelButtonId : "btnCancel"
						    },
						    debug: false,

						    button_image_url : "<?php echo $wwwPatch; ?>js/admin/SWFUpload/images/wybierz.png",
						    button_placeholder_id : "spanButtonPlaceHolder",
						    button_width: 48,
						    button_height: 22,

						    file_dialog_start_handler : fileDialogStart,
						    file_queued_handler : fileQueued,
						    file_queue_error_handler : fileQueueError,
						    file_dialog_complete_handler : fileDialogComplete,
						    upload_start_handler : uploadStart,
						    upload_progress_handler : uploadProgress,
						    upload_error_handler : uploadError,
						    upload_success_handler : uploadSuccess,
						    upload_complete_handler : uploadComplete
					    });

					});
				    </script>

				    <form id="form1" action="index.php" method="post" enctype="multipart/form-data" style="float: left; display: inline-block">
					<div class="fieldset flash" id="fsUploadProgress" style="float: left; margin-left: 5px;margin-bottom: 5px; text-align: left;">
						<span class="legend">Wgraj zdjęcia</span>
					</div>
					<div style="width: 375px; text-align: left; margin-left: 5px;">
						<span id="spanButtonPlaceHolder"></span>
						<input id="btnCancel" type="button" value="Anuluj" onclick="cancelQueue(swfu);" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 22px; padding-top: 0px; border-radius: 5px 5px 5px 5px; background: url(<?php echo $wwwPatch; ?>js/admin/SWFUpload/images/input.png) repeat-x scroll 0 0 transparent;" />
					</div>
				    </form>

				    <br style="clear:both;" />
				    <form action="#pow" method="post" style="float: left; margin-top: 5px;">
					<input type="hidden" name="table" value="<?php echo $row_r['nazwa_tabeli']; ?>" />
					<input type="hidden" name="place" value="<?php echo $row_r['info'][$row_r['info']['main_column']]['file_miejsce'].$atr4; ?>" />
					<input type="hidden" name="form_id" value="<?php echo $row_r['id_formularza']; ?>" />
					<input type="hidden" name="photo_order_field" value="1" />
					<input type="hidden" name="<?php echo $obMain -> idColumn; ?>_field" value="<?php echo $atr4; ?>" />
					<input type="hidden" name="<?php echo $row_r['info']['main_column']; ?>_field" value="<?php echo $row_r['info'][$row_r['info']['main_column']]['image_main_roz']; ?>" />
					<input type="submit" name="zapisz_foto" value="Dodaj wgrane zdjęcia" style="float: left; margin-left: 5px;" />
				    </form>


				    <?php } ?>
				</td>
			    </tr>
			    <?php if($row_r['info']['main_pow_type'] != 'checkbox') { ?>
			    <tr>
				<td colspan="3" style="padding-top: 5px;text-align: right; border-top: 0;">
				    <a href="javascript:void(0);" rel="show_add_form" id="<?php echo $row_r['id_formularza']; ?>" style="display: block; text-decoration: none; color: #848E92">
					<div style="float: right; margin-top: 1px; margin-left: 4px;"><?php echo $row_r['info'][$row_r['info']['main_column']]['tresc_przycisku']; ?></div>
					<div style="float: right;"><img src="../images/admin/icons/add.gif" alt="" style="border: 1px solid #E0E0E0;"></div>
				    </a>
				</td>
			    </tr>
			   <?php } ?>
			</table>
		    </td>
		</tr>
		<?php } ?>
	    </tbody>
	</table>
    </div>
</div>
							<?php /*if($row_c['powiazana_tabela'] != '') { ?>
								<?php if($row['powColumn_rec'][0] == '') { ?><span style="color: #9C938B;">brak powiązania</span><?php } else { echo $row['powColumn_rec'][0]; } ?>
									<?php } else { if($row_c['typ_pola'] == 'file') { ?>
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
					<?php if($visibleColumn) { ?><td style="text-align: center; width: 55px;"><a rel="show_record" href="javascript:void(0);" title="Zmień status" id="<?php echo $row[$idColumn]; ?>" ><img style="border: 0;" src="../images/admin/icons/main_<?php if($row[$visibleColumn]) echo 'on'; else echo 'off'; ?>.png" alt="Zmień status" /></a></td><?php } ?>
					<td style="text-align: center; width: 70px;">
						<a href="<?php echo $wwwPatchPanel.$menu_url.',edit,'.$idForm.','.$row[$idColumn]; ?>.html" title="Edytuj"><img src="../images/admin/icons/edit.png" alt="Edytuj" style="border: 0;" /></a>
						<?php foreach($dodActions as $row_d) { ?>
							<a href="<?php if($row_d['pow_link']) { echo $row_d['pow_link']; } else { echo $wwwPatchPanel.$row_d['nazwa_url'].',add,'.$row_d['pow_form'].','.$row[$idColumn]; } ?>.html" title="<?php echo $row_d['nazwa_formularza']; ?>"><img src="../images/admin/icons/<?php echo $row_d['image']; ?>" alt="<?php echo $row_d['nazwa_formularza']; ?>" style="border: 0;" /></a>
						<?php } ?>
						<a href="javascript:void(0);" rel="delete_record" id="<?php echo $row[$idColumn]; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					</td>
				</tr>
			</tbody>
		</table>
		<div style="clear:both;"></div>
	</div>

	
	
</div> */ ?>