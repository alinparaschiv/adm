<?php
$asocId = $_GET['asoc_id'];

if ($_POST['asociatia'] != '') {
    $asocId = mysql_real_escape_string($_POST['asociatia']);
}
$scaraId = $_POST['scara'];
if ($_GET['scara'] != '')
    $scaraId = mysql_real_escape_string($_GET['scara']);
$scaraP = $scaraId;

$locId = $_POST['locatar'];
if ($_GET['locatar'] != '')
    $locId = mysql_real_escape_string($_GET['locatar']);
if ($_GET['edit'] != '')
    $locId = mysql_real_escape_string($_GET['edit']);
?>

<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=locatar&asoc_id="+obj
        }
        function infoScari(obj) {
            window.location = "index.php?link=locatar&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }

		function checkNr(valoare){
            if (valoare != 1){
                document.getElementById('nr_rep').disabled = true;
            } else {
                document.getElementById('nr_rep').disabled = false;
            }
        }
        function checkNrT(valoare){
            if (valoare != 1){
                document.getElementById('nr_repT').disabled = true;
            } else {
                document.getElementById('nr_repT').disabled = false;
            }
        }

function actualizeaza_proprietar(id_locatar) {
	window.open('actualizare-proprietar/?id_locatar=' + id_locatar, '_blank', 'width=400,height=400,location=0,menubar=0,resizable=0,status=0,scrollbars=0,toolbar=0,titlebar=0');
}
</script>

<div id="mainCol" class="clearfix"><div id="maincon"  style="width:620px; height:100px;">
    <form id="addForm1" name="addForm1" method="post" action="index.php?link=locatar&asoc_id=<?php echo $asocId; ?>&scara=<?php echo $scaraId; ?>">
                Asociatia:
                <?php
                        $query = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
                        echo '<select name="asociatia" onchange="infoAsoc(this.value)">';
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
                        echo '<select name="scara" onchange="infoScari(this.value)">';
                                echo '<option  value="nimic">Alege Scara</option>';
                                $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                    if ($_POST['scara'] == $row['scara_id'] && $_POST['scara'] != '')
                                        echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                    else
                                        if ($_GET['scara'] == $row['scara_id'] )
                                                echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                            else
                                                echo '<option  value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                }
                        echo '</select>';

                echo '<br />Locatar:';
                        $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON S.scara_id=L.scara_id WHERE L.asoc_id = '$asocId' AND S.scara_id='$scaraId' ORDER BY S.scara, L.loc_id ASC";
                        echo '<select name="locatar">';
                                echo '<option  value="nimic">Alege Locatarul</option>';
                                $result = mysql_query($query) or die(mysql_error());
                                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                    if ($_POST['locatar'] == $row['loc_id'] && $_POST['locatar'] != '')
                                        echo '<option selected="selected" value="'.$row['loc_id'].'">'.$row['nume'].'</option>';
                                    else
                                        if ( $_GET['locatar'] == $row['loc_id'] )
                                                echo '<option selected="selected" value="'.$row['loc_id'].'">'.$row['nume'].'</option>';
                                            else
                                                echo '<option  value="'.$row['loc_id'].'">'.$row['nume'].'</option>';
                                }
                        echo '</select>';
                echo '<br /><br /><div id="buton"><a onclick="functionSubmit1()" style="">Afiseaza</a></div>';
                ?>
                <br />
            </div>
            <br />
            </form>

<?php /*********************************EDITEAZA SI STERGE LOCATARII ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=locatar&asoc_id='.$asocId.'&scara='.$scara.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editLoc = mysql_real_escape_string($_GET['edit']);

    if ($_POST['salveaza'] == 'apasat') {
            $i = 0;
            $lcLd = mysql_real_escape_string($_POST['lc']);
            //$numeT = mysql_real_escape_string($_POST['numeT']);
            $cnpT = mysql_real_escape_string($_POST['cnpT']);
            $nr_persT = mysql_real_escape_string($_POST['nr_persT']);
            $suprT = mysql_real_escape_string($_POST['suprT']);
            $cotaT = mysql_real_escape_string($_POST['cotaT']);
            $ap_receT = mysql_real_escape_string($_POST['ap_receT']);
            $ap_caldaT = mysql_real_escape_string($_POST['ap_caldaT']);
            $nr_repT = mysql_real_escape_string($_POST['nr_repT']);
            $centralaT = mysql_real_escape_string($_POST['centralaT']);
            $incalzireT = mysql_real_escape_string($_POST['incalzireT']);
            $gazT = mysql_real_escape_string($_POST['gazT']);
            $ilum_liftT = mysql_real_escape_string($_POST['ilum_liftT']);
            $service_liftT = mysql_real_escape_string($_POST['service_liftT']);

			//added after
			$tipIncalzireT = mysql_real_escape_string($_POST['tipIncalzireT']);
			$apLocuitT = mysql_real_escape_string($_POST['apLocuitT']);

			if ($apLocuitT == "on"){
				$apLocuitT = 1;
			} else {
				$apLocuitT = 0;
			}
            $plafonT = mysql_real_escape_string($_POST['plafonT']);
            $procentT = mysql_real_escape_string($_POST['procentT']);
			//end added

            $locId = mysql_real_escape_string($_POST['lc']);

            //if ( $numeT == '') {
            //        $numeErr1 = 'Campul "nume" nu trebuie lasat necompletat.<br>';
            //        $i=1;
            //}
            if ( $cnpT != '') {
                if (strlen($cnpT)!=13) {
                   $cnpErr1 = 'Campul "CNP" este incorect.<br>';
                   $i=1;
                }
            }
            if(ereg('[^0-9]', $nr_persT)) {
                  $i=1;
                  $nr_persErr1 = 'Campul "Numar persoane" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9.]', $suprT)) {
                  $i=1;
                  $suprErr1 = 'Campul "Suprafata" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9.]', $cotaT)) {
                  $i=1;
                  $cotaErr1 = 'Campul "Cota indiviza" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $ap_receT)) {
                  $i=1;
                  $ap_receErr1 = 'Campul "Ap Apa rece" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $ap_caldaT)) {
                  $i=1;
                  $ap_caldaErr1 = 'Campul "Ap Apa calda" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $nr_repT)) {
                  $i=1;
                  $nr_repErr1 = 'Campul "Nr rep" poate sa contina doar cifre.<br>';
            }
			if(ereg('[^0-9]', $procentT)) {
                  $i=1;
                  $nr_repErr1 = 'Campul "Procent" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $plafonT)) {
                  $i=1;
                  $nr_repErr1 = 'Campul "Plafon" poate sa contina doar cifre.<br>';
            }
            if ($i == 0) {
                    $query = "UPDATE locatari SET `cnp`='$cnpT',`nr_pers`='$nr_persT',`supr`='$suprT',`cota`='$cotaT',`ap_rece`='$ap_receT',
                    `ap_calda`='$ap_caldaT', `tip_incalzire`='$tipIncalzireT', `nr_rep`='$nr_repT', `centrala`='$centralaT', `incalzire`='$incalzireT', `gaz`='$gazT', `ilum_lift`='$ilum_liftT',
                    `service_lift`='$service_liftT', `ap_locuit`='$apLocuitT', `plafon`='$plafonT', `procent`='$procentT' WHERE loc_id='$lcLd'";
                    mysql_query($query) or die(mysql_error());
                    $mesaj1 = '<font color="green">Datele au fost salvate.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="green">Au aparut urmatoarele erori:<br></font>';
            }
    }
?>
<div id="maincon" style="width:950px;">
<?php
        if ($mesaj1 != '') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$mesaj1.$numeErr1.$cnpErr1.$nr_persErr1.$suprErr1.$cotaErr1.$ap_receErr1.$ap_caldaErr1.$nr_repErr1.'</font>
                  </div>
            ';
        }
?>
    <table width=950>
            <tr bgcolor="#19AF62">
                   <td><font size=2 color="white"><center>Scara</center></font></td>
                   <td><font size=2 color="white"><center>Et</center></font></td>
                   <td><font size=2 color="white"><center>Ap</center></font></td>
                   <td><font size=2 color="white"><center>Nume</center></font></td>
                   <td><font size=2 color="white"><center>CNP</center></font></td>
                   <td><font size=2 color="white"><center>Nr<br>pers</center></font></td>
                   <td><font size=2 color="white"><center>Supr.</center></font></td>
                   <td><font size=2 color="white"><center>Cota<br>indiviza</center></font></td>
                   <td><font size=2 color="white"><center>Ap apa<br>rece</center></font></td>
                   <td><font size=2 color="white"><center>Ap apa<br>calda</center></font></td>
				   <td><font size=2 color="white"><center>Tip<br />incalzire</center></font></td>
                   <td><font size=2 color="white"><center>Nr<br>rep</center></font></td>
                   <td><font size=2 color="white"><center>Centrala</center></font></td>
                   <td><font size=2 color="white"><center>Incalzire</center></font></td>
                   <td><font size=2 color="white"><center>Gaz</center></font></td>
                   <td><font size=2 color="white"><center>Ilum <br>Lift</center></font></td>
                   <td><font size=2 color="white"><center>Service <br>Lift</center></font></td>
				   <td><font size=2 color="white"><center>Ap<br />Locuit</center></font></td>
				   <td><font size=2 color="white"><center>Procent<br />Subventie</center></font></td>
				   <td><font size=2 color="white"><center>Plafon<br />Incalzire</center></font></td>
                   <td><font size=2 color="white"><center>Optiuni</center></font></td>
            </tr>
           <?php

              if ($locId != '')
                    $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON S.scara_id=L.scara_id WHERE L.asoc_id = '$asocId' AND L.loc_id='$locId' ORDER BY S.scara, L.loc_id ASC";

              $result = mysql_query($query) or die(mysql_query());
              $i=0;
              $scaraM = '';
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //schimb culoarea la coloane
                   if ($scaraM == '') $scaraM = $row['scara'];
                   if ($scaraM != $row['scara'] ) {
                        echo '<tr align=center bgcolor="#19AF62">
                                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>';
                        $scaraM = $row['scara'];
                   }
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editLoc == $row['loc_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=locatar&asoc_id='.$asocId.'&scara='.$scaraId.'&loc_id='.$locId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="lc" value="'.$editLoc.'" />';
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>et '.$row['etaj'].'</td>';
                                    echo '<td>ap '.$row['ap'].'</td>';
                                    echo '<td>'.$row['nume'].'</td>';
                                    echo '<td><input style="width:110px;" type="text" name="cnpT" value="'.$row['cnp'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="nr_persT" value="'.$row['nr_pers'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="suprT" value="'.$row['supr'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="cotaT" value="'.$row['cota'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="ap_receT" value="'.$row['ap_rece'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 name="ap_caldaT" value="'.$row['ap_calda'].'"></td>';

									echo '<td><select name="tipIncalzireT" onchange="checkNrT(this.value)">';
                                        switch ($row['tip_incalzire']) {
                                            case 0:     echo '<option value="0">Nimic</option>';
                                                        echo '<option value="1">Repartitoare</option>';
                                                        echo '<option value="2">Gigacalorimetru</option>';
                                                    break;
                                            case 1:     echo '<option value="1">Repartitoare</option>';
                                                        echo '<option value="0">Nimic</option>';
                                                        echo '<option value="2">Gigacalorimetru</option>';
                                                    break;
                                            case 2:     echo '<option value="2">Gigacalorimetru</option>';
                                                        echo '<option value="0">Nimic</option>';
                                                        echo '<option value="1">Repartitoare</option>';
                                                    break;
                                        }
                                    echo '</select></td>';

                                    switch ($row['tip_incalzire']) {
                                        case 0:
                                        case 2: echo '<td><input style="width:30px;" maxlength=1 name="nr_repT" id="nr_repT" value="'.$row['nr_rep'].'" disabled></td>';
                                                break;
                                        case 1: echo '<td><input style="width:30px;" maxlength=1 name="nr_repT" id="nr_repT" value="'.$row['nr_rep'].'"></td>';
                                                break;
                                    }

									echo '<td ><select name="centralaT">
                                                      <option value="'.$row['centrala'].'">'.$row['centrala'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="incalzireT">';
                                                echo '<option value="'.$row['incalzire'].'">'.$row['incalzire'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="gazT">';
                                                echo '<option value="'.$row['gaz'].'">'.$row['gaz'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="ilum_liftT">';
                                                echo '<option value="'.$row['ilum_lift'].'">'.$row['ilum_lift'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="service_liftT">';
                                                echo '<option value="'.$row['service_lift'].'">'.$row['service_lift'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';

									if ($row['ap_locuit'] == 1){
										echo '<td><input type="checkbox" name="apLocuitT" checked /></td>';
									} else {
										echo '<td><input type="checkbox" name="apLocuitT" /></td>';
									}

                                    echo '<td><input type="text" style="width:40px;" name="procentT" value="'.$row['procent'].'"></td>';
                                    echo '<td><input type="text" style="width:40px;" name="plafonT" value="'.$row['plafon'].'"></td>';

                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>et '.$row['etaj'].'</td>';
                                    echo '<td>ap '.$row['ap'].'</td>';
                                    echo '<td>'.$row['nume'].'</td>';
                                    echo '<td>'.$row['cnp'].'</td>';
                                    echo '<td>'.$row['nr_pers'].'</td>';
                                    echo '<td>'.$row['supr'].'</td>';
                                    echo '<td>'.$row['cota'].'</td>';
                                    echo '<td>'.$row['ap_rece'].'</td>';
                                    echo '<td>'.$row['ap_calda'].'</td>';

									switch ($row['tip_incalzire']){
                                        case 0: echo '<td> Nimic </td>';
                                                break;
                                        case 1: echo '<td> Repartitoare </td>';
                                                break;
                                        case 2: echo '<td> Gigacalorimetru </td>';
                                                break;
                                    }

                                    echo '<td>'.$row['nr_rep'].'</td>';
                                    echo '<td>'.$row['centrala'].'</td>';
                                    echo '<td>'.$row['incalzire'].'</td>';
                                    echo '<td>'.$row['gaz'].'</td>';
                                    echo '<td>'.$row['ilum_lift'].'</td>';
                                    echo '<td>'.$row['service_lift'].'</td>';

									if ($row['ap_locuit'] == 0){
										echo '<td>nu</td>';
									} else {
										echo '<td>da</td>';
									}
                                    echo '<td>'.$row['procent'].'</td>';
                                    echo '<td>'.$row['plafon'].'</td>';

                                    echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=locatar&asoc_id='.$asocId.'&edit='.$row['loc_id'].'&scara='.$scaraId.'">Date apartament</a><br/><a href="#" onclick="actualizeaza_proprietar('.$row['loc_id'].')">Date proprietar</a></center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>
</div></div>

