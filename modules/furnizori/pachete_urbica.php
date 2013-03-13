<?php
/*
	update automat nr apartamente
	modificare factura
*/
/************************** FACTURARE PROPRIU-ZISA **************************/
if ($_POST['factureaza'] == "OK"){ 
	$lunaCur = date('m-Y');
	$lunaUrm = date('m-Y', mktime(0, 0, 0, date('m')+1, 1, date('Y')));
	
	foreach ($_POST as $asoc => $val){
		if ($asoc != "factureaza"){
			$updateLunaCurr = "UPDATE facturare_urbica SET `stare`=1 WHERE asoc_id=".$val." AND luna='".$lunaCur."'";
			$updateLunaCurr = mysql_query($updateLunaCurr) or die ("Nu pot updata situatia pe luna curenta<br />".mysql_error());
		}
	}
}

/************************** ADAUG ASOCIATIILE INEXISTENTE **************************/
	$lunaCurenta = date('m-Y');

	$putData = "SELECT * FROM asociatii";
	$putData = mysql_query($putData) or die ("Nu pot selecta asociatiile<br />".mysql_error());
	
	while ($asociatie = mysql_fetch_array($putData)){
		$checkInFacturare = "SELECT * FROM facturare_urbica WHERE asoc_id=".$asociatie['asoc_id']." AND luna='".$lunaCurenta."'";
		$checkInFacturare = mysql_query($checkInFacturare) or die ("Nu pot selecta asociatia din facturari<br />".mysql_error());

		if (mysql_num_rows($checkInFacturare) == 0){
			$setareAsoc = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asociatie['asoc_id'];
			$setareAsoc = mysql_query($setareAsoc) or die ("Nu pot accesa setarile asociatiei<br />".mysql_error());
			
			$nrApartamente = "SELECT COUNT(*) FROM locatari WHERE asoc_id=".$asociatie['asoc_id']." AND ap_locuit=1";
			$nrApartamente = mysql_query($nrApartamente) or die ("Nu pot afla numarul de apartamente locuite<br />".mysql_error());
			
			$nrApartamente = mysql_result($nrApartamente, 0, 'COUNT(*)');
			
			$putAsociatie = "INSERT INTO facturare_urbica VALUES (null, ".$asociatie['asoc_id'].", ".$nrApartamente.", '".$lunaCurenta."', 0, ".mysql_result($setareAsoc, 0, 'pachet_id').", ".mysql_result($setareAsoc, 0, 'data_factura').", ".mysql_result($setareAsoc, 0, 'factura_auto').")";
			$putAsociatie = mysql_query($putAsociatie) or die ("Nu pot insera asociatia in facturare Urbica<br />".mysql_error());
		}
	}
	
	//La fiecare Rulare verific numarul de apartamente locuite
	$putData = "SELECT * FROM asociatii";
	$putData = mysql_query($putData) or die ("Nu pot selecta asociatiile<br />".mysql_error());

	while ($asociatie = mysql_fetch_array($putData)){
		$apLocuiteAzi = "SELECT COUNT(*) FROM locatari WHERE asoc_id=".$asociatie['asoc_id']." AND ap_locuit=1";
		$apLocuiteAzi = mysql_query($apLocuiteAzi) or die ("Nu pot afla numarul de apartamente locuite<br />".mysql_error());
		
		$apLocuiteAzi = mysql_result($apLocuiteAzi, 0, 'COUNT(*)');
		
		$updatez = "UPDATE facturare_urbica SET `nr_ap`='$apLocuiteAzi' WHERE asoc_id=".$asociatie['asoc_id']." AND luna='".$lunaCurenta."'";
		$updatez = mysql_query($updatez) or die ("Nu pot updata numarul de apartamente<br />".mysql_error());
	}
	
/************************** CONTENTUL TABELULUI **************************/
	function putContent(){
		$lunaCurenta = date('m-Y');
		
		$afisAsociatii = "SELECT * FROM facturare_urbica WHERE luna='".$lunaCurenta."'";
		$afisAsociatii = mysql_query($afisAsociatii) or die ("Nu pot accesa facturare_urbica<br />".mysql_error());
		
		$i = 0;
		$deFacturat = 0;
		while ($lunaAsta = mysql_fetch_array($afisAsociatii)){
			
			$detaliiAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$lunaAsta['asoc_id'];
			$detaliiAsoc = mysql_query($detaliiAsoc) or die ("Nu pot afla detaii despre asociatie<br />".mysql_error());
			
			$AdrStr = "SELECT * FROM strazi WHERE str_id=".mysql_result($detaliiAsoc, 0, 'str_id');
			$AdrStr = mysql_query($AdrStr) or die ("Nu pot afla numele strazii<br />".mysql_error());
			$strada = mysql_result($AdrStr, 0, 'strada');
			
			$numeAsoc = mysql_result($detaliiAsoc, 0, 'asociatie');
			$adresaAsoc = "Strada ".$strada.", numar ".mysql_result($detaliiAsoc, 0, 'nr').", bloc ".mysql_result($detaliiAsoc, 0, 'bloc').", scara ".mysql_result($detaliiAsoc, 0, 'scara').", ".mysql_result($detaliiAsoc, 0, 'oras');
		
			if ($i % 2 == 0) { $culoare = "#DDDDDD"; } else { $culoare = "#FFFFFF"; }
			echo '<tr bgcolor="'.$culoare.'">';
			
				if ($lunaAsta['stare'] == 0) {
					echo '<td><input type="checkbox" name="asoc'.$i.'" value="'.$lunaAsta['asoc_id'].'" class="debifat" /></td>';
					$deFacturat++;
				} else {
					echo '<td>&nbsp;</td>';
				}
				
				echo '<td align="left">'.$numeAsoc.'</td>';
				echo '<td align="left">'.$adresaAsoc.'</td>';
				echo '<td>'.$lunaAsta['nr_ap'].'</td>';
				
				if ($lunaAsta['tip_facturare'] == 1){
					echo '<td>Da</td>';
				} else {
					echo '<td>Nu</td>';
				}
				
				if ($lunaAsta['stare'] == 0) {
					echo '<td>Pending</td>';
				} else {
					echo '<td>Facturat</td>';
				}
			echo '</tr>';
			$i++;
		}
		echo '<input type="hidden" name="maxval" value="'.($i+1).'" />';
		if ($deFacturat != 0){
			echo '<tr align="left">';
				echo '<td colspan="2"><a id="clicker">ChecK All</a></td>';
				echo '<td colspan="4" align="right"><input type="submit" value="Factureaza" /></td>';
			echo '</tr>';
		}
	}
?>


<form action="" method="post">
<input type="hidden" name="factureaza" value="OK" />
<table width="450px">
	<tr>
		<td bgcolor="#DDDDDD">Delegat</td>
		<td bgcolor="#DDDDDD"><select name="delegat">
				<?php
					$del = "SELECT * FROM admin WHERE user_id='6'";
					$del = mysql_query($del) or die ("Nu pot selecta userii care voi avea relatii cu furnizorii<br />".mysql_error());
					
					while ($maiMulti = mysql_fetch_array($del)){
						echo '<option value="'.$maiMulti['id'].'">'.$maiMulti['nume'].'</option>';
					}
				?>
			</select>
		</td>
	</tr>
</table>
<br clear="left" />
<table width="750px" bgcolor="#BBBBBB">
	<tr bgcolor="#000000" style="color:#FFFFFF">
		<td width="20px">&nbsp;</td>
		<td>Asociatie</td>
		<td>Adresa</td>
		<td width="100px">Nr Apartamente</td>
		<td width="100px">Facturare Automata</td>
		<td width="50px">Stare</td>		
	</tr>
	
	<?php putContent(); ?>

</table>
</form>

<?php 
switch (date('m')){
	case '01': $luna = "ianuarie";
				break;
	case '02': $luna = "februarie";
				break;
	case '03': $luna = "martie";
				break;
	case '04': $luna = "aprilie";
				break;
	case '05': $luna = "mai";
				break;
	case '06': $luna = "iunie";
				break;
	case '07': $luna = "iulie";
				break;
	case '08': $luna = "august";
				break;
	case '09': $luna = "septembrie";
				break;
	case '10': $luna = "octombrie";
				break;
	case '11': $luna = "noiembrie";
				break;
	case '12': $luna = "decembrie";
				break;

}
if ($_POST['factureaza'] == "OK"){ 
	foreach ($_POST as $asoc => $val){
			if ($asoc != "factureaza" && $asoc != "maxval" && $asoc != "delegat"){
				$detaliiDelegat = "SELECT * FROM admin WHERE id=".$_POST['delegat'];
				$detaliiDelegat = mysql_query($detaliiDelegat) or die ("Nu pot afla detaliile delegatului<br />");
				
				$nume = mysql_result($detaliiDelegat, 0, 'nume');
				$cnp = mysql_result($detaliiDelegat, 0, 'cnp');
				$buletin = mysql_result($detaliiDelegat, 0, 'buletin');
				$politia = mysql_result($detaliiDelegat, 0, 'politia');
				
				$selectAsoc = "SELECT * FROM facturare_urbica WHERE asoc_id=".$val;
				$selectAsoc = mysql_query($selectAsoc) or die ("Nu pot selecta asociatia in vederea facturarii<br />".mysql_error());
				
				$pretA = "SELECT P.pret FROM pachete P, facturare_urbica T WHERE T.pachet=P.pachet_id AND T.asoc_id=".$val;
				$pretA = mysql_query($pretA) or die ("Nu pot selecta pachetul pentru fiecare asociatie in parte<br />".mysql_error());
				
				$pret = mysql_result($pretA, 0, 'pret');
				
				echo '<table bgcolor="#BBBBBB" width="750px">';
					echo '<tr bgcolor="#000000" style="color:#FFFFFF">';
						echo '<td width="100px"><strong>Nr Crt</strong></td>';
						echo '<td width="250px"><strong>Denumirea produselor sau serviciilor</strong></td>';
						echo '<td><strong>U.M.</strong></td>';
						echo '<td><strong>Cantitate</strong></td>';
						echo '<td><strong>Pret Unitar lei</strong></td>';
						echo '<td><strong>Valoare lei</strong></td>';
					echo '</tr>';
					
					echo '<tr bgcolor="#DDDDDD">';
						echo '<td>0</td>';
						echo '<td>1</td>';
						echo '<td>2</td>';
						echo '<td>3</td>';
						echo '<td>4</td>';
						echo '<td>5 (3x4)</td>';
					echo '</tr>';
					
					echo '<tr align="left" bgcolor="#FFFFFF">';
						echo '<td>1</td>';
						echo '<td>Tarif administrare luna '.$luna.'</td>';
						echo '<td>AP</td>';
						echo '<td>'.mysql_result($selectAsoc, 0, 'nr_ap').'</td>';
						echo '<td>'.$pret.'</td>';
						echo '<td>'.( mysql_result($selectAsoc, 0, 'nr_ap') * $pret).'</td>';
					echo '</tr>';
					
					echo '<tr align="left" bgcolor="#DDDDDD">';
						echo '<td rowspan="3" width="50px">Semnatura si<br />stampila<br />furnizorului</td>';
						echo '<td rowspan="3" colspan="3"><strong>Date privind expeditia</strong><br />Numele delegatului: '.$nume.'<br />BI/CI seria '.$buletin.' eliberat '.$politia.'<br />Mijloc de transport ................ Nr<br />Expedierea s-a facut in prezenta noastra<br />la data de<br />Semnaturile</td>';
						echo '<td rowspan="2">Total<br />din care<br />accize:</td>';
						echo '<td>'.( mysql_result($selectAsoc, 0, 'nr_ap') * $pret).'</td>';
					echo '</tr>';
					
					echo '<tr bgcolor="#DDDDDD">';
						echo '<td>&nbsp;</td>';
					echo '</tr>';
					
					echo '<tr bgcolor="#DDDDDD">';		
						echo '<td>Semnatura<br />de primire</td>';
						echo '<td>Total de Plata:<br align="right" />'.( mysql_result($selectAsoc, 0, 'nr_ap') * $pret).'</td>';
					echo '</tr>';
					
				echo '</table>';
			}
		}
	}
?>