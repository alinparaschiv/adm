<?php
		$sql = "SELECT predare FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
		$query = mysql_query($sql) or die("Nu am putut afla data de afsare a listelor de plata");
		list($lu,$an) = explode("-",$_GET['luna']);
		$start = date("Y-m-d", mktime(0, 0, 0, $lu-1, mysql_result($query,0,'predare'),  $an));
		$end = date("Y-m-d", mktime(0, 0, 0, $lu, mysql_result($query,0,'predare'),  $an));


$ResultsPDFContent .='

<htmlpageheader name="urb_reg_jur">
	<table style="margin-bottom:0px" border="0">
        <tr>
            <td bordercolor="white"><img src="sigla.jpg" width="170"></td>
            <td bordercolor="white" width="700">
                <center>
                    <div><h2>Registru jurnal</h2><br /><h3>'.Util::get_asoc_name($asoc_id).' <br />'.$start.' - '.$end.'</h3> </div>
                </center>
            </td>
            <td bordercolor="white" align="right">' . $adresaOfficeUrbica . '</td>
        </tr>
    </table>
</htmlpageheader>
<sethtmlpageheader name="urb_reg_jur" page="ALL" value="1" show-this-page="0"/>
<pagebreak margin-top="100" />
';

$ResultsPDFContent .='
<table width="1100" border="0" cellpadding="1" >
<thead>
  <tr bgcolor="#666666">
    <td rowspan="2" align="center" valign="middle" color="#FFFFFF" width="20"><strong>Nr. Crt.</strong></td>
    <td rowspan="2" align="center" valign="middle" color="#FFFFFF"><strong>Data Inregistrari</strong></td>
    <td rowspan="2" align="center" valign="middle" color="#FFFFFF"><strong>Documentul</strong></td>
    <td rowspan="2" align="center" valign="middle" color="#FFFFFF"><strong>Explicatii</strong></td>
    <td colspan="2" align="center" valign="middle" color="#FFFFFF"><strong>Suma</strong></td>
    <td colspan="2" align="center" valign="middle" color="#FFFFFF"><strong>Penalizari</strong></td>
  </tr>
  <tr bgcolor="#666666">
    <td align="center" valign="middle" color="#FFFFFF"><strong>Debit</strong></td>
    <td align="center" valign="middle" color="#FFFFFF"><strong>Credit</strong></td>
    <td align="center" valign="middle" color="#FFFFFF"><strong>Debit</strong></td>
    <td align="center" valign="middle" color="#FFFFFF"><strong>Credit</strong></td>
  </tr>
</thead>';
  
$sql_total ="SELECT tip, sum(valoare) as valoare, sum(penalizare) as penalizare FROM (
SELECT fisa.`asoc_id`, fisa.data_inreg, fisa.document, fisa.valoare, fisa.penalizare, 
0 as tip, f.furnizor as nume, 'plata' as explicatie
FROM `fisa_furnizori` fisa, furnizori f
WHERE explicatii='Ordin Plata' AND fisa.fur_id=f.fur_id AND fisa.asoc_id=".$_GET['asoc_id']." AND fisa.data_inreg < '".$start."'
UNION
SELECT C.asoc_id, C.data_inserarii as data_inreg, concat(C.chitanta_serie,'/',C.chitanta_nr) as document,
CASE C.reprezentand WHEN 'Plata Penalizare' THEN 0 ELSE C.suma END as valoare,
CASE C.reprezentand WHEN 'Plata Penalizare' THEN C.suma ELSE 0 END as penalizare,
1 as tip, L.nume, C.reprezentand
FROM `casierie` C, locatari L
WHERE C.loc_id=L.loc_id AND C.asoc_id=".$_GET['asoc_id']." AND C.data_inserarii < '".$start."') as X GROUP BY tip order by tip asc";


$query_total = mysql_query($sql_total) or die("Nu pot extrage totalul pentru registru jurnal<br />".$sql_total."<br />".mysql_error());
$i = 2;
if(mysql_num_rows($query_total) > 0) {
$data = array((mysql_result($query_total, 0, 'valoare') * -1),
			  (mysql_result($query_total, 0, 'penalizare') * -1),
			  (mysql_result($query_total, 1, 'valoare')),
			  (mysql_result($query_total, 1, 'penalizare'))
			  );
} else {$data = array(0,0,0,0);} 
  
$ResultsPDFContent .='<tr>
	<td>1</td>
	<td colspan="2">&nbsp;</td>
	<td><strong>sume reportate: </strong></td>
    <td><strong>'.$data[0].'</strong></td>
    <td><strong>'.$data[2].'</strong></td>
    <td><strong>'.$data[1].'</strong></td>
    <td><strong>'.$data[3].'</strong></td>
  </tr>';
  
$sql ="SELECT fisa.`asoc_id`, fisa.data_inreg, fisa.document, fisa.valoare, fisa.penalizare, 
0 as tip, f.furnizor as nume, 'plata' as explicatie
FROM `fisa_furnizori` fisa, furnizori f
WHERE explicatii='Ordin Plata' AND fisa.fur_id=f.fur_id AND 
fisa.asoc_id=".$_GET['asoc_id']." AND
fisa.data_inreg between '".$start."' AND '".$end."'
 UNION 
SELECT C.asoc_id, C.data_inserarii as data_inreg, concat(C.chitanta_serie,'/',C.chitanta_nr) as document,
CASE C.reprezentand WHEN 'Plata Penalizare' THEN 0 ELSE C.suma END as valoare,
CASE C.reprezentand WHEN 'Plata Penalizare' THEN C.suma ELSE 0 END as penalizare,
1 as tip, L.nume, C.reprezentand
FROM `casierie` C, locatari L
WHERE C.loc_id=L.loc_id AND
C.asoc_id=".$_GET['asoc_id']." AND
C.data_inserarii between '".$start."' AND '".$end."'";

$query = mysql_query($sql) or die("Nu pot extrage inregistrarile pentru registru jurnal<br />".$sql."<br />".mysql_error());

while($row = mysql_fetch_assoc($query)) {
if ($row['tip']==0){
	$data[0] += $row['valoare'] * -1;
	$data[1] += $row['penalizare'] * -1;
} else {
	$data[2] += $row['valoare'];
	$data[3] += $row['penalizare'];
}

$ResultsPDFContent .= '<tr bgcolor="#'.(($i%2)==0 ? 'CCCCCC' : 'EEEEEE').'">
    <td>'.$i++.'</td>
    <td>'.$row['data_inreg'].'</td>
    <td>'.(($row['tip']==0) ? $row['document'] : ('chitanta '.$row['document'])).', '.$row['data_inreg'].'</td>
    <td>'.$row['explicatie'].' '.$row['nume'].'</td>
    <td>'.($row['tip']==0 ? ($row['valoare']==0 ? '&nbsp;' : (-1 * $row['valoare'])) : '&nbsp;').'</td>
    <td>'.($row['tip']==1 ? ($row['valoare']==0 ? '&nbsp;' : $row['valoare']) : '&nbsp;').'</td>
    <td>'.($row['tip']==0 ? ($row['penalizare']==0 ? '&nbsp;' : (-1 * $row['penalizare'])) : '&nbsp;').'</td>
    <td>'.($row['tip']==1 ? ($row['penalizare']==0 ? '&nbsp;' : $row['penalizare']) : '&nbsp;').'</td>
  </tr>';
}

$ResultsPDFContent .='<tr><td colspan="4" align="right"><strong>Total:</strong></td>
    <td><strong>'.$data[0].'</strong></td>
    <td><strong>'.$data[2].'</strong></td>
    <td><strong>'.$data[1].'</strong></td>
    <td><strong>'.$data[3].'</strong></td>
  </tr>';
  
$ResultsPDFContent .='</table><pagebreak margin-top="100" />';