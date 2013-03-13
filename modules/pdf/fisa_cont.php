<?php

$asocId = $_GET['asoc_id'];
$scaraId = $_GET['scara_id'];
$loc_id = isset($_GET['loc_id']) ? $_GET['loc_id'] : null;


$sql = "SELECT L.*, A.*, S.*, ST.*, ADM.nume as reprezentant, ADM.telefon, ASET.termen, ASET.predare  FROM locatari L, asociatii A, scari S,  strazi ST, admin ADM, asociatii_setari ASET WHERE A.asoc_id=ASET.asoc_id AND ADM.id=A.administrator_id AND ST.str_id=A.str_id AND L.asoc_id=A.asoc_id AND L.scara_id=S.scara_id AND L.scara_id=".$scara_id;
if ($loc_id) $sql .= ' AND L.loc_id='.$loc_id;
$sql = mysql_query($sql) or die ("Nu pot afla locuitori acestei scari<br />".mysql_error());

$FISA_CONT_CONTENT = '';

while ($proc = mysql_fetch_array($sql)) {
$locId = $proc['loc_id'];

if (!$PDF_newPage) 
	$FISA_CONT_CONTENT .='<pagebreak  orientation="P" type="SINGLE-SIDED">';

$FISA_CONT_CONTENT .= pdf_header('Fisa Cont');


$FISA_CONT_CONTENT .='
<table width="100%" border="0" cellpadding="0" cellspacing="10">
	<tr>
		<td>Date identificare client<br />Nume: '.$proc['nume'].'<br />Asociatie:'.$proc['asociatie'].'<br />Adresa: Str. '.$proc['strada'].', Nr. '.$proc['nr'].', Bloc '.$proc['bloc'].', Sc. '.$proc['scara'].', ap. '.$proc['ap'].'</td>
		<td align="right">Responsabil administrativ:<br />'.$proc['reprezentant'].'<br />Telefon: '.$proc['telefon'].'</td>
	</tr>
</table>


<table width="100%">
<thead>
<tr>
  <td bgcolor="#666666" width="13%" align="center">Data</td>
  <td bgcolor="#666666" width="24%" align="center">Explicatie</td>
  <td bgcolor="#666666" width="10%" align="center">Act</td>
  <td bgcolor="#666666" width="14%" align="center">Valoare</td>
  <td bgcolor="#666666" width="11%" align="center">Datorie</td>
  <td bgcolor="#666666" width="15%" align="center">Total Penalizari</td>
  <td bgcolor="#666666" width="13%" align="center">Total General</td>
  </tr>
</thead>
<tbody>
';

$sql_fc = "SELECT * FROM fisa_cont WHERE loc_id=".$locId."  ORDER BY data";
$sql_fc = mysql_query($sql_fc) or die(mysql_error());

$i = 1;
$aliniere = "right";

while($row = mysql_fetch_assoc($sql_fc)) {
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	if ($row['act'] == "LP") { $aliniere = "left"; } else { $aliniere = "right"; }

	$FISA_CONT_CONTENT .='<tr bgcolor="'.$color.'">
	  <td align="center">'.$row['data'].'</td>
	  <td align="center">'.$row['explicatie'].'</td>
	  <td align="center">'.$row['act'].'</td>
	  <td align="'.$aliniere.'">'.round($row['valoare'], 2).'</td>
	  <td align="center">'.round($row['datorie'], 2).'</td>
	  <td align="center">'.round($row['total_penalizari'], 2).'</td>
	  <td align="center">'.round($row['total_general'], 2).'</td>
	</tr>';

	$i++;
}
$FISA_CONT_CONTENT .='
</tbody>
</table>
';

}
$ResultsPDFContent .= $FISA_CONT_CONTENT;

$PDF_newPage = false;
?>