<?php
$supTot = "SELECT SUM(supr) FROM locatari WHERE scara_id=".$scaraId;
$supTot = mysql_query($supTot) or die ("Nu pot afla suprafata totala a apartamentelor<br />".mysql_error());
$supTot = mysql_result($supTot, 0, 'SUM(supr)');

$ppu = $cost / $supTot;

if (mysql_num_rows($sql1) != 0) {
    while ($row = mysql_fetch_array($sql1)) {
        $aveaDePlata = $row['fond_rep_rest'];
        $aveaDePlata = $aveaDePlata + $ppu;

        $fondRulment = "UPDATE fisa_fonduri SET fond_rep_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$luna."'";
        $fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de reparatii<br />".mysql_error());
    }
} else {
    $iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
    $iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());

    //calculez luna anterioara
    $luna1 = explode("-",$luna);
    $lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
    $lunaAnt = date('m-Y',$lunaAnt);

    while ($amLocatar = mysql_fetch_array($iiDamDePlata)) {
        //restul de plata de luna trecuta
        $aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
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

        //insertul pt fiecare locatar
        $fondRepRest = $ppu + $fondRepRest;

        $iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')";
        $iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
    }
}