<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author tomi
 */
class Tresc {

	public function getOne($id) {
		
		$sql = "SELECT a_lang.*, c_lang.* FROM " . MyConfig::getValue("__formind_article") . " a
					    LEFT JOIN " . MyConfig::getValue("__formind_article_lang") . " a_lang ON a.art_id = a_lang.art_id AND a_lang.lang_prefix = '" . LANG_PREFIX . "'
					    LEFT JOIN " . MyConfig::getValue("__formind_article_category") . " ac ON a.art_id = ac.art_id
					    LEFT JOIN " . MyConfig::getValue("__formind_category") . " c ON ac.cat_id = c.cat_id
					    LEFT JOIN " . MyConfig::getValue("__formind_category_lang") . " c_lang ON c.cat_id = c_lang.cat_id AND c_lang.lang_prefix = '" . LANG_PREFIX . "'
			   WHERE a.art_publish = 1 AND a.art_id = " . $id ;
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, "fetch");

			$aResult['images'] = $this->GetArticleImages($aResult['art_id']);
			$aResult['files'] = $this->GetArticleFiles($aResult['art_id']);

		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
	
	/**
	 * Pobiera listę artykułów
	 *
	 * @param int $id_kat	Id kategorii
	 * @param int $start		od którego rokurdu zacząć pobierać dane
	 * @param int $limit		limit pobieranych danych
	 * @param bool $media	czy pobierać grafiki i pliki
	 * @return array		Zwraca tablicę rekordów
	 */
	public function getList($id_kat, $start, $limit, $media = true) {
	
		$sql = "SELECT a_lang.*, c_lang.* FROM " . MyConfig::getValue("__formind_article") . " a
					    LEFT JOIN " . MyConfig::getValue("__formind_article_lang") . " a_lang ON a.art_id = a_lang.art_id AND a_lang.lang_prefix = '" . LANG_PREFIX . "'
					    LEFT JOIN " . MyConfig::getValue("__formind_article_category") . " ac ON a.art_id = ac.art_id
					    LEFT JOIN " . MyConfig::getValue("__formind_category") . " c ON ac.cat_id = c.cat_id
					    LEFT JOIN " . MyConfig::getValue("__formind_category_lang") . " c_lang ON c.cat_id = c_lang.cat_id AND c_lang.lang_prefix = '" . LANG_PREFIX . "'
			   WHERE a.art_publish = 1 AND ac.cat_id = " . $id_kat . "
			   ORDER BY a.art_create_date DESC
			   LIMIT " . $start . ", " . $limit;
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			if($media) {
				$aResult2 = array();
				
				if(count($aResult) > 0 && is_array($aResult)) {
					foreach($aResult as $row) {

						$row['images'] = $this->GetArticleImages($row['art_id']);
						$row['files'] = $this->GetArticleFiles($row['art_id']);
						$aResult2[] = $row;
					}
				}
				return $aResult2;
			}
			
			return $aResult;
		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	
	}
	
	public function GetArticleImages($art_id) {
		
		$sql = "SELECT * FROM " . MyConfig::getValue("__formind_media_img") . " WHERE art_id = ".$art_id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	public function GetArticleFiles($art_id) {
		
		$sql = "SELECT * FROM " . MyConfig::getValue("__formind_media_files") . " WHERE art_id = ".$art_id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
}
?>
