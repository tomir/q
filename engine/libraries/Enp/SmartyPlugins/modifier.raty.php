<?php

/**
 * Modifier pokazuje wartosc rat wg oprocentowania i ilosci rat.
 * 
 * @param flaot $wartosc cena produktu
 * @param int $iloscRat
 * @param int $oprocentowanie w formie np 0.1 -> 10%
 * @return float
 * 
 * @author aswierc
 */
function smarty_modifier_raty($wartosc, $iloscRat, $oprocentowanie)
{
	$iloscRat = (int) $iloscRat; 
	$oprocentowanie = (float) $oprocentowanie; 
	
	$rata = (($wartosc + ($wartosc * $oprocentowanie)) / $iloscRat);
	return sprintf("%.2f", $rata);
}
