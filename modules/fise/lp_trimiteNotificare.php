<?php
ini_set('display_errors','1');
error_reporting(E_ALL);
ini_set('max_execution_time', 600);

include_once './../../componente/config.php';
include_once './../../Util.php';
$scara_id = 1;
$luna = '11-2012';

$deprocesare_s = "SELECT dep.*
				  FROM lista_plata lp
				  LEFT OUTER JOIN deprocesare_lista_plata dep
				  	ON (dep.scara_id=lp.scara_id 
				  	AND date_format(dep.luna, '%m-%Y')=lp.luna)
				  WHERE lp.scara_id=$scara_id
				  AND lp.luna='$luna'
				  GROUP BY dep.scara_id";

$deprocesare_q = mysql_query($deprocesare_s) or die(mysql_error().'<br />'.$deprocesare_s);

if (mysql_num_rows($deprocesare_q) > 0) {
	$deprocesare_r = mysql_fetch_assoc($deprocesare_q);
	$reprocesare = true;
}

$notificari_s ="SELECT l.*,
				ep.e_mail,
				p.nume1, p.nume2, p.tip as sex,
				s.scara, s.bloc, s.nr, 
				a.denumire,
				at.denumire as tip
				FROM locatari l
				JOIN id_locatar_proprietar lp ON l.loc_id = lp.id_locatar
				JOIN adm_proprietar p ON lp.id_proprietar = p.id
				JOIN adm_eproprietar ep ON p.id = ep.id

				JOIN scari s ON l.scara_id=s.scara_id
				JOIN adm_artera a ON s.strada=a.id
				JOIN adm_tip_artera at ON a.id_tip=at.id
				
				WHERE l.scara_id=".$scara_id;

$notificari_q = mysql_query($notificari_s) or die(mysql_error().'<br />'.$notificari_s);

while($notificari_r = mysql_fetch_assoc($notificari_q)) {

	$adresa_artera_nr = sprintf('%s %s nr. %s', $notificari_r['tip'], $notificari_r['denumire'], $notificari_r['nr']);
	$adresa_bloc_scara_ap = sprintf('bl. %s, sc. %s, ap. %s', $notificari_r['bloc'], $notificari_r['scara'], $notificari_r['ap']);

	$headersMail   = array();
	$headersMail []= 'From: Urbica <contact@urbica.ro>';
	$headersMail []= 'MIME-Version: 1.0';
	$headersMail []= 'Content-type: text/html; charset="utf-8";'."\r";

	$lunaListaPlata = Util::dateToLuna($luna);
	$anListaPlata = explode('-', $luna); $anListaPlata = $anListaPlata[1];
	$urlPrimaPagina = 'www.urbica.ro';
	$adresaProprietar = $adresa_artera_nr.' '.$adresa_bloc_scara_ap;

	switch ($notificari_r['sex']) {
		case 'M':
			$apelare = 'Domnule ';
			break;
		case 'F':
			$apelare = 'Doamna ';
			break;
		case 'PJ':
			$apelare = 'Către ';
			break;
	}
	$apelare .= $notificari_r['nume1'].' '.$notificari_r['nume2'].',';

	$headers []= 'To: '.$notificari_r['nume1'].' '.$notificari_r['nume2'].' <'.$notificari_r['e_mail'].'>';

	$subiectMail = 'Notificare lista plata '.$lunaListaPlata.' '.$anListaPlata;

	ob_start();
	include 'lp_notificare.php';
	$corpMail = ob_get_clean();
	var_dump($corpMail);
	//if(mail($notificari_r['e_mail'], $subiectMail, $corpMail, implode("\n", $headersMail)."\n"))

}
?>
