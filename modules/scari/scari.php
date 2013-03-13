<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        function functionSubmit1() {
             document.addForm1.submit();
        }
		function infoAsoc(obj) {
            window.location = "index.php?link=scari&asoc_id="+obj
        }
        var strArr=new Array();
</script>

<?php
$nr = 0;
$query = "SELECT * FROM strazi ORDER BY `strada`";
$result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        echo "<script> strArr[".$nr."] = '".$row['strada']."'; </script>";
        $nr ++;
}

$asocId = mysql_real_escape_string($_POST['asociatia']);
if ($_GET['edit'] != '') {
    $asocId = mysql_real_escape_string($_GET['edit']);
}
if ($_POST['asoc'] != '') {
    $asocId = mysql_real_escape_string($_POST['asoc']);
}

if ($_GET['asoc_id'] != '') {
    $asocId = mysql_real_escape_string($_GET['asoc_id']);
}


if ($_POST['buton'] == 'apasat' && $_POST['scara'] != '' && $_POST['strada'] != '' && $_POST['bloc'] != '') {
        $i = 0;

        $scara = mysql_real_escape_string($_POST['scara']);
        $strada = mysql_real_escape_string($_POST['strada']);

        $scNr = 0;

		$q1 = "SELECT * FROM scari WHERE asoc_id=".$asocId;
		$q1 = mysql_query($q1) or die("Nu pot afla numarul de scari --> ".mysql_error());

		if (mysql_num_rows($q1) > 0){
				$query = "SELECT A.* FROM asociatii_setari AS A JOIN scari AS S ON A.asoc_id=S.asoc_id WHERE A.asoc_id='$asocId'";
				$result = mysql_query($query) or die("Ceva nu merge bine".mysql_error());
				while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
					 $scNr++;
					 $numar = $row['nr_scari'];
				}
		}
		else
		{
			$query = "SELECT * FROM asociatii_setari WHERE asoc_id=".$asocId;
			$query = mysql_query($query) or die("Nu pot afla numarul de scari --> ".mysql_error());

			$numar = mysql_result($query, 0, 'nr_scari');
		}

        if ($scNr >= $numar) {
            $strIdErr = 'Numarul maxim de scari pe care puteti sa le introduceti pentru aceasta asociatie este '.$numar;
            $i = 1;
        }

        $query = "SELECT str_id FROM strazi WHERE strada='$strada'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
             $strId = $row['str_id'];
        }
        if ($strId == '') {
            $strIdErr .= 'Aceasta strada este inexistenta in baza de date!';
            $i = 1;
        }

        if ($i==0) {
                $scara = mysql_real_escape_string($_POST['scara']);
                $strada = mysql_real_escape_string($_POST['strada']);
                $nr = mysql_real_escape_string($_POST['nr']);
                $bloc = mysql_real_escape_string($_POST['bloc']);

                $query = "SELECT str_id FROM strazi WHERE strada='$strada'";
                $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                     $strada = $row['str_id'];
                }
                $query = "INSERT INTO scari (`asoc_id`, `scara`, `strada`, `nr`, `bloc`) VALUES
                                                        ('$asocId', '$scara', '$strada', '$nr', '$bloc')";
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');

                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);
                /*if ($link=='w_asoc_fonduri') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_setari&asoc_id='.$asocId.'"</script>';*/
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}

?>
<div id="mainCol" class="clearfix" ><div id="maincon" >
        <form id="addForm" name="addForm" method="post" action="index.php?link=scari&asoc_id=<?php echo $asocId; ?>">
			<table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>
				<tr>
                    <td width="106" bgcolor="#CCCCCC">Asociatia:</td>
                    <td width="117" bgcolor="#CCCCCC">
						<?php
							$query = "SELECT * FROM asociatii "." ORDER BY administrator_id, asoc_id";
							echo '<select name="asociatia" onchange="infoAsoc(this.value)"><option value="">Alege asociatia</option>';
							$result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
							while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
								if ($asocId == $row['asoc_id'] )  echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
								else echo '<option  value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
							}
							echo '</select>';
						?>
                    </td>
                </tr>
            </table>
        </form>
        </div>

        <div id="maincon" >
        <form id="addForm1" name="addForm1" method="post" action="index.php?link=scari&asoc_id=<?php echo $asocId; ?>">
               <input type="hidden" name="buton" value="apasat" />
               <table cellspacing=1 style="margin:20px 0 0 0px; width:230px;" border=0>
                    <tr bgcolor="#999999">
                           <td><font size=2 color="white"><center>Scara</center></font></td>
                           <td><font size=2 color="white"><center>Strada</center></font></td>
                           <td><font size=2 color="white"><center>Nr</center></font></td>
                           <td><font size=2 color="white"><center>Bloc</center></font></td>
                    </tr>
                    <?php
                        if ($strIdErr != '') echo '<tr><td></td><td><font color="red">'.$strIdErr.'</font></td></tr>';
                        echo '<tr>';
                                echo '<td><input style="width:60px;" type="text" name="scara" value="'.$_POST['scara'].'"></td>';
                                echo "<td>
                                        <input type='text' name='strada' style='font-family:verdana;width:150px;' id='tb' value='".$_POST['strada']."'/>
                                            <script>
                                            var obj = actb(document.getElementById('tb'),strArr);
                                        </script>
                                      </td>
                                ";
                                echo '<td><input style="width:60px;" type="text" name="nr" value="'.$_POST['nr'].'"></td>';
                                echo '<td><input style="width:60px;" type="text" name="bloc" value="'.$_POST['bloc'].'"></td>
                              </tr>';
                    ?>
                    <tr><td></td><td><input type="submit" name="Afiseaza2" value="Salveaza" id="Salveaza" /></td></tr>

            </table>
                    <?php if ($mesaj != '') echo $mesaj; ?>

        </form>
</div>

<br />

<?php /*********************************EDITEAZA SI STERGE DATORII ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=scari&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editSc = mysql_real_escape_string($_GET['edit']);

    if ($_POST['salveaza'] == 'apasat') {
            $scId = mysql_real_escape_string($_POST['sc']);
            $scaraT = mysql_real_escape_string($_POST['scaraT']);
            //$stradaT = mysql_real_escape_string($_POST['stradaT']);
            $nrT = mysql_real_escape_string($_POST['nrT']);
            $blocT = mysql_real_escape_string($_POST['blocT']);

            $query = "UPDATE scari SET `scara`='$scaraT',`nr`='$nrT',`bloc`='$blocT' WHERE scara_id='$scId'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
            $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
            unset ($_POST);
    }

    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $asociatie == '') {
            $query = "DELETE FROM scari WHERE scara_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Detaliile au fost sterse.</font>';
    }

?>

<div id="maincon" style="width:625px;">
<?php
        if ($sterge != '' && $mesaj1 != '') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$mesaj1.'</font>
                  </div>
            ';
        }
?>
    <table width=600>
            <tr bgcolor="#19AF62">
                   <td bgcolor="#999999"><font size=2 color="white"><center>Asociatia</center></font></td>
         <td bgcolor="#999999"><font size=2 color="white"><center>Scara</center></font></td>
         <td bgcolor="#999999"><font size=2 color="white"><center>Strada</center></font></td>
         <td bgcolor="#999999"><font size=2 color="white"><center>Nr</center></font></td>
         <td bgcolor="#999999"><font size=2 color="white"><center>Bloc</center></font></td>
              <td bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>
            </tr>
           <?php
              $query = "SELECT S.*, A.asociatie, ST.strada FROM scari AS S JOIN asociatii AS A ON S.asoc_id=A.asoc_id JOIN strazi AS ST ON ST.str_id=S.strada WHERE A.asoc_id='$asocId' ORDER BY S.scara";
              $result = mysql_query($query) or die(mysql_query());
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editSc == $row['scara_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=scari&asoc_id='.$asocId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="sc" value="'.$editSc.'" />';
                                    echo '<input type="hidden" name="asoc" value="'.$asocId.'" />';

                                    echo '<td>'.$row['asociatie'].'</td>';
                                    echo '<td><input style="width:60px;" type="text" name="scaraT" value="'.$row['scara'].'"></td>';
                                    echo '<td>'.$row['strada'].'</td>';
                                    echo '<td><input style="width:30px;" type="text" name="nrT" value="'.$row['nr'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="blocT" value="'.$row['bloc'].'"></td>';

                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                                    echo '<td>'.$row['asociatie'].'</td>';
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>'.$row['strada'].'</td>';
                                    echo '<td>'.$row['nr'].'</td>';
                                    echo '<td>'.$row['bloc'].'</td>';
                                    echo '<td><center>
                                                    <a style="font-size:12px;" href="index.php?link=scari&asoc_id='.$asocId.'&edit='.$row['scara_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                          </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>
</div></div>

