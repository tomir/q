<?php

class AllegroApi_Exception extends SoapFault{
	private $faultcodeMessages = array(
		'ERR_ITEMS_ARRAY_OVERSIZED' => 'Wybierz maksymalnie 25 produktów',
		'ERROR_NOT_DEFINED' => 'Nieobsłużony wyjątek',
		'ERR_USER_PASSWD' => 'Błędne hasło',
		'ERR_LIST_THUMB_AND_NO_PHOTO' => 'Aby skorzystać z opcji miniaturka, należy załadować co najmniej jedno zdjęcie.',
		'ERR_ALCOHOL_NO_COMPANY' => 'Aby wystawiać produkty w kategorii Wina należy posiadać konto Firma.',
		'ERR_ALCOHOL_NOT_ACTIVE' => 'Aby wystawiać produkty w kategorii Wina należy posiadać aktywny status Alkohol.',
		'ERR_AUCTION_TYPE_NOT_EXISTS' => 'Nie można wystawić wieloprzedmiotowej aukcji z licytacją (dotyczy np. Czech, Rosji, Węgier, Rumunii, Słowacji i Bułgarii).',
		'ERR_BAD_PICTURE_FORMAT' => 'Niepoprawny format zdjęcia (dopuszczalne formaty: GIF, JPG, PNG).',
		'ERR_BLOCK_SELL_COMPANY_INFO' => 'Użytkownik z blokadą znaczka Firma nie może wystawiać nowych aukcji.',
		'ERR_BLOCK_SELL_SELL' => 'Użytkownik z blokadą sprzedaży na koncie nie może wystawiać nowych aukcji. Aby wyjaśnić powód blokady, należy skontaktować się z Allegro poprzez formularz kontaktowy.',
		'ERR_BLOCKED_USER_CANT_INVOKE_METHOD' => 'Użytkownik ma zablokowane konto.',
		'ERR_BUY_NOW_LESS_THAN_RESERVE_PRICE' => 'Cena Kup Teraz! nie może być być niższa od ceny minimalnej.',
		'ERR_BUY_NOW_LESS_THAN_STARTING_PRICE' => 'Cena Kup Teraz! nie może być być niższa od ceny wywoławczej.',
		'ERR_CATEGORY_NOT_FOUND' => 'Nie podano identyfikatora kategorii, identyfikator jest błędny, lub wskazana kategoria nie jest kategorią najniższego rzędu.',
		'ERR_CATEGORY_PAGE_AND_NO_PHOTO' => 'Aby skorzystać z opcji promowania aukcji na stronie wskazanej kategorii, należy załadować co najmniej jedno zdjęcie.',
		'ERR_CATEGORY_PAGE_NOT_ENOUGH_MONEY' => 'Nie można ustawić opcji promowania aukcji na stronie kategorii z powodu braku funduszy na koncie wystarczających do uiszczenia przedpłaty (dotyczy Czech).',
		'ERR_CATEGORY_PAGE_RATING_TOO_LOW' => 'Nie można ustawić opcji promowania aukcji na stronie kategorii z powodu niewystarczającej liczby punktów użytkownika (wymagane min. 5 punktów).',
		'ERR_CONVERSION_ERROR' => 'Wyniknęły problemy podczas konwersji zdjęć.',
		'ERR_DEBT_NO_EXTRAS' => 'Stan konta jest zbyt niski, by wystawienie aukcji było możliwe (dotyczy Ukrainy i Bułgarii).',
		'ERR_DESC_TOO_LONG_INFO' => 'Przekroczono maksymalny dopuszczalny rozmiar opisu aukcji (65 kB).',
		'ERR_DESCRIPTION_NOT_FOUND' => 'Nie podano opisu aukcji.',
		'ERR_EAN_CODE_NOT_AVAILABLE' => 'W tej kategorii nie ma możliwości podawania kodu EAN.',
		'ERR_EXPLICIT_CONTENT_WARN' => 'Nie można skorzystać z niektórych opcji dodatkowych w przypadku sprzedaży przedmiotu w tej kategorii.',
		'ERR_FEATURED_NOT_ACTIVATED' => 'Nie można ustawić opcji Wyróżnienie z powodu braku pełnej aktywacji konta (dotyczy Rosji).',
		'ERR_FEATURED_RATING_TOO_LOW' => 'Nie można ustawić opcji Wyróżnienie z powodu zbyt niskiej punktacji w systemie komentarzy (dotyczy Rosji).',
		'ERR_FULFILLMENT_TIME_NOT_AVAILABLE_FOR_CATEGORY' => 'Opcja Czas dostawy nie jest dostępna dla wybranej kategorii.',
		'ERR_IF_YOU_WANNA_SELL' => 'Użytkownik bez pełnej aktywacji konta nie może wystawiać aukcji w serwisie.',
		'ERR_INPUT_COUNTRY_ERROR' => 'Brak/niepoprawny kod kraju.',
		'ERR_INVALID_BUY_NOW_PRICE' => 'Niepoprawna wartość ceny Kup Teraz! (min. 1 zł).',
		'ERR_INVALID_DURATION_TIME' => 'Niepoprawna wartość w polu określającym czas trwania aukcji.',
		'ERR_INVALID_EAN_CODE' => 'Niepoprawny kod EAN.',
		'ERR_INVALID_FIRST_BANK_ACCOUNT_FORMAT' => 'Niepoprawny format pierwszego podanego numeru konta bankowego (dopuszczalny format: 26 cyfr pisanych łącznie, ew. rozdzielonych spacjami lub myślnikami).',
		'ERR_INVALID_PARAM_AUCTION_TYPE' => 'Niepoprawna wartość w polu określającym format sprzedaży.',
		'ERR_INVALID_PARAM_SELL_BY_SET' => 'Niepoprawna wartość w polu określającym sztuki/komplety/pary.',
		'ERR_INVALID_PARAM_SHOP_PROLONG' => 'Niepoprawna wartość w polu określającym automatyczne wznowienie oferty w sklepie.',
		'ERR_INVALID_POSTCODE_FORMAT' => 'Niepoprawny format kodu pocztowego (dopuszczalny format: 5 cyfr w postaci XX-XXX).',
		'ERR_INVALID_PRODUCTS_CATALOGUE' => 'Błędny identyfikator produktu.',
		'ERR_INVALID_QUANTITY' => 'Niepoprawna wartość w polu określającym liczbę sztuk (dopuszczalny zakres: 1-999).',
		'ERR_INVALID_RESERVE_PRICE' => 'Niepoprawna wartość ceny minimalnej.',
		'ERR_INVALID_SECOND_BANK_ACCOUNT_FORMAT' => 'Niepoprawny format drugiego podanego numeru konta bankowego (dopuszczalny format: 26 cyfr pisanych łącznie, ew. rozdzielonych spacjami lub myślnikami).',
		'ERR_INVALID_STARTING_PRICE' => 'Niepoprawna wartość ceny wywoławczej (min. 1 zł).',
		'ERR_INVALID_STATE' => 'Niepoprawna wartość w polu określającym województwo.',
		'ERR_INVALID_VALUE_IN_ATTRIB_FIELD' => 'Niepoprawne przekazanie parametrów struktury (np. przekazano nieistniejące pole formularza sprzedaży).',
		'ERR_INVALID_VALUE_IN_LOCAL_ID' => 'Niepoprawna wartość lokalnego identyfikatora (dopuszczalny zakres: 1-9999999999999).',
		'ERR_ITEM_NAME_NOT_FOUND' => 'Nie podano tytułu aukcji.',
		'ERR_ITEM_NAME_TOO_LONG' => 'Przekroczono maksymalny dopuszczalny rozmiar tytułu aukcji (50 znaków, przy założeniu, że niektóre symbole liczone są jako więcej niż 1 znak: < i > - 4 znaki,  & - 5 znaków, " - 6 znaków).',
		'ERR_JUNIOR_CANT_BID_LIST_IN_THIS_CATEGORY' => 'Użytkownik konta Junior nie może wystawić aukcji w wybranej kategorii (ograniczenia dot. kategorii: Nieruchomości, Samochody, Motocykle, Inne pojazdy i łodzie, Trafika, Broń, Wiatrówki, Erotyka).',
		'ERR_JUNIOR_LIMIT_EXCEEDED' => 'Przekroczono limit 50 zł na wystawianie aukcji, który obowiązuje dla użytkowników konta Junior.',
		'ERR_LIST_THUMB_AND_NO_PHOTO' => 'Aby skorzystać z opcji miniaturka, należy załadować co najmniej jedno zdjęcie.',
		'ERR_LOCATION_NOT_FOUND' => 'Nie podano lokalizacji przedmiotu (miejscowości).',
		'ERR_LOCATION_TOO_LONG' => 'Przekroczono maksymalny dopuszczalny rozmiar nazwy miejscowości (40 znaków).',
		'ERR_MAIN_AND_CATEGORY_PAGE_NOT_ENOUGH_MONEY' => 'Nie można ustawić opcji promowania aukcji na stronie głównej serwisu oraz stronie kategorii z powodu braku funduszy na koncie wystarczających do uiszczenia przedpłaty (dotyczy Czech).',
		'ERR_MAIN_PAGE_INCORRECT_CATEGORY' => 'Dla wybranej kategorii nie można ustawić opcji promowania oferty na stronie głównej serwisu.',
		'ERR_MAIN_PAGE_NOT_ENOUGH_MONEY' => 'Nie można ustawić opcji promowania aukcji na stronie głównej serwisu z powodu braku funduszy na koncie wystarczających do uiszczenia przedpłaty (dotyczy Czech).',
		'ERR_MAIN_PAGE_RATING_TOO_LOW' => 'Nie można ustawić opcji promowania aukcji na stronie głównej  serwisu z powodu niewystarczającej liczby punktów użytkownika (wymagane min. 10 punktów).',
		'ERR_MULTI_ITEM_AND_BUYNOW_AND_STARTING' => 'Dla aukcji wieloprzedmiotowych należy wybrać albo cenę Kup Teraz!, albo cenę wywoławczą (nie obie równocześnie).',
		'ERR_NO_DATABASE' => 'Problemy z bazą danych Allegro.',
		'ERR_NO_ON_DELIVERY_PAYMENT' => 'Dla wybranych opcji płatności przy odbiorze należy wybrać odpowiednią formę płatności.',
		'ERR_NO_PREPAID_PAYMENT' => 'Dla wybranych opcji płatności z góry należy wybrać odpowiednią formę płatności.',
		'ERR_NO_SESSION / ERR_SESSION_EXPIRED' => 'Niepoprawny identyfikator sesji lub sesja wygasła.',
		'ERR_NO_STARTING_AND_BUY_NOW_PRICE' => 'Nie podano min. jednego formatu ceny (wywoławcza i/lub Kup Teraz!).',
		'ERR_NOT_ALLOWED_CHARS_IN_ITEM_NAME_OFFLINER' => 'W tytule aukcji użyto niedozwolonych znaków. Wszystkie użyte znaki muszą istnieć w alfabecie kraju zalogowanego użytkownika.',
		'ERR_NOT_ENOUGH_MONEY_TO_SELL' => 'Stan konta jest zbyt niski, by wystawić aukcję z płatnymi opcjami promowania (dotyczy Czech i Rosji).',
		'ERR_OBLIGATORY_ATTRIB_NOT_SET' => 'Nie podano parametru wymaganego dla wybranej kategorii.',
		'ERR_OTHER_PHOTO_ERROR' => 'Wyniknęły problemy podczas przesyłania zdjęć na serwer.',
		'ERR_PAY_FORM_NOT_SELECTED' => 'Nie podano formy płatności.',
		'ERR_PAYS_SHIPPING_NOT_SELECTED' => 'Nie podano wartości w polu określającym, która strona pokrywa koszty transportu.',
		'ERR_PHARMACY_NO_COMPANY' => 'Aby móc wystawiać produkty w kategorii Leki bez recepty należy posiadać konto Firma.',
		'ERR_PHARMACY_NOT_ACTIVE' => 'Aby móc wystawiać produkty w kategorii Leki bez recepty należy posiadać aktywny status Apteka.',
		'ERR_PHARMACY_NOT_ALLOWED_AUCTION_TYPE' => 'W kategorii Leki bez recepty wystawiane być mogą wyłącznie oferty Kup Teraz!.',
		'ERR_PHARMACY_REQUIREMENTS_NOT_MET' => 'Aby móc wystawiać produkty w kategorii Leki bez recepty należy brać udział w programie Aukro+, a wystawiany towar nie może być w stanie innym niż nowy (dotyczy Czech).',
		'ERR_PHOTO_TOO_LARGE' => 'Przekroczono maksymalny dopuszczalny format zdjęcia (200 kB).',
		'ERR_POSTAGE_OPTIONS_ERROR' => 'Nie podano minimum jednego sposobu dostawy.',
		'ERR_POSTAGE_OPTIONS_VALUES_INCORRECT' => 'Niepoprawnie wypełniono koszta dostawy (należy podać wartość we wszystkich trzech polach: pierwsza sztuka, kolejna sztuka, ilość w paczce - lub jedynie dla pierwszej sztuki).',
		'ERR_POSTAGE_VALUES_SELLER_PAYMENT' => 'Nie można podać cen za dostawę większych od 0, gdy sprzedający pokrywa koszty przesyłki.',
		'ERR_POSTAL_CODE_EMPTY_ERROR' => 'Nie podano kodu pocztowego.',
		'ERR_PRODUCT_CATALOGUE_FOTO_NOT_ENABLED' => 'Aby załączyć do oferty zdjęcie pochodzące z katalogu Produktów w Allegro, aukcja musi być powiązana z produktem.',
		'ERR_PRODUCTS_CATEGORY_NOT_AVAILABLE' => 'W tej kategorii nie udostępniono możliwości powiązania aukcji z Produktem w Allegro.',
		'ERR_RESERVE_LESS_THAN_STARTING_PRICE' => 'Cena minimalna nie może być niższa od ceny wywoławczej.',
		'ERR_RESERVED_PRICE_NOT_ALLOWED' => 'Cena minimalna jest niedopuszczalna dla tego formatu aukcji.',
		'ERR_RESERVED_PRICE_RATING_TOO_LOW' => 'Nie można ustawić ceny minimalnej z powodu niewystarczającej liczby punktów użytkownika (dotyczy Rosji i Ukrainy).',
		'ERR_SELECT_PHOTO' => 'Brak danych zdjęcia.',
		'ERR_SELL_NOT_ALLOWED' => 'Użytkownik nie może wystawić aukcji w tym kraju.',
		'ERR_SHIPMENT_DESCRIPTION_REQUIRED' => 'Po wskazaniu chęci przekazania dodatkowych informacji o przesyłce i płatności, należy podać ich treść.',
		'ERR_SHOP_BUY_NOW_PRICE_ERROR' => 'Aukcja wystawiana w Sklepie Allegro musi mieć ustaloną cenę Kup Teraz!.',
		'ERR_SHOP_CANT_LIST_IN_THIS_COUNTRY' => 'Użytkownik nie ma aktywnego Sklepu Allegro lub nie może wystawiać aukcji sklepowych w tym kraju.',
		'ERR_SHOP_CATEGORY_NOT_FOUND' => 'Niepoprawna wartość identyfikatora kategorii sklepowej (informacje o kategoriach sklepowych zalogowanego użytkownika pobrać można za pomocą metody doGetShopCatsData)',
		'ERR_SHOP_NOT_ALLOWED_CATS' => 'Dla aukcji wystawianych w Sklepie Allegro nie można skorzystać z wybranej kategorii (ograniczenia dot. kategorii: Nieruchomości, Samochody, Motocykle, Inne pojazdy i łodzie, Wakacje).',
		'ERR_SHOP_NOT_ALLOWED_OPTIONS' => 'Dla aukcji wystawianych w Sklepie Allegro nie można ustawić opcji promowania aukcji na stronie głównej  serwisu, stronie kategorii oraz wyróżnienia aukcji na liście kategorii.',
		'ERR_SHOP_PRIVATE_AUCTION_ERROR' => 'Aukcja prywatna nie może być wystawiana w Sklepie Allegro (dotyczy Ukrainy).',
		'ERR_SHOP_RESERVE_PRICE_ERROR' => 'Aukcja wystawiana w Sklepie Allegro nie może mieć ustawionej ceny minimalnej.',
		'ERR_SHOP_STARTING_PRICE_ERROR' => 'Aukcja wystawiana w Sklepie Allegro nie może mieć ustawionej ceny wywoławczej.',
		'ERR_SOME_ITEMS_AND_RESERVE_PRICE' => 'Cena minimalna jest niedopuszczalna dla aukcji wieloprzedmiotowych.',
		'ERR_TICKETS_AND_AUCTION' => 'Bilety i wejściowki na imprezy artystyczne, rozrywkowe lub sportowe muszą mieć ustaloną cenę Kup Teraz!.',
		'ERR_TOO_DISTANT_FUTURE' => 'Niepoprawna (zbyt odległa) data rozpoczęcia aukcji (maks. 30 dni naprzód).',
		'ERR_TOO_MANY_FUTURE_AT_THIS_TIME' => 'Przekroczono ogólny limit aukcji do planowanego wystawienia (1000) w wybranej 5-minutowej puli.',
		'ERR_TOO_MANY_ITEMS' => 'Przekroczono limit na wystawianie aukcji przez użytkowników bez pełnej aktywacji konta (dotyczy Rosji).',
		'ERR_TOO_MANY_YOUR_FUTURE_AUCTIONS' => 'Przekroczono jednostkowy (dla użytkownika) limit aukcji do planowanego wystawienia (1000).',
		'ERR_TRANSPORT_SHIPMENT_DESCRIPTION_TOO_LONG' => 'Przekroczono maksymalny dopuszczalny rozmiar pola z dodatkowymi informacjami o przesyłce i płatności (500 znaków).',
		'ERR_WEBAPI_NOT_AVAIL' => 'Problemy z usługą Allegro WebAPI.',
		'ERR_WORD_IN_ITEM_NAME_TOO_LONG' => 'Przekroczono maksymalny dopuszczalny rozmiar pojedynczego słowa w tytule aukcji (30 znaków).',
		'ERR_YOU_HAVE_TO_ACTIVE_CODE' => 'Użytkownik bez aktywowanego kodu sprzedaży nie może wystawiać aukcji w serwisie.'

	);
	/**
	 * Dodatkowe parametry
	 * @var array 
	 */
	private $param = array();
	
	public function __construct($message, $code='ERROR_NOT_DEFINED', array $param = array()) {
		$this->message = $message;
		$this->code = $code;
		$this->param = $param;
		
		// message
		if(empty($message)) {	
			$this->message = $this->faultcodeMessages['ERROR_NOT_DEFINED'];
			if(array_key_exists($code, $this->faultcodeMessages)) {
				$this->message =  $this->faultcodeMessages[$code];
			}	
		}
		
		$this->faultstring = $this->message;
		$this->faultcode= $this->code;
    }
	
	public function getParam() {
		return $this->param;
	}
	
};