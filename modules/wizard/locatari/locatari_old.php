<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }
        var strArr=new Array();        
</script>

<?php
$asocId = $_GET['asoc_id'];

$i=0;
$pas = 0;
$query = "SELECT SS.*, L.loc_id FROM scari_setari AS SS LEFT JOIN locatari AS L ON SS.scara_id=L.scara_id WHERE SS.asoc_id='$asocId' ORDER BY SS.ss_id DESC";
$result = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {    
    if ($row['loc_id'] == '' && $i==0){
        $scaraId = $row['scara_id'];
        $parter = $row['parter'];
        $etaje = $row['etaje'];
        $apartamente = $row['apartamente'];
        $i=1;
        
    } else {
        $pas = 1;
    }
}
$nrApartamente = $apartamente * $etaje;
if ($parter == 'da') $nrApartamente1 = $nrApartamente + $apartamente;

//echo '**********'.$nrApartamente;

$i = 0;
$apNr = 0;
if ($_POST['buton'] == 'apasat' ) {
        if ($parter == 'da') {
             for ($e=1; $e<=$apartamente; $e++) {
                $apNr ++;                
                if ($_POST['nume_'.$apNr] != '') {
                        if ($_POST['nr_pers_'.$apNr] == '') $i = 1;
                        if ($_POST['supr_'.$apNr] == '') $i = 1;
                        if ($_POST['cota_'.$apNr] == '') $i = 1;
                        if ($_POST['ap_rece_'.$apNr] == '') $i = 1;
                        if ($_POST['ap_calda_'.$apNr] == '') $i = 1;
                        if ($_POST['nr_rep_'.$apNr] == '') $i = 1;
                        if ($_POST['centrala_'.$apNr] == 'nimic') $i = 1;
                        if ($_POST['incalzire_'.$apNr] == 'nimic') $i = 1;
                        if ($_POST['gaz_'.$apNr] == 'nimic') $i = 1;
                        if ($_POST['ilum_lift_'.$apNr] == 'nimic') $i = 1;
                        if ($_POST['service_lift_'.$apNr] == 'nimic') $i = 1;
                }
                
            }
        }

        for ($j=1; $j<=$etaje; $j++) {
            for ($e=1; $e<=$apartamente; $e++) {
                $apNr ++;
                if ($_POST['nume_'.$apNr] == '') $i = 1;
                if ($_POST['nr_pers_'.$apNr] == '') $i = 1;
                if ($_POST['supr_'.$apNr] == '') $i = 1;
                if ($_POST['cota_'.$apNr] == '') $i = 1;                                
                if ($_POST['ap_rece_'.$apNr] == '') $i = 1;
                if ($_POST['ap_calda_'.$apNr] == '') $i = 1;        
                if ($_POST['nr_rep_'.$apNr] == '') $i = 1;        
                if ($_POST['centrala_'.$apNr] == 'nimic') $i = 1;
                if ($_POST['incalzire_'.$apNr] == 'nimic') $i = 1;
                if ($_POST['gaz_'.$apNr] == 'nimic') $i = 1;        
                if ($_POST['ilum_lift_'.$apNr] == 'nimic') $i = 1;        
                if ($_POST['service_lift_'.$apNr] == 'nimic') $i = 1;                
            }
        }
}
/*
echo '<pre>';
print_r ($_POST);
echo '</pre>';
  */
if ($_POST['buton'] == 'apasat' && $i == 0 ) {
        //$i = 0;
        //$asociatie = mysql_real_escape_string($_POST['asociatie']);
        $apNr = 0;
        
        if ($parter == 'da') {
             for ($e=1; $e<=$apartamente; $e++) {
                $apNr ++;         
                $nume = mysql_real_escape_string($_POST['nume_'.$apNr]);
                $cnp = mysql_real_escape_string($_POST['cnp_'.$apNr]);
                $nr_pers = mysql_real_escape_string($_POST['nr_pers_'.$apNr]);
                $supr = mysql_real_escape_string($_POST['supr_'.$apNr]);
                $cota = mysql_real_escape_string($_POST['cota_'.$apNr]);
                $ap_rece = mysql_real_escape_string($_POST['ap_rece_'.$apNr]);   
                $ap_calda = mysql_real_escape_string($_POST['ap_calda_'.$apNr]);   
                $nr_rep = mysql_real_escape_string($_POST['nr_rep_'.$apNr]);   
                $centrala = mysql_real_escape_string($_POST['centrala_'.$apNr]);   
                $incalzire = mysql_real_escape_string($_POST['incalzire_'.$apNr]);   
                $gaz = mysql_real_escape_string($_POST['gaz_'.$apNr]);   
                $ilum_lift =mysql_real_escape_string( $_POST['ilum_lift_'.$apNr]);   
                $service_lift = mysql_real_escape_string($_POST['service_lift_'.$apNr]);                
                if ($nume != '') {
                        if ( $cnp != '') {                        
                            if (strlen($cnp)!=13) {                        
                               $cnpErr[$apNr] = '(Ap '.$apNr.')Campul "CNP" este incorect.<br>';
                               $i=1;             
                            }               
                        }
                        if(ereg('[^0-9]', $nr_pers)) {
                              $i=1;
                              $nr_persErr[$apNr] = '(Ap '.$apNr.')Campul "Numar persoane" poate sa contina doar cifre.<br>';
                        }                 
                        if(ereg('[^0-9]', $supr)) {
                              $i=1;
                              $suprErr[$apNr] = '(Ap '.$apNr.')Campul "Suprafata" poate sa contina doar cifre.<br>';
                        }                 
                        if(ereg('[^0-9]', $cota)) {
                              $i=1;
                              $cotaErr[$apNr] = '(Ap '.$apNr.')Campul "Cota indiviza" poate sa contina doar cifre.<br>';
                        }                 
                        if(ereg('[^0-9]', $ap_rece)) {
                              $i=1;
                              $ap_receErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa rece" poate sa contina doar cifre.<br>';
                        }                 
                        if(ereg('[^0-9]', $ap_calda)) {
                              $i=1;
                              $ap_caldaErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa calda" poate sa contina doar cifre.<br>';
                        }                                 
                        if($ap_rece > 5) {
                              $i=1;
                              $ap_receErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa rece" NU poate sa aibe valoarea mai mare decat 5.<br>';
                        }                 
                        if($ap_calda > 5) {
                              $i=1;
                              $ap_caldaErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa calda" NU poate sa aibe valoarea mai mare decat 5.<br>';
                        }                                 
                        if(ereg('[^0-9]', $nr_rep)) {
                              $i=1;
                              $nr_repErr[$apNr] = '(Ap '.$apNr.')Campul "Nr rep" poate sa contina doar cifre.<br>';
                        }       
                }
            }
        } 
        
        for ($j=1; $j<=$etaje; $j++) {
            for ($e=1; $e<=$apartamente; $e++) {
                $apNr ++;
                $nume = mysql_real_escape_string($_POST['nume_'.$apNr]);
                $cnp = mysql_real_escape_string($_POST['cnp_'.$apNr]);
                $nr_pers = mysql_real_escape_string($_POST['nr_pers_'.$apNr]);
                $supr = mysql_real_escape_string($_POST['supr_'.$apNr]);
                $cota = mysql_real_escape_string($_POST['cota_'.$apNr]);
                $ap_rece =mysql_real_escape_string($_POST['ap_rece_'.$apNr]);   
                $ap_calda = mysql_real_escape_string($_POST['ap_calda_'.$apNr]);   
                $nr_rep = mysql_real_escape_string($_POST['nr_rep_'.$apNr]);   
                $centrala = mysql_real_escape_string($_POST['centrala_'.$apNr]);   
                $incalzire = mysql_real_escape_string($_POST['incalzire_'.$apNr]);   
                $gaz = mysql_real_escape_string($_POST['gaz_'.$apNr]);   
                $ilum_lift = mysql_real_escape_string($_POST['ilum_lift_'.$apNr]);   
                $service_lift = mysql_real_escape_string($_POST['service_lift_'.$apNr]);                
                
                if ( $cnp != '') {                        
                    if (strlen($cnp)!=13) {
                       $cnpErr[$apNr] = '(Ap '.$apNr.')Campul "CNP" este incorect.<br>';
                       $i=1;               
                    }             
                }
                if(ereg('[^0-9]', $nr_pers)) {
                      $i=1;
                      $nr_persErr[$apNr] = '(Ap '.$apNr.')Campul "Numar persoane" poate sa contina doar cifre.<br>';
                }                 
                if(ereg('[^0-9]', $supr)) {
                      $i=1;
                      $suprErr[$apNr] = '(Ap '.$apNr.')Campul "Suprafata" poate sa contina doar cifre.<br>';
                }                 
                if(ereg('[^0-9]', $cota)) {
                      $i=1;
                      $cotaErr[$apNr] = '(Ap '.$apNr.')Campul "Cota indiviza" poate sa contina doar cifre.<br>';
                }                
                if(ereg('[^0-9]', $ap_rece)) {
                      $i=1;
                      $ap_receErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa rece" poate sa contina doar cifre.<br>';
                }                 
                if(ereg('[^0-9]', $ap_calda)) {
                      $i=1;
                      $ap_caldaErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa calda" poate sa contina doar cifre.<br>';
                } 
                if($ap_rece > 5) {
                      $i=1;
                      $ap_receErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa rece" NU poate sa aibe valoarea mai mare decat 5.<br>';
                }                 
                if($ap_calda > 5) {
                      $i=1;
                      $ap_caldaErr[$apNr] = '(Ap '.$apNr.')Campul "Ap Apa calda" NU poate sa aibe valoarea mai mare decat 5.<br>';
                }                                                                                 
                if(ereg('[^0-9]', $nr_rep)) {
                      $i=1;
                      $nr_repErr[$apNr] = '(Ap '.$apNr.')Campul "Nr rep" poate sa contina doar cifre.<br>';
                } 
                
            }
        }                     
        if ($i==0) {
                    $apNr = 0;
                    if ($parter == 'da') {
                         for ($e=1; $e<=$apartamente; $e++) {
                                $apNr ++;         
                                $nume = $_POST['nume_'.$apNr];
                                $cnp = $_POST['cnp_'.$apNr];
                                $nr_pers = $_POST['nr_pers_'.$apNr];
                                $supr = $_POST['supr_'.$apNr];
                                $cota = $_POST['cota_'.$apNr];
                                $ap_rece = $_POST['ap_rece_'.$apNr];   
                                $ap_calda = $_POST['ap_calda_'.$apNr];   
                                $nr_rep = $_POST['nr_rep_'.$apNr];   
                                $centrala = $_POST['centrala_'.$apNr];   
                                $incalzire = $_POST['incalzire_'.$apNr];   
                                $gaz = $_POST['gaz_'.$apNr];   
                                $ilum_lift = $_POST['ilum_lift_'.$apNr];   
                                $service_lift = $_POST['service_lift_'.$apNr];  
                                $query = "INSERT INTO locatari (`scara_id`,`asoc_id`, `nume`,`cnp`,`nr_pers`,`supr`,`cota`, `ap`, `ap_rece`, `ap_calda`, `nr_rep`, `centrala`, `incalzire`, `gaz`, `ilum_lift`, `service_lift`) VALUES 
                                                               ('$scaraId', '$asocId', '$nume', '$cnp','$nr_pers','$supr','$cota', '$apNr', '$ap_rece', '$ap_calda', '$nr_rep', '$centrala', '$incalzire', '$gaz', '$ilum_lift', '$service_lift')";                                                              
                                mysql_query($query) or die(mysql_error());        
                         }
                    }
                    for ($j=1; $j<=$etaje; $j++) {
                        for ($e=1; $e<=$apartamente; $e++) {
                                $apNr ++;
                                $nume = $_POST['nume_'.$apNr];
                                $cnp = $_POST['cnp_'.$apNr];
                                $nr_pers = $_POST['nr_pers_'.$apNr];
                                $supr = $_POST['supr_'.$apNr];
                                $cota = $_POST['cota_'.$apNr];                                
                                $ap_rece = $_POST['ap_rece_'.$apNr];   
                                $ap_calda = $_POST['ap_calda_'.$apNr];   
                                $nr_rep = $_POST['nr_rep_'.$apNr];   
                                $centrala = $_POST['centrala_'.$apNr];   
                                $incalzire = $_POST['incalzire_'.$apNr];   
                                $gaz = $_POST['gaz_'.$apNr];   
                                $ilum_lift = $_POST['ilum_lift_'.$apNr];   
                                $service_lift = $_POST['service_lift_'.$apNr];  
                                $query = "INSERT INTO locatari (`scara_id`,`asoc_id`, `nume`,`cnp`,`nr_pers`,`supr`,`cota`,`etaj`, `ap`, `ap_rece`, `ap_calda`, `nr_rep`, `centrala`, `incalzire`, `gaz`, `ilum_lift`, `service_lift`) VALUES 
                                                               ('$scaraId', '$asocId', '$nume', '$cnp','$nr_pers','$supr','$cota','$j', '$apNr', '$ap_rece', '$ap_calda', '$nr_rep', '$centrala', '$incalzire', '$gaz', '$ilum_lift', '$service_lift')";                                                              
                                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 *****');
                        }
                    }                                
                    $mesaj = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);
                    if ($link=='w_locatari') echo '<script language="javascript">window.location.href = "index.php?link=w_locatari&asoc_id='.$asocId.'"</script>';                                                             
           
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }                                       
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}
?>

<div id="mainCol" class="clearfix" align="left"><div id="maincon"  style="width:950px;">
<?php
if ($link=='w_locatari') {
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_locatari&asoc_id='.$asocId.'">';
} else if ($link == 'asociatii'){  // aici trebuie schimbat ex: asoc_dat - de pus in index
    echo '<form id="addForm" name="addForm" method="post" action="index.php?link=w_locatari">';
}

?>        
                <input type="hidden" name="buton" value="apasat" /> 
                Asociatia:
                <?php 
                      $query = "SELECT * FROM asociatii WHERE asoc_id='$asocId'";
                      $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                          echo $row['asociatie'];
                      }
                ?>
                <br />
                Scara:
                <?php 
                      $query = "SELECT * FROM scari WHERE scara_id='$scaraId'";
                      $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                          echo '<b><font style="font-size:14px;">'.$row['scara'].'</font></b>';
                          
                      }
                
                if ($i == 1) {
                    $apNr = 0;             
                    echo '<div id="errorBox" style="">';
                        if ($parter == 'da') {
                            for ($j=1; $j<=$etaje; $j++) {                                
                                $apNr ++;
                                echo '<font color="red">'.$cnpErr[$apNr].$ap_receErr[$apNr].$ap_caldaErr[$apNr].$nr_repErr[$apNr].'</font>';
                            }
                        }
                        for ($e=1; $e<=$apartamente; $e++) {
                            for ($j=1; $j<=$etaje; $j++) {
                                $apNr ++;
                                echo '<font color="red">'.$cnpErr[$apNr].$nr_persErr[$apNr].$suprErr[$apNr].$cotaErr[$apNr].$ap_receErr[$apNr].$ap_caldaErr[$apNr].$nr_repErr[$apNr].'</font>';
                            }
                        }
                    echo'</div>
                    ';
                    
                }
                ?>                                        
                <table cellspacing=1 style="margin:10px 0 0 0px; width:950px;" border=0>                                                
                        <tr bgcolor="#19AF62">
                               <td><font size=2 color="white"><center>Et</center></font></td>
                               <td><font size=2 color="white"><center>Ap</center></font></td>
                               <td><font size=2 color="white"><center>Nume</center></font></td>                               
                               <td><font size=2 color="white"><center>CNP</center></font></td>
                               <td><font size=2 color="white"><center>Nr<br>pers</center></font></td>
                               <td><font size=2 color="white"><center>Supr.</center></font></td>
                               <td><font size=2 color="white"><center>Cota<br>indiviza</center></font></td>
                               <td><font size=2 color="white"><center>Ap apa<br>rece</center></font></td>
                               <td><font size=2 color="white"><center>Ap apa<br>calda</center></font></td>                               
                               <td><font size=2 color="white"><center>Nr<br>rep</center></font></td>
                               <td><font size=2 color="white"><center>Centrala</center></font></td>
                               <td><font size=2 color="white"><center>Incalzire</center></font></td>
                               <td><font size=2 color="white"><center>Gaz</center></font></td>
                               <td><font size=2 color="white"><center>Ilum <br>Lift</center></font></td>
                               <td><font size=2 color="white"><center>Service <br>Lift</center></font></td>
                        </tr>
                        <?php
                        $apNr = 0;
                        // PARTER
                        if ($parter == 'da') {
                        for ($e=1; $e<=$apartamente; $e++) {
                            $apNr ++;
                            if ($apNr %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                                echo '<td width=30><center>P</center></td>';
                                echo '<td ><center>ap '.$apNr.'</center></td>';
                                echo '<td><input style="width:130px;" type="text" name="nume_'.$apNr.'" value="'.$_POST['nume_'.$apNr].'"></td>';                                    
                                echo '<td><input style="width:110px;" type="text" name="cnp_'.$apNr.'" value="'.$_POST['cnp_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 type="text" name="nr_pers_'.$apNr.'" value="'.$_POST['nr_pers_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 type="text" name="supr_'.$apNr.'" value="'.$_POST['supr_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 type="text" name="cota_'.$apNr.'" value="'.$_POST['cota_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 type="text" name="ap_rece_'.$apNr.'" value="'.$_POST['ap_rece_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 name="ap_calda_'.$apNr.'" value="'.$_POST['ap_calda_'.$apNr].'"></td>';                                                                        
                                echo '<td><input style="width:30px;" maxlength=1 name="nr_rep_'.$apNr.'" value="'.$_POST['nr_rep_'.$apNr].'"></td>';                                                                                                            
                                echo '<td ><select name="centrala_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                            if ($_POST['centrala_'.$apNr] != '' && $_POST['centrala_'.$apNr] != 'nimic') 
                                            echo '<option selected="selected" value="'.$_POST['centrala_'.$apNr].'">'.$_POST['centrala_'.$apNr].'</option>';                                                
                                            echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                        echo '</select></td>';                                                                                   
                                echo '<td ><select name="incalzire_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                            if ($_POST['incalzire_'.$apNr] != '' && $_POST['incalzire_'.$apNr] != 'nimic') 
                                            echo '<option selected="selected" value="'.$_POST['incalzire_'.$apNr].'">'.$_POST['incalzire_'.$apNr].'</option>';                                                
                                            echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                        echo '</select></td>';                                                                                  
                                echo '<td ><select name="gaz_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                            if ($_POST['gaz_'.$apNr] != '' && $_POST['gaz_'.$apNr] != 'nimic') 
                                            echo '<option selected="selected" value="'.$_POST['gaz_'.$apNr].'">'.$_POST['gaz_'.$apNr].'</option>';                                                
                                            echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                        echo '</select></td>';                                                
                                echo '<td ><select name="ilum_lift_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                            if ($_POST['ilum_lift_'.$apNr] != '' && $_POST['ilum_lift_'.$apNr] != 'nimic') 
                                            echo '<option selected="selected" value="'.$_POST['ilum_lift_'.$apNr].'">'.$_POST['ilum_lift_'.$apNr].'</option>';                                                
                                            echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                        echo '</select></td>';                                                
                                echo '<td ><select name="service_lift_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                            if ($_POST['service_lift_'.$apNr] != '' && $_POST['service_lift_'.$apNr] != 'nimic') 
                                            echo '<option selected="selected" value="'.$_POST['service_lift_'.$apNr].'">'.$_POST['service_lift_'.$apNr].'</option>';                                                
                                            echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                        echo '</select></td>';                                                
                            echo '</tr>';
                        }}   
                        // ETAJE                                  
                        for ($j=1; $j<=$etaje; $j++) {
                            //if ($strIdErr[$j] != '') echo '<tr><td></td><td><font color="red">'.$strIdErr[$j].'</font></td></tr>';
                            for ($e=1; $e<=$apartamente; $e++) {
                                $apNr ++;
                                if ($apNr %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                                    echo '<input type="hidden" name="etaj_'.$j.'" value="apasat" />';
                                    echo '<td width=30><center>et '.$j.'</center></td>';
                                    echo '<td ><center>ap '.$apNr.'</center></td>';
                                    echo '<td><input style="width:130px;" type="text" name="nume_'.$apNr.'" value="'.$_POST['nume_'.$apNr].'"></td>';                                    
                                    echo '<td><input style="width:110px;" type="text" name="cnp_'.$apNr.'" value="'.$_POST['cnp_'.$apNr].'"></td>';                                                                                                            
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="nr_pers_'.$apNr.'" value="'.$_POST['nr_pers_'.$apNr].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="supr_'.$apNr.'" value="'.$_POST['supr_'.$apNr].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="cota_'.$apNr.'" value="'.$_POST['cota_'.$apNr].'"></td>';                                
                                    echo '<td><input style="width:30px;" maxlength=2 type="text" name="ap_rece_'.$apNr.'" value="'.$_POST['ap_rece_'.$apNr].'"></td>';                                                                                                            
                                    echo '<td><input style="width:30px;" maxlength=2 name="ap_calda_'.$apNr.'" value="'.$_POST['ap_calda_'.$apNr].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=2 name="nr_rep_'.$apNr.'" value="'.$_POST['nr_rep_'.$apNr].'"></td>';                                                                                                            
                                    echo '<td ><select name="centrala_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                                if ($_POST['centrala_'.$apNr] != '' && $_POST['centrala_'.$apNr] != 'nimic') 
                                                echo '<option selected="selected" value="'.$_POST['centrala_'.$apNr].'">'.$_POST['centrala_'.$apNr].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                                                   
                                    echo '<td ><select name="incalzire_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                                if ($_POST['incalzire_'.$apNr] != '' && $_POST['incalzire_'.$apNr] != 'nimic') 
                                                echo '<option selected="selected" value="'.$_POST['incalzire_'.$apNr].'">'.$_POST['incalzire_'.$apNr].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                                                  
                                    echo '<td ><select name="gaz_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                                if ($_POST['gaz_'.$apNr] != '' && $_POST['gaz_'.$apNr] != 'nimic') 
                                                echo '<option selected="selected" value="'.$_POST['gaz_'.$apNr].'">'.$_POST['gaz_'.$apNr].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                
                                    echo '<td ><select name="ilum_lift_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                                if ($_POST['ilum_lift_'.$apNr] != '' && $_POST['ilum_lift_'.$apNr] != 'nimic') 
                                                echo '<option selected="selected" value="'.$_POST['ilum_lift_'.$apNr].'">'.$_POST['ilum_lift_'.$apNr].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                
                                    echo '<td ><select name="service_lift_'.$apNr.'"><option value="nimic">Alege</option>'; 
                                                if ($_POST['service_lift_'.$apNr] != '' && $_POST['service_lift_'.$apNr] != 'nimic') 
                                                echo '<option selected="selected" value="'.$_POST['service_lift_'.$apNr].'">'.$_POST['service_lift_'.$apNr].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                
                                echo '</tr>';
                            }
                        }
                        echo '</table>';
                        
                        echo '<table><tr><td></td><td><br /><br /><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>';
                        ?>                        
                        
                        <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
                        </table>
                        <table>
                             <?php
                                        echo'
                                        <tr><td></td><td></td></tr>
                                        <tr><td><div id="butonBack"><a href="index.php?link=w_scari_setari&asoc_id='.$asocId.'" style="">Pasul Anterior</a></div></td>';
                                    if ($pas == 1) {    
                                        echo '<td><div id="buton"><a href="index.php?link=w_locatari_apometre&asoc_id='.$asocId.'" style="">Pasul Urmator</a></div></td></tr>';                            
                                    }                                    
                            ?>
                        </table>
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE LOCATARII ****************************************/ ?>

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
    $editLoc = mysql_real_escape_string($_GET['edit']);
    
    if ($_POST['salveaza'] == 'apasat') {
            $i = 0;
            $lcLd = mysql_real_escape_string($_POST['lc']);            
            $numeT = mysql_real_escape_string($_POST['numeT']);
            $cnpT = mysql_real_escape_string($_POST['cnpT']);
            $nr_persT = mysql_real_escape_string($_POST['nr_persT']);
            $suprT = mysql_real_escape_string($_POST['suprT']);
            $cotaT = mysql_real_escape_string($_POST['cotaT']);
            $ap_receT = mysql_real_escape_string($_POST['ap_receT']);   
            $ap_caldaT = mysql_real_escape_string($_POST['ap_caldaT']);   
            $nr_repT = mysql_real_escape_string($_POST['nr_repT']);   
            $centralaT = mysql_real_escape_string($_POST['centralaT']);   
            $incalzireT = mysql_real_escape_string($_POST['incalzireT']);   
            $gazT = mysql_real_escape_string($_POST['gazT']);   
            $ilum_liftT = mysql_real_escape_string($_POST['ilum_liftT']);   
            $service_liftT = mysql_real_escape_string($_POST['service_liftT']);                
            
            if ( $numeT == '') {       
                    $numeErr1 = 'Campul "nume" nu trebuie lasat necompletat.<br>';
                    $i=1;                                
            }
            if ( $cnpT != '') {                        
                if (strlen($cnpT)!=13) {
                   $cnpErr1 = 'Campul "CNP" este incorect.<br>';
                   $i=1;               
                }             
            }
            if(ereg('[^0-9]', $nr_persT)) {
                  $i=1;
                  $nr_persErr1 = 'Campul "Numar persoane" poate sa contina doar cifre.<br>';
            }                 
            if(ereg('[^0-9]', $suprT)) {
                  $i=1;
                  $suprErr1 = 'Campul "Suprafata" poate sa contina doar cifre.<br>';
            }                 
            if(ereg('[^0-9]', $cotaT)) {
                  $i=1;
                  $cotaErr1 = 'Campul "Cota indiviza" poate sa contina doar cifre.<br>';
            }                
            if(ereg('[^0-9]', $ap_receT)) {
                  $i=1;
                  $ap_receErr1 = 'Campul "Ap Apa rece" poate sa contina doar cifre.<br>';
            }                 
            if(ereg('[^0-9]', $ap_caldaT)) {
                  $i=1;
                  $ap_caldaErr1 = 'Campul "Ap Apa calda" poate sa contina doar cifre.<br>';
            }                                 
            if(ereg('[^0-9]', $nr_repT)) {
                  $i=1;
                  $nr_repErr1 = 'Campul "Nr rep" poate sa contina doar cifre.<br>';
            } 
            if ($i == 0) {
                    $query = "UPDATE locatari SET `nume`='$numeT',`cnp`='$cnpT',`nr_pers`='$nr_persT',`supr`='$suprT',`cota`='$cotaT',`ap_rece`='$ap_receT',
                    `ap_calda`='$ap_caldaT', `nr_rep`='$nr_repT', `centrala`='$centralaT', `incalzire`='$incalzireT', `gaz`='$gazT', `ilum_lift`='$ilum_liftT',
                    `service_lift`='$service_liftT' WHERE loc_id='$lcLd'";            
                    mysql_query($query) or die(mysql_error());
                    $mesaj1 = '<font color="green">Datele au fost introduse.</font>';
                    unset ($_POST);                                
            } else {
                    $mesaj1 = '<font color="green">Au aparut urmatoarele erori:<br></font>';
            }
            
    }    
?>

<div id="maincon" style="width:950px;">
<?php
        if ($mesaj1 != '') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$mesaj1.$numeErr1.$cnpErr1.$nr_persErr1.$suprErr1.$cotaErr1.$ap_receErr1.$ap_caldaErr1.$nr_repErr1.'</font>
                  </div>
            ';
        }
?>                             
    <table width=950>
            <tr bgcolor="#19AF62">                   
                   <td><font size=2 color="white"><center>Scara</center></font></td>
                   <td><font size=2 color="white"><center>Et</center></font></td>
                   <td><font size=2 color="white"><center>Ap</center></font></td>
                   <td><font size=2 color="white"><center>Nume</center></font></td>                               
                   <td><font size=2 color="white"><center>CNP</center></font></td>
                   <td><font size=2 color="white"><center>Nr<br>pers</center></font></td>
                   <td><font size=2 color="white"><center>Supr.</center></font></td>
                   <td><font size=2 color="white"><center>Cota<br>indiviza</center></font></td>
                   <td><font size=2 color="white"><center>Ap apa<br>rece</center></font></td>
                   <td><font size=2 color="white"><center>Ap apa<br>calda</center></font></td>                               
                   <td><font size=2 color="white"><center>Nr<br>rep</center></font></td>
                   <td><font size=2 color="white"><center>Centrala</center></font></td>
                   <td><font size=2 color="white"><center>Incalzire</center></font></td>
                   <td><font size=2 color="white"><center>Gaz</center></font></td>
                   <td><font size=2 color="white"><center>Ilum <br>Lift</center></font></td>
                   <td><font size=2 color="white"><center>Service <br>Lift</center></font></td>
                   <td><font size=2 color="white"><center>Optiuni</center></font></td>
            </tr>
           <?php
              $query = "SELECT L.*, S.scara FROM locatari AS L                                        
                                        JOIN scari AS S ON S.scara_id=L.scara_id
                                        WHERE L.asoc_id = '$asocId'
                             ORDER BY L.loc_id DESC";                                                                     
              $result = mysql_query($query) or die(mysql_query());
              $i=0;
              while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                   
                   //schimb culoarea la coloane
                   if ($i %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                        if ($editLoc == $row['loc_id']){                                
                                echo '<form name="formEdit" id="formEdit" method="post" action="index.php?link=w_locatari&asoc_id='.$asocId.'">';
                                    echo '<input type="hidden" name="salveaza" value="apasat" />';
                                    echo '<input type="hidden" name="lc" value="'.$editLoc.'" />';                                    
                                    
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>'.$row['etaj'].'</td>';                                                                      
                                    echo '<td>'.$row['ap'].'</td>';                                                                      
                                    echo '<td><input style="width:130px;" type="text" name="numeT" value="'.$row['nume'].'"></td>';                                    
                                    echo '<td><input style="width:110px;" type="text" name="cnpT" value="'.$row['cnp'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="nr_persT" value="'.$row['nr_pers'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="suprT" value="'.$row['supr'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="cotaT" value="'.$row['cota'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 type="text" name="ap_receT" value="'.$row['ap_rece'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 name="ap_caldaT" value="'.$row['ap_calda'].'"></td>';                                                                        
                                    echo '<td><input style="width:30px;" maxlength=1 name="nr_repT" value="'.$row['nr_rep'].'"></td>';                                                                                                            
                                    echo '<td ><select name="centralaT">
                                                      <option value="'.$row['centrala'].'">'.$row['centrala'].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                                                   
                                    echo '<td ><select name="incalzireT">';                                                 
                                                echo '<option value="'.$row['incalzire'].'">'.$row['incalzire'].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                                                  
                                    echo '<td ><select name="gazT">';                                                 
                                                echo '<option value="'.$row['gaz'].'">'.$row['gaz'].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                
                                    echo '<td ><select name="ilum_liftT">';                                                 
                                                echo '<option value="'.$row['ilum_lift'].'">'.$row['ilum_lift'].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';                                                
                                    echo '<td ><select name="service_liftT">';                                                 
                                                echo '<option value="'.$row['service_lift'].'">'.$row['service_lift'].'</option>';                                                
                                                echo '<option value="da">da</option><option value="nu">nu</option>';                                                
                                            echo '</select></td>';
                                                             
                                    echo '<td><center><a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></td>';
                                echo '</form>';
                        } else {                                                      
                                    echo '<td>'.$row['scara'].'</td>';
                                    echo '<td>et '.$row['etaj'].'</td>';                                                                      
                                    echo '<td>ap '.$row['ap'].'</td>';                                                                      
                                    echo '<td>'.$row['nume'].'</td>';                                      
                                    echo '<td>'.$row['cnp'].'</td>';
                                    echo '<td>'.$row['nr_pers'].'</td>';
                                    echo '<td>'.$row['supr'].'</td>';                                                                      
                                    echo '<td>'.$row['cota'].'</td>';                                                                      
                                    echo '<td>'.$row['ap_rece'].'</td>';                                    
                                    echo '<td>'.$row['ap_calda'].'</td>';                                    
                                    echo '<td>'.$row['nr_rep'].'</td>';                                    
                                    echo '<td>'.$row['centrala'].'</td>';                                    
                                    echo '<td>'.$row['incalzire'].'</td>';                                    
                                    echo '<td>'.$row['gaz'].'</td>';                                    
                                    echo '<td>'.$row['ilum_lift'].'</td>';                                    
                                    echo '<td>'.$row['service_lift'].'</td>';                                    
                                    echo '<td><center>
                                                <a style="font-size:12px;" href="index.php?link=w_locatari&asoc_id='.$asocId.'&edit='.$row['loc_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;                                                
                                          </center></td>';                                
                        }
                   echo '</tr>';
                   $i++;
              }
           ?>
    </table>          
</div></div> 

