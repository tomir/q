<?php
class PanelJS extends Panel {
	
	public function pobierzKolumnyJS() {
		
		$i = 0;
		if($_POST['table'] != '') {
			$sql = "SHOW COLUMNS FROM ".$_POST['table'];
			$columnTable = array();
			$columnTable = ConnectDB::subQuery($sql);
			if(count($columnTable) > 0) {
				echo '<table class="header" cellspacing="1" cellpadding="0"><tr><td></td><td class="header">value</td><td class="header">display</td></tr>';
				foreach ($columnTable as $row) {
					echo '<tr><td style="padding: 2px;">'.$row['Field'].'</td><td style="padding: 2px; text-align: center;"><input type="radio" value="'.$row['Field'].'" name="pow_value_'.$_POST['field'].'" /></td><td style="padding: 2px; text-align: center;"><input type="checkbox" value="'.$row['Field'].'" name="pow_display_'.$_POST['field'].'_'.$i.'" /></td></tr>';
					$i++;
				}
				echo '<input type="hidden" value="'.$i.'" name="pow_ilosc_kolumn_'.$_POST['field'].'" />';
				echo '</table>';
			}
		}
	}
	
	public function pobierzFormEditJS($id) {
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE id_formularza = ".$id;
		$aForm = array();
		$aForm = ConnectDB::subQuery($sql);
		if(count($aForm) > 0) {
			foreach($aForm as $row) {
				echo '<td>'.$row['id_formularza'].'</td>';
				echo '<td><input style="width: 100px;" type="text" value="'.$row['nazwa_formularza'].'" name="edited_name" id="edited_name" /></td>';
				echo '<td>'.$row['nazwa_tabeli'].'</td>';
				if($row['active'] == 1)
					echo '<td style="text-align: center;"><input type="checkbox" value="1" checked="checked" name="edited_active" id="edited_active" /></td>';
				else
					echo '<td style="text-align: center;"><input type="checkbox" value="1" name="edited_active" id="edited_active" /></td>';
				echo '<td style="text-align: center;">'.$row['data_stworzenia'].'</td>';
				echo '<td style="text-align: center;">'.$row['data_modyfikacji'].'</td>';
				echo '<td colspan="2"><input type="hidden" name="edited_formid" id="edited_formid" value="'.$row['id_formularza'].'" /><button value="Zapisz" name="zapisz_form" id="zapisz_form">Zapisz</button></td>';
			}
		}
		return true;
	}
	
	public function zapiszFormEditJS($id) {
		$wwwPatch = MyConfig::getValue("wwwPatch");
		$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz SET nazwa_formularza = :nazwa_formularza, data_modyfikacji = now(), active = :active WHERE id_formularza=".$id;
		//echo $_POST['stan'];
		if($_POST['stan'])
			$active = 1;
		else
			$active = 0;
			
		$pdo = new ConnectDB();
		$wynik = $pdo -> prepare($sql) ;
		$wynik -> bindValue (':nazwa_formularza'  		  , $_POST['name'] 			, PDO::PARAM_STR) ;
		$wynik -> bindValue (':active' 	          		  , $active				    , PDO::PARAM_INT) ;

		$liczbaZmian = $wynik -> execute();
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE id_formularza = ".$id;
		$aForm = array();
		$aForm = ConnectDB::subQuery($sql);
		if(count($aForm) > 0) {
			foreach($aForm as $row) {
				echo '<td>'.$row['id_formularza'].'</td>';
				echo '<td>'.$row['nazwa_formularza'].'</td>';
				echo '<td>'.$row['nazwa_tabeli'].'</td>';
				if($row['active'] == 1)
					echo '<td style="text-align: center;">tak</td>';
				else
					echo '<td style="text-align: center;">nie</td>';
				echo '<td style="text-align: center;">'.$row['data_stworzenia'].'</td>';
				echo '<td style="text-align: center;">'.$row['data_modyfikacji'].'</td>';
				echo '<td style="text-align: center;>
						<a href="'.$wwwPatch.'podglad,'.$row['id_formularza'].',html" title="Podgląd"><img src="'.$wwwPatch.'images/admin/icons/lupa.gif" alt="Pogląd formularza" style="border: 0;" /></a>
						<a href="javascript:void(0);" rel="edit_form" title="Edytuj"><img src="'.$wwwPatch.'images/admin/icons/edit.jpg" id="'.$row['id_formularza'].'" alt="Edytuj" style="border: 0;" /></a>
						<a href="javascript:void(0);" rel="delete_form" title="Usuń"><img src="'.$wwwPatch.'images/admin/icons/delete.gif" id="'.$row['id_formularza'].'" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					  </td>';
	      		echo '<td style="text-align: center;"><a href="javascript:void(0);" rel="details_form" id="'.$row['id_formularza'].'" title="Rozwiń formularz">rozwiń</td>';
			}
		}
		return true;
	}
	
	public function pobierzFormDetailsJS($id) {
		$wwwPatch = MyConfig::getValue("wwwPatch");
		$tffPatch = MyConfig::getValue("tffPatch");
		
		echo '<tr id="arrive'.$id.'"><td></td><td colspan="7">';
		echo '<table class="header" cellspacing="1" cellpadding="0" style="width:100%;">';
		echo '<tr>
      			<td class="header"><b>Nazwa kolumny</b></td>
      			<td class="header"><b>Nazwa pola</b></td>
      			<td class="header"><b>Typ pola</b></td>
      			<td class="header" style="width: 100px;"><b>Wymagane</b></td>
      			<td class="header" style="width: 238px;"><b>Powiązania</b></td>
      			<td class="header" style="text-align: center;"><b>Akcje</b></td>
      		  </tr>';
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$id;
		$tablicaWynikow = array();
		$tablicaWynikow = ConnectDB::subQuery($sql);
		
		if(count($tablicaWynikow) > 0) {
		    echo '<tr><td colspan="6"><ul id="test-list" style="list-style: none; padding-left: 0;">';
			foreach($tablicaWynikow as $row) {
			    echo '<li id="'.$row['id_pola'].'">';
			    echo '<table class="handle">';
				echo '<tr>';
				echo '<td style="width: 244px; cursor: move;">'.$row['column_s'].'</td>';
				echo '<td style="width: 167px; cursor: move;">'.$row['nazwa_pola'].'</td>';
				echo '<td style="width: 121px; cursor: move;">'.$row['typ_pola'].'</td>';
				if($row['wymagane'])
					echo '<td style="width: 100px; cursor: move;">tak</td>';
				else
					echo '<td style="width: 100px; cursor: move;">nie</td>';
				if($row['powiazana_tabela'] != '')
					echo '<td style="width: 238px; cursor: move;">'.$row['powiazana_tabela'].' (value: '.$row['pow_value'].',name: '.$row['pow_name'].')</td>';
				else
					echo '<td style="width: 238px; cursor: move;">brak powiązań</td>';
				echo '<td style="text-align: center; width: 75px;">
						 <a href="javascript:void(0);" rel="details" title="Szczegóły"><img src="'.$wwwPatch.'images/admin/icons/lupa.png" id="'.$row['id_pola'].'" alt="Szczegóły" style="border: 0;" /></a>
						 <a href="'.$tffPatch.'pole,edytuj,'.$row['id_pola'].'.html" rel="" title="Edytuj"><img src="'.$wwwPatch.'images/admin/icons/edit.png" id="'.$row['id_pola'].'" alt="Edytuj" style="border: 0;" /></a>
						 <a href="javascript:void(0);" rel="delete_pole" id="'.$row['id_pola'].'" title="Usuń"><img src="'.$wwwPatch.'images/admin/icons/delete.gif" id="'.$id.'" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
					  </td>';
				echo '</tr>';
				echo '</table>';
			    echo '</li>';
			}
		    echo '</td></tr></ul>';
		}
		echo '</table>';
		echo '</td></tr>';
	}

	public function fieldsSortJS($order) {
		if($order) {
			$aOrder = explode(",",$order);
			$i = 1;

			$pdo = new ConnectDB() ;
			foreach($aOrder as $row) {
				$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz_pola SET pole_order = ".$i." WHERE id_pola = ".$row;
				ConnectDB::subExec($sql);
				$i++;
			}
		}
	}
	
}
?>