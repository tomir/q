<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$siteTitle}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="../css/admin/reset.css"/>
	<link type="text/css" rel="stylesheet" href="../css/admin/style.css"/>
	<link type="text/css" rel="stylesheet" href="../js/admin/jquery.wysiwyg.css"/>
	<link href="../js/admin/colorbox.css" rel="stylesheet" media="screen" type="text/css"/>
	<link href="../js/admin/colorbox-custom.css" rel="stylesheet" media="screen" type="text/css"/>
	{literal}
	<style type="text/css">
        div.wysiwyg ul.panel li {padding:0px !important;} /**textarea visual editor padding override**/
    </style>
    {/literal}
	<script src="../js/admin/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script src="../js/admin/jquery.colorbox-min.js" type="text/javascript"></script>
	<script src="../js/admin/jquery.ui.js" type="text/javascript"></script>
	<script src="../js/admin/jquery.corners.min.js" type="text/javascript"></script>
	<script src="../js/admin/bg.pos.js" type="text/javascript"></script>
	<script src="../js/admin/jquery.wysiwyg.js" type="text/javascript"></script>
	<script src="../js/admin/tabs.pack.js"  type="text/javascript"></script>
	<script src="../js/admin/cleanity.js" type="text/javascript"></script>
	<script type="text/javascript" src="{$wwwPatch}js/admin/jquery.asmselect.js" ></script>
  	<link rel="stylesheet" type="text/css" href="{$wwwPatch}css/admin/jquery.asmselect.css" />

	{literal}
	<script type="text/javascript">
		function MM_jumpMenu(targ,selObj,restore){
			eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
			if (restore) selObj.selectedIndex=0;
		} 
		
		$(document).ready(function() {
			$("select[multiple]").asmSelect({
				addItemTarget: 'top',
				sortable: true 
			});
		});
	</script>
	<script type="text/javascript">
		
		
		function przejdz(url) {
			url = "{/literal}{$adminPatch}{literal}"+url;
			window.location = url; // pozostale
	        href_remove.href = url; // explorer
		}
	</script>
	{/literal}
</head>
<body>
<div id="container">
    <div class="hidden"><!-- the modal box container - this div is hidden until it is called from a modal box trigger. see cleanity.js for details -->
    <div id="sample-modal"><h2 style="margin: 10px 0pt; font-size: 160%; font-weight: bold;">Modal Box Content</h2><p>Place your desired modal box content here</p></div>
    </div><!-- end of hidden -->
    <div id="header">
		<div id="top">
			<h1><a href="http://tform.net" title="Strona frameworku tForm"><img alt="tForm.net" src="../images/admin/logo_tff.png"></a></h1>
			<p id="userbox">Jesteś zalogowany jako <strong style="color: #333333;">{$zalogowany.login}</strong> |  <a href="zmienhaslo.html">Zmień hasło</a>  |  <a href="wyloguj.html">Wyloguj</a> <br/>
			<small>Ostatnie logowanie: {$zalogowany.data_log|date_format:"%e %B %Y, %H:%M:%S"}</small></p>
			<span class="clearFix"> </span>
		</div>
  		<ul id="menu">
		    <li class="selected"><a href="{$tffPatch}panel.php" style="background-position: 0pt 0pt;">tForm Admin</a></li>
		    <li><a href="#" class="top-level" style="background-position: 0pt 0pt;">Formularze <span> </span></a>
  				<ul>
    				<li><a href="{$tffPatch}stworz.html" style="background-position: 0pt 0pt;">Dodaj formularz</a></li>
    				<li><a href="{$tffPatch}lista.html" style="background-position: 0pt 0pt;">Lista formularzy</a></li>
  				</ul>
    		</li>
    		<li><a href="#" class="top-level" style="background-position: 0pt 0pt;">Akcje JS <span> </span></a>
        		<ul>
        			<li><a href="{$tffPatch}akcje,dodaj.html" style="background-position: 0pt 0pt;">Dodaj akcję</a></li>
			        <li><a href="{$tffPatch}akcje.html" style="background-position: 0pt 0pt;">Lista akcji</a></li>
      			</ul>
      		</li>
      		<li><a href="kontakt.html" style="background-position: 0pt 0pt;">Zgłoś błąd</a></li>
      		<li><a href="{$wwwPatchPanel}" style="background-position: 0pt 0pt;">tForm Panel</a></li>
  		</ul>
 		<form id="form1" name="form1" method="get" action="">
  			<fieldset>
  				<legend>Wyszukaj</legend>
    			<label id="searchbox">
    				<input type="text" id="s" name="s"/>
    			</label>
    			<input type="submit" value="Search" name="Submit" class="hidden"/>
  			</fieldset>
  		</form>
  		<span class="clearFix"> </span>
    </div><!-- end of #header -->

<div id="content">
	<div id="content-top">
    	<h2>tForm Admin</h2>
      	{*<a id="topLink" href="#">Change Order</a> *}
      	<span class="clearFix"> </span>
	</div><!-- end of div#content-top -->
      <div class="full-col" id="mid-col" style="width: 990px !important;"><!-- end of div.box -->
      	{if $komunikat}
			<div style="margin-bottom: 10px;">
				{$komunikat}
			</div>
			<br style="clear: both" />
		{/if}
		<div class="box">
			{$siteContent}
		</div>
      	{*<!-- end of div.box-container -->
      	*}
		</div> <!-- end of div.box --> 
	</div>    
      
	<span class="clearFix"> </span>     
</div><!-- end of div#content -->

<div class="push"></div>
<div id="footer-wrap">
	<div id="footer">
		<div id="footer-top">
        	<div class="align-left">
            	<h4>Dashboard</h4>
            	<p><a href="#">Dasboard Sub 1</a> | <a href="#">Dasboard Sub 2</a> | <a href="#">Dasboard Sub 3</a></p>
        	</div>
            <div class="align-right">
            	<h2><a href="#">Cleanity</a></h2>
            </div>
            <span class="clearFix"/>
        </div><!-- end of div#footer-top -->
        
        <div id="footer-bottom">
        	<p>2010 tForm framework | All rights reserved | ver. 0.5 | powerd by <a href="http://www.subvision.pl">subVision.pl</a></p>
        </div>
        
	</div>
</div>
</body>
</html>
