<?php

namespace Opinie;


class Lista {
	
	const OPINIA_POTWIERDZENIE_TYTUL	= 'Prośba o potwierdzenie autentyczności opinii dla produktu';
	const OPINIA_ODPOWIEDZ_TYTUL		= 'Dodano pytanie dotyczące Twojej opinii';
	
	const OPINIA_INFORMACJA_TYTUL		= 'Dodano odpowiedż do Twojego pytania';
	
	const SECRET_KEY		= '#$^tgw798023';
	const OPINION_ACCEPT	= 1;

	public function getOpinion($id) {
		
		$sql = "SELECT o.*, u.*,
					os.status_name AS status,
					p.p_name AS produkt_nazwa,
					p.p_id AS produkt_ident
				FROM shop_opinion o
				LEFT JOIN shop_opinion_statusy os ON o.status_id = os.status_id
				LEFT JOIN shop_product p ON o.p_id = p.p_id
				LEFT JOIN shop_users u ON o.user_id = u.user_id
				WHERE 1 AND o.opinion_id = ".$id;
		
		$row = array();
		$row = \ConnectDB::subQuery($sql);
		
		$row['ocena_opinii']	=	round(($row['opinion_yes']/($row['opinion_yes']+$row['opinion_no']))*100, 1);	
		
		return $row;
	}
	
	public function getOpinionList($filtr = null) {
		
		$sql = "SELECT * FROM shop_opinion o
				WHERE 1";
		
		$sql .= $this->getFiltr($filtr);
		$sql .= " ORDER BY o.opinion_data DESC";
		
		$aResult = array();
		$aResult = \ConnectDB::subQuery($sql);
		
		foreach($aResult as &$row) {
			$row['row'] = $this->getOpinion($row['opinion_id']);
		}
		
		return $aResult;
	}
	
	public function getOpinionListCount($filtr) {
		
		$sql = "SELECT COUNT(o.opinion_id) as ile FROM shop_opinion o
				WHERE 1 ";
		
		$sql .= $this->getFiltr($filtr);
		
		try {
			$ile = \ConnectDB::subQuery($sql, 'one');
			if($ile == ' ') 
				$ile = 0;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $ile;
	}
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['status_id']) && is_numeric($this->filtr['status_id'])) {
			$sql .= " AND o.status_id = ".$this->filtr['status_id']." ";
		}
		
		if(isset($this->filtr['p_id']) && is_numeric($this->filtr['p_id'])) {
			$sql .= " AND o.p_id = ".$this->filtr['p_id']." ";
		}
		
		if(isset($this->filtr['user_id']) && is_numeric($this->filtr['user_id'])) {
			$sql .= " AND o.user_id = ".$this->filtr['user_id']." ";
		}
	
		return $sql;
	}
	
	public function insert($data) {
		
		$data['opinion_hash'] = md5($data['opinion_mail'].$data['opinion_nick'].sha1(self::SECRET_KEY));
		$data['opinion_data'] = date("Y-m-d H:i:s");
		$data['status_id'] = 1;
		
		$res = \ConnectDB::subAutoExec('shop_opinion', $data, 'INSERT');
		
		if($res > 0) {
			if($data['opinion_mail'] != '') {
				//wysyłamy maila z podziękowaniem z założenie opinii
			}
			
			return $res;
		}
	}

	
	/**
	 * @param int $idProdukt
	 * @return float
	 */
	public function pobierzOceneDlaProduktu($filtr) {
		
		$sql = "SELECT SUM(opinion_ocena)/COUNT(*) as ile FROM shop_opinion o
				WHERE 1 ";
		
		$sql .= $this->getFiltr($filtr);
		$res = \ConnectDB::subQuery($sql, 'one');
		
		return $res; 
	}
	
	public function weryfikuj($hash, $id_serwisu) {
		
		$row = $this->getOne(array('x.hash' => $hash, 'x.id_serwisu' => $id_serwisu));
		if(is_array($row) && !empty($row) && count($row) > 0) {
			$this->update(array('id_status' => 3), $row['id']);
			return $row;
		} else {
			return false;
		}
				
	}
	
	public function sprawdzHash($hash = null) {
		
		if(strlen($hash) == 32) {
			$one = $this->getOne(array('hash' => $hash)); 
			if(is_array($one) && !empty($one) && count($one) > 0) {
				return $one['id'];
			}
		}
		
		return 0;
	}
		
}
