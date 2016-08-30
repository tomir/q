<script type="text/javascript" src="../js/admin/jquery-ui-1.7.2.custom.min.js" ></script>
<script type="text/javascript">

	$(document).ready( function() {
		
		$('A[rel="show_record"]').click(function() {
			var id = $(this).attr('id');
			var this_a = $(this);
			
			$.ajax({  
			  type: "GET",  
			  url: "<?php echo $wwwPanelPatch; ?>newsletter,activate,"+id+".html",

			  success: function(transport) {
					this_a.find("img").attr({src: transport});
				}
			})
		});


	});
	function MM_jumpMenu(targ,selObj,restore){
			eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
			if (restore) selObj.selectedIndex=0;
		}
</script>	
<div style="margin: 0 auto;">
	
   	<h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;padding-left: 10px; margin-top:1px;">Lista klientów</div>
   		<br style="clear: both;" />
   	</h4>
	<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long">
			<thead>
				<tr>
					<td class="header">Firma</td>
					<td class="header">Imię</td>
					<td class="header">Nazwisko</td>
					<td class="header">Data zapisania</td>
					<td class="header">Saldo</td>
					<td class="header" style="text-align: center;font-size: 12px;">Akcje</td>
				</tr>
			</thead>
			<?php if(is_array($klienciList) && count($klienciList) > 0 && !empty($klienciList)) { ?>
			<tbody>
				<?php $class = 'odd'; foreach($klienciList as $row) { ?>
					<tr class="<?php echo $class; ?>">
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['firma']; ?>
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['imie']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['nazwisko']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['data_rejestracji']; ?>	
						</td>
						<td style="vertical-align: middle; font-size: 12px; padding: 5px 8px;">
							<?php echo $row['saldo']; ?> zł
						</td>
						<td style="vertical-align: middle; text-align: center; width: 70px;">
							<a style="margin-left: 3px;" href="klienci,edit,<?php echo $row['id']; ?>.html"  title="Podgląd"><img src="../images/admin/icons/edit.png" alt="" /></a>
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
		<?php if($show_pages > 1) {
			$pages_limit = $show_page + 5;
			$start_limit = $show_page - 5;
		?>
		
		<div class="pagination right">
			<a href="<?php echo $wwwPatchPanel.'klienci,'.($show_page-1); ?>.html<?php echo $filtr_url; ?>"<?php if($show_page == 1) { ?> style="Visibility:Hidden;"<?php } ?>>&laquo;</a>
			<?php
			if($pages_limit > $show_pages)
				$pages_limit = $show_pages+1;
			if($start_limit <= 0)
				$start_limit = 1;

		for($i = $start_limit;$i<$pages_limit; $i++) { ?>
			<?php echo '<a '; if($show_page == $i) { echo ' class="active" '; } echo 'href="'.$wwwPatchPanel.'klienci,'.$i.'.html'.$filtr_url.'">'.$i.'</a></td>'; ?>
		<?php } ?>
		<?php if($show_page != $show_pages) { ?><a href="<?php echo $wwwPatchPanel.'klienci,'.($show_page+1); ?>.html<?php echo $filtr_url; ?>">&raquo;</a><?php } ?>
		</div>
		<div style="clear:both;"></div>
		<?php } ?>
	</div>

	
	
</div>