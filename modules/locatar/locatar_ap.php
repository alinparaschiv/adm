<?php
    $asocId = $_GET['asoc_id'];
    if ($_POST['asociatia'] != '') {
        $asocId = mysql_real_escape_string($_POST['asociatia']);
    }
    $scaraId = $_POST['scara'];
    if ($_GET['scara_id'] != '')
        $scaraId = mysql_real_escape_string($_GET['scara_id']);
    $scaraP = $scaraId;
    echo $scaraP;
?>


<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }
        function infoScari(obj) {
            window.location = "index.php?link=locatari_ap&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=locatari_ap&asoc_id="+obj
        }

        var strArr=new Array();
</script>

<?php


$i = 0;
if ($_POST['buton'] == 'apasat' && $i == 0) {
        $query = "SELECT * FROM locatari_apometre WHERE asoc_id='$asocId'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $i = 1;
            $asociatieErr = 'Apometrele pentru aceasta asociatie au fost deja introduse!';
        }
        $q = 0;

        $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id DESC";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ap = $row['ap'];
            $locId = $row['loc_id'];
            $scara = $row['scara'];
            $scaraId =  $row['scara_id'];

            for ($q = 1; $q<=$row['ap_rece']; $q++) {
                   $r = mysql_real_escape_string($_POST ['r_'.$scara.'_'.$q.'_'.$row['ap']]);
                   $c = mysql_real_escape_string($_POST ['c_'.$scara.'_'.$q.'_'.$row['ap']]);

                if(ereg('[^0-9.]', $r)) {
                      $i=1;
                      $rErr[$row['loc_id']][$q]='Scara '.$scara.' ap '.$row['ap'].'(Apa Rece'.$q.')Acest camp poate sa contina doar cifre.';
                }

                if(ereg('[^0-9.]', $c)) {
                      $i=1;
                      $cErr[$row['loc_id']][$q]='Scara '.$scara.' ap '.$row['ap'].'(Apa Calda'.$q.')Acest camp poate sa contina doar cifre.';
                }
            }
        }
        $q = 0;
        if ($i==0) {
                $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id DESC";
                $result = mysql_query($query) or die(mysql_error());
                $q = 0;
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $ap = $row['ap'];
                    $locId = $row['loc_id'];
                    $scara = $row['scara'];
                    $scaraId =  $row['scara_id'];
                    for ($q = 1; $q<=$row['ap_rece']; $q++) {
                         $r[$q] = mysql_real_escape_string($_POST ['r_'.$scara.'_'.$q.'_'.$row['ap']]);
                         $c[$q] = mysql_real_escape_string($_POST ['c_'.$scara.'_'.$q.'_'.$row['ap']]);
                    }
                    $cc = "INSERT INTO locatari_apometre (`loc_id`, `scara_id`, `asoc_id`, `r1`,`r2`,`r3`,`r4`,`r5`,`c1`,`c2`,`c3`,`c4`, `c5`) VALUES
                                                                ('$locId', '$scaraId', '$asocId', '$r[1]', '$r[2]', '$r[3]', '$r[4]', '$r[5]', '$c[1]', '$c[2]', '$c[3]', '$c[4]', '$c[5]' )";
                    mysql_query($cc) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');
                    $mesaj = '<font color="green">Datele au fost introduse.</font>';
                        /*if ($link=='w_asoc_fonduri') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_setari&asoc_id='.$asocId.'"</script>';*/
                }
                unset ($_POST);
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }

} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" >

<div id="maincon"  style="width:620px; height:150px;">
        <form id="addForm1" name="addForm1" method="post" action="index.php?link=locatari_ap&asoc_id=<?php echo $asocId; ?>">
                    Asociatia:
                    <?php
                            $query = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
                            echo '<select name="asociatia" onchange="infoAsoc(this.value)"><option value="">Alege asociatia</option>';
                                    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        if ($asocId == $row['asoc_id'] )  echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                        else echo '<option  value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                    }
                            echo '</select>';
                    ?>
                    <br />
                    Scara:
                    <?php
                            $query = "SELECT SS.*, S.scara FROM scari_setari AS SS LEFT JOIN scari AS S ON SS.scara_id=S.scara_id WHERE SS.asoc_id='$asocId' ORDER BY SS.scara_id";
                            //echo '<select name="scara" onchange="infoScari(this.value)">';
                            echo '<select name="scara">';
                                    echo '<option  value="nimic">Alege Scara</option>';
                                    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        if ($_POST['scara'] == $row['scara_id'] )
                                            echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                        else
                                        if ($_GET['scara_id'] == $row['scara_id'] )  echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                        else
                                            echo '<option  value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                    }
                            echo '</select>';
                    echo '<br /><br /><div id="buton"><a onclick="functionSubmit1()" style="">Afiseaza</a></div>';
                    ?>
                    <br />
                </div>
                <br />
        </form>

<div id="maincon" style="width:950px;">
                <?php
        echo '<form id="addForm" name="addForm" method="post" action="index.php?link=locatari_ap&asoc_id='.$asocId.'">';
                    echo '<div id="errorBox" style="">';
                        if ($asociatieErr != '') echo '<font color="red">'.$asociatieErr.'<br>';
                        $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
                        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                            if (!empty($rErr[$row['loc_id']])) foreach ($rErr[$row['loc_id']] as $r){echo $r.'<br>'; }
                            if (!empty($cErr[$row['loc_id']])) foreach ($cErr[$row['loc_id']] as $c){echo $c.'<br>'; }
                        }
                    echo'</font></div>
                    ';
                ?>
                <input type="hidden" name="buton" value="apasat" />
                Asociatia:
                <?php
                      $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
                      $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                          echo $row['asociatie'];
                      }
                ?>
                <br />
                        <?php
                              $m = 0;
                              $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
                              $result = mysql_query($query) or die(mysql_query());
                              $scara = '';
                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                    if ($row['scara'] != $scara) {
                                        if ($scara != '') echo '</table>';
                                        $scara = $row['scara'];
                                        echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:900px;" border=0>
                                                    <tr bgcolor="#19AF62"><td><font size=2 color="white"><center>Etaj</center></font></td><td><font size=2 color="white"><center>Scara</center></font></td><td><font size=2 color="white"><center>Apartament</center></font></td><td><font size=2 color="white"><center>Nume</center></font></td><td><font size=2 color="white"><center>Apa rece 1</center></font></td><td><font size=2 color="white"><center>Apa rece 2</center></font></td><td><font size=2 color="white"><center>Apa rece 3</center></font></td><td><font size=2 color="white"><center>Apa rece 4</center></font></td><td><font size=2 color="white"><center>Apa rece 5</center></font></td><td><font size=2 color="white"><center>Apa calda 1</center></font></td><td><font size=2 color="white"><center>Apa calda 2</center></font></td><td><font size=2 color="white"><center>Apa calda 3</center></font></td><td><font size=2 color="white"><center>Apa calda 4</center></font></td><td><font size=2 color="white"><center>Apa calda 5</center></font></td></tr>';
                                    }
                                    if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                                    echo '<td ><center>'.$row['etaj'].'</center></td>';
                                    echo '<td ><center>'.$row['scara'].'</center></td>';
                                    echo '<td ><center>ap '.$row['ap'].'</center></td>';
                                    echo '<td width=100><center>'.$row['nume'].'</center></td>';
                                    echo '<input type="hidden" name="scara_'.$row['ap'].'" value="'.$row['scara_id'].'">';
                                    $q = 1;
                                    while ($q <= 5) { // numarul maxim de apometre permis
                                        if ($q <= $row['ap_rece']){
                                                echo '<td><input style="width:30px;" type="text" name="r_'.$scara.'_'.$q.'_'.$row['ap'].'" value="'.$_POST['r_'.$scara.'_'.$q.'_'.$row['ap']].'"></td>';
                                        } else {
                                                echo '<td></td>';
                                        }
                                        $q++;
                                    }
                                    $q = 1;
                                    while ($q <= 5) { // numarul maxim de apometre permis
                                        if ($q <= $row['ap_calda']){
                                                echo '<td><input style="width:30px;" type="text" name="c_'.$scara.'_'.$q.'_'.$row['ap'].'" value="'.$_POST['c_'.$scara.'_'.$q.'_'.$row['ap']].'"></td>';
                                        } else {
                                                echo '<td></td>';
                                        }
                                        $q++;
                                    }
                                    $m++;
                              }
                        ?>
                </table>
                <table cellspacing=1 style="margin:20px 0 0 0px; width:230px;" border=0>
                        <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE apometre locatari ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=locatari_ap&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit1.submit();
}
</script>

<?php
    $editLoc = mysql_real_escape_string($_GET['edit']);

    if ($_POST['salveaza'] == 'apasat') {
            $locId = mysql_real_escape_string($_POST['la']);

            $etajT = mysql_real_escape_string($_POST['etajT']);
            $scaraT = mysql_real_escape_string($_POST['scaraT']);
            $apT = mysql_real_escape_string($_POST['apT']);

            $r1 = mysql_real_escape_string($_POST['r1']);
            $r2 = mysql_real_escape_string($_POST['r2']);
            $r3 = mysql_real_escape_string($_POST['r3']);
            $r4 = mysql_real_escape_string($_POST['r4']);
            $r5 = mysql_real_escape_string($_POST['r5']);
            $c1 = mysql_real_escape_string($_POST['c1']);
            $c2 = mysql_real_escape_string($_POST['c2']);
            $c3 = mysql_real_escape_string($_POST['c3']);
            $c4 = mysql_real_escape_string($_POST['c4']);
            $c5 = mysql_real_escape_string($_POST['c5']);

            if(ereg('[^0-9.]', $r1)) {
                  $i=1;
                  $r1Err = 'scara '.$scaraT.' ap'.$apT.'(Apa rece 1)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $r2)) {
                  $i=1;
                  $r2Err='scara '.$scaraT.' ap'.$apT.'(Apa rece 2)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $r3)) {
                  $i=1;
                  $r3Err='scara '.$scaraT.' ap'.$apT.'(Apa rece 3)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $r4)) {
                  $i=1;
                  $r4Err='scara '.$scaraT.' ap'.$apT.'(Apa rece 4)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $r5)) {
                  $i=1;
                  $r5Err='scara '.$scaraT.' ap'.$apT.'(Apa rece 5)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $c1)) {
                  $i=1;
                  $c1Err='scara '.$scaraT.' ap'.$apT.'(Apa calda 1)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $c2)) {
                  $i=1;
                  $c2Err='scara '.$scaraT.' ap'.$apT.'(Apa calda 2)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $c3)) {
                  $i=1;
                  $c3Err='scara '.$scaraT.' ap'.$apT.'(Apa calda 3)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $c4)) {
                  $i=1;
                  $c4Err='scara '.$scaraT.' ap'.$apT.'(Apa calda 4)Acest camp poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $c5)) {
                  $i=1;
                  $c5Err='scara '.$scaraT.' ap'.$apT.'(Apa calda 5)Acest camp poate sa contina doar cifre.<br />';
            }
            if ( $i == 0) {
                    $query = "UPDATE locatari_apometre SET `r1`='$r1', `r2`='$r2', `r3`='$r3', `r4`='$r4', `r5`='$r5', `c1`='$c1', `c2`='$c2', `c3`='$c3', `c4`='$c4', `c5`='$c5' WHERE loc_id='$locId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
    }
?>

<div id="maincon" style="width:950px;">
<?php

        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$r1Err.$r2Err.$r3Err.$r4Err.$r5Err.$c1Err.$c2Err.$c4Err.$c5Err.$c3Err.$mesaj1.'</font>
                  </div>
            ';
        }

$m = 0;
$query = "SELECT L.*, LA.*, S.scara FROM locatari_apometre AS L
                        JOIN scari AS S ON L.scara_id=S.scara_id
                        JOIN locatari AS LA ON LA.loc_id=L.loc_id
                        WHERE L.asoc_id='$asocId'
                        ORDER BY L.loc_id ASC";
$result = mysql_query($query) or die(mysql_query());
$scara = '';
echo '<form id="formEdit1" name="formEdit1" method="post" action="index.php?link=locatari_ap&asoc_id='.$asocId.'">';
            echo '<input type="hidden" name="salveaza" value="apasat" />';
            echo '<input type="hidden" name="la" value="'.$editLoc.'" />';
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    if ($row['scara'] != $scara) {
                        if ($scara != '') echo '</table>';
                        $scara = $row['scara'];
                        echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:900px;" border=0>
                                    <tr bgcolor="#19AF62"><td><font size=2 color="white"><center>Etaj</center></font></td><td><font size=2 color="white"><center>Scara</center></font></td><td><font size=2 color="white"><center>Apartament</center></font></td><td><font size=2 color="white"><center>Nume</center></font></td><td><font size=2 color="white"><center>Apa rece 1</center></font></td><td><font size=2 color="white"><center>Apa rece 2</center></font></td><td><font size=2 color="white"><center>Apa rece 3</center></font></td><td><font size=2 color="white"><center>Apa rece 4</center></font></td><td><font size=2 color="white"><center>Apa rece 5</center></font></td><td><font size=2 color="white"><center>Apa calda 1</center></font></td><td><font size=2 color="white"><center>Apa calda 2</center></font></td><td><font size=2 color="white"><center>Apa calda 3</center></font></td><td><font size=2 color="white"><center>Apa calda 4</center></font></td><td><font size=2 color="white"><center>Apa calda 5</center></font></td><td><font size=2 color="white"><center>Optiuni</center></font></td></tr>';
                    }
                    if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                    echo '<td ><center>'.$row['etaj'].'</center></td>';
                    echo '<td ><center>'.$row['scara'].'</center></td>';
                    echo '<td ><center>ap '.$row['ap'].'</center></td>';

                    echo '<input type="hidden" name="etajT" value="'.$row['etaj'].'" />';
                    echo '<input type="hidden" name="scaraT" value="'.$row['scara'].'" />';
                    echo '<input type="hidden" name="apT" value="'.$row['ap'].'" />';

                    echo '<td width=100><center>'.$row['nume'].'</center></td>';
                    echo '<input type="hidden" name="scara_'.$row['ap'].'" value="'.$row['scara_id'].'">';

                            $q = 1;
                            while ($q <= 5) { // numarul maxim de apometre permis
                                if ($q <= $row['ap_rece']){
                                        if ($editLoc == $row['loc_id']) {
                                                    echo '<td><input style="width:30px;" type="text" name="r'.$q.'" value="'.$row['r'.$q].'"></td>';
                                            } else {
                                                echo '<td>'.$row['r'.$q].'</td>';
                                            }
                                } else { echo '<td></td>'; }
                                $q++;
                            }
                            $q = 1;
                            while ($q <= 5) { // numarul maxim de apometre permis
                                if ($q <= $row['ap_calda']){
                                        if ($editLoc == $row['loc_id']) {
                                            echo '<td><input style="width:30px;" type="text" name="c'.$q.'" value="'.$row['c'.$q].'"></td>';
                                        } else {
                                            echo '<td>'.$row['c'.$q].'</td>';
                                        }
                                } else { echo '<td></td>'; }
                                $q++;
                            }
                            if ($editLoc == $row['loc_id']) {
                                    echo '<td><center>';
                                            echo '<a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></center></td>
                            </form>';
                            } else
                                    echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=locatari_ap&asoc_id='.$asocId.'&edit='.$row['loc_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                </center>
                                          </td>';
                    $m++;
                    }
                    echo '</table>';

?>

</div></div>

