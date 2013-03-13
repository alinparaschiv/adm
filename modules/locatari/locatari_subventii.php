<?php
if (isset($_GET['loc_id']) && isset($_GET['scara_id']) && isset($_GET['asoc_id'])) {

$id_asociatie = $_GET['asoc_id'];
$id_scara = $_GET['scara_id'];
$id_locatar = $_GET['loc_id'];

function get_nume($loc_id){
	$sql = "SELECT nume FROM locatari WHERE loc_id=".$loc_id;
	$sql = mysql_query($sql) or die ("Nu pot accesa baza de date!<br />".mysql_error());

	echo mysql_result($sql, 0, 'nume');
}

function get_adresa($loc_id, $scara_id){
$sql = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
			scari.scara, scari.nr, scari.bloc,
			strazi.strada
		FROM locatari, scari, strazi
		WHERE loc_id=".$loc_id." AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id";

	$sql = mysql_query($sql) or die("Nu gasesc locatarul <br />".mysql_error());

while($row = mysql_fetch_assoc($sql)) {
	echo 'Str. '.$row['strada'].', Nr. '.$row['nr'].', Bloc: '.$row['bloc'].' Et: '.$row['etaj'].' Ap: '.$row['ap'];
}
}

function get_asociatie($asoc_id){
	$sql = "SELECT * FROM asociatii WHERE asoc_id=".$asoc_id;
	$sql = mysql_query($sql) or die ("Nu gasesc asociatia <br />".mysql_error());

	echo mysql_result($sql,0, 'asociatie');
}
	?>
	<table width="450" style="align:left; top:250px; background-color:white;">
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Nume si Prenume:</strong></td>
		<td bgcolor="#FFFFFF"><?php get_nume($id_locatar) ?></td>
	</tr>
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Adresa:</strong></td>
		<td bgcolor="#FFFFFF"><?php get_adresa($id_locatar, $id_strada) ?></td>
	</tr>
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Asociatie:</strong></td>
		<td bgcolor="#FFFFFF"><?php get_asociatie($id_asociatie) ?></td>
	</tr>
	</table>
	<?php

	if(isset($_GET['obtiune'])){
		if ($_GET['obtiune'] == 'sterge') {
			$sql = "DELETE FROM subventii WHERE subv_id=".mysql_escape_string($_GET['id']);
			mysql_query($sql) or die("NU am putut subvenctia curenta </ br>".mysql_error());
		}
		if ($_GET['obtiune'] == 'adaugare') {
			if (isset($_GET['procent']) && $_GET['procent'] >= 0 && $_GET['procent'] <= 100) {
				$sql = "INSERT INTO `subventii` (`subv_id` ,`serv_id` ,`loc_id` ,`procent`)VALUES (NULL ,  '".mysql_escape_string($_GET['serviciu'])."',  '".mysql_escape_string($_GET['loc_id'])."',  '".mysql_escape_string($_GET['procent'])."');";
				mysql_query($sql) or die("NU am putut subvenctia curenta </ br>".mysql_error());
			}
		}

	}


	$sql = "SELECT * FROM locatari L, subventii S, servicii SR WHERE L.loc_id=S.loc_id AND S.serv_id=SR.serv_id AND L.loc_id=".$_GET['loc_id'];
	$query = mysql_query($sql) or die("NU am putut selecta toti locatarii care au subvenctii </ br>".mysql_error());

	?>
	<table>
		<tr bgcolor="#19AF62" style="color:#000000">
			<td>Serviciu</td>
			<td>Procent</td>
			<td>Obtiuni</td>
		</tr>
		<?php
			while($row = mysql_fetch_array($query)){
				?>
				<tr>
					<td><?php echo $row['serviciu'];?></td>
					<td><?php echo $row['procent'];?></td>
					<td><a href="<?php echo 'index.php?link=locatari_sub&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&loc_id='.$row['loc_id'].'&obtiune=sterge&id='.$row['subv_id']; ?>" style="">Sterge</a></td>
				</tr>
				<?php
			}
			?>
			<form method="get">
				<input type="hidden" name="link" value="locatari_sub" >
				<input type="hidden" name="asoc_id" value="<?php echo $id_asociatie; ?>" >
				<input type="hidden" name="scara_id" value="<?php echo $id_scara; ?>" >
				<input type="hidden" name="loc_id" value="<?php echo $id_locatar; ?>" >
				<input type="hidden" name="obtiune" value="adaugare" >
				<tr>
					<td><select name="serviciu">
						<?php
							//$sql = "SELECT serviciu, serv_id FROM servicii WHERE serv_id NOT IN (SELECT serv_id FROM subventii WHERE loc_id=".$id_locatar.")";
	$sql = 'SELECT S.serviciu, S.serv_id

FROM servicii S, furnizori_servicii FS,

(SELECT fur_id FROM asociatii_furnizori WHERE asoc_id='.$id_asociatie.') AF,
(SELECT fur_id FROM scari_furnizori WHERE scara_id='.$id_scara.') SF

WHERE S.serv_id=FS.serv_id AND (FS.fur_id=AF.fur_id OR FS.fur_id=SF.fur_id) AND S.serv_id NOT IN (SELECT serv_id FROM subventii WHERE loc_id='.$id_locatar.')

GROUP BY S.serv_id';

						  $sql = mysql_query($sql) or die("NU am afla toate serviciile </ br>".mysql_error());

							while ($srv = mysql_fetch_array($sql)){
							switch ($srv['serv_id']) {
								case 21:
									echo '<option value="41">Diferente apa rece</option>';
									break;
								case 35:
									echo '<option value="84">Iluminat Comun (SA)</option>';
									echo '<option value="90">Iluminat Comun (SP)</option>';
									echo '<option value="91">Energie Lift (SP)</option>';
									echo '<option value="107">Energie Lift (SA)</option>';
									echo '<option value="92">Energie Apa Calda (SX)</option>';
									echo '<option value="93">Energie Incalzire (SX)</option>';
									break;
								case 26:
									echo '<option value="39">Diferenta apa rece pt acm</option>';
									echo '<option value="40">Diferenta Ag Termic pt acm</option>';
									break;
								default:
									echo '<option value="'.$srv['serv_id'].'">'.$srv['serviciu'].'</option>';
							} // switch

							}

						?>
					</select></td>
					<td><input type="text" maxlength="3" value="100" name="procent"></td>
					<td><input type="submit" value="Adauga"></td>
				</tr>
			</form>
			<?php

		?>
	</table>


	<?php


} else {
?>
<script type="text/javascript">
			$(function () {
				$('input#id_search').quicksearch('table#quicksearch tbody tr');
				});
</script>

<?php
function get_locatari (){
$sql = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume,
				asociatii.asociatie,
				scari.scara, scari.nr, scari.bloc,
				strazi.strada
		FROM locatari, asociatii, scari, strazi
		WHERE locatari.asoc_id=asociatii.asoc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id";
	$sql = mysql_query($sql) or die("Imi ceri prea multe <br />".mysql_error());
	$i = 1;

while($row = mysql_fetch_assoc($sql)) {
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }

	echo'
	<tr bgcolor="'.$color.'">
	  <td align="center">'.$i.'</td>
	  <td align="center">'.$row['nume'].'</td>
	  <td align="center">'.$row['asociatie'].'</td>
	  <td align="center">'.$row['strada'].'</td>
	  <td align="center">'.$row['nr'].'</td>
	  <td align="center">'.$row['bloc'].'</td>
	  <td align="center">'.$row['scara'].'</td>
	  <td align="center"><a href="index.php?link=locatari_sub&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&loc_id='.$row['loc_id'].'">Alege</a></td>
	</tr>';

	$i++;
}
}
?>
<script type="text/javascript">
function check(id) {

	if(id.value == "Scrie aici....") {  id.value = '';  }

	}
</script>

<br  />
<form action="#" id="searchform" style="float:left">
	<input type="text" name="search" size="40" id="id_search" value="Scrie aici...." onclick="check(this)"  style="background-color: #fff; -moz-border-radius: 5px; -webkit-border-radius: 5px; border: 2px solid #0CC; padding: 5px; width:930px; font-size:16px; font-weight:bold; color:#999;" />
</form>
<table width="950" style="position:absolute; top:200px; background-color:white;" id="quicksearch">
<thead style="color:#FFFFFF">
<tr>
  <td bgcolor="#666666">ID</td>
  <td bgcolor="#666666">Nume</td>
  <td bgcolor="#666666">Asociatie</td>
  <td bgcolor="#666666">Strada</td>
  <td bgcolor="#666666">Numar</td>
  <td bgcolor="#666666">Bloc</td>
  <td bgcolor="#666666">Scara</td>
  <td bgcolor="#666666">&nbsp;</td>
  </tr>
</thead>
<tbody>
<?php  get_locatari();  ?>
</tbody>
</table>
<?php } ?>