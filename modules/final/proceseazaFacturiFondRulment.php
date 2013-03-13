<?php

$inpartiePeSuprafata = intval($proc['mod_impartire']) == 0 ? false : true;

echo '<br />Factura de F. Rul. se imparte pe '.( $inpartiePeSuprafata ? 'sup' : 'ap' );

$supTot = "SELECT SUM((".( $inpartiePeSuprafata ? 'L.supr' : '1' )." * IFNULL( S.procent, 100 )) / 100) as total FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
$supTot = mysql_query($supTot) or die ("Nu pot afla suprafata totala a apartamentelor<br />".mysql_error());
$supTot = mysql_result($supTot, 0, 'total');

//pretul pe care il plateste fiecare locatar
$ppu = $cost / $supTot;
if (mysql_num_rows($sql1) != 0) {
    while ($row = mysql_fetch_array($sql1)) {
      $iiDamSaDuca = "SELECT L.*, IFNULL( S.procent, 100 ) AS sub FROM `locatari` L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE L.`loc_id`=".$row['loc_id'];
      $iiDamSaDuca = mysql_query($iiDamSaDuca) or die("Nu pot selecta locatarul<br />".mysql_error());

        $aveaDePlata = $row['fond_rul_rest'];
        $aveaDePlata = $aveaDePlata + round($ppu * ( $inpartiePeSuprafata ? mysql_result($iiDamSaDuca, 0, 'supr') : 1 ) * mysql_result($iiDamSaDuca, 0, 'sub') / 100, 2);

        $fondRulment = "UPDATE fisa_fonduri SET fond_rul_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$lunaUrm."'";
        $fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de rulment in fisa fonduri<br />".mysql_error());

    	$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$row['loc_id']."', '$luna', '$tipServiciu', '".( $inpartiePeSuprafata ? mysql_result($iiDamSaDuca, 0, 'supr') : 1 ) * mysql_result($iiDamSaDuca, 0, 'sub') / 100 ."', '".($ppu)."', '".( $inpartiePeSuprafata ? 'mp' : 'ap' )."','".$serieFactura.'/'.$numarFactura."','$ppu', '$supTot')";
    	$plateste = mysql_query($plateste) or die ("Nu pot insera fondul in fisa individuala<br />".mysql_error());
    }
} else {
  $iiDamDePlata = "SELECT L.*, IFNULL( S.procent, 100 ) AS sub FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$tipServiciu.") S ON L.loc_id = S.loc_id WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
  $iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());

    //calculez luna anterioara
    //$luna1 = explode("-",$luna);
    //$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
    //$lunaAnt = date('m-Y',$lunaAnt);
  $lunaAnt = $luna;

    while ($amLocatar = mysql_fetch_array($iiDamDePlata)) {
        //restul de plata de luna trecuta
        //$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
    	//nu se mai ia luna trecuta ci se ia ultima luna a acestei persoane ... astfel se acopera posibilitatea ca intr-o luna sa nu apara nici o intrare in fisa fond.
    	$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY STR_TO_DATE(data, '%m-%Y') DESC LIMIT 1";
		  $aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

        if (mysql_num_rows($aveaDePlata) != 0) {
            $fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
            $fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
            $fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
            $fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
            $luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest;
        } else {
            $fondRulRest = 0;
            $fondRepRest = 0;
            $fondSpecRest = 0;
            $fondPenConst = 0;
            $luna_trecuta = 0;
        }

        //insertul pt fiecare locatar
        $fondRulRest =  round($ppu * ( $inpartiePeSuprafata ? $amLocatar['supr'] : 1 ) * $amLocatar['sub'] / 100, 2) + $fondRulRest;

        $iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$lunaUrm', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
        $iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());

    	$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$amLocatar['loc_id']."', '$luna', '$tipServiciu', '".( $inpartiePeSuprafata ? $amLocatar['supr'] : 1 ) * $amLocatar['sub'] / 100 ."', '".($ppu) ."', '".( $inpartiePeSuprafata ? 'mp' : 'ap' )."','".$serieFactura.'/'.$numarFactura."','$cost', '1')";
    	$plateste = mysql_query($plateste) or die ("Nu pot insera fondul in fisa individuala<br />".mysql_error());
    }
}