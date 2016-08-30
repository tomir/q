<?php
if ($_SERVER['HTTPS'] != 'on' && SSL == 1){
	Common::Redirect('https://'.$_SERVER['HTTP_HOST'].$x);
}

if(!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) 
	Common::redirect("/login.html");