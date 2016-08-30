<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		 Edytuj rekord  		
</h4>


<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
	<form class="middle-forms" style="padding: 10px;" id="myFromularz" name="formularz" action="" method="post">
		<p>pola wymagane oznaczone <em>*</em></p>
		<br style="clear: both;" />
		<fieldset>
			<ol>
				<li class="even">
					<label class="field-title">Firma:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[firma]" id="producer_name_field" value="<?php echo $aData['firma']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">NIP:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[nip]" id="producer_name_field" value="<?php echo $aData['nip']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Imię:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[imie]" id="producer_name_field" value="<?php echo $aData['imie']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Nazwisko:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[nazwisko]" id="producer_name_field" value="<?php echo $aData['nazwisko']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Ulica:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[ulica]" id="producer_name_field" value="<?php echo $aData['ulica']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Numer domu/mieszkania:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[nr_dom]" id="producer_name_field" value="<?php echo $aData['nr_dom']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Kod pocztowy:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[kod_pocztowy]" id="producer_name_field" value="<?php echo $aData['kod_pocztowy']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Miejscowość:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[miejscowosc]" id="producer_name_field" value="<?php echo $aData['miejscowosc']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Telefon:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[telefon]" id="producer_name_field" value="<?php echo $aData['telefon']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Kraj:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[kraj]" id="producer_name_field" value="<?php echo $aData['kraj']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Email/login:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[email]" id="producer_name_field" value="<?php echo $aData['email']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
				<li class="even">
					<label class="field-title">Saldo:</label>
					<label><input class="txtbox-long" style="" type="text" name="formData[saldo]" id="producer_name_field" value="<?php echo $aData['saldo']; ?>" /></label>
					<span class="clearFix">&nbsp;</span>
				</li>
			</ol>
		</fieldset>
		<p style="text-align: right;"><input type="submit" value="Zapisz" /></p>
	</form>
</div>
