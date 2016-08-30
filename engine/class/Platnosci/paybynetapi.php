<?php

/// known searchd commands
define ( "PAY_PASS_KEY",		'm5489rfuhsb23t9efoiksejhf43gsdaa' ); //haslo do szyfrowania hasha
define ( "PAY_HOST_URL",		'https://pbn.paybynet.com.pl/PayByNetT/trans.do' ); //nazwa hosta nadającego test
//define ( "PAY_HOST_URL",		'https://pbn.paybynet.com.pl/PayByNet/trans.do' ); //nazwa hosta nadającego produkcja
//define ( "PAY_HOST_RESULT",		'https://pbn.paybynet.com.pl/axist/services/PBNTransactionsGetStatus?wsdl' ); //nazwa hosta odbierającego test
define ( "PAY_HOST_RESULT",	'https://pbn.paybynet.com.pl/axis/services/PBNTransactionsGetStatus?wsdl' ); //nazwa hosta odbierającego produkcja
define ( "PAY_NIP",				'9542701287' ); //nip firmy odbierającej platności
define ( "PAY_AUTO",			0 ); //brak automatycznego przekierowanie do strony wyboru banku w PayByNet

define ( "PAY_ACCOUNT",			'98114020040000310269184220' ); //kraj
define ( "PAY_DATA_NM",			'zambi.pl' ); //nazwa
define ( "PAY_DATA_ZP",			'41-902' ); //kod pocztowy
define ( "PAY_DATA_CI",			'Bytom' ); //miasto
define ( "PAY_DATA_ST",			'ul.Fałata 10b/9' ); //ulica
define ( "PAY_DATA_CT",			'Polska' ); //kraj

define ( "PAY_TIME",			86400 ); //24h wazności płatności
