<?php
$locatari = explode(',', $locatari);
$ppu = explode(',', $proc['ppu']);

if (mysql_num_rows($sql1) != 0) {
    while ($row = mysql_fetch_array($sql1)) {
        if (in_array($row['loc_id'], $locatari)) {

        	foreach ($locatari as $key=>$loc )
        		if ($loc == $row['loc_id'])
        			$ppuloc = $ppu[$key];

            $aveaDePlata = $row['fond_spec_rest'];
            $aveaDePlata = $aveaDePlata + $ppuloc;

            $fondRulment = "UPDATE fisa_fonduri SET fond_spec_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$lunaUrm."'";
            $fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de reparatii<br />".mysql_error());

        	$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$row['loc_id']."', '$luna', '$tipServiciu', '1', '".($ppuloc)."', 'ap','".$serieFactura.'/'.$numarFactura."','$cost', '1')";
        	$plateste = mysql_query($plateste) or die ("Nu pot insera fondul in fisa individuala<br />".mysql_error());
        }
    }
} else {
    $iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
	$iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());

    //calculez luna anterioara
    //$luna1 = explode("-",$luna);
    //$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
    //$lunaAnt = date('m-Y',$lunaAnt);
    $lunaAnt = $luna;

    while ($amLocatar = mysql_fetch_array($iiDamDePlata)) {
        if (in_array($amLocatar['loc_id'], $locatari)) {
            //restul de plata de luna trecuta
            //$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
        	//nu se mai ia luna trecuta ci se ia ultima luna a acestei persoane ... astfel se acopera posibilitatea ca intr-o luna sa nu apara nici o intrare in fisa fond.
        	$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY id_fond DESC LIMIT 1";
			$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

            if (mysql_num_rows($aveaDePlata) != 0) {
                $fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
                $fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
                $fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
                $fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
                $luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
            } else {
                $fondRulRest = 0;
                $fondRepRest = 0;
                $fondSpecRest = 0;
                $fondPenConst = 0;
                $luna_trecuta = 0;
            }

        	foreach ($locatari as $key=>$loc )
        		if ($loc == $amLocatar['loc_id'])
        			$ppuloc = $ppu[$key];

            //insertul pt fiecare locatar
            $fondSpecRest = $ppuloc + $fondSpecRest;

            $iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$lunaUrm', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
            $iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());

        	$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$amLocatar['loc_id']."', '$luna', '$tipServiciu', '1', '".($ppuloc)."', 'ap','".$serieFactura.'/'.$numarFactura."','$cost', '1')";
        	$plateste = mysql_query($plateste) or die ("Nu pot insera fondul in fisa individuala<br />".mysql_error());

        } else {
            //$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
        	//nu se mai ia luna trecuta ci se ia ultima luna a acestei persoane ... astfel se acopera posibilitatea ca intr-o luna sa nu apara nici o intrare in fisa fond.
        	$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." ORDER BY id_fond DESC LIMIT 1";
        	$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());

            if (mysql_num_rows($aveaDePlata) != 0) {
                $fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
                $fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
                $fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
                $fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
                $luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
            } else {
                $fondRulRest = 0;
                $fondRepRest = 0;
                $fondSpecRest = 0;
                $fondPenConst = 0;
                $luna_trecuta = 0;
            }

            $iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$lunaUrm', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
            $iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul special<br />".mysql_error());
			
			$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$amLocatar['loc_id']."', '$luna', '$tipServiciu', '1', '".($ppuloc)."', 'ap','".$serieFactura.'/'.$numarFactura."','$cost', '1')";
        	$plateste = mysql_query($plateste) or die ("Nu pot insera fondul in fisa individuala<br />".mysql_error());

        }
    }
}