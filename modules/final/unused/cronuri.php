<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=genereazaListe&asoc_id=" + value;	
}
function select_scara(value,value2) {
 window.location = "index.php?link=genereazaListe&asoc_id=" + value + "&scara_id=" + value2;	
}
</script>



<?php



////////////////////////////////////////////////////////////////////////////////////
if ($_GET['asoc_id']<>null){
	$sql = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id'];
} else {
	$sql = "SELECT * FROM asociatii";
}
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
}

if($_GET['asoc_id']<>null) {
	if ($_GET['scara_id']<>null){ 
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id']; 
	} else {
		$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	}
	$sql2 = mysql_query($sql2) or die ("Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());
	while ($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">'.$row2[2].'</option>';	
	}	
}
?>

<style type="text/css">

thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { border:none; height:100%; }
tbody tr.newline td { border:solid 1px #0CC;   }
tfoot { color:#FFF; }
.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
.addnew2 {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:95px; margin-top:-9px;  }
tr.newline input { text-align:center; }
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
#print {float:left; margin-left:900px; margin-top:15px;}
#plata {float:left; margin-left:5px; margin-top:107px; position:absolute;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/2) Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 
					
					$asociatia = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
					$asociatia = mysql_query($asociatia) or die ("Nu pot selecta asociatiile<br />".mysql_error());
					
					echo '<option value="">'.mysql_result($asociatia, 0, 'asociatie').'</option>';   
				}?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/2) Alegeti scara:</td>
  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { 
					$scara = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
					$scara = mysql_query($scara) or die ("Nu pot selecta scarile<br />".mysql_error());
					
					echo '<option value="">'.mysql_result($scara, 0, 'scara').'</option>';
				}?>
        		<?php  echo $scari; ?>
            </select></td>
    </tr>
    <?php endif;?>
 
</table>
</div>

<?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
 	
<form action="" method="post">
    <table width="700" style="top:250px; background-color:white; float:left;">
        <thead style="font-weight:bold;" valign="top">
            <tr>
                <td bgcolor="#666666" colspan="3">Selectati listele pe care doriti sa le generati pentru asociatia <?php echo $_GET['asoc_id'].', scara '.$_GET['scara_id'] ?></td>
            </tr>
        </thead>
        <tbody align="left" style="padding-left:10px">
            <tr bgcolor="#CCCCCC">
            	<td width="175"><input type="checkbox" name="loc" value="loc"></td>
                <td>Lista de Plata</td>
            </tr>
            <tr bgcolor="#EEEEEE">
            	<td colspan="2" align="right"><input type="submit" value="Genereaza" /></td>
            </tr>
        </tbody>
    </table>
<!-- <a href="#">print</a> -->
</form>
<?php endif; ?>