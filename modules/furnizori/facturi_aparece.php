<?php
if ($_POST['insereaza'] == "OK") {
    $asocId = $_GET['asoc_id'];                 //id-ul asociatiei
    $tipFactura = 1;          //fact. pe asoc, scara, ap, locatari
    $furnizor = $_POST['fur_id'];              //id-ul furnizorului
    //aflu numarul de scari si numarul de pasante
        $afluServiciuSQL = "SELECT servicii.serv_id, servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
        $afluServiciu = mysql_query($afluServiciuSQL) or die ("#Facturi: 90 -- Nu pot afla serviciul pentru introducerea facturilor<br />".$afluServiciuSQL."<br />".mysql_error());

        $servId = mysql_result($afluServiciu, 0, 'serv_id');

        $dateAsoc = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
        $dateAsoc = mysql_query($dateAsoc) or die ("#Facturi: 100 -- Nu pot afla detalii despre asociatie<br />".mysql_error());

        $nrScari = mysql_result($dateAsoc, 0, 'nr_scari');
        $nrPasante = mysql_result($dateAsoc, 0, 'pasante');

        $pasante = array();
        $indexVechi = array();
        $indexNou = array();
        $diferenta = array();

        foreach ($_POST as $cheie=>$valoare) {
            $peBucati = strrpos($cheie, "-");
            $peBucati = array(substr($cheie, 0, $peBucati ? $peBucati : 100), substr($cheie, $peBucati ? $peBucati+1 : 0, $peBucati ? 100 : 0));

            //verific daca e pasant sau index sau consum
            if ($peBucati[0] == "P") {
                if ($valoare == "") {
                    $valoare = 0;
                }
                $pasante[] = $valoare;
            	//$peBucati[1].'|'.$valoare;
            }

            if ($peBucati[1] == "indexVechi") {
                if ($valoare == "") {
                    $valoare = 0;
                }
                $indexVechi[] = $valoare;
            	//$peBucati[0].'|'.$valoare;
            }

            if ($peBucati[1] == "indexNou") {
                if ($valoare == "") {
                    $valoare = 0;
                }
                $indexNou[] = $valoare;
            	//$peBucati[0].'|'.$valoare;
            }

        	if ($peBucati[0] == "totCons") {
        		if ($valoare == "") {
        			die("Total Consum facturat nu este trecut!");
        		}
        		$cant = $valoare;
        	}

        }
        $pasante = implode(',', $pasante);

        $indexNou = implode(',', $indexNou);
        $indexVechi = implode(',', $indexVechi);
        //$diferenta = implode(',', $diferenta);

        $facturaApa = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`, `pasant`, `observatii`, `procesata`)
        VALUES (null, '".date('d-m-Y')."', '$asocId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '$indexNou', '$indexVechi', '".$_POST['valoareFactura']."', '$cant', '$pasante', '".$_POST['observatii']."', 0) ";
        $facturaApa = mysql_query($facturaApa) or die ("#Factura: 1000 -- Nu pot insera factura de apa rece<br />".mysql_error());


		$getFacturaIdSQL = "SELECT fact_id FROM facturi WHERE asoc_id='$asocId' AND tipServiciu='$servId' AND numarFactura='".$_POST['numarFactura']."' AND serieFactura='".$_POST['serieFactura']."' ORDER BY `fact_id` DESC ;";
		$getFacturaId =  mysql_query($getFacturaIdSQL) or die ("#Factura: 1000-'$t' -- Eroare citirea indexului facture curente <br />".$getFacturaIdSQL."<br />".mysql_error());
		$getFacturaId = mysql_result($getFacturaId, 0, 'fact_id');

		$dataScadentaTrans = explode('-', $_POST['dataScadenta']);
		$dataScadentaTrans = $dataScadentaTrans[2].'-'.$dataScadentaTrans[1].'-'.$dataScadentaTrans[0];


		$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`,  `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
	        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', NULL, NULL, '$furnizor', '$servId', '$getFacturaId', '".date('Y-m-d')."', '".$dataScadentaTrans."', '".$_POST['numarFactura']."/".$_POST['serieFactura']."', 'Factura', '".$_POST['valoareFactura']."', NULL, NULL);";
		mysql_query($insertFisaFurnizoriSQL) or die ("#Factura: 1000 -- Eroare la introducerea facturi in fisa_furnizori <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());

        if (isset($_FILES['file'])) {
            if ($_FILES['file']['error'] == 4){
                echo "<p>Factura a fost salvata fara fisier atasat.</p>";
            } else if ($_FILES['file']['type'] != "application/pdf") {
                echo "<p>Se accepta doar fisere PDF pentru upload,  factura a fost salvata fara fisier atasat.</p>";
            } else {
                $factFileNewName = date('Y-m-d').'_'.$asocId.'_'.$servId.'_'.$getFacturaId.'_'.addslashes($_FILES['file']['name']);

                if (file_exists('doc/'.$factFileNewName)) die ('Fisierul exista');


                move_uploaded_file($_FILES['file']['tmp_name'], 'doc/'.$factFileNewName);


                $file_insert = 'INSERT INTO `doc` (`id`, `tip`, `filename`, `time`, `obs`)
                                VALUES (NULL, \'factura\', \''.'doc/'.$factFileNewName.'\', \''.date('Y-m-d H:i:s').'\', NULL)';

                $file_query = mysql_query($file_insert) or die('Nu pot incarca documentul atasat in BD.');

                $fact_update_s = 'UPDATE facturi SET observatii='.mysql_insert_id().' WHERE fact_id='.$getFacturaId;
                $fact_update_q = mysql_query($fact_update_s) or die('Nu pot actualiza id-ul documentului pt factura introdusa.');


            }
        }

        echo "Factura a fost salvata cu succes";

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

while ($asoc = mysql_fetch_array($selectAsoc)) {
    $asociatie .= '<option value="'.$asoc[0].'">'.$asoc[1].'</option>';
}

include_once('util_facturi.php');
?>

<script type="text/javascript">
    function select_asoc(value1){
        window.location = "index.php?link=facturi_aparece&asoc_id=" + value1 + "&tipFactura=1" ;
    }

    function canti(valoare){
        var iNou = document.getElementById(valoare + 'indexNou').value;
        var iVechi = document.getElementById(valoare + 'indexVechi').value;

        if ((iNou != null) && (iVechi != null)){
            document.getElementById(valoare + 'diferenta').value = iNou - iVechi;
        }
    }

$(function () {
	$("#FactApaVit").submit(function() {
		var valid=true;
		var focus_el;
		$('#FactApaVit :text[class*="ver_req"]').each(function(index) {
			if(valid){
			var ck_req = /^[\w -.]{1,20}$/;
			if(!ck_req.test(this.value)) {
				valid=false;
				alert(this.name+" nu este completat !");
				focus_el = focus_el ? focus_el : this;
			}}
		})

		$('#FactApaVit :text[class*="ver_nr"]').each(function(index) {
			if(valid) {
				var ck_nr = /^[0-9.]{1,6}$/;
				if(!ck_nr.test(this.value)) {
					valid=false;
					alert(this.name+' nu este numar: "'+this.value+'"');
					focus_el = focus_el ? focus_el : this;
				}}
		})

		$(focus_el).focus();
		return valid;
	})
})
</script>

<div id="content" style="float:left;">
    <table width="400">
        <tr align="center">
            <td width="388" bgcolor="#CCCCCC" colspan="2"><strong>Inserare factura Apa Rece</strong></td>
        </tr>
        <tr>
            <td width="173" align="left" bgcolor="#CCCCCC">Alegeti asociatia:</td>
            <td width="215" align="left" bgcolor="#CCCCCC">
                <select onChange="select_asoc(this.value)">
                <?php  if($_GET['asoc_id']==null) {
                echo '<option value="">----Alege----</option>';
                } else {
                    $afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
                    $afisAsoc = mysql_query($afisAsoc) or die ("#Facturi: 8 -- Nu pot selecta asociatiile<br />".mysql_error());

                    echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
                }
                echo $asociatie;
                ?>
                </select>
            </td>
        </tr>
    </table>
</div>


<?php
/**	IN CAZUL IN CARE AM SELECTAT TOT, CONTINUI	**/
$_GET['tipFactura'] = 1;
if ($_GET['asoc_id'] != ""){

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                       Formular                       //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
?>


<form id="FactApaVit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="insereaza" value="OK" />
    <input type="hidden" name="tipFactura" value="1" />
    <table width="750" style="float:left;  margin-top:10px; background-color:#BBBBBB;">

    <?php
    $furniz = "SELECT F.furnizor as furnizor, F.fur_id as id FROM asociatii_furnizori A, furnizori F, servicii S, furnizori_servicii FS WHERE A.asoc_id=".$_GET['asoc_id']." AND F.fur_id=A.fur_id AND S.serviciu='apa rece' AND S.serv_id=FS.serv_id AND FS.fur_id=F.fur_id";
    $furniz = mysql_query($furniz) or die ("#Factura: 4 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

    echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($furniz, 0, 'furnizor').' - Serviciu facturat: Apa rece </strong></td></tr>';
    ?>
    <tr bgcolor="#CCC"><td colspan="5"> &nbsp; </td></tr>

    <tr bgcolor="#000000" style="color:#FFFFFF">
        <td width="125px">Serie Factura</td>
        <td width="125px">Numar Factura</td>
        <td width="125px">Data Emiterii</td>
        <td width="125px">Data Scadenta</td>
        <td width="125px">Valoare Factura</td>
    </tr>
    <tr bgcolor="#DDDDDD">
        <td><input type="text" class="ver_req" name="serieFactura" /></td>
        <td><input type="text" class="ver_req" name="numarFactura" /></td>
        <td><input type="text" class="ver_req datepicker" name="dataEmitere" /></td>
        <td><input type="text" class="ver_req datepicker" name="dataScadenta" /></td>
        <td><input type="text" class="ver_req" name="valoareFactura" id="valoareFactura" /></td>
    </tr>

    <tr bgcolor="#000000" style="color:#FFFFFF">
        <td width="125px">Debite</td>
        <td width="125px">Penalizari</td>
        <td width="125px">Factura</td>
        <td width="125px">Luna</td>
        <td width="125px">Furnizor</td>
    </tr>
    <tr bgcolor="#DDDDDD">';
        <td><input type="text" name="debite" disabled /></td>
        <td><input type="text" name="penalizari" disabled/></td>
        <td><input type="file" name="file" /></td>
        <td>
            <select name="luna" style="width:125px">
                <?php echo Util_facturi::getSelectLuna($_GET['asoc_id'], (isset($_POST['luna']) ? $_POST['luna'] : null)); ?>
            </select>
        </td>
        <td>
            <?php
            $furn_id = mysql_result($furniz, 0, 'id');
            $furn_nume = mysql_result($furniz, 0, 'furnizor');
            echo $furn_nume;
            ?>
            <input type="hidden" name="fur_id" value="<?php echo $furn_id; ?>" />

        </td>
    </tr>

    <?php
    //selectez scarile care au pasant
    $scariPas = "SELECT * FROM scari_setari WHERE asoc_id=".$_GET['asoc_id']." AND pasant='da' ORDER BY scara_id";
    $scariPas = mysql_query($scariPas) or die ("#Facturi: 21 -- Nu pot afla scarile care au pasant<br />".mysql_error());

    $i = 0;
    while ($sP = mysql_fetch_array($scariPas)) {
        $scaraCurr = "SELECT * FROM scari WHERE scara_id=".$sP['scara_id']." ORDER BY scara_id";
        $scaraCurr = mysql_query($scaraCurr) or die ("#Facturi: 22 -- Nu pot afla detaliile scarii care are pasant<br />".mysql_error());

        if ($i % 2 == 0) {
            $culoare = "#DDD";
        } else {
            $culoare = "#EEE";
        }
        echo '<tr bgcolor="'.$culoare.'" valign="top">';
        echo '<td>Blocul '.mysql_result($scaraCurr, 0, 'bloc').', scara '.mysql_result($scaraCurr, 0, 'scara').'</td>';
        echo '<td><input type="text" class="ver_nr ver_req" name="P-'.$sP['scara_id'].'" /></td>';
        echo '<td colspan="3">&nbsp;</td>';
        echo '</tr>';
        $i++;
    }?>

    <tr bgcolor="#000000" style="color:#FFFFFF">
        <td width="125px">Bloc</td>
        <td width="125px">Index Nou</td>
        <td width="125px">Index Vechi</td>
        <td width="125px">Diferenta</td>
        <td width="125px">&nbsp;</td>
    </tr>

    <?php
    $nrBlocuri = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'].' GROUP BY bloc';
    $nrBlocuri = mysql_query($nrBlocuri) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());

	//$nrBlocuri = mysql_num_rows($nrBlocuri);
	$i=1;
    while($bloc = mysql_fetch_assoc($nrBlocuri)) {
        if ($i % 2 == 0) {
            $culoare = "#DDD";
        } else {
            $culoare = "#EEE";
        }
        echo '<tr bgcolor="'.$culoare.'">';
        $afisScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'].' GROUP BY bloc';
        $afisScara = mysql_query($afisScara) or die ("Nu pot selecta scarile pentru citirea indecsilor<br />".mysql_error());

        echo '<td>Blocul '.$bloc['bloc'].'</td>';
        echo '<td><input type="text" class="ver_nr ver_req" name="'.$bloc['bloc'].'-indexNou" id="'.$bloc['bloc'].'-indexNou" onkeyup="canti(\''.$bloc['bloc'].'-\');" onblur="totConsAR(this.value,1)"/></td>';
        echo '<td><input type="text" class="ver_nr ver_req" name="'.$bloc['bloc'].'-indexVechi" id="'.$bloc['bloc'].'-indexVechi" onkeyup="canti(\''.$bloc['bloc'].'-\');" onblur="totConsAR(this.value,2)"/></td>';
        echo '<td><input type="text" name="'.$bloc['bloc'].'-diferenta" id="'.$bloc['bloc'].'-diferenta" readonly="readonly"/></td>';
        //echo '<td><input type="text" name="'.$i.'-cost"/></td>';
        echo '<td width="125px">&nbsp;</td>';
        echo '</tr>';

    	$i++;
    }

    if ($i % 2 == 0) {
        $culoare = "#DDD";
    } else {
        $culoare = "#EEE";
    }
    echo '<tr bgcolor="'.$culoare.'">';
    echo '<td><b>Total Consum</td>';
    echo '<td colspan="2">&nbsp;</td>';
    echo '<td><input class="ver_nr ver_req" type="text" name="totCons" id="totCons"/></td>';
    echo '<td>&nbsp;</td>';
    echo '</tr>';
    ?>
    <tr>
        <td colspan="4">&nbsp;</td>
        <td><input type="submit" value="Salveaza"/></td>
    </tr>
    </table>
</form>
<?php }

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                       Procesare                      //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
if ($_POST['deProcesat'] == "OK") {
    $factId = $proc['fact_id'];
    $tipFactura = 1;
    $tipServiciu = $proc['tipServiciu'];
    $asocId = $proc['asoc_id'];

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

    $indexNou = $proc['indexNou'];
    $indexVechi = $proc['indexVechi'];
    $cantitate = $proc['cantitate'];
    $pasant = $proc['pasant'];
    $cost = $proc['cost'];


    //  Verific pasante
    $arePasant = "SELECT * FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='da'";
    $arePasant = mysql_query($arePasant) or die ("Nu pot afla scarile cu pasant<br />".mysql_error());

    if (mysql_num_rows($arePasant) != 0) {
        $pasant = $proc['pasant'];
    }

    $indexNou = explode(',', $indexNou);
    $indexVechi = explode(',', $indexVechi);
    $pasant = explode(',',$pasant);

    $facturaAR = $serieFactura.' / '.$numarFactura;

    for ($i = 0; $i < count($indexNou); $i++) {
        $diferenta[] = $indexNou[$i] - $indexVechi[$i];
    }

    $pretAR = $cost / $cantitate;
    echo '<br />Pretul pentru un m<sup>3</sup> de apa rece este '.$pretAR.'<br />';

    $scariVizate = "SELECT * FROM scari WHERE asoc_id=".$asocId." GROUP BY bloc";
    echo $scariVizate;
    $scariVizate = mysql_query($scariVizate) or die ("Nu pot afla scarile asociatiei curente<br />".mysql_error());

    $nrOrdine = 0;
    $nrPass = 0;

    while ($sV = mysql_fetch_array($scariVizate)) {
        echo '<br />Bloc '.$sV['bloc'].': '.$diferenta[$nrOrdine];
        $consumBloc[$sV['bloc']] = $diferenta[$nrOrdine];			//un vector in care pastrez consumul pentru fiecare bloc in parte

        $nrOrdine ++;

        echo '<br />Asta e blocul: '.$sV['bloc'];

        $arePasant = "SELECT * FROM scari_setari WHERE scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$sV['bloc']."') AND pasant='da'";
        echo $arePasant;
        $arePasant = mysql_query($arePasant) or die ("Nu pot afla setarile scarilor din cadrul asociatiei<br />".mysql_error());

        $nrPasanteBloc[$sV['bloc']] = mysql_num_rows($arePasant);			//pastrez numarul de pasante pentru fiecare bloc in parte

        for ($i = 0; $i < mysql_num_rows($arePasant); $i++) {
            $scaraCurr = "SELECT * FROM scari WHERE scara_id=".mysql_result($arePasant, $i, 'scara_id');
            $scaraCurr = mysql_query($scaraCurr) or die ("Nu pot afla numele scarii curente<br />".mysql_error());

            echo '<br />		Consum scara '.mysql_result($scaraCurr, 0, 'scara').': '.$pasant[$nrPass];
            $scaraCuPasant[mysql_result($scaraCurr, 0, 'scara_id')] = $pasant[$nrPass];				//vector in care pastrez corespondenta dintre scara_id si consumul pasantului pentru scara respectiva
            $nrPass++;
        }
    }

    echo '<br />--------------------------';

    foreach ($consumBloc as $numeBloc=>$consumulBloc) {
        echo '<br />Blocul '.$numeBloc.' are consumul '.$consumulBloc.';';
    }

    echo '<br />--------------------------';

    foreach ($nrPasanteBloc as $numeScara=>$numarPasante) {
        echo '<br />Blocul '.$numeScara.' are '.$numarPasante.' pasante;';
    }

    //fac un vector care are ca indici numarul blocului si ca valoare numarul de scari ale blocului
    $ordBloc = "SELECT * FROM scari WHERE asoc_id=".$asocId." GROUP BY bloc";
    $ordBloc = mysql_query($ordBloc) or die ("Nu pot afla blocurile<br />".mysql_error());

    while ($blocPeRand = mysql_fetch_array($ordBloc)) {
        $numarScari = "SELECT * FROM scari WHERE bloc='".$blocPeRand['bloc']."' AND asoc_id=".$asocId;
        $numarScari = mysql_query($numarScari) or die ("Nu pot afla numarul de scari pentru fiecare bloc in parte<br />".mysql_error());

        $bloc[$blocPeRand['bloc']] = mysql_num_rows($numarScari);			//vector in care pastrez numarul de scari pentru fiecare bloc
    }

    echo '<br />--------------------------';

    foreach ($bloc as $nrBloc=>$nrScari) {
        echo '<br />Blocul '.$nrBloc.' are '.$nrScari.' scari.';
    }

    echo '<br />--------------------------';

    echo '<br />Consum pe scari cu pasant: ';
    foreach ($scaraCuPasant as $idScara=>$consumScara) {
        echo '<br />Scara cu id-ul '.$idScara.' are consumul '.$consumScara;
    }

    //in cazul in care un bloc are mai multe scari si scarile nu au pasant
    //impart apa intre scari conform criteriilor

    //in primul rand aflu modul de impartire
    $impartApaIntreScari = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
    $impartApaIntreScari = mysql_query($impartApaIntreScari) or die ("Nu pot afla setarile asociatiei curente<br />".mysql_error());

    $declaraTotiAR = mysql_result($impartApaIntreScari, 0, 'impartire1');
    $nDeclaraTotiAR = mysql_result($impartApaIntreScari, 0, 'impartire2');
    $impartApaIntreScari = mysql_result($impartApaIntreScari, 0, 'criteriu2');

    echo '<br />--------------------------';

    echo '<br />Modul de impartire a apei intre scari pentru asociatia '.mysql_result(mysql_query("SELECT asociatie FROM asociatii WHERE asoc_id=".$asocId), 0, 'asociatie').' este "'.$asocCriteriuImpartireArr[$impartApaIntreScari].'"';

    //criterii de impartire a diferentelor
    echo '<br />Modul de impartire a ape in cazul in care declara toti: '.$apaDeclaraArr[$declaraTotiAR];
    echo '<br />Modul de impartire a ape in cazul in care nu declara toti: '.$apaNuDeclaraArr[$nDeclaraTotiAR];

    //aplic switchul de mai jos pentru fiecare bloc in parte

    //aici calculeaza consumul pentru fiecare scara in parte
    foreach ($consumBloc as $numarBloc=>$consumulBloc) {
        switch ($impartApaIntreScari) {
            case 0:			// consumul locatarilor din fiecare scara
            //verific daca am scari care nu au pasant pentru blocul curent
                if ($bloc[$numarBloc] > $nrPasanteBloc[$numarBloc]) {
                    //suma consumurilor de apa rece
                    //pentru locatarii care stau pe
                    //scarile fara pasant din bloc
                    $consumARDeclarat = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))";
                    $consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                    $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                    $diferenta = $cantitate - $consumARDeclarat;

                    //pentru fiecare bloc, calculez procentul din diferente pe care il are de adaugat la consum
                    $toateScarile = "SELECT * FROM scari WHERE bloc='".$numarBloc."' AND asoc_id=".$asocId." AND scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu')";
                    $toateScarile = mysql_query($toateScarile) or die ("Nu pot parcurge scarile blocului<br />".mysql_error());

                    while ($scariPeRand = mysql_fetch_array($toateScarile)) {
                        $consumDeclaratScara = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
                        $consumDeclaratScara = mysql_query($consumDeclaratScara) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());

                        $consumDeclaratScara = mysql_result($consumDeclaratScara, 0, 'SUM(consum_rece)');

                        $procentDiferenta = ($consumDeclaratScara * 100)/$consumARDeclarat;
                        $procentDiferenta = ($procentDiferenta * $diferenta) / 100;
                        $scaraCuPasant[$scariPeRand['scara_id']] = ($consumDeclaratScara + $procentDiferenta);
                    }
                }
                break;
            case 1:			// nr de apartamente din fiecare scara
            //verific daca am scari care nu au pasant pentru blocul curent
                if ($bloc[$numarBloc] > $nrPasanteBloc[$numarBloc]) {
                    //consumul total declarat de locatari
                    $consumARDeclarat = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))";
                    $consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                    $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                    echo '<br />Consumul declarat de locatari este '.$consumARDeclarat;

                    $diferenta = $cantitate - $consumARDeclarat;
                    echo '<br />Diferenta consumurilor este: '.$diferenta;

                    //aflu nr total de apartamente din blocurile fara pasant
                    $nrTotAp = "SELECT SUM(apartamente) FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."')"; //echo '<br />SQL --> '.$nrTotAp;
                    $nrTotAp = mysql_query($nrTotAp) or die ("Nu pot afla numarul total de apartamente pentru scarile fara pasant<br />".mysql_error());
                    $nrTotAp = mysql_result($nrTotAp, 0, 'SUM(apartamente)');

                    echo '<br />Numarul total de apartamente: '.$nrTotAp;

                    $difPeAp = $diferenta / $nrTotAp;
                    echo '<br />Diferenta aferenta fiecarul apartament este: '.$difPeAp;

                    //pentru fiecare bloc, calculez procentul din diferente pe care il are de adaugat la consum
                    $toateScarile = "SELECT * FROM scari WHERE bloc='".$numarBloc."' AND asoc_id=".$asocId." AND scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu')";
                    $toateScarile = mysql_query($toateScarile) or die ("Nu pot parcurge scarile blocului<br />".mysql_error());

                    while ($scariPeRand = mysql_fetch_array($toateScarile)) {
                        $nrApScaraCurenta = "SELECT * FROM scari_setari WHERE scara_id=".$scariPeRand['scara_id'];
                        $nrApScaraCurenta = mysql_query($nrApScaraCurenta) or die ("Nu pot afla numarul de apartamente de pe scara curenta<br />".mysql_error());
                        $nrApScaraCurenta = mysql_result($nrApScaraCurenta, 0, 'apartamente');

                        $consumAp = $nrApScaraCurenta * $difPeAp;

                        $consumDeclaratScara = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
                        $consumDeclaratScara = mysql_query($consumDeclaratScara) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());
                        $consumDeclaratScara = mysql_result($consumDeclaratScara, 0, 'SUM(consum_rece)');

                        $scaraCuPasant[$scariPeRand['scara_id']] = ($consumDeclaratScara + $consumAp);
                    }
                }
                break;
            case 2:			// nr de persoane din fiecare scara
            //verific daca am scari care nu au pasant pentru blocul curent
                if ($bloc[$numarBloc] > $nrPasanteBloc[$numarBloc]) {
                    $consumARDeclarat = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))";
                    $consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                    $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                    echo '<br />Consumul declarat de locatari este '.$consumARDeclarat;

                    $diferenta = $cantitate - $consumARDeclarat;
                    echo '<br />Diferenta consumurilor este: '.$diferenta;

                    //aflu nr total de persoane din blocurile fara pasant
                    $nrTotPers = "SELECT SUM(nr_pers) FROM locatari WHERE asoc_id=".$asocId." AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu'))"; //echo '<br />SQL --> '.$nrTotPers;
                    $nrTotPers = mysql_query($nrTotPers) or die ("Nu pot afla numarul total de apartamente pentru scarile fara pasant<br />".mysql_error());
                    $nrTotPers = mysql_result($nrTotPers, 0, 'SUM(nr_pers)');

                    $cantPePers = $diferenta / $nrTotPers;
                    echo '<br />Cantitatea aferenta fiecarei persoane este: '.$cantPePers;

                    //pentru fiecare scara in parte trebuie sa stabilesc consumul
                    $scariFaraPasante = "SELECT * FROM scari WHERE scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu') AND bloc='".$numarBloc."' AND asoc_id=".$asocId;
                    $scariFaraPasante = mysql_query($scariFaraPasante) or die ("Nu pot afla scarile care nu beneficiaza de pasante<br />".mysql_error());

                    while ($scariPeRand = mysql_fetch_array($scariFaraPasante)) {
                        $nrPersScaraCurr = "SELECT SUM(nr_pers) FROM locatari WHERE scara_id=".$scariPeRand['scara_id'];
                        $nrPersScaraCurr = mysql_query($nrPersScaraCurr) or die ("Nu pot afla numarul de persoane de pe scara curenta<br />".mysql_error());
                        $nrPersScaraCurr = mysql_result($nrPersScaraCurr, 0, 'SUM(nr_pers)');

                        $consumDeclaratScara = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
                        $consumDeclaratScara = mysql_query($consumDeclaratScara) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());
                        $consumDeclaratScara = mysql_result($consumDeclaratScara, 0, 'SUM(consum_rece)');

                        $consumScara = $nrPersScaraCurr * $cantPePers;

                        $scaraCuPasant[$scariPeRand['scara_id']] = ($consumScara + $consumDeclaratScara);
                        echo '<br />Pe scara asta sunt '.$nrPersScaraCurr.' persoane, iar consumul scarii este '.$consumScara;
                    }
                }
                break;
            case 3:			// suprafata utila din fiecare scara
            //verific daca am scari care nu au pasant pentru blocul curent
                if ($bloc[$numarBloc] > $nrPasanteBloc[$numarBloc]) {
                    $consumARDeclarat = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))";
                    $consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                    $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                    echo '<br />Consumul declarat de locatari este '.$consumARDeclarat;

                    $diferenta = $cantitate - $consumARDeclarat;
                    echo '<br />Diferenta consumurilor este: '.$diferenta;

                    //aflu suprafata totala din blocurile fara pasant
                    $nrTotMp = "SELECT SUM(supr) FROM locatari WHERE asoc_id=".$asocId." AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu'))"; //echo '<br />SQL --> '.$nrTotMp;
                    $nrTotMp = mysql_query($nrTotMp) or die ("Nu pot afla numarul total de apartamente pentru scarile fara pasant<br />".mysql_error());
                    $nrTotMp = mysql_result($nrTotMp, 0, 'SUM(supr)');

                    $cantPePers = $diferenta / $nrTotMp;
                    echo '<br />Cantitatea aferenta fiecarei persoane este: '.$cantPePers;

                    //pentru fiecare scara in parte trebuie sa stabilesc consumul
                    $scariFaraPasante = "SELECT * FROM scari WHERE scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu') AND bloc='".$numarBloc."' AND asoc_id=".$asocId;
                    $scariFaraPasante = mysql_query($scariFaraPasante) or die ("Nu pot afla scarile care nu beneficiaza de pasante<br />".mysql_error());

                    while ($scariPeRand = mysql_fetch_array($scariFaraPasante)) {
                        $mpScaraCurr = "SELECT SUM(supr) FROM locatari WHERE scara_id=".$scariPeRand['scara_id'];
                        $mpScaraCurr = mysql_query($mpScaraCurr) or die ("Nu pot afla numarul de persoane de pe scara curenta<br />".mysql_error());
                        $mpScaraCurr = mysql_result($mpScaraCurr, 0, 'SUM(supr)');

                        $consumScara = $mpScaraCurr * $cantPePers;

                        $consumDeclaratScara = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
                        $consumDeclaratScara = mysql_query($consumDeclaratScara) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());
                        $consumDeclaratScara = mysql_result($consumDeclaratScara, 0, 'SUM(consum_rece)');

                        $scaraCuPasant[$scariPeRand['scara_id']] = ($consumScara + $consumDeclaratScara);
                        echo '<br />Pe scara asta sunt '.$nrPersScaraCurr.' persoane, iar consumul scarii este '.$consumScara;
                    }
                }
                break;
        }
    }
    echo '<br />--------------------------------------';

    $apaDeclarataAsoc = "SELECT SUM(consum_rece) FROM apometre WHERE asoc_id=".$asocId." AND luna='".$luna."'";
    $apaDeclarataAsoc = mysql_query($apaDeclarataAsoc) or die ("Nu pot afla cantitatea de apa declarata de locatari<br />".mysql_error());
    $apaDeclarataAsoc = mysql_result($apaDeclarataAsoc, 0, 'SUM(consum_rece)');

    foreach ($scaraCuPasant as $scaraBlocului=>$consumulScarii) {

        //pentru fiecare scara in parte calculez consumul declarat
        //si apoi in functie de acesta calculez cat are fiecare om
        //de plata
        $totalApaDeclarata = "SELECT SUM(consum_rece) FROM apometre WHERE scara_id=".$scaraBlocului." AND luna='".$luna."'";
        $totalApaDeclarata = mysql_query($totalApaDeclarata) or die ("Nu pot afla consumul de apa rece declarat pentru scara curenta<br />".mysql_error());
        $totalApaDeclarata = mysql_result($totalApaDeclarata, 0, 'SUM(consum_rece)');

        echo '<br />Scara '.$scaraBlocului.' are consumul '.$consumulScarii.' m<sup>3</sup> iar consumul declarat este de '.$totalApaDeclarata.' m<sup>3</sup>';

        //verific cati locatari nu au declarat consumul
        $nuAuDeclarat = "SELECT COUNT(*) FROM apometre WHERE scara_id=".$scaraBlocului." AND luna='".$luna."' AND completat=0";
        $nuAuDeclarat = mysql_query($nuAuDeclarat) or die ("Nu pot afla cate persoane nu au declarat consumul de apa rece<br />".mysql_error());
        $nuAuDeclarat = mysql_result($nuAuDeclarat, 0, 'COUNT(*)');

        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care au apa rece<br />".mysql_error());

        while ($aAR = mysql_fetch_array($areApaRece)) {
            $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$aAR['loc_id']." AND luna='".$luna."'";
            $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa rece a consumat un locatar<br />".mysql_error());

            $apaReceConsumata = mysql_result($aConsumat, 0, 'consum_rece');

            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$aAR['loc_id']."', '$luna', '21', '".$apaReceConsumata."', '$pretAR','m<sup>3</sup>', '$facturaAR','$pretAR', ".$apaDeclarataAsoc.")";
            $plateste = mysql_query($plateste) or die ("Nu pot insera factura de apa rece<br />".mysql_error());
        }

        if ($totalApaDeclarata < $consumulScarii) {
            $diferentaAR = $consumulScarii - $totalApaDeclarata;

            if ($nuAuDeclarat == 0) {
                switch ($declaraTotiAR) {
                    case 5:			// O Persoana
                    case 0:			// Numar Persoane
                        echo '0';
                        $nrPers = "SELECT SUM(nr_pers) FROM locatari WHERE scara_id=".$scaraBlocului;
                        $nrPers = mysql_query($nrPers) or die ("Nu pot afla numarul de persoane din bloc<br />".mysql_error());
                        $nrPers = mysql_result($nrPers, 0, 'SUM(nr_pers)');

                        $cantPePers = ($diferentaAR * $pretAR) / $nrPers;

                        echo '<br /><---------------------------------->';
                        echo '<br />Cant pe pers: '.$cantPePers;
                        echo '<br />Diferenta AR: '.$diferentaAR;
                        echo '<br />Nr Pers scara: '.$nrPers;
                        echo '<br />Pret apa rece: '.$pretAR;

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".$areApaPlateste['nr_pers']."', '$cantPePers','persoana', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                        }
                        break;

                    case 1:			// Cota Indiviza
                        echo '1';
                        $cotaIndiviza = "SELECT SUM(supr) FROM locatari WHERE scara_id=".$scaraBlocului;
                        $cotaIndiviza = mysql_query($cotaIndiviza) or die ("Nu pot suprafata apartamentelor<br />".mysql_error());
                        $cotaIndiviza = mysql_result($cotaIndiviza, 0, 'SUM(supr)');

                        $cantPePers = ($diferentaAR * $pretAR) / $cotaIndiviza;	//cantitatea aferenta fiecarei persoane

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".$areApaPlateste['supr']."', '$cantPePers','cota indiviza', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                        }
                        break;

                    case 2:			// Numar Apometre
                        echo '2';
                        $numarApometre = "SELECT SUM(ap_rece) FROM locatari WHERE scara_id=".$scaraBlocului;
                        $numarApometre = mysql_query($numarApometre) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                        $numarApometre = mysql_result($numarApometre, 0, 'SUM(ap_rece)');

                        $cantPePers = ($diferentaAR * $pretAR) / $numarApometre;	//cantitatea aferenta fiecarei persoane

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".$areApaPlateste['ap_calda']."', '$cantPePers','nr. apometre', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                        }
                        break;

                    case 3:			// Pe Apartamente
                        echo '3';
                        $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE scara_id=".$scaraBlocului;
                        $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                        $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                        $cantPePers = ($diferentaAR * $pretAR) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '1', '$cantPePers','apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                        }
                        break;

                    case 4:			// Proportional Consumului
                        echo '4';
                        $consumTotal = "SELECT SUM(consum_rece) FROM apometre WHERE scara_id=".$scaraBlocului." AND luna='".$luna."'";
                        $consumTotal = mysql_query($consumTotal) or die ("Nu pot afla consumul total de apa rece<br />".mysql_error());
                        $consumTotal = mysql_result($consumTotal, 0, 'SUM(consum_rece)');

                        $cantPePers = ($diferentaAR * $pretAR) / $consumTotal;

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa rece a consumat un locatar<br />".mysql_error());

                            $consumApaRece = mysql_result($aConsumat, 0, 'consum_rece');

                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".$consumApaRece."', '$cantPePers','proportional cu consumul', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                        }
                        break;
                    case 6:			// Pe Apartamente Locuite
                        echo '6';
                        $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE ap_locuit=1 AND scara_id=".$scaraBlocului;
                        $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                        $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                        $cantPePers = ($diferentaAR * $pretAR) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '1', '$cantPePers','apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                            $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa rece<br />".mysql_error());
                        }
                        break;
                }
            } else {
                switch ($nDeclaraTotiAR) {
                    case 2:			// Dif nr persoane prezente amenda
                        echo '8';
                        $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."')";
                        $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                        $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                        $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                        $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                        $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                        $cantPePers = ($diferentaAR * $pretAR) / $nrPersPrezente;
                        $metriPePersoana = $diferentaAR / $nrPersPrezente;

                        //inserez in fisa individuala doar la cei care nu au declarat
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            //verific cine nu a declarat apa
                            $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                            $apometreRece = array();

                            //indecsii vechi
                            if ($nrLuniApometre == 2) {
                                $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                                $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                                for ($i=1; $i<6; $i++) {
                                    $apometreRece[] = mysql_result($apoIni, 0, 'r'.$i);
                                }
                            } else {
                                for ($i=1; $i<6;$i++) {
                                    $apometreRece[] = mysql_result($nuADeclarat, 0, 'r'.$i);
                                }
                            }

                            if (mysql_num_rows($nuADeclarat) != 0) {

                                $nrPers = $areApaPlateste['nr_pers'];

                                $locId = $areApaPlateste['loc_id'];

                                //inserez in apometre consumurile
                                for ($i=1; $i<=$nrApoRece; $i++) {
                                    $inserezConsumuri = "UPDATE apometre SET r".$i." = ".$apometreRece[($i-1)]." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                    $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                                }

                                //inserez in apometre amenda
                                $updatezAmenda = "UPDATE apometre SET amenda_rece=1, auto=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $updatezAmenda = mysql_query($updatezAmenda) or die ("Nu pot updata amenda rece<br />".mysql_error());

                                //daca apa rece a fost procesata, setez si consumul
                                if ($nuADeclarat['amenda_cald'] != null) {
                                    $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                    $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                                }

                                //inserez in apometre consumul
                                $consumCurent = $cantPePers;
                                $inApo = $metriPePersoana * $nrPers;

                                $insertApo = "UPDATE apometre SET consum_rece = ".$inApo." WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $insertApo = mysql_query($insertApo) or die("Nu pot insera consumul de apa rece<br />".mysql_error());

                                //inserez in fisa individuala consumul
                                $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41, '$nrPers', '$cantPePers', 'persoana', '$facturaAR','$pretAR', '$diferentaAR')";
                                $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.1<br />".mysql_error());
                            }
                        }
                        break;
                    case 3:			// Dif pe apartamente amenda
                        echo '9';
                        $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."')";
                        $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                        $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                        $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                        $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                        $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                        $cantPePers = ($diferentaAR * $pretAR) / $nrApNedeclarat;
                        $metriPePersoana =  $diferentaAR / $nrApNedeclarat;

                        //inserez in fisa individuala doar la cei care nu au declarat
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            //verific cine nu a declarat apa
                            $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                            $apometreRece = array();

                            //indecsii vechi
                            if ($nrLuniApometre == 2) {
                                $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                                $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                                for ($i=1; $i<6; $i++) {
                                    $apometreRece[] = mysql_result($apoIni, 0, 'r'.$i);
                                }
                            } else {
                                for ($i=1; $i<6;$i++) {
                                    $apometreRece[] = mysql_result($nuADeclarat, 0, 'r'.$i);
                                }
                            }

                            if (mysql_num_rows($nuADeclarat) != 0) {

                                $nrPers = $areApaPlateste['nr_pers'];

                                $locId = $areApaPlateste['loc_id'];

                                //inserez in apometre consumurile
                                for ($i=1; $i<=$nrApoRece; $i++) {
                                    $inserezConsumuri = "UPDATE apometre SET r".$i." = ".$apometreRece[($i-1)]." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                    $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                                }

                                //inserez in apometre amenda
                                $updatezAmenda = "UPDATE apometre SET amenda_rece=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $updatezAmenda = mysql_query($updatezAmenda) or die ("Nu pot updata amenda rece<br />".mysql_error());

                                //daca apa rece a fost procesata, setez si consumul
                                if ($nuADeclarat['amenda_cald'] != null) {
                                    $updatezConsum = "UPDATE apometre SET completat=1, auto=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                    $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                                }

                                //inserez in apometre consumul
                                $consumCurent = $cantPePers;
                                $inApo = $metriPePersoana;

                                $insertApo = "UPDATE apometre SET consum_rece = ".$inApo." WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $insertApo = mysql_query($insertApo) or die("Nu pot insera consumul de apa rece<br />".mysql_error());

                                //inserez in fisa individuala consumul
                                $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41, '1', '$cantPePers', 'apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                                $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                            }
                        }
                        break;
                    case 4:			// Dif nr persoane prezente cu modificarea indexului
                        echo '10';
                        $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."')";
                        $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                        $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                        $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                        $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                        $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                        $cantPePers = ($diferentaAR * $pretAR) / $nrPersPrezente;
                        $metriPePersoana = $diferentaAR / $nrPersPrezente;

                        //inserez in fisa individuala doar la cei care nu au declarat
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            //verific cine nu a declarat apa
                            $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                            $apometreRece = array();

                            //indecsii vechi
                            if ($nrLuniApometre == 2) {
                                $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                                $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                                for ($i=1; $i<6; $i++) {
                                    $apometreRece[] = mysql_result($apoIni, 0, 'r'.$i);
                                }
                            } else {
                                for ($i=1; $i<6;$i++) {
                                    $apometreRece[] = mysql_result($nuADeclarat, 0, 'r'.$i);
                                }
                            }

                            if (mysql_num_rows($nuADeclarat) != 0) {
                                $apartamente[] = $areApaPlateste['loc_id'];

                                $nrPers = $areApaPlateste['nr_pers'];
                                $consumTotalAp = $cantPePers;
                                $inApo = $metriPePersoana * $nrPers;
                                $nrApoRece = $areApaPlateste['ap_rece'];

                                $restApa = $inApo % $nrApoRece;
                                $cPApometru = ($inApo - $restApa) / $nrApoRece;
                                $diferenta = 1;

                                $locId = $areApaPlateste['loc_id'];

                                //inserez in apometre consumurile
                                for ($i=1; $i<=$nrApoRece; $i++) {
                                    if ($diferenta == 1) {
                                        $inserezConsumuri = "UPDATE apometre SET r".$i." = ".($apometreRece[($i-1)] + $cPApometru+$restApa)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                        echo '<br />SQL: --> '.$inserezConsumuri;
                                        $diferenta = 0;
                                    } else {
                                        $inserezConsumuri = "UPDATE apometre SET r".$i." = ".($apometreRece[($i-1)] + $cPApometru)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                        echo '<br />SQL: --> '.$inserezConsumuri;
                                    }
                                    $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                                }
                                $inserezConsumLuna = "UPDATE apometre SET consum_rece = '$inApo', auto=1 WHERE loc_id=".$locId." AND luna='".$luna."'";
                                $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa rece<br />".mysql_error());

                                //daca apa rece a fost procesata, setez si consumul
                                if ($nuADeclarat['consum_cald'] != 0) {
                                    $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                    $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                                }

                                //inserez in fisa individuala consumul
                                $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41 '$nrPers', '$consumTotalAp', 'persoana', '$facturaAR','$pretAR', '$diferentaAR')";
                                $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                            }
                        }
                        break;
                    case 5:			// Dif pe apartamente cu modif indexului
                        echo '11';
                        $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."')";
                        $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                        $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                        $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                        $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                        $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                        $cantPePers = ($diferentaAR * $pretAR) / $nrApNedeclarat;
                        $metriPePersoana = $diferentaAR / $nrApNedeclarat;

                        //inserez in fisa individuala doar la cei care nu au declarat
                        $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului;
                        $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                        while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                            //verific cine nu a declarat apa
                            $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                            $apometreRece = array();

                            //indecsii vechi
                            if ($nrLuniApometre == 2) {
                                $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                                $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                                for ($i=1; $i<6; $i++) {
                                    $apometreRece[] = mysql_result($apoIni, 0, 'r'.$i);
                                }
                            } else {
                                for ($i=1; $i<6;$i++) {
                                    $apometreRece[] = mysql_result($nuADeclarat, 0, 'r'.$i);
                                }
                            }

                            if (mysql_num_rows($nuADeclarat) != 0) {

                                $locId = $areApaPlateste['loc_id'];

                                $apartamente[] = $areApaPlateste['loc_id'];
                                $nrPers = $areApaPlateste['nr_pers'];

                                $consumTotalAp = $cantPePers;
                                $inApo = $metriPePersoana;

                                $nrApoRece = $areApaPlateste['ap_calda'];

                                $restApa = $inApo % $nrApoRece;
                                $cPApometru = ($inApo - $restApa) / $nrApoRece;
                                $diferenta = 1;

                                //inserez in apometre consumurile
                                for ($i=1; $i<=$nrApoRece; $i++) {
                                    if ($diferenta == 1) {
                                        $inserezConsumuri = "UPDATE apometre SET r".$i." = ".($apometreRece[($i-1)] + $cPApometru+$restApa)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                        $diferenta = 0;
                                    } else {
                                        $inserezConsumuri = "UPDATE apometre SET r".$i." = ".($apometreRece[($i-1)] + $cPApometru)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                    }
                                    $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                                }
                                $inserezConsumLuna = "UPDATE apometre SET consum_cald = '$inApo', auto=1 WHERE loc_id=".$locId." AND luna='".$luna."'";
                                $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa rece<br />".mysql_error());

                                //daca apa rece a fost procesata, setez si consumul
                                if ($nuADeclarat['consum_cald'] != null) {
                                    $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                    $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                                }

                                //inserez in fisa individuala consumul
                                $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41, '1', '$consumTotalAp', 'apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                                $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                            }
                        }
                        break;
                }
            }
        }
    }
}
?>