<div id="bg">
	<div id="content">
		
		<div id="leftbox">
			
		</div> <!-- end leftbox -->
		<div id="rightbox">
			<h1 class="naglowek">Edycja pola dla kolumny <?php echo $aPole[0]['column_s']; ?></h1>
	      	<form name="form1" method="post" action="<?php echo $tffPatch; ?>pole,edytuj,<?php echo $atr3 ?>.html">
	      		<table class="header" cellspacing="1" cellpadding="0">
	      			<tr>
	      				<td class="header right">Nazwa pola:</td>
	      				<td><input type="text" name="<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['nazwa_pola']; ?>" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Typ pola:</td>
	      				<td>
	      					<select name="pole_<?php echo $aPole[0]['column_s']; ?>">
      							<option <?php if($aPole[0]['typ_pola'] == "checkbox") echo 'selected="selected"'; ?> value="checkbox">checkbox</option>
      							<option <?php if($aPole[0]['typ_pola'] == "password") echo 'selected="selected"'; ?> value="password">hasło</option>
      							<option <?php if($aPole[0]['typ_pola'] == "file") echo 'selected="selected"'; ?> value="file">plik</option>
      							<option <?php if($aPole[0]['typ_pola'] == "select") echo 'selected="selected"'; ?> value="select">lista pojedynczego wyboru</option>
      							<option <?php if($aPole[0]['typ_pola'] == "multiple") echo 'selected="selected"'; ?> value="multiple">lista wielokrotnego wyboru</option>
      							<option <?php if($aPole[0]['typ_pola'] == "text") echo 'selected="selected"'; ?> value="text">text</option>
      							<option <?php if($aPole[0]['typ_pola'] == "textarea") echo 'selected="selected"'; ?> value="textarea">textarea</option>
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wymagane:</td>
	      				<td><input type="checkbox" name="wymagane_<?php echo $aPole[0]['column_s']; ?>" <?php if($pole[0].wymagane == 1) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Powiązanie:</td>
	      				<td></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Akcje:</td>
	      				<td>
	      					<select multiple="multiple" name="akcje_<?php echo $aPole[0]['column_s']; ?>[]">
      							<option <?php if($aPole[0]['akcje']['ograniczenie'] == 1) echo 'selected="selected"'; ?> value="ograniczenie">ograniczona ilość znaków</option>
      							<option <?php if($aPole[0]['akcje']['data'] == 1) echo 'selected="selected"'; ?> value="data">poprawność daty</option>
      							<option <?php if($aPole[0]['akcje']['kalendarz'] == 1) echo 'selected="selected"'; ?> value="kalendarz">kalendarz</option>
      							<option <?php if($aPole[0]['akcje']['kod_pocztowy'] == 1) echo 'selected="selected"'; ?> value="kod_pocztowy">poprawność kodu pocztowego</option>
      							<option <?php if($aPole[0]['akcje']['visible'] == 1) echo 'selected="selected"'; ?> value="visible">sterowanie publikacją</option>
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Styl:</td>
	      				<td>
	      					<textarea style="width: 300px; height: 80px" name="styl_<?php echo $aPole[0]['column_s']; ?>"><?php echo $aPole[0]['styl']; ?></textarea>
	      				</td>
	      			</tr>
	      			<?php   //zabieramy sie za sprawdzanie rodzaju pola
                                        if( $aPole[0]['typ_pola'] == "text") { ?>
	      			<tr>
	      				<td class="header right">Liczba znaków min:</td>
	      				<td><input type="text" style="width: 30px;" value="<?php echo $aPole[0]['text_ilosc_min']; ?>" name="text_ilosc_min_<?php echo $aPole[0]['column_s']; ?>" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Liczba znaków max:</td>
	      				<td><input type="text" style="width: 30px;" value="<?php echo $aPole[0]['text_ilosc_max']; ?>" name="text_ilosc_max_<?php echo $aPole[0]['column_s']; ?>" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Długość pola:</td>
	      				<td><input type="text" style="width: 30px;" value="<?php echo $aPole[0]['text_dlugosc']; ?>" name="text_dlugosc_<?php echo $aPole[0]['column_s']; ?>" /> px</td>
	      			</tr>
	      			<?php } elseif($aPole[0]['typ_pola'] == 'file') { ?>
	      			<tr>
	      				<td class="header right">Akceptowane rozszerzenia:</td>
	      				<td>
	      					<select multiple="multiple" name="rozszerzenia_<?php echo $aPole[0]['column_s']; ?>[]">

                                                    <?php foreach($aRozszerzenia as $row_r) { ?>
      								<option <?php if($aPole[0]['file_rozszerzenia'][$row_r['nazwa']] == 1) echo 'selected="selected"'; ?> value="<?php echo $row_r['nazwa']; ?>"><?php echo $row_r['nazwa']; ?></option>
                                                    <?php } ?>
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Ścieżka zapisu:</td>
	      				<td><input style="width: 300px;" type="text" name="sciezka_<?php echo $aPole[0]['column_s']; ?>" value="<?php if($aPole[0]['file_miejsce'] == '') echo $serverPatch; else $aPole[0]['file_miejsce']; ?>" /></td>
	      			</tr>
	      			<tr><td class="header" colspan="2" style="background: #ffffff;">Jeśli ładowany plik jest obrazkiem</td></tr>
	      			<tr>
	      				<td class="header right">Konwertuj do:</td>
	      				<td>
	      					<select name="image_main_roz_<?php echo $aPole[0]['column_s']; ?>">
	      						<option value=""></option>
	      						<?php foreach($aRozszerzenia as $row_r) { ?>
      								<option <?php if($aPole[0]['image_main_roz'] == $row_r['nazwa']) echo 'selected="selected"'; ?> value="<?php echo $row_r['nazwa']; ?>"><?php echo $row_r['nazwa']; ?></option>
      							<?php } ?>
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Szerokość:</td>
	      				<td><input style="width: 30px;" type="text" name="image_scalex_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['image_scalex']; ?>" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wysokość:</td>
	      				<td><input style="width: 30px;" type="text" name="image_scaley_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['image_scaley']; ?>" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Thumb szerokość:</td>
	      				<td><input style="width: 30px;" type="text" name="thumb_scalex_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['thumb_scalex']; ?>" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Thumb wysokość:</td>
	      				<td><input style="width: 30px;" type="text" name="thumb_scaley_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['thumb_scaley']; ?>" /> px</td>
	      			</tr>
	      			<?php } elseif($aPole[0]['typ_pola'] == 'textarea') { ?>
	      			<tr>
	      				<td class="header right">Szerokość pola:</td>
	      				<td><input style="width: 30px;" type="text" name="textarea_x_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['textarea_x']; ?>" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wysokość pola:</td>
	      				<td><input style="width: 30px;" type="text" name="textarea_y_<?php echo $aPole[0]['column_s']; ?>" value="<?php echo $aPole[0]['textarea_y']; ?>" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Użyj TinyMc:</td>
	      				<td><input type="checkbox" name="textarea_tinymc_<?php echo $aPole[0]['column_s']; ?>" <?php if($aPole[0]['textarea_tinymc']) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<?php } ?>
	      			<tr>
	      				<td class="header right">Pokaż jako kolumnę:</td>
	      				<td><input type="checkbox" name="kolumna_<?php echo $aPole[0]['column_s']; ?>" <?php if($aPole[0]['column_show']) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Środkuj:</td>
	      				<td><input type="checkbox" name="wysrodkowana_<?php echo $aPole[0]['column_s']; ?>" <?php if($aPole[0]['wysrodkowana']) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">W użyciu:</td>
	      				<td><input type="checkbox" name="active_<?php echo $aPole[0]['column_s']; ?>" <?php if($aPole[0]['active']) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Klucz obcy:</td>
	      				<td><input type="checkbox" name="klucz_zew_<?php echo $aPole[0]['column_s']; ?>" <?php if($aPole[0]['klucz_zew']) echo 'checked="checked"'; ?> value="1" /></td>
	      			</tr>
	      			<tr><td colspan="2" class="right"><input type="hidden" value="<?php echo $aPole[0]['column_s']; ?>" name="field_name" /><input type="submit" name="zapisz" value="Edytuj" /></td></tr>
	      		</table>
	      	</form>
		</div>
		
	</div> <!-- end content -->
<div id="bgcontentbottom"></div>
</div> <!-- end bg -->
