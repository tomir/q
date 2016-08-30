<?
	/**
	 * language pack
	 * @author Logan Cai (cailongqun@yahoo.com.cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 * 
	 * @Polish language Translation
	 * @by Tomkiewicz (mail at webpage)
	 * @http://www.wasilczyk.pl
	 * @10/06/2007
	 */
	define('DATE_TIME_FORMAT', 'Y-M-d H:i:s');
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', 'Odśwież');
		define("LBL_ACTION_DELETE", 'Usuń');
		define('LBL_ACTION_CUT', 'Wytnij');
		define('LBL_ACTION_COPY', 'Kopiuj');
		define('LBL_ACTION_PASTE', 'Wklej');
		//File Listing
	define('LBL_NAME', 'Nazwa');
	define('LBL_SIZE', 'Rozmiar');
	define('LBL_MODIFIED', 'Zmodyfikowano');
		//File Information
	define('LBL_FILE_INFO', 'Informacje o pliku:');
	define('LBL_FILE_NAME', 'Nazwa:');	
	define('LBL_FILE_CREATED', 'Utworzony:');
	define("LBL_FILE_MODIFIED", 'Zmodyfikowany:');
	define("LBL_FILE_SIZE", 'Rozmiar:');
	define('LBL_FILE_TYPE', 'Typ:');
	define("LBL_FILE_WRITABLE", 'Do zapisu?');
	define("LBL_FILE_READABLE", 'Do odczytu?');
		//Folder Information
	define('LBL_FOLDER_INFO', 'Informacje o folderze');
	define("LBL_FOLDER_PATH", 'Ścieżka:');
	define("LBL_FOLDER_CREATED", 'Utworzony:');
	define("LBL_FOLDER_MODIFIED", 'Zmodyfikowany:');
	define('LBL_FOLDER_SUDDIR', 'Podkatalogi:');
	define("LBL_FOLDER_FIELS", 'Pliki:');
	define("LBL_FOLDER_WRITABLE", 'Do zapisu?');
	define("LBL_FOLDER_READABLE", 'Do odczytu?');
		//Preview
	define("LBL_PREVIEW", 'Podgląd');
	//Buttons
	define('LBL_BTN_SELECT', 'Wybierz');
	define('LBL_BTN_CANCEL', 'Anuluj');
	define("LBL_BTN_UPLOAD", 'Wyślij');
	define('LBL_BTN_CREATE', 'Utwórz');
	define('LBL_BTN_CLOSE', 'Zamknij');
	define("LBL_BTN_NEW_FOLDER", 'Nowy Folder');
	define('LBL_BTN_EDIT_IMAGE', 'Edytuj');
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', 'Nie zaznaczono dokumentów do wycięcia.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', 'Nie zaznaczono dokumentów do skopiowania.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', 'Nie zaznaczono dokumentów do wklejenia.');
	define('WARNING_CUT_PASTE', 'Na pewno chcesz przenieść zaznaczone dokumenty do bieżącego folderu?');
	define('WARNING_COPY_PASTE', 'Na pewno chcesz skopiować zaznaczone dokumenty do bieżącego folderu?');
	
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', 'Wybierz plik.');
	define('ERR_NOT_DOC_SELECTED', 'Nie zaznaczono dokumentów do usunięcia.');
	define('ERR_DELTED_FAILED', 'Nie udało się usunąć zaznaczonych dokumentów.');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', 'Wybrana ścieżka katalogu nie jest dozwolona.');
		//class manager
	define("ERR_FOLDER_NOT_FOUND", 'Nie można znaleźć: ');
		//rename
	define('ERR_RENAME_FORMAT', 'Nazwa może zawierać tylko litery, cyfry, spacje, kreskę i podkreślenie.');
	define('ERR_RENAME_EXISTS', 'Podana nazwa już istnieje.');
	define('ERR_RENAME_FILE_NOT_EXISTS', 'Plik/folder nie istnieje.');
	define('ERR_RENAME_FAILED', 'Nie mogę zmienić nazwy, spróboj ponownie.');
	define('ERR_RENAME_EMPTY', 'Podaj nazwę.');
	define("ERR_NO_CHANGES_MADE", 'Nie wprowadzono zmian.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', 'Nie masz uprawnień do zmiany rozszerzenia na podane.');
		//folder creation
	define('ERR_FOLDER_FORMAT', 'Nazwa może zawierać tylko litery, cyfry, spacje, kreskę i podkreślenie.');
	define('ERR_FOLDER_EXISTS', 'Podana nazwa już istnieje.');
	define('ERR_FOLDER_CREATION_FAILED', 'Nie mogę utworzyć folderu, spróbuj ponownie.');
	define('ERR_FOLDER_NAME_EMPTY', 'Podaj nazwę.');
	
		//file upload
	define("ERR_FILE_NAME_FORMAT", 'Nazwa może zawierać tylko litery, cyfry, spacje, kreskę i podkreślenie.');
	define('ERR_FILE_NOT_UPLOADED', 'Nie wybrano pliku do wysłania.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', 'Nie masz uprawnień do wysyłania plików tego typu.');
	define('ERR_FILE_MOVE_FAILED', 'Nie udało się przenieść pliku.');
	define('ERR_FILE_NOT_AVAILABLE', 'Plik jest niedostępny.');
	define('ERROR_FILE_TOO_BID', 'Plik jest za duży (maksimum: %s)');
	

	//Tips
	define('TIP_FOLDER_GO_DOWN', 'Kliknij, aby otworzyć folder...');
	define("TIP_DOC_RENAME", 'Kliknij podwójnie, aby edytować...');
	define('TIP_FOLDER_GO_UP', 'Kliknij, aby wejść folder wyżej...');
	define("TIP_SELECT_ALL", 'Zaznacz wszystko');
	define("TIP_UNSELECT_ALL", 'Odznacz wszystko');
	//WARNING
	define('WARNING_DELETE', 'Jesteś pewien, że chcesz usunąć zaznaczone pliki?');
	define('WARNING_IMAGE_EDIT', 'Wybierz obraz do edycji.');
	define('WARING_WINDOW_CLOSE', 'Jesteś pewien, że chcesz zamknąć okno?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', 'Podgląd niedostępny.');
	define('PREVIEW_OPEN_FAILED', 'Nie udało się otworzyć pliku.');
	define('PREVIEW_IMAGE_LOAD_FAILED', 'Nie udało się wczytać obrazka');

	//Login
	define('LOGIN_PAGE_TITLE', 'Formularz logowania Ajax File Manager');
	define('LOGIN_FORM_TITLE', 'Formularz logowania');
	define('LOGIN_USERNAME', 'Login:');
	define('LOGIN_PASSWORD', 'Hasło:');
	define('LOGIN_FAILED', 'Niepoprawny login lub hasło.');
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', "Nie wprowadziłeś zmian.");
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', 'Obraz nie istnieje');
		define('IMG_WARNING_LOST_CHANAGES', 'Wszystkie nie zapisane zmiany zostaną utracone, kontynuować?');
		define('IMG_WARNING_REST', 'Wszystkie nie zapisane zmiany zostaną utracone, kontynuować?');
		define('IMG_WARNING_EMPTY_RESET', 'Nie wprowadzono jeszcze jakichkolwiek zmian');
		define('IMG_WARING_WIN_CLOSE', 'Na pewno chcesz zamknąć okno?');
		define('IMG_WARNING_UNDO', 'Na pewno przywrócić obrazek do poprzedniego stanu?');
		define('IMG_WARING_FLIP_H', 'Na pewno odbić obrazek poziomo?');
		define('IMG_WARING_FLIP_V', 'Na pewno odbić obrazek pionowo?');
		define('IMG_INFO', 'Informacje o obrazku');
		
		//Mode
			define('IMG_MODE_RESIZE', 'Skaluj:');
			define('IMG_MODE_CROP', 'Przytnij:');
			define('IMG_MODE_ROTATE', 'Obróć:');
			define('IMG_MODE_FLIP', 'Odbij:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg; w lewo');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg; w prawo');
			define('IMG_BTN_FLIP_H', 'Odbij poziomo');
			define('IMG_BTN_FLIP_V', 'Odbij pionowo');
			define('IMG_BTN_RESET', 'Resetuj');
			define('IMG_BTN_UNDO', 'Cofnij');
			define('IMG_BTN_SAVE', 'Zapisz');
			define('IMG_BTN_CLOSE', 'Zamknij');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', 'Wymusić?');
		//Label
			define('IMG_LBL_WIDTH', 'Szerokość:');
			define('IMG_LBL_HEIGHT', 'Wysokość:');
			define('IMG_LBL_X', 'X:');
			define('IMG_LBL_Y', 'Y:');
			define('IMG_LBL_RATIO', 'Proporcje:');
			define('IMG_LBL_ANGLE', 'Kąt:');
		//Editor

			
		//Save
		define('IMG_SAVE_EMPTY_PATH', 'Pusta ścieżka obrazka.');
		define('IMG_SAVE_NOT_EXISTS', 'Obrazek nie istnieje.');
		define('IMG_SAVE_PATH_DISALLOWED', 'Nie masz uprawnień do tego pliku.');
		define('IMG_SAVE_UNKNOWN_MODE', 'Nieoczekiwany tryb operacji na obrazku');
		define('IMG_SAVE_RESIZE_FAILED', 'Nie udało się przeskalować obrazka.');
		define('IMG_SAVE_CROP_FAILED', 'Nie udało się przyciąć obrazka.');
		define('IMG_SAVE_FAILED', 'Nie udało się zapisać obrazka.');
		define('IMG_SAVE_BACKUP_FAILED', 'Nie udało się zapisać kopii zapasowej obrazka.');
		define('IMG_SAVE_ROTATE_FAILED', 'Nie udało się obrócić obrazka.');
		define('IMG_SAVE_FLIP_FAILED', 'Nie udało się odbić obrazka.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', 'Nie udało się otworzyć obrazka z sesji.');
		define('IMG_SAVE_IMG_OPEN_FAILED', 'Nie udało się otworzyć obrazka.');
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', 'Nie da się cofnąć.');
		define('IMG_UNDO_COPY_FAILED', 'Nie da się przywrócić obrazka.');
		define('IMG_UNDO_DEL_FAILED', 'Nie da się usunąć obrazka z sesji');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   Session   888888888888888888888
		define("SESSION_PERSONAL_DIR_NOT_FOUND", 'Nie udało się znaleźć folderu sesji.');
		define("SESSION_COUNTER_FILE_CREATE_FAILED", 'Nie udało się otworzyć pliku licznika sesji.');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', 'Nie udało się zapisać pliku licznika sesji.');
	//88888888888   Session   888888888888888888888
	
	
?>