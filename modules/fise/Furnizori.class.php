<?php

class Furnizori{

	private static $SQL = "SELECT A.asociatie
     , S.scara
     , SER.serviciu
     , U.nume
     , F.per_gratie AS s_gratie
     , F.proc_penalizare AS s_procent
     , F.furnizor
     , CA.cont AS cont_asociatie
     , CF.cont AS cont_furnizor
     , date_add(FF.data_scadenta, INTERVAL 30 + F.per_gratie DAY) AS data_finala
     , datediff(date_add(FF.data_scadenta, INTERVAL 30 + F.per_gratie DAY), now()) AS zile_ramase
     , TOTAL.total_facturata
     , TOTAL.total_penalizare
     , AF.codClient
     , SF.codClient
     , FF.*
FROM
  fisa_furnizori FF
INNER JOIN asociatii A
ON FF.asoc_id = A.asoc_id
LEFT OUTER JOIN scari S
ON FF.scara_id = S.scara_id
INNER JOIN servicii SER
ON FF.serviciu_id = SER.serv_id
INNER JOIN admin U
ON FF.operator_id = U.id
INNER JOIN furnizori F
ON FF.fur_id = F.fur_id
INNER JOIN conturi CF
ON F.cont_id = CF.cont_id
INNER JOIN conturi CA
ON A.cont_id = CA.cont_id
INNER JOIN furnizori_total TOTAL
ON FF.fact_id = TOTAL.fact_id
LEFT OUTER JOIN asociatii_furnizori AF
ON FF.fur_id = AF.fur_id AND FF.asoc_id = AF.asoc_id
LEFT OUTER JOIN scari_furnizori SF
ON FF.fur_id = SF.fur_id AND FF.scara_id = SF.scara_id

WHERE 1=1 ";
	/*"SELECT
F.`furnizor`, F.`per_gratie` `s_gratie`, F.`proc_penalizare` `s_procent`,
TOTAL.`total_facturata`, TOTAL.`total_penalizare`,
asf.*,
FF.`id`, FF.`serviciu_id`, FF.`fact_id`,
FF.`data_inreg`, FF.`data_scadenta`, DATE_ADD(FF.`data_scadenta`, INTERVAL (30 + F.`per_gratie`) DAY) data_finala, DATEDIFF(DATE_ADD(FF.`data_scadenta`, INTERVAL (30 + F.`per_gratie`) DAY), NOW()) zile_ramase,
FF.`document`, FF.`explicatii`, FF.`valoare`, FF.`penalizare`, FF.`procent`, FF.`operator_id`, FF.`ip`,
A.`asociatie`,
S.`scara`,
U.`nume`,
CA.`cont` cont_asociatie,
CF.`cont` cont_furnizor,
SER.`serviciu`
FROM furnizori F, asociatii A, admin U, servicii SER, conturi CA, conturi CF,
(SELECT `fact_id`, SUM(valoare) total_facturata, SUM(penalizare) total_penalizare FROM `fisa_furnizori` GROUP BY fact_id) TOTAL,
(SELECT `asoc_id`, NULL AS `scara_id`, `fur_id`, `codClient` FROM asociatii_furnizori UNION (SELECT `asoc_id`, `scara_id`, `fur_id`, `codClient` FROM scari_furnizori)) asf,
fisa_furnizori FF LEFT OUTER JOIN scari S ON (FF.scara_id=S.scara_id)
WHERE
F.cont_id=CF.cont_id AND
A.cont_id=CA.cont_id AND
FF.`fact_id`=TOTAL.`fact_id` AND
FF.`serviciu_id`=SER.`serv_id` AND
FF.`asoc_id`=A.`asoc_id` AND
FF.`operator_id`=U.`id` AND
FF.`fur_id`=F.`fur_id` AND
((FF.`scara_id` IS NULL AND FF.`asoc_id`=asf.`asoc_id` AND FF.`fur_id`=asf.`fur_id`) OR
(FF.`scara_id` IS NOT NULL AND FF.`scara_id`=asf.`scara_id` AND FF.`fur_id`=asf.`fur_id`)) ";*/

	static function getAllByfur_id($asociatie = NULL, $furnizor = NULL){
		$sql = Furnizori::$SQL;
		if ($asociatie != NULL)
			$sql .= "AND FF.`asoc_id`=".$asociatie;
		if ($furnizor != NULL)
			$sql .= " AND FF.`fur_id`=".$furnizor;
		$sql .= " ORDER BY FF.`fur_id`, FF.`id`"; // FF.`asoc_id`, FF.`scara_id`, FF.`fact_id`
		$sql_rezult = mysql_query($sql) or die("A aparut o eroare la selectia datelor din fisa furnizori <br /><br />".$sql."<br /><br />".mysql_error());
		$rez = array();
		while($aux = mysql_fetch_assoc($sql_rezult))
			$rez[] = $aux;
		return Furnizori::addPenalizare($rez);
	}

	static function getAllByasoc_id($asociatie = NULL, $furnizor = NULL){
		$sql = Furnizori::$SQL;
		if ($asociatie != NULL)
			$sql .= "AND FF.`asoc_id`=".$asociatie;
		if ($furnizor != NULL)
			$sql .= " AND FF.`fur_id`=".$furnizor;
		$sql .= " ORDER BY FF.`asoc_id`, FF.`id`"; // FF.`scara_id`, FF.`fur_id`, FF.`fact_id`
		$sql_rezult = mysql_query($sql) or die("A aparut o eroare la selectia datelor din fisa furnizori <br /><br />".$sql."<br /><br />".mysql_error());
		$rez = array();
		while($aux = mysql_fetch_assoc($sql_rezult))
			$rez[] = $aux;
		return Furnizori::addPenalizare($rez);
	}

	static function getAllBydata_inreg($asociatie = NULL, $furnizor = NULL){
		$sql = Furnizori::$SQL;
		if ($asociatie != NULL)
			$sql .= "AND FF.`asoc_id`=".$asociatie;
		if ($furnizor != NULL)
			$sql .= " AND FF.`fur_id`=".$furnizor;
		$sql .= " ORDER BY FF.`data_inreg`, FF.`id` ";
		$sql_rezult = mysql_query($sql) or die("A aparut o eroare la selectia datelor din fisa furnizori <br /><br />".$sql."<br /><br />".mysql_error());
		$rez = array();
		while($aux = mysql_fetch_assoc($sql_rezult))
			$rez[] = $aux;
		return Furnizori::addPenalizare($rez);
	}

	static function getAllBy($asociatie = NULL, $furnizor = NULL){
		$sql = Furnizori::$SQL;

		if ($asociatie != NULL)
			$sql .= "AND FF.`asoc_id`=".$asociatie;
		if ($furnizor != NULL)
			$sql .= " AND FF.`fur_id`=".$furnizor;
		$sql .= " ORDER BY FF.`id` ";
		$sql_rezult = mysql_query($sql) or die("A aparut o eroare la selectia datelor din fisa furnizori <br /><br />".$sql."<br /><br />".mysql_error());
		$rez = array();
		while($aux = mysql_fetch_assoc($sql_rezult))
			$rez[] = $aux;
		return Furnizori::addPenalizare($rez);
	}
// de corectat
	private static function addPenalizare($data){
		foreach($data as $key => $row)
		{
			$data[$key]['zile_ramase'] = 0;
			if (!isset($data[$key]['penalizare'])) {
				if ($data[$key]['explicatii'] != 'Factura')
					$dataScadenta = explode('-', $data[$key]['data_inreg']);
				else
					$dataScadenta = explode('-', $data[$key]['data_scadenta']);
				$dataScadenta = mktime(0,0,0,$dataScadenta[1],$dataScadenta[2],$dataScadenta[0]);
				$dataCurenta = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$zile = ( $dataCurenta - $dataScadenta ) / 86400;

				if ($dataScadenta < mktime(0,0,0,date('m'),  ($data[$key]['explicatii'] != 'Factura') ? date('d') : (date('d')-30-$data[$key]['s_gratie'])) ) {
					$data[$key]['penalizare'] = $zile * $data[$key]['total_facturata'] * ($row['s_procent'] / 100);
					$data[$key]['zile_ramase'] = -$zile;
				}else{
					$data[$key]['zile_ramase'] = ($data[$key]['explicatii'] != 'Factura') ? -$zile : (-$zile + 30 + $data[$key]['s_gratie']);
					$data[$key]['penalizare'] = 0;
				}
				$data[$key]['procent'] = $row['s_procent'];
			}
		}
		return $data;
	}

	static function getPlati($order = 'fur_id', $asoc = NULL, $fur = NULL) {
		$sql = Furnizori::$SQL;
		
		$sql = str_split($sql, strrpos($sql, 'WHERE', -1));
		
		$sql[0] .= "LEFT OUTER JOIN (SELECT `fact_id`, 1 as 'pen_null' FROM `fisa_furnizori` WHERE `penalizare` IS NULL GROUP BY `fact_id`) X1
						ON FF.fact_id = X1.fact_id ";
		
		$sql = implode($sql);
		$sql .= "AND( X1.`pen_null`=1 OR TOTAL.`total_penalizare` > 0 )";

		if ($asoc != NULL)
			$sql .= " AND FF.`asoc_id`=".$asoc;
		if ($fur != NULL)
			$sql .= " AND FF.`fur_id`=".$fur;
		if($order == 'asoc_id')
			$sql .= " ORDER BY FF.`asoc_id`, FF.`scara_id`, FF.`fur_id`, FF.`fact_id`, FF.`id`";
		else
			$sql .= " ORDER BY FF.`fur_id`, FF.`asoc_id`, FF.`scara_id`, FF.`fact_id`, FF.`id`";

//		var_dump($sql);
//		die();

		$sql_rezult = mysql_query($sql) or die("A aparut o eroare la selectia datelor din fisa furnizori <br /><br />".$sql."<br /><br />".mysql_error());
		$rez = array();
		while($aux = mysql_fetch_assoc($sql_rezult))
			$rez[] = $aux;
		return Furnizori::addPenalizare($rez);
	}

	static function insertPlata($fact_id, $val, $op, $type = "valoare", $dataPlata = null)
	{
           //var_dump($fact_id);

		$facCurentaSQL = "SELECT FF.*, F.`furnizor`, F.`per_gratie` `s_gratie`, F.`proc_penalizare` `s_procent` FROM fisa_furnizori FF, furnizori F WHERE FF.fur_id=F.fur_id AND FF.fact_id=$fact_id AND FF.penalizare IS NULL";
		$facCurenta = mysql_query($facCurentaSQL) or die("Nu pot selecta factura curenta din fisa furnizori <br />".$facCurentaSQL."<br />".mysql_error());
		if (mysql_num_rows($facCurenta) == 0) {
			$facCurentaSQL = "SELECT FF.*, F.`furnizor`, F.`per_gratie` `s_gratie`, F.`proc_penalizare` `s_procent` FROM fisa_furnizori FF, furnizori F WHERE FF.fur_id=F.fur_id AND FF.fact_id=$fact_id AND FF.valoare > 0";
			$facCurenta = mysql_query($facCurentaSQL) or die("Nu pot selecta factura initala din fisa furnizori <br />".$facCurentaSQL."<br />".mysql_error());
		}

		$facCurenta = mysql_fetch_assoc($facCurenta);
                //var_dump($facCurentaSQL);die();
		$scara = $facCurenta['scara_id'] == NULL ? 'NULL' : "'".$facCurenta['scara_id']."'";
		$locatar = $facCurenta['loc_id'] == NULL ? 'NULL' : "'".$facCurenta['loc_id']."'";

		if($dataPlata == null)
			$dataCurenta = mktime(0,0,0,date('m'),date('d'),date('Y'));
		else
			$dataCurenta = $dataPlata;

		if ($type == "valoare") {
			$restPlata = "SELECT sum(valoare) rest_plata FROM fisa_furnizori WHERE fact_id=$fact_id";
			$restPlata = mysql_query($restPlata) or die("Nu pot afla restul de plata pentru factura curenta <br />".mysql_error());
			$restPlata = mysql_result($restPlata, 0, 'rest_plata');

	  		$dataScadenta = explode('-', $facCurenta['data_scadenta']);	
			$dataScadenta = mktime(0,0,0,$dataScadenta[1],$dataScadenta[2],$dataScadenta[0]);

			$penalizare;

			if ($dataScadenta < strtotime(' -30 days' ,$dataCurenta) ) {
				$zile = round(( $dataCurenta - $dataScadenta ) / 86400, 0);
				$penalizare = $zile * $restPlata * ($facCurenta['s_procent'] / 100);
			}else{
				$penalizare = 0;
			}

			$update = "UPDATE fisa_furnizori SET penalizare=$penalizare, procent=".$facCurenta['s_procent']." WHERE id=".$facCurenta['id'];
			mysql_query($update) or die("Nu am putut actualiza penalizarile in momentul inserari unei noi plati <br />".$update."<br />".mysql_error());

			$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '".$facCurenta['asoc_id']."', $scara, $locatar, '".$facCurenta['fur_id']."', '".$facCurenta['serviciu_id']."', '".$facCurenta['fact_id']."', '".date('Y-m-d', $dataCurenta)."', '".date('Y-m-d', $dataCurenta)."', '".$op."', 'Ordin Plata', '".-$val."', ".(($val >= $restPlata) ? ("'0', '".$facCurenta['s_procent']."' ") : 'NULL, NULL' ).");";
			mysql_query($insertFisaFurnizoriSQL) or die ("Eroare la introducerea unei plati in fisa_furnizori <br />".mysql_error());

		} else
		if ($type == "penalizare") {
			$facCurentaSQL = "SELECT FF.*, F.`furnizor`, F.`per_gratie` `s_gratie`, F.`proc_penalizare` `s_procent` FROM fisa_furnizori FF, furnizori F WHERE FF.fur_id=F.fur_id AND FF.fact_id=$fact_id";
			//var_dump($facCurentaSQL);
			$facCurenta = mysql_query($facCurentaSQL) or die("Nu pot selecta factura initala din fisa furnizori <br />".$facCurentaSQL."<br />".mysql_error());
			$facCurenta = mysql_fetch_assoc($facCurenta);
	                
			$scara = $facCurenta['scara_id'] == NULL ? 'NULL' : "'".$facCurenta['scara_id']."'";
			$locatar = $facCurenta['loc_id'] == NULL ? 'NULL' : "'".$facCurenta['loc_id']."'";
			
			$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '".$facCurenta['asoc_id']."', $scara, $locatar, '".$facCurenta['fur_id']."', '".$facCurenta['serviciu_id']."', '".$facCurenta['fact_id']."', '".date('Y-m-d', $dataCurenta)."', '".date('Y-m-d', $dataCurenta)."', '".$op."', 'Ordin Plata', '0', '".-$val."', '".$facCurenta['s_procent']."');";
			//var_dump($insertFisaFurnizoriSQL);die();
			mysql_query($insertFisaFurnizoriSQL) or die ("Eroare la introducerea unei plati in fisa_furnizori <br />".mysql_error());

		}

	}

	static function getInfo($fur_id)
	{
		$rez = array('valoare' => 0, 'debit' => 0, 'penalizari' => 0);
		return $rez;
	}

  
}