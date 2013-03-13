<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php           
if ($_POST['buton'] == 'apasat' && $_POST['pachet'] != ''  && $_POST['pret'] != '' ) {
        $pachet = mysql_real_escape_string($_POST['pachet']);
        $pret = mysql_real_escape_string($_POST['pret']);
        
        if ($i==0) {
                $query = "INSERT INTO pachete (`pachet`, `pret`) VALUES ('$pachet', '$pret')";
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
        <form id="addForm" name="addForm" method="post" action="<?php 'index.php?link=pachete';?>">
                <input type="hidden" name="buton" value="apasat" />
                <table width="237" border=0 style="margin:20px 0 0 0px;">                        
                        <tr><td width="79" bgcolor="#CCCCCC" >Pachet:</td><td width="148" bgcolor="#CCCCCC"><input type="text" name="pachet" value="<?php echo $_POST['pachet'];?>" /></td></tr>                        
                        <tr><td bgcolor="#CCCCCC">Pret:</td><td bgcolor="#CCCCCC"><input type="text" name="pret" value="<?php echo $_POST['pret'];?>" /></td></tr>                                                

                        <tr><td bgcolor="#CCCCCC"></td><td align="right" bgcolor="#CCCCCC"><input type="submit" name="salveaza" value="Salveaza" /></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE pachete   ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi pachetul "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=pachete&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editPachet = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $pachetId = mysql_real_escape_string($_POST['pac']);
            
            $pachetT = mysql_real_escape_string($_POST['pachetT']);
            $pretT = mysql_real_escape_string($_POST['pretT']);            
             
            if ($i==0) {
                    $query = "UPDATE pachete SET `pachet`= '$pachetT', `pret`= '$pretT' WHERE pachet_id='$pachetId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }            
    }
    
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $cont == '') {
            $query = "DELETE FROM pachete WHERE pachet_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Pachet a fost sters.</font>';
    }
    
?>

<div id="maincon">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$mesaj1.'</font>
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
    <table width=341>
           <tr bgcolor="#19AF62">
               <td width="126" bgcolor="#999999"><font size=2 color="white"><center>Pachet</center></font></td>
           <td width="106" bgcolor="#999999"><font size=2 color="white"><center>Pret</center></font></td>               
             <td width="93" bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>               
           </tr>
           <?php
              $query = "SELECT * FROM pachete ORDER BY `pachet`";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editPachet == $row['pachet_id']){
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=pachete">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="pac" value="'.$editPachet.'" />';                                    
                                    echo '<td><input style="width:120px;" type="text" name="pachetT" value="'.$row['pachet'].'"></td>';
                                    echo '<td><input style="width:120px;" type="text" name="pretT" value="'.$row['pret'].'"></td>';
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {                                
                                echo '<td>'.$row['pachet'].'</td>';                                
                                echo '<td>'.$row['pret'].'</td>';                                
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=pachete&edit='.$row['pachet_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['pachet']."','".$row['pachet_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

