<?php
$asocId = $_GET['asoc_id'];

if ($_POST['asociatia'] != '') {
    $asocId = mysql_real_escape_string($_POST['asociatia']);
}
$scaraId = $_POST['scara'];
if ($_GET['scara_id'] != '')
    $scaraId = mysql_real_escape_string($_GET['scara_id']);
$scaraP = $scaraId;
?>

<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }
        function infoScari(obj) {
            window.location = "index.php?link=locatari&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=locatari&asoc_id="+obj
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

<?php

if ($scaraId == '') {
        $query = "SELECT SS.*, L.loc_id, S.scara FROM scari_setari AS SS
                                LEFT JOIN locatari AS L ON SS.scara_id=L.scara_id
                                LEFT JOIN scari AS S ON S.scara_id=S.scara_id
                                WHERE SS.asoc_id='$asocId' ORDER BY S.scara ASC";
} else {
        $query = "SELECT SS.*, L.loc_id, S.scara FROM scari_setari AS SS
                                LEFT JOIN locatari AS L ON SS.scara_id=L.scara_id
                                LEFT JOIN scari AS S ON S.scara_id=S.scara_id
                                WHERE SS.scara_id='$scaraId' ORDER BY S.scara ASC";
}

$result = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $scaraId = $row['scara_id'];
            $parter = $row['parter'];
            $etaje = $row['etaje'];
            $apartamente = $row['apartamente'];
            $stop = 1;
}
//echo '****'.$apartamente.'*********'.$_POST['apNr'].'*********'.$scaraId;
$next = 0;
$apNr = mysql_real_escape_string($_POST['apNr']);
if ($_POST['buton'] == 'apasat' && $_POST['nume'] != '' && $_POST['nr_pers'] !='' && $_POST['supr'] != '' && $_POST['cota']!='' && $_POST['ap_rece']!='' && $_POST['ap_calda']!='' && $_POST['nr_rep']!=''  && $_POST['centrala']!='nimic'  && $_POST['incalzire']!='nimic'  && $_POST['gaz']!='nimic'  && $_POST['ilum_lift']!='nimic'   && $_POST['service_lift']!='nimic' ) {
        $i = 0;

        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        $apNr = mysql_real_escape_string($_POST['apNr']);
        $etaj = mysql_real_escape_string($_POST['etaj']);
        //$scaraId = mysql_real_escape_string($_POST['scara']);
        $nume = mysql_real_escape_string($_POST['nume']);
        $cnp = mysql_real_escape_string($_POST['cnp']);
        $nr_pers = mysql_real_escape_string($_POST['nr_pers']);
        $supr = mysql_real_escape_string($_POST['supr']);
        $cota = mysql_real_escape_string($_POST['cota']);
        $ap_rece = mysql_real_escape_string($_POST['ap_rece']);
        $ap_calda = mysql_real_escape_string($_POST['ap_calda']);
        $tipIncalzire = mysql_real_escape_string($_POST['tipIncalzire']);
        $nr_rep = mysql_real_escape_string($_POST['nr_rep']);
        $centrala = mysql_real_escape_string($_POST['centrala']);
        $incalzire = mysql_real_escape_string($_POST['incalzire']);
        $gaz = mysql_real_escape_string($_POST['gaz']);
        $ilum_lift =mysql_real_escape_string( $_POST['ilum_lift']);
        $service_lift = mysql_real_escape_string($_POST['service_lift']);
		$apLocuit = mysql_real_escape_string($_POST['apLocuit']);

		if ($apLocuit == "on"){
			$apLocuit = 1;
		} else {
			$apLocuit = 0;
		}

        $plafon = mysql_real_escape_string($_POST['plafon']);
        $procent = mysql_real_escape_string($_POST['procent']);

        $query = "SELECT * FROM locatari WHERE scara_id='$scaraId' AND ap='$apNr'";
        $result = mysql_query($query) or die(mysql_error());
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $i=1;
            $nrApErr = 'Ati mai introdus locatari pentru acest apartament.<br>';
        }

        $query = "SELECT * FROM scari_setari WHERE scara_id='$scaraId' ";
        $result = mysql_query($query) or die(mysql_error());
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $apartamente = $row['apartamente'];
        }

        if ($apNr == $row['apartamente']) {
            $i=1;
            $nrApErr = 'Ati introdus numarul maxim de apartamente pentru aceasta asociatie.<br>';
        }
        if ($nume == '') {
            $i=1;
            $numeErr = 'Campul "Nume" trebuie completat.<br>';
        }

        if ( $cnp != '') {
            if (strlen($cnp)!=13) {
               $cnpErr = 'Campul "CNP" este incorect.<br>';
               $i=1;
            }
        }
        if(ereg('[^0-9]', $nr_pers)) {
              $i=1;
              $nr_persErr = 'Campul "Numar persoane" poate sa contina doar cifre.<br>';
        }
        if(ereg('[^0-9\.]', $supr)) {
              $i=1;
              $suprErr = 'Campul "Suprafata" poate sa contina doar cifre.<br>';
        }

        if(ereg('[^0-9\.]', $cota)) {
              $i=1;
              $cotaErr = 'Campul "Cota indiviza" poate sa contina doar cifre.<br>';
        }
        if(ereg('[^0-9]', $ap_rece)) {
              $i=1;
              $ap_receErr = 'Campul "Ap Apa rece" poate sa contina doar cifre.<br>';
        }
        if(ereg('[^0-9]', $ap_calda)) {
              $i=1;
              $ap_caldaErr = 'Campul "Ap Apa calda" poate sa contina doar cifre.<br>';
        }
        if($ap_rece > 5) {
              $i=1;
              $ap_receErr = 'Campul "Ap Apa rece" NU poate sa aibe valoarea mai mare decat 5.<br>';
        }
        if($ap_calda > 5) {
              $i=1;
              $ap_caldaErr= 'Campul "Ap Apa calda" NU poate sa aibe valoarea mai mare decat 5.<br>';
        }
        if(ereg('[^0-9]', $nr_rep)) {
              $i=1;
              $nr_repErr = 'Campul "Nr rep" poate sa contina doar cifre.<br>';
        }

        if(ereg('[^0-9\.]', $plafon)) {
              $i=1;
              $nr_repErr = 'Campul "Plafon" poate sa contina doar cifre.<br>';
        }

        if(ereg('[^0-9]', $procent)) {
              $i=1;
              $nr_repErr = 'Campul "Procent" poate sa contina doar cifre.<br>';
        }

        if ($i==0) {
            $next = 1;
            $query = "INSERT INTO locatari (`scara_id`,`asoc_id`, `nume`,`cnp`,`nr_pers`,`supr`,`cota`,`etaj`, `ap`, `ap_rece`, `ap_calda`, `tip_incalzire`, `nr_rep`, `centrala`, `incalzire`, `gaz`, `ilum_lift`, `service_lift`, `ap_locuit`, `plafon`, `procent`) VALUES
                                           ('$scaraId', '$asocId', '$nume', '$cnp','$nr_pers','$supr','$cota','$etaj', '$apNr', '$ap_rece', '$ap_calda', '$tipIncalzire', '$nr_rep', '$centrala', '$incalzire', '$gaz', '$ilum_lift', '$service_lift', '$apLocuit', '$plafon', '$procent')";
            mysql_query($query) or die(mysql_error());

            $mesaj = '<font color="green">Datele au fost introduse.</font>';
            unset ($_POST);
/*            if ($link=='w_locatari') echo '<script language="javascript">window.location.href="index.php?link=w_locatari&asoc_id='.$asocId.'"</script>';
*/
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }

} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}

if ($apNr+1 <= $apartamente && $i == 0 && $next == 1)
    $apNr++; // trec la urmatorul apartament
?>

<div id="mainCol" class="clearfix"><div id="maincon"  style="text-align:left;">
    <form id="addForm1" name="addForm1" method="post" action="index.php?link=locatari&asoc_id=<?php echo $asocId; ?>">
                Asociatia:
                <?php
					if ($_SESSION['uid'] == 0){
                        $query = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
					}
					else
					{
					   $query = "SELECT * FROM asociatii WHERE administrator_id=".$_SESSION['rank']." ORDER BY administrator_id, asoc_id";
					}
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



    </form>
              </div>
<?php
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=locatari&asoc_id='.$asocId.'&scara_id='.$scaraId.'">';
?>
            <input type="hidden" name="buton" value="apasat" />
            <div id="maincon" style="width:950px;">
            <?php
                if ($i == 1) {
                    //$apNr = 0;
                    echo '<div id="errorBox" style="">';
                                echo '<font color="red">'.$cnpErr.$numeErr.$nr_persErr.$suprErr.$cotaErr.$ap_receErr.$ap_caldaErr.$nr_repErr.$nrApErr.'</font>';
                    echo'</div>
                    ';
                }
            ?>
                <table cellspacing=1 style="margin:10px 0 0 0px; width:1000px;" border=0>
                        <tr bgcolor="#19AF62">
                     <td bgcolor="#999999"><font size=2 color="white"><center>Et</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Ap</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Nume si Prenume</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>CNP</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Nr<br />pers</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Supr.</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Cota<br />indiviza</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Ap apa<br />rece</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Ap apa<br />calda</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Tip<br />incalzire</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Nr<br />rep</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Centrala</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Incalzire</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Gaz</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Ilum <br />Lift</center></font></td>
                     <td bgcolor="#999999"><font size=2 color="white"><center>Service <br />Lift</center></font></td>
					 <td bgcolor="#999999"><font size=2 color="white"><center>Ap<br />Locuit</center></font></td>
					 <td bgcolor="#999999"><font size=2 color="white"><center>Procent<br />subventie</center></font></td>
                                         <td bgcolor="#999999"><font size=2 color="white"><center>Plafon<br />incalzire</center></font></td>
                        </tr>
                        <?php
                                if ($apNr %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                                    echo '<td><select name="etaj">';
                                            if ($etaj != ''){
                                                echo '<option selected="selected" value="'.$etaj.'">'.$etaj.'</option>';
                                            }
                                            if ($parter == 'da') {
                                                echo '<option value="p">p</option>';
                                            }
                                            for ($i=1; $i<=$etaje; $i++)
                                                echo '<option value="'.$i.'">'.$i.'</option>';
                                    echo '</select></td>';
                                    echo '<td><select name="apNr">';
                                            if ($apNr != ''){
                                                echo '<option selected="selected" value="'.$apNr.'">'.$apNr.'</option>';
                                            }
                                            for ($i=1; $i<=$apartamente; $i++)
                                                echo '<option value="'.$i.'">'.$i.'</option>';
                                    echo '</select></td>';
                                    echo '<td><input style="width:130px;" type="text" name="nume" value="'.$_POST['nume'].'"></td>';
                                    echo '<td><input style="width:110px;" type="text" name="cnp" value="'.$_POST['cnp'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=2 type="text" name="nr_pers" value="'.$_POST['nr_pers'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=5 type="text" name="supr" value="'.$_POST['supr'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=5 type="text" name="cota" value="'.$_POST['cota'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="ap_rece" value="'.$_POST['ap_rece'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=1 name="ap_calda" value="'.$_POST['ap_calda'].'"></td>';

                                    echo '<td><select name="tipIncalzire" onchange="checkNr(this.value)">';
                                         echo '<option value="0">Nimic</option>';
                                         echo '<option value="1">Repartitoare</option>';
                                         echo '<option value="2">Gigacalorimetru</option>';
                                    echo '</select></td>';

                                    echo '<td><input style="width:30px;" maxlength=2 id="nr_rep" name="nr_rep" value="'.$_POST['nr_rep'].'" disabled></td>';
                                    echo '<td ><select name="centrala"><option value="nimic">Alege</option>';
                                                if ($_POST['centrala'] != '' && $_POST['centrala'] != 'nimic')
                                                echo '<option selected="selected" value="'.$_POST['centrala'].'">'.$_POST['centrala'].'</option>';
                                                echo '<option value="CT">da</option><option value="">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="incalzire"><option value="nimic">Alege</option>';
                                                if ($_POST['incalzire'] != '' && $_POST['incalzire'] != 'nimic')
                                                echo '<option selected="selected" value="'.$_POST['incalzire'].'">'.$_POST['incalzire'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="gaz"><option value="nimic">Alege</option>';
                                                if ($_POST['gaz'] != '' && $_POST['gaz'] != 'nimic')
                                                echo '<option selected="selected" value="'.$_POST['gaz'].'">'.$_POST['gaz'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="ilum_lift"><option value="nimic">Alege</option>';
                                                if ($_POST['ilum_lift'] != '' && $_POST['ilum_lift'] != 'nimic')
                                                echo '<option selected="selected" value="'.$_POST['ilum_lift'].'">'.$_POST['ilum_lift'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';
                                    echo '<td ><select name="service_lift"><option value="nimic">Alege</option>';
                                                if ($_POST['service_lift'] != '' && $_POST['service_lift'] != 'nimic')
                                                echo '<option selected="selected" value="'.$_POST['service_lift'].'">'.$_POST['service_lift'].'</option>';
                                                echo '<option value="da">da</option><option value="nu">nu</option>';
                                            echo '</select></td>';

                                    if ($_POST['apLocuit'] == "on") echo '<td><input type="checkbox" name="apLocuit" checked/></td>';
                                    else                            echo '<td><input type="checkbox" name="apLocuit" /></td>';


                                    echo '<td><input type="text" style="width:40px;" name="procent" value="'.$_POST['procent'].'"></td>';
                                    echo '<td><input type="text" style="width:40px;" name="plafon" value="'.$_POST['plafon'].'"></td>';

                                echo '</tr>';

                        echo '</table>';

                        echo '<table><tr><td></td><td><br /><br /><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>';
                        ?>

                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                        </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE LOCATARII ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=locatari&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
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
            $tipIncalzireT = mysql_real_escape_string($_POST['tipIncalzireT']);
            $nr_repT = mysql_real_escape_string($_POST['nr_repT']);
            $centralaT = mysql_real_escape_string($_POST['centralaT']);
            $incalzireT = mysql_real_escape_string($_POST['incalzireT']);
            $gazT = mysql_real_escape_string($_POST['gazT']);
            $ilum_liftT = mysql_real_escape_string($_POST['ilum_liftT']);
            $service_liftT = mysql_real_escape_string($_POST['service_liftT']);
			$apLocuitT = mysql_real_escape_string($_POST['apLocuitT']);

			if ($apLocuitT == "on"){
				$apLocuitT = 1;
			} else {
				$apLocuitT = 0;
			}
            $plafonT = mysql_real_escape_string($_POST['plafonT']);
            $procentT = mysql_real_escape_string($_POST['procentT']);

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
            if(ereg('[^0-9\.]', $suprT)) {
                  $i=1;
                  $suprErr1 = 'Campul "Suprafata" poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9\.]', $cotaT)) {
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
            if(ereg('[^0-9\.]', $plafonT)) {
                  $i=1;
                  $nr_repErr1 = 'Campul "Plafon" poate sa contina doar cifre.<br>';
            }
            if ($i == 0) {
                    $query = "UPDATE locatari SET `cnp`='$cnpT',`nr_pers`='$nr_persT',`supr`='$suprT',`cota`='$cotaT',`ap_rece`='$ap_receT',
                    `ap_calda`='$ap_caldaT', `tip_incalzire`='$tipIncalzireT', `nr_rep`='$nr_repT', `centrala`='$centralaT', `incalzire`='$incalzireT', `gaz`='$gazT', `ilum_lift`='$ilum_liftT',
                    `service_lift`='$service_liftT', `ap_locuit`='$apLocuitT', `plafon`='$plafonT', `procent`='$procentT' WHERE loc_id='$lcLd'";
                    mysql_query($query) or die(mysql_error());
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
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
    <table width="1000px" class="tablestyle">
            <tr bgcolor="#19AF62">
             <td bgcolor="#999999"><font size=2 color="white"><center>Scara</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Et</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Ap</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Nume</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>CNP</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Nr<br />pers</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Supr.</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Cota<br />indiviza</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Ap apa<br />rece</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Ap apa<br />calda</center></font></td>
             <td bgcolor="#999999"><font size=2 color="white"><center>Tip<br />incalzire</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Nr<br />rep</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Centrala</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Incalzire</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Gaz</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Ilum <br />Lift</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Service <br />Lift</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Ap<br />Locuit</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Procent<br />Subventie</center></font></td>
			 <td bgcolor="#999999"><font size=2 color="white"><center>Plafon<br />Incalzire</center></font></td>
              <td bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>
            </tr>
           <?php
              if ($scaraP != '')
                    $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON S.scara_id=L.scara_id WHERE L.asoc_id = '$asocId' AND S.scara_id='$scaraId' ORDER BY S.scara, L.loc_id ASC";
              else
                    $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON S.scara_id=L.scara_id WHERE L.asoc_id = '$asocId' ORDER BY S.scara, L.loc_id ASC";
              $result = mysql_query($query) or die(mysql_query());
              $i=0;
              $scaraM = '';
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //schimb culoarea la coloane
                   if ($scaraM == '') $scaraM = $row['scara'];
                   if ($scaraM != $row['scara'] ) {
                        echo '<tr align=center bgcolor="#19AF62">
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                        </tr>';
                        $scaraM = $row['scara'];
                   }
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editLoc == $row['loc_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=locatari&asoc_id='.$asocId.'&scara_id='.$scaraId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="lc" value="'.$editLoc.'" />';
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>'.$row['etaj'].'</td>';
                                    echo '<td>'.$row['ap'].'</td>';
                                    echo '<td><a href="#" onclick="actualizeaza_proprietar('.$row['loc_id'].')">'.$row['nume'].'</a></td>';
                                    echo '<td><input style="width:110px;" type="text" name="cnpT" value="'.$row['cnp'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=2 type="text" name="nr_persT" value="'.$row['nr_pers'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=5 type="text" name="suprT" value="'.$row['supr'].'"></td>';
                                    echo '<td><input style="width:30px;" maxlength=5 type="text" name="cotaT" value="'.$row['cota'].'"></td>';
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
                                    echo '</seect></td>';

                                    switch ($row['tip_incalzire']) {
                                        case 0:
                                        case 2: echo '<td><input style="width:30px;" maxlength=1 name="nr_repT" id="nr_repT" value="'.$row['nr_rep'].'" disabled></td>';
                                                break;
                                        case 1: echo '<td><input style="width:30px;" maxlength=1 name="nr_repT" id="nr_repT" value="'.$row['nr_rep'].'"></td>';
                                                break;
                                    }

                                    echo '<td ><select name="centralaT">
                                                      <option value="'.$row['centrala'].'">'.$row['centrala'].'</option>';
                                                echo '<option value="CT">da</option><option value="">nu</option>';
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
                                    echo '<td><a href="#" onclick="actualizeaza_proprietar('.$row['loc_id'].')">'.$row['nume'].'</a></td>';
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
                                                <a style="font-size:12px;" href="index.php?link=locatari&asoc_id='.$asocId.'&edit='.$row['loc_id'].'&scara_id='.$scaraId.'">Date apartament</a><br/>
                                                <a href="#" onclick="actualizeaza_proprietar('.$row['loc_id'].')">Date proprietar</a>
                                          </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>
</div></div>

