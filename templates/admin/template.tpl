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

	{literal}
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
    {if $showMenu}
		<div id="top">
			<h1><a href="http://tform.net" title="Strona frameworku tForm"><img alt="tForm.net" src="../images/admin/logo_tff.png"></a></h1>
			<p id="userbox">Jesteś zalogowany jako <strong style="color: #333333;">{$zalogowany.login}</strong> |  <a href="zmienhaslo.html">Zmień hasło</a>  |  <a href="wyloguj.html">Wyloguj</a> <br/>
			<small>Ostatnie logowanie: {$zalogowany.data_log|date_format:"%e %B %Y, %H:%M:%S"}</small></p>
			<span class="clearFix"> </span>
		</div>
  		<ul id="menu">
		    <li class="selected"><a href="{$adminPatch}" style="background-position: 0pt 0pt;">tForm Panel</a></li>
		    <li><a href="#" style="background-position: 0pt 0pt;">tForm Sklep</a></li>
		    {if $zalogowany.id_grupy == 1}
		    <li><a href="#" class="top-level" style="background-position: 0pt 0pt;">Użytkownicy <span> </span></a>
  				<ul>
    				<li><a href="{$panelPatch}uzytkownicy,dodaj.html" style="background-position: 0pt 0pt;">Dodaj użytkownika</a></li>
    				<li><a href="{$panelPatch}uzytkownicy.html" style="background-position: 0pt 0pt;">Lista użytkowników</a></li>
  				</ul>
    		</li>
    		<li><a href="#" style="background-position: 0pt 0pt;">Zarządzaj menu</a></li>
    		<li><a href="{$tffPatch}panel.php" class="top-level" style="background-position: 0pt 0pt;">tForm Admin <span> </span></a>
        		<ul>
        			<li><a href="{$tffPatch}lista.html" style="background-position: 0pt 0pt;">Lista formularzy</a></li>
			        <li><a href="{$tffPatch}stworz.html" style="background-position: 0pt 0pt;">Dodaj formularz</a></li>
      			</ul>
      		</li>
      		{/if}
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
  	{/if}
  		<span class="clearFix"> </span>
    </div><!-- end of #header -->

<div id="content">
	<div id="content-top">
    	<h2>tForm Panel</h2>
      	{*<a id="topLink" href="#">{$actSite}</a> *}
      	<span class="clearFix"> </span>
	</div><!-- end of div#content-top -->
	<div id="left-col">
		<div class="box">
			<h4 class="yellow rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">Menu</h4>
			<div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;"><!-- use no-padding wherever you need element padding gone -->
				{if $showMenu}
					<ul class="list-links ui-accordion ui-widget ui-helper-reset">
					{section loop=$aMenu name=id}
						<li>
						{if $aMenu[id].podmenu[0].nazwa_url != ''}
							<a href="#">{$aMenu[id].nazwa}</a>
							<ul>
							{section loop=$aMenu[id].podmenu name=id2}
								<li><a href="{$aMenu[id].podmenu[id2].nazwa_url}.html" title="{$aMenu[id].podmenu[id2].nazwa}">{$aMenu[id].podmenu[id2].nazwa}</a></li>
							{/section}
							</ul>
						{else}
							<a onclick="przejdz('{$aMenu[id].nazwa_url}.html');" href="{$aMenu[id].nazwa_url}.html" title="{$aMenu[id].nazwa}">{$aMenu[id].nazwa}</a>
						{/if}
						</li>
					{/section}
					</ul>
				{/if}
				  <!-- <ul class="list-links ui-accordion ui-widget ui-helper-reset">
					  <li class="ui-accordion-li-fix"><a href="#" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top"><span class="ui-icon ui-icon-triangle-1-s"/>Manage Filters</a></li>
					  <li class="ui-accordion-li-fix"><a href="#" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><span class="ui-icon ui-icon-triangle-1-e"/>Setup a New Site</a>
							<ul class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="height: 58px; display: none;">
							<li><a href="#">Configure Paths</a></li>
							<li><a href="#">Define Database Name</a></li>
							</ul>
					  </li>
					  <li class="ui-accordion-li-fix"><a href="#" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" role="tab" aria-expanded="false" tabindex="-1"><span class="ui-icon ui-icon-triangle-1-e"/>Manage Site Accounts</a></li>
				  </ul> -->
			  </div><!--end of div.box-container -->
          </div>
          
          {*<div class="box">
              <h4 class="light-blue rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">System Messages</h4>
              <div class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
                  <div id="sys-messages-container">
                  <h5>Latest Messages <span>(Opens Modal)</span></h5>
                      <ul>
                      <li class="even-messages"><a href="#">Where is Everyone? New Stuff i...</a>
                        <a class="sysmessage-delete" href="#"><img alt=" " src="../images/admin/icon-delete-message.gif"/></a></li>
                      <li class="odd-messages"><a href="#">Version 2 is Online. You can upd...</a>
                        <a class="sysmessage-delete" href="#"><img alt=" " src="../images/admin/icon-delete-message.gif"/></a></li>
                      </ul>
                  </div>
                  
                 <div id="quick-send-message-container">
                  <h5>Quick Send</h5>
                  
                    <form action="" method="get" name="send-message-form" id="form2">
                  		<fieldset>
                  			<legend>Quick Send Message</legend>
                  			<p><label>Message Title:</label>
                  			<input type="text" id="message-title" name="message-title"/></p>
        	          		<p><label>Content:</label>
        	          		<textarea rows="5" cols="10" name="content"></textarea></p>
                            <div class="inner-nav">
                				<div class="align-left"><input type="checkbox" value="" name="send-everyone"/>To Everyone</div>
                				<div class="align-right"><a onclick="javascript:document.forms['send-message-form'].submit();" href="#"><span>send</span></a></div>
                                <span class="clearFix"> </span>
                			</div>  	
    	    	          	<input type="button" value="Send Message" name="button" class="hidden"/>
        	          	</fieldset>
                  	</form>
                  </div>
          </div><!--end of div.box-container -->
          </div><!--end of div.box -->
          
              
          <div id="to-do" class="box">
              <ul class="tab-menu tabs-nav">
                  <li class="tabs-selected"><a href="#to-dos" class="rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">To Do</a></li>
                  <li class=""><a href="#completed" class="rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">Completed</a></li>
              </ul>
              <div id="to-dos" class="box-container rounded_by_jQuery_corners tabs-container" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">
                 <div id="to-do-list">
                    <ul>
                        <li class="even"><input type="checkbox" value="" name="check"/>
                        <a href="#">To Do Will Open in Modal Box</a><br/>
                        <small><strong>Deadline:</strong>Today</small>
                        </li>
                        <li class="odd"><input type="checkbox" value="" name="check"/>
                        <a href="#">To Do Will Open in Modal Box</a><br/>
                        <small><strong>Deadline:</strong>Today</small>
                        </li>
                        
                        <li class="even"><input type="checkbox" value="" name="check"/>
                        <a href="#">To Do Will Open in Modal Box</a><br/>
                        <small><strong>Deadline:</strong>Today</small>
                        </li>
    
                        <li class="odd"><input type="checkbox" value="" name="check"/>
                        <a href="#">To Do Will Open in Modal Box</a><br/>
                        <small><strong>Deadline:</strong>Today</small>
                        </li>
                    </ul>    
                    <div class="inner-nav">
                        <div class="align-left"><a href="#"><span>view all</span></a></div>
                        <div class="align-right"><a href="#"><span>to-do config</span></a></div>
                        <span class="clearFix"> </span>
                    </div>       
                  </div><!-- end of div#to-do-list -->
              </div><!-- end of div.box-container -->

              <div id="completed" class="box-container rounded_by_jQuery_corners tabs-container tabs-hide" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;">Completed tasks div content</div>  
          </div><!-- end of div.box -->*}
      </div> <!-- end of div#left-col -->
      
      <div class="full-col" id="mid-col"><!-- end of div.box -->
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
            	<h4>Na skróty</h4>
            	<p><a href="{$adminPatch}">tForm Panel</a> | <a href="kontakt.html">Kontakt</a> | <a href="wyloguj.html">Wyloguj</a></p>
        	</div>
            <div class="align-right">
            	<h2><a href="http://tform.net">tForm</a></h2>
            </div>
            <span class="clearFix"/>
        </div><!-- end of div#footer-top -->
        
        <div id="footer-bottom">
        	<p>2010 tForm framework | All rights reserved | ver. 0.5 | powerd by <a href="http://tform.net">tform.net</a></p>
        </div>
        
	</div>
</div>
</body>
</html>
