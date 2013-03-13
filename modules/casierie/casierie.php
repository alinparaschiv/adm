<script type="text/javascript"> 
			$(function () {
				$('input#id_search').quicksearch('table#quicksearch tbody tr');
				});
</script> 

<?php
function get_locatari (){
$sql = "SELECT locatari.loc_id, locatari.scara_id, locatari.asoc_id, locatari.nume, locatari.ap,
				asociatii.asociatie,
				scari.scara, scari.nr, scari.bloc,
				strazi.strada
		FROM locatari, asociatii, scari, strazi
		WHERE locatari.asoc_id=asociatii.asoc_id AND locatari.scara_id=scari.scara_id AND scari.strada=strazi.str_id";
$sql = mysql_query($sql) or die("Imi ceri prea multe <br />".mysql_error());
$i = 1;

$lunaApo = ((int) date('d')) > 15 ? date('m-Y') : date('m-Y', strtotime(date('Y').'-'.(date('m')-1).'-10'));

while($row = mysql_fetch_assoc($sql)) {
	if ($i%2 == 0) {  $color = "#CCCCCC"; } else { $color="#EEEEEE";  }
	
	echo' 
	<tr bgcolor="'.$color.'">
	  <td align="center">'.$i.'</td>
	  <td align="center">'.$row['nume'].' Ap.'.$row['ap'].'</td>
	  <td align="center">'.$row['asociatie'].'</td>
	  <td align="center">'.$row['strada'].'</td>
	  <td align="center">'.$row['nr'].'</td>
	  <td align="center">'.$row['bloc'].'</td>
	  <td align="center">'.$row['scara'].'</td>
	  <td align="center">
		<a target="_blank" href="index.php?link=plata&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&loc_id='.$row['loc_id'].'">Plata</a> 
		<a target="_blank" href="index.php?link=fisa_indv&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&loc_id='.$row['loc_id'].'">FInd</a>
		<a target="_blank" href="index.php?link=fisa_cons&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&locatar='.$row['loc_id'].'">FCons</a>
		<a target="_blank" href="index.php?link=fisa_cont&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&locatar='.$row['loc_id'].'">FCont</a>

		<a target="_blank" href="index.php?link=locatari_apometre&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'&luna='.$lunaApo.'&editeaza='.$row['loc_id'].'">Apo</a>
		<a target="_blank" href="index.php?link=lista_plata&asoc_id='.$row['asoc_id'].'&scara_id='.$row['scara_id'].'">LP</a>
	  </td>
	</tr>';
	
	$i++;
	}
}
?>
<script type="text/javascript">
function check(id) {
	
	if(id.value == "Scrie aici....") {  id.value = '';  } 
		
	}
</script>

<br  />
<form action="#" id="searchform" style="float:left">
	<input type="text" name="search" size="40" id="id_search" value="Scrie aici...." onclick="check(this)"  style="background-color: #fff; -moz-border-radius: 5px; -webkit-border-radius: 5px; border: 2px solid #0CC; padding: 5px; width:930px; font-size:16px; font-weight:bold; color:#999;" />
</form>
<table width="950" style="font-size: 12px; position:absolute; top:200px; background-color:white;" id="quicksearch">
<thead style="color:#FFFFFF">
<tr>
  <td bgcolor="#666666">ID</td>
  <td bgcolor="#666666">Nume</td>
  <td bgcolor="#666666">Asociatie</td>
  <td bgcolor="#666666">Strada</td>
  <td bgcolor="#666666">Numar</td>
  <td bgcolor="#666666">Bloc</td>
  <td bgcolor="#666666">Scara</td>
  <td bgcolor="#666666">&nbsp;</td>
  </tr>
</thead>
<tbody>
<?php  get_locatari();  ?>
</tbody>
</table>