<?php

namespace Przepis\Service;

/**
 * Description of Save
 *
 * @author tomi
 */
class Save {
	
	public $postData = array();
	public $id = 0;
	
	public function __construct($data) {
		$this->postData = $data;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function preparePrzepis() {
		
		$ingredients = '';
		foreach($this->postData['ingredients'] as $key => $ingredient) {
			$ingredients .= $ingredient.', '.$this->postData['ingredients_ilosc'][$key].' '.$this->postData['ingredients_gram'][$key].';';
		}
		
		$this->postData['ingredients'] = $ingredients;
		$this->postData['add_date'] = date("Y-m-d H:i:s");
		$this->postData['active'] = 0;
	}
	
	public function addUser() {
		
		$sql = "SELECT * FROM shop_users WHERE user_email = '".trim($this->postData['user_email'])."' LIMIT 1";
		$aResult = \ConnectDB::subQuery($sql, "fetch");
			
		if(is_array($aResult)) {
			$this->postData['user_id'] = $aResult['user_id'];
		} else {
			$id = \ConnectDB::subAutoExec('shop_users', array(
				'user_email' => trim($this->postData['user_email']),
				'user_first_name' => trim($this->postData['user_nick']),
				'user_add_date' => date("Y-m-d H:i:s")
			), 'INSERT');
			
			$this->postData['user_id'] = $id;
		}
		
	}
	
	public function save() {
		
		if($this->check()) {
			$objPrzepis = new \Przepis\Repository\Przepis();
			$this->id = $objPrzepis->insert($this->postData);
			
			$this->postData['hash'] = md5(trim($this->postData['user_email']).$this->id);
			$objPrzepis->update(array(
				'hash' => $this->postData['hash']
			), $this->id);
			
			return true;
		} else {
			\Enp\Tool::setFlashMsg("Twój przepis już istnieje dla wybranego działu.", \Enp\Tool::ERROR);
			return false;
		}

	}
	
	public function check() {
		
		$objPrzepis = new \Przepis\Repository\Przepis();
		$row = $objPrzepis->getOne(array(
			'x.user_id' => $this->postData['user_id'],
			'x.przepis_rodzaj_id' => $this->postData['przepis_rodzaj_id'],
			'x.active_not' => 2
		));
		
		if(is_array($row) && $row['id'] > 0) {
			return false;
		} else {
			return true;
		}
		
	}
	
	public function saveImage() {
		
		if (isset($_FILES['zdjecie']) && $this->id > 0) {

			$file_name = $this->file['name'];
			$rozszerzenie = explode(".",$file_name);
			$cnt = count($rozszerzenie);
			$file_name = "img_".$this->id.".".$rozszerzenie[$cnt-1];

			$uploadFile = \MyConfig::getValue("serverPatch")."temp/images/".$file_name;
			if (move_uploaded_file ($_FILES['zdjecie']['tmp_name'], $uploadFile)) {
				return true;
			} else {
				return false;
				
			}

		} else return false;
	
	}
}
