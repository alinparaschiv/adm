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

<script type="text/javascript">
	function select_asoc(value1){
		window.location = "index.php?link=facturiV2&asoc_id=" + value1;
	}

	function select_factura(value1, value2){
		window.location = "index.php?link=facturiV2&asoc_id=" + value1 + "&tipFactura=" + value2;
	}
	
	function select_furnizor(value1, value2, value3){
		window.location = "index.php?link=facturiV2&asoc_id=" + value1 + "&tipFactura=" + value2 + "&furnizor=" + value3;
	}
	
	function select_scara(value1, value2, value3){
		window.location = "index.php?link=facturiV2&asoc_id=" + value1 + "&tipFactura=" + value2 + "&scara_id=" + value3;
	}
	
	function select_furnizor_1(value1, value2, value3, value4) {
		window.location = "index.php?link=facturiV2&asoc_id=" + value1 + "&tipFactura=" + value2 + "&scara_id=" + value3 + "&furnizor=" + value4;
	}
</script>

<script>
function verificaBifat(bifat, id){
	if (document.getElementById('nrApartamente').value == '')
	{
		document.getElementById('nrApartamente').value = 0;
	}
	if (bifat == true){
		document.getElementById('nrApartamente').value ++;
		document.getElementById(id).disabled = false;
	}
	else
	{
		document.getElementById('nrApartamente').value --;	
		document.getElementById(id).disabled = true;
		document.getElementById(id).value = '';
	}
}

function verificaBifat1(bifat){
	if (document.getElementById('nrApartamente').value == '')
	{
		document.getElementById('nrApartamente').value = 0;
	}
	if (bifat == true){
		document.getElementById('nrApartamente').value ++;
	}
	else
	{
		document.getElementById('nrApartamente').value --;	
	}
}

function putAll(bifat, valoare){
	if (bifat == true){
		document.getElementById('nrApartamente').value = valoare;
		for (var i=1; i <= valoare; i++){
			document.getElementById('cost-'+i).disabled = false;
		}
	} else {
		document.getElementById('nrApartamente').value = 0;
		for (var i=1; i <= valoare; i++){
			document.getElementById('cost-'+i).disabled = true;
		}
	}
}

function canti(valoare){
	var iNou = document.getElementById(valoare + 'indexNou').value;
	var iVechi = document.getElementById(valoare + 'indexVechi').value;
	if ((iNou != null) && (iVechi != null)){
		document.getElementById(valoare + 'diferenta').value = iNou - iVechi;	
	}

}
</script>

<?php 
/*	SALVAREA FACTURILOR	*/
if ($_POST['insereaza'] == "OK"){
	//iau datele principale ale facturii (asociatie, tip factura, furnizor, scara)
	$asocId = $_GET['asoc_id'];
	$tipFactura = $_GET['tipFactura'];
	$furnizor = $_GET['furnizor'];
	
	if ($tipFactura != 1){
		$scaraId = $_GET['scara_id'];
	}
	
	//aflu numele serviciului
	$afluServiciu = "SELECT servicii.serv_id, servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
	$afluServiciu = mysql_query($afluServiciu) or die ("#Facturi: 90 -- Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());	
	
	$servId = mysql_result($afluServiciu, 0, 'serv_id');
	$serviciu = mysql_result($afluServiciu, 0, 'serviciu');
	
	$tipulServiciului = "SELECT * FROM servicii WHERE serv_id=".$servId;
	$tipulServiciului = mysql_query($tipulServiciului) or die ("#Facturi: 91 -- Nu pot afla informatii despre serviciu<br />".mysql_error());
	
	$areIndecsi = mysql_result($tipulServiciului, 0, 'cu_indecsi');
	
	
	// tratez cazurile separate - apa rece, apa calda, iluminat
	if ($serviciu == 'apa rece'){
		//aflu numarul de scari si numarul de pasante
		$dateAsoc = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
		$dateAsoc = mysql_query($dateAsoc) or die ("#Facturi: 100 -- Nu pot afla detalii despre asociatie<br />".mysql_error());
		
		$nrScari = mysql_result($dateAsoc, 0, 'nr_scari');
		$nrPasante = mysql_result($dateAsoc, 0, 'pasante');
		
		foreach ($_POST as $cheie=>$valoare){
			$peBucati = explode("-",$cheie);
			
			//verific daca e pasant sau index sau consum
			if ($peBucati[0] == "P"){
				if ($valoare == ""){
					$valoare = 0;
				}
				$pasante[] = $valoare; 
			}
			
			if ($peBucati[1] == "indexVechi"){
				if ($valoare == ""){
					$valoare = 0;
				}
				$indexVechi[] = $valoare;
			}
			
			if ($peBucati[1] == "indexNou"){
				if ($valoare == ""){
					$valoare = 0;
				}
				$indexNou[] = $valoare;
			}			
			
			if ($peBucati[1] == "diferenta"){
				if ($valoare == ""){
					$valoare = 0;
				}
				$cant += $valoare;
				$diferenta[] = $valoare;
			}
		}
                
                if (count($pasante) != 0){
		        $pasante = implode(',', $pasante);
                }

		$indexNou = implode(',', $indexNou);
		$indexVechi = implode(',', $indexVechi);
		$diferenta = implode(',', $diferenta);

		$facturaApa = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`, `pasant`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '$indexNou', '$indexVechi', '".$_POST['valoareFactura']."', '$cant', '$pasante', '".$_POST['observatii']."', 0) ";
		$facturaApa = mysql_query($facturaApa) or die ("#Factura: 1000 -- Nu pot insera factura de apa rece<br />".mysql_error());
		
		echo "Factura a fost salvata cu succes";
	} else 
	if ($serviciu == 'apa calda'){
		foreach ($_POST as $cehie=>$valoare){
			$peBucati = explode("-", $cheie);
			
			if ($peBucati[0] == 24){
				if ($peBucati[1] == "serieFactura"){
					$serieFactura24 = $valoare;
				}
				if ($peBucati[1] == "numarFactura"){
					$numarFactura24 = $valoare;
				}
				if ($peBucati[1] == "dataEmiterie"){
					$dataEmitere24 = $valoare;
				}
				if ($peBucati[1] == "dataScadenta"){
					$dataScadenta24 = $valoare;
				}
				if ($peBucati[1] == "valoareFactura"){
					$valoareFactura24 = $valoare;
				}
				if ($peBucati[1] == "debite"){
					$debite24 = $valoare;
				}
				if ($peBucati[1] == "penalizari"){
					$penalizari24 = $valoare;
				}
				if ($peBucati[1] == "observatii"){
					$observatii24 = $valoare;
				}
				if ($peBucati[1] == "luna"){
					$luna24 = $valoare;
				}
				if ($peBucati[1] == "consum"){
					$consum24 = $valoare;
				}
			}
			
			if ($peBucati[0] == 27){
				if ($peBucati[1] == "serieFactura"){
					$serieFactura27 = $valoare;
				}
				if ($peBucati[1] == "numarFactura"){
					$numarFactura27 = $valoare;
				}
				if ($peBucati[1] == "dataEmitere"){
					$dataEmitere27 = $valoare;
				}
				if ($peBucati[1] == "dataScadenta"){
					$dataScadenta27 = $valoare;
				}
				if ($peBucati[1] == "valoareFactura"){
					$valoareFactura27 = $valoare;
				}
				if ($peBucati[1] == "debite"){
					$debite27 = $valoare;
				}
				if ($peBucati[1] == "penalizari"){
					$penalizari27 = $valoare;
				}
				if ($peBucati[1] == "observatii"){
					$observatii27 = $valoare;
				}
				if ($peBucati[1] == "luna"){
					$luna27 = $valoare;
				}
				if ($peBucati[1] == "indexVechi"){
					$indexVechi27 = $valoare;
				}
				if ($peBucati[1] == "indexNou"){
					$indexNou27 = $valoare;
				}
				if ($peBucati[1] == "diferenta"){
					$diferenta27 = $valoare;
				}
				if ($peBucati[1] == "cost"){
					$cost27 = $valoare;
				}
				
				$apaRecePentruApaCalda = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '24', '$numarFactura24', '$serieFactura24', '$dataEmitere24', '$dataScadenta24', '$debite24', '$penalizari24', '$luna24', '$consum24', '$valoareFactura24', '$observatii24', 0)";
				$apaRecePentruApaCalda = mysql_query($apaRecePentruApaCalda) or die ("#Facturi: 1001 -- Nu pot salva factura pentru apa calda 1<br />".mysql_error());
				

				$agentTermicPentruApaCalda = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `diferenta`, `cost`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '27', '$numarFactura27', '$serieFactura27', '$dataEmitere27', '$dataScadenta27', '$debite27', '$penalizari27', '$luna27', '$indexNou27', '$indexVechi27', '$diferenta27', '$valoareFactura27', '$observatii27', 0)";
				$agentTermicPentruApaCalda = mysql_query($agentTermicPentruApaCalda) or die ("#Facturi: 1002 -- Nu pot salva factura pentru apa calda 2<br />".mysql_error());
			}
		}
	} else 
	if ($serviciu == 'iluminat'){
		$cont1 = 0;
		$cont2 = 0;
		$cont3 = 0;
		foreach ($_POST as $cheie=>$valoare){
			$peBucati = explode("-", $cheie);
			
			if ($peBucati[0] == 1){
				$cont1 = 1;
				if ($peBucati[1] == 'serieFactura'){
					$serieFactura1 = $valoare; 
				}
				if ($peBucati[1] == 'numarFactura'){
					$numarFactura1 = $valoare;
				}
				if ($peBucati[1] == 'dataEmitere'){
					$dataEmitere1 = $valoare;
				}
				if ($peBucati[1] == 'dataScadenta'){
					$dataScadenta1 = $valoare;
				}
				if ($peBucati[1] == 'valoareFactura'){
					$valoareFactura1 = $valoare;
				}
				if ($peBucati[1] == 'debite'){
					$debite1 = $valoare;
				}
				if ($peBucati[1] == 'penalizari'){
					$penalizari1 = $valoare;
				}
				if ($peBucati[1] == 'observatii'){
					$observatii1 = $valoare;
				}
				if ($peBucati[1] == 'luna'){
					$luna1 = $valoare;
				}
				if ($peBucati[1] == 'indexVechi'){
					$indexVechi1 = $valoare;
				}
				if ($peBucati[1] == 'indexNou'){
					$indexNou1 = $valoare;
				}
				if ($peBucati[1] == 'diferenta'){
					$diferenta1 = $valoare;
				}
			}
			
			if ($peBucati[0] == 2){
				$cont2 = 1;
				if ($peBucati[1] == 'serieFactura'){
					$serieFactura2 = $valoare;
				}
				if ($peBucati[1] == 'numarFactura'){
					$numarFactura2 = $valoare;
				}
				if ($peBucati[1] == 'dataEmitere'){
					$dataEmitere2 = $valoare;
				}
				if ($peBucati[1] == 'dataScadenta'){
					$dataScadenta2 = $valoare;
				}
				if ($peBucati[1] == 'valoareFactura'){
					$valoareFactura2 = $valoare;
				}
				if ($peBucati[1] == 'debite'){
					$debite2 = $valoare;
				}
				if ($peBucati[1] == 'penalizari'){
					$penalizari2 = $valoare;
				}
				if ($peBucati[1] == 'observatii'){
					$observatii2 = $valoare;
				}
				if ($peBucati[1] == 'luna'){
					$luna2 = $valoare;
				}
				if ($peBucati[1] == 'indexVechi'){
					$indexVechi2 = $valoare;
				}
				if ($peBucati[1] == 'indexNou'){
					$indexNou2 = $valoare;
				}
				if ($peBucati[1] == 'diferenta'){
					$diferenta2 = $valoare;
				}
			}
			
			if ($peBucati[0] == 3){ 
				$cont3 = 1;
				if ($peBucati[1] == 'serieFactura'){
					$serieFactura3 = $valoare;
				}
				if ($peBucati[1] == 'numarFactura'){
					$numarFactura3 = $valoare;
				}
				if ($peBucati[1] == 'dataEmitere'){
					$dataEmitere3 = $valoare;
				}
				if ($peBucati[1] == 'dataScadenta'){
					$dataScadenta3 = $valoare;
				}
				if ($peBucati[1] == 'valoareFactura'){
					$valoareFactura3 = $valoare;
				}
				if ($peBucati[1] == 'debite'){
					$debite3 = $valoare;
				}
				if ($peBucati[1] == 'penalizari'){
					$penalizari3 = $valoare;
				}
				if ($peBucati[1] == 'observatii'){
					$observatii3 = $valoare;
				}
				if ($peBucati[1] == 'luna'){
					$luna3 = $valoare;
				}
				if ($peBucati[1] == 'indexVechi'){
					$indexVechi3 = $valoare;
				}
				if ($peBucati[1] == 'indexNou'){
					$indexNou3 = $valoare;
				}
				if ($peBucati[1] == 'diferenta'){
					$diferenta3 = $valoare;
				}
			}
		}
		for ($t = 1; $t <= 3; $t++){
			$contT = 'cont'.$t;
			if ($$contT == 1){
			//echo 'Inside '.$contT.'<br />';
				$numarFacturaT = 'numarFactura'.$t;
				$serieFacturaT = 'serieFactura'.$t;
				$dataEmitereT = 'dataEmitere'.$t;
				$dataScadentaT = 'dataScadenta'.$t;
				$debiteT = 'debite'.$t;
				$penalizariT = 'penalizari'.$t;
				$lunaT = 'luna'.$t;
				$indexNouT = 'indexNou'.$t;
				$indexVechiT = 'indexVechi'.$t;
				$diferentaT = 'diferenta'.$t;
				$valoareFacturataT = 'valoareFacturata'.$t;
				$observatiiT = 'observatii'.$t;
			
				$putCurent = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$$numarFacturaT."', '".$$serieFacturaT."', '".$$dataEmitereT."', '".$$dataScadentaT."', '".$$debiteT."', '".$$penalizariT."', '".$$lunaT."', '".$$indexNouT."', '".$$indexVechiT."', '".$$diferentaT."', '".$$valoareFacturataT."', '".$$observatiiT."', 0)"; 
				//echo '<br /><br />Interogare: --><br />'.$putCurent.'<br />';
				$putCurent = mysql_query($putCurent) or die ("#Factura: 1003-'$t' -- Eroare la salvarea facturii<br />".mysql_error());
			}
		}
	} else {
		if ($tipFactura == 1){
			if ($areIndecsi == "da"){
				$facturaAsociatieCuIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`,`pasant`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['valoareFactura']."', '".$_POST['cant']."', '".$_POST['pasante']."', '".$_POST['observatii']."', 0) ";
				$facturaAsociatieCuIndecsi = mysql_query($facturaAsociatieCuIndecsi) or die ("#Factura: 1004 -- Nu pot insera factura/asociatie - cu indecsi<br />".mysql_error());

			} else {
				$facturaAsociatieFaraIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['consum']."', '".$_POST['valoareFactura']."', '".$_POST['observatii']."', 0)";
				$facturaAsociatieFaraIndecsi = mysql_query($facturaAsociatieFaraIndecsi) or die ("#Facturi: 1005 -- Nu pot salva factura/asociatie fara indecsi<br />".mysql_error());
			}
		}
		
		if ($tipFactura == 2){
			if ($areIndecsi == "da"){
				$facturaScaraCuIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`,`pasant`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['valoareFactura']."', '".$_POST['cant']."', '".$_POST['pasante']."', '".$_POST['observatii']."', 0) ";
				$facturaScaraCuIndecsi = mysql_query($facturaScaraCuIndecsi) or die ("#Factura: 1006 -- Nu pot insera factura/scara - cu indecsi<br />".mysql_error());

			} else {
				$facturaScaraFaraIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['consum']."', '".$_POST['valoareFactura']."', '".$_POST['observatii']."', 0)";
				$facturaScaraFaraIndecsi = mysql_query($facturaScaraFaraIndecsi) or die ("#Facturi: 1007 -- Nu pot salva factura/scara fara indecsi<br />".mysql_error());
			}
		}
		
		if ($tipFactura == 3){
			foreach ($_POST as $valoare=>$cheie){
				$peBucati = explode("-", $valoare);
				
				if ($peBucati[0] == "loc"){
					$locatar[] = $valoare;
				}
			}
			$locatari = implode(",",$locatar);
			
			$putFacturaApartamente = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `nrRate`, `luna`, `cantitate`, `cost`, `locatari`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['nrRate']."', '".$_POST['luna']."', '".$_POST['nrApartamente']."', '".$_POST['valoareFactura']."', '$locatari', '".$_POST['observatii']."', 0)";
			$putFacturaApartamente = mysql_query($putFacturaApartamente) or die ("#Facturi: 1008 -- Nu pot insera factura pe apartamente<br />".mysql_error());
		}
		
		if ($tipFactura == 4){
			foreach ($_POST as $cheie=>$valoare){
				$peBucati = explode ("-", $cheie);
				
				if ($peBucati[0] == "loc") {
					if ($valoare != "") {
						$locatar[] = $peBucati[1]; 
					}
				}
				
				if ($peBucati[0] == "cost"){
					if ($_POST['loc-'.$peBucati[1]] != ""){
						$cost[] = $valoare;
					}
				}				
			}
			
			$locatari = implode(",",$locatar);
			$cost = implode(",",$cost);
						
			$insertFacturaPeLocatari = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `nrRate`, `luna`, `indexNou`, `indexVechi`, `cantitate`, `cost`, `ppu`, `locatari`, `observatii`, `procesata`) VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['nrRate']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['diferenta']."', '".$_POST['valoareFactura']."', '$cost', '$locatari', '".$_POST['observatii']."', 0)";
			$insertFacturaPeLocatari = mysql_query($insertFacturaPeLocatari) or die ("#Facturi: 1009 -- Nu pot salva factura pe locatari<br />".mysql_error());
		}
	}
	
	//print_r($_POST);
	unset($_POST);
}

/*	FUNCTII PENTRU TABEL INTRODUCERE FACTURA	*/
function dateFactura($val){
	if ($val != 0){
		$val = $val."-";
	} else {
		$val = "";
	}
	/*  */
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td width="125px">Serie Factura</td>';
		echo '<td width="125px">Numar Factura</td>';
		echo '<td width="125px">Data Emiterii</td>';
		echo '<td width="125px">Data Scadenta</td>';
		echo '<td width="125px">Valoare Factura</td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#DDDDDD">';
		echo '<td><input type="text" name="'.$val.'serieFactura" /></td>';
		echo '<td><input type="text" name="'.$val.'numarFactura" /></td>';
		echo '<td><input type="text" name="'.$val.'dataEmitere" class="datepicker" /></td>';
		echo '<td><input type="text" name="'.$val.'dataScadenta" class="datepicker" /></td>';
		echo '<td><input type="text" name="'.$val.'valoareFactura" id="valoareFactura" /></td>';
	echo '</td>';
}

function infoPlati($val){
	if ($val != 0){
		$val = $val."-";
	} else {
		$val = "";
	}
	
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td width="125px">Debite</td>';
		echo '<td width="125px">Penalizari</td>';
		echo '<td width="125px">Observatii</td>';
		echo '<td width="125px">Luna</td>';
		echo '<td width="125px">&nbsp;</td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#DDDDDD">';
		echo '<td><input type="text" name="'.$val.'debite" disabled /></td>';
		echo '<td><input type="text" name="'.$val.'penalizari" disabled/></td>';
		echo '<td><input type="text" name="'.$val.'observatii" /></td>';
		echo '<td>';
			echo '<select name="'.$val.'luna" style="width:125px">';
				if ($_POST['luna'] != ""){
					echo '<option selected="selected" value="'.$_POST[$val.'luna'].'">'.$_POST[$val.'luna'].'</option>';
				}
				for ($i=0; $i<12; $i++){
					echo '<option value="'.date('m-Y', mktime(0, 0, 0, (date('m')-$i), 1, date('Y'))).'">'.date('m-Y', mktime(0, 0, 0, (date('m')-$i), 1, date('Y'))).'</option>';
				}
			echo '</select>';
		echo '</td>';
		echo '<td>&nbsp;</td>';
	echo '</tr>';
}

function infoFonduri($val){
	if ($val != 0){
		$val = $val."-";
	} else {
		$val = "";
	}
	
	echo '<tr bgcolor="#000000" style="color:#FFFFFF"';
		echo '<td width="125px">Luna</td>';
		echo '<td width="125px">Valoare Factura</td>';
		echo '<td width="125px">Nr. Apartamente</td>';
		echo '<td width="125px">Nr. Rate</td>';
		echo '<td width="125px">Observatii</td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#DDDDDD">';
		echo '<td>';
			echo '<select name="luna" style="width:125px">';
				if ($_POST['luna'] != ""){
					echo '<option selected="selected" value="'.$_POST['luna'].'">'.$_POST['luna'].'</option>';
				}
				for ($i=0; $i<12; $i++){
					echo '<option value="'.date('m-Y', mktime(0, 0, 0, (date('m')-$i), 1, date('Y'))).'">'.date('m-Y', mktime(0, 0, 0, (date('m')-$i), 1, date('Y'))).'</option>';
				}
			echo '</select>';
		echo '</td>';
		echo '<td><input type="text" name="'.$val.'valoareFactura" /></td>';
		echo '<td><input type="text" name="'.$val.'nrApartamente" id="nrApartamente" readonly="readonly"/></td>';
		echo '<td><input type="text" name="'.$val.'nrRate" /></td>';
		echo '<td><input type="text" name="'.$val.'observatii" /></td>';
	echo '</tr>';
}

function putIndecsi($val){
	if ($val != 0){
		$val = $val."-";
	} else {
		$val = "";
	}
	
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td width="125px">Index Vechi</td>';
		echo '<td width="125px">Index Nou</td>';
		echo '<td width="125px">Diferenta</td>';
		//echo '<td width="125px">Cost</td>';
		echo '<td colspan="2 "width="125px">&nbsp;</td>';
	echo '</tr>';
			
	echo '<tr bgcolor="#DDDDDD">';
		echo '<td><input type="text" name="'.$val.'indexVechi" id="'.$val.'indexVechi" onchange="canti(\''.$val.'\');"/></td>';
		echo '<td><input type="text" name="'.$val.'indexNou" id="'.$val.'indexNou" onchange="canti(\''.$val.'\');"/></td>';
		echo '<td><input type="text" name="'.$val.'diferenta" id="'.$val.'diferenta" readonly="readonly"/></td>';
		//echo '<td><input type="text" name="'.$val.'cost" /></td>';
		echo '<td colspan="2">&nbsp;</td>';
	echo '</tr>';
}

function putPasante(){
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td colspan="2">Pasante, scari (consum - m<sup>3</sup>)</td>';
		echo '<td colspan="3">&nbsp;</td>';
	echo '</tr>';
	
	$nrPasante = "SELECT * FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
	$nrPasante = mysql_query($nrPasante) or die ("#Facturi: 21 -- Nu pot afla numarul de pasante<br />".mysql_error());
	$nrPasante = mysql_result($nrPasante, 0, 'pasante');
	
	$pasante = "SELECT * FROM scari_pasante WHERE asoc_id=".$_GET['asoc_id'];
	$pasante = mysql_query($pasante) or die ("#Facturi: 22 -- Nu pot selecta pasantele pentru fiecare scara in parte<br />".mysql_error());
	
	//	BUCATA ASTA VA TREBUI REFACUTA	//
	$setPas = array();
	if (mysql_num_rows($pasante) != 0){
		$setPas = mysql_result($pasante, 0, 'pasante');
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
				$scriuBloc = mysql_query($scriuBloc) or die ("#Facturi: 23 -- Nu pot afla scara si blocul<br />".mysql_error());
				
				$verific[$pasantul] .= "Blocul ".mysql_result($scriuBloc, 0, 'bloc').", scara ".mysql_result($scriuBloc, 0, 'scara')."; ";
			}
		}
	}
	
	for ($i=0; $i<=$nrPasante; $i++){
		if ($i % 2 == 0) { $culoare = "#DDD"; } else { $culoare = "#EEE"; }
			if ($verific[$i] == ""){
				$afisez = "Nu sunt scari pentru aceasta optiune";
				$modAfis = "disabled";
			} else {								
				$afisez = "Valoarea pasantului pentru: ".$verific[$i];
				$modAfis="";
			}
			
		echo '<tr bgcolor="'.$culoare.'" valign="top">';
			echo '<td>Pasant '.$i.'</td>';
			echo '<td><input type="text" name="P-'.$i.'" '.$modAfis.'/></td>';
			echo '<td colspan="3" align="left">*) '.$afisez.'</td>';
		echo '</tr>';
	}
}

function putApometre(){
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td width="125px">Bloc, scara</td>';
		echo '<td width="125px">Index Nou</td>';
		echo '<td width="125px">Index Vechi</td>';
		echo '<td width="125px">Diferenta</td>';
		//echo '<td width="125px">Cost</td>';
		echo '<td width="125px">&nbsp;</td>';
	echo '</tr>';
	
	$nrScari = "SELECT * FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
	$nrScari = mysql_query($nrScari) or die ("#Facturi: 24 -- Nu pot afla numarul scarilor.<br />".mysql_error());
	$nrScari = mysql_result($nrScari, 0, 'nr_scari');
	
	for ($i=1; $i<=$nrScari; $i++){
		if ($i % 2 == 0) { $culoare = "#DDD"; } else { $culoare = "#EEE"; }
		echo '<tr bgcolor="'.$culoare.'">';
			$afisScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
			$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());
				
			echo '<td>Blocul '.mysql_result($afisScara, ($i-1), 'bloc').', scara '.mysql_result($afisScara, ($i-1), 'scara').'</td>';
			echo '<td><input type="text" name="'.$i.'-indexNou" id="'.$i.'-indexNou" onchange="canti(\''.$i.'-\');"/></td>';
			echo '<td><input type="text" name="'.$i.'-indexVechi" id="'.$i.'-indexVechi" onchange="canti(\''.$i.'-\');"/></td>';
			echo '<td><input type="text" name="'.$i.'-diferenta" id="'.$i.'-diferenta" readonly="readonly"/></td>';
			//echo '<td><input type="text" name="'.$i.'-cost"/></td>';
			echo '<td width="125px">&nbsp;</td>';
		echo '</tr>';
	}
}

function putContoare(){
	$contoare = "SELECT * FROM scari_setari WHERE scara_id=".$_GET['scara_id'];
	$contoare = mysql_query($contoare) or die ("#Facturi: 30 -- Nu pot afla numarul de contoare<br />".mysql_error());
	
	$tipContor = array("General", "Lift", "Centrala"); 
	
	$nrContoare[] = 1;
	
	$contorLift = mysql_result($contoare, 0, 'contor_lift');
	$nrContoare[] = $contorLift;
		
	$contorCentrala = mysql_result($contoare, 0, 'contor_centrala');
	$nrContoare[] = $contorCentrala;
	
	for ($cont=0; $cont<count($nrContoare); $cont++){
		if ($nrContoare[$cont] == 1){
			echo '<tr bgcolor="#AAAAAA" style="color:#000000">';
				echo '<td colspan="5"><strong>Contoar '.$tipContor[$cont].'</strong></td>';
			echo '</tr>';
			
			dateFactura(($cont+1));
			infoPlati(($cont+1));
			putIndecsi(($cont+1));
		}
	}
}

function putApaRece(){
	dateFactura(0);
	infoPlati(0);
	putPasante();
	putApometre();
}

function putApaRecePentruApaCalda(){
	echo '<tr bgcolor="#DDDDDD" style="color:#000000">';
		echo '<td colspan="5"><strong>Apa Rece pentru Apa Calda - ApaVital</strong></td>';
	echo '</tr>';
	
	dateFactura(24);
	infoPlati(24);
	
	echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
		echo '<td>Consum ( m<sup>3</sup> )</td>';
		//echo '<td>Cost</td>';
		echo '<td colspan="4">&nbsp;</td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#DDDDDD">';
		echo '<td><input type="text" name="24-consum" value="'.$_POST['24-consum'].'" /></td>';
		//echo '<td><input type="text" name="24-cost" value="'.$_POST['24-cost'].'" /></td>';
		echo '<td colspan="4">&nbsp;</td>';
	echo '</tr>';
}

function putAgentTermicPentruApaCalda(){
	echo '<tr bgcolor="#DDDDDD" style="color:#000000">';
		echo '<td colspan="5"><strong>Agent termic pentru Apa Calda - CET</strong></td>';
	echo '</tr>';
	
	dateFactura(27);
	infoPlati(27);
	putIndecsi(27);
}

/*	SELECTUL PENTRU AFISAREA ASOCIATIILOR	*/
if ($_GET['asoc_id'] != ""){
	$selectAsoc = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id'];
} else {
	$selectAsoc = "SELECT * FROM asociatii";
}
$selectAsoc = mysql_query($selectAsoc) or die ("#Facturi: 1 -- Nu pot selecta asociatiile<br />".mysql_error());

while ($asoc = mysql_fetch_array($selectAsoc)){
	$asociatie .= '<option value="'.$asoc[0].'">'.$asoc[1].'</option>';
}

/*	SELECTUL PENTRU AFISAREA SCARILOR	*/
if ($_GET['asoc_id'] != ''){
	if ($_GET['scara_id'] != ''){
		$selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id'];
	} else {
		$selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	}
	$selectScara = mysql_query($selectScara) or die ("#Facturi: 2 -- Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());
	
	while ($scari = mysql_fetch_array($selectScara)){
		$scara .= '<option value="'.$scari[0].'">Bloc '.$scari[5].', scara '.$scari[2].'</option>';
	}
}

/*	SELECTUL PENTRU AFISAREA TIPULUI DE FACTURA	*/
if ($_GET['tipFactura'] != ''){
	$tipFact = "SELECT * FROM tip_factura WHERE id<>".$_GET['tipFactura'];
} else {
	$tipFact = "SELECT * FROM tip_factura";
}
$tipFact = mysql_query($tipFact) or die ("#Facturi: 3 -- Nu pot selecta tipul de factura<br />".mysql_error());
	
while ($nivel = mysql_fetch_array($tipFact)){
	$tipFactura .= '<option value="'.$nivel[0].'">'.$nivel[1].'</option>';
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE ASOCIATIE	*/
if ($_GET['asoc_id'] != '' && $_GET['tipFactura'] != ''){
	if ($_GET['furnizor'] != ''){
		$furniz = "SELECT F.furnizor, F.fur_id FROM asociatii_furnizori A, furnizori F WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor'];
	} else {
		$furniz = "SELECT F.furnizor, F.fur_id FROM asociatii_furnizori A, furnizori F WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id";
	}
	$furniz = mysql_query($furniz) or die ("#Factura: 4 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());
	
	while ($furn = mysql_fetch_array($furniz)){
		$furnizor1 .= '<option value="'.$furn['fur_id'].'">'.$furn['furnizor'].'</option>'; 
	}
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE SCARA	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != ''){
	if ($_GET['furnizor'] != ''){
		$furniz2 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND FS.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND F.fur_id=A.fur_id AND SE.nivel=2";
	} else {
		$furniz2 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND FS.fur_id=A.fur_id AND FS.serv_id=SE.serv_id AND F.fur_id=A.fur_id AND SE.nivel=2";
	}
	$furniz2 = mysql_query($furniz2) or die ("#Factura: 5 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());
	
	while ($furn2 = mysql_fetch_array($furniz2)){
		$furnizor2 .= '<option value="'.$furn2['fur_id'].'">'.$furn2['furnizor'].'</option>';
	}
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE APARTAMENT	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != ''){
	if ($_GET['furnizor'] != ''){
		$furniz3 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=3";
	} else {
		$furniz3 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=3";
	}
	$furniz3 = mysql_query($furniz3) or die ("#Factura: 6 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());
	
	while ($furn3 = mysql_fetch_array($furniz3)){
		$furnizor3 .= '<option value="'.$furn3['fur_id'].'">'.$furn3['furnizor'].'</option>';
	}
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE LOCATAR	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != ''){
	if ($_GET['furnizor'] != ''){
		$furniz4 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=4";
	} else {
		$furniz4 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=4";
	}
	$furniz4 = mysql_query($furniz4) or die ("#Factura: 7 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());
	
	while ($furn4 = mysql_fetch_array($furniz4)){
		$furnizor4 .= '<option value="'.$furn4['fur_id'].'">'.$furn4['furnizor'].'</option>';
	}
}
?>



<div id="content" style="float:left;">
<table width="400">
	<tr align="center">
		<td width="388" bgcolor="#CCCCCC" colspan="2"><strong>Configurare Factura - Beta</strong></td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">( 1 ) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select onChange="select_asoc(this.value)">
				<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
					{
						$afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
						$afisAsoc = mysql_query($afisAsoc) or die ("#Facturi: 8 -- Nu pot selecta asociatiile<br />".mysql_error());

						echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
					}
				?>
		        	<?php echo $asociatie; ?>
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
						$afisFactura = mysql_query($afisFactura) or die ("#Facturi: 9 -- Nu pot selecta tipul de factura<br />".mysql_error());

						echo '<option value="">'.mysql_result($afisFactura, 0, 'tip_factura').'</option>';
					}
				?>
        		    <?php  echo $tipFactura; ?>
        	</select>
        </td>
	</tr>
	<?php } ?>
<?php
/*	AUXILIARE PENTRU ASOCIATII	*/
	if ($_GET['tipFactura'] == 1){
		echo '<tr>';
			echo '<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti furnizorul:</td>';
			echo '<td width="215" align="left" bgcolor="#CCCCCC">';
				echo '<select style="width:125px" onchange="select_furnizor('.$_GET['asoc_id'].','.$_GET['tipFactura'].', this.value)">';
					if ($_GET['furnizor'] == null){
						echo '<option value="">----Alege----</option>';
					} else {
						$afisFurnizori = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor']; 
						$afisFurnizori = mysql_query($afisFurnizori) or die ("#Facturi: 10 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());
						
						echo '<option value="">'.mysql_result($afisFurnizori, 0, 'furnizor').'</option>';
					}
					echo $furnizor1;
				echo '</select>';
			echo '</td>';
		echo '</tr>';
	} else
?>

<?php
/*	AUXILIARE PENTRU SCARI	*/
	if ($_GET['tipFactura'] != '' && $_GET['tipFactura'] != 1) {
		echo '<tr>';
			echo '<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti scara:</td>';
			echo '<td width="215" align="left" bgcolor="#CCCCCC">';
				echo '<select style="width:125px" onchange="select_scara('.$_GET['asoc_id'].','.$_GET['tipFactura'].', this.value);">';
					if ($_GET['scara_id'] == null){
						echo '<option value="">----Alege----</option>';
					} else {
						$afisScari = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
						$afisScari = mysql_query($afisScari) or die ("#Facturi: 11 -- Nu pot selecta scara pentru tipul de factura<br />".mysql_error());
						
						echo '<option value="">Bloc '.mysql_result($afisScari, 0, 'bloc').', Scara '.mysql_result($afisScari, 0, 'scara').'</option>';
					}
					echo $scara;
				echo '</select>';
			echo '</td>';
		echo '</tr>';
	}
	
	if ($_GET['scara_id'] != null) {
		echo '<tr>';
			echo '<td width="173" align="left" bgcolor="#CCCCCC">( 4 ) Alegeti furnizorul:</td>';
			echo '<td width="215" align="left" bgcolor="#CCCCCC">';
				echo '<select style="width:125px" onchange="select_furnizor_1('.$_GET['asoc_id'].','.$_GET['tipFactura'].','.$_GET['scara_id'].', this.value);">';
					if ($_GET['furnizor'] == null) {
						echo '<option value="">----Alege----</option>';
					} else {
						$afisFurnizor = "SELECT * FROM furnizori WHERE fur_id=".$_GET['furnizor'];
						$afisFurnizor = mysql_query($afisFurnizor) or die ("#Facturi: 12 -- Nu pot selecta furnizorii pentru scara curenta<br />".mysql_error());
						
						echo '<option value="">'.mysql_result($afisFurnizor, 0, 'furnizor').'</option>';
					}
					
					switch ($_GET['tipFactura']){
						case "2": echo $furnizor2;
								break;
						case "3": echo $furnizor3;
								break;
						case "4": echo $furnizor4;
								break;
					}
				echo '</select>';
			echo '</td>';
		echo '</tr>';
	}
?>

<?php
/*	TABELUL PENTRU INSERARE FACTURI	*/
?>

<br clear="left" />

<form action="" method="post">
	<input type="hidden" name="insereaza" value="OK" />
	<table width="750" style="float:left;  margin-top:10px; background-color:#BBBBBB;">
	<?php
		/**	IN CAZUL IN CARE AM SELECTAT TOT, CONTINUI	**/
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
			$furnizor = $_GET['furnizor'];
			$tipPlata = $_GET['tipFactura'];
			
			$afluFurnizor = "SELECT * FROM furnizori WHERE fur_id=".$furnizor;
			$afluFurnizor = mysql_query($afluFurnizor) or die ("Nu pot afla furnizorul pentru afisarea tabelului<br />".mysql_error());

			$afluServiciu = "SELECT servicii.serviciu, servicii.cu_indecsi FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
			$afluServiciu = mysql_query($afluServiciu) or die ("Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());
			
			$eCuIndecsi = mysql_result($afluServiciu, 0, 'cu_indecsi');
			
			echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($afluFurnizor, 0, 'furnizor').' - Serviciu facturat: '.mysql_result($afluServiciu, 0, 'serviciu').'</strong></td></tr>';
			echo '<tr bgcolor="#CCC"><td colspan="5"> &nbsp; </td></tr>';
			
			if (mysql_result($afluServiciu, 0, 'serviciu') == 'apa rece'){ 
				putApaRece();
			} else if (mysql_result($afluServiciu, 0, 'serviciu') == 'iluminat'){
				putContoare();	
			} else if (mysql_result($afluServiciu, 0, 'serviciu') == 'apa calda'){
				putApaRecePentruApaCalda();
				putAgentTermicPentruApaCalda();
			} else {
				$tipServiciu = "SELECT servicii.serviciu, servicii.cu_indecsi, servicii.fonduri FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$_GET['furnizor']." AND furnizori_servicii.serv_id=servicii.serv_id";
				$tipServiciu = mysql_query($tipServiciu) or die ("#Facturi: 40 -- Nu pot selecta tipul de serviciu<br />".mysql_error());
				
				if (mysql_result($tipServiciu, 0, 'cu_indecsi') == 'da'){
					dateFactura(0);
					infoPlati(0);
					putIndecsi(0);
				}else if (mysql_result($tipServiciu, 0, 'fonduri') == 'da'){
					infoFonduri(0);
				}
				
				//verific tipul de factura
				if ($_GET['tipFactura'] == 3){	//pe apartament
				
					$locatari = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id'];
					$locatari = mysql_query($locatari) or die ("#Factura: 50 -- Nu pot afla numele locatarilor<br />".mysql_error());
					
					echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
						echo '<td colspan="2" width="250px">Locatar</td>';
						echo '<td colspan="3">&nbsp;</td>';
					echo '</tr>';
					
					if (mysql_num_rows($locatari) != 0){
						$nrLoc = 0;
						
						while ($row = mysql_fetch_array($locatari)){
							if ($nrLoc %2 == 0) { $culoare = "#DDDDDD"; } else { $culoare = "#FFFFFF"; }
							echo '<tr bgcolor="'.$culoare.'">';
								echo '<td width="125px"><input type="checkbox" name="loc-'.($row['loc_id']).'" class="debifat" onclick="verificaBifat1(this.checked)"></td>';
								echo '<td>'.$row['nume'].'</td>';
								echo '<td colspan="3">&nbsp;</td>';
							echo '</tr>';
							
							$nrLoc ++;
						}
						
						echo '<tr bgcolor="#BBBBBB">';
							echo '<td><input type="checkbox" id="clicker" onclick="putAll(this.checked, '.($nrLoc).');"/></td>';
							echo '<td>Check All</td>';
							echo '<td colspan="3">&nbsp</td>';
						echo '</tr>';
					} else {
						echo '<tr bgcolor="#DDDDDD">';
							echo '<td colspan="5">Nu sunt locatari inregistrati pe aceasta scara</td>';
						echo '</tr>';
					}
				} else if ($_GET['tipFactura'] == 4){	//pe locatar

					$locatari = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id'];
					$locatari = mysql_query($locatari) or die ("#Factura: 50 -- Nu pot afla numele locatarilor<br />".mysql_error());
					
					echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
						echo '<td colspan="2">Locatar</td>';
						echo '<td>Cost</td>';
						echo '<td colspan="2">&nbsp;</td>';
					echo '</tr>';
					
					if (mysql_num_rows($locatari) != 0){
						$nrLoc = 0;
						
						while ($row = mysql_fetch_array($locatari)){
							if ($nrLoc %2 == 0) { $culoare = "#DDDDDD"; } else { $culoare = "#FFFFFF"; }
							echo '<tr bgcolor="'.$culoare.'">';
								echo '<td width="125px"><input type="checkbox" name="loc-'.($row['loc_id']).'" class="debifat" onclick="verificaBifat(this.checked, \'cost-'.($nrLoc+1).'\');" value="'.$row['loc_id'].'"></td>';
								echo '<td width="125px">'.$row['nume'].'</td>';
								echo '<td width="125px"><input type="text" name="cost-'.($row['loc_id']).'" id="cost-'.($nrLoc+1).'" disabled/></td>';
								echo '<td colspan="2">&nbsp;</td>';
							echo '</tr>';
							
							$nrLoc ++;
						}
						
						echo '<tr bgcolor="#BBBBBB">';
							echo '<td><input type="checkbox" id="clicker" onclick="putAll(this.checked, '.($nrLoc).');"/></td>';
							echo '<td>Check All</td>';
							echo '<td colspan="3">&nbsp</td>';
						echo '</tr>';
					} else {
						echo '<tr bgcolor="#DDDDDD">';
							echo '<td colspan="5">Nu sunt locatari inregistrati pe aceasta scara</td>';
						echo '</tr>';
					}
				}
			}
	?>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td><input type="submit" value="Salveaza"/></td>
	</tr>

	<?php
		}	//endul de la "potAfisa"
	?>
	</table>
</form>