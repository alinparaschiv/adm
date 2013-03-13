<script language="javascript">
        function functionSubmit() {
             document.addForm.submit();
        }
        var strArr=new Array();

</script>
<?php
$asocId = mysql_real_escape_string($_POST['asociatia']);
if ($_GET['edit'] != '') {
    $asocId = mysql_real_escape_string($_GET['edit']);
}
if ($_GET['asoc_id'] != '') {
    $asocId = mysql_real_escape_string($_GET['asoc_id']);
}
?>
<div id="mainCol" class="clearfix" ><div id="maincon">
        <form id="addForm" name="addForm" method="post" action="index.php?link=asociatii">
                <input type="hidden" name="search" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>
                        <?php if ($asociatieErr != '') echo '<tr><td></td><td><font color="red">'.$asociatieErr.'</font></td></tr>'; ?>
                        <tr><td bgcolor="#CCCCCC">Asociatia:</td>
                            <td bgcolor="#CCCCCC">
                          <select name="asociatia">
                                        <?php
                                              $query = "SELECT * FROM asociatii ORDER BY `asociatie`";
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
                          <td align="right" bgcolor="#CCCCCC"><input name="Afiseaza" type="submit" id="Afiseaza" value="Afiseaza" /></td>
                        </tr>
                </table>
        </form>
</div>

<?php /********************************* EDITEAZA SI STERGE ASOCIATII ****************************************/ ?>


<script type="text/javascript">
    function functionStergeFur(furnizor,fur_id){
         var answer = confirm ("Esti sigur ca vrei sa stergi asociatia "+furnizor+" ?");
         if(answer)
            window.location = "index.php?link=asociatii&sterge="+fur_id;
    }
    function functionFormEdit(){
        document.formEdit.submit();
    }
</script>

<?php
    $editAsoc = mysql_real_escape_string($_GET['edit']);
    if ($_POST['salveaza'] == 'apasat') {
            $asocId = mysql_real_escape_string($_POST['aso']);

            $asociatieT = mysql_real_escape_string($_POST['asociatieT']);
            $stradaT = mysql_real_escape_string($_POST['stradaT']);
            if ($stradaT != '') {
                $query = "SELECT str_id FROM strazi WHERE strada='$stradaT'";
                $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                     $stradaT = $row['str_id'];
                }
            }
            $nrT = mysql_real_escape_string($_POST['nrT']);
            $scaraT = mysql_real_escape_string($_POST['scaraT']);
            $blocT = mysql_real_escape_string($_POST['blocT']);
            //$orasT = mysql_real_escape_string($_POST['posesorT']);
            $cod_fiscalT = mysql_real_escape_string($_POST['cod_fiscalT']);
            $presedinteT = mysql_real_escape_string($_POST['presedinteT']);
            $cont_bancarT = mysql_real_escape_string($_POST['cont_bancarT']);

			$adresa_casierieT = mysql_real_escape_string($_POST['adresa_casierieT']);
			$orar_casierieT = mysql_real_escape_string($_POST['orar_casierieT']);

            $o_scaraT = mysql_real_escape_string($_POST['o_scaraT']);
            if ($o_scaraT == 'on') { $o_scaraVal = 1; } else { $o_scaraVal = 0; }
            if ($i==0) {
                    $query = "UPDATE asociatii SET `asociatie`= '$asociatieT', `str_id`= '$stradaT', `nr`='$nrT', `scara`='$scaraT',
                                                    `bloc`='$blocT', `cod_fiscal`='$cod_fiscalT', `presedinte`='$presedinteT',
                                                    `cont_id`='$cont_bancarT', `o_scara`='$o_scaraVal', `adresa_casierie`='$adresa_casierieT', `orar_casierie`='$orar_casierieT'
                                    WHERE asoc_id='$asocId'";
                    //echo $query;
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
    }

    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $asociatie == '') {
            $query = "DELETE FROM asociatii WHERE asoc_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Asociatia a fost stearsa.</font>';
            $asocId = '';
    }
    if ( $asocId != '' ) {
        echo '<br /><br /><br />';
    }
?>

<div id="maincon" style="width:900px;">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$asociatieErr1.$mesaj1.'</font>
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
<table width=900>
       <tr bgcolor="#19AF62">
               <td bgcolor="#999999"><font size=2 color="white"><center>Asociatie</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Strada</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Nr</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Scara</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Bloc</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Oras</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Presedinte</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Cod Fiscal</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Cont Bancar</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>O Scara</center></font></td>

   <td bgcolor="#999999"><font size=2 color="white"><center>Adresa Casierie</center></font></td>
   <td bgcolor="#999999"><font size=2 color="white"><center>Orar Casierie</center></font></td>

   <td bgcolor="#999999" width=80><font size=2 color="white"><center>Optiuni</center></font></td>
       </tr>
      <?php

        if ( $asocId == '' ) {
            $query = "SELECT A.*, S.strada, C.cont FROM asociatii AS A JOIN strazi AS S ON A.str_id=S.str_id JOIN conturi AS C ON A.cont_id=C.cont_id ORDER BY A.asociatie";
      } else {
            $query = "SELECT A.*, S.strada, C.cont FROM asociatii AS A JOIN strazi AS S ON A.str_id=S.str_id JOIN conturi AS C ON A.cont_id=C.cont_id  WHERE A.asoc_id = '$asocId' ORDER BY A.asociatie";
      }
     	$result = mysql_query($query) or die(mysql_error());
      $i=0;
      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
           //schimb culoarea la coloane
           if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                if ($editAsoc == $row['asoc_id']){
                        echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=asociatii&asoc_id='.$asocId.'">';
                            echo '<input type="hidden" name="salveaza" value="apasat" />';
                            echo '<input type="hidden" name="aso" value="'.$editAsoc.'" />';

                            echo '<td><input style="width:120px;" type="text" name="asociatieT" value="'.$row['asociatie'].'"></td>';
                            echo '<td>';
                                  $nr = 0;
                                  $q = "SELECT * FROM strazi ORDER BY `strada`";
                                  $res = mysql_query($q) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                  while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
                                      echo "<script> strArr[".$nr."] = '".$r['strada']."'; </script>";
                                      $nr ++;
                                  }
                                  ?>
                                    <input type='text' name="stradaT" style='font-family:verdana;width:150px;font-size:12px' id='tb1' value="<?php echo $row['strada']?>"/>
                                        <script>
                                            var obj = actb(document.getElementById('tb1'),strArr);
                                            //setTimeout(function(){obj.actb_keywords = custom2;},10000);
                                        </script>
                                 <?php
                            echo '</td>';
                            echo '<td><input style="width:30px;" type="text" name="nrT" value="'.$row['nr'].'"></td>';
                            echo '<td><input style="width:30px;" type="text" name="scaraT" value="'.$row['scara'].'"></td>';
                            echo '<td><input style="width:40px;" type="text" name="blocT" value="'.$row['bloc'].'"></td>';
                            echo '<td>'.$row['oras'].'</td>';
                            echo '<td><input style="width:60px;" type="text" name="presedinteT" value="'.$row['presedinte'].'"></td>';
                            echo '<td><input style="width:60px;" type="text" name="cod_fiscalT" value="'.$row['cod_fiscal'].'"></td>';
                            echo '<td>';
                                    echo '<select name="cont_bancarT">';
                                          $q = "SELECT * FROM conturi ORDER BY `cont`";
                                          $res = mysql_query($q) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                                          while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
                                              if ($row['cont_id'] == $r['cont_id']) {
                                                    echo '<option selected="selected" value="'.$r['cont_id'].'">'.$r['cont'].'</option>';
                                              }
                                              echo '<option value="'.$r['cont_id'].'">'.$r['cont'].'</option>';
                                          }
                                    echo '</select>';
                            echo '</td>';
                            if ( $row['o_scara'] == 1) {
                            echo '<td><input style="width:60px;" type="checkbox" name="o_scaraT" checked="on"></td>';
                            } else {
                            echo '<td><input style="width:60px;" type="checkbox" name="o_scaraT"></td>';
                            }

							echo '<td><input style="width:60px;" type="text" name="adresa_casierieT" value="'.$row['adresa_casierie'].'"></td>';
							echo '<td><input style="width:60px;" type="text" name="orar_casierieT" value="'.$row['orar_casierie'].'"></td>';

                            echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                        echo '</form>';
                } else {
                        echo '<td>'.$row['asociatie'].'</td>';
                        echo '<td>'.$row['strada'].'</td>';
                        echo '<td>'.$row['nr'].'</td>';
                        echo '<td>'.$row['scara'].'</td>';
                        echo '<td>'.$row['bloc'].'</td>';
                        echo '<td>'.$row['oras'].'</td>';
                        echo '<td>'.$row['presedinte'].'</td>';
                        echo '<td>'.$row['cod_fiscal'].'</td>';
                        echo '<td>'.$row['cont'].'</td>';

                        if ( $row['o_scara'] == 1) {
                        echo '<td>Bifat</td>';
                        } else {
                        echo '<td>Ne bifat</td>';
                        }

						echo '<td>'.$row['adresa_casierie'].'</td>';
						echo '<td>'.$row['orar_casierie'].'</td>';

                        echo '<td><center>
                                        <a style="font-size:12px;" href="index.php?link=asociatii&edit='.$row['asoc_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                        <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['asociatie']."','".$row['asoc_id']."'".')" >[sterge]</a>
                              </center></td>';
                }
           echo '</tr>';
           $i++;
      }
?>
</table>

</div></div>