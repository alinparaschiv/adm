<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        var strArr=new Array();
</script>

<?php


$asocId = $_GET['asoc_id'];
$i = 0;
$query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
$result = mysql_query($query) or die('Nu pot selecta locatarii asociatiei curente'.mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$id = $row['loc_id'];
	if (isset($_POST['F_rul_i_'.$id]) && $_POST['F_rul_i_'.$id] == '') $i = 1;
	if (isset($_POST['F_rul_r_'.$id]) && $_POST['F_rul_r_'.$id] == '') $i = 1;
	if (isset($_POST['F_rep_i_'.$id]) && $_POST['F_rep_i_'.$id] == '') $i = 1;
	if (isset($_POST['F_rep_r_'.$id]) && $_POST['F_rep_r_'.$id] == '') $i = 1;
	if (isset($_POST['F_spe_i_'.$id]) && $_POST['F_spe_i_'.$id] == '') $i = 1;
	if (isset($_POST['F_spe_r_'.$id]) && $_POST['F_spe_r_'.$id] == '') $i = 1;
}

if (isset($_POST['buton']) && $_POST['buton'] == 'apasat' && $i == 0) {

	$query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
	$result = mysql_query($query) or die('Nu pot selecta locatarii din asociatia curenta'.mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$id = $row['loc_id'];
		/*
		$q = "SELECT * FROM `fisa_fonduri` WHERE `loc_id`='$id'";
		$res = mysql_query($q) or die('<br><br>Nu pot afla daca a fost introdus deja protocolu pentru locataru curent'.mysql_error());
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
			$i=1;
			$locErr .= (isset($locErr) ? $locErr : '' ). 'Informatiile pentru locatarul "'.$row['nume'].'" au fost deja introduse.<br>';
		}
		*/
		if(ereg('[^0-9.-]', $_POST['F_rul_i_'.$id]) || ereg('[^0-9.-]', $_POST['F_rul_r_'.$id]) ||
		   ereg('[^0-9.-]', $_POST['F_rep_i_'.$id]) || ereg('[^0-9.-]', $_POST['F_rep_r_'.$id]) ||
		   ereg('[^0-9.-]', $_POST['F_spe_i_'.$id]) || ereg('[^0-9.-]', $_POST['F_spe_r_'.$id])) {
			$i=1;
			$locErr .= (isset($locErr) ? $locErr : '' ).'Toate campurile sunt numerice, verificati informatiile introduse pentru locatarul "'.$row['nume'].'"<br>';
		}

	}

	if ($i==0) {
	if(strcmp($_POST['operatie'],"insert") == 0)
		{
		$query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
		$result = mysql_query($query) or die('Nu pot selecta locatarii din asociatia curenta'.mysql_error());

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$id = $row['loc_id'];

			$luna = date("m-Y");
			$asocId = $row['asoc_id'];
			$scaraId = $row['scara_id'];
			$locId = $row['loc_id'];

			$fondRulIncasat = $_POST['F_rul_i_'.$id];
			$fondRepIncasat = $_POST['F_rep_i_'.$id];
			$fondSpecIncasat =$_POST['F_spe_i_'.$id];

			$fondRulRest = $_POST['F_rul_r_'.$id];
			$fondRepRest = $_POST['F_rep_r_'.$id];
			$fondSpecRest =$_POST['F_spe_r_'.$id];

			$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', '$locId', '$fondRulIncasat', '$fondRulRest', '$fondRepIncasat', '$fondRepRest', '$fondSpecIncasat', '$fondSpecRest', 0, 0, 0, 0)";
			$insert = mysql_query($insert) or die ("Nu pot insera fondul initial<br />".mysql_error());
		}
		$mesaj = '<font color="green">Datele au fost introduse.</font>';
		unset ($_POST);
		} else
		if(strcmp($_POST['operatie'],"update") == 0)
		{
			$query = "SELECT L.*,  F.`fond_rul_incasat` rul_i, F.`fond_rul_rest` rul_r, F.`fond_rep_incasat` rep_i, F.`fond_rep_rest` rep_r, F.`fond_spec_incasat` spe_i, F.`fond_spec_rest` spe_r  FROM locatari L, fisa_fonduri F WHERE L.loc_id=F.loc_id AND L.asoc_id='$asocId'";
			$result = mysql_query($query) or die('Nu pot selecta locatarii din asociatia curenta <br />'.$query.'<br />'.mysql_error());

			while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$id =$row['loc_id'];

				$luna = date("m-Y");
				$asocId = $row['asoc_id'];
				$scaraId = $row['scara_id'];
				$locId = $row['loc_id'];

				if(($_POST['F_rul_i_'.$id] != $row['rul_i']) ||
				   ($_POST['F_rul_r_'.$id] != $row['rul_r']) ||
			  	   ($_POST['F_rep_i_'.$id] != $row['rep_i']) ||
				   ($_POST['F_rep_r_'.$id] != $row['rep_r']) ||
				   ($_POST['F_spe_i_'.$id] != $row['spe_i']) ||
				   ($_POST['F_spe_r_'.$id] != $row['spe_r'])) { //verific daca e vre-o diferenta ca sa merite facut update-ul

					$fondRulIncasat = $_POST['F_rul_i_'.$id];
					$fondRepIncasat = $_POST['F_rep_i_'.$id];
					$fondSpecIncasat =$_POST['F_spe_i_'.$id];

					$fondRulRest = $_POST['F_rul_r_'.$id];
					$fondRepRest = $_POST['F_rep_r_'.$id];
					$fondSpecRest =$_POST['F_spe_r_'.$id];

					$sql = "UPDATE fisa_fonduri SET fond_rul_incasat=".$fondRulIncasat.", fond_rul_rest=".$fondRulRest.", fond_rep_incasat=".$fondRepIncasat.", fond_rep_rest=".$fondRepRest.", fond_spec_incasat=".$fondSpecIncasat.", fond_spec_rest=".$fondSpecRest." WHERE loc_id=".$locId;
					$sql = mysql_query($sql) or die ("Nu pot actualiza datele<br />". mysql_error());
				}
			}
			$mesaj = '<font color="green">Datele au fost actualizate.</font>';
			unset ($_POST);
		}
	} else {
		$mesaj = isset($locErr) ? $locErr : "";
		$mesaj .= '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
	}
}
else if (isset($_POST['buton']) && $_POST['buton'] == 'apasat') {
	$mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}

$query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
$result = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	echo $row['asociatie'];
}

$m = 0;
$query = "SELECT L.*, S.scara, F.`fond_rul_incasat` rul_i, F.`fond_rul_rest` rul_r, F.`fond_rep_incasat` rep_i, F.`fond_rep_rest` rep_r, F.`fond_spec_incasat` spe_i, F.`fond_spec_rest` spe_r FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id, fisa_fonduri F WHERE F.loc_id=L.loc_id AND L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
$result = mysql_query($query) or die("Eroare: <br />".$query."<br />".mysql_query());
$operatie = 'update';
if (mysql_num_rows($result) == 0) {
	$query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
	$result = mysql_query($query) or die(mysql_query());
	$operatie = 'insert';
}
?>
<div id="mainCol" class="clearfix" align="left"><div id="maincon" style="width:600px;">
<form id="addForm" name="addForm" method="post" action="index.php?link=w_locatari_fond&asoc_id=<?php echo $asocId ?>">
<input type="hidden" name="buton" value="apasat" />
<input type="hidden" name="operatie" value="<?php echo $operatie ?>" />
Asociatia:
<?php
$scara = '';
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row['scara'] != $scara) {
		if ($scara != '') echo '</table>';
		$scara = $row['scara'];
		echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:700px;" border=0>
		             <tr bgcolor="#19AF62"><td><font size=2 color="white"><center>Etaj</center></font></td><td><font size=2 color="white"><center>Scara</center></font></td><td><font size=2 color="white"><center>Apartament</center></font></td><td><font size=2 color="white"><center>Nume</center></font></td>
		             <td><font size=2 color="white"><center>Fond Rulment Incasat</center></font></td>
		             <td><font size=2 color="white"><center>Fond Rulment Restanta</center></font></td>
		             <td><font size=2 color="white"><center>Fond Reparatii Incasat</center></font></td>
		             <td><font size=2 color="white"><center>Fond Reparatii Restanta</center></font></td>
		             <td><font size=2 color="white"><center>Fond Special Incasat</center></font></td>
		             <td><font size=2 color="white"><center>Fond Special Restanta</center></font></td>';
	}
	$locId = $row['loc_id'];
	if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
	echo '<td ><center>'.$row['etaj'].'</center></td>';
	echo '<td ><center>'.$row['scara'].'</center></td>';
	echo '<td ><center>ap '.$row['ap'].'</center></td>';
	echo '<td width=100><center>'.$row['nume'].'</center></td>';
	echo '<td><input style="width:60px;" type="text" name="F_rul_i_'.$locId.'" value="'.(isset($_POST['F_rul_i_'.$locId]) ? $_POST['F_rul_i_'.$locId] : (isset($row['rul_i']) ? $row['rul_i'] : '')).'"></td>';
	echo '<td><input style="width:60px;" type="text" name="F_rul_r_'.$locId.'" value="'.(isset($_POST['F_rul_r_'.$locId]) ? $_POST['F_rul_r_'.$locId] : (isset($row['rul_r']) ? $row['rul_r'] : '')).'"></td>';
	echo '<td><input style="width:60px;" type="text" name="F_rep_i_'.$locId.'" value="'.(isset($_POST['F_rep_i_'.$locId]) ? $_POST['F_rep_i_'.$locId] : (isset($row['rep_i']) ? $row['rep_i'] : '')).'"></td>';
	echo '<td><input style="width:60px;" type="text" name="F_rep_r_'.$locId.'" value="'.(isset($_POST['F_rep_r_'.$locId]) ? $_POST['F_rep_r_'.$locId] : (isset($row['rep_r']) ? $row['rep_r'] : '')).'"></td>';
	echo '<td><input style="width:60px;" type="text" name="F_spe_i_'.$locId.'" value="'.(isset($_POST['F_spe_i_'.$locId]) ? $_POST['F_spe_i_'.$locId] : (isset($row['spe_i']) ? $row['spe_i'] : '')).'"></td>';
	echo '<td><input style="width:60px;" type="text" name="F_spe_r_'.$locId.'" value="'.(isset($_POST['F_spe_r_'.$locId]) ? $_POST['F_spe_r_'.$locId] : (isset($row['spe_r']) ? $row['spe_r'] : '')).'"></td>';

	$m++;
}
echo '</table>';
?>

<table cellspacing=1 style="margin:20px 0 0 0px; width:600px;" border=0>
	<tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
	<?php if (isset($mesaj)) echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
</table>
<table><tr>
	<td><div id="butonBack"><a href="index.php?link=w_locatari_dat&asoc_id=<?php echo $asocId ?>" style="">Pasul Anterior</a></div></td>
	<td><div id="buton"><a href="index.php" style="">Gata</a></div></td>
</tr></table>
</div>