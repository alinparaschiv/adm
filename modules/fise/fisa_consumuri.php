<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=fisa_cons&asoc_id=" + value;
}
function select_scara(value,value2) {
 window.location = "index.php?link=fisa_cons&asoc_id=" + value + "&scara_id=" + value2;
}
function select_locatar(value,value2,value3) {
 window.location = "index.php?link=fisa_cons&asoc_id=" + value + "&scara_id=" + value2 + "&locatar=" + value3;
}

</script>
<?php
// FUNCTIONS
function format($data) {
$data = explode("-",$data);
switch($data[0]) {
	case "01":
	$month = "Ian";
	break;
	case "02":
	$month = "Feb";
	break;
	case "03":
	$month = "Mar";
	break;
	case "04":
	$month = "Apr";
	break;
	case "05":
	$month = "Mai";
	break;
	case "06":
	$month = "Iun";
	break;
	case "07":
	$month = "Iul";
	break;
	case "08":
	$month = "Aug";
	break;
	case "09":
	$month = "Sep";
	break;
	case "10":
	$month = "Oct";
	break;
	case "11":
	$month = "Nov";
	break;
	case "12":
	$month = "Dec";
	break;
}
	return $month."-".$data[1];
}


function convert($string) {
	return format(date("m-Y",$string));
}

function getConsumServiciu($asocId, $scaraId, $locId, $pos, $serviciu, $serviciu2 = null ){
	$luna_curenta = mktime(0, 0, 0, date("m")-$pos, date("d"),   date("Y"));
	$luna_curenta = date('m-Y',$luna_curenta);

	$serviciuId = "SELECT serv_id FROM servicii WHERE serviciu='".$serviciu."'";
	$serviciuId =  mysql_query($serviciuId) or die("Error #01 <br />".mysql_error());
	$serviciuId = mysql_result($serviciuId, 0, 'serv_id');


	$serviciuId2;
	if ($serviciu2 != null) {
		$serviciuId2 = "SELECT serv_id FROM servicii WHERE serviciu='".$serviciu2."'";
		$serviciuId2 =  mysql_query($serviciuId2) or die("Error #02 <br />".mysql_error());
		$serviciuId2 = mysql_result($serviciuId2, 0, 'serv_id');
	}


	$consum = "SELECT * FROM fisa_indiv WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$locId." AND luna='".$luna_curenta."' AND serviciu=".$serviciuId;
	//if ($serviciuId == 27 && $pos == 1) { var_dump($consum); die();}
	$consum = mysql_query($consum) or die("Error #03 <br />".mysql_error());
	if (mysql_num_rows($consum)<>0) {
		$cantitatePersonala =  mysql_result($consum, 0, 'cant_fact_pers');
		$pretUnitar = mysql_result($consum, 0, 'pret_unitar');
		$pretUnitarFactura = mysql_result($consum, 0, 'pret_unitar2');
		$consum = round((($cantitatePersonala * $pretUnitar) / $pretUnitarFactura), 2);
	} else {
		$consum = 'n/a';
	}
	if ($serviciu2 != null) {
		$consum2 = "SELECT * FROM fisa_indiv WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$locId." AND luna='".$luna_curenta."' AND serviciu=".$serviciuId2;
		$consum2 = mysql_query($consum2) or die("Error #04 <br />".mysql_error());
		if (mysql_num_rows($consum2)<>0) {
			$cantitatePersonala =  mysql_result($consum2, 0, 'cant_fact_pers');
			$pretUnitar = mysql_result($consum2, 0, 'pret_unitar');
			$pretUnitarFactura = mysql_result($consum2, 0, 'pret_unitar2');
			$consum2 = round((($cantitatePersonala * $pretUnitar) / $pretUnitarFactura), 2);

			return is_numeric($consum) ? ($consum + $consum2) : $consum2;
		}
	}
	return $consum;
}

########################## CONSUM TOTAL ASOCIATIE #################################
function getC_asociatie($asoc,$pos) {
	// Luna este de forma 05-2010
	$pos2 = $pos + 1;
	$luna_curenta = mktime(0, 0, 0, date("m")-$pos, date("d"),   date("Y"));
	$luna_curenta = date('m-Y',$luna_curenta);
	$luna_anterioara = mktime(0, 0, 0, date("m")-$pos2, date("d"),   date("Y"));
	$luna_anterioara = date('m-Y',$luna_anterioara);

	$total_lunaCur = 0;
	$sql = "SELECT r1+r2+r3+r4+r5+c1+c2+c3+c4+c5 AS total FROM apometre WHERE asoc_id=".$asoc." AND luna='".$luna_curenta."'";
	$sql = mysql_query($sql);
		if(mysql_num_rows($sql) == 0)
			{ $total_lunaCur = 0; }
		else
			{
				while($row = mysql_fetch_assoc($sql)) {  $total_lunaCur += $row['total'];   }
				//$total_lunaCur = mysql_result($sql,0,'total');
			}
	$total_lunaAnt = 0;
	$sql2 = "SELECT r1+r2+r3+r4+r5+c1+c2+c3+c4+c5 AS total FROM apometre WHERE asoc_id=".$asoc." AND luna='".$luna_anterioara."'";
	$sql2 = mysql_query($sql2);
			if(mysql_num_rows($sql2) == 0)
				{

					$sql3 = "SELECT r1+r2+r3+r4+r5+c1+c2+c3+c4+c5 AS total FROM locatari_apometre WHERE asoc_id=".$asoc;
					$sql3 = mysql_query($sql3);
					while($row2 = mysql_fetch_assoc($sql3)) {  $total_lunaAnt += $row2['total'];   }
					//$total_lunaAnt = mysql_result($sql3,0,'total');

				}
			else
				{
					while($row3 = mysql_fetch_assoc($sql2)) {  $total_lunaAnt += $row3['total'];   }
					//$total_lunaAnt = mysql_result($sql2,0,'total');
				}
	$total = $total_lunaCur-$total_lunaAnt;
	if($total <= 0) { echo 'n/a';  }
	else { echo $total; }

	}
###################################################################################
###################################################################################

############################## ALTE FUNCTII #######################################

function countAp() {
$sql = "SELECT * FROM locatari WHERE scara_id=".$_GET['scara_id']." AND gaz='da'";
$sql = mysql_query($sql) or die ("Nu pot afla numarul de apartamente care are gaz<br />".mysql_error());
return mysql_num_rows($sql);
}
####################################################################################

function months() {
for($i=0;$i<12;$i++) {
$month_for_tabel[] = convert(mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
}
return $month_for_tabel;
}

$month_for_tabel = months();

function get_apometru_rece($asoc,$scara,$locatar,$month,$j) {
	$data = date("m-Y",$month);

	$dataT = explode("-",$data);
	$lunaTrecutaT = mktime(0,0,0,$dataT[0]+1,1,$dataT[1]);
	$dataT = date("m-Y",$lunaTrecutaT);


	$ap = "r".($j+1);
	$sql = "SELECT r".($j+1).",auto FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$data."'";
	//echo $sql."<br />";
	$sql = mysql_query($sql);
	if(mysql_num_rows($sql) == 0) {


	$sqlT = "SELECT a_id FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$dataT."'";
	$sqlT = mysql_query($sqlT);
	if(mysql_num_rows($sqlT)<>0) {
		$sqlInit = "SELECT * FROM locatari_apometre WHERE loc_id='".$locatar."'";
		$sqlInit = mysql_query($sqlInit);
		return "<b>".mysql_result($sqlInit,0,$ap)."</b>";

		} else {
	return '<b>X</b>'; }




	} else {
		if(mysql_result($sql,0,'auto')==1) {
	return "<b style='width:100%; height:100%; float:left; background-color:red; color:white;'>".mysql_result($sql,0,$ap)."</b>"; } else {      return "<b>".mysql_result($sql,0,$ap)."</b>";               }}
		}

function get_apometru_calda($asoc,$scara,$locatar,$month,$k) {
	$data = date("m-Y",$month);

	$dataT = explode("-",$data);
	$lunaTrecutaT = mktime(0,0,0,$dataT[0]+1,1,$dataT[1]);
	$dataT = date("m-Y",$lunaTrecutaT);

	$ap = "c".($k+1);
	$sql = "SELECT c".($k+1).",auto FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$data."'";
	$sql = mysql_query($sql);
	if(mysql_num_rows($sql) == 0) {

	$sqlT = "SELECT a_id FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$dataT."'";
	$sqlT = mysql_query($sqlT);
	if(mysql_num_rows($sqlT)<>0) {
		$sqlInit = "SELECT * FROM locatari_apometre WHERE loc_id='".$locatar."'";
		$sqlInit = mysql_query($sqlInit);
		return "<b>".mysql_result($sqlInit,0,$ap)."</b>";

		} else {
	return '<b>X</b>'; }

	} else {  if(mysql_result($sql,0,'auto')==1) {
	return "<b style='width:100%; height:100%; float:left; background-color:red; color:white;'>".mysql_result($sql,0,$ap)."</b>"; } else {      return "<b>".mysql_result($sql,0,$ap)."</b>";               } }
		}

function get_consum($asoc,$scara,$locatar,$pos,$tip) {
if(($asoc<>null) && ($scara<>null) && ($locatar<>null)) {
	$pos2 = $pos + 1;
	$luna_curenta = mktime(0, 0, 0, date("m")-$pos, date("d"),   date("Y"));
	$luna_curenta = date('m-Y',$luna_curenta);
	$luna_anterioara = mktime(0, 0, 0, date("m")-$pos2, date("d"),   date("Y"));
	$luna_anterioara = date('m-Y',$luna_anterioara);

	if($tip == "rece") {
	$sql = "SELECT r1+r2+r3+r4+r5 AS total FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$luna_curenta."' AND (r1<>'X' OR r2<>'X' OR r3<>'X' OR r4<>'X' OR r5<>'X')";
	$sql = mysql_query($sql) or die("Error #05 <br />".mysql_error());
	$con_cur;
	if(mysql_num_rows($sql)==0) { $con_cur = 'n/a';  } else {  $con_cur = mysql_result($sql,0,'total');  }

	$sql = "SELECT r1+r2+r3+r4+r5 AS total FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$luna_anterioara."'";
	$sql = mysql_query($sql) or die("Error #06 <br />".mysql_error());
	$con_ant;
	if(mysql_num_rows($sql)==0) {
		// Find vorba de luna curenta, verificam pentru apometrele initiale
		$sql2 = "SELECT r1+r2+r3+r4+r5 AS total FROM locatari_apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar;
		$sql2 = mysql_query($sql2);
		$con_ant = mysql_result($sql2,0,'total');


	} else {  $con_ant = mysql_result($sql,0,'total');  }

	if(is_numeric($con_cur) && is_numeric($con_ant) && $con_cur>=0 && $con_ant>=0) {  $total = $con_cur - $con_ant;     }
	else { $total = "n/a"; }
	//if(is_numeric($total)) { $total = "<b>".$total."</b>"; }
	return $total;
	}

	if($tip == "calda") {
	$sql = "SELECT c1+c2+c3+c4+c5 AS total FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$luna_curenta."' AND (c1<>'X' OR c2<>'X' OR c3<>'X' OR c4<>'X' OR c5<>'X')";
	$sql = mysql_query($sql) or die("Error #07 <br />".mysql_error());
	if(mysql_num_rows($sql)==0) { $con_cur = 'n/a';  } else {  $con_cur = mysql_result($sql,0,'total');  }

	$sql = "SELECT c1+c2+c3+c4+c5 AS total FROM apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar." AND luna='".$luna_anterioara."'";
	$sql = mysql_query($sql) or die("Error #08 <br />".mysql_error());
	if(mysql_num_rows($sql)==0) {

		// Find vorba de luna curenta, verificam pentru apometrele initiale
	$sql2 = "SELECT c1+c2+c3+c4+c5 AS total FROM locatari_apometre WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar;
	$sql2 = mysql_query($sql2);
	$con_ant = mysql_result($sql2,0,'total');

		} else {  $con_ant = mysql_result($sql,0,'total');  }
	if(is_numeric($con_cur) && is_numeric($con_ant) && $con_cur>0 && $con_ant>0) {  $total = $con_cur - $con_ant;     }

	else { $total = "n/a"; }
	//echo $con_cur."<br />";
	//if(is_numeric($total)) { $total = "<b>".$total."</b>"; }
	return $total;
	}
	}
}

function tabel_indecsi_apometre($asoc,$scara,$locatar) {
	if(($asoc<>null) && ($scara<>null) && ($locatar<>null)) {
		$sql = "SELECT ap_rece,ap_calda FROM locatari WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar;
		$sql = mysql_query($sql) or die("Error #09 <br />".mysql_error());
			$nr_ap_rece = mysql_result($sql,0,'ap_rece');
			$nr_ap_calda = mysql_result($sql,0,'ap_calda');


	for($i=0;$i<12;$i++) { $month_for_tabel[] = mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")); }
	 $color = "#EEEEEE";
	for($j=0;$j<$nr_ap_rece;$j++) {
		if($color == "#EEEEEE")  {  $color = "#CCCCCC"; } else {  $color = "#EEEEEE"; }
		echo '<tr bgcolor="'.$color.'">
  <td align="center">Ar0'.($j+1).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[0],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[1],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[2],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[3],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[4],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[5],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[6],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[7],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[8],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[9],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[10],$j).'</td>
  <td align="center">'.get_apometru_rece($asoc,$scara,$locatar,$month_for_tabel[11],$j).'</td>
</tr>';
	}
	for($k=0;$k<$nr_ap_calda;$k++) {
		if($color == "#EEEEEE")  {  $color = "#CCCCCC"; } else {  $color = "#EEEEEE"; }
		echo '<tr bgcolor="'.$color.'">
  <td align="center">Ac0'.($k+1).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[0],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[1],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[2],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[3],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[4],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[5],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[6],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[7],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[8],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[9],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[10],$k).'</td>
  <td align="center">'.get_apometru_calda($asoc,$scara,$locatar,$month_for_tabel[11],$k).'</td>
</tr>';
	}
}
}

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

thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { width:100%; border:none; }
tbody tr.newline td { border:solid 1px #0CC;   }
tfoot { color:#FFF; }
.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
tr.newline input { text-align:center; }
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
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
  <a  target="_blank" href="modules/pdf/pdf.php?<?php echo 'afisare=ok&FC=0&luna='.date('m-Y').'&asoc_id='.$_GET['asoc_id'].'&scara_id='.$_GET['scara_id'].'&loc_id='.$_GET['locatar'] ;?>" class="pdf1" style="border:none;"></a>
  <form action="" method="post" style="top:250px;"><br clear="all" />
  <h2 style="float:left; padding:0; margin:0;">Tabel indecsi apometre</h2>
  <br clear="left" />
<table width="950" style="float:left; background-color:white;">
<thead>
<tr>
  <td width="158" bgcolor="#666666">Luna</td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[0];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[1];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[2];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[3];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[4];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[5];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[6];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[7];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[8];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[9];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[10];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[11];  ?></td>
</tr>
</thead>
<tbody>


<?php tabel_indecsi_apometre($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar']);
?>




</tbody>
<tfoot>
<tr bgcolor="#CCCCCC" style="color:black;">
  <td colspan="13" align="center">Legenda: indecsii declarati de locatari vor fi marcati diferit de cei completati autmoat de calculator; Ar01=apometru apa rece; Ac01=apometru apa calda.</td>
  </tr>
</tfoot>
</table>
  <br clear="left" />
  <h2 style="float:left; padding:0; margin:0;">Tabel consum apa (mc)</h2>
  <br clear="left" />
<table width="950" style="float:left; background-color:white;">
<thead>
<?php $apa = array (array(0 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0), array(), array(), array()); ?>
<tr>
  <td width="158" bgcolor="#666666">Luna</td>
<?php foreach ($apa[0] as $key => $val){
	$apa[0][$key] = $month_for_tabel[$key];
	echo '<td width="66" bgcolor="#666666">'.$apa[0][$key]."</td>";
} ?>

</tr>
</thead>
<tbody>

<tr bgcolor="#CCCCCC">
  <td>Apa Rece / ap</td>
  <?php foreach ($apa[0] as $key => $val){
  	$apa[1][$key] = get_consum($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],$key,"rece");
  	echo "<td>".$apa[1][$key]."</td>";
  }?>
</tr>
<tr bgcolor="#EEEEEE">
  <td>Apa Calda / ap</td>
  <?php foreach ($apa[0] as $key => $val){
  	$apa[2][$key] = get_consum($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],$key,"calda");
  	echo "<td>".$apa[2][$key]."</td>";
  }?>
</tr>

<tr bgcolor="#CCCCCC">
  <td>Diferente / ap</td>
  <?php foreach ($apa[0] as $key => $val){
  	$apa[3][$key] = getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],$key,"Diferenta apa rece pt acm", "Diferenta apa rece");
  	echo "<td>".$apa[3][$key]."</td>";
  }?>
</tr>

<tr bgcolor="#EEEEEE">
  <td>Consum Total Apa / ap</td>
	<?php foreach ($apa[0] as $key => $val){
		$val = is_numeric($apa[1][$key]) ? $apa[1][$key] : 0;
		$val += is_numeric($apa[2][$key]) ? $apa[2][$key] : 0;
		$val += is_numeric($apa[3][$key]) ? $apa[3][$key] : 0;
		if (is_numeric($apa[1][$key]) || is_numeric($apa[2][$key]) || is_numeric($apa[3][$key])) {
			echo "<td><b>".$val."</b></td>";
		} else
			echo "<td>n/a</td>";

	}?>
</tr>

<tr bgcolor="#CCCCCC">
  <td colspan="13">&nbsp;</td>
</tr>

<tr bgcolor="#EEEEEE">
  <td>Diferente / asociatie</td>
  <?php foreach ($apa[0] as $key => $val){
  	$luna_curenta = mktime(0, 0, 0, date("m")-$key, date("d"),   date("Y"));
  	$luna_curenta = date('m-Y',$luna_curenta);

  	$serv1 = "SELECT serv_id FROM servicii WHERE serviciu='Diferenta apa rece pt acm'";
  	$serv1 =  mysql_query($serv1) or die("Error #12 <br />".mysql_error());
  	$serv1 = mysql_result($serv1, 0, 'serv_id');
  	$serv2 = "SELECT serv_id FROM servicii WHERE serviciu='Diferenta apa rece'";
  	$serv2 =  mysql_query($serv2) or die("Error #13 <br />".mysql_error());
  	$serv2 = mysql_result($serv2, 0, 'serv_id');

  	$sql = "SELECT sum(`cant_fact_pers`*`pret_unitar`/`pret_unitar2`) as total FROM `fisa_indiv` WHERE `asoc_id`=".$_GET['asoc_id']." AND `luna`='".$luna_curenta."' AND (`serviciu`=$serv1 OR `serviciu`=$serv2)";
	$sql = mysql_query($sql) or die ("Nu pot afla totalul diferentelor <br />".mysql_error());
	$sql = mysql_result($sql, 0, 'total');
  	$sql = $sql != null ? round($sql, 2) : 'n/a';
	echo "<td>".$sql."</td>";
  }?>
</tr>

<tr bgcolor="#CCCCCC">
  <td>Consum Delarat / asociatie</td>
   <?php foreach ($apa[0] as $key => $val){
   	$luna_curenta = mktime(0, 0, 0, date("m")-$key, date("d"),   date("Y"));
   	$luna_curenta = date('m-Y',$luna_curenta);

   	$serv1 = "SELECT serv_id FROM servicii WHERE serviciu='Apa rece pentru apa calda'";
   	$serv1 =  mysql_query($serv1) or die("Error #14 <br />".mysql_error());
   	$serv1 = mysql_result($serv1, 0, 'serv_id');
   	$serv2 = "SELECT serv_id FROM servicii WHERE serviciu='apa rece'";
   	$serv2 =  mysql_query($serv2) or die("Error #15 <br />".mysql_error());
   	$serv2 = mysql_result($serv2, 0, 'serv_id');

   	$sql = "SELECT sum(`cant_fact_pers`*`pret_unitar`/`pret_unitar2`) as total FROM `fisa_indiv` WHERE `asoc_id`=".$_GET['asoc_id']." AND `luna`='".$luna_curenta."' AND (`serviciu`=$serv1 OR `serviciu`=$serv2)";
   	$sql = mysql_query($sql) or die ("Nu pot afla totalul diferentelor <br />".mysql_error());
   	$sql = mysql_result($sql, 0, 'total');
   	$sql = $sql != null ? round($sql, 2) : 'n/a';
   	echo "<td>".$sql."</td>";
   }?>
</tr>

</tbody>
<tfoot>
<tr bgcolor="#EEEEEE" style="color:black;">
  <td colspan="13" align="center">Legenda: Diferentele de apa rezultate se impart conform hotararii adunarii generale nr 11 din 12.12.2007</td>
  </tr>
</tfoot>
</table>
  <br clear="left" />
  <h2 style="float:left; padding:0; margin:0;">Alte consumuri</h2>
  <br clear="left" />
<table width="950" style="float:left; background-color:white;">
<thead>
<tr>
  <td width="158" bgcolor="#666666">Luna</td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[0];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[1];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[2];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[3];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[4];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[5];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[6];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[7];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[8];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[9];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[10];  ?></td>
  <td width="66" bgcolor="#666666"><?php  echo $month_for_tabel[11];  ?></td>
</tr>
</thead>
<tbody>
<tr bgcolor="#CCCCCC">
  <td width="158">Agent termic - apa calda (GKAL)</td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"0","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"1","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"2","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"3","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"4","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"5","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"6","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"7","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"8","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"9","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"10","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"11","Agent termic pentru apa calda", "Diferenta Ag Termic pt acm"); ?></td>
</tr>

<tr bgcolor="#EEEEEE">
  <td width="158">Agent termic - incalzire (GKAL)</td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"0","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"1","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"2","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"3","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"4","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"5","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"6","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"7","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"8","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"9","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"10","incalzire"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"11","incalzire"); ?></td>
</tr>

<tr bgcolor="#CCCCCC">
  <td width="158">Energie electrica spatii comune (KW)</td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"0","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"1","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"2","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"3","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"4","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"5","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"6","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"7","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"8","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"9","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"10","iluminat"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"11","iluminat"); ?></td>
</tr>

<tr bgcolor="#EEEEEE">
  <td width="158">Gaz (mc)</td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"0","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"1","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"2","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"3","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"4","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"5","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"6","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"7","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"8","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"9","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"10","gaz"); ?></td>
  <td width="66"><?php  echo getConsumServiciu($_GET['asoc_id'],$_GET['scara_id'],$_GET['locatar'],"11","gaz"); ?></td>
</tr>


</tbody>
</table>
</form>
<?php endif; ?>
