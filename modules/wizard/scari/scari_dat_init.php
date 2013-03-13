<?php
if (isset($_POST['actiune']) && $_POST['actiune'] == "Adauga Furnizor") {
	$asocId = mysql_real_escape_string($_POST['asoc_id']);
	$scaraId = mysql_real_escape_string($_POST['scara_id']);
	$furnizor = mysql_real_escape_string($_POST['fur_id']);
	$codClient = mysql_real_escape_string($_POST['cod_client']);
	$datorie = mysql_real_escape_string($_POST['datorie']);
	$penalizare = mysql_real_escape_string($_POST['penalizare']);
	$protocol =  mysql_real_escape_string($_POST['protocol']);
	$protocol = explode('-', $protocol);
	$protocol = $protocol[2].'-'.$protocol[1].'-'.$protocol[0];
	$i = 0;
	if ($_POST['asoc_id'] == '' || $_POST['scara_id'] == '' || $_POST['fur_id'] == '' || $_POST['cod_client'] == '' || $_POST['datorie'] == '' || $_POST['penalizare'] == '' || $_POST['protocol'] == '') {
		$i=1;
		$mesaj = '<font color="red">Trebuie completate toate campurile <br /></font>';
	}
	if(preg_match('/^((0[1-9])|([12][0-9])|(3[01]))[-]((0[1-9])|(1[012]))[-](19|20)\d\d]$/', $_POST['protocol'])) {
		$i=1;
		$errprotocol='Campul "Protocol" trebuie sa fie de forma DD-LL-AAAA.<br>';
	}
	if(preg_match('/[^0-9.]/', $datorie)) {
		$i=1;
		$errdatorie='Campul "Datorie" poate sa contina doar cifre.<br>';
	}
	if(preg_match('/[^0-9.]/', $penalizare)) {
		$i=1;
		$errpenalizare='Campul "Penalizare" poate sa contina doar cifre.<br>';
	}

	if ($i == 0) {
		$q = "INSERT INTO scari_furnizori (`asoc_id`, `scara_id`, `fur_id`, `codClient`, `nr`, `serie`,`emitere`, `scadenta`, `datorie`, `penalizare`)
																		 VALUES ('$asocId', '$scaraId', '$furnizor', '$codClient', NULL, NULL, '".date('Y-m-d')."', '$protocol', '$datorie', '$penalizare')";
		mysql_query($q) or die('Nu am putut adauga furnizorul pentru asociatia curenta<br />'/mysql_error());
		$mesaj = '<font color="green">Datele au fost introduse.</font>';
		$q = "SELECT S.serv_id, F.proc_penalizare FROM `furnizori_servicii` FS, servicii S, furnizori F WHERE F.fur_id=FS.fur_id AND FS.serv_id=S.serv_id AND F.fur_id=".$furnizor." ORDER BY FS.fur_id, FS.fs_id LIMIT 1";
		$q = mysql_query($q) or die("Nu pot afla serviciul care corespunde furnizorului curent <br />".mysql_error());
		$serviciuId = mysql_result($q, 0, 'serv_id');
		$q = "SELECT min(fact_id) id FROM fisa_furnizori";
		$q = mysql_query($q) or die("Nu pot gasi un id de factura valid <br />".mysql_error());
		$factId = mysql_result($q, 0, 'id') - 1;
		$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
	        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', '$scaraId', '$furnizor', '$serviciuId', '$factId', '".$protocol."', '".$protocol."', 'Setari Initiale', 'Protocol', '".$datorie."', NULL, NULL);";
		mysql_query($insertFisaFurnizoriSQL) or die ("#Nu am reusit sa introduc in fisa furnizori datoria initiala <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());
		$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
	        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', '$scaraId', '$furnizor', '$serviciuId', '$factId', '".$protocol."', '".$protocol."', 'Setari Initiale', 'Protocol', '0', '".$penalizare."', '".mysql_result($q, 0, 'proc_penalizare')."');";
		mysql_query($insertFisaFurnizoriSQL) or die ("#Nu am reusit sa introduc in fisa furnizori penalizarea initiala <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());

		$_GET['scara_id'] = $_POST['scara_id'];
		$_GET['asoc_id'] = $_POST['asoc_id'];
		unset ($_POST);
	} else {
		$mesaj = '<font color="red">Trebuie sa completati toate campurile si sa rezolvati erorile inaintea salvarii datelor introduse.</font>';
	}
}
$asoc = isset($_POST['asoc_id']) ? $_POST['asoc_id'] : ( isset($_GET['asoc_id']) ? $_GET['asoc_id'] : null ) ;
$scara = isset($_POST['scara_id']) ? $_POST['scara_id'] : ( isset($_GET['scara_id']) ? $_GET['scara_id'] : null ) ;
$fur = isset($_POST['fur_id']) ? $_POST['fur_id'] : ( isset($_GET['fur_id']) ? $_GET['fur_id'] : null ) ;
?>

<script type="text/javascript">
function scara_change(sc){
	window.location = 'index.php?link=w_scari_dat&asoc_id=' + sc[sc.selectedIndex].id + '&scara_id=' + sc.value;
}
</script>

<form action="index.php?link=w_scari_dat&asoc_id=<?php echo $asoc;?>" method="post">
<table cellspacing=5 style="margin:20px 0 0 0px; width:500px;" border=0>
	<?php if (isset($mesaj) && $mesaj != '') {
		echo '<tr><td colspan="2">'.$mesaj.'</td></tr><td></td><tr></tr>';
	}
	?>
	<tr><td colspan="2" bgcolor="#19AF62"> Adaugare Furnizori</td></tr>
	<input type="hidden" name="asoc_id" value="<?php echo $asoc;?>" />
	<tr>
		<td bgcolor="#CCCCCC">Scara:</td>
		<td bgcolor="#CCCCCC">
			<select name="scara_id" onchange="scara_change(this)">
			<?php
			$q = "SELECT S.*, A.asociatie FROM scari S, asociatii A WHERE S.asoc_id=A.asoc_id ";
			if($asoc != null) $q .= 'AND A.asoc_id='.$asoc;
			$q = mysql_query($q) or die("Nu pot selecta asociatiile".mysql_error());
			if (mysql_num_rows($q) == 1) {
				$row = mysql_fetch_assoc($q);
				$_GET['scara_id'] = $row['scara_id'];
				echo '<option value="'.$row['scara_id'].'" id="'.$row['asoc_id'].'" '. ((isset($_GET['scara_id']) && ($_GET['scara_id'] == $row['scara_id']) || (isset($_POST['scara_id']) && ($_POST['scara_id'] == $row['scara_id']))) ? 'selected="selected"' : '' ).'>'.$row['scara'].' ('.$row['asociatie'].')</option>';
				$scara = isset($_POST['scara_id']) ? $_POST['scara_id'] : ( isset($_GET['scara_id']) ? $_GET['scara_id'] : null ) ;
			} else {
				echo '<option disabled="disabled" value="Alege Scara" />';
			}
			while($row = mysql_fetch_assoc($q)){
				echo '<option value="'.$row['scara_id'].'" id="'.$row['asoc_id'].'" '. ((isset($_GET['scara_id']) && ($_GET['scara_id'] == $row['scara_id']) || (isset($_POST['scara_id']) && ($_POST['scara_id'] == $row['scara_id']))) ? 'selected="selected"' : '' ).'>'.$row['scara'].' ('.$row['asociatie'].')</option>';
			}
			?></select>
		</td>
	</tr>
<?php if (isset($_POST['scara_id']) || isset($_GET['scara_id'])) { ?>
	<tr>
		<td bgcolor="#CCCCCC">Furnizorul:</td>
		<td bgcolor="#CCCCCC">
		<select name="fur_id"><?php
		$q = "SELECT F.*, S.* FROM servicii S, furnizori_servicii FS, furnizori F WHERE S.serv_id=FS.serv_id AND FS.fur_id=F.fur_id AND S.nivel<>1 ";
		$q .= 'AND F.fur_id NOT IN (SELECT fur_id FROM scari_furnizori WHERE scara_id='.$scara.')';
		$q = mysql_query($q) or die("Nu pot selecta asociatiile".mysql_error());
		while($row = mysql_fetch_assoc($q)){
			echo '<option value="'.$row['fur_id'].'" '. ((isset($_GET['fur_id']) && ($_GET['fur_id'] == $row['fur_id']) || (isset($_POST['fur_id']) && ($_POST['fur_id'] == $row['fur_id']))) ? 'selected="selected"' : '' ).'>'.$row['furnizor'].' ('.$row['serviciu'].')</option>';
		}

		?></select></td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td>Cod Client:</td>
		<td><input type="text" name="cod_client" <?php if(isset($_POST['cod_client'])) echo 'value="'.$_POST['cod_client'].'"';?> /></td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td>Data protocol:</td>
		<td><input type="text" <?php if(isset($errprotocol)) echo 'style="background-color:#FF9494;"'; ?> class="datepicker" name="protocol"  <?php if(isset($_POST['protocol'])) echo 'value="'.$_POST['protocol'].'"';?>/></td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td>Datorie Initiala:</td>
		<td><input type="text" <?php if(isset($errdatorie)) echo 'style="background-color:#FF9494;"'; ?> name="datorie" <?php if(isset($_POST['datorie'])) echo 'value="'.$_POST['datorie'].'"';?>/></td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td>Penalizare Initiala:</td>
		<td><input type="text" <?php if(isset($errpenalizare)) echo 'style="background-color:#FF9494;"'; ?> name="penalizare" <?php if(isset($_POST['penalizare'])) echo 'value="'.$_POST['penalizare'].'"';?>/></td>
	</tr>
	<tr><td  bgcolor="#CCCCCC" colspan="2"><input type="submit" name="actiune" value="Adauga Furnizor" /></td></tr>
	<tr><td></td><td></td></tr>
  <tr><td></td><td></td></tr>
	
<?php } ?>
  <tr>
   <td><div id="butonBack"><a href="index.php?link=w_scari&asoc_id=<?php echo $_GET['asoc_id']; ?>" style="">Pasul Anterior</a></div></td>
   <td><div id="buton"><a href="index.php?link=w_scari_setari&asoc_id=<?php echo $_GET['asoc_id']; ?>" style="">Pasul Urmator</a></div></td>
  </tr>
</table>
</form>
<br /><br /> <br />

<?php if (isset($_POST['scara_id']) || isset($_GET['scara_id'])) {
$scaraId = isset($_POST['scara_id']) ? $_POST['scara_id'] : $_GET['scara_id'];
?>
	<table width="800">
		<tr bgcolor="#19AF62">
			<td>Furnizor</td>
			<td>Cod Client</td>
			<td>Data Protocol</td>
			<td>Datorie Initiala</td>
			<td>Penalizare Initiala</td>
		</tr>
	<?php
	$color = 0;
	$q = "SELECT SF.*, F.furnizor FROM scari_furnizori SF, furnizori F WHERE SF.fur_id=F.fur_id AND SF.scara_id=".$scaraId;
	$q = mysql_query($q) or die("Nu pot selecta furnizori pentru asociatia curenta".mysql_error());
	while($row = mysql_fetch_assoc($q)){ ?>
		<tr bgcolor="#<?php echo ($color++ % 2) == 0 ? 'CCCCCC' : '999999';?>">
			<td><?php echo $row['furnizor']; ?></td>
			<td><?php echo $row['codClient']; ?></td>
			<td><?php echo $row['scadenta']; ?></td>
			<td><?php echo $row['datorie']; ?></td>
			<td><?php echo $row['penalizare']; ?></td>
		</tr>
	<?php }
echo '</table>';
} //sfarsitul de la tabelul afisat cu furnizorii unei anumite asociatii
?>