<script language="javascript">

/** TRANSFORMA CIFRELE IN LITERE **/
var MASCULIN = 0;
var FEMININ = 1;

var DeLa1La9InLitere_Masculin = new Array(9);
var DeLa1La9InLitere_Feminin = new Array(9);
var DeLa11La19InLitere = new Array(9);

function InLit(nr_pana_la_999, gen){
	var ce = nr_pana_la_999;
	if (ce < 0 || ce > 999)
	return "nr_pana_la_999 trebuie sa fie intre 0 si 999";

	if ((gen != MASCULIN) && (gen != FEMININ))
	return "parametrul gen nu poate fi decat 0 sau 1";

	var text = "";
	var sute = Math.floor(ce / 100);
	var zeci = Math.floor((ce - sute * 100) / 10);
	var unitati = Math.floor(ce - 100 * sute - 10 * zeci);



	if (sute != 0)
	text = DeLa1La9InLitere_Feminin[sute - 1] + (sute == 1 ? " suta " : " sute ");

	if (zeci == 1)
	{
		var sufix = DeLa11La19InLitere[unitati - 1];
		return text + ((unitati == 0) ? " zece " : sufix);
	}

	if (zeci > 1)
	{
		text += DeLa1La9InLitere_Feminin[zeci - 1] + " zeci ";
		if (unitati != 0)
		text += "si ";
	}

	if (unitati != 0)
	text += (gen == MASCULIN) ? DeLa1La9InLitere_Masculin[unitati - 1] : DeLa1La9InLitere_Feminin[unitati - 1];

	return text;


}

function Convert(Nr){
	var punct = 0;
	var invalid = false;

	for (var i = 0; i < Nr.length; i++){
		var ch = Nr.charAt(i);

		if (ch == ".")
		punct++;

		if (ch != "0" && ch != "1" && ch != "2" && ch != "3" && ch != "4" &&
		ch != "5" && ch != "6" && ch != "7" && ch != "8" && ch != "9" &&
		ch != "-" && ch != ".")
		invalid = true;
	}

	if (punct > 1 || invalid)
	return "Numar invalid";

	InitializeazaDeLa1La9InLitere();
	InitializeazaDeLa11La19InLitere();

	var litere = "";
	var minus = false;

	if (Nr < 0)
	{
		Nr = Math.abs(Nr);
		minus = true;
	}
	var miliarde = Math.floor(Nr / 1000000000);

	if (miliarde > 999)
	return "Numar prea mare - maxim: 999999999999";

	var milioane = Math.floor((Nr - miliarde * 1000000000) / 1000000);
	var mii = Math.floor((Nr - miliarde * 1000000000 - milioane * 1000000) / 1000);
	var lei = Math.floor(Nr - miliarde * 1000000000 - milioane * 1000000 - mii * 1000);
	var indexOfBani = Nr.indexOf(".");
	var bani = 0;


	if (indexOfBani != -1)
	bani = Nr.substr(indexOfBani + 1);

	if (miliarde != 0)
	{
		if (miliarde == 1)
		litere += "un miliard ";

		else
		{
			if (miliarde % 10 == 1)
			litere += InLit(miliarde, MASCULIN) + " miliarde ";
			else
			litere += InLit(miliarde, FEMININ) + " miliarde ";
		}
	}

	if (milioane != 0)
	{
		if (milioane == 1)
		litere += "un milion ";
		else
		{
			if (milioane % 10 == 1)
			litere += InLit(milioane, MASCULIN) + " milioane ";
			else
			litere += InLit(milioane, FEMININ) + " milioane ";
		}
	}

	if (mii != 0)
	{
		if (mii == 1)
		litere += "o mie ";
		else
		litere += InLit(mii, FEMININ) + " mii ";
	}

	if (lei != 0)
	litere += InLit(lei, MASCULIN);

	if (Math.floor(Nr) == 1)
	litere = "un leu";
	else
	if (Math.floor(Nr) > 0)
	litere += " lei";

	//daca avem mai multe cifre dupa ',', ne intereseaza doar primele 2
	if ((bani != 0) && (bani.length>2))
	bani = bani.substr(indexOfBani + 1,2);

	if (bani != 0)
	{
		if (Math.floor(Nr) > 0)
		{
			if (bani.length == 1)
			{
				if (bani == 1)
				litere += " si zece bani";
				else
				litere += " si " + InLit(bani, FEMININ) + " zeci bani";
			}
			else
			{
				if (bani == 1)
				litere += " si un ban";
				else
				litere += " si " + InLit(bani, MASCULIN) + " bani";
			}
		}
		else
		{
			if (bani.length == 1)
			{
				if (bani == 1)
				litere += "zece bani";
				else
				litere += InLit(bani, FEMININ) + " zeci bani";
			}
			else
			{
				if (bani == 1)
				litere += "un ban";
				else
				litere += InLit(bani, MASCULIN) + " bani";
			}
		}
	}


	var res = (minus == true ? "minus " : "") + litere;
	res = res.replace("doua zeci", "douazeci");
	res = res.replace("trei zeci", "treizeci");
	res = res.replace("patru zeci", "patruzeci");
	res = res.replace("cinci zeci", "cincizeci");
	res = res.replace("sase zeci", "saizeci");
	res = res.replace("sapte zeci", "saptezeci");
	res = res.replace("opt zeci", "optzeci");
	res = res.replace("noua zeci", "nouazeci");
	//return res;

	document.getElementById("adica").value = res;

}

function InitializeazaDeLa11La19InLitere() {
	DeLa11La19InLitere[0] = "unsprezece";
	DeLa11La19InLitere[1] = "doisprezece";
	DeLa11La19InLitere[2] = "treisprezece";
	DeLa11La19InLitere[3] = "paisprezece";
	DeLa11La19InLitere[4] = "cincisprezece";
	DeLa11La19InLitere[5] = "saisprezece";
	DeLa11La19InLitere[6] = "saptesprezece";
	DeLa11La19InLitere[7] = "optsprezece";
	DeLa11La19InLitere[8] = "nouasprezece";
}

function InitializeazaDeLa1La9InLitere(){
	DeLa1La9InLitere_Masculin[0] = "unu";
	DeLa1La9InLitere_Masculin[1] = "doi";
	DeLa1La9InLitere_Masculin[2] = "trei";
	DeLa1La9InLitere_Masculin[3] = "patru";
	DeLa1La9InLitere_Masculin[4] = "cinci";
	DeLa1La9InLitere_Masculin[5] = "sase";
	DeLa1La9InLitere_Masculin[6] = "sapte";
	DeLa1La9InLitere_Masculin[7] = "opt";
	DeLa1La9InLitere_Masculin[8] = "noua";

	DeLa1La9InLitere_Feminin[0] = "una";
	DeLa1La9InLitere_Feminin[1] = "doua";
	DeLa1La9InLitere_Feminin[2] = "trei";
	DeLa1La9InLitere_Feminin[3] = "patru";
	DeLa1La9InLitere_Feminin[4] = "cinci";
	DeLa1La9InLitere_Feminin[5] = "sase";
	DeLa1La9InLitere_Feminin[6] = "sapte";
	DeLa1La9InLitere_Feminin[7] = "opt";
	DeLa1La9InLitere_Feminin[8] = "noua";
}

function reprezinta(value){
	//alert("Functia reprezinta primeste: "+value);
	switch (value){
		case "1": document.getElementById('reprezinta').value= "Plata Intretinere";
			break;
		case "2": document.getElementById('reprezinta').value = "Plata Penalizare";
			break;
		case "3": document.getElementById('reprezinta').value = "Plata Fond Rulment";
			break;
		case "4": document.getElementById('reprezinta').value = "Plata Fond Reparatii";
			break;
		case "5": document.getElementById('reprezinta').value = "Plata Fonduri Speciale";
			break;
	}
}

/** SCHIMBA CULOAREA IN FUNCTIE DE PLATA **/
function culoare(value){
	//alert("Valoarea este: "+value);
	//alert("Functia culoare primeste: "+value);
	switch (value){
		case "1":
			document.getElementById('casierie').style.backgroundColor = "#FFFFFF";
			break;
		case "2": document.getElementById('casierie').style.backgroundColor = "#FF9999";
			break;
		case "3": document.getElementById('casierie').style.backgroundColor = "#99FFCC";
			break;
		case "4": document.getElementById('casierie').style.backgroundColor = "#99CCFF";
			break;
		case "5": document.getElementById('casierie').style.backgroundColor = "#CC99FF";
			break;
	}
	reprezinta(value);
}

function dezactiveazaButon(b) {
	b.disabled = true;
	return true;
}
</script>

<?php
include_once("modules/fise/Penalizare.class.php");

function verificaChitantaIntroducere($loc_id, $valoare, $reprezinta) {
	if(isset($_SESSION['chitanta_id'])) {
		$c_s = 'SELECT * FROM casierie WHERE id = '.$_SESSION['chitanta_id'];
		$c_q = mysql_query($c_s) or die(mysql_error().'<br />'.$c_s);

		if(mysql_num_rows($c_q) == 1) {
			$c_r = mysql_fetch_assoc($c_q);

			/*if(strtotime($c_r['data_inserarii']) >  (strtotime("now") - 3)){
				var_dump(strtotime($c_r['data_inserarii']));
				echo '<br />';
				var_dump(strtotime('-3 seconds', strtotime("now")));
				echo '<br />';
				var_dump(strtotime("now"));
				echo '<br />';
				return true;
			}*/

			$c_s = "SELECT * FROM casierie WHERE loc_id=$loc_id AND data_inserarii>'".date('Y-m-d')."' AND suma=$valoare AND reprezentand='".$reprezinta."'";
			$c_q = mysql_query($c_s) or die(mysql_error().'<br />'.$c_s);

			if(mysql_num_rows($c_q) > 0) {
				var_dump($c_s);
				echo '<br />';
				var_dump(mysql_fetch_assoc($c_q));
				echo '<br />';
				return true;
			}
				
		}
	}
	return false;
}

if ($_POST['buton'] == 'apasat') {
	if (($_POST['select'] != '') && ($_POST['textfield'] != '') && ($_POST['adica'] != '') && ($_POST['reprezinta'] != '')){
		

		$numar_initial = 000000001;
		$serie_initial = "URB";

		$sql = "Select Max(chitanta_nr) as chitanta_nr, chitanta_serie From casierie Where chitanta_serie = 'URB' And chitanta_nr Is Not Null Group By chitanta_serie";
		$sql = mysql_query($sql) or die ("Nu pot sa ma conectez la casierie <br />".mysql_error());
		if (mysql_num_rows($sql)!=0) {
			$serie = mysql_result($sql, 0, 'chitanta_serie');
			$numar = mysql_result($sql, 0, 'chitanta_nr');
			$numar ++;
		}
		else
		{
			$numar = $numar_initial;
			$serie = $serie_initial;
		}

		$loc_id = $_GET['loc_id'];
		$scara_id = $_GET['scara_id'];
		$asoc_id = $_GET['asoc_id'];
		$valoare = $_POST['textfield'];	 //suma pe care o plateste omuletul nostru
		$adica = $_POST['adica'];
		$reprezinta = $_POST['reprezinta']." ".$_POST['luna'];
		$casier_id = $_SESSION['rank'];
		$data = date('Y-m-d')." ".date('H:m:s');
		$ip = $_SERVER['REMOTE_ADDR'];
		$data1 = date('m/d/Y');

		if (verificaChitantaIntroducere($loc_id, $valoare, $reprezinta)) {
			die('Nu poate fi introdusa doua chitanta la un interval mai mic de 3 secunde sau 2 chitante identice');
		}

		switch ($_POST['select']){
			case 1: $tip_plata = "Intretinere";

				$penalizare = new Penalizare($loc_id, $scara_id, $asoc_id);

				$dat = round($penalizare->getRestPlata(), 2);
				$pen = round($penalizare->getPenalizari(), 2);
				$tot = 0;

				$sql = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
					NULL , '".$asoc_id."', '".$scara_id."', '".$loc_id."', '".date('Y-m-d')."', 'plata intretinere', '".$serie."/".$numar."', '".round($valoare, 2)."', '".$dat."' , '".$pen."', '".$tot."', '0' );";
				mysql_query($sql) or die ("Nu pot insera in fisa cont <br />".mysql_error());

				$idPlataIntretinere = mysql_insert_id();

				$penalizare->platesteDebit($valoare, $idPlataIntretinere);

				$dat = $penalizare->getRestPlata();

				$update_sql = "UPDATE fisa_cont SET datorie=".round($dat, 2)." , total_general=".round($dat + $pen, 2)." WHERE id=".$idPlataIntretinere.'';
				mysql_query($update_sql) or die ("Nu pot actualiza in fisa_cont datoria corespunzatoare platii de intretinere <br />".$update_sql." <br />".mysql_error());
				break;

			case 2: $tip_plata = "Penalizare";
				$penalizare = new Penalizare($loc_id, $scara_id, $asoc_id);


				$dat = round($penalizare->getRestPlata(), 2);
				$pen = round($penalizare->getPenalizari() - $valoare, 2);
				$tot = $dat + $pen;

				$sql = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
				NULL , '".$asoc_id."', '".$scara_id."', '".$loc_id."', '".date('Y-m-d')."', 'plata penalizare', '".$serie."/".$numar."', '".round($valoare, 2)."', '".round($dat, 2)."' , '".round($pen, 2)."', '".round($tot, 2)."', '0' );";
				mysql_query($sql) or die ("Nu pot insera in fisa cont <br />".mysql_error());

				$penalizare->platestePenalizare($valoare, mysql_insert_id());

				Util::regularizare_conturi(null, null, $loc_id);
				break;

			case 3: $tip_plata = "Fond Rulment";
				$sql = "SELECT * FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
				$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());
				$dataFacturii = mysql_result($sql, 0, 'data');

				$lunaNoua = false;
				$dataFacArray = explode("-", $dataFacturii);
				if ($dataFacArray[1] < date('Y') || ($dataFacArray[1] == date('Y') && $dataFacArray[0] < date('m')))
					$lunaNoua = true;  //inseamna ca a trecut cel putin o luna de la ultima intrare in fisa facturi;


				$sumaDePlata = mysql_result($sql, 0, 'fond_rul_rest');				//suma pe care o are de plata
				$sumaDePanaAcum = mysql_result($sql, 0, 'fond_rul_incasat');		//suma pe care a achitat-o pana acum
				$restante = mysql_result($sql, 0, 'restante');						//restante

				if ($valoare > $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare == $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare < $restante){
					$sumaDePlata = $sumaDePlata - $valoare;
					$restante = $restante - $valoare;
					$newSumaDePlata = $sumaDePlata;
				}

				$newSumaDePanaAcum = $sumaDePanaAcum + $valoare;					//adaug la suma platita pana acum, suma pe care o achita

				if ($lunaNoua) {
					$luna = date("m-Y");
					$asocId = mysql_result($sql, 0, 'asoc_id');
					$scaraId = mysql_result($sql, 0, 'scara_id');
					$locId = mysql_result($sql, 0, 'loc_id');

					$fondRulIncasat = $valoare;
					$fondRepIncasat = 0;
					$fondSpecIncasat = 0;

					$fondRulRest = $newSumaDePlata;
					$fondRepRest = mysql_result($sql, 0, 'fond_rep_rest');
					$fondSpecRest = mysql_result($sql, 0, 'fond_spec_rest');
					$fondPenConst = mysql_result($sql, 0, 'fond_pen_constituit');
					$luna_trecuta = $restante;

					$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', '$locId', '$fondRulIncasat', '$fondRulRest', '$fondRepIncasat', '$fondRepRest', '$fondSpecIncasat', '$fondSpecRest', 0, '$fondPenConst', 0, '$luna_trecuta')";
					$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());

					$locatari = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id<>".$locId;
					$locatari = mysql_query($locatari) or die("Nu pot selecta locatarii<br />".mysql_error());

					while ($amLocatar = mysql_fetch_array($locatari)) {
						$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
						$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

						$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
						$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
						$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
						$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
						$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;

						$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
						$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
					}

				}
				else
				{
					$sql = "UPDATE fisa_fonduri SET fond_rul_incasat=".$newSumaDePanaAcum.", fond_rul_rest=".$newSumaDePlata.", restante=".$restante." WHERE loc_id=".$loc_id." AND scara_id=".$scara_id." AND asoc_id=".$asoc_id." AND data='".$dataFacturii."'";
					$sql = mysql_query($sql) or die ("Nu pot efectua modificarile pentru fondul de rulment<br />". mysql_error());
				}
				break;

			case 4: $tip_plata = "Fond Reparatii";
				$sql = "SELECT * FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
				$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());
				$dataFacturii = mysql_result($sql, 0, 'data');

				$lunaNoua = false;
				$dataFacArray = explode("-", $dataFacturii);
				if ($dataFacArray[1] < date('Y') || ($dataFacArray[1] == date('Y') && $dataFacArray[0] < date('m')))
					$lunaNoua = true;  //inseamna ca a trecut cel putin o luna de la ultima intrare in fisa facturi;


				$sumaDePlata = mysql_result($sql, 0, 'fond_rep_rest');				//suma pe care o are de plata
				$sumaDePanaAcum = mysql_result($sql, 0, 'fond_rep_incasat');		//suma pe care a achitat-o pana acum
				$restante = mysql_result($sql, 0, 'restante');						//restante

				if ($valoare > $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare == $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare < $restante){
					$restante = $restante - $valoare;
					$sumaDePlata = $sumaDePlata - $valoare;
					$newSumaDePlata = $sumaDePlata;
				}

				$newSumaDePanaAcum = $sumaDePanaAcum + $valoare;					//adaug la suma platita pana acum, suma pe care o achita

				if ($lunaNoua) {
					$luna = date("m-Y");
					$asocId = mysql_result($sql, 0, 'asoc_id');
					$scaraId = mysql_result($sql, 0, 'scara_id');
					$locId = mysql_result($sql, 0, 'loc_id');

					$fondRulIncasat = 0;
					$fondRepIncasat = $valoare;
					$fondSpecIncasat = 0;

					$fondRulRest = mysql_result($sql, 0, 'fond_rul_rest');
					$fondRepRest = $newSumaDePlata;
					$fondSpecRest = mysql_result($sql, 0, 'fond_spec_rest');
					$fondPenConst = mysql_result($sql, 0, 'fond_pen_constituit');
					$luna_trecuta = $restante;

					$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', '$locId', '$fondRulIncasat', '$fondRulRest', '$fondRepIncasat', '$fondRepRest', '$fondSpecIncasat', '$fondSpecRest', 0, '$fondPenConst', 0, '$luna_trecuta')";
					$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());

					$locatari = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id<>".$locId;
					$locatari = mysql_query($locatari) or die("Nu pot selecta locatarii<br />".mysql_error());

					while ($amLocatar = mysql_fetch_array($locatari)) {
						$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
						$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

						$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
						$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
						$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
						$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
						$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;

						$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
						$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
					}
				}
				else
				{
					$sql = "UPDATE fisa_fonduri SET fond_rep_incasat=".$newSumaDePanaAcum.", fond_rep_rest=".$newSumaDePlata.", restante=".$restante." WHERE loc_id=".$loc_id." AND scara_id=".$scara_id." AND asoc_id=".$asoc_id." AND data='".$dataFacturii."'";
					$sql = mysql_query($sql) or die ("Nu pot efectua modificarile pentru fondul de reparatii<br />". mysql_error());
				}
				break;

			case 5: $tip_plata = "Fonduri Speciale";
				$sql = "SELECT * FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
				$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());
				$dataFacturii = mysql_result($sql, 0, 'data');

				$lunaNoua = false;
				$dataFacArray = explode("-", $dataFacturii);
				if ($dataFacArray[1] < date('Y') || ($dataFacArray[1] == date('Y') && $dataFacArray[0] < date('m')))
					$lunaNoua = true;  //inseamna ca a trecut cel putin o luna de la ultima intrare in fisa facturi;


				$sumaDePlata = mysql_result($sql, 0, 'fond_spec_rest');				//suma pe care o are de plata
				$sumaDePanaAcum = mysql_result($sql, 0, 'fond_spec_incasat');		//suma pe care a achitat-o pana acum
				$restante = mysql_result($sql, 0, 'restante');						//restante

				if ($valoare > $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare == $restante){
					$restante = 0;
					$newSumaDePlata = $sumaDePlata - $valoare;
				}

				if ($valoare < $restante){
					$restante = $restante - $valoare;
					$sumaDePlata = $sumaDePlata - $valoare;
					$newSumaDePlata = $sumaDePlata;
				};
				$newSumaDePanaAcum = $sumaDePanaAcum + $valoare;					//adaug la suma platita pana acum, suma pe care o achita

				if ($lunaNoua) {
					$luna = date("m-Y");
					$asocId = mysql_result($sql, 0, 'asoc_id');
					$scaraId = mysql_result($sql, 0, 'scara_id');
					$locId = mysql_result($sql, 0, 'loc_id');

					$fondRulIncasat = 0;
					$fondRepIncasat = 0;
					$fondSpecIncasat = $valoare;

					$fondRulRest = mysql_result($sql, 0, 'fond_rul_rest');
					$fondRepRest = mysql_result($sql, 0, 'fond_rep_rest');
					$fondSpecRest = $newSumaDePlata;
					$fondPenConst = mysql_result($sql, 0, 'fond_pen_constituit');
					$luna_trecuta = $restante;

					$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', '$locId', '$fondRulIncasat', '$fondRulRest', '$fondRepIncasat', '$fondRepRest', '$fondSpecIncasat', '$fondSpecRest', 0, '$fondPenConst', 0, '$luna_trecuta')";
					$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());

					$locatari = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id<>".$locId;
					$locatari = mysql_query($locatari) or die("Nu pot selecta locatarii<br />".mysql_error());

					while ($amLocatar = mysql_fetch_array($locatari)) {
						$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
						$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

						$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
						$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
						$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
						$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
						$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;

						$insert = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
						$insert = mysql_query($insert) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
					}
				}
				else
				{
					$sql = "UPDATE fisa_fonduri SET fond_spec_incasat=".$newSumaDePanaAcum.", fond_spec_rest=".$newSumaDePlata.", restante=".$restante." WHERE loc_id=".$loc_id." AND scara_id=".$scara_id." AND asoc_id=".$asoc_id." AND data='".$dataFacturii."'";
					$sql = mysql_query($sql) or die ("Nu pot efectua modificarile pentru fondul special<br />". mysql_error());
				}
				break;
		}



		$sql = "INSERT INTO casierie VALUES (null, '$asoc_id', '$scara_id', '$loc_id', '$serie', '$numar', '$valoare', '$tip_plata', '$reprezinta', '$data', '$casier_id', '$ip')";
		$sql = mysql_query($sql) or die("Nu am putut salva factura in Baza de Date<br />".mysql_error());

		$_SESSION['chitanta_id'] = mysql_insert_id();

		//echo $_POST['select']." <--> ".$loc_id." <--> ".$scara_id." <--> ".$asoc_id." <--> ".$valoare." <--> ".$adica." <--> ".$reprezinta." <--> ".$data." <--> ".$ip;
		if (isset($_POST['checkbox'])){
			echo '<script language="javascript">document.location.href="/app/modules/casierie/chitanta.php";</script>';
		}
		else
		{
			echo "Chitanta nu va fi printata";
		}
	}
	else
	{
		echo "Nu ati completat toate campurile";
	}
}

$nr_initial = 000001;
$serie_initial = "URB";

$suma_de_plata = 0;

$data = date("m/d/Y");

$id_asociatie = $_GET['asoc_id'];
$id_scara = $_GET['scara_id'];
$id_locatar = $_GET['loc_id'];

function get_nume($loc_id){
	$sql = "SELECT nume FROM locatari WHERE loc_id=".$loc_id;
	$sql = mysql_query($sql) or die ("Nu pot accesa baza de date!<br />".mysql_error());

	echo mysql_result($sql, 0, 'nume');
}

function get_adresa($loc_id, $scara_id){
	$sql = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.etaj, locatari.ap,
			scari.scara, scari.nr, scari.bloc,
			strazi.strada
		FROM locatari, scari, strazi
		WHERE loc_id=".$loc_id." AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id";

	$sql = mysql_query($sql) or die("Nu gasesc locatarul <br />".mysql_error());

	while($row = mysql_fetch_assoc($sql)) {
		echo 'Str. '.$row['strada'].', Nr. '.$row['nr'].', Bloc: '.$row['bloc'].' Et: '.$row['etaj'].' Ap: '.$row['ap'];
	}
}

function get_asociatie($asoc_id){
	$sql = "SELECT * FROM asociatii WHERE asoc_id=".$asoc_id;
	$sql = mysql_query($sql) or die ("Nu gasesc asociatia <br />".mysql_error());

	echo mysql_result($sql,0, 'asociatie');
}

function get_chitanta($serie_initial, $nr_initial){
	$sql = "Select Max(chitanta_nr) as chitanta_nr, chitanta_serie From casierie Where chitanta_serie = 'URB' And chitanta_nr Is Not Null Group By chitanta_serie";
	//$sql = "SELECT * FROM casierie WHERE chitanta_serie='URB' AND chitanta_nr IS NOT NULL ORDER by id DESC";
	$sql = mysql_query($sql) or die ("Nu pot sa ma conectez la chitante <br />".mysql_error());

	//daca avem deja date in tabela
	if (mysql_num_rows($sql)!=0) {
		$serie = mysql_result($sql, 0, 'chitanta_serie');
		$numar = mysql_result($sql, 0, 'chitanta_nr');
		$numar ++;
		echo '<strong>Chitanta Seria:</strong> '.$serie.' / <strong>Nr.</strong> '.$numar;
	}
	else
		//daca nu avem date in tabela
	{
		echo '<strong>Chitanta Seria:</strong> '.$serie_initial.' / <strong>Nr.</strong> '.$nr_initial;
	}
}

function get_datorie($loc_id, $scara_id, $asoc_id){
	$pen = new Penalizare($loc_id, $scara_id, $asoc_id);
	return round ($pen->getDatorii(), 2);
}

function get_penalizare($loc_id, $scara_id, $asoc_id){
	$pen = new Penalizare($loc_id, $scara_id, $asoc_id);
	return round ($pen->getPenalizari(), 2);
}

function get_fond_reparatii($loc_id){
	$sql = "SELECT fond_rep_rest FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
	$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());

	if (mysql_num_rows($sql) != 0){
		$fond_rep_rest = mysql_result($sql, 0, 'fond_rep_rest');
		return round($fond_rep_rest, 2);
	}

	return 0;
}
function get_fond_rulment($loc_id){
	$sql = "SELECT fond_rul_rest FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
	$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());

	if (mysql_num_rows($sql) != 0){
		$fond_rul_rest = mysql_result($sql, 0, 'fond_rul_rest');
		return round($fond_rul_rest, 2);
	}

	return 0;
}
function get_fonduri_spciale($loc_id){
	$sql = "SELECT fond_spec_rest FROM fisa_fonduri WHERE loc_id=".$loc_id." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC";
	$sql = mysql_query($sql) or die("Nu pot accesa tabela<br />".mysql_error());

	if (mysql_num_rows($sql) != 0){
		$fond_spec_rest = mysql_result($sql, 0, 'fond_spec_rest');
		return round($fond_spec_rest, 2);
	}

	return 0;
}
function get_total_de_plata($loc_id){
	$rest = "SELECT sum(rest_plata) as rest FROM `fisa_cont` WHERE loc_id=".$loc_id;
	$rest = mysql_query($rest) or die ("Nu pot afla restul sumei de platit <br />".mysql_error());
	$rest = mysql_result($rest, 0, 'rest');

	return round($rest, 2);
}

?>
<div align="left">
<form method="post" onsubmit="document.getElementById('register').disabled = true">
<input type="hidden" name="buton" value="apasat" />
<table width="950" style="align:left; top:250px; background-color:white;" id="casierie">
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Nume si Prenume:</strong></td>
		<td bgcolor="#EEEEEE"><?php get_nume($id_locatar) ?></td>
		<td rowspan="3" colspan="2"><div align="center">
			<?php get_chitanta($serie_initial, $nr_initial) ?><br />
            <?php echo $data; ?></div>
       	</td>
	</tr>
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Adresa:</strong></td>
		<td bgcolor="#EEEEEE"><?php get_adresa($id_locatar, $id_strada) ?></td>
	</tr>
	<tr>
		<td style="color:#FFFFFF;" bgcolor="#666666"><strong>Asociatie:</strong></td>
		<td bgcolor="#EEEEEE"><?php get_asociatie($id_asociatie) ?></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong><a href="index.php?link=fisa_cont<?php echo '&asoc_id='.$id_asociatie.'&scara_id='.$id_scara.'&locatar='.$id_locatar ; ?>" target="_blank">Intretinere</a> (<?php echo get_total_de_plata($id_locatar)-get_datorie($id_locatar, $id_scara, $id_asociatie); ?>):</strong></td>
		<td ><?php echo get_total_de_plata($id_locatar); ?></td>
		<td bgcolor="#CCCCCC"><strong>Tip Plata:</strong></td>
		<td >
        	<select name="select" id="select" onchange="culoare(this.value)">
              <option value="1">Intretinere</option>
              <option value="2">Penalizare</option>
              <option value="3">Fond Rulment</option>
              <option value="4">Fond Reparatii</option>
              <option value="5">Fonduri Speciale</option>
            </select>
    	</td>
	</tr>
	<tr>
		<td bgcolor="#EEEEEE"><strong><a href="index.php?link=fisa_pen<?php echo '&asoc_id='.$id_asociatie.'&scara_id='.$id_scara.'&locatar='.$id_locatar ; ?>" target="_blank">Penalizari:</a></strong></td>
		<td ><?php echo get_penalizare($id_locatar, $id_scara, $id_asociatie) ?></td>

        <td bgcolor="#EEEEEE"><strong>Valoare:</strong></td>
        <td ><input type="text" size="50" name="textfield" id="textfield" onkeyup="Convert(this.value)" /></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCCC"><strong>Fond Reparatii:</strong></td>
		<td ><?php echo get_fond_reparatii($id_locatar) ?></td>

        <td bgcolor="#CCCCCC"><strong>Adica:</strong></td>
        <td ><input name="adica" type="text" id="adica" size="50" readonly="readonly" /></td>
	</tr>
	<tr>
		<td bgcolor="#EEEEEE"><strong>Fond Rulment:</strong></td>
		<td ><?php echo get_fond_rulment($id_locatar) ?></td>

        <td bgcolor="#EEEEEE"><strong>Reprezentand:</strong></td>
        <td >
        	<input type="text" size="20" name="reprezinta" id="reprezinta" readonly="readonly" value="Plata Intretinere"/>
        	<input type="text" size="25" name="luna" />
        </td>
	</tr>
    <tr>
		<td bgcolor="#CCCCCC"><strong>Fonduri Speciale:</strong></td>
		<td ><?php echo get_fonduri_spciale($id_locatar) ?></td>

        <td >&nbsp;</td>
        <td >&nbsp;</td>
	</tr>
    <tr>
    	<td bgcolor="#EEEEEE"><strong>Total de Plata:</strong></td>
        <td ><?php echo (get_total_de_plata($id_locatar)+get_penalizare($id_locatar, $id_scara, $id_asociatie)+get_fond_reparatii($id_locatar)+get_fond_rulment($id_locatar)+get_fonduri_spciale($id_locatar)); ?></td>

        <td >&nbsp;</td>
        <td >&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="#CCCCCC"><strong>Printeaza</strong></td>
   	  	<td > <input type="checkbox" name="checkbox" id="checkbox" checked="checked" />

    <td colspan="2"><div align="right"><a href="index.php?link=casierie">Inapoi</a> | <input type="submit" id="register" name="inregistreaza" value="Înregistrează" /> </div></td>
    </tr>
</table>
</form>
</div>