<?php
namespace Enp\Sphinx\Enum;

class Pola {

	static protected $pola = array(
		'p0_.nrKatalogowy'	=> 'Nr. katalogowy',
		'p0_.model'			=> 'Model',
		'p0_.kodProducenta' => 'Kod producenta',
		'p0_.ean'			=> 'EAN',
		'p0_.nazwa'			=> 'Nazwa', //jest w nakladce
		'p0_.typ'			=> 'Typ', 	// jest w nakladce
		'p3_.nazwa'			=> 'Nazwa kategorii',
		'p5_.nazwa'			=> 'Nazwa producenta'
	);

	static protected $polaStale = array(
		'p0_.id',
		//'p0_.shopVisible AS aktywny'
	);

	static protected $sqlWarunki = array(
		'p0_.shopVisible = 1'
	);

	static public function getPola() {
		return self::$pola;
	}

	/**
	 * Dopisane sa:
	 * AND p2_.domyslna = 1 >> LEFT JOIN produkt_kategoria_produkt p2_ ON p0_.id = p2_.produkt_id AND p2_.domyslna = 1
	 *
	 *
	 * @var string
	 */
	static protected $SQL =
'\
SELECT {POLA} \
FROM produkt p0_ \
LEFT JOIN produkt_nakladka_serwis p1_ ON p0_.id = p1_.produkt_id AND (p1_.serwis_id = {SERWIS_ID}) \
LEFT JOIN produkt_kategoria_produkt p2_ ON p0_.id = p2_.produkt_id AND p2_.domyslna = 1 \
LEFT JOIN produkt_kategoria p3_ ON p2_.kategoria_id = p3_.id \
LEFT JOIN produkt_kategoria_nakladka_serwis p4_ ON p3_.id = p4_.kategoria_id AND (p4_.serwis_id = {SERWIS_ID}) \
LEFT JOIN produkt_producent p5_ ON p0_.producent_id = p5_.id';

	static protected $SQLINFO = 'SELECT id FROM produkt WHERE id=$id';

	static public function getSQLInfo() {
		return self::$SQLINFO;
	}

	static public function getSQL($pola, $serwisID) {


		$select = array();

		foreach(self::$polaStale as $pole) {
			array_push($select, $pole);
		}

		foreach($pola as $pole) {
			if (array_key_exists($pole, self::$pola)) {
				$select[] = $pole;
			}
		}

		$replace = array(
			'{POLA}' 			=> implode(' , ', $select),
			'{SERWIS_ID}'		=> $serwisID
		);

		$sql = str_replace(array_keys($replace), array_values($replace), self::$SQL);

		$where = array();
		foreach(self::$sqlWarunki as $one) {
			$where[] = $one;
		}

		if (count($where) > 0) {
			$sqlWhere = implode(' AND ', $where);
			$sql .= " \\\n".'WHERE '.$sqlWhere;
		}

		return $sql;
	}


	static public function getInfixFields($pola, $polaInfix) {

		$infix = array();

		foreach ($polaInfix as $pole) {

			if (array_key_exists($pole, self::$pola) && in_array($pole, $pola)) {
				$infix[] = $pole;
			}
		}

		return implode(' , ',$infix);

	}
}