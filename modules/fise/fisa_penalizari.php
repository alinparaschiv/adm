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

function afiseazaPenalizari($ascoId, $scaraId, $locId){
	//verificarePenalizari($ascoId, $scaraId, $locId);

	$sql = "SELECT P.* FROM fisa_pen P, fisa_cont C WHERE P.id_restanta=C.id AND P.`loc_id`=".$locId." ORDER BY C.data, C.id";
	$sql = mysql_query($sql) or die(mysql_error());

$i=1;

	$totalPenalizari = 0;

	while($row = mysql_fetch_array($sql)) {

		if ($row['data_platii'] == null) {
			$row['data_platii'] = date('Y-m-d');
			$date = explode('-', $row['data_scadenta']);
			$z1 = mktime(0,0,0, date('m'), date('j'), date('Y'));
			$z2 = mktime(0,0,0, $date[1], $date[2], $date[0]);
			$zile = round(($z1 - $z2) / 86400);
			$row['nr_zile'] = $zile;
			$row['val_pen'] = $zile * (floatval($row['proc_pen']) / 100) * $row['valoare_debit'];
		}

	  $row['val_pen'] = round($row['val_pen'], 2);

		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	  if($row['val_pen'] > 0) {
	    echo '<tr>';
        echo '<td bgcolor="'.$color.'">'.$row['luna'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['valoare_debit'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['data_scadenta'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['data_platii'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['nr_zile'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['proc_pen'].'</td>';
        echo '<td bgcolor="'.$color.'">'.$row['val_pen'].'</td>';
	    echo ' </tr>';
		  $i++;

		  $totalPenalizari += $row['val_pen'];
	  }
	}

	return $totalPenalizari;
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
$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id"; }
else {	$sql = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";}
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	}
if($_GET['asoc_id']<>null) {	if ($_GET['scara_id']<>null){ 		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id']; 	} else {		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];	}	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());	while ($row2 = mysql_fetch_array($sql2)) {		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';		}	}if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {	if ($_GET['locatar']<>null){ 		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id']." AND loc_id<>".$_GET['locatar']; 	} else {		$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];	}	$sql3 = mysql_query($sql3);	while($row3 = mysql_fetch_array($sql3)) {		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';		}	}

?>
<style type="text/css">
thead tr td {
	border:solid 1px #000;
	color:#FFF;
}
tbody {
	border:solid 1px #000;
}
tbody tr td input {
	width:100%;
	border:none;
	height:100%;
}
tbody tr.newline td {
	border:solid 1px #0CC;
}
tfoot {
	color:#FFF;
}
.addnew {
	position:absolute;
	width:120px;
	background-color:none;
	background-image:url(images/adauga.jpg);
	width:19px;
	height:20px;
	border:none;
	background-color:none;
	cursor:pointer;
	margin-left:5px;
}
.addnew2 {
	position:absolute;
	width:120px;
	background-color:none;
	background-image:url(images/adauga.jpg);
	width:19px;
	height:20px;
	border:none;
	background-color:none;
	cursor:pointer;
	margin-left:95px;
	margin-top:-9px;
}
tr.newline input {
	text-align:center;
}
.pdf1 {
	clear:both;
	width:51px;
	height:51px;
	float:left;
	background-image:url(images/pdf_down.jpg);
	margin-left:900px;
	margin-top:-20px;
	text-decoration:none;
	border-bottom:0px solid white;
}
a.pdf1:hover {
	background-image:url(images/pdf_up.jpg);
}
</style>
<div id="content" style="float:left;">
  <table width="400">
    <tr>
      <td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
      <td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
          <?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 										$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];					$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());										echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';   				}?>
          <?php echo $asociatii; ?>
        </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
      <td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
      <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
          <?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 					$scara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];					$scara = mysql_query($scara) or die ("Nu pot selecta scarile<br />".mysql_error());										echo '<option value="">'.mysql_result($scara, 0, 'scara').'</option>';				}?>
          <?php  echo $scari; ?>
        </select></td>
    </tr>
    <?php endif;?>
    <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
    <tr>
      <td align="left" bgcolor="#CCCCCC">(3/3) Alegeti locatarul:</td>
      <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
          <?php  if($_GET['locatar']==null)  { echo '<option value="">----Alege----</option>';    }  else { 			$locatar = "SELECT * FROM locatari WHERE loc_id=".$_GET['locatar'];			$locatar = mysql_query($locatar) or die ("Nu pot selecta locatarii<br />".mysql_error());								echo '<option value="">'.mysql_result($locatar, 0, 'nume').'</option>';				}?>
          <?php echo $locatari; ?>
        </select></td>
    </tr>
    <?php endif;?>
  </table>
</div>
<?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['locatar']<>null)):?>


  	<a class="pdf1" style="border:none;" target="_blank" href="modules/pdf/pdf.php?<?php echo 'afisare=ok&FPen=0&asoc_id='.$_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&loc_id='.$_GET['locatar'] ;?>"></a>

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
     <?php $total = afiseazaPenalizari($_GET['asoc_id'], $_GET['scara_id'], $_GET['locatar'])?>
     <tr>
     	<td bgcolor="#aaaaaa" colspan="6">Total:</td>
     	<td bgcolor="#aaaaaa"><?php echo round($total, 2); ?></td>
	 </tr>
    </tbody>
  </table>
</form>

<?php include_once("Penalizare.class.php");
	$pen = new Penalizare( $_GET['locatar'], $_GET['scara_id'], $_GET['asoc_id']);
?>

<?php endif; ?>