<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php           
if ($_POST['buton'] == 'apasat' && $_POST['strada'] != '') {
        $strada = mysql_real_escape_string($_POST['strada']);      
        
        $query = "SELECT * FROM strazi WHERE strada='$strada'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $stradaErr = 'Aceasta strada exista deja!<br />';
             $i = 1;                 
        }   
        
        if ($i==0) {
                $query = "INSERT INTO strazi (`strada`) VALUES ('$strada')";
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }
        
}
?>

<div id="mainCol" class="clearfix" ><div id="maincon" >
        <form id="addForm" name="addForm" method="post" action="<?php 'index.php?link=strazi';?>">
                <input type="hidden" name="buton" value="apasat" />
                <table style="margin:20px 0 0 0px; width:400px;" border=0>
                        
                        <?php if ($stradaErr != '') echo '<tr><td></td><td><font color="red">'.$stradaErr.'</font></td></tr>'; ?>                
                        <tr><td width="88" bgcolor="#CCCCCC" >Strada:</td><td width="302" bgcolor="#CCCCCC"><input type="text" name="strada" value="<?php echo $_POST['strada'];?>" />
                          <input type="submit" value="Salveaza" /></td></tr>                     
                        
                        
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE --------- STRAZI  ---------------- ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi strada "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=strazi&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editStr = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat' && $_POST['stradaT'] != '') {
            $strId = mysql_real_escape_string($_POST['str']);
            $strada = mysql_real_escape_string($_POST['stradaT']);            
                  
            if ($i==0) {
                    $query = "UPDATE strazi SET `strada`= '$strada' WHERE str_id='$strId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
            
    }
    
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $strada== '') {
            $query = "DELETE FROM strazi WHERE str_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Strada a fost stearsa.</font>';
    }
    
?>

<div id="maincon">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$stradaErr.$mesaj1.'</font>
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
    <table width=300>
           <tr bgcolor="#19AF62">
               <td width="166" bgcolor="#999999"><font size=2 color="white"><center>Strada</center></font></td>
             <td width="122" bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>
           </tr>
           <?php
              $query = "SELECT * FROM strazi ORDER BY `strada`";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ***');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //echo '<input type="hidden" name="furnizor'.$i.'" value="'.$row['furnizor'].'">';
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=left>'; else echo '<tr align=left bgcolor="#f1f2f2">';
                        if ($editStr == $row['str_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=strazi">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="str" value="'.$editStr.'" />';
                                    echo '<td><input style="width:120px;" type="text" name="stradaT" value="'.$row['strada'].'"></td>';                                    
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                                echo '<td>'.$row['strada'].'</td>';                                
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=strazi&edit='.$row['str_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['strada']."','".$row['str_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

