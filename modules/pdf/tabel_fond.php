<?php

$ResultsPDFContent .= pdf_header('Tabel Centralizator Fonduri<br />'.Util::get_asoc_name($asoc_id).'<br />'.Util::get_sc_address($_GET['scara_id']).' ('.date('d-m-Y').')');
if (!$PDF_newPage) 
	$ResultsPDFContent .='<pagebreak  orientation="L" type="SINGLE-SIDED"/>';


function getData($asocId, $scaraId) {

	$ultimaLuna_s = 'SELECT data FROM fisa_fonduri WHERE scara_id=' .$scaraId.' GROUP BY data ORDER BY STR_TO_DATE(data, "%m-%Y") DESC LIMIT 1';
	$ultimaLuna_q = mysql_query($ultimaLuna_s) or die('NU pot afla care e cea mai veche inregistrare din fisa fonduri pentru scara curenta <br />'.$ultimaLuna_s);
	$ultimaLuna_r = mysql_result($ultimaLuna_q, 0, 'data');

	$rezult = '';

	// Selectez situatia fondurilor de pe ultima luna introdusa
	$sql = "SELECT * FROM fisa_fonduri ff, locatari l WHERE l.loc_id=ff.loc_id AND l.asoc_id=".$asocId." AND l.scara_id=".$scaraId." AND data='$ultimaLuna_r' ORDER BY l.loc_id";
	$sql = mysql_query($sql) or die ("Nu pot afisa fondurile pe luna curenta. <br />".mysql_error());

	// TOTAL: fondul de rulment
	$tot_fond_rul_incasat = 0;
	$tot_fond_rul_rest = 0;

	// TOTAL: fondurile speciale
	$tot_fond_spec_incasat = 0;
	$tot_fond_spec_rest = 0;
	$tot_fond_spec_cheltuit = 0;
	$tot_fond_spec_cumulat = 0;

	// TOTAL: fondul de reparatii
	$tot_fond_rep_incasat = 0;
	$tot_fond_rep_rest = 0;
	$tot_fond_rep_cheltuit = 0;
	$tot_fond_rep_cumulat = 0;

	// TOTAL: fondul de penalitati --> Nu merge momentan deoarece nu am realizat fisa furnizorilor
	$tot_fond_pen_constituit = 0;
	$tot_fond_pen_incasat = 0;

	// TOTAL: restante
	$tot_inc_gen = 0;
	$total_plata_gen = 0;


	$i = 0;
	while ($row = mysql_fetch_array($sql)){
		if ($i%2 == 0) { $color = "#CCCCCC"; } else { $color="#EEEEEE"; }
		$i++;

		$sql_grup = 'SELECT SUM(fond_rul_incasat) rul_i, SUM(fond_rep_incasat) rep_i, SUM(fond_spec_incasat) spe_i, SUM(fond_rep_cheltuit) rep_c FROM fisa_fonduri WHERE loc_id='.$row['loc_id'].' GROUP BY loc_id';
		$sql_grup = mysql_query($sql_grup) or die('Nu pot afla totalul incasarilor in fonduri');
		$sql_grup = mysql_fetch_assoc($sql_grup);

		$rezult .= '<tr>';
		$rezult .= '<td bgcolor="'.$color.'">Ap. '.$row['ap'].'</td>';

		//	$locatar = "SELECT * FROM locatari WHERE loc_id=".$row['loc_id'];
		//	$locatar = mysql_query($locatar) or die ("Nu pot afisa locatarii<br />".mysql_error());

		$rezult .= '<td bgcolor="'.$color.'">'.$row['nume'].'</td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rul_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_rul_rest'], 2).'</td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rep_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_rep_rest'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rep_c'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'"><strong>'.round(($sql_grup['rep_i'] - $sql_grup['rep_c']), 2).'</strong></td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['spe_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_spec_rest'], 2).'</td>';

		$tot_inc_loc = $sql_grup['rul_i'] + $sql_grup['rep_i'] + $sql_grup['spe_i'];
		$tot_rest_loc = $row['fond_rul_rest'] + $row['fond_rep_rest'] + $row['fond_spec_rest'];

		// Suma de plata pentru luna curenta este formata din totalul fondurilor - restantele de luna trecuta
		//$plata_gen = $tot_rest_loc - $row['restante'];

		$rezult .= '<td bgcolor="'.$color.'">'.round($tot_inc_loc, 2).'</td>';		// total incasari / locatar
		$rezult .= '<td bgcolor="'.$color.'">'.round($tot_rest_loc, 2).'</td>';		// cat are de plata pe luna curenta
		//$rezult .= '<td bgcolor="'.$color.'">'.$row['restante'].'</td>';  // restante de pe luna anterioara

		// Calculez totalurile pentru footerul tabelului
		$tot_fond_rul_incasat += $sql_grup['rul_i'];
		$tot_fond_rul_rest += $row['fond_rul_rest'];

		$tot_fond_rep_incasat += $sql_grup['rep_i'];
		$tot_fond_rep_rest += $row['fond_rep_rest'];
		$tot_fond_rep_cheltuit += $sql_grup['rep_c'];

		$tot_fond_spec_incasat += $sql_grup['spe_i'];
		$tot_fond_spec_rest += $row['fond_spec_rest'];

		//$tot_fond_pen_constituit += $row['fond_pen_constituit'];
		//$tot_fond_pen_incasat += $row['fond_pen_incasat'];

		$tot_inc_gen += $tot_inc_loc;
		$total_plata_gen += $tot_rest_loc;

		//$tot_rest_gen += $row['restante'];
	}

	$rezult .= '<tr> <td colspan="11">&nbsp;</td></tr>';

	$rezult .= '<tr>';
	$rezult .= '<td colspan="2" align="center"><strong>TOTAL:</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rul_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rul_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_cheltuit, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round(($tot_fond_rep_incasat - $tot_fond_rep_cheltuit), 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_spec_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_spec_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_inc_gen, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($total_plata_gen, 2).'</strong></td>';
	//$rezult .= '<td><strong>'.$tot_rest_gen.'</strong></td>';
	$rezult .= '</tr>';

	return $rezult;
}



$ResultsPDFContent .='<table width="1100">
<thead>
<tr>
  <td bgcolor="#666666">Nr. Crt.</td>
  <td bgcolor="#666666">Proprietar Apartament</td>

  <td bgcolor="#666666">Rulment <br /> Incasat</td>
  <td bgcolor="#666666">Rulment <br /> Restant</td>

  <td bgcolor="#666666">Reparatii <br /> Incasat</td>
  <td bgcolor="#666666">Reparatii <br /> Restant</td>
  <td bgcolor="#666666">Reparatii <br /> Cheltuit</td>
  <td bgcolor="#666666">Reparatii <br /> Cumulat</td>

  <td bgcolor="#666666">Special <br /> Incasat</td>
  <td bgcolor="#666666">Special <br /> Restant</td>

  <td bgcolor="#666666">Total <br /> Incasat</td>
  <td bgcolor="#666666">Total <br /> Restante</td>
  </tr>
</thead>';
$ResultsPDFContent .= getData($_GET['asoc_id'], $_GET['scara_id']);
$ResultsPDFContent .='</table>';