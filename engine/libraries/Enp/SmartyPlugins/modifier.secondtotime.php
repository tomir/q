<?php

function smarty_modifier_secondtotime($secound, $format="H:i:s")
{
	$daySecond = 60*60*24;
	
	$day = floor($secound / $daySecond);
	$time = $secound % $daySecond;
	
	$time = gmdate($format, $time);
	
	if ($day > 0) {
		$time = $day.'d '.$time;
	}
	
	return $time;
}
