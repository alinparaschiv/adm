<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=genereazaListe&asoc_id=" + value;
}
function select_scara(value,value2) {
 window.location = "index.php?link=genereazaListe&asoc_id=" + value + "&scara_id=" + value2;
}
function select_luna(value,value2,value3) {
 window.location = "index.php?link=genereazaListe&asoc_id=" + value + "&scara_id=" + value2 + "&luna=" + value3;
}

</script>
<?php
//creez luna noua in cazul in care nu exista
function put_luna($asoc_id, $scara_id){
	$luna_curr = date('m-Y');
	
	$sql = "SELECT * FROM lista_plata WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." ORDER BY `luna` ASC ";
	$sql = mysql_query($sql) or die ("Nu pot accesa tabela pentru a afla data<br />".mysql_error());
	
	if (mysql_num_rows($sql) == 0)//prima lista
	{
		$sql1 = "INSERT INTO lista_plata VALUES (null, '".$luna_curr."', null, ".$scara_id.", ".$asoc_id.", null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0)";
		$sql1 = mysql_query($sql1) or die ("Nu pot adauga data in tabela_1<br />".mysql_error());
	}
	
	if (mysql_num_rows($sql)>=1)
	{
		$luna_tabel = mysql_result($sql, 0, 'luna');
		$luna_tabel = explode('-',$luna_tabel);	

                if (mktime(0,0,0,$luna_tabel[0],0,$luna_tabel[1]) < mktime(0,0,0,date('m'),0,date('Y')) ){ //daca nu exista luna curenta
			$sql1 = "INSERT INTO lista_plata VALUES (null, '".$luna_curr."', null, ".$scara_id.", ".$asoc_id.", null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0)";
			$sql1 = mysql_query($sql1) or die ("Nu pot adauga data in tabela_2<br />".mysql_error());
		}
	}
}

/***************************APA RECE***********************/
function valoare_mc_apa_rece(){
	$sql = "SELECT * FROM servicii WHERE serviciu='apa rece'";
	$sql = mysql_query($sql) or die ("Nu ma pot conecta la servicii<br />".mysql_error());
	
	return mysql_result($sql, 0, 'cost');
}

function put_valoare_apa_rece($asoc_id, $scara_id, $loc_id){
	$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
	$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
	if (mysql_num_rows($sql) >=2) {	
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." ORDER BY data DESC";
		$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
		
		$apa_rece = mysql_result($sql,0,'r1') - mysql_result($sql,1,'r1') + mysql_result($sql,0,'r2') - mysql_result($sql,1,'r2') + mysql_result($sql,0,'r3') - mysql_result($sql,1,'r3') + mysql_result($sql,0,'r4') - mysql_result($sql,1,'r4') + mysql_result($sql,0,'r5') - mysql_result($sql,1,'r5');
	}
	else {
		$sql = "SELECT (A.r1-LA.r1) AS DIFF1, (A.r2-LA.r2) AS DIFF2, (A.r3-LA.r3) AS DIFF3, (A.r4-LA.r4) AS DIFF4, (A.r5-LA.r5) AS DIFF5 
				FROM locatari_apometre AS LA, apometre AS A 
				WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$loc_id." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
		$sql = mysql_query($sql);
			
		//echo $sql;
		
		$diff1 = mysql_result($sql,0,'DIFF1');
		$diff2 = mysql_result($sql,0,'DIFF2');
		$diff3 = mysql_result($sql,0,'DIFF3'); 
		$diff4 = mysql_result($sql,0,'DIFF4'); 
		$diff5 = mysql_result($sql,0,'DIFF5'); 
		
		$apa_rece = $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		return $apa_rece * valoare_mc_apa_rece();
}


function put_total_consum_apa_rece($asoc_id, $scara_id){
	$consum_apa_rece = 0;
	
	$useri = "SELECT * FROM locatari WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id;
	$useri = mysql_query($useri);
	
	while ($r_useri = mysql_fetch_array($useri))
	{
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
		$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
		if (mysql_num_rows($sql) >=2) {	
			$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$r_useri['loc_id']." ORDER BY data DESC";
			$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
			
			$consum_apa_rece += mysql_result($sql,0,'r1') - mysql_result($sql,1,'r1') + mysql_result($sql,0,'r2') - mysql_result($sql,1,'r2') + mysql_result($sql,0,'r3') - mysql_result($sql,1,'r3') + mysql_result($sql,0,'r4') - mysql_result($sql,1,'r4') + mysql_result($sql,0,'r5') - mysql_result($sql,1,'r5');
		}
		else {
			$sql = "SELECT (A.r1-LA.r1) AS DIFF1, (A.r2-LA.r2) AS DIFF2, (A.r3-LA.r3) AS DIFF3, (A.r4-LA.r4) AS DIFF4, (A.r5-LA.r5) AS DIFF5 
					FROM locatari_apometre AS LA, apometre AS A 
					WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$r_useri['loc_id']." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
			
			$sql = mysql_query($sql) or die(mysql_error());
			//echo $sql;
			
			$diff1 = mysql_result($sql,0,'DIFF1');
			$diff2 = mysql_result($sql,0,'DIFF2');
			$diff3 = mysql_result($sql,0,'DIFF3'); 
			$diff4 = mysql_result($sql,0,'DIFF4'); 
			$diff5 = mysql_result($sql,0,'DIFF5'); 
			
			$consum_apa_rece += $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		}
	return $consum_apa_rece * valoare_mc_apa_rece();
}


function put_apa_rece($asoc_id, $scara_id, $loc_id){
	$curr = date('Y-m-d');
	
	$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
	$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
	if (mysql_num_rows($sql) >=2) {	
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." ORDER BY data DESC";
		$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
		
		$apa_rece = mysql_result($sql,0,'r1') - mysql_result($sql,1,'r1') + mysql_result($sql,0,'r2') - mysql_result($sql,1,'r2') + mysql_result($sql,0,'r3') - mysql_result($sql,1,'r3') + mysql_result($sql,0,'r4') - mysql_result($sql,1,'r4') + mysql_result($sql,0,'r5') - mysql_result($sql,1,'r5');
	}
	else {
		$sql = "SELECT (A.r1-LA.r1) AS DIFF1, (A.r2-LA.r2) AS DIFF2, (A.r3-LA.r3) AS DIFF3, (A.r4-LA.r4) AS DIFF4, (A.r5-LA.r5) AS DIFF5 
				FROM locatari_apometre AS LA, apometre AS A 
				WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$loc_id." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
		$sql = mysql_query($sql);
			
		//echo $sql;
		
		$diff1 = mysql_result($sql,0,'DIFF1');
		$diff2 = mysql_result($sql,0,'DIFF2');
		$diff3 = mysql_result($sql,0,'DIFF3'); 
		$diff4 = mysql_result($sql,0,'DIFF4'); 
		$diff5 = mysql_result($sql,0,'DIFF5'); 
		
		$apa_rece = $diff1 + $diff2 + $diff3 + $diff4 + $diff5;
		
		return $apa_rece;	
	}
	return $apa_rece;
}

function put_consum_apa_rece($asoc_id, $scara_id){
	$consum_apa_rece = 0;
	
	$useri = "SELECT * FROM locatari WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id;
	$useri = mysql_query($useri);
	
	while ($r_useri = mysql_fetch_array($useri))
	{
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
		$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
		if (mysql_num_rows($sql) >=2) {	
			$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$r_useri['loc_id']." ORDER BY data DESC";
			$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
			
			$consum_apa_rece += mysql_result($sql,0,'r1') - mysql_result($sql,1,'r1') + mysql_result($sql,0,'r2') - mysql_result($sql,1,'r2') + mysql_result($sql,0,'r3') - mysql_result($sql,1,'r3') + mysql_result($sql,0,'r4') - mysql_result($sql,1,'r4') + mysql_result($sql,0,'r5') - mysql_result($sql,1,'r5');
		}
		else {
			$sql = "SELECT (A.r1-LA.r1) AS DIFF1, (A.r2-LA.r2) AS DIFF2, (A.r3-LA.r3) AS DIFF3, (A.r4-LA.r4) AS DIFF4, (A.r5-LA.r5) AS DIFF5 
					FROM locatari_apometre AS LA, apometre AS A 
					WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$r_useri['loc_id']." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
			
			$sql = mysql_query($sql) or die(mysql_error());
			//echo $sql;
			
			$diff1 = mysql_result($sql,0,'DIFF1');
			$diff2 = mysql_result($sql,0,'DIFF2');
			$diff3 = mysql_result($sql,0,'DIFF3'); 
			$diff4 = mysql_result($sql,0,'DIFF4'); 
			$diff5 = mysql_result($sql,0,'DIFF5'); 
			
			$consum_apa_rece += $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		}
	return $consum_apa_rece;
}

/******************APA CALDA**********************/
function valoare_mc_apa_calda(){
	$sql = "SELECT * FROM servicii WHERE serviciu='apa calda'";
	$sql = mysql_query($sql) or die ("Nu ma pot conecta la servicii<br />".mysql_error());
	
	return mysql_result($sql, 0, 'cost');
}

function put_valoare_apa_calda($asoc_id, $scara_id, $loc_id){
	$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
	$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
	if (mysql_num_rows($sql) >=2) {	
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." ORDER BY data DESC";
		$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
		
		$apa_calda = mysql_result($sql,0,'c1') - mysql_result($sql,1,'c1') + mysql_result($sql,0,'c2') - mysql_result($sql,1,'c2') + mysql_result($sql,0,'c3') - mysql_result($sql,1,'c3') + mysql_result($sql,0,'c4') - mysql_result($sql,1,'c4') + mysql_result($sql,0,'c5') - mysql_result($sql,1,'c5');
	}
	else {
		$sql = "SELECT (A.c1-LA.c1) AS DIFF1, (A.c2-LA.c2) AS DIFF2, (A.c3-LA.c3) AS DIFF3, (A.c4-LA.c4) AS DIFF4, (A.c5-LA.c5) AS DIFF5 
				FROM locatari_apometre AS LA, apometre AS A 
				WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$loc_id." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
		$sql = mysql_query($sql);
			
		//echo $sql;
		
		$diff1 = mysql_result($sql,0,'DIFF1');
		$diff2 = mysql_result($sql,0,'DIFF2');
		$diff3 = mysql_result($sql,0,'DIFF3'); 
		$diff4 = mysql_result($sql,0,'DIFF4'); 
		$diff5 = mysql_result($sql,0,'DIFF5'); 
		
		$apa_calda = $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		return $apa_calda * valoare_mc_apa_calda();
}


function put_total_consum_apa_calda($asoc_id, $scara_id){
	$consum_apa_calda = 0;
	
	$useri = "SELECT * FROM locatari WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id;
	$useri = mysql_query($useri);
	
	while ($r_useri = mysql_fetch_array($useri))
	{
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
		$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
		if (mysql_num_rows($sql) >=2) {	
			$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$r_useri['loc_id']." ORDER BY data DESC";
			$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
			
			$consum_apa_calda += mysql_result($sql,0,'c1') - mysql_result($sql,1,'c1') + mysql_result($sql,0,'c2') - mysql_result($sql,1,'c2') + mysql_result($sql,0,'c3') - mysql_result($sql,1,'c3') + mysql_result($sql,0,'c4') - mysql_result($sql,1,'c4') + mysql_result($sql,0,'c5') - mysql_result($sql,1,'c5');
		}
		else {
			$sql = "SELECT (A.c1-LA.c1) AS DIFF1, (A.c2-LA.c2) AS DIFF2, (A.c3-LA.c3) AS DIFF3, (A.c4-LA.c4) AS DIFF4, (A.c5-LA.c5) AS DIFF5 
					FROM locatari_apometre AS LA, apometre AS A 
					WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$r_useri['loc_id']." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
			
			$sql = mysql_query($sql) or die(mysql_error());
			//echo $sql;
			
			$diff1 = mysql_result($sql,0,'DIFF1');
			$diff2 = mysql_result($sql,0,'DIFF2');
			$diff3 = mysql_result($sql,0,'DIFF3'); 
			$diff4 = mysql_result($sql,0,'DIFF4'); 
			$diff5 = mysql_result($sql,0,'DIFF5'); 
			
			$consum_apa_calda += $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		}
	return $consum_apa_calda * valoare_mc_apa_calda();
}

function put_apa_calda($asoc_id, $scara_id, $loc_id){
	$curr = date('m-Y');
	
	$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
	$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
	if (mysql_num_rows($sql) >=2) {	
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." ORDER BY data DESC";
		$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
		
		$apa_rece = mysql_result($sql,0,'c1') - mysql_result($sql,1,'c1') + mysql_result($sql,0,'c2') - mysql_result($sql,1,'c2') + mysql_result($sql,0,'c3') - mysql_result($sql,1,'c3') + mysql_result($sql,0,'c4') - mysql_result($sql,1,'c4') + mysql_result($sql,0,'c5') - mysql_result($sql,1,'c5');
	}
	else {
		$sql = "SELECT (A.c1-LA.c1) AS DIFF1, (A.c2-LA.c2) AS DIFF2, (A.c3-LA.c3) AS DIFF3, (A.c4-LA.c4) AS DIFF4, (A.c5-LA.c5) AS DIFF5 
				FROM locatari_apometre AS LA, apometre AS A 
				WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$loc_id." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
			
		$sql = mysql_query($sql) or die("Nu pot face join-ul<br />".mysql_error());
		
		//echo $sql;
		
		$diff1 = mysql_result($sql,0,'DIFF1');
		$diff2 = mysql_result($sql,0,'DIFF2');
		$diff3 = mysql_result($sql,0,'DIFF3'); 
		$diff4 = mysql_result($sql,0,'DIFF4'); 
		$diff5 = mysql_result($sql,0,'DIFF5'); 
		
		$apa_calda = $diff1 + $diff2 + $diff3 + $diff4 + $diff5;
	}
	return $apa_calda;
}

function put_consum_apa_calda($asoc_id, $scara_id){
	$consum_apa_calda = 0;
	
	$useri = "SELECT * FROM locatari WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id;
	$useri = mysql_query($useri);
	
	while ($r_useri = mysql_fetch_array($useri))
	{
		$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." GROUP BY data";
		$sql = mysql_query($sql) or die("Nu pot grupa dupa data<br />".mysql_error());
	
		if (mysql_num_rows($sql) >=2) {	
			$sql = "SELECT * FROM apometre WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$r_useri['loc_id']." ORDER BY data DESC";
			$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari_apometre<br />".mysql_error());
			
			$consum_apa_calda += mysql_result($sql,0,'c1') - mysql_result($sql,1,'c1') + mysql_result($sql,0,'c2') - mysql_result($sql,1,'c2') + mysql_result($sql,0,'c3') - mysql_result($sql,1,'c3') + mysql_result($sql,0,'c4') - mysql_result($sql,1,'c4') + mysql_result($sql,0,'c5') - mysql_result($sql,1,'c5');
		}
		else {
			$sql = "SELECT (A.c1-LA.c1) AS DIFF1, (A.c2-LA.c2) AS DIFF2, (A.c3-LA.c3) AS DIFF3, (A.c4-LA.c4) AS DIFF4, (A.c5-LA.c5) AS DIFF5 
					FROM locatari_apometre AS LA, apometre AS A 
					WHERE LA.asoc_id=".$asoc_id." AND LA.scara_id=".$scara_id." AND LA.loc_id=".$r_useri['loc_id']." AND LA.loc_id=A.loc_id ORDER BY A.loc_id";
			
			$sql = mysql_query($sql) or die(mysql_error());
			//echo $sql;
			
			$diff1 = mysql_result($sql,0,'DIFF1');
			$diff2 = mysql_result($sql,0,'DIFF2');
			$diff3 = mysql_result($sql,0,'DIFF3'); 
			$diff4 = mysql_result($sql,0,'DIFF4'); 
			$diff5 = mysql_result($sql,0,'DIFF5'); 
			
			$consum_apa_calda += $diff1 + $diff2 + $diff3 + $diff4 + $diff5;}
		}
	return $consum_apa_calda;
}


/**********************INCALZIRE********************/

function put_incalzire($asoc_id, $scara_id, $loc_id) {
    $luna_curr = date('m-Y');

    $serviciu_id = 'SELECT serv_id FROM servicii WHERE serviciu="incalzire"';
    $serviciu_id = mysql_query($serviciu_id) or die ("Nu pot afla id-ul serviciului de invcalzire<br />".mysql_error());
    $serviciu_id = mysql_result($serviciu_id, 0, 'serv_id');

    $dePlata = 'SELECT sum(`cant_fact_pers`*`pret_unitar`) as Incalzire FROM `fisa_indiv` WHERE `asoc_id`='.$asoc_id.' AND `scara_id`='.$scara_id.' AND `serviciu`='.$serviciu_id.' AND`loc_id`='.$loc_id.' AND `luna`="'.$luna_curr.'"';
    $dePlata = mysql_query($dePlata) or die ("Nu pot afla suma de plata la invcalzire<br />".mysql_error());
    $dePlata = mysql_result($dePlata, 0, 'Incalzire');

    return round($dePlata, 2);
}

/**********************TABEL************************/

function fillTable($asoc_id, $scara_id){
	$i = 0;
	$nr_pers = 0;
	$sup_tot = 0;
	
	$sql = "SELECT * FROM locatari WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id;
	$sql = mysql_query($sql) or die(mysql_error("Nu ma pot conecta la tabela locatari"));
	
	while ($row = mysql_fetch_array($sql)){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		
		$nr_pers += $row['nr_pers'];
		$sup_tot += $row['supr'];
		
		echo '<tr bgcolor='.$color.'>';
			echo '<td>'.$row['ap'].'</td>';
			echo '<td>'.$row['nume'].'</td>';
			echo '<td>'.$row['centrala'].'</td>';
			echo '<td>'.$row['nr_pers'].'</td>';
			echo '<td>'.$row['supr'].'</td>';
			echo '<td>'.put_apa_rece($asoc_id, $scara_id, $row['loc_id']).'</td>';
			echo '<td>'.put_valoare_apa_rece($asoc_id, $scara_id, $row['loc_id']).'</td>';
			echo '<td>'.put_apa_calda($asoc_id, $scara_id, $row['loc_id']).'</td>';
			echo '<td>'.put_valoare_apa_calda($asoc_id, $scara_id, $row['loc_id']).'</td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td>'.put_incalzire($asoc_id, $scara_id, $row['loc_id']).'</td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td> - </td>';
			echo '<td>'.$row['ap'].'</td>';
		echo '</tr>';
		$i++;
	}

	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '<tr bgcolor='.$color.'>';
		echo '<td colspan="2">TOTAL</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>'.$nr_pers.'</td>';
		echo '<td>'.$sup_tot.'</td>';
		echo '<td>'.put_consum_apa_rece($asoc_id, $scara_id).'</td>';
		echo '<td>'.put_total_consum_apa_rece($asoc_id, $scara_id).'</td>';
		echo '<td>'.put_consum_apa_calda($asoc_id, $scara_id).'</td>';
		echo '<td>'.put_total_consum_apa_calda($asoc_id, $scara_id).'</td>';
		echo '<td colspan="15"> - </td>';
		echo '<td>&nbsp;</td>';
	echo '</tr>';
	$i++;
}


////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2);
	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">Bloc '.$row2[5].', scara '.$row2[2].'</option>';	
	}	
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {
    put_luna($_GET['asoc_id'], $_GET['scara_id']);

	$sql3 = "SELECT * FROM lista_plata WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." ORDER BY id_lista_plata DESC";
	$sql3 = mysql_query($sql3) or die ("Nu pot sa scot luna");
	while($row3 = mysql_fetch_array($sql3)) {
		$luna .= '<option value="'.$row3[0].'">'.$row3[1].'</option>';	
	}	
}

?>
<style type="text/css">

thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { width:100%; border:none; height:100%; }
tbody tr.newline td { border:solid 1px #0CC;   }
tfoot { color:#FFF; }
.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
.addnew2 {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:95px; margin-top:-9px;  }
tr.newline input { text-align:center; }
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
#print {float:left; margin-left:970px; margin-top:15px;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
  <td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Asociatia cu nr. '.$_GET['asoc_id'].'</option>';   }?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Scara cu nr. '.$_GET['scara_id'].'</option>';   }?>
        		<?php  echo $scari; ?>
            </select></td>
    </tr>
    <?php endif;?>
      <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti luna:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_luna(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['luna']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Luna cu nr. '.$_GET['luna'].'</option>';   }?>
        		<?php echo $luna; ?>
            </select></td>
    </tr>
    <?php endif;?>
</table>
    


</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']!=null) && ($_GET['luna']<>null)):?>
 
 	
  <form action="" method="post">
  <div id="print">
  	<a href="#">printeaza</a>
  </div>
<table width="1024" style="position:absolute; top:250px; background-color:white;">
<thead>
	<tr>
        <td bgcolor="#666666" rowspan="2">Nr. Ap.</td>
        <td bgcolor="#666666" rowspan="2" width="100">Nume</td>
        <td bgcolor="#666666" rowspan="2">CT</td>
        <td bgcolor="#666666" rowspan="2">Nr. Pers.</td>
        <td bgcolor="#666666" rowspan="2">ST</td>
        <td bgcolor="#666666" colspan="2">Apa Rece</td>
        <td bgcolor="#666666" colspan="2">Apa Calda</td>
        <td bgcolor="#666666" colspan="3">Diferenta</td>
        <td bgcolor="#666666" rowspan="2">Incalzire</td>
        <td bgcolor="#666666" rowspan="2">Chelt / pers</td>
        <td bgcolor="#666666" rowspan="2">Chelt / cota indivizia</td>
        <td bgcolor="#666666" rowspan="2">Chelt / beneficiari</td>
        <td bgcolor="#666666" rowspan="2">Chelt de alta natura</td>
        <td bgcolor="#666666" rowspan="2">Total Luna</td>
        <td bgcolor="#666666" rowspan="2">Restante</td>
        <td bgcolor="#666666" rowspan="2">Penalizari</td>
        <td bgcolor="#666666" rowspan="2">Restanta fond</td>
        <td bgcolor="#666666" rowspan="2">Fond Rulment</td>
        <td bgcolor="#666666" rowspan="2">Fond Reparatii</td>
        <td bgcolor="#666666" rowspan="2">Total General</td>
        <td bgcolor="#666666" rowspan="2">Nr. Ap.</td>
	</tr>
  
	<tr>
    	<td bgcolor="#666666">AR</td>
        <td bgcolor="#666666">valoare</td>
        <td bgcolor="#666666">AC</td>
        <td bgcolor="#666666">valoare</td>
        <td bgcolor="#666666">AR</td>
        <td bgcolor="#666666">AC</td>
        <td bgcolor="#666666">valoare</td>
    </tr>  	
</thead>
<tbody align="left">
	<?php fillTable($_GET['asoc_id'], $_GET['scara_id'])?>
	
</tbody>
</table>
</form>
<?php endif; ?>