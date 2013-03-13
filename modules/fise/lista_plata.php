<?php
function proceseazaLista($inregistrareListaPlata, $total_general, $datorie, $penalizari) {
	$total_partial = $inregistrareListaPlata['ar_val'] + $inregistrareListaPlata['ac_val'] + $inregistrareListaPlata['dif_ac'] + $inregistrareListaPlata['dif_ar'] + $inregistrareListaPlata['incalzire'] + $inregistrareListaPlata['chelt_pe_pers'] + $inregistrareListaPlata['chelt_cota_indiv'] + $inregistrareListaPlata['chelt_pe_benef'] + $inregistrareListaPlata['alte_cheltuieli'];
	$data = date('Y-m-d');

	/* Aceasta bucata de cod vede daca cineva a platit in avans si scade din suma de plata valoarea curenta
	!!!! Trebuie avut grija ca aceasta suma nu se elimina si partea negativa. Peantru aceasta am facut o functie care regleaza astfel de sume automat

	$rest = "SELECT sum(rest_plata) as rest FROM `fisa_cont` WHERE loc_id=".$inregistrareListaPlata['loc_id'];
	$rest = mysql_query($rest) or die ("Nu pot restul de plata <br />".mysql_error());
	$rest = mysql_result($rest, 0, 'rest');
	$rest = ($rest > 0) ? 0 : $rest; */

	$rest = 0;

	$dataExplicatie = strtotime('-1 month', strtotime($data));
	$explicatie = 'LP '.Util::lunaToString(date('m', $dataExplicatie)).' '.date('Y', $dataExplicatie);

    $sql = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
        	NULL , '".$inregistrareListaPlata['asoc_id']."', '".$inregistrareListaPlata['scara_id']."', '".$inregistrareListaPlata['loc_id']."', '".$data."', '".$explicatie."', 'LP', '".round($total_partial, 2)."', '".round($datorie, 2)."' , '".round($penalizari, 2)."', '".round($total_general, 2)."', '".round($total_partial - $rest, 2)."' );";
	mysql_query($sql) or die ("Nu pot insera in fisa cont <br />".mysql_error());
	
  Util::regularizare_conturi(null, null, $inregistrareListaPlata['loc_id']);
}
?>
<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=lista_plata&asoc_id=" + value;
}
function select_scara(value,value2) {
 window.location = "index.php?link=lista_plata&asoc_id=" + value + "&scara_id=" + value2;
}
function select_luna(value,value2,value3) {
 window.location = "index.php?link=lista_plata&asoc_id=" + value + "&scara_id=" + value2 + "&luna=" + value3;
}

</script>
<?php
if (isset($_GET['proceseaza']) && $_GET['proceseaza'] == 1) {
  //verifica reprocesarea cu F5 unei liste deja procesate
    $verificaProcesataS="SELECT SUM(procesata) sum, count(1) countp,asoc_id
        FROM lista_plata 
        WHERE scara_id='".mysql_real_escape_string($_GET['scara_id']).
        "' AND luna='".mysql_real_escape_string($_GET['luna'])."'";
    $verificaProcesata = mysql_query($verificaProcesataS) or die ("Nu pot afla informatiile despre liste procesate.<br />".mysql_error());
    $rowVerProc=mysql_fetch_array($verificaProcesata);

    if($rowVerProc[sum]!=$rowVerProc[countp]){ 
      $updateProcesareLista = "UPDATE lista_plata SET procesata=1 WHERE `luna`='".mysql_real_escape_string($_GET['luna'])."' AND `scara_id`='".mysql_real_escape_string($_GET['scara_id'])."'";
      $updateProcesareLista = mysql_query($updateProcesareLista) or die ("Nu pot procesa lista de plata <br />".mysql_error());
      //echo '<script language="javascript">document.location.href="index.php?link=lista_plata&asoc_id='
      //  .$rowVerProc[asoc_id].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']
      //  .'";</script>';
      
    } else {

      echo '<script language="javascript">document.location.href="index.php?link=lista_plata&asoc_id='
            .$rowVerProc[asoc_id].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']
            .'";</script>';    
     die ("Este deja procesata.<br />".mysql_error());
    }
}
if (isset($_GET['proceseaza']) && $_GET['proceseaza'] == 0) {

    $scara_id = $_GET['scara_id'];
    $luna = $_GET['luna'];
    $motivDeprocesare = $_GET['motivDeprocesare'];

    include_once 'lp_deprocesare.php';

    if($f_eroare)
        die($f_mesaj.'<br /><br /><br /><br />'.$update);
}

/*****************************TABEL************************/
function putDataInTable($asocId, $scaraId, $luna, $situatie){
    //aici va trebui sa pun datele despre locatari

    if ($situatie == 0){
        //pentru inceput punem nrAp, Nume, CT, nrPers, ST
        $infoLoc = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
        $infoLoc = mysql_query($infoLoc) or die ("Nu pot afla informatiile despre locatari<br />".mysql_error());

        $deleteLista = "DELETE FROM `lista_plata` WHERE `luna`='$luna' AND `scara_id`='$scaraId' AND procesata=0";
        $deleteLista = mysql_query($deleteLista) or die ("Nu pot sterge lista de plata <br />".mysql_error());

        while ($locPeRand = mysql_fetch_array($infoLoc)){
        	//verific daca nu exista lista procesa pt acest locatar in aceasta luna
        	$verificare = "SELECT * FROM lista_plata WHERE loc_id=".$locPeRand['loc_id']." AND `luna`='$luna' AND procesata=1";
        	$verificare = mysql_query($verificare) or die ("Nu potafla daca lista pt aceasta persoana a fost procesata <br />".mysql_error());
        	if (mysql_num_rows($verificare) != 0) {
        		continue;
        	}

            if ($locPeRand['centrala'] == "da"){
                $centrala = "CT";
            } else {
                $centrala = " ";
            }
            $putInListaSQL = "INSERT INTO lista_plata VALUES (null, '".$luna."', ".$locPeRand['loc_id'].", ".$locPeRand['scara_id'].", ".$locPeRand['asoc_id'].", '".$locPeRand['ap']."', '".$locPeRand['nume']."', '".$centrala."', ".$locPeRand['nr_pers'].", ".$locPeRand['supr'].", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
            $putInLista = mysql_query($putInListaSQL) or die ("Nu pot insera in lista de plata datele despre locatari<br />".$putInListaSQL."<br />". mysql_error());
        }
    }

    //inserez consumurile de apa pentru fiecare locatar in parte (daca nu au fost introduse)
    $apoLoc = "SELECT * FROM apometre WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $apoLoc = mysql_query($apoLoc) or die ("Nu pot obtine consumurile de apa pentru scara curenta<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($apoLoc)){
        $locCurr = "SELECT * FROM lista_plata WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'";
        $locCurr = mysql_query($locCurr) or die ("Nu pot afla detaliile locatarului curent<br />".mysql_error());

        if (mysql_result($locCurr, 0, 'procesata') == 0){
            $putConsum = "UPDATE lista_plata SET ar=".$locPeRand['consum_rece']." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa rece pentru luna ".$luna."<br />".mysql_error());
        }

        if (mysql_result($locCurr, 0, 'procesata') == 0){
            $putConsum = "UPDATE lista_plata SET ac=".$locPeRand['consum_cald']." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa calda pentru luna ".$luna."<br />".mysql_error());
        }
    }

    /*
     *  DIFERENTE
     */

    //verific daca oamenii au apa(DIFERENTA) trecuta in fisa individuala
    $verifFI = "SELECT * FROM fisa_indiv WHERE scara_id=".$scaraId." AND luna='".$luna."' AND serviciu='21'";
    $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
    if (mysql_num_rows($verifFI) == 0) { $areApaRece = 0; } else { $areApaRece = 1; }

    $verifFI = "SELECT * FROM fisa_indiv WHERE scara_id=".$scaraId." AND luna='".$luna."' AND serviciu='37'";
    $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
    if (mysql_num_rows($verifFI) == 0) { $areApaCalda = 0; } else { $areApaCalda = 1; }

    //inserez diferentele pentru apa -- diferentele se trec in lei
    $difLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $difLoc = mysql_query($difLoc) or die ("Nu pot afla detaliile din lista de plata a locatarilor<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($difLoc)){
        if ($areApaRece == 1){
            //diferente apa rece (ID: 41)
            $locCurr = "SELECT * FROM fisa_indiv WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."' AND serviciu='41'";
            $locCurr = mysql_query($locCurr) or die ("Nu pot afla diferenta la apa rece locatarul curent din fisa individuala<br />".mysql_error());

            if (mysql_num_rows($locCurr) != 0){
                $dif = (mysql_result($locCurr, 0, 'cant_fact_pers')*mysql_result($locCurr, 0, 'pret_unitar'));
            } else {
                $dif = 0;
            }
            $putConsum = "UPDATE lista_plata SET dif_ar=".$dif." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa rece pentru luna ".$luna."<br />".mysql_error());
        }

        if ($areApaCalda == 1){
            //diferente apa calda (ID: 39 + 40)
            $locCurr = "SELECT * FROM fisa_indiv WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."' AND (serviciu='39' OR serviciu='40')";
            $locCurr = mysql_query($locCurr) or die ("Nu pot afla diferenta la apa rece locatarul curent din fisa individuala<br />".mysql_error());

            if (mysql_num_rows($locCurr) != 0){
                $dif1 = (mysql_result($locCurr, 0, 'cant_fact_pers')*mysql_result($locCurr, 0, 'pret_unitar'));
                $dif2 = (mysql_result($locCurr, 1, 'cant_fact_pers')*mysql_result($locCurr, 1, 'pret_unitar'));
                $dif = $dif1 + $dif2;
            } else {
                $dif = 0;
            }
            $putConsum = "UPDATE lista_plata SET dif_ac=".$dif." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa calda pentru luna ".$luna."<br />".mysql_error());
        }
    }

    /*
     * APA
     */

    //verific daca oamenii au APA trecuta in fisa individuala
    $verifFI = "SELECT * FROM fisa_indiv WHERE scara_id=".$scaraId." AND luna='".$luna."' AND serviciu='21'";
    $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
    if (mysql_num_rows($verifFI) == 0) { $areApaRece = 0; } else { $areApaRece = 1; }

    $verifFI = "SELECT * FROM fisa_indiv WHERE scara_id=".$scaraId." AND luna='".$luna."' AND serviciu='37'";
    $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
    if (mysql_num_rows($verifFI) == 0) { $areApaCalda = 0; } else { $areApaCalda = 1; }

    //inserez pretul pentru apa din fisa individuala
    $apaLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $apaLoc = mysql_query($apaLoc) or die ("Nu pot afla detaliile din lista de plata a locatarilor<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($apaLoc)){
        if ($areApaRece == 1){
            //apa rece (ID: 21)
            $locCurr = "SELECT * FROM fisa_indiv WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."' AND serviciu='21'";
            $locCurr = mysql_query($locCurr) or die ("Nu pot afla diferenta la apa rece locatarul curent din fisa individuala<br />".mysql_error());

            if (mysql_num_rows($locCurr) != 0){
                $dif = (mysql_result($locCurr, 0, 'cant_fact_pers')*mysql_result($locCurr, 0, 'pret_unitar'));
            } else {
                $dif = 0;
            }
            $putConsum = "UPDATE lista_plata SET ar_val=".$dif." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa rece pentru luna ".$luna."<br />".mysql_error());
        }

        if ($areApaCalda == 1){
            //diferente apa calda (ID: 37 + 38)
            $locCurr = "SELECT * FROM fisa_indiv WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."' AND (serviciu='37' OR serviciu='38')";
            $locCurr = mysql_query($locCurr) or die ("Nu pot afla diferenta la apa rece locatarul curent din fisa individuala<br />".mysql_error());

            if (mysql_num_rows($locCurr) != 0){
                $dif1 = (mysql_result($locCurr, 0, 'cant_fact_pers')*mysql_result($locCurr, 0, 'pret_unitar'));
                $dif2 = (mysql_result($locCurr, 1, 'cant_fact_pers')*mysql_result($locCurr, 1, 'pret_unitar'));
                $dif = $dif1 + $dif2;
            } else {
                $dif = 0;
            }
            $putConsum = "UPDATE lista_plata SET ac_val=".$dif." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot updata consumul pentru apa calda pentru luna ".$luna."<br />".mysql_error());
        }
    }

    //cheltuieli / pers
    $cheltLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $cheltLoc = mysql_query($cheltLoc) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($cheltLoc)){
        $chelt = "SELECT * FROM fisa_indiv WHERE serviciu IN (SELECT serv_id FROM servicii WHERE localizare='Persoana') AND luna='".$luna."' AND loc_id=".$locPeRand['loc_id'];
        $chelt = mysql_query($chelt) or die ("Nu pot afla serviciile care sunt pe persoana<br />".mysql_error());

        $cheltuiala = 0;
        if (mysql_num_rows($chelt ) > 0){
            while ($cheltPeRand = mysql_fetch_array($chelt)){
                $cheltuiala += ($cheltPeRand['cant_fact_pers']*$cheltPeRand['pret_unitar']);
            }
            $putConsum = "UPDATE lista_plata SET chelt_pe_pers=".$cheltuiala." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot introduce cheltuielile pe persoana<br />".mysql_error());
        }
    }


    //cheltuieli / cota indiviza
    $cheltLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $cheltLoc = mysql_query($cheltLoc) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($cheltLoc)){
        $chelt = "SELECT * FROM fisa_indiv WHERE serviciu IN (SELECT serv_id FROM servicii WHERE localizare='Cota parte indiviza') AND luna='".$luna."' AND loc_id=".$locPeRand['loc_id'];
        $chelt = mysql_query($chelt) or die ("Nu pot afla serviciile care sunt pe Cota parte indiviza<br />".mysql_error());

        $cheltuiala = 0;
        if (mysql_num_rows($chelt ) > 0){
            while ($cheltPeRand = mysql_fetch_array($chelt)){
                $cheltuiala += ($cheltPeRand['cant_fact_pers']*$cheltPeRand['pret_unitar']);
            }
            $putConsum = "UPDATE lista_plata SET chelt_cota_indiv=".$cheltuiala." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot introduce cheltuielile pe Cota parte indiviza<br />".mysql_error());
        }
    }

    //cheltuieli / beneficiari
    $cheltLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $cheltLoc = mysql_query($cheltLoc) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($cheltLoc)){
        $chelt = "SELECT * FROM fisa_indiv WHERE serviciu IN (SELECT serv_id FROM servicii WHERE localizare='Beneficiari') AND luna='".$luna."' AND loc_id=".$locPeRand['loc_id'];
        $chelt = mysql_query($chelt) or die ("Nu pot afla serviciile care sunt pe Beneficiari<br />".mysql_error());

        $cheltuiala = 0;
        if (mysql_num_rows($chelt ) > 0){
            while ($cheltPeRand = mysql_fetch_array($chelt)){
                $cheltuiala += ($cheltPeRand['cant_fact_pers']*$cheltPeRand['pret_unitar']);
            }
            $putConsum = "UPDATE lista_plata SET chelt_pe_benef=".$cheltuiala." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot introduce cheltuielile pe Beneficiari<br />".mysql_error());
        }
    }

    //cheltuieli / alta natura
    $cheltLoc = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $cheltLoc = mysql_query($cheltLoc) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($cheltLoc)){
        $chelt = "SELECT * FROM fisa_indiv WHERE serviciu IN (SELECT serv_id FROM servicii WHERE localizare='Alta natura') AND luna='".$luna."' AND loc_id=".$locPeRand['loc_id'];
        $chelt = mysql_query($chelt) or die ("Nu pot afla serviciile care sunt pe Beneficiari<br />".mysql_error());

        $cheltuiala = 0;
        if (mysql_num_rows($chelt ) > 0){
            while ($cheltPeRand = mysql_fetch_array($chelt)){
                $cheltuiala += ($cheltPeRand['cant_fact_pers']*$cheltPeRand['pret_unitar']);
            }
            $putConsum = "UPDATE lista_plata SET alte_cheltuieli=".$cheltuiala." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
            $putConsum = mysql_query($putConsum) or die ("Nu pot introduce cheltuielile pe Beneficiari<br />".mysql_error());
        }
    }

    //RESTANTE + PENALIZARI
    $restPen = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $restPen = mysql_query($restPen) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($restPen)){
    	include_once("Penalizare.class.php");
    	$pen = new Penalizare($locPeRand['loc_id'], $locPeRand['scara_id'], $locPeRand['asoc_id']);

        $penalizare = $pen->getPenalizari();
        //varianta in care se tine cont de data scadenta 30 + termen plata
		//$restante = round($pen->getDatorii(), 2);
		$restante = $pen->getRestPlata();

        $putConsum = "UPDATE lista_plata SET restante=".round($restante, 5).", penalizari=".round($penalizare, 5)." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
        //var_dump($putConsum); die();
		$putConsum = mysql_query($putConsum) or die ("Nu pot salva restantele si penalizarile<br />".mysql_error());
    }

    //Restanta FOND
    $restFond = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $restFond = mysql_query($restFond) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    $lunaUrmX = explode('-', $luna);
    $lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
    $lunaUrmX = strtotime('+1 month', $lunaUrmX);
    $lunaUrm = date('m-Y', $lunaUrmX);

    while ($locPeRand = mysql_fetch_array($restFond)){
    		$lunaUrmEX = explode("-", $lunaUrm);

			$sql = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locPeRand['loc_id']." AND STR_TO_DATE(data, '%m-%Y') <= '".($lunaUrmEX[1].'-'.$lunaUrmEX[0].'-00')."' ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
    		$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());
    		$dataUltimulFond= mysql_result($sql, 0, 'data');
			$dataUFArray = explode("-", $dataUltimulFond);

    		$lunaNoua = false;

    		if ($dataUFArray[1] < $lunaUrmEX[1] || ($dataUFArray[1] == $lunaUrmEX[1] && $dataUFArray[0] < $lunaUrmEX[0]))
    			$lunaNoua = true;  //inseamna ca a trecut cel putin o luna de la ultima intrare in fisa facturi;

    		if ($lunaNoua) {
    			$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locPeRand['loc_id']." AND STR_TO_DATE(data, '%m-%Y') <= STR_TO_DATE('".$luna."', '%m-%Y') ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
    			$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

    		  if (mysql_num_rows($aveaDePlata) != 0) {

	    		    if(mysql_result($aveaDePlata, 0, 'data') != $luna)
	    		      die('Ultima inregistrare din fisa fond este mai veche de 1 luna fata de luna curent');

	    		    $fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
	    		    $fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
	    		    $fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
	    		    $fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
	    		  }
	          	  else {
		            $fondRulRest = 0;
		            $fondRepRest = 0;
		            $fondSpecRest = 0;
		            $fondPenConst = 0;
	    		  }


    			$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;

    			$insert = "INSERT INTO fisa_fonduri VALUES (null, '".$lunaUrm."', '".$locPeRand['asoc_id']."', '".$locPeRand['scara_id']."', ".$locPeRand['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";

				  $insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
    		}


        $restanta = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locPeRand['loc_id']." AND data='".$lunaUrm."'";
        $restanta = mysql_query($restanta) or die ("Nu pot selecta datele din fisa fonduri<br />".mysql_error());



    	$restanta_lunaAnt = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locPeRand['loc_id']." AND data='".$luna."'";
    	$restanta_lunaAnt = mysql_query($restanta_lunaAnt) or die ("Nu pot selecta datele din fisa fonduri luna anterioara<br />".mysql_error());


        $datoria = 0;
        $fondRep = 0;
        $fondRul = 0;
        if (mysql_num_rows($restanta) > 0){
            //$datoria = mysql_result($restanta, 0, 'restante');

        	$fondRul = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rul_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rul_rest');
        	$fondRep = mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_rep_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rep_rest');
        	$fondSpec = mysql_result($restanta, 0, 'fond_spec_rest') + mysql_result($restanta, 0, 'fond_spec_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_spec_rest');

        	$datoria = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_spec_rest')
        			   - $fondRul - $fondRep - $fondSpec;
			//restanta  = SUM(restanta fond X) - SUM(Fond curent X)
        	//Fond curent -> calculat dupa formula de mai jos
        	//Restanta fond -> restanta pe fiecare fond asa cum apare in DB



			//Fondul curent = RLC + ILC - RLT
        	//R - restanta
        	//I - incasare
        	//LC - luna curenta
        	//LT - luna trecuta


			//$fondRul = mysql_result($restanta, 0, 'fond_rul_rest');
            //$fondRep = mysql_result($restanta, 0, 'fond_rep_rest');
        }

        $putConsum = "UPDATE lista_plata SET rest_fond=".$datoria.", fond_rul=".$fondRul.", fond_rep=".$fondRep." WHERE loc_id=".$locPeRand['loc_id']." AND luna='".$luna."'  AND procesata=0";
        $putConsum = mysql_query($putConsum) or die ("Nu pot updata informatiile ce tin de fonduri<br />".mysql_error());
    }

    //Incalzire
    $incalzire = "SELECT * FROM lista_plata WHERE scara_id=".$scaraId." AND luna='".$luna."'";
    $incalzire = mysql_query($incalzire) or die ("Nu pot selecta toti locatarii din lista de plata<br />".mysql_error());

    while ($locPeRand = mysql_fetch_array($incalzire)){
        $dePlata = "SELECT sum(`cant_fact_pers`*`pret_unitar`) as Incalzire FROM `fisa_indiv` WHERE serviciu IN (SELECT serv_id FROM servicii WHERE serviciu LIKE 'incalzire%') AND luna='".$luna."' AND loc_id=".$locPeRand['loc_id'];
        $dePlata = mysql_query($dePlata) or die ("Nu pot afla suma de plata la invcalzire<br />".mysql_error());
        $dePlata = mysql_result($dePlata, 0, 'Incalzire');
        $dePlata = $dePlata == 0 ? 0 : $dePlata;

        $putConsum = "UPDATE lista_plata SET incalzire='".$dePlata."' WHERE loc_id='".$locPeRand['loc_id']."' AND luna='".$luna."'  AND procesata=0";
        $putConsum = mysql_query($putConsum) or die ("Nu pot updata informatiile ce tin de fonduri<br />".mysql_error());
    }

    fillTable($asocId, $scaraId, $luna, 1);
}


function fillTable($asoc_id, $scara_id, $luna, $state){
    $temp = 0;
    $selectLuna = "SELECT * FROM lista_plata WHERE scara_id=".$scara_id." AND luna='".$luna."' GROUP BY procesata";
    $selectLuna = mysql_query($selectLuna) or die ("Nu pot selecta datele corespunzatoare lunii curente<br />".mysql_error());

    if (mysql_num_rows($selectLuna) > 0){
        if (mysql_result($selectLuna, 0, 'procesata') == 0){        //daca lista de plata nu e finalizata
            $temp = 1;
        }

        if ($state == 1) { $temp = 0; } //daca am revenit aici din alta functie decat main, pot sa umplu tabelul
        if ($temp == 0){
            $i = 0;

            $nr_pers = 0;
            $sup_tot = 0;

            $total_consum_apa_rece = 0;
            $total_valoare_apa_rece = 0;
            $total_diferente_apa_rece = 0;

            $total_consum_apa_calda = 0;
            $total_valoare_apa_calda = 0;
            $total_diferente_apa_calda = 0;

            $total_diferente_apa_locatar = 0;
            $total_valoare_diferente = 0;

            $total_incalzire = 0;

            $total_chelt_pers = 0;
            $total_chelt_cota_indiv = 0;
            $total_chelt_benef = 0;
            $total_chelt_alta_natura = 0;

            $total_restante = 0;
            $total_penalizari = 0;
            $total_restanta_fond = 0;
            $total_fond_rulment = 0;
            $total_fond_reparatii = 0;

            $total_partial_final = 0;
            $total_general_final = 0;

            $sql = "SELECT * FROM lista_plata WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND luna='".$luna."' ORDER BY cast(ap as UNSIGNED ) ASC , SUBSTR( ap, -1, 1 ) ASC ";
            $sql = mysql_query($sql) or die("Nu ma pot conecta la tabela lista_plata<br />".mysql_error());

            $verifFI = "SELECT SUM(cant_fact_pers*pret_unitar)/SUM(cant_fact_pers) as pret_apa_rece FROM fisa_indiv WHERE scara_id=".$scara_id." AND luna='".$luna."' AND serviciu='21'";
            $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
            if (mysql_num_rows($verifFI) == 0) { $areApaRece = false; $pret_apa_rece = 1;} else { 
                $pret_apa_rece = mysql_result($verifFI, 0, 'pret_apa_rece');
                $areApaRece = true; 
            }

            $verifFI = "SELECT SUM(cant_fact_pers*pret_unitar)/SUM(cant_fact_pers)*2 as pret_apa_calda FROM fisa_indiv WHERE scara_id=".$scara_id." AND luna='".$luna."' AND (serviciu='37' OR serviciu='38')";
            $verifFI = mysql_query($verifFI) or die ("Nu pot accesa fisele individuale<br />".mysql_error());
            if (mysql_num_rows($verifFI) == 0) { $areApaCalda = false; $pret_apa_calda = 1;} else { 
                $pret_apa_calda = mysql_result($verifFI, 0, 'pret_apa_calda');
                $areApaCalda = true; 
            }

            while ($row = mysql_fetch_array($sql)){

                if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
                    $nr_pers += $row['nr_pers'];
                    $sup_tot += $row['supr'];

                    $total_diferente_apa_locatar = $row['dif_ac'] + $row['dif_ar'];

                    $total_partial = $row['ar_val'] + $row['ac_val'] + $total_diferente_apa_locatar + $row['incalzire'] + $row['chelt_pe_pers'] + $row['chelt_cota_indiv'] + $row['chelt_pe_benef'] + $row['alte_cheltuieli'];
                    $total_general = $total_partial + $row['restante'] + $row['penalizari'] + $row['fond_rul'] + $row['fond_rep'] + $row['rest_fond'];

                    $total_consum_apa_rece += $row['ar'];
                    $total_valoare_apa_rece += $row['ar_val'];
                    $total_diferente_apa_rece += $row['dif_ar'];

                    $total_consum_apa_calda += $row['ac'];
                    $total_valoare_apa_calda += $row['ac_val'];
                    $total_diferente_apa_calda += $row['dif_ac'];

                    $total_valoare_diferente = $total_diferente_apa_calda + $total_diferente_apa_rece;

                    $total_incalzire += $row['incalzire'];

                    $total_chelt_pers += $row['chelt_pe_pers'];
                    $total_chelt_cota_indiv += $row['chelt_cota_indiv'];
                    $total_chelt_benef += $row['chelt_pe_benef'];
                    $total_chelt_alta_natura += $row['alte_cheltuieli'];

                    $total_restante += $row['restante'];
                    $total_penalizari += $row['penalizari'];
                    $total_restanta_fond += $row['rest_fond'];
                    $total_fond_rulment += $row['fond_rul'];
                    $total_fond_reparatii += $row['fond_rep'];

                    $total_partial_final += $total_partial;
                    $total_general_final += $total_general;


                    echo '<tr bgcolor='.$color.' class="ap '.$row['ap'].'">';
                            echo '<td>'.$row['ap'].'</td>';
                            echo '<td><a href="index.php?link=locatari&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&edit='.$row['loc_id'].'" target="_blank">'.$row['nume'].'</a></td>';
                            echo '<td>'.$row['centrala'].'</td>';
                            echo '<td>'.$row['nr_pers'].'</td>';
                            echo '<td>'.round($row['supr']).'</td>';
                            echo '<td><a href="index.php?link=fisa_cons&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&locatar='.$row['loc_id'].'" target="_blank">'.$row['ar'].'</a></td>';
                            echo '<td>'.round($row['ar_val'], 2).'</td>';
                            echo '<td><a href="index.php?link=locatari_apometre&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&editeaza='.$row['loc_id'].'&luna='.$luna.'" target="_blank">'.$row['ac'].'</a></td>';
                            echo '<td>'.round($row['ac_val'], 2).'</td>';
                            echo '<td>'.round($row['dif_ar'] / $pret_apa_rece, 2).'</td>';
                            echo '<td>'.round($row['dif_ac'] / $pret_apa_calda, 2).'</td>';
                            echo '<td>'.round($total_diferente_apa_locatar, 2).'</td>';
                            echo '<td>'.round($row['incalzire'], 2).'</td>';
                            echo '<td>'.round($row['chelt_pe_pers'], 2).'</td>';
                            echo '<td>'.round($row['chelt_cota_indiv'], 2).'</td>';
                            echo '<td>'.round($row['chelt_pe_benef'], 2).'</td>';
                            echo '<td>'.round($row['alte_cheltuieli'], 2).'</td>';
                            echo '<td><a href="index.php?link=fisa_indv&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&loc_id='.$row['loc_id'].'&luna='.$luna.'" target="_blank">'.round($total_partial, 2).'</a></td>';
                            echo '<td><a href="index.php?link=fisa_cont&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&locatar='.$row['loc_id'].'" target="_blank">'.round($row['restante'], 2).'</a></td>';
                            echo '<td><a href="index.php?link=fisa_pen&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&locatar='.$row['loc_id'].'" target="_blank">'.round($row['penalizari'], 2).'</a></td>';
                            echo '<td><a href="index.php?link=fisa_fonduri&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&loc_id='.$row['loc_id'].'&luna='.$luna.'" target="_blank">'.round($row['rest_fond'], 2).'</td>';
                            echo '<td>'.round($row['fond_rul'], 2).'</td>';
                            echo '<td>'.round($row['fond_rep'], 2).'</td>';
                            echo '<td>'.round($total_general, 2).'</td>';
                            echo '<td><a href="index.php?link=locatari_sub&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&loc_id='.$row['loc_id'].'" target="_blank">'.$row['ap'].'</a></td>';
                    echo '</tr>';
                    $i++;

            	if (isset($_GET['proceseaza']) && $_GET['proceseaza'] == 1) {
            		proceseazaLista($row, $total_general, $row['restante'], $row['penalizari']);
            	}
            }

    echo '<tr bgcolor="#FFFFFF"> <td colspan="25">&nbsp;</td></tr>';

            if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
            echo '<tr bgcolor='.$color.'>';
                    echo '<td colspan="2">TOTAL</td>';
                    echo '<td>&nbsp;</td>';
                    echo '<td>'.$nr_pers.'</td>';
                    echo '<td>'.$sup_tot.'</td>';
                    echo '<td>'.round($total_consum_apa_rece, 2).'</td>';
                    echo '<td>'.round($total_valoare_apa_rece, 2).'</td>';
                    echo '<td>'.round($total_consum_apa_calda, 2).'</td>';
                    echo '<td>'.round($total_valoare_apa_calda, 2).'</td>';
                    echo '<td>'.round($total_diferente_apa_rece / $pret_apa_rece, 2).'</td>';
                    echo '<td>'.round($total_diferente_apa_calda / $pret_apa_calda, 2).'</td>';
                    echo '<td>'.round($total_valoare_diferente, 2).'</td>';
                    echo '<td>'.round($total_incalzire, 2).'</td>';
                    echo '<td>'.round($total_chelt_pers, 2).'</td>';
                    echo '<td>'.round($total_chelt_cota_indiv, 2).'</td>';
                    echo '<td>'.round($total_chelt_benef, 2).'</td>';
                    echo '<td>'.round($total_chelt_alta_natura, 2).'</td>';
                    echo '<td>'.round($total_partial_final, 2).'</td>';
                    echo '<td>'.round($total_restante, 2).'</td>';
                    echo '<td>'.round($total_penalizari, 2).'</td>';
                    echo '<td>'.round($total_restanta_fond, 2).'</td>';
                    echo '<td>'.round($total_fond_rulment, 2).'</td>';
                    echo '<td>'.round($total_fond_reparatii, 2).'</td>';
                    echo '<td>'.round($total_general_final, 2).'</td>';
                    echo '<td>&nbsp;</td>';
            echo '</tr>';
            $i++;
        } else {
            putDataInTable($asoc_id, $scara_id, $luna, 0);  //daca nu este prima accesare a functiei putDataInTable, nu este necesar sa reintroduc luna, numele & so on
        }
    } else {
        putDataInTable($asoc_id, $scara_id, $luna, 0);  //nu am inregistrarea in BD si introduc datele
    }
}
////////////////////////////////////////////////////////////////////////////////////

if ($_GET['asoc_id']<>null){
	$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
} else {
	$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
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


//daca data este >15 introducem luna anterioara in cazul in care nu exista

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {
	if ($_GET['luna']<>null){
		$sql3 = "SELECT * FROM lista_plata WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND luna<>'".$_GET['luna']."' GROUP BY luna ORDER BY id_lista_plata DESC";
	} else {
		$sql3 = "SELECT * FROM lista_plata WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." GROUP BY luna ORDER BY id_lista_plata DESC";
	}
	$sql3 = mysql_query($sql3) or die ("Nu pot selecta lunile pentru care este inregistrata intretinerea<br />".mysql_error());

        if (mysql_num_rows($sql3) > 0){
            while($row3= mysql_fetch_array($sql3)) {
                $lunaS .= '<option value="'.$row3[1].'">'.$row3[1].'</option>';
            }

            /* if (mktime(0, 0, 0, $dataS[0], 1, $dataS[1]) < (mktime(0, 0, 0, date('m')-1, 1, date('Y')))){
             *
             *  if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) > mktime(0, 0, 0, $dataS[0]-1, 14, $dataS[1]))
             *
             * if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) > mktime(0, 0, 0, date('m'), 14, date('Y');
             */

            // trebuie sa verific datele
            $dataS = explode('-', mysql_result(mysql_query("SELECT * FROM lista_plata WHERE scara_id=".$_GET['scara_id']." GROUP BY luna ORDER BY id_lista_plata DESC"), 0, 'luna'));
            if (mktime(0, 0, 0, $dataS[0], 1, $dataS[1]) <= (mktime(0, 0, 0, date('m')-1, 1, date('Y')))){//daca nu e luna anteriora
                //die();
                if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) > mktime(0, 0, 0, $dataS[0]-1, 1, $dataS[1])){//daca a trecut mai mult de 1l si 14z
                    $lunaS .= '<option value="'.date('m-Y', mktime(0, 0, 0, $dataS[0]+1, 1, $dataS[1])).'">'.date('m-Y', mktime(0, 0, 0, $dataS[0]+1, 1, $dataS[1])).'</option>';
                }//afiseaza luna urmatoare fata de ce e in BD
            }
        } else {
            if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) >= mktime(0, 0, 0, date('m'), 1, date('Y'))){
                //daca nu este nici o inregistrare in baza de date, inserez automat luna anterioara
                $lunaS = '<option value="'.date('m-Y', mktime(0, 0, 0, date('m')-1, 1, date('Y'))).'">'.date('m-Y', mktime(0, 0, 0, date('m')-1, 1, date('Y'))).'</option>';
            } else {
                $lunaS = '<option value="'.date('m-Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))).'">'.date('m-Y', mktime(0, 0, 0, date('m')-2, 1, date('Y'))).'</option>';
            }
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
#print {float:left; margin-left:960px; margin-top:15px;}
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
    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti luna:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_luna(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['luna']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">'.$_GET['luna'].'</option>';   }?>
        		<?php echo $lunaS; ?>
            </select></td>
    </tr>
    <?php endif;?>
</table>

</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']!=null) && ($_GET['luna']<>null)):?>
<?php include 'lista_fact_ajax.php'; ?>  

  <?php $procesata = "SELECT 1 FROM lista_plata WHERE `luna`='".$_GET['luna']."' AND `scara_id`='".$_GET['scara_id']."' AND procesata=1";
  		$procesata = mysql_query($procesata) or die ("Nu pot afla daca lista a fost procesata<br />".mysql_error());
  		$procesata = mysql_num_rows($procesata) != 0 ? true : false;
		if ($procesata) : ?>
  			<h2>Lista Procesata</h2>
  		<?php endif; ?>
  <form action="" method="post">
    <?php if (!$procesata) : ?>
    <a href="index.php?link=lista_plata&asoc_id=<?php echo $_GET['asoc_id'] ?>&scara_id=<?php echo $_GET['scara_id'] ?>&luna=<?php echo $_GET['luna'] ?>&proceseaza=1">Proceseaza</a>
    <?php elseif($procesata && $_SESSION['uid'] == 0): ?>
    <a onClick="
  s=prompt('Introdu motivul deprocesarii LP:','Motiv');
  window.location.href = 'index.php?link=lista_plata&asoc_id=<?php echo $_GET['asoc_id'] ?>&scara_id=<?php echo $_GET['scara_id'] ?>&luna=<?php echo $_GET['luna'] ?>&proceseaza=0&motivDeprocesare='+s;">Deproceseaza</a>
    <?php endif; ?>
  <div id="print">
  	
  </div>


  
  <a target="_blank" href="modules/pdf/pdf.php?asoc_id=<?php echo $_GET['asoc_id'] ?>&scara_id=<?php echo $_GET['scara_id'] ?>&luna=<?php echo $_GET['luna'] ?>"><div class="pdf1"></div></a>
  <br clear="all" />
<table width="1024" style="top:250px; background-color:white;" id="ListaPlata">
<thead>
	<tr>
        <td bgcolor="#666666" rowspan="2">Nr. Ap.</td>
        <td bgcolor="#666666" rowspan="2" width="100">Nume</td>
        <td bgcolor="#666666" rowspan="2">CT</td>
        <td bgcolor="#666666" rowspan="2">Nr. Pers.</td>
        <td bgcolor="#666666" rowspan="2">ST</td>
        <td bgcolor="#666666" colspan="2"><a href="index.php?link=locatari_apometre&asoc_id=<?php echo $_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']; ?>" target="_blank">Apa Rece</a></td>
        <td bgcolor="#666666" colspan="2"><a href="index.php?link=locatari_apometre&asoc_id=<?php echo $_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']; ?>" target="_blank">Apa Calda</a></td>
        <td bgcolor="#666666" colspan="3">Diferenta</td>
        <td bgcolor="#666666" rowspan="2">Incalzire</td>
        <td bgcolor="#666666" rowspan="2">Chelt / pers</td>
        <td bgcolor="#666666" rowspan="2">Chelt / cota indivizia</td>
        <td bgcolor="#666666" rowspan="2">Chelt / beneficiari</td>
        <td bgcolor="#666666" rowspan="2">Chelt de alta natura</td>
        <td bgcolor="#666666" rowspan="2">Total Luna</td>
        <td bgcolor="#666666" rowspan="2">Restante</td>
        <td bgcolor="#666666" rowspan="2">Penalizari</td>
        <td bgcolor="#666666" rowspan="2"><a href="index.php?link=fisa_fonduri&asoc_id=<?php echo $_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']; ?>" target="_blank">Restanta fond</a></td>
        <td bgcolor="#666666" rowspan="2">Fond Rulment</td>
        <td bgcolor="#666666" rowspan="2">Fond Reparatii</td>
        <td bgcolor="#666666" rowspan="2">Total General</td>
        <td bgcolor="#666666" rowspan="2">Nr. Ap.</td>
	</tr>

	<tr>
    	<td bgcolor="#666666">AR</td>
        <td bgcolor="#666666">valoare</td>
        <td bgcolor="#666666">AC</td>
        <td bgcolor="#666666">valoare</td>
        <td bgcolor="#666666">AR</td>
        <td bgcolor="#666666">AC</td>
        <td bgcolor="#666666">valoare</td>
    </tr>
</thead>
<tbody align="left">
	<?php
            fillTable($_GET['asoc_id'], $_GET['scara_id'], $_GET['luna'], 0)      //0(state) este numarul de cicluri in care intra in fillTable
        ?>

</tbody>
</table>
</form>
<?php endif; ?>