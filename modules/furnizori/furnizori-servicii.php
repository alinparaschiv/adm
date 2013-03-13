<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php           
if ($_POST['buton'] == 'apasat' && $_POST['furnizor'] != 'nimic' && $_POST['serviciu'] != 'nimic'  ) {
        $furnizor = mysql_real_escape_string($_POST['furnizor']);
        $serviciu = mysql_real_escape_string($_POST['serviciu']);
        
        $query = "SELECT * FROM furnizori_servicii WHERE fur_id='$furnizor' AND serv_id='$serviciu'";
        $result = mysql_query($query) or die(mysql_error());
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $furnizorErr = 'Asocierea acestui furnizor cu acest serviciu exista deja!';
             $i = 1;                 
        }
                
        if ($i==0) {
                $query = "INSERT INTO furnizori_servicii (`fur_id`, `serv_id`) VALUES ('$furnizor', '$serviciu')";
                mysql_query($query) or die(mysql_error());
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
        <form id="addForm" name="addForm" method="post" action="<?php 'index.php?link=furnizori-servicii';?>">
                <input type="hidden" name="buton" value="apasat" />
                <table style="margin:20px 0 0 0px; width:250px;" border=0>
                        
                        <?php if ($furnizorErr != '') echo '<tr><td></td><td><font color="red">'.$furnizorErr.'</font></td></tr>'; ?>                
                        <tr><td>Frunizori:</td>
                            <td>
                                <select name="furnizor">
                                        <?php 
                                              $query = "SELECT * FROM furnizori ORDER BY `furnizor`";
                                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                                              echo '<option value="nimic">Alege furnizorul</option>';
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                  if ($_POST['furnizor'] == $row['fur_id'])
                                                        echo '<option selected="selected" value="'.$row['fur_id'].'">'.$row['furnizor'].'</option>';                                                  
                                                  echo '<option value="'.$row['fur_id'].'">'.$row['furnizor'].'</option>';                                                  
                                              }                                              
                                        ?>                        
                                </select>
                            </td>
                        </tr>
                        <tr><td>Servicii:</td>
                            <td>
                                <select name="serviciu">
                                        <?php 
                                              $query = "SELECT * FROM servicii ORDER BY `serviciu`";
                                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                                              echo '<option value="nimic">Alege serviciul</option>';
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                  if ($_POST['serviciu'] == $row['serv_id'])
                                                        echo '<option selected="selected" value="'.$row['serv_id'].'">'.$row['serviciu'].'</option>';                                                  
                                                  echo '<option value="'.$row['serv_id'].'">'.$row['serviciu'].'</option>';                                                  
                                              }                                              
                                        ?>                        
                                </select>
                            </td>
                        </tr>                        
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
        window.location = "index.php?link=furnizori-servicii&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '') {
        $query = "DELETE FROM furnizori_servicii WHERE fs_id='$sterge'";
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
    <table width=400>
           <tr bgcolor="#19AF62">
               <td><font size=2 color="white"><center>Furnizor</center></font></td>
               <td><font size=2 color="white"><center>Serviciu</center></font></td>
               <td><font size=2 color="white"><center>Optiuni</center></font></td>
           </tr>
           <?php
              $query = "SELECT FS.*, F.furnizor, S.serviciu FROM furnizori_servicii AS FS
                                        JOIN furnizori AS F ON FS.fur_id=F.fur_id              
                                        JOIN servicii AS S ON FS.serv_id=S.serv_id              
                                        ORDER BY `fs_id` DESC";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //echo '<input type="hidden" name="furnizor'.$i.'" value="'.$row['furnizor'].'">';
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                    echo '<td>'.$row['furnizor'].'</td>';
                    echo '<td>'.$row['serviciu'].'</td>';                                
                    echo '<td><center>                                                
                                    <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['furnizor']."','".$row['fs_id']."'".')" >[sterge]</a>
                          </center></td>';
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

