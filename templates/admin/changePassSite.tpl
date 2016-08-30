<form action="{$wwwPatch}admin/zmienhaslo.html" method="post" id="myform" style="width: 300px; margin: 0 auto;">
	<table class="header2" cellspacing="1" cellpadding="0">
		<tr>
			<td class="header2" style="padding: 5px; font-weight: bold;">Aktualne hasło:</td>
			<td style="padding: 2px;"><input type="password" name="haslo" style="width: 200px;" /></td>
		</tr>
		<tr>
			<td class="header2" style="padding: 5px; font-weight: bold;">Nowe hasło:</td>
			<td style="padding: 2px;"><input type="password" name="haslo_new" style="width: 200px;" /></td>
		</tr>
		<tr>
			<td class="header2" style="padding: 5px; font-weight: bold;">Powtórz nowe hasło:</td>
			<td style="padding: 2px;"><input type="password" name="haslo_new_2" style="width: 200px;" /></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;padding: 2px;"><input type="submit" value="Zmień" name="zapisz" /></td>
		</tr>
	</table>
</form>