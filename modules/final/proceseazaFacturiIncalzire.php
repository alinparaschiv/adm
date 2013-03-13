<?php
// BLOCURI CU/FARA REPARTITOARE
// nu au repartitoare si centrala sau sunt debransati (incalzire == nu && centrala == nu) --> partile comune
// necontorizati --> incalzirea pausal

// BLOCURI CU GIGACALORIMETRE
// apartamente fara gigacalorimetre --> centrala

include_once(  'modules/fise/Furnizori.class.php');

$facturaI = $serieFactura.' / '.$numarFactura;
$pretUnit = $cost / $cantitate;

//verific daca este bloc cu repartitoare, cu gigacalorimetre sau necontorizat
$verificTipIncalzireBloc = "SELECT MAX(tip_incalzire) FROM locatari WHERE scara_id=".$scaraId;
$verificTipIncalzireBloc = mysql_query($verificTipIncalzireBloc) or die ("Nu pot afla tipul de incalzire din blocul curent<br />".mysql_error());

$verificTipIncalzireBloc = mysql_result($verificTipIncalzireBloc, 0, 'MAX(tip_incalzire)');

if ($verificTipIncalzireBloc == 0) {//pausal
    //ii selectez pe toti care au incalzire (centrala == nu && incalzire == da)
    $sql = "SELECT proc_incalzire FROM `scari_setari` WHERE `scara_id`=".$scaraId;
    $sql = mysql_query($sql) or die ("Proceseaza Incalzire 1: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());
    $procentComun = mysql_result($sql, 0, 'proc_incalzire');

    $sumaComuna = $procentComun == 0 ? 0 : $cost * $procentComun/100;


    $sql = "SELECT SUM(supr) FROM `locatari` WHERE `scara_id`='".$scaraId."' AND `incalzire`='da'";
    $sql = mysql_query($sql) or die ("Proceseaza Incalzire 1: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());
    $suprafataTotala = mysql_result($sql, 0, 'SUM(supr)');

    $sumaPeUnitateComuna = $sumaComuna / $suprafataTotala;

    $cotaIncalzire = "SELECT SUM(cota) FROM locatari WHERE scara_id='".$scaraId."' AND (centrala<>'CT' OR centrala IS NULL)";
    $cotaIncalzire = mysql_query($cotaIncalzire) or die ("Proceseaza Incalzire 2: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());
    $cotaIncalzire = mysql_result($cotaIncalzire, 0, 'SUM(cota)');

    $pretCI =  ($cost - $sumaComuna) / $cotaIncalzire;

    $selectLocatari = "SELECT * FROM locatari WHERE scara_id='".$scaraId."'";
    $selectLocatari = mysql_query($selectLocatari) or die ("Proceseaza Incalzire 3: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());

    while ($parcurg = mysql_fetch_array($selectLocatari)) {
        //pentru fiecare locatar in parte trebuie sa vad cat are de plata si eventual sa ii scad subventia
        $dePlataLocInit = 0;
        $unitateMasura = 'pausal';
        if($parcurg['incalzire'] == 'da') {//daca tre sa plateasca partile comune
            $dePlataLocInit += $sumaPeUnitateComuna*$parcurg['supr'];
        }
        if($parcurg['centrala'] <> 'CT') { //daca are incalzire de la CET
            $unitateMasura = 'm<sup>2</sup>';
            $dePlataLocInit += $parcurg['cota'] * $pretCI;
        }

        $pretIndividual = $dePlataLocInit / $parcurg['supr'];


        $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$parcurg['loc_id']."', '".$luna."', '".$tipServiciu."', '".$parcurg['supr']."', '$pretIndividual', '$unitateMasura', '".$facturaI."', '$pretUnit', '$cantitate')";
        $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala factura de incalzire<br />".mysql_error());


        if ($parcurg['procent'] > 0) { //are subventie
            $subventie = $dePlataLocInit*$parcurg['procent']/100;
            if ($subventie > $parcurg['plafon'])
                $subventie = $parcurg['plafon'];
        		Furnizori::insertPlata($factId, $subventie, 'Subventie Incalzire');
        		$subventie *= -1;
            $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$parcurg['loc_id']."', '".$luna."', '".$tipServiciu."', '1', '$subventie', 'subventie', '".$facturaI."', '$pretUnit', '$cantitate')";
            $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala subventia de incalzire 1<br />".mysql_error());
        }
    }
}
else if ($verificTipIncalzireBloc == 2) { // gigacalorimetru
    // verific daca macar 1 are gigacalorimetru si in caz afirmativ, ii selectez pe toti care au gigacalorimetre
    $verificGiga = "SELECT * FROM locatari WHERE scara_id=".$scaraId." AND tip_incalzire='2'";
    $verificGiga = mysql_query($verificGiga) or die ("Nu pot verifica locatarii care au gigacalorimetre<br />".mysql_error());

    $ppu = $proc['ppu'];
    $locatari = $proc['locatari'];

    //explode pentru fiecare locatar in parte
    $ppu = explode(',', $ppu);
    $canTotala = 0;
    foreach ($ppu as $key => $ppuLocatar) {
        $ppu[$key] = explode('#', $ppuLocatar);
        $ppu[$key] = ($ppu[$key][1] - $ppu[$key][0]) > 0 ? ($ppu[$key][1] - $ppu[$key][0]) : 0;
        $canTotala += $ppu[$key];
    }
    $locatari = explode(',', $locatari);

    $ppuGeneral = $cost / $canTotala;

    $selectLocatari = "SELECT * FROM locatari WHERE scara_id='".$scaraId."'";
    $selectLocatari = mysql_query($selectLocatari) or die ("Proceseaza Incalzire 4: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());
    $locatariArray = array();
    while($row = mysql_fetch_assoc($selectLocatari)) {
        $locatariArray[$row['loc_id']] = array('procent' => $row['procent'], 'plafon' => $row['plafon']);
    }

    foreach ($locatari as $nrOrdine=>$locatarId) {
        $um = 'megawatt';

        $dePlataLocInit = $ppu[$nrOrdine] * $ppuGeneral;
        $unitatiIndividuale = $dePlataLocInit / $ppuGeneral;

        $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$locatarId', '".$luna."', '".$tipServiciu."', '$unitatiIndividuale', '$ppuGeneral', '".$um."', '".$facturaI."', '$pretUnit', '$cantitate')";
        $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala factura de incalzire<br />".mysql_error());

        if ($locatariArray[$locatarId]['procent'] > 0) { //are subventie
            $subventie = $dePlataLocInit*$locatariArray[$locatarId]['procent']/100;
            if ($subventie > $locatariArray[$locatarId]['plafon'])
                $subventie = $locatariArray[$locatarId]['plafon'];
        		Furnizori::insertPlata($factId, $subventie, 'Subventie Incalzire');
            $subventie *= -1;
            $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$locatarId."', '".$luna."', '".$tipServiciu."', '1', '$subventie', 'subventie', '".$facturaI."', '$pretUnit', '$cantitate')";
            $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala subventia de incalzire 2<br />".mysql_error());
        }
    }
}
else if ($verificTipIncalzireBloc == 1) {//repartitoare
    $ppu = $proc['ppu'];
    $locatari = $proc['locatari'];
    //explode pentru fiecare locatar in parte
    $ppu = explode(',', $ppu);
    $locatari = explode(',', $locatari);

    $selectLocatari = "SELECT * FROM locatari WHERE scara_id='".$scaraId."'";
    $selectLocatari = mysql_query($selectLocatari) or die ("Proceseaza Incalzire 4: </br> Nu pot afla locatarii care beneficiaza de incalzire<br />".mysql_error());
    $locatariArray = array();
    while($row = mysql_fetch_assoc($selectLocatari)) {
        $locatariArray[$row['loc_id']] = array('procent' => $row['procent'], 'plafon' => $row['plafon']);
    }

    foreach ($locatari as $nrOrdine=>$locatarId) {
        // trebuie sa verific ce "tip incalzire are"
        $tipIncalzire = "SELECT * FROM locatari WHERE loc_id='".$locatarId."'";
        $tipIncalzire = mysql_query($tipIncalzire) or die ("Nu pot selecta setarile locatarilor <br />".mysql_error());

        $can = mysql_result($tipIncalzire, 0, 'nr_rep');
        $tipContor = mysql_result($tipIncalzire, 0, 'tip_incalzire');
        if ($tipContor == 0) {
            $can = 1;
            $um = "pausal";
        }
        if ($tipContor == 1) {
            $um = 'repartitor';
        }

        $dePlataLocInit = $ppu[$nrOrdine] / $can;

        $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '$locatarId', '".$luna."', '".$tipServiciu."', '$can', '$dePlataLocInit', '".$um."', '".$facturaI."', '$pretUnit', '$cantitate')";
        $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala factura de incalzire<br />".mysql_error());

        if ($locatariArray[$locatarId]['procent'] > 0 && $ppu[$nrOrdine]>0) { //are subventie
            $subventie = $ppu[$nrOrdine]*$locatariArray[$locatarId]['procent']/100;
            if ($subventie > $locatariArray[$locatarId]['plafon'])
                $subventie = $locatariArray[$locatarId]['plafon'];
        		Furnizori::insertPlata($factId, $subventie, 'Subventie Incalzire');
            $subventie *= -1;
            $locatarulPlateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$locatarId."', '".$luna."', '".$tipServiciu."', '1', '$subventie', 'subventie', '".$facturaI."', '$pretUnit', '$cantitate')";
            $locatarulPlateste = mysql_query($locatarulPlateste) or die ("Nu pot insera in fisa individuala subventia de incalzire 2<br />".mysql_error());
        }
    }
}
