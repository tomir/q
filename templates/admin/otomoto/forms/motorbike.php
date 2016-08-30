<form name="form_otomoto" id="form_otomoto" method="post" action="">
	<table class="table-long">
		<tr>
			<td style="width: 20%"><span style="color: red; font-weight: bold;">Wybierz typ ogłoszenia:</span></td>
			<td colspan="4">
				<select name="formData[type]" class="car_type">
					<option value="CAR">Samochody</option>
					<option value="MOTORBIKE"  selected="selected">Motocykle</option>
					<option value="TRUCK">Ciężarówki</option>
					<option value="CONSTRUCTION">Maszyny budowlane</option>
					<option value="AGRO">Maszyny rolnicze</option>
				</select>
			</td>
		</tr>
	</table>

	<input type="hidden" name="formData[otomoto_id]" value="<?php echo $aData['otomoto_id']; ?>" />
	<input type="hidden" name="formData[id]" value="<?php echo $aData['id']; ?>" />
	<input type="hidden" name="formData[car_id]" value="<?php echo $atr4; ?>" />
	<input type="hidden" class="formAction" name="form_action" value="wystaw" />

	<table class="table-long">
		<tbody>
			<tr>
				<td style="border-top: 0;">
					<table style="width: 720px; margin-bottom: 15px;">
						<tr>
							<td colspan="3" style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: left; width: 250px;">
								<span style="color: #848E92;">Zobacz oryginał aukcji w serwisie aukcyjnym - <a target="_blank" href="<?php echo $aCar['car_link']; ?>">tutaj</a>.</span>
							</td>
							<td colspan="3" style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92;">Data zakończenia - <?php echo $aCar['sale_date']; ?> <?php echo $aCar['sale_time']; ?></span>
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Marka*</span>:
							</td>
							<td>
								<select name="formData[make-id]" style="width: 125px;" class="form_make_id">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['make'] as $row_ma) { ?>
										<option value="<?php echo $row_ma['otomoto_id']; ?>" <?php if(($row_ma['producer_id'] == $aCar['producer_id'] && !isset($aData)) || ($aData['make-id'] == $row_ma['otomoto_id'])) echo 'selected="selected"'; ?> ><?php echo $row_ma['producer_name']; ?></option>
									<?php } ?>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Model*</span>:
							</td>
							<td>
								<select name="formData[model-id]" style="width: 125px;" class="form_model_id">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['model'] as $row_mo) { ?>
										<option value="<?php echo $row_mo['otomoto_id']; ?>" <?php if(($row_mo['model_id'] == $aCar['model_id'] && !isset($aData)) || ($aData['model-id'] == $row_mo['otomoto_id'])) echo 'selected="selected"'; ?> ><?php echo $row_mo['model_name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Model - dod. inf.</span>:
							</td>
							<td>
								<input type="text" style="width: 100px" name="formData[model-name-extension]" value="<?php if(!isset($aData['model-name-extension'])) echo mb_strtolower($aCar['car_model'], 'utf8'); else echo mb_strtolower($aData['model-name-extension'], 'utf8'); ?>" />
							</td>
							
						</tr>
						<tr>
							<td style="vertical-align: top; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Wersja</span>:
							</td>
							<td style="vertical-align: top;">
								<select name="formData[version-id]" class="form_version_id">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['version'] as $row_ve) { ?>
										<option value="<?php echo $row_ve['otomoto_id']; ?>" <?php if(($row_ve['version_id'] == $aCar['version_id'] && !isset($aData)) || ($aData['version-id'] == $row_ve['otomoto_id'])) echo 'selected="selected"'; ?> ><?php echo $row_ve['version_name']; ?></option>
									<?php } ?>
								</select>
							</td>
							
							<td style="vertical-align: top; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Przebieg</span>:
							</td>
							<td style="vertical-align: top;">
								<input type="text" style="width: 100px;" name="formData[odometer]" value="<?php if(!isset($aData['odometer'])) { echo ($aCar['car_mileage'] > 0 ? $aCar['car_mileage'] : 1); } else { echo ($aData['odometer'] > 0 ? $aData['odometer'] : 1); }?>" />
							</td>
							
							<td style="vertical-align: top; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Kategoria Allegro*</span>:
							</td>
							<td>
								<select size="15" style="width: 220px;" name="formData[allegro-cat-id]" class="allegro_id">

									<?php foreach($aAddsMoto['allegro'] as $row_al) { ?>
										<option class="<?php echo $row_al['parent_id']; ?>" value="<?php echo $row_al['otomoto_id']; ?>" <?php if($aData['allegro-cat-id'] == $row_al['otomoto_id']) echo 'selected="selected"'; ?>><?php echo $row_al['name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Cena*</span>:
							</td>
							<td>
								<input type="text"  style="width: 50px" name="formData[price]" value="<?php if(!isset($aData['price'])) echo $aCar['car_price']; else echo $aData['price']; ?>" />
							</td>
							
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Moc</span>:
							</td>
							<td>
								<input type="text" style="width: 50px;" name="formData[power]" value="<?php if(!isset($aData['power'])) echo $aCar['car_power']; else echo $aData['power']; ?>" />
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Model</span>:
							</td>
							<td>
								<input type="text" name="formData[model-name]" value="<?php echo $aData['model-name']; ?>" />
							</td>
							
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Rok*</span>:
							</td>
							<td>
								<select name="formData[build-year]">
									<option value="0">-- rrrr --</option>
									<?php for($i=date('Y');$i>1970;$i--) { ?>
										<option value="<?php echo $i; ?>" <?php if(($aCar['car_year'] == $i && !isset($aData)) || ($aData['build-year'] == $i)) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Pojemność silnika (cm)</span>:
							</td>
							<td  colspan="3">
								<input type="text" style="width: 50px;" name="formData[cubic-capacity]" value="<?php if(!isset($aData['cubic-capacity'])) echo $aCar['car_capacity']; else echo $aData['cubic-capacity']; ?>" />
							</td>

						</tr>
						<tr>
							
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Nadwozie*</span>:
							</td>
							<td>
								<select name="formData[vehicle-category-key]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['body'] as $row_bo) { ?>
										<option value="<?php echo $row_bo['otomoto_key']; ?>" <?php if(($aCar['body_list_id'] == $row_bo['body_id'] && !isset($aData)) || ($aData['vehicle-category-key'] == $row_bo['otomoto_key'])) echo 'selected="selected"'; ?>><?php echo $row_bo['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Paliwo</span>:
							</td>
							<td colspan="3">
								<select name="formData[fuel-type-key]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['fuel'] as $row_f) { ?>
										<option value="<?php echo $row_f['otomoto_key']; ?>" <?php if(($aCar['fuel_id'] == $row_f['fuel_id'] && !isset($aData)) || ($aData['fuel-type-key'] == $row_f['otomoto_key'])) echo 'selected="selected"'; ?>><?php echo $row_f['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							
						</tr>
						
						<tr>
							<td style="text-align: left; height: 80px; padding-top: 10px" colspan="6">
								<button style="cursor: pointer; padding: 5px 12px; margin: 4px;" onclick="$('.formAction').val('save'); $('#form_otomoto').submit();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="test-api-connection" role="button" aria-disabled="false">Zapisz</button>
								<input style="cursor: pointer; margin: 4px;" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" name="zapisz" value="<?php if($aData['otomoto_id'] != '') echo 'Edytuj aukcję'; else echo 'Wystaw aukcję'; ?>" style="margin:5px"/>
							</td>
						</tr>
						<tr>
							<td style="border-top: none;"  colspan="6">
								<h1 style="width: 720px; padding-bottom: 3px; border-bottom: 1px solid #DFDFDF; font-size: 14px; font-weight: bold;">Zdjęcia</h1>
								<table style="width: 720px; margin-bottom: 20px;">
									<?php $i=0; foreach($aCar['photo'] as $row_p) { ?>
									<?php if($i == 0) { ?><tr><?php } ?>
									<?php if(is_int($i/3)) { ?></tr><tr><?php } ?>
											<td style="border-top: 0; width: 33%; vertical-align: top; padding: 5px 0;">
											<div style="float: left;"><input type="checkbox" id="<?php echo $row_p['photo_id']; ?>_photo" name="formData[photos][<?php echo $i; ?>]" value="<?php echo $row_p['photo_id']; ?>" checked="checked" /><input type="hidden" name="formData[photo_files][<?php echo $row_p['photo_id']; ?>]" value="pliki/cars/<?php echo $atr4.'/'.$row_p['photo_id'].'.'.$row_p['photo_filename']; ?>" /></div>
											<div style="float: left; margin-left: 5px; margin-top: 3px; width: 200px;"><label style="cursor: pointer;" for="<?php echo $row_p['photo_id']; ?>_photo"><img style="margin: 0px; width: 120px; height:80px; padding: 5px 0 0 5px;" src="/pliki/cars/<?php echo $atr4.'/'.$row_p['photo_id'].'_thumb.'.$row_p['photo_filename']; ?>" /></label></div>
										</td>
									<?php $i++; } ?>
									</tr>   
								</table>
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Opis</span>:
							</td>
							<td colspan="5">
								<textarea name="formData[description]" style="width:480px; height: 180px;"><?php if(!isset($aData['description'])) { 
												echo preg_replace('#<br\s*/?>#', "\n", $aCar['opis']);; 
											} else {
												echo $aData['description']; 
											}
									?>
								</textarea>
							</td>

						</tr>
			
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								
							</td>
							<td>
								
								
							</td>
							
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Na raty</span>:
							</td>
							<td>
								<input type="checkbox" name="formData[credit-ability]" value="1" <?php if($aData['credit-ability'] == 1) echo 'checked="checked"'; ?> />
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Do negocjacji</span>:
							</td>
							<td>
								<input type="checkbox" name="formData[price-negotiable]" value="1" <?php if($aData['price-negotiable'] == 1) echo 'checked="checked"'; ?> />
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Leasing</span>:
							</td>
							<td>
								<input type="checkbox" name="formData[leasing]" value="1" <?php if($aData['leasing'] == 1) echo 'checked="checked"'; ?>  />
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Faktura VAT</span>:
							</td>
							<td>
								<input type="checkbox" name="formData[invoice-available]" value="1" <?php if($aData['invoice-available'] == 1) echo 'checked="checked"'; ?> />
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Waluta</span>:
							</td>
							<td>
								<select name="formData[price-currency]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['country'] as $row_co) { ?>
										<option value="<?php echo $row_co['otomoto_key']; ?>" <?php if(($row_co['otomoto_key'] == 'PL' && !isset($aData)) || ($aData['price-currency'] == $row_co['otomoto_key'])) echo 'selected="selected"'; ?> ><?php echo $row_co['name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Data przeglądu</span>:
							</td>
							<td>
								<select name="formData[tech-check-year]">
									<option value="0">-- rrrr --</option>
									<?php for($i=2020;$i>1980;$i--) { ?>
										<option value="<?php echo $i; ?>" <?php if($aData['tech-check-year'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
								<select name="formData[tech-check-month]">
									<option value="0">-- mm --</option>
									<?php for($i=1;$i<13;$i++) { ?>
										<option value="0<?php echo $i; ?>" <?php if($aData['tech-check-month'] == '0'.$i) echo 'selected="selected"'; ?>>0<?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Wersja (jeśli brak)</span>:
							</td>
							<td>
								<input type="text" style="width: 50px;" name="formData[version-name]" value="<?php echo $aData['version-name']; ?>" />
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Data ważności ubezpieczenia</span>:
							</td>
							<td>
								<select name="formData[insurance-year]">
									<option value="0">-- rrrr --</option>
									<?php for($i=2020;$i>1980;$i--) { ?>
										<option value="<?php echo $i; ?>" <?php if($aData['insurance-year'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
								<select name="formData[insurance-month]">
									<option value="0">-- mm --</option>
									<?php for($i=1;$i<13;$i++) { ?>
										<option value="0<?php echo $i; ?>" <?php if($aData['insurance-month'] == '0'.$i) echo 'selected="selected"'; ?>>0<?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Data pierwszej rejestracji</span>:
							</td>
							<td>
								<select name="formData[first-registration-year]">
									<option value="0">-- rrrr --</option>
									<?php for($i=2020;$i>1980;$i--) { ?>
										<option value="<?php echo $i; ?>" <?php if($aData['first-registration-year'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
								<select name="formData[first-registration-month]">
									<option value="0">-- mm --</option>
									<?php for($i=1;$i<13;$i++) { ?>
										<option value="0<?php echo $i; ?>" <?php if($aData['first-registration-month'] == '0'.$i) echo 'selected="selected"'; ?>>0<?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Jednostka mocy</span>:
							</td>
							<td>
								<select name="formData[power-unit]">
									<option value="">-- wybierz --</option>
									<option value="kW" <?php if($aData['power-unit'] == 'kW') echo 'selected="selected"'; ?>>KW</option>
									<option value="PS" <?php if($aData['power-unit'] == 'PS' || $aData['power-unit'] == '' || !isset($aData['power-unit'])) echo 'selected="selected"'; ?>>KM</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Skrzynia biegów</span>:
							</td>
							<td>
								<select name="formData[gearbox-type-key]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['gearbox'] as $row_g) { ?>
										<option value="<?php echo $row_g['otomoto_key']; ?>" <?php if(($aCar['gearbox_id'] == $row_g['gearbox_id'] && !isset($aData)) || ($aData['gearbox-type-key'] == $row_g['otomoto_key'])) echo 'selected="selected"'; ?>><?php echo $row_g['name']; ?></option>
									<?php } ?>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Kolor</span>:
							</td>
							<td>
								<select name="formData[colour-key]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['colour'] as $row_col) { ?>
										<option value="<?php echo $row_col['otomoto_key']; ?>" <?php if(($aCar['colour_id'] == $row_col['colour_id'] && !isset($aData)) || ($aData['colour-key'] == $row_col['otomoto_key'])) echo 'selected="selected"'; ?>><?php echo $row_col['name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">

							</td>
							<td>

							</td>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Metalic</span>:
							</td>

							<td>
								<select name="formData[colour-metallic]">
									<option value="">-- wybierz --</option>
									<option value="y" <?php if($aData['colour-metallic'] == 'y') echo 'selected="selected"'; ?>>tak</option>
									<option value="n" <?php if($aData['colour-metallic'] == 'n') echo 'selected="selected"'; ?>>nie</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Ilość drzwi</span>:
							</td>
							<td>
								<select name="formData[doors-key]">
									<option value="">-- wybierz --</option>
									<option value="2/3" <?php if(($aCar['car_doors_key'] == '2/3' && !isset($aData)) || ($aData['doors-key'] == '2/3')) echo 'selected="selected"'; ?>>2/3</option>
									<option value="4/5" <?php if(($aCar['car_doors_key'] == '4/5' && !isset($aData)) || ($aData['doors-key'] == '4/5')) echo 'selected="selected"'; ?>>4/5</option>
								</select>
							</td>

							
						</tr>
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: red; font-weight: bold;">Stan techniczny*</span>:
							</td>
							<td>
								<select name="formData[tech-condition]">
									<option value="new" <?php if($aData['tech-condition'] == 'new') echo 'selected="selected"'; ?>>nowy</option>
									<option value="undamaged" <?php if($aData['tech-condition'] == 'undamaged') echo 'selected="selected"'; ?>>używany</option>
									<option value="damaged" <?php if($aData['tech-condition'] == 'damaged' || !isset($aData['tech-condition'])) echo 'selected="selected"'; ?> >uszkodzony</option>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Uszkodzony</span>:
							</td>
							<td>
								<select name="formData[damaged]">
									<option value="t" <?php if($aData['damaged'] == 't') echo 'selected="selected"'; ?>>tak</option>
									<option value="n" <?php if($aData['damaged'] == 'n') echo 'selected="selected"'; ?>>nie</option>
								</select>
							</td>
						</tr>
						<tr>
							

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Status pojazdu</span>:
							</td>
							<td>
								<select name="formData[origin-status]">
									<option value="1" <?php if($aData['origin-status'] == 1) echo 'selected="selected"'; ?>>do sprowadzenia</option>
									<option value="2" <?php if($aData['origin-status'] == 2) echo 'selected="selected"'; ?>>sprowadz./nieopł.</option>
									<option value="3" <?php if($aData['origin-status'] == 3) echo 'selected="selected"'; ?>>przyg. do rej./opłac.</option>
									<option value="4" <?php if($aData['origin-status'] == 4) echo 'selected="selected"'; ?>>sprowadz./zarej.</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Kraj pochodzenia</span>:
							</td>
							<td>
								<select name="formData[origin-country]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['country'] as $row_co) { ?>
										<option value="<?php echo $row_co['otomoto_key']; ?>" <?php if(($row_co['otomoto_key'] == $aCar['symbol_kraju'] && !isset($aData)) || ($aData['registration-country'] == $row_co['otomoto_key'])) echo 'selected="selected"'; ?> ><?php echo $row_co['name']; ?></option>
									<?php } ?>
								</select>
							</td>

							<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px; text-align: right; width: 150px;">
								<span style="color: #848E92; font-weight: bold;">Kraj rejestracji</span>:
							</td>
							<td>
								<select name="formData[registration-country]">
									<option value="0">-- wybierz --</option>
									<?php foreach($aAddsMoto['country'] as $row_co) { ?>
										<option value="<?php echo $row_co['otomoto_key']; ?>" <?php if(($row_co['otomoto_key'] == $aCar['symbol_kraju'] && !isset($aData)) || ($aData['registration-country'] == $row_co['otomoto_key'])) echo 'selected="selected"'; ?> ><?php echo $row_co['name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top: none;">
					<h1 style="width: 720px; padding-bottom: 3px; border-bottom: 1px solid #DFDFDF; font-size: 14px; font-weight: bold;">Informacje dodatkowe</h1>
					<table style="width: 720px; margin-bottom: 20px;">
						<?php $i=0; foreach($aAddsMoto['extras'] as $row_i) { ?>
						<?php if($i == 0) { ?><tr><?php } ?>
						<?php if(is_int($i/3)) { ?></tr><tr><?php } ?>
							<td style="border-top: 0; width: 33%; vertical-align: top; padding: 5px 0;">
								<div style="float: left;"><input type="checkbox" id="<?php echo $row_i['otomoto_key']; ?>_features" name="formData[features][]" <?php if($aCar['info'][$row_i['info_id']]['info_id'] > 0 || $aData['info'][$row_i['info_id']]['info_id'] > 0) echo 'checked="checked"'; ?> value="<?php echo $row_i['otomoto_key']; ?>" /></div>
								<div style="float: left; margin-left: 5px; margin-top: 3px; width: 200px;"><label style="cursor: pointer;" for="<?php echo $row_i['otomoto_key']; ?>_features"><?php echo $row_i['info_name']; ?></label></div>
							</td>
						<?php $i++; } ?>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top: none;">
					<h1 style="width: 720px; padding-bottom: 3px; border-bottom: 1px solid #DFDFDF; font-size: 14px; font-weight: bold;">Wyposażenie</h1>
					<table style="width: 720px; margin-bottom: 20px;">
						<?php $i=0; foreach($aAddsMoto['features'] as $row_e) { ?>
						<?php if($i == 0) { ?><tr><?php } ?>
						<?php if(is_int($i/3)) { ?></tr><tr><?php } ?>
							<td style="border-top: 0; width: 33%; vertical-align: top; padding: 5px 0;">
								<div style="float: left;"><input id="<?php echo $row_e['otomoto_key']; ?>_extras" type="checkbox" name="formData[extras][]" <?php if($aCar['equipment'][$row_e['equipment_id']]['equipment_id'] > 0 || $aData['equipment'][$row_e['equipment_id']]['equipment_id'] > 0) echo 'checked="checked"'; ?> value="<?php echo $row_e['otomoto_key']; ?>" /></div>
								<div style="float: left; margin-left: 5px; margin-top: 3px; width: 200px;"><label style="cursor: pointer;" for="<?php echo $row_e['otomoto_key']; ?>_extras"><?php echo $row_e['equipment_name']; ?></label></div>
							</td>
						<?php $i++; } ?>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</form>