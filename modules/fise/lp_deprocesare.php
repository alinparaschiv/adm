<?php
if(isset($_GET['scara_id'])) $_GET['scara_id']=  mysql_real_escape_string($_GET['scara_id']);
if(isset($_GET['luna'])) $_GET['luna']=  mysql_real_escape_string($_GET['luna']);

//include_once 'componente/config.php';
//include_once 'Util.php';


/**
 * Intoarce id-ul inregistrarii anterioare din fisa cont
 * @param  int $id_fc 		id-ul inrefistrarii curente
 * @return int 
 */
function getInregistrareaAnterioaraDinFisaCont($id_fc) {
	$s = 'SELECT r.* 
		  FROM fisa_cont r 
		  JOIN fisa_cont s 
		  	ON (r.loc_id=s.loc_id) 
		  WHERE s.id='.$id_fc.'
		  AND r.id < s.id
		  ORDER BY r.id DESC
		  LIMIT 1
		  ';
	$q = mysql_query($s) or die($s .' Error1 <br />'.mysql_error());

	return mysql_num_rows($q) == 0 ? null : mysql_fetch_assoc($q);
};

$f_eroare = false;
$f_mesaj = '';

$update = '';

$lp_data = $dataProcesata = Util::getDataProcesare($scara_id, $luna);

$lp_s = 'SELECT * 
		FROM lista_plata l
		LEFT JOIN fisa_cont f 
			ON l.loc_id=f.loc_id
		WHERE f.act=\'LP\' 
		AND f.scara_id='.$scara_id.'
		AND f.data=\''.$lp_data.'\'
		AND l.luna=\''.$luna.'\'';

$lp_q = mysql_query($lp_s) or die($lp_s.' Error2 <br />'.mysql_error());

while($lp_r = mysql_fetch_assoc($lp_q)) {
	$fp_s = 'SELECT * 
			 FROM fisa_pen 
			 WHERE loc_id='.$lp_r['loc_id'].'
			 AND id_restanta='.$lp_r['id'].'
			 ';

	$fp_q = mysql_query($fp_s) or die(mysql_error().' Error3 <br />'.$fp_s);
	if (mysql_num_rows($fp_q)) {
		$f_eroare = true;
		$f_mesaj .= 'O LP nu poate fi deprocesata deoarece e prtatoare de penalizari';
	}


	if ($lp_r['datorie'] < 0) {
		$lp_sumaTotala = -$lp_r['datorie'];
		$inregistrare = getInregistrareaAnterioaraDinFisaCont($lp_r['id']);

		while ($lp_sumaTotala > 0) {
			if ($inregistrare['explicatie'] == 'plata intretinere' && ((-$inregistrare['rest_plata']) < $inregistrare['valoare'] )) {
				$sumaOperata = $inregistrare['valoare'] + $inregistrare['rest_plata'];
				$sumaOperata = $sumaOperata > $lp_sumaTotala ? $lp_sumaTotala : $sumaOperata;
				
				$update .= 'UPDATE fisa_cont SET rest_plata=rest_plata-'.$sumaOperata.' WHERE id='.$inregistrare['id']."; ";
				
				$lp_sumaTotala -= $sumaOperata;
			} else if($inregistrare['act'] == 'Protocol') {
				$lp_sumaTotala = 0;
			}

			$inregistrare = getInregistrareaAnterioaraDinFisaCont($inregistrare['id']);
		}
	}

	$lp_sumaTotala = $lp_r['valoare'] - $lp_r['rest_plata'];

	$fc_ver_s = 'SELECT *
	FROM fisa_cont f
	WHERE loc_id='.$lp_r['loc_id'].'
	AND (data>\''.$lp_data.'\' 
		 AND id< '.$lp_r['id'].')
	ORDER BY data DESC';
	$fc_ver_q = mysql_query($fc_ver_s) or die($fc_ver_s.' Error4 <br />'.mysql_error());
	if(mysql_num_rows($fc_ver_q) > 0) {
		$f_eroare = true;
		$f_mesaj .= 'Exista chitante introduse inainte de LP cu data>LP<br />'.$fc_ver_s;
	}


	$fc_s = 'SELECT *
	FROM fisa_cont f
	WHERE loc_id='.$lp_r['loc_id'].'
	AND (data>\''.$lp_data.'\' 
		OR (data=\''.$lp_data.'\' AND id> '.$lp_r['id'].'))
	ORDER BY data DESC';

	$fc_q = mysql_query($fc_s) or die($fc_s.' Error5 <br />'.mysql_error());

	if(mysql_num_rows($fc_q) > 0) {
		while($fc_r = mysql_fetch_assoc($fc_q)) {
			if($fc_r['act'] == 'LP') {
				$f_eroare = true;
				$f_mesaj .= 'O LP nu poate fi deprocesata pana nu sunt deprocesate totate LP emise dupa';
				break;
			}

			if($fc_r['explicatie'] == 'plata intretinere' && $fc_r['valoare']>((-1)*$fc_r['rest_plata']) && $lp_sumaTotala > 0) {
				$sumaOperata = $fc_r['valoare'] + $fc_r['rest_plata'];
				$sumaOperata = $sumaOperata > $lp_sumaTotala ? $lp_sumaTotala : $sumaOperata;
				$lp_sumaTotala -= $sumaOperata;

				$update .= 'UPDATE fisa_cont SET rest_plata=rest_plata-'.$sumaOperata.' WHERE id='.$fc_r['id']."; ";
			}
		}
	}
    
	$update .= 'DELETE FROM fisa_cont WHERE id='.$lp_r['id']."; ";
	$update .= 'UPDATE lista_plata SET procesata=0 WHERE id_lista_plata='.$lp_r['id_lista_plata']."; ";
}

if (!$f_eroare) {
	//var_dump($update); die();
	//mysql_query($update) or die(mysql_error() .' Error6 <br />'. $update);
	//var_dump($update);
	
	$luna_data = explode('-', $luna);
  //verifica reprocesarea cu F5 unei liste deja procesate
  
  $verificaProcesataS="SELECT SUM(procesata) sum, count(1) countp, asoc_id
      FROM lista_plata 
      WHERE scara_id='".mysql_real_escape_string($_GET['scara_id']).
      "' AND luna='".mysql_real_escape_string($_GET['luna'])."'";
  $verificaProcesata = mysql_query($verificaProcesataS) or die ("Nu pot afla informatiile despre liste procesate.<br />".mysql_error());
  $rowVerProc=mysql_fetch_array($verificaProcesata);

  if($rowVerProc[sum]==$rowVerProc[countp]){ 
      mysql_query("INSERT INTO `deprocesare_lista_plata` (`id`, `timp`, `scara_id`, `luna`, `motiv`) 
        VALUES (NULL, CURRENT_TIMESTAMP, '$scara_id', '".date('Y-m-01', strtotime($luna_data[1].'-'.$luna_data[0].'-01'))."', '".mysql_escape_string($motivDeprocesare)."');") 
	or die('Error7 -> Nu pot sa completez tabela deprocesare_liste_plata');


	$update = explode("; ", $update);
	foreach ($update as $value) {

		if ($value != "")
			mysql_query($value) or die(mysql_error() .' Error6 <br />'. $value.'<br /><br /><br /><br />'.var_dump($update));
          echo '<script language="javascript">document.location.href="index.php?link=lista_plata&asoc_id='
              .$rowVerProc[asoc_id].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']
              .'";</script>';
	}
      } else {
     
      echo '<script language="javascript">document.location.href="index.php?link=lista_plata&asoc_id='
              .$rowVerProc[asoc_id].'&scara_id='.$_GET['scara_id'].'&luna='.$_GET['luna']
              .'";</script>';
      die ("Este deja deprocesata.<br />".mysql_error()); 
    }
    
}