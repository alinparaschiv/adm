<?php
if (strlen($_POST['e_mail']) > 50) {
	throw new Exception('Hmm, tata mi-a spus că adresa de e-mail ar trebui să conțină maxim 50 de caractere.');
}

if (strlen($_POST['telefon']) > 20) {
	throw new Exception('Hmm, tata mi-a spus că numărul de telefon ar trebui să conțină maxim 20 de caractere.');
}

if ($_POST['tip'] != 'M' && $_POST['tip'] != 'F' && $_POST['tip'] != 'PJ') {
	throw new Exception('Hmm, am primit un tip invalid.');
}

if (isset($_POST['id'])) {
	$id = mysqli_real_escape_string($mc, $_POST['id']);
}
$nume1 = mysqli_real_escape_string($mc, $_POST['nume1']);
$nume2 = mysqli_real_escape_string($mc, $_POST['nume2']);
$e_mail = mysqli_real_escape_string($mc, $_POST['e_mail']);
$telefon = mysqli_real_escape_string($mc, $_POST['telefon']);
$tip = $_POST['tip'];
?>