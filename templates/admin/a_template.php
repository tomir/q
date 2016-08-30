<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $siteTitle; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="../css/admin/reset.css"/>
	<link type="text/css" rel="stylesheet" href="../css/admin/style.css"/>
	<link href="../css/admin/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
	    div.wysiwyg ul.panel li {padding:0px !important;}
	</style>
	<script src="../js/admin/jquery-1.5.1.min.js" type="text/javascript"></script>
	<script src="../js/admin/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		
		function przejdz(url) {
			url = "<?php echo $adminPatch; ?>"+url;
			window.location = url; // pozostale
	        href_remove.href = url; // explorer
		}
	</script>
</head>
<body>
<div id="container">
    <div class="hidden"><!-- the modal box container - this div is hidden until it is called from a modal box trigger. see cleanity.js for details -->
    <div id="sample-modal"><h2 style="margin: 10px 0pt; font-size: 160%; font-weight: bold;">Modal Box Content</h2><p>Place your desired modal box content here</p></div>
    </div><!-- end of hidden -->
    <div id="header">
		<div id="top">
			<h1><a href="http://tform.net" title="Strona frameworku tForm"><img alt="tForm.net" src="../images/admin/logo_tff.png" /></a></h1>
			<p id="userbox">Jesteś zalogowany jako <strong style="color: #333333;"><?php echo $aUser['username']; ?></strong> |  <a href="zmienhaslo.html">Zmień hasło</a>  |  <a href="wyloguj.html">Wyloguj</a> <br/>
			<small>Ostatnie logowanie: <?php echo $aUser['data_log']; ?></small></p>
			<span class="clearFix"> </span>
		</div>
  		<ul id="menu">
		    <li class="selected"><a href="<?php echo $adminPatch; ?>" style="background-position: 0pt 0pt;">tForm Panel</a></li>
		    <?php if($aUser['group_id'] == 1) { ?>
		    <li><a href="#" class="top-level" style="background-position: 0pt 0pt;">Użytkownicy <span> </span></a>
  				<ul>
    				<li><a href="<?php echo $panelPatch; ?>uzytkownicy,dodaj.html" style="background-position: 0pt 0pt;">Dodaj użytkownika</a></li>
    				<li><a href="<?php echo $panelPatch; ?>uzytkownicy.html" style="background-position: 0pt 0pt;">Lista użytkowników</a></li>
  				</ul>
    		</li>
    		<li><a href="#" style="background-position: 0pt 0pt;">Zarządzaj menu</a></li>
    		<li><a href="<?php echo $tffPatch; ?>panel.php" class="top-level" style="background-position: 0pt 0pt;">tForm Admin <span> </span></a>
        		<ul>
        			<li><a href="<?php echo $tffPatch; ?>lista.html" style="background-position: 0pt 0pt;">Lista formularzy</a></li>
			        <li><a href="<?php echo $tffPatch; ?>stworz.html" style="background-position: 0pt 0pt;">Dodaj formularz</a></li>
      			</ul>
      		</li>
      		<?php } ?>
  		</ul>
 		<form id="form1" name="form1" method="post" action="">
  			<fieldset>
  				<legend>Wyszukaj</legend>
    			<label id="searchbox">
    				<input type="text" id="s" name="model"/>
    			</label>
    			<input type="submit" value="Search" name="Submit" class="hidden"/>
  			</fieldset>
  		</form>
  		<span class="clearFix"> </span>
    </div><!-- end of #header -->

<div id="content">

	<div id="left-col">
		<div class="box">
			<h4 class="yellow rounded_by_jQuery_corners" style="-moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;">Menu</h4>
			<div id="sidebar" class="box-container rounded_by_jQuery_corners" style="-moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px;"><!-- use no-padding wherever you need element padding gone -->
				<ul>
				<?php foreach($aMenu as $row_m) { ?>
					<li>
						<?php if($row_m['podmenu'][0]['nazwa_url'] != '') { ?>
						<a href="javacript:void(0);" class="expandable"><?php echo $row_m['nazwa']; ?></a>
						<ul class="categoryitems">
						<?php $k=0; foreach($row_m['podmenu'] as $row_m2) { ?>
							<li <?php if($row_m['podmenu'][$k]['nazwa_url'] == '') {?>class="last"<?php } ?>><a href="<?php echo $row_m2['nazwa_url']; ?>.html" title="<?php echo $row_m2['nazwa']; ?>"><?php echo $row_m2['nazwa']; ?></a></li>
						<?php $k++; } ?>
						</ul>
						<?php } else { ?>
						<a onclick="przejdz('<?php echo $row_m['nazwa_url']; ?>.html');" href="<?php echo $row_m['nazwa_url']; ?>.html" title="<?php echo $row_m['nazwa']; ?>"><?php echo $row_m['nazwa']; ?></a>
						<?php } ?>
					</li>
				<?php } ?>
				</ul>
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
          
          <?php /* <div class="box">
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
          </div><!-- end of div.box -->
		   *
		   */ ?>
      </div> <!-- end of div#left-col -->
      
      <div class="full-col" id="mid-col"><!-- end of div.box -->
      	<?php if($komunikat) { ?>
			<div style="margin-bottom: 10px;">
				<?php echo $komunikat; ?>
			</div>
			<br style="clear: both" />
		<?php } ?>
			<div class="box">
				<?php include(MyConfig::getValue("templatePatch").'admin/'.$template); ?>
			</div><!-- end of div.box-container -->
      	
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
            	<p><a href="<?php echo $adminPatch; ?>">tForm Panel</a> | <a href="kontakt.html">Kontakt</a> | <a href="wyloguj.html">Wyloguj</a></p>
        	</div>
            <div class="align-right">
            	<h2><a href="http://tform.net">tForm</a></h2>
            </div>
            <span class="clearFix"/>
        </div><!-- end of div#footer-top -->
        
        <div id="footer-bottom">
        	<p>2011 tForm framework | All rights reserved | ver. 0.7 | powerd by <a href="http://www.subvision.pl">subvision.pl</a></p>
        </div>
        
	</div>
</div>
</body>
</html>
