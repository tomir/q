<script type="text/javascript" src="http://gfx.tvs.pl/js/upload/jquery.flash.js"></script>
<script src="../js/admin/jquery-ui-1.8.5.custom.min"  type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#tformtabs").tabs();
	})
</script>
<div class="box">
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		 <?php if($akcja2 == 'edit') { ?>Edytuj rekord<?php } else { ?>Dodaj nowy rekord<?php } ?>  		
	</h4>

    <?php if($aPola) { 
		    $file   = false;
		    $ftp    = false;
		    foreach($aPola as $row_p) {
   	 		if($row_p['typ_pola'] == "file" && $row_p['ftp_host'] == '') {
				
				$file = true;
				$field_column = $row_p['column_s'] ?>
				<link rel="stylesheet" type="text/css" href="../css/admin/SWFUpload.css" />
				<style type="text/css">
					ul#menu li a.top-level:hover span, .blue span {background-image: none;}
				</style>
				<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload/swfupload.js"></script>
				<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload.queue.js"></script>
				<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/fileprogress.js"></script>
				<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/SWFUpload/handlers.js"></script>
				<script type="text/javascript">
				$(document).ready(function() {
					var swfu;
					swfu = new SWFUpload({
						flash_url : "<?php echo $wwwPatch; ?>js/admin/SWFUpload/swfupload/swfupload.swf",
						upload_url: "<?php echo $wwwPatch; ?>tff/flash_upload.php?sciezka=<?php echo $row_p['file_miejsce']; ?>&convert=<?php echo $row_p['image_main_roz']; ?>",
						file_post_name : "Filedata",
						file_size_limit : "300 MB",
						file_types : "<?php echo $row_p['file_rozszerzenia']; ?>",
						file_types_description : "Pliki graficzne (<?php echo $row_p['file_rozszerzenia']; ?>)",
						file_upload_limit : 1,
						file_queue_limit : 1,
						custom_settings : {
							progressTarget : "fsUploadProgress",
							cancelButtonId : "btnCancel"
						},
						debug: false,

						button_image_url : "<?php echo $wwwPatch; ?>images/admin/XPButtonUploadText_61x22.png",
						button_placeholder_id : "spanButtonPlaceHolder",
						button_width: 61,
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
				})
				</script>
				
		    <?php } elseif($row_p['ftp_host'] != '') { $ftp = true; ?>
				<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/RelatedObjectLookups.js"></script>
		    <?php } ?>
   	 	<?php } ?>
      	
      	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
			<form class="middle-forms" style="padding: 10px;" id="myFromularz" name="formularz" action="<?php echo $adminPatch; ?><?php echo $akcja; ?>,<?php echo $akcja2; ?>,<?php echo $formId; if($akcja4 != '') echo ','.$akcja4; ?>.html" method="post"><input type="hidden" name="zapisz" value="1" />
	      		<p>pola wymagane oznaczone <em>*</em></p>
				<br style="clear: both;" />
						<fieldset>
							<ol>
								<?php $class = "even"; foreach($aPola as $row_p) {
									$colum = $row_p['column_s'];
									if($row_p['textarea_tinymc'] == 1)
										$tinymc = $row_p['textarea_tinymc']; ?>
									<li class="<?php echo $class; ?>">
									<?php if($row_p['typ_pola'] != "file") { ?><label class="field-title"><?php echo $row_p['nazwa_pola']; if($row_p['wymagane']) echo '<em>*</em>'; ?>:</label><?php } ?>
									<?php if($row_p['typ_pola'] == "file") {
										if($file == true) { ?>
										    <input type="hidden" name="<?php echo $row_p['column_s']; ?>_field" id="<?php echo $row_p['column_s']; ?>_field" value="" />
										    <input type="hidden" name="nazwaPliku" id="nazwaPliku" value="" />
										    <label><div class="fieldset flash" id="fsUploadProgress">
											    <span class="legend">Wgraj plik</span>
										    </div>
										    <div>
											    <span id="spanButtonPlaceHolder"></span>
											    <input id="btnCancel" type="button" value="Anuluj" onclick="cancelQueue(swfu);" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 22px;" />
										    </div></label>
										<?php } elseif($ftp == true) { ?>
										   
										    <label class="field-title">Plik:</label>
										    <label>
											<input type="text" name="<?php echo $row_p['column_s']; ?>_field" id="adres" class="txtbox-long" /><br />
											<a href="<?php echo $adminPatch; ?>addpopup.html" id="lookup_adres" onclick="return showRelatedObjectLookupPopup(this);" title="Dodaj nowege wideo" style="">wybierz plik</a>
										    </label>
										<?php } ?>
									<?php } ?>
									<?php if($row_p['typ_pola'] == "text" || $row_p['typ_pola'] == "password") { ?>
										<?php if($row_p['akcje'] == 'kalendarz') {
											$kalendarz = 1;
											$kalendarz_id= $row_p['column_s'];
											if($aDane[0][$colum] == '')
												$aDane[0][$colum] = date("Y-m-d");
										 } ?>
										<label><input class="txtbox-long" style="<?php echo $row_p['styl']; ?>" type="<?php echo $row_p['typ_pola']; ?>" name="<?php echo $row_p['column_s'].'_field'; ?>" id="<?php echo $row_p['column_s'].'_field'; ?>" value="<?php echo stripslashes($aDane[0][$colum]); ?>" /></label>
									<?php } elseif($row_p['typ_pola'] == "textarea") { ?>
										<label><textarea id="<?php if($row_p['textarea_tinymc'] == 1) echo 'tinymc'; else echo $row_p['column_s'].'_field'; ?>" style="width: <?php echo $row_p['textarea_x']; ?>px; height: <?php echo $row_p['textarea_y']; ?>px; <?php echo $row_p['styl']; ?>" name="<?php echo $row_p['column_s'].'_field'; ?>" ><?php echo stripslashes($aDane[0][$colum]); ?></textarea></label>
									<?php } elseif($row_p['typ_pola'] == "select") { ?>
										<label><select style="<?php echo $row_p['styl']; ?>" name="<?php echo $row_p['column_s']; ?>_field">
												<option value=""></option>
												<?php foreach($row_p['powiazania'] as $row_p2) { ?>
													<option <?php if($row_p2['pow_value'] == $aDane[0][$colum]) echo 'selected="selected"'; ?> value="<?php echo $row_p2['pow_value']; ?>"><?php echo $row_p2['pow_name']; ?></option>
												<?php } ?>
											</select>
										</label>
									<?php } elseif($row_p['typ_pola'] == "checkbox") { ?>
										<label><input name="<?php echo $row_p['column_s']; ?>_field" type="checkbox" <?php if($aDane[0][$colum] == 1) echo 'checked="checked"'; ?> value="1" /></label>
									<?php } ?>
									<span class="clearFix">&nbsp;</span>
									</li>
								<?php if($class == "even") $class = ""; else $class = "even"; } ?>

							</ol>
						</fieldset>
				<?php if($file == 1) { ?>
			    	<p style="text-align: right;"><?php if($akcja2 == 'edit') { ?><input type="button" id="zapisz" value="Edytuj" /><?php } else { ?><input type="button" id="zapisz" value="Dodaj" /><?php } ?></p>
			    <?php } else { ?>
			    	<p style="text-align: right;"><?php if($akcja2 == 'edit') { ?><input type="submit" value="Zapisz" /><?php } else { ?><input type="submit" value="Dodaj" /><?php } ?></p>
			    <?php } ?>
      		</form>
		</div>
	<?php } ?>
</div>
<?php if($kalendarz == 1) { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/admin/jquery-ui-1.7.2.custom.css" /> 
	<script type="text/javascript" src="../js/admin/jquery-datepicker.min.js" ></script>
	<script type="text/javascript">
	$(function() {
			$("#<?php echo $kalendarz_id; ?>_field").datepicker({dateFormat: 'yy-mm-dd'});
	});
	</script>
<?php } ?>
<?php if($tinymc == 1) { ?>
	<script type="text/javascript" src="../js/admin/tiny_mce/tiny_mce.js" ></script>
	<script type="text/javascript">
		tinyMCE.init({
			mode : "textareas",
			theme : "advanced",
			plugins : "phpimage,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
			
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,,",
			theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "formatselect,fontsizeselect,|,link,unlink,code|,forecolor,backcolor,",
			theme_advanced_buttons4 : ",tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,code"
		});
	</script>
<?php } ?>
<?php if($file == 1) { ?>
	<script type="text/javascript">
		$(document).ready(function(){
			
			$("#zapisz").click( function() {
				var foto_name = $(".progressName").html();
				$("#nazwaPliku").val(foto_name);
				$("#<?php echo $field_column; ?>_field").val(foto_name);
				$("#myFromularz").submit();
			});
					
		});
	</script>
<?php } ?>