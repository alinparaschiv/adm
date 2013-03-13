<?php

$asocId = $_GET['asoc_id'];
$scaraId = $_GET['scara_id'];
$loc_id = isset($_GET['loc_id']) ? $_GET['loc_id'] : null;


$sql = "SELECT L.*, A.*, S.*, ST.*, ADM.nume as reprezentant, ADM.telefon, ASET.termen, ASET.predare  FROM locatari L, asociatii A, scari S,  strazi ST, admin ADM, asociatii_setari ASET WHERE A.asoc_id=ASET.asoc_id AND ADM.id=A.administrator_id AND ST.str_id=A.str_id AND L.asoc_id=A.asoc_id AND L.scara_id=S.scara_id AND L.scara_id=".$scara_id;
if ($loc_id) $sql .= ' AND L.loc_id='.$loc_id;
$sql = mysql_query($sql) or die ("Nu pot afla locuitori acestei scari<br />".mysql_error());



while ($proc = mysql_fetch_array($sql)) {
	$locId = $proc['loc_id'];

	if (!$PDF_newPage) 
		$FISA_PEN_CONTENT .='<pagebreak  orientation="P" type="SINGLE-SIDED"/>';
	
	$FISA_PEN_CONTENT .= pdf_header('Fisa Penalizare');
	$FISA_PEN_CONTENT .='
<table width="100%" border="0" cellpadding="0" cellspacing="10">
	<tr>
		<td>Date identificare client<br />Nume: '.$proc['nume'].'<br />Asociatie:'.$proc['asociatie'].'<br />Adresa: Str. '.$proc['strada'].', Nr. '.$proc['nr'].', Bloc '.$proc['bloc'].', Sc. '.$proc['scara'].', ap. '.$proc['ap'].'</td>
		<td align="right">Responsabil administrativ:<br />'.$proc['reprezentant'].'<br />Telefon: '.$proc['telefon'].'</td>
	</tr>
</table>


<table width="100%">
<thead>
<tr>
    <td bgcolor="#666666" align="center" width="20%">Luna</td>
    <td bgcolor="#666666" align="center" width="10%">Valoare Debit</td>
    <td bgcolor="#666666" align="center" width="15%">Data scadenta</td>
    <td bgcolor="#666666" align="center" width="15%">Data Platii</td>
    <td bgcolor="#666666" align="center" width="10%">Nr.Zile</td>
    <td bgcolor="#666666" align="center" width="10%">Procentul de penalizare</td>
    <td bgcolor="#666666" align="center" width="20%">Valoarea penalizarii</td>
  </tr>
</thead>
<tbody>
';

	$sql_fp = "SELECT P.* FROM fisa_pen P, fisa_cont C WHERE P.id_restanta=C.id AND P.`loc_id`=".$locId." ORDER BY C.data, C.id";
	$sql_fp = mysql_query($sql_fp) or die(mysql_error());

	$i = 1;
	$totalPenalizari = 0;

	while($row = mysql_fetch_array($sql_fp)) {

		if ($row['data_platii'] == null) {
			$row['data_platii'] = date('Y-m-d');
			$date = explode('-', $row['data_scadenta']);
			$z1 = mktime(0,0,0, date('m'), date('j'), date('Y'));
			$z2 = mktime(0,0,0, $date[1], $date[2], $date[0]);
			$zile = floor(($z1 - $z2) / 86400);
			$row['nr_zile'] = $zile;
			$row['val_pen'] = $zile * (floatval($row['proc_pen']) / 100) * $row['valoare_debit'];
		}

		$row['val_pen'] = round($row['val_pen'], 2);

		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		if($row['val_pen'] > 0) {
			$FISA_PEN_CONTENT .= '<tr>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'">'.$row['luna'].'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'">'.round($row['valoare_debit'], 2).'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'" align="center">'.$row['data_scadenta'].'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'" align="center">'.$row['data_platii'].'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'">'.$row['nr_zile'].'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'">'.$row['proc_pen'].'</td>';
			$FISA_PEN_CONTENT .= '<td bgcolor="'.$color.'">'.$row['val_pen'].'</td>';
			$FISA_PEN_CONTENT .= ' </tr>';
			$i++;

			$totalPenalizari += $row['val_pen'];
		}
	}
	$FISA_PEN_CONTENT .='

	  <tr>
     	<td bgcolor="#aaaaaa" colspan="6" align="right">Total:</td>
     	<td bgcolor="#aaaaaa" align="left">'.round($totalPenalizari, 2).'</td>
	  </tr>
	</tbody>
	</table>
	';

}
$ResultsPDFContent .= $FISA_PEN_CONTENT;

$PDF_newPage = false;
?>