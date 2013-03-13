<script type="text/css">
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
</script>

<?php
if ($_POST['deProcesat'] == "OK"){
	$procesare = "SELECT * FROM facturi WHERE procesata=0 AND fact_id=".$_POST['factura'];
	$procesare = mysql_query($procesare) or die ("#Procesare Facturi: 10 -- Nu pot selecta factura pentru procesare<br />".mysql_error());
	
	if (mysql_num_rows($procesare) == 0){
		echo "Nu sunt facturi neprocesare";
	} else {
		while ($proc = mysql_fetch_array($procesare)){
		// Informatii Generale
			$factId = $proc['fact_id'];
			$tipFactura = $proc['tipFactura'];
			$tipServiciu = $proc['tipServiciu'];
			$asocId = $proc['asoc_id'];
			if ($tipFactura == 1){
				$scaraId = $proc['scara_id'];
			}
		
		//  Informatii Factura
			$numarFactura = $proc['numarFactura'];
			$serieFactura = $proc['serieFactura'];
			$dataEmitere = $proc['dataEmitere'];
			$dataScadenta = $proc['dataScadenta'];
			$observatii = $proc['observatii'];
			
		//  Informatii Asociatie
			$debite = $proc['$debite'];
			$penalizari = $proc['penalizari'];
			$nrRate = $proc['nrRate'];
			$luna = $proc['luna'];
		
		//  Facturi cu Indecsi (sau NU)
			$verif = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
			$verif = mysql_query($verif) or die ("Nu pot selecta serviciul ales<br />".mysql_error());
			
			if (mysql_result($verif, 0, 'cu_indecsi') == 'da'){
				$indexNou = $proc['indexNou'];
				$indexVechi = $proc['indexVechi'];
				$cantitate = $proc['indexNou'] - $proc['indexVechi'];
				$cost = $proc['cost'];
				//$ppu = $proc['ppu'];		
			} else {
				$cantitate = $proc['cantitate']; 		
				$cost = $proc['cost'];
				//$ppu = $proc['ppu']; 			//apare in servicii
			}
		}
	
	}
}
?>

<?php
function afiseazaFacturi(){
	$lunaCur = date('m-Y');
	$selectFacturi = "SELECT * FROM facturi WHERE luna<='".$lunaCur."' ORDER BY fact_id DESC";
	$selectFacturi = mysql_query($selectFacturi) or die ("#Procesare Facturi: 1 -- Nu pot selecta facturile<br />".mysql_error());

	$i = 1;
	if (mysql_num_rows($selectFacturi) > 0){
		while ($parcurgFacturi = mysql_fetch_array($selectFacturi)) {
			if ($i % 2 == 0) { $culoare = "#FFFFFF"; } else { $culoare = "#DDDDDD"; }
			echo '<form action="" method="post">';
				echo '<tr bgcolor="'.$culoare.'">';
					//camputi ascunse
					echo '<input type="hidden" name="deProcesat" value="OK" />';
					echo '<input type="hidden" name="factura" value="'.$parcurgFacturi['fact_id'].'" />';
					
					//tabel
					echo '<td>'.$i.'</td>';
					
					//select Asociatie
					$asociatie = "SELECT * FROM asociatii WHERE asoc_id=".$parcurgFacturi['asoc_id'];
					$asociatie = mysql_query($asociatie) or die ("#Procesare Facturi: 2 -- Nu pot afla detalii despre asociatie<br />".mysql_error());
					
					$numeAsociatie = "Asociatie ".mysql_result($asociatie, 0, 'asociatie');
					
					//select Scara
					if ($parcurgFacturi['scara_id'] != null){
						$scara = "SELECT * FROM scari WHERE scara_id=".$parcurgFacturi['scara_id'];
						$scara = mysql_query($scara) or die ("#Procesare Facturi: 3 -- Nu pot afla detalii despre scara<br />".mysql_error());
						
						$numeScara = " / Blocul ".mysql_result($scara, 0, 'bloc').", scara ".mysql_result($scara, 0, 'scara');
					} else {
						$numeScara = "";
					}
					echo '<td>'.$numeAsociatie.$numeScara.'</td>';
					
					//pun Serie / Numar
					if ($parcurgFacturi['numarFactura'] != null && $parcurgFacturi['serieFactura'] != null){
						$serieNumar = $parcurgFacturi['serieFactura'].' / '.$parcurgFacturi['numarFactura'];
					} else {
						$serieNumar = " - ";
					}
					echo '<td>'.$serieNumar.'</td>';
					
					//aflu serviciul si furnizorul
					$serviciul = "SELECT * FROM servicii WHERE serv_id=".$parcurgFacturi['tipServiciu'];
					$serviciul = mysql_query($serviciul) or die ("#Procesre Facturi: 4 -- Nu pot afla detalii despre serviciu<br />".mysql_error());
					
					$numeServiciu = mysql_result($serviciul, 0, 'serviciu');
					
					if ($parcurgFacturi['tipFactura'] == 1){	//facturi pe asociatie
						$numeFurnizor = "SELECT furnizori.furnizor FROM furnizori, asociatii_furnizori, furnizori_servicii WHERE furnizori_servicii.serv_id=".$parcurgFacturi['tipServiciu']." AND asociatii_furnizori.asoc_id=".$parcurgFacturi['asoc_id']." AND furnizori_servicii.fur_id=asociatii_furnizori.fur_id AND asociatii_furnizori.fur_id=furnizori.fur_id";
						$numeFurnizor = mysql_query($numeFurnizor) or die ("#Procesare Facturi: 5 -- Nu pot afla numele furnizorului<br />".mysql_error());
						
						$numeFurnizor = ' ( '.mysql_result($numeFurnizor, 0, 'furnizori.furnizor').' )';
					} else {	//facturi pe scara
						$numeFurnizor = "SELECT furnizori.furnizor FROM furnizori, scari_furnizori, furnizori_servicii WHERE furnizori_servicii.serv_id=".$parcurgFacturi['tipServiciu']." AND scari_furnizori.scara_id=".$parcurgFacturi['scara_id']." AND furnizori_servicii.fur_id=scari_furnizori.fur_id AND scari_furnizori.fur_id=furnizori.fur_id"; 
						$numeFurnizor = mysql_query($numeFurnizor) or die ("#Procesare Facturi: 6 -- Nu pot afla numele furnizorului<br />".mysql_error());
						
						$numeFurnizor = ' ( '.mysql_result($numeFurnizor, 0, 'furnizori.furnizor').' )';
					}
					echo '<td>'.$numeServiciu.$numeFurnizor.'</td>';
					
					//afisez luna
					echo '<td>'.$parcurgFacturi['luna'].'</td>';
					
					//afisez data emiterii si data scadenta
					if ($parcurgFacturi['dataEmitere'] != null && $parcurgFacturi['dataScadenta'] != null){
						echo '<td>'.$parcurgFacturi['dataEmitere'].' / '.$parcurgFacturi['dataScadenta'].'</td>';
					} else {
						echo '<td>-</td>';
					}
					
					//afisez cantitatea
					if ($parcurgFacturi['cantitate'] != null) {
						echo '<td>'.$parcurgFacturi['cantitate'].'</td>';
					} else {
						echo '<td>-</td>';
					}
					
					//afisez costul
					if ($parcurgFacturi['cost'] != null) {
						echo '<td>'.$parcurgFacturi['cost'].'</td>';
					} else {
						echo '<td>-</td>';
					}
					
					//numarul de rate
					if ($parcurgFacturi['rate'] != 0 && $parcurgFacturi['rate'] != null) {
						echo '<td>'.$parcurgFacturi['rate'].'</td>';
					} else {
						echo '<td>-</td>';
					}
					
					//starea facturii
					if ($parcurgFacturi['procesata'] == '1'){
						echo '<td> <img src="images/ok.png" width="20px" height="20px" border="0px" /> </td>';
					} else {
						echo '<td> <input type="submit" value="Proceseaza" /> </td>';
					}
					
				echo '</tr>';
			echo '</form>';
			$i++;
		}
	} else {
		echo '<tr bgcolor="#DDDDDD">';
			echo '<td colspan="10">Nu sunt facturi inregistrate</td>';
		echo '</tr>';
	}
}
?>

<table width="950" bgcolor="#BBBBBB" style="top:250px;">
	<thead>
		<tr bgcolor="#000000" style="color:#FFFFFF">
			<td >Nr. Crt.</td>
			<td >Asocitatie / Scara</td>
			<td >Serie/Numar</td>
			<td >Serviciu (Furnizor)</td>
			<td >Luna</td>
			<td >Data Emitere / Data Scadenta</td>
			<td >Cantitate</td>
			<td >Cost</td>
			<td >Rate</td>
			<td >Stare</td>
		</tr>
	</thead>

	<tbody>
		<?php  afiseazaFacturi();  ?>
	</tbody>
</table>
</form>