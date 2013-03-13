<?php
	 include_once( 'Furnizori.class.php');
	 $change = null;
	 $order = isset($_GET['orderBy']) ? $_GET['orderBy'] : ''; // fur_id asoc_id data_inreg

/*******************  SELECTEAZA FURNIZORUL  *******************/
$sql = "SELECT * FROM furnizori";
$sql = mysql_query($sql) or die("Nu pot selecta furnizorii pt afisarea lor in lista furnizorilor<br />".mysql_error());
$furnizori = '';
while($row = mysql_fetch_array($sql)) {
	$furnizori .= '<option ';
	if(isset($_GET['fur_id']) && $row[0] == $_GET['fur_id']) $furnizori .= 'selected="yes" ';
	$furnizori .= 'value="'.$row[0].'">'.$row[1].'</option>';
}

/*******************  SELECTEAZA Asociatii  *******************/
$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die("Nu pot selecta asociatiile pt afisarea lor in lista asociatiilor<br />".mysql_error());
$asociatii = '';
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option ';
	if(isset($_GET['asoc_id']) && $row[0] == $_GET['asoc_id']) $asociatii .= 'selected="yes" ';
	$asociatii .= 'value="'.$row[0].'">'.$row[1].'</option>';
}

?>
<script type="text/javascript">
    function select_change(value){
        	window.location = "index.php?link=fisa_furnizori"+
					"&fur_id="+document.getElementById("change_furnizor").value+
					"&asoc_id="+document.getElementById("change_asociatie").value+
					"&orderBy="+document.getElementById("change_orderBy").value;
    }
</script>

<div id="content" style="float:left;">
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Alegeti Furnizorul:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_furnizor" onChange="select_change()">
				<?php echo '<option value="all">Toti</option>'; ?>
        <?php echo $furnizori; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Alegeti Asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_asociatie" onChange="select_change()">
				<?php echo '<option value="all">Toate</option>'; ?>
        <?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC"><label for="orderBy">Ordoneaza dupa: </label></td>
		<td width="215" align="left" bgcolor="#CCCCCC">
		<select name="orderBy" id="change_orderBy" onchange="select_change()">
			<option <?php if(!isset($_GET['orderBy'])) echo 'selected="selected" disabled="disabled"'; ?> value="">Default</option>
			<option <?php if(isset($_GET['orderBy']) && $_GET['orderBy']=='fur_id') echo 'selected="selected" disabled="disabled"'; ?> value="fur_id">Furnizori</option>
			<option <?php if(isset($_GET['orderBy']) && $_GET['orderBy']=='asoc_id') echo 'selected="selected" disabled="disabled"'; ?> value="asoc_id">Asociatii</option>
			<option <?php if(isset($_GET['orderBy']) && $_GET['orderBy']=='data_inreg') echo 'selected="selected" disabled="disabled"'; ?> value="data_inreg">Data inregistrari</option>
		</select>
		</td>
	</tr>
</table>
</div>

<form action="" method="post">
  <table width="1250" style="float:left; margin-top:50px; background-color:white;">
	<?php
	if(isset($_GET['asoc_id']) && $_GET['asoc_id']=='all') $_GET['asoc_id'] = NULL;
	if(isset($_GET['fur_id']) && $_GET['fur_id']=='all') $_GET['fur_id'] = NULL;
	$x ='getAllBy'.$order;
	if(!$_GET['asoc_id'] && !$_GET['fur_id']) die();
	foreach(Furnizori::$x(isset($_GET['asoc_id']) ? $_GET['asoc_id'] : NUll, isset($_GET['fur_id']) ? $_GET['fur_id'] : NUll) as $key => $row) {
	//	var_dump($row);

	switch($order) {
		case 'fur_id':
		if($change != $row[$order]) {
			$change = $row[$order];
	?>
	<tr><td></td></tr>
	<tr>
		<td colspan="6" align="left">Furnizor <?php echo $row['furnizor']; ?></td>
		<td colspan="5" align="right">Serviciu: <?php echo $row['serviciu']; ?> </td>
	</tr>
	<tr>
		<td bgcolor="#666666">Asociatie</td>
		<td bgcolor="#666666">Scara</td>
		<td bgcolor="#666666">Document</td>
		<td bgcolor="#666666">Explicatie</td>
		<td bgcolor="#666666">Data inregistrare</td>
		<td bgcolor="#666666">Data scadenta</td>
		<td bgcolor="#666666" width="70">Valoare</td>
		<td bgcolor="#666666" width="70">Penalizarii</td>
		<td bgcolor="#666666">Operator</td>
		<td bgcolor="#666666">IP</td>
	</tr>
	<?php
			$data = array();
			//$data [] = 'furnizor';
			$data [] = 'asociatie';
			$data [] = 'scara';
			$data [] = 'document';
			$data [] = 'explicatii';
			$data [] = 'data_inreg';
			$data [] = 'data_scadenta';
			$data [] = 'valoare';
			$data [] = 'penalizare';
			$data [] = 'nume';
			$data [] = 'ip';

			}
		break;

		case 'asoc_id':
		if($change != $row[$order]) {
		$change = $row[$order];
		?>
			<tr><td></td></tr>
			<tr>
				<td colspan="11" align="left">Asociatie <?php echo $row['asociatie']; ?></td>
			</tr>
			<tr>
				<td bgcolor="#666666">Furnizor</td>
				<td bgcolor="#666666">Scara</td>
				<td bgcolor="#666666">Document</td>
				<td bgcolor="#666666">Explicatie</td>
				<td bgcolor="#666666">Data inregistrare</td>
				<td bgcolor="#666666">Data scadenta</td>
				<td bgcolor="#666666" width="70">Valoare</td>
				<td bgcolor="#666666" width="70">Penalizarii</td>
				<td bgcolor="#666666">Operator</td>
				<td bgcolor="#666666">IP</td>
			</tr>
			<?php
			$data = array();
			$data [] = 'furnizor';
			//$data [] = 'asociatie';
			$data [] = 'scara';
			$data [] = 'document';
			$data [] = 'explicatii';
			$data [] = 'data_inreg';
			$data [] = 'data_scadenta';
			$data [] = 'valoare';
			$data [] = 'penalizare';
			$data [] = 'nume';
			$data [] = 'ip';
		}
		break;

		case 'data_inreg':
		if($change != $row[$order]) {
		$change = $row[$order];
		?>
			<tr><td></td></tr>
			<tr>
				<td colspan="11" align="left">Data Inregistrare <?php echo $row['data_inreg']; ?></td>
			</tr>
			<tr>
				<td bgcolor="#666666">Furnizor</td>
				<td bgcolor="#666666">Asociatie</td>
				<td bgcolor="#666666">Scara</td>
				<td bgcolor="#666666">Document</td>
				<td bgcolor="#666666">Explicatie</td>
				<td bgcolor="#666666">Data scadenta</td>
				<td bgcolor="#666666" width="70">Valoare</td>
				<td bgcolor="#666666" width="70">Penalizarii</td>
				<td bgcolor="#666666">Operator</td>
				<td bgcolor="#666666">IP</td>
			</tr>
			<?php
			$data = array();
			$data [] = 'furnizor';
			$data [] = 'asociatie';
			$data [] = 'scara';
			$data [] = 'document';
			$data [] = 'explicatii';
			//$data [] = 'data_inreg';
			$data [] = 'data_scadenta';
			$data [] = 'valoare';
			$data [] = 'penalizare';
			$data [] = 'nume';
			$data [] = 'ip';

		}
		break;

		default:
			if($change == NULL) {
			$change = 1;
			?>
			<tr><td></td></tr>
			<tr>
				<td bgcolor="#666666">Furnizor</td>
				<td bgcolor="#666666">Asociatie</td>
				<td bgcolor="#666666">Scara</td>
				<td bgcolor="#666666">Document</td>
				<td bgcolor="#666666">Explicatie</td>
				<td bgcolor="#666666">Data inregistrare</td>
				<td bgcolor="#666666">Data scadenta</td>
				<td bgcolor="#666666" width="70">Valoare</td>
				<td bgcolor="#666666" width="70">Penalizarii</td>
				<td bgcolor="#666666">Operator</td>
				<td bgcolor="#666666">IP</td>
			</tr>
			<?php
			$data = array();
			$data [] = 'furnizor';
			$data [] = 'asociatie';
			$data [] = 'scara';
			$data [] = 'document';
			$data [] = 'explicatii';
			$data [] = 'data_inreg';
			$data [] = 'data_scadenta';
			$data [] = 'valoare';
			$data [] = 'penalizare';
			$data [] = 'nume';
			$data [] = 'ip';

			}
	}
	?>
		<tr>
		<?php foreach ($data as $info) {
                    if ($info == 'valoare' || $info == 'penalizare') { ?>
                        <td bgcolor="#<?php echo ($key % 2) == 0 ? '999999' : 'cccccc' ; ?>" <?php echo $row[$info] >= 0 ? 'align="right"' : 'align="left"'; ?>><?php echo abs(round($row[$info], 2)); ?></td>
                    <?php } else { ?>
                        <td bgcolor="#<?php echo ($key % 2) == 0 ? '999999' : 'cccccc' ; ?>"><?php echo $row[$info]; ?></td>
                    <?php }
		 }// endforeach; ?>
	</tr>
	<?php }//end de la foreach?>
  </table>
</form>