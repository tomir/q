<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
<script type="text/javascript">

	$(document).ready( function() {
		$('A[rel="delete_record"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć rekord i wszystkie możliwe powiązania?");
    		if (answer == 1) {
    			var url;
				var id = $(this).attr('id');
				url = "<?php echo $wwwPanelPatch; ?>pojazdy,delete," + id + "<?php if($atr2 > 1) echo ','.$atr2; ?>.html";
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
			  url: "<?php echo $wwwPanelPatch; ?>pojazdy,activate,"+id+".html",

			  success: function(transport) {
					this_a.find("img").attr({src: transport});
				}
			})
		});


	});
	function MM_jumpMenu(targ,selObj,restore){
			eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
			if (restore) selObj.selectedIndex=0;
		}
</script>
<script type="text/javascript">
	$(document).ready( function() {
		
		$('#car_type').change(function() {
			
			if($(this).val() != 'car' && $(this).val() != 'motorbike') {
				$('#car_model').attr('disabled', true);
			} else {
				$('#car_model').attr('disabled', false);
			}
				
			$.getJSON('http://www.autolicytacje.pl/index.php?action=newsletter&action2=ajax_type&typ='+$(this).val(), function(json) {
 
				$('#car_producer').find('option').remove();
				$('#car_model').find('option').remove();
				
				var select = $('#car_producer');
				$('<option />').attr('value', '').html('-- wybierz --').appendTo(select);
				
				
                $.each(json.producer, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.producer_id)
							.html(v.producer_name)
							.appendTo(select);
                });
		
			});
			
		});
		
		$('#car_producer').live("change", function() {
			
			$.getJSON('http://www.autolicytacje.pl/index.php?action=newsletter&action2=ajax_marka&marka='+$(this).val(), function(json) {
 
				$('#car_model').find('option').remove();
				
				var select = $('#car_model');
				$('<option />').attr('value', '').html('-- wybierz --').appendTo(select);
				
                $.each(json.model, function(k, v) {
					var option = $('<option />');

					option.attr('value', v.model_id)
							.html(v.model_name)
							.appendTo(select);
                });
		
			});
			
		});
	})
</script>
<div style="margin: 0 auto;">
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		Filtrowanie (ilość: <?php echo $list_count; ?>)		
	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<form action="" method="get" name="">
		<table class="table-long" style="width: 730px;">
			<thead>
				<tr>
					<td colspan="3">
						<fieldset>
							<ol>
								<li>
									<label class="field-title">Typ:</label>
									<select id="car_type" name="filtr[car_type]" class="form-select">
										<option value="">-- wybierz --</option>
										<option value="car" <?php if($_GET['filtr']['car_type'] == "car") echo 'selected="selected"'; ?> >Samochody</option>
										<option value="motorbike" <?php if($_GET['filtr']['car_type'] == "motorbike") echo 'selected="selected"'; ?> >Motocykle</option>
										<option value="truck" <?php if($_GET['filtr']['car_type'] == "truck") echo 'selected="selected"'; ?> >Ciężarówki</option>
										<option value="construction" <?php if($_GET['filtr']['car_type'] == "construction") echo 'selected="selected"'; ?> >Maszyny budowlane</option>
										<option value="agro" <?php if($_GET['filtr']['car_type'] == "agro") echo 'selected="selected"'; ?> >Maszyny rolnicze</option>
										
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<ol>
								<li>
									<label class="field-title">Marka:</label>
									<select id="car_producer" name="filtr[car_producer]" class="form-select">
										<option value="">-- wybierz --</option>
										<?php foreach($aAdds['make'] as $row_ma) { ?>
											<option value="<?php echo $row_ma['producer_id']; ?>" <?php if($_GET['filtr']['car_producer'] == $row_ma['producer_id']) echo 'selected="selected"'; ?> ><?php echo $row_ma['producer_name']; ?></option>
										<?php } ?>
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
					<td>
						<fieldset>
							<ol>
								<li> 
									<label class="field-title">Model:</label>
									<select id="car_model" name="filtr[car_model]" class="form-select">
										<option value="">-- wybierz --</option>
										<?php foreach($aAdds['model'] as $row_mo) { ?>
											<option value="<?php echo $row_mo['model_id']; ?>" <?php if($_GET['filtr']['car_model'] == $row_mo['model_id']) echo 'selected="selected"'; ?> ><?php echo $row_mo['model_name']; ?></option>
										<?php } ?>
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
					<td>
						<fieldset>
							<ol>
								<li>
									<label class="field-title">Pochodzenie:</label>
									<select id="pochodzenie" name="filtr[pochodzenie]" class="form-select">
										<option value="">-- wybierz --</option>
										<?php foreach($aAdds['pochodzenie'] as $row_po) { ?>
											<option value="<?php echo $row_po['id_pochodzenie']; ?>" <?php if($_GET['filtr']['pochodzenie'] == $row_po['id_pochodzenie']) echo 'selected="selected"'; ?> ><?php echo $row_po['nazwa']; ?></option>
										<?php } ?>
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<ol>
								<li>
									<label class="field-title">Rocznik od:</label>
									<select id="car_year_od" name="filtr[car_year_od]" class="form-select">
										<option value="">-- rrrr --</option>
										<?php for($i=date('Y');$i>1945;$i--) { ?>
											<option value="<?php echo $i; ?>" <?php if($_GET['filtr']['car_year_od'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
										<?php } ?>
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
					<td colspan="2">
						<fieldset>
							<ol>
								<li>
									<label class="field-title">Rocznik do:</label>
									<select id="car_year_do" name="filtr[car_year_do]" class="form-select">
										<option value="">-- rrrr --</option>
										<?php for($i=date('Y');$i>1945;$i--) { ?>
											<option value="<?php echo $i; ?>" <?php if($_GET['filtr']['car_year_do'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
										<?php } ?>
									</select>
								</li>
							</ol>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: right; padding-right: 7px;"><input type="submit" name="" value="Filtruj" /></td>
				</tr>
			</thead>
		</table>
		</form>
	</div>
	
	
   	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;padding-left: 10px; margin-top:1px;">Lista samochodów</div>
   		<br style="clear: both;" />
   	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<thead>
				<tr>
					<td class="header" colspan="2">Marka</td>
					<td class="header">Model</td>
					<td class="header">Model ręcznie</td>
					<td class="header">Rocznik</td>
					<td class="header">Pochodzenie</td>
					<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
				</tr>
			</thead>
			<?php if(is_array($carList) && count($carList) > 0 && !empty($carList)) { ?>
			<tbody>
				<?php $class = 'odd'; foreach($carList as $row) { ?>
					<tr class="<?php echo $class; ?>">
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php if(is_array($row['photos']) && count($row['photos']) > 0) { ?>
							<img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="/pliki/cars/<?php echo $row['car_id'].'/'.$row['photos'][0]['photo_id'].'_thumb.jpg'; ?>" />
							<?php } ?>
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['producer_name']; ?>
							<?php if($row['kategoria'] != '') echo '<br /><br />'.$row['kategoria']; ?>
							<?php echo '<br />Data zak.: '.$row['sale_date']; ?>
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['model_name']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['car_model']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['car_year']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['pochodzenie']; ?>	
						</td>
						<td style="vertical-align: middle; text-align: center; width: 70px;">
							<a href="<?php echo $wwwPatchPanel.'pojazdy,view,12,'.$row['car_id']; ?>.html" title="Edytuj"><img src="../images/admin/icons/edit.png" alt="Edytuj" style="border: 0;" /></a>
							<?php if($row['id_auction'] == '') { ?><a title="" href="otomoto,edit,0,<?php echo $row['car_id']; ?>.html"><img style="border: 0; width: 30px;" alt="" src="https://www.gletech.com/graphics/icons/11_48x48.png"></a><?php } ?>
							<a href="javascript:void(0);" rel="delete_record" id="<?php echo $row['car_id']; ?>" title="Usuń"><img src="../images/admin/icons/delete.gif" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
						</td>
					</tr>
				<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
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
			<a href="<?php echo $wwwPatchPanel.'car,'.($show_page-1); ?>.html<?php echo $filtr_url; ?>"<?php if($show_page == 1) { ?> style="Visibility:Hidden;"<?php } ?>>&laquo;</a>
			<?php
			if($pages_limit > $show_pages)
				$pages_limit = $show_pages+1;
			if($start_limit <= 0)
				$start_limit = 1;

		for($i = $start_limit;$i<$pages_limit; $i++) { ?>
			<?php echo '<a '; if($show_page == $i) { echo ' class="active" '; } echo 'href="'.$wwwPatchPanel.'car,'.$i.'.html'.$filtr_url.'">'.$i.'</a></td>'; ?>
		<?php } ?>
		<?php if($show_page != $show_pages) { ?><a href="<?php echo $wwwPatchPanel.'car,'.($show_page+1); ?>.html<?php echo $filtr_url; ?>">&raquo;</a><?php } ?>
		</div>
		<div style="clear:both;"></div>
		<?php } ?>
	</div>

	
	
</div>