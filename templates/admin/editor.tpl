<div id="bg">
	<div id="content">
		
		<div id="leftbox">
			
		</div> <!-- end leftbox -->
		<div id="rightbox">
			<h1 class="naglowek">Edycja pola dla kolumny '{$pole[0].column_s}'</h1>
	      	<form name="form1" method="post" action="{$tffPatch}pole,edytuj,{$field_id}.html">
	      		<table class="header" cellspacing="1" cellpadding="0">
	      			<tr>
	      				<td class="header right">Nazwa pola:</td>
	      				<td><input type="text" name="{$pole[0].column_s}" value="{$pole[0].nazwa_pola}" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Typ pola:</td>
	      				<td>
	      					<select name="pole_{$pole[0].column_s}">
      							<option {if $pole[0].typ_pola == "checkbox"}selected="selected"{/if} value="checkbox">checkbox</option>
      							<option {if $pole[0].typ_pola == "password"}selected="selected"{/if} value="password">hasło</option>
      							<option {if $pole[0].typ_pola == "file"}selected="selected"{/if} value="file">plik</option>
      							<option {if $pole[0].typ_pola == "select"}selected="selected"{/if} value="select">lista pojedynczego wyboru</option>
      							<option {if $pole[0].typ_pola == "multiple"}selected="selected"{/if} value="multiple">lista wielokrotnego wyboru</option>
      							<option {if $pole[0].typ_pola == "text"}selected="selected"{/if} value="text">text</option>
      							<option {if $pole[0].typ_pola == "textarea"}selected="selected"{/if} value="textarea">textarea</option>
	      					</section>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wymagane:</td>
	      				<td><input type="checkbox" name="wymagane_{$pole[0].column_s}" {if $pole[0].wymagane == 1}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Powiązanie:</td>
	      				<td></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Akcje:</td>
	      				<td>
	      					<select multiple="multiple" name="akcje_{$pole[0].column_s}[]">
      							<option {if $aAkcje.ograniczenie == 1}selected="selected"{/if} value="ograniczenie">ograniczona ilość znaków</option>
      							<option {if $aAkcje.data == 1}selected="selected"{/if} value="data">poprawność daty</option>
      							<option {if $aAkcje.kalendarz == 1}selected="selected"{/if} value="kalendarz">kalendarz</option>
      							<option {if $aAkcje.kod_pocztowy == 1}selected="selected"{/if} value="kod_pocztowy">poprawność kodu pocztowego</option>
      							<option {if $aAkcje.visible == 1}selected="selected"{/if} value="visible">sterowanie publikacją</option>
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Styl:</td>
	      				<td>
	      					<textarea style="width: 300px; height: 80px" name="styl_{$pole[0].column_s}">{$pole[0].styl}</textarea>
	      				</td>
	      			</tr>
	      			{* zabieramy sie za sprawdzanie rodzaju pola *}
	      			{if $pole[0].typ_pola == "text"}
	      			<tr>
	      				<td class="header right">Liczba znaków min:</td>
	      				<td><input type="text" style="width: 30px;" value="{$pole[0].text_ilosc_min}" name="text_ilosc_min_{$pole[0].column_s}" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Liczba znaków max:</td>
	      				<td><input type="text" style="width: 30px;" value="{$pole[0].text_ilosc_max}" name="text_ilosc_max_{$pole[0].column_s}" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Długość pola:</td>
	      				<td><input type="text" style="width: 30px;" value="{$pole[0].text_dlugosc}" name="text_dlugosc_{$pole[0].column_s}" /> px</td>
	      			</tr>
	      			{elseif $pole[0].typ_pola == 'file'}
	      			<tr>
	      				<td class="header right">Akceptowane rozszerzenia:</td>
	      				<td>
	      					<select multiple="multiple" name="rozszerzenia_{$pole[0].column_s}[]">
      							{section name="id" loop="$aRozszerzenia"}
      								{assign var=foo value=$aRozszerzenia[id].nazwa}
      								<option {if $aRoz.$foo == 1}selected="selected"{/if} value="{$aRozszerzenia[id].nazwa}">{$aRozszerzenia[id].nazwa}</option>
      							{/section}
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Ścieżka zapisu:</td>
	      				<td><input style="width: 300px;" type="text" name="sciezka_{$pole[0].column_s}" value="{if $pole[0].file_miejsce == ''}{$serverPatch}{else}{$pole[0].file_miejsce}{/if}" /></td>
	      			</tr>
	      			<tr><td class="header" colspan="2" style="background: #ffffff;">Jeśli ładowany plik jest obrazkiem</td></tr>
	      			<tr>
	      				<td class="header right">Konwertuj do:</td>
	      				<td>
	      					<select name="image_main_roz_{$pole[0].column_s}">
	      							<option value=""></option>
	      						{section name="id" loop="$aRozszerzenia"}
      								<option {if $pole[0].image_main_roz == $aRozszerzenia[id].nazwa}selected="selected"{/if} value="{$aRozszerzenia[id].nazwa}">{$aRozszerzenia[id].nazwa}</option>
      							{/section}
	      					</select>
	      				</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Szerokość:</td>
	      				<td><input style="width: 30px;" type="text" name="image_scalex_{$pole[0].column_s}" value="{$pole[0].image_scalex}" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wysokość:</td>
	      				<td><input style="width: 30px;" type="text" name="image_scaley_{$pole[0].column_s}" value="{$pole[0].image_scaley}" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Thumb szerokość:</td>
	      				<td><input style="width: 30px;" type="text" name="thumb_scalex_{$pole[0].column_s}" value="{$pole[0].thumb_scalex}" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Thumb wysokość:</td>
	      				<td><input style="width: 30px;" type="text" name="thumb_scaley_{$pole[0].column_s}" value="{$pole[0].thumb_scaley}" /> px</td>
	      			</tr>
	      			{elseif $pole[0].typ_pola == 'textarea'}
	      			<tr>
	      				<td class="header right">Szerokość pola:</td>
	      				<td><input style="width: 30px;" type="text" name="textarea_x_{$pole[0].column_s}" value="{$pole[0].textarea_x}" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Wysokość pola:</td>
	      				<td><input style="width: 30px;" type="text" name="textarea_y_{$pole[0].column_s}" value="{$pole[0].textarea_y}" /> px</td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Użyj TinyMc:</td>
	      				<td><input type="checkbox" name="textarea_tinymc_{$pole[0].column_s}" {if $pole[0].textarea_tinymc}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			{/if}
	      			<tr>
	      				<td class="header right">Pokaż jako kolumnę:</td>
	      				<td><input type="checkbox" name="kolumna_{$pole[0].column_s}" {if $pole[0].column_show}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Środkuj:</td>
	      				<td><input type="checkbox" name="wysrodkowana_{$pole[0].column_s}" {if $pole[0].wysrodkowana}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">W użyciu:</td>
	      				<td><input type="checkbox" name="active_{$pole[0].column_s}" {if $pole[0].active}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			<tr>
	      				<td class="header right">Klucz obcy:</td>
	      				<td><input type="checkbox" name="klucz_zew_{$pole[0].column_s}" {if $pole[0].klucz_zew}checked="checked"{/if} value="1" /></td>
	      			</tr>
	      			<tr><td colspan="2" class="right"><input type="hidden" value="{$pole[0].column_s}" name="field_name" /><input type="submit" name="zapisz" value="Edytuj" /></td></tr>
	      		</table>
	      	</form>
		</div>
		
	</div> <!-- end content -->
<div id="bgcontentbottom"></div>
</div> <!-- end bg -->
