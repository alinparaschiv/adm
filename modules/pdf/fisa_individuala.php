<?php

$asocId = $_GET['asoc_id'];
$scaraId = $_GET['scara_id'];
$loc_id = isset($_GET['loc_id']) ? $_GET['loc_id'] : null;
$luna = $_GET['luna'];

$sql = "SELECT L.*, A.*, S.*, ST.*, ADM.nume as reprezentant, ADM.telefon, ASET.termen, ASET.predare  FROM locatari L, asociatii A, scari S,  strazi ST, admin ADM, asociatii_setari ASET WHERE A.asoc_id=ASET.asoc_id AND ADM.id=A.administrator_id AND ST.str_id=A.str_id AND L.asoc_id=A.asoc_id AND L.scara_id=S.scara_id AND L.scara_id=".$scara_id;
if ($loc_id) $sql .= ' AND L.loc_id='.$loc_id;
$sql = mysql_query($sql) or die ("Nu pot afla locuitori acestei scari<br />".mysql_error());

while ($proc = mysql_fetch_array($sql)) {
	$locId = $proc['loc_id'];

	$pen = new Penalizare( $locId, $scaraId, $asocId);
	//$pen->verifica();

	if (!$PDF_newPage) {
		$ResultsPDFContent .='<pagebreak orientation="L" type="SINGLE-SIDED">';
	}

	$FISA_INDIVIDUALA_CONTENT ='<table class="FI content" style="page-break-inside:avoid" border="1" cellspacing="0" bordercolor="#000000" width="100%" autosize="5">
	<thead style="font-weight:bold;" valign="top">
	    <tr>
	        <td bgcolor="#666666">&nbsp;</td>
	        <td bgcolor="#666666" colspan="4" align="center">Servicii pe apartament</td>
	        <td bgcolor="#666666" colspan="5" align="center">Servicii generale</td>
	    </tr>
	    <tr bgcolor="#CCCCCC" valign="middle">
	        <td align="center" width="220">Serviciu</td>
	        <td align="center" width="80">Cantitate</td>
	        <td align="center" width="100">U.M.</td>
	        <td align="center" width="90">Pret unitar</td>
	        <td align="center" width="80">Total lei</td>
	        <td align="center" width="180">Act doveditor<br />Serie / Nr</td>
	        <td align="center" width="90">Cantitate<br />per total</td>
	        <td align="center" width="90">U.M.</td>
	        <td align="center" width="90">Pret unitar</td>
	        <td align="center" width="90">Total lei</td>
	    </tr>
	</thead>
	<tbody align="left">';


	$i = 0;
	$fonduri = 0;
	$lunaCurenta = 0;


	//Facturi obisnuite
	$select = "SELECT F.*, S.serviciu as serv, S.unitate FROM fisa_indiv F, servicii S WHERE S.serv_id=F.serviciu AND S.fonduri='nu' AND F.loc_id=".$locId." AND F.luna='".$luna."' ORDER BY F.id ASC";
	$query = mysql_query($select) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
	if (mysql_num_rows($query) != 0){
		while ($row = mysql_fetch_array($query)){
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			if ($row['cant_fact_pers'] * $row['pret_unitar'] <> 0) {
				$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
  			<td align="center">'.$row['serv'].'</td>
  			<td align="center">'.$row['cant_fact_pers'].'</td>
  			<td align="center">'.$row['um'].'</td>
  			<td align="center">'.round($row['pret_unitar'], 2).'</td>
  			<td align="center">'.round($row['cant_fact_pers'] * $row['pret_unitar'], 2).'</td>
  			<td align="center">'.$row['factura'].'</td>
  			<td align="center">'.round($row['cant_fact_tot'], 2).'</td>
  			<td align="center">'.$uMasuraArr[$row['unitate']].'</td>
  			<td align="center">'.round($row['pret_unitar2'],2).'</td>
  			<td align="center">'.round($row['cant_fact_tot'] * $row['pret_unitar2'],2).'</td>
  		</tr>';
				$i ++;
				$lunaCurenta += $row['cant_fact_pers'] * $row['pret_unitar'];
			}
		}
	}


	$lunaUrmX = explode('-', $luna);
	$lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
	$lunaUrmX = strtotime('+1 month', $lunaUrmX);
	$lunaUrm = date('m-Y', $lunaUrmX);

	// FONDURI
	$select = "SELECT F.*, S.serviciu as serv, S.unitate FROM fisa_indiv F, servicii S WHERE S.serv_id=F.serviciu AND S.fonduri='da' AND F.asoc_id=".$asocId." AND F.scara_id=".$scaraId." AND F.loc_id=".$locId." AND F.luna='".$luna."' ORDER BY F.id ASC";
	$query = mysql_query($select) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
	if (mysql_num_rows($query) != 0){
		while ($row = mysql_fetch_array($query)){
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			if (round($row['cant_fact_pers'] * $row['pret_unitar'], 2) <> 0) {
				$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
					<td align="center">'.$row['serv'].'</td>
					<td align="center">'.$row['cant_fact_pers'].'</td>
					<td align="center">'.$uMasuraArr[$row['unitate']].'</td>
					<td align="center">'.round($row['pret_unitar'], 2).'</td>
					<td align="center">'.round($row['cant_fact_pers'] * $row['pret_unitar'], 2).'</td>
					<td align="center">'.$row['factura'].'</td>
					<td align="center">'.round($row['cant_fact_tot'], 2).'</td>
					<td align="center">'.$uMasuraArr[$row['unitate']].'</td>
					<td align="center">'.round($row['pret_unitar2'],2).'</td>
					<td align="center">'.round($row['cant_fact_tot'] * $row['pret_unitar2'],2).'</td>
				</tr>';
				$i ++;
				$fonduri += $row['cant_fact_pers'] * $row['pret_unitar'];
			}
		}
	}

	//restanta fonduri
	$restanta = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locId." AND data='".$lunaUrm."'";
	$restanta = mysql_query($restanta) or die ("Nu pot selecta datele din fisa fonduri<br />".mysql_error());

	$restanta_lunaAnt = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locId." AND data='".$luna."'";
	$restanta_lunaAnt = mysql_query($restanta_lunaAnt) or die ("Nu pot selecta datele din fisa fonduri luna anterioara<br />".mysql_error());


	if(mysql_num_rows($restanta) > 0 && mysql_num_rows($restanta_lunaAnt) > 0) {
		$fondRul = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rul_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rul_rest');
		$fondRep = mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_rep_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rep_rest');
		$fondSpec = mysql_result($restanta, 0, 'fond_spec_rest') + mysql_result($restanta, 0, 'fond_spec_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_spec_rest');

		$datoria = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_spec_rest')
		- $fondRul - $fondRep - $fondSpec;

		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		if ($datoria != 0){

			$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
				<td align="center">Restanta fonduri</td>
				<td align="center" colspan="3">&nbsp;</td>
				<td align="center">'.$datoria.'</td>
				<td align="center" colspan="5">&nbsp;</td>
				</tr>';
			$fonduri += $datoria;
			$i ++;
		}
	}
	//camp gol
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'><td colspan="10">&nbsp;</td></tr>';
	$i ++;

	// total pe luna curenta
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
		<td align="center">Luna curenta</td>
		<td align="center" colspan="3">&nbsp;</td>
		<td align="center">'.round($lunaCurenta, 2).'</td>
		<td align="center" colspan="5">&nbsp;</td>
	</tr>';
	$i ++;

	//restante
	$luna_procesata_s = 'SELECT * FROM lista_plata WHERE loc_id='.$locId.' AND luna=\''.$luna.'\' AND procesata=1';
	$luna_procesata_q = mysql_query($luna_procesata_s) or die('Nu pot afla daca luna curenta a fost procesata <br /> '.$luna_procesata_s);
	$luna_procesata_n = mysql_num_rows($luna_procesata_q);
	$luna_procesata_d = mysql_fetch_array($luna_procesata_q);
	$restante;

	if ($luna_procesata_n == 1) {
		$restante = $luna_procesata_d['restante'];
	} else {
		$restante = round($pen->getRestPlata(), 2);
	}

	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
	<td align="center">Restante</td>
	<td align="center" colspan="3">&nbsp;</td>
	<td align="center">'.($restante == 0 ? 0 : $restante).'</td>
	<td align="center" colspan="5">&nbsp;</td>
	</tr>';
	$i ++;

	//penalizari
	$penalizari;

	if ($luna_procesata_n == 1) {
		$penalizari = $luna_procesata_d['penalizari'];
	} else {
		$penalizari = round($pen->getPenalizari(), 2);
	}

	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
	<td align="center">Penalizari</td>
	<td align="center" colspan="3">&nbsp;</td>
	<td align="center">'.(round($penalizari, 2) == 0 ? 0 : round($penalizari, 2)).'</td>
	<td align="center" colspan="5">&nbsp;</td>
	</tr>';
	$i ++;


	//camp gol
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'><td colspan="10">&nbsp;</td></tr>';
	$i ++;

	$totalGeneral = $lunaCurenta + $restante + $penalizari + $fonduri;
	//total general
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	$FISA_INDIVIDUALA_CONTENT .='<tr bgcolor='.$color.'>
		<td align="center">TOTAL GENERAL</td>
		<td align="center" colspan="3">&nbsp;</td>
		<td align="center">'.round($totalGeneral, 2).'</td>
		<td align="center" colspan="5">&nbsp;</td>
	</tr>';
	$i ++;

	$FISA_INDIVIDUALA_CONTENT .= '</tbody>
	</table>';

	$ResultsPDFContent .='
	<table class="FI header" width="1100" style="margin-bottom:0px" border="0" backgound="background.jpg">
	        <tr>
	            <td bordercolor="white"><img src="sigla-urbica-72dpi.png"></td>
	            <td bordercolor="white" width="700">
	                <center>
	                    <div><h3>Fișa individuală</h3> </div>
	                    <div><h3>'.Util::format_date_pdf($luna).'</h3></div>
	                </center>
	            </td>
	            <td colspan="2" bordercolor="white" align="right">' . $adresaOfficeUrbica . '</td>
	        </tr>
	    ';
	// </table>
	list($l, $a) = explode('-',$luna);
	//$afisare = $proc['predare'].'-'.$l.'-'.$a;
	$afisare = mktime(0,0,0,$l+1, $proc['predare'], $a);
	$aux=0;
	if(date("l",$afisare) == "Saturday")
		$aux=2; //daca e sambata lista de plata este afisata luni (peste 2 zile)
	if(date("l",$afisare) == "Sunday")
		$aux=1; //daca e duminica lista de plata este afisata luni (peste 1 zii)

$dataProcesata = Util::getDataProcesare($scara_id, $luna);
if(!$dataProcesata) $dataProcesata = date('Y-m-d');

	$afisare = time(); //mktime(0,0,0,$l+1, $proc['predare']+$aux, $a);
	$termen  = strtotime("+".$proc['termen']." day"); // mktime(0,0,0,$l+1, $proc['predare']+$proc['termen']+$aux, $a);
	$scadenta= strtotime("+".(30+$proc['termen'])." day"); //mktime(0,0,0,$l+1, 30+$proc['predare']+$proc['termen']+$aux, $a);
	// <table  border="0" cellpadding="0" cellspacing="10">
	$ResultsPDFContent .='

	  <tr>
	    <td colspan="2" width="200"><h4>'.$proc['nume'].'</h4>Asociatia '.$proc['asociatie'].'<br />Str. '
			.$proc['strada'].', nr. '.$proc['nr'].', bl. '.$proc['bloc'].', sc. '.$proc['scara'].', '.strtolower($ap_nume).' '.$proc['ap'].'</td>
    <td>
	<div style="position: absolute; top: 25mm; left: 150mm; width: 300mm;" overflow="visible">'.Util::getTabelApometreFI($locId, $luna).'</div>
	</td>
    <td width="250" align="right">Responsabil administrativ:<br/>'.$proc['reprezentant'].'<br />Telefon: '.$proc['telefon'].'</td>
  </tr>
  <tr>
    <td colspan="3">Casierie Cuza Voda: luni - vineri, 9:00 - 17:00<br />Casierie '.$proc['adresa_casierie'].': '.$proc['orar_casierie'].'</td>
    <td rowspan="3">Data emiterii '.date('d.m.Y', strtotime($dataProcesata)).'<br />Termen de plata '.Util::termen_plata($asoc_id, $dataProcesata).'<br />Data scadenta '.Util::data_scadentei($asoc_id, $dataProcesata).'</td>
  </tr>';
  if($afiseaza_cont_bancar)
  $ResultsPDFContent .='
  <tr>
    <td colspan="3">Pentru transfer bancar, va rugam specificati adresa completa<br />
                    ING Bank: RO74INGB0000999901625008<br/>
						Millennium Bank: RO45MILB0000000001140222
	</td>
  </tr>';
  $ResultsPDFContent .='
  <tr>
    <td colspan="3">Total de plata <b>'.Util::format_date_pdf($luna).'</b>:  <b>'.round($totalGeneral, 2).'</b> lei.</td>
  </tr>
</table>';

	$ResultsPDFContent .= $FISA_INDIVIDUALA_CONTENT.'</pagebreak>';
	$PDF_newPage = false;
}
