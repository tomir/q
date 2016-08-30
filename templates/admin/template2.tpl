<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl" >
<head>
	<title>{$siteTitle}</title>
	<meta name="Keywords" content="" />
	<meta name="Description" content="" />  
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="../css/admin/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="../css/admin/superfish.css" /> 
	<link rel="stylesheet" type="text/css" media="screen" href="../css/admin/superfish-navbar.css" /> 
	
	<script type="text/javascript" src="../js/jquery-1.3.2.min.js" ></script>
	<script type="text/javascript" src="../js/hoverIntent.js"></script> 
	<script type="text/javascript" src="../js/superfish.js"></script> 
	{literal}
	<script type="text/javascript"> 
	 
	    $(document).ready(function(){ 
	        $("ul.sf-menu").superfish({ 
	            pathClass:  'current' 
	        }); 
	    }); 
	 
	</script>
	{/literal}
</head>

<body>
	<div id="container">
		<div id="wrapper">
			<div id="header">
				<div style="float: left;">
					<img src="../img/admin/logo_tff.png" alt="tFF" />
				</div>
				{if $showMenu}
				<div style="float: right; margin-right: 5px; padding-top: 23px;margin-bottom: 10px;">
					<span style="font-size: 12px;">Jesteś zalogowany jako <strong style="color: #23AAAA;">{$zalogowany.login}</strong> <span style="color: #B4AEA8; font-size: 11px;">({$zalogowany.imie} {$zalogowany.nazwisko})</span>
				</div>
				{/if}
			</div>
			<br style="clear: both;" />
			<div id="menu" {if !$showMenu}style="height: 6px;"{/if}>
				{if $showMenu}
				<div style="float: left;">
					<ul id="sample-menu-4" class="sf-menu sf-navbar">
					{section loop=$aMenu name=id}
						<li {if $actSite == $aMenu[id].nazwa_url}id="current_li" class="current"{/if}>
							<a class="sf-with-ul" href="{$aMenu[id].nazwa_url}.html" {if !$aMenu[id].podmenu}style="padding-right: 0.20em"{/if} title="{$aMenu[id].nazwa}">{$aMenu[id].nazwa}{if $aMenu[id].podmenu}<span class="sf-sub-indicator"> &#187;</span>{/if}</a>
							{if $aMenu[0].podmenu[0].id_menu != 0}
							<ul>
								{section loop=$aMenu[id].podmenu name=id2}
									<li {if $actSite2 == $aMenu[id].podmenu[id2].nazwa_url}class="current"{/if} {if $smarty.section.id2.first}style="border-left: 1px solid #CDCDCD"{/if}><a href="{$aMenu[id].podmenu[id2].nazwa_url}.html" title="{$aMenu[id].podmenu[id2].nazwa}">{$aMenu[id].podmenu[id2].nazwa}</a></li>
								{/section}
							</ul>
							{/if}
						</li>
					{/section}
						<li style="border-right: 0px;" {if $actSite == 'kontakt'}class="current"{/if}><a href="kontakt.html" title="Kontakt">Kontakt</a></li>
					</ul>
				</div>
				<div style="float: right; margin-right: 20px;">
					<ul>
						<li><a href="zmienhaslo.html" title="Zmień hasło">Zmień hasło</a></li>
						<li><a href="menu.html" title="Zarządzaj menu">Zarządzaj menu</a></li>
						{if $zalogowany.id_grupy == 1}
							<li><a href="uzytkownicy.html" title="Zarządzaj użytkownikami">Zarządzaj użytkownikami</a></li>
							<li><a href="{$wwwPatch}tff/panel.php" title="tFF admin">tFF admin</a></li>
						{/if}
						<li style="border-right: 0px;"><a href="wyloguj.html" title="Wyloguj">Wyloguj</a></li>
					</ul>
				</div>
				{/if}
			</div>
			
			<div id="maincontent">
				{if $komunikat}
					<div style="margin-bottom: 10px;">
						{$komunikat}
					</div>
					<br style="clear: both" />
				{/if}
				<div>
				{$siteContent}
				</div>
			</div>  <!-- end maincontent -->
			<div id="footer">
				<div style="float: left;">tForm framework | All rights reserved | ver. 0.1 beta | 2009</div>
				<div style="float: right">prowerd by <a href="http://tform.net">tform.net</a></div>
			</div>		
		</div>	<!-- end wrapper -->
					
	</div>	<!-- end container -->

</body>
</html>