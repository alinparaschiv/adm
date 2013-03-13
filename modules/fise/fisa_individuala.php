<script type="text/javascript">
function select_asoc(value) {
	window.location = "index.php?link=fisa_indv&asoc_id=" + value;
}
function select_scara(value,value2) {
	window.location = "index.php?link=fisa_indv&asoc_id=" + value + "&scara_id=" + value2;
}
function select_locatar(value,value2,value3) {
	window.location = "index.php?link=fisa_indv&asoc_id=" + value + "&scara_id=" + value2 + "&loc_id=" + value3;
}

function select_luna(value,value2,value3,value4) {
	window.location = "index.php?link=fisa_indv&asoc_id=" + value + "&scara_id=" + value2 + "&loc_id=" + value3+ "&luna=" + value4;
}

</script>
<?php
include_once("Penalizare.class.php");

function putFisaIndividuala($asocId, $scaraId, $locId, $luna, $uMasuraArr){

	$pen = new Penalizare( $locId, $scaraId, $asocId);
	$pen->verifica();

	$lastMonth = date("m-Y", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	$i = 0;
	$fonduri = 0;
	$lunaCurenta = 0;

	$sql = "SELECT * FROM fisa_indiv WHERE loc_id=".$locId." AND luna='".$luna."' ORDER BY id ASC";
	$sql = mysql_query($sql) or die ("Nu pot accesa fisele individuale<br />".mysql_error());

	if (mysql_num_rows($sql) != 0){
		while ($row = mysql_fetch_array($sql)){
			if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
			// Aflu unitatea de masura
			$UM = "SELECT * FROM servicii WHERE serv_id=".$row['serviciu'];
			$UM = mysql_query($UM) or die ("Nu ma pot conecta la tabela servicii<br />".mysql_error());

			//$factura = mysql_query("SELECT * FROM facturi") or die(mysql_error());

			echo '<tr bgcolor='.$color.'>';
			echo '<td>'.mysql_result($UM, 0, 'serviciu').'</td>';
			echo '<td>'.$row['cant_fact_pers'].'</td>';
			echo '<td>'.$row['um'].'</td>';
			echo '<td>'.round($row['pret_unitar'], 2).'</td>';
			echo '<td>'.round($row['cant_fact_pers'] * $row['pret_unitar'], 2).'</td>';
			echo '<td>'.$row['factura'].'</td>';
			echo '<td>'.round($row['cant_fact_tot'], 2).'</td>';
			echo '<td>'.$uMasuraArr[mysql_result($UM, 0, 'unitate')].'</td>';
			echo '<td>'.round($row['pret_unitar2'],2).'</td>';
			echo '<td>'.round($row['cant_fact_tot'] * $row['pret_unitar2'],2).'</td>';
			echo '</tr>';
			$i ++;

			//calculez fondurile
			$eFond = "SELECT * FROM servicii WHERE serv_id=".$row['serviciu'];
			$eFond = mysql_query($eFond) or die ("Nu pot selecta serviciile care sunt fonduri<br />".mysql_error());

			if (mysql_result($eFond, 0, 'fonduri') == 'da')
				$fonduri += $row['cant_fact_pers'] * $row['pret_unitar'];

			//total pe luna curenta
			$nuEFond = "SELECT * FROM servicii WHERE serv_id=".$row['serviciu'];
			$nuEFond = mysql_query($nuEFond) or die ("Nu pot selecta serviciile care nu sunt fonduri<br />".mysql_error());

			if (mysql_result($nuEFond, 0, 'fonduri') == "nu")
				$lunaCurenta += $row['cant_fact_pers'] * $row['pret_unitar'];
		}

		//restanta fonduri


		$lunaUrmX = explode('-', $luna);
		$lunaUrmX = strtotime($lunaUrmX[1].'-'.$lunaUrmX[0].'-1');
		$lunaUrmX = strtotime('+1 month', $lunaUrmX);
		$lunaUrm = date('m-Y', $lunaUrmX);

		$restanta = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locId." AND data='".$lunaUrm."'";
		$restanta = mysql_query($restanta) or die ("Nu pot selecta datele din fisa fonduri<br />".mysql_error());

		$restanta_lunaAnt = "SELECT * FROM fisa_fonduri WHERE loc_id=".$locId." AND data='".$luna."'";
		$restanta_lunaAnt = mysql_query($restanta_lunaAnt) or die ("Nu pot selecta datele din fisa fonduri luna anterioara<br />".mysql_error());

		if(mysql_num_rows($restanta) > 0 && mysql_num_rows($restanta_lunaAnt) > 0) {
			$fondRul = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rul_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rul_rest');
			$fondRep = mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_rep_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_rep_rest');
			$fondSpec = mysql_result($restanta, 0, 'fond_spec_rest') + mysql_result($restanta, 0, 'fond_spec_incasat') - mysql_result($restanta_lunaAnt, 0, 'fond_spec_rest');

			$datoria = mysql_result($restanta, 0, 'fond_rul_rest') + mysql_result($restanta, 0, 'fond_rep_rest') + mysql_result($restanta, 0, 'fond_spec_rest')
			- $fondRul - $fondRep - $fondSpec;

			if ($i%2 == 0) {
				$color = "#CCCCCC";
			} else { $color="#EEEEEE";
			}
			if ($datoria > 0){

				echo '<tr bgcolor='.$color.'>
				<td align="center">Restanta fonduri</td>
				<td  align="center" colspan="3">&nbsp;</td>
				<td align="center">'.$datoria.'</td>
				<td align="center" colspan="5">&nbsp;</td>
				</tr>';
				$fonduri += $datoria;
				$i ++;
			}
		}
		//camp gol
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '<tr bgcolor='.$color.'>';
		echo '<td colspan="10">&nbsp;</td>';
		echo '</tr>';
		$i ++;

		// total pe luna curenta
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '<tr bgcolor='.$color.'>';
		echo '<td>Luna Curenta</td>';
		echo '<td colspan="3">&nbsp;</td>';
		echo '<td>'.round($lunaCurenta, 2).'</td>';
		echo '<td colspan="5">&nbsp;</td>';
		echo '</tr>';
		$i ++;
	}
	else
	{
		echo '<tr> <td colspan="10">Nu Sunt Inregistrate Facturi Pe Luna Curenta</td></tr>';
	}
}

////////////////////////////////////////////////////////////////////////////////////
if ($_GET['asoc_id']<>null){
	$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
} else {
	$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
}
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';
}

if($_GET['asoc_id']<>null) {
	if ($_GET['scara_id']<>null){
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id'];
	} else {
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	}
	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());
	while ($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';
	}
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {
	if ($_GET['loc_id']<>null){
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND loc_id<>".$_GET['loc_id'];
	} else {
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];
	}
	$sql3 = mysql_query($sql3);
	while($row3 = mysql_fetch_array($sql3)) {
		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';
	}
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['loc_id']<>null)) {
	if ($_GET['luna']<>null){
		$sql4 = "SELECT * FROM fisa_indiv WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']."  AND loc_id=".$_GET['loc_id']." AND luna<>'".$_GET['luna']."' GROUP BY luna ORDER BY id DESC";
	} else {
		$sql4 = "SELECT * FROM fisa_indiv WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']."  AND loc_id=".$_GET['loc_id']." GROUP BY luna ORDER BY id DESC";
	}
	$sql4 = mysql_query($sql4);
	while($row4= mysql_fetch_array($sql4)) {
		$lunaS .= '<option value="'.$row4[4].'">'.$row4[4].'</option>';
	}
}
////////////////////////////////////////////////////////////////////////////////////
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
#plata {float:left; margin-left:5px; margin-top:107px; position:absolute;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/4) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {

        			$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
        			$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());

        			echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';
        		}?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/4) Alegeti scara:</td>
  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {
        			$scara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
        			$scara = mysql_query($scara) or die ("Nu pot selecta scarile<br />".mysql_error());

        			echo '<option value="">'.mysql_result($scara, 0, 'scara').'</option>';
        		}?>
        		<?php  echo $scari; ?>
            </select></td>
    </tr>
    <?php endif;?>
      <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(3/4) Alegeti locatarul:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['loc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {
        	$locatar = "SELECT * FROM locatari WHERE loc_id=".$_GET['loc_id'];
        	$locatar = mysql_query($locatar) or die ("Nu pot selecta locatarii<br />".mysql_error());

        	echo '<option value="">'.mysql_result($locatar, 0, 'nume').'</option>';

        }?>
        <?php echo $locatari; ?>
        </select></td>
    </tr>
    <?php endif;?>


	<?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['loc_id']<>null)):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(4/4) Alegeti luna:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_luna(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,<?php  echo $_GET['loc_id']; ?>,this.value)">
        <?php  if($_GET['luna']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">'.$_GET['luna'].'</option>';   }?>
        		<?php echo $lunaS; ?>
            </select></td>
    </tr>
    <?php endif;?>
</table>



</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['loc_id']<>null) && ($_GET['luna']<>null)):?>

  <form action="" method="post">
  <br clear="all" />
  	<a class="pdf1" style="border:none;" target="_blank" href="modules/pdf/pdf.php?<?php echo 'afisare=ok&FI=0&luna='.$_GET['luna'].'&asoc_id='.$_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&loc_id='.$_GET['loc_id'] ;?>"></a>

<table width="950" style="top:250px; background-color:white;">
<thead style="font-weight:bold;" valign="top">
    <tr>
        <td bgcolor="#666666">&nbsp;</td>
        <td bgcolor="#666666" colspan="4">Sume / apartament</td>
        <td bgcolor="#666666" colspan="5">Sume Generale</td>
    </tr>
    <tr bgcolor="#CCCCCC" valign="middle">
        <td style="color:#000000">Produse facturate</td>
        <td style="color:#000000">Cantitatea<br /> facturata</td>
        <td style="color:#000000">U.M.</td>
        <td style="color:#000000">Pret unitar</td>
        <td style="color:#000000">Total lei</td>
        <td style="color:#000000">Act doveditor<br />Serie / Nr</td>
        <td style="color:#000000">Cantitatea<br />facturata<br />per total</td>
        <td style="color:#000000">U.M.</td>
        <td style="color:#000000">Pret unitar</td>
        <td style="color:#000000">Total lei</td>
    </tr>
</thead>
<tbody align="left">
	<?php putFisaIndividuala($_GET['asoc_id'], $_GET['scara_id'], $_GET['loc_id'], $_GET['luna'], $uMasuraArr); ?>
</tbody>
</table>
<!-- <a href="#">print</a> -->
</form>
  	<?php endif; ?>