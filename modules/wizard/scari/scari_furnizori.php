<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php

$asocId = $_GET['asoc_id'];

if ($_POST['buton'] == 'apasat' && $_POST['asociatie'] != 'nimic' && $_POST['datorie'] != '') {

        $asociatie = mysql_real_escape_string($_POST['asociatie']);
        $scara = mysql_real_escape_string($_POST['scara']);
        $furnizor = mysql_real_escape_string($_POST['furnizor']);
        $datorie = mysql_real_escape_string($_POST['datorie']);
        $procent = mysql_real_escape_string($_POST['procent']);
                
        $query = "SELECT * FROM scari_furnizori WHERE asoc_id = '$asociatie' AND scara_id='$scara' AND fur_id='$furnizor'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $furnizorErr = 'Acest furnizor a fost deja alocat pentru aceasta scara!';
             $i = 1;                 
        }
        if(ereg('[^0-9]', $datorie)) {
              $i=1;
              $errDatorie='Campul "Datorie" poate sa contina doar cifre.';
        }
        if ($i==0) {
                $query = "INSERT INTO scari_furnizori (`asoc_id`, `scara_id`, `fur_id`, `datorie`, `penalizare`) VALUES ('$asociatie', '$scara', '$furnizor', '$datorie', '$procent')";                
                
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');
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

<div id="mainCol" class="clearfix" align="left"><div id="maincon" >
<?php
if ($link=='w_scari_furnizori') {
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_scari_furnizori&asoc_id='.$asocId.'">';
} else if ($link == 'asociatii'){  // aici trebuie schimbat ex: asoc_dat - de pus in index
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=asociatii">';
}
?>        
                <input type="hidden" name="buton" value="apasat" />                
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>                                                
                        <tr><td>Asociatie:</td>
                            <td>
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
                            </td>
                        </tr>                        
                        <tr><td>Scara:</td>
                            <td>
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
                        <?php if ($furnizorErr != '') echo '<tr><td></td><td><font color="red">'.$furnizorErr.'</font></td></tr>'; ?>                                        
                        <tr><td>Furnizor:</td>
                            <td>
                                <select name="furnizor">
                                        <?php 
                                              $query = "SELECT F.* FROM furnizori AS F
                                                                    JOIN furnizori_servicii AS FS ON F.fur_id=FS.fur_id
                                                                    JOIN servicii AS S ON S.serv_id = FS.serv_id                   
                                                                    WHERE S.nivel=2
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
                        <?php if ($errDatorie != '') echo '<tr><td></td><td><font color="red">'.$errDatorie.'</font></td></tr>'; ?>                                                       
                        <tr><td>Datorie Initiala:</td><td><input type="text" name="datorie" value="<?php echo $_POST['datorie'];?>" /></td></tr>                        
                        <tr><td>Procent Penalizare:</td><td><input style="width:20px;" type="text" name="procent" value="<?php echo $_POST['procent'];?>" />%</td></tr>                        
                       
                        <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
                        <tr><td></td><td></td></tr>
                        <tr><td></td><td></td></tr>
                        <tr><td></td><td><div id="buton"><a href="" style="">Pasul Urmator</a></div></td></tr>
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
        window.location = "<?php echo 'index.php?link=w_scari_furnizori&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editSf = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $sfId = mysql_real_escape_string($_POST['sf']);
            
            $datorieT = mysql_real_escape_string($_POST['datorieT']);            
            $procentT = mysql_real_escape_string($_POST['procentT']);                        
            
            if ($i==0) {
                    $query = "UPDATE scari_furnizori SET `datorie`='$datorieT', penalizare='$procentT' WHERE sf_id='$sfId'";                    
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

<div id="maincon" style="width:625px;">
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
    <table width=600>
           <tr bgcolor="#19AF62">
               <td><font size=2 color="white"><center>Asociatie</center></font></td>
               <td><font size=2 color="white"><center>Scara</center></font></td>
               <td><font size=2 color="white"><center>Furnizor</center></font></td>
               <td><font size=2 color="white"><center>Datorie</center></font></td>               
               <td><font size=2 color="white"><center>Procent</center></font></td>               
               <td><font size=2 color="white"><center>Optiuni</center></font></td>               
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
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=w_scari_furnizori&asoc_id='.$asocId.'">';
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
                                echo '<td>'.$row['datorie'].'</td>';
                                echo '<td>'.$row['penalizare'].'</td>';                                
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=w_scari_furnizori&asoc_id='.$asocId.'&edit='.$row['sf_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['furnizor']."','".$row['sf_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

