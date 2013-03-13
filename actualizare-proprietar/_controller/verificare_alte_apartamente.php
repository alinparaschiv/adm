<?php
require('../_model/conectare_selectare_baza_date.php');

if (isset($_GET['id_locatar'])) {
	$mesaj = '';
	$id_locatar = mysqli_real_escape_string($mc, $_GET['id_locatar']);
} else {
	throw new Exception('Nu s-a furnizat un id locatar.');
}

require('_model/verificare_alte_apartamente.php');

if ($nr_apartamente > 1) {
	echo '<p style="color: coral;">Proprietarul are mai multe apartamente. Se vor actualiza datele doar pentru apartamentul selectat. Pentru actualizarea datelor proprietarului pentru toate apartamentele deținute contactează departamentul IT.</p>';
}
?>