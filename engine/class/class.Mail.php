<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classMail
 *
 * @author t.jurdzinski
 */

require_once( LIBRARY_DIR.'/PHPMailer/class.phpmailer.php' );

class Mail {

	protected $htmlTemplate;
	protected $subject;
	protected $receiver;
	protected $from = 'newsletter@autolicytacje.pl';

	public function send() {

		if($this-> receiver != '') {
			$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
			try {
				$mail->SMTPDebug  = 2;
				$mail->CharSet	= "UTF-8";
				$mail->SMTPAuth   = true;                  // enable SMTP authentication
				$mail->Host       = "autolicytacje.pl"; // sets the SMTP server
				$mail->Port       = 26;                    // set the SMTP port for the GMAIL server
				$mail->Username   = "newsletter@auto-licytacje.pl"; // SMTP account username
				$mail->Password   = "killer";        // SMTP account password
				$mail->AddReplyTo('noreplay@autolicytacje.pl', 'Auto Licytacje');
				$mail->AddAddress($this->receiver, $this->receiver);
				$mail->SetFrom($this->from, 'Auto Licytacje');
				$mail->Subject = sprintf("=?utf-8?B?%s?=", base64_encode($this-> subject));
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
				$mail->MsgHTML($this-> htmlTemplate);
				
				Common::log($this->htmlTemplate);
				$res = $mail->Send();
				//echo "Message Sent OK</p>\n";
			  
			} catch (phpmailerException $e) {
				echo $e->errorMessage(); //Pretty error messages from PHPMailer
			  
			} catch (Exception $e) {
				echo $e->getMessage(); //Boring error messages from anything else!
			}
		}
	}


	public function generateMailTemplate($status, $aData, $email) {

		ob_start();

		switch($status) {
			case 'potwierdzenie':
				include(MyConfig::getValue("serverPatch")."templates/newsletter/t_potwierdzenie.php");
			break;

			case 'newsletter':
				include(MyConfig::getValue("serverPatch")."templates/newsletter/t_mailing.php");
			break;
		
			case 'rejestracja':
				include(MyConfig::getValue("serverPatch")."templates/newsletter/t_rejestracja.php");
			break;
		
			case 'saldo':
				include(MyConfig::getValue("serverPatch")."templates/newsletter/t_saldo.php");
			break;
		
			case 'zapytanie':
				include(MyConfig::getValue("serverPatch")."templates2/newsletter/t_zapytanie.php");
			break;
		
			case 'oferta':
				include(MyConfig::getValue("serverPatch")."templates/newsletter/t_oferta.php");
			break;

		}

		$includedphp = ob_get_contents();
		ob_end_clean();
		$this -> htmlTemplate = $includedphp;

	}
	
	public function setSubject($subject) {
		$this -> subject = $subject;
	}
	
	public function setReceiver($email) {
		$this -> receiver = $email;
	}
	
	public function setFrom($email) {
		$this -> from = $email;
	}
}
?>
