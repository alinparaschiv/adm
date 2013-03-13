<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
</script>

<?php           
if ($_POST['buton'] == 'apasat' && $_POST['serviciu'] != '' && $_POST['nivel'] != 'nimic' && $_POST['modImpartire'] != ''  && $_POST['unitate'] != '' && $_POST['cuIndecsi'] != 'nimic' && $_POST['fonduri'] != 'nimic' && $_POST['servicii'] != 'nimic' && $_POST['lucrari'] != 'nimic') {
        $serviciu = mysql_real_escape_string($_POST['serviciu']);
        $nivel = mysql_real_escape_string($_POST['nivel']);
        
		$modImpartire = mysql_real_escape_string($_POST['modImpartire']);
		
		$localizare = mysql_real_escape_string($_POST['localizare']);
        $unitate = mysql_real_escape_string($_POST['unitate']);
		$cuIndecsi = mysql_real_escape_string($_POST['cuIndecsi']);

		$fonduri = mysql_real_escape_string($_POST['fonduri']);
		$servicii = mysql_real_escape_string($_POST['servicii']);
		$lucrari = mysql_real_escape_string($_POST['lucrari']);
		
        if($nivel == 'nimic') {
              $i=1;              
              $nivelErr='Alegeti nivelul serviciului.';
        }
        foreach ($nivelServiciiArr as $key=>$value) {
            if ($value == $nivel) {
				$nivel = $key;
			}
        }
        
        if ($i==0) {
                $query = "INSERT INTO servicii (`serviciu`, `nivel`, `mod_impartire`, `unitate`, `localizare`, `cu_indecsi`, `fonduri`, `servicii`, `lucrari`) VALUES ('$serviciu', '$nivel', '$modImpartire', '$unitate', '$localizare', '$cuIndecsi', '$fonduri', '$servicii', '$lucrari')";
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
<?php   if($_GET['add']=="true") { ?>
        <form id="addForm" name="addForm" method="post" action="index.php?link=servicii&add=true">
                <input type="hidden" name="buton" value="apasat" />
                <table style="margin:20px 0 0 0px; width:400px;" border=0>
                        
                        <?php if ($serviciuErr != '') echo '<tr><td></td><td><font color="red">'.$serviciuErr.'</font></td></tr>'; ?>                
                        <tr><td align="left" bgcolor="#CCCCCC" >Serviciu:</td><td align="left" bgcolor="#CCCCCC"><input type="text" name="serviciu" value="<?php echo $_POST['serviciu'];?>" /></td></tr>
                        <?php if ($nivelErr != '') echo '<tr><td></td><td><font color="red">'.$nivelErr.'</font></td></tr>'; ?>                
                        <tr><td align="left" bgcolor="#CCCCCC">Nivel Serviciu:</td>
                            <td align="left" bgcolor="#CCCCCC">
                                <select name="nivel">
                                        <?php 
                                            if ($_POST['nivel'] != '' && $_POST['nivel'] != 'nimic') {
                                                echo '<option value="'.$_POST['nivel'].'">'.$_POST['nivel'].'</option>';
                                            }
                                        ?>
                                        <option value="nimic">Alege Nivel</option>
                                        <option value="asociatie">asociatie</option>
                                        <option value="scara">scara</option>
                                        <option value="apartament">apartament</option>
                                        <option value="locatar">locatar</option>
                                </select>
                            </td>
                        </tr>                              
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Mod Impartire Valoare Factura:</td>
							<td align="left" bgcolor="#CCCCCC">
								<select name="modImpartire" />
									<?php
										for ($i=0; $i<count($modImpartireFacturiArr); $i++){
											echo '<option value="'.$i.'">'.$modImpartireFacturiArr[$i].'</option>';
										}
									?>
								</select>
							</td>
						</tr>                              
                        
						<?php if ($unitateErr != '') echo '<tr><td></td><td><font color="red">'.$unitateErr.'</font></td></tr>'; ?>
                        <tr>
			<td align="left" bgcolor="#CCCCCC">Cheltuieli de:</td>
			<td align="left" bgcolor="#CCCCCC">
				<select name="localizare">
				<?php 
                                            if ($_POST['localizare'] != '' && $_POST['localizare'] != 'nimic') {
                                                echo '<option value="'.$_POST['localizare'].'">'.$_POST['localizare'].'</option>';
                                            }
                                        ?>
					<option value="Nici un fel">Nici un fel</option>
					<option value="Alta natura">Alta natura</option>
					<option value="Beneficiari">Beneficiari</option>
					<option value="Persoana">Persoana</option>
					<option value="Cota parte indiviza">Cota parte indiviza</option>
				</select>

			</td>
			</tr>
			<tr><td align="left" bgcolor="#CCCCCC">Unit. Masura</td>
                            <td align="left" bgcolor="#CCCCCC">
                                    <select name="unitate">
                                        <?php
                                        echo '<option value="nimic">Alege unitatea</option>';
                                        foreach ($uMasuraArr as $key=>$val) {
                                            if ($_POST['unitate'] == $key && $_POST['unitate'] != '')
                                            echo '<option value="'.$key.'">'.$val.'</option>';
                                            echo '<option value="'.$key.'">'.$val.'</option>';
                                        }
                                        ?>                                        
                                    </select>                                    
                  </td>
                        </tr>  
		<tr>
		          <td align="left" bgcolor="#CCCCCC">Factura cu indecsi</td>
			  <td align="left" bgcolor="#CCCCCC">
				  <select name="cuIndecsi">
					<option value="nimic">Alege tipul de factura</option>
					<option value="da">Da</option>
					<option value="nu">Nu</option>
				  </select>
			  </td> 
		</tr>
		<tr>
			<td align="left" bgcolor="#CCCCCC">Fond</td>
			<td align="left" bgcolor="#CCCCCC">
				<select name="fonduri">
					<option value="nimic">Alege tipul de factura</option>
					<option value="da">Da</option>
					<option value="nu">Nu</option>
				</select>
			</td> 
		</tr>
        <tr>
			<td align="left" bgcolor="#CCCCCC">Serviciu</td>
			<td align="left" bgcolor="#CCCCCC">
				<select name="servicii">
					<option value="nimic">Este serviciu?</option>
					<option value="da">Da</option>
					<option value="nu">Nu</option>
				</select>
			</td> 
		</tr>
		
		<tr>
			<td align="left" bgcolor="#CCCCCC">Lucrari</td>
			<td align="left" bgcolor="#CCCCCC">
				<select name="lucrari">
					<option value="nimic">Este lucrare?</option>
					<option value="da">Da</option>
					<option value="nu">Nu</option>
				</select>
			</td> 
		</tr>
                       
					   <tr><td align="left" bgcolor="#CCCCCC">
                        Sa apara la Tarife?
                        </td>
                        <td align="left" bgcolor="#CCCCCC">
                        <input type="checkbox" name="ap_tarife" /></td>
                        </tr>                           
                        
                        <tr><td bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"><input type="submit" value="Salveaza" /></td></tr>
                        <?php if ($mesaj != '') echo '<tr><td></td><td align="left">'.$mesaj.'</td></tr>'; ?>
                </table>
        </form>
        <?php   } ?>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE SERVICII   ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi serviciul "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=servicii&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editSer = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
		$serId = mysql_real_escape_string($_POST['ser']);
		    
		$serviciu = mysql_real_escape_string($_POST['serviciuT']);
		$nivel = mysql_real_escape_string($_POST['nivelT']);
		
		$modImpartire = mysql_real_escape_string($_POST['modImpartireT']);
		
		$unitate = mysql_real_escape_string($_POST['unitateT']);
		$cuIndecsiT = mysql_real_escape_string($_POST['cuIndecsiT']);
		$localizareT = mysql_real_escape_string($_POST['localizareT']);
		$fonduriT = mysql_real_escape_string($_POST['fonduriT']);
		$serviciiT = mysql_real_escape_string($_POST['serviciiT']);
		$lucrariT = mysql_real_escape_string($_POST['lucrariT']);
            
        if($nivel == 'nimic') {              
            $nivelErr='Alegeti nivelul serviciului.';
        }
            
        foreach ($nivelServiciiArr as $key=>$value) {
            if ($value == $nivel) {
				$nivel = $key;
			}
        }
             
        if ($i==0) {
            $query = "UPDATE servicii SET `serviciu`= '$serviciu', `nivel`= '$nivel', `mod_impartire`='$modImpartire', `unitate`='$unitate', `localizare`='$localizareT', `cu_indecsi`='$cuIndecsiT', `fonduri`='$fonduriT', `servicii`='$serviciiT', `lucrari`='$lucrariT' WHERE serv_id='$serId'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 '.mysql_error());
            $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
            unset ($_POST);
        } else {
            $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }            
    }
    
    $sterge = mysql_real_escape_string($_GET['sterge']);
    if ($sterge != '' && $serviciu == '') {
            $query = "DELETE FROM servicii WHERE serv_id='$sterge'";
            mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
            $mesaj1 = '<font color="red">Serviciul a fost sters.</font>';
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
<table width="800" class="tablestyle">
	<tr bgcolor="#000" valign="middle">
		<td bgcolor="#000"><font size=2 color="white"><center>Serviciu</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Nivel</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Mod Impartire<br /> Valoare Factura</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>U. M.</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Cheltuieli de</center></font></td>
      	<td bgcolor="#000"><font size=2 color="white"><center>Cu Indecsi</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Fond</center></font></td>
        <td bgcolor="#000"><font size=2 color="white"><center>Serviciu</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Lucrari</center></font></td>
		<td bgcolor="#000"><font size=2 color="white"><center>Optiuni</center></font></td>
	</tr>
           <?php
              $query = "SELECT * FROM servicii ORDER BY `serv_id` DESC";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   //echo '<input type="hidden" name="furnizor'.$i.'" value="'.$row['furnizor'].'">';
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editSer == $row['serv_id']){
                                /*
                                foreach ($nivelServiciiArr as $key=>$value) {
                                    if ($value == $nivel) $nivel = $key;
                                } */
                                $nivelServ =  $nivelServiciiArr[$row['nivel']];
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=servicii">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="ser" value="'.$editSer.'" />';
                                    echo '<td><input style="width:120px;" type="text" name="serviciuT" value="'.$row['serviciu'].'"></td>';
				    $textAsociatie = $nivelServ == 'asociatie' ? ' selected="selected"' : '';
				    $textScara = $nivelServ == 'scara' ? ' selected="selected"' : '';
				    $textApartament = $nivelServ == 'apartament' ? ' selected="selected"' : '';
				    $textLocatar = $nivelServ == 'locatar' ? ' selected="selected"' : '';
                                    echo '<td>
                                                <select name="nivelT">
                                                        <option'.$textAsociatie.' value="asociatie">asociatie</option>
                                                        <option'.$textScara.' value="scara">scara</option>
                                                        <option'.$textApartament.' value="apartament">apartament</option>
                                                        <option'.$textLocatar.' value="locatar">locatar</option>
                                                </select>
                                          </td>';
									
									echo '<td>
											<select name="modImpartireT">'; 
											for ($i=0; $i<count($modImpartireFacturiArr); $i++){
												echo '<option value="'.$i.'" '.($i == $row['mod_impartire'] ? 'selected="selected"' : '').'>'.$modImpartireFacturiArr[$i].'</option>';
											}
									echo '</td>';
											
									echo '<td>';
                                           echo ' <select name="unitateT">';
                                                echo '<option value="'.$row['unitate'].'">'.$uMasuraArr[$row['unitate']].'</option>';
                                                foreach ($uMasuraArr as $key=>$val) {                                                    
                                                    echo '<option value="'.$key.'">'.$val.'</option>';                                                    
                                                }                             
                                            echo '</select>';                                    
                                    echo '</td>'; 
echo '<td>';
				echo ' <select name="localizareT">';
                                                echo '<option value="'.$row['localizare'].'">'.$row['localizare'].'</option>';
                                               echo '<option value="Nici un fel">Nici un fel</option>
					<option value="Alta natura">Alta natura</option>
					<option value="Beneficiari">Beneficiari</option>
					<option value="Persoana">Persoana</option>
					<option value="Cota parte indiviza">Cota parte indiviza</option>';                        
                                            echo '</select>'; 
				
				echo '</td>';      
				echo '
					<td> 
						<select name="cuIndecsiT">
								<option value="'.$row['cu_indecsi'].'">'.$row['cu_indecsi'].'</option>
								<option value="da">Da</option>
								<option value="nu">Nu</option>
					</td>';
				echo '
					<td> 
						<select name="fonduriT">
								<option value="'.$row['fonduri'].'">'.$row['fonduri'].'</option>
								<option value="da">Da</option>
								<option value="nu">Nu</option>
					</td>';
				echo '
					<td> 
						<select name="serviciiT">
								<option value="'.$row['servicii'].'">'.$row['servicii'].'</option>
								<option value="da">Da</option>
								<option value="nu">Nu</option>
					</td>';
					
				echo '
					<td> 
						<select name="lucrariT">
								<option value="'.$row['lucrari'].'">'.$row['lucrari'].'</option>
								<option value="da">Da</option>
								<option value="nu">Nu</option>
					</td>';
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                                $nivelServ =  $nivelServiciiArr[$row['nivel']];
                                echo '<td>'.$row['serviciu'].'</td>';
                                echo '<td>'.$nivelServ.'</td>';
                                echo '<td>'.$modImpartireFacturiArr[$row['mod_impartire']].'</td>';
                                echo '<td>'.$uMasuraArr[$row['unitate']].'</td>';
				
				echo '<td>';
				echo $row['localizare'];
				echo '</td>';

				echo '<td>'.$row['cu_indecsi'].'</td>';
				echo '<td>'.$row['fonduri'].'</td>';
				echo '<td>'.$row['servicii'].'</td>';
				echo '<td>'.$row['lucrari'].'</td>';

                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=servicii&edit='.$row['serv_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['serviciu']."','".$row['serv_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
           <tr>
           <td colspan="9">
           <a href="index.php?link=servicii&add=true">Adauga serviciu nou</a>
           </td>
           </tr>
    </table>          
</div></div> 

