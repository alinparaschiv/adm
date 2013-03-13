<?php

class Penalizare{
	private $locatarId;
	private $scaraId;
	private $asocId;

	private $procentPenalizare; //0.02 pe zii
	private $termenPlata; //intre 1 si 15 zile
	private $termenScadent = 30; //stabilit prin lege
	/**
	 * Constructor
	 */
	function __construct($loc, $scara, $asoc){
		$this->locatarId = $loc;
		$this->scaraId = $scara;
		$this->asocId = $asoc;

		$sql = "SELECT penalizare AS proc_pen, termen FROM asociatii_setari WHERE asoc_id=".$asoc;
		$sql = mysql_query($sql);

		$this->procentPenalizare = floatval(mysql_result($sql,0,'proc_pen'));
		$this->termenPlata = mysql_result($sql,0,'termen');

		$this->verifica();
	}

	public function verifica() {
		$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d')-$this->termenScadent-$this->termenPlata-160,date('Y')));
		$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d')-$this->termenScadent-$this->termenPlata,date('Y')));

		$penalizariSQL = "SELECT data_scadenta, id_restanta FROM `fisa_pen` WHERE loc_id=".$this->locatarId." GROUP BY id_restanta";
		$penalizariSQL = mysql_query($penalizariSQL) or die(mysql_error());

		$penalizariDate = "";
		while($row = mysql_fetch_array($penalizariSQL))
		{
			//$data = explode('-',$row['data_scadenta']);
			//$dataScadenta = date('Y-m-d' ,mktime(0,0,0, $data[1], ($data[2] - $this->termenScadent - $this->termenPlata), $data[0]));

			//$penalizariDate .= "'".$dataScadenta."', ";

			$penalizariDate .= "'".$row['id_restanta']."', ";
		}

		//adaug si data protocolului
		//$protocolSQL = "SELECT data FROM `fisa_cont` WHERE `act`='Protocol' AND loc_id=".$this->locatarId;
		//$protocol = mysql_query($protocolSQL) or die("Nu pot afla data protocolului <br />".$protocolSQL."<br />".mysql_error());
		//if(mysql_num_rows($protocol)>=1)
		//	$penalizariDate .= "'".mysql_result($protocol,0,'data')."', ";

		$penalizariDate = substr($penalizariDate, 0, -2);
		//$penalizariDate = " AND `data` NOT IN (".$penalizariDate.")";
		$penalizariDate = " AND `id` NOT IN (".$penalizariDate.")";
		if (strlen($penalizariDate) == 19) {
			$penalizariDate = "";
		}

		$sql = "SELECT * FROM `fisa_cont` WHERE loc_id=".$this->locatarId." AND rest_plata>0 AND (`data` BETWEEN '".$start."' AND '".$end."')".$penalizariDate;
		$sql = mysql_query($sql) or die("Nu pot afla verifica daca trebuie calculate penalizari pt intrarile din fisa cont:".mysql_error());

		while($row = mysql_fetch_array($sql))
		{
			if ($row['act'] == 'Protocol')
				$explicatie = 'Protocol';
			else {
				$data = strtotime('-1 month', strtotime($row['data']));
				$explicatie = 'LP '.Util::lunaToString(date('m', $data)).' '.date('Y', $data);
			}
			$data = explode('-',$row['data']);

			$dataScadenta = date('Y-m-d' ,mktime(0,0,0, $data[1], ($data[2] + $this->termenScadent + $this->termenPlata), $data[0]));
			$insert = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
			(NULL, '".$this->asocId."', '".$this->scaraId."', '".$this->locatarId."', '".$explicatie."', '".$row['rest_plata']."', '".$dataScadenta."', NULL, '0', '".$this->procentPenalizare."', '0', ".$row['id'].", NULL)";

			 mysql_query($insert) or die("Nu am putut insera noua penalizare in BD <br />".mysql_error());
		}
	}

	public function platesteDebit($suma, $idChitanta) {

		$sql = "SELECT * FROM `fisa_cont` WHERE rest_plata>0 AND loc_id=".$this->locatarId." AND act IN ('LP', 'Protocol') ORDER BY `data` ASC";
		$sql = mysql_query($sql) or die ("Nu pot afla listele neplatite din fisa cont<br />".mysql_error());

		while(($row = mysql_fetch_array($sql)) && $suma != 0)
		{
			$update;

			$pen = $this->arePenalizari($row['id']);

			$date = explode('-', $row['data']);
			if ($pen != false)
				$date =  explode('-',$pen['data_scadenta']);
			$z1 = mktime(0,0,0, date('m'), date('j'), date('Y'));
			$z2 = mktime(0,0,0, $date[1], $date[2], $date[0]);
			$zile = ($z1 - $z2) / 86400;

			$valoare = $zile * ($this->procentPenalizare / 100)* $row['rest_plata'];


			if ($row['rest_plata'] <= $suma) {
				$suma -= $row['rest_plata'];
				$update = "UPDATE fisa_cont SET rest_plata=0 WHERE id=".$row['id'];

				if ($pen != false) {
					$update2 = "UPDATE fisa_pen SET data_platii='".date('Y-m-d')."', proc_pen='".$this->procentPenalizare."', nr_zile='".$zile."', val_pen='".round($valoare, 2)."', id_incasare='".$idChitanta."' WHERE fisapen_id=".$pen['fisapen_id'];
					mysql_query($update2) or die ("Nu pot actualiza sumele din fisa penalizare<br />".mysql_error());
				}
			}
			else {
				$rest = $row['rest_plata'] - $suma;
				$suma = 0;
				$update = "UPDATE fisa_cont SET rest_plata=".round($rest, 2)." WHERE id=".$row['id'];

				if ($pen != false) {
					$update2 = "UPDATE fisa_pen SET data_platii='".date('Y-m-d')."', proc_pen='".$this->procentPenalizare."', nr_zile='".$zile."', val_pen='".round($valoare, 2)."', id_incasare='".$idChitanta."' WHERE fisapen_id=".$pen['fisapen_id'];
					mysql_query($update2) or die ("Nu pot actualiza sumele din fisa penalizare<br />".mysql_error());

					$insert = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
			(NULL, '".$this->asocId."', '".$this->scaraId."', '".$this->locatarId."', '".$pen['luna']."', '".round($rest, 2)."', '".date('Y-m-d')."', NULL, '0', '".$this->procentPenalizare."', '0', '".$pen['id_restanta']."', NULL)";

					mysql_query($insert) or die("Nu am putut insera noua penalizare in BD pt suma ramasa <br />".mysql_error());
				}
			}

			 mysql_query($update) or die ("Nu pot actualiza sumele din fisa cont<br />".mysql_error());
		}

		if($suma > 0) { //a mai ramas o suma de bani care s-a platit in avans
			//$sql = "SELECT id, rest_plata FROM `fisa_cont` WHERE loc_id=".$this->locatarId." ORDER BY id DESC LIMIT 1";
			//$query = mysql_query($sql) or die ("Nu pot afla o inregistrare din fisa cont<br />".$sql."<br />".mysql_error());

			$sql = "UPDATE fisa_cont SET rest_plata=".round((mysql_result($query, 0, 'rest_plata') - $suma), 2)." WHERE id=".$idChitanta;
			mysql_query($sql) or die ("Nu pot afla actualiza restul de plata platit in avans <br />".$sql."<br />".mysql_error());
		}
		return $suma;
	}

	public function platestePenalizare($valoare, $idChitanta) {

		$insert = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
			(NULL, '".$this->asocId."', '".$this->scaraId."', '".$this->locatarId."', 'Plata penalizare', '0', '".date('Y-m-d')."', '".date('Y-m-d')."', '0', '".$this->procentPenalizare."', '".round(-$valoare, 2)."', ".$idChitanta.", ".$idChitanta.")";

		mysql_query($insert) or die("Nu am putut insera noua plata de penalizare in BD <br />".mysql_error());
	}

	public function arePenalizari($fisaContId) {
		$sql = "SELECT * FROM fisa_pen WHERE loc_id=".$this->locatarId." AND id_restanta=".$fisaContId." AND id_incasare IS NULL";
		$sql = mysql_query($sql) or die ("Nu pot afla daca fisa cont are penalizari<br />".mysql_error());

		return mysql_fetch_array($sql);
	/*
		$sql = "SELECT * FROM fisa_cont WHERE id=".$fisaContId;
		$sql = mysql_query($sql) or die ("Nu pot afla informatii despre inregistrarea din fisa cont<br />".mysql_error());

		$data = explode('-',mysql_result($sql,0,'data'));
		$dataScadenta = date('Y-m-d' ,mktime(0,0,0, $data[1], ($data[2] + $this->termenScadent + $this->termenPlata), $data[0]));

		$sql = "SELECT * FROM fisa_pen WHERE data_scadenta='".$dataScadenta."' AND locatar_id=".$this->locatarId;
		$sql = mysql_query($sql) or die ("Nu pot afla informatii despre inregistrarea din fisa penalizari<br />".mysql_error());
		if (mysql_num_rows($sql) == 0 )
			return false;

		$rezult = mysql_fetch_array($sql);

	  $istoricArray = $rezult['fisapen_id'];

		while($rezult['data_platii'] != null)
		{
			$sql = "SELECT * FROM fisa_pen WHERE data_scadenta='".$rezult['data_platii']."' AND locatar_id=".$this->locatarId." AND fisapen_id NOT IN (".$istoricArray.")" ;
			$sql = mysql_query($sql) or die ("Nu pot afla info despte urmatoare inregistrare a penalizarii <br />".mysql_error());
			$rezult = mysql_fetch_array($sql);
		  $istoricArray .= " ,".$rezult['fisapen_id'];
		}
		return $rezult;
	*/
	}

	public function getPenalizari() {
		$totalPenalizari = 0;

		$sql = "SELECT * FROM fisa_pen WHERE loc_id=".$this->locatarId;
		$sql = mysql_query($sql) or die ("Nu pot afla penalisarile acestei persoane<br />".mysql_error());

		if (mysql_num_rows($sql) == 0)
			return 0;

		while($row = mysql_fetch_array($sql)) {
			if ($row['data_platii'] != null) {
				$totalPenalizari += $row['val_pen'];
			}
			else {
				$date = explode('-', $row['data_scadenta']);
				$z1 = mktime(0,0,0, date('m'), date('j'), date('Y'));
				$z2 = mktime(0,0,0, $date[1], $date[2], $date[0]);
				$zile = round(($z1 - $z2) / 86400);

				$valoare = $zile * ($this->procentPenalizare / 100) * $row['valoare_debit'];
				$totalPenalizari += ($valoare);

			}
		}

		return $totalPenalizari;
	}

	public function getDatorii() {
		$sql = "SELECT * FROM fisa_cont WHERE rest_plata>0 AND loc_id=".$this->locatarId." AND act='LP' AND data<'".date('Y-m-d', mktime(0,0,0, date('m'), date('j')-$this->termenScadent-$this->termenPlata, date('Y')))."'";
		$sql = mysql_query($sql) or die ("Nu pot afla datoriile acestei persoane<br />".mysql_error());

		$sql_p="SELECT * FROM `fisa_cont` WHERE loc_id=".$this->locatarId." AND act='Protocol'";
		$sql_p = mysql_query($sql_p) or die ("Nu pot afla protocolul acestei persoane<br />".mysql_error());

		$totalDatorii = 0;

		if (mysql_num_rows($sql) == 0)
			return mysql_num_rows($sql_p)>0 ? mysql_result($sql_p, 0, 'rest_plata') + $this->getPlatiAvans() : $this->getPlatiAvans();

		while($row = mysql_fetch_array($sql)) {
			$totalDatorii += floatval($row['rest_plata']);
		}

		return mysql_result($sql_p, 0, 'rest_plata') + $totalDatorii + $this->getPlatiAvans();
	}

	public function getRestPlata() {
		$sql = "SELECT sum(rest_plata) as total FROM fisa_cont WHERE loc_id=".$this->locatarId;
		$sql = mysql_query($sql) or die ("Nu pot afla total rest de plata al acestei persoane<br />".mysql_error());

		return mysql_result($sql, 0, 'total');
	}

	public function getPlatiAvans() {
		$sql = "SELECT ifnull(sum(rest_plata),0) as avans FROM fisa_cont WHERE rest_plata<0 AND loc_id=".$this->locatarId;
		$sql = mysql_query($sql) or die ("Nu pot afla plata in avans acestei persoane<br />".mysql_error());

		return mysql_result($sql, 0, 'avans');
	}

	public static function schimbaProcentPenalizare($aroc_id, $procent) {

		$sql = "SELECT penalizare AS proc_pen FROM asociatii_setari WHERE asoc_id=".$aroc_id;
		$sql = mysql_query($sql) or die ("Nu pot afla detaliile despre asociatie<br />".mysql_error());;

		$procentPenalizare = floatval(mysql_result($sql,0,'proc_pen'));

		if ($procentPenalizare == $procent)
			return;

	  die('TREBUIE ACTUALIZATA FUNCTIA IN Penalizari.class.php schimbaProcentPenalizare() CU NOILE CAMPURI IN TABELA');

		$sql = "SELECT * FROM fisa_pen WHERE data_platii IS NULL AND asoc_id=".$aroc_id;
		$sql = mysql_query($sql) or die ("Nu pot afla penalizarile care apartin de aceasta asociatie<br />".mysql_error());

		while($row = mysql_fetch_array($sql)) {

			$date =  explode('-',$row['data_scadenta']);
			$z1 = mktime(0,0,0, date('m'), date('j'), date('Y'));
			$z2 = mktime(0,0,0, $date[1], $date[2], $date[0]);
			$zile = ($z1 - $z2) / 86400;

			$valoare = $zile * ($procentPenalizare / 100) * $row['valoare_debit'];

			$update = "UPDATE fisa_pen SET data_platii='".date('Y-m-d')."', proc_pen='".$procentPenalizare."', nr_zile='".$zile."', val_pen='".$valoare."',  WHERE fisapen_id=".$row['fisapen_id'];
			mysql_query($update) or die ("Nu pot actualiza sumele din fisa penalizare<br />".mysql_error());

			$insert = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `locatar_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`) VALUES
			(NULL, '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['locatar_id']."', '".$row['luna']."', '".$row['valoare_debit']."', '".date('Y-m-d')."', NULL, '0', '".$procent."', '0')";
			mysql_query($insert) or die("Nu am putut insera noua penalizare in BD <br />".mysql_error());
		}
	}
}