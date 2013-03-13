<?php
if (mysql_num_rows($sql1) == 0) {
	die('Ferifica scriptul de introducere fonduri in "ProceseazaFacturiReparatii.php"');

//daca nu e introdusa luna curenta in fisa_fonduri o adaug
    $aveaDePlata = "SELECT * FROM fisa_fonduri WHERE scara_id =".$scaraId." AND data < STR_TO_DATE(  '".$luna."',  '%m-%Y' ) ";
	$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre ultima luna <br />".mysql_error());

	while($row = mysql_fetch_assoc($aveaDePlata)) {
		$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$row['loc_id'].", 0, '".$row['fond_rul_rest']."', 0, '".$row['fond_rep_rest']."', 0, '".$row['fond_spec_rest']."', 0, 0, 0, '".($row['fond_rul_rest']+$row['fond_rep_rest']+$row['fond_spec_rest'])."')";
        $iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera reparatiile<br />".mysql_error());
	}
}
include_once(  'modules/fise/Furnizori.class.php');

//aflu cat fond special are acumulat scara
$fondSpecial_sql = "SELECT sum(fond_spec_incasat) - sum(COALESCE(fond_spec_cheltuit,0)) AS fond_special
FROM `fisa_fonduri` FF LEFT OUTER JOIN fisa_fonduri_completare FFC ON FF.id_fond=FFC.id_fond WHERE scara_id=".$scaraId;
$fondSpecial_query = mysql_query($fondSpecial_sql) or die("Nu pot afla cat fond special are acumulat asociatia<br />".$fondSpecial_sql."<br />".mysql_error());
$fondSpecial = mysql_result($fondSpecial_query, 0 ,'fond_special');

if($fondSpecial > $proc['cost'])
	$fondSpecial = $proc['cost'];

$proc['cost'] -= $fondSpecial;
Furnizori::insertPlata($factId, $fondSpecial, 'Plata automata din FOND SPECIAL');

$fondPlata_sql = "SELECT FF.id_fond, fond_spec_incasat - COALESCE(fond_spec_cheltuit,0) as fond_ramas, fond_spec_incasat, COALESCE(fond_spec_cheltuit,0) as fond_spec_cheltuit
FROM `fisa_fonduri` FF LEFT OUTER JOIN fisa_fonduri_completare FFC ON FF.id_fond=FFC.id_fond
WHERE fond_spec_incasat>0 AND COALESCE(fond_spec_cheltuit,0)<fond_spec_incasat AND scara_id=".$scaraId;
$fondPlata_query = mysql_query($fondPlata_sql) or die("Nu pot afla cat fond special are acumulat asociatia<br />".$fondPlata_sql."<br />".mysql_error());

while(($row = mysql_fetch_assoc($fondPlata_query)) && $fondSpecial>0) {
	if($row['fond_ramas'] > $fondSpecial){
		$update_sql = "INSERT INTO fisa_fonduri_completare (id_fond, fond_spec_cheltuit) VALUES (".$row['id_fond'].", ".$fondSpecial.")
						ON DUPLICATE KEY UPDATE fond_spec_cheltuit=".($fondSpecial+$row['fond_spec_cheltuit']);
	}
	else {
		$update_sql = "INSERT INTO fisa_fonduri_completare (id_fond, fond_spec_cheltuit) VALUES (".$row['id_fond'].", ".$row['fond_ramas'].")
					ON DUPLICATE KEY UPDATE fond_spec_cheltuit=".($row['fond_ramas']+$row['fond_spec_cheltuit']);
		$fondSpecial -= $row['fond_ramas'];
	}

	mysql_query($update_sql) or die("NU am putut sa fac update-ul<br />".$update_sql."<br />".mysql_error());
}

if( $tipFactura == 4 ) //in cazul in care avem o factura la nivelul de locatar
{
	$locatari = explode(',', $proc['locatari']);
	$ppu = explode(',', $proc['ppu']);
	$facturaCurr = $serieFactura.'/'.$numarFactura;
	foreach ($locatari as $key => $value) {
		$fondRepAcumulat = "SELECT sum(`fond_rep_incasat`)-sum(`fond_rep_cheltuit`) as total_acumulat FROM `fisa_fonduri` WHERE loc_id=".$locatari[$key];
		$fondRepAcumulat = mysql_query($fondRepAcumulat) or die("Nu am putut afla fondul de reparatii total acumulat pana in acest moment<br />".mysql_error());
		$fondRepAcumulat = mysql_result($fondRepAcumulat,0,'total_acumulat');

		if($fondRepAcumulat > 0) {
			$fondRepCheltuitLunaCurenta = "SELECT fond_rep_cheltuit FROM fisa_fonduri WHERE data='".$luna."' AND loc_id=".$locatari[$key];
			$fondRepCheltuitLunaCurenta = mysql_query($fondRepCheltuitLunaCurenta) or die("Nu am putut afla fondul de reparatii cheltuit in luna curenta <br />".mysql_error());
			$fondRepCheltuitLunaCurenta = mysql_result($fondRepCheltuitLunaCurenta,0,'fond_rep_cheltuit');

			if($fondRepAcumulat >= $ppu[$key])
			{
				//actualizam doar fondul
				$update = "UPDATE fisa_fonduri SET fond_rep_cheltuit=".($fondRepCheltuitLunaCurenta+$ppu[$key])." WHERE data='".$luna."' AND loc_id=".$locatari[$key];
				mysql_query($update) or die ("Nu pot actualiza fondul de reparatii cheltuit<br />".mysql_error());

				//facem plata automata catre furnizor
				Furnizori::insertPlata($factId, $ppu[$key], 'Plata automata din FOND REPARATI');
			} else {
				if ($fondRepAcumulat > 0) {
				//actualizam fondul
				$update = "UPDATE fisa_fonduri SET fond_rep_cheltuit=".($fondRepCheltuitLunaCurenta+($ppu[$key]-$fondRepAcumulat))." WHERE data='".$luna."' AND loc_id=".$locatari[$key];
				mysql_query($update) or die ("Nu pot actualiza fondul de reparatii cheltuit<br />".mysql_error());

				//facem plata automata catre furnizor
				Furnizori::insertPlata($factId, $fondRepAcumulat, 'Plata automata din FOND REPARATI');
				$ppu[$key] -= $fondRepAcumulat;
				}

			$insert = "INSERT INTO fisa_indiv (`id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `serviciu`, `cant_fact_pers`, `pret_unitar`, `um`, `factura`, `pret_unitar2`, `cant_fact_tot`)
								VALUES(NULL , '$asocId', '$scaraId', ".$locatari[$key].", '$luna', '$tipServiciu', 1, '$ppu[$key]', 'apartament', '$facturaCurr', '1', '$cost')";
			mysql_query($insert) or die ("Nu pot insera factura (inpartitaa pe locatari) la locatartul cu loc_id=".$value."<br />".mysql_error());
			}
		}
	}
}
else {
	$select = "SELECT ";

	switch ($proc['mod_impartire']) {
		case 0: //pe nr apartamente
			$select .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
			break;
		case 1: //pe nr persoane
			$select .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
			break;
		case 2: //pe suparafata
			$select .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
			break;
		case 4: //pe repartitoare
			$select .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_rep as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
			break;
		default:
			die("Nu se poate procesa o factura cu acest mod de inpartire a facturi");
	}

	switch ($tipFactura) {
		case 1: //asociatie
			$select .= ' L.asoc_id='.$asocId;
			break;
		case 2: //scara
			$select .= ' L.scara_id='.$scaraId;
			break;
		case 3: //apartament
			$locatari = explode(',', $proc['locatari']);
			$locatari = implode(', ', $locatari);
			$select .= ' L.loc_id IN ('.$locatari.')';
			break;
		default:
			die("Nu se poate procesa o factura de pe nivelul ".$tipFactura);
	}
	$query = mysql_query($select) or die("A aparut o eroare la citirea informatiilor di BD <br />".$select."<br />".  mysql_error());
	$info = array();
	$total = 0;
	$facturaCurr = $serieFactura.'/'.$numarFactura;
	while ($row = mysql_fetch_assoc($query)) {
		$info [] = $row;
		$total += $row['valoare']*$row['procent']/100;
	}
	$ppu = $proc['cost'] / $total;
	//var_dump($select);
	//die();
	foreach ($info as $key => $value) {

		$fondRepAcumulat = "SELECT sum(`fond_rep_incasat`)-sum(`fond_rep_cheltuit`) as total_acumulat FROM `fisa_fonduri` WHERE loc_id=".$value['loc_id'];
		$fondRepAcumulat = mysql_query($fondRepAcumulat) or die("Nu am putut afla fondul de reparatii total acumulat pana in acest moment<br />".mysql_error());
		$fondRepAcumulat = mysql_result($fondRepAcumulat,0,'total_acumulat');

		$totalPlata = ($value['valoare']*$value['procent']/100)*$ppu;

		if($fondRepAcumulat > 0) {
			$fondRepCheltuitLunaCurenta = "SELECT fond_rep_cheltuit FROM fisa_fonduri WHERE data='".$luna."' AND loc_id=".$value['loc_id'];
			$fondRepCheltuitLunaCurenta = mysql_query($fondRepCheltuitLunaCurenta) or die("Nu am putut afla fondul de reparatii cheltuit in luna curenta <br />".mysql_error());
			$fondRepCheltuitLunaCurenta = mysql_result($fondRepCheltuitLunaCurenta,0,'fond_rep_cheltuit');

			if($fondRepAcumulat >= $totalPlata)
			{
				//actualizam doar fondul
				$update = "UPDATE fisa_fonduri SET fond_rep_cheltuit=".($fondRepCheltuitLunaCurenta+$totalPlata)." WHERE data='".$luna."' AND loc_id=".$value['loc_id'];
				mysql_query($update) or die ("Nu pot actualiza fondul de reparatii cheltuit<br />".mysql_error());

				//facem plata automata catre furnizor
				Furnizori::insertPlata($factId, $totalPlata, 'Plata automata din FOND REPARATI');
				$totalPlata = 0;
			} else {
				if ($fondRepAcumulat > 0) {
				//actualizam fondul
				$update = "UPDATE fisa_fonduri SET fond_rep_cheltuit=".($fondRepCheltuitLunaCurenta+($fondRepAcumulat))." WHERE data='".$luna."' AND loc_id=".$value['loc_id'];
				mysql_query($update) or die ("Nu pot actualiza fondul de reparatii cheltuit<br />".mysql_error());

				//facem plata automata catre furnizor
				Furnizori::insertPlata($factId, $fondRepAcumulat, 'Plata automata din FOND REPARATI');
				$totalPlata -= $fondRepAcumulat;
				}
			}
		}
		if($totalPlata > 0) {
			//trebuie recalculata valoarea care o v-a avea locatarul curent de plata dupa ce s-a scazut ce s-a platit cu fondul
			$ppuLocatar = $totalPlata / ($value['valoare']*$value['procent']/100);

			$insert = "INSERT INTO fisa_indiv (`id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `serviciu`, `cant_fact_pers`, `pret_unitar`, `um`, `factura`, `pret_unitar2`, `cant_fact_tot`)
								VALUES(NULL , ".$value['asoc_id'].",".$value['scara_id'].", ".$value['loc_id'].", '$luna', '$tipServiciu', '".($value['valoare']*$value['procent']/100)."', '$ppuLocatar', '".$uMasuraArr[$proc['unitate']]."', '$facturaCurr', '1', '$cost')";
			mysql_query($insert) or die ("Nu pot insera factura (inpartitaa pe locatari) la locatartul cu loc_id=".$value."<br />".mysql_error());
		}


	}
}