<script type="text/javascript" src="http://gfx.tvs.pl/js/upload/jquery.flash.js"></script>

<div class="box">
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		 {if $akcja2 == 'edit'}Edytuj rekord{else}Dodaj nowy rekord{/if}  		
	</h4>
    {if $aPola}
   	 	{section name=id loop=$aPola}
   	 		{if $aPola[id].typ_pola == "file"}
				
				{assign var="file" value=1}
				{assign var="field_column" value=$aPola[id].column_s}
				<link rel="stylesheet" type="text/css" href="../css/admin/SWFUpload.css" />
				{literal}
				<style type="text/css">
					ul#menu li a.top-level:hover span, .blue span {background-image: none;}
				</style>
				{/literal}
				<script type="text/javascript" src="{$wwwPatch}js/admin/SWFUpload/swfupload/swfupload.js"></script>
				<script type="text/javascript" src="{$wwwPatch}js/admin/SWFUpload/swfupload.queue.js"></script>
				<script type="text/javascript" src="{$wwwPatch}js/admin/SWFUpload/fileprogress.js"></script>
				<script type="text/javascript" src="{$wwwPatch}js/admin/SWFUpload/handlers.js"></script>
				{literal}
				<script type="text/javascript">
				$(document).ready(function() {
					var swfu;
					swfu = new SWFUpload({
						flash_url : "{/literal}{$wwwPatch}{literal}js/admin/SWFUpload/swfupload/swfupload.swf",
						upload_url: "{/literal}{$wwwPatch}tff/flash_upload.php?sciezka={$aPola[id].file_miejsce}&convert={$aPola[id].image_main_roz}{literal}",
						file_post_name : "Filedata",
						file_size_limit : "300 MB",
						file_types : "{/literal}{$aPola[id].file_rozszerzenia}{literal}",
						file_types_description : "Pliki graficzne ({/literal}{$aPola[id].file_rozszerzenia}{literal})",
						file_upload_limit : 1,
						file_queue_limit : 1,
						custom_settings : {
							progressTarget : "fsUploadProgress",
							cancelButtonId : "btnCancel"
						},
						debug: false,
				
						button_image_url : "{/literal}{$wwwPatch}{literal}images/admin/XPButtonUploadText_61x22.png",
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
				});
				</script>
				{/literal}
      		{/if}
   	 	{/section}
      	
      	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
			<form class="middle-forms" style="padding: 10px;" id="myFromularz" name="formularz" action="{$wwwPatch}admin/{$akcja},{$akcja2},{$formId}{if $akcja4 != ''},{$akcja4}{/if}.html" method="post"><input type="hidden" name="zapisz" value="1" />
	      		<p>pola wymagane oznaczone <em>*</em></p>
      			<fieldset>
      				<ol>
			      		{section name=id loop=$aPola}
			      			{assign var="colum" value=$aPola[id].column_s}
				      		{if $aPola[id].textarea_tinymc == 1}
				      			{assign var="tinymc" value=$aPola[id].textarea_tinymc}
				      		{/if}
		      				<li class="{cycle values="even,"}">
		      				{if $aPola[id].typ_pola != "file"}<label class="field-title">{$aPola[id].nazwa_pola}{if $aPola[id].wymagane} <em>*</em>{/if}:</label>{/if}
		      				{if $aPola[id].typ_pola == "file"}
		      					<input type="hidden" name="{$aPola[id].column_s}_field" id="{$aPola[id].column_s}_field" value="" />
		      					<input type="hidden" name="nazwaPliku" id="nazwaPliku" value="" />
		      					<label><div class="fieldset flash" id="fsUploadProgress">
									<span class="legend">Wgraj plik</span>
								</div>
								<div>
									<span id="spanButtonPlaceHolder"></span>
									<input id="btnCancel" type="button" value="Anuluj" onclick="cancelQueue(swfu);" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 22px;" />
								</div></label>
		      				{/if}
		      				{if $aPola[id].typ_pola == "text" || $aPola[id].typ_pola == "password"}
		      					{if $aPola[id].akcje == 'kalendarz'}
		      						{assign var="kalendarz" value="1"}
		      						{assign var="kalendarz_id" value=$aPola[id].column_s}
		      					{/if}
		      					<label><input class="txtbox-long" style="{$aPola[id].styl}" type="{$aPola[id].typ_pola}" name="{$aPola[id].column_s}_field" id="{$aPola[id].column_s}_field" value="{$aDane[0].$colum|stripslashes}" /></label>
		      				{elseif $aPola[id].typ_pola == "textarea"}
		      					<label><textarea {if $aPola[id].textarea_tinymc == 1}id="tinymc"{/if} style="width: {$aPola[id].textarea_x}px; height: {$aPola[id].textarea_y}px; {$aPola[id].styl}" name="{$aPola[id].column_s}_field" id="{$aPola[id].column_s}_field">{$aDane[0].$colum|stripslashes}</textarea></label>
		      				{elseif $aPola[id].typ_pola == "select"}
		      					<label><select style="{$aPola[id].styl}" name="{$aPola[id].column_s}_field">
		      							<option value=""></option>
		      							{section name=id2 loop=$aPola[id].powiazania}
		      								<option {if ($aPola[id].powiazania[id2].pow_value == $akcja4) || ($aPola[id].powiazania[id2].pow_value == $aDane[0].$colum)}selected="selected"{/if} value="{$aPola[id].powiazania[id2].pow_value}">{$aPola[id].powiazania[id2].pow_name}</option>
		      							{/section}
		      						</select>
		      					</label>
		      				{elseif $aPola[id].typ_pola == "checkbox"}
								<label><input name="{$aPola[id].column_s}_field" type="checkbox" {if $aDane[0].$colum == 1}checked="checked"{/if} value="1" /></label>
		      				{/if}
		      				<span class="clearFix">&nbsp;</span>
		      				</li>
			      		{/section}
			      		
			      	</ol>
				</fieldset>
				{if $file == 1}
			    	<p style="text-align: right;">{if $akcja2 == 'edit'}<input type="button" id="zapisz" value="Edytuj" />{else}<input type="button" id="zapisz" value="Dodaj" />{/if}</p>
			    {else}
			    	<p style="text-align: right;">{if $akcja2 == 'edit'}<input type="submit" value="Zapisz" />{else}<input type="submit" value="Dodaj" />{/if}</p>
			    {/if}
      		</form>
      	</div>
	{/if}
</div>
{if $kalendarz == 1}
	<link rel="stylesheet" type="text/css" media="screen" href="../css/admin/jquery-ui-1.7.2.custom.css" /> 
	<script type="text/javascript" src="../js/admin/jquery-datepicker.min.js" ></script>
	<script type="text/javascript">
	{literal}$(function() {
			$("#{/literal}{$kalendarz_id}{literal}_field").datepicker({dateFormat: 'yy-mm-dd'});
	});{/literal}
	</script>
{/if}
{if $tinymc == 1}
	{literal}
	<script type="text/javascript" src="../js/admin/tiny_mce/tiny_mce.js" ></script>
	<script type="text/javascript">
		tinyMCE.init({
			mode : "textareas",
			theme : "advanced",
			plugins : "phpimage,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
			
			theme_advanced_buttons1 : ",phpimage,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,,",
			theme_advanced_buttons2 : "formatselect,fontsizeselect,|,link,unlink,|,forecolor,backcolor,",
			theme_advanced_buttons3 : ",tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,code",
		});
	</script>
	{/literal}
{/if}
{if $file == 1}
	{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			
			$("#zapisz").click( function() {
				var foto_name = $(".progressName").html();
				$("#nazwaPliku").val(foto_name);
				$("#{/literal}{$field_column}{literal}_field").val(foto_name);
				$("#myFromularz").submit();
			});
					
		});
	</script>
	{/literal}
{/if}