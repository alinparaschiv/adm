<?php
class Util_facturi {

public function getSelectLuna($asoc_id, $luna = null) {
	$select_s = "SELECT * FROM lista_plata WHERE asoc_id=$asoc_id AND procesata=1 ORDER BY STR_TO_DATE(luna, '%m-%Y') DESC";
	$select_q = mysql_query($select_s) or die('Nu pot afla utima luna procesata');
	$select_r = mysql_fetch_assoc($select_q);
	$rezult = '';

	$lunaUrmX = explode('-', $select_r['luna']);
	$lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
	$lunaUrmX = strtotime('+1 month', $lunaUrmX);
	$lunaUrm = date('m-Y', $lunaUrmX);

	for ($i=-1; $i<4; $i++) {
		$data = date('m-Y', mktime(0, 0, 0, (date('m')-$i), 1, date('Y')));

		if ($luna == $data || $lunaUrm==$data) {
			$selectat = 'selected="selected"';
		} else
			$selectat = '';

		$rezult .= '<option '.$selectat.' value="'.$data.'">'.$data.'</option>';
	}

	return $rezult;
}

}
?>