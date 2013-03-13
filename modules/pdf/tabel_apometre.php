<?php

$lunaUrmX = explode('-', $luna);
$lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
$lunaUrmX = strtotime('+1 month', $lunaUrmX);
$lunaUrm = date('m-Y', $lunaUrmX);


$ResultsPDFContent .=  pdf_header('Tabel Centralizator Apometre<br />'.Util::get_asoc_name($asoc_id).' <br /> '.Util::get_sc_address($_GET['scara_id']).' <br /> '.Util::format_date_pdf($lunaUrm));
if (!$PDF_newPage) 
	$ResultsPDFContent .='<pagebreak  orientation="P" type="SINGLE-SIDED"/>';


function putHeader($scaraId){
	$data = '';

	$verificNrApo = "SELECT MAX(ap_rece), MAX(ap_calda) FROM locatari WHERE scara_id=".$scaraId;
	$verificNrApo = mysql_query($verificNrApo) or die ("Nu pot afla numarul maxim de apometre pe care il are un locatar<br />".mysql_error());

	$nrApoR = mysql_result($verificNrApo, 0, 'MAX(ap_rece)');
	$nrApoC = mysql_result($verificNrApo, 0, 'MAX(ap_calda)');

	$data .= '<tr valign="middle">';
		$data .= '<td bgcolor="#000" rowspan="2" style="color:#FFF">Etaj</td>';
		$data .= '<td bgcolor="#000" rowspan="2" style="color:#FFF">Ap.</td>';
		$data .= '<td bgcolor="#000" rowspan="2" style="color:#FFF">Nume</td>';

		for ($i=0; $i < $nrApoR; $i++){
			$poz = $i;
			$data .= '<td bgcolor="#000" colspan="2" style="color:#FFF">AR'.($poz+1).'</td>';
		}

		for ($i=0; $i < $nrApoC; $i++){
			$poz = $i;
			$data .= '<td bgcolor="#000" colspan="2" style="color:#FFF">AC'.($poz+1).'</td>';
		}

		$data .= '<td bgcolor="#000" colspan="2" style="color:#FFF">Consum</td>';
	$data .= '</tr>';

	$data .= '<tr>';
		for ($i = 0; $i<($nrApoR + $nrApoC); $i++){
			$data .= '<td bgcolor="#000" style="color:#FFF">I. Vechi</td>';
			$data .= '<td bgcolor="#000" style="color:#FFF">I. Nou</td>';
		}

		$data .= '<td bgcolor="#000" style="color:#FFF">Apa Rece</td>';
		$data .= '<td bgcolor="#000" style="color:#FFF">Apa Calda</td>';
	$data .= '</tr>';

	return $data;
}


function putContent($asocId, $scaraId, $luna){
	$luna = explode('-', $luna);
	$luna = date('m-Y', strtotime('+ 1 month', strtotime($luna[1].'-'.$luna[0].'-01')));

	$rezult = '';

	$verificNrApo = "SELECT MAX(ap_rece), MAX(ap_calda) FROM locatari WHERE scara_id=".$scaraId;
	$verificNrApo = mysql_query($verificNrApo) or die ("Nu pot afla numarul maxim de apometre pe care il are un locatar<br />".mysql_error());

	$nrApoR = mysql_result($verificNrApo, 0, 'MAX(ap_rece)');
	$nrApoC = mysql_result($verificNrApo, 0, 'MAX(ap_calda)');

	$ordine = 0;

	$nrLuni = "SELECT * FROM apometre WHERE asoc_id=".$asocId." GROUP BY luna";
	$nrLuni = mysql_query($nrLuni) or die ("Nu pot afla numarul de luni din tabela<br />".mysql_error());
	$nrLuni = mysql_num_rows($nrLuni);

	$primaLunaDinTabel = "SELECT luna FROM apometre WHERE asoc_id=".$asocId." GROUP BY luna ORDER BY a_id ASC";
	$primaLunaDinTabel = mysql_query($primaLunaDinTabel) or die ("Nu pot afla care este prima luna din tabel<br />".mysql_error());
	$primaLunaDinTabel = mysql_result($primaLunaDinTabel, 0, 'luna');

	$punLocatarii = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
	$punLocatarii = mysql_query($punLocatarii) or die ("Nu pot selecta locatarii<br />".mysql_error());

	while ($peRand = mysql_fetch_array($punLocatarii)){

		if ($ordine % 2 == 0){
			$culoare = "#DDDDDD";
			$text = "#000000";
		} else {
			$culoare = "#FFFFFF";
			$text = "#000000";
		}

		//culori diferite in functie de tipul de calculare a indecsilor
		$checkApo = "SELECT * FROM apometre WHERE loc_id=".$peRand['loc_id']." AND luna='".$luna."'";
		$checkApo = mysql_query($checkApo) or die ("Nu pot afla detalii despre locatarul curent <br />".mysql_error());

		if (mysql_result($checkApo, 0, 'auto') != 0){
			$culoare = "#CCFF99";
			$text = "#000000";
		}

		if (mysql_result($checkApo, 0, 'pausal') != 0){
			$culoare = "#6666CC";
			$text = "#FFFFFF";
		}

		if ((mysql_result($checkApo, 0, 'amenda_rece') != null) || (mysql_result($checkApo, 0, 'amenda_calda') != null)){
			$culoare = "#FF3366";
			$text = "#FFFFFF";
		}

		$rezult .= '<tr bgcolor="'.$culoare.'" style="color:'.$text.'">';
			$rezult .= '<td>'.$peRand['etaj'].'</td>';
			$rezult .= '<td>'.$peRand['ap'].'</td>';
			$rezult .= '<td>'.$peRand['nume'].'</td>';

			$nrOrdine = 0;

			//$verifLoc = "SELECT * FROM apometre WHERE loc_id=".$peRand['loc_id']." ORDER BY a_id DESC";
			$verifLoc = "SELECT * FROM `apometre` WHERE loc_id=".$peRand['loc_id']." AND  STR_TO_DATE(luna, '%m-%Y') < STR_TO_DATE('".$luna."', '%m-%Y') ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC LIMIT 1";
			$verifLoc = mysql_query($verifLoc) or die ("Nu pot afla detalii despre locatarul curent <br />".mysql_error());

			while ($nrOrdine < $nrApoR){
				if ($nrOrdine < $peRand['ap_rece']){
					$temp = $nrOrdine;

					// asta este pentru Indexul Vechi
					if ($primaLunaDinTabel == $luna){
						$apInit = "SELECT * FROM locatari_apometre WHERE loc_id=".$peRand['loc_id'];
						$apInit = mysql_query($apInit) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

						$rezult .= '<td align="center">'.mysql_result($apInit, 0, 'r'.($temp+1)).'</td>';
					} else {
						$rezult .= '<td align="center">'.mysql_result($verifLoc, 0, 'r'.($temp+1)).'</td>';
					}

					$rezult .= '<td></td>';

					$nrOrdine ++;
				} else {
					$rezult .= '<td></td>';
					$rezult .= '<td></td>';
					$nrOrdine ++;
				}
			}

			$nrOrdine = 0;
			while ($nrOrdine < $nrApoC){
				if ($nrOrdine < $peRand['ap_calda']){
					$temp = $nrOrdine;

					// asta este pentru Indexul Vechi
					if ($primaLunaDinTabel == $luna){
						$apInit = "SELECT * FROM locatari_apometre WHERE loc_id=".$peRand['loc_id'];
						$apInit = mysql_query($apInit) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

						$rezult .= '<td align="center">'.mysql_result($apInit, 0, 'c'.($temp+1)).'</td>';
					} else {
						$rezult .= '<td align="center">'.mysql_result($verifLoc, 0, 'c'.($temp+1)).'</td>';
					}

					$rezult .= '<td></td>';

					$nrOrdine ++;
				} else {
					$rezult .= '<td></td>';
					$rezult .= '<td></td>';
					$nrOrdine ++;
				}
			}

			//consumurile
			$rezult .= '<td></td>';
			$rezult .= '<td></td>';

		$rezult .= '</tr>';

		$ordine++;
	}
	return $rezult;
}




$ResultsPDFContent .='<table width="1100" style="margin-top:10px; background-color:#BBB;"><thead>';
	$ResultsPDFContent .= putHeader($_GET['scara_id']);
$ResultsPDFContent .='</thead>';
	$ResultsPDFContent .= putContent($_GET['asoc_id'], $_GET['scara_id'], $_GET['luna']);
$ResultsPDFContent .='</table>';