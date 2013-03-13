<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
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


$asocId = $_GET['asoc_id'];

$query = "SELECT nr_scari FROM asociatii_setari WHERE asoc_id='$asocId'";
$result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
     $nrScari = $row['nr_scari'];        
}

$i = 0;
$scariArr = array();
for ($j=0; $j<$nrScari; $j++) {
    $scariArr[] = $_POST['scara_'.$j];
    if ($_POST['scara_'.$j] == '') {        
        $i = 1;
    }    
    if ($_POST['bloc_'.$j] == '') {        
        $i = 1;
    }
}
if ($_POST['buton'] == 'apasat' && $i == 0 ) {
        $i = 0;
        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        for ($j=0; $j<$nrScari; $j++) {
            $scara = mysql_real_escape_string($_POST['scara_'.$j]);
            $strada = mysql_real_escape_string($_POST['strada_'.$j]);
                         
            $query = "SELECT str_id FROM strazi WHERE strada='$strada'";
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
                 $strId[$j] = $row['str_id'];
                 //$strada = $row['str_id'];
            }
            if ($strId[$j] == '') {
                $strIdErr[$j] = 'Aceasta strada este inexistenta in baza de date!'; 
                $i = 1;
            }
        }
                
        if ($i==0) {         
                for ($j=0; $j<$nrScari; $j++) {
                    $scara = mysql_real_escape_string($_POST['scara_'.$j]);
                    $strada = mysql_real_escape_string($_POST['strada_'.$j]);                
                    $nr = mysql_real_escape_string($_POST['nr_'.$j]);
                    $bloc = mysql_real_escape_string($_POST['bloc_'.$j]);
                    
                    $query = "SELECT str_id FROM strazi WHERE strada='$strada'";
                    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
                    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
                         //$strId[$j] = $row['str_id'];
                         $strada = $row['str_id'];
                    }
                    $query = "INSERT INTO scari (`asoc_id`, `scara`, `strada`, `nr`, `bloc`) VALUES 
                                                            ('$asocId', '$scara', '$strada', '$nr', '$bloc')";                                
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');        
                }                                
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
				$completed = true;
                unset ($_POST);                                             
                //if ($link=='w_asoc_fonduri') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_setari&asoc_id='.$asocId.'"</script>';                
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }                                       
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" align="left"><div id="maincon" >
<?php
if ($link=='w_scari') {
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_scari&asoc_id='.$asocId.'">';
} else if ($link == 'asociatii'){  // aici trebuie schimbat ex: asoc_dat - de pus in index
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=asociatii">';
}
?>        
                <input type="hidden" name="buton" value="apasat" /> 
                
                <?php 
                /*
                Asociatie:
                <select name="asociatie">
                        <?php 
                              $query = "SELECT * FROM asociatii ORDER BY `asoc_id`";
                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                              echo '<option value="nimic">Alege asociatia</option>';
                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                  if ($asocId == $row['asoc_id'])
                                        echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';                                                  
                                  echo '<option value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                              }
                        ?>                        
                </select>                               
                */
                ?>
                Asociatie:                            
                <?php                                                 
                      $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
                      $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                                                  
                          echo $row['asociatie'];
                      }   
                ?>
                <?php if($completed<>TRUE) { ?>
                <table cellspacing=1 style="margin:20px 0 0 0px; width:230px;" border=0>                                                
                        <tr bgcolor="#19AF62">
                               <td><font size=2 color="white"><center>Scara</center></font></td>
                               <td><font size=2 color="white"><center>Strada</center></font></td>
                               <td><font size=2 color="white"><center>Nr</center></font></td>
                               <td><font size=2 color="white"><center>Bloc</center></font></td>                                                      
                        </tr>
                        <?php
                        for ($j=0; $j<$nrScari; $j++) {                            
                            if ($strIdErr[$j] != '') echo '<tr><td></td><td><font color="red">'.$strIdErr[$j].'</font></td></tr>';
                            echo '<tr>';
                                    echo '<td><input style="width:60px;" type="text" name="scara_'.$j.'" value="'.$_POST['scara_'.$j].'"></td>';                                    
                                    
                                    //echo '<td><input style="width:150px;" type="text" name="strada_'.$j.'" value="'.$_POST['strada_'.$j].'"></td>';                                    
                                    echo "<td>
                                            <input type='text' name='strada_".$j."' style='font-family:verdana;width:150px;' id='tb_".$j."' value='".$_POST['strada_'.$j]."'/> 
                                                <script>
                                                var obj = actb(document.getElementById('tb_".$j."'),strArr);                                        
                                            </script>
                                          </td>
                                    ";
                                    echo '<td><input style="width:60px;" type="text" name="nr_'.$j.'" value="'.$_POST['nr_'.$j].'"></td>';                                    
                                    echo '<td><input style="width:60px;" type="text" name="bloc_'.$j.'" value="'.$_POST['bloc_'.$j].'"></td>                                                                                    
                                  </tr>';
                        }
                                
                        ?>                        
                        <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>                        
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
                <?php }
                    
                        echo '<table><tr>';
                            echo '
                                    <td><div id="butonBack"><a href="index.php?link=w_asoc_setari&asoc_id='.$asocId.'" style="">Pasul Anterior</a></div></td>
                                    <td><div id="buton"><a href="index.php?link=w_scari_dat&asoc_id='.$asocId.'" style="">Pasul Urmator</a></div></td>';                                    
                        echo '</tr>
                              </table>';
                ?>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE DATORII ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=w_strazi&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
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
                   <td><font size=2 color="white"><center>Asociatia</center></font></td>
                   <td><font size=2 color="white"><center>Scara</center></font></td>
                   <td><font size=2 color="white"><center>Strada</center></font></td>
                   <td><font size=2 color="white"><center>Nr</center></font></td>
                   <td><font size=2 color="white"><center>Bloc</center></font></td>                                                      
                   <td><font size=2 color="white"><center>Optiuni</center></font></td>                                                      
            </tr>
           <?php
              $query = "SELECT S.*, A.asociatie, ST.strada FROM scari AS S 
                                        JOIN asociatii AS A ON S.asoc_id=A.asoc_id                                                
                                        JOIN strazi AS ST ON ST.str_id=S.strada
                             WHERE A.asoc_id='$asocId'                                             
                             ORDER BY S.scara";                                                                     
              $result = mysql_query($query) or die(mysql_query());
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editSc == $row['scara_id']){                                
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=w_scari&asoc_id='.$asocId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="sc" value="'.$editSc.'" />';                                    
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
                                                    <a style="font-size:12px;" href="index.php?link=w_scari&asoc_id='.$asocId.'&edit='.$row['scara_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;                                                    
                                          </center></td>';                                
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

