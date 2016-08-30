<?php

class Controller {
	
	private $file = '';
	private $fileInit = '';
	
	public function __construct($mod = 'main', $act='index') {	
		
		$this->file = CONTROLLERS_DIR . $mod . '/' . $act . '.php';
		if (file_exists($this->file)) {
			$this->fileInit = CONTROLLERS_DIR . $mod . '/init.php';
		}

		if(!file_exists($this->file)) {
			Common::redirect('/blad,404');
		}
		
		if($this->fileInit != '' && !file_exists($this->fileInit)) {
			$this->fileInit = '';
		}
		
	}
	
	public function getFile() {
		return $this->file;
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getFileInit() {
		return $this->fileInit;
	}

	public function setFileInit($fileInit) {
		$this->fileInit = $fileInit;
	}
}

?>