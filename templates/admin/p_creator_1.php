<div id="bg">
	<div id="content" style="padding-top: 10px;">
		<div id="rightbox" style="width: 990px;">
			<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		   		
		   	</h4>
    		<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
				<table class="table-long" style="width: 980px;">
					<thead>
						<tr>
							<td style="width: 300px;">
								<fieldset style="width: 200px;">
			      					<ol>
	      								<li>
											<label class="field-title">Wybierz tabelę:</label>
											<select onchange="MM_jumpMenu('parent',this,0)" id="kategoria" name="kategoria" class="form-select">
												<option value="">&nbsp;</option>
												<?php foreach($contentList as $row_c) { ?>
													<option <?php if($row_c[$database] == $selected_table) echo 'selected="selected"'; ?> value="<?php echo $tffPatch; ?>stworz,<?php echo $row_c[$database]; ?>.html"><?php echo $row_c[$database]; ?></option>
									      		<?php } ?>
									      	</select>
									    </li>
									</ol>
								</fieldset>
							</td>
							<td>
								<fieldset style="width: 200px;">
			      					<ol>
	      								<li> 
											<label class="field-title">Wybierz tabelę językową:</label>
											<select onchange="MM_jumpMenu('parent',this,0)" id="kategoria2" name="kategoria2" class="form-select">
												<option value="">&nbsp;</option>
												<?php foreach($contentLangList as $row_c) { ?>
												<option <?php if($row_c[$database2] == $selected_table2) echo 'selected="selected"'; ?> value="<?php echo $tffPatch; ?>stworz,<?php echo $selected_table; ?>,<?php echo $row_c[$database2]; ?>.html"><?php echo $row_c[$database2]; ?></option>
									      		<?php } ?>
									      	</select>
									    </li>
									</ol>
								</fieldset>
							</td>
						</tr>
					</thead>
				</table>
			</div>
	      	<?php if(is_array($columnList) && count($columnList) > 0) { ?>
			<form name="form1" class="middle-forms" method="post" action="<?php echo $tffPatch; ?>zapisz,<?php echo $selected_table; ?>.html">
	      	<h4 class="white rounded_by_jQuery_corners" style="margin-top: 10px; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		   		Lista kolumn
		   	</h4>
    		<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
				<table class="table-long" style="width: 980px;">
					<thead>
						<tr>
		      				<td class="header">Nazwa kolumny</td>
		      				<td class="header">Nazwa pola</td>
		      				<td class="header">Typ kolumny</td>
		      				<td class="header">Typ pola</td>
		      				<td class="header">Wymagane</td>
		      				<td class="header">W użyciu</td>
		      				<td class="header">Powiązania</td>
		      			</tr>
		      		</thead>
					<?php $class = 'odd'; foreach($columnList as $row_col) { ?>
		      		<tbody>
						<tr class="<?php echo $class; ?>">
		      				<td><?php echo $row_col['Field']; ?></td>
		      				<td><?php if($row_col['Extra'] != 'auto_increment') { ?><input type="text" name="<?php echo $row_col['Field']; ?>" value="" /><?php } elseif($row_col['Key'] == 'PRI') echo 'Primary key'; ?></td>
		      				<td><?php echo $row_col['Type']; ?>
		      					<input type="hidden" value="<?php echo $row_col['Field']; ?>" name="column_<?php echo $row_col['Field']; ?>" />
		      				</td>
		      				<td><?php if($row_col['Extra'] != 'auto_increment') { ?>
		      						<select name="pole_<?php echo $row_col['Field']; ?>">
		      							<option value="checkbox">checkbox</option>
		      							<option value="password">hasło</option>
		      							<option value="file">plik</option>
		      							<option value="select">lista pojedynczego wyboru</option>
		      							<option value="multiple">lista wielokrotnego wyboru</option>
		      							<option value="text">text</option>
		      							<option value="textarea">textarea</option>
		      						</select>
		      					<?php } else { ?>
		      						<?php echo $row_col['Extra']; ?>
		      					<?php } ?>
		      				</td>
		      				<td style="text-align: center;"><input type="checkbox" value="1" name="wymagane_<?php echo $row_col['Field']; ?>" <?php if($row_col['Extra'] == 'auto_increment') echo 'disabled="disabled"'; ?> /></td>
		      				<td style="text-align: center;"><input type="checkbox" value="1" name="wybor_<?php echo $row_col['Field']; ?>" <?php if($row_col['Extra'] == 'auto_increment') echo 'disabled="disabled"'; else echo 'checked="checked"'; ?> /></td>
		      				<td><?php if($row_col['Extra'] != 'auto_increment') { ?>
		      					<select id="id_powiazania_<?php echo $row_col['Field']; ?>" name="powiazania_<?php echo $row_col['Field']; ?>">
	      							<option value=""></option>
									<?php foreach($contentList as $row_cl) { ?>
										<option value="<?php echo $row_cl[$database]; ?>"><?php echo $row_cl[$database]; ?></option>
									<?php } ?>
		      					</select>
		      					<?php } ?>
		      					<div id="message_<?php echo $row_col['Field']; ?>"></div>
		      				</td>
								<script type="text/javascript">
									$('#id_powiazania_<?php echo $row_col['Field']; ?>').change(function() {
										var table = $("#id_powiazania_<?php echo $row_col['Field']; ?>").val();
										var field_relation = '<?php echo $row_col['Field']; ?>';
										var dataString = 'table='+table+'&field='+field_relation;  
										$.ajax({  
										  type: "POST",  
										  url: "<?php echo $tffPatch; ?>pobierzKolumnyJS.html",
										  data: dataString,  
										  beforeSend: function() {
										  		$("#message_<?php echo $row_col['Field']; ?>").html('<img style="padding-left: 5px; margin-top: 3px" src="http://gfx.tvs.pl/img/35.gif">');
										  	},
										  success: function(transport) {
												$("#message_<?php echo $row_col['Field']; ?>").html(transport);
											}
										});  
									});
								</script>
		      			</tr>
		      		</tbody>
      				<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
      			</table>
	      	</div>

			<?php if(is_array($columnLangList) && count($columnLangList) > 0) { ?>
				<h4 class="white rounded_by_jQuery_corners" style="margin-top: 10px; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
					Lista kolumn językowych
				</h4>
				<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
					<table class="table-long" style="width: 980px;">
						<thead>
							<tr>
								<td class="header">Nazwa kolumny</td>
								<td class="header">Nazwa pola</td>
								<td class="header">Typ kolumny</td>
								<td class="header">Typ pola</td>
								<td class="header">Wymagane</td>
								<td class="header">W użyciu</td>
								<td class="header">Powiązania</td>
							</tr>
						</thead>
					<?php $class = 'odd'; foreach($columnLangList as $row_col_lang) { ?>
						<tbody>
							<?php if(stristr($row_col_lang['Type'], 'varchar') || stristr($row_col_lang['Type'], 'text')) { ?>
							<tr class="<?php echo $class; ?>">
								<td><?php echo $row_col_lang['Field']; ?></td>
								<td><?php if($row_col_lang['Extra'] != 'auto_increment') { ?><input type="text" name="lang_<?php echo $row_col_lang['Field']; ?>" value="" /><?php } elseif($row_col_lang['Key'] == 'PRI') echo 'Primary key'; ?></td>
								<td><?php echo $row_col_lang['Type']; ?>
									<input type="hidden" value="<?php echo $row_col_lang['Field']; ?>" name="lang_column_<?php echo $row_col_lang['Field']; ?>" />
								</td>
								<td><?php if($row_col_lang['Extra'] != 'auto_increment') { ?>
										<select name="lang_pole_<?php echo $row_col_lang['Field']; ?>">
											<option value="checkbox">checkbox</option>
											<option value="password">hasło</option>
											<option value="file">plik</option>
											<option value="select">lista pojedynczego wyboru</option>
											<option value="multiple">lista wielokrotnego wyboru</option>
											<option value="text">text</option>
											<option value="textarea">textarea</option>
										</select>
									<?php } else { ?>
										<?php echo $row_col_lang['Extra']; ?>
									<?php } ?>
								</td>
								<td style="text-align: center;"><input type="checkbox" value="1" name="lang_wymagane_<?php echo $row_col_lang['Field']; ?>" <?php if($row_col_lang['Extra'] == 'auto_increment') echo 'disabled="disabled"'; ?> /></td>
								<td style="text-align: center;"><input type="checkbox" value="1" name="lang_wybor_<?php echo $row_col_lang['Field']; ?>" <?php if($row_col_lang['Extra'] == 'auto_increment') echo 'disabled="disabled"'; else echo 'checked="checked"'; ?> /></td>
								<td><?php if($row_col_lang['Extra'] != 'auto_increment') { ?>
									<select id="lang_id_powiazania_<?php echo $row_col_lang['Field']; ?>" name="lang_powiazania_<?php echo $row_col_lang['Field']; ?>">
										<option value=""></option>
										<?php foreach($contentList as $row_cl) { ?>
											<option value="<?php echo $row_cl[$database]; ?>"><?php echo $row_cl[$database]; ?></option>
										<?php } ?>
									</select>
									<?php } ?>
									<div id="lang_message_<?php echo $row_col_lang['Field']; ?>"></div>
								</td>
								<script type="text/javascript">
									$('#lang_id_powiazania_<?php echo $row_col_lang['Field']; ?>').change(function() {
										var table = $("#lang_id_powiazania_<?php echo $row_col_lang['Field']; ?>").val();
										var field_relation = '<?php echo $row_col_lang['Field']; ?>';
										var dataString = 'table='+table+'&field='+field_relation;
										$.ajax({
										  type: "POST",
										  url: "<?php echo $tffPatch; ?>pobierzKolumnyJS.html",
										  data: dataString,
										  beforeSend: function() {
												$("#lang_message_<?php echo $row_col_lang['Field']; ?>").html('<img style="padding-left: 5px; margin-top: 3px" src="http://gfx.tvs.pl/img/35.gif">');
											},
										  success: function(transport) {
												$("#lang_message_<?php echo $row_col_lang['Field']; ?>").html(transport);
											}
										});
									});
								</script>
							</tr>
							<?php } ?>
						</tbody>
					<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
					</table>
				</div>
			<?php } ?>

	      	<h4 class="white rounded_by_jQuery_corners" style="margin-top: 10px; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
				Opcje dodatkowe formularza
		   	</h4>
      		<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
	      		<table class="table-long">
	      			<tbody>
		      			<tr style="border: 0px;">
		      				<td style="text-align: right; font-weight: bold;padding-top: 8px;">
		      					<fieldset style="width: 900px;">
			      					<ol>
	      								<li class="even">
	      									<label class="field-title">Nazwa formularza <em>*</em>:</label>
											<label><input class="txtbox-long" type="text" name="nazwa_formularza" value="" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li>
	      									<label class="field-title">Select filtr:</label>
											<label>
												<select id="select_filtr" class="form-select" name="select_filtr">
						     						<option value=""></option>
						     						<?php foreach($contentList as $row_cl) { ?>
													<option value="<?php echo $row_cl[$database]; ?>"><?php echo $row_cl[$database]; ?></option>
						     						<?php } ?>
						      					</select>
						      					<div id="message_select_filtr"></div>
					      					</label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li class="even">
	      									<label class="field-title">Treść przycisku <em>*</em>:</label>
											<label><input class="txtbox-middle" type="text" name="tresc_przycisku" value="" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li>
	      									<label class="field-title">Sortowanie:</label>
											<label><input type="checkbox" name="sortuj" value="1" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li class="even">
	      									<label class="field-title">Główny formularz:</label>
											<label><input type="checkbox" name="main_form" value="1" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li>
	      									<label class="field-title">Sort by:</label>
											<label>
												<select class="form-select" id="sort_column" name="select_filtr">
						     						<option value=""></option>
						     						<?php foreach($columnList as $row_col2) { ?>
														<option value="<?php echo $row_col2['Field']; ?>"><?php echo $row_col2['Field']; ?></option>
						     						<?php } ?>
						      					</select>
											</label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li class="even">
	      									<label class="field-title">DESC:</label>
											<label><input type="checkbox" name="sort_desc" value="1" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li id="akcje_box"><input type="hidden" value="0" name="ilosc_akcji" id="ilosc_akcji" />
	      									<label class="field-title">ikona:</label>
											<label<input class="txtbox-short" type="text" name="image[]" value="" /></label>
											<label class="field-title" style="width: 80px;">formularz:</label>
											<label>
												<select class="form-select" name="pow_form[]">
					      							<option value=""></option>
													<?php foreach($aForms as $form) { ?>
													<option value="<?php echo $form['id_formularza']; ?>"><?php echo $form['nazwa_formularza']; ?></option>
													<?php } ?>
					      						</select>
					      					</label>
					      					<label class="field-title" style="width: 80px;">lub link:</label>
											<label><input class="txtbox-short" type="text" name="link[]" value="" /></label>
											<span class="clearFix">&nbsp;</span>
										</li>
										<li class="even">
											<label><a title="Dodaj następną akcję" href="javascript:void(0);" rel="nowe_slowo"><div style="float: left;"><img style="border: 0pt none ;" alt="" src="../img/admin/add.gif"/></div><div style="float: left;padding-left: 5px;">dodaj następną akcję</div></a></label>
											<span class="clearFix">&nbsp;</span>
										</li>
	      							</ol>
      							</fieldset>
		      				</td>
		      			</tr>
		      			<tr style="border: 0px;"><td colspan="2" style="text-align: right;"><input type="submit" name="zapisz" value="Zapisz formularz" /></td></tr>
		      		</tbody>
	      		</table>
      		</div>
		</form>
	      	<script type="text/javascript">
				$('#select_filtr').change(function() {
					var table = $("#select_filtr").val();
					var field_relation = 'filtr';
					var dataString = 'table='+table+'&field='+field_relation;  
					$.ajax({
					  type: "POST",  
					  url: "<?php echo $tffPatch; ?>phppobierzKolumnyJS.html",
					  data: dataString,  
					  beforeSend: function() {
					  		$("#message_select_filtr").html('<img style="padding-left: 5px; margin-top: 3px" src="http://gfx.tvs.pl/img/35.gif">');
					  	},
					  success: function(transport) {
							$("#message_select_filtr").html(transport);
						}
					});  
				});

				$('A[rel="nowe_slowo"]').click( function() {
					var ile = $("#ilosc_akcji").val();
					ile++;
					$("#ilosc_akcji").val(ile);
					var $temp = $("#akcje_box").clone();
					$("#akcje_box").after($temp);
				});
			</script>
	      	<?php } ?>
		</div>
		
	</div> <!-- end content -->
<div id="bgcontentbottom"></div>
</div> <!-- end bg -->
