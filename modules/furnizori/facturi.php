<style type="text/css">
    thead tr td { border:solid 1px #000; color:#FFF; }
    tbody { border:solid 1px #000; }
    tbody tr td input { width:100%; border:none; height:100%; }
    tbody tr.newline td { border:solid 1px #0CC;   }
    tfoot { color:#FFF; }
    .addnew {position:absolute; width:120px; background-image:url(images/adauga.jpg); width:19px; height:20px; border:0; cursor:pointer; margin-left:5px;  }
    .addnew2 {position:absolute; width:120px; background-image:url(images/adauga.jpg); width:19px; height:20px; border:0; cursor:pointer; margin-left:95px; margin-top:-9px;  }
    tr.newline input { text-align:center; }
    .pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
    a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
    #print {float:left; margin-left:700px; margin-top:15px;}
</style>

<script type="text/javascript">
    function select_asoc(value1){
        window.location = "index.php?link=facturi&asoc_id=" + value1;
    }

    function select_factura(value1, value2){
        window.location = "index.php?link=facturi&asoc_id=" + value1 + "&tipFactura=" + value2;
    }

    function select_furnizor(value1, value2, value3){
        window.location = "index.php?link=facturi&asoc_id=" + value1 + "&tipFactura=" + value2 + "&furnizor=" + value3;
    }

    function select_scara(value1, value2, value3){
        window.location = "index.php?link=facturi&asoc_id=" + value1 + "&tipFactura=" + value2 + "&scara_id=" + value3;
    }

    function select_furnizor_1(value1, value2, value3, value4) {
        window.location = "index.php?link=facturi&asoc_id=" + value1 + "&tipFactura=" + value2 + "&scara_id=" + value3 + "&furnizor=" + value4;
    }

    function verificaBifat(bifat, id){
        //if (document.getElementById('nrApartamente').value == '')
        //{
        //    document.getElementById('nrApartamente').value = 0;
        //}
        if (bifat == true){
         //   document.getElementById('nrApartamente').value ++;
            document.getElementById(id).disabled = false;
        }
        else
        {
        //    document.getElementById('nrApartamente').value --;
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

    function verificaBifat2(bifat, id){
        if (bifat == true){
            document.getElementById(id).disabled = false;
        }
        else
        {
            document.getElementById(id).disabled = true;
            document.getElementById(id).value = '';
        }
    }

    function putAll(bifat, valoare){
        if (bifat == true){
            //document.getElementById('nrApartamente').value = valoare;
            for (var i=1; i <= valoare; i++){
                document.getElementById('cost-'+i).disabled = false;
            }
        } else {
            //document.getElementById('nrApartamente').value = 0;
            for (var i=1; i <= valoare; i++){
                document.getElementById('cost-'+i).disabled = true;
                document.getElementById('cost-'+i).value = '';
            }
        }
    }

    function putAll1(bifat, valoare){
        if (bifat == true){
            for (var i=1; i <= valoare; i++){
                document.getElementById('cost-'+i).disabled = false;
            }
        } else {
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

    function totConsAR(value1, value2){
        var oldCanti = document.getElementById('totCons').value;
        if (oldCanti != ''){
            oldCanti = parseInt(oldCanti);
        }

        if (value1 != ''){
            value1 = parseInt(value1);
        }

        if (value2 == 1){		//adaug indexul nou
            document.getElementById('totCons').value = '';
            document.getElementById('totCons').value = oldCanti + value1;
        } else
            if (value2 == 2){		//adaug indexul vechi
                document.getElementById('totCons').value = '';
                document.getElementById('totCons').value = oldCanti - value1;
            }
    }

</script>

<?php
include_once('util_facturi.php');

//print_r($_POST);
//die();
/*	SALVAREA FACTURILOR	*/
if ($_POST['insereaza'] == "OK") {
    //iau datele principale ale facturii (asociatie, tip factura, furnizor, scara)
    $asocId     = $_GET['asoc_id'];                 //id-ul asociatiei
    $tipFactura = $_GET['tipFactura'];          //fact. pe asoc, scara, ap, locatari
    $furnizor   = $_GET['furnizor'];              //id-ul furnizorului

    if ($tipFactura != 1) {                      //daca nu e factura pentru toata asociatia
        $scaraId = $_GET['scara_id'];       	//salvez id-ul scarii
    }

    //aflu numele serviciului
    $afluServiciu = "SELECT servicii.serv_id, servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id AND servicii.nivel=".$tipFactura;
    $afluServiciu = mysql_query($afluServiciu) or die ("#Facturi: 90 -- Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());

    $servId = mysql_result($afluServiciu, 0, 'serv_id');
    $serviciu = mysql_result($afluServiciu, 0, 'serviciu');

    $tipulServiciului = "SELECT * FROM servicii WHERE serv_id=".$servId;
    $tipulServiciului = mysql_query($tipulServiciului) or die ("#Facturi: 91 -- Nu pot afla informatii despre serviciu<br />".mysql_error());

    $areIndecsi = mysql_result($tipulServiciului, 0, 'cu_indecsi');

    // tratez cazurile separate - apa rece, apa calda, iluminat, incalzire
    if (strtolower($serviciu) == 'apa rece') {
        echo "Factura de apa rece trebuie introdusa prin formularul special";
    } else
    if (strtolower($serviciu) == 'apa calda') {
        echo "Factura de apa calda trebuie introdusa prin formularul special";
    } else
    if (strtolower($serviciu) == 'iluminat') {
        echo "Factura de iluminat trebuie introdusa prin formularul special";
    } else
	if(strtolower($serviciu) == 'incalzire') {
        echo "Factura de incalzire trebuie introdusa prin formularul special";
    }
	if(strtolower($serviciu) == 'gaz') {
		echo "Factura de gaz trebuie introdusa prin formularul special";
	}
	else {
        if ($tipFactura == 1) {
            if ($areIndecsi == "da") {
                $facturaAsociatieCuIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`, `observatii`, `procesata`) VALUES
				(null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['valoareFactura']."', '".$_POST['diferenta']."', '".$_POST['pasante']."', '".$_POST['observatii']."', 0) ";
				$facturaAsociatieCuIndecsi = mysql_query($facturaAsociatieCuIndecsi) or die ("#Factura: 1004 -- Nu pot insera factura/asociatie - cu indecsi<br />".mysql_error());

            } else {
                $facturaAsociatieFaraIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES
				(null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['consum']."', '".$_POST['valoareFactura']."', '".$_POST['observatii']."', 0)";
                $facturaAsociatieFaraIndecsi = mysql_query($facturaAsociatieFaraIndecsi) or die ("#Facturi: 1005 -- Nu pot salva factura/asociatie fara indecsi<br />".mysql_error());
            }
        }

        if ($tipFactura == 2) {
            if ($areIndecsi == "da") {
                $facturaScaraCuIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`, `observatii`, `procesata`) VALUES
				(null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['valoareFactura']."', '".$_POST['diferenta']."', '".$_POST['observatii']."', 0) ";
                //var_dump($facturaScaraCuIndecsi);
                //die();
                $facturaScaraCuIndecsi = mysql_query($facturaScaraCuIndecsi) or die ("#Factura: 1004 -- Nu pot insera factura/scara - cu indecsi<br />".mysql_error());

            } else {
                $facturaScaraFaraIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `observatii`, `procesata`) VALUES
				(null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['consum']."', '".$_POST['valoareFactura']."', '".$_POST['observatii']."', 0)";
                $facturaScaraFaraIndecsi = mysql_query($facturaScaraFaraIndecsi) or die ("#Facturi: 1005 -- Nu pot salva factura/scara fara indecsi<br />".mysql_error());
            }
        }

        if ($tipFactura == 3) {
            foreach ($_POST as $valoare=>$cheie) {
                $peBucati = explode("-", $valoare);

                if ($peBucati[0] == "loc") {
                    $locatar[] = $peBucati[1];
                }
            }
            $locatari = implode(",",$locatar);
            $putFacturaApartamente = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `nrRate`,`numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `cantitate`, `cost`, `locatari`, `observatii`, `procesata`) VALUES
			(null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['nrRate']."', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['nrApartamente']."', '".$_POST['valoareFactura']."', '$locatari', '".$_POST['observatii']."', 0)";
            $putFacturaApartamente = mysql_query($putFacturaApartamente) or die ("#Facturi: 1006 -- Nu pot insera factura pe apartamente<br />".mysql_error());
        }

        if ($tipFactura == 4) {
            foreach ($_POST as $cheie=>$valoare) {
                $peBucati = explode ("-", $cheie);

                /*if ($peBucati[0] == "loc") {
                    if ($valoare != "") {
                        $locatar[] = $peBucati[1];
                    }
                }*/

                if ($peBucati[0] == "cost") {
                    //if ($_POST['loc-'.$peBucati[1]] != ""){
                    $locatar[] = $peBucati[1];
                    $cost[] = $valoare;
                    //}
                }


            }

            if ($locatar != null) {
                $locatari = implode(",",$locatar);
            }

            if ($cost != null) {
                $cost = implode(",",$cost);
            }

            $insertFacturaPeLocatari = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `nrRate`, `luna`, `indexNou`, `indexVechi`, `cantitate`, `cost`, `ppu`, `locatari`, `observatii`, `procesata`)
			VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['nrRate']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['diferenta']."', '".$_POST['valoareFactura']."', '$cost', '$locatari', '".$_POST['observatii']."', 0)";
            $insertFacturaPeLocatari = mysql_query($insertFacturaPeLocatari) or die ("#Facturi: 1007 -- Nu pot salva factura pe locatari<br />".mysql_error());
        }

		$getFacturaIdSQL = "SELECT fact_id FROM facturi WHERE asoc_id='$asocId' AND tipServiciu='$servId' AND numarFactura='".$_POST['numarFactura']."' AND serieFactura='".$_POST['serieFactura']."' ORDER BY `fact_id` DESC ;";
		$getFacturaId =  mysql_query($getFacturaIdSQL) or die ("Eroare citirea indexului facture curente <br />".$getFacturaIdSQL."<br />".mysql_error());
		$getFacturaId = mysql_result($getFacturaId, 0, 'fact_id');

		$dataScadentaTrans = explode('-', $_POST['dataScadenta']);
		$dataScadentaTrans = $dataScadentaTrans[2].'-'.$dataScadentaTrans[1].'-'.$dataScadentaTrans[0];

		if($scaraId == null) $scaraId = "NULL";

		$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`,  `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
	        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', $scaraId, NULL, '$furnizor', '$servId', '$getFacturaId', '".date('Y-m-d')."', '".$dataScadentaTrans."', '".$_POST['numarFactura']."/".$_POST['serieFactura']."', 'Factura', '".$_POST['valoareFactura']."', NULL, NULL);";
		mysql_query($insertFisaFurnizoriSQL) or die ("#Factura: 1000 -- Eroare la introducerea facturi in fisa_furnizori <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());

		if (isset($_FILES['file'])) {
			if ($_FILES['file']['error'] == 4){
				echo "<p>Factura a fost salvata fara fisier atasat.</p>";
			} else if ($_FILES['file']['type'] != "application/pdf") {
                echo "<p>Se accepta doar fisere PDF pentru upload,  factura a fost salvata fara fisier atasat.</p>";
            } else {
                $factFileNewName = date('Y-m-d').'_'.$asocId.'_'.$servId.'_'.$getFacturaId.'_'.$_FILES['file']['name'];

                if (file_exists('doc/'.$factFileNewName)) die ('Fisierul exista');


                move_uploaded_file($_FILES['file']['tmp_name'], 'doc/'.$factFileNewName);


                $file_insert = 'INSERT INTO `doc` (`id`, `tip`, `filename`, `time`, `obs`)
                                VALUES (NULL, \'factura\', \''.'doc/'.addslashes($factFileNewName).'\', \''.date('Y-m-d H:i:s').'\', NULL)';

                $file_query = mysql_query($file_insert) or die('Nu pot incarca documentul atasat in BD.');

                $fact_update_s = 'UPDATE facturi SET observatii='.mysql_insert_id().' WHERE fact_id='.$getFacturaId;
                $fact_update_q = mysql_query($fact_update_s) or die('Nu pot actualiza id-ul documentului pt factura introdusa.');


			}
		}

    }

    //print_r($_POST);
    unset($_POST);
}
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                Utile Facturi                         //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

/*	FUNCTII PENTRU TABEL INTRODUCERE FACTURA	*/
function dateFactura($val) {
    if ($val != 0) {
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
    echo '</tr>';
}

function infoPlati($val) {
    if ($val != 0) {
        $val = $val."-";
    } else {
        $val = "";
    }

    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
    echo '<td width="125px">Debite</td>';
    echo '<td width="125px">Penalizari</td>';
    echo '<td width="125px">Factura</td>';
    echo '<td width="125px">Luna</td>';
    echo '<td width="125px">&nbsp;</td>';
    echo '</tr>';

    echo '<tr bgcolor="#DDDDDD">';
    echo '<td><input type="text" name="'.$val.'debite" disabled /></td>';
    echo '<td><input type="text" name="'.$val.'penalizari" disabled/></td>';
    echo '<td><input type="file" name="'.$val.'file" /></td>';
    echo '<td>';
    echo '<select name="'.$val.'luna" style="width:125px">';
    echo Util_facturi::getSelectLuna($_GET['asoc_id'], (isset($_POST['luna']) ? $_POST['luna'] : null));
    echo '</select>';
    echo '</td>';
    echo '<td>&nbsp;</td>';
    echo '</tr>';
}

function infoFonduri($val) {
    if ($val != 0) {
        $val = $val."-";
    } else {
        $val = "";
    }
/*
    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
    echo '<td width="125px">Luna</td>';
    echo '<td width="125px">Valoare Factura</td>';
    echo '<td width="125px">Nr. Apartamente</td>';
    echo '<td width="125px">Nr. Rate</td>';
    echo '<td width="125px">Observatii</td>';
    echo '</tr>';

    echo '<tr bgcolor="#DDDDDD">';
    echo '<td>';
    echo '<select name="luna" style="width:125px">';
	echo Util_facturi::getSelectLuna($_GET['asoc_id'], (isset($_POST['luna']) ? $_POST['luna'] : null));
    echo '</select>';
    echo '</td>';
    echo '<td><input type="text" name="'.$val.'valoareFactura" /></td>';
    echo '<td><input type="text" name="'.$val.'nrApartamente" id="nrApartamente" readonly="readonly"/></td>';
    echo '<td><input type="text" name="'.$val.'nrRate" /></td>';
    echo '<td><input type="file" name="'.$val.'file" /></td>';
    echo '</tr>';*/
}

function putIndecsi($val) {
    if ($val != 0) {
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
    echo '<td><input type="text" name="'.$val.'indexVechi" id="'.$val.'indexVechi" onkeyup="canti(\''.$val.'\');"/></td>';
    echo '<td><input type="text" name="'.$val.'indexNou" id="'.$val.'indexNou" onkeyup="canti(\''.$val.'\');"/></td>';
    echo '<td><input type="text" name="'.$val.'diferenta" id="'.$val.'diferenta" readonly="readonly"/></td>';
    //echo '<td><input type="text" name="'.$val.'cost" /></td>';
    echo '<td colspan="2">&nbsp;</td>';
    echo '</tr>';
}

function putPasante() {
    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
    echo '<td colspan="2">Consum scari (m<sup>3</sup>)</td>';
    echo '<td colspan="3">&nbsp;</td>';
    echo '</tr>';

    //selectez scarile care au pasant
    $scariPas = "SELECT * FROM scari_setari WHERE asoc_id=".$_GET['asoc_id']." AND pasant='da'";
    $scariPas = mysql_query($scariPas) or die ("#Facturi: 21 -- Nu pot afla scarile care au pasant<br />".mysql_error());

    $i = 0;
    while ($sP = mysql_fetch_array($scariPas)) {
        $scaraCurr = "SELECT * FROM scari WHERE scara_id=".$sP['scara_id'];
        $scaraCurr = mysql_query($scaraCurr) or die ("#Facturi: 22 -- Nu pot afla detaliile scarii care are pasant<br />".mysql_error());

        if ($i % 2 == 0) {
            $culoare = "#DDD";
        } else {
            $culoare = "#EEE";
        }
        echo '<tr bgcolor="'.$culoare.'" valign="top">';
        echo '<td>Blocul '.mysql_result($scaraCurr, 0, 'bloc').', scara '.mysql_result($scaraCurr, 0, 'scara').'</td>';
        echo '<td><input type="text" name="P-'.$sP['scara_id'].'" /></td>';
        echo '<td colspan="3">&nbsp;</td>';
        echo '</tr>';
        $i++;
    }
}

function putApometre() {
    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
    echo '<td width="125px">Bloc</td>';
    echo '<td width="125px">Index Nou</td>';
    echo '<td width="125px">Index Vechi</td>';
    echo '<td width="125px">Diferenta</td>';
    //echo '<td width="125px">Cost</td>';
    echo '<td width="125px">&nbsp;</td>';
    echo '</tr>';

    $nrBlocuri = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'].' GROUP BY bloc';
    $nrBlocuri = mysql_query($nrBlocuri) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());
    $nrBlocuri = mysql_num_rows($nrBlocuri);

    for ($i=1; $i<=$nrBlocuri; $i++) {
        if ($i % 2 == 0) {
            $culoare = "#DDD";
        } else {
            $culoare = "#EEE";
        }
        echo '<tr bgcolor="'.$culoare.'">';
        $afisScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'].' GROUP BY bloc';
        $afisScara = mysql_query($afisScara) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());

        echo '<td>Blocul '.mysql_result($afisScara, ($i-1), 'bloc').'</td>';
        echo '<td><input type="text" name="'.$i.'-indexNou" id="'.$i.'-indexNou" onkeyup="canti(\''.$i.'-\');" onblur="totConsAR(this.value,1)"/></td>';
        echo '<td><input type="text" name="'.$i.'-indexVechi" id="'.$i.'-indexVechi" onkeyup="canti(\''.$i.'-\');" onblur="totConsAR(this.value,2)"/></td>';
        echo '<td><input type="text" name="'.$i.'-diferenta" id="'.$i.'-diferenta" readonly="readonly"/></td>';
        //echo '<td><input type="text" name="'.$i.'-cost"/></td>';
        echo '<td width="125px">&nbsp;</td>';
        echo '</tr>';
    }

    if ($i % 2 == 0) {
        $culoare = "#DDD";
    } else {
        $culoare = "#EEE";
    }
    echo '<tr bgcolor="'.$culoare.'">';
    echo '<td><b>Total Consum</td>';
    echo '<td colspan="2">&nbsp;</td>';
    echo '<td><input type="text" name="totCons" id="totCons" readonly="readonly"/></td>';
    echo '<td>&nbsp;</td>';
    echo '</tr>';
}

function putContoare() {
    $contoare = "SELECT * FROM scari_setari WHERE scara_id=".$_GET['scara_id'];
    $contoare = mysql_query($contoare) or die ("#Facturi: 30 -- Nu pot afla numarul de contoare<br />".mysql_error());

    $tipContor = array("General", "Lift", "Centrala");

    $nrContoare[] = 1;

    $contorLift = mysql_result($contoare, 0, 'contor_lift');
    $nrContoare[] = $contorLift;

    $contorCentrala = mysql_result($contoare, 0, 'contor_centrala');
    $nrContoare[] = $contorCentrala;

    for ($cont=0; $cont<count($nrContoare); $cont++) {
        if ($nrContoare[$cont] == 1) {
            echo '<tr bgcolor="#AAAAAA" style="color:#000000">';
            echo '<td colspan="5"><strong>Contoar '.$tipContor[$cont].'</strong></td>';
            echo '</tr>';

            dateFactura(($cont+1));
            infoPlati(($cont+1));
            putIndecsi(($cont+1));
        }
    }
}

function putApaRece() {
    dateFactura(0);
    infoPlati(0);
    putPasante();
    putApometre();
}

function putApaRecePentruApaCalda() {
    echo '<tr bgcolor="#DDDDDD" style="color:#000000">';
    echo '<td colspan="5"><strong>Apa Rece pentru Apa Calda - ApaVital</strong></td>';
    echo '</tr>';

    dateFactura(37);
    infoPlati(37);

    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
    echo '<td>Consum ( m<sup>3</sup> )</td>';
    //echo '<td>Cost</td>';
    echo '<td colspan="4">&nbsp;</td>';
    echo '</tr>';

    echo '<tr bgcolor="#DDDDDD">';
    echo '<td><input type="text" name="37-consum" value="'.$_POST['37-consum'].'" /></td>';
    //echo '<td><input type="text" name="24-cost" value="'.$_POST['24-cost'].'" /></td>';
    echo '<td colspan="4">&nbsp;</td>';
    echo '</tr>';
}

function putAgentTermicPentruApaCalda() {
    echo '<tr bgcolor="#DDDDDD" style="color:#000000">';
    echo '<td colspan="5"><strong>Agent termic pentru Apa Calda - CET</strong></td>';
    echo '</tr>';

    dateFactura(38);
    infoPlati(38);
    putIndecsi(38);
}

function incalzire() {
    dateFactura(0);
    infoPlati(0);
    putIndecsi(0);

    //daca am locatari care sa aiba gigacalorimetre sau repartitoare,
    //afisez locatarii, daca nu, factura se imparte pe suprafata

    //verific daca au repartitoare sau gigacalorimetre
    $checkType = "SELECT SUM(tip_incalzire) FROM locatari WHERE scara_id=".$_GET['scara_id']." AND centrala='nu'";
    $checkType = mysql_query($checkType) or die ("Nu pot face suma<br />".mysql_error());
    $checkType = mysql_result($checkType, 0, 'SUM(tip_incalzire)');

    //die($checkType);

    if ($checkType != 0) {		//sunt locatari cu repartitoare sau gigacalorimetre
        //se verifica daca sunt gigacalorimetre
        $gigacal = Util::getIncalzireScara($_GET['scara_id']) == '2';

        // in acest caz trebuie sa determin cine are centrala si cine plateste restu
        $locatari = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id'];
        if($gigacal) $locatari .= " AND tip_incalzire='2'";
        $locatari = mysql_query($locatari) or die ("#Factura: 50 -- Nu pot afla numele locatarilor<br />".mysql_error());

        if (mysql_num_rows($locatari) != 0) {	//am locatari care au repartitoare sau gigacalorimetre

            echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
            echo '<td colspan="2">Locatar</td>';
            if($gigacal) {
                echo '<td>Index Vechi</td>';
                echo '<td>Index Nou</td>';
            } else {
                echo '<td>Cost</td>';
            }
            echo '<td colspan="2">&nbsp;</td>';
            echo '</tr>';

            if($gigacal) //daca este cineva cu gigacalorimetru caut indexul trecut
                $lastIndex = Util::getGigacalorimetruLastIndex($_GET['asoc_id'],$_GET['scara_id'],$_GET['tipFactura'],'25');
            $nrLoc = 0;

            while ($row = mysql_fetch_array($locatari)) {
                if ($nrLoc %2 == 0) {
                    $culoare = "#DDDDDD";
                } else {
                    $culoare = "#FFFFFF";
                }

                switch ($row['tip_incalzire']) {
                    case 1: $culoareCost = "#D7D9A7";	//repartitoare
                        $culoareFont = "#000000";
                        break;
                    case 2: $culoareCost = "#C1836A";	//gigacalorimetre
                        $culoareFont = "#FFFFFF";
                        break;
                    default:$culoareCost = "#FFFFFF";	//nu are nici una nici alta
                        $culoareFont = "#000000";
                        break;
                }
                if ( $row['centrala'] == "da" && $row['tip_incalzire'] == 0) {			//este debransat
                    $culoareCost = "#40182E";
                    $culoareFont = "#FFFFFF";
                }

                echo '<tr bgcolor="'.$culoare.'">';
                echo '<td width="125px">Ap. '.$row['ap'].'</td>';
                echo '<td width="125px">'.$row['nume'].'</td>';
                if($row['tip_incalzire'] == 2) {// daca are gigacalorimetru
                    echo '<td width="125px"><input '.($lastIndex != null ? 'readonly="readonly" value="'.$lastIndex[$row['loc_id']].'"' : '').' style="background-color:'.$culoareCost.'; color: '.$culoareFont.'" type="text" name="indexGigacalVechi-'.($row['loc_id']).'" id="indexGigacalVechi-'.($nrLoc+1).'" /></td>';
                    echo '<td width="125px"><input style="background-color:'.$culoareCost.'; color: '.$culoareFont.'" type="text" name="indexGigacalNou-'.($row['loc_id']).'" id="indexGigacalNou-'.($nrLoc+1).'" /></td>';
                }else
                    echo '<td width="125px"><input style="background-color:'.$culoareCost.'; color: '.$culoareFont.'" type="text" name="cost-'.($row['loc_id']).'" id="cost-'.($nrLoc+1).'" /></td>';
                echo '<td colspan="2">&nbsp;</td>';
                echo '</tr>';

                $nrLoc ++;
            }

            echo '<tr bgcolor="#BBBBBB" align="left">
					<td colspan="5">
						<table>
							<tr>
								<td width="30px" style="background: #D7D9A7">&nbsp;</td>
								<td width="70px"> Repartitoare </td>
								<td width="25px"> &nbsp;</td>

								<td width="30px" style="background: #C1836A">&nbsp;</td>
								<td width="70px"> Gigacalorimetre </td>
								<td width="25px"> &nbsp;</td>

								<td width="30px" style="background: #FFFFFF">&nbsp;</td>
								<td width="70px"> Necontorizat </td>
								<td width="25px"> &nbsp;</td>

								<td width="30px" style="background: #40182E">&nbsp;</td>
								<td width="70px"> Debransat </td>
								<td width="25px"> &nbsp;</td>

								<td width="125px"> &nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>';
        } else {
            echo '<tr bgcolor="#DDDDDD">';
            echo '<td colspan="5">Nu sunt locatari inregistrati pe aceasta scara</td>';
            echo '</tr>';
        }
    }
}

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                Utile pt. Asoc/Sc/Fur                 //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

/*	SELECTUL PENTRU AFISAREA ASOCIATIILOR	*/
if ($_GET['asoc_id'] != "") {
    $selectAsoc = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
} else {
    $selectAsoc = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
}
$selectAsoc = mysql_query($selectAsoc) or die ("#Facturi: 1 -- Nu pot selecta asociatiile<br />".mysql_error());

$asociatie = '';
while ($asoc = mysql_fetch_array($selectAsoc)) {
    $asociatie .= '<option value="'.$asoc[0].'">'.$asoc[1].'</option>';
}

/*	SELECTUL PENTRU AFISAREA SCARILOR	*/
if ($_GET['asoc_id'] != '') {
    if ($_GET['scara_id'] != '') {
        $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id'];
    } else {
        $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
    }
    $selectScara = mysql_query($selectScara) or die ("#Facturi: 2 -- Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());

    while ($scari = mysql_fetch_array($selectScara)) {
        $scara .= '<option value="'.$scari[0].'">Bloc '.$scari[5].', scara '.$scari[2].'</option>';
    }
}

/*	SELECTUL PENTRU AFISAREA TIPULUI DE FACTURA	*/
if ($_GET['tipFactura'] != '') {
    $tipFact = "SELECT * FROM tip_factura WHERE id<>".$_GET['tipFactura'];
} else {
    $tipFact = "SELECT * FROM tip_factura";
}
$tipFact = mysql_query($tipFact) or die ("#Facturi: 3 -- Nu pot selecta tipul de factura<br />".mysql_error());

while ($nivel = mysql_fetch_array($tipFact)) {
    $tipFactura .= '<option value="'.$nivel[0].'">'.$nivel[1].'</option>';
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE ASOCIATIE	*/
if ($_GET['asoc_id'] != '' && $_GET['tipFactura'] != '') {
    if ($_GET['furnizor'] != '') {
        $furniz = "SELECT F.furnizor, F.fur_id FROM asociatii_furnizori A, furnizori F WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor'];
    } else {
        $furniz = "SELECT F.furnizor, F.fur_id FROM asociatii_furnizori A, furnizori F WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id";
    }
    $furniz = mysql_query($furniz) or die ("#Factura: 4 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

    while ($furn = mysql_fetch_array($furniz)) {
        $furnizor1 .= '<option value="'.$furn['fur_id'].'">'.$furn['furnizor'].'</option>';
    }
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE SCARA	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != '') {
    if ($_GET['furnizor'] != '') {
        $furniz2 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND FS.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND F.fur_id=A.fur_id AND SE.nivel=2";
    } else {
        $furniz2 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND FS.fur_id=A.fur_id AND FS.serv_id=SE.serv_id AND F.fur_id=A.fur_id AND SE.nivel=2";
    }
    $furniz2 = mysql_query($furniz2) or die ("#Factura: 5 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

	$furnizor2;
    while ($furn2 = mysql_fetch_array($furniz2)) {
        $furnizor2 .= '<option value="'.$furn2['fur_id'].'">'.$furn2['furnizor'].'</option>';
    }
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE APARTAMENT	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != '') {
    if ($_GET['furnizor'] != '') {
        $furniz3 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=3";
    } else {
        $furniz3 = "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=3";
    }
    $furniz3 = mysql_query($furniz3) or die ("#Factura: 6 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

    while ($furn3 = mysql_fetch_array($furniz3)) {
        $furnizor3 .= '<option value="'.$furn3['fur_id'].'">'.$furn3['furnizor'].'</option>';
    }
}

/*	SELECTUL PENTRU AFISAREA FURNIZORULUI IN FUNCTIE DE LOCATAR	*/
if ($_GET['scara_id'] != '' && $_GET['tipFactura'] != '') {
    if ($_GET['furnizor'] != '') {
        $furniz4 = "SELECT DISTINCT F.furnizor, F.fur_id 
                    FROM furnizori_as FAS, furnizori F, furnizori_servicii FS, servicii SE 
                    WHERE (FAS.scara_id=".$_GET['scara_id']." OR FAS.asoc_id=".$_GET['asoc_id'].")
                    AND F.fur_id=FAS.fur_id 
                    AND FS.serv_id=SE.serv_id 
                    AND FS.fur_id=FAS.fur_id 
                    AND F.fur_id<>".$_GET['furnizor']."
                    AND SE.nivel=4";

      //  "SELECT F.furnizor, F.fur_id FROM scari_furnizori A, furnizori F, furnizori_servicii FS, servicii SE WHERE A.scara_id=".$_GET['scara_id']." AND F.fur_id=A.fur_id AND F.fur_id<>".$_GET['furnizor']." AND FS.serv_id=SE.serv_id AND FS.fur_id=A.fur_id AND SE.nivel=4";
    } else {
        $furniz4 = "SELECT DISTINCT F.furnizor, F.fur_id 
                    FROM furnizori_as FAS, furnizori F, furnizori_servicii FS, servicii SE 
                    WHERE (FAS.scara_id=".$_GET['scara_id']." OR FAS.asoc_id=".$_GET['asoc_id'].")
                    AND F.fur_id=FAS.fur_id 
                    AND FS.serv_id=SE.serv_id 
                    AND FS.fur_id=FAS.fur_id 
                    AND SE.nivel=4";
    }
    $furniz4 = mysql_query($furniz4) or die ("#Factura: 7 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

    while ($furn4 = mysql_fetch_array($furniz4)) {
        $furnizor4 .= '<option value="'.$furn4['fur_id'].'">'.$furn4['furnizor'].'</option>';
                        }
                    }



//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                 Formular Asoc/Sc/Fur                 //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
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
<?php  if($_GET['asoc_id']==null) {
    echo '<option value="">----Alege----</option>';
                    }  else {
                        $afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
                        $afisAsoc = mysql_query($afisAsoc) or die ("#Facturi: 8 -- Nu pot selecta asociatiile<br />".mysql_error());

                        echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
                    }
                    ?>
                    <?php echo $asociatie; ?>
                </select>
            </td>
        </tr>
<?php if ($_GET['asoc_id'] != "") { ?>
        <tr>
            <td width="173" align="left" bgcolor="#CCCCCC">( 2 ) Alegeti tipul de factura:</td>
            <td width="215" align="left" bgcolor="#CCCCCC">
                <select onChange="select_factura(<?php  echo $_GET['asoc_id']; ?>,this.value)">
            <?php if($_GET['tipFactura']==null) {
                echo '<option value="">----Alege----</option>';
            }  else {
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
        if ($_GET['tipFactura'] == 1) {
            echo '<tr>';
    echo '<td width="173" align="left" bgcolor="#CCCCCC">( 3 ) Alegeti furnizorul:</td>';
            echo '<td width="215" align="left" bgcolor="#CCCCCC">';
            echo '<select style="width:125px" onchange="select_furnizor('.$_GET['asoc_id'].','.$_GET['tipFactura'].', this.value)">';
            if ($_GET['furnizor'] == null) {
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
            if ($_GET['scara_id'] == null) {
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

    switch ($_GET['tipFactura']) {
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
    </table>
</div>

<?php
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                 Formular Facturi                     //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
?>

<br clear="left" />

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="insereaza" value="OK" />
    <table width="750" style="float:left;  margin-top:10px; background-color:#BBBBBB;">
        <?php
        /**	IN CAZUL IN CARE AM SELECTAT TOT, CONTINUI	**/
        $potAfisa = 0;
        if ($_GET['asoc_id'] != "" && $_GET['tipFactura'] != "") {
            if ($_GET['tipFactura'] == "1" && $_GET['furnizor'] != "") {
                $potAfisa = 1;
            } else
            if ($_GET['scara_id'] != "" && $_GET['furnizor'] != "") {
                $potAfisa = 1;
            }
        }

        if ($potAfisa == 1) {
            $furnizor = $_GET['furnizor'];
            $tipPlata = $_GET['tipFactura'];

            $afluFurnizor = "SELECT * FROM furnizori WHERE fur_id=".$furnizor;
            $afluFurnizor = mysql_query($afluFurnizor) or die ("Nu pot afla furnizorul pentru afisarea tabelului<br />".mysql_error());

            $afluServiciu = "SELECT servicii.serviciu, servicii.cu_indecsi FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id AND servicii.nivel=".$tipPlata;
            $afluServiciu = mysql_query($afluServiciu) or die ("Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());

            $eCuIndecsi = mysql_result($afluServiciu, 0, 'cu_indecsi');

            echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($afluFurnizor, 0, 'furnizor').' - Serviciu facturat: '.mysql_result($afluServiciu, 0, 'serviciu').'</strong></td></tr>';
            echo '<tr bgcolor="#CCC"><td colspan="5"> &nbsp; </td></tr>';

            if (strtolower(mysql_result($afluServiciu, 0, 'serviciu')) == 'apa rece') {
                echo '<tr colspan="5"><td>Factura de apa rece trebue introdusa prin formularul special</td></tr>'; die();
            } else if (strtolower(mysql_result($afluServiciu, 0, 'serviciu')) == 'iluminat') {
                echo '<tr colspan="5"><td>Factura de iluminat trebue introdusa prin formularul special</td></tr>'; die();
            } else if (strtolower(mysql_result($afluServiciu, 0, 'serviciu')) == 'apa calda') {
                echo '<tr colspan="5"><td>Factura de apa calda trebue introdusa prin formularul special</td></tr>'; die();
            } else if (strtolower(mysql_result($afluServiciu, 0, 'serviciu')) == 'incalzire') {
                echo '<tr colspan="5"><td>Factura de incalzite trebue introdusa prin formularul special</td></tr>'; die();
            } else if (strtolower(mysql_result($afluServiciu, 0, 'serviciu')) == 'gaz') {
            	echo '<tr colspan="5"><td>Factura de gaz trebue introdusa prin formularul special</td></tr>'; die();
            } else {
                $tipServiciu = "SELECT servicii as serviciu, cu_indecsi as cu_indecsi, fonduri as fonduri FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$_GET['furnizor']." AND furnizori_servicii.serv_id=servicii.serv_id";
                $tipServiciu = mysql_query($tipServiciu) or die ("#Facturi: 40 -- Nu pot selecta tipul de serviciu<br />".mysql_error());

                dateFactura(0);
                infoPlati(0);

                if (mysql_result($tipServiciu, 0, 'cu_indecsi') == 'da') {
                    putIndecsi(0);
                } else if (mysql_result($tipServiciu, 0, 'fonduri') == 'da') {
                    infoFonduri(0);
                }

                //verific tipul de factura
                //if ($_GET['tipFactura'] == 1) {
                //    dateFactura(0);
                //}

                if ($_GET['tipFactura'] == 3) {	//pe apartament

                    $locatari = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id'];
                    $locatari = mysql_query($locatari) or die ("#Factura: 50 -- Nu pot afla numele locatarilor<br />".mysql_error());

                    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
                    echo '<td colspan="2" width="250px">Locatar</td>';
                    echo '<td colspan="3">&nbsp;</td>';
                    echo '</tr>';

                    if (mysql_num_rows($locatari) != 0) {
                        $nrLoc = 0;

                        while ($row = mysql_fetch_array($locatari)) {
                            if ($nrLoc %2 == 0) {
                                $culoare = "#DDDDDD";
                            } else {
                                $culoare = "#FFFFFF";
                            }
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
                } else if ($_GET['tipFactura'] == 4) {	//pe locatar

                    $locatari = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id'];
                    $locatari = mysql_query($locatari) or die ("#Factura: 50 -- Nu pot afla numele locatarilor<br />".mysql_error());

                    echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
                    echo '<td colspan="2">Locatar</td>';
                    echo '<td>Cost</td>';
                    echo '<td colspan="2">&nbsp;</td>';
                    echo '</tr>';

                    if (mysql_num_rows($locatari) != 0) {
                        $nrLoc = 0;

                        while ($row = mysql_fetch_array($locatari)) {
                            if ($nrLoc %2 == 0) {
                                $culoare = "#DDDDDD";
                            } else {
                        $culoare = "#FFFFFF";
                    }
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
