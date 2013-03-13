<?php
if ($nr_apartamente > 1) {
	mysqli_query($mc,
		"INSERT INTO adm_proprietar(nume1, nume2, tip, e_mail, telefon) 
		VALUES('$nume1', '$nume2', '$tip', '$e_mail', '$telefon')"
	);
	if (mysqli_errno($mc)) {
		throw new Exception(sprintf('A apărut o eroare la actualizarea proprietarului în baza de date. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
	}
	$id_nou = mysqli_insert_id($mc);
	mysqli_query($mc,
		"UPDATE id_locatar_proprietar 
		SET id_proprietar=$id_nou 
		WHERE id_locatar=$id_locatar AND id_proprietar=$id_proprietar"
	);
	if (mysqli_errno($mc)) {
		throw new Exception(sprintf('A apărut o eroare la actualizarea proprietarului în baza de date. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
	}
} else {
	mysqli_query($mc,
		"UPDATE adm_proprietar 
		SET nume1='$nume1', nume2='$nume2', tip='$tip', e_mail='$e_mail', telefon='$telefon' 
		WHERE id=$id_proprietar"
	);
	if (mysqli_errno($mc)) {
		throw new Exception(sprintf('A apărut o eroare la actualizarea proprietarului în baza de date. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
	}
}

//**
// * Actualizare și în tabela locatari
// 
if ($tip == 'M' || $tip == 'F')
	$nume_final = $nume2.' '.$nume1;
else
	$nume_final = $nume1.' '.$nume2;

mysqli_query($mc,
	"UPDATE locatari 
	SET nume='$nume_final' 
	WHERE loc_id=$id_locatar"
);
if (mysqli_errno($mc)) {
	throw new Exception(sprintf('A apărut o eroare la actualizarea proprietarului în baza de date. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
}
//*/
?>