<?php
		$sql = "SELECT predare FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
		$query = mysql_query($sql) or die("Nu am putut afla data de afsare a listelor de plata");
		list($lu,$an) = explode("-",$_GET['luna']);
		$start = date("Y-m-d", mktime(0, 0, 0, $lu-1, mysql_result($query,0,'predare'),  $an));
		$end = date("Y-m-d", mktime(0, 0, 0, $lu, mysql_result($query,0,'predare')-1,  $an));

$ResultsPDFContent .='

<htmlpageheader name="urb_fond_rulment">
	<table style="margin-bottom:0px" border="0">
        <tr>
            <td bordercolor="white"><img src="sigla.jpg" width="170"></td>
            <td bordercolor="white" width="700">
                <center>
                    <div><h2>Registru Fond Rulment</h2><br /><h3>'.Util::get_asoc_name($asoc_id).' <br />'.$start.' - '.$end.'</h3> </div>
                </center>
            </td>
            <td bordercolor="white" align="right">' . $adresaOfficeUrbica . '</td>
        </tr>
    </table>
</htmlpageheader>
<sethtmlpageheader name="urb_fond_rulment" page="ALL" value="1" show-this-page="1"/>
';

$ResultsPDFContent .='
<table width="1100" border="0" cellpadding="1">
  <thead>
  <tr bgcolor="#666666">
    <td color="#FFFFFF" width="50" align="center"><strong>Nr. Crt.</strong></td>
    <td color="#FFFFFF" align="center"><strong>Documentul, Data</strong></td>
    <td color="#FFFFFF" align="center"><strong>Explicatie</strong></td>
    <td color="#FFFFFF" align="center"><strong>Suma stabilita</strong></td>
    <td color="#FFFFFF" align="center"><strong>Suma incasata</strong></td>
    <td color="#FFFFFF" align="center"><strong>Suna restituita</strong></td>
  </tr>
  </thead>';
  
  $sql_incasat = "SELECT sum(fond_rul_incasat) as incasat
FROM fisa_fonduri WHERE asoc_id=".$_GET['asoc_id']." AND STR_TO_DATE(data, '%m-%Y') < STR_TO_DATE('".$_GET['luna']."', '%m-%Y')";
  $qery_incasat = mysql_query($sql_incasat) or die("Nu am putut afla suma raportata incasata<br />".$sql_incasat."<br />".mysql_error());
  
  $sql_restanta = "SELECT sum(fond_rul_rest) as restanta
					FROM fisa_fonduri 
					WHERE asoc_id=".$_GET['asoc_id']." AND
					STR_TO_DATE(data, '%m-%Y') < STR_TO_DATE('".$_GET['luna']."', '%m-%Y')
					GROUP BY data ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
  $qery_restanta = mysql_query($sql_restanta) or die("Nu am putut afla suma restanta<br />".$sql_restanta."<br />".mysql_error());
  
  
  
  
  $total = array(mysql_result($qery_restanta,0,'restanta')+mysql_result($qery_incasat,0,'incasat'),
				 mysql_result($qery_incasat,0,'incasat'));
  
  
  $sume_initiale = array($total[0], $total[1]);
  
  $sql_loc = "SELECT L.loc_id, L.nume, F.fond_rul_incasat as i, F.fond_rul_rest as r
			FROM locatari L LEFT OUTER JOIN (SELECT * FROM fisa_fonduri WHERE asoc_id=".$_GET['asoc_id']." AND data='".$_GET['luna']."') F ON L.loc_id=F.loc_id
			WHERE L.asoc_id=".$_GET['asoc_id']." ORDER BY L.loc_id";
  $query_loc = mysql_query($sql_loc) or die("Nu am putut selecta locatari si fondurile de pe luna curenta<br />".$sql_loc."<br />".mysql_error());
  $i = 2;
  
	$sql = "SELECT predare FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
	$query = mysql_query($sql) or die("Nu am putut afla data de afsare a listelor de plata");
	list($lu,$an) = explode("-",$_GET['luna']);
	$start = date("Y-m-d", mktime(0, 0, 0, $lu-1, mysql_result($query,0,'predare'),  $an));
	$end = date("Y-m-d", mktime(0, 0, 0, $lu, mysql_result($query,0,'predare'),  $an));
	
  $color = 0;
  
  $data = '';
while($row = mysql_fetch_assoc($query_loc)) {
	
	$color = ($color+1) % 2;
 
	 $suma_stabilita='XXX';
	 if($row['i'] == null)
		$suma_stabilita = 0;
	 else {
		$sql_last = "SELECT fond_rul_incasat as i, fond_rul_rest as r FROM fisa_fonduri WHERE loc_id=".$row['loc_id']." AND
					STR_TO_DATE(data, '%m-%Y') < STR_TO_DATE('".$_GET['luna']."', '%m-%Y')
					ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
		$query_last = mysql_query($sql_last) or die("Nu pot afla penultima inregistrare din fisa fonduri pt locatarul curent<br />".$sql_last."<br />".mysql_error());
		
		$suma_stabilita = $row['r'] + $row['i'] - mysql_result($query_last,0,'r');
	 }
	 
	 $sql_chitante = "SELECT date_format(data_inserarii, '%d.%m.%Y') as data, concat(C.chitanta_serie,'/',C.chitanta_nr) as document, suma
					FROM `casierie` C WHERE loc_id=".$row['loc_id']." AND tip_plata='Fond Rulment' AND
					C.data_inserarii between '$start' AND '$end'";
	 $qery_chitante = mysql_query($sql_chitante) or die ("NU am putut afla ce chitante are locatarul curent pentru fondul rulment <br />".$sql_chitante."<br />".mysql_error());
	 
	 $total[0] += $suma_stabilita;
	 
	 if(mysql_num_rows($qery_chitante) == 0)
		$data .=' 
		  <tr bgcolor="#'.($color==0 ? 'CCCCCC' : 'EEEEEE').'">
			<td>'.$i++.'</td>
			<td>Lista Plata '.$_GET['luna'].'</td>
			<td>'.$row['nume'].'</td>
			<td>'.$suma_stabilita.'</td>
			<td>0</td>
			<td>&nbsp;</td>
		  </tr>';
	else {
		while($chitanta = mysql_fetch_assoc($qery_chitante)) {
			$data_document = explode( '.', $chitanta['data']);
			$luna_curenta = explode('-', $_GET['luna']);
		
			if($luna_curenta[0] != $data_document[1]) 
				$sume_initiale[1] -= $chitanta['suma'];
			 else
				$total[1] += $chitanta['suma'];
				
			$data .=' 
			  <tr bgcolor="#'.($color==0 ? 'CCCCCC' : 'EEEEEE').'">
				<td>'.$i++.'</td>
				<td>Chitanta '.$chitanta['document'].' din '.$chitanta['data'].'</td>
				<td>Plata '.$row['nume'].'</td>
				<td>'.$suma_stabilita.'</td>
				<td>'.$chitanta['suma'].'</td>
				<td>&nbsp;</td>
			  </tr>';
		}
	}
	
}

$ResultsPDFContent .='
  <tr bgcolor="#CCCCCC">
    <td>1</td>
    <td>sume reportate</td>
    <td>sold casa</td>
    <td>'.$sume_initiale[0].'</td>
    <td>'.$sume_initiale[1].'</td>
    <td></td>
  </tr>';

$ResultsPDFContent .= $data;
  
$ResultsPDFContent .=' 
  <tr>
    <td colspan="3" align="right"><strong>Sold Final:</strong></td>
	<td align="left"><strong>'.$total[0].'</strong></td>
    <td colspan="2" align="left"><strong>'.$total[1].'</strong></td>
  </tr>
</table>';
$ResultsPDFContent .='</table><pagebreak margin-top="100" />';