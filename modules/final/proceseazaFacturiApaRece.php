<?php

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
//echo $scariVizate;
$scariVizate = mysql_query($scariVizate) or die ("Nu pot afla scarile asociatiei curente<br />".mysql_error());

$nrOrdine = 0;
$nrPass = 0;

while ($sV = mysql_fetch_array($scariVizate)) {
    echo '<br />Bloc '.$sV['bloc'].': '.$diferenta[$nrOrdine];
    $consumBloc[$sV['bloc']] = $diferenta[$nrOrdine];			//un vector in care pastrez consumul pentru fiecare bloc in parte

    $nrOrdine ++;

    echo '<br />Asta e blocul: '.$sV['bloc']."\n";

    $arePasant = "SELECT * FROM scari_setari WHERE scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$sV['bloc']."') AND pasant='da'";
    //echo $arePasant;
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

echo '<br />Modul de impartire a apei intre scari pentru asociatia '.mysql_result(mysql_query("SELECT asociatie FROM asociatii WHERE asoc_id=".$asocId), 0, 'asociatie').' este "'.$asocCriteriuImpartireArr[(int)$impartApaIntreScari].'"';

//criterii de impartire a diferentelor
echo '<br />Modul de impartire a ape in cazul in care declara toti: '.$apaDeclaraArr[(int)$declaraTotiAR];
echo '<br />Modul de impartire a ape in cazul in care nu declara toti: '.$apaNuDeclaraArr[(int)$nDeclaraTotiAR];

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

                $diferenta = $consumulBloc - $consumARDeclarat;

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

		echo $consumARDeclarat.'<br />';
		$consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                echo '<br />Consumul declarat de locatari este '.$consumARDeclarat;

                $diferenta = $consumulBloc - $consumARDeclarat;
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

                $diferenta = $consumulBloc - $consumARDeclarat;
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
		echo $consumARDeclarat.'<br />';
		$consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
                $consumARDeclarat = mysql_result($consumARDeclarat, 0, 'SUM(consum_rece)');

                echo '<br />Consumul declarat de locatari este '.$consumARDeclarat;

                $diferenta = $consumulBloc - $consumARDeclarat;
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
    	case 4:			// consumul Total al locatarilor din fiecare scara (APA RECE + APA CALDA)
    		//verific daca am scari care nu au pasant pentru blocul curent
    		if ($bloc[$numarBloc] > $nrPasanteBloc[$numarBloc]) {
    			//suma consumurilor de apa rece
    			//pentru locatarii care stau pe
    			//scarile fara pasant din bloc
    			$consumARDeclarat = "SELECT SUM(consum_rece) as consum FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))";
    			$consumARDeclarat = mysql_query($consumARDeclarat) or die ("Nu pot afla consumul declarat de locatari<br />".mysql_error());
    			$consumARDeclarat = mysql_result($consumARDeclarat, 0, 'consum');

    			$diferenta = $consumulBloc - $consumARDeclarat;

    			//aflu consumul totala din blocurile fara pasant
    			$totalConsum = "SELECT SUM(consum_rece+consum_cald) as consum FROM apometre WHERE luna='".$luna."' AND scara_id IN (SELECT scara_id FROM scari WHERE bloc='".$numarBloc."' AND scara_id IN (SELECT scara_id FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='nu'))"; //echo '<br />SQL --> '.$nrTotMp;
    			$totalConsum = mysql_query($totalConsum) or die ("Nu pot afla numarul total de apartamente pentru scarile fara pasant<br />".mysql_error());
    			$totalConsum = mysql_result($totalConsum, 0, 'consum');

    			$cantPePers = $diferenta / $totalConsum;
    			echo '<br />Cantitatea aferenta fiecarei persoane este: '.$cantPePers;

    			//pentru fiecare bloc, calculez procentul din diferente pe care il are de adaugat la consum
    			$toateScarile = "SELECT * FROM scari WHERE bloc='".$numarBloc."' AND asoc_id=".$asocId." AND scara_id IN (SELECT scara_id FROM scari_setari WHERE pasant='nu')";
    			$toateScarile = mysql_query($toateScarile) or die ("Nu pot parcurge scarile blocului<br />".mysql_error());

    			while ($scariPeRand = mysql_fetch_array($toateScarile)) {
    				$consumTotalDeclarat = "SELECT SUM(consum_rece+consum_cald) as consum FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
    				$consumTotalDeclarat = mysql_query($consumTotalDeclarat) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());
    				$consumTotalDeclarat = mysql_result($consumTotalDeclarat, 0, 'consum');

    				$consumScara = $consumTotalDeclarat * $cantPePers;

    				$consumDeclaratScara = "SELECT SUM(consum_rece) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scariPeRand['scara_id'];
    				$consumDeclaratScara = mysql_query($consumDeclaratScara) or die ("Nu pot afla consumul declarat pentru scara<br />".mysql_error());
    				$consumDeclaratScara = mysql_result($consumDeclaratScara, 0, 'SUM(consum_rece)');

    				$scaraCuPasant[$scariPeRand['scara_id']] = ($consumScara + $consumDeclaratScara);
    				echo '<br />Pe scara asta sunt '.$nrPersScaraCurr.' MC consumati, iar consumul scarii este '.$consumScara;
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

        $platesteSQL = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$aAR['loc_id']."', '$luna', '21', '".$apaReceConsumata."', '$pretAR','m<sup>3</sup>', '$facturaAR','$pretAR', ".$apaDeclarataAsoc.")";
		$plateste = mysql_query($platesteSQL) or die ("Nu pot insera factura de apa rece<br />".$platesteSQL."<br />".mysql_error());
    }

    if ($totalApaDeclarata < $consumulScarii) {
        $diferentaAR = $consumulScarii - $totalApaDeclarata;

        if ($nuAuDeclarat == 0) {
            switch ($declaraTotiAR) {
                case 5:			// O Persoana
                case 0:			// Numar Persoane
                    echo '0';
                	$nrPers = "SELECT SUM(nr_pers * IFNULL( S.procent, 100) / 100) as 'SUM(nr_pers)' FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $nrPers = mysql_query($nrPers) or die ("Nu pot afla numarul de persoane din bloc<br />".mysql_error());
                    $nrPers = mysql_result($nrPers, 0, 'SUM(nr_pers)');

                    $cantPePers = ($diferentaAR * $pretAR) / $nrPers;

                    echo '<br /><---------------------------------->';
                    echo '<br />Cant pe pers: '.$cantPePers;
                    echo '<br />Diferenta AR: '.$diferentaAR;
                    echo '<br />Nr Pers scara: '.$nrPers;
                    echo '<br />Pret apa rece: '.$pretAR;

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                    $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($areApaPlateste['nr_pers'] * $areApaPlateste['procent'] / 100) ."', '$cantPePers','persoana', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                    }
                    break;

                case 1:			// Cota Indiviza
                    echo '1';
                    $cotaIndiviza = "SELECT SUM(supr * IFNULL( S.procent, 100) / 100) as 'SUM(supr)' FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $cotaIndiviza = mysql_query($cotaIndiviza) or die ("Nu pot suprafata apartamentelor<br />".mysql_error());
                    $cotaIndiviza = mysql_result($cotaIndiviza, 0, 'SUM(supr)');

                    $cantPePers = ($diferentaAR * $pretAR) / $cotaIndiviza;	//cantitatea aferenta fiecarei persoane

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                    $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($areApaPlateste['supr'] * $areApaPlateste['procent'] / 100)."', '$cantPePers','cota indiviza', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                    }
                    break;

                case 2:			// Numar Apometre
                    echo '2';
                    $numarApometre = "SELECT SUM(ap_rece * IFNULL( S.procent, 100) / 100) as 'SUM(ap_rece)' FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $numarApometre = mysql_query($numarApometre) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                    $numarApometre = mysql_result($numarApometre, 0, 'SUM(ap_rece)');

                    $cantPePers = ($diferentaAR * $pretAR) / $numarApometre;	//cantitatea aferenta fiecarei persoane

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                    $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                	  $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($areApaPlateste['ap_rece'] * $areApaPlateste['procent'] / 100)."', '$cantPePers','nr. apometre', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                    }
                    break;

                case 3:			// Pe Apartamente
                    echo '3';
                    $numarApartamente = "SELECT SUM(IFNULL( S.procent, 100) / 100) as 'COUNT(*)' FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                    $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                    $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                    $cantPePers = ($diferentaAR * $pretAR) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                	  $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                	  $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($areApaPlateste['procent'] / 100 )."', '$cantPePers','apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                    }
                    break;

                case 4:			// Proportional Consumului
                    echo '4';
                    $consumTotal = "SELECT SUM(consum_rece * IFNULL( S.procent, 100) / 100) as 'SUM(consum_rece)' FROM apometre A LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON A.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului." AND luna='".$luna."'";
                    $consumTotal = mysql_query($consumTotal) or die ("Nu pot afla consumul total de apa rece<br />".mysql_error());
                    $consumTotal = mysql_result($consumTotal, 0, 'SUM(consum_rece)');

                    $cantPePers = ($diferentaAR * $pretAR) / $consumTotal;

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                	  $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
                	  $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa rece a consumat un locatar<br />".mysql_error());

                        $consumApaRece = mysql_result($aConsumat, 0, 'consum_rece');

                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($consumApaRece * $areApaPlateste['procent'] / 100)."', '$cantPePers','proportional cu consumul', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
                    }
                    break;
                case 6:			// Pe Apartamente Locuite
                    echo '6';
                    $numarApartamente = "SELECT SUM(IFNULL( S.procent, 100) / 100) as 'COUNT(*)' FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE ap_locuit=1 AND scara_id=".$scaraBlocului;
                    $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa rece<br />".mysql_error());
                    $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                    $cantPePers = ($diferentaAR * $pretAR) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                    //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
                    $areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE ap_locuit=1 AND scara_id=".$scaraBlocului;
                	  $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($areApaPlateste['procent'] / 100 )."', '$cantPePers','apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                        $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa rece<br />".mysql_error());
                    }
                    break;
            	case 7:			// Proportional Consumului Total
            		echo '7';
            		$consumTotal = "SELECT SUM((consum_rece+consum_cald) * IFNULL( S.procent, 100) / 100) as 'SUM(consum_rece)' FROM apometre A LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON A.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului." AND luna='".$luna."'";
            		$consumTotal = mysql_query($consumTotal) or die ("Nu pot afla consumul total de apa rece<br />".mysql_error());
            		$consumTotal = mysql_result($consumTotal, 0, 'SUM(consum_rece)');

            		$cantPePers = ($diferentaAR * $pretAR) / $consumTotal;

            		//trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
            		$areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
            		$areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

            		while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
            			$aConsumat = "SELECT (consum_rece+consum_cald) AS consum_total, A.* FROM apometre A WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
            			$aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa rece a consumat un locatar<br />".mysql_error());

            			$consumApaRece = mysql_result($aConsumat, 0, 'consum_total');

            			$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '".($consumApaRece * $areApaPlateste['procent'] / 100)."', '$cantPePers','proportional cu consumul', '$facturaAR','$pretAR', '$diferentaAR')";
            			$plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
            		}
            		break;
            	case 8:			// Jumate Proportional cu consumul Jumate pe apartamente la cine nu a declarat
            		echo '8';
            		$consumTotal_s = "SELECT SUM((consum_rece) * IFNULL( S.procent, 100) / 100) as 'SUM(consum_rece)' FROM apometre A LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON A.loc_id = S.loc_id WHERE asoc_id=".$asocId." AND luna='".$luna."'";
            		$consumTotal_q = mysql_query($consumTotal_s) or die ("Nu pot afla consumul total de apa rece<br />".mysql_error());
            		$consumTotal = mysql_result($consumTotal_q, 0, 'SUM(consum_rece)');

            		$apAuto_s = "SELECT SUM(IFNULL( S.procent, 100) / 100) as 'SUM(consum_rece)' FROM apometre A LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON A.loc_id = S.loc_id WHERE asoc_id=".$asocId." AND luna='".$luna."' AND auto=1";
            		$apAuto_q = mysql_query($apAuto_s) or die ("Nu pot afla consumul total de apa rece<br />".mysql_error());
            		$apAuto = mysql_result($apAuto_q, 0, 'SUM(consum_rece)');

            		$catiAuto = $apAuto > 0 ? (floor($diferentaAR)) : 0;
					$diferentaAR = $diferentaAR * 2 - $catiAuto;

					$cantPePers = ($diferentaAR * $pretAR) / $consumTotal;
            		$pretAutoPers = ($catiAuto * $pretAR) / $apAuto;
            		$um ='proportional cu consumul';

            		//trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa rece
            		$areApaRece = "SELECT L.*, IFNULL( S.procent, 100) as procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=41) S ON L.loc_id = S.loc_id WHERE scara_id=".$scaraBlocului;
            		$areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

            		while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
            			$aConsumat = "SELECT (consum_rece+consum_cald) AS consum_total, A.* FROM apometre A WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
            			$aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa rece a consumat un locatar<br />".mysql_error());

            			$cantPePersCurent = $cantPePers+0;
            			$consumApaRece = mysql_result($aConsumat, 0, 'consum_total') * $areApaPlateste['procent'] / 100;

            			if (((int) mysql_result($aConsumat, 0, 'auto')) == 1 && $catiAuto > 0) {
            				if ($consumApaRece > 0)
								$cantPePersCurent += $pretAutoPers/$consumApaRece;
            				else {
            					$cantPePersCurent = $pretAutoPers;
            					$consumApaRece = 1;
            					$um = 'apartament';
            				}
            			}


            			$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului', '".$areApaPlateste['loc_id']."', '$luna', '41', '$consumApaRece', '$cantPePersCurent','$um', '$facturaAR','$pretAR', '$diferentaAR')";
            			$plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece<br />".mysql_error());
            		}
            		break;
            }
        }
		else {
            switch ($nDeclaraTotiAR) {
                case 2:			// Dif nr persoane prezente amenda
                    echo '8';
                    $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                    $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                    $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                    $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                    $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                    $cantPePers = ($diferentaAR * $pretAR) / $nrPersPrezente;
                    $metriPePersoana = $diferentaAR / $nrPersPrezente;

                    //inserez in fisa individuala doar la cei care nu au declarat
                    $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        //verific cine nu a declarat apa
                        $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    	$nrApoRece = $areApaPlateste['ap_rece'];;
                        $apometreRece = array();

                    	$nrLuniApDeclarate = aflaLuniApometre($areApaPlateste['asoc_id'], $areApaPlateste['loc_id']);

                    	$indexInitial = ($nrLuniApDeclarate <= 2) ? indexInitial($areApaPlateste['loc_id']) : null;
                    	$apometruLunaTrecuta = ($nrLuniApDeclarate > 2) ? apTrecute($areApaPlateste['loc_id'], $luna, 1) : null;
                    	switch($nrLuniApDeclarate){
                    		case 2: //nu a declarat nici o data apometrele
                    			for ($i=1; $i<6; $i++){
                    				$apometreRece[] = $indexInitial['r'.$i];
                    			}
                    			break;
                    		default: //sunt cel putin 2 inregistrari in tabela
                    			for ($i=1; $i<6; $i++){
                    				$apometreRece[] = $apometruLunaTrecuta['r'.$i];
                    			}
                    		} // switch

                        if (mysql_num_rows($nuADeclarat) != 0) {

                            $nrPers = $areApaPlateste['nr_pers'];

                            $locId = $areApaPlateste['loc_id'];

                            //inserez in apometre consumurile
                            for ($i=1; $i<=$nrApoRece; $i++) {
                                $inserezConsumuri = "UPDATE apometre SET r".$i." = '".$apometreRece[($i-1)]."' WHERE loc_id=".$locId." AND luna='".$luna."'";
                                $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                            }

                            //inserez in apometre amenda
                            $updatezAmenda = "UPDATE apometre SET amenda_rece=1, auto=1, repetari=0 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
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
                            //$insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 21, '$inApo', '$pretAR', 'm<sup>3</sup>', '$facturaAR','$pretAR', '$diferentaAR')";

                        	$insertConsum = "UPDATE fisa_indiv SET cant_fact_pers='$inApo' WHERE serviciu=21 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.1<br />".mysql_error());
                        }
                    }
                    break;
                case 3:			// Dif pe apartamente amenda
                    echo '9';
                    $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                    $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                    $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                    $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                    $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                    $cantPePers = ($diferentaAR * $pretAR) / $nrApNedeclarat;
                    $metriPePersoana =  $diferentaAR / $nrApNedeclarat;

                    //inserez in fisa individuala doar la cei care nu au declarat
                    $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $areApaRece = mysql_query($areApaRece) or die ("Nu pot selecta locatarii care beneficiaza de apa rece<br />".mysql_error());

                    while ($areApaPlateste = mysql_fetch_array($areApaRece)) {
                        //verific cine nu a declarat apa
                        $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    	$nrApoRece = $areApaPlateste['ap_rece'];;
                        $apometreRece = array();

                    	$nrLuniApDeclarate = aflaLuniApometre($areApaPlateste['asoc_id'], $areApaPlateste['loc_id']);

                    	$indexInitial = ($nrLuniApDeclarate <= 2) ? indexInitial($areApaPlateste['loc_id']) : null;
                    	$apometruLunaTrecuta = ($nrLuniApDeclarate > 2) ? apTrecute($areApaPlateste['loc_id'], $luna, 1) : null;
                    	switch($nrLuniApDeclarate){
                    		case 2: //nu a declarat nici o data apometrele
                    			for ($i=1; $i<6; $i++){
                    				$apometreRece[] = $indexInitial['r'.$i];
                    			}
                    			break;
                    		default: //sunt cel putin 2 inregistrari in tabela
                    			for ($i=1; $i<6; $i++){
                    				$apometreRece[] = $apometruLunaTrecuta['r'.$i];
                    			}
                    	} // switch

                        if (mysql_num_rows($nuADeclarat) != 0) {

                            $nrPers = $areApaPlateste['nr_pers'];

                            $locId = $areApaPlateste['loc_id'];

                            //inserez in apometre consumurile
                            for ($i=1; $i<=$nrApoRece; $i++) {
                                $inserezConsumuri = "UPDATE apometre SET r".$i." = '".$apometreRece[($i-1)]."' WHERE loc_id=".$locId." AND luna='".$luna."'";
                                $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa rece<br />".mysql_error());
                            }

                            //inserez in apometre amenda
                            $updatezAmenda = "UPDATE apometre SET amenda_rece=1, auto=1, repetari=0 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $updatezAmenda = mysql_query($updatezAmenda) or die ("Nu pot updata amenda rece<br />".mysql_error());

                            //daca apa rece a fost procesata, setez si consumul
                            if ($nuADeclarat['amenda_cald'] != null) {
                                $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                            }

                            //inserez in apometre consumul
                            $consumCurent = $cantPePers;
                            $inApo = $metriPePersoana;

                            $insertApo = "UPDATE apometre SET consum_rece = ".$inApo." WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $insertApo = mysql_query($insertApo) or die("Nu pot insera consumul de apa rece<br />".mysql_error());

                            //inserez in fisa individuala consumul
                            //$insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 21, '$inApo', '$pretAR', 'm<sup>3</sup>', '$facturaAR','$pretAR', '$diferentaAR')";
                        	$insertConsum = "UPDATE fisa_indiv SET cant_fact_pers='$inApo' WHERE serviciu=21 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2 1<br />".mysql_error());
                        }
                    }
                    break;
                case 4:			// Dif nr persoane prezente cu modificarea indexului
                    echo '10';
                    $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                    $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                    $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                    $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                    $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                    $cantPePers = ($diferentaAR * $pretAR) / $nrPersPrezente;
                    $metriPePersoana = $diferentaAR / $nrPersPrezente;

                    //inserez in fisa individuala doar la cei care nu au declarat
                    $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
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
                            $inserezConsumLuna = "UPDATE apometre SET consum_rece = '$inApo', auto=1, repetari=0 WHERE loc_id=".$locId." AND luna='".$luna."'";
                            $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa rece<br />".mysql_error());

                            //daca apa rece a fost procesata, setez si consumul
                            if ($nuADeclarat['consum_cald'] != 0) {
                                $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                            }

                            //inserez in fisa individuala consumul
                            //$insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41, '$nrPers', '$consumTotalAp', 'persoana', '$facturaAR','$pretAR', '$diferentaAR')";
                        	$insertConsum = "UPDATE fisa_indiv SET cant_fact_pers='$inApo' WHERE serviciu=21 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
							$insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2 2<br />".mysql_error());
                        }
                    }
                    break;
                case 5:			// Dif pe apartamente cu modif indexului
                    echo '11';
                    $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraBlocului." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
                    $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                    $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                    $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraBlocului." GROUP BY luna";
                    $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                    $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                    $cantPePers = ($diferentaAR * $pretAR) / $nrApNedeclarat;
                    $metriPePersoana = $diferentaAR / $nrApNedeclarat;

                    //inserez in fisa individuala doar la cei care nu au declarat
                    $areApaRece = "SELECT * FROM locatari WHERE scara_id=".$scaraBlocului." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=41 AND procent != 100)";
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
                            $inserezConsumLuna = "UPDATE apometre SET consum_cald = '$inApo', auto=1, repetari=0 WHERE loc_id=".$locId." AND luna='".$luna."'";
                            $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa rece<br />".mysql_error());

                            //daca apa rece a fost procesata, setez si consumul
                            if ($nuADeclarat['consum_cald'] != null) {
                                $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                                $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                            }

                            //inserez in fisa individuala consumul
                            //$insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraBlocului',".$areApaPlateste['loc_id'].", '$luna', 41, '1', '$consumTotalAp', 'apartament', '$facturaAR','$pretAR', '$diferentaAR')";
                        	$insertConsum = "UPDATE fisa_indiv SET cant_fact_pers='$inApo' WHERE serviciu=21 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2 3<br />".mysql_error());
                        }
                    }
                    break;
            }
        }
    }
}
