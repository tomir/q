<script type="text/javascript">
	$(document).ready( function() {
		$('A[rel="delete_form"]').click(function() {
			var answer;
    		answer = window.confirm("Napewno chcesz usunąć ten formularz?");
    		if (answer == 1) {
    			var url;
				var id = $(this).find('img').attr('id');
				url = "<?php echo $tffPatch; ?>usun," + id + ".html";
				window.location = url; // pozostale
	        	href_remove.href = url; // explorer
			}
			return false;
		});
		
		$('A[rel="details_form"]').click(function() {
			var id = $(this).attr('id');
			var tr = $(this).closest("tr");
			var this_a = $(this);
			var arrive = $("#arrive"+id).attr('id');

			if(!arrive) {
				$.ajax({  
				  type: "POST",  
				  url: "<?php echo $tffPatch; ?>pobierzFormDetailsJS,"+id+".html",   
	
				  success: function(transport) {
						tr.after(transport);
						this_a.html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Schowaj formularz">schowaj</a>');
						
					}
				})
			} else {
				tr.find('A[rel="details_form"]').html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Rozwiń formularz">rozwiń</a>');
				$("#arrive"+id).remove();
			}
		
		});
		
		$('A[rel="hide_form"]').click(function() {
			tr.find('A[rel="hide_form"]').html('<a href="javascript:void(0);" rel="details_form" id="'+id+'" title="Rozwiń formularz">rozwiń</a>');
			$("#arrive").remove();
		
		});
	});
	
	$(document).hover( function() {
		$('A[rel="edit_form"]').click(function() {
			var id = $(this).find('img').attr('id');
			var tr = $(this).closest("tr");
			$.ajax({  
			  type: "POST",  
			  url: "<?php echo $tffPatch; ?>pobierzFormEditJS,"+id+".html",   
			  beforeSend: function() {
					tr.html('<td colspan="8"><img alt="" style="padding-left: 5px; margin-top: 5px;" src="http://gfx.tvs.pl/img/35.gif" /></td>');
					$(document).find('A[rel="edit_form"]').css({'display' : 'none'});
				},
			  success: function(transport) {
					tr.html(transport);	
				}
			})
		
		});
		
		$("#zapisz_form").click(function() {
			var tr = $(this).closest("tr");
			var id = $("#edited_formid").val();
			var name = $("#edited_name").val();
			var stan = 0;
			if($("#edited_active:checked").val()) {
				stan = $("#edited_active:checked").val();
			}

			var dataString = "name="+name+"&stan="+stan; 
			
			$.ajax({  
			  type: "POST",  
			  url: "<?php echo $tffPatch; ?>zapiszFormEditJS,"+id+".html",   
			  data: dataString,
			  beforeSend: function() {
					tr.html('<td colspan="8"><img alt="" style="padding-left: 5px; margin-top: 5px;" src="http://gfx.tvs.pl/img/35.gif" /></td>');
				},
			  success: function(transport) {
					tr.html(transport);
					$(document).find('A[rel="edit_form"]').css({'display' : 'inline'});
				}
			})
		
		});

	});

</script>
<div style="margin: 0 auto; width: 990px;">    	
    <?php if(is_array($aForms) && count($aForms) > 0) { ?>
    <h4 class="white rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">
   		<div style="float: left;">
			<a title="" href="<?php echo $tffPatch; ?>stworz.html"><img style="border: 1px solid #E0E0E0;" alt="" src="../images/admin/icons/add.gif"/></a>
   		</div>
   		<div style="float: left;padding-left: 10px; margin-top:1px;"><a style="color: #E0E0E0; text-decoration: none;" title="dodaj nowy formularz" href="<?php echo $tffPatch; ?>stworz.html">dodaj nowy formularz</a></div>
   		<br style="clear: both;" />
   	</h4>
    <div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
		<table class="table-long" style="width: 990px;">
			<thead>
				<tr>
					<td class="header">Id</td>
					<td class="header">Nazwa formularza</td>
					<td class="header">Nazwa tabeli</td>
					<td class="header">Aktywny</td>
					<td class="header" style="text-align: center; width: 170px;">Data stworzenia</td>
					<td class="header" style="text-align: center; width: 170px;">Ostatnia modyfikacja</td>
					<td class="header" style="text-align: center; width: 90px;">Akcje</td>
					<td class="header">Rozwiń</td>
				</tr>
			</thead>
			<?php $class = 'odd'; foreach($aForms as $form) { ?>
   			<tbody>
				<tr class="<?php echo $class; ?>">
	   				<td><?php echo $form['id_formularza']; ?></td>
	   				<td><?php echo $form['nazwa_formularza']; ?></td>
	   				<td><?php echo $form['nazwa_tabeli']; ?></td>
	   				<td style="text-align: center;"><?php if($form['active']) { ?>tak<?php } else { ?>nie<?php } ?></td>
	   				<td style="text-align: center;"><?php echo $form['data_stworzenia']; ?></td>
	   				<td style="text-align: center;"><?php echo $form['data_modyfikacji']; ?></td>
	   				<td style="text-align: center; width: 90px;">
						<a href="<?php echo $wwwPatch; ?>podglad,<?php echo $form['id_formularza']; ?>,html" title="Podgląd"><img src="<?php echo $wwwPatch; ?>images/admin/icons/lupa.gif" alt="Pogląd formularza" style="border: 0;" /></a>
	   					<a href="javascript:void(0);" rel="edit_form" title="Edytuj"><img src="<?php echo $wwwPatch; ?>images/admin/icons/edit.jpg" id="<?php echo $form['id_formularza']; ?>" alt="Edytuj" style="border: 0;" /></a>
						<a href="javascript:void(0);" rel="delete_form" title="Usuń"><img src="<?php echo $wwwPatch; ?>images/admin/icons/delete.gif" id="<?php echo $form['id_formularza']; ?>" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					</td>
	   				<td style="text-align: center;"><a href="javascript:void(0);" rel="details_form" id="<?php echo $form['id_formularza']; ?>" title="Rozwiń formularz">rozwiń</a></td>
   				</tr>
   			</tbody>
			<?php if($class == 'odd') $class = ''; else $class = 'odd'; } ?>
   		</table>
   	</div>
   	<?php } ?>
</div>
