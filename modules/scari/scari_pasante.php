<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=scari_pasante&asoc_id=" + value;	
}

function trimiteForm(){
	document.passante.submit();
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
/*******************  SELECTEAZA ASOCIATIA  *******************/
$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
}

/*******************  FUNCTIE CARE PUNE HEADERUL LA TABEL  *******************/
function putHeader($asocId){
	$setariAsociatie = "SELECT A.asociatie, S.pasante FROM asociatii A, asociatii_setari S WHERE A.asoc_id=S.asoc_id AND A.asoc_id=".$asocId;
	$setariAsociatie = mysql_query($setariAsociatie) or die ("Nu pot selecta setarile asociatiei<br />".mysql_error());
	
	$nrPasante = mysql_result($setariAsociatie, 0, 'pasante');
	$numeAsoc = mysql_result($setariAsociatie, 0, 'asociatie');
	
	echo '<tr>';
		echo '<td bgcolor="#000" style="color:#FFF">Asociatie: '.$numeAsoc.'</td>';
		
		for ($i=0; $i<=$nrPasante; $i++){
			echo '<td bgcolor="#000" style="color:#FFF">P'.$i.'</td>';
		}
	echo '</tr>';
	
}
/*******************  FUNCTIE CARE PUNE CONTENTUL IN TABEL  *******************/
function putContent($asocId){
	//aflu numarul total de pasante
	$setariAsociatie = "SELECT A.asociatie, S.pasante FROM asociatii A, asociatii_setari S WHERE A.asoc_id=S.asoc_id AND A.asoc_id=".$asocId;
	$setariAsociatie = mysql_query($setariAsociatie) or die ("Nu pot selecta setarile asociatiei<br />".mysql_error());
	$nrPasante = mysql_result($setariAsociatie, 0, 'pasante');

	//verific daca am setarile salvate
	$verificSalvat = "SELECT * FROM scari_pasante WHERE asoc_id=".$asocId;
	$verificSalvat = mysql_query($verificSalvat) or die ("Nu pot verifica Setari Pasante<br />".mysql_error());
	
	if (mysql_num_rows($verificSalvat) == 0){					//daca setarile pentru asociatia curenta nu sunt salvate
		$ordine = 0;
	
		$selectScari = "SELECT * FROM scari WHERE asoc_id=".$asocId;
		$selectScari = mysql_query($selectScari) or die ("Nu pot accesa tabela scari<br />".mysql_error());
		
		while ($peRand = mysql_fetch_array($selectScari)){
			$scara = $peRand['scara'];
			$bloc = $peRand['bloc'];
			$scaraId = $peRand['scara_id'];
	
			if ($ordine % 2 == 0){
					$culoare = "#DDDDDD";
			} else {
					$culoare = "#FFFFFF";	
			}
					
			echo '<tr bgcolor="'.$culoare.'">';
				echo '<td> Bloc '.$bloc.', scara '.$scara.'</td>';
					for ($i = 0; $i <= $nrPasante; $i++){
						echo '<td><input type="checkbox" name="'.$asocId.'-'.$i.'-'.$scaraId.'"></td>';
					}
			echo '</tr>';
			$ordine++;
		}
		
		echo '<tr bgcolor="'.$culoare.'" align="right"><td align="left">*) P0 - scara nu are pasant</td><td colspan="'.($nrPasante+1).'"><a style="cursor:pointer; color:#006EAB" onclick="trimiteForm()">Salveaza</a></td></tr>';
	} else {													//daca setarile pentru asociatia curenta sunt salvate
		//aici afisam doar pasantele salvate
		$pasante = mysql_result($verificSalvat, 0, 'pasante');
		$pasante = explode(',', $pasante);
		
		$ordine = 0;
		
		$scariAsoc = "SELECT * FROM scari WHERE asoc_id=".$asocId;
		$scariAsoc = mysql_query($scariAsoc) or die ("Nu pot afla scarile acestei asociatii<br />".mysql_error());
		
		while ($pScari = mysql_fetch_array($scariAsoc)){
			$scara = $pScari['scara'];
			$bloc = $pScari['bloc'];
			$scaraId = $pScari['scara_id'];
			
			if ($ordine % 2 == 0){
					$culoare = "#DDDDDD";
			} else {
					$culoare = "#FFFFFF";	
			}
			
			echo '<tr bgcolor="'.$culoare.'">';
				echo '<td> Bloc '.$bloc.', scara '.$scara.'</td>';
				for ($i=0; $i<=$nrPasante; $i++){
					$test = $i.'-'.$scaraId;
					if (in_array($test, $pasante)){
						echo '<td>&#x2713;</td>';
					} else {
						echo '<td>-</td>';
					}
				}
			echo '</tr>';
		}
	}		
}

/*******************  FUNCTIE CARE SALVEAZA DATELE  *******************/
if ($_POST['formular']== "ok"){
	if (count($_POST) == 1){
		echo '<font color="red" style="font-size:16px">Trebuie sa bifati cel putin o casuta</font><br />';
	} else {
		foreach ($_POST as $asoc=>$bifat){
			if ($bifat == "on"){
				$dateScara = explode("-", $asoc, 2);
				$asocId = $dateScara[0];
				$scari[] = $dateScara[1];
			}
		}	
		$finalizare = implode(",", $scari);
		
		$salvezDate = "INSERT INTO scari_pasante VALUES (null, '$asocId', '$finalizare')"; echo $salvezDate;
		$salvezDate = mysql_query($salvezDate) or die ("Nu pot salva datele legate de pasante<br />".mysql_error());
	}
}
unset($_POST);


?>


<div id="content" style="float:left;">
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC"> ( 1 ) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select onChange="select_asoc(this.value)">
				<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else 
					{
						$afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
						$afisAsoc = mysql_query($afisAsoc) or die ("Nu pot selecta asociatiile<br />".mysql_error());
			
						echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
					}
				?>
		        	<?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
</table>
</div>

<?php if($_GET['asoc_id'] != ""):?>
<form id="passante" name="passante" method="post" action="<?php echo "index.php?link=scari_pasante&asoc_id=".$_GET['asoc_id'];?>" >
	<input type="hidden" name="formular" value="ok" />
	
	<table width="750" style="float:left;  margin-top:10px; background-color:#BBB;">
		<thead>
			<?php putHeader($_GET['asoc_id']); ?>
		</thead>
		
			<?php putContent($_GET['asoc_id']); ?>
			
		<?php //citescApometre($_GET['asoc_id'], $_GET['scara_id']) ?>
	</table>
</form>
<?php	endif; ?>