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
ini_set('max_upload_filesize', 80388608);

$uploadDir = $_GET['sciezka'];
if($_GET['sciezka2'] != '') {
    $uploadDir .= $_GET['sciezka2'].'/';
    @mkdir($uploadDir);
}

if($_GET['sciezka3'] != '') {
    $uploadDir .= $_GET['sciezka3'].'/';
    @mkdir($uploadDir);
}
$uploadFile = $uploadDir . basename($_FILES['Filedata']['name']);

if ($_FILES['Filedata']['name']) {
	if (move_uploaded_file ($_FILES['Filedata']['tmp_name'], $uploadFile)) {
		
		$file_name = $_FILES['Filedata']['name'];
		$rozszerzenie = explode(".",$file_name);
		$cnt = count($rozszerzenie);
		$file_name = str_replace(".".$rozszerzenie[$cnt-1],"",$file_name);
		
		if($cnt && (strtolower($rozszerzenie[$cnt-1]) == "tiff")) {

			$im = new Imagick();
			$im->readImage( $uploadFile );
			$im->setImageFileName( $uploadDir.$file_name.'.jpg' );
			$im->writeImage();
			$im->destroy();
			@unlink ($uploadFile);
			$uploadFile2 = $uploadDir.$file_name.'.jpg';
		}
		elseif($cnt && (strtolower($rozszerzenie[$cnt-1]) == "png")) {
			
			$im = new Imagick();
			$im->readImage( $uploadFile );
			$im->setImageFileName( $uploadDir.$file_name.'.jpg' );
			$im->writeImage();
			$im->destroy();
			@unlink ($uploadFile);
			$uploadFile2 = $uploadDir.$file_name.'.jpg';
		}
		elseif($cnt && (strtolower($rozszerzenie[$cnt-1]) == "gif")) {
			
			$im = new Imagick();
			$im->readImage( $uploadFile );
			$im->setImageFileName( $uploadDir.$file_name.'.jpg' );
			$im->writeImage();
			$im->destroy();
			@unlink ($uploadFile);
			$uploadFile2 = $uploadDir.$file_name.'.jpg';
		} else {
			$uploadFile2 = $uploadDir.$file_name.'.'.strtolower($rozszerzenie[$cnt-1]);
		}
		// delete the file
		//@unlink ($uploadFile);
		return $uploadFile2;
	}
} else {
	if ($_FILES['Filedata']['error']) {
		return $_FILES['Filedata']['error'];
	}
}

?>
