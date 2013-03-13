<?php
$denumire_buton = 'Actualizare';

if (isset($_POST['id_locatar']) && isset($_POST['id_proprietar']) &&
 isset($_POST['nume1']) && isset($_POST['nume2']) && isset($_POST['e_mail']) && 
 isset($_POST['tip']) && isset($_POST['telefon'])) {
	try {
		require('_controller/verificare_date_proprietar.php');
		require('_model/actualizare_proprietar.php');
		$mesaj = '<p>Am actualizat datele proprietarului.</p>';
	} catch (Exception $e) {
		$mesaj = '<p>' . $e->getMessage() . '</p>';
	}
} else {
	require('_model/incarcare_date_proprietar.php');
}

if(isset($_GET['tip_persoana'])) {
	if ($_GET['tip_persoana'] == 'juridica') {
		$tip = 'PJ';
	} else {
		$tip = 'M';
	}
}

if ($tip == 'PJ') {
	$titlu_nume1 = 'Denumire';
	$titlu_nume2 = 'Tip';
	$camp_nume2 = "\n\t<select name='nume2'>\n";
	foreach (array('S.R.L.', 'S.A.', 'S.C.M.', 'A.P.', 'C.M.I.') as $t) {
		if ($t == $nume2) {
			$camp_nume2 .= "\t\t<option value='$t' selected>$t</option>\n";
		} else {
			$camp_nume2 .= "\t\t<option value='$t'>$t</option>\n";
		}
	}
	$camp_nume2 .= "\t</select>\n";
	$camp_tip = '<input type="hidden" name="tip" value="PJ"/>';
} else {
	$titlu_nume1 = 'Prenume';
	$titlu_nume2 = 'Nume';
	$camp_nume2 = "<input type='text' name='nume2' value='$nume2'/>\n";
	$camp_tip = "<tr><td>Sex: </td>\n\t<td><input type='radio' name='tip' value='F'";
	if ($tip == 'F') $camp_tip .= ' checked';
	$camp_tip .= "/> feminin <input type='radio' name='tip' value='M'";
	if ($tip == 'M' || $tip == 'PJ') $camp_tip .= ' checked';
	$camp_tip .= "/> masculin</td></tr>\n";
}

$camp_ascuns = "<input type='hidden' name='id_locatar' value='$id_locatar'/>\n
	<input type='hidden' name='id_proprietar' value='$id_proprietar'/>\n";

require('_view/formular_proprietar.php');
?>