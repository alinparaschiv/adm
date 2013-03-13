<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }

		function testData(valuare){
		//alert(document.getElementById("autoF").checked );
			if (document.getElementById("autoF").checked == true) {
				document.getElementById("dataF").disabled = false;
			} else {
				document.getElementById("dataF").disabled = true;
			}
		}

		function testDataT(valuare){
			if (document.getElementById("autoFT").checked == true){
				document.getElementById("dataFT").disabled = false;
			} else {
				document.getElementById("dataFT").disabled = true;
			}
		}
</script>

<?php
$asocId = mysql_real_escape_string($_POST['asociatia']);
if ($_GET['edit'] != '') {
    $asocId = mysql_real_escape_string($_GET['edit']);
}
if ($_GET['asoc_id'] != '') {
    $asocId = mysql_real_escape_string($_GET['asoc_id']);
}

if ($_POST['buton'] == 'apasat' && $_POST['asociatie'] != 'nimic' && $_POST['penalizare'] != '' && $_POST['termen'] != '' && $_POST['impartire1'] != 'nimic' && $_POST['impartire2'] != 'nimic' && $_POST['predare'] != '' && $_POST['criteriu1'] != 'nimic' && $_POST['criteriu2'] != 'nimic' && $_POST['pausal_rece'] != '' && $_POST['pausal_calda'] != '' && $_POST['luni'] != ''  && $_POST['sold'] != '') {

        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        $nrScari = mysql_real_escape_string($_POST['nr_scari']);

        $penalizare = mysql_real_escape_string($_POST['penalizare']);
        $termen = mysql_real_escape_string($_POST['termen']);

		$nrPasante = mysql_real_escape_string($_POST['nrPasante']);

        $impartire1 = mysql_real_escape_string($_POST['impartire1']);
        $impartire2 = mysql_real_escape_string($_POST['impartire2']);
        $predare = mysql_real_escape_string($_POST['predare']);
        $criteriu1 = mysql_real_escape_string($_POST['criteriu1']);
        $criteriu2 = mysql_real_escape_string($_POST['criteriu2']);
        $pausal_rece = mysql_real_escape_string($_POST['pausal_rece']);
        $pausal_calda = mysql_real_escape_string($_POST['pausal_calda']);
        $luni = mysql_real_escape_string($_POST['luni']);

		$sold = mysql_real_escape_string($_POST['sold']);


        $query = "SELECT * FROM asociatii_setari WHERE asoc_id='$asocId'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
             $asociatieErr = 'Exista deja setari pentru aceasta asociatie!';
             $i = 1;
        }

        if($penalizare > 0.2) {
              $i=1;
              $penalizareErr='Procentul de penalizare trebuie sa fie maximum 0.2';
        }
        if(ereg('[^0-9.]', $penalizare)) {
              $i=1;
              $penalizareErr='Procentul de penalizare trebuie sa fie maximum 0.2';
        }
		if (!ctype_digit($nrPasante)){
			$i = 1;
			$pasanteErr = 'Acest camp poate sa contina numai cifre.';
		}
        if($termen > 20 || $termen < 1) {
              $i=1;
              $termenErr='Termenul de plata trebuie sa fie cuprins intre 1 si 20 de zile';
        }
        if(ereg('[^0-9]', $termen)) {
              $i=1;
              $termenErr='Acest camp poate sa contina doar cifre.';
        }
        if($predare > 31 || $predare <1) {
              $i=1;
              $predareErr='Termenul de plata trebuie sa fie intre 1 si 31 zile';
        }
        if(ereg('[^0-9]', $pausal_rece)) {
              $i=1;
              $pausal_receErr='Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9]', $pausal_calda)) {
              $i=1;
              $pausal_caldErr='Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9]', $luni)) {
              $i=1;
              $luniErr = 'Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9]', $nrScari)) {
              $i=1;
              $nrScariErr = 'Acest camp poate sa contina doar cifre.';
        }

		if(ereg('[^0-9\-\.]', $sold)) {
              $i=13;
              $soldErr = 'Acest camp poate sa contina doar cifre.';
        }


        $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
              if ($row['o_scara'] == 1) $nrScari = 1;
        }

        if ($nrScari == '') {
              $i=1;
              $nrScariErr = 'Acest camp trebuie completat.';
        }


        if ($i==0) {
                $query = "INSERT INTO asociatii_setari (`asoc_id`, `penalizare`, `termen`, `pasante`, `impartire1`, `impartire2`, `predare`, `criteriu1`, `criteriu2`, `pausal_rece`, `pausal_cald`, `luni`, `pachet_id`, `nr_scari`, `factura_auto`, `data_factura`)
                                                VALUES ('$asocId', '$penalizare', '$termen', '$nrPasante', '$impartire1', '$impartire2', '$predare', '$criteriu1', '$criteriu2', '$pausal_rece', '$pausal_calda', '$luni', '$pachet', '$nrScari', '$autoF', '$dataF')";
                mysql_query($query) or die(mysql_error());
                //$asoc_id = mysql_insert_id();
				$casier_id = $_SESSION['rank'];
				$data = date('Y-m-d')." ".date('H:m:s');
				$ip = $_SERVER['REMOTE_ADDR'];

				$qery_sold = "INSERT INTO casierie (`id`, `asoc_id`, `scara_id`, `loc_id`, `chitanta_serie`, `chitanta_nr`, `suma`, `tip_plata`, `reprezentand`, `data_inserarii`, `casier_id`, `ip`) VALUES (NULL, '$asocId', NULL, NULL, 'Protocol', '0', '$sold', 'Protocol', 'Sold in casa', '$data', '$casier_id', '$ip');";

				mysql_query($qery_sold ) or die(mysql_error());

                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);
                // trece la pasul urmator
                //if ($link=='wizard') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_dat&asoc_id='.$asoc_id.'"</script>';
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }

} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" >

     <?php  /************************AFISEAZA **************************/ ?>
    <div id="maincon">
        <form id="addForm1" name="addForm1" method="post" action="index.php?link=asoc_setari">
                <input type="hidden" name="search" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>
                        <tr><td width="98" bgcolor="#CCCCCC">Asociatia:</td>
                            <td width="112" bgcolor="#CCCCCC">
                          <select name="asociatia">
                                        <?php
                                              $query = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
                                              $result = mysql_query($query) or die('A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                              echo '<option value="nimic">Alege asociatia</option>';
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                  if ($asocId == $row['asoc_id'])
                                                        echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                                  echo '<option value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                              }
                                        ?>
                                </select>
                            </td>
                          <td width="64" bgcolor="#CCCCCC"><input type="submit" name="Afiseaza" value="Afiseaza" id="Afiseaza" /></td>
                        </tr>
                </table>
        </form>
    </div>




<div id="maincon" >
<?php
echo '<form id="addForm" name="addForm" method="post" action="index.php?link=asoc_setari&asoc_id='.$asocId.'">';
?>
                <input type="hidden" name="buton" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:600px;" border=0>
                        <?php if ($asociatieErr != '') echo '<tr><td></td><td><font color="red">'.$asociatieErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Asociatie:</td>
                            <td align="left" bgcolor="#CCCCCC">
                                        <?php
                                              $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
                                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                  echo $row['asociatie'];
                                              }
                                        ?>
                            </td>
                        </tr>
						<?php
                              if ($oScara == 0) {
                                    if ($nrScariErr != '') echo '<tr><td align="left" bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"><font color="red">'.$nrScariErr.'</font></td></tr>';
                                    echo '<tr><td align="left" bgcolor="#CCCCCC">Nr scari:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="nr_scari" value="'.$_POST['nr_scari'].'" /></td></tr>';
                              }
                        ?>
						<tr><td align="left" bgcolor="#CCCCCC">&nbsp;</td><td align="left" bgcolor="#CCCCCC"><u>Lista de Plata</u></td></tr>

						<?php if ($penalizareErr != '') echo '<tr><td></td><td><font color="red">'.$penalizareErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Procent Penalizare:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="penalizare" value="<?php echo $_POST['penalizare'];?>" />(intre 0.001 si 0.2)</td></tr>
                        <?php if ($termenErr != '') echo '<tr><td></td><td><font color="red">'.$termenErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Termen Plata:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="termen" value="<?php echo $_POST['termen'];?>" />(intre 1 si 20 de zile)</td></tr>

						<tr><td align="left" bgcolor="#CCCCCC">&nbsp;</td><td align="left" bgcolor="#CCCCCC"><u>Informatii Apa</u></td></tr>

						<tr><td align="left" bgcolor="#CCCCCC">Nr. Pasante:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="nrPasante" value="<?php echo $_POST['nrPasante'];?>" /></td></tr>

						<tr><td align="left" bgcolor="#CCCCCC">Impartirea apei cand declara toti:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="impartire1">
                                        <?php
                                              echo '<option value="nimic">Alege Modul de Impartire a Apei </option>';

                                                  foreach ($apaDeclaraArr as $key=>$value) {
                                                        if ( $_POST['impartire1'] == $key && $_POST['impartire1'] !='nimic' && $_POST['impartire1'] !='') {
                                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                        }
                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                  }

                                        ?>
                                </select>
                            </td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Impartirea apei cand NU declara toti:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="impartire2">
                                        <?php
                                              echo '<option value="nimic">Alege Modul de Impartire a Apei </option>';
                                                  foreach ($apaNuDeclaraArr as $key=>$value) {
                                                        if ( $_POST['impartire2'] == $key && $_POST['impartire2'] !='' && $_POST['impartire2'] !='nimic') {
                                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                        }
                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                  }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <?php if ($predareErr != '') echo '<tr><td></td><td><font color="red">'.$predareErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Zi predare citiri:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="predare" value="<?php echo $_POST['predare'];?>" />(intre 1 si 31)</td></tr>

                        <tr><td align="left" bgcolor="#CCCCCC">Criteriul modului de actiune <br>in urma citirii gresite:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="criteriu1">
                                        <?php
                                              echo '<option value="nimic">Alege Criteriul</option>';
                                                  foreach ($asocCriteriuGresitArr as $key=>$value) {
                                                        if ( $_POST['criteriu1'] == $key && $_POST['criteriu1'] !='' && $_POST['criteriu1'] !='nimic') {
                                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                        }
                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                  }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Criteriul de impartire a apei intre scari:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="criteriu2">
                                        <?php
                                              echo '<option value="nimic">Alege Criteriul</option>';
                                                  foreach ($asocCriteriuImpartireArr as $key=>$value) {
                                                        if ( $_POST['criteriu2'] == $key && $_POST['criteriu2'] !='' && $_POST['criteriu2'] !='nimic') {
                                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                        }
                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                  }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <?php if ($pausal_receErr != '') echo '<tr><td></td><td><font color="red">'.$pausal_receErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Pausal Apa Rece:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="pausal_rece" value="<?php echo $_POST['pausal_rece'];?>" />(mc-luna/pers)</td></tr>
                        <?php if ($pausal_caldErr != '') echo '<tr><td></td><td><font color="red">'.$pausal_caldErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Pausal Apa Calda:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="pausal_calda" value="<?php echo $_POST['pausal_calda'];?>" />(mc-luna/pers)</td></tr>
					    <?php if ($luniErr != '') echo '<tr><td></td><td><font color="red">'.$luniErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Nr Luni Repetari Consum:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="luni" value="<?php echo $_POST['luni'];?>" /></td></tr>

						<tr><td align="left" bgcolor="#CCCCCC">&nbsp;</td><td align="left" bgcolor="#CCCCCC"><u>Setari Urbica</u></td></tr>

						<?php if ($soldErr != '') echo '<tr><td></td><td><font color="red">'.$soldErr.'</font></td></tr>'; ?>
                        <tr>
							<td align="left" bgcolor="#CCCCCC">Sold in casa</td>
							<td align="left" bgcolor="#CCCCCC"><input type="text" name="sold" id="sold" value="<?php echo $_POST['sold'];?>" /></td>
						</tr>

                        <tr><td bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"><input type="submit" name="Salveaza" value="Salveaza" /></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td align="left" bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC">'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE SETARI INITIALE ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi setarile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=asoc_setari&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editSet = mysql_real_escape_string($_GET['edit']);
    if ($_POST['salveaza'] == 'apasat') {
            $setId = mysql_real_escape_string($_POST['set']);

            $penalizareT = mysql_real_escape_string($_POST['penalizareT']);
            $termenT = mysql_real_escape_string($_POST['termenT']);

			$nrPasanteT = mysql_real_escape_string($_POST['nrPasanteT']);

			$impartire1T = mysql_real_escape_string($_POST['impartire1T']);
            $impartire2T = mysql_real_escape_string($_POST['impartire2T']);
            $predareT = mysql_real_escape_string($_POST['predareT']);
            $criteriu1T = mysql_real_escape_string($_POST['criteriu1T']);
            $criteriu2T = mysql_real_escape_string($_POST['criteriu2T']);
            $pausal_receT = mysql_real_escape_string($_POST['pausal_receT']);
            $pausal_caldaT = mysql_real_escape_string($_POST['pausal_caldaT']);
            $luniT = mysql_real_escape_string($_POST['luniT']);
            $nrScariT = mysql_real_escape_string($_POST['nr_scariT']);

			$soldT = mysql_real_escape_string($_POST['soldT']);

            if($penalizareT > 0.2) {
                  $i=1;
                  $penalizareErrT='Procentul de penalizare trebuie sa fie maximum 0.2<br>';
            }
            if(ereg('[^0-9.]', $penalizareT)) {
                  $i=1;
                  $penalizareErrT='Procentul de penalizare trebuie sa fie maximum 0.2<br>';
            }
			if (!ctype_digit($nrPasanteT)){
				$i = 1;
				$pasanteErrT = 'Acest camp poate sa contina numai cifre.';
			}
            if($termenT > 20 || $termenT < 1) {
				$i=1;
                $termenErrT='Termenul de plata trebuie sa fie cuprins intre 1 si 20 de zile<br>';
            }
            if(ereg('[^0-9]', $termenT)) {
                  $i=1;
                  $termenErrT='(Termenul de plata)Acest camp poate sa contina doar cifre.<br>';
            }
            if($predareT > 31 && $predareT <1) {
                  $i=1;
                  $predareErrT='Termenul de plata trebuie sa fie intre 1 si 31 zile<br>';
            }
            if(ereg('[^0-9]', $pausal_receT)) {
                  $i=1;
                  $pausal_receErrT='(Pausal Apa rece)Acest camp poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $pausal_caldaT)) {
                  $i=1;
                  $pausal_caldErrT='(Pausal Apa calda)Acest camp poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $luniT)) {
                  $i=1;
                  $luniErrT = '(Numarul de luni repetari)Acest camp poate sa contina doar cifre.<br>';
            }
            if(ereg('[^0-9]', $nrScariT)) {
                  $i=1;
                  $nrScariErrT = '(Nr scari)Acest camp poate sa contina doar cifre.<br>';
            }
			if(ereg('[^0-9\-\.]', $soldT)) {
              $i=13;
              $nrScariErr = 'Acest camp poate sa contina doar cifre.';
			}

           /* $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                  if ($row['o_scara'] == 1) $nrScariT = 1;
            } */

            if ($nrScariT == '') {
                  $i=1;
                  $nrScariErrT = 'Acest camp trebuie completat.<br>';
            }

            if ($i==0) {
            	include_once("modules/fise/Penalizare.class.php");
            	Penalizare::schimbaProcentPenalizare($_GET['asoc_id'], $penalizareT);

                    $query = "UPDATE asociatii_setari SET `penalizare`='$penalizareT', `termen`='$termenT', `pasante`='$nrPasante',
														  `impartire1`='$impartire1T', `impartire2`='$impartire2T',
                                                          `predare`='$predareT', `criteriu1`='$criteriu1T', `criteriu2`='$criteriu2T',
                                                          `pausal_rece`='$pausal_receT', `pausal_cald`='$pausal_caldaT', `luni`='$luniT', `pachet_id`='$pachetT',
                                                          `nr_scari`='$nrScariT', `factura_auto`='$autoFT', `data_factura`='$dataFT'
                                      WHERE set_id='$setId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
					$query_sold = "UPDATE casierie SET suma='$soldT' WHERE asoc_id='$asocId' AND scara_id IS NULL AND loc_id IS NULL";
					mysql_query($query_sold) or die('Nu pot updata setarile asociatiei (SOLD IN CASA)<br />'.mysql_error());

                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
    }

    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $asociatie == '') {
            $query = "DELETE FROM asociatii_setari WHERE set_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Setarile initiale au fost sterse.</font>';
    }

?>

<div id="maincon" style="width:500px;">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$penalizareErrT.$termenErrT.$predareErrT.$pausal_receErrT.$pausal_caldErrT.$luniErrT.$nrScariErrT.$dataFErrT.$mesaj1.'</font>
                  </div>
            ';
        }
        if ($sterge != '' && $mesaj1 != '') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$mesaj1.'</font>
                  </div>
            ';
        }
?>
    <table border=0>
           <?php
              $query = "SELECT ASET.*, A.asociatie FROM asociatii_setari AS ASET
                                                                        JOIN asociatii AS A ON ASET.asoc_id=A.asoc_id
                              WHERE A.asoc_id='$asocId'
                             ORDER BY A.asoc_id DESC";
              $result = mysql_query($query) or die(mysql_error());
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
						$sql_sold = "SELECT * FROM `casierie` WHERE asoc_id='$asocId' and scara_id IS null and loc_id IS NULL";
						$query_sold = mysql_query($sql_sold) or die(mysql_error());
						$soldD = mysql_result($query_sold, 0, 'suma');

                        if ($editSet == $row['set_id']){

                            echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=asoc_setari&asoc_id='.$asocId.'">';
                                echo '<input type="hidden" name="salveaza" value="apasat" />';
                                echo '<input type="hidden" name="set" value="'.$editSet.'" />';

								echo '<tr><td bgcolor="#19AF62" style="width:200px;"><font size=2 color="white">Asociatie</font></td><td>'.$row['asociatie'].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr Scari</font></td>';
                                          if ($row['nr_scari'] != 1) {
                                                echo '<td><input type="text" name="nr_scariT" value="'.$row['nr_scari'].'" /></td>';
                                          } else {
                                                echo '<td><input type="text" style="width:20px;" name="nr_scariT" value="1" /></td>';
                                          }
                                    '</tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Procent penalizare</font></td><td><input style="width:120px;" type="text" name="penalizareT" value="'.$row['penalizare'].'"></td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Termen Plata</font></td><td><input style="width:120px;" type="text" name="termenT" value="'.$row['termen'].'"></td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr. Pasante</font></td><td><input style="width:120px;" type="text" name="nrPasanteT" value="'.$row['pasante'].'"></td></tr>';
								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa cand declara toti</font></td>
                                            <td>
                                                <select name="impartire1T">';
                                                                  foreach ($apaDeclaraArr as $key=>$value) {
                                                                        if ( $row['impartire1'] == $key ) {
                                                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                                        }
                                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                                  }
                                          echo '</select>
                                            </td>
                                     </tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa NU cand declara</font></td>
                                        <td>
                                                    <select name="impartire2T">';
                                                                      foreach ($apaNuDeclaraArr as $key=>$value) {
                                                                            if ( $row['impartire2'] == $key ) {
                                                                                echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                                            }
                                                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                                                      }
                                              echo '</select>
                                                </td>
                                     </tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Zi predare citiri</center></font></td><td><input type="text" name="predareT" value="'.$row['predare'].'"</td></tr>';

                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Citire gresita</font></td>
                                            <td>
                                                <select name="criteriu1T">';
                                                      foreach ($asocCriteriuGresitArr as $key=>$value) {
                                                            if ( $row['criteriu1'] == $key ) {
                                                                echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                            }
                                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                                      }
                                                echo '</select>
                                            </td>
                                     </tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa intre scari</font></td>
                                            <td>
                                                <select name="criteriu2T">';
                                                      foreach ($asocCriteriuImpartireArr as $key=>$value) {
                                                            if ( $row['criteriu2'] == $key) {
                                                                echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                                            }
                                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                                      }
                                                echo '</select>
                                            </td>
                                      </tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Pausal apa rece</font></td><td><input type="text" name="pausal_receT" value="'.$row['pausal_rece'].'" /></td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Pausal apa calda</font></td><td><input type="text" name="pausal_caldaT" value="'.$row['pausal_cald'].'" /></td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr luni repetari</font></td><td><input type="text" name="luniT" value="'.$row['luni'].'" /></td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Sold in casa</font></td>
                                            <td>
                                                <input type="text" name="soldT" value="'.$soldD.'" />
                                            </td>
                                </tr>';

                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white"></font></td><td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td></tr>';
                            echo '</form>';
                        }else {
                                echo '<tr><td bgcolor="#19AF62" style="width:200px;"><font size=2 color="white">Asociatie</font></td><td>'.$row['asociatie'].'</td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr Scari</font></td><td>'.$row['nr_scari'].'</td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Procent penalizare</font></td><td>'.$row['penalizare'].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Termen Plata</font></td><td>'.$row['termen'].'</td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr. Pasante</font></td><td>'.$row['pasante'].'</td></tr>';
								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa cand declara toti</font></td><td>'.$apaDeclaraArr[$row['impartire1']].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa NU cand declara</font></td><td>'.$apaNuDeclaraArr[$row['impartire2']].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Zi predare citiri</center></font></td><td>'.$row['predare'].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Citire gresita</font></td><td>'.$asocCriteriuGresitArr[$row['criteriu1']].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Impartire apa intre scari</font></td><td>'.$asocCriteriuImpartireArr[$row['criteriu2']].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Pausal apa rece</font></td><td>'.$row['pausal_rece'].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Pausal apa calda</font></td><td>'.$row['pausal_cald'].'</td></tr>';
                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Nr luni repetari</font></td><td>'.$row['luni'].'</td></tr>';

								echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Sold in casa</font></td><td>'.$soldD.'</td></tr>';

                                echo '<tr><td bgcolor="#19AF62" ><font size=2 color="white">Optiuni</font></td>
                                        <td><center>
                                                <a style="font-size:12px;" href="index.php?link=asoc_setari&asoc_id='.$asocId.'&edit='.$row['set_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['asociatie']."','".$row['set_id']."'".')" >[sterge]</a>
                                        </center></td></tr>';
                        }
              }
           ?>
    </table>
</div></div>

