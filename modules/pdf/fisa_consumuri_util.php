<?php
$consumuri_util_load = 1;

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
	$consum = mysql_query($consum) or die("Error #03 <br />".mysql_error());
	if (mysql_num_rows($consum)<>0) {
		$cantitatePersonala =  mysql_result($consum, 0, 'cant_fact_pers');
		$pretUnitar = mysql_result($consum, 0, 'pret_unitar');
		$pretUnitarFactura = mysql_result($consum, 0, 'pret_unitar2');
		$consum = $pretUnitarFactura == 0 ? 0 : round((($cantitatePersonala * $pretUnitar) / $pretUnitarFactura), 2);
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
			$consum2 = $pretUnitarFactura == 0 ? 0 : round((($cantitatePersonala * $pretUnitar) / $pretUnitarFactura), 2);

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
	$sql = "SELECT * FROM locatari WHERE scara_id=".$scara_id." AND gaz='da'";
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
	$rez = "";
	if(($asoc<>null) && ($scara<>null) && ($locatar<>null)) {
		$sql = "SELECT ap_rece,ap_calda FROM locatari WHERE asoc_id=".$asoc." AND scara_id=".$scara." AND loc_id=".$locatar;
		$sql = mysql_query($sql) or die("Error #09 <br />".mysql_error());
		$nr_ap_rece = mysql_result($sql,0,'ap_rece');
		$nr_ap_calda = mysql_result($sql,0,'ap_calda');


		for($i=0;$i<12;$i++) { $month_for_tabel[] = mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")); }
		$color = "#EEEEEE";
		for($j=0;$j<$nr_ap_rece;$j++) {
			if($color == "#EEEEEE")  {  $color = "#CCCCCC"; } else {  $color = "#EEEEEE"; }
			$rez .= '<tr bgcolor="'.$color.'">
  <td align="center">Apa rece 0'.($j+1).'</td>
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
			$rez .= '<tr bgcolor="'.$color.'">
  <td align="center">Apa calda 0'.($k+1).'</td>
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
	return $rez;
}