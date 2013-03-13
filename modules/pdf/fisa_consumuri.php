<?php
if(!isset($consumuri_util_load)) include('fisa_consumuri_util.php');
/////////////////////////


$asoc_id = $_GET['asoc_id'];
$scara_id = $_GET['scara_id'];
$loc_id = isset($_GET['loc_id']) ? $_GET['loc_id'] : null;

$sql_data = "SELECT L.*, A.*, S.*, ST.*, ADM.nume as reprezentant, ADM.telefon FROM locatari L, scari S, asociatii A, strazi ST, admin ADM WHERE ADM.id=A.administrator_id AND ST.str_id=A.str_id AND L.asoc_id=A.asoc_id AND L.scara_id=S.scara_id AND L.scara_id=".$scara_id;
if ($loc_id) $sql_data .= ' AND L.loc_id='.$loc_id;
$sql_data = mysql_query($sql_data) or die ("Nu pot afla locuitori acestei scari<br />".mysql_error());

while ($proc = mysql_fetch_array($sql_data)) {
$locatar_id = $proc['loc_id'];

//====================================================
//======================HEADER========================
//====================================================
  if (!$PDF_newPage) 
    $ResultsPDFContent .='<pagebreak orientation="L" type="SINGLE-SIDED"><div class="FCons noBreak"';

$ResultsPDFContent .=  pdf_header('Fisa Consumuri');

$ResultsPDFContent .='
<table class="FCons DateClient" border="0" cellpadding="0" cellspacing="10" width="100%">
  <tr>
    <td colspan="3">Date identificare client<br />Nume: '.$proc['nume'].'<br />Asociatie:'.$proc['asociatie'].'<br />Adresa: Str. '.$proc['strada'].', Nr. '.$proc['nr'].', Bloc '.$proc['bloc'].', Sc. '.$proc['scara'].', ap. '.$proc['ap'].'</td>
    <td width="250">Nume responsabil administrativ:<br />'.$proc['reprezentant'].'<br />Telefon: '.$proc['telefon'].'</td>
  </tr>
</table>';


//=====================================================
//======================Content========================
//=====================================================
$apa = array (array(0 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0), array(), array(), array());
$ResultsPDFContent .='
<table class="FCons IndecsiApo" style="page-break-inside:avoid" width="1100">
<tr><td ><h2 style="float:left; padding:0; margin:0;">Tabel indecsi apometre</h2></td></tr>

<tr><td ><table width="100%" style="float:left; background-color:white;">
	<tr>
	  <td width="28%" bgcolor="#666666"  align="center">Luna</td>';
 foreach ($apa[0] as $key => $val){
	$apa[0][$key] = $month_for_tabel[$key];
	$ResultsPDFContent .= '<td bgcolor="#666666" width="6%" align="center">'.$apa[0][$key]."</td>";
}
$ResultsPDFContent .='</tr>'.tabel_indecsi_apometre($asoc_id,$scara_id,$locatar_id).'
	<tr bgcolor="#CCCCCC" style="color:black;">
	  <td colspan="13" align="center">Legenda: indecsii declarati de locatari vor fi marcati diferit de cei completati autmoat de calculator.</td>
	</tr>
</table></td></tr>
</table>
<tr><td><h2 style="float:left; padding:0; margin:0;">Tabel consum apa (mc)</h2></td></tr>

<tr><td ><table width="100%"   style="float:left; background-color:white;">
<tr>
  <td width="28%" bgcolor="#666666"  align="center">Luna</td>';
 foreach ($apa[0] as $key => $val){
	$ResultsPDFContent .= '<td bgcolor="#666666" width="6%" align="center">'.$apa[0][$key]."</td>";
}
$ResultsPDFContent .='</tr>
<tr bgcolor="#CCCCCC">
  <td>Apa Rece / ap</td>';
  foreach ($apa[0] as $key => $val){
  	$apa[1][$key] = get_consum($asoc_id,$scara_id,$locatar_id,$key,"rece");
  	$ResultsPDFContent .='<td align="center">'.$apa[1][$key]."</td>";
  }
$ResultsPDFContent .='</tr>
<tr bgcolor="#EEEEEE">
  <td>Apa Calda / ap</td>';
  foreach ($apa[0] as $key => $val){
  	$apa[2][$key] = get_consum($asoc_id,$scara_id,$locatar_id,$key,"calda");
  	$ResultsPDFContent .='<td align="center">'.$apa[2][$key]."</td>";
  }
$ResultsPDFContent .='</tr>

<tr bgcolor="#CCCCCC">
  <td>Diferente / ap</td>';

 foreach ($apa[0] as $key => $val){
  	$apa[3][$key] = getConsumServiciu($asoc_id,$scara_id,$locatar_id,$key,"Diferenta apa rece pt acm", "Diferenta apa rece");
  	$ResultsPDFContent .='<td  align="center">'.$apa[3][$key]."</td>";
  }
$ResultsPDFContent .='</tr>

<tr bgcolor="#EEEEEE">
  <td>Consum Total Apa / ap</td>';
	foreach ($apa[0] as $key => $val){
		$val = is_numeric($apa[1][$key]) ? $apa[1][$key] : 0;
		$val += is_numeric($apa[2][$key]) ? $apa[2][$key] : 0;
		$val += is_numeric($apa[3][$key]) ? $apa[3][$key] : 0;
		if (is_numeric($apa[1][$key]) || is_numeric($apa[2][$key]) || is_numeric($apa[3][$key])) {
			$ResultsPDFContent .= '<td  align="center"><b>'.$val."</b></td>";
		} else
			$ResultsPDFContent .= '<td align="center">n/a</td>';

	}
$ResultsPDFContent .='</tr>

<tr bgcolor="#CCCCCC">
  <td colspan="13">&nbsp;</td>
</tr>

<tr bgcolor="#EEEEEE">
  <td>Diferente / asociatie</td>';
   foreach ($apa[0] as $key => $val){
  	$luna_curenta = mktime(0, 0, 0, date("m")-$key, date("d"),   date("Y"));
  	$luna_curenta = date('m-Y',$luna_curenta);

  	$serv1 = "SELECT serv_id FROM servicii WHERE serviciu='Diferenta apa rece pt acm'";
  	$serv1 =  mysql_query($serv1) or die("Error #12 <br />".mysql_error());
  	$serv1 = mysql_result($serv1, 0, 'serv_id');
  	$serv2 = "SELECT serv_id FROM servicii WHERE serviciu='Diferenta apa rece'";
  	$serv2 =  mysql_query($serv2) or die("Error #13 <br />".mysql_error());
  	$serv2 = mysql_result($serv2, 0, 'serv_id');

  	$sql = "SELECT sum(`cant_fact_pers`*`pret_unitar`/`pret_unitar2`) as total FROM `fisa_indiv` WHERE `asoc_id`=".$asoc_id." AND `luna`='".$luna_curenta."' AND (`serviciu`=$serv1 OR `serviciu`=$serv2)";
	$sql = mysql_query($sql) or die ("Nu pot afla totalul diferentelor <br />".mysql_error());
	$sql = mysql_result($sql, 0, 'total');
  	$sql = $sql != null ? round($sql, 2) : 'n/a';
	$ResultsPDFContent .= '<td align="center">'.$sql."</td>";
  }
$ResultsPDFContent .='</tr>

<tr bgcolor="#CCCCCC">
  <td>Consum Delarat / asociatie</td>';
  foreach ($apa[0] as $key => $val){
   	$luna_curenta = mktime(0, 0, 0, date("m")-$key, date("d"),   date("Y"));
   	$luna_curenta = date('m-Y',$luna_curenta);

   	$serv1 = "SELECT serv_id FROM servicii WHERE serviciu='Apa rece pentru apa calda'";
   	$serv1 =  mysql_query($serv1) or die("Error #14 <br />".mysql_error());
   	$serv1 = mysql_result($serv1, 0, 'serv_id');
   	$serv2 = "SELECT serv_id FROM servicii WHERE serviciu='apa rece'";
   	$serv2 =  mysql_query($serv2) or die("Error #15 <br />".mysql_error());
   	$serv2 = mysql_result($serv2, 0, 'serv_id');

   	$sql = "SELECT sum(`cant_fact_pers`*`pret_unitar`/`pret_unitar2`) as total FROM `fisa_indiv` WHERE `asoc_id`=".$asoc_id." AND `luna`='".$luna_curenta."' AND (`serviciu`=$serv1 OR `serviciu`=$serv2)";
   	$sql = mysql_query($sql) or die ("Nu pot afla totalul diferentelor <br />".mysql_error());
   	$sql = mysql_result($sql, 0, 'total');
   	$sql = $sql != null ? round($sql, 2) : 'n/a';
   	$ResultsPDFContent .='<td align="center">'.$sql."</td>";
   }
$ResultsPDFContent .='</tr>
<tr bgcolor="#EEEEEE" style="color:black;">
  <td colspan="13" align="center">Legenda: Diferentele de apa rezultate se impart conform hotararii adunarii generale</td>
  </tr>
</table></td></tr>';



$ResultsPDFContent .= '
<tr><td ><h2 style="float:left; padding:0; margin:0;">Alte consumuri</h2></td></tr>

<tr><td ><table  width="100%"  style="float:left; background-color:white;">
<tr>
  <td width="28%" bgcolor="#666666">Luna</td>';
foreach ($apa[0] as $key => $val){
	$ResultsPDFContent .= '<td bgcolor="#666666" width="6%"  align="center">'.$apa[0][$key]."</td>";
}

$ResultsPDFContent .= '</tr>
<tr bgcolor="#CCCCCC">
  <td >Agent termic - apa calda (GKAL)</td>';
  for($i = 0; $i < 12; $i++){
  $ResultsPDFContent .= '<td  align="center">'.getConsumServiciu($asoc_id,$scara_id,$locatar_id,$i,"Agent termic pentru apa calda", "Diferenta Ag Termic pt acm").'</td>';
  }
$ResultsPDFContent .= '</tr>

<tr bgcolor="#EEEEEE">
  <td >Agent termic - incalzire (GKAL)</td>';
  for($i = 0; $i < 12; $i++){
  $ResultsPDFContent .= '<td  align="center">'.getConsumServiciu($asoc_id,$scara_id,$locatar_id,$i,"Incalzire").'</td>';
  }/*
$ResultsPDFContent .= '</tr>

<tr bgcolor="#CCCCCC">
  <td >Energie electrica spatii comune (KW)</td>';
  for($i = 0; $i < 12; $i++){
  $ResultsPDFContent .= '<td  align="center">'.getConsumServiciu($asoc_id,$scara_id,$locatar_id,$i,"Iluminat").'</td>';
  }
$ResultsPDFContent .= '</tr>

<tr bgcolor="#EEEEEE">
  <td >Gaz (mc)</td>';
  for($i = 0; $i < 12; $i++){
  $ResultsPDFContent .= '<td  align="center">'.getConsumServiciu($asoc_id,$scara_id,$locatar_id,$i,"Gaz").'</td>';
  }*/
$ResultsPDFContent .= '</tr>
</table></td></tr></table>
</div></pagebreak>';



//$PDF_newPage = false;
}