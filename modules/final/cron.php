<?php

//***************  INSERARE FISIERE NECESARE  ***************//
include_once ('../../componente/config.php');

//***************  STABILIRE CONSUM APA  ***************//
//***************  SETARE INDECSI APOMETRE NECITITE  ***************//

/** 		Verific daca in ziua anterioara
 * 		a fost termenul de introducere a
 * 		citirilor pentru o asociatie.
 * 		In caz afirmativ, in cazul in care
 * 		nu este prima rulare a cronului
 * 		(am deja date introduse in tabela),
 * 		verific daca au introdus toti citirile
 * 		iar daca nu, completez automat in
 * 		functie de setarile asociatiei.
 */
$termenCitire = "SELECT * FROM asociatii_setari a_s, asociatii a WHERE a.asoc_id=a_s.asoc_id";
$termenCitire = mysql_query($termenCitire) or die("#1 -- Nu am putut selecta termenul de predare a citirilor<br />" . mysql_error());

while ($ultimaZi = mysql_fetch_array($termenCitire)) {
    echo "<br /><br /><strong>" . $ultimaZi['asociatie'] . "</strong><br />";
    $termen = $ultimaZi['predare'];
    //$termen = 9;
    $ziuaCurenta = date('d');
    //$ziuaCurenta = 16;
    $lunaCurenta = date('m');
    $anuCurent = date('Y');

    $dataTermen = date('d-m-Y', mktime(0, 0, 0, $lunaCurenta, $termen, $anuCurent));
    echo '<br />Data termen: ' . $dataTermen;
    $dataIeri = date('d-m-Y', mktime(0, 0, 0, $lunaCurenta, $ziuaCurenta - 1, $anuCurent));
    echo '<br />Data ieri: ' . $dataIeri . '<br />';

    if ($termen < 15) {
        $lunaCitire = date('m-Y', mktime(0, 0, 0, $lunaCurenta, 1, $anuCurent));
        echo '<br />(<15) Luna Citire: ' . $lunaCitire;
        $lunaAnt = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 1, 1, $anuCurent));
        echo '<br />(<15) Luna Anterioara: ' . $lunaAnt;
        $acumDouaLuni = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 2, 1, $anuCurent));
        echo '<br />(<15) Acum 2 Luni: ' . $acumDouaLuni;
        $acumTreiLuni = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 3, 1, $anuCurent));
        echo '<br />(<15) Acum 3 Luni: ' . $acumTreiLuni . '<br />';
    } else {
        $lunaCitire = date('m-Y', mktime(0, 0, 0, $lunaCurenta + 1, 1, $anuCurent));
        echo '<br />(>15) Luna Citire: ' . $lunaCitire;
        $lunaAnt = date('m-Y', mktime(0, 0, 0, $lunaCurenta, 1, $anuCurent));
        echo '<br />(>15) Luna Anterioara: ' . $lunaAnt;
        $acumDouaLuni = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 1, 1, $anuCurent));
        echo '<br />(>15) Acum 2 Luni: ' . $acumDouaLuni;
        $acumTreiLuni = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 2, 1, $anuCurent));
        echo '<br />(>15) Acum 3 Luni: ' . $acumTreiLuni . '<br />';
    }

    //daca ieri a fost termenul limita
    //verific daca au dat toti citirile
    //pentru luna anterioara
    if (strtotime($dataIeri) == strtotime($dataTermen)) {
        echo '<br />Data ieri = Data termen<br />';
        //aflu cate luni sunt in tabela apometre
        $nrLuni = "SELECT * FROM apometre WHERE asoc_id=" . $ultimaZi['asoc_id'] . " AND luna<='" . $lunaAnt . "' GROUP BY luna";
        $nrLuni = mysql_query($nrLuni) or die("Nu pot afla cate luni sunt in tabela apometre pentru asociatia curenta<br />" . mysql_error());
        $nrLuni = mysql_num_rows($nrLuni);

        $verificCitiriComplete = "SELECT * FROM apometre WHERE asoc_id=" . $ultimaZi['asoc_id'] . " AND luna='" . $lunaAnt . "' AND completat=0"; //verific daca sunt persoane care nu au declarat apometrele
		$verificCitiriComplete = mysql_query($verificCitiriComplete) or die("#2 -- Nu pot accesa citirile de pe luna anterioara<br />" . mysql_error());

        echo '<br />Numar persoane care nu au declarat apa: ' . mysql_num_rows($verificCitiriComplete);

        if (mysql_num_rows($verificCitiriComplete) != 0) {   //daca sunt persoane care nu au declarat apa
            while ($parcurgLocatari = mysql_fetch_array($verificCitiriComplete)) {
			
				echo $parcurgLocatari['repetari'] != 0 ? "<br />Mai sunt ".$parcurgLocatari['repetari']." repetari pt ".$parcurgLocatari['loc_id']."<br />" : "<br />Locatarul ".$parcurgLocatari['loc_id']." nu mai are repetari disonibile<br />" ;
                //aici trebuie sa verific daca nrLuniRepetariConsum >0
                //in caz afirmativ
                //completam cu consumul de luna trecuta
                //scadem nrLuniRepetariConsum cu 1
                //in caz negativ
                //verificam criteriul de impartire in cazul in care nu declara toti
                if ($parcurgLocatari['repetari'] != 0) {
                    //daca nr de repetari > 0
                    //in cazul in care suntem la a 3-a luna, este in regula, verificam indecsii direct de aici
                    //daca suntem la a 2-a luna va trebui sa verificam si in locatari_apometre
                    $nrRepetari = $parcurgLocatari['repetari'] - 1;   //am scazut 1 si repet consumurile de luna trecuta

                    if ($nrLuni == 1) {  //nu da lumea citirile din prima luna
                        $consumRece = 0;
                        $consumCald = 0;

                        $copiezIndecsi = "SELECT * FROM locatari_apometre WHERE loc_id=" . $parcurgLocatari['loc_id'];
                        $copiezIndecsi = mysql_query($copiezIndecsi) or die("#3 -- Nu pot accesa apometrele initiale<br />" . mysql_error());

                        for ($i = 1; $i < 6; $i++) {
                            $updateApoR = "UPDATE apometre SET r" . $i . "=" . mysql_result($copiezIndecsi, 0, ('r' . $i)) . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            echo '<br />--> SQL: ' . $updateApoR . '<br />';
                            $updateApoR = mysql_query($updateApoR) or die("#4 - Nu pot copia apometrele initiale in apometre -- apa rece<br />" . mysql_error());
                            //$consumRece += mysql_result($copiezIndecsi, 0, ('r'.$i));

                            $updateApoC = "UPDATE apometre SET c" . $i . "=" . mysql_result($copiezIndecsi, 0, ('c' . $i)) . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            echo '<br />--> SQL: ' . $updateApoC . '<br />';
                            $updateApoC = mysql_query($updateApoC) or die("#5 - Nu pot copia apometrele initiale in apometre -- apa calda<br />" . mysql_error());
                            //$consumCald += mysql_result($copiezIndecsi, 0, ('c'.$i));
                        }

                        $consumGen = "UPDATE apometre SET completat=1, consum_rece=" . $consumRece . ", consum_cald=" . $consumCald . ", repetari=" . $nrRepetari . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                        $consumGen = mysql_query($consumGen) or die("#6 - Nu pot updata consumul general<br />" . mysql_error());
                    }

                    if ($nrLuni == 2) {  // daca am doar o luna introdusa in tabela apometre, trebuie sa iau date si din apometre initiale
                        $selectAcumDouaLuni = "SELECT * FROM apometre WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $acumDouaLuni . "' ORDER BY a_id ASC";
                        $selectAcumDouaLuni = mysql_query($selectAcumDouaLuni) or die("Nu pot afla consumurile de acum 2 luni<br />" . mysql_error());

                        $apoInit = "SELECT * FROM locatari_apometre WHERE loc_id=" . $parcurgLocatari['loc_id'];
                        $apoInit = mysql_query($apoInit) or die("#7 - Nu pot accesa apometrele initiale<br />" . mysql_error());

                        for ($i = 1; $i < 6; $i++) {
                            $r = mysql_result($selectAcumDouaLuni, 0, 'r' . $i) - mysql_result($apoInit, 0, 'r' . $i) + mysql_result($selectAcumDouaLuni, 0, 'r' . $i);

                            $updateApo = "UPDATE apometre SET r" . $i . "=" . $r . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            $updateApo = mysql_query($updateApo) or die("#8 - Nu pot salva valoarea<br />" . mysql_error());
                        }
                        $consumR = mysql_result($selectAcumDouaLuni, 0, 'consum_rece');

                        $updateApo = "UPDATE apometre SET completat=1, repetari=" . $nrRepetari . ", consum_rece=" . $consumR . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                        $updateApo = mysql_query($updateApo) or die("#9 - Nu pot inregistra consumul si numarul de repetari ramase<br />" . mysql_error());

                        for ($i = 1; $i < 6; $i++) {
                            $c = mysql_result($selectAcumDouaLuni, 0, 'c' . $i) - mysql_result($apoInit, 0, 'c' . $i) + mysql_result($selectAcumDouaLuni, 0, 'c' . $i);

                            $updateApo = "UPDATE apometre SET c" . $i . "=" . $c . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            $updateApo = mysql_query($updateApo) or die("#10 - Nu pot salva valoarea<br />" . mysql_error());
                        }
                        $consumC = mysql_result($selectAcumDouaLuni, 0, 'consum_cald');

                        $updateApo = "UPDATE apometre SET completat=1, repetari=" . $nrRepetari . ", consum_cald=" . $consumC . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                        $updateApo = mysql_query($updateApo) or die("#11 - Nu pot inregistra consumul si numarul de repetari ramase<br />" . mysql_error());
                    }

                    if ($nrLuni > 2) {  // daca am mai mult de doua luni introduse in tabela apometre, le folosesc pe ultimele doua
                        $selectAcumDouaLuni = "SELECT * FROM apometre WHERE luna='" . $acumDouaLuni . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                        $selectAcumDouaLuni = mysql_query($selectAcumDouaLuni) or die("#12 - Nu pot selecta consumurile de acum doua luni<br />" . mysql_error());

                        $selectAcumTreiLuni = "SELECT * FROM apometre WHERE luna='" . $acumTreiLuni . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                        $selectAcumTreiLuni = mysql_query($selectAcumTreiLuni) or die("#13 - Nu pot selecta consumurile de acum trei luni<br />" . mysql_error());

                        for ($i = 1; $i < 6; $i++) {
                            $r = mysql_result($selectAcumDouaLuni, 0, 'r' . $i) - mysql_result($selectAcumTreiLuni, 0, 'r' . $i) + mysql_result($selectAcumDouaLuni, 0, 'r' . $i);

                            $updateApo = "UPDATE apometre SET r" . $i . "=" . $r . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            $updateApo = mysql_query($updateApo) or die("#14 - Nu pot salva valoarea<br />" . mysql_error());
                        }
                        $consumR = mysql_result($selectAcumDouaLuni, 0, 'consum_rece');

                        $updateApo = "UPDATE apometre SET completat=1, repetari=" . $nrRepetari . ", consum_rece=" . $consumR . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                        $updateApo = mysql_query($updateApo) or die("#15 - Nu pot inregistra consumul si numarul de repetari ramase<br />" . mysql_error());


                        for ($i = 1; $i < 6; $i++) {
                            $c = mysql_result($selectAcumDouaLuni, 0, 'c' . $i) - mysql_result($selectAcumTreiLuni, 0, 'c' . $i) + mysql_result($selectAcumDouaLuni, 0, 'c' . $i);

                            $updateApo = "UPDATE apometre SET c" . $i . "=" . $c . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                            $updateApo = mysql_query($updateApo) or die("#16 - Nu pot salva valoarea<br />" . mysql_error());
                        }
                        $consumC = mysql_result($selectAcumDouaLuni, 0, 'consum_cald');

                        $updateApo = "UPDATE apometre SET completat=1, repetari=" . $nrRepetari . ", consum_cald=" . $consumC . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                        $updateApo = mysql_query($updateApo) or die("#17 - Nu pot inregistra consumul si numarul de repetari ramase<br />" . mysql_error());
                    }
                } 
				else {
                    $setariAsociatie = "SELECT * FROM asociatii_setari WHERE asoc_id=" . $parcurgLocatari['asoc_id'];
                    $setariAsociatie = mysql_query($setariAsociatie) or die("#18 - Nu pot afla setarile asociatiei<br />" . mysql_error());

                    $nrLuniRepetare = mysql_result($setariAsociatie, 0, 'luni');
                    $pausalRece = mysql_result($setariAsociatie, 0, 'pausal_rece');
                    $pausalCald = mysql_result($setariAsociatie, 0, 'pausal_cald');

                    $nrPersAp = "SELECT * FROM locatari WHERE loc_id=" . $parcurgLocatari['loc_id'];
                    $nrPersAp = mysql_query($nrPersAp) or die("#19 - Nu pot afla numarul de locatari al apartamentului curent<br />" . mysql_error());
                    $nrPersAp = mysql_result($nrPersAp, 0, 'nr_pers');

                    echo '<br />Nr Pers Ap Selectat: ' . $nrPersAp;

                    $pausalRece = $pausalRece * $nrPersAp;
                    $pausalCald = $pausalCald * $nrPersAp;
					
                    switch ($ultimaZi['impartire2']) {
                        case 0:  //pausal amenda
                            //trebuie sa introduc si indecsii de luna trecuta
                            if ($nrLuni == 1) {
                                $copiezIndecsi = "SELECT * FROM locatari_apometre WHERE loc_id=" . $parcurgLocatari['loc_id'];
                                $copiezIndecsi = mysql_query($copiezIndecsi) or die("#20 - Nu pot afla Consumul initial<br />" . mysql_error());

                                for ($i = 1; $i < 6; $i++) {
                                    $updateApoR = "UPDATE apometre SET r" . $i . "=" . mysql_result($copiezIndecsi, 0, ('r' . $i)) . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                                    $updateApoR = mysql_query($updateApoR) or die("#21 - Nu pot copia apometrele initiale in apometre -- apa rece -- pausal amenda<br />" . mysql_error());

                                    $updateApoC = "UPDATE apometre SET c" . $i . "=" . mysql_result($copiezIndecsi, 0, ('c' . $i)) . " WHERE luna='" . $lunaAnt . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                                    $updateApoC = mysql_query($updateApoC) or die("#22 - Nu pot copia apometrele initiale in apometre -- apa calda<br />" . mysql_error());
                                }
                            } else {
                                $copiezIndecsi = "SELECT * FROM apometre WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $acumDouaLuni . "' ORDER BY a_id ASC";
                                $copiezIndecsi = mysql_query($copiezIndecsi) or die("#23 - Nu pot afla consumurile de acum 2 luni<br />" . mysql_error());

                                for ($i = 1; $i < 6; $i++) {
                                    $updateApoR = "UPDATE apometre SET r" . $i . "='" . mysql_result($copiezIndecsi, 0, ('r' . $i)) . "' WHERE luna='" . $acumDouaLuni . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                                    $updateApoR = mysql_query($updateApoR) or die("#24 - Nu pot copia apometrele initiale in apometre -- apa rece -- pausal amenda<br />" . mysql_error());

                                    $updateApoC = "UPDATE apometre SET c" . $i . "='" . mysql_result($copiezIndecsi, 0, ('c' . $i)) . "' WHERE luna='" . $acumDouaLuni . "' AND loc_id=" . $parcurgLocatari['loc_id'];
                                    $updateApoC = mysql_query($updateApoC) or die("#25 - Nu pot copia apometrele initiale in apometre -- apa calda<br />" . mysql_error());
                                }
                            }

                            $plateste = "UPDATE apometre SET `pausal`=1, `amenda_rece`='1', `amenda_calda`='1', `completat`='1', `repetari`='$nrLuniRepetare', `consum_rece`='$pausalRece', `consum_cald`='$pausalCald' WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                            $plateste = mysql_query($plateste) or die("#26 - Nu pot insera pausalul<br />" . mysql_error());

                            break;
                        case 1:  //pausal cu modificarea indexului
                            $nrApo = "SELECT * FROM locatari WHERE loc_id=" . $parcurgLocatari['loc_id'];
                            $nrApo = mysql_query($nrApo) or die("#27 - Nu pot afla numarul de apometre pentru fiecare apartament in parte<br />" . mysql_error());

                            $nrApoCald = mysql_result($nrApo, 0, 'ap_calda');
                            $nrApoRece = mysql_result($nrApo, 0, 'ap_rece');

                            //updatez consumul si completat;
                            $plateste = "UPDATE apometre SET `completat`='1', `repetari`='$nrLuniRepetare', `consum_rece`='$pausalRece', `consum_cald`='$pausalCald' WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
                            $plateste = mysql_query($plateste) or die("#28 -- Nu pot insera pausalul<br />" . mysql_error());

                            //calculez consumul pentru fiecare apometru in parte;
                            $diferentaC = 1;
                            $diferentaR = 1;

                            $rPApoCald = $pausalCald % ($nrApoCald == 0 ? 1 : $nrApoCald);
                            $cPApoCald = ($pausalCald - $rPApoCald) / ($nrApoCald == 0 ? 1 : $nrApoCald);

                            $rPApoRece = $pausalRece % ($nrApoRece == 0 ? 1 : $nrApoRece);
                            $cPApoRece = ($pausalRece - $rPApoRece) / ($nrApoRece == 0 ? 1 : $nrApoRece);

                            $c = array();
                            $r = array();
                            //aflu indecsii vechi
                            if ($nrLuni == 1) {
                                $copiezIndecsi = "SELECT * FROM locatari_apometre WHERE loc_id=" . $parcurgLocatari['loc_id'];
                                $copiezIndecsi = mysql_query($copiezIndecsi) or die("#29 - Nu pot afla Consumul Initial<br />" . mysql_error());

                                for ($i = 1; $i <= $nrApoCald; $i++) {
                                    $c[] = mysql_result($copiezIndecsi, 0, 'c' . $i);
                                }

                                for ($i = 1; $i <= $nrApoRece; $i++) {
                                    $r[] = mysql_result($copiezIndecsi, 0, 'r' . $i);
                                }
                            } else {
                                $copiezIndecsi = "SELECT * FROM apometre WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $acumDouaLuni . "' ORDER BY a_id ASC";
                                $copiezIndecsi = mysql_query($copiezIndecsi) or die("#30 - Nu pot afla consumurile de acum 2 luni<br />" . mysql_error());

                                for ($i = 1; $i <= $nrApoCald; $i++) {
                                    $c[] = mysql_result($copiezIndecsi, 0, 'c' . $i);
                                }

                                for ($i = 1; $i <= $nrApoRece; $i++) {
                                    $r[] = mysql_result($copiezIndecsi, 0, 'r' . $i);
                                }
                            }
							if($nrApoCald > 0)
								for ($aC = 1; $aC <= nrApoCald; $aC++) {
									if ($diferentaC == 1) {
										$inserezConsumuri = "UPDATE apometre SET c" . $aC . " = " . ($c[$aC - 1] + $cPApoCald + $rPApoCald) . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
										echo '<br />--> SQL:<br />		- ' . $inserezConsumuri;
										$diferentaC = 0;
									} else {
										$inserezConsumuri = "UPDATE apometre SET c" . $aC . " = " . ($c[$aC - 1] + $cPApoCald) . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
										echo '<br />--> SQL:<br />		- ' . $inserezConsumuri;
									}
									$inserezConsumuri = mysql_query($inserezConsumuri) or die("#31 - Nu pot insera consumul pausal/modificare index pt apa calda<br />" . mysql_error());
								}
							if($nrApoRece > 0)
								for ($aR = 1; $aR <= $nrApoRece; $aR++) {
									if ($diferentaR == 1) {
										$inserezConsumuri = "UPDATE apometre SET r" . $aR . " = " . ($r[$aR - 1] + $cPApoRece + $rPApoRece) . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
										echo '<br />--> SQL:<br />		- ' . $inserezConsumuri;
										$diferentaR = 0;
									} else {
										$inserezConsumuri = "UPDATE apometre SET r" . $aR . " = " . ($r[$aR - 1] + $cPApoRece) . " WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
										echo '<br />--> SQL:<br />		- ' . $inserezConsumuri;
									}

									$inserezConsumuri = mysql_query($inserezConsumuri) or die("#32 - Nu pot insera consumul pausal/modificare index pt apa calda<br />" . mysql_error());
								}

                            $plateste = "UPDATE apometre SET `pausal`=1, `completat`='1', `repetari`='$nrLuniRepetare', `consum_rece`='$pausalRece', `consum_cald`='$pausalCald' WHERE loc_id=" . $parcurgLocatari['loc_id'] . " AND luna='" . $lunaAnt . "'";
							$plateste = mysql_query($plateste) or die("#33 - Nu pot insera pausalul cu modificarea indexului<br />" . mysql_error());
                            break;
                    }
                }
            }
        }
    }
}


//***************  INSERARE CONSUMURI APA LUNA CURENTA  ***************//

/** 		Verific pentru fiecare asociatie data termen
 * 		de predare a citirilor pentru apometre.
 * 		Daca am depasit un termen/este o tabela
 * 		goala, inserez in ea datele pentru asociatie
 * 		cu 0 si pastrez neschimbat doar campul de
 * 		repetare consum
 */
$termenCitire = "SELECT * FROM asociatii_setari";
$termenCitire = mysql_query($termenCitire) or die("#34 - Nu am putut selecta termenul de predare a citirilor<br />" . mysql_error());

$dataAzi = date('d-m-Y');

while ($parcurgAsociatiile = mysql_fetch_array($termenCitire)) {
    $termen = $parcurgAsociatiile['predare'];
    $nrRepetariConsum = $parcurgAsociatiile['luni'];

    //in cazul in care termenul de citire este in primele 14 zile
    //luna pentru care se contorizeaza citirea este luna anterioara
    //in caz contrar este luna curenta
    if ($termen < 15) {
        $lunaCitire = date('m-Y', mktime(0, 0, 0, $lunaCurenta, 1, $anuCurent));
        $lunaAnt = date('m-Y', mktime(0, 0, 0, $lunaCurenta - 1, 1, $anuCurent));
    } else {
        $lunaCitire = date('m-Y', mktime(0, 0, 0, $lunaCurenta + 1, 1, $anuCurent));
        $lunaAnt = date('m-Y', mktime(0, 0, 0, $lunaCurenta, 1, $anuCurent));
    }

    //ziua de citire din luna curenta
    $ziCitire = date('d-m-Y', mktime(0, 0, 0, $lunaCurenta, $termen, $anuCurent));

    $verificDateIntroduse = "SELECT * FROM apometre WHERE asoc_id=" . $parcurgAsociatiile['asoc_id'] . " AND luna='" . $lunaCitire . "'";
    $verificDateIntroduse = mysql_query($verificDateIntroduse) or die("#35 - Nu pot verifica daca au fost introduse date in tabela apometre<br />" . mysql_error());

    //daca inca nu am introdus in listele de apometre
    //datele pentru luna curenta
    if (mysql_num_rows($verificDateIntroduse) == 0) {
        $verificLunaTrecuta = "SELECT * FROM apometre WHERE asoc_id=" . $parcurgAsociatiile['asoc_id'] . " AND luna='" . $lunaAnt . "'";
        $verificLunaTrecuta = mysql_query($verificLunaTrecuta) or die("#36 - Nu pot afla apometrele de luna trecuta<br />" . mysql_error());

        if (mysql_num_rows($verificLunaTrecuta) == 0) { //daca nu este trecut nimeni in lista
            $locatariAsociatie = "SELECT * FROM locatari WHERE asoc_id=" . $parcurgAsociatiile['asoc_id'];
            $locatariAsociatie = mysql_query($locatariAsociatie) or die("#37 - Nu pot selecta locatarii<br />" . mysql_error());

            while ($iiParcurg = mysql_fetch_array($locatariAsociatie)) {
                $primaInserare = "INSERT INTO apometre VALUES (null, '$lunaAnt', '$dataAzi', " . $iiParcurg['loc_id'] . ", " . $iiParcurg['scara_id'] . ", " . $iiParcurg['asoc_id'] . ", 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 0, 0, 0, null, null, 0, 0, '$nrRepetariConsum', 0)";
                $primaInserare = mysql_query($primaInserare) or die("#38 - Nu pot insera locatarii in listele de apometre pentru prima data!<br />" . mysql_error());
            }
        } else {//datele pentru luna trecuta sunt salvate ==>
            //inseram datele pentru luna curenta ==>
            //pastram nr_repetari consum
            $locatariAsociatie = "SELECT * FROM locatari WHERE asoc_id=" . $parcurgAsociatiile['asoc_id'];
            $locatariAsociatie = mysql_query($locatariAsociatie) or die("#39 - Nu pot selecta locatarii<br />" . mysql_error());

            while ($iiParcurg = mysql_fetch_array($locatariAsociatie)) {
                $repetariRamase = "SELECT * FROM apometre WHERE loc_id=" . $iiParcurg['loc_id'] . " ORDER BY a_id DESC";
				$repetariRamase = mysql_query($repetariRamase) or die("#40 - Nu pot afla numarul de repetari ramase<br />" . mysql_error());

                $inserareLunara = "INSERT INTO apometre VALUES (null, '$lunaCitire', '$dataAzi', " . $iiParcurg['loc_id'] . ", " . $iiParcurg['scara_id'] . ", " . $iiParcurg['asoc_id'] . ", 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 0, 0, 0, null, null, 0, 0, " . mysql_result($repetariRamase, 0, 'repetari') . ", 0)";
				$inserareLunara = mysql_query($inserareLunara) or die("#41 - Nu pot reintroduce locatarii in listele de apometre<br />" . mysql_error());
            }
        }
    }
}
?>