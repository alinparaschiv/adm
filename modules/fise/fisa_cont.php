<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=fisa_cont&asoc_id=" + value;
}
function select_scara(value,value2) {
 window.location = "index.php?link=fisa_cont&asoc_id=" + value + "&scara_id=" + value2;
}
function select_locatar(value,value2,value3) {
 window.location = "index.php?link=fisa_cont&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3;
}

</script>
<?php
////////////////////////////////////////////////////////////////////////////////////


//incarc datele din tabela
function get_cont($asoc_id,$scara_id,$loc_id) {
	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." ORDER BY data DESC";
	$sql = mysql_query($sql) or die(mysql_error());
	
	$i = 1;
	$aliniere = "right";

	while($row = mysql_fetch_assoc($sql)) {
		if ($i%2 == 0) {  $color = "skyblue"; } else { $color="#EEEEEE";  }
		if ($row['act'] == "LP") { $aliniere = "left"; } else { $aliniere = "right"; }
	
		echo'<tr bgcolor="'.$color.'">
		  <td align="center">'.$row['data'].'</td>
		  <td align="center">'.$row['explicatie'].'</td>
		  <td align="center">'.$row['act'].'</td>
		  <td align="'.$aliniere.'">'.round($row['valoare'], 2).'</td>
		  <td align="center">'.round($row['datorie'], 2).'</td>
		  <td align="center">'.round($row['total_penalizari'], 2).'</td>
		  <td align="center">'.round($row['total_general'], 2).'</td>
		  </tr>';
		$i++;
	}
}

function calcul_datorie($asoc_id,$scara_id,$loc_id){
	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND datorie IS NOT NULL ORDER BY id DESC";
	$sql = mysql_query($sql) or die(mysql_error());

	$datorie_initiala = mysql_result($sql, 0, 'datorie');

	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND act='LP' AND datorie IS NOT NULL ORDER BY id DESC";
	$sql = mysql_query($sql) or die(mysql_error());

	$valoare_initiala = mysql_result($sql, 0, 'valoare');

	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND datorie IS NULL ORDER BY id ASC";
	$sql = mysql_query($sql) or die(mysql_error());

	while($row = mysql_fetch_assoc($sql)) {
		$act = $row['act'] ;
		$valoare = $row['valoare'] ;
		$datorie = $row['datorie'] ;

		if ($act == "LP")
		{
			$datorie = $datorie_initiala + $valoare_initiala;
			$datorie_initiala = $datorie;
			$valoare_initiala = $valoare;
		}
	else
		if ($act != "LP")
		{
			$datorie = $datorie_initiala - $valoare;
			$datorie_initiala = $datorie;
		}

	$sql1 = mysql_query("UPDATE fisa_cont SET datorie=".$datorie."WHERE id=".$row['id']);
	}
}

function total_general($asoc_id,$scara_id,$loc_id){
	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND total_general IS NOT NULL ORDER BY id DESC";
	$sql = mysql_query($sql) or die(mysql_error());

	if (mysql_num_rows($sql)!=0) {
		$total_partial = mysql_result($sql, 0, 'total_general');
	}

	$sql = "SELECT * FROM fisa_cont WHERE asoc_id=".$asoc_id." AND scara_id=".$scara_id." AND loc_id=".$loc_id." AND total_general IS NULL ORDER BY id ASC";
	$sql = mysql_query($sql) or die(mysql_error());

	while($row = mysql_fetch_assoc($sql)) {
	$act = $row['act'] ;
	$valoare = $row['valoare'] ;
	$datorie = $row['datorie'] ;
	$total_penalizari = $row['total_penalizari'] ;

		if ($act == "LP")
		{
			$total_general = $total_partial + $valoare + $datorie + $total_penalizari;
			$total_partial = $total_general;
		}
	else
		if ($act != "LP")
		{
			$total_general = $total_partial - $valoare + $datorie + $total_penalizari;
			$total_partial = $total_general;
		}

	$sql1 = mysql_query("UPDATE fisa_cont SET total_general=".$total_general."WHERE id=".$row['id']);
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
	if ($_GET['locatar']<>null){
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND loc_id<>".$_GET['locatar'];
	} else {
		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];
	}
	$sql3 = mysql_query($sql3);
	while($row3 = mysql_fetch_array($sql3)) {
		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';
	}
}

?>
<style type="text/css">

thead tr td { border-bottom:solid 1px #000; font-weight: bold;}
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
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else {

					$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
					$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());

					echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';
				}?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
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
    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti locatarul:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['locatar']==null)  { echo '<option value="">----Alege----</option>';    }  else {
			$locatar = "SELECT * FROM locatari WHERE loc_id=".$_GET['locatar'];
			$locatar = mysql_query($locatar) or die ("Nu pot selecta locatarii<br />".mysql_error());

			echo '<option value="">'.mysql_result($locatar, 0, 'nume').'</option>';

		}?>
        <?php echo $locatari; ?>
        </select></td>
    </tr>
    <?php endif;?>
</table>



</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['locatar']<>null)):?>

  <?php calcul_datorie($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar']) ?>
  <?php total_general($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar']) ?>

<a class="pdf1" style="border:none;" target="_blank" href="modules/pdf/pdf.php?<?php echo 'afisare=ok&FCon=0&asoc_id='.$_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&loc_id='.$_GET['locatar'] ;?>"></a>


  <br clear="all" />
<h3>Ultimele 25 de operațiuni</h3>
<table width="950">
<thead>
<tr>
  <td>Data</td>
  <td>Explicație</td>
  <td>Act</td>
  <td>Valoare</td>
  <td>Datorie</td>
  <td>Total penalizări</td>
  <td>Total general</td>
  </tr>
</thead>
<tbody>
<?php  get_cont($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar']);  ?>

</tbody>
</table>
</form>
<?php endif; ?>