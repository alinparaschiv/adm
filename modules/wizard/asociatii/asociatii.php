<script language="javascript"> 
    function functionSubmit() {
        document.addForm.submit();        
    }        
    var strArr=new Array();
		
</script>
<?php
$asocId = mysql_real_escape_string($_GET['asoc_id']);
if ($_GET['edit'] != '') {
    $asocId = mysql_real_escape_string($_GET['edit']);
}
if ($_GET['asoc_id'] != '') {
    $asocId = mysql_real_escape_string($_GET['asoc_id']);
}

if ($_POST['buton'] == 'apasat' && $_POST['asociatie'] != '' && ($_POST['strada'] != 'nimic' || $_POST['strada1'] != '')  && $_POST['nr'] != '' && $_POST['scara'] != ''   && $_POST['bloc'] != ''   && $_POST['oras'] != ''  && $_POST['cod_fiscal'] != '' && $_POST['presedinte'] != '' && $_POST['cont_bancar'] != 'nimic' && $_POST['administrator'] != '') {

        $asociatie = mysql_real_escape_string($_POST['asociatie']);
        $strada = mysql_real_escape_string($_POST['strada']);
        $strada1 = mysql_real_escape_string($_POST['strada1']);
        if ($strada1 != '') {
            $query = "SELECT str_id FROM strazi WHERE strada='$strada1'";
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
                 $strada = $row['str_id'];
            }
            if($strada == 'nimic') {
                  $i=1;
                  $stradaErr = 'Strada pe care ati introdus-o este inexistenta.<br /> Pentru a putea alege aceasta strada mergeti la "Informatii->strazi<br /> si inregistrati aceasta scara."';
            }
        }
        $nr = mysql_real_escape_string($_POST['nr']);
        $scara = mysql_real_escape_string($_POST['scara']);
        $bloc = mysql_real_escape_string($_POST['bloc']);
        $oras = mysql_real_escape_string($_POST['oras']);
		
		$adresa_casierie = mysql_real_escape_string($_POST['adresa_casierie']);
		$orar_casierie = mysql_real_escape_string($_POST['orar_casierie']);
		$administrator = mysql_real_escape_string($_POST['administrator']);
        
		$cod_fiscal = mysql_real_escape_string($_POST['cod_fiscal']);
        $presedinte = mysql_real_escape_string($_POST['presedinte']);
        $cont_bancar = mysql_real_escape_string($_POST['cont_bancar']);
        $o_scara = mysql_real_escape_string($_POST['o_scara']);
        
        if ($o_scara == 'on') {
            $o_scaraVal = 1;
        } else {
            $o_scaraVal = 0;
        }
        $query = "SELECT asociatie FROM asociatii WHERE asociatie='$asociatie'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $asociatieErr = 'Aceasta asociatie exista deja!';
             $i = 1;                 
        }
        
        if ($i==0) {
                $query = "INSERT INTO asociatii (`asociatie`, `str_id`, `nr`, `scara`, `bloc`, `oras`, `presedinte`, `cod_fiscal`, `cont_id`, `o_scara`, `adresa_casierie`, `orar_casierie`, `administrator_id`) VALUES 
                                                ('$asociatie', '$strada', '$nr', '$scara', '$bloc', '$oras', '$presedinte', '$cod_fiscal', '$cont_bancar', '$o_scaraVal', '$adresa_casierie', '$orar_casierie', '$administrator')";                
                
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');
                $_SESSION['asoc_id'] = mysql_insert_id();
                $asocId = mysql_insert_id();
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);   
				
				
                // trece la pasul urmator                
/*                if ($link=='wizard') echo '<script language="javascript">window.location.href = "index.php?link=w_asoc_dat&asoc_id='.$asoc_id.'"'</script>';                
*/        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        } 
        
} else if ($_POST['buton'] == 'apasat') {
	if ($_POST['asociatie'] == ''){
		$asociatieErr = "Campul nu poate fi liber";
	}
	if ($_POST['strada'] == 'nimic' && $_POST['strada1'] == ''){
		$stradaErr = "Campul nu poate fi liber";
	}
	if ($_POST['nr'] == ''){
		$nrErr = "Campul nu poate fi liber";
	}
	if ($_POST['scara'] == ''){
		$scaraErr = "Campul nu poate fi liber";
	}
	if ($_POST['bloc'] == ''){
		$blocErr = "Campul nu poate fi liber";
	}
	if ($_POST['presedinte'] == ''){
		$presedinteErr = "Campul nu poate fi liber";
	}
	if ($_POST['cod_fiscal'] == ''){
		$codFiscalErr = "Campul nu poate fi liber";
	}
	if ($_POST['cont_bancar'] == 'nimic'){
		$codBancarErr = "Campul nu poate fi liber";
	}
	//$mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" align="left">
	<div id="maincon" >

<form id="addForm" name="addForm" method="post" action="index.php?link=wizard">
    <!-- Tabel introducere asociatie -->
	<input type="hidden" name="buton" value="apasat" />
    <table cellspacing=5 style="margin:20px 0 0 0px; width:500px;" border=0>                        
                
    <?php //if ($asociatieErr != '') echo '<tr><td></td><td><font color="red">'.$asociatieErr.'</font></td></tr>'; ?>
		<tr>
			<td align="left" bgcolor="#CCCCCC">Asociatie:</td>
			<td align="left" bgcolor="#CCCCCC">
				<input id="asociatie" type="text" name="asociatie" value="<?php echo $_POST['asociatie'];?>" />
			</td>
			<td align="left">
				<?php if ($asociatieErr != '') echo '<font color="red">'.$asociatieErr.'</font>'; ?>
			</td>
		</tr>                        
                        
		<tr>
			<td align="left" bgcolor="#CCCCCC"></td>
			<td align="left" bgcolor="#CCCCCC"><u>Adresa Asociatie</u></td>
			<td align="left"></td>
		</tr>
                    
		<tr>
			<td align="left" bgcolor="#CCCCCC">Strada:</td>
            <td align="left" bgcolor="#CCCCCC">
				<select name="strada">
                    <?php 
						$nr = 0;  
						$query = "SELECT * FROM strazi ORDER BY `strada`";
                        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                                        
						echo '<option value="nimic">Alege strada</option>';
                        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                            if ($_POST['strada'] == $row['str_id'])
                                echo '<option selected="selected" value="'.$row['str_id'].'">'.$row['strada'].'</option>';                                                  
                                echo '<option value="'.$row['str_id'].'">'.$row['strada'].'</option>';
                                echo "<script> strArr[".$nr."] = '".$row['strada']."'; </script>";
                                $nr ++;
						}
                                                                                 
                    ?>                        
                </select> sau
            </td>
			<td align="left" ></td>
        </tr>
                        
						<tr><td align="left" bgcolor="#CCCCCC">Strada:</td>
                            <td align="left" bgcolor="#CCCCCC">
                          <input type='text' name="strada1" style='font-family:verdana;width:150px;font-size:12px' id='tb' value="<?php echo $_POST['strada1']; ?>"/> 
                                        <script>
                                        var obj = actb(document.getElementById('tb'),strArr);
                                        //setTimeout(function(){obj.actb_keywords = custom2;},10000);
                                    </script>
                            </td>
							<td align="left" >
								<?php if ($stradaErr != '') echo '<font color="red">'.$stradaErr.'</font>'; ?>                
							</td>
                        </tr>                        
                        
                        <tr>
							<td align="left" bgcolor="#CCCCCC">Nr:</td>
							<td align="left" bgcolor="#CCCCCC"><input id="nr" type="text" name="nr" value="<?php echo $_POST['nr'];?>" /></td>
							<td align="left" >
								<?php if ($nrErr != '') echo '<font color="red">'.$nrErr.'</font>'; ?>      
                        	</td>
						</tr>
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Scara:</td>
							<td align="left" bgcolor="#CCCCCC"><input id="scara" type="text" name="scara" value="<?php echo $_POST['scara'];?>" /></td>
							<td align="left" >
								<?php if ($scaraErr != '') echo '<font color="red">'.$scaraErr.'</font>'; ?>
							</td>
						</tr>                                                
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Bloc:</td>
							<td align="left" bgcolor="#CCCCCC"><input id="bloc" type="text" name="bloc" value="<?php echo $_POST['bloc'];?>" /></td>
							<td align="left" >
								<?php if ($blocErr != '') echo '<font color="red">'.$blocErr.'</font>'; ?>
							</td>
						</tr>
                        
						<tr><td align="left" bgcolor="#CCCCCC">Oras:</td>
                            <td align="left" bgcolor="#CCCCCC">
                                <select name="oras">
                                <?php 
                                    if ($_POST['oras'] != '')
                                    echo '<option value="'.$_POST['oras'].'">'.$_POST['oras'].'</option>';
                                    foreach ( $oraseArr as $a ) {
                                          echo '<option value="'.$a.'">'.$a.'</option>';
                                    }
                                ?>
                                </select>
                            </td>
							<td align="left" ></td>
                        </tr>
                       
						<?php
                        if ($_POST['o_scara'] == 'on') {
                            echo '<tr><td style="text-align:right;"><input type="checkbox" checked="on" name="o_scara"></td><td>Bifati daca asociatia are o singura scara</td></tr>';
                        } else {
                            echo '<tr><td style="text-align:right;"><input type="checkbox" name="o_scara"></td><td>Bifati daca asociatia are o singura scara</td></tr>';
                        }
                        ?>
                        
                        <tr>
							<td align="left" bgcolor="#CCCCCC"></td>
							<td align="left" bgcolor="#CCCCCC"><u>Informatii Casierie</u></td>
							<td align="left" ></td>
						</tr>
                        
                        <tr>
                        	<td align="left" bgcolor="#CCCCCC">Adresa Casierie:</td>
                            <td align="left" bgcolor="#CCCCCC">
                            	<input type="text" name="adresa_casierie" value="<?php echo $_POST['adresa_casierie'];?>" />
                            </td>
							<td align="left" ></td>
                        </tr>
                        
                        <tr>
                        	<td align="left" bgcolor="#CCCCCC">Orar Casierie:</td>
                            <td align="left" bgcolor="#CCCCCC">
                            	<input type="text" name="orar_casierie" value="<?php echo $_POST['orar_casierie'];?>" />
                            </td>
							<td align="left" ></td>
                        </tr>
                        
                        <tr>
                        	<td align="left" bgcolor="#CCCCCC">Administrator</td>
                            <td align="left" bgcolor="#CCCCCC">
                            	
                               	<?php 
									echo '<select name="administrator">';
									$q = "SELECT * FROM admin WHERE user_id=3";
									$res = mysql_query($q) or die ("Nu pot afisa administratorii<br />".mysql_error());
									
									while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
                                              /*if ($row['administrator_id'] == $r['id']) {
                                                    echo '<option selected="selected" value="'.$r['id'].'">'.$r['nume'].'</option>';
                                              }*/
                                              echo '<option value="'.$r['id'].'">'.$r['nume'].'</option>';
                                          }
									echo '</select>';
                                ?>
                            </td>
							<td align="left" ></td>
                        </tr>
                        
                        <tr>
							<td align="left" bgcolor="#CCCCCC"></td>
							<td align="left" bgcolor="#CCCCCC"><u>Alte informatii</u></td>
							<td align="left" ></td>
						</tr>
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Nume Presedinte:</td>
							<td align="left" bgcolor="#CCCCCC">
								<input id="presedinte" type="text" name="presedinte" value="<?php echo $_POST['presedinte'];?>" />(nume)
							</td>
							<td align="left" >
								<?php if ($presedinteErr != '') echo '<font color="red">'.$presedinteErr.'</font>'; ?>
							</td>
						</tr>
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Cod Fiscal:</td>
							<td align="left" bgcolor="#CCCCCC">
								<input id="cod_fiscal" type="text" name="cod_fiscal" value="<?php echo $_POST['cod_fiscal'];?>" />
							</td>
							<td align="left" >
								<?php if ($codFiscalErr != '') echo '<font color="red">'.$codFiscalErr.'</font>'; ?>
							</td>
						</tr>                        
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC">Cont Bancar:</td>
                            <td align="left" bgcolor="#CCCCCC">
							<select name="cont_bancar">
								<?php
									if ($_POST['cont_bancar'] == 'nimic' || $_POST['cont_bancar'] == '') { ?>
										<option value="nimic">Alege Contul</option>
									<?php } else {
										$q = "SELECT * FROM conturi WHERE cont_id=".$_POST['cont_bancar'];
										$q = mysql_query($q) or die ("Nu pot afisa contul selectat<br />".mysql_error());
										
										echo '<option value="'.mysql_result($q, 0, 'cont_id').'">'.mysql_result($q, 0, 'cont').'</option>';
									} ?>
									<?php    
										if ($_POST['cont_bancar'] == 'nimic' || $_POST['cont_bancar'] == '') {
											$q = "SELECT * FROM conturi";
										} else {
											$q = "SELECT * FROM conturi WHERE cont_id<>".$_POST['cont_bancar'];
										}
										$res = mysql_query($q) or die ("Nu pot afisa conturile<br />".mysql_error());
										
										while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
											echo '<option value="'.$r['cont_id'].'">'.$r['cont'].'</option>';
										}
									?>                        
                                </select>
                            </td>
							<td align="left" >
								<?php if ($codBancarErr != '') echo '<font color="red">'.$codBancarErr.'</font>'; ?>
							</td>
                        </tr>
                        
                        <tr>
							<td align="left" bgcolor="#CCCCCC"></td>
							<td align="left" bgcolor="#CCCCCC">
								<input type="submit" name="Salveaza" value="Salveaza" />
							</td>
							<td align="left" ></td>
						</tr>
                        
						<tr>
							<td align="left" bgcolor="#CCCCCC"></td>
							<td align="left" bgcolor="#CCCCCC"></td>
							<td align="left" ></td>
						</tr>
                        
						<tr><td align="left" bgcolor="#CCCCCC"></td><td align="left" bgcolor="#CCCCCC"></td></tr>
                        <?php 
                        if ($asocId != '') {
                            ?>
                            <tr>
								<td align="left" bgcolor="#CCCCCC"></td>
								<td align="left" bgcolor="#CCCCCC"><div id="buton"><a href="index.php?link=w_asoc_dat&asoc_id=<?php echo isset($_SESSION['asoc_id']) ? $_SESSION['asoc_id'] : $_GET['asoc_id']; ?>" style="">Pasul Urmator</a></div></td>
								<td align="left" ></td>
							</tr>                        
                            <?php
                        }
                        ?>
                </table>
</form> 
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE ASOCIATII   ****************************************/ ?>



<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi asociatia "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=wizard&sterge="+fur_id;
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
			$administratorT = mysql_real_escape_string($_POST['administratorT']);
			
            $o_scaraT = mysql_real_escape_string($_POST['o_scaraT']);                        
            if ($o_scaraT == 'on') { $o_scaraVal = 1; } else { $o_scaraVal = 0; }                         
            if ($i==0) {
                    $query = "UPDATE asociatii SET `asociatie`= '$asociatieT', `str_id`= '$stradaT', `nr`='$nrT', `scara`='$scaraT', 
                                                    `bloc`='$blocT', `cod_fiscal`='$cod_fiscalT', `presedinte`='$presedinteT', 
                                                    `cont_id`='$cont_bancarT', `o_scara`='$o_scaraVal', `adresa_casierie`='$adresa_casierieT', `orar_casierie`='$orar_casierieT', `administrator_id`='$administratorT'
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
		if ($asocId != '' ) {
?>                             
<table width=950>
       <tr bgcolor="#19AF62">
   <td ><font size=2 color="white"><center>Asociatie</center></font></td>
   <td ><font size=2 color="white"><center>Strada</center></font></td>
   <td ><font size=2 color="white"><center>Nr</center></font></td>               
   <td ><font size=2 color="white"><center>Scara</center></font></td>               
   <td ><font size=2 color="white"><center>Bloc</center></font></td>               
   <td ><font size=2 color="white"><center>Oras</center></font></td>                              
   <td ><font size=2 color="white"><center>Presedinte</center></font></td>               
   <td ><font size=2 color="white"><center>Cod Fiscal</center></font></td>               
   <td ><font size=2 color="white"><center>Cont Bancar</center></font></td>               
   <td ><font size=2 color="white"><center>O Scara</center></font></td>    
     
   <td ><font size=2 color="white"><center>Adresa Casierie</center></font></td>      
   <td ><font size=2 color="white"><center>Orar Casierie</center></font></td>      
   <td ><font size=2 color="white"><center>Administrator</center></font></td>   
   
   <td ><font size=2 color="white"><center>Optiuni</center></font></td>               
       </tr>
      <?php      
                             
        $query = "SELECT A.*, U.nume, S.strada, C.cont FROM asociatii AS A JOIN strazi AS S ON A.str_id=S.str_id JOIN conturi AS C ON A.cont_id=C.cont_id JOIN admin AS U ON A.administrator_id=U.id WHERE A.asoc_id = '$asocId' ORDER BY A.asociatie";
     	$result = mysql_query($query) or die(mysql_error());
      $i=0;
      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
           //schimb culoarea la coloane
           if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                if ($editAsoc == $row['asoc_id']){                                
                        echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=wizard&asoc_id='.$asocId.'">';
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
							echo '<td>';
								echo '<select name="administratorT">';
									$q = "SELECT * FROM admin WHERE user_id=3";
									$res = mysql_query($q) or die ("Nu pot afisa administratorii<br />".mysql_error());
									
									while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
                                              /*if ($row['administrator_id'] == $r['id']) {
                                                    echo '<option selected="selected" value="'.$r['id'].'">'.$r['nume'].'</option>';
                                              }*/
                                              echo '<option value="'.$r['id'].'">'.$r['nume'].'</option>';
                                          }
								echo '</select>';
							echo '</td>';
							
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
						echo '<td>'.$row['nume'].'</td>';
						
                        echo '<td><center>
                                        <a style="font-size:12px;" href="index.php?link=wizard&edit='.$row['asoc_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                        <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['asociatie']."','".$row['asoc_id']."'".')" >[sterge]</a>
                              </center></td>';
                }
           echo '</tr>';
           $i++;
      }
		}
?>
 </table>    
        
                          
</div></div> 

