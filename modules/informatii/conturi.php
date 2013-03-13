<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php           
if ($_POST['buton'] == 'apasat' && $_POST['cont'] != '' && $_POST['banca'] != ''  && $_POST['posesor'] != '' ) {
        $cont = mysql_real_escape_string($_POST['cont']);
		$cont2 = mysql_real_escape_string($_POST['cont2']);
		$cont3 = mysql_real_escape_string($_POST['cont3']);
		$cont4 = mysql_real_escape_string($_POST['cont4']);
		
        $banca = mysql_real_escape_string($_POST['banca']);
        $posesor = mysql_real_escape_string($_POST['posesor']);
                
      
        if ((strlen($cont) != 2 ) && (strlen($cont2) != 2) && (strlen($cont3) != 4) && (strlen($cont4) != 16)){
             $contErr = 'Contul nu este scris corect!';
             $i = 1;
        } else {
		$contfinal = $cont.$cont2.$cont3.$cont4;
		  $query = "SELECT * FROM conturi WHERE cont='$contfinal'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $contErr = 'Acest cont exista deja!';
             $i = 1;                 
        }
		}
		
        if ($i==0) {
				$contfinal = $cont.$cont2.$cont3.$cont4;
                $query = "INSERT INTO conturi (`cont`, `banca`, `posesor` ) VALUES ('$contfinal', '$banca', '$posesor')";
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
        <form id="addForm" name="addForm" method="post" action="<?php 'index.php?link=conturi';?>">
                <input type="hidden" name="buton" value="apasat" />
                <table width="394" border=0 style="margin:20px 0 0 0px; width:400px;">
                        
                        <?php if ($contErr != '') echo '<tr><td></td><td><font color="red">'.$contErr.'</font></td></tr>'; ?>                
                        <tr><td width="91" bgcolor="#CCCCCC" >Cont:</td><td width="299" bgcolor="#CCCCCC">
                        
                        <input name="cont" type="text" value="<?php echo $_POST['cont'];?>" size="2" maxlength="2" />
                        <input name="cont2" type="text" value="<?php echo $_POST['cont2'];?>" size="2" maxlength="2" />
                        <input name="cont3" type="text" value="<?php echo $_POST['cont3'];?>" size="4" maxlength="4" />
                        <input name="cont4" type="text" value="<?php echo $_POST['cont4'];?>" size="16" maxlength="16" />
                        
                        
                        
                  </td></tr>                        
                        <tr><td bgcolor="#CCCCCC">Banca:</td><td bgcolor="#CCCCCC"><input type="text" name="banca" value="<?php echo $_POST['banca'];?>"  style="width:99%;" /></td></tr>                                                
                        <tr><td bgcolor="#CCCCCC">Posesor:</td>
                            <td bgcolor="#CCCCCC">
                          <select name="posesor" class="full" style="width:100%;">
                                        <?php 
                                            if ($_POST['posesor'] != '' && $_POST['posesor'] != 'nimic') {
                                                echo '<option value="'.$_POST['posesor'].'">'.$_POST['posesor'].'</option>';
                                            }
                                        ?>
                                        <option value="nimic">Alege</option>
                                        <option value="asociatii">asociatii</option>
                                        <option value="urbica">urbica</option>                                        
                                </select>
                            </td>
                        </tr>                              

                        <tr><td bgcolor="#CCCCCC"></td><td align="right" bgcolor="#CCCCCC"><input type="submit" name="save" value="Salveaza" /></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE CONTURI   ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi contul "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=conturi&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editCont = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $contId = mysql_real_escape_string($_POST['con']);
            
            $contT = mysql_real_escape_string($_POST['contT']);
			 $contT2 = mysql_real_escape_string($_POST['contT2']);
			  $contT3 = mysql_real_escape_string($_POST['contT3']);
			   $contT4 = mysql_real_escape_string($_POST['contT4']);
			   
            $bancaT = mysql_real_escape_string($_POST['bancaT']);
            $posesorT = mysql_real_escape_string($_POST['posesorT']);
            
       
			 if ((strlen($contT) != 2 ) && (strlen($contT2) != 2) && (strlen($contT3) != 4) && (strlen($contT4) != 16)){
             $contErr = 'Contul nu este scris corect!';
             $i = 1;
        }
             
            if ($i==0) {
				$contTfinal = $contT.$contT2.$contT3.$contT4;
                    $query = "UPDATE conturi SET `cont`= '$contTfinal', `banca`= '$bancaT', `posesor`='$posesorT' WHERE cont_id='$contId'";
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }            
    }
    
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $cont == '') {
            $query = "DELETE FROM conturi WHERE cont_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Contul a fost sters.</font>';
    }
    
?>

<div id="maincon">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$errCod1.$contErrT.$gratieErr1.$mesaj1.'</font>
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
               <td bgcolor="#999999"><font size=2 color="white"><center>Cont</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Banca</center></font></td>
           <td bgcolor="#999999"><font size=2 color="white"><center>Posesor</center></font></td>               
             <td bgcolor="#999999"><font size=2 color="white"><center>Optiuni</center></font></td>               
           </tr>
           <?php
              $query = "SELECT * FROM conturi ORDER BY `cont_id` DESC";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editCont == $row['cont_id']){                                
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=conturi">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="con" value="'.$editCont.'" />';                                    
                                    echo '<td>
									
									<input style="width:20px;" type="text" name="contT" value="'.substr($row['cont'],0,2).'">
									<input style="width:20px;" type="text" name="contT2" value="'.substr($row['cont'],2,2).'">
									<input style="width:38px;" type="text" name="contT3" value="'.substr($row['cont'],4,4).'">
									<input style="width:120px;" type="text" name="contT4" value="'.substr($row['cont'],8,16).'">
									
									</td>';
                                    echo '<td><input style="width:120px;" type="text" name="bancaT" value="'.$row['banca'].'"></td>';
                                    echo '<td>
                                                <select name="posesorT">
                                                        <option value="'.$row['posesor'].'">'.$row['posesor'].'</option>
                                                        <option value="asociatii">asociatii</option>
                                                        <option value="urbica">urbica</option>
                                                </select>
                                          </td>';
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {                                
                                echo '<td>'.$row['cont'].'</td>';                                
                                echo '<td>'.$row['banca'].'</td>';
                                echo '<td>'.$row['posesor'].'</td>';
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=conturi&edit='.$row['cont_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['cont']."','".$row['cont_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

