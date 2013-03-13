<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }
        function infoScari(obj) {
            window.location = "index.php?link=scari_furnizori&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=scari_furnizori&asoc_id="+obj
        }
</script>

<?php

$asocId = $_GET['asoc_id'];

if ($_POST['buton'] == 'apasat' && $_POST['asociatia'] != 'nimic' && $_POST['datorie'] != '' && $_POST['nr'] != '' && $_POST['serie'] != '' && $_POST['cal1'] != '' && $_POST['cal2'] != '') {

	//$asociatie = mysql_real_escape_string($_POST['asociatia']);
	$scara = mysql_real_escape_string($_POST['scara']);
	$furnizor = mysql_real_escape_string($_POST['furnizor']);

	$codClient = mysql_real_escape_string($_POST['codClient']);

	$datorie = mysql_real_escape_string($_POST['datorie']);
	$procent = mysql_real_escape_string($_POST['procent']);

	$nr = mysql_real_escape_string($_POST['nr']);
	$serie = mysql_real_escape_string($_POST['serie']);
	$emitere =  mysql_real_escape_string($_POST['cal1']);
	$scadenta =  mysql_real_escape_string($_POST['cal2']);

/*
   $query = "SELECT * FROM scari_furnizori WHERE asoc_id = '$asocId' AND scara_id='$scara' AND fur_id='$furnizor'";
   $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
   while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
   $furnizorErr = 'Acest furnizor a fost deja alocat pentru aceasta scara!';
   $i = 1;
   } */
	if(ereg('[^0-9]', $datorie)) {
		$i=1;
		$errDatorie='Campul "Datorie" poate sa contina doar cifre.';
	}
	if ($i==0) {
		$query = "INSERT INTO scari_furnizori (`asoc_id`, `scara_id`, `fur_id`, `codClient`, `nr`, `serie`,`emitere`, `scadenta`, `datorie`, `penalizare`) VALUES ('$asocId', '$scara', '$furnizor', '$codClient', '$nr', '$serie', '$emitere', '$scadenta', '$datorie', '$procent')";
		mysql_query($query) or die(mysql_error());
		$asoc_id = mysql_insert_id();
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
    <div id="maincon" style="text-align:left;">
    <form id="addForm1" name="addForm1" method="post" action="index.php?link=scari_furnizori&asoc_id=<?php echo $asocId; ?>">
            Asociatia:
            <?php
            $query = "SELECT * FROM asociatii ORDER BY asociatie";
            echo '<select name="asociatia" onchange="infoAsoc(this.value)"><option value="">Alege asociatia</option>';
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            	if ($asocId == $row['asoc_id'] )  echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
            	else echo '<option  value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
            }
            echo '</select>';
            ?>




    </form>
    </div>

<div id="maincon" >
<?php
echo '<form id="addForm" name="addForm" method="post" action="index.php?link=scari_furnizori&asoc_id='.$asocId.'">';
?>
                <input type="hidden" name="buton" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>
                        <tr><td align="left" bgcolor="#CCCCCC">Asociatie:</td>
                            <td align="left" bgcolor="#CCCCCC">
                                        <?php
                                        $query = "SELECT * FROM asociatii ORDER BY `asoc_id`";
                                        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');

                                        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        	if ($asocId == $row['asoc_id'])
                                        		echo $row['asociatie'];
                                        }
                                        ?>
                            </td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Scara:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="scara">
                                        <?php
                                        $query = "SELECT * FROM scari WHERE asoc_id='$asocId' ORDER BY `scara`";
                                        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        	if ($scara == $row['scara_id'])
                                        		echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                        	echo '<option value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                        }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Nr:</td>
                          <td align="left" bgcolor="#CCCCCC"><input type="text" name="nr" value="<?php echo $_POST['nr']; ?>" /></td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Serie:</td>
                          <td align="left" bgcolor="#CCCCCC"><input type="text" name="serie" value="<?php echo $_POST['serie']; ?>" /></td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Data Emiterii:</td>
                            <td align="left" bgcolor="#CCCCCC">
                              <input type="text" class="datepicker" name="cal1" value="<?php echo $_POST['cal1']; ?>" />
                            </td>
                        </tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Data Scadentei:</td>
                            <td align="left" bgcolor="#CCCCCC">
                              <input type="text" class="datepicker" name="cal2" value="<?php echo $_POST['cal2']; ?>" />
                            </td>
                        </tr>
                        <?php if ($furnizorErr != '') echo '<tr><td></td><td><font color="red">'.$furnizorErr.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Furnizor:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <select name="furnizor">
                                        <?php
                                        $query = "SELECT F.* FROM furnizori AS F
                                                                    JOIN furnizori_servicii AS FS ON F.fur_id=FS.fur_id
                                                                    JOIN servicii AS S ON S.serv_id = FS.serv_id
                                                                    WHERE S.nivel<>1
                                                                    ORDER BY F.fur_id";
                                        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                        echo '<option value="nimic">Alege furnizor</option>';
                                        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        	if ($_POST['furnizor'] == $row['fur_id'])
                                        		echo '<option selected="selected" value="'.$row['fur_id'].'">'.$row['furnizor'].'</option>';
                                        	echo '<option value="'.$row['fur_id'].'">'.$row['furnizor'].'</option>';
                                        }
                                        ?>
                                </select>
                            </td>
                        </tr>

                        <tr bgcolor="#CCCCCC"><td align="left">Cod Client:</td><td align="left"><input type="text" name="codClient" value="<?php echo $_POST['codClient'];?>" /></td></tr>

						<?php if ($errDatorie != '') echo '<tr><td></td><td><font color="red">'.$errDatorie.'</font></td></tr>'; ?>
                        <tr><td align="left" bgcolor="#CCCCCC">Datorie Initiala:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="datorie" value="<?php echo $_POST['datorie'];?>" /></td></tr>
                        <tr><td align="left" bgcolor="#CCCCCC">Penalizare:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="procent" value="<?php echo $_POST['procent'];?>" /></td></tr>

                        <tr><td align="left" bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"><input type="submit" name="Salveaza" value="Salveaza" id="Salveaza" /></td></tr>

                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE DATORII ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi furnizorul "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=scari_furnizori&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
$editSf = mysql_real_escape_string($_GET['edit']);

if ($_POST['salveaza'] == 'apasat') {
	$sfId = mysql_real_escape_string($_POST['sf']);

	$nrT = mysql_real_escape_string($_POST['nrT']);
	$serieT = mysql_real_escape_string($_POST['serieT']);
	$emitereT =  mysql_real_escape_string($_POST['emitereT']);
	$scadentaT =  mysql_real_escape_string($_POST['scadentaT']);

	$codClientT = mysql_real_escape_string($_POST['codClientT']);
	$datorieT = mysql_real_escape_string($_POST['datorieT']);
	$procentT = mysql_real_escape_string($_POST['procentT']);

	if ($i==0) {
		$query = "UPDATE scari_furnizori SET `codClient`='$codClientT', `datorie`='$datorieT', penalizare='$procentT', `nr`='$nrT', `serie`='$serieT', `emitere`='$emitereT', `scadenta`='$scadentaT' WHERE sf_id='$sfId'";
		mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
		$mesaj1 = '<font color="green">Datele au fost introduse.</font>';
		unset ($_POST);
	} else {
		$mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
	}
}

$sterge = mysql_real_escape_string($_GET['sterge']);
if ($sterge != '' && $asociatie == '') {
	$query = "DELETE FROM scari_furnizori WHERE sf_id='$sterge'";
	mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
	$mesaj1 = '<font color="red">Furnizorul asociat a fost sters.</font>';
}
?>
<div id="maincon" style="width:825px;">
<?php
if ($i == 1 && $_POST['salveaza'] == 'apasat') {
	echo '<div id="errorBox" style="">
	            <font color="red">'.$errDatorie1.$mesaj1.'</font>
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
    <table width=800>
           <tr bgcolor="#19AF62">
               <td bgcolor="#999999"><font size=2 color="white"><center>Asociatie</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Scara</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Furnizor</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Cod Client</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Nr</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Serie</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Data Emitere</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Data Scadenta</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Datorie</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Penalizare</center></font></td>
             <td bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>
           </tr>
           <?php
           $query = "SELECT SF.*, A.asociatie, F.furnizor FROM scari_furnizori AS SF
                                                                        JOIN asociatii AS A ON SF.asoc_id=A.asoc_id
                                                                        JOIN furnizori AS F ON SF.fur_id=F.fur_id
                                                  WHERE A.asoc_id='$asocId'
                             ORDER BY A.asociatie";
           $result = mysql_query($query) or die(mysql_error());
           $i=0;
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
           	//schimb culoarea la coloane
           	if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
           	if ($editSf == $row['sf_id']){
           		echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=scari_furnizori&asoc_id='.$asocId.'">';
           		echo '<input type="hidden" name="salveaza" value="apasat" />';
           		echo '<input type="hidden" name="sf" value="'.$editSf.'" />';

           		echo '<td>'.$row['asociatie'].'</td>';
           		$scId = $row['scara_id'];
           		$q1 = "SELECT * FROM scari WHERE scara_id = '$scId'";
           		$res1 = mysql_query($q1) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
           		while($r1 = mysql_fetch_array($res1, MYSQL_ASSOC)) {
           			echo '<td>'.$r1['scara'].'</td>';
           		}
           		echo '<td>'.$row['furnizor'].'</td>';

           		echo '<td><input style="width:60px;" type="text" name="codClientT" value="'.$row['codClient'].'"></td>';

           		echo '<td><input style="width:60px;" type="text" name="nrT" value="'.$row['nr'].'"></td>';
           		echo '<td><input style="width:60px;" type="text" name="serieT" value="'.$row['serie'].'"></td>';
           		echo '<td><input style="width:60px;" type="text" class="datepicker" name="emitereT" value="'.$row['emitere'].'"></td>';
           		echo '<td><input style="width:60px;" type="text" class="datepicker" name="scadentaT" value="'.$row['scadenta'].'"></td>';
           		echo '<td><input style="width:60px;" type="text" name="datorieT" value="'.$row['datorie'].'"></td>';
           		echo '<td><input style="width:60px;" type="text" name="procentT" value="'.$row['penalizare'].'"></td>';

           		echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
           		echo '</form>';
           	} else {
           		echo '<td>'.$row['asociatie'].'</td>';
           		$scId = $row['scara_id'];
           		$q = "SELECT * FROM scari WHERE scara_id = '$scId'";
           		$res = mysql_query($q) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
           		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
           			echo '<td>'.$r['scara'].'</td>';
           		}
           		echo '<td>'.$row['furnizor'].'</td>';

           		echo '<td>'.$row['codClient'].'</td>';

           		echo '<td>'.$row['nr'].'</td>';
           		echo '<td>'.$row['serie'].'</td>';
           		echo '<td>'.$row['emitere'].'</td>';
           		echo '<td>'.$row['scadenta'].'</td>';
           		echo '<td>'.$row['datorie'].'</td>';
           		echo '<td>'.$row['penalizare'].'</td>';
           		echo '<td><center>
           		                <a style="font-size:12px;" href="index.php?link=scari_furnizori&asoc_id='.$asocId.'&edit='.$row['sf_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
           		                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['furnizor']."','".$row['sf_id']."'".')" >[sterge]</a>
           		      </center></td>';
           	}
           	echo '</tr>';
           	$i++;
           }
           ?>
    </table>
</div></div>

