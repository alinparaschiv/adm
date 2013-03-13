<?php

/*
 * Aceasta functie intorce numarul de luni in care sunt contorizare apometrele BD
 */
function aflaLuniApometre($asocId, $locId = null, $luna = null){
	$valSQL = 'SELECT 1 FROM `apometre` WHERE asoc_id='.$asocId;
	if ($locId != null) {
		$valSQL .= ' AND loc_id='.$locId;
	}
	$valSQL .=' GROUP BY luna';
	if ($luna != null) {
		$valSQL .= ' HAVING luna="'.$luna.'"';
	}
	$val = mysql_query($valSQL) or die("#0.1 -- Nu am putut cate luni s-au declart apometre<br />".$valSQL."<br />" . mysql_error());

	return mysql_num_rows($val);
}

function indexInitial($persId){
	$valSQL = 'SELECT * FROM locatari_apometre WHERE loc_id='.$persId;
	$val = mysql_query($valSQL) or die("#0.2 -- Nu am putut afla indexsi initiali<br />".$valSQL."<br />" . mysql_error());

	return mysql_fetch_array($val);
}

function apTrecute($locId, $lunaFactura, $nrLuni){

	list($lunaF, $anF) = explode('-', $lunaFactura);
	$luna = date('m-Y', mktime(0, 0, 0, $lunaF-$nrLuni, 1, $anF));

	$valSQL = 'SELECT * FROM apometre WHERE loc_id='.$locId.' AND luna="'.$luna.'"';
	$val = mysql_query($valSQL) or die("#0.3 -- Nu am putut gasii apometrul declarat acuma $nrLuni luni pentru locatarul cu id-ul $locId<br />$valSQL<br />" . mysql_error());

	return mysql_fetch_array($val);
}

function setApometruIndex($locId, $luna, $apometru, $valoare){
	$updateAp = "UPDATE apometre SET $apometru='$valoare' WHERE luna='$luna' AND loc_id=$locId";
	$updateAp = mysql_query($updateAp) or die ("Nu pot modifica indexul apometrului $apometru (val: $valoare) pt locID: $locId / Luna: $luna <br />".mysql_error());
}

function setApometruInfo($locId, $lunaCurenta, $totalRece, $totalCald, $repetari, $pausal = false, $amenda = false){
	$consumGen = "UPDATE apometre SET completat=1, auto=1,";
	if ($pausal) {
		$consumGen .=" pausal=1,";
	}
	if ($amenda) {
		$consumGen .=" amenda_calda=1, amenda_rece=1, diferente=1, ";
	}
	$consumGen .=" consum_rece=$totalRece, consum_cald=$totalCald, repetari=$repetari WHERE luna='$lunaCurenta' AND loc_id=$locId";
        $consumGen = mysql_query($consumGen) or die ("Nu pot updata consumul general<br />".mysql_error());
}

function insertInregistrareNouaApometre($lunaCurenta, $apometru, $repetari){
	list($lunaF, $anF) = explode('-', $lunaCurenta);
	$luna = date('m-Y', mktime(0, 0, 0, $lunaF+1, 1, $anF));

	$exist_s = "SELECT * FROM apometre WHERE loc_id=".$apometru['loc_id']." AND luna='".$luna."'";
	$exist_q = mysql_query($exist_s) or die('Nu pot afla daca are deja introdus apometrul pt luna urmatoare');

	$insertSQL = "INSERT INTO apometre VALUES (null, '$luna', '".date('d-m-Y')."', ".$apometru['loc_id'].", ".$apometru['scara_id'].", ".$apometru['asoc_id'].", 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 0, 0, 0, null, null, 0, 0, ".$repetari.", 0)";

	if(mysql_num_rows($exist_q) == 0)
		mysql_query($insertSQL)or die ("Nu pot introduce o noua inregistrare in apometre<br />$insertSQL<br />".mysql_error());
}

function getLocatar($locId, $select="*"){
	$locatarSQL = "SELECT $select FROM locatari WHERE loc_id=$locId";
	$locatar = mysql_query($locatarSQL) or die ("Nu pot afla informatii despre locatarul cu id-ul $locId<br />$locatarSQL<br />".mysql_error());
	return mysql_fetch_array($locatar);
}

function preprocesareApa($asocId, $luna, $factId){
	$nrLuniApDeclarate = aflaLuniApometre($asocId);

	$setariAsociatieSQL = 'SELECT * FROM asociatii_setari WHERE asoc_id='.$asocId;
	$setariAsociatie = mysql_query($setariAsociatieSQL) or die("#2 -- Nu am putut afla setarile asociatiei<br />".$setariAsociatieSQL."<br />" . mysql_error());
	$setariAsociatie = mysql_fetch_array($setariAsociatie);

	list($lunaF, $anF) = explode('-', $luna);
	$dataFactura = (((int)$setariAsociatie['predare']) > 15) ? mktime(0, 0, 0, $lunaF, $setariAsociatie['predare'], $anF) : mktime(0, 0, 0, $lunaF+1, $setariAsociatie['predare'], $anF) ;
	if ($dataFactura > mktime(0, 0, 0, date('m'), date('d'), date('Y'))) {
            echo "Dupa data de ".date('d-m-Y',$dataFactura)." se poate procesa aceasta factura (trebuire sa permitem locatarilor sa declare apometrele). Data curenta: ".date('d-m-Y', mktime(0, 0, 0, date('m'), date('d'), date('Y')))."<br />";
            return false;
        }


	//verific daca luna nu a fost procesata deja sau nu au declarat toti;
	$apometreNedeclarateSQL = 'SELECT * FROM apometre WHERE luna="'.$luna.'" AND asoc_id="'.$asocId.'" AND completat=0';
	$apometreNedeclarate = mysql_query($apometreNedeclarateSQL) or die("#1 -- Nu am putut afla daca a fost procestat aceasta luna<br />".$apometreNedeclarateSQL."<br />" . mysql_error());
	if (mysql_num_rows($apometreNedeclarate) == 0 && aflaLuniApometre($asocId) != 0) {
            //inserez luna urmatoare pt toate apometrele
            $toateApometreleSQL = 'SELECT * FROM apometre WHERE luna="'.$luna.'" AND asoc_id='.$asocId;
            $toateApometrele = mysql_query($toateApometreleSQL) or die("#0 -- Nu am putut afla apometrele corespunzatoare acestei facturi<br />".$toateApometreleSQL."<br />" . mysql_error());
            while($apometru = mysql_fetch_array($toateApometrele))
		insertInregistrareNouaApometre($luna, $apometru, $setariAsociatie['luni']);
            return true; //daca am 0 inregistrari necompletate pot sa ies din functie si sa procesez factura
        }

	while($apometru = mysql_fetch_array($apometreNedeclarate)){
	//pentru fiecare apometru care nu a fost completat, se completeaza automat
		$nrLuniApDeclarate = aflaLuniApometre($asocId, $apometru['loc_id']);

		$indexInitial = ($nrLuniApDeclarate <= 1) ? indexInitial($apometru['loc_id']) : null;
		$apometruLunaTrecuta = ($nrLuniApDeclarate > 1) ? apTrecute($apometru['loc_id'], $luna, 1) : null;
		$apometruAcuDouaLuni = ($nrLuniApDeclarate > 2) ? apTrecute($apometru['loc_id'], $luna, 2) : null;

		if ($apometruLunaTrecuta == null || (int)$apometruLunaTrecuta['repetari'] != 0) {

			$loc = getLocatar($apometru['loc_id']);
			if ($loc['nr_pers'] == 0) {
				switch($nrLuniApDeclarate){
					case 1: //nu a declarat nici o data apometrele
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", $indexInitial['r'.$i]);
							setApometruIndex($apometru['loc_id'], $luna, "c$i", $indexInitial['c'.$i]);
						}
						setApometruInfo($apometru['loc_id'], $luna, 0, 0, ((int)$setariAsociatie['luni']-1));
						break;
					case 2: //a mai declarat o data
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", ((int)$apometruLunaTrecuta['r'.$i]));
							setApometruIndex($apometru['loc_id'], $luna, "c$i", ((int)$apometruLunaTrecuta['r'.$i]));
						}
						setApometruInfo($apometru['loc_id'], $luna, 0, 0, ((int)$apometruLunaTrecuta['repetari']-1));
						break;
					default: //sunt cel putin 2 inregistrari in tabela
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", ((int)$apometruLunaTrecuta['r'.$i]));
							setApometruIndex($apometru['loc_id'], $luna, "c$i", ((int)$apometruLunaTrecuta['c'.$i]));
						}
						setApometruInfo($apometru['loc_id'], $luna, 0, 0, ((int)$apometruLunaTrecuta['repetari']-1));
				} // switch
			}
			else {
				switch($nrLuniApDeclarate){
					case 1: //nu a declarat nici o data apometrele
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", $indexInitial['r'.$i]);
							setApometruIndex($apometru['loc_id'], $luna, "c$i", $indexInitial['c'.$i]);
						}
						setApometruInfo($apometru['loc_id'], $luna, 0, 0, ((int)$setariAsociatie['luni']-1));
						break;
					case 2: //a mai declarat o data
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", (2*(int)$apometruLunaTrecuta['r'.$i] - (int)$indexInitial['r'.$i]));
							setApometruIndex($apometru['loc_id'], $luna, "c$i", (2*(int)$apometruLunaTrecuta['r'.$i] - (int)$indexInitial['r'.$i]));
						}
						setApometruInfo($apometru['loc_id'], $luna, $apometruLunaTrecuta['consum_rece'], $apometruLunaTrecuta['consum_cald'], ((int)$apometruLunaTrecuta['repetari']-1));
						break;
					default: //sunt cel putin 2 inregistrari in tabela
						for ($i=1; $i<6; $i++){
							setApometruIndex($apometru['loc_id'], $luna, "r$i", (2*(int)$apometruLunaTrecuta['r'.$i] - (int)$apometruAcuDouaLuni['r'.$i]));
							setApometruIndex($apometru['loc_id'], $luna, "c$i", (2*(int)$apometruLunaTrecuta['c'.$i] - (int)$apometruAcuDouaLuni['c'.$i]));
						}
						setApometruInfo($apometru['loc_id'], $luna, $apometruLunaTrecuta['consum_rece'], $apometruLunaTrecuta['consum_cald'], ((int)$apometruLunaTrecuta['repetari']-1));
				} // switch
			}
		}
		else {
			$locatar = getLocatar($apometru['loc_id']);
			$indecsiVechi = ($nrLuniApDeclarate > 1) ? $apometruLunaTrecuta : $indexInitial;
			$pausalRece = $setariAsociatie['pausal_rece'] * $locatar['nr_pers'] * ($locatar['ap_rece'] > 0 ? 1 : 0);
			$pausalCald = $setariAsociatie['pausal_cald'] * $locatar['nr_pers'] * ($locatar['ap_calda'] > 0 ? 1 : 0);

			switch($setariAsociatie['impartire2']){
				case 0: //pausal amenda
					for ($i=1; $i<6; $i++){
						setApometruIndex($apometru['loc_id'], $luna, "r$i", $indecsiVechi['r'.$i]);
						setApometruIndex($apometru['loc_id'], $luna, "c$i", $indecsiVechi['c'.$i]);
					}
					setApometruInfo($apometru['loc_id'], $luna, $pausalRece, $pausalCald, 0, true, true);
					break;
				case 1: //pausal cu modificarea indexului

					if ($locatar['ap_rece'] != 0) { //daca are apa calda
						$rPApoRece = $pausalRece % $locatar['ap_rece'];
						$cPApoRece = ($pausalRece - $rPApoRece) / $locatar['ap_rece'];
						setApometruIndex($apometru['loc_id'], $luna, "r1", ($indecsiVechi['r1'] + $cPApoRece + $rPApoRece));
						for ($i=2; $i<=$locatar['ap_rece']; $i++)
							setApometruIndex($apometru['loc_id'], $luna, ("r".$i), ($indecsiVechi['r'.$i] + $cPApoRece));
					}
					else $pausalRece = 0;

					if ($locatar['ap_calda'] != 0) { //daca are apa calda
						$rPApoCald = $pausalCald % $locatar['ap_calda'];
						$cPApoCald = ($pausalCald - $rPApoCald) / $locatar['ap_calda'];
						setApometruIndex($apometru['loc_id'], $luna, "c1", ($indecsiVechi['c1'] + $cPApoCald + $rPApoCald));
						for ($i=2; $i<=$locatar['ap_calda']; $i++)
							setApometruIndex($apometru['loc_id'], $luna, ("c".$i), ($indecsiVechi['c'.$i] + $cPApoCald));
					}
					else $pausalCald = 0;

					setApometruInfo($apometru['loc_id'], $luna, $pausalRece, $pausalCald, 0, true);
					break;
				default:
			} // switch
		}
		//insertInregistrareNouaApometre($luna, $apometru, $setariAsociatie['luni']);
	}
       	//inserez luna urmatoare pt toate apometrele
	$toateApometreleSQL = 'SELECT * FROM apometre WHERE luna="'.$luna.'" AND asoc_id='.$asocId;
	$toateApometrele = mysql_query($toateApometreleSQL) or die("#0 -- Nu am putut afla apometrele corespunzatoare acestei facturi<br />".$toateApometreleSQL."<br />" . mysql_error());
	while($apometru = mysql_fetch_array($toateApometrele))
		insertInregistrareNouaApometre($luna, $apometru, $setariAsociatie['luni']);

	return true;
}

function getOLDNumeContor($idContor, $luna=null) {
	//luna trecuta indecsi trebuie sa fie aceeasi
	if (!$luna) 
		die('Momentan nu functioneaza functia daca nu este dat parametru luna curenta');

	$luna = explode('-', $luna);
	$luna = strtotime($luna[1].'-'.$luna[0].'-1');
	$luna = strtotime('-1 month', $luna);
	$luna = date('m-Y', $luna);

	$ap_new_s = "SELECT L.*,
					ap.id as id_apartament,
					apo.*,
					c.*, d.*, i.*
				FROM locatari L
				JOIN id_locatar_proprietar lp ON L.loc_id=lp.id_locatar
				JOIN adm_proprietar p ON lp.id_proprietar=p.id
				JOIN adm_apartament ap ON (p.id=ap.id_proprietar AND ap.numar=L.ap)
				 
				JOIN apometre apo ON L.loc_id=apo.loc_id

				 LEFT JOIN adm_declarare_ca d ON (d.id_apartament=ap.id AND d.luna=apo.luna)
				 LEFT JOIN adm_continut_dca i ON d.id=i.id_declarare
				 LEFT JOIN adm_contor c ON i.id_contor=c.id

				 
				 WHERE apo.luna = '$luna'
				 AND c.id=$idContor";

	$ap_new_q = mysql_query($ap_new_s) or die('Nu pot sa aflu numele contorului <br />'.mysql_error().'<br />'.$ap_new_s);

	if(mysql_num_rows($ap_new_q) != 1)
		die('Nu pot sa aflu numele contorului - nr inregistrari inorect <br />'.$ap_new_s);

	$ap_new_r = mysql_fetch_assoc($ap_new_q);

	$rez = array();

	if($ap_new_r['tip'] == 'apa rece') {
		for ($i=1; $i<=$ap_new_r['ap_rece']; $i++) { 
			if($ap_new_r['r'.$i] == $ap_new_r['index'])
				$rez []= 'r'.$i;
		}
	}

	if($ap_new_r['tip'] == 'apa calda') {
		for ($i=1; $i<=$ap_new_r['ap_calda']; $i++) { 
			if($ap_new_r['c'.$i] == $ap_new_r['index'])
				$rez []= 'c'.$i;
		}
	}

	if(count($rez) == 1)
		return $rez[0];

	if(count($rez) == 0)
		die('nu stiu ce nume are apometu curent <br />'.$ap_new_s);

	//daca am mai multi indexsi la fel incerc sa vad luna trecuta care au fost la fel
	//functia se apeleaza recursiv
	return getOLDNumeContor($idContor, $luna);
}

function transferApometre ($asocId, $luna, $debug=false) {
	$l_s = "SELECT *, ap.id as id_apartament
			FROM locatari L
			JOIN asociatii A ON L.asoc_id=A.asoc_id
			JOIN scari S ON L.scara_id=S.scara_id
			JOIN adm_artera art ON S.strada=art.id
			JOIN id_locatar_proprietar lp ON L.loc_id=lp.id_locatar
			JOIN adm_proprietar p ON lp.id_proprietar=p.id
			JOIN adm_apartament ap ON (p.id=ap.id_proprietar AND ap.numar=L.ap)
			JOIN adm_scara sc ON (ap.id_scara=sc.id AND sc.denumire=S.scara)";
	if($asocId != null)
		$l_s .= "WHERE L.asoc_id = $asocId";

	$l_q = mysql_query($l_s) or die(mysql_error().'<br />'.$l_s);

	while ($l_r = mysql_fetch_assoc($l_q)) {
		$ap_old_s = "SELECT * 
					 FROM apometre 
					 WHERE loc_id=".$l_r['loc_id']."
					 AND luna='$luna'
					 LIMIT 1";
		$ap_old_q = mysql_query($ap_old_s) or die(mysql_error().'<br />'.$ap_old_s);
if($debug) var_dump($l_r); echo '<br /><br /><br /><br />';
		if (mysql_num_rows($ap_old_q) == 0) 
			die('Nu exista inregistrarea in tabela apometre. Trebuie procesata factura de luna trecuta');
		
		$ap_old_r = mysql_fetch_assoc($ap_old_q);

		$ap_new_s = "SELECT *
					 FROM adm_declarare_ca d 
					 JOIN adm_continut_dca i ON d.id=i.id_declarare
					 JOIN adm_contor c ON i.id_contor=c.id
					 WHERE d.luna='$luna'
					 AND d.id_apartament=".$l_r['id_apartament'];
		$ap_new_q = mysql_query($ap_new_s) or die(mysql_error().'<br />'.$ap_new_s);
if($debug) var_dump(mysql_num_rows($ap_new_q)); echo '<br /><br />';

		$ap_old_u = '';
		//--------------------------------------------------
		//---------------------  FAZA 1  -------------------
		//-------------------  NEW -> OLD  -----------------
		//--------------------------------------------------
if($debug) var_dump($ap_old_r); echo '<br /><br />';		
		//daca avem inregistrari in noua tabela
		//si nu e completata vechea tabela
		if (mysql_num_rows($ap_new_q) > 0 && $ap_old_r['completat'] == 0) {
			$consum = array('r' => 0, 'c' => 0);
			while ( $ap_new_r = mysql_fetch_assoc($ap_new_q) ) {
				if ($ap_new_r['tip'] == 'apa rece') 
					$consum['r'] += $ap_new_r['consum'];
				if ($ap_new_r['tip'] == 'apa calda') 
					$consum['c'] += $ap_new_r['consum'];

				$ap_old_u .= "UPDATE apometre 
							  SET ".getOLDNumeContor($ap_new_r['id_contor'], $luna)."=".$ap_new_r['index'] ." 
							  WHERE loc_id=".$l_r['loc_id']." 
							  AND luna='$luna'; ";

				$tipDeclarare = $ap_new_r['tip_declarare'];
			}

			if ($consum['r'] > 0) 
				$ap_old_u .= "UPDATE apometre 
							  SET consum_rece=".$consum['r']." 
							  WHERE loc_id=".$l_r['loc_id']." 
							  AND luna='$luna'; ";

			if ($consum['c'] > 0) 
				$ap_old_u .= "UPDATE apometre 
							  SET consum_cald=".$consum['c']." 
							  WHERE loc_id=".$l_r['loc_id']." 
							  AND luna='$luna'; ";
			
			switch ($tipDeclarare) {
				case 'automat operator':
					$ap_old_u .= "UPDATE apometre 
								  SET completat=1, auto=1
								  WHERE loc_id=".$l_r['loc_id']." 
								  AND luna='$luna'; ";
					break;
				
				default:
					$ap_old_u .= "UPDATE apometre 
								  SET completat=1
								  WHERE loc_id=".$l_r['loc_id']." 
								  AND luna='$luna'; ";
					break;
			}

			
		}

		if($ap_old_u != '')
			$ap_old_uq = mysql_query($ap_old_u) or die(mysql_error().'<br />'.$ap_old_u);
if($debug) var_dump($ap_old_u); echo '<br /><br />';



		//--------------------------------------------------
		//---------------------  FAZA 2  -------------------
		//-------------------  OLD -> NEW  -----------------
		//--------------------------------------------------
		if (mysql_num_rows($ap_new_q) == 0 && $ap_old_r['completat'] == 1) {

			$lunaTrecuta = explode('-', $luna);
			$lunaTrecuta = strtotime($lunaTrecuta[1].'-'.$lunaTrecuta[0].'-1');
			$lunaTrecuta = strtotime('-1 month', $lunaTrecuta);
			$lunaTrecuta = date('m-Y', $lunaTrecuta);

			$ap_old_tr_s = "SELECT * 
						 FROM apometre 
						 WHERE loc_id=".$l_r['loc_id']."
						 AND luna='$lunaTrecuta'
						 LIMIT 1";
			$ap_old_tr_q = mysql_query($ap_old_tr_s) or die(mysql_error().'<br />'.$ap_old_tr_s);

			if (mysql_num_rows($ap_old_tr_q) == 0) 
				die('Nu exista inregistrarea in tabela apometre. Trebuie procesata factura de luna trecuta');
		
			$ap_old_tr_r = mysql_fetch_assoc($ap_old_tr_q);

			$ap_new_tr_s = "SELECT *
						 FROM adm_declarare_ca d 
						 JOIN adm_continut_dca i ON d.id=i.id_declarare
						 JOIN adm_contor c ON i.id_contor=c.id
						 WHERE d.luna='$lunaTrecuta'
						 AND d.id_apartament=".$l_r['id_apartament'];
			$ap_new_tr_q = mysql_query($ap_new_tr_s) or die(mysql_error().'<br />'.$ap_new_tr_s);

			if (mysql_num_rows($ap_new_tr_q) < 1) 
				die('Luna trecuta trebuiau sa aiba apometre declarate in BD noua <br />'.$ap_new_tr_s);
			
			$contoare = array();

			while ($ap_new_tr_r = mysql_fetch_assoc($ap_new_tr_q))
				$contoare[getOLDNumeContor($ap_new_tr_r['id_contor'], $luna)] = $ap_new_tr_r['id_contor'];
if($debug) var_dump($contoare); echo '<br /><br />';
			
			$dataDeclarare = explode('-', $ap_old_r['luna']);
			$dataDeclarare = $dataDeclarare[1].'-'.$dataDeclarare[0].'-01';

			if($ap_old_r['auto'] == 0)
				$tipDeclarare = 'operator borderou';
			else if($ap_old_r['auto'] == 1 && ($ap_old_r['amenda_rece'] != null || $ap_old_r['amenda_calda'] != null))
				$tipDeclarare = 'automat amenda';
			else if ($ap_old_r['auto'] == 1 && $ap_old_r['pausal'] == 1) 
				$tipDeclarare = 'automat pausal';
			else if ($ap_old_r['auto'] == 1 && $ap_old_tr_r != null && ($ap_old_tr_r['consum_rece'] == $ap_old_r['consum_rece'])) 
				$tipDeclarare = 'automat repetare';
			else
				$tipDeclarare = 'automat operator';

			$dec_i = "INSERT INTO adm_declarare_ca (`id`, `data_timp`, `tip_declarare`, `id_operator`, `id_apartament`, `luna`) 
						  VALUES (NULL, '$dataDeclarare', '$tipDeclarare', '23', '".$l_r['id_apartament']."', '".$ap_old_r['luna']."');";
			$dec_q = mysql_query($dec_i) or die(mysql_error().'<br />'.$dec_i);
			$dec_r = mysql_insert_id();
if($debug) var_dump($dec_i); echo '<br /><br />';

			switch ($tipDeclarare) {
			case 'automat amenda':
				$consum_r = $l_r['ap_rece'] == 0 ? 0 : round($ap_old_r['consum_rece']/$l_r['ap_rece'], 2);
				$consum_c = $l_r['ap_calda'] == 0 ? 0 : round($ap_old_r['consum_cald']/$l_r['ap_calda'], 2);
				break;
			
			default:
				if ($ap_old_tr_r == null) {
					$consum_r = 0;
					$consum_c = 0;
				} else {
					$consum_r = null;
					$consum_c = null;
				}
				
				break;
			}

			$con_i = 'INSERT INTO adm_continut_dca (`id_declarare`, `id_contor`, `index`, `consum`) VALUES ';

			foreach ($contoare as $key => $value) {

				if(substr($key, 0, 1) == 'r') {
					if ($consum_r === null) {
						$consum = $ap_old_r[$key] - $ap_old_tr_r[$key];
					} else {
						$consum = $consum_r;
					}
				}

				if(substr($key, 0, 1) == 'c') {
					if ($consum_c === null)
						$consum = $ap_old_r[$key] - $ap_old_tr_r[$key];
					else
						$consum = $consum_c;
				}

				$con_i .= " ($dec_r, ".$value.", '".$ap_old_r[$key]."', ".$consum."), ";
			}
if($debug) var_dump($con_i); echo '<br /><br />';
			$con_q = mysql_query(substr($con_i, 0, -2).';') or die(mysql_error().'<br />'.$con_i);
		}
	}
}

function transferDiferentePretApa($asocId, $luna) {
	//functia functioneaza doar daca diferentele se impart proportional consumului
	
	$fi_s = "SELECT a.id as id_a, d.id as id_d,
			a.loc_id, a.luna, 
			a.pret_unitar, d.pret_unitar as pret_unitar_d,
			d.um
			FROM fisa_indiv a
			JOIN fisa_indiv d ON (a.loc_id=d.loc_id AND a.luna=d.luna)
			WHERE a.asoc_id=$asocId
			AND a.luna='$luna'
			AND a.serviciu = 21
			AND d.serviciu = 41
			AND a.cant_fact_pers = d.cant_fact_pers
			AND d.um='proportional cu consumul'";
var_dump($fi_s);
	$fi_q = mysql_query($fi_s) or die(mysql_error().'<br />'.$fi_s);
  
	if(mysql_num_rows($fi_q) < 1)
		die('Nu pot transfera valoarea diferentelor in apa deoarece nu am rezultate <br />'.$fi_s);

	$fi_u = '';
	while($fi_r = mysql_fetch_assoc($fi_q)) {
		$fi_u .=   'UPDATE fisa_indiv SET pret_unitar=pret_unitar+'.$fi_r['pret_unitar_d'].' WHERE id = '.$fi_r['id_a'].'; ';
		$fi_u .=   'UPDATE fisa_indiv SET pret_unitar=0 WHERE id = '.$fi_r['id_d'].'; ';
	}

	return $fi_u;
}

//include_once './../../componente/config.php';
//var_dump(transferApometre(10, '11-2012'));
