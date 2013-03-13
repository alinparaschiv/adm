<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=fisa_fonduri&asoc_id=" + value;
}
function select_scara(value,value2) {
	window.location = "index.php?link=fisa_fonduri&asoc_id=" + value + "&scara_id=" + value2;
}
function select_luna(value,value2,value3) {
	window.location = "index.php?link=fisa_fonduri&asoc_id=" + value + "&scara_id=" + value2 + "&luna=" + value3;
}

</script>
<?php

function getData($asocId, $scaraId) {

	$ultimaLuna_s = 'SELECT data FROM fisa_fonduri WHERE scara_id=' .$scaraId.' GROUP BY data ORDER BY STR_TO_DATE(data, "%m-%Y") DESC LIMIT 1';
	$ultimaLuna_q = mysql_query($ultimaLuna_s) or die('NU pot afla care e cea mai veche inregistrare din fisa fonduri pentru scara curenta <br />'.$ultimaLuna_s);
	$ultimaLuna_r = mysql_result($ultimaLuna_q, 0, 'data');

	$rezult = '';

	// Selectez situatia fondurilor de pe ultima luna introdusa
	$sql = "SELECT * FROM fisa_fonduri ff, locatari l WHERE l.loc_id=ff.loc_id AND l.asoc_id=".$asocId." AND l.scara_id=".$scaraId." AND data='$ultimaLuna_r' ORDER BY l.loc_id";
	$sql = mysql_query($sql) or die ("Nu pot afisa fondurile pe luna curenta. <br />".mysql_error());

	// TOTAL: fondul de rulment
	$tot_fond_rul_incasat = 0;
	$tot_fond_rul_rest = 0;

	// TOTAL: fondurile speciale
	$tot_fond_spec_incasat = 0;
	$tot_fond_spec_rest = 0;
	$tot_fond_spec_cheltuit = 0;
	$tot_fond_spec_cumulat = 0;

	// TOTAL: fondul de reparatii
	$tot_fond_rep_incasat = 0;
	$tot_fond_rep_rest = 0;
	$tot_fond_rep_cheltuit = 0;
	$tot_fond_rep_cumulat = 0;

	// TOTAL: fondul de penalitati --> Nu merge momentan deoarece nu am realizat fisa furnizorilor
	$tot_fond_pen_constituit = 0;
	$tot_fond_pen_incasat = 0;

	// TOTAL: restante
	$tot_inc_gen = 0;
	$total_plata_gen = 0;


	$i = 0;
	while ($row = mysql_fetch_array($sql)){
		if ($i%2 == 0) { $color = "#CCCCCC"; } else { $color="#EEEEEE"; }
		$i++;

		$sql_grup = 'SELECT SUM(fond_rul_incasat) rul_i, SUM(fond_rep_incasat) rep_i, SUM(fond_spec_incasat) spe_i, SUM(fond_rep_cheltuit) rep_c FROM fisa_fonduri WHERE loc_id='.$row['loc_id'].' GROUP BY loc_id';
		$sql_grup = mysql_query($sql_grup) or die('Nu pot afla totalul incasarilor in fonduri');
		$sql_grup = mysql_fetch_assoc($sql_grup);

		$rezult .= '<tr>';
		$rezult .= '<td bgcolor="'.$color.'">Ap. '.$row['ap'].'</td>';

		//	$locatar = "SELECT * FROM locatari WHERE loc_id=".$row['loc_id'];
		//	$locatar = mysql_query($locatar) or die ("Nu pot afisa locatarii<br />".mysql_error());

		$rezult .= '<td bgcolor="'.$color.'">'.$row['nume'].'</td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rul_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_rul_rest'], 2).'</td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rep_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_rep_rest'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['rep_c'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'"><strong>'.round(($sql_grup['rep_i'] - $sql_grup['rep_c']), 2).'</strong></td>';

		$rezult .= '<td bgcolor="'.$color.'">'.round($sql_grup['spe_i'], 2).'</td>';
		$rezult .= '<td bgcolor="'.$color.'">'.round($row['fond_spec_rest'], 2).'</td>';

		$tot_inc_loc = $sql_grup['rul_i'] + $sql_grup['rep_i'] + $sql_grup['spe_i'];
		$tot_rest_loc = $row['fond_rul_rest'] + $row['fond_rep_rest'] + $row['fond_spec_rest'];

		// Suma de plata pentru luna curenta este formata din totalul fondurilor - restantele de luna trecuta
		//$plata_gen = $tot_rest_loc - $row['restante'];

		$rezult .= '<td bgcolor="'.$color.'">'.round($tot_inc_loc, 2).'</td>';		// total incasari / locatar
		$rezult .= '<td bgcolor="'.$color.'">'.round($tot_rest_loc, 2).'</td>';		// cat are de plata pe luna curenta
		//$rezult .= '<td bgcolor="'.$color.'">'.$row['restante'].'</td>';  // restante de pe luna anterioara

		// Calculez totalurile pentru footerul tabelului
		$tot_fond_rul_incasat += $sql_grup['rul_i'];
		$tot_fond_rul_rest += $row['fond_rul_rest'];

		$tot_fond_rep_incasat += $sql_grup['rep_i'];
		$tot_fond_rep_rest += $row['fond_rep_rest'];
		$tot_fond_rep_cheltuit += $sql_grup['rep_c'];

		$tot_fond_spec_incasat += $sql_grup['spe_i'];
		$tot_fond_spec_rest += $row['fond_spec_rest'];

		//$tot_fond_pen_constituit += $row['fond_pen_constituit'];
		//$tot_fond_pen_incasat += $row['fond_pen_incasat'];

		$tot_inc_gen += $tot_inc_loc;
		$total_plata_gen += $tot_rest_loc;

		//$tot_rest_gen += $row['restante'];
	}

	$rezult .= '<tr> <td colspan="11">&nbsp;</td></tr>';

	$rezult .= '<tr>';
	$rezult .= '<td colspan="2" align="center"><strong>TOTAL:</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rul_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rul_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_rep_cheltuit, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round(($tot_fond_rep_incasat - $tot_fond_rep_cheltuit), 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_spec_incasat, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_fond_spec_rest, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($tot_inc_gen, 2).'</strong></td>';
	$rezult .= '<td><strong>'.round($total_plata_gen, 2).'</strong></td>';
	//$rezult .= '<td><strong>'.$tot_rest_gen.'</strong></td>';
	$rezult .= '</tr>';

	return $rezult;
}

////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
$sql = mysql_query($sql) or die(mysql_error());

while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2);

	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}
}

if($_GET['asoc_id']<>null && $_GET['scara_id']<>null) {
	$sql3 = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." GROUP BY data";
	$sql3 = mysql_query($sql3) or die("Nu pot afla luna<br />".mysql_error());

	while($row3 = mysql_fetch_array($sql3)) {
		$luna .= '<option value="'.$row3[1].'">'.$row3[1].'</option>';
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
#print {float:left; margin-left:900px; margin-top:15px;}
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

</table>



</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>


  <!-- <a href="#" class="pdf1" style="border:none;"></a> -->
	<br clear="all" />
  <form action="" method="post">
  <div id="print">
  	<a href="#">printeaza</a>
  </div>
<table width="950" style="top:250px; background-color:white;">
<thead>
<tr>
  <td bgcolor="#666666">Nr. Crt.</td>
  <td bgcolor="#666666">Proprietar Apartament</td>

  <td bgcolor="#666666">Rulment <br /> Incasat</td>
  <td bgcolor="#666666">Rulment <br /> Restant</td>

  <td bgcolor="#666666">Reparatii <br /> Incasat</td>
  <td bgcolor="#666666">Reparatii <br /> Restant</td>
  <td bgcolor="#666666">Reparatii <br /> Cheltuit</td>
  <td bgcolor="#666666">Reparatii <br /> Cumulat</td>

  <td bgcolor="#666666">Special <br /> Incasat</td>
  <td bgcolor="#666666">Special <br /> Restant</td>

  <td bgcolor="#666666">Total <br /> Incasat</td>
  <td bgcolor="#666666">Total <br /> Restante</td>
  </tr>
</thead>
<tbody>
	<?php echo getData($_GET['asoc_id'], $_GET['scara_id']) ?>
</tbody>
</table>
<!-- <a href="#">print</a> -->
</form>
<?php endif; ?>
