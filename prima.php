<?php
ini_set('display_errors','1');
error_reporting(E_ALL);
ini_set('max_execution_time', 600);

include_once 'componente/config.php';
include_once 'Util.php';

// SELECT * , MIN( rest_plata ) , MAX( rest_plata ) 
// FROM fisa_cont fc
// JOIN locatari l ON fc.loc_id = l.loc_id
// GROUP BY fc.loc_id
// HAVING MIN( rest_plata ) <0
// AND MAX( rest_plata ) >0

function adaugaImobil($id = null, $den = null, $nr_n = null, $id_art = null, $nr_a = null) {
	$im = null;

	if($id) {
		$im_s = "SELECT * FROM adm_imobil WHERE id=$id";
		$im_q = mysql_query($im_s) or die(mysql_error().'<br />'.$im_s);
		if (mysql_num_rows($im_q) > 1)
			die('exista mai multe imobile cu acelasi id !!!');
		if(mysql_num_rows($im_q) == 1)
			$im = mysql_fetch_assoc($im_q);
		if(mysql_num_rows($im_q) == 0) {
			$im_i = "INSERT INTO adm_imobil (`id`, `denumire`, `numar_nivele`, `id_artera`, `numar_artera`) 
					 VALUES ($id, '$den', '$nr_n', $id_art, '$nr_a');";
			$im_q = mysql_query($im_i) or die(mysql_error().'<br />'.$im_i);
			return mysql_insert_id();
		}
	}

	if ($den && !$im) {
		$im_s = "SELECT * FROM adm_imobil WHERE denumire='$den'";
		$im_q = mysql_query($im_s) or die(mysql_error().'<br />'.$im_s);
		if (mysql_num_rows($im_q) > 1)
			die('exista mai multe imobile cu acelasi denumire !!!');
		if(mysql_num_rows($im_q) == 1)
			$im = mysql_fetch_assoc($im_q);
		if(mysql_num_rows($im_q) == 0) {
			$im_i = "INSERT INTO adm_imobil (`id`, `denumire`, `numar_nivele`, `id_artera`, `numar_artera`) 
					VALUES (NULL, '$den', '$nr_n', $id_art, '$nr_a');";
			$im_q = mysql_query($im_i) or die(mysql_error().'<br />'.$im_i);
			return mysql_insert_id();
		}
	}

	if($nr_n && $im) {
		$im_u = "UPDATE adm_imobil SET numar_nivele=$nr_n WHERE id=".$im['id'];
		$im_q = mysql_query($im_u) or die(mysql_error().'<br />'.$im_u);
	}

	if($id_art && $im) {
		$im_u = "UPDATE adm_imobil SET id_artera=$id_art WHERE id=".$im['id'];
		$im_q = mysql_query($im_u) or die(mysql_error().'<br />'.$im_u);
	}

	if($nr_a && $im) {
		$im_u = "UPDATE adm_imobil SET numar_artera='$nr_a' WHERE id=".$im['id'];
		$im_q = mysql_query($im_u) or die(mysql_error().'<br />'.$im_u);
	}

	return ($im == null ? die('caz netratat in adauga imobil!') : $im['id']);
}

function adaugaScara($id = null, $den = null, $im = null, $asoc = null) {
	$sc = null;
	if($id) {
		$sc_s = "SELECT * FROM adm_scara WHERE id=$id";
		$sc_q = mysql_query($sc_s) or die(mysql_error().'<br />'.$sc_s);
		if (mysql_num_rows($sc_q) > 1)
			die('exista mai multe scari cu acelasi id !!!');
		if(mysql_num_rows($sc_q) == 1)
			$sc = mysql_fetch_assoc($sc_q);
		if(mysql_num_rows($sc_q) == 0) {
			$sc_i = "INSERT INTO adm_scara (`id`, `denumire`, `id_imobil`, `id_asociatie`) VALUES ($id, '$den', $im, $asoc);";
			$sc_q = mysql_query($sc_i) or die(mysql_error().'<br />'.$sc_i);
			return mysql_insert_id();
		}
	}

	if ($den && $im && !$sc) {
		$sc_s = "SELECT * FROM adm_scara WHERE denumire='$den' AND id_imobil='$im'";
		$sc_q = mysql_query($sc_s) or die(mysql_error().'<br />'.$sc_s);
		if (mysql_num_rows($sc_q) > 1)
			die('exista mai multe scari cu acelasi denumire !!!');
		if(mysql_num_rows($sc_q) == 1)
			$sc = mysql_fetch_assoc($sc_q);
		if(mysql_num_rows($sc_q) == 0) {
			$sc_i = "INSERT INTO adm_scara (`id`, `denumire`, `id_imobil`, `id_asociatie`) VALUES (NULL, '$den', $im, $asoc);";
			$sc_q = mysql_query($sc_i) or die(mysql_error().'<br />'.$sc_i);
			return mysql_insert_id();
		}
	}

	if($im && $sc) {
		$sc_u = "UPDATE adm_scara SET id_imobil=$im WHERE id=".$sc['id'];
		$sc_q = mysql_query($sc_u) or die(mysql_error().'<br />'.$sc_u);
	}

	if($asoc && $sc) {
		$sc_u = "UPDATE adm_scara SET id_asociatie=$asoc WHERE id=".$sc['id'];
		$sc_q = mysql_query($sc_u) or die(mysql_error().'<br />'.$sc_u);
	}

	return ($sc == null ? die('caz netratat in adauga scara!') : $sc['id']);
}


function clean() {
	$c_s = '
	TRUNCATE adm_declarare_contoare_apartament;
	TRUNCATE adm_contor;
	TRUNCATE adm_apartament;
	TRUNCATE adm_scara;
	TRUNCATE adm_imobil;
	';
	mysql_query($c_s) or die(mysql_error().'<br />'.$c_s);
}


function insereazaApometreInitiale() {
	$l_s = 'SELECT *, ap.id as id_apartament
			FROM locatari L
			INNER JOIN asociatii A ON L.asoc_id=A.asoc_id
			INNER JOIN scari S ON L.scara_id=S.scara_id
			INNER JOIN adm_artera art ON S.strada=art.id
			INNER JOIN id_locatar_proprietar lp ON L.loc_id=lp.id_locatar
			INNER JOIN adm_proprietar p ON lp.id_proprietar=p.id
			INNER JOIN adm_apartament ap ON (p.id=ap.id_proprietar AND ap.numar=L.ap)
			INNER JOIN adm_scara sc ON (ap.id_scara=sc.id AND sc.denumire=S.scara)

			LEFT OUTER JOIN adm_declarare_ca d ON d.id_apartament=ap.id
			WHERE d.id_apartament IS NULL';
	$l_q = mysql_query($l_s) or die(mysql_error().'<br />'.$l_s);
		
	$con_i = 'INSERT INTO adm_continut_dca (`id_declarare`, `id_contor`, `index`, `consum`) VALUES ';

	//pt fiecare locatar
	while($l_r = mysql_fetch_assoc($l_q)) {

		$ap_s = "SELECT 
					la.asoc_id, la.scara_id,
					la.loc_id, a.lunax as luna,
					la.r1, la.r2, la.r3, la.r4, la.r5, la.c1, la.c2, la.c3, la.c4, la.c5, 
					0 as consum_rece, 0 as consum_cald,
					0 as auto, 0 as pausal, null as amenda_rece, null as amenda_calda, 0 as repetari, 1 as completat
				FROM locatari_apometre la
				INNER JOIN (
					SELECT *, DATE_FORMAT(DATE_SUB(STR_TO_DATE(
						min(STR_TO_DATE(CONCAT('01-', luna), '%d-%m-%Y')), '%Y-%m-%d')
					, INTERVAL 10 DAY), '%m-%Y') as lunax 
					FROM apometre 
					GROUP BY loc_id) a ON la.loc_id=a.loc_id

				WHERE la.loc_id=".$l_r['loc_id'];

		$ap_q = mysql_query($ap_s) or die(mysql_error().'<br />'.$ap_s);

		//introducem contorii
		$contoare = array();

		for ($i=1; $i <= $l_r['ap_rece']; $i++) { 
			$cont_i = "INSERT INTO adm_contor (`id`, `denumire`, `tip`) VALUES (NULL, 'r$i', 'apa rece')";
			$cont_q = mysql_query($cont_i) or die(mysql_error().'<br />'.$cont_i);
			$cont_r = mysql_insert_id();

			$contoare['r'.$i] = $cont_r;
		}
		for ($i=1; $i <= $l_r['ap_calda']; $i++) { 
			$cont_i = "INSERT INTO adm_contor (`id`, `denumire`, `tip`) VALUES (NULL, 'c$i', 'apa calda')";
			$cont_q = mysql_query($cont_i) or die(mysql_error().'<br />'.$cont_i);
			$cont_r = mysql_insert_id();

			$contoare['c'.$i] = $cont_r;
		}

		if (mysql_num_rows($ap_q) <= 0){
			echo '<br /><strong>Pentru locatarul curent nu exista apometre!!! </strong><br /><br />'.$ap_s.'<br /><br />';
			continue;
		}


		//pentru fiecare inregistrare
		$ultimaInregistrare = null;
		while ($ap_r = mysql_fetch_assoc($ap_q)) {
			$dataDeclarare = explode('-', $ap_r['luna']);
			$dataDeclarare = $dataDeclarare[1].'-'.$dataDeclarare[0].'-01';

			if($ap_r['auto'] == 0)
				$tipDeclarare = 'operator borderou';
			else if($ap_r['auto'] == 1 && ($ap_r['amenda_rece'] != null || $ap_r['amenda_calda'] != null))
				$tipDeclarare = 'automat amenda';
			else if ($ap_r['auto'] == 1 && $ap_r['pausal'] == 1) 
				$tipDeclarare = 'automat pausal';
			else if ($ap_r['auto'] == 1 && $ultimaInregistrare != null && ($ultimaInregistrare['consum_rece'] == $ap_r['consum_rece'])) 
				$tipDeclarare = 'automat repetare';
			else
				$tipDeclarare = 'automat operator';

			$dec_i = "INSERT INTO adm_declarare_ca (`id`, `data_timp`, `tip_declarare`, `id_operator`, `id_apartament`, `luna`) 
						  VALUES (NULL, '$dataDeclarare', '$tipDeclarare', '23', '".$l_r['id_apartament']."', '".$ap_r['luna']."');";
			$dec_q = mysql_query($dec_i) or die(mysql_error().'<br />'.$dec_i);
			$dec_r = mysql_insert_id();

			switch ($tipDeclarare) {
				case 'automat amenda':
					$consum_r = $l_r['ap_rece'] == 0 ? 0 : round($ap_r['consum_rece']/$l_r['ap_rece'], 2);
					$consum_c = $l_r['ap_calda'] == 0 ? 0 : round($ap_r['consum_cald']/$l_r['ap_calda'], 2);
					break;
				
				default:
					if ($ultimaInregistrare == null) {
						$consum_r = 0;
						$consum_c = 0;
					} else {
						$consum_r = null;
						$consum_c = null;
					}
					
					break;
			}


			foreach ($contoare as $key => $value) {

				if(substr($key, 0, 1) == 'r') {
					if ($consum_r === null) {
						$consum = $ap_r[$key] - $ultimaInregistrare[$key];
					} else {
						$consum = $consum_r;
					}
				}

				if(substr($key, 0, 1) == 'c') {
					if ($consum_c === null)
						$consum = $ap_r[$key] - $ultimaInregistrare[$key];
					else
						$consum = $consum_c;
				}

				$con_i .= " ($dec_r, ".$value.", '".$ap_r[$key]."', ".$consum."), ";

				
			}

			$ultimaInregistrare = $ap_r;
			
		}
		
	}

	$con_q = mysql_query(substr($con_i, 0, -2).';') or die(mysql_error().'<br />'.$con_i);
	
}

function insereaza_proprietari() {
	$l_s = 'SELECT *
			FROM locatari L
			INNER JOIN asociatii A ON L.asoc_id=A.asoc_id
			INNER JOIN scari S ON L.scara_id=S.scara_id
			LEFT OUTER JOIN id_locatar_proprietar lp ON L.loc_id=lp.id_locatar 
			WHERE lp.id_proprietar IS NULL';
	$l_q = mysql_query($l_s) or die('nu pot afala locatarii fara proprietar <br />'.mysql_error().'<br />'.$l_s);
	$propInserati = 0;

	while($l_r = mysql_fetch_assoc($l_q)) {

		$nume1 = substr($l_r['nume'], 0, strpos($l_r['nume'], ' '));
		$nume2 = substr($l_r['nume'], strpos($l_r['nume'], ' ')+1);

		//adaug proprietar
		$p_i = "INSERT INTO `adm_proprietar` (`id`, `nume1`, `nume2`, `tip`, `e_mail`, `telefon`, `id_imobil`, `id_apartament`) 
				VALUES (NULL, '$nume2', '$nume1', 'M', '', '', '0', '0');";

		mysql_query($p_i) or die(mysql_error().$p_i);

		$l_r['id_proprietar'] = mysql_insert_id();

		//adaug legatura intre proprietar si locatar
		$lp_i = "INSERT INTO id_locatar_proprietar (`id_locatar`, `id_proprietar`) 
				VALUES ('".$l_r['loc_id']."', '".$l_r['id_proprietar']."');";
		mysql_query($lp_i) or die('nu pot face legatura inre locatar si proprietar');

		//adaug apartament
		$l_r['adm_imobil'] =  adaugaImobil(null, $l_r['bloc'], 0, $l_r['strada'], $l_r['nr']);
		$l_r['adm_scara'] = adaugaScara(null, $l_r['scara'], $l_r['adm_imobil'], $l_r['asoc_id']);
		$a_i = "INSERT INTO adm_apartament (`id`, `numar`, `etaj`, `suprafata`, `id_scara`, `id_proprietar`, `adresa_diferita`)
				VALUES (NULL, '".$l_r['ap']."', ".(strtolower($l_r['etaj']) == 'p' ? 0 : 
												(is_integer($l_r['etaj']) ? $l_r['supr'] :
												'NULL' )).
				", ".$l_r['supr'].",  ".$l_r['adm_scara'].", ".$l_r['id_proprietar'].", 0);";
		$a_q = mysql_query($a_i) or die(mysql_error().'<br />'.$a_i);

		$propInserati++;
	}

	return $propInserati;
}

/*
SELECT loc_id, sum(valoare, data_lp
FROM (SELECT *,max(data) as data_lp FROM fisa_cont WHERE act IN ('Protocol', 'LP') GROUP BY loc_id) lp
JOIN fisa_cont c ON lp.loc_id=c.loc_id
WHERE c.act NOT IN ('Protocol', 'LP') 
AND (c.data>lp.data_lp OR (c.data=lp.data_lp AND lp.id<c.id))
AND lp.loc_id=850
*/
insereazaApometreInitiale();
//echo insereaza_proprietari();

