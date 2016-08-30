<?php

namespace Przepis\Service;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author tomi
 */
class Mail {
	
	protected $title = "";
	protected $status = "";
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function sendMail(\Przepis\Repository\Przepis $przepis) {
		$obMail = new \Mail();
		$obMail -> setSubject($this->title);
		$obMail -> generateMailTemplate($this->status, $przepis);
		$obMail -> send();
		$obMail -> send('t.cisowski@gmail.com');
	}
}
