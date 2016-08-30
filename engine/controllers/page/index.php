<?php
$atr2 = 'index';

if (isset($_GET['page']) && strlen($_GET['page']) > 3) {
    $page = $_GET['page'];
    
    $obPage = new Page();
    
    $aText = $obPage->get($page);
    
    if (!is_array($aText)) {
		header("HTTP/1.1 404 Not Found");
        header("Location:".$wwwPatch);
    }
    
} else {
	header("HTTP/1.1 404 Not Found");
    header("Location:".$wwwPatch);
}

$head_title = $aText['page_title'].' -';

?>
