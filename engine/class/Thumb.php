<?php

class Thumb
{
	function make( $src, $output, $wmax, $hmax, $bgcol = 'FFFFFF', $ext = NULL )
	{
		if( isset($ext) )
		{
			if( $ext == 'jpg' || $ext == 'jpeg' ) {
				$source = imagecreatefromjpeg($src);
			} elseif( $ext == 'gif' ) {
				$source = imagecreatefromgif($src);
			} elseif( $ext == 'png' ) {
				$source = imagecreatefrompng ($src);
			} else {
				return false;
			}

		}else{

			if( strstr( $src, '.jpg' ) || strstr( $src, '.jpeg' ) ) {
				$source = imagecreatefromjpeg($src);
			} elseif( strstr( $src, '.gif' ) ) {
				$source = imagecreatefromgif($src);
			} elseif( strstr( $src, '.png' ) ) {
				$source = imagecreatefrompng($src);
			} else {
				return false;
			}

		}



	    if( !$source )
	    	return false;

	    $orig_w=imagesx($source);
	    $orig_h=imagesy($source);

	    if ($orig_w>$wmax || $orig_h>$hmax)
	    {
	        $thumb_w=$wmax;
	        $thumb_h=$hmax;
	        if ($thumb_w/$orig_w*$orig_h>$thumb_h)
	            $thumb_w=round($thumb_h*$orig_w/$orig_h);
	        else
	            $thumb_h=round($thumb_w*$orig_h/$orig_w);
	    } else  {
	        $thumb_w=$orig_w;
	        $thumb_h=$orig_h;
	    }

	    if (!@$bgcol)
	    {
	        $thumb=imagecreatetruecolor($thumb_w,$thumb_h);
	        imagecopyresampled($thumb,$source,
	                           0,0,0,0,$thumb_w,$thumb_h,$orig_w,$orig_h);
	    }else
	    {
	        $thumb=imagecreatetruecolor($wmax,$hmax);
	        imagefilledrectangle($thumb,0,0,$wmax-1,$hmax-1,intval($bgcol,16));
	        imagecopyresampled($thumb,$source,
	                           round(($wmax-$thumb_w)/2),round(($hmax-$thumb_h)/2),
	                           0,0,$thumb_w,$thumb_h,$orig_w,$orig_h);
	    }

	    if (!@$quality) $quality=100;


		if( isset($ext) )
		{
			if( $ext == 'jpg' || $ext == 'jpeg' ) {
				imagejpeg($thumb,$output,$quality);
			} elseif( $ext == 'gif' ) {
				imagegif($thumb,$output);
			} elseif( $ext == 'png' ) {
				imagepng($thumb,$output);
			} else {
				return false;
			}

		}else{

			if( strstr( $src, '.jpg' ) || strstr( $src, '.jpeg' ) ) {
				imagejpeg($thumb, $output, $quality);
			} elseif( strstr( $src, '.gif' ) ) {
				imagegif($thumb, $output);
			} elseif( strstr( $src, '.png'  ) ) {
				imagepng($thumb, $output);
			} else {
				return false;
			}

		}

	    imagedestroy($thumb);

	    return true;
	}


}


?>