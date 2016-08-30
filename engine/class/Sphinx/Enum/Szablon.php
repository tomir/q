<?php
namespace Enp\Sphinx\Enum;

class Szablon {

	/**
	 * NAME
	 * SQL
	 * SQL_INFO
	 * INFIX_FIELDS
	 *
	 * @var string
	 */
	static protected $SZABLON =
'source {NAME}_source
{
	type			= mysql

	sql_host		= localhost
	sql_user		= root
	sql_pass		=
	sql_db			= mshp_local
	sql_port		= 3306	# optional, default is 3306

	sql_query_pre   = SET NAMES utf8

	sql_query		= {SQL}

	#parametr po ktorym bedziemy filtrowac wyniki przez sphinxa
	#sql_attr_uint		= aktywny

	# tak zostana zwrocone wyniki
	# tylko id produktu
	sql_query_info		= {SQL_INFO}
}

index {NAME}_index
{
	# ponizesz 3 pola sprawiaja ze dziala to tak
	# produkt o nazwie p.nazwa LIKE %"abcdef"% bedzie wyszukany dla fraz:
	# - "abc*"
	enable_star 	= 1
	min_infix_len 	= 2 #dlugos najkrotczych ciagow na jakie rozbic kolumny z infix_fileds
	infix_fields	= {INFIX_FIELDS}


	source         	= {NAME}_source #nazwa zdefiniowanego zrodla
	path           	= ./data/mshp_source
	docinfo        	= extern
	mlock          	= 0
	morphology  	= stem_en

	# slownik odmian : telewizory => telewizor, pralki => pralka
	wordforms    	= ./morfologia/wordform-pl-dict-urf-8
	# slownik slow pomijanych : a, od, do, i, aby, lecz, lub, ...
	stopwords       = ./morfologia/wikipedia-pl-utf-8


	# minimalna dlugos slow indeksowanych , domyslnie 1 (index everything).
	min_word_len 	= 1

	# kodowanie znakow
	charset_type  	= utf-8
	# tablica znakow jakie ma indeksowac
	# 0..9 - wszystkie cyfry
	# a..z - male litery
	# A..Z->a..z - duze litery, ale jako male litery
	# dziwne znaczki to polskie litery, ktore ma zamieniac na znaki bez ogonkow
	# U+0144->n - to male ni zamieniane na n
	charset_table = 0..9, a..z, A..Z->a..z, \
					U+0143->N, \
					U+0144->n, \
					U+0104->A, \
					U+0105->a, \
					U+0106->C, \
					U+0107->c, \
					U+0118->E, \
					U+0119->e, \
					U+0141->L, \
					U+0142->l, \
					U+00D3->O, \
					U+00F3->o, \
					U+015A->S, \
					U+015B->s, \
					U+0179->Z, \
					U+017A->z, \
					U+017B->Z, \
					U+017C->z

}
';

	static public function get($sql, $sqlInfo, $infixFileds, $name = 'adora') {


		$replace = array(
			'{SQL}' 			=> $sql,
			'{SQL_INFO}'		=> $sqlInfo,
			'{INFIX_FIELDS}'	=> $infixFileds,
			'{NAME}'			=> $name
		);

		$szablon = str_replace(array_keys($replace), array_values($replace), self::$SZABLON);

		return $szablon;
	}


}