<?php
//gazul de imparte la numarul de persoane care beneficiaza de serviciu
$nrPers = "SELECT SUM(L.nr_pers * IFNULL( S.procent, 100 ) / 100 ) total_pers FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id  WHERE L.scara_id=".$scaraId;
$nrPers = mysql_query($nrPers) or die ("Nu pot calcula numarul total de persoane de pe scara<br />".mysql_error());
$nrPers = mysql_result($nrPers, 0, 'total_pers');

$ppu = $cost / $nrPers;	//pretul pentru o persoana;
$ppupers = $cantitate / $nrPers;	//cantitatea consumata de o persoana;
$pret_unitar2 = $cost / $cantitate;	//pretul pentru un M3

if ($nrPers == 0) {
    echo 'Nu sunt persoane pe aceasta scara care sa beneficieze acest serviciu. Va rugam sa verificati factura. <br />Mergi la pagina <a href="http://urbica.ro/app/index.php?link=proceseazaFacturi">anterioara</a>.';
    exit(0);
} else {
    $plataGaz = "SELECT *, (IFNULL( S.procent, 100 )/100) as procent_final, L.loc_id FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id  WHERE scara_id=".$scaraId;
    $plataGaz = mysql_query($plataGaz) or die ("Nu pot selecta locatarii care folosesc gaz<br />".mysql_error());
    
    while ($platesteGaz = mysql_fetch_array($plataGaz)) {
        //inserez in fisa idividuala
        $gaz = $ppu * ($platesteGaz['nr_pers']*$platesteGaz['procent_final']);
        $facturaCurr = $serieFactura.'/'.$numarFactura;

        $plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$platesteGaz['loc_id']."', '$luna', '$tipServiciu', '".round(($platesteGaz['nr_pers']*$platesteGaz['procent_final']), 2)."', '$ppu', 'persoana','$facturaCurr','$pret_unitar2', '$cantitate')";
        $plateste = mysql_query($plateste) or die ("Nu pot insera factura de gaz<br />".mysql_error());
    }
}