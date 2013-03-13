<?php
$r = mysqli_query($mc,
	"SELECT id_proprietar 
	FROM id_locatar_proprietar 
	WHERE id_locatar=$id_locatar"
);

if (mysqli_errno($mc)) {
	throw new Exception(sprintf('A apărut o eroare la citirea numărului de apartamente. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
}

$rr = mysqli_fetch_array($r);
$id_proprietar = $rr[0];

$r = mysqli_query($mc,
	"SELECT COUNT(id_locatar) 
	FROM id_locatar_proprietar 
	WHERE id_proprietar=$id_proprietar 
	GROUP BY id_proprietar"
);

if (mysqli_errno($mc)) {
	throw new Exception(sprintf('A apărut o eroare la citirea numărului de apartamente. (%d: %s)', mysqli_errno($mc), mysqli_error($mc)));
}

$rr = mysqli_fetch_array($r);
$nr_apartamente = $rr[0];
?>