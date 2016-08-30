<?php

/// known searchd commands
define ( "PAY_PASS_KEY",		'' ); //haslo do szyfrowania hasha
define ( "PAY_HOST_URL",		'https://pbn.paybynet.com.pl/PayByNetT/trans.do' ); //nazwa hosta nadającego test
//define ( "PAY_HOST_URL",		'https://pbn.paybynet.com.pl/PayByNet/trans.do' ); //nazwa hosta nadającego produkcja
define ( "PAY_HOST_RESULT",		'https://pbn.paybynet.com.pl/axist/services/PBNTransactionsGetStatus?wsdl' ); //nazwa hosta odbierającego test
//define ( "PAY_HOST_RESULT",	'https://pbn.paybynet.com.pl/axis/services/PBNTransactionsGetStatus?wsdl' ); //nazwa hosta odbierającego produkcja
define ( "PAY_NIP",				'' ); //nip firmy odbierającej platności
define ( "PAY_AUTO",			0 ); //brak automatycznego przekierowanie do strony wyboru banku w PayByNet

define ( "PAY_ACCOUNT",			'' ); //kraj
define ( "PAY_DATA_NM",			'' ); //nazwa
define ( "PAY_DATA_ZP",			'' ); //kod pocztowy
define ( "PAY_DATA_CI",			'' ); //miasto
define ( "PAY_DATA_ST",			'' ); //ulica
define ( "PAY_DATA_CT",			'' ); //kraj

define ( "PAY_TIME",			86400 ); //24h wazności płatności