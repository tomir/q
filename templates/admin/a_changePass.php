<div class="box">
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		 Zmień hasło  		
	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<form action="<?php echo $wwwPatch; ?>admin/zmienhaslo.html" method="post" id="myform" class="middle-forms" style="padding: 10px;">
			<br style="clear: both;" />
			<fieldset>
				<ol>
					<li class="even">
						<label class="field-title">Aktualne hasło:</label>
						<label><input class="txtbox-long" type="password" name="haslo" style="width: 200px;" /></label>
					</li>
					<li>
						<label class="field-title">Nowe hasło:</label>
						<label><input type="password" name="haslo_new" style="width: 200px;" /></label>
					</li>
					<li class="even">
						<label class="field-title">Powtórz nowe hasło:</label>
						<label><input type="password" name="haslo_new_2" style="width: 200px;" /></label>
					</li>
				</ol>
			</fieldset>
			<p style="text-align: right;">
					<input type="submit" value="Zmień" name="zapisz" />
			</p>
		</form>
	</div>
</div>