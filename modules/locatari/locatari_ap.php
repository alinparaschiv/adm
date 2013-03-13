<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=locatari_ap&asoc_id=" + value;
}

function select_scara(value,value2) {
	window.location = "index.php?link=locatari_ap&asoc_id=" + value + "&scara_id=" + value2;
}

function trimiteForm(){
	document.apometre.submit();
}
</script>

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
</style>

<?php
/*******************  FAC INSERTUL SI VERIFICARILE DE RIGOARE  *******************/
$err = 0;
if ($_POST['potAdauga'] == "OK"){
	foreach($_POST as $key=>$value){
		if ($value != "OK"){
			if ($value != '') {
				$key = explode("-", $key);

				$verificDacaEste = "SELECT * FROM locatari_apometre WHERE loc_id=".$key[0];
				$verificDacaEste = mysql_query($verificDacaEste) or die ("Nu pot verifica locatarul<br />".mysql_error());

				if (mysql_num_rows($verificDacaEste) == 0){
					$inserezLocatar = "INSERT INTO locatari_apometre VALUES (null, ".$key[0].", ".$_GET['scara_id'].", ".$_GET['asoc_id'].", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
					$inserezLocatar = mysql_query($inserezLocatar) or die ("Nu pot insera locatarul in tabela apometre initiale<br />".mysql_error());
				}

				$adaugInBD = "UPDATE locatari_apometre SET `".$key[1]."` = ".$value.", `setat`='1' WHERE loc_id=".$key[0];
				$adaugInBD = mysql_query($adaugInBD) or die ("Ceva nu a mers bine la adaugarea citirii<br />".mysql_error());
			} else {
				if ($err != 1){
					echo "Toate campurile trebuiesc completate";
					$err = 1;
				}
			}
		}
	}
}

/*******************  SELECTEAZA ASOCIATIA SI SCARA  *******************/
$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2);
	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}
}

/*******************  CITIM INITIALE APOMETRELE DACA E CAZUL  *******************/
function citescApometre($asocId, $scaraId){
	$selectezScara = "SELECT * FROM locatari_apometre WHERE scara_id=".$scaraId." ORDER BY loc_id ASC";
	$selectezScara = mysql_query($selectezScara) or die ("Nu pot selecta scara din setariApometre<br />".mysql_error());

	$ordine = 0;

	if (mysql_num_rows($selectezScara) != 0){
		while ($iiIauPeRand = mysql_fetch_array($selectezScara)){
			$detaliiLocatar = "SELECT * FROM locatari WHERE loc_id=".$iiIauPeRand['loc_id']." ORDER BY loc_id ASC";
			$detaliiLocatar = mysql_query($detaliiLocatar) or die ("Nu pot afla detaliile despre locatari<br />".mysql_error());

			if ($ordine % 2 == 0){
				$culoare = "#DDDDDD";
			} else {
				$culoare = "#FFFFFF";
			}


			echo '<tr bgcolor="'.$culoare.'">';
					echo '<td>'.mysql_result($detaliiLocatar, 0, 'etaj').'</td>';
					echo '<td>'.mysql_result($detaliiLocatar, 0, 'ap').'</td>';
					echo '<td>'.mysql_result($detaliiLocatar, 0, 'nume').'</td>';

					$apRece = 0;
					$apCalda = 0;

					// Afisez apa rece
					for ($apRece; $apRece < mysql_result($detaliiLocatar, 0, 'ap_rece'); $apRece++){
						$apoR = $iiIauPeRand['loc_id'].'-'.'r'.($apRece+1);
						$nrApo = 'r'.($apRece+1);

						if (($iiIauPeRand['r'.($apRece+1)] == 0) && ($iiIauPeRand['setat'] == 0)){
							echo '<td><input style="background-color:#000; color: #FFF; width:40px;" type="text" name="'.$apoR.'" value="'.$_POST[$apoR].'"/></td>';
						} else {
							echo '<td>'.$iiIauPeRand['r'.($apRece+1)].'</td>';
						}
					}

					$apRece = mysql_result($detaliiLocatar, 0, 'ap_rece');
					for ($apRece; $apRece < 5; $apRece++){
						echo '<td> - </td>';
					}

					// Afisez apa calda
					for ($apCalda; $apCalda < mysql_result($detaliiLocatar, 0, 'ap_calda'); $apCalda++){
						$apoC = $iiIauPeRand['loc_id'].'-'.'c'.($apCalda+1);
						$nrApo = 'c'.($apCalda+1);

						if (($iiIauPeRand['r'.($apCalda+1)] == 0) && ($iiIauPeRand['setat'] == 0)){
							echo '<td><input style="background-color:#000; color: #FFF; width:40px;" type="text" name="'.$apoC.'" value="'.$_POST[$apoC].'"/></td>';
						} else {
							echo '<td>'.$iiIauPeRand['c'.($apCalda+1)].'</td>';
						}
					}

					$apCalda = mysql_result($detaliiLocatar, 0, 'ap_calda');
					for ($apCalda; $apCalda < 5; $apCalda++){
						echo '<td> - </td>';
					}
				echo '</tr>';
			$ordine++;
		}

		$apare = 0;
		$verificButonSalvare = "SELECT * FROM locatari_apometre WHERE scara_id=".$scaraId;
		$verificButonSalvare = mysql_query($verificButonSalvare) or die ("Nu pot accesa tabela locatari_apometre 1<br />".mysql_error());
		while ($check = mysql_fetch_array($verificButonSalvare)){
			if ($check['setat'] == 0){
				$apare++;
			}
		}

		if ($apare != 0){
			echo '<tr bgcolor="#BBB"><td colspan="13" align="right"><a style="cursor:pointer;" onclick="trimiteForm()">Salveaza</a></td></tr>';
		}
	} else {
		$iiAdaug = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
		$iiAdaug = mysql_query($iiAdaug) or die ("Nu pot selecta locatarii<br />".mysql_error());

		$ordine = 0;

		while ($peRand = mysql_fetch_array($iiAdaug)){
			if ($ordine % 2 == 0){
				$culoare = "#DDDDDD";
			} else {
				$culoare = "#FFFFFF";
			}

			echo '<tr bgcolor="'.$culoare.'">';
				echo '<td>'.$peRand['etaj'].'</td>';
				echo '<td>'.$peRand['ap'].'</td>';
				echo '<td>'.$peRand['nume'].'</td>';

				$apRece = 0;
				$apCalda = 0;

				// Afisez apa rece
				for ($apRece; $apRece < $peRand['ap_rece']; $apRece++){
					$apoR = $peRand['loc_id'].'-'.'r'.($apRece+1);
					$nrApo = 'r'.($apRece+1);

					echo '<td><input style="background-color:#000; color: #FFF; width:40px;" type="text" name="'.$apoR.'" value="'.$_POST[$apoR].'"/></td>';
				}

				$apRece = $peRand['ap_rece'];
				for ($apRece; $apRece < 5; $apRece++){
					echo '<td> - </td>';
				}

				// Afisez apa calda
				for ($apCalda; $apCalda < $peRand['ap_calda']; $apCalda++){
					$apoC = $peRand['loc_id'].'-'.'c'.($apCalda+1);
					$nrApo = 'c'.($apCalda+1);

					echo '<td><input style="background-color:#000; color: #FFF; width:40px;" type="text" name="'.$apoC.'" value="'.$_POST[$apoC].'"/></td>';
				}

				$apCalda = $peRand['ap_calda'];
				for ($apCalda; $apCalda < 5; $apCalda++){
					echo '<td> - </td>';
				}
			echo '</tr>';
		$ordine++;
		}
		echo '<tr bgcolor="#BBB"><td colspan="13" align="right"><a style="cursor:pointer;" onclick="trimiteForm()">Salveaza</a></td></tr>';
	}
}

?>

<div id="content" style="float:left;">
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">(1/2) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select onChange="select_asoc(this.value)">
				<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
					{
						$afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
						$afisAsoc = mysql_query($afisAsoc) or die ("Nu pot selecta asociatiile<br />".mysql_error());

						echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
					}
				?>
		        	<?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
		<?php if($_GET['asoc_id']<>null):?>
	<tr>
		<td align="left" bgcolor="#CCCCCC">(2/2) Alegeti scara:</td>
		<td align="left" bgcolor="#CCCCCC">
        		<select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
				<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
					{
						$afisScara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
						$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scara<br />".mysql_error());

						echo '<option value="">Bloc '.mysql_result($afisScara, 0, 'bloc').', scara '.mysql_result($afisScara, 0, 'scara').'</option>';
					}
				?>
				<?php  echo $scari; ?>
			</select>
		</td>
	</tr>
		<?php endif;?>
</table>

</div>

<?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
<form id="apometre" name="apometre" method="post" action="">
	<input type="hidden" name="potAdauga" value="OK" />
    <table width="750" style="float:left;  margin-top:10px; background-color:#BBB;">
    	<thead>
        	<tr bgcolor="#000">
            	<td>Etaj</td>
                <td>Apartament</td>
                <td>Nume</td>
                <td>AR 1</td>
                <td>AR 2</td>
                <td>AR 3</td>
                <td>AR 4</td>
                <td>AR 5</td>
                <td>AC 1</td>
                <td>AC 2</td>
                <td>AC 3</td>
                <td>AC 4</td>
                <td>AC 5</td>
            </tr>
        </thead>

		<?php citescApometre($_GET['asoc_id'], $_GET['scara_id']) ?>
    </table>
</form>
<?php endif; ?>
