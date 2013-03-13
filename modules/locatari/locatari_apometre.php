<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=locatari_apometre&asoc_id=" + value;
}

function select_scara(value,value2) {
	window.location = "index.php?link=locatari_apometre&asoc_id=" + value + "&scara_id=" + value2;
}

function select_luna(value,value2,value3){
	window.location = "index.php?link=locatari_apometre&asoc_id=" + value + "&scara_id=" + value2 + "&luna=" + value3;
}

function trimiteForm(){
	document.apometre.submit();
	return false;
}
</script>

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
</style>

<?php

/*******************  SELECTEAZA ASOCIATIA SI SCARA SI LUNA  *******************/
$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile<br />".mysql_error());

        if (mysql_num_rows($sql2) == 1)
            $_GET['scara_id'] = mysql_result($sql2, 0, 'scara_id');
	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}

//***************  INSERARE CONSUMURI APA LUNA CURENTA  ***************//

/** 		Verific pentru fiecare asociatie data termen
 * 		de predare a citirilor pentru apometre.
 * 		Daca am depasit un termen/este o tabela
 * 		goala, inserez in ea datele pentru asociatie
 * 		cu 0 si pastrez neschimbat doar campul de
 * 		repetare consum
 */
        /*
$asocId=$_GET['asoc_id'];

$ziuaCurenta = date('d');
$lunaCurenta = date('m');
$anuCurent = date('Y');

$termenCitire = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
$termenCitire = mysql_query($termenCitire) or die("#34 - Nu am putut selecta termenul de predare a citirilor<br />" . mysql_error());

$dataAzi = date('d-m-Y');

while ($parcurgAsociatiile = mysql_fetch_array($termenCitire)) {//o sa se execute numai o singura data
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
}*/
}

if ($_GET['asoc_id']<>null && $_GET['scara_id']<>null){
	if ($_GET['luna'] != null){
		$sql3 = "SELECT * FROM apometre WHERE scara_id=".$_GET['scara_id']." AND luna<>'".$_GET['luna']."' GROUP BY luna ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
	} else {
		$sql3 = "SELECT * FROM apometre WHERE scara_id=".$_GET['scara_id']." GROUP BY luna ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
	}
	$sql3 = mysql_query($sql3) or die ("Nu pot selecta luna<br />".mysql_error());

	while ($row3 = mysql_fetch_array($sql3)){
		$luna .= '<option value="'.$row3[1].'">'.$row3[1].'</option>';
	}
}

/*******************  FUNCTIE CARE PUNE HEADERUL LA TABEL  *******************/
function putHeader($asocId, $scaraId){
	$setariAsociatie = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
	$setariAsociatie = mysql_query($setariAsociatie) or die ("Nu pot selecta ziua de predare a citirilor<br />".mysql_error());

	$termen = mysql_result($setariAsociatie, 0, 'predare');
	$dataAzi = date('d-m-Y');

	$lunaSelectata = $_GET['luna'];
	$lunaSelectata = explode("-", $lunaSelectata);

	if ($termen < 15){
		$startCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0], $termen+1, date('Y')));
		$stopCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0]+1, $termen, date('Y')));
	} else {
		$startCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0]-1, $termen+1, date('Y')));
		$stopCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0], $termen, date('Y')));
	}


	$verificNrApo = "SELECT MAX(ap_rece), MAX(ap_calda) FROM locatari WHERE scara_id=".$scaraId;
	$verificNrApo = mysql_query($verificNrApo) or die ("Nu pot afla numarul maxim de apometre pe care il are un locatar<br />".mysql_error());

	$nrApoR = mysql_result($verificNrApo, 0, 'MAX(ap_rece)');
	$nrApoC = mysql_result($verificNrApo, 0, 'MAX(ap_calda)');

	echo '<tr valign="middle">';
		echo '<td bgcolor="#000" rowspan="2" style="color:#FFF">Etaj</td>';
		echo '<td bgcolor="#000" rowspan="2" style="color:#FFF">Apartament</td>';
		echo '<td bgcolor="#000" rowspan="2" style="color:#FFF">Nume</td>';

		for ($i=0; $i < $nrApoR; $i++){
			$poz = $i;
			echo '<td bgcolor="#000" colspan="2" style="color:#FFF">AR'.($poz+1).'</td>';
		}

		for ($i=0; $i < $nrApoC; $i++){
			$poz = $i;
			echo '<td bgcolor="#000" colspan="2" style="color:#FFF">AC'.($poz+1).'</td>';
		}

		echo '<td bgcolor="#000" colspan="2" style="color:#FFF">Consum</td>';
                //*****************************************************************************************************
                //***              Decomenteaza acest IF ca sa reactivez verificarea datei pt apometre         ********
                //*****************************************************************************************************
		if ((strtotime($startCitire) <= strtotime($dataAzi)) && (strtotime($dataAzi) <= strtotime($stopCitire))){
			echo '<td bgcolor="#000" rowspan="2" style="color:#FFF">Optiuni</td>';
		} else echo '<td bgcolor="#000" rowspan="2" style="color:#F00"><strong>Optiuni</strong></td>';
	echo '</tr>';

	echo '<tr>';
		for ($i = 0; $i<($nrApoR + $nrApoC); $i++){
			echo '<td bgcolor="#000" style="color:#FFF">I. Vechi</td>';
			echo '<td bgcolor="#000" style="color:#FFF">I. Nou</td>';
		}

		echo '<td bgcolor="#000" style="color:#FFF">Apa Rece</td>';
		echo '<td bgcolor="#000" style="color:#FFF">Apa Calda</td>';
	echo '</tr>';
}

/*******************  FUNCTIE CARE PUNE CONTENTUL IN TABEL  *******************/
function putContent($asocId, $scaraId, $luna){
    $total_apa_rece = 0;
    $total_apa_calda = 0;

	$setariAsociatie = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
	$setariAsociatie = mysql_query($setariAsociatie) or die ("Nu pot selecta ziua de predare a citirilor<br />".mysql_error());

	$termen = mysql_result($setariAsociatie, 0, 'predare');
	$dataAzi = date('d-m-Y');

	$lunaSelectata = $_GET['luna'];
	$lunaSelectata = explode("-", $lunaSelectata);

	if ($termen < 15){
		$startCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0], $termen+1, date('Y')));  echo '<br />Start Citire: '.$startCitire;
		$stopCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0]+1, $termen, date('Y')));  echo '<br />Stop Citire: '.$stopCitire;
	} else {
		$startCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0]-1, $termen+1, date('Y')));  echo '<br />Start Citire: '.$startCitire;
		$stopCitire = date('d-m-Y', mktime(0, 0, 0, $lunaSelectata[0], $termen, date('Y'))); 	echo '<br />Stop Citire: '.$stopCitire;
	}

	$verificNrApo = "SELECT MAX(ap_rece), MAX(ap_calda) FROM locatari WHERE scara_id=".$scaraId;
	$verificNrApo = mysql_query($verificNrApo) or die ("Nu pot afla numarul maxim de apometre pe care il are un locatar<br />".mysql_error());

	$nrApoR = mysql_result($verificNrApo, 0, 'MAX(ap_rece)');
	$nrApoC = mysql_result($verificNrApo, 0, 'MAX(ap_calda)');

	$ordine = 0;

	$nrLuni = "SELECT * FROM apometre WHERE asoc_id=".$_GET['asoc_id']." GROUP BY luna";
	$nrLuni = mysql_query($nrLuni) or die ("Nu pot afla numarul de luni din tabela<br />".mysql_error());
	$nrLuni = mysql_num_rows($nrLuni);

	$primaLunaDinTabel = "SELECT luna FROM apometre WHERE asoc_id=".$_GET['asoc_id']." GROUP BY luna ORDER BY STR_TO_DATE(luna, '%m-%Y') ASC";
	$primaLunaDinTabel = mysql_query($primaLunaDinTabel) or die ("Nu pot afla care este prima luna din tabel<br />".mysql_error());
	$primaLunaDinTabel = mysql_result($primaLunaDinTabel, 0, 'luna');

	$punLocatarii = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
	$punLocatarii = mysql_query($punLocatarii) or die ("Nu pot selecta locatarii<br />".mysql_error());

	while ($peRand = mysql_fetch_array($punLocatarii)){

		if ($ordine % 2 == 0){
			$culoare = "#DDDDDD";
			$text = "#000000";
		} else {
			$culoare = "#FFFFFF";
			$text = "#000000";
		}

		//culori diferite in functie de tipul de calculare a indecsilor
		$checkApo = "SELECT * FROM apometre WHERE loc_id=".$peRand['loc_id']." AND luna='".$_GET['luna']."'";
		$checkApo = mysql_query($checkApo) or die ("Nu pot afla detalii despre locatarul curent <br />".mysql_error());

		if (mysql_result($checkApo, 0, 'auto') != 0){
			$culoare = "#CCFF99";
			$text = "#000000";
		}

		if (mysql_result($checkApo, 0, 'pausal') != 0){
			$culoare = "#6666CC";
			$text = "#FFFFFF";
		}

		if ((mysql_result($checkApo, 0, 'amenda_rece') != null) || (mysql_result($checkApo, 0, 'amenda_calda') != null)){
			$culoare = "#FF3366";
			$text = "#FFFFFF";
		}

		echo '<tr bgcolor="'.$culoare.'" style="color:'.$text.'">';
			echo '<td>'.$peRand['etaj'].'</td>';
			echo '<td>'.$peRand['ap'].'</td>';
			echo '<td><a href="index.php?link=locatari&asoc_id='.$peRand['asoc_id'].'&scara_id='.$peRand['scara_id'].'&edit='.$peRand['loc_id'].'" target="_blank">'.$peRand['nume'].'</a></td>';

			$nrOrdine = 0;

			$verifLoc = "SELECT * FROM apometre WHERE loc_id=".$peRand['loc_id']." AND STR_TO_DATE(luna, '%m-%Y')<=STR_TO_DATE('".$_GET['luna']."', '%m-%Y') ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
			$verifLoc = mysql_query($verifLoc) or die ("Nu pot afla detalii despre locatarul curent <br />".mysql_error());

			while ($nrOrdine < $nrApoR){
				if ($nrOrdine < $peRand['ap_rece']){
					$temp = $nrOrdine;

					// asta este pentru Indexul Vechi
					if ($primaLunaDinTabel == $_GET['luna']){
						$apInit = "SELECT * FROM locatari_apometre WHERE loc_id=".$peRand['loc_id'];
						$apInit = mysql_query($apInit) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

						echo '<td>'.mysql_result($apInit, 0, 'r'.($temp+1)).'</td>';
					} else {
						echo '<td>'.mysql_result($verifLoc, 1, 'r'.($temp+1)).'</td>';
					}

                                        //verific indexul nou
                                        $sql = "SELECT r".($temp+1)." as r FROM apometre WHERE loc_id=".$peRand['loc_id']." AND luna='".$_GET['luna']."'";
                                        $sql = mysql_query($sql) or die ("Nu pot afla consumul acestui apometru<br />".mysql_error());

					if (isset($_GET['editeaza']) && ($_GET['editeaza'] == $peRand['loc_id']) ){
						echo '<td>';
						echo '<script type="text/javascript">
								$(document).ready(function(){$(\':text\')[0].focus();});
								</script>';

						echo '<input type="text" name="r'.($temp+1).'" style="width:40px; background-color:#000; color:#FFF;" value="'.mysql_result($sql, 0, 'r').'" /></td>';
					} else {
						if ((strtotime($startCitire) <= strtotime($dataAzi)) && (strtotime($dataAzi) <= strtotime($stopCitire))){
							echo '<td><strong>'.mysql_result($verifLoc, 0, 'r'.($temp+1)).'</strong></td>';
						} else {
							//echo '<td><strong>'.mysql_result($verifLoc, 0, 'r'.($temp+1)).'</strong></td>';

                                                        echo '<td><strong>'.mysql_result($sql, 0, 'r').'</strong></td>';
						}
					}

					$nrOrdine ++;
				} else {
					echo '<td>-</td>';
					echo '<td>-</td>';
					$nrOrdine ++;
				}
			}

			$nrOrdine = 0;
			while ($nrOrdine < $nrApoC){
				if ($nrOrdine < $peRand['ap_calda']){
					$temp = $nrOrdine;

					// asta este pentru Indexul Vechi
					if ($primaLunaDinTabel == $_GET['luna']){
						$apInit = "SELECT * FROM locatari_apometre WHERE loc_id=".$peRand['loc_id'];
						$apInit = mysql_query($apInit) or die ("Nu pot selecta apometrele initiale<br />".mysql_error());

						echo '<td>'.mysql_result($apInit, 0, 'c'.($temp+1)).'</td>';
					} else {
						echo '<td>'.mysql_result($verifLoc, 1, 'c'.($temp+1)).'</td>';
					}

					//verific indexul nou
                                        $sql = "SELECT c".($temp+1)." as c FROM apometre WHERE loc_id=".$peRand['loc_id']." AND luna='".$_GET['luna']."'";
                                        $sql = mysql_query($sql) or die ("Nu pot afla consumul acestui apometru<br />".mysql_error());

					if (isset($_GET['editeaza']) && ($_GET['editeaza'] == $peRand['loc_id']) ){
						echo '<td><input type="text" name="c'.($temp+1).'" style="width:40px; background-color:#000; color:#FFF;" value="'.mysql_result($sql, 0, 'c').'" /></td>';
					} else {
						if ((strtotime($startCitire) <= strtotime($dataAzi)) && (strtotime($dataAzi) <= strtotime($stopCitire))){
							echo '<td><strong>'.mysql_result($verifLoc, 0, 'c'.($temp+1)).'</strong></td>';
						} else {
							//echo '<td><strong>'.mysql_result($verifLoc, 0, 'c'.($temp+1)).'</strong></td>';
                                                        echo '<td><strong>'.mysql_result($sql, 0, 'c').'</strong></td>';
						}
					}

					$nrOrdine ++;
				} else {
					echo '<td>-</td>';
					echo '<td>-</td>';
					$nrOrdine ++;
				}
			}

			//consumurile
			if (mysql_num_rows($verifLoc) == 0) {
				echo '<td>0</td>';
				echo '<td>0</td>';
			} else {
				if ((strtotime($startCitire) <= strtotime($dataAzi)) && (strtotime($dataAzi) <= strtotime($stopCitire))){
					echo '<td>'.mysql_result($verifLoc, 0, 'consum_rece').'</td>';
					echo '<td>'.mysql_result($verifLoc, 0, 'consum_cald').'</td>';

                                        $total_apa_rece += mysql_result($verifLoc, 0, 'consum_rece');
                                        $total_apa_calda += mysql_result($verifLoc, 0, 'consum_cald');
				} else {
                                        $sql = "SELECT consum_rece, consum_cald FROM apometre WHERE loc_id=".$peRand['loc_id']." AND luna='".$_GET['luna']."'";
                                        $sql = mysql_query($sql) or die ("Nu pot afla consumul acestui apometru<br />".mysql_error());
                                        //echo '<td><strong>'.mysql_result($sql, 0, 'r').'</strong></td>';


                                        echo '<td><strong>'.mysql_result($sql, 0, 'consum_rece').'</strong></td>';
					echo '<td><strong>'.mysql_result($sql, 0, 'consum_cald').'</strong></td>';

                                        $total_apa_rece += mysql_result($sql, 0, 'consum_rece');
                                        $total_apa_calda += mysql_result($sql, 0, 'consum_cald');
				}
			}
                //*****************************************************************************************************
                //***              Decomenteaza acest IF ca sa reactivez verificarea datei pt apometre         ********
                //*****************************************************************************************************
			if ((strtotime($startCitire) <= strtotime($dataAzi)) && (strtotime($dataAzi) <= strtotime($stopCitire))){
				if (isset($_GET['editeaza']) && ($_GET['editeaza'] == $peRand['loc_id']) ){
					echo '<input type="hidden" name="salv" value="OK-'.$peRand['loc_id'].'" />';
					echo '<td><a style="cursor:pointer; color:#006EAB" onclick="trimiteForm()">Salveaza</a></td>';
				} else {
					echo '<input type="hidden" name="edit" value="OK" />';
					echo '<td><a href="index.php?link=locatari_apometre&asoc_id='.$asocId.'&scara_id='.$scaraId.'&luna='.$luna.'&editeaza='.$peRand['loc_id'].'">Editeaza</a></td>';
				}
			} //*
	   else {
                            if (isset($_GET['editeaza']) && ($_GET['editeaza'] == $peRand['loc_id']) ){
					echo '<input type="hidden" name="salv" value="OK-'.$peRand['loc_id'].'" />';
					echo '<td><a style="cursor:pointer; color:#F00" onclick="trimiteForm()" href="javascript: trimiteForm()">Salveaza</a></td>';
				} else {
					echo '<input type="hidden" name="edit" value="OK" />';
					if(isset($GLOBALS['locatar']) && $GLOBALS['locatar']+1==$peRand['loc_id']){
						echo '<script type="text/javascript">
								$(document).ready(function(){$(\'.loc'.$peRand['loc_id'].'\')[0].focus();});
								</script>';
					}

					echo '<td><a class="loc'.$peRand['loc_id'].'" style="cursor:pointer; color:#F00" href="index.php?link=locatari_apometre&asoc_id='.$asocId.'&scara_id='.$scaraId.'&luna='.$luna.'&editeaza='.$peRand['loc_id'].'">Editeaza</a></td>';
				}
     }//*/
		echo '</tr>';

		$ordine++;
	}

        if ($ordine % 2 == 0){
                $culoare = "#DDDDDD";
                $text = "#000000";
        } else {
                $culoare = "#FFFFFF";
                $text = "#000000";
        }
        echo '<tr bgcolor="'.$culoare.'" style="color:'.$text.'"><td colspan="3"></td><td colspan="'.(($nrApoR+$nrApoC)*2).'"></td>';
        echo '<td>'.$total_apa_rece.'</td><td>'.$total_apa_calda.'</td>';
        echo '</tr>';
}


/*******************  FAC TOATE VERIFICARILE NECESARE  *******************/
	$apoRece = array("r1", "r2", "r3", "r4", "r5");
	$apoCald = array("c1", "c2", "c3", "c4", "c5");

	if ($_POST['salv'] <> ''){
		$locatarSalvat = explode("-", $_POST['salv']);

		$GLOBALS['locatar'] = $locatarSalvat[1];

		$locatiaSalvarii = "SELECT * FROM apometre WHERE loc_id=".$locatarSalvat[1]." ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
		$locatiaSalvarii = mysql_query($locatiaSalvarii) or die ("Nu pot afla apometrele pentru inserare/update<br />".mysql_error());

		$toateCifre = 0;
		foreach ($_POST as $apometru=>$consum){
			if (in_array($apometru, $apoRece) || in_array($apometru, $apoCald)){
				if ($consum < 0 || !is_numeric($consum)){
					$toateCifre = 1;
				}
			}
		}

		if ($toateCifre == 0){
			foreach ($_POST as $apometru=>$consum){
				if (in_array($apometru, $apoRece) || in_array($apometru, $apoCald)){

					//iau consumurile de luna anterioara sau indicii initiali ai apometrelor
					if (mysql_num_rows($locatiaSalvarii) == 1){
						$consumAnterior = "SELECT * FROM locatari_apometre WHERE loc_id=".$locatarSalvat[1];
						$consumAnterior = mysql_query($consumAnterior) or die ("Nu pot afla consumul din luna anterioara<br />".mysql_error());

						$indexVechi = mysql_result($consumAnterior, 0, $apometru);
					} else {
						$consumAnterior = "SELECT * FROM apometre WHERE loc_id=".$locatarSalvat[1]." ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
						$consumAnterior = mysql_query($consumAnterior) or die ("Nu pot afla consumul din luna anterioara<br />".mysql_error());

						$indexVechi = mysql_result($consumAnterior, 1, $apometru);
					}

					$setezIndecsi = "UPDATE apometre SET ".$apometru."=".$consum.", completat=1 WHERE a_id=".mysql_result($locatiaSalvarii, 0, 'a_id');
					$setezIndecsi = mysql_query($setezIndecsi) or die ("Nu pot updata consumul<br />".mysql_error());

					//calculez si inserez consumul pe luna curenta

					if (mysql_num_rows($locatiaSalvarii) >= 2){
						$consumApa = "SELECT * FROM apometre WHERE loc_id=".$locatarSalvat[1]." ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
						$consumApa = mysql_query($consumApa) or die ("Nu pot selecta apometrele pentru calculul consumului<br />".mysql_error());

						$consumApaRece = mysql_result($consumApa, 0, 'r1') - mysql_result($consumApa, 1, 'r1') + mysql_result($consumApa, 0, 'r2') - mysql_result($consumApa, 1, 'r2') + mysql_result($consumApa, 0, 'r3') - mysql_result($consumApa, 1, 'r3') + mysql_result($consumApa, 0, 'r4') - mysql_result($consumApa, 1, 'r4') + mysql_result($consumApa, 0, 'r5') - mysql_result($consumApa, 1, 'r5');
						$consumApaCalda = mysql_result($consumApa, 0, 'c1') - mysql_result($consumApa, 1, 'c1') + mysql_result($consumApa, 0, 'c2') - mysql_result($consumApa, 1, 'c2') + mysql_result($consumApa, 0, 'c3') - mysql_result($consumApa, 1, 'c3') + mysql_result($consumApa, 0, 'c4') - mysql_result($consumApa, 1, 'c4') + mysql_result($consumApa, 0, 'c5') - mysql_result($consumApa, 1, 'c5');

						$inserezConsumNou = "UPDATE apometre SET consum_rece=".$consumApaRece.", consum_cald=".$consumApaCalda." WHERE a_id=".mysql_result($consumApa, 0, 'a_id');
                                                $inserezConsumNou = mysql_query($inserezConsumNou) or die ("Nu pot insera consumul 1<br />".mysql_error());
					} else {
						$consumApaR = "SELECT (A.r1-LA.r1) AS DIFR1, (A.r2-LA.r2) AS DIFR2, (A.r3-LA.r3) AS DIFR3, (A.r4-LA.r4) AS DIFR4, (A.r5-LA.r5) AS DIFR5
										FROM locatari_apometre LA, apometre A
										WHERE A.loc_id=".$locatarSalvat[1]." AND LA.loc_id=A.loc_id";
						$consumApaR = mysql_query($consumApaR) or die ("Nu pot selecta apometrele pentru calculul consumului - rece<br />".mysql_error());

						$consumApaC = "SELECT (A.c1-LA.c1) AS DIFC1, (A.c2-LA.c2) AS DIFC2, (A.c3-LA.c3) AS DIFC3, (A.c4-LA.c4) AS DIFC4, (A.c5-LA.c5) AS DIFC5
										FROM locatari_apometre LA, apometre A
										WHERE A.loc_id=".$locatarSalvat[1]." AND LA.loc_id=A.loc_id";
						$consumApaC = mysql_query($consumApaC) or die ("Nu pot selecta apometrele pentru calculul consumului - cald<br />".mysql_error());

						$difr1 = mysql_result($consumApaR,0,'DIFR1');
						$difr2 = mysql_result($consumApaR,0,'DIFR2');
						$difr3 = mysql_result($consumApaR,0,'DIFR3');
						$difr4 = mysql_result($consumApaR,0,'DIFR4');
						$difr5 = mysql_result($consumApaR,0,'DIFR5');

						$difc1 = mysql_result($consumApaC,0,'DIFC1');
						$difc2 = mysql_result($consumApaC,0,'DIFC2');
						$difc3 = mysql_result($consumApaC,0,'DIFC3');
						$difc4 = mysql_result($consumApaC,0,'DIFC4');
						$difc5 = mysql_result($consumApaC,0,'DIFC5');

						$consumApaRece = $difr1 + $difr2 + $difr3 + $difr4 + $difr5;
						$consumApaCalda = $difc1 + $difc2 + $difc3 + $difc4 + $difc5;

						$inserezConsumNou = "UPDATE apometre SET consum_rece=".$consumApaRece.", consum_cald=".$consumApaCalda." WHERE loc_id=".$locatarSalvat[1];
						$inserezConsumNou = mysql_query($inserezConsumNou) or die ("Nu pot insera consumul 2<br />".mysql_error());
					}
				}
			}
		} else {
			echo '<font style="color:red; font-size:16px">Citirile trebuie sa fie formate numai din numere. Pentru citirile cu zecimale folositi "." (Ex: 9.1)</font>';
		}
	}
	unset($_POST);
?>

<div id="content" style="float:left;">
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select onChange="select_asoc(this.value)">
				<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else
					{
						$afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
						$afisAsoc = mysql_query($afisAsoc) or die ("Nu pot selecta asociatiile<br />".mysql_error());

						echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
					}
				?>
		        	<?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
		<?php if($_GET['asoc_id']<>null):?>
	<tr>
		<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
		<td align="left" bgcolor="#CCCCCC">
        		<select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
				<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>'; }  else
					{
						$afisScara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
						$afisScara = mysql_query($afisScara) or die ("Nu pot selecta scara<br />".mysql_error());

						echo '<option value="">Bloc '.mysql_result($afisScara, 0, 'bloc').', scara '.mysql_result($afisScara, 0, 'scara').'</option>';
					}
				?>
				<?php  echo $scari; ?>
			</select>
		</td>
	</tr>
		<?php endif;?>

       	<?php if($_GET['asoc_id']<>null && $_GET['scara_id']<>null):?>
	<tr>
		<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti luna:</td>
		<td align="left" bgcolor="#CCCCCC">
        		<select onChange="select_luna(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
				<?php if($_GET['luna']==null) { echo '<option value="">----Alege----</option>'; } else
					{
						echo '<option value="">'.$_GET['luna'].'</option>';
					}
					echo $luna;
				?>
			</select>
		</td>
	</tr>
		<?php endif;?>
</table>

</div>

<?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['luna']<>null)):?>
<style type="text/css">
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
</style>
<?php 	$lunaPDF = explode('-', $_GET['luna']);
		$lunaPDF = date('m-Y', strtotime('- 1 month', strtotime($lunaPDF[1].'-'.$lunaPDF[0].'-01')));
	?>
  	<a class="pdf1" style="border:none;" target="_blank" href="modules/pdf/pdf.php?<?php echo 'afisare=ok&AP=0&luna='.$lunaPDF.'&asoc_id='.$_GET['asoc_id'].'&scara_id='.$_GET['scara_id'] ;?>"></a>

<form id="apometre" name="apometre" method="post" action=<?php echo "index.php?link=locatari_apometre&asoc_id=".$_GET['asoc_id']."&scara_id=".$_GET['scara_id']."&luna=".$_GET['luna']; ?> >
	<input type="hidden" name="potAdauga" value="OK" />
    <table width="1000" style="float:left;  margin-top:10px; background-color:#BBB;">
    	<thead>
        	<?php putHeader($_GET['asoc_id'], $_GET['scara_id']); ?>
        </thead>

        	<?php putContent($_GET['asoc_id'], $_GET['scara_id'], $_GET['luna']); ?>

		<?php //citescApometre($_GET['asoc_id'], $_GET['scara_id']) ?>
    </table>
</form>
<?php endif; ?>
