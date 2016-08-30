<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
	
<div style="margin: 0 auto;">
	
	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;padding-left: 10px; margin-top:1px;">Newsletter dla:</div>
   		<br style="clear: both;" />
   	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<tbody>
				<tr class="">
					<td>Email:</td><td><?php echo $aData['email']; ?>&nbsp;</td>
				</tr>
				<tr class="">
					<td>Telefon:</td><td><?php echo $aData['telefon']; ?>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	
   	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;padding-left: 10px; margin-top:1px;">Podgląd wybranych opcji</div>
   		<br style="clear: both;" />
   	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<thead>
				<tr>
					<td class="header">Typ</td>
					<td class="header">Marka</td>
					<td class="header">Model</td>
					<td class="header">Rocznik od</td>
					<td class="header">Rocznik do</td>
				</tr>
			</thead>
			<?php if(is_array($aData['wariants']) && count($aData['wariants']) > 0 && !empty($aData['wariants'])) { ?>
			<tbody>
				<?php $class = 'odd'; foreach($aData['wariants'] as $row) { ?>
					<tr class="<?php echo $class; ?>">
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $aData['car_type']; ?>
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['producer_name']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['model_name']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['rok_od']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['rok_do']; ?>		
						</td>
					</tr>
				<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
			</tbody>
			<?php } else { ?>
			<tbody>
				<tr class="odd"><td colspan="6" style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">Brak rekordów w bazie</td></tr>
			</tbody>
			<?php } ?>
		</table>
		
	</div>

	
	
</div>