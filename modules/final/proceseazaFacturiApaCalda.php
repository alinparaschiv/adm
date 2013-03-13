<?php

$calculezTotalApaCalda = "SELECT * FROM facturi WHERE scara_id=".$scaraId." AND tipServiciu=".$tipServiciu." AND luna='".$luna."' AND procesata=0 ORDER BY subtipFactura ASC";
$calculezTotalApaCalda = mysql_query($calculezTotalApaCalda) or die ("Nu pot selecta subFacturile pentru apa calda<br />".mysql_error());

while ($factApaCalda = mysql_fetch_array($calculezTotalApaCalda)) {
    /*
                                    - calculez totalul de apa consumat de locatari
                                    - verific cantitatea consumata de locatari si cantitatea de pe factura (pt apa)
    */

    //aflu cantitatea de apa calda declarata de locatari
    $totalApaCaldaDeclarata = "SELECT SUM(consum_cald) FROM apometre WHERE luna='".$luna."' AND scara_id=".$scaraId;
    $totalApaCaldaDeclarata = mysql_query($totalApaCaldaDeclarata) or die ("Nu pot afla consumul total de apa calda<br />".mysql_error());
    $totalApaCaldaDeclarata = mysql_result($totalApaCaldaDeclarata, 0, 'SUM(consum_cald)');

    //apa rece pentru apa calda
    if ($factApaCalda['subtipFactura'] == 37) {
        $ppuApaRecePentruApaCalda = $factApaCalda['cost'] / $factApaCalda['cantitate'];					//pretul pentru un m3
        $valoareApaRecePentruApaCalda = $factApaCalda['cost'];											//valoarea din factura
        $cantitateApaRecePentruApaCalda = $factApaCalda['cantitate'];									//cantitatea facturata
        $facturaCurrARPAC = $factApaCalda['serieFactura'].' / '.$factApaCalda['numarFactura'];

        //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
        $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0";
        $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

        while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
            $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
            $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa calda a consumat un locatar<br />".mysql_error());

            $apaCaldaConsumata = mysql_result($aConsumat, 0, 'consum_cald');
            $apa = $ppu * $apaCaldaConsumata;	//costul apei cheltuite de fiecare locatar (fara diferente)

            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '37', '".$apaCaldaConsumata."', '$ppuApaRecePentruApaCalda','metru cub', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$totalApaCaldaDeclarata')";
            $plateste = mysql_query($plateste) or die ("Nu pot insera factura de apa rece pentru apa calda<br />".mysql_error());
        }

        $update = "UPDATE facturi SET procesata=1 WHERE fact_id=".$factApaCalda['fact_id'];
        $update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());
    }

    //agent termic pentru apa calda
    if ($factApaCalda['subtipFactura'] == 38) {
        $ppunitateAgTermicPtApaCalda = $factApaCalda['cost'] / $cantitateApaRecePentruApaCalda;				//pretul care apare in stanga
        $cantitateAgTermicPtApaCalda = $factApaCalda['cantitate'] / $cantitateApaRecePentruApaCalda;		//cantitatea aferenta unui m3 de apa calda (apare in stanga)

        $AgTermicPentruApaCalda = $cantitateAgTermicPtApaCalda*$totalApaCaldaDeclarata;
        $diferentaAgTermicPentruApaCalda = $factApaCalda['cantitate'] - $cantitateAgTermicPtApaCalda*$totalApaCaldaDeclarata;	//cantitatea care apare la diferente

        $ppuAgTermicPtApaCalda = $factApaCalda['cost'] / $factApaCalda['cantitate'];	//pretul pt 1 GKal (apare in dreapta)

        $facturaCurrATPAC = $factApaCalda['serieFactura'].' / '.$factApaCalda['numarFactura'];

        $update = "UPDATE facturi SET procesata=1 WHERE fact_id=".$factApaCalda['fact_id'];
        $update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());

        $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0";
        $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

        while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
            $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
            $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa calda a consumat un locatar<br />".mysql_error());

            $apaCaldaConsumata = mysql_result($aConsumat, 0, 'consum_cald');

            $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '38', '".$apaCaldaConsumata."', '$ppunitateAgTermicPtApaCalda','m<sup>3</sup> apa rece pt acm', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', ".$AgTermicPentruApaCalda.")";
            $plateste = mysql_query($plateste) or die ("Nu pot insera factura de apa rece pentru apa calda<br />".mysql_error());
        }
    }
}

/**
 *		Calcul diferente apa rece pentru apa calda
 */

//metri cub diferenta
$diferenteApaRecePentruApaCalda = $cantitateApaRecePentruApaCalda - $totalApaCaldaDeclarata;

//persoane care nu au dat citirea

$nuAuDeclarat = "SELECT COUNT(loc_id) FROM apometre WHERE completat=0 AND luna='".$luna."' AND loc_id IN (SELECT loc_id FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0)";
$nuAuDeclarat = mysql_query($nuAuDeclarat) or die ("Nu pot afla cate persoane nu au declarat<br />".mysql_error());
$nuAuDeclarat = mysql_result($nuAuDeclarat, 0, 'COUNT(loc_id)');
echo '<br />Nu Au Declarat: '.$nuAuDeclarat;

//metodele de impartire in cazul diferentelor
$metode = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
$metode = mysql_query($metode) or die ("Nu pot afla metodele de impartire a diferentelor<br />".mysql_error());

$aDToti = mysql_result($metode, 0, 'impartire1');
$nADToti = mysql_result($metode, 0, 'impartire2');

if ($diferenteApaRecePentruApaCalda > 0) {
    if ($nuAuDeclarat == 0) {					//toata lumea a declarat consumul
        switch ($aDToti) {
            case 0:		// Numar Persoane			--> In cazul in care am apartamente nelocuite si se calculeaza diferentele/nr pers se trece 1 pers din of
                $numarLocatari = "SELECT SUM(nr_pers) FROM locatari WHERE ap_locuit=1 AND ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
				$numarLocatari = mysql_query($numarLocatari) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarLocatari = mysql_result($numarLocatari, 0, 'SUM(nr_pers)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda*$ppuApaRecePentruApaCalda) / $numarLocatari;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND ap_locuit=1  AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
				$areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '".$areApaPlateste['nr_pers']."', '$cantitatePePersoana','persoana', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 5:		// Din Oficiu (1 persoana)
            case 1:		// Cota Indiviza	(Suprafata)
                $cotaIndiviza = "SELECT SUM(supr) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $cotaIndiviza = mysql_query($cotaIndiviza) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $cotaIndiviza = mysql_result($cotaIndiviza, 0, 'SUM(supr)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $cotaIndiviza;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '".$areApaPlateste['supr']."', '$cantitatePePersoana','cota indiviza', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 2:		// Numar Apometre
                $numarApometre = "SELECT SUM(ap_calda) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $numarApometre = mysql_query($numarApometre) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApometre = mysql_result($numarApometre, 0, 'SUM(ap_calda)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $numarApometre;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0  AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '".$areApaPlateste['ap_calda']."', '$cantitatePePersoana','nr. apometre', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 3:		// Pe Apartamente
                $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '1', '$cantitatePePersoana','apartament', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 4:		// Proportional Consumului
                $consumTotal = "SELECT SUM(consum_cald) FROM apometre WHERE scara_id=".$scaraId." AND luna='".$luna."' AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";

				$consumTotal = mysql_query($consumTotal) or die ("Nu pot afla consumul total de apa calda<br />".mysql_error());
                $consumTotal = mysql_result($consumTotal, 0, 'SUM(consum_cald)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $consumTotal;

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";

				$areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                    $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa calda a consumat un locatar<br />".mysql_error());

                    $consumApaCalda = mysql_result($aConsumat, 0, 'consum_cald');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '".$consumApaCalda."', '$cantitatePePersoana','proportional cu consumul', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 6:		// Pe Apartamente Locuite
                $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE ap_locuit=1 AND ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND ap_locuit=1 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '39', '1', '$cantitatePePersoana','apartament', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;
        }
    }
	else {									//sunt persoane care nu au declarat consumul de apa calda
        switch ($nADToti) {
            case 2:		// Dif nr persoane prezente amenda
                $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $nrPersPrezente;
                $metriPePersoana = $diferenteApaRecePentruApaCalda / $nrPersPrezente;

                //inserez in fisa individuala doar la cei care nu au declarat
                $areApaCalda = "SELECT * FROM locatari WHERE ap_calda>0 AND scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {

					//verific cine nu a declarat apa
                	$nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                	$nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    $apometreCalda = array();

                    //indecsii vechi
                    if ($nrLuniApometre == 2) {
                        $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                        $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                        for ($i=1; $i<6; $i++) {
                            $apometreCalda[] = mysql_result($apoIni, 0, 'c'.$i);
                        }
                    } else {
                        for ($i=1; $i<6;$i++) {
                            $apometreCalda[] = mysql_result($nuADeclarat, 0, 'c'.$i);
                        }
                    }

                    if (mysql_num_rows($nuADeclarat) != 0) {

                        $apartamente[] = $areApaPlateste['loc_id'];

                        $nrPers = $areApaPlateste['nr_pers'];
                        $locId = $areApaPlateste['loc_id'];

                        //copiez indecsii anteriori
                        for ($i=1; $i<=$nrApoCald; $i++) {
                            $inserezConsumuri = "UPDATE apometre SET c".$i." = ".$apometreCalda[($i-1)]." WHERE loc_id=".$locId." AND luna='".$luna."'";
                            $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa calda<br />".mysql_error());
                        }
                        //inserez in apometre amenda
                        $updatezAmenda = "UPDATE apometre SET amenda_calda=1, auto=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $updatezAmenda = mysql_query($updatezAmenda) or die ("Nu pot updata amenda cald<br />".mysql_error());

                        //daca apa rece a fost procesata, setez si consumul
                        if ($nuADeclarat['amenda_rece'] != null) {
                            $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                        }

                        //inserez in apometre consumul
                        $consumCurent = $cantitatePePersoana;
                        $inApo = $metriPePersoana * $nrPers;

                        $insertApo = "UPDATE apometre SET consum_cald = ".$inApo." WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $insertApo = mysql_query($insertApo) or die("Nu pot insera consumul de apa calda<br />".mysql_error());

                        //inserez in fisa individuala consumul
                        $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId',".$areApaPlateste['loc_id'].", '$luna', 39, '$nrPers', '$cantitatePePersoana', 'persoana', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                        $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.1<br />".mysql_error());
                    }
                }
                break;

            case 3:		// Dif pe apartamente amenda
                $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $nrApNedeclarat;
                $metriPePersoana =  $diferenteApaRecePentruApaCalda / $nrApNedeclarat;

                //inserez in fisa individuala doar la cei care nu au declarat
                $areApaCalda = "SELECT * FROM locatari WHERE ap_calda>0 AND scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {

                	//verific cine nu a declarat apa
                	$nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                	$nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    $apometreCalda = array();

                    //indecsii vechi
                    if ($nrLuniApometre == 2) {
                        $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                        $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                        for ($i=1; $i<6; $i++) {
                            $apometreCalda[] = mysql_result($apoIni, 0, 'c'.$i);
                        }
                    } else {
                        for ($i=1; $i<6;$i++) {
                            $apometreCalda[] = mysql_result($nuADeclarat, 0, 'c'.$i);
                        }
                    }

                    if (mysql_num_rows($nuADeclarat) != 0) {

                        $apartamente[] = $areApaPlateste['loc_id'];
                        $nrApoCald = $areApaPlateste['ap_calda'];

                        $nrPers = $areApaPlateste['nr_pers'];
                        $locId = $areApaPlateste['loc_id'];

                        //copiez indecsii anteriori
                        for ($i=1; $i<=$nrApoCald; $i++) {
                            $inserezConsumuri = "UPDATE apometre SET c".$i." = ".$apometreCalda[($i-1)]." WHERE loc_id=".$locId." AND luna='".$luna."'";
                            $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa calda<br />".mysql_error());
                        }

                        //inserez in apometre amenda
                        $updatezAmenda = "UPDATE apometre SET amenda_calda=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $updatezAmenda = mysql_query($updatezAmenda) or die ("Nu pot updata amenda cald<br />".mysql_error());

                        //daca apa rece a fost procesata, setez si consumul
                        if ($nuADeclarat['amenda_rece'] != null) {
                            $updatezConsum = "UPDATE apometre SET completat=1, auto=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                        }

                        //inserez in apometre consumul
                        $consumCurent = $cantitatePePersoana;
                        $inApo = $metriPePersoana;

                        $insertApo = "UPDATE apometre SET consum_cald = ".$inApo." WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                        $insertApo = mysql_query($insertApo) or die("Nu pot insera consumul de apa calda<br />".mysql_error());

                        //inserez in fisa individuala consumul
                        $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId',".$areApaPlateste['loc_id'].", '$luna', 39, '1', '$cantitatePePersoana', 'apartament', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                        $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                    }
                }
                break;

            case 4:		// Dif nr persoane prezente cu modificarea indexului
                $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                echo '<br />Nr Prezente: '.$nrPersPrezente;

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $nrPersPrezente;
                $metriPePersoana = $diferenteApaRecePentruApaCalda / $nrPersPrezente;

                echo '<br />Cantitatea/Pers: '.$cantitatePePersoana;

                //inserez in fisa individuala doar la cei care nu au declarat
                $areApaCalda = "SELECT * FROM locatari WHERE ap_calda>0 AND scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    //verific cine nu a declarat apa
                    $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                    $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    $apometreCalda = array();

                    //indecsii vechi
                    if ($nrLuniApometre == 2) {
                        $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                        $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                        for ($i=1; $i<6; $i++) {
                            $apometreCalda[] = mysql_result($apoIni, 0, 'c'.$i);
                        }
                    } else {
                        for ($i=1; $i<6;$i++) {
                            $apometreCalda[] = mysql_result($nuADeclarat, 0, 'c'.$i);
                        }
                    }

                    if (mysql_num_rows($nuADeclarat) != 0) {
                        $apartamente[] = $areApaPlateste['loc_id'];

                        $nrPers = $areApaPlateste['nr_pers'];
                        $consumTotalAp = $cantitatePePersoana;
                        $inApo = $metriPePersoana * $nrPers;
                        $nrApoCald = $areApaPlateste['ap_calda'];

                        $restApa = $inApo % $nrApoCald;
                        $cPApometru = ($inApo - $restApa) / $nrApoCald;
                        $diferenta = 1;

                        $locId = $areApaPlateste['loc_id'];

                        //inserez in apometre consumurile
                        for ($i=1; $i<=$nrApoCald; $i++) {
                            if ($diferenta == 1) {
                                $inserezConsumuri = "UPDATE apometre SET c".$i." = ".($apometreCalda[($i-1)] + $cPApometru+$restApa)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                echo '<br />SQL: --> '.$inserezConsumuri;
                                $diferenta = 0;
                            } else {
                                $inserezConsumuri = "UPDATE apometre SET c".$i." = ".($apometreCalda[($i-1)] + $cPApometru)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                echo '<br />SQL: --> '.$inserezConsumuri;
                            }
                            $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa calda<br />".mysql_error());
                        }
                        $inserezConsumLuna = "UPDATE apometre SET consum_cald = '$inApo', auto=1 WHERE loc_id=".$locId." AND luna='".$luna."'";
                        $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa calda<br />".mysql_error());

                        //daca apa rece a fost procesata, setez si consumul
                        if ($nuADeclarat['consum_rece'] != 0) {
                            $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                        }

                        //inserez in fisa individuala consumul
                        $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId',".$areApaPlateste['loc_id'].", '$luna', 39, '$nrPers', '$consumTotalAp', 'persoana', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                        $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                    }
                }
                break;

            case 5:		// Dif pe apartamente cu modif indexului
                $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."') AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                $nrLuniApometre = "SELECT * FROM apometre WHERE scara_id=".$scaraId." GROUP BY luna";
                $nrLuniApometre = mysql_query($nrLuniApometre) or die ("Nu pot afla numarul de citiri din apometre<br />".mysql_error());
                $nrLuniApometre = mysql_num_rows($nrLuniApometre);

                $cantitatePePersoana = ($diferenteApaRecePentruApaCalda * $ppuApaRecePentruApaCalda) / $nrApNedeclarat;
                $metriPePersoana = $diferenteApaRecePentruApaCalda / $nrApNedeclarat;

                //inserez in fisa individuala doar la cei care nu au declarat
                $areApaCalda = "SELECT * FROM locatari WHERE ap_calda>0 AND scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=39 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                echo '<br />Nr Luni: '.$nrLuniApometre;
                echo '<br />';
                print_r($apometreCalda);

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    //verific cine nu a declarat apa
                    $nuADeclarat = "SELECT * FROM apometre WHERE completat=0 AND loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                    $nuADeclarat = mysql_query($nuADeclarat) or die ("Nu pot selecta locatarul care nu a declarat citirea de apa<br />".mysql_error());

                    $apometreCalda = array();

                    //indecsii vechi
                    if ($nrLuniApometre == 2) {
                        $apoIni = "SELECT * FROM locatari_apometre WHERE loc_id=".$areApaPlateste['loc_id'];
                        $apoIni = mysql_query($apoIni) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

                        for ($i=1; $i<6; $i++) {
                            $apometreCalda[] = mysql_result($apoIni, 0, 'c'.$i);
                        }
                    } else {
                        for ($i=1; $i<6;$i++) {
                            $apometreCalda[] = mysql_result($nuADeclarat, 0, 'c'.$i);
                        }
                    }
                    echo '<br />Urmeaza Apo Calda: ';
                    print_r($apometreCalda);

                    if (mysql_num_rows($nuADeclarat) != 0) {

                        $locId = $areApaPlateste['loc_id'];

                        $apartamente[] = $areApaPlateste['loc_id'];
                        $nrPers = $areApaPlateste['nr_pers'];

                        $consumTotalAp = $cantitatePePersoana;
                        $inApo = $metriPePersoana;

                        $nrApoCald = $areApaPlateste['ap_calda'];

                        $restApa = $inApo % $nrApoCald;
                        $cPApometru = ($inApo - $restApa) / $nrApoCald;
                        $diferenta = 1;

                        //inserez in apometre consumurile
                        for ($i=1; $i<=$nrApoCald; $i++) {
                            if ($diferenta == 1) {
                                $inserezConsumuri = "UPDATE apometre SET c".$i." = ".($apometreCalda[($i-1)] + $cPApometru+$restApa)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                                $diferenta = 0;
                            } else {
                                $inserezConsumuri = "UPDATE apometre SET c".$i." = ".($apometreCalda[($i-1)] + $cPApometru)." WHERE loc_id=".$locId." AND luna='".$luna."'";
                            }
                            $inserezConsumuri = mysql_query($inserezConsumuri) or die ("Nu pot salva consumurile de apa calda<br />".mysql_error());
                        }
                        $inserezConsumLuna = "UPDATE apometre SET consum_cald = '$inApo', auto=1 WHERE loc_id=".$locId." AND luna='".$luna."'";
                        $inserezConsumLuna = mysql_query($inserezConsumLuna) or die ("Nu pot insera consumul total de apa calda<br />".mysql_error());

                        //daca apa rece a fost procesata, setez si consumul
                        if ($nuADeclarat['consum_rece'] != null) {
                            $updatezConsum = "UPDATE apometre SET completat=1 WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                            $updatezConsum = mysql_query($updatezConsum) or die ("Nu pot updata consumul<br />".mysql_error());
                        }

                        //inserez in fisa individuala consumul
                        $insertConsum = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId',".$areApaPlateste['loc_id'].", '$luna', 39, '1', '$consumTotalAp', 'apartament', '$facturaCurrARPAC','$ppuApaRecePentruApaCalda', '$diferenteApaRecePentruApaCalda')";
                        $insertConsum = mysql_query($insertConsum) or die ("Nu pot insera in fisa individuala criteriul 1.2<br />".mysql_error());
                    }
                }
                break;
        }
    }
}

/**
 *		Calculul diferentelor pentru agent termic pentru apa calda
 */

$valoareDiferenta = $diferentaAgTermicPentruApaCalda * $ppuAgTermicPtApaCalda;

if ($diferentaAgTermicPentruApaCalda > 0) {
    if ($nuAuDeclarat == 0) {					//toata lumea a declarat consumul
        switch ($aDToti) {
            case 5:		// Din Oficiu (1 persoana)
            case 0:		// Numar Persoane
                $numarLocatari = "SELECT SUM(nr_pers) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $numarLocatari = mysql_query($numarLocatari) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarLocatari = mysql_result($numarLocatari, 0, 'SUM(nr_pers)');

                $cantitatePePersoana = $valoareDiferenta / $numarLocatari;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '".$areApaPlateste['nr_pers']."', '$cantitatePePersoana','persoana', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 1:		// Cota Indiviza
                $cotaIndiviza = "SELECT SUM(supr) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $cotaIndiviza = mysql_query($cotaIndiviza) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $cotaIndiviza = mysql_result($cotaIndiviza, 0, 'SUM(supr)');

                $cantitatePePersoana = $valoareDiferenta / $cotaIndiviza;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '".$areApaPlateste['supr']."', '$cantitatePePersoana','cota indiviza', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 2:		// Numar Apometre
                $numarApometre = "SELECT SUM(ap_calda) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $numarApometre = mysql_query($numarApometre) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApometre = mysql_result($numarApometre, 0, 'SUM(ap_calda)');

                $cantitatePePersoana = $valoareDiferenta / $numarApometre;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '".$areApaPlateste['ap_calda']."', '$cantitatePePersoana','nr. apometre', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 3:		// Pe Apartamente
                $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                $cantitatePePersoana = $valoareDiferenta / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '1', '$cantitatePePersoana','apartament', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 4:		// Proportional Consumului
            	$consumTotal = "SELECT SUM(consum_cald) FROM apometre WHERE scara_id=".$scaraId." AND luna='".$luna."' AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
            	$consumTotal = mysql_query($consumTotal) or die ("Nu pot afla consumul total de apa calda<br />".mysql_error());
                $consumTotal = mysql_result($consumTotal, 0, 'SUM(consum_cald)');

                $cantitatePePersoana = $valoareDiferenta / $consumTotal;

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
            	$areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
            	$areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $aConsumat = "SELECT * FROM apometre WHERE loc_id=".$areApaPlateste['loc_id']." AND luna='".$luna."'";
                    $aConsumat = mysql_query($aConsumat) or die ("Nu pot afla cata apa calda a consumat un locatar<br />".mysql_error());

                    $consumApaCalda = mysql_result($aConsumat, 0, 'consum_cald');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '".$consumApaCalda."', '$cantitatePePersoana','proportional cu consumul', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;
            case 6:		// Pe Apartamente Locuite
                $numarApartamente = "SELECT COUNT(*) FROM locatari WHERE ap_locuit=1 AND ap_calda>0 and scara_id=".$scaraId." AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $numarApartamente = mysql_query($numarApartamente) or die ("Nu pot afla numarul de locatari care beneficiaza de apa calda<br />".mysql_error());
                $numarApartamente = mysql_result($numarApartamente, 0, 'COUNT(*)');

                $cantitatePePersoana = $valoareDiferenta / $numarApartamente;	//cantitatea aferenta fiecarei persoane

                //trebuie sa inserez pentru fiecare in parte in fisa individuala doar daca beneficiaza de apa calda
                $areApaCalda = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND ap_calda>0 AND ap_locuit=1 AND loc_id NOT IN (SELECT loc_id FROM subventii WHERE serv_id=40 AND procent != 100)";
                $areApaCalda = mysql_query($areApaCalda) or die ("Nu pot selecta locatarii care beneficiaza de apa calda<br />".mysql_error());

                while ($areApaPlateste = mysql_fetch_array($areApaCalda)) {
                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$areApaPlateste['loc_id']."', '$luna', '40', '1', '$cantitatePePersoana','apartament', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;
        }
    }
	else {									//sunt persoane care nu au declarat consumul de apa calda
        switch ($nADToti) {
            case 2:		// Dif nr persoane prezente amenda
                $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."')";
                $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                $cantitatePePersoana = $valoareDiferenta / $nrPersPrezente;

                for ($i=0; $i<count($apartamente); $i++) {
                    $nrPersApaCalda = "SELECT nr_pers FROM locatari WHERE loc_id=".$apartamente[$i];
                    $nrPersApaCalda = mysql_query($nrPersApaCalda) or die ("Nu pot afla cati locatari sunt in apartament<br />".mysql_error());
                    $nrPersApaCalda = mysql_result($nrPersApaCalda, 0, 'nr_pers');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$apartamente[$i]', '$luna', '40', '$nrPersApaCalda', '$cantitatePePersoana', 'persoana', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 3:		// Dif pe apartamente amenda
                $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."')";
                $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                $cantitatePePersoana = $valoareDiferenta / $nrApNedeclarat;

                for ($i=0; $i<count($apartamente); $i++) {
                    $nrPersApaCalda = "SELECT nr_pers FROM locatari WHERE loc_id=".$apartamente[$i];
                    $nrPersApaCalda = mysql_query($nrPersApaCalda) or die ("Nu pot afla cati locatari sunt in apartament<br />".mysql_error());
                    $nrPersApaCalda = mysql_result($nrPersApaCalda, 0, 'nr_pers');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$apartamente[$i]', '$luna', '40', '1', '$cantitatePePersoana', 'apartament', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 4:		// Dif nr persoane prezente cu modificarea indexului
                $nrPersPrezente = "SELECT SUM(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."')";
                $nrPersPrezente = mysql_query($nrPersPrezente) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrPersPrezente = mysql_result($nrPersPrezente, 0, 'SUM(nr_pers)');

                $cantitatePePersoana = $valoareDiferenta / $nrPersPrezente;

                for ($i=0; $i<count($apartamente); $i++) {
                    $nrPersApaCalda = "SELECT nr_pers FROM locatari WHERE loc_id=".$apartamente[$i];
                    $nrPersApaCalda = mysql_query($nrPersApaCalda) or die ("Nu pot afla cati locatari sunt in apartament<br />".mysql_error());
                    $nrPersApaCalda = mysql_result($nrPersApaCalda, 0, 'nr_pers');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$apartamente[$i]', '$luna', '40', '$nrPersApaCalda', '$cantitatePePersoana', 'persoana', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;

            case 5:		// Dif pe apartamente cu modif indexului
                $nrApNedeclarat = "SELECT COUNT(nr_pers) FROM locatari WHERE ap_calda>0 AND loc_id IN (SELECT loc_id FROM apometre WHERE scara_id=".$scaraId." AND completat=0 AND luna='".$luna."')";
                $nrApNedeclarat = mysql_query($nrApNedeclarat) or die ("Nu pot afla numarul total de persoane care nu au declarat<br />".mysql_error());
                $nrApNedeclarat = mysql_result($nrApNedeclarat, 0, 'COUNT(nr_pers)');

                $cantitatePePersoana = $valoareDiferenta / $nrApNedeclarat;

                for ($i=0; $i<count($apartamente); $i++) {
                    $nrPersApaCalda = "SELECT nr_pers FROM locatari WHERE loc_id=".$apartamente[$i];
                    $nrPersApaCalda = mysql_query($nrPersApaCalda) or die ("Nu pot afla cati locatari sunt in apartament<br />".mysql_error());
                    $nrPersApaCalda = mysql_result($nrPersApaCalda, 0, 'nr_pers');

                    $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$apartamente[$i]', '$luna', '40', '1', '$cantitatePePersoana', 'apartament', '$facturaCurrATPAC','$ppuAgTermicPtApaCalda', '$diferentaAgTermicPentruApaCalda')";
                    $plateste = mysql_query($plateste) or die ("Nu pot insera diferenta pentru factura de apa rece pentru apa calda<br />".mysql_error());
                }
                break;
        }
    }
}