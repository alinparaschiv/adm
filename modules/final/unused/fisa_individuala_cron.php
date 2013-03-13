<script type="text/javascript">
function select_asoc(value) {
 window.location = "index.php?link=fisa_indv&asoc_id=" + value;	
}
function select_scara(value,value2) {
 window.location = "index.php?link=fisa_indv&asoc_id=" + value + "&scara_id=" + value2;	
}
function select_locatar(value,value2,value3) {
 window.location = "index.php?link=fisa_indv&asoc_id=" + value + "&scara_id=" + value2 + "&loc_id=" + value3;	
}

</script>
<?php
function verifica_factura($asoc_id, $scara_id, $loc_id, $uMasuraArr){
	$curr = date('m-Y');
	$lastMonth = date("m-Y", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	$i = 0;
	
	$sql = "SELECT * FROM facturi WHERE scara_id=".$scara_id." AND asoc_id=".$asoc_id." AND luna='".$lastMonth."'";
	$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela facturi<br />".mysql_error());
	
	while ($row = mysql_fetch_array($sql)){
		$sql1 = "SELECT * FROM servicii WHERE serv_id=".$row['tipServiciu'];
		$sql1 = mysql_query($sql1) or die ("Nu ma pot conecta la tabela servicii<br />".mysql_error());
		
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }

		echo'
		<tr bgcolor='.$color.'>
			<td>'.mysql_result($sql1, 0, 'serviciu').'</td>
			<td>'.$row['cost']/$row['cantitate'].'</td>
			<td>'.$uMasuraArr[mysql_result($sql1, 0, 'unitate')].'</td>
			<td>'.$row['ppu'].'</td>
			<td> Total locatar </td>
			<td>'.$row['serieFactura'].' / '.$row['numarFactura'].'</td>
			<td>'.$row['cost'].'</td>
			<td>'.$uMasuraArr[mysql_result($sql1, 0, 'unitate')].'</td>
			<td>'.$row['ppu'].'</td>
			<td> Total Lei </td>
		</tr>';
		$i++;
	}
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '<tr bgcolor='.$color.'><td colspan="10">&nbsp;</td></tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Luna Curenta</td>
		<td colspan="3">&nbsp;</td>
		<td>$total/om/luna</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Restante</td>
		<td colspan="3">&nbsp;</td>
		<td>$restante</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Penalizari</td>
		<td colspan="3">&nbsp;</td>
		<td>$penalizari</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>TOTAL GENERAL</td>
		<td colspan="3">&nbsp;</td>
		<td>$total/general/om/luna</td>
		<td colspan="4">&nbsp;</td>
		<td>$total/general/luna</td>
	</tr>';
	$i++;
}

function creeaza_tabel_fisa($asoc_id, $scara_id, $loc_id ){
	$i = 0;
	
	$sql = "SELECT * FROM locatari WHERE loc_id=".$loc_id." AND scara_id=".$scara_id." AND asoc_id=".$asoc_id;
	$sql = mysql_query($sql) or die ("Nu ma pot conecta la tabela locatari<br />".mysql_error());
	
	$nr_pers = mysql_result($sql, 0, 'nr_pers');
	$suprafata = mysql_result($sql, 0, 'supr');
	$cota = mysql_result($sql, 0, 'cota');
	$nr_apometre_rece = mysql_result($sql, 0, 'ap_rece');
	$nr_apometre_cald = mysql_result($sql, 0, 'ap_calda');
	$nr_repartitoare = mysql_result($sql, 0, 'nr_rep');
	$centrala = mysql_result($sql, 0, 'centrala');
	$incalzire = mysql_result($sql, 0, 'incalzire');
	$gaz = mysql_result($sql, 0, 'gaz');
	$lumina_lift = mysql_result($sql, 0, 'ilum_lift');
	$service_lift = mysql_result($sql, 0, 'service_lift');
	
	if ($nr_apometre_rece != 0){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Apa rece</td>
		</tr>';
		$i++;
		
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Diferenta apa rece</td>
		</tr>';	
		$i++;
	}
	
	if ($centrala != "da"){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Apa calda</td>
		</tr>';
		$i++;
		
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Diferenta apa calda</td>
		</tr>';	
		$i++;
	}
	
	if ($incalzire != "nu"){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Incalzire</td>
		</tr>';
		$i++;
	}
	
	if($gaz != "nu"){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Gaz</td>
		</tr>';
		$i++;
	}
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '
	<tr bgcolor='.$color.'>
		<td colspan="10">Iluminat comun</td>
	</tr>';
	$i++;
	
	if($lumina_lift != "nu"){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Iluminat lift</td>
		</tr>';
		$i++;
	}
	
	if($service_lift != "nu"){
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
		echo '
		<tr bgcolor='.$color.'>
			<td colspan="10">Service lift</td>
		</tr>';
		$i++;
	}
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '
	<tr bgcolor='.$color.'>
		<td colspan="10">Tarif administrare</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '
	<tr bgcolor='.$color.'>
		<td colspan="10">Curatenie $de_luat_luna</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '
	<tr bgcolor='.$color.'>
		<td colspan="10">Fond rulment</td>
	</tr>';
	$i++;
	
		if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo '<tr bgcolor='.$color.'><td colspan="10">&nbsp;</td></tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Luna Curenta</td>
		<td colspan="3">&nbsp;</td>
		<td>$total/om/luna</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Restante</td>
		<td colspan="3">&nbsp;</td>
		<td>$restante</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>Penalizari</td>
		<td colspan="3">&nbsp;</td>
		<td>$penalizari</td>
		<td colspan="5">&nbsp;</td>
	</tr>';
	$i++;
	
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	echo'
	<tr bgcolor='.$color.'>
		<td>TOTAL GENERAL</td>
		<td colspan="3">&nbsp;</td>
		<td>$total/general/om/luna</td>
		<td colspan="4">&nbsp;</td>
		<td>$total/general/luna</td>
	</tr>';
	$i++;
//	echo $nr_pers.' '.$suprafata.' '.$cota.' '.$nr_apometre_rece.' '.$nr_apometre_cald.' '.$nr_repartitoare.' '.$centrala.' '.$incalzire.' '.$gaz.' '.$lumina_lift.' '.$service_lift;

}

////////////////////////////////////////////////////////////////////////////////////
$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
}

if($_GET['asoc_id']<>null) {
	$sql2 = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id'];
	$sql2 = mysql_query($sql2);
	while($row2 = mysql_fetch_array($sql2)) {
		$scari .= '<option value="'.$row2[0].'">Bloc '.$row2[5].', scara '.$row2[2].'</option>';	
	}	
}

if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)) {
	$sql3 = "SELECT * FROM locatari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id=".$_GET['scara_id'];
	$sql3 = mysql_query($sql3);
	while($row3 = mysql_fetch_array($sql3)) {
		$locatari .= '<option value="'.$row3[0].'">'.$row3[3].'</option>';	
	}	
}

////////////////////////////////////////////////////////////////////////////////////
?>
<style type="text/css">

thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { width:100%; border:none; height:100%; }
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
    	<td width="173" align="left" bgcolor="#CCCCCC">(1/3) Alegeti asociatia:</td>
  <td width="215" align="left" bgcolor="#CCCCCC"><select onChange="select_asoc(this.value)">
        		<?php  if($_GET['asoc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Asociatia cu nr. '.$_GET['asoc_id'].'</option>';   }?>
        		<?php echo $asociatii; ?>
            </select></td>
    </tr>
    <?php if($_GET['asoc_id']<>null):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(2/3) Alegeti scara:</td>
  <td align="left" bgcolor="#CCCCCC"><select onChange="select_scara(<?php  echo $_GET['asoc_id']; ?>,this.value)">
        		<?php  if($_GET['scara_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Scara cu nr. '.$_GET['scara_id'].'</option>';   }?>
        		<?php  echo $scari; ?>
            </select></td>
    </tr>
    <?php endif;?>
      <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null)):?>
    <tr>
    	<td align="left" bgcolor="#CCCCCC">(3/3) Alegeti locatarul:</td>
        <td align="left" bgcolor="#CCCCCC"><select onChange="select_locatar(<?php  echo $_GET['asoc_id']; ?>,<?php  echo $_GET['scara_id']; ?>,this.value)">
        <?php  if($_GET['loc_id']==null)  { echo '<option value="">----Alege----</option>';    }  else { echo '<option value="">Locatar cu nr. '.$_GET['loc_id'].'</option>';   }?>
        		<?php echo $locatari; ?>
            </select></td>
    </tr>
    <?php endif;?>
</table>
    


</div>

  <?php if(($_GET['asoc_id']<>null) && ($_GET['scara_id']<>null) && ($_GET['loc_id']<>null)):?>
 	
  <form action="" method="post">
  
  <div id="print">
  	<a href="#">printeaza</a>
  </div>
<table width="950" style="top:250px; background-color:white;">
<thead style="font-weight:bold;" valign="top">
    <tr>
        <td bgcolor="#666666">&nbsp;</td>
        <td bgcolor="#666666" colspan="4">Sume / apartament</td>
        <td bgcolor="#666666" colspan="5">Sume Generale</td>
    </tr>
    <tr bgcolor="#CCCCCC" valign="middle">
        <td style="color:#000000">Produse facturate</td>
        <td style="color:#000000">Cantitatea<br /> facturata</td>
        <td style="color:#000000">U.M.</td>
        <td style="color:#000000">Pret unitar</td>
        <td style="color:#000000">Total lei</td>
        <td style="color:#000000">Act doveditor<br />Serie / Nr</td>
        <td style="color:#000000">Cantitatea<br />facturata<br />per total</td>
        <td style="color:#000000">U.M.</td>
        <td style="color:#000000">Pret unitar</td>
        <td style="color:#000000">Total lei</td>    
    </tr>
</thead>
<tbody align="left">
	<?php //creeaza_tabel_fisa($_GET['asoc_id'], $_GET['scara_id'], $_GET['loc_id']); ?>
	<?php verifica_factura($_GET['asoc_id'], $_GET['scara_id'], $_GET['loc_id'],$uMasuraArr); ?>
</tbody>
</table>
<!-- <a href="#">print</a> -->
</form>
<?php endif; ?>