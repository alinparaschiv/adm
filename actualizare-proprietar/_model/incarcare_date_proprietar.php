<?php
$r = mysqli_query($mc, 
	"SELECT p.id, p.nume1, p.nume2, p.tip, p.e_mail, p.telefon 
	FROM adm_proprietar p JOIN id_locatar_proprietar idlp 
	ON p.id = idlp.id_proprietar AND idlp.id_locatar = $id_locatar"
	);
if (mysqli_errno($mc)) {
	throw new Exception(sprintf('A apărut o eroare la citirea datelor proprietarului din baza de date. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
}
$rr = mysqli_fetch_assoc($r);
$id_proprietar = $rr['id'];
$nume1 = $rr['nume1'];
$nume2 = $rr['nume2'];
$tip = $rr['tip'];
$e_mail = $rr['e_mail'];
$telefon = $rr['telefon'];
?>