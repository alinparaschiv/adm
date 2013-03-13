<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=facturiV2&asoc_id=" + value;
}

function select_factura(value,value2) {
	window.location = "index.php?link=facturiV2&asoc_id=" + value + "&tipFactura=" + value2;
}

function select_furnizor_1(value,value2,value3) {
	window.location = "index.php?link=facturiV2&asoc_id=" + value + "&tipFactura=" + value2 + "&furnizor=" + value3;
}


function select_scara_1(value,value2,value3) {
	window.location = "index.php?link=facturiV2&asoc_id=" + value + "&tipFactura=" + value2 + "&scara_id=" + value3;
}

function select_furnizor_2(value,value2,value3,value4) {
	window.location = "index.php?link=facturiV2&asoc_id=" + value + "&tipFactura=" + value2 + "&scara_id=" + value3 + "&furnizor=" + value4;
}

function canti(valoare){
	var iNou = document.getElementById('indexNou').value;
	var iVechi = document.getElementById('indexVechi').value;
	if ((iNou != null) && (iVechi != null)){
		document.getElementById(valoare).value = iNou - iVechi;	
	}
}

function verificaBifat(bifat, id){
	if (document.getElementById('cantitate').value == '')
	{
		document.getElementById('cantitate').value = 0;
	}
	if (bifat == true){
		document.getElementById('cantitate').value ++;
		document.getElementById(id).disabled = false;
	}
	else
	{
		document.getElementById('cantitate').value --;	
		document.getElementById(id).disabled = true;
		document.getElementById(id).value = '';
	}
}

function verificaBifat1(bifat){
	if (document.getElementById('cantitate').value == '')
	{
		document.getElementById('cantitate').value = 0;
	}
	if (bifat == true){
		document.getElementById('cantitate').value ++;
	}
	else
	{
		document.getElementById('cantitate').value --;	
	}
}

function prpu(valoare){
	var cant = document.getElementById('cantitate').value;
	var cost = document.getElementById('cost').value;
	
	if ((cant != null) && (cost != null)){
		document.getElementById(valoare).value = cost/cant;	
	}
}
</script>

<?php

/*******************  FUNCTII  *******************/
if ($_POST['insereaza'] == 'OK'){

// Informatii Generale
	$asocId = mysql_real_escape_string($_GET['asoc_id']);
	$scaraId = mysql_real_escape_string($_GET['scara_id']);
	$tipFactura = mysql_real_escape_string($_GET['tipFactura']);
	$tipServiciu = mysql_real_escape_string($_GET['tipServiciu']);
	$dataInserare = date('d-m-Y');

//  Informatii Factura
	$numarFactura = mysql_real_escape_string($_POST['numarFactura']);
	$serieFactura = mysql_real_escape_string($_POST['serieFactura']);
	$dataEmitere = mysql_real_escape_string($_POST['dataEmitere']);
	$dataScadenta = mysql_real_escape_string($_POST['dataScadenta']);
	$observatii = mysql_real_escape_string($_POST['observatii']);
	
//  Informatii Asociatie
	$debite = mysql_real_escape_string($_POST['debite']);
	$penalizari = mysql_real_escape_string($_POST['penalizari']);
	$nrRate = mysql_real_escape_string($_POST['nrRate']);
	$luna = mysql_real_escape_string($_POST['luna']);

//  Facturi cu Indecsi
	$verif = "SELECT S.cu_indecsi FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=".$_GET['furnizor']." AND F.fur_id=FS.fur_id AND S.serv_id=FS.serv_id"; echo $verif;
//	$verif = "SELECT * FROM servicii WHERE serv_id=".$_GET['tipServiciu'];
	$verif = mysql_query($verif) or die ("Nu pot selecta serviciul ales<br />".mysql_error());
	
	if (mysql_result($verif, 0, 'cu_indecsi') == 'da'){
		$indexNou = mysql_real_escape_string($_POST['indexNou']);
		$indexVechi = mysql_real_escape_string($_POST['indexVechi']);
		$cantitate = mysql_real_escape_string($_POST['cantitate']);		//nu apare in tabela
		$cost = mysql_real_escape_string($_POST['cost']);
		$ppu = mysql_real_escape_string($_POST['ppu']);				//apare in servicii
	} else {
		$indexNou = null;
		$indexVechi = null;
		$cantitate = mysql_real_escape_string($_POST['cantitate']); 		//nu apare in tabela
		$cost = mysql_real_escape_string($_POST['cost']);
		$ppu = mysql_real_escape_string($_POST['ppu']); 			//apare in servicii
	}

//  Facturi cu Pasant
    $alegServiciu = "SELECT S.serviciu FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=".$_GET['furnizor']." AND F.fur_id=FS.fur_id AND S.serv_id=FS.serv_id";
//	$alegServiciu = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
	$alegServiciu = mysql_query($alegServiciu) or die ("Nu pot accesa serviciile<br />".mysql_error());
		
	if (mysql_result($alegServiciu, 0, 'serviciu') == 'apa rece' || mysql_result($alegServiciu, 0, 'serviciu') == 'apa calda'){
		$arePasant = "SELECT * FROM scari_setari WHERE scara_id=".$scaraId;
		$arePasant = mysql_query($arePasant) or die ("Nu pot accesa setarile scarii<br />".mysql_error());
				
		if (mysql_result($arePasant, 0, 'pasant') == "da"){
                        $tipApa = mysql_result($alegServiciu, 0, 'serviciu');
			$tipApa = explode(' ', $tipApa);
                        
                        if ($tipApa[1] == "rece"){
                                $pasant_rece = mysql_real_escape_string($_POST["pasant_rece"]);
                                $pasant_calda = null;
                        } else
                        if ($tipApa[1] == "calda"){
                                $pasant_calda = mysql_real_escape_string($_POST["pasant_calda"]);
                                $pasant_rece = null;
                        }
                } else {
                        $pasant_rece = null;
                        $pasant_calda = null;
                }
        }

//  Facturi pe Apartamente
if ($tipFactura == 3){
        $nrLoc = $_POST['nrLoc'];
        $i = 1;

        while ($i <= $nrLoc){
                $locatar = $_POST['loc'.$i];
				$cost = $_POST['cost'.$i];
                if (($locatar <> null) && ($cost <> null)){
                        $array[] = $locatar; 
						$array1[] = $cost;
                }
        	$i++;
        }
	$locatari = implode(",",$array);
	$cost = implode(",",$array1);
}

//  Facturi pe Locatari
if ($tipFactura == 4){
        $nrLoc = $_POST['nrLoc'];
        $i = 1;

        while ($i <= $nrLoc){
                $locatar = $_POST['loc'.$i];
                if ($locatar <> null){
                        $array[] = $locatar; 
                }
        $i++;
        }
	$locatari = implode(",",$array);
} 

$sql = "INSERT INTO facturi VALUES (null, '$dataInserare', '$asocId', '$scaraId', '$tipFactura', '$tipServiciu', '$numarFactura', '$serieFactura', '$dataEmitere', '$dataScadenta', '$debite', '$penalizari', '$nrRate', '$luna', '$indexNou', '$indexVechi', '$cantitate', '$cost', '$ppu', '$pasant_rece', '$pasant_cald', '$locatari', '$observatii', '0')";
$sql = mysql_query($sql) or die ("Nu pot insera factura<br />".mysql_error());

//daca asta se face 1, afisez chitanta introdusa
$amIntrodus = 1;
}
/*******************  SELECTEAZA ASOCIATIA SI TIPUL DE SERVICIU  *******************/
$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2) or die(mysql_error());
	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}
}

$tip = "SELECT * FROM tip_factura";
$tip = mysql_query($tip) or die("Nu pot scoate tipurile de facturi<br />".mysql_error());
while ($row = mysql_fetch_array($tip)){
	$tipFactura .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

//asociatii
if ($_GET['asoc_id']<>null){
	$sFurA = "SELECT A.fur_id, F.furnizor, F.fur_id FROM asociatii_furnizori A, furnizori F WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id";
	$sFurA = mysql_query($sFurA) or die("Nu pot selecta furnizorii pt aceasta asociatie<br />".mysql_error());
	while($furnA = mysql_fetch_array($sFurA)){
		$furnizori1 .= '<option value="'.$furnA['fur_id'].'">'.$furnA['furnizor'].'</option>';
	}
}

//scari
if ($_GET['asoc_id']<>null && $_GET['scara_id']<>null){
	$sFurS = "SELECT F.furnizor, F.fur_id FROM scari_furnizori S, furnizori_servicii FS, servicii SE, furnizori F WHERE S.scara_id=".$_GET['scara_id']." AND F.fur_id=S.fur_id AND S.fur_id=FS.fur_id AND FS.serv_id=SE.serv_id AND SE.nivel=2";
	$sFurS = mysql_query($sFurS) or die("Nu pot selecta furnizorii pt aceasta scara<br />".mysql_error());
	while($furnS = mysql_fetch_array($sFurS)){
		$furnizori2 .= '<option value="'.$furnS['fur_id'].'">'.$furnS['furnizor'].'</option>';
	}
}

//apartamente
if ($_GET['asoc_id']<>null && $_GET['scara_id']<>null){
	$sFurS = "SELECT F.furnizor, F.fur_id FROM scari_furnizori S, furnizori_servicii FS, servicii SE, furnizori F WHERE S.scara_id=".$_GET['scara_id']." AND F.fur_id=S.fur_id AND S.fur_id=FS.fur_id AND FS.serv_id=SE.serv_id AND SE.nivel=3";
	$sFurS = mysql_query($sFurS) or die("Nu pot selecta furnizorii pt aceasta scara<br />".mysql_error());
	while($furnS = mysql_fetch_array($sFurS)){
		$furnizori3 .= '<option value="'.$furnS['fur_id'].'">'.$furnS['furnizor'].'</option>';
	}
}

//locatari
if ($_GET['asoc_id']<>null && $_GET['scara_id']<>null){
	$sFurS = "SELECT F.furnizor, F.fur_id FROM scari_furnizori S, furnizori_servicii FS, servicii SE, furnizori F WHERE S.scara_id=".$_GET['scara_id']." AND F.fur_id=S.fur_id AND S.fur_id=FS.fur_id AND FS.serv_id=SE.serv_id AND SE.nivel=4";
	$sFurS = mysql_query($sFurS) or die("Nu pot selecta furnizorii pt aceasta scara<br />".mysql_error());
	while($furnS = mysql_fetch_array($sFurS)){
		$furnizori4 .= '<option value="'.$furnS['fur_id'].'">'.$furnS['furnizor'].'</option>';
	}
}



if ($_GET['tipFactura'] != ''){
	$serv = "SELECT * FROM servicii WHERE nivel=".$_GET['tipFactura'];
	$serv = mysql_query($serv) or die("Nu pot selecta serviciile<br />".mysql_error());
	while ($row = mysql_fetch_array($serv)){
		$tipServiciu .= '<option value="'.$row[0].'">'.$row[1].'</option>';
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
	#print {float:left; margin-left:700px; margin-top:15px;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr align="center">
		<td width="388" bgcolor="#CCCCCC" colspan="2"><strong>Configurare Factura</strong></td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">( 1 ) Alegeti asociatia:</td>
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
	<?php if ($_GET['asoc_id'] != ""){ ?>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">( 2 ) Alegeti tipul de factura:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
        	<select onChange="select_factura(<?php  echo $_GET['asoc_id']; ?>,this.value)">
				<?php if($_GET['tipFactura']==null)  { echo '<option value="">----Alege----</option>';    }  else
					{
						$afisFactura = "SELECT * FROM tip_factura WHERE id=".$_GET['tipFactura'];
						$afisFactura = mysql_query($afisFactura) or die ("Nu pot selecta tipul de factura<br />".mysql_error());

						echo '<option value="">'.mysql_result($afisFactura, 0, 'tip_factura').'</option>';
					}
				?>
        		    <?php  echo $tipFactura; ?>
        	</select>
        </td>
	</tr>
	<?php }

	if ($_GET['asoc_id'] != "" && $_GET['tipFactura'] != "") {
		$tipulFacturii = $_GET['tipFactura'];

		$afluTipul = "SELECT * FROM tip_factura WHERE id=".$tipulFacturii;
		$afluTipul = mysql_query($afluTipul) or die ("Nu pot afla tipul facturii 1<br />".mysql_error());

		$caz = $_GET['tipFactura'];
		
		//asociatie
		if ($caz == 1){ ?>
			<tr>
				<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti furnizorul:</td>
				<td width="215" align="left" bgcolor="#CCCCCC">
					<select style="width:125px" onChange="select_furnizor_1(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>,this.value)">
						<?php if($_GET['furnizor']==null)  { echo '<option value="">----Alege----</option>';    }  else
							{
								$afisFurnizori = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor'];
								$afisFurnizori = mysql_query($afisFurnizori) or die ("NU pot selecta furnizorul pentru aceasta asociatie<br />".mysql_error());

								echo '<option value="">'.mysql_result($afisFurnizori, 0, 'furnizor').'</option>';
							}
						?>
		                <?php  echo $furnizori1; ?>
					</select>
				</td>
			</tr>
		<?php } else 
		
		//scara
		if ($caz == 2){ 
		?>
			<tr>
				<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti scara:</td>
				<td width="215" align="left" bgcolor="#CCCCCC">
					<select style="width:125px" onChange="select_scara_1(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, this.value)">
						<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
							{
								$afisScara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
								$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scara<br />".mysql_error());

								echo '<option value="">Scara '.mysql_result($afisScara, 0, 'scara').'</option>';
							}
						?>
						<?php  echo $scari; ?>
					</select>
				</td>
			</tr>
		<?php if ($_GET['scara_id'] != ""){ ?>

				<tr>
					<td width="173" align="left" bgcolor="#CCCCCC">( 4 ) Alegeti furnizorul:</td>
					<td width="215" align="left" bgcolor="#CCCCCC">
						<select style="width:125px" onChange="select_furnizor_2(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, <?php echo $_GET['scara_id']; ?>,this.value)">
							<?php if($_GET['furnizor']==null)  { echo '<option value="">----Alege----</option>';    }  else
								{
									$afisFurnizori = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor'];
									$afisFurnizori = mysql_query($afisFurnizori) or die ("NU pot selecta furnizorul pentru aceasta scara<br />".mysql_error());

									echo '<option value="">'.mysql_result($afisFurnizori, 0, 'furnizor').'</option>';
								}
								?>
							<?php  echo $furnizori2; ?>
						</select>
					</td>
				</tr>
			<?php } ?>
		<?php } else 
		
		//apartament
		if ($caz == 3){ 
		?>
			<tr>
				<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti scara:</td>
				<td width="215" align="left" bgcolor="#CCCCCC">
					<select style="width:125px" onChange="select_scara_1(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, this.value)">
						<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
							{
								$afisScara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
								$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scara<br />".mysql_error());

								echo '<option value="">Scara '.mysql_result($afisScara, 0, 'scara').'</option>';
							}
						?>
						<?php  echo $scari; ?>
					</select>
				</td>
			</tr>
		<?php if ($_GET['scara_id'] != ""){ ?>

				<tr>
					<td width="173" align="left" bgcolor="#CCCCCC">( 4 ) Alegeti furnizorul:</td>
					<td width="215" align="left" bgcolor="#CCCCCC">
						<select style="width:125px" onChange="select_furnizor_2(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, <?php echo $_GET['scara_id']; ?>,this.value)">
							<?php if($_GET['furnizor']==null)  { echo '<option value="">----Alege----</option>';    }  else
								{
									$afisFurnizori = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor'];
									$afisFurnizori = mysql_query($afisFurnizori) or die ("NU pot selecta furnizorul pentru aceasta scara<br />".mysql_error());

									echo '<option value="">'.mysql_result($afisFurnizori, 0, 'furnizor').'</option>';
								}
								?>
							<?php  echo $furnizori3; ?>
						</select>
					</td>
				</tr>
			<?php } ?>
		<?php } else 
		//locatar
		if ($caz == 4){ 
		?>
			<tr>
				<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti scara:</td>
				<td width="215" align="left" bgcolor="#CCCCCC">
					<select style="width:125px" onChange="select_scara_1(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, this.value)">
						<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
							{
								$afisScara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
								$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scara<br />".mysql_error());

								echo '<option value="">Scara '.mysql_result($afisScara, 0, 'scara').'</option>';
							}
						?>
						<?php  echo $scari; ?>
					</select>
				</td>
			</tr>
		<?php if ($_GET['scara_id'] != ""){ ?>

				<tr>
					<td width="173" align="left" bgcolor="#CCCCCC">( 4 ) Alegeti furnizorul:</td>
					<td width="215" align="left" bgcolor="#CCCCCC">
						<select style="width:125px" onChange="select_furnizor_2(<?php  echo $_GET['asoc_id']; ?>, <?php echo $_GET['tipFactura']; ?>, <?php echo $_GET['scara_id']; ?>,this.value)">
							<?php if($_GET['furnizor']==null)  { echo '<option value="">----Alege----</option>';    }  else
								{
									$afisFurnizori = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor'];
									$afisFurnizori = mysql_query($afisFurnizori) or die ("NU pot selecta furnizorul pentru aceasta scara<br />".mysql_error());

									echo '<option value="">'.mysql_result($afisFurnizori, 0, 'furnizor').'</option>';
								}
								?>
							<?php  echo $furnizori4; ?>
						</select>
					</td>
				</tr>
			<?php } ?>
		<?php }
	}
	?>

</table>
</div>

<?php
	$potAfisa = 0;
	if ($_GET['asoc_id'] != "" && $_GET['tipFactura'] != ""){
		if ($_GET['tipFactura'] == "1" && $_GET['furnizor'] != ""){
			$potAfisa = 1;
		} else
		if ($_GET['scara_id'] != "" && $_GET['furnizor'] != ""){
			$potAfisa = 1;
		}
	}

	if ($potAfisa == 1){
?>

<?php
	/**************************  ASTA E TABELUL AFISAT  ****************************/
?>
<form action="" method="post">
<input type="hidden" name="insereaza" value="OK" />
<div id="print">
  	<a href="#">printeaza</a>
</div>
<table width="750" style="float:left;  margin-top:10px; background-color:#BBB;">
<?php
	$furnizor = $_GET['furnizor'];
	$tipPlata = $_GET['tipFactura'];
	
	$afluFurnizor = "SELECT * FROM furnizori WHERE fur_id=".$furnizor;
	$afluFurnizor = mysql_query($afluFurnizor) or die ("Nu pot afla furnizorul pentru afisarea tabelului<br />".mysql_error());

	$afluServiciu = "SELECT servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
	$afluServiciu = mysql_query($afluServiciu) or die ("Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());
	
	echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($afluFurnizor, 0, 'furnizor').' - Serviciu facturat: '.mysql_result($afluServiciu, 0, 'serviciu').'</strong></td></tr>';
	echo '<tr bgcolor="#CCC"><td colspan="5"> &nbsp; </td></tr>';
	
	// ::daca introduc factura pentru APA RECE::
	if (mysql_result($afluServiciu, 0, 'serviciu') == 'apa rece'){ 
?>
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Numar Factura</td>
			<td width="125px">Serie Factura</td>
			<td width="125px">Data Emiterii</td>
			<td width="125px">Data Scadenta</td>
			<td width="125px">Valoare Factura</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="numarFactura" /></td>
			<td ><input type="text" name="serieFactura" /></td>
			<td ><input type="text" name="dataEmitere" class="datepicker" /></td>
        	<td ><input type="text" name="dataScadenta" class="datepicker" /></td>
        	<td ><input type="text" name="valoareFactura" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Debite</td>
			<td width="125px">Penalizari</td>
			<td width="125px">Nr. Rate</td>
			<td width="125px">Luna</td>
			<td width="125px">Observatii</td>
		</tr>  
		
		<tr>
			<td ><input type="text" name="debite" /></td>
			<td ><input type="text" name="penalizari" /></td>
			<td ><input type="text" name="nrRate" /></td>
			<td >
				<select name="luna" style="width:125px" >
					<?php                        
						if ($_POST['data'] != '' && $_POST['data'] != 'nimic') 
							echo '<option selected="selected" value="'.$_POST['data'].'">'.$_POST['data'].'</option>';                            
						for ($i=0; $i<12; $i++) 
							echo '<option  value="'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'">'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'</option>';
					?>
				</select>
			</td>
			<td><input type="text" name="observatii" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td colspan="2">Pasante, scari (consum - m<sup>3</sup>)</td>
			<td colspan="3">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<?php
				$pas = "SELECT * FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
				$pas = mysql_query($pas) or die ("Nu pot afla setarile asociatiei pentru afisarea pasantelor<br />".mysql_error());
				
				$nrPasante = mysql_result($pas, 0, 'pasante');
								
				$pas = "SELECT * FROM scari_pasante WHERE asoc_id=".$_GET['asoc_id'];
				$pas = mysql_query($pas) or die ("Nu pot afla setarile pasantelor<br />".mysql_error());
				
				$setPas = array();
				if (mysql_num_rows($pas)){
					$setPas = mysql_result($pas, 0, 'pasante');
					$setPas = explode(",", $setPas);
				}
				
				for ($i=0; $i<count($setPas); $i++){
					$setPas[$i] = explode("-", $setPas[$i]);
				}
				 
				foreach ($setPas as $index){
					foreach ($index as $scara=>$val){
						if ($scara == 0){
							$pasantul = $val;
						} else {
							$scriuBloc = "SELECT * FROM scari WHERE scara_id=".$val;
							$scriuBloc = mysql_query($scriuBloc) or die ("Nu pot afla scara si blocul<br />".mysql_error());
							
							$verific[$pasantul] .= "Blocul ".mysql_result($scriuBloc, 0, 'bloc').", scara ".mysql_result($scriuBloc, 0, 'scara')."; ";
						}
					}
				}
				
				for ($i=0; $i<=nrPasante; $i++){
					echo "<br />";
				}
				
				for ($i=0; $i<=$nrPasante; $i++){
					if ($i % 2 == 0) { $culoare = "#DDD"; } else { $culoare = "#EEE"; }
						if ($verific[$i] == ""){
							$afisez = "Nu sunt scari pentru aceasta optiune";
						} else
						if (count($verific[$i]) == 1){
							$afisez = "Valoarea pasantului pentru: ".$verific[$i];
						} else {
							$afisez = "Valoarea pasantului pentru: ".$verific[$i];
						}
					echo '<tr bgcolor="'.$culoare.'">';
						echo '<td>Pasant '.$i.'</td>';
						echo '<td><input type="text" name="'.$_GET['asoc_id'].'-'.$i.'"/></td>';
						echo '<td colspan="3" align="left">*) '.$afisez.'</td>';
					echo '</tr>';
				}
			?>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td >Bloc, Scara</td>
			<td >Index Nou</td>
			<td >Index Vechi</td>
			<td >Diferenta</td>
			<td >&nbsp;</td>
		</tr>

		<tr bgcolor="#DDD">
			<?php
				$sc = "SELECT * FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
				$sc = mysql_query($sc) or die ("Nu pot afla setarile asociatiei pentru afisarea pasantelor<br />".mysql_error());
				
				$nrScari = mysql_result($sc, 0, 'nr_scari');
				
				for ($i=1; $i<=$nrScari; $i++){
					if ($i % 2 == 0) { $culoare = "#DDD"; } else { $culoare = "#EEE"; }
					echo '<tr bgcolor="'.$culoare.'">';
						$afisScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
						$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());
						
						echo '<td>Blocul '.mysql_result($afisScara, ($i-1), 'bloc').', scara '.mysql_result($afisScara, ($i-1), 'scara').'</td>';
						echo '<td><input type="text" name="IN-'.mysql_result($afisScara, ($i-1), 'scara_id').'"/></td>';
						echo '<td><input type="text" name="IV-'.mysql_result($afisScara, ($i-1), 'scara_id').'"/></td>';
						echo '<td><input type="text" name="DIF-'.mysql_result($afisScara, ($i-1), 'scara_id').'"/></td>';
						echo '<td>&nbsp;</td>';
					echo '</tr>';
				}
			?>
		</tr>
	
		<tr >
			<td colspan="4">&nbsp;</td>
			<td ><input type="submit" value="Salveaza" /></td>
		</tr>


			
<?php
	} 
	else if (mysql_result($afluServiciu, 0, 'serviciu') == 'iluminat'){

		$contoare = "SELECT * FROM scari_setari WHERE scara_id=".$_GET['scara_id'];
		$contoare = mysql_query($contoare) or die ("Nu pot selecta setarile scarii pentru a afla numarul de contoare<br />".mysql_error());
		
		$tipContor = array("General", "Lift", "Centrala");
		
		$nrContoare[] = 1;
		
		$contorLift = mysql_result($contoare, 0, 'contor_lift');
		$nrContoare[] = $contorLift;
		
		$contorCentrala = mysql_result($contoare, 0, 'contor_centrala');
		$nrContoare[] = $contorCentrala;
		
		for ($cont=0; $cont<count($nrContoare); $cont++){
			if ($nrContoare[$cont] == 1){
		
?>

		<tr bgcolor="#AAA" style="color:#000;">
			<td colspan="5"><strong>Contoar <?php echo $tipContor[$cont]; ?></strong></td>
		</tr>

		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Numar Factura</td>
			<td width="125px">Serie Factura</td>
			<td width="125px">Data Emiterii</td>
			<td width="125px">Data Scadenta</td>
			<td width="125px">Observatii</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="numarFactura-<?php echo $cont; ?>" /></td>
			<td ><input type="text" name="serieFactura-<?php echo $cont; ?>" /></td>
			<td ><input type="text" name="dataEmitere-<?php echo $cont; ?>" class="datepicker" /></td>
        	<td ><input type="text" name="dataScadenta-<?php echo $cont; ?>" class="datepicker" /></td>
        	<td ><input type="text" name="observatii-<?php echo $cont; ?>" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Debite</td>
			<td width="125px">Penalizari</td>
			<td width="125px">Nr. Rate</td>
			<td width="125px">Luna</td>
			<td width="125px">&nbsp;</td>
		</tr>  
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="debite-<?php echo $cont; ?>" /></td>
			<td ><input type="text" name="penalizari-<?php echo $cont; ?>" /></td>
			<td ><input type="text" name="nrRate-<?php echo $cont; ?>" /></td>
			<td >
				<select name="luna-<?php echo $cont; ?>" style="width:125px" >
					<?php                        
						if ($_POST['data'] != '' && $_POST['data'] != 'nimic') 
							echo '<option selected="selected" value="'.$_POST['data'].'">'.$_POST['data'].'</option>';                            
						for ($i=0; $i<12; $i++) 
							echo '<option  value="'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'">'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'</option>';
					?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Index Vechi</td>
			<td width="125px">Index Nou</td>
			<td width="125px">Consum</td>
			<td width="125px">Valoare Factura</td>
			<td width="125px">Pret/unitate</td>
		</tr>  
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="indexVechi-<?php echo $cont; ?>" id="indexVechi-<?php echo $cont; ?>" onchange="canti(this.id);" /></td>
			<td ><input type="text" name="indexNou-<?php echo $cont; ?>" id="indexNou-<?php echo $cont; ?>" onchange="canti(this.id);"/></td>
			<td ><input type="text" name="consum-<?php echo $cont; ?>" id="consum-<?php echo $cont; ?>" readonly="readonly" value="" onchange="prpu(this.id);" /></td>
			<td ><input type="text" name="cost-<?php echo $cont; ?>" id="cost-<?php echo $cont; ?>" onchange="prpu(this.id);"/></td>
			<td ><input type="text" name="ppu-<?php echo $cont; ?>" id="ppu-<?php echo $cont; ?>"/></td>
		</tr>
		
	<?php
			}
		}
	?>
		
		<tr >
			<td colspan="4">&nbsp;</td>
			<td ><input type="submit" value="Salveaza" /></td>
		</tr>

<?php
	}
	else if (mysql_result($afluServiciu, 0, 'serviciu') == "apa calda"){
?>	
		<tr bgcolor="#DDD" style="color:#000">
			<td colspan="5"><strong>Apa Rece pentru Apa Calda - ApaVital</strong></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Numar Factura</td>
			<td width="125px">Serie Factura</td>
			<td width="125px">Data Emiterii</td>
			<td width="125px">Data Scadenta</td>
			<td width="125px">Observatii</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="numarFactura" /></td>
			<td ><input type="text" name="serieFactura" /></td>
			<td ><input type="text" name="dataEmitere" class="datepicker" /></td>
        	<td ><input type="text" name="dataScadenta" class="datepicker" /></td>
        	<td ><input type="text" name="observatii" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Debite</td>
			<td width="125px">Penalizari</td>
			<td width="125px">Nr. Rate</td>
			<td width="125px">Luna</td>
			<td width="125px">&nbsp;</td>
		</tr>  

		<tr>
			<td ><input type="text" name="debite" /></td>
			<td ><input type="text" name="penalizari" /></td>
			<td ><input type="text" name="nrRate" /></td>
			<td >
				<select name="luna" style="width:125px" >
					<?php                        
						if ($_POST['data'] != '' && $_POST['data'] != 'nimic') 
							echo '<option selected="selected" value="'.$_POST['data'].'">'.$_POST['data'].'</option>';                            
						for ($i=0; $i<12; $i++) 
							echo '<option  value="'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'">'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'</option>';
					?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Consum ( m<sup>3</sup> )</td>
			<td width="125px">Valoare ( RON )</td>
			<td colspan="3">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="consum" /></td>
			<td ><input type="text" name="valoare" /></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		
		<tr  bgcolor="#CCC" style="color:#FFF">
			<td colspan="5">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#DDD" style="color:#000">
			<td colspan="5"><strong>Agent Termic pentru Apa Calda - CET</strong></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Numar Factura</td>
			<td width="125px">Serie Factura</td>
			<td width="125px">Data Emiterii</td>
			<td width="125px">Data Scadenta</td>
			<td width="125px">Observatii</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="numarFactura" /></td>
			<td ><input type="text" name="serieFactura" /></td>
			<td ><input type="text" name="dataEmitere" class="datepicker" /></td>
        	<td ><input type="text" name="dataScadenta" class="datepicker" /></td>
        	<td ><input type="text" name="observatii" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Debite</td>
			<td width="125px">Penalizari</td>
			<td width="125px">Nr. Rate</td>
			<td width="125px">Luna</td>
			<td width="125px">&nbsp;</td>
		</tr>  

		<tr bgcolor="#DDD">
			<td ><input type="text" name="debite" /></td>
			<td ><input type="text" name="penalizari" /></td>
			<td ><input type="text" name="nrRate" /></td>
			<td >
				<select name="luna" style="width:125px" >
					<?php                        
						if ($_POST['data'] != '' && $_POST['data'] != 'nimic') 
							echo '<option selected="selected" value="'.$_POST['data'].'">'.$_POST['data'].'</option>';                            
						for ($i=0; $i<12; $i++) 
							echo '<option  value="'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'">'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'</option>';
					?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Index Nou</td>
			<td width="125px">Index Vechi</td>
			<td width="125px">Diferenta</td>
			<td width="125px">Pret</td>
			<td width="125px">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="indexNou" /></td>
			<td ><input type="text" name="indexVechi" /></td>
			<td ><input type="text" name="diferenta" /></td>
        	<td ><input type="text" name="pret" /></td>
        	<td >&nbsp;</td>
		</tr>
		
		<tr  bgcolor="#CCC" style="color:#FFF">
			<td colspan="5">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Valoare Finala</td>
			<td width="125px">Cantitate Facturata</td>
			<td width="125px">Pret / m<sup>3</sup></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="valoareFinala" /></td>
			<td ><input type="text" name="cantitateFacturata" /></td>
			<td ><input type="text" name="pretFinal" /></td>
        	<td colspan="2">&nbsp;</td>
		</tr>
		
		<tr >
			<td colspan="4">&nbsp;</td>
			<td ><input type="submit" value="Salveaza" /></td>
		</tr>
<?php
	}
	else {
?>
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Numar Factura</td>
			<td width="125px">Serie Factura</td>
			<td width="125px">Data Emiterii</td>
			<td width="125px">Data Scadenta</td>
			<td width="125px">Observatii</td>
		</tr>
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="numarFactura" /></td>
			<td ><input type="text" name="serieFactura" /></td>
			<td ><input type="text" name="dataEmitere" class="datepicker" /></td>
        	<td ><input type="text" name="dataScadenta" class="datepicker" /></td>
        	<td ><input type="text" name="observatii" /></td>
		</tr>
		
		<tr bgcolor="#000" style="color:#FFF">
			<td width="125px">Debite</td>
			<td width="125px">Penalizari</td>
			<td width="125px">Nr. Rate</td>
			<td width="125px">Luna</td>
			<td width="125px">&nbsp;</td>
		</tr>  
		
		<tr bgcolor="#DDD">
			<td ><input type="text" name="debite" /></td>
			<td ><input type="text" name="penalizari" /></td>
			<td ><input type="text" name="nrRate" /></td>
        	<td >
				<select name="luna" style="width:125px" >
					<?php                        
						if ($_POST['data'] != '' && $_POST['data'] != 'nimic') 
							echo '<option selected="selected" value="'.$_POST['data'].'">'.$_POST['data'].'</option>';                            
						for ($i=0; $i<12; $i++) 
							echo '<option  value="'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'">'.date('m-Y', mktime(0,0,0,date("m")-$i, 1, date("Y"))).'</option>';
					?>
				</select>
			</td>
        	<td >&nbsp;</td>
		</tr>
<?php
// here comes the custom code
//cazuri de tratat: gaz, incalzire
//					fonduri
//					servicii

$verif = "SELECT servicii.serviciu, servicii.cu_indecsi FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
$verif = mysql_query($verif) or die ("Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());

if (mysql_result($verif, 0, 'cu_indecsi') == "da"){
?>

	<tr bgcolor="#000" style="color:#FFF">
		<td >Index Nou</td>
		<td >Index Vechi</td>
		<td >Diferenta</td>
		<td >Cost</td>
		<td >Pret/unitate de masura</td>
	</tr>  
	
	<tr bgcolor="#DDD">
    	<td ><input type="text" name="indexNou" id="indexNou" /></td>
		<td ><input type="text" name="indexVechi" id="indexVechi" /></td>
		<td ><input type="text" name="cantitate" id="cantitate" /></td>
        <td ><input type="text" name="cost" id="cost" /></td>
        <td ><input type="text" name="ppu" id="ppu" /></td>
    </tr>

<?php } else { 
	if (mysql_result($verif, 0, 'cu_indecsi') == 'nu'){ 
?>
	
	<tr bgcolor="#000" style="color:#FFF">
		<td >Cantitate</td>
		<td >Cost</td>
		<td >Pret/unitate de masura</td>
		<td colspan="2">&nbsp;</td>
	</tr>  
	
	<tr bgcolor="#DDD">
		<td ><input type="text" name="cantitate" id="cantitate" /></td>
       	<td ><input type="text" name="cost" id="cost" /></td>
       	<td ><input type="text" name="ppu" id="ppu" /></td>
		<td colspan="2">&nbsp;</td>
    </tr>



<?php
		}
	}
	
	//factura pe apartamente
	if ($_GET['tipFactura'] == 3){
	
	$locatari = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET["scara_id"];
	$locatari = mysql_query($locatari); 
?>
	<tr bgcolor="#000" style="color:#FFF">
		<td colspan="2">Locatar</td>  
		<td >Cost/Locatar</td>
        <td colspan="2">&nbsp;</td>
	</tr>
	
<?php 
	if (mysql_num_rows($locatari) != 0) {
                $nrLoc = 0;
		while($row = mysql_fetch_array($locatari)){
		$nrLoc++;
		echo '<tr bgcolor="#DDD">';
			echo '<td>';
				echo '<input type="checkbox" onclick="verificaBifat(this.checked, \'cost'.$nrLoc.'\');" name="loc'.$nrLoc.'" value="'.$row['loc_id'].'">';
			echo '</td>';
			echo '<td>'.$row['nume'].'</td>';
			echo '<td ><input type="text" id="cost'.$nrLoc.'" name="cost'.$nrLoc.'" disabled /></td>';
			echo '<td colspan="2">&nbsp;</td>';
		echo '</tr>';
		}
        echo '<input type="hidden" name="nrLoc" value="'.$nrLoc.'" />';
	}
	else
	{
		echo '<tr> <td colspan="5"> Nu sunt locatari inregistrati la aceasta asociatie </td> </tr>';
	}
}

//facturi pe locatari
if ($_GET['tipFactura'] == 4){
	$locatari = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET["scara_id"];
	$locatari = mysql_query($locatari) or die ("Ceva nu a mers bine in selectia locatarilor pentru fondul special<br />".mysql_error()); ?>
    
	<tr bgcolor="#000" style="color:#FFF">
		<td colspan="2">Locatar</td>  
		<td colspan="3">&nbsp;</td>
	</tr>

<?php 
	if (mysql_num_rows($locatari) != 0) {
                $nrLoc = 0;
		while($row = mysql_fetch_array($locatari)){
		$nrLoc++;
		echo '<tr bgcolor="#DDD">';
			echo '<td>';
				echo '<input type="checkbox" onclick="verificaBifat1(this.checked);" name="loc'.$nrLoc.'" value="'.$row['loc_id'].'">';
			echo '</td>';
			echo '<td>'.$row['nume'].'</td>';
			echo '<td colspan="3">&nbsp;</td>'; 
		echo '</tr>';
		}
        echo '<input type="hidden" name="nrLoc" value="'.$nrLoc.'" />';
	}
	else
	{
		echo '<tr> <td colspan="5"> Nu sunt locatari inregistrati la aceasta asociatie </td> </tr>';
	}

	} ?>
	
		<tr >
			<td colspan="4">&nbsp;</td>
			<td ><input type="submit" value="Salveaza" /></td>
		</tr>
		
<?php
	}
?>
</table>
</form>
<?php } ?>