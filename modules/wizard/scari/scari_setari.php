<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }        
        function hideShow(x){
            el=document.getElementById("centrala")            
            if(x=="show"){
				el.style.display="block"            
            }
            if(x=="hide"){
				el.style.display="none"            
            }
        }
        function hideShow1(x){
            el1=document.getElementById("contor")            
            if(x=="show"){
				el1.style.display="block"            
            }
            if(x=="hide"){
				el1.style.display="none"            
            }
        }        
        function hideShow2(x){
            el3=document.getElementById("div_lift")     
			el5=document.getElementById("span_lift");
            if(x=="show"){
				el3.style.display="none"     
				el5.style.display="none"
            }
            if(x=="hide"){
				el3.style.display="block"
				el5.style.display="block"
            }
        }        
        function hideShow3(x){
            el2=document.getElementById("div_lift1")            
            if(x=="show"){
				el2.style.display="block"            
            }
            if(x=="hide"){
				el2.style.display="none"            
            }
        }
</script>
<?php
$asociatie = mysql_real_escape_string($_POST['asociatie']);

$sterge = mysql_real_escape_string($_GET['sterge']);
if ($sterge != '' && $asociatie == '') {
        $query = "DELETE FROM scari_setari WHERE ss_id='$sterge'";
        mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 ');
        $mesaj1 = '<font color="red">Informatiile initiale pentru asociatie au fost sterse.</font>';
}


$asocId = $_GET['asoc_id'];

	if ($_POST['buton'] == 'apasat' && $_POST['etaje'] != '' && $_POST['apartamente'] != '' && $_POST['proc_incalzire'] != '') {
        
        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        $scara = mysql_real_escape_string($_POST['scara']);
        $gaz_calda = mysql_real_escape_string($_POST['gaz_calda']);
        $contor_centrala = mysql_real_escape_string($_POST['contor_centrala']);
        $ag_termic = mysql_real_escape_string($_POST['ag_termic']);
        $ag_termic_calda = mysql_real_escape_string($_POST['ag_termic_calda']);
        $ag_termic_incalzire = mysql_real_escape_string($_POST['ag_termic_incalzire']);
		
		$areLift = mysql_real_escape_string($_POST['liftSelect1']);
        
		$lift = mysql_real_escape_string($_POST['lift']);
        $etaje = mysql_real_escape_string($_POST['etaje']);
        $apartamente = mysql_real_escape_string($_POST['apartamente']);
        $parter = mysql_real_escape_string($_POST['parter']);
        $proc_incalzire = mysql_real_escape_string($_POST['proc_incalzire']);
        $pasant = mysql_real_escape_string($_POST['pasant']);
        
        if ( $gaz_calda != '' && ereg('[^0-9.]', $gaz_calda)) {
                $gaz_caldaErr = 'Campul "Mc Gaz Apa calda" poate sa contina doar cifre.';  
                $i=1;
        }
        
        if ( $ag_termic != '') {               
                if(ereg('[^0-9.]', $ag_termic)) {
                      $i=1;
                      $ag_termicErr = 'Campul "Agent Termic" poate sa contina doar cifre.';
                }
                
				if($ag_termic > 100){
                  $i=1;
                  $ag_termicErr = 'Campul "Agent Termic" nu poate sa depaseasca 100%.';
                }        
        }
        
		if ( $ag_termic_calda != '') {
            if(ereg('[^0-9.]', $ag_termic_calda)) {
                  $i=1;
                  $ag_termic_caldaErr = 'Campul "Agent Termic Calda" poate sa contina doar cifre.';
            }                    
            
			if($ag_termic_calda > 100){
                  $i=1;
                  $ag_termic_caldaErr = 'Campul "Agent Termic Calda" nu poate sa depaseasca 100%.';
            }        
        }
        
		if ( $ag_termic_incalzire != '') {
            if(ereg('[^0-9.]', $ag_termic_incalzire)) {
                  $i=1;
                  $ag_termic_incalzireErr = 'Campul "Agent Termic Incalzire" poate sa contina doar cifre.';
            }        
            
			if($ag_termic_incalzire > 100){
                  $i=1;
                  $ag_termic_incalzireErr = 'Campul "Agent Termic Incalzire" nu poate sa depaseasca 100%.';
            }        
        }
       
	   if ( $ag_termic_incalzire != '' && $ag_termic_calda != '') {
                if (( $ag_termic_incalzire + $ag_termic_calda) > 100) {
                    $i=1;
                    $ag_termic_incalzireErr = 'Campul "Agent Termic Incalzire" si "Agent Termic Calda" insumate nu pot sa depaseasca 100%.';
                }
        }
       
	   if ( $lift != ''){
            if(ereg('[^0-9.]', $lift)) {
                  $i=1;
                  $liftErr = 'Campul "Procent iluminare lift" poate sa contina doar cifre.';
            }
		}
        
		if ( $proc_incalzire != '') {
            if(ereg('[^0-9.]', $proc_incalzire)) {
                  $i=1;
                  $proc_incalzireErr = 'Campul "Procent incalzire Incalzire" poate sa contina doar cifre.';
            }
            
			if($proc_incalzire > 100){
                  $i=1;
                  $proc_incalzireErr = 'Campul "Agent Termic Incalzire" nu poate sa depaseasca 100%.';
            }
        }
        
		if ( $ag_termic != '' && $lift != '') {
            if (( $ag_termic + $lift) > 100) {
                $i=1;
                $liftErr = 'Campul "Procent iluminare lift"  si "Agent Termic" insumate nu pot sa depaseasca 100%.';
                $ag_termicErr = 'Campul "Procent iluminare lift"  si "Agent Termic" insumate nu pot sa depaseasca 100%.';
            }
        }
		
        $query = "SELECT * FROM scari_setari WHERE scara_id='$scara'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                     
             $scaraErr = 'Au fost introduse setari pentru aceasta scara!';
             $i = 1;                 
        }
		
        if ($i==0) {
                $query = "INSERT INTO scari_setari (`asoc_id`, `scara_id`, `mc_gaz`, `contor_centrala`, `ag_termic`, `ag_termic_calda`, `ag_termic_incalzire`, `are_lift`, `iluminare_lift`, `etaje`, `apartamente`,`parter`, `proc_incalzire`, `pasant`) VALUES 
                                                ('$asocId', '$scara', '$gaz_calda', '$contor_centrala', '$ag_termic', '$ag_termic_calda', '$ag_termic_incalzire', '$areLift', '$lift', '$etaje', '$apartamente', '$parter', '$proc_incalzire', '$pasant')";                
                
                mysql_query($query) or die(mysql_error());
                
                $mesaj = '<font color="green">Datele au fost introduse.</font>';
                unset ($_POST);                
                // trece la pasul urmator                
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        } 
        
	} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
	}
?>

<div id="mainCol" class="clearfix" align="left"><div id="maincon" >
<?php
if ($link=='w_scari_setari') {
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_scari_setari&asoc_id='.$asocId.'">';
} else if ($link == 'asociatii'){
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=asociatii">';
}
?>        
                <input type="hidden" name="buton" value="apasat" />
                <table cellspacing=5 style="margin:20px 0 0 0px; width:220px;" border=0>                        
                        <tr><td>Asociatie:</td>
                            <td>                                
                                        <?php                                                 
                                              $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
                                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                                                  
                                                  echo $row['asociatie'];
                                              }   
                                        ?>                                
                            </td>
                        </tr>
                        <?php
                        /*
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
                        */
                        ?>
                        <?php if ($scaraErr != '') echo '<tr><td></td><td><font color="red">'.$scaraErr.'</font></td></tr>'; ?>                        
                        <tr><td>Scara:</td>                        
                            <td>
                                <select name="scara">
                                        <?php 
                                              $pas = 0;
                                              $query = "SELECT S.*, SS.etaje FROM scari AS S LEFT JOIN scari_setari AS SS ON S.scara_id=SS.scara_id WHERE S.asoc_id='$asocId'";
                                              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                                              
                                              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                  if ($_POST['scara'] == $row['scara_id'])
                                                        echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                                  if ($row['etaje'] == '' ) {
                                                        $pas = 1;
                                                        echo '<option value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                                  }
                                              }
                                        ?>                        
                                </select>                                 
                            </td>
                        </tr>
                       
						<tr>
							<td>Centrala:</td>                        
                            <td>
                                <select name="centralaSelect" onChange="hideShow(this.value)">
                                          <option value="show">Da</option>
                                          <option value="hide" selected="selected">Nu</option>
                                </select>                               
                            </td>
                        </tr>   
                </table>
                        
                        <div id="centrala" style="display:none">
                            <table cellspacing="5" style="margin:0px 0 0 0px; width:350px; border:1px solid #DFDFDF;">                        
                                <tr align="center"><td colspan="2"><u>Informatii Centrala</u></td></tr>
                                <tr><td><u>Informatii Gaz</u></td><td></td></tr>                                
                                <?php if ($gaz_caldaErr != '') echo '<tr><td></td><td><font color="red">'.$gaz_caldaErr.'</font></td></tr>'; 
                                       if ($_POST['gaz_calda'] == '') $_POST['gaz_calda'] = 4;
                                ?>
                                
                                <tr><td>Mc gaz pt mc apa calda:</td><td><input style="width:20px;" type="text" name="gaz_calda" value="<?php echo $_POST['gaz_calda'];?>" /></td></tr>                                
                                <tr><td>Contor Centrala:</td><td>
                                        <select name="contor_centrala">
                                                  <?php 
                                                        if ($_POST['contor_centrala'] != '')
                                                            echo '<option value="'.$_POST['contor_centrala'].'">'.$_POST['contor_centrala'].'</option>';
                                                  ?>
                                                  <option value="nu">nu</option>
                                                  <option value="da">da</option>                                                  
                                          </select> 
                                </td></tr>                                                                                                           
                                <tr><td><u>Informatii Electricitate</u></td><td></td></tr>
                                <tr><td>Contor electric:</td>                        
                                    <td>
                                        <select name="contorSelect" onChange="hideShow1(this.value)">
                                                  <option value="hide">Da</option>
                                                  <option value="show" selected="selected">Nu</option>
                                        </select>(diferit de casa scarii)                               
                                    </td>
                                </tr>
                                <?php if ($ag_termicErr != '') echo '<tr><td></td><td><font color="red">'.$ag_termicErr.'</font></td></tr>'; ?>
                                <tr><td>Proc. Ag. Termic:</td><td><div id="contor"><input style="width:20px; " type="text" name="ag_termic" value="<?php echo $_POST['ag_termic'];?>" />%</div></td></tr>
                                <?php if ($ag_termic_caldaErr != '') echo '<tr><td></td><td><font color="red">'.$ag_termic_caldaErr.'</font></td></tr>'; ?>                
                                <tr><td>Proc. Ag. Termic Apa Calda:</td><td><input style="width:20px;" type="text" name="ag_termic_calda" value="<?php echo $_POST['ag_termic_calda'];?>" />%</td></tr>
                                <?php if ($ag_termic_incalzireErr != '') echo '<tr><td></td><td><font color="red">'.$ag_termic_incalzireErr.'</font></td></tr>'; ?>
                                <tr><td>Proc. Ag. Termic Incalzire:</td><td><input style="width:20px;" type="text" name="ag_termic_incalzire" value="<?php echo $_POST['ag_termic_incalzire'];?>" />%</td></tr>
                                
                            </table>
                        </div>
                        <table cellspacing=5 style="margin:20px 0 0 0px; width:240px;" border=0>
                            <tr><td>Are Lift?</td>                            
                                <td>
                                        <select name="liftSelect1" onChange="hideShow3(this.value)">
                                              <option value="show" selected="selected">Da</option>
                                              <option value="hide" >Nu</option>
                                        </select>
                                </td>
                            </tr>                            
                        </table>
                        <div id="div_lift1"> 
                                    <table cellspacing=5 style="margin:20px 0 0 0px; width:300px;" border=0>
                                        <tr><td>Contor Lift:</td>                            
                                                <td>
                                                    <select name="liftSelect" onChange="hideShow2(this.value)">
                                                              <option value="show" selected="selected">Da</option>
                                                              <option value="hide" >Nu</option>
                                                    </select>(diferit de casa scarii)
                                                </td>
                                        </tr>
                                        <?php if ($liftErr != '') echo '<tr><td></td><td><font color="red">'.$liftErr.'</font></td></tr>'; ?>
                                        <tr><td><span id="span_lift" style="display:none;">Proc iluminare pt lift:</span></td><td>
                                                <div id="div_lift" style="display:none;">
                                                    <?php if ($_POST['lift'] == '') $_POST['lift'] = 70; ?>
                                                    <input style="width:20px;" type="text" name="lift" value="<?php echo $_POST['lift'];?>" />%
                                                </div>
                                        </td></tr>                            
                                      </table>
                          </div>  
                          <table cellspacing=5 style="margin:20px 0 0 0px; width:485px;" border=0>                              
                            <tr><td>Nr Etaje:</td><td><input style="width:20px;" type="text" name="etaje" value="<?php echo $_POST['etaje'];?>" /></td></tr>                        
                            <tr><td>Nr Apartamente:</td><td><input style="width:20px;" type="text" name="apartamente" value="<?php echo $_POST['apartamente'];?>" /> / scara</td></tr> 
                            <tr><td>Parter:</td><td>
                                  <select name="parter">
                                          <?php 
                                                    if ($_POST['parter'] != '')
                                                        echo '<option value="'.$_POST['parter'].'">'.$_POST['parter'].'</option>';
                                          ?>
                                          <option value="da">da</option>
                                          <option value="nu">nu</option>
                                  </select>
                            </td></tr>
                            <?php if ($proc_incalzireErr != '') echo '<tr><td></td><td><font color="red">'.$proc_incalzireErr.'</font></td></tr>'; ?>                        
                            <tr><td>Procent Incalzire:</td><td><input style="width:20px;" type="text" name="proc_incalzire" value="<?php echo $_POST['proc_incalzire'];?>" />%</td></tr>            
                            <?php
                                echo '<tr><td>Are pasant?</td><td>
                                                <select name="pasant">
                                                      <option value="nu">nu</option>
                                                      <option value="da">da</option>';
                                                      if ($_POST['pasant'] != '')
                                                            echo '<option value="'.$_POST['pasant'].'" selected="selected">'.$_POST['pasant'].'</option>';                                                                                                                
                                          echo '</select></td></tr>';   
                            ?>

                            <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>
                            <?php
                                        echo'
                                        <tr><td></td><td></td></tr>
                                        <tr><td><div id="butonBack"><a href="index.php?link=w_scari_dat&asoc_id='.$asocId.'" style="">Pasul Anterior</a></div></td>';
                                    if ($pas == 0) {    
                                        echo '<td><div id="buton"><a href="index.php?link=w_locatari&asoc_id='.$asocId.'" style="">Pasul Urmator</a></div></td></tr>';                                        
                                    }                                    
                            ?>
                            <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                        </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE ASOCIATII   ****************************************/ ?>


<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru scara "+furnizor+" ?");
     if(answer)
        window.location = "index.php?link=w_scari_setari&asoc_id=<?php echo $asocId;?>&sterge="+fur_id;
}
function functionFormEdit(){
    document.formEdit.submit();
}
</script>

<?php
    $editSs = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $ssId = mysql_real_escape_string($_POST['ss']);

            $mc_gazT = mysql_real_escape_string($_POST['mc_gazT']);
            $ag_termicT = mysql_real_escape_string($_POST['ag_termicT']);
            $ag_termic_caldaT = mysql_real_escape_string($_POST['ag_termic_caldaT']);
            $ag_termic_incalzireT = mysql_real_escape_string($_POST['ag_termic_incalzireT']);
            $iluminare_liftT = mysql_real_escape_string($_POST['iluminare_liftT']);
            $etajeT = mysql_real_escape_string($_POST['etajeT']);
            $apartamenteT = mysql_real_escape_string($_POST['apartamenteT']);            
            $proc_incalzireT = mysql_real_escape_string($_POST['proc_incalzireT']);
                
            if ( $mc_gazT != '' && ereg('[^0-9.]', $mc_gazT)) {
                    $gaz_caldaErr1 = 'Campul "Mc Gaz Apa calda" poate sa contina doar cifre.';  
                    $i=1;
            }
            
            if ( $ag_termicT != '') {               
                    if(ereg('[^0-9.]', $ag_termicT)) {
                          $i=1;
                          $ag_termicErr1 = 'Campul "Agent Termic" poate sa contina doar cifre.<br />';
                    }
                    if($ag_termicT > 100){
                      $i=1;
                      $ag_termicErr1 = 'Campul "Agent Termic" nu poate sa depaseasca 100%.<br />';
                    }        
            }
            if ( $ag_termic_caldaT != '') {
                if(ereg('[^0-9.]', $ag_termic_caldaT)) {
                      $i=1;
                      $ag_termic_caldaErr1 = 'Campul "Agent Termic Calda" poate sa contina doar cifre.<br />';
                }                    
                if($ag_termic_caldaT > 100){
                      $i=1;
                      $ag_termic_caldaErr1 = 'Campul "Agent Termic Calda" nu poate sa depaseasca 100%.<br />';
                }        
            }
            if ( $ag_termic_incalzireT != '') {
                if(ereg('[^0-9.]', $ag_termic_incalzireT)) {
                      $i=1;
                      $ag_termic_incalzireErr1 = 'Campul "Agent Termic Incalzire" poate sa contina doar cifre.<br />';
                }        
                if($ag_termic_incalzireT > 100){
                      $i=1;
                      $ag_termic_incalzireErr1 = 'Campul "Agent Termic Incalzire" nu poate sa depaseasca 100%.<br />';
                }        
            }
            if ( $ag_termic_incalzireT != '' && $ag_termic_caldaT != '') {
                    if (( $ag_termic_incalzireT + $ag_termic_caldaT) > 100) {
                        $i=1;
                        $ag_termic_incalzireErr1 = 'Campul "Agent Termic Incalzire" si "Agent Termic Calda" insumate nu pot sa depaseasca 100%.<br />';
                    }
            }
            if ( $ag_termicT != '' && $iluminare_liftT != '') {
                if (( $ag_termicT + $iluminare_liftT) > 100) {
                    $i=1;
                    $liftErr1 = 'Campul "Procent iluminare lift"  si "Agent Termic" insumate nu pot sa depaseasca 100%.<br />';                    
                }
            }         
            if ( $proc_incalzireT != '') {
                if(ereg('[^0-9.]', $proc_incalzireT)) {
                      $i=1;
                      $proc_incalzireErr1 = 'Campul "Procent incalzire Incalzire" poate sa contina doar cifre.<br />';
                }
                if($proc_incalzireT > 100){
                      $i=1;
                      $proc_incalzireErr1 = 'Campul "Agent Termic Incalzire" nu poate sa depaseasca 100%.<br />';
                }
            }
            if ($i==0) {
                    $query = "UPDATE scari_setari SET `mc_gaz`= '$mc_gazT', `ag_termic`= '$ag_termicT', `ag_termic_calda`='$ag_termic_caldaT', `ag_termic_incalzire`='$ag_termic_incalzireT', 
                                                    `iluminare_lift`='$iluminare_liftT', `etaje`='$etajeT', `apartamente`='$apartamenteT', 
                                                    `proc_incalzire`='$proc_incalzireT' 
                                    WHERE ss_id='$ssId'";
                    //echo $query;
                    mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
            } else {
                    $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
            }
    }
?>

<div id="maincon" style="width:900px;">
<?php
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$gaz_caldaErr1.$ag_termicErr1.$ag_termic_caldaErr1.$ag_termic_incalzireErr1.$proc_incalzireErr1.$mesaj1.'</font>
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
    <table width=880>
           <tr bgcolor="#19AF62">
                <td><font size=2 color="white"><center>Asociatie</center></font></td>
                <td><font size=2 color="white"><center>Scara</center></font></td>
                <td><font size=2 color="white"><center>Mc Gaz<br>Calda</center></font></td>               
                <td><font size=2 color="white"><center>Cont<br>Centrala</center></font></td>               
                <td><font size=2 color="white"><center>Agent<br>Termic</center></font></td>               
                <td><font size=2 color="white"><center>Agent<br>Termic Calda</center></font></td>                              
                <td><font size=2 color="white"><center>Agent<br>Termic<br> Inclazire</center></font></td> 
				
				<td><font size=2 color="white"><center>Are<br>Lift</center></font></td>			   
                
				<td><font size=2 color="white"><center>Ilum.<br>Lift</center></font></td>               
                <td><font size=2 color="white"><center>Etaje</center></font></td>               
                <td><font size=2 color="white"><center>Apartamente</center></font></td>               
                <td><font size=2 color="white"><center>Parter</center></font></td>               
                <td><font size=2 color="white"><center>Proc<br>Incalzire</center></font></td>               
                <td><font size=2 color="white"><center>Pasant</center></font></td>               
                <td><font size=2 color="white"><center>Optiuni</center></font></td>               
           </tr>
           <?php
              $query = "SELECT SS.*, S.scara, A.asociatie FROM scari_setari AS SS 
                                                                            JOIN asociatii AS A ON A.asoc_id=SS.asoc_id 
                                                                            JOIN scari AS S ON SS.scara_id=S.scara_id 
                                                  WHERE A.asoc_id = '$asocId'
                                                  ORDER BY S.scara ";
              $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editSs == $row['ss_id']){                                
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=w_scari_setari&asoc_id='.$asocId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="ss" value="'.$editSs.'" />';                                    
                                    
                                    echo '<td>'.$row['asociatie'].'</td>';
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td><input style="width:30px;" type="text" name="mc_gazT" value="'.$row['mc_gaz'].'"></td>';
                                    echo '<td>'.$row['contor_centrala'].'</td>';                                    
                                    echo '<td><input style="width:30px;" type="text" name="ag_termicT" value="'.$row['ag_termic'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="ag_termic_caldaT" value="'.$row['ag_termic_calda'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="ag_termic_incalzireT" value="'.$row['ag_termic_incalzire'].'"></td>';
									
									echo '<td>'.$row['are_lift'].'</td>';
									
                                    echo '<td><input style="width:30px;" type="text" name="iluminare_liftT" value="'.$row['iluminare_lift'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="etajeT" value="'.$row['etaje'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="apartamenteT" value="'.$row['apartamente'].'"></td>';
                                    echo '<td>'.$row['parter'].'</td>';
                                    echo '<td><input style="width:30px;" type="text" name="proc_incalzireT" value="'.$row['proc_incalzire'].'"></td>';
                                    echo '<td>'.$row['pasant'].'</td>';
                                      
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {
                        
                        //(`asoc_id`, `scara_id`, `mc_gaz`, `contor_centrala`, `ag_termic`, `ag_termic_calda`, `ag_termic_incalzire`, `iluminare_lift`, `etaje`, `apartamente`,`parter`, `proc_incalzire`) VALUES 
                                                        
                                echo '<td>'.$row['asociatie'].'</td>';
                                echo '<td>'.$row['scara'].'</td>';
                                echo '<td>'.$row['mc_gaz'].'</td>';
                                echo '<td>'.$row['contor_centrala'].'</td>';
                                echo '<td>'.$row['ag_termic'].'</td>';
                                echo '<td>'.$row['ag_termic_calda'].'</td>';
                                echo '<td>'.$row['ag_termic_incalzire'].'</td>';
								
								echo '<td>'.$row['are_lift'].'</td>';
								
                                echo '<td>'.$row['iluminare_lift'].'</td>';                                
                                echo '<td>'.$row['etaje'].'</td>';
                                echo '<td>'.$row['apartamente'].'</td>';
                                echo '<td>'.$row['parter'].'</td>';
                                echo '<td>'.$row['proc_incalzire'].'</td>';
                                echo '<td>'.$row['pasant'].'</td>';
                                
                                echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=w_scari_setari&asoc_id='.$asocId.'&edit='.$row['ss_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;
                                                <a style="font-size:12px; color:red; cursor:pointer;" onclick="functionStergeFur('."'".$row['scara']."','".$row['ss_id']."'".')" >[sterge]</a>
                                      </center></td>';
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

