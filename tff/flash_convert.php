<?php

/*
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
jqUploader serverside example: (author : pixeline, http://www.pixeline.be)

when javascript is available, a variable is automatically created that you can use to dispatch all the possible actions

This file examplifies this usage: javascript available, or non available.

1/ a form is submitted
1.a javascript is off, so jquploader could not be used, therefore the file needs to be uploaded the old way
1.b javascript is on, so the file, by now is already uploaded and its filename is available in the $_POST array sent by the form

2/ a form is not submitted, and jqUploader is on
jqUploader flash file is calling home! process the upload.



+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

*/
include("../../../config/config.php");
ini_set("include_path", $sciezka.'jurdziol/inc/');
//include('Asido/class.asido.php');
//include('Asido/class.imagick.php');

$uploadDir = $sciezka . 'temp/';
$uploadFile = $uploadDir . "frame--000348.tiff";
//$uploadFile = $uploadDir . "1085775299.jpg";


//$im = new Imagick($uploadFile);
//$im -> thumbnailImage(88, 0);
//echo $im;

/*$exec = "convert ".$uploadFile." ". $sciezka . 'temp/temp.jpg';
exec($exec, $yaks);
print_r($yaks);*/
/*$obAsido = new Asido();
$obAsido -> driver('imagick_ext');
//asido::driver('imagick_ext');
$i1 = $obAsido -> image($uploadFile,$sciezka . 'temp/temp.jpg');
$obAsido ->convert($i1,'image/tiff');
$i1->save(ASIDO_OVERWRITE_ENABLED);*/

$im = new Imagick();
/* Read the image file */
$im->readImage( $uploadFile );

/* Thumbnail the image ( width 100, preserve dimensions ) */
$im->setImageFileName( $sciezka . 'temp/temp.jpg' );
 
/* Write the thumbail to disk */
$im->writeImage();
 
/* Free resources associated to the Imagick object */
$im->destroy();
// delete the file
//@unlink ($uploadFile);
//return $uploadFile;
            

?>
