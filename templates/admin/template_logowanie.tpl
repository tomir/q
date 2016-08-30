<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$siteTitle}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	{literal}
	<style type="text/css">
		html, body {height: 100%; }
		div#distance { margin-bottom: -10em; width: 1px;height: 50%; float: left;}
		div#container {	position: relative;   text-align: left; height: 150px;	width: 575px; margin: 0 auto; padding-top: 65px; clear: left;}
		#form-container {margin-top: 30px;}
		#top {margin-bottom: 10px;}
		
		body {background: url(../images/admin/bg-login.jpg) repeat-x center; margin: 0; padding: 0;
		font-family: Helvetica, Arial, Tahoma, serif; font-size: 9pt;}
		h1 {font-size:250%; text-transform:uppercase; letter-spacing:-1px; font-weight:bold; width:450px; 
			margin: 0 0 35px 0; padding: 0;}
		h1 a {color:#fff; text-decoration:none;}
		h1 a:hover {color:#ccc;}
		
		fieldset, form {margin: 0; padding: 0; border: 0; outline: 0;}
		fieldset legend {display: none;}
		
		ol {margin: 0; padding: 0; list-style: none;}
		ol li {float: left; margin-right: 15px;}
		label {display: block;}
		label.field-title {width:75px; color:#fff; font-weight: bold; float: left; padding-top: 3px;}
		label.txt-field {width: 186px; height: 21px; background: url(../images/admin/bg-loginboxes.gif) no-repeat; float: left; margin-right: 10px}
		label.txt-field input {border: none; outline: none; background: none; padding: 2px 0 0 8px;}
		label.remember {color:#ccc; float:left; width: 200px; margin-top: 20px; margin-left:75px; margin-right: 215px;}
		div.align-right {float: left; width: 56px;  margin-top: 20px;}
		.komunikat_box_zew {width: 360px;padding: 10px;border: 1px solid #535A5F;background: #EFF8FF;margin: 0 auto;font-size: 14px;font-weight: bold;margin-top: 10px;}
		.komunikat_box_wew {width: 260px;margin-left: 8px;margin-top: 5px;float: left;}
	</style>
	{/literal}
</head>
<body>
<div id="distance"></div>
<div id="container">
	<div id="top"><a href="http://tform.net" style="height: 47px; width: 150px; display: block;" title="Strona główna tForm"><img style="border: 0;" alt="tForm.net" src="../images/admin/logo_tff.png"></a></div>
	<div id="form-container">
		{if $komunikat}
			<div style="margin-bottom: 10px;">
				{$komunikat}
			</div>
			<br style="clear: both" />
		{/if}
		<form method="post" id="login-form" action="{$wwwPanelPatch}zaloguj.html">
			<fieldset>
				<legend>Logowanie</legend>
				<ol>
					<li><label class="field-title" style="width: 82px;">Użytkownik:</label><label class="txt-field"><input type="text" name="login"/></label></li>
					<li><label class="field-title" style="width: 45px;">Hasło:</label><label class="txt-field"><input type="password" name="haslo"/></label></li>
					<li><div class="align-right" style="margin-left: 468px;">
						<input type="image" src="../images/admin/bt-login.gif" name="submit"/>
					</div></li>
				</ol>
			</fieldset>
		</form>
	</div>
</div>
<br style="clear: both;" /><br /><br /><br />
<div id="footer-wrap" style="width: 100%;">
	<div id="footer" style="width: 470px; margin: 40px auto;">
        <div id="footer-bottom">
        	<p style="color: #fff;">2010 tForm framework | All rights reserved | ver. 0.5 | powerd by <a style="color: #fff;" href="http://www.subvision.pl">subVision.pl</a></p>
        </div> 
	</div>
</div>
</body>
</html>
