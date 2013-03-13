<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }
        function functionSubmit1() {
             document.addForm1.submit();        
        }                
</script>

<?php

//$asocId = $_GET['asoc_id'];
if ($_GET['edit'] != '') $asocId = mysql_real_escape_string($_GET['edit']);
if ($_GET['sterge'] != '') $asocId = mysql_real_escape_string($_GET['sterge']);


$asocId = mysql_real_escape_string($_POST['asociatia']);
if ($_GET['edit'] != '') {
    $asocId = mysql_real_escape_string($_GET['edit']);
}
if ($_GET['asoc_id'] != '') {
    $asocId = mysql_real_escape_string($_GET['asoc_id']);
}



if ($_POST['buton'] == 'apasat' && $_POST['asociatie'] != 'nimic' && $_POST['reparatii'] != '' && $_POST['special'] != '' && $_POST['rulment'] != '' && $_POST['penalizari'] != '') {
        $i = 0;
        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        $reparatii = mysql_real_escape_string($_POST['reparatii']);
        $special = mysql_real_escape_string($_POST['special']);
        $rulment = mysql_real_escape_string($_POST['rulment']);
        $penalizari = mysql_real_escape_string($_POST['penalizari']);
        
                
        $query = "SELECT * FROM asociatii_fonduri WHERE asoc_id='$asocId'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $asociatieErr = 'Fonduri pentru aceasta asociatie au fost deja introduse!';
             $i = 1;                 
        }        
        if(ereg('[^0-9.]', $reparatii)) {
              $i=1;
              $errReparatii='Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9.]', $special)) {
              $i=1;
              $errSpecial='Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9.]', $rulment)) {
              $i=1;
              $errRulment='Acest camp poate sa contina doar cifre.';
        }
        if(ereg('[^0-9.]', $penalizari)) {
              $i=1;
              $errPenalizari='Acest camp poate sa contina doar cifre.';
        }
        if ($i==0) {
                $query = "INSERT INTO asociatii_fonduri (`asoc_id`, `reparatii`, `special`, `rulment`, `penalizari`) VALUES ('$asocId', '$reparatii', '$special', '$rulment', '$penalizari')";                                
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');
                //$asoc_id = mysql_insert_id();
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);                
                // trece la pasul urmator                
                //if ($link=='w_asoc_fonduri') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_setari&asoc_id='.$asocId.'"</script>';                
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
        <form id="addForm1" name="addForm1" method="post" action="index.php?link=asoc_fonduri">
                <input type="hidden" name="search" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>                        
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
                            <td bgcolor="#CCCCCC"><input type="submit" name="Selecteaza" value="Selecteaza" id="Selecteaza" /></td>
                        </tr>                        
                </table>
        </form>
    </div>
  


<div id="maincon" >
<?php
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=asoc_fonduri&asoc_id='.$asocId.'">';
?>        
                <input type="hidden" name="buton" value="apasat" />                
                <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>                                               
                        <?php if ($asociatieErr != '') echo '<tr><td></td><td><font color="red">'.$asociatieErr.'</font></td></tr>'; ?>
                        <tr><td bgcolor="#CCCCCC">Asociatie:</td>
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
                        <?php if ($errReparatii != '') echo '<tr><td></td><td><font color="red">'.$errReparatii.'</font></td></tr>'; ?>
                        <tr><td bgcolor="#CCCCCC">Fond Reparatii:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="reparatii" value="<?php echo $_POST['reparatii'];?>" /></td></tr>                        
                        <?php if ($errSpecial != '') echo '<tr><td></td><td><font color="red">'.$errSpecial.'</font></td></tr>'; ?>                                                       
                        <tr><td bgcolor="#CCCCCC">Fond Special:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="special" value="<?php echo $_POST['special'];?>" /></td></tr>                        
                        <?php if ($errRulment != '') echo '<tr><td></td><td><font color="red">'.$errRulment.'</font></td></tr>'; ?>                                                       
                        <tr><td bgcolor="#CCCCCC">Fond Rulment:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="rulment" value="<?php echo $_POST['rulment'];?>" /></td></tr>                        
                        <?php if ($errPenalizari != '') echo '<tr><td></td><td><font color="red">'.$errPenalizari.'</font></td></tr>'; ?>                                                       
                        <tr><td bgcolor="#CCCCCC">Fond Penalizari:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="penalizari" value="<?php echo $_POST['penalizari'];?>" /></td></tr>                        
                       
                        <tr><td bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"><input type="submit" name="Salveaza" value="Salveaza" /></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE DATORII ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=asoc_fonduri&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editAf = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $afId = mysql_real_escape_string($_POST['af']);
            
            $reparatiiT = mysql_real_escape_string($_POST['reparatiiT']);
            $specialT = mysql_real_escape_string($_POST['specialT']);
            $rulmentT = mysql_real_escape_string($_POST['rulmentT']);
            $penalizariT = mysql_real_escape_string($_POST['penalizariT']);
            
            if(ereg('[^0-9]', $reparatiiT)) {
                  $i=1;
                  $errReparatii1='<br>(Reparatii)Acest camp poate sa contina doar cifre.';
            }
            if(ereg('[^0-9]', $specialT)) {
                  $i=1;
                  $errSpecial1='<br>(Special)Acest camp poate sa contina doar cifre.';
            }
            if(ereg('[^0-9]', $rulmentT)) {
                  $i=1;
                  $errRulment1='<br>(Rulment)Acest camp poate sa contina doar cifre.';
            }
            if(ereg('[^0-9]', $penalizariT)) {
                  $i=1;
                  $errPenalizari1='<br>(Penalizari)Acest camp poate sa contina doar cifre.';
            }
            
            if ($i==0) {
                    $query = "UPDATE asociatii_fonduri SET `reparatii`='$reparatiiT',`special`='$specialT',`rulment`='$rulmentT',`penalizari`='$penalizariT' WHERE af_id='$afId'";                    
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }            
    }
    
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $asociatie == '') {
            $query = "DELETE FROM asociatii_fonduri WHERE af_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Detaliile au fost sterse.</font>';
    }    
?>
<div id="maincon" style="width:625px;">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$errReparatii1.$errRulment1.$errPenalizari1.$errSpecial1.'</font>
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
               <td bgcolor="#999999"><font size=2 color="white"><center>Asociatie</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Reparatii</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Special</center></font></td>               
           <td bgcolor="#999999"><font size=2 color="white"><center>Rulment</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Penalizari</center></font></td>               
             <td bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>               
           </tr>
           <?php
              $query = "SELECT AF.*, A.asociatie FROM asociatii_fonduri AS AF 
                                                                        JOIN asociatii AS A ON AF.asoc_id=A.asoc_id 
                             WHERE AF.asoc_id='$asocId'                                               
                             ORDER BY AF.af_id";                                                                     
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editAf == $row['af_id']){                                
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=asoc_fonduri&asoc_id='.$asocId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="af" value="'.$editAf.'" />';                                                                        
                                    echo '<td>'.$row['asociatie'].'</td>';                                    
                                    echo '<td><input style="width:60px;" type="text" name="reparatiiT" value="'.$row['reparatii'].'"></td>';                                                                      
                                    echo '<td><input style="width:60px;" type="text" name="specialT" value="'.$row['special'].'"></td>';                                                                      
                                    echo '<td><input style="width:60px;" type="text" name="rulmentT" value="'.$row['rulment'].'"></td>';                                                                      
                                    echo '<td><input style="width:60px;" type="text" name="penalizariT" value="'.$row['penalizari'].'"></td>';                                                                      
                                    
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {                                
                                echo '<td>'.$row['asociatie'].'</td>';
                                echo '<td>'.$row['reparatii'].'</td>';
                                echo '<td>'.$row['special'].'</td>';
                                echo '<td>'.$row['rulment'].'</td>';
                                echo '<td>'.$row['penalizari'].'</td>';                                
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=asoc_fonduri&asoc_id='.$asocId.'&edit='.$row['af_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['asociatie']."','".$row['af_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

