<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=istoric_plati&asoc_id=" + value;
}

function select_scara(value,value2) {
 window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2;
}

function select_locatar(value,value2,value3) {
 window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3;
}

function deLa(value,value2,value3,value4){
	if ((value == "") && (value2 == "") && (value3 == "") && (value4 != "")){
		window.location = "index.php?link=istoric_plati&deLa=" + value4;
	} else
	if ((value != "") && (value2 =="") && (value3 == "") && (value4 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&deLa=" + value4;
	} else
	if ((value != "") && (value2 != "") && (value3 == "") && (value4 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2 + "&deLa=" + value4;
	} else
	if ((value != "") && (value2 != "") && (value3 != "") && (value4 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3 + "&deLa=" + value4;
	}
}

function panaLa(value,value2,value3,value4,value5){
	if ((value == "") && (value2 == "") && (value3 == "") && (value4 != "") && (value5 != "")){
		window.location = "index.php?link=istoric_plati&deLa=" + value4 + "&panaLa=" + value5;
	} else
	if ((value != "") && (value2 =="") && (value3 == "") && (value4 != "") && (value5 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&deLa=" + value4 + "&panaLa=" + value5;
	} else
	if ((value != "") && (value2 != "") && (value3 == "") && (value4 != "") && (value5 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2 + "&deLa=" + value4 + "&panaLa=" + value5;
	} else
	if ((value != "") && (value2 != "") && (value3 != "") && (value4 != "") && (value5 != "")){
		window.location = "index.php?link=istoric_plati&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3 + "&deLa=" + value4 + "&panaLa=" + value5;
	}
}

</script>
<?php
if ($_POST['buton'] == 'apasat')
{
	echo "O sa anulam chitanta";
}

function total_incasari($asoc_id, $scara_id, $loc_id, $user_id, $pozitie){
	$suma = 0;

	$deLa = $_GET['deLa'];
	$panaLa = $_GET['panaLa'];
	if($deLa<>null && $panaLa==null) {  $extra = " AND data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra = " AND data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if($deLa<>null && $panaLa==null) {  $extra1 = " WHERE data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra1 = " WHERE data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if(($_GET['asoc_id']==null) && ($_GET['scara_id']==null) && ($_GET['locatar']==null))
	{
		if ($user_id == 0)
		{
			$sql = "SELECT * FROM casierie".$extra1;
		}
		else
		{
			$sql = "SELECT * FROM casierie WHERE casier_id=".$pozitie.$extra;
		}
		$sql = mysql_query($sql) or die("Nu pot afisa informatiile generale <br />".mysql_error());

		while ($row = mysql_fetch_array($sql)){
			$suma += $row['suma'];
		}
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']==null) && ($_GET['locatar']==null))
	{
		if ($user_id == 0)
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id.$extra;
		}
		else
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND casier_id=".$pozitie.$extra;
		}
		$sql = mysql_query($sql) or die ("Nu pot afisa informatiile pentru asociatie <br />".mysql_error());

		while ($row = mysql_fetch_array($sql)){
			$suma += $row['suma'];
		}
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']!=null) && ($_GET['locatar']==null))
	{
		if ($user_id == 0)
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id.$extra;
		}
		else
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND casier_id=".$pozitie.$extra;
		}
		$sql = mysql_query($sql) or die("Nu pot afisa informatiile pentru scara <br />".mysql_error());

		while ($row = mysql_fetch_array($sql)){
			$suma += $row['suma'];
		}
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']!=null) && ($_GET['locatar']!=null))
	{
		if ($user_id == 0)
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id.$extra;
		}
		else
		{
			$sql = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND casier_id=".$pozitie.$extra;
		}

		$sql = mysql_query($sql)or die("Nu pot afisa informatiile pentru locatar <br />".mysql_error());

		while ($row = mysql_fetch_array($sql)){
			$suma += $row['suma'];
		}
	}

	if ($user_id != 0)
		echo 'Nume casier: '.$_SESSION['user_name'].'<br />';

	echo 'Incasari perioada selectata: '.$suma.' LEI';

}

// Selectul pentru cazul in care nu este selectata nicio asociatie
function istoric_general($pozitie, $user_id){
	$i = 1;

	$deLa = $_GET['deLa'];
	$panaLa = $_GET['panaLa'];
	if($deLa<>null && $panaLa==null) {  $extra = " AND data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra = " AND data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if($deLa<>null && $panaLa==null) {  $extra1 = " WHERE data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra1 = " WHERE data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }


	if ($pozitie < 3){
		$parcurgChitantele = "SELECT * FROM casierie".$extra1;
	} else {
		$parcurgChitantele = "SELECT * FROM casierie WHERE casier_id=".$user_id.$extra;
	}
	$parcurgChitantele = mysql_query($parcurgChitantele) or die ("Nu pot accesa baza de date pentru istoric general!<br />".mysql_error());

if (mysql_num_rows($parcurgChitantele)==0) {
	echo '
	<tr>
		<td colspan="9">Nu sunt plati inregistrate in baza de date.</td>
	</tr>';
	} else {
		while($plati = mysql_fetch_array($parcurgChitantele)) {

			//aflu numele casierului
			$selectCasier = "SELECT C.casier_id, A.nume FROM casierie C, admin A WHERE C.casier_id=".$plati['casier_id']." AND A.id=C.casier_id";
			$selectCasier = mysql_query($selectCasier) or die ("Nu pot afla numele casierului 1<br />".mysql_error());
			while ($numeCasier = mysql_fetch_assoc($selectCasier)){
				$nume_casier = $numeCasier['nume'];
			}

			$selectAdresa = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
							scari.scara, scari.nr, scari.bloc,
							strazi.strada,
							C.loc_id
							FROM locatari, scari, strazi, casierie C
							WHERE locatari.loc_id=C.loc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id AND C.loc_id=".$plati['loc_id'];
			$selectAdresa = mysql_query($selectAdresa) or die("Nu gasesc locatarul 1<br />".mysql_error());
			while($adr = mysql_fetch_assoc($selectAdresa)) {
				$adresa = 'Str. '.$adr['strada'].', Nr. '.$adr['nr'].', Bl. '.$adr['bloc'].' Sc. '.$adr['scara'].' Et. '.$adr['etaj'].' Ap. '.$adr['ap'];
				$nume = $adr['nume'];
			}

			$suma += $plati['suma'];
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			echo '<form action="" method="post" />';
				echo '<input type="hidden" name="buton" value="apasat" />';
				echo '<tr bgcolor="'.$color.'">';
					echo '<td align="center">'.$i.'</td>';
					echo '<td align="center">'.$nume.'</td>';
					echo '<td align="center">'.$adresa.'</td>';
					echo '<td align="center"><a href="/app/modules/casierie/chitanta.php?id='.$plati['id'].'" target="_blank" >'.$plati['chitanta_serie'].'/'.$plati['chitanta_nr'].'</a></td>';
					echo '<td align="center">'.$plati['data_inserarii'].'</td>';
					echo '<td align="center">'.$plati['suma'].'</td>';
					echo '<td align="center">'.$plati['tip_plata'].'</td>';
					echo '<td align="center">'.$plati['reprezentand'].'</td>';
					echo '<td align="center">'.$nume_casier.'</td>';
					if ($pozitie == 0){
						echo '<td align="center"><input type="submit" value="Anuleaza" /></td>';
					}
				echo '</tr>';
				$i++;
			echo '</form>';
		}
	}
}

function istoric_asociatie($asoc_id, $pozitie, $user_id){
	$i = 1;

	$deLa = $_GET['deLa'];
	$panaLa = $_GET['panaLa'];
	if($deLa<>null && $panaLa==null) {  $extra = " AND data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra = " AND data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if ($pozitie == 0){
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id.$extra;
	} else {
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND casier_id=".$user_id.$extra;
	}

	$parcurgChitantele = mysql_query($parcurgChitantele) or die ("Nu pot accesa baza de date pentru istoric asociatie!<br />".mysql_error());

if (mysql_num_rows($parcurgChitantele)==0){
	$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$asoc_id;
	$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatia 2<br >".mysql_error());

	echo '
	<tr>
		<td colspan="9">Nu sunt plati inregistrate in baza de date pentru asociatia '.mysql_result($asociatia, 0, 'asociatie').'.</td>
	</tr>';
	} else {
		while($plati = mysql_fetch_array($parcurgChitantele)) {

			$numeCasier = "SELECT C.casier_id, A.nume FROM casierie C, admin A WHERE C.casier_id=".$plati['casier_id']." AND A.id=C.casier_id";
			$numeCasier = mysql_query($numeCasier) or die ("Nu pot afla numele casierului 2<br />".mysql_error());

			while ($casier = mysql_fetch_assoc($numeCasier)){
				$nume_casier = $casier['nume'];
			}

			$detaliiLoc = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
				scari.scara, scari.nr, scari.bloc,
				strazi.strada,
				C.loc_id
					FROM locatari, scari, strazi, casierie C
					WHERE locatari.loc_id=C.loc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id AND C.loc_id=".$plati['loc_id'];
			$detaliiLoc = mysql_query($detaliiLoc) or die("Nu gasesc locatarul 2<br />".mysql_error());

			while($detalii = mysql_fetch_assoc($detaliiLoc)) {
				$adresa = 'Str. '.$detalii['strada'].', Nr. '.$detalii['nr'].', Bl. '.$detalii['bloc'].' Sc. '.$detalii['scara'].' Et. '.$detalii['etaj'].' Ap. '.$detalii['ap'];
				$nume = $detalii['nume'];
			}

			$suma += $row['suma'];
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			echo '<form action="" method="post" />';
				echo '<input type="hidden" name="buton" value="apasat" />';
				echo '<tr bgcolor="'.$color.'">';
					echo '<td align="center">'.$i.'</td>';
					echo '<td align="center">'.$nume.'</td>';
					echo '<td align="center">'.$adresa.'</td>';
		      echo '<td align="center"><a href="/app/modules/casierie/chitanta.php?id='.$plati['id'].'" target="_blank" >'.$plati['chitanta_serie'].'/'.$plati['chitanta_nr'].'</a></td>';
					echo '<td align="center">'.$plati['data_inserarii'].'</td>';
					echo '<td align="center">'.$plati['suma'].'</td>';
					echo '<td align="center">'.$plati['tip_plata'].'</td>';
					echo '<td align="center">'.$plati['reprezentand'].'</td>';
					echo '<td align="center">'.$nume_casier.'</td>';
					if ($pozitie == 0){
						echo '<td align="center"><input type="submit" value="Anuleaza" /></td>';
					}
				echo '</tr>';
				$i++;
			echo '</form>';
		}
	}
}

function istoric_scara($asoc_id, $scara_id, $pozitie, $user_id){
	$i = 1;

	$deLa = $_GET['deLa'];
	$panaLa = $_GET['panaLa'];
	if($deLa<>null && $panaLa==null) {  $extra = " AND data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra = " AND data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if ($pozitie == 0){
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id.$extra;
	} else {
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND casier_id=".$user_id.$extra;
	}
	$parcurgChitantele = mysql_query($parcurgChitantele) or die ("Nu pot accesa baza de date pentru istoric scara!<br />".mysql_error());

	if (mysql_num_rows($parcurgChitantele)==0) {
		$scara = "SELECT * FROM scari WHERE scara_id=".$scara_id;
		$scara = mysql_query($scara) or die ("Nu pot afla scara<br />".mysql_error());
		$scara = mysql_result($scara, 0, 'bloc').', scara '.mysql_result($scara, 0, 'scara');
		echo '
		<tr>
			<td colspan="9">Nu sunt plati inregistrate in baza de date pentru blocul '.$scara.'.</td>
		</tr>';
		} else {
			while($plati = mysql_fetch_array($parcurgChitantele)) {

				// numele casierului
				$numeCasier = "SELECT C.casier_id, A.nume FROM casierie C, admin A WHERE C.casier_id=".$plati['casier_id']." AND A.id=C.casier_id";
				$numeCasier = mysql_query($numeCasier) or die ("Nu pot afla numele casierului 3<br />".mysql_error());
				while ($casier = mysql_fetch_assoc($numeCasier)){
					$nume_casier = $casier['nume'];
				}

				$detaliiLocatar = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
						scari.scara, scari.nr, scari.bloc,
						strazi.strada,
						C.loc_id
					FROM locatari, scari, strazi, casierie C
					WHERE locatari.loc_id=C.loc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id AND C.loc_id=".$plati['loc_id'];
				$detaliiLocatar = mysql_query($detaliiLocatar) or die("Nu gasesc locatarul <br />".mysql_error());

				while($detalii = mysql_fetch_assoc($detaliiLocatar)) {
					$adresa = 'Str. '.$adr['strada'].', Nr. '.$adr['nr'].', Bl. '.$adr['bloc'].' Sc. '.$adr['scara'].' Et. '.$adr['etaj'].' Ap. '.$adr['ap'];
					$nume = $detalii['nume'];
				}

			$suma += $plati['suma'];
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			echo '<form action="" method="post" />';
			echo '<input type="hidden" name="buton" value="apasat" />';
				echo '<tr bgcolor="'.$color.'">';
				echo '<td align="center">'.$i.'</td>';
				echo '<td align="center">'.$nume.'</td>';
				echo '<td align="center">'.$adresa.'</td>';
			  echo '<td align="center"><a href="/app/modules/casierie/chitanta.php?id='.$plati['id'].'" target="_blank" >'.$plati['chitanta_serie'].'/'.$plati['chitanta_nr'].'</a></td>';
        echo '<td align="center">'.$plati['data_inserarii'].'</td>';
				echo '<td align="center">'.$plati['suma'].'</td>';
				echo '<td align="center">'.$plati['tip_plata'].'</td>';
				echo '<td align="center">'.$plati['reprezentand'].'</td>';
				echo '<td align="center">'.$nume_casier.'</td>';
				if ($pozitie == 0){
					echo '<td align="center"><input type="submit" value="Anuleaza" /></td>';
				}
			echo '</tr>';
			$i++;
		echo '</form>';
		}
	}
}

function istoric_locatar($asoc_id, $scara_id, $loc_id, $pozitie, $user_id){
	$i = 1;

	$deLa = $_GET['deLa'];
	$panaLa = $_GET['panaLa'];
	if($deLa<>null && $panaLa==null) {  $extra = " AND data_inserarii>='".$deLa."'"; }
	if($deLa<>null && $panaLa<>null) {  $extra = " AND data_inserarii>='".$deLa."' AND data_inserarii<='".$panaLa." 23:59:59'"; }

	if ($pozitie == 0){
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id.$extra;
	} else {
		$parcurgChitantele = "SELECT * FROM casierie WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND casier_id=".$user_id.$extra;
	}
	$parcurgChitantele = mysql_query($parcurgChitantele) or die ("Nu pot accesa baza de date pentru istoric locatar!<br />".mysql_error());

	if (mysql_num_rows($parcurgChitantele)==0) {
		$locatar = "SELECT * FROM locatari WHERE loc_id=".$loc_id;
		$locatar = mysql_query($locatar) or die ("Nu pot afla numele locatarului 4<br />".mysql_error());
		$locatar = mysql_result($locatar, 0, 'nume');
		echo '
		<tr>
			<td colspan="9">Nu sunt plati inregistrate in baza de date pentru '.$locatar.'.</td>
		</tr>';
	} else {
		while($plati = mysql_fetch_array($parcurgChitantele)) {

			$numeCasier = "SELECT C.casier_id, A.nume FROM casierie C, admin A WHERE C.casier_id=".$plati['casier_id']." AND A.id=C.casier_id";
			$numeCasier = mysql_query($numeCasier) or die ("Nu pot afla numele casierului 4<br />".mysql_error());
			while ($casier = mysql_fetch_assoc($numeCasier)){
				$nume_casier = $casier['nume'];
			}

			//aflu detalii despre locatar
			$detaliiLocatar = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
								scari.scara, scari.nr, scari.bloc,
								strazi.strada,
								C.loc_id
							FROM locatari, scari, strazi, casierie C
							WHERE locatari.loc_id=C.loc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id AND C.loc_id=".$plati['loc_id'];
			$detaliiLocatar = mysql_query($detaliiLocatar) or die("Nu gasesc locatarul 4<br />".mysql_error());

			while($detalii = mysql_fetch_assoc($detaliiLocatar)) {
				$adresa = 'Str. '.$adr['strada'].', Nr. '.$adr['nr'].', Bl. '.$adr['bloc'].' Sc. '.$adr['scara'].' Et. '.$adr['etaj'].' Ap. '.$adr['ap'];
				$nume = $detalii['nume'];
			}

			$suma += $plati['suma'];
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			echo '<form action="" method="post" />';
			echo '<input type="hidden" name="buton" value="apasat" />';
				echo '<tr bgcolor="'.$color.'">';
				echo '<td align="center">'.$i.'</td>';
				echo '<td align="center">'.$nume.'</td>';
				echo '<td align="center">'.$adresa.'</td>';
		  echo '<td align="center"><a href="/app/modules/casierie/chitanta.php?id='.$plati['id'].'" target="_blank" >'.$plati['chitanta_serie'].'/'.$plati['chitanta_nr'].'</a></td>';
				echo '<td align="center">'.$plati['data_inserarii'].'</td>';
				echo '<td align="center">'.$plati['suma'].'</td>';
				echo '<td align="center">'.$plati['tip_plata'].'</td>';
				echo '<td align="center">'.$plati['reprezentand'].'</td>';
				echo '<td align="center">'.$nume_casier.'</td>';
				if ($pozitie == 0){
					echo '<td align="center"><input type="submit" value="Anuleaza" /></td>';
				}
				echo '</tr>';
				$i++;
			echo "</form>";
			}
		}
}

////////////////////////////////////////////////////////////////////////////////////

if ($_GET['asoc_id']<>null){
	$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id'];
} else {
	$sql = "SELECT * FROM asociatii";
}
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	if ($_GET['scara_id']<>null){
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id'];
	} else {
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	}
	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());
	while ($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {
	if ($_GET['locatar']<>null){
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND loc_id<>".$_GET['locatar'];
	} else {
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];
	}
	$sql3 = mysql_query($sql3);
	while($row3 = mysql_fetch_array($sql3)) {
		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';
	}
}

?>
<style type="text/css">

thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { width:100%; border:none; height:100%; }
tbody tr.newline td { border:solid 1px #0CC;   }
tfoot { color:#FFF; }
.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
.addnew2 {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:95px; margin-top:-9px;  }
tr.newline input { text-align:center; }
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
#print {float:left; margin-left:900px; margin-top:40px;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {

					$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
					$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());

					echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';
				}?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {
					$scara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
					$scara = mysql_query($scara) or die ("Nu pot selecta scarile<br />".mysql_error());

					echo '<option value="">'.mysql_result($scara, 0, 'scara').'</option>';
				}?>
        		<?php  echo $scari; ?>
            </select></td>
    </tr>
    <?php endif;?>
      <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti locatarul:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['locatar']==null)  { echo '<option value="">----Alege----</option>';    }  else {
			$locatar = "SELECT * FROM locatari WHERE loc_id=".$_GET['locatar'];
			$locatar = mysql_query($locatar) or die ("Nu pot selecta locatarii<br />".mysql_error());

			echo '<option value="">'.mysql_result($locatar, 0, 'nume').'</option>';

		}?>
        <?php echo $locatari; ?>
        </select></td>
    </tr>
    <?php endif;?>
</table>



</div>

 <!-- <form action="" method="post"> -->
  <div id="print">
  	<a href="#">printeaza</a>
  </div>
  <br clear="left" />
<table width="1000" style="top:250px; background-color:#FFFFFF;">
<thead>
<!--<tr>
	<td colspan="9" style="color:#000000;" align="left">
    	$calendar_picker
    </td>
</tr>-->

<tr>
	<td colspan="10" style="color:#000000;" align="left"><br />
        Afiseaza de la:
        	<input type="text" name="deLa" value="<?php echo $_GET['deLa']; ?>" onchange="deLa('<?php echo $_GET['asoc_id']; ?>', '<?php echo $_GET['scara_id']; ?>', '<?php echo $_GET['locatar']; ?>', this.value)" class="datepicker1" />
        pana la:
        	<input type="text" name="panaLa" value="<?php echo $_GET['panaLa']; ?>" onchange="panaLa('<?php echo $_GET['asoc_id']; ?>', '<?php echo $_GET['scara_id']; ?>', '<?php echo $_GET['locatar']; ?>', '<?php echo $_GET['deLa']; ?>', this.value)" class="datepicker1" /> <br /> &nbsp;
    </td>
</tr>

<tr>
  <?php if ($_SESSION['uid'] == 0) {?>
	<td colspan="10" style="color:#000000;" align="left"><?php total_incasari($_GET['asoc_id'], $_GET['scara_id'], $_GET['locatar'], $_SESSION['uid'], $_SESSION['rank']) ?></td>
  <?php } else {?>
	<td colspan="9" style="color:#000000;" align="left"><?php total_incasari($_GET['asoc_id'], $_GET['scara_id'], $_GET['locatar'], $_SESSION['uid'], $_SESSION['rank']) ?></td>
  <?php } ?>
</tr>
<tr>
  <td bgcolor="#666666">Nr. Crt.</td>
  <td bgcolor="#666666">Nume</td>
  <td bgcolor="#666666" width="250">Adresa</td>
  <td bgcolor="#666666">Chitanta(Serie/Nr)</td>
  <td bgcolor="#666666">Data</td>
  <td bgcolor="#666666">Valoare</td>
  <td bgcolor="#666666">Tip Plata</td>
  <td bgcolor="#666666">Reprezentand</td>
  <td bgcolor="#666666">Nume Casier</td>
  <?php if ($_SESSION['uid'] == 0) {?>
	  <td>&nbsp;</td>
  <?php } ?>
</tr>
</thead>
<tbody>

<?php
  	if(($_GET['asoc_id']==null) && ($_GET['scara_id']==null) && ($_GET['locatar']==null)) {
		istoric_general($_SESSION['uid'], $_SESSION['rank']);
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']==null) && ($_GET['locatar']==null)) {
		istoric_asociatie($_GET['asoc_id'], $_SESSION['uid'], $_SESSION['rank']);
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']!=null) && ($_GET['locatar']==null)) {
		istoric_scara($_GET['asoc_id'], $_GET['scara_id'], $_SESSION['uid'], $_SESSION['rank']);
	}

	if(($_GET['asoc_id']!=null) && ($_GET['scara_id']!=null) && ($_GET['locatar']!=null)) {
		istoric_locatar($_GET['asoc_id'], $_GET['scara_id'], $_GET['locatar'], $_SESSION['uid'], $_SESSION['rank']);
	}
?>
</tbody>
</table>
<!-- <a href="#">print</a> -->
<!-- </form> -->