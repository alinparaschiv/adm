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
if($_POST['deProcesat'] == "Revert")
{
    $procesare = "SELECT * FROM facturi WHERE procesata=0 AND fact_id=".$_POST['factura'];
    $procesare = mysql_query($procesare) or die ("#Procesare Facturi: 10.1 -- Nu pot selecta factura pentru procesare<br />".mysql_error());

    if (mysql_num_rows($procesare) == 0) {
        //echo "Nu sunt facturi procesare";
        $procesare = "SELECT * FROM facturi WHERE procesata=1 AND fact_id=".$_POST['factura'];
        $procesare = mysql_query($procesare) or die ("#Procesare Facturi: 10.12 -- Nu pot selecta factura pentru deprocesare<br />".mysql_error());

        while ($proc = mysql_fetch_array($procesare)) {
             $update = "UPDATE facturi SET procesata=0 WHERE fact_id=".$proc['fact_id'];
             $update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());

             $delete = 'DELETE FROM `fisa_indiv` WHERE luna="'.$proc['luna'].'" '.(isset($proc['scara_id']) ? 'AND scara_id='.$proc['scara_id'] : '').' AND asoc_id='.$proc['asoc_id'].' AND serviciu='.(isset($proc['subtipFactura']) ? $proc['subtipFactura'] : $proc['tipServiciu']).' AND (`factura`="'.$proc['serieFactura'].'/'.$proc['numarFactura'].'" OR `factura`="'.$proc['serieFactura'].' / '.$proc['numarFactura'].'")';
      			 $delete = mysql_query($delete) or die ("Nu pot sterge inregistrarile din fisa individuala<br />".mysql_error());

              $sql = "SELECT * FROM servicii WHERE serv_id=".$proc['tipServiciu'];
              $sql = mysql_query($sql) or die("Nu pot afla tipul serviciului<br />".mysql_error());


              if (strtolower(mysql_result($sql, 0, 'serviciu')) == "iluminat" ) {
                $delete = 'DELETE FROM `fisa_indiv` WHERE luna="'.$proc['luna'].'" '.(isset($proc['scara_id']) ? 'AND scara_id='.$proc['scara_id'] : '').' AND asoc_id='.$proc['asoc_id'].' AND serviciu IN (84, 90, 91, 92, 93, 107) AND (`factura`="'.$proc['serieFactura'].'/'.$proc['numarFactura'].'" OR `factura`="'.$proc['serieFactura'].' / '.$proc['numarFactura'].'")';
                $delete = mysql_query($delete) or die ("Nu pot sterge inregistrarile din fisa individuala<br />".mysql_error());
              }

            if(substr(strtolower(mysql_result($sql, 0, 'serviciu')),0,14) == "fond reparatii"){
                    
                $lunaUrmatoare = explode("-",$proc['luna']);
                $lunaUrmatoare = mktime(0, 0, 0, $lunaUrmatoare[0], 1, $lunaUrmatoare[1]);
                $lunaUrmatoare = strtotime('+ 1 month', $lunaUrmatoare);
                $lunaUrmatoare = date('m-Y',$lunaUrmatoare);
                
                $updateFRepSql="UPDATE fisa_fonduri F1, fisa_fonduri F2
                            SET F1.fond_rep_rest=F2.fond_rep_rest-F1.fond_rep_incasat
                            WHERE F1.scara_id=".$proc['scara_id']." AND F2.scara_id=".$proc['scara_id']." AND 
                            F1.data='".$lunaUrmatoare."' AND F2.data='".$proc['luna']."' AND 
                            F1.loc_id=F2.loc_id";
         
                $updateFRepQuery = mysql_query($updateFRepSql) or die('Nu am putut deprocesa fondul de reparatie <br />'. mysql_error());
            }
  
            if(substr(strtolower(mysql_result($sql, 0, 'serviciu')),0,12) == "fond rulment"){
                    
                $lunaUrmatoare = explode("-",$proc['luna']);
                $lunaUrmatoare = mktime(0, 0, 0, $lunaUrmatoare[0], 1, $lunaUrmatoare[1]);
                $lunaUrmatoare = strtotime('+ 1 month', $lunaUrmatoare);
                $lunaUrmatoare = date('m-Y',$lunaUrmatoare);
                
                $updateFRepSql="UPDATE fisa_fonduri F1, fisa_fonduri F2
                            SET F1.fond_rul_rest=F2.fond_rul_rest-F1.fond_rul_incasat
                            WHERE F1.scara_id=".$proc['scara_id']." AND F2.scara_id=".$proc['scara_id']." AND 
                            F1.data='".$lunaUrmatoare."' AND F2.data='".$proc['luna']."' AND 
                            F1.loc_id=F2.loc_id";
                       
                $updateFRepQuery = mysql_query($updateFRepSql) or die('Nu am putut deprocesa fondul de reparatie <br />'. mysql_error());
             }
  
            if(substr(strtolower(mysql_result($sql, 0, 'serviciu')),0,12) == "fond special"){
                    
                $lunaUrmatoare = explode("-",$proc['luna']);
                $lunaUrmatoare = mktime(0, 0, 0, $lunaUrmatoare[0], 1, $lunaUrmatoare[1]);
                $lunaUrmatoare = strtotime('+ 1 month', $lunaUrmatoare);
                $lunaUrmatoare = date('m-Y',$lunaUrmatoare);
  
                $updateFRepSql="UPDATE fisa_fonduri F1, fisa_fonduri F2
                            SET F1.fond_spec_rest=F2.fond_spec_rest-F1.fond_spec_incasat
                            WHERE F1.scara_id=".$proc['scara_id']." AND F2.scara_id=".$proc['scara_id']." AND 
                            F1.data='".$lunaUrmatoare."' AND F2.data='".$proc['luna']."' AND 
                            F1.loc_id=F2.loc_id";
   
                $updateFRepQuery = mysql_query($updateFRepSql) or die('Nu am putut deprocesa fondul de reparatie <br />'. mysql_error());
            }

            if (strtolower(mysql_result($sql, 0, 'serviciu')) == "apa calda" || strtolower(mysql_result($sql, 0, 'serviciu')) == "apa rece" ) {
        		   $update = "UPDATE `apometre` SET r1='X', r2='X', r3='X', r4='X', r5='X', c1='X', c2='X', c3='X', c4='X', c5='X', auto=0, pausal=0, diferente=0, `amenda_rece`=null, `amenda_calda`=null, `consum_rece`=0, `consum_cald`=0, `repetari`=0, `completat`=0 WHERE asoc_id=".$proc['asoc_id']." AND luna='".$proc['luna']."' AND auto=1";
        		   $update = mysql_query($update) or die ("Nu pot updata apometrele<br />".mysql_error());

        	   	$facturiAPA_s = 'SELECT * FROM facturi WHERE asoc_id='.$proc['asoc_id'].' AND luna="'.$proc['luna'].'" AND tipServiciu IN (21, 26)';
        	   	$facturiAPA_q = mysql_query($facturiAPA_s) or die('Nu pot afla celelate facturi de apa');

        	   	while ($facturiAPA_r = mysql_fetch_assoc($facturiAPA_q)) {
        	   		$update = "UPDATE facturi SET procesata=0 WHERE fact_id=".$facturiAPA_r['fact_id'];
        	   		$update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());
        	   	}


        	   	$delete = 'DELETE FROM `fisa_indiv` WHERE luna="'.$proc['luna'].'" AND asoc_id='.$proc['asoc_id'].' AND serviciu IN (21, 26, 37, 38, 39, 40, 41)';
        	   	$delete = mysql_query($delete) or die ("Nu pot sterge inregistrarile din fisa individuala<br />".mysql_error());


        		   list($lunaF, $anF) = explode('-', $proc['luna']);
        		   $luna = date('m-Y', mktime(0, 0, 0, $lunaF+1, 1, $anF));

				   $delete = "DELETE FROM `apometre` WHERE asoc_id=".$proc['asoc_id']." AND luna='".$luna."'";
        		   $delete = mysql_query($delete) or die ("Nu pot sterge apometrele de luna viitoare<br />".mysql_error());

			      }
        }
    }
}
else if ($_POST['deProcesat'] == "OK") {
    $procesare = "SELECT F.*, S.mod_impartire, S.unitate  FROM facturi F, servicii S WHERE F.tipServiciu=S.serv_id AND F.procesata=0 AND F.fact_id=".$_POST['factura'];
    $procesare = mysql_query($procesare) or die ("#Procesare Facturi: 10 -- Nu pot selecta factura pentru procesare<br />".mysql_error());

    if (mysql_num_rows($procesare) == 0) {
        echo "Nu sunt facturi neprocesare";
    } else {
    	$dontUpdate = false;
        while ($proc = mysql_fetch_array($procesare)) {
            // Informatii Generale
            $factId = $proc['fact_id'];
            $tipFactura = $proc['tipFactura'];
            $tipServiciu = $proc['tipServiciu'];
            $asocId = $proc['asoc_id'];
            if ($tipFactura != 1) {
                $scaraId = $proc['scara_id'];
            }

            //  Informatii Factura
            $numarFactura = $proc['numarFactura'];
            $serieFactura = $proc['serieFactura'];
            $dataEmitere = $proc['dataEmitere'];
            $dataScadenta = $proc['dataScadenta'];
            $observatii = $proc['observatii'];

            //  Informatii Asociatie
            $debite = $proc['debite'];
            $penalizari = $proc['penalizari'];
            $nrRate = $proc['nrRate'];
            $luna = $proc['luna'];

            //  Facturi cu Indecsi (sau NU)
            $verif = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
            $verif = mysql_query($verif) or die ("Nu pot selecta serviciul ales<br />".mysql_error());

            if (mysql_result($verif, 0, 'cu_indecsi') == 'da') {
                $indexNou = $proc['indexNou'];
                $indexVechi = $proc['indexVechi'];
                $cantitate = $proc['cantitate'];
                $pasant = $proc['pasant'];
                $cost = $proc['cost'];
                //$ppu = $proc['ppu'];
            } else {
                $cantitate = $proc['cantitate'];
                $cost = $proc['cost'];
                //$ppu = $proc['ppu']; 			//apare in servicii
            }

            //  Verific pasante
            $arePasant = "SELECT * FROM scari_setari WHERE asoc_id=".$asocId." AND pasant='da'";
            $arePasant = mysql_query($arePasant) or die ("Nu pot afla scarile cu pasant<br />".mysql_error());

            if (mysql_num_rows($arePasant) != 0) {
                $pasant = $proc['pasant'];
            }

            // Factura pe apartament
            if ($tipFactura == 3) {
                $locatari = $proc['locatari'];
                $cost = $proc['cost'];
            }

            // Factura pe locarari
            if ($tipFactura == 4) {
                $locatari = $proc['locatari'];
                $cost = $proc['cost'];
                $ppu = $proc['ppu'];
            }

            /**
             VERIFIC TIPUL DE FACTURA SI FAC IMPARTIRILE NECESARE
             */

            //verific pentru fiecare factura, de ce tip e
            $sql = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
            $sql = mysql_query($sql) or die("Nu pot afla tipul serviciului<br />".mysql_error());

            $fond = mysql_result($sql, 0, 'fonduri');
            $serviciu = strtolower(mysql_result($sql, 0, 'serviciu'));
            $servici = mysql_result($sql, 0, 'servicii');

            $procesareGenerala = true;

            //daca este fond		--			Trebuie sa le mai inserez in fisa individuala
            if ($fond == "da") {

              $lunaUrmX = explode('-', $luna);
              $lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
              $lunaUrmX = strtotime('+1 month', $lunaUrmX);
              $lunaUrm = date('m-Y', $lunaUrmX);


                $sql1 = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND data='".$lunaUrm."' ORDER BY loc_id ASC";
                $sql1 = mysql_query($sql1) or die ("Nu ma pot conecta la fisa_fonduri<br />".mysql_error());

                //fond rulment		--		in mod egal pe apartamente
                if (strpos($serviciu, "fond rulment") !== false)
                    include('proceseazaFacturiFondRulment.php');


                //fond reparatii	--		pe suprafata SAU AP
                if (strpos($serviciu, "fond reparatii") !== false)
                    include('proceseazaFacturiFondReparatii.php');

                //fonduri speciale		--		fond rulment/reparatii (pot alege apartamentele si tipul de impartire)
                if (strpos($serviciu, "fond special") !== false)
                    include('proceseazaFacturiFondSpecial.php');


                if ($serviciu == "reparatii")
                    include('proceseazaFacturiReparatii.php');

                $procesareGenerala = false;
            	$nrRate = 0;
            }

            //verificam daca este cu indecsi
            if (mysql_result($sql, 0, 'cu_indecsi') == "da") {

                if (strtolower(mysql_result($sql, 0, 'serviciu')) == "gaz")
                    include('proceseazaFacturiGaz.php');

                if (strrpos(strtolower(mysql_result($sql, 0, 'serviciu')),  "iluminat") !== false)
                    include('proceseazaFacturiIluminat.php');

                if (strtolower(mysql_result($sql, 0, 'serviciu')) == "apa calda") {
                    include('modules/final/preprocesareApa.php');
                    if (preprocesareApa($asocId, $luna, $factId))
                        include('proceseazaFacturiApaCalda.php');
                    else {
                            echo '<strong>Factura nu a fost procesata<br /></strong>';
                            $dontUpdate = true;
                    }

                }


                if (strtolower(mysql_result($sql, 0, 'serviciu')) == "apa rece")  {
                    include('modules/final/preprocesareApa.php');
                    if (preprocesareApa($asocId, $luna, $factId))
                        include('proceseazaFacturiApaRece.php');
                	else{
                		echo '<strong>Factura nu a fost procesata<br /></strong>';
                		$dontUpdate = true;
                	}
                }

                if (strtolower(mysql_result($sql, 0, 'serviciu')) == "incalzire")
                    include('proceseazaFacturiIncalzire.php');

                $procesareGenerala=false;
            }

            //tratam cazul general
            if ($procesareGenerala) {
                if( $tipFactura == 4 ) //in cazul in care avem o factura la nivelul de locatar
                {
                    $locatari = explode(',', $proc['locatari']);
                    $ppu = explode(',', $proc['ppu']);
                    $facturaCurr = $serieFactura.'/'.$numarFactura;
                    foreach ($locatari as $key => $value) {
                        $insert = "INSERT INTO fisa_indiv (`id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `serviciu`, `cant_fact_pers`, `pret_unitar`, `um`, `factura`, `pret_unitar2`, `cant_fact_tot`)
                                                VALUES(NULL , '$asocId', '$scaraId', ".$locatari[$key].", '$luna', '$tipServiciu', 1, '$ppu[$key]', 'apartament', '$facturaCurr', '1', '$cost')";
                        mysql_query($insert) or die ("Nu pot insera factura (inpartitaa pe locatari) la locatartul cu loc_id=".$value."<br />".mysql_error());
                    }
                } else {
                    $select = "SELECT ";

                    switch ($proc['mod_impartire']) {
                        case 0: //pe nr apartamente
                            $select .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                            break;
                        case 1: //pe nr persoane
                            $select .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                            break;
                        case 2: //pe suparafata
                            $select .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                            break;
                        case 4: //pe repartitoare
                            $select .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_rep as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                            break;
                        case 7: //pe consum apa rece
                            $select .= "L.loc_id, L.scara_id, L.asoc_id, A.consum_rece as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L INNER JOIN (SELECT * FROM apometre WHERE luna='$luna' AND asoc_id=$asocId) A ON L.loc_id = A.loc_id LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE";
                            break;
                        default:
                            die("Nu se poate procesa o factura cu acest mod de inpartire a facturi");
                    }

                    switch ($tipFactura) {
                        case 1: //asociatie
                            $select .= ' L.asoc_id='.$asocId;
                            break;
                        case 2: //scara
                            $select .= ' L.scara_id='.$scaraId;
                            break;
                        case 3: //apartament
                            $locatari = explode(',', $proc['locatari']);
                            $locatari = implode(', ', $locatari);
                            $select .= ' L.loc_id IN ('.$locatari.')';
                            break;
                        default:
                            die("Nu se poate procesa o factura de pe nivelul ".$tipFactura);
                    }
                    $query = mysql_query($select) or die("A aparut o eroare la citirea informatiilor di BD <br />".$select."<br />".  mysql_error());
                    $info = array();
                    $total = 0;
                    $facturaCurr = $serieFactura.'/'.$numarFactura;
                    while ($row = mysql_fetch_assoc($query)) {
                        $info [] = $row;
                        $total += $row['valoare']*$row['procent']/100;
                    }
                    $ppu = $proc['cost'] / $total;
                    //var_dump($select);
                	//die();
                    foreach ($info as $key => $value) {
                        $insert = "INSERT INTO fisa_indiv (`id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `serviciu`, `cant_fact_pers`, `pret_unitar`, `um`, `factura`, `pret_unitar2`, `cant_fact_tot`)
                                                VALUES(NULL , ".$value['asoc_id'].",".$value['scara_id'].", ".$value['loc_id'].", '$luna', '$tipServiciu', '".($value['valoare']*$value['procent']/100)."', '$ppu', '".$uMasuraArr[$proc['unitate']]."', '$facturaCurr', '$total', '$ppu')";
						mysql_query($insert) or die ("Nu pot insera factura (inpartitaa pe locatari) la locatartul cu loc_id=".$value."<br />".mysql_error());
                    }
                }
            }

        }

        if ($nrRate > 1) {
            $nrRate --;

            $proprietariApFS = $proc['locatari'];

            $lunaUrm = mktime(0, 0, 0, date('m')+1, 1, date('Y'));
            $lunaUrm = date('m-Y', $lunaUrm);

            $azi = date('d-m-Y');

            $scadNrRate = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `nrRate`, `luna`, `cantitate`, `cost`, `locatari`, `observatii`, `procesata`) VALUES (null, '$azi', '$asocId', '$scaraId', '$tipFactura', '$tipServiciu', '$nrRate', '".$lunaUrm."', '$cantitate', '$cost', '$locatari', '$observatii', 0)";
            $scadNrRate = mysql_query($scadNrRate) or die ("Nu pot updata numarul de rate pentru factura curenta<br />".mysql_error());
        }
        if (!$dontUpdate) {
                $update = "UPDATE facturi SET procesata=1 WHERE fact_id=".$factId;
                $update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());
        }
    }
}
unset($_POST);
?>

<?php
function afiseazaFacturi() {
    $lunaCur = date('m-Y');
    $selectFacturi = "SELECT * FROM facturi WHERE STR_TO_DATE(luna, '%m-%Y')<=STR_TO_DATE('".$lunaCur."', '%m-%Y') AND STR_TO_DATE(luna, '%m-%Y')>='".date('Y-m-d', strtotime('- 4 month'))."' ORDER BY fact_id DESC";
    $selectFacturi = mysql_query($selectFacturi) or die ("#Procesare Facturi: 1 -- Nu pot selecta facturile<br />".mysql_error());

    $i = 1;
    if (mysql_num_rows($selectFacturi) > 0) {
        while ($parcurgFacturi = mysql_fetch_array($selectFacturi)) {
            if ($i % 2 == 0) {
                $culoare = "#FFFFFF";
            } else {
                $culoare = "#DDDDDD";
            }
            echo '<form action="" method="post">';
            echo '<tr bgcolor="'.$culoare.'">';
            //camputi ascunse
            echo '<input type="hidden" name="factura" value="'.$parcurgFacturi['fact_id'].'" />';

            //tabel
            echo '<td>'.$i.'</td>';

            //select Asociatie
            $asociatie = "SELECT * FROM asociatii WHERE asoc_id=".$parcurgFacturi['asoc_id'];
            $asociatie = mysql_query($asociatie) or die ("#Procesare Facturi: 2 -- Nu pot afla detalii despre asociatie<br />".mysql_error());

            $numeAsociatie = "Asociatie ".mysql_result($asociatie, 0, 'asociatie');

            //select Scara
            if ($parcurgFacturi['scara_id'] != null) {
                $scara = "SELECT * FROM scari WHERE scara_id=".$parcurgFacturi['scara_id'];
                $scara = mysql_query($scara) or die ("#Procesare Facturi: 3 -- Nu pot afla detalii despre scara<br />".mysql_error());

                $numeScara = " / Blocul ".mysql_result($scara, 0, 'bloc').", scara ".mysql_result($scara, 0, 'scara');
            } else {
                $numeScara = "";
            }
            echo '<td>'.$numeAsociatie.$numeScara.'</td>';

            //pun Serie / Numar
            if ($parcurgFacturi['numarFactura'] != null && $parcurgFacturi['serieFactura'] != null) {
                $serieNumar = $parcurgFacturi['serieFactura'].' / '.$parcurgFacturi['numarFactura'];
            } else {
                $serieNumar = " - ";
            }
            /** */
            if ($parcurgFacturi['observatii'] != null) {
                $doc_s = 'SELECT * FROM doc WHERE id='.$parcurgFacturi['observatii'];
                $doc_q = mysql_query($doc_s) or die('Nu pot afla daca acesta factura are un document atasat');

                if (mysql_num_rows($doc_q) > 0) {
                    $doc_r = mysql_fetch_assoc($doc_q);

                    $serieNumar = '<a href="'.$doc_r['filename'].'">'.$serieNumar.'</a>';
                }                 
            }

            echo '<td>'.$serieNumar.'</td>';

            //aflu serviciul si furnizorul
            $serviciul = "SELECT * FROM servicii WHERE serv_id=".$parcurgFacturi['tipServiciu'];
            $serviciul = mysql_query($serviciul) or die ("#Procesre Facturi: 4 -- Nu pot afla detalii despre serviciu<br />".mysql_error());
            $numeServiciu = mysql_result($serviciul, 0, 'serviciu');

            if ($numeServiciu == 'apa calda') {
                $serviciul = "SELECT * FROM servicii WHERE serv_id=".$parcurgFacturi['subtipFactura'];
                $serviciul = mysql_query($serviciul) or die ("#Procesare Facturi: 4\' -- Nu pot afla detalii despre serviciu<br />".mysql_error());
                $numeServiciu = mysql_result($serviciul, 0, 'serviciu');
            }

            if ($parcurgFacturi['tipFactura'] == 1) {	//facturi pe asociatie
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
            if ($parcurgFacturi['dataEmitere'] != null && $parcurgFacturi['dataScadenta'] != null) {
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
            if ($parcurgFacturi['procesata'] == '1') {
                echo '<td> ';
                //echo '<img src="images/ok.png" width="20px" height="20px" border="0px" />';
                echo ' <input type="hidden" name="deProcesat" value="Revert" /><input type="submit" value="Deproceseaza" /> </td>';
            } else {
                echo '<td> <input type="hidden" name="deProcesat" value="OK" /><input type="submit" value="Proceseaza" /> </td>';
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
