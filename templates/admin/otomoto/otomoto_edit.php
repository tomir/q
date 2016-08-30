<style type="text/css">
	.ui-state-highlight { height: 92px; width: 122px;}
	
	button.ui-state-default {
		background: url("../images/admin/button_highlight.png") repeat-x scroll 0 0 #6D859C;
		border-color: #8E9CAA #66798C #5A6E83 #7B8C9C;
		border-style: solid;
		border-width: 1px;
		color: #FFFFFF;
	}
	
	button.ui-state-hover {
		background: url("../images/admin/button_highlight_selected.png") repeat-x scroll 0 0 #69909E;
		border-color: #8CA2AB #537481 #456977 #527480;
		border-style: solid;
		border-width: 1px;
		color: #FFFFFF;
	}
	
	input.ui-state-default {
		background: url("../images/admin/button.png") repeat-x scroll 0 0 #E5E3E3;
		border-color: #DDDDDD #DDDDDD #C6C6C6 #C6C6C6;
		border-style: solid;
		border-width: 1px;
		color: #515151;
		margin: 0;
		outline: medium none;
		padding: 6px 12px;
	}
	
	input.ui-state-hover {
		background: url("../images/admin/button_selected.png") repeat-x scroll 0 0 #B4B4B4;
		border-color: #CCCCCC #B1B1B1 #AFAFAF #BEBEBE;
		border-style: solid;
		border-width: 1px;
		color: #515151;
		margin: 0;
		outline: medium none;
		padding: 6px 12px;
	}
	
	#header {margin-top: -130px;}
	
</style>
<script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	} 

	$(document).ready( function() {
		$("input:submit, input:reset, button").button();
		
		$('.form_make_id').change(function() {
			
			var type = $('.car_type option:selected').val();
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_marka&marka='+$(this).val()+'&type='+type, function(json) {
 
				$('.form_model_id').find('option').remove();
				
				var select = $('.form_model_id');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.model, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.otomoto_id)
							.html(v.model_name)
							.appendTo(select);
                });
				
				$('.form_version_id').find('option').remove();
				
				var select = $('.form_version_id');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.version, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.otomoto_id)
							.html(v.version_name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.form_model_id').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_model&marka='+$('.form_make_id option:selected').val()+'&model='+$(this).val(), function(json) {
				
				$('.form_version_id').find('option').remove();
				
				var select = $('.form_version_id');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.version, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.otomoto_id)
							.html(v.version_name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-category-key').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$(this).val(), function(json) {
				
				$('.vehicle-type').find('option').remove();
				
				var select = $('.vehicle-type');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-category-key2').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$(this).val(), function(json) {
				
				$('.vehicle-type2').find('option').remove();
				
				var select = $('.vehicle-type2');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-type').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp2&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$('.vehicle-category-key option:selected').val()+'&type='+$(this).val(), function(json) {
				
				$('.vehicle-subtype1').find('option').remove();
				
				var select = $('.vehicle-subtype1');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-type2').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp2&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$('.vehicle-category-key2 option:selected').val()+'&type='+$(this).val(), function(json) {
				
				$('.vehicle-subtype1').find('option').remove();
				
				var select = $('.vehicle-subtype1');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-subtype1').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp3&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$('.vehicle-category-key2 option:selected').val()+'&type='+$('.vehicle-type2').val()+'&subtype='+$(this).val(), function(json) {
				
				$('.vehicle-subtype2').find('option').remove();
				
				var select = $('.vehicle-subtype2');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.vehicle-subtype2').change(function() {
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_podtyp4&kategoria=<?php if($aData['type'] != '') echo $aData['type']; else echo $aCar['car_type']; ?>&nadwozie='+$('.vehicle-category-key2 option:selected').val()+'&type='+$('.vehicle-type2').val()+'&subtype='+$('.vehicle-subtype1').val()+'&subtype2='+$(this).val(), function(json) {
				
				$('.vehicle-subtype3').find('option').remove();
				
				var select = $('.vehicle-subtype3');
				$('<option />').attr('value', 0).html('-- wybierz --').appendTo(select)
				
                $.each(json.key, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.key)
							.html(v.name)
							.appendTo(select);
                });
			});
			
		});
		
		$('.allegro_id').live("change", function() {
		
			var id = $(this).val();
			
			$.getJSON('/admin/index.php?action=otomoto&action2=ajax_allegro&parent_id='+id, function(json) {
				
				if (json.length > 1) {

					var nazwa = "..wstecz";
					var first = $('.allegro_id option:selected').attr('class');

						
					$('.allegro_id').find('option').remove();

					var select = $('.allegro_id');
					if(id != 3) {
						$('<option />').attr('value', first).html(nazwa).appendTo(select)
					}
					$.each(json, function(k, v) {
						var option = $('<option />');

						option.attr('value', v.otomoto_id)
								.attr('class', v.parent_id)
								.html(v.name)
								.appendTo(select);
					});
				}
			});
			
		});
		
		$('.car_type').live("change", function() {
			
				var id = $(this).val();
				$(this).filter('option:selected').attr('selected', false);
				$('.box-form').hide();
				$('#' + id + '_box').show();

				if(id == 'CAR' || id == 'MOTORBIKE') {
					$(".form_model_id").attr('disabled', false);
					$(".form_version_id").attr('disabled', false);
				} else {
					$(".form_model_id").attr('selected', false);
					$(".form_model_id").attr('disabled', true);
					$(".model-name-extension").attr('disabled', true);
					$(".model-name-extension").val('');
					$(".form_version_id").attr('disabled', true);
				}
			});

		<?php if($aData['type'] == 'CAR' || $aCar['car_type'] == 'CAR' || $aData['type'] == 'MOTORBIKE' || $aCar['car_type'] == 'MOTORBIKE') { ?>
			$(".form_model_id").attr('disabled', false);
			$(".form_version_id").attr('disabled', false);
		<?php } else { ?>
			$(".form_model_id").attr('selected', false);
			$(".form_model_id").attr('disabled', true);
			$(".model-name-extension").attr('disabled', true);
			$(".model-name-extension").val('');
			$(".form_version_id").attr('disabled', true);
		<?php } ?>
		
	});
</script>

<div style="margin: 0 auto;">
    <h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		<div style="float: left;margin-top:1px;"><?php if($akcja3 > 0) { ?>Edytuj aukcję<?php } else { ?>Dodaj nową aukcję<?php } ?>  </div>
		<br style="clear: both;" />
    </h4>
    <div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<div id="CAR_box" class="box-form" <?php if($aData['type'] != 'CAR' && $aCar['car_type'] != 'CAR') { ?>style="display: none;"<?php } ?>>
			<?php include(MyConfig::getValue("templatePatch").'admin/otomoto/forms/car.php'); ?>
		</div>
		<div id="MOTORBIKE_box" <?php if($aData['type'] != 'MOTORBIKE' && $aCar['car_type'] != 'MOTORBIKE') { ?>style="display: none;"<?php } ?> class="box-form">
			<?php include(MyConfig::getValue("templatePatch").'admin/otomoto/forms/motorbike.php'); ?>
		</div>
		<div id="TRUCK_box" <?php if($aData['type'] != 'TRUCK' && $aCar['car_type'] != 'TRUCK') { ?>style="display: none;"<?php } ?> class="box-form">
			<?php include(MyConfig::getValue("templatePatch").'admin/otomoto/forms/truck.php'); ?>
		</div>
		<div id="CONSTRUCTION_box" <?php if($aData['type'] != 'CONSTRUCTION' && $aCar['car_type'] != 'CONSTRUCTION') { ?>style="display: none;"<?php } ?> class="box-form">
			<?php include(MyConfig::getValue("templatePatch").'admin/otomoto/forms/construction.php'); ?>
		</div>
		<div id="AGRO_box" <?php if($aData['type'] != 'AGRO' && $aCar['car_type'] != 'AGRO') { ?>style="display: none;"<?php } ?> class="box-form">
			<?php include(MyConfig::getValue("templatePatch").'admin/otomoto/forms/agro.php'); ?>
		</div>
    </div>
</div>
						