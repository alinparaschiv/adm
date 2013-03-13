<?php
    $asocId = $_GET['asoc_id'];
    if ($_POST['asociatia'] != '') {
        $asocId = mysql_real_escape_string($_POST['asociatia']);
    } 
    $scaraId = $_POST['scara']; 
    if ($_GET['scara_id'] != '')
        $scaraId = mysql_real_escape_string($_GET['scara_id']);
    
    $raport = $_POST['raport'];
    $luna = $_POST['luna'];
    
    if ($raport == 'Lista de Plata'){
        ?>
        <script type="text/javascript">
                window.open( "modules/pdf/lista.php?asoc_id=<?php echo $asocId; ?>&luna=<?php echo $luna; ?>&scara_id=<?php echo $scaraId; ?>" )
        </script>             
    <?php
    }                
?>  



<script language="javascript"> 

        function functionSubmit1() {
             document.addForm1.submit();        
        }
        function infoScari(obj) {
            window.location = "index.php?link=rapoarte&asoc_id=<?php echo $asocId; ?>&scara="+obj
        }
        function infoAsoc(obj) {
            window.location = "index.php?link=rapoarte&asoc_id="+obj            
        }
        
        var strArr=new Array();        
</script>
        
<div id="mainCol" class="clearfix" >
<div id="maincon"  style="width:620px; height:150px;">
        <table>
        <form id="addForm1" name="addForm1" method="post" action="index.php?link=rapoarte&asoc_id=<?php echo $asocId; ?>">                       
                    <tr>
                    <td>Asociatia:</td><td>
                    <?php 
                            $query = "SELECT * FROM asociatii ORDER BY asociatie";
                            echo '<select name="asociatia" onchange="infoAsoc(this.value)">';
                                    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                                    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                                        if ($asocId == $row['asoc_id'] )  echo '<option selected="selected" value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                        else echo '<option  value="'.$row['asoc_id'].'">'.$row['asociatie'].'</option>';
                                    }                                
                            echo '</select></td></tr>';
                    ?>
                    <tr>
                    <td>Scara:</td><td>
                    <?php                
                            $query = "SELECT SS.*, S.scara FROM scari_setari AS SS LEFT JOIN scari AS S ON SS.scara_id=S.scara_id WHERE SS.asoc_id='$asocId' ORDER BY SS.scara_id";
                            //echo '<select name="scara" onchange="infoScari(this.value)">';
                            echo '<select name="scara">';
                                    echo '<option  value="">Alege Scara</option>';
                                    $result = mysql_query($query) or die('<br><br>A avut loc o eroare, contactati andreista@hotmail.com / 0744832335 ');                              
                                    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {                              
                                        if ($_POST['scara'] == $row['scara_id'] )  
                                            echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                        else
                                        if ($_GET['scara_id'] == $row['scara_id'] )  echo '<option selected="selected" value="'.$row['scara_id'].'">'.$row['scara'].'</option>';                                    
                                        else
                                            echo '<option  value="'.$row['scara_id'].'">'.$row['scara'].'</option>';
                                    }                                
                            echo '</select></td></tr>';
                    echo '<tr><td>Raport:</td> <td><select name="raport">';        
                        echo '<option  value="nimic">Alege</option>';
                        if ($_POST['raport'] != '' && $_POST['raport'] != 'nimic') 
                            echo '<option selected="selected" value="'.$_POST['raport'].'">'.$_POST['raport'].'</option>';    
                        echo '<option  value="Lista de Plata">Lista de Plata</option>';
                    echo '</select></td></tr>';         
                    echo '<tr><td>Luna:</td> <td><select name="luna">';                        
                        if ($_POST['luna'] != '' && $_POST['luna'] != 'nimic') 
                            echo '<option selected="selected" value="'.$_POST['luna'].'">'.$_POST['luna'].'</option>';                            
                        for ($i=0; $i<12; $i++) 
                            echo '<option  value="'.date('n-Y', mktime(0,0,0,date("n")-$i, 1, date("Y"))).'">'.date('n-Y', mktime(0,0,0,date("n")-$i, 1, date("Y"))).'</option>';
                            
                    echo '</select></td></tr>'; 
                    echo '<tr><td></td><td><div id="buton"><a onclick="functionSubmit1()" style="">Afiseaza</a></div></td></tr></table>';
                    ?>
                    <br />
                </div>
                <br />
        </form>  
</div>

