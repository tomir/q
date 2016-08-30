<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classArticle
 *
 * @author t.jurdzinski
 */
class Article {
    public function articleGet($id, $lang) {
		$sql = "SELECT * FROM mgc_article JOIN mgc_article_lang USING (art_id)
					LEFT JOIN mgc_article_media USING (art_id)
					LEFT JOIN mgc_article_video USING (art_id)
					WHERE art_id = ".$id." AND lang_prefix = '".$lang."'";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql,'','','fetch');
			return $aResult;
		} catch(PDOException $e) {
			//mail('jurdziol@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
}
?>
