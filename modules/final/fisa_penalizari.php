<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=fisa_pen&asoc_id=" + value;	
}
function select_scara(value,value2) {
 window.location = "index.php?link=fisa_pen&asoc_id=" + value + "&scara_id=" + value2;	
}
function select_locatar(value,value2,value3) {
 window.location = "index.php?link=fisa_pen&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3;	
}

$(document).ready(function() {
    $("#addPeriod").click(function() {
								   var maxim = $("#periods > p").size();
								   var toAppend = "<p class='hPenPara'>Penalizare: <input type='text' style='width:20px;' maxlength='4' name='proc" + maxim + "' /> % incepand cu data <input type='text' maxlength='10' class='datepicker' style='width:70px;' name='data" + maxim + "' /></p>";
								   var maxim = $("#periods > p").size();
								   $("#periods").append(toAppend);
								   $(".maxim").val(maxim);
								   $(".datepicker").datepicker({ dateFormat: 'dd-mm-yy'});
								   });
  });
</script>



<?php
//addPeriod
//periods

############### PERIOADE PROCENT PENALIZARE ################################

function has_history($asoc) {
	
	$sql = "SELECT id FROM historyProcPen WHERE asoc_id=".$asoc;
	$sql = mysql_query($sql);
	if(mysql_num_rows($sql)==0) { return false; } else { return true; }
	}
	
function date_reverse($data) {
	$data = explode("-",$data);
	return $data[2]."-".$data[1]."-".$data[0];
	}
	
function yesterday($data) {
	$data = explode("-",$data);
	$luna_curenta = mktime(0, 0, 0, $data[1], $data[2]-1,   $data[0]);
	$luna_curenta = date('Y-m-d',$luna_curenta);
	return $luna_curenta;
	}
	
	
if(isset($_POST['finalP'])) {

	$asoc = $_GET['asoc_id'];
	$maxim = $_POST['maxim'];

	for($i=0;$i<$maxim+1;$i++) {
		$penalizare = $_POST['proc'.$i];
		$data = date_reverse($_POST['data'.$i]);
		$z = $i + 1;
		if($_POST['data'.$z]<>null) { $Fdata = yesterday(date_reverse($_POST['data'.$z]));   } else { $Fdata = '0000-00-00';   }
		
			$sql = mysql_query("INSERT INTO historyProcPen VALUES('','$asoc','$data','$Fdata','$penalizare','')") or die(mysql_error());
		
		}
	
	
	}

############################################################################



///FUNCTIONS
function get_proc_pen($id) {
$sql = "SELECT penalizare AS proc_pen FROM asociatii_setari WHERE asoc_id=".$id;	
	$sql = mysql_query($sql);
	return mysql_result($sql,0,'proc_pen');
}

function date_change_one($data) {
	$data = explode("-",$data);
	return $data[2]."-".$data[1]."-".$data[0];
}

function date_change_two($data) {
	$data = explode("-",$data);
	return $data[2].".".$data[1].".".$data[0];
}

function days_between_now($data) {
$data = explode("-",$data);
$first = mktime(0,0,0,$data[2],$data[1],$data[0]);
$now = time();
if($first>$now) { return 0;  } else { $offset = $now - $first;  return floor($offset/60/60/24);  }
}

function days_between_dates($date1,$date2) {
if($date1<>null && $date2<>null) {
$date1 = explode("-",$date1);
$date2 = explode("-",$date2);
$offset = mktime(0,0,0,$date1[1],$date1[2],$date1[0])-mktime(0,0,0,$date2[1],$date2[2],$date2[0]);
return floor($offset/60/60/24);
} else { return 0;  }
}
////////////////////////////////////////////////////////////////////////////////////

/*if(isset($_POST['adauga2'])) {
	$luna = mysql_real_escape_string($_POST['luna']);
$debit = mysql_real_escape_string($_POST['debit']);
$data_scad = date_change_one(mysql_real_escape_string($_POST['data_scad']));
$data_plata = date_change_one(mysql_real_escape_string($_POST['data_plata']));

$asoc_id = mysql_real_escape_string($_GET['asoc_id']);
$scara_id = mysql_real_escape_string($_GET['scara_id']);
$locatar_id = mysql_real_escape_string($_GET['locatar']);




$val_pen = mysql_real_escape_string($_POST['val_pen']);	
$proc_pen = get_proc_pen($asoc_id);

if($nr_zile == 0) {  $nr_zile = ""; }

$sql = mysql_query("INSERT INTO fisa_pen VALUES('','$asoc_id','$scara_id','$locatar_id','$luna','$debit','$data_scad','$data_plata','$nr_zile','$proc_pen','$val_pen')");

}*/


if(isset($_POST['adauga']) || isset($_POST['adauga2'])) {
$luna = mysql_real_escape_string($_POST['luna']);
$debit = mysql_real_escape_string($_POST['debit']);
$data_scad = date_change_one(mysql_real_escape_string($_POST['data_scad']));
$data_plata = date_change_one(mysql_real_escape_string($_POST['data_plata']));
$nrzile = mysql_real_escape_string($_POST['nrzile']);

$asoc_id = mysql_real_escape_string($_GET['asoc_id']);
$scara_id = mysql_real_escape_string($_GET['scara_id']);
$locatar_id = mysql_real_escape_string($_GET['locatar']);

# Avem 2 cazuri. Caz 1 cand nu avem data platii, si caz 2 cand avem data platii
if($data_plata<>null) {  

$sql = "SELECT * FROM historyProcPen WHERE asoc_id=".$asoc_id." ORDER BY sDate ASC";
$sql = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_assoc($sql)) {
	if($row['fDate']=="0000-00-00") { $row['fDate']=date('Y-m-d'); }
	if($continue==1) { $data_scad = $row['sDate']; }
	$continue=0;
	echo 'Data scad este: '.$data_scad.' & $rowSdate: '.$row['sDate'].' & $rowFdate: '.$row['fDate'].' & data platii este: '.$data_plata.'<br />';
	if($data_scad>=$row['sDate'] && $data_plata<=$row['fDate']) {
		$nrZile = days_between_dates($data_plata,$data_scad);
		$procPen = $row['value'];
		$penalizare = $debit * $proPen/100 * $nrZile;
		while($penalizare>$debit) {
			$nrZile--;
			$penalizare = $debit * $proPen/100 * $nrZile;
			}
		echo 'aici 1';
		die('Eroarea 1 in final\fisa_penalizari.php');
		mysql_query("INSERT INTO fisa_pen VALUE('','$asoc_id','$scara_id','$locatar_id','$luna','$debit','$data_scad','$data_plata','$nrZile','$procPen','$penalizare')") or die(mysql_error());
		break;
		}
	if($data_scad>$row['sDate'] && $data_plata>$row['fDate']) {
		$nrZile = days_between_dates($row['fDate'],$data_scad);
		$procPen = $row['value'];
		$penalizare = $debit * $proPen/100 * $nrZile;
		while($penalizare>$debit) {
			$nrZile--;
			$penalizare = $debit * $proPen/100 * $nrZile;
			}
		$data_plata = $row['fDate'];
		die('Eroarea 2 in final\fisa_penalizari.php');
		mysql_query("INSERT INTO fisa_pen VALUE('','$asoc_id','$scara_id','$locatar_id','$luna','$debit','$data_scad','$data_plata','$nrZile','$procPen','$penalizare')") or die(mysql_error());
		$continue = 1;
		}
	
	}
}






	//$sql = mysql_query("INSERT INTO fisa_pen VALUES('','$asoc_id','$scara_id','$locatar_id','$luna','$debit','$data_scad','$data_plata','$nrzile','$proc_pen','$val_pen')");
	
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['locatar']<>null)) {
	$asoc_id = $_GET['asoc_id'];
	$scara_id = $_GET['scara_id'];
	$locatar_id = $_GET['locatar'];
$sql0 = "SELECT * FROM fisa_pen WHERE loc_id=".$locatar_id;
//echo $sql0;
$result = mysql_query($sql0) or die(mysql_error());
//echo 'sunt '.mysql_num_rows($result);
while($row = mysql_fetch_assoc($result)) {
$continut[] = $row;
}
}

if ($_GET['asoc_id']<>null){	
	$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id'];
} else {	
	$sql = "SELECT * FROM asociatii";
}

$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {	
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
}
if($_GET['asoc_id']<>null) {	if ($_GET['scara_id']<>null){ 		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id']; 	} else {		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];	}	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());	while ($row2 = mysql_fetch_array($sql2)) {		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';		}	}if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {	if ($_GET['locatar']<>null){ 		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND loc_id<>".$_GET['locatar']; 	} else {		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];	}	$sql3 = mysql_query($sql3);	while($row3 = mysql_fetch_array($sql3)) {		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';		}	}

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
</style>

<div id="content" style="float:left;">
<table width="400">	<tr>    	<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>		<td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 										$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];					$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());										echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';   				}?>        		<?php echo $asociatii; ?>            </select></td>    </tr>    <?php if($_GET['asoc_id']<>null):?>    <tr>    	<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 					$scara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];					$scara = mysql_query($scara) or die ("Nu pot selecta scarile<br />".mysql_error());										echo '<option value="">'.mysql_result($scara, 0, 'scara').'</option>';				}?>        		<?php  echo $scari; ?>            </select></td>    </tr>    <?php endif;?>      <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>    <tr>    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti locatarul:</td>        <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">        <?php  if($_GET['locatar']==null)  { echo '<option value="">----Alege----</option>';    }  else { 			$locatar = "SELECT * FROM locatari WHERE loc_id=".$_GET['locatar'];			$locatar = mysql_query($locatar) or die ("Nu pot selecta locatarii<br />".mysql_error());								echo '<option value="">'.mysql_result($locatar, 0, 'nume').'</option>';				}?>        <?php echo $locatari; ?>        </select></td>    </tr>    <?php endif;?></table>
    


</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['locatar']<>null)):?>
  
 <?php  if(!has_history($_GET['asoc_id'])) {   
 echo '<br clear="left" />';
 echo '<form action="" method="post">';
 echo '<div style="float:left; text-align:left;">';
 echo "<h2 style='margin-bottom:0px; padding-bottom:0px;'>Asociatia selectata nu are setat un istoric al procentului de penalizare.</h2>";
 echo "<em>Pana nu setati un istoric al procentului de penalizare nu veti putea introduce istoricul penalizarilor pentru nici un locatar din asociatia selectata.</em>";
 echo "<br /><br /><div id='periods'><p class='hPenPara'>Penalizare: <input type='text' style='width:20px;' maxlength='4' name='proc0' /> % incepand cu data <input type='text' maxlength='10' class='datepicker' style='width:70px;' name='data0' /></p></div>";
 echo "<br clear='left' /><input type='button' id='addPeriod' value=' + ' /> <input type='submit' name='finalP' value=' Salveaza ' />";
 echo "<input type='hidden' name='maxim' value='1' class='maxim' /></div>";
 echo "</form>";
 }
 ?>
  <?php  if(has_history($_GET['asoc_id'])):?> 
  
  <!-- <a href="#" class="pdf1" style="border:none;"></a> -->
  <form action="" method="post">
<table width="950" style="float:left; margin-top:50px; background-color:white;">
<thead>
<tr>
  <td bgcolor="#666666">Luna</td>
  <td bgcolor="#666666">Valoare Debit</td>
  <td bgcolor="#666666">Data scadenta</td>
  <td bgcolor="#666666">Data Platii</td>
  <td bgcolor="#666666">Nr.Zile</td>
  <td bgcolor="#666666">Procentul de penalizare</td>
  <td bgcolor="#666666">Valoarea penalizarii</td>
</tr>
</thead>
<tbody>
<?php   
//$proc_pen = get_proc_pen($_GET['asoc_id']);
if(!empty($continut)) {
	//print_r($continut);
	$i=2;
foreach($continut as $line) {
if($i%2 == 0) { $color = "CCC"; } else { $color = "EEE"; } 
if($line['data_platii']<>"0000-00-00") { $data_platii = date_change_two($line['data_platii']); } else {  $data_platii = ""; }
echo '<tr bgcolor="#'.$color.'">
  <td align="center">'.$line['luna'].'</td>
  <td align="center">'.$line['valoare_debit'].'</td>
  <td align="center">'.date_change_two($line['data_scadenta']).'</td>
  <td align="center">'.$data_platii.'</td>
  <td align="center">'.$line['nr_zile'].'</td>
  <td align="center">'.$line['proc_pen'].'%</td>
  <td align="center">'.$line['val_pen'].'</td>
</tr>'; 
	

$i++;
}
} 
?>
<?php   
if(empty($continut)) { ?>
<tr bgcolor="#FFF" class="newline">
  <td align="center"><input name="luna" type="text" id="luna" /></td>
  <td align="center"><input name="debit" type="text" id="debit" /></td>
  <td align="center"><input name="data_scad" type="text" id="data_scad" class="datepicker" /></td>
  <td align="center"><input name="data_plata" type="text" id="data_plata" class="datepicker" /></td>
  <td align="center"><input name="temp" type="text" id="temp" disabled="disabled" /></td>
  <td align="center"><input name="temp2" type="text" id="temp2" disabled="disabled" value="<?php  echo $proc_pen; ?>" /></td>
  <td align="center"><input name="val_pen" type="text" id="val_pen" /><input name="adauga2" type="submit" class="addnew" id="adauga2" value=" " /></td>
</tr>
<?php  } else {   ?>
<tr bgcolor="#FFF" class="newline">
  <td align="center"><input name="luna" type="text" id="luna" /></td>
  <td align="center"><input name="debit" type="text" id="debit" /></td>
  <td align="center"><input name="data_scad" type="text" id="data_scad" class="datepicker" /></td>
  <td align="center"><input name="data_plata" type="text" id="data_plata" class="datepicker" /></td>
  <td align="center"><input name="temp" type="text" id="temp" disabled="disabled" /></td>
  <td align="center"><input name="temp2" type="text" id="temp2" disabled="disabled" value="<?php  echo $proc_pen; ?>" /></td>
  <td align="center"><input name="adauga" type="submit" class="addnew2" id="adauga" value=" " /></td>
</tr>
<?php   }  ?>
</tbody>
<tfoot>
<tr bgcolor="#FFF">
  <td align="center"></td>
  <td align="center"></td>
  <td align="center"></td>
  <td align="center"></td>
  <td align="center"></td>
  <td align="center"></td>
  <td align="center" bgcolor="#666666" >0</td>
</tr>
</tfoot>
</table>
</form>
<?php endif; ?>
<?php endif; ?>