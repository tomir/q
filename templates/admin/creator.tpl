<div id="bg">
	<div id="content" style="padding-top: 10px;">
		<div id="rightbox" style="width: 990px;">
			<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		   		
		   	</h4>
    		<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
				<table class="table-long" style="width: 980px;">
					<thead>
						<tr>
							<td>
								<fieldset style="width: 900px;">
			      					<ol>
	      								<li>
											<label class="field-title">Wybierz tabelę:</label>
											<select onchange="MM_jumpMenu('parent',this,0)" id="kategoria" name="kategoria" class="form-select">
									      		{section name=id loop=$contentList}
													<option {if $contentList[id].$database == $selected_table}selected="selected"{/if} value="{$tffPatch}stworz,{$contentList[id].$database}.html">{$contentList[id].$database}</option>
									      		{/section}
									      	</select>
									    </li>
									</ol>
								</fieldset>
							</td>
						</tr>
					</thred>
				</table>
			</div>
	      	{if $columnList}
		<form name="form1" class="middle-forms" method="post" action="{$tffPatch}zapisz,{$selected_table}.html">
	      	<h4 class="white rounded_by_jQuery_corners" style="margin-top: 10px; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
		   		Dodawanie nowego formularza
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
		      		</thred>
		      		{section name=id2 loop=$columnList}
		      		<tbody>
		      			<tr class="{cycle values="odd,"}">
		      				<td>{$columnList[id2].Field}</td>
		      				<td>{if $columnList[id2].Extra != 'auto_increment'}<input type="text" name="{$columnList[id2].Field}" value="" />{elseif $columnList[id2].Key == 'PRI'}Primary key{/if}</td>
		      				<td>{$columnList[id2].Type}
		      					<input type="hidden" value="{$columnList[id2].Field}" name="column_{$columnList[id2].Field}" />
		      				</td>
		      				<td>{if $columnList[id2].Extra != 'auto_increment'}
		      						<select name="pole_{$columnList[id2].Field}">
		      							<option value="checkbox">checkbox</option>
		      							<option value="password">hasło</option>
		      							<option value="file">plik</option>
		      							<option value="select">lista pojedynczego wyboru</option>
		      							<option value="multiple">lista wielokrotnego wyboru</option>
		      							<option value="text">text</option>
		      							<option value="textarea">textarea</option>
		      						</section>
		      					{else}
		      						{$columnList[id2].Extra}
		      					{/if}
		      				</td>
		      				<td style="text-align: center;"><input type="checkbox" value="1" name="wymagane_{$columnList[id2].Field}" {if $columnList[id2].Extra == 'auto_increment'}disabled="disabled"{/if} /></td>
		      				<td style="text-align: center;"><input type="checkbox" value="1" name="wybor_{$columnList[id2].Field}" {if $columnList[id2].Extra == 'auto_increment'}disabled="disabled"{else}checked="checked"{/if} /></td>
		      				<td>{if $columnList[id2].Extra != 'auto_increment'}
		      					<select id="id_powiazania_{$columnList[id2].Field}" name="powiazania_{$columnList[id2].Field}">
	      							<option value=""></option>
	      						{section name=id loop=$contentList}
									<option value="{$contentList[id].$database}">{$contentList[id].$database}</option>
	      						{/section}
		      					</select>
		      					{/if}
		      					<div id="message_{$columnList[id2].Field}"></div>
		      				</td>
								<script type="text/javascript">
									$('#id_powiazania_{$columnList[id2].Field}').change(function() {literal}{{/literal}
										var table = $("#id_powiazania_{$columnList[id2].Field}").val();
										var field_relation = '{$columnList[id2].Field}';
										var dataString = 'table='+table+'&field='+field_relation;  
										{literal}
										$.ajax({  {/literal}
										  type: "POST",  
										  url: "{$tffPatch}pobierzKolumnyJS.html",  
										  data: dataString,  
										  beforeSend: function() {literal}{
										  		{/literal}$("#message_{$columnList[id2].Field}").html('<img style="padding-left: 5px; margin-top: 3px" src="http://gfx.tvs.pl/img/35.gif">');{literal}
										  	},
										  success: function(transport) {
												{/literal}$("#message_{$columnList[id2].Field}").html(transport);{literal}
											}
										});  
									});
								</script>
							{/literal}
		      			</tr>
		      		</tbody>
      				{/section}
      			</table>
	      	</div>

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
						     						{section name=id loop=$contentList}
													<option value="{$contentList[id].$database}">{$contentList[id].$database}</option>
						     						{/section}
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
						     						{section name=id loop=$columnList}
													<option value="{$contentList[id].$database}">{$columnList[id].Field}</option>
						     						{/section}
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
					      						{section name=id loop=$aForms}
					      							<option value="{$aForms[id].id_formularza}">{$aForms[id].nazwa_formularza}</option>
				     							{/section}
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
      							</filedset>
		      				</td>
		      			</tr>

		      			
		      			
		      			<tr style="border: 0px;"><td colspan="2" style="text-align: right;"><input type="submit" name="zapisz" value="Zapisz formularz" /></td></tr>
		      		</tbody>
	      		</table>
      		</div>
		</form>
	      	{literal}
	      	<script type="text/javascript">
				$('#select_filtr').change(function() {
					var table = $("#select_filtr").val();
					var field_relation = 'filtr';
					var dataString = 'table='+table+'&field='+field_relation;  
					$.ajax({
					  type: "POST",  
					  url: "{/literal}{$tffPatch}{literal}pobierzKolumnyJS.html",  
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
			{/literal}
	      	{/if}
		</div>
		
	</div> <!-- end content -->
<div id="bgcontentbottom"></div>
</div> <!-- end bg -->
