<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Wybór filmu wideo</title>
<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/jquery-1.3.2.min.js" ></script>
<script src="<?php echo $wwwPatch; ?>js/admin/jqueryFileTree.js" type="text/javascript"></script>
<link href="<?php echo $wwwPatch; ?>css/admin/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo $wwwPatch; ?>js/admin/RelatedObjectLookups.js"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$('#fileTreeDemo_1').fileTree({ loadMessage: 'Ładowanie plików...proszę czekać', root: '/www/tawizja/pliki/', script: '<?php echo $adminPatch; ?>jqueryFileTree.php' }, function(file) {
			opener.dismissRelatedLookupPopup(window, file, ''); return false;
		});

	});
</script>
<style type="text/css">
	.wait {background: url(/images/admin/ajax-loader2.gif) no-repeat; width: 220px; height: 19px; }
</style>
</head>
<body>
	<div>
		<div style="background-color: #ECECEC; margin-left: 5px; width: 580px;">
			<h3 class="news_naglowek" style="margin-top: 0px;">Wybierz wideo</h3>
		</div>
		<div class="news_box_dol" style="margin-left: 8px;">
			<div id="fileTreeDemo_1" class="demo"></div>
		</div>
	</div>
</body>
</html>
