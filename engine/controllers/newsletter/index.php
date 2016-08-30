<?php
if($_POST['email']) {
    if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
        $n = new Newsletter;
        $n->email = $_POST['email'];
        $n->add();
    } else {
        //echo 'Musisz podać poprawny adres email.';
    }
} else {
    echo 'Musisz podać poprawny adres email.';
}
exit;
?>
