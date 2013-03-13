<script type="text/javascript">
function select_change() {

	if(document.getElementById("startDate") == null) {
	window.location = "index.php?link=reg_jurn"+
	"&asoc_id="+document.getElementById("change_asociatie").value+
	"&operatie="+document.getElementById("change_plata").value;
	} else {
	window.location = "index.php?link=reg_jurn"+
	"&asoc_id="+document.getElementById("change_asociatie").value+
	"&operatie="+document.getElementById("change_plata").value+
	"&start="+document.getElementById("startDate").value+
	"&end="+document.getElementById("endDate").value;}
}
</script>
<?php

$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die("Nu pot selecta asociatiile pt afisarea lor in lista asociatiilor<br />".mysql_error());
$asociatii = '';
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option ';
	if(isset($_GET['asoc_id']) && $row[0] == $_GET['asoc_id']) $asociatii .= 'selected="yes" ';
	$asociatii .= 'value="'.$row[0].'">'.$row[1].'</option>';
}

if (isset($_GET['asoc_id'])) {
	if ((!isset($_GET['start'])) || (!isset($_GET['end']))) {
		$sql = "SELECT predare FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
		$query = mysql_query($sql) or die("Nu am putut afla data de afsare a listelor de plata");
		
		$_GET['start'] = date("d-m-Y", mktime(0, 0, 0, (mysql_result($query,0,'predare')>date('d') ? date("n")-1 : date("n")), mysql_result($query,0,'predare'),  date("Y")));
		$_GET['end'] = date("d-m-Y", mktime(0, 0, 0, (mysql_result($query,0,'predare')>date('d') ? date("n") : date("n")+1), mysql_result($query,0,'predare')-1,  date("Y")));
	}
}

?>
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Alegeti Asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_asociatie" onChange="select_change()">
				<?php if (!isset($_GET['asoc_id'])) { echo '<option value="all">Alege</option>'; } ?>
        <?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Operatie:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_plata" onChange="select_change()">
				<option value="0" <?php if(!isset($_GET['operatie']) || $_GET['operatie'] == '0') echo 'selected="selected"'; ?> >Toate</option>
				<option value="2" <?php if(isset($_GET['operatie']) && $_GET['operatie'] == '2') echo 'selected="selected"'; ?> >Plati</option>
				<option value="1" <?php if(isset($_GET['operatie']) && $_GET['operatie'] == '1') echo 'selected="selected"'; ?> >Incasari</option>
			</select>
		</td>
	</tr>
	<?php if (isset($_GET['asoc_id'])) : ?>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Data de inceput:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<input type="text" name="startDate" id="startDate" class="datepicker" onChange="select_change()" value="<?php echo $_GET['start']; ?>"/>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Data de sfarsit:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<input type="text" name="endDate" id="endDate" class="datepicker" onChange="select_change()" value="<?php echo $_GET['end']; ?>"/>
		</td>
	</tr>
	<?php endif ?>
</table>
<?php
if (isset($_GET['asoc_id'])) {

list($zi,$lu,$an) = explode("-", $_GET['start']);
$start = $an."-".$lu."-".$zi;
list($zi,$lu,$an) = explode("-", $_GET['end']);
$end = $an."-".$lu."-".$zi;

$sql = '';
if ($_GET['operatie'] % 2 == 0)
	$sql .="SELECT fisa.`asoc_id`, fisa.data_inreg, fisa.document, round(fisa.valoare, 2) as valoare, round(fisa.penalizare, 2) as penalizare, 
0 as tip, f.furnizor as nume, 'plata' as explicatie
FROM `fisa_furnizori` fisa, furnizori f
WHERE explicatii='Ordin Plata' AND fisa.fur_id=f.fur_id AND 
fisa.asoc_id=".$_GET['asoc_id']." AND
fisa.data_inreg between '".$start."' AND '".$end."'
";
if ($_GET['operatie'] == 0)
	$sql .= " UNION ALL";
if ($_GET['operatie'] <= 1)
	$sql .= "
SELECT C.asoc_id, C.data_inserarii as data_inreg, concat(C.chitanta_serie,'/',C.chitanta_nr) as document,
CASE C.tip_plata WHEN 'Penalizare' THEN 0 ELSE round(C.suma, 2) END as valoare,
CASE C.tip_plata WHEN 'Penalizare' THEN round(C.suma, 2) ELSE 0 END as penalizare,
1 as tip, L.nume, C.reprezentand
FROM `casierie` C
LEFT OUTER JOIN locatari L ON C.loc_id=L.loc_id
WHERE  
C.asoc_id=".$_GET['asoc_id']." AND
C.data_inserarii between '".$start."' AND '".$end."'";
$query = mysql_query($sql) or die("Nu pot extrage inregistrarile pentru registru jurnal<br />".$sql."<br />".mysql_error());


echo '<strong>REGISTRU JURNAL <br />';
echo $_GET['start']." / ".$_GET['end']."</strong>";
?>

<table width="700" border="0" cellpadding="0">
  <tr bgcolor="#19AF62">
    <td rowspan="2" align="center" valign="middle"><strong>Nr. Crt.</strong></td>
    <td rowspan="2" align="center" valign="middle"><strong>Data Inregistrari</strong></td>
    <td rowspan="2" align="center" valign="middle"><strong>Documentul</strong></td>
    <td rowspan="2" align="center" valign="middle"><strong>Explicatii</strong></td>
    <td colspan="2" align="center" valign="middle"><strong>Suma</strong></td>
    <td colspan="2" align="center" valign="middle"><strong>Penalizari</strong></td>
  </tr>
  <tr bgcolor="#19AF62">
    <td align="center" valign="middle"><strong>Debit</strong></td>
    <td align="center" valign="middle"><strong>Credit</strong></td>
    <td align="center" valign="middle"><strong>Debit</strong></td>
    <td align="center" valign="middle"><strong>Credit</strong></td>
  </tr>
<?php
$sql_total ="SELECT tip, sum(valoare) as valoare, sum(penalizare) as penalizare FROM (
SELECT 0 as asoc_id, 0 as data_inreg, 0 as document, 0 as valoare, 0 as penalizare, 0 as tip, 0 as nume, 0 as explicatie
UNION ALL
SELECT 0 as asoc_id, 0 as data_inreg, 0 as document, 0 as valoare, 0 as penalizare, 1 as tip, 0 as nume, 0 as explicatie
UNION ALL
SELECT fisa.`asoc_id`, fisa.data_inreg, fisa.document, fisa.valoare, fisa.penalizare, 
0 as tip, f.furnizor as nume, 'plata' as explicatie
FROM `fisa_furnizori` fisa, furnizori f
WHERE explicatii='Ordin Plata' AND fisa.fur_id=f.fur_id AND fisa.asoc_id=".$_GET['asoc_id']." AND fisa.data_inreg < '".$start."'
UNION ALL
SELECT C.asoc_id, C.data_inserarii as data_inreg, concat(C.chitanta_serie,'/',C.chitanta_nr) as document,
CASE C.tip_plata WHEN 'Penalizare' THEN 0 ELSE round(C.suma, 2) END as valoare,
CASE C.tip_plata WHEN 'Penalizare' THEN round(C.suma, 2) ELSE 0 END as penalizare,
1 as tip, L.nume, C.reprezentand
FROM `casierie` C
LEFT OUTER JOIN locatari L ON C.loc_id=L.loc_id
WHERE C.asoc_id=".$_GET['asoc_id']." AND C.data_inserarii < '".$start."'
UNION ALL
SELECT asoc_id, 0 as data_inreg, 0 as document, suma as valoare, 0 as penalizare, 1 as tip, 0 as nume, 0 as explicatie
FROM casierie
WHERE asoc_id=".$_GET['asoc_id']." AND scara_id IS NULL AND loc_id IS NULL) as X GROUP BY tip order by tip asc";

$query_total = mysql_query($sql_total) or die("Nu pot extrage totalul pentru registru jurnal<br />".$sql_total."<br />".mysql_error());
$i = 2;
if(mysql_num_rows($query_total) > 0) {
$data = array((mysql_result($query_total, 0, 'valoare') * -1),
			  (mysql_result($query_total, 0, 'penalizare') * -1),
			  (mysql_result($query_total, 1, 'valoare')),
			  (mysql_result($query_total, 1, 'penalizare'))
			  );
} else {$data = array(0,0,0,0);}
// echo '<td colspan="4" align="right">sume reportate</td>
//     <td><strong>'.$data[0].'</strong></td>
//     <td><strong>'.$data[2].'</strong></td>
//     <td><strong>'.$data[1].'</strong></td>
//     <td><strong>'.$data[3].'</strong></td>
//   </tr>';
echo '<td colspan="4" align="right"><strong>Total:</strong></td>
<td colspan="2"><strong>'.($data[2]-$data[0]).'</strong></td>
<td colspan="2"><strong>'.($data[3]-$data[1]).'</strong></td>
</tr>';

while($row = mysql_fetch_assoc($query)) {
if ($row['tip']==0){
	$data[0] += $row['valoare'] * -1;
	$data[1] += $row['penalizare'] * -1;
} else {
	$data[2] += $row['valoare'];
	$data[3] += $row['penalizare'];
}

echo '<tr bgcolor="'.(($i%2)==0 ? 'CCCCCC' : 'EEEEEE').'">
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

echo '<td colspan="4" align="right"><strong>Total:</strong></td>
    <td colspan="2"><strong>'.($data[2]-$data[0]).'</strong></td>
    <td colspan="2"><strong>'.($data[3]-$data[1]).'</strong></td>
  </tr>';
?>
 </table>
 <?php } ?>