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

	public function send($email = null) {

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
			  $mail->AddReplyTo('adam.marciszewski@y-c.com.pl', 'Konkurs Biedronka');
			  if($email != null) {
				   $mail->AddAddress($email, 'Konkurs Biedronka');
			  } else {
				 $mail->AddAddress($this-> receiver, $this-> receiver);
			  }
			  $mail->SetFrom('konkurs@biedronka.pl', 'Konkurs Biedronka');
			  $mail->Subject = sprintf("=?utf-8?B?%s?=", base64_encode($this-> subject));
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			  $mail->MsgHTML($this-> htmlTemplate);
			  $mail->Send();
			  //echo "Message Sent OK</p>\n";
			} catch (phpmailerException $e) {
			  echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
			  echo $e->getMessage(); //Boring error messages from anything else!
			}
		}
	}


	public function generateMailTemplate($status, $przepis, $mail = 0) {

		$this -> setReceiver($przepis->data['email']);
		$control = $przepis->data['hash'];
		ob_start();

		switch($status) {
			case 'potwierdzenie':
				include(MyConfig::getValue("serverPatch")."templates/maile/t_potwierdzenie.php");
			break;

			case 'aktywacja':
				include(MyConfig::getValue("serverPatch")."templates/maile/t_mod_ok.php");
			break;

			case 'odrzucenie':
				include(MyConfig::getValue("serverPatch")."templates/maile/t_mod_error.php");
			break;

			case 'winner':
				include(MyConfig::getValue("serverPatch")."templates/maile/t_winner.php");
			break;

		}

		$includedphp = ob_get_contents();
		ob_end_clean();
		$this -> htmlTemplate = $includedphp;
		if($mail) {
			echo $includedphp;
		}
	}
	
	public function setSubject($subject) {
		$this -> subject = $subject;
	}
	
	public function setReceiver($email) {
		$this -> receiver = $email;
	}
}
?>
