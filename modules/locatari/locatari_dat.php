<?php
    $asocId = $_GET['asoc_id'];
    if ($_POST['asociatia'] != '') {
        $asocId = mysql_real_escape_string($_POST['asociatia']);
    } 
    $scaraId = $_POST['scara']; 
    if ($_GET['scara_id'] != '')
        $scaraId = mysql_real_escape_string($_GET['scara_id']);
    $scaraP = $scaraId;
    //echo $scaraP;
?>

<script language="javascript"> 
        function functionSubmit() {
             document.addForm.submit();        
        }
        function functionSubmit1() {
             document.addForm1.submit();        
        }
        function infoScari(obj) {
            window.location = "index.php?link=locatari_dat&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=locatari_dat&asoc_id="+obj            
        }
        var strArr=new Array();        
</script>

<?php

//$asocId = $_GET['asoc_id'];
$i = 0;
$query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
$result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
    $id = $row['loc_id'];
	if(($_POST['datorie_'.$id] == '') && ($_POST['penalizare_'.$id] == '') && ($_POST['cal_'.$id]=='')) { $i = 0; }
	elseif(($_POST['datorie_'.$id] <> '') && ($_POST['penalizare_'.$id] <> '') && ($_POST['cal_'.$id]<>'')) { $i = 0;  }
	else { $i = 1; }
   
}

if ($_POST['buton'] == 'apasat' && $i == 0) {
        $query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
        $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
            $id = $row['loc_id'];
            
            /* $q = "SELECT * FROM locatari_datorii WHERE loc_id='$id'";                        
            $res = mysql_query($q) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
            while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
                  $i=1;
                  $locErr[$id] = 'Informatiile pentru locatarul "'.$row['nume'].'" au fost deja introduse.<br>';                  
            } */

	if(($_POST['datorie_'.$id] <> '') && ($_POST['penalizare_'.$id] <> '') && ($_POST['cal_'.$id]<>'')) {   
            
            if(ereg('[^0-9.]', $_POST['datorie_'.$id])) {
                  $i=1;
                  $datorieErr[$id] = 'Campul "Datorie" pentru locatarul "'.$row['nume'].'" poate sa contina doar cifre.<br>';                  
            }
            if(ereg('[^0-9.]', $_POST['penalizare_'.$id])) {
                  $i=1;
                  $penalizareErr[$id] = 'Campul "Penalizare" pentru locatarul "'.$row['nume'].'" poate sa contina doar cifre.<br>';                  
            }
		}
            
        }        
    
        if ($i==0) {                         
            $query = "SELECT * FROM locatari WHERE asoc_id='$asocId'";
            $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                $id = $row['loc_id'];             
if(($_POST['datorie_'.$id] <> '') && ($_POST['penalizare_'.$id] <> '') && ($_POST['cal_'.$id]<>'')) {               
                $datorie = $_POST['datorie_'.$id];
                $penalizare = $_POST['penalizare_'.$id];
                $data = $_POST['cal_'.$id];                
                $cc = "INSERT INTO locatari_datorii (`loc_id`, `datorie`, `penalizare`, `data`) VALUES
                                                    ('$id', '$datorie', '$penalizare', '$data')";            
                mysql_query($cc) or die(mysql_error()); }
            }
            $mesaj = '<font color="green">Datele au fost introduse.</font>';
            unset ($_POST);
        } else {
                $mesaj = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }                                       
} else if ($_POST['buton'] == 'apasat') {
        $mesaj = '<font color="red">Trebuie sa completati toate campurile.</font>';
}



?>

<div id="mainCol" class="clearfix" >
    <div id="maincon"  style="width:620px; height:90px;">
            <form id="addForm1" name="addForm1" method="post" action="index.php?link=locatari_dat&asoc_id=<?php echo $asocId; ?>">                       
                Asociatia:                                
                <?php 
                        $query = "SELECT * FROM asociatii ORDER BY asociatie";
                        echo '<select name="asociatia" onchange="infoAsoc(this.value)"><option value="">Alegeti asociatia</option>';
                                $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                                    if ($asocId == $row['asoc_id'] )  echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                    else echo '<option  value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                }                                
                        echo '</select>';
                ?>
                <br />
                Scara:
                <?php                
                        $query = "SELECT SS.*, S.scara FROM scari_setari AS SS LEFT JOIN scari AS S ON SS.scara_id=S.scara_id WHERE SS.asoc_id='$asocId' ORDER BY SS.scara_id";                     
                        echo '<select name="scara">';
                                echo '<option  value="nimic">Alege Scara</option>';
                                $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                                    if ($_POST['scara'] == $row['scara_id'] )  
                                        echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                    else
                                    if ($_GET['scara_id'] == $row['scara_id'] )  echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';                                    
                                    else
                                        echo '<option  value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                }                                
                        echo '</select>';
                echo '<br /><br /><div id="buton"><a onclick="functionSubmit1()" style="">Afiseaza</a></div>';
                ?>
            </form>             
    </div>
<br /> <br /> 
<div id="maincon" style="width:600px;">
<?php
echo '<form id="addForm" name="addForm" method="post" action="index.php?link=locatari_dat&asoc_id='.$asocId.'">';
            echo '<div id="errorBox" style="">';                        
                if (!empty($datorieErr)) foreach ($datorieErr as $r) echo $r;
                if (!empty($penalizareErr)) foreach ($penalizareErr as $c) echo $c;
                if (!empty($locErr)) foreach ($locErr as $c) echo $c;
            echo'</font></div>
            ';
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
                <?php
                      $m = 0;
                      $query = "SELECT L.*, S.scara FROM locatari AS L JOIN scari AS S ON L.scara_id=S.scara_id WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
                      $result = mysql_query($query) or die(mysql_query());
                      $scara = '';
                      while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                                    
                            if ($row['scara'] != $scara) {
                                if ($scara != '') echo '</table>';
                                $scara = $row['scara'];
                                echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:600px;" border=0>
                                             <tr bgcolor="#19AF62"><td><font size=2 color="white"><center>Etaj</center></font></td><td><font size=2 color="white"><center>Scara</center></font></td><td><font size=2 color="white"><center>Apartament</center></font></td><td><font size=2 color="white"><center>Nume</center></font></td><td><font size=2 color="white"><center>Datorie</center></font></td><td><font size=2 color="white"><center>Penalizare</center></font></td><td><font size=2 color="white"><center>Data</center></font></td></tr>';
                            }
                            $locId = $row['loc_id'];
                            if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                            echo '<td ><center>'.$row['etaj'].'</center></td>';
                            echo '<td ><center>'.$row['scara'].'</center></td>';                                    
                            echo '<td ><center>ap '.$row['ap'].'</center></td>';
                            echo '<td width=100><center>'.$row['nume'].'</center></td>';                                    
                            echo '<td><input style="width:30px;" type="text" name="datorie_'.$locId.'" value="'.$_POST['datorie_'.$locId].'"></td>';
                            echo '<td><input style="width:30px;" type="text" name="penalizare_'.$locId.'" value="'.$_POST['penalizare_'.$locId].'"></td>';                                                                    
                            echo '<td><input style="width:80px;" type="text" name="cal_'.$locId.'" value="'.$_POST['cal_'.$locId].'" />';
                                  echo "<script> new tcal ({ 'formname': 'addForm', 'controlname': 'cal_".$locId."' }); </script></td>";                                                                        
                            $m++;
                      }                                
                ?>                        
        </table>
        <table cellspacing=1 style="margin:20px 0 0 0px; width:230px;" border=0>
                <tr><td></td><td><div id="buton"><a onclick="functionSubmit()" style="">Salveaza</a></div></td></tr>                        
                <?php if ($mesaj != '') echo '<tr><td></td><td>'.$mesaj.'</td></tr>'; ?>
        </table>                
        </form>
</div>
<br />

<?php /*********************************EDITEAZA SI STERGE apometre locatari ****************************************/ ?>

<script type="text/javascript">
function functionStergeFur(furnizor,fur_id){
     var answer = confirm ("Esti sigur ca vrei sa stergi informatiile pentru "+furnizor+" ?");
     if(answer)
        window.location = "<?php echo 'index.php?link=locatari_dat&asoc_id='.$asocId.'&sterge='; ?>"+fur_id;
}
function functionFormEdit(){
    document.formEdit1.submit();
}
</script>

<?php
    $editLoc = mysql_real_escape_string($_GET['edit']);
    $i = 0;
    if ($_POST['salveaza'] == 'apasat') {            
        $locId = mysql_real_escape_string($_POST['la']);            
        
        $datorieT = $_POST['datorieT'];
        $penalizareT = $_POST['penalizareT'];
        $dataT = $_POST['calT'];  
                  
        if(ereg('[^0-9.]', $_POST['datorieT'])) {
              $i=1;
              $datorieErrT = 'Campul "Datorie" pentru locatarul ales poate sa contina doar cifre.<br>';                                    
        }
        
        if(ereg('[^0-9.]', $_POST['penalizareT'])) {
              $i=1;
              $penalizareErrT = 'Campul "Penalizare" pentru locatarul ales poate sa contina doar cifre.<br>';  
        } 

        if ( $i == 0) {
                $query = "UPDATE locatari_datorii SET `datorie`='$datorieT', `penalizare`='$penalizareT', `data`='$dataT' WHERE loc_id='$locId'";                                
                mysql_query($query) or die('<br><br>Insertul nu a avut loc, contactati andreista@hotmail.com / 0744832335 **');                    
                $mesaj1 = '<font color="green">Datele au fost introduse.</font>';                    
                unset ($_POST);
        } else {
                $mesaj1 = '<font color="red">Trebuie sa rezolvati toate erorile inaintea salvarii datelor introduse.</font>';
        }
    }
?>
<div id="maincon" style="width:750px;">                            
<?php        
        if ($i == 1 && $_POST['salveaza'] == 'apasat') {
            echo '<div id="errorBox" style="">
                        <font color="red">'.$datorieErrT.$penalizareErrT.$mesaj1.'</font>
                  </div>
            ';
        }

$m = 0;
$query = "SELECT L.*,LA.*, S.scara FROM locatari_datorii AS LA                        
                        JOIN locatari AS L ON LA.loc_id=L.loc_id
                        JOIN scari AS S ON L.scara_id=S.scara_id
                        WHERE L.asoc_id='$asocId' ORDER BY L.loc_id ASC";
$result = mysql_query($query) or die(mysql_query());
$scara = '';
echo '<form id="formEdit1" name="formEdit1" method="post" action="index.php?link=locatari_dat&asoc_id='.$asocId.'">';
            echo '<input type="hidden" name="salveaza" value="apasat" />';
            echo '<input type="hidden" name="la" value="'.$editLoc.'" />';
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    if ($row['scara'] != $scara) {
                        if ($scara != '') echo '</table>';
                        $scara = $row['scara'];
                        echo '<table cellspacing=1 style="margin:0px 0 0 0px; width:700px;" border=0>
                                    <tr bgcolor="#19AF62"><td><font size=2 color="white"><center>Etaj</center></font></td><td><font size=2 color="white"><center>Scara</center></font></td><td><font size=2 color="white"><center>Apartament</center></font></td><td><font size=2 color="white"><center>Nume</center></font></td><td><font size=2 color="white"><center>Datorie</center></font></td><td><font size=2 color="white"><center>Penalizare</center></font></td><td><font size=2 color="white"><center>Data</center></font></td><td><font size=2 color="white"><center>Optiuni</center></font></td></tr>';
                    }    
                    if ($m %2 == 0) echo '<tr align=center>'; else echo '<tr align=center bgcolor="#f1f2f2">';
                    echo '<td ><center>'.$row['etaj'].'</center></td>';
                    echo '<td ><center>'.$row['scara'].'</center></td>';                                    
                    echo '<td ><center>ap '.$row['ap'].'</center></td>';                    
                    echo '<td width=100><center>'.$row['nume'].'</center></td>';

                    if ($editLoc == $row['loc_id']) {
                                    echo '<td><input style="width:30px;" type="text" name="datorieT" value="'.$row['datorie'].'"></td>';
                                    echo '<td><input style="width:30px;" type="text" name="penalizareT" value="'.$row['penalizare'].'"></td>';                                                                    
                                    echo '<td width=120><input style="width:80px;" type="text" name="calT" value="'.$row['data'].'" />';
                                          echo "<script> new tcal ({ 'formname': 'formEdit1', 'controlname': 'calT' }); </script></td>";                                                                                                           
                            echo '<td><center>';
                                    echo '<a style="font-size:12px; color:green; cursor:pointer;"  onclick="functionFormEdit()">[salveaza]</a></center></td>
     </form>';
                    } else {
                            echo '<td width=100><center>'.$row['datorie'].'</center></td>';
                            echo '<td width=100><center>'.$row['penalizare'].'</center></td>';
                            echo '<td width=100><center>'.$row['data'].'</center></td>';
                            echo '<td><center>
                                    <a style="font-size:12px;" href="index.php?link=locatari_dat&asoc_id='.$asocId.'&edit='.$row['loc_id'].'">[edit]</a>&nbsp;&nbsp;&nbsp;                                                    
                                    </center></td>';                                
                    }                            
                    $m++;                   
                }                                                                                  
                echo '</table>';     

?>

</div></div> 

