<?php
//functie folosita pentru generarea informatiilor din lista de plata
function get_info_lista($asoc, $scara, $luna) {
	$data = array();
	$tarife = array();

	$chelt_an = array();
	$chelt_ben = array();
	$chelt_pers = array();
	$chelt_cota = array();

	$ApaRServiciId = '21, 41';
	$ApaCServiciId = '26, 37, 38, 39, 40';
	$IncaServiciId = '25';
	$EnerServiciId = '35, 84, 90, 91, 92, 93, 107, 211, 212';
	$GazMServiciId = '27';
	$SaluServiciId = '55';

	$ServiciiSpecialeIds = array($GazMServiciId);
	$ServiciiSpecialeIds = array_merge($ServiciiSpecialeIds, explode(', ', $EnerServiciId));

	// ---------------------- APAVITAL -----------------------------------------
	$sql = "SELECT sum(F.cantitate) as cant, sum(F.cost) as pret
		FROM facturi F
		WHERE
		F.asoc_id=".$asoc." AND F.luna='".$luna."' AND
		(F.tipServiciu IN (21, 37, 39, 41) OR F.subtipFactura IN (37, 39, 41) )AND
		F.procesata=1"; //apavital
	$q = mysql_query($sql) or die("A aparut o eroare la citirea facturilor APAVITAL din BD <br />".$sql."<br />".  mysql_error());

	if (mysql_result($q,0,'pret') != NULL) {
	$data []= array("fur" => "APAVITAL",
					"val" => round( mysql_result($q,0,'pret'), 2),
					"total" => round( mysql_result($q,0,'cant'), 2),
					"um" => "mc");

	$tarife []= array("fur" => "APAVITAL",
					  "val" => round(mysql_result($q,0,'pret')/mysql_result($q,0,'cant'),2),
					  "um" => "lei/mc");
	}

	// --------------------- CET -----------------------------------------------
	$sql = "SELECT sum(F.cantitate) as cant, sum(F.cost) as pret
		FROM facturi F
		WHERE
		F.asoc_id=".$asoc." AND F.luna='".$luna."' AND (F.scara_id=".$scara." OR F.scara_id IS NULL) AND
		(F.tipServiciu=25 OR F.subtipFactura IN (38, 40) )AND
		F.procesata=1"; //CET
	$q = mysql_query($sql) or die("A aparut o eroare la citirea facturilor CET din BD <br />".$sql."<br />".  mysql_error());

	if (mysql_result($q,0,'pret') != NULL && mysql_result($q,0,'pret')) {
	$data []= array("fur" => "Agent Termic",
					"val" => round( mysql_result($q,0,'pret'), 2),
					"total" => round( mysql_result($q,0,'cant'), 2),
					"um" => "Gkal");

	$tarife []= array("fur" => "Agent Termic",
					  "val" => round(mysql_result($q,0,'pret')/mysql_result($q,0,'cant'),2),
					  "um" => "lei/Gkal");
	}

	// --------------------ILUMINAT --------------------------------------------
	$sql = "SELECT sum(F.cantitate) as cant, sum(F.cost) as pret
		FROM facturi F
		WHERE
		F.asoc_id=".$asoc." AND F.luna='".$luna."' AND (F.scara_id=".$scara." OR F.scara_id IS NULL) AND
		(F.tipServiciu IN (35, 84, 90, 91, 92, 93, 107, 211, 212) )AND
		F.procesata=1"; //ILUMINAT
	$q = mysql_query($sql) or die("A aparut o eroare la citirea facturilor ILUMINAT din BD <br />".$sql."<br />".  mysql_error());

	if (mysql_result($q,0,'pret') != NULL && mysql_result($q,0,'pret')) {
		$iluminatCanti = explode(',', mysql_result($q,0,'cant'));

		$data []= array("fur" => "E-on Moldova",
						"val" => round( mysql_result($q,0,'pret'), 2),
						"total" => round( $iluminatCanti[0], 2),
						"um" => "kw");

		$tarife []= array("fur" => "E-on Moldova",
						  "val" => round(mysql_result($q,0,'pret')/$iluminatCanti[0],2),
						  "um" => "lei/kw");
	}

	// ----------------------GAZ -----------------------------------------------
	$sql = "SELECT sum(F.cantitate) as cant, sum(F.cost) as pret
		FROM facturi F
		WHERE
		F.asoc_id=".$asoc." AND F.luna='".$luna."' AND (F.scara_id=".$scara." OR F.scara_id IS NULL) AND
		(F.tipServiciu IN (27) )AND
		F.procesata=1"; //GAZ
	$q = mysql_query($sql) or die("A aparut o eroare la citirea facturilor GAZ din BD <br />".$sql."<br />".  mysql_error());

	if (mysql_result($q,0,'pret') != NULL && mysql_result($q,0,'pret')) {
		$data []= array("fur" => "E-on Gaz",
						"val" => round( mysql_result($q,0,'pret'), 2),
						"total" => round( mysql_result($q,0,'cant'), 2),
						"um" => "mc");

		$tarife []= array("fur" => "E-on Gaz",
						  "val" => round(mysql_result($q,0,'pret')/mysql_result($q,0,'cant'),2),
						  "um" => "lei/mc");
	}

	//obtinem facturile fara a include nimic referitor la apa sau incalzire
	$sql = "
		SELECT Fur.furnizor, F.cost, S.mod_impartire, S.unitate, S.serv_id, F.locatari, F.tipFactura, S.localizare
		FROM facturi F, servicii S, furnizori_servicii FS, furnizori Fur
		WHERE
		(Fur.fur_id IN (SELECT fur_id FROM asociatii_furnizori WHERE asoc_id=".$asoc.") OR Fur.fur_id IN (SELECT fur_id FROM scari_furnizori WHERE scara_id=".$scara.")) AND
		S.serv_id=FS.serv_id AND FS.fur_id=Fur.fur_id AND
		F.tipServiciu=S.serv_id AND
		F.asoc_id=".$asoc." AND F.luna='".$luna."' AND (F.scara_id=".$scara." OR F.scara_id IS NULL) AND
		F.tipServiciu NOT IN (".$ApaRServiciId.', '.$ApaCServiciId.', '.$IncaServiciId.") AND
		F.procesata=1";

	$q = mysql_query($sql) or die("A aparut o eroare la citirea facturilor din BD <br />".$sql."<br />".  mysql_error());


	$um = array('ap', 'mp', 'pers', 'mc', 'gcal', 'kw', 'rep');
	while ($proc = mysql_fetch_array($q)) {
		$tipServiciu = $proc['serv_id'];
		$tipFactura = $proc['tipFactura'];
		if( $tipFactura == 4 ) //in cazul in care avem o factura la nivelul de locatar
		{//nu stiu ce se intampla la acest nivel

		//***********************************//
		// NU SE PROCESEAZA DACA E PE LOCATAR//
		//***********************************//

		/*die("Factura pe locatar !!! (nu stiu cum sa o adaug in lista de facturi)");
		$data []= array("fur" => $proc['furnizor'],
						"val" => round( $proc['cost'], 2),
						"total" => "",
						"um" => "");
		*/
		$chelt_ben []= array("fur" => $proc['furnizor'],
						  "val" => round($proc['cost'],4),
						  "um" => "lei");

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
				case 7: //pe consum apa rece
                    $select .= "L.loc_id, L.scara_id, L.asoc_id, A.consum_rece as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L INNER JOIN (SELECT * FROM apometre WHERE luna='$luna' AND asoc_id=$asoc) A ON L.loc_id = A.loc_id LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                    break;
				default:
					die("Nu se poate procesa o factura cu acest mod de inpartire a facturi");
			}

			switch ($tipFactura) {
				case 1: //asociatie
					$select .= ' L.asoc_id='.$asoc;
					break;
				case 2: //scara
					$select .= ' L.scara_id='.$scara;
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

			while ($row = mysql_fetch_assoc($query)) {
				$info [] = $row;
				$total += $row['valoare']*$row['procent']/100;
			}
			//In total avem cantitatea dorita

			if (!in_array($tipServiciu, $ServiciiSpecialeIds)) {
				$data []= array("fur" => $proc['furnizor'],
							"val" => round( $proc['cost'], 2),
							"total" => round( $total, 2),
							"um" => $um[$proc['unitate']]);
			}

			switch($tipServiciu) {
				/*case 27: //gaz
					$tarife []= array("fur" => "E-on GAZ",
									  "val" => round($proc['cost']/$total,2),
									  "um" => "lei/mc");
					break;

				case 35: //iluminat
					$tarife []= array("fur" => "E-on",
									  "val" => round($proc['cost']/$total,2),
									  "um" => "lei/kw");
					break;*/
				case 55: //gunoi
					$tarife []= array("fur" => "Salubris",
									  "val" => 7.02,//round($proc['cost']/$total,2),
									  "um" => "lei/pers");
					break;
				default:
			}

			switch($proc['localizare']) {
				case "Alta natura":
					$chelt_an []= array("fur" => $proc['furnizor'],
						  "val" => round($proc['cost']/$total,2),
						  "um" => "lei/ap");
					break;
				case "Persoana":
					$chelt_pers []= array("fur" => $proc['furnizor'],
						  "val" => round($proc['cost']/$total,2),
						  "um" => "lei/pers");
					break;
				case "Cota parte indiviza":
					$chelt_cota []= array("fur" => $proc['furnizor'],
						  "val" => round($proc['cost']/$total,2),
						  "um" => "lei/mp");
					break;
				case "Beneficiari":
					$chelt_ben []= array("fur" => $proc['furnizor'],
						  "val" => round($proc['cost'],2),
						  "um" => "lei");
					break;
				default:
			}
		}
	}

	//iluminat

	$sql = "
	SELECT Fur.furnizor, F.cost, S.mod_impartire, S.unitate, S.serv_id, F.locatari, F.tipFactura, S.localizare
	FROM facturi F, servicii S, furnizori_servicii FS, furnizori Fur
	WHERE
	(Fur.fur_id IN (SELECT fur_id FROM asociatii_furnizori WHERE asoc_id=".$asoc.") OR Fur.fur_id IN (SELECT fur_id FROM scari_furnizori WHERE scara_id=".$scara.")) AND
	S.serv_id=FS.serv_id AND FS.fur_id=Fur.fur_id AND
	F.tipServiciu=S.serv_id AND
	F.asoc_id=".$asoc." AND F.luna='".$luna."' AND (F.scara_id=".$scara." OR F.scara_id IS NULL) AND
	F.tipServiciu = 35 AND
	F.procesata=1";

	$sql_q = mysql_query($sql) or die('nu pot afla daca are factura de iluminat procesata');
	if (mysql_num_rows($sql_q) >= 1) {
		$iluminat_s = "SELECT

		round(sum(cant_fact_pers), 2) AS cantitate_totala,
		round(sum(cant_fact_pers*pret_unitar), 2) AS valoare_totala,
		round(max(pret_unitar), 2) AS pret_maxim,
		round((sum(cant_fact_pers*pret_unitar) / sum(cant_fact_pers)), 2) AS pret_calculat,
		S.serviciu, S.localizare

		FROM `fisa_indiv` FI INNER JOIN servicii S ON FI.serviciu=S.serv_id
		WHERE luna='".$luna."' AND scara_id=".$scara." AND FI.serviciu IN (90, 91, 84, 107, 92, 93, 211, 212)
		GROUP BY FI.serviciu";



		$iluminat_q = mysql_query($iluminat_s) or die('Nu pot afla daca exista servicii de Iluminat la afisarea LP <br />'.$iluminat_s);

		while ($iluminat_r = mysql_fetch_assoc($iluminat_q)) {
			switch($iluminat_r['localizare']) {
				case "Alta natura":
					$chelt_an []= array("fur" => $iluminat_r['serviciu'],
					"val" => $iluminat_r['pret_calculat'],
					"um" => "lei/ap");
					break;
				case "Persoana":
					$chelt_pers []= array("fur" => $iluminat_r['serviciu'],
					"val" => $iluminat_r['pret_calculat'],
					"um" => "lei/pers");
					break;
				case "Cota parte indiviza":
					$chelt_cota []= array("fur" => $iluminat_r['serviciu'],
					"val" => $iluminat_r['pret_calculat'],
					"um" => "lei/mp");
					break;
				case "Beneficiari":
					$chelt_ben []= array("fur" => $iluminat_r['serviciu'],
					"val" => $iluminat_r['valoare_totala'],
					"um" => "lei");
					break;
				default:
			}

		}


	}

	$output = '<table width="360" border=0>';
	foreach ( $data as $row ) {
		$output .= '<tr><td>Fact '.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>lei</td>
						<td align="right">'.$row['total'].' </td>
						<td>'.$row['um'].' </td></tr>';
	}
	$output .= '</table>';
	$afisare_facturi = $output;

	$output = '<table width="240" border=0>';
	foreach ( $tarife as $row ) {
		$output .= '<tr><td>Tarif '.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>'.$row['um'].' </td></tr>';
	}
	$output .= '</table>';
	$afisare_tarife = $output;

	if (count($chelt_an) > 0) {
	$output = '<table width="240" border=0><tr><td colspan="2" style="text-align:center; font-weight:bold">Cheltuieli de alta natura</td></tr>';
	$total = 0;
	foreach ( $chelt_an as $row ) {
		$output .= '<tr><td>'.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>'.$row['um'].'</td></tr>';
		$total += $row['val'];
	}

	$output .= '<tr><td style="font-weight:bold">Total</td>
						<td align="right" style="font-weight:bold">'.$total.'</td>
						<td style="font-weight:bold">lei/ap</td></tr>';
	$output .= '</table>';
	$chelt_an = $output;
	} else $chelt_an = '';

	if (count($chelt_ben) > 0) {
	$output = '<table width="360" border=0><tr><td colspan="2" style="text-align:center; font-weight:bold">Cheltuieli pe beneficiari</td></tr>';
	$total = 0;
	foreach ( $chelt_ben as $row ) {
		$output .= '<tr><td>'.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>'.$row['um'].'</td></tr>';
		$total += $row['val'];
	}
	$output .= '<tr><td style="font-weight:bold">Total</td>
						<td align="right" style="font-weight:bold">'.$total.'</td>
						<td style="font-weight:bold">lei</td></tr>';
	$output .= '</table>';
	$chelt_ben = $output;
	} else $chelt_ben = '';

	$output = '<table width="260" border=0>';
	$total = 0;
	foreach ( $chelt_pers as $row ) {
		$output .= '<tr><td>'.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>'.$row['um'].'</td></tr>';
		$total += $row['val'];
	}
	$output .= '<tr><td style="font-weight:bold">Total</td>
						<td align="right" style="font-weight:bold">'.$total.'</td>
						<td style="font-weight:bold">lei</td></tr>';
	$output .= '</table>';
	$chelt_pers = $output;

	if (count($chelt_cota) > 0) {
	$output = '<table width="260" border=0><tr><td colspan="2" style="text-align:center; font-weight:bold">Cheltuieli pe cota parte indiviza</td></tr>';
	$total = 0;
	foreach ( $chelt_cota as $row ) {
		$output .= '<tr><td>'.$row['fur'].'</td>
						<td align="right">'.$row['val'].'</td>
						<td>'.$row['um'].'</td></tr>';
		$total += $row['val'];
	}
	$output .= '<tr><td style="font-weight:bold">Total</td>
						<td align="right" style="font-weight:bold">'.$total.'</td>
						<td style="font-weight:bold">lei</td></tr>';
	$output .= '</table>';
	$chelt_cota = $output;
	} else $chelt_cota = '';


	return array($afisare_facturi, $afisare_tarife, $chelt_an, $chelt_ben, $chelt_pers, $chelt_cota);
}

/*************************************************************
 ****************   Header   *********************************
 *************************************************************/
$ResultsPDFContent .=
    '
    <div class="LP-PDF">
    <table class="LP header" style="margin-bottom:0px" border="0" backgound="background.jpg">
        <tr>
            <td bordercolor="white"><img src="sigla.jpg" width="170"></td>
            <td bordercolor="white" width="700">
                <center>
                    <div><h2>Lista de plata</h2> </div>
                    <div>'.Util::format_date_pdf($luna).'</div>
                </center>
            </td>
            <td bordercolor="white" align="right" style="font-size:13px;">' . $adresaOfficeUrbica . '</td>
        </tr>
    </table>
    <div style="font-size:10px; margin-left:0px; margin-top:0px;">' . Util::get_asoc_name($asoc_id) . '</div>';


/*****************************************************
 *************   Tabel initial  **********************
 *****************************************************/
list($afisare_facturi, $afisare_tarife, $chelt_an, $chelt_ben, $chelt_pers, $chelt_cota) = get_info_lista($_GET['asoc_id'], $_GET['scara_id'], $_GET['luna']);

$dataProcesata = Util::getDataProcesare($scara_id, $luna);
if(!$dataProcesata) $dataProcesata = date('Y-m-d');

$ResultsPDFContent.='
<table class="LP facturi" border="1" style="font-size:'.$hedder_font_size_LP.'px; margin:0px 0px 10px 0px;" cellspacing="0">
    <tr>
        <td width=240 ><center><b>Adresa</b></center></td>
        <td width=240 ><center><b>Tarife</b></center></td>
        <td width=360 ><center><b>Valoare/Consumuri din facturi furnizori</b></center></td>
        <td width=260 ><center><b>Cheltuieli pe persoana</b></center></td>

    </tr>
        <tr style="font-size:9px;">
        <td>' . Util::get_sc_address($scara_id) . '
            <br /><br />
            <span style="text-align:left;">Data afisarii: ' . date('d.m.Y', strtotime($dataProcesata)) . '<br />
            Termen de plata: ' . Util::termen_plata($asoc_id, $dataProcesata) . '<br />
            Data scadentei: ' . Util::data_scadentei($asoc_id, $dataProcesata) . '<br />
            <br />
            <small>Procent de penalizare ' . Util::procent_penalizare($asoc_id) . '% pe zi</small></span>
        </td>
        <td valign="top">'.$afisare_tarife.$chelt_an.'</td>
        <td valign="top">'.$afisare_facturi.$chelt_ben.'</td>
        <td valign="top">'.$chelt_pers.$chelt_cota.'</td>

    </tr>
</table>
';

/********************************************************
 *************   Tabel cap general  **********************
 *********************************************************/

$ResultsPDFContent.='
<table class="LP content" border=1 style="border-bottom:1px solid black;  border-top:1px solid black; " cellspacing=0 repeat_header="1">
   <thead style="text-align:center; font-weight:bold; '.$td_hedder_style_LP.'">
   <tr>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" text-rotate="90">Nr. '.$ap_nume.'</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Nume</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">CT</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" text-rotate="90">Nr. Pers.</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">ST</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" colspan="2">Apa Rece</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" colspan="2">Apa Calda</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" colspan="3">Diferenta</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Incalzire</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" nowrap="nowrap" style="'.$td_hedder_style_LP.'white-space:nowrap">Chelt pe<br />pers</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" nowrap="nowrap" style="'.$td_hedder_style_LP.'white-space:nowrap">Chelt pe<br />cota<br />indivizia</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" nowrap="nowrap" style="'.$td_hedder_style_LP.'white-space:nowrap">Chelt pe<br />benefi<br />ciari</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" nowrap="nowrap" style="'.$td_hedder_style_LP.'white-space:nowrap">Chelt <br />de alta<br />natura</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Total<br />Luna</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Restante</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Penalizari</td>';
	   if($restanta_fond)
       		$ResultsPDFContent.='<td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Restanta<br />fond</td>';
       $ResultsPDFContent.='<td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Fond<br />'.$fond_nume.'.</td>';
       if($fond_rep)
       		$ResultsPDFContent.='<td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Fond<br />rep.</td>';
       	if($incasari) {
       		$ResultsPDFContent.='<td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Incasari</td>';
       		$lunaTrecutaIncasari = explode('-', $luna);
			$lunaTrecutaIncasari = strtotime($lunaTrecutaIncasari[1].'-'.$lunaTrecutaIncasari[0].'-1');
			$lunaTrecutaIncasari = strtotime('-1 month', $lunaTrecutaIncasari);
			$lunaTrecutaIncasari = date('m-Y', $lunaTrecutaIncasari);
       		$dataProcesataLunaTrecutaIncasari = Util::getDataProcesare($scara_id, $lunaTrecutaIncasari);
       	}
       $ResultsPDFContent.='<td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2">Total<br />General</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC" rowspan="2" text-rotate="90">Nr. '.$ap_nume.'</td>
   </tr>

   <tr>
   	   <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">AR</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">valoare</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">AC</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">valoare</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">AR</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">AC</td>
       <td style="'.$td_hedder_style_LP.'" bgcolor="#CCCCCC">valoare</td>
   </tr>
   </thead>';

/**************************************************************
 ****************** Continut tabel general ********************
 **************************************************************/
$i = 0;

$nr_pers = 0;
$sup_tot = 0;

$total_consum_apa_rece = 0;
$total_valoare_apa_rece = 0;
$total_diferente_apa_rece = 0;

$total_consum_apa_calda = 0;
$total_valoare_apa_calda = 0;
$total_diferente_apa_calda = 0;

$total_diferente_apa_locatar = 0;
$total_valoare_diferente = 0;

$total_incalzire = 0;

$total_chelt_pers = 0;
$total_chelt_cota_indiv = 0;
$total_chelt_benef = 0;
$total_chelt_alta_natura = 0;

$total_restante = 0;
$total_penalizari = 0;
$total_restanta_fond = 0;
$total_fond_rulment = 0;
$total_fond_reparatii = 0;
$total_fond_special = 0;

$total_partial_final = 0;
$total_general_final = 0;

$total_incasari = 0;

$sql = "SELECT * FROM lista_plata WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND luna='".$luna."' ORDER BY cast(ap as UNSIGNED ) ASC, SUBSTR( ap, -1, 1 ) ASC ";
$sql = mysql_query($sql) or die("Nu ma pot conecta la tabela lista_plata<br />".mysql_error());
//$ResultsPDFContent.='<tbody style="'.$td_content_style_LP.'">';

$lunaUrmX = explode('-', $luna);
$lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
$lunaUrmX = strtotime('+1 month', $lunaUrmX);
$lunaUrm = date('m-Y', $lunaUrmX);


$verifFI = "SELECT SUM(cant_fact_pers*pret_unitar)/SUM(cant_fact_pers) as pret_apa_rece FROM fisa_indiv WHERE scara_id=".$scara_id." AND luna='".$luna."' AND serviciu='21'";
$verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
if (mysql_num_rows($verifFI) == 0) { $areApaRece = false; $pret_apa_rece = 1;} else { 
    $pret_apa_rece = mysql_result($verifFI, 0, 'pret_apa_rece');
    $areApaRece = true; 
}

$verifFI = "SELECT SUM(cant_fact_pers*pret_unitar)/SUM(cant_fact_pers)*2 as pret_apa_calda FROM fisa_indiv WHERE scara_id=".$scara_id." AND luna='".$luna."' AND (serviciu='37' OR serviciu='38')";
$verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
if (mysql_num_rows($verifFI) == 0) { $areApaCalda = false; $pret_apa_calda = 1;} else { 
    $pret_apa_calda = mysql_result($verifFI, 0, 'pret_apa_calda');
    $areApaCalda = true; 
}
if (!isset($pret_apa_rece) || $pret_apa_rece == 0 || $pret_apa_rece == NULL)
	$pret_apa_rece = 1;
if (!isset($pret_apa_calda) || $pret_apa_calda == 0 || $pret_apa_calda == NULL)
	$pret_apa_calda = 1;

while ($row = mysql_fetch_array($sql)){

	if ($i%2 == 0) {  $color = "#EEEEEE"; } else { $color="#FFFFFF";  }
	$nr_pers += $row['nr_pers'];
	$sup_tot += $row['supr'];

	$total_diferente_apa_locatar = $row['dif_ac'] + $row['dif_ar'];

	$restanta = "SELECT * FROM fisa_fonduri WHERE loc_id=".$row['loc_id']." AND data='".$lunaUrm."'";
	$restanta = mysql_query($restanta) or die ("Nu pot selecta datele din fisa fonduri<br />".mysql_error());

	$restanta_lunaAnt = "SELECT * FROM fisa_fonduri WHERE loc_id=".$row['loc_id']." AND data='".$luna."'";
	$restanta_lunaAnt = mysql_query($restanta_lunaAnt) or die ("Nu pot selecta datele din fisa fonduri luna anterioara<br />".mysql_error());

	$fondSpec = mysql_result($restanta, 0, 'fond_spec_rest') + mysql_result($restanta, 0, 'fond_spec_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_spec_rest');
	//$total_fond_rulment += $fondSpec;

	$total_partial = $row['ar_val'] + $row['ac_val'] + $total_diferente_apa_locatar + $row['incalzire'] + $row['chelt_pe_pers'] + $row['chelt_cota_indiv'] + $row['chelt_pe_benef'] + $row['alte_cheltuieli'];
	$total_general = round($total_partial, 2) + round($row['restante'], 2) + round($row['penalizari'], 2) + round($row['rest_fond'], 2) + round($row['fond_rul'], 2) + round($row['fond_rep'], 2);
	$total_general += $fondSpec;

	$total_consum_apa_rece += $row['ar'];
	$total_valoare_apa_rece += $row['ar_val'];
	$total_diferente_apa_rece += $row['dif_ar'];

	$total_consum_apa_calda += $row['ac'];
	$total_valoare_apa_calda += $row['ac_val'];
	$total_diferente_apa_calda += $row['dif_ac'];

	$total_valoare_diferente = $total_diferente_apa_calda + $total_diferente_apa_rece;

	$total_incalzire += $row['incalzire'];

	$total_chelt_pers += $row['chelt_pe_pers'];
	$total_chelt_cota_indiv += $row['chelt_cota_indiv'];
	$total_chelt_benef += $row['chelt_pe_benef'];
	$total_chelt_alta_natura += $row['alte_cheltuieli'];

	$total_restante += $row['restante'];
	$total_penalizari += $row['penalizari'];
	$total_restanta_fond += $row['rest_fond'];
	$total_fond_rulment += $row['fond_rul'];
	$total_fond_reparatii += $row['fond_rep'];
	$total_fond_special += $fondSpec;


	if($incasari) {
		$incasari_s = " SELECT sum(valoare) as total_incasari 
						FROM fisa_cont 
						WHERE loc_id=".$row['loc_id']." 
						AND explicatie = 'plata intretinere'
						AND data >= '$dataProcesataLunaTrecutaIncasari'
						AND data <= '$dataProcesata'"; 
		$incasari_q = mysql_query($incasari_s) or die(mysql_error().'<br />'.$incasari_s);
		$incasari_r = mysql_fetch_assoc($incasari_q);
		$incasari_r = $incasari_r['total_incasari'];

		$total_incasari += $incasari_r;
	}


	$total_partial_final += $total_partial;
	$total_general_final += $total_general;



	$ResultsPDFContent.='<tr bgcolor='.$color.'>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$row['ap'].'</td>';
	$ResultsPDFContent.= '<td style="white-space:nowrap; '.$td_content_style_LP.'" height="'.$thHeight.'" nowrap="nowrap">'.$row['nume'].'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$row['centrala'].'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$row['nr_pers'].'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['supr'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['ar'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['ar_val'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['ac'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['ac_val'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['dif_ar'] / $pret_apa_rece, 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['dif_ac'] / $pret_apa_calda, 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_diferente_apa_locatar, 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['incalzire'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['chelt_pe_pers'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['chelt_cota_indiv'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['chelt_pe_benef'], 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($row['alte_cheltuieli'], 2).'</td>';
	$ResultsPDFContent.= '<td style="font-weight:bold; '.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($total_partial, 2) == 0 ? 0 : round($total_partial, 2)).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($row['restante'], 2) == 0 ? 0 : round($row['restante'], 2)).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($row['penalizari'], 2) == 0 ? 0 : round($row['penalizari'], 2)).'</td>';
	if($restanta_fond)
		$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($row['rest_fond'], 2) == 0 ? 0 : round($row['rest_fond'], 2)).'</td>';
	if($fond_nume == 'spe')
		$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($fondSpec, 2) == 0 ? 0 : round($fondSpec, 2)).'</td>';
	if($fond_nume == 'rul')
		$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($row['fond_rul'], 2) == 0 ? 0 : round($row['fond_rul'], 2)).'</td>';
	if($fond_rep)
		$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($row['fond_rep'], 2) == 0 ? 0 : round($row['fond_rep'], 2)).'</td>';
	if($incasari)
		$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.(round($incasari_r, 2)).'</td>';
	$ResultsPDFContent.= '<td style="font-weight:bold; '.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_general, 2).'</td>';
	$ResultsPDFContent.= '<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$row['ap'].'</td>';
	$ResultsPDFContent.= '</tr>';

	$i++;
}

/**************************************************************
 ****************** Total tabel general ********************
 **************************************************************/

$ResultsPDFContent.='
    <tr>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'" colspan="2">TOTAL</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">&nbsp;</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$nr_pers.'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$sup_tot.'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.$total_consum_apa_rece.'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_valoare_apa_rece,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_consum_apa_calda,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_valoare_apa_calda,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_diferente_apa_rece / $pret_apa_rece,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_diferente_apa_calda / $pret_apa_calda,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_valoare_diferente,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_incalzire,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_chelt_pers,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_chelt_cota_indiv,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_chelt_benef,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_chelt_alta_natura,0).'</td>
        <td style="font-weight:bold; '.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_partial_final,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_restante,0).'</td>
        <td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_penalizari,0).'</td>';
    if($restanta_fond)
        $ResultsPDFContent.='<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_restanta_fond,0).'</td>';
	if($fond_nume == 'spe')
		$ResultsPDFContent.='<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_fond_special,0).'</td>';
	if($fond_nume == 'rul')
    	$ResultsPDFContent.='<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_fond_rulment,0).'</td>';
    if($fond_rep)
        $ResultsPDFContent.='<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_fond_reparatii,0).'</td>';
    if($incasari)
        $ResultsPDFContent.='<td style="'.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_incasari,0).'</td>';
        
        $ResultsPDFContent.='<td style="font-weight:bold; '.$td_content_style_LP.'" height="'.$thHeight.'">'.round($total_general_final,0).'</td>
		<td style="'.$td_content_style_LP.'" height="'.$thHeight.'"></td>
    </tr>
</table>';
$ResultsPDFContent.='
<table class="LP footer">
    <tr>
        <td width="250"><center>Presedinte '.Util::getPresedinte($asoc_id).'</center></td>
        <td width="500"><center>Administrator</center></td>
        <td width="300"><center>Cenzor</center></td>
    </tr>
	<tr>
		<td></td>
	</tr>
</table>
</div>';
