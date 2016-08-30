<div style="width: 490px; margin: 0 auto;">
	<form name="form1" method="post" action="{$wwwPatchPanel}kontakt,send.html">
		<table class="header" cellspacing="1" cellpadding="0" style="width: 100%;">
			<tr>
				<td class="header">Treść wiadomości:</td>
			</tr>
			<tr>
				<td>Rodzaj wiadomości: 
					<select name="rodzaj">
						<option value="info">Prośba o informację</option>
						<option value="blad">Zgłoszenie błędu</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<textarea name="tresc" style="width: 99%; height: 200px;"></textarea>
				</td>
			</tr>
			<tr>
				<td style="text-align: right;"><input type="submit" name="wyslij" value="Wyślij" /></td>
			</tr>
		</table>
	</form>
</div>