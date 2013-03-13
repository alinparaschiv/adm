<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
</script>

<?php
if ($_POST['buton'] == 'apasat' && $_POST['furnizor'] != '' && $_POST['cod'] != '' && $_POST['penalizare'] != ''  && $_POST['gratie'] != '' ) {
        $furnizor = mysql_real_escape_string($_POST['furnizor']);
        $cod = mysql_real_escape_string($_POST['cod']);
        $penalizare = mysql_real_escape_string($_POST['penalizare']);
        $gratie = mysql_real_escape_string($_POST['gratie']);
				$cont = mysql_real_escape_string($_POST['cont']);

        $query = "SELECT * FROM furnizori WHERE furnizor='$furnizor'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
             $furnizorErr = 'Acest furnizor exista deja!';
             $i = 1;
        }
        if(ereg('[^0-9.]', $cod)) {
              $i=1;
              $errCod='Codul Fiscal poate sa contina doar cifre.';
        }
        if(ereg('[^0-9.]', $penalizare)) {
              $i=1;
              $penalizareErr='Procentul de penalizare poate sa contina doar cifre.<br />';
        }
        if(ereg('[^0-9]', $gratie)) {
              $i=1;
              $gratieErr='Perioada de gratie poate sa contina doar cifre.<br />';
        }
				if($gratie > 30) {
					    $i=1;
			        $gratieErr='Perioada de gratie poate fi maxim 30 de zile.<br />';
				}
        if($penalizare > 0.2) {
              $i=1;
              $penalizareErr='Procentul de penalizare trebuie sa fie maximum 0.2';
        }
		$q = "SELECT * FROM conturi WHERE cont_id=$cont";
		$q = mysql_query($q) or die("A aparut o problema la verificare Contului bancar pentru furnizorul curent <br />".mysql_error());
		if (mysql_fetch_row($q) == 0) {
					$i=1;
					$contErr='Contul bancar nu exista.<br />';
		}
        if ($i==0) {
                $query = "INSERT INTO furnizori (`furnizor`, `cont_id`, `cod_fiscal`, `proc_penalizare`, `per_gratie` ) VALUES ('$furnizor', '$cont', '$cod', '$penalizare', '$gratie')";
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" ><div id="maincon" >
        <form id="addForm" name="addForm" method="post" action="<?php 'index.php?link=furnizori';?>">
                <input type="hidden" name="buton" value="apasat" />
                <table style="margin:20px 0 0 0px; width:400px;" border=0>

                        <?php if ($furnizorErr != '') echo '<tr><td></td><td><font color="red">'.$furnizorErr.'</font></td></tr>'; ?>
                        <tr><td >Furnizor:</td><td><input type="text" name="furnizor" value="<?php echo $_POST['furnizor'];?>" /></td></tr>
                        <?php if ($contErr != '') echo '<tr><td></td><td><font color="red">'.$contErr.'</font></td></tr>'; ?>
                        <tr><td>Cont Bancar:</td><td><select name="cont">
                        <?php
                        	$q = "SELECT * FROM conturi";
                        	$q = mysql_query($q) or die("A aparut o erorea la extragerea conturilor <br />".mysql_error());
	                        while($r = mysql_fetch_array($q))
	          								echo '<option value="'.$r['cont_id'].'" '. ($_POST['cont']==$r['cont_id'] ? 'selected="selected"' : '').'>'.$r['cont'].'</option>';
												?>
												</select></td></tr>
                        <?php if ($errCod != '') echo '<tr><td></td><td><font color="red">'.$errCod.'</font></td></tr>'; ?>
                        <tr><td>Cod fiscal:</td><td><input type="text" name="cod" value="<?php echo $_POST['cod'];?>" /></td></tr>
                        <?php if ($penalizareErr != '') echo '<tr><td></td><td><font color="red">'.$penalizareErr.'</font></td></tr>'; ?>
                        <tr><td>Proc. Penalizare:</td><td><input type="text" name="penalizare" value="<?php echo $_POST['penalizare'];?>" /> (intre 0.001 si 0.2)</td></tr>
                        <?php if ($gratieErr != '') echo '<tr><td></td><td><font color="red">'.$gratieErr.'</font></td></tr>'; ?>
                        <tr><td>Per. Gratie:</td><td><input type="text" name="gratie" value="<?php echo $_POST['gratie'];?>" /> (intre 1 si 30 zile)</td></tr>

                        <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE FURNIZORIII****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi furnizorul "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=furnizori&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editFur = mysql_real_escape_string($_GET['edit']);

    if ($_POST['salveaza'] == 'apasat') {
            $furId = mysql_real_escape_string($_POST['fur']);
            $furnizor = mysql_real_escape_string($_POST['furnizorT']);
            $cod = mysql_real_escape_string($_POST['cod_fiscal']);
            $penalizare = mysql_real_escape_string($_POST['proc_penalizare']);
            $gratie = mysql_real_escape_string($_POST['per_gratie']);
    				$cont = mysql_real_escape_string($_POST['cont']);

            if(ereg('[^0-9]', $cod)) {
                  $i=1;
                  $errCod1='Codul Fiscal poate sa contina doar cifre.<br />';
            }
            if(ereg('[^0-9.]', $penalizare)) {
                  $i=1;
                  $penalizareErr1='Procentul de penalizare poate sa contina doar cifre.<br />';
            }
            if($penalizare > 0.2) {
                  $i=1;
                  $penalizareErr1='Procentul de penalizare trebuie sa fie maximum 0.2<br />';
            }
            if(ereg('[^0-9]', $gratie)) {
                  $i=1;
                  $gratieErr1='Perioada de gratie poate sa contina doar cifre.<br />';
            }
			    	$q = "SELECT * FROM conturi WHERE cont_id=$cont";
			    	$q = mysql_query($q) or die("A aparut o problema la verificare Contului bancar pentru furnizorul curent <br />".mysql_error());
			    	if (mysql_fetch_row($q) == 0) {
			    		$i=1;
			    		$contErr='Contul bancar nu exista.<br />';
			    	}
            if($gratie > 30 || $gratie < 1) {
                  $i=1;
                  $gratieErr1='Perioada de gratie trebuie sa fie cuprinsa intre 1 si 30 zile<br />';
            }
            if ($i==0) {
                    $query = "UPDATE furnizori SET `furnizor`= '$furnizor', `cod_fiscal`= '$cod', `proc_penalizare`= '$penalizare', `per_gratie`='$gratie', `cont_id`=$cont WHERE fur_id='$furId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
    }

    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $furnizor == '') {
        $query = "DELETE FROM furnizori WHERE fur_id='$sterge'";
        mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
        $mesaj1 = '<font color="red">Furnizorul a fost sters.</font>';

    }

?>

<div id="maincon">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$errCod1.$penalizareErr1.$gratieErr1.$mesaj1.'</font>
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
    <table width=600>
           <tr bgcolor="#19AF62">
               <td><font size=2 color="white"><center>Furnizor</center></font></td>
               <td><font size=2 color="white"><center>Cont Bancar</center></font></td>
               <td><font size=2 color="white"><center>Cod Fiscal</center></font></td>
               <td><font size=2 color="white"><center>Proc. Penalizare</center></font></td>
               <td><font size=2 color="white"><center>Per. Gratie</center></font></td>
               <td><font size=2 color="white"><center>Optiuni</center></font></td>
           </tr>
           <?php
              $query = "SELECT F.*, C.cont FROM furnizori F, conturi C WHERE F.cont_id=C.cont_id ORDER BY `fur_id` DESC";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //echo '<input type="hidden" name="furnizor'.$i.'" value="'.$row['furnizor'].'">';
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editFur == $row['fur_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=furnizori">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="fur" value="'.$editFur.'" />';
                                    echo '<td><input style="width:120px;" type="text" name="furnizorT" value="'.$row['furnizor'].'"></td>';
												            echo '<td><select name="cont">';
												            $q = "SELECT * FROM conturi";
												            $q = mysql_query($q) or die("A aparut o erorea la extragerea conturilor <br />".mysql_error());
												            while($r = mysql_fetch_array($q))
												           		echo '<option value="'.$r['cont_id'].'" '. ($row['cont_id']==$r['cont_id'] ? 'selected="selected"' : '').'>'.$r['cont'].'</option>';
																		echo '</select></td>';
                                    echo '<td><input style="width:50px;" type="text" name="cod_fiscal" value="'.$row['cod_fiscal'].'"></td>';
                                    echo '<td><input style="width:50px;" type="text" name="proc_penalizare" value="'.$row['proc_penalizare'].'"></td>';
                                    echo '<td><input style="width:50px;"type="text" name="per_gratie" value="'.$row['per_gratie'].'"></td>';
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                                echo '<td>'.$row['furnizor'].'</td>';
                        				echo '<td>'.$row['cont'].'</td>';
                                echo '<td>'.$row['cod_fiscal'].'</td>';
                                echo '<td>'.$row['proc_penalizare'].'</td>';
                                echo '<td>'.$row['per_gratie'].'</td>';
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=furnizori&edit='.$row['fur_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['furnizor']."','".$row['fur_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>
</div></div>

