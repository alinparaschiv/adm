<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        var strArr=new Array();
</script>

<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////
	//***************  INSERARE CONSUMURI APA LUNA CURENTA  ***************//

	/** 		Verific pentru fiecare asociatie data termen
	 * 		de predare a citirilor pentru apometre.
	 * 		Daca am depasit un termen/este o tabela
	 * 		goala, inserez in ea datele pentru asociatie
	 * 		cu 0 si pastrez neschimbat doar campul de
	 * 		repetare consum
	 */
	$termenCitire = "SELECT * FROM asociatii_setari WHERE asoc_id=".$_GET['asoc_id'];
	$termenCitire = mysql_query($termenCitire) or die("#34 - Nu am putut selecta termenul de predare a citirilor<br />" . mysql_error());
	$dataAzi = date('d-m-Y');

        $lunaCurenta = date('m');
        $anuCurent = date('Y');

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

/////////////////////////////////////////////////////////////////////////////////////////////////////////


$asocId = $_GET['asoc_id'];
$i = 0;
$query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
$result = mysql_query($query) or die('Nu pot afla informatiile despre asoiatie <br />'.mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $id = $row['loc_id'];
    if (!isset($_POST['datorie_'.$id]) || $_POST['datorie_'.$id] == '') $i = 1;
    if (!isset($_POST['penalizare_'.$id]) || $_POST['penalizare_'.$id] == '') $i = 1;
	if (!isset($_POST['lista_'.$id]) || $_POST['lista_'.$id] == '') $i = 1;
}
if (!isset($_POST['data_protocol']) || $_POST['data_protocol'] == '') $i= 1;
if (!isset($_POST['data_lista']) || $_POST['data_lista'] == '') $i= 1;

if (isset($_POST['buton']) && $_POST['buton'] == 'apasat' && $i == 0) {
	$procentPenalizareSQL = "SELECT penalizare AS proc_pen, termen FROM asociatii_setari WHERE asoc_id='$asocId'";
	$procentPenalizareSQL = mysql_query($procentPenalizareSQL);
	$procentPenalizare = floatval(mysql_result($procentPenalizareSQL,0,'proc_pen'));


    $query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['loc_id'];
        if(ereg('[^0-9.-]', $_POST['datorie_'.$id])) {
              $i=1;
              $datorieErr[$id] = 'Campul "Datorie" pentru locatarul "'.$row['nume'].'" poate sa contina doar cifre.<br>';
        }
        if(ereg('[^0-9.-]', $_POST['penalizare_'.$id])) {
              $i=1;
              $penalizareErr[$id] = 'Campul "Penalizare" pentru locatarul "'.$row['nume'].'" poate sa contina doar cifre.<br>';
        }

    }

    if ($i==0) {
        if (strcmp($_POST['operatie'],"insert") == 0)
        {
            $query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $id = $row['loc_id'];
                $datorie = $_POST['datorie_'.$id];
                $penalizare = $_POST['penalizare_'.$id];
            	$lista = $_POST['lista_'.$id];
                $data = explode('/', $_POST['data_protocol']);
            	$data = date('Y-m-d' ,mktime(0,0,0, $data[0], $data[1], $data[2]));

            	$dataLista = explode('/', $_POST['data_lista']);
            	$dataLista = date('Y-m-d' ,mktime(0,0,0, $data[0], $data[1], $data[2]));

				//
				// Protocol
				//
				
            	$insertFisaCont = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
								   NULL , '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', '".$data."', '', 'Protocol', '".$datorie."', '0' , '".$penalizare."', '".($datorie + $penalizare)."', '".($datorie)."' );";
            	mysql_query($insertFisaCont) or die("A aparut o eroare cand am dorit sa inserez protocolul in fisa cont <br />".$insertFisaCont."<br />".mysql_error());

				$idProtocol = mysql_insert_id();
				
				
				//
				// Penalizare
				//

				$insertPenalizariTotale = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
								     	   (NULL, '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', 'Penalizari initiale', 0, '".$data."', '".$data."', '0', '0', '".$penalizare."', ".$idProtocol.", ".$idProtocol.");";

				mysql_query($insertPenalizariTotale) or die("A aparut o eroare cand am dorit sa inserez penalizarea initiala in fisa penalizari <br />".$insertPenalizariTotale."<br />".mysql_error());

				
            	if ($datorie != 0) {
					$insertPenalizariStart = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
									     	   (NULL, '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', 'Protocol', '".$datorie."', '".$data."', NULL, '0', '".$procentPenalizare."', '0', ".$idProtocol.", NULL);";

	            	mysql_query($insertPenalizariStart) or die("A aparut o eroare cand am dorit sa inserez protocolul in fisa penalizari pentru calcularea penalizarilor in continuare <br />".$insertPenalizariStart."<br />".mysql_error());
            	}
				
				//
				// LP
				//
				
				$insertFisaCont = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
								   NULL , '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', '".$data."', 'Lista de plata', 'LP', '".$lista."', '".$datorie."' , '".$penalizare."', '".($datorie + $penalizare + $lista)."', '".($lista)."' );";
            	mysql_query($insertFisaCont) or die("A aparut o eroare cand am dorit sa inserez prima lista de plata in fisa cont <br />".$insertFisaCont."<br />".mysql_error());

            	
			}
            $mesaj = '<font color="green">Datele au fost introduse.</font>';
            unset ($_POST);
        }else
        if (strcmp($_POST['operatie'],"update") == 0) {
        	$query = "SELECT L.nume, S.scara, L.etaj, L.ap, L.loc_id, L.scara_id, L.asoc_id,
			F1.valoare as lista, F1.datorie as datorie, F1.total_penalizari as penalizare,
			F1.data as DataLista, F2.data as DataProtocol
			FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id, fisa_cont F1, fisa_cont F2
			WHERE L.asoc_id='$asocId' AND L.loc_id=F1.loc_id AND F1.act='LP' AND F2.act='Protocol' AND L.loc_id=F2.loc_id
			ORDER BY L.loc_id ASC";
			$result = mysql_query($query) or die(mysql_query());
        	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	        	$id = $row['loc_id'];

        		$dataProtocol = explode('/', $_POST['data_protocol']);
        		$dataProtocol = date('Y-m-d' ,mktime(0,0,0, $dataProtocol[0], $dataProtocol[1], $dataProtocol[2]));

        		$dataLista = explode('/', $_POST['data_lista']);
        		$dataLista = date('Y-m-d' ,mktime(0,0,0, $dataProtocol[0], $dataProtocol[1], $dataProtocol[2]));

        		$update = false;
        		if ($_POST['datorie_'.$id] != $row['datorie']) $update = true;
        		if ($_POST['penalizare_'.$id] != $row['penalizare']) $update = true;
        		if ($_POST['lista_'.$id] != $row['lista']) $update = true;
				if ($dataProtocol != $row['DataProtocol']) $update = true;
        		if ($dataLista != $row['DataLista']) $update = true;

        		if ($update) {
        			$sql1 = "DELETE FROM fisa_cont WHERE loc_id=".$id;
        			$sql2 = "DELETE FROM fisa_pen WHERE loc_id=".$id;
        			mysql_query($sql1) or die("A aparut o eroare cand am dorit sa sterg vechile informatii ale locatarului curent din fisa cont<br />".mysql_error());
        			mysql_query($sql2) or die("A aparut o eroare cand am dorit sa sterg vechile informatii ale locatarului curent din fisa penalizari<br />".mysql_error());

        			$datorie = $_POST['datorie_'.$id];
        			$penalizare = $_POST['penalizare_'.$id];
        			$lista = $_POST['lista_'.$id];
        			$data = explode('/', $_POST['data_protocol']);
        			$data = date('Y-m-d' ,mktime(0,0,0, $data[0], $data[1], $data[2]));

        			$dataLista = explode('/', $_POST['data_lista']);
        			$dataLista = date('Y-m-d' ,mktime(0,0,0, $data[0], $data[1], $data[2]));

				//
				// Protocol
				//
				
            	$insertFisaCont = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
								   NULL , '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', '".$data."', '', 'Protocol', '".$datorie."', '0' , '".$penalizare."', '".($datorie + $penalizare)."', '".($datorie)."' );";
            	mysql_query($insertFisaCont) or die("A aparut o eroare cand am dorit sa inserez protocolul in fisa cont <br />".$insertFisaCont."<br />".mysql_error());
	
				$idProtocol = mysql_insert_id();
				
				
				//
				// Penalizare
				//

				$insertPenalizariTotale = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
								     	   (NULL, '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', 'Penalizari initiale', 0, '".$data."', '".$data."', '0', '0', '".$penalizare."', ".$idProtocol.", ".$idProtocol.");";

				mysql_query($insertPenalizariTotale) or die("A aparut o eroare cand am dorit sa inserez penalizarea initiala in fisa penalizari <br />".$insertPenalizariTotale."<br />".mysql_error());

				
            	if ($datorie != 0) {
					$insertPenalizariStart = "INSERT INTO `fisa_pen` (`fisapen_id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `valoare_debit`, `data_scadenta`, `data_platii`, `nr_zile`, `proc_pen`, `val_pen`, `id_restanta`, `id_incasare`) VALUES
									     	   (NULL, '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', 'Protocol', '".$datorie."', '".$data."', NULL, '0', '".$procentPenalizare."', '0', ".$idProtocol.", NULL);";

	            	mysql_query($insertPenalizariStart) or die("A aparut o eroare cand am dorit sa inserez protocolul in fisa penalizari pentru calcularea penalizarilor in continuare <br />".$insertPenalizariStart."<br />".mysql_error());
            	}
				
				//
				// LP
				//
				
				$insertFisaCont = "INSERT INTO `fisa_cont` (`id` ,`asoc_id` ,`scara_id` ,`loc_id` ,`data` ,`explicatie` ,`act` ,`valoare` ,`datorie` ,`total_penalizari` ,`total_general`, `rest_plata`)VALUES (
								   NULL , '".$row['asoc_id']."', '".$row['scara_id']."', '".$row['loc_id']."', '".$data."', 'Lista de plata', 'LP', '".$lista."', '".$datorie."' , '".$penalizare."', '".($datorie + $penalizare + $lista)."', '".($lista)."' );";
            	mysql_query($insertFisaCont) or die("A aparut o eroare cand am dorit sa inserez prima lista de plata in fisa cont <br />".$insertFisaCont."<br />".mysql_error());

				}


        	}
        	$mesaj = '<font color="green">Datele au fost actualizate.</font>';
        	unset ($_POST);
        }
	} else {
        $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
    }
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" align="left"><div id="maincon" style="width:600px;">
<?php
if ($link=='w_locatari_dat') {
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_locatari_dat&asoc_id='.$asocId.'">';
}

//afisez eventualele erori daca exista
echo '<div id="errorBox" style="">';
    if (!empty($datorieErr)) foreach ($datorieErr as $r) echo $r;
    if (!empty($penalizareErr)) foreach ($penalizareErr as $c) echo $c;
    if (!empty($locErr)) foreach ($locErr as $c) echo $c;
echo'</font></div><br />';
?>
<input type="hidden" name="buton" value="apasat" />
Asociatia:
<?php
      $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
      $result = mysql_query($query) or die('Nu am putut afla informatii despre asociatia curenta<br />'.msql_error());
      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
          echo $row['asociatie'];
      }

$m = 0;

$query = "SELECT L.*, S.scara, FC.valoare as lista, FC.datorie as datorie, FC.total_penalizari as penalizare, FC.data as data FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id, fisa_cont FC WHERE L.asoc_id='$asocId' AND L.loc_id=FC.loc_id AND FC.act<>'Protocol' ORDER BY L.loc_id ASC";
$result = mysql_query($query) or die(mysql_query());
$operatie = 'update';

if (mysql_num_rows($result) == 0) {
	$query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
	$result = mysql_query($query) or die(mysql_query());
	$operatie = 'insert';
}

echo '<input type="hidden" name="operatie" value="'.$operatie.'" />';
$scara = '';
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row['scara'] != $scara) {
		 if ($scara != '') echo '</table>';
		 $scara = $row['scara'];
		 echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:600px;" border=0>
		 <tr bgcolor="#19AF62">
		  <td><font size=2 color="white"><center>Etaj</center></font></td>
		  <td><font size=2 color="white"><center>Scara</center></font></td>
		  <td><font size=2 color="white"><center>Apartament</center></font></td>
		  <td><font size=2 color="white"><center>Nume</center></font></td>
		  <td><font size=2 color="white"><center>Datorie</center></font></td>
		  <td><font size=2 color="white"><center>Penalizare</center></font></td>
		  <td><font size=2 color="white"><center>Lista initiala</center></font></td>
		 </tr>';
	}
	$locId = $row['loc_id'];
	if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
	echo '<td ><center>'.$row['etaj'].'</center></td>';
	echo '<td ><center>'.$row['scara'].'</center></td>';
	echo '<td ><center>ap '.$row['ap'].'</center></td>';
	echo '<td width=100><center>'.$row['nume'].'</center></td>';
	echo '<td><input style="width:80px;" type="text" name="datorie_'.$locId.'" value="'.(isset($_POST['datorie_'.$locId]) ? $_POST['datorie_'.$locId] : (isset($row['datorie']) ? $row['datorie'] : '')).'"></td>';
	echo '<td><input style="width:80px;" type="text" name="penalizare_'.$locId.'" value="'.(isset($_POST['penalizare_'.$locId]) ? $_POST['penalizare_'.$locId] : (isset($row['penalizare']) ? $row['penalizare'] : '')).'"></td>';
	echo '<td><input style="width:80px;" type="text" name="lista_'.$locId.'" value="'.(isset($_POST['lista_'.$locId]) ? $_POST['lista_'.$locId] : (isset($row['lista']) ? $row['lista'] : '')).'" />';
	$m++;

}
?>
</table>
<br />
<?php
if ($operatie == 'update') {
	$sql = "SELECT F1.data lista, F2.data protocol FROM fisa_cont F1, fisa_cont F2 WHERE F1.loc_id=F2.loc_id AND F1.id<>F2.id AND F1.act='LP' AND F2.act='Protocol' AND F1.asoc_id=".$asocId;
	$sql = mysql_query($sql) or die("Nu pot afla data initiala a protocolului<br />".mysql_error());

	$dataLista = mysql_result($sql, 0, 'lista');
	$dataLista = explode('-', $dataLista);
	$dataLista = $dataLista[1].'/'.$dataLista[2].'/'.$dataLista[0];
	$dataProtocol = mysql_result($sql, 0, 'protocol');
	$dataProtocol = explode('-', $dataProtocol);
	$dataProtocol = $dataProtocol[1].'/'.$dataProtocol[2].'/'.$dataProtocol[0];
}
 ?>
<label for="data_protocol">Data preluari asociatiei:</label>
<input type="text" id="data_protocol" name="data_protocol" value="<?php echo ($operatie == 'insert') ? date('m/d/Y') : $dataProtocol ?>" />
<script> new tcal ({ 'formname': 'addForm', 'controlname': 'data_protocol' }); </script><br /><br />

<label for="data_lista">Data listei cu care a fost preluata asociatia:</label>
<input type="text" id="data_lista" name="data_lista" value="<?php echo ($operatie == 'insert') ? date('m/d/Y') : $dataLista ?>" />
<script> new tcal ({ 'formname': 'addForm', 'controlname': 'data_lista' }); </script><br /><br />

<table cellspacing=1 style="margin:20px 0 0 0px; width:230px;" border=0>
        <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
</table>
<?php

echo '<table><tr>';
   echo '   <td><div id="butonBack"><a href="index.php?link=w_locatari_apometre&asoc_id='.$asocId.'" style="">Pasul Anterior</a></div></td>
           <td><div id="buton"><a href="index.php?link=w_locatari_fond&asoc_id='.$asocId.'" style="">Pasul Urmator</a></div></td>';
   echo '</tr>
</table>';
?>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE apometre locatari ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=w_strazi&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit1.submit();
}
</script>

<?php
    $editLoc = mysql_real_escape_string($_GET['edit']);
    $i = 0;
    if ($_POST['salveaza'] == 'apasat') {
        $locId = mysql_real_escape_string($_POST['la']);

        $datorieT = $_POST['datorieT'];
        $penalizareT = $_POST['penalizareT'];
        $dataT = $_POST['calT'];

        if(ereg('[^0-9.]', $_POST['datorieT'])) {
              $i=1;
              $datorieErrT = 'Campul "Datorie" pentru locatarul ales poate sa contina doar cifre.<br>';
        }

        if(ereg('[^0-9.]', $_POST['penalizareT'])) {
              $i=1;
              $penalizareErrT = 'Campul "Penalizare" pentru locatarul ales poate sa contina doar cifre.<br>';
        }

        if ( $i == 0) {
                $query = "UPDATE locatari_datorii SET `datorie`='$datorieT', `penalizare`='$penalizareT', `data`='$dataT' WHERE loc_id='$locId'";
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
                $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);
        } else {
                $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }
    }
?>
</div>

