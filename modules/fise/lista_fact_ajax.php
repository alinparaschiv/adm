<?php

$luna = strtotime(substr($_GET['luna'], -4).'-'.substr($_GET['luna'], 0, 2).'-01');
$asoc_id = $_GET['asoc_id'];
$scara_id = $_GET['scara_id'];
$lunaArray = array();



for($i=3; $i>=0; $i--) {
	$lunaCurenta = date('m-Y', strtotime('- '.$i.' month', $luna));
	$lunaArray [$lunaCurenta] = array();
	$servicii_s = 
"SELECT *, '$lunaCurenta' as lunaCurenta 
FROM (
	SELECT *
	FROM furnizori_as 
	WHERE asoc_id=$asoc_id 
	AND (scara_id=$scara_id OR scara_id IS NULL)
	) F_AS 
INNER JOIN furnizori_servicii FS ON F_AS.fur_id=FS.fur_id 
INNER JOIN servicii S ON FS.serv_id=S.serv_id 
LEFT OUTER JOIN (
	SELECT *, sum(cost) as costx
	FROM facturi 
	WHERE luna='$lunaCurenta'
	AND asoc_id=$asoc_id 
	AND (scara_id=$scara_id OR scara_id IS NULL)
	GROUP BY tipServiciu) F 
ON (S.serv_id=F.tipServiciu) 

ORDER BY FS.serv_id";


//var_dump($servicii_s); 
	$servicii_q = mysql_query($servicii_s) or die('Nu por afla ce facturi sunt pe luna curenta '.mysql_error().'<br />'.$servicii_s);
		
	while($servicii_r = mysql_fetch_assoc($servicii_q))
		$lunaArray [$lunaCurenta][$servicii_r['serv_id']] = $servicii_r;
}
//var_dump($lunaArray);die();

$lunaArrayT = array();
foreach ($lunaArray as $key => $subarr) {
	foreach ($subarr as $subkey => $subvalue) {
		$lunaArrayT[$subkey][$key] = $subvalue;
	}
}
//var_dump($lunaArrayT);
foreach ($lunaArrayT as $key => $subarr) {
	$flag = true;
	foreach ($subarr as $subkey => $subvalue) {
		$flag = ($subvalue['costx'] != NULL && $subvalue['costx'] != 0) ? false : $flag;
	}
	if ($flag) unset($lunaArrayT[$key]);
}

$lpCurenta_s = 'SELECT * FROM lista_plata WHERE scara_id='.$scara_id.' AND luna="'.date('m-Y', $luna).'" LIMIT 1';
$lpCurenta_q = mysql_query($lpCurenta_s) or die('Nu pot sa aflu daca LP de pe luna curenta este procesata <br />'.$lpCurenta_s);
$lpCurenta_r = mysql_fetch_assoc($lpCurenta_q);
?>

<table style="float: right;" id='ListaFacturi'>
	<thead>
		<tr>
			<th width="150">Serviciu</th>
			<?php foreach ($lunaArray as $key => $value): ?>
				<th><?php echo $key; ?></th>
			<?php endforeach; ?>
			<th><?php echo (($lpCurenta_r['procesata'] == 0) ? 'Actiuni' : ('<a href="index.php?link=lista_plata&asoc_id='.$asoc_id.'&scara_id='.$scara_id.'&luna='.date('m-Y', strtotime('+ 1 month', $luna)).'">Next</a>')); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($lunaArrayT as $key1 => $value1): ?>
		<?php $lastServ = $lunaArray[date('m-Y', $luna)][$key1]; ?>
		<tr class="serv-<?php echo $lastServ['serv_id']; ?>"> <?php
		switch ($key1) {
			case '21':
				$subvKey = '=41';
				break;
			case '35':
				$subvKey = ' IN (84, 90, 91, 92, 93, 107)';
				break;
			case '26':
				$subvKey = ' IN (39, 40)';
				break;
			
			default:
				$subvKey = '='.$key1;
				break;
		}

		$subventii_s = 'SELECT DISTINCT ap FROM subventii s JOIN locatari l ON s.loc_id=l.loc_id WHERE l.scara_id='.$scara_id.' AND s.serv_id'.$subvKey.' AND s.procent<100 ORDER BY cast(ap as UNSIGNED )' ;
		$subventii_q = mysql_query($subventii_s) or die('Nu pot afla daca pt serviciul curent exista subventii <br />'.$subventii_s); ?>
			<th>
				<div id="serviciu-<?php echo $lastServ['serv_id']; ?>" 
					<?php /*
					onmouseover="$(this).next().css('display','block');"
					onmouseout="$(this).next().css('display','none');"
					*/
					?>
					onclick="
if($('#serviciu-<?php echo $lastServ['serv_id']; ?>').css('font-weight') == 'bold')
	{
	$('#serviciu-<?php echo $lastServ['serv_id']; ?>').css('font-weight', 'normal');
	$(this).next().css('display','block');
	$.each($('#serviciu-<?php echo $lastServ['serv_id']; ?>').next().html().split(', '), function(index, value) {
		value = value ? value : 'inexistent';
		$('.ap.'+value).css('font-weight', 'bold'  ).css('color', 'red'  )}); 
	 }

else{
	$('#serviciu-<?php echo $lastServ['serv_id']; ?>').css('font-weight', 'bold');
	$(this).next().css('display','none');
	$.each($('#serviciu-<?php echo $lastServ['serv_id']; ?>').next().html().split(', '), function(index, value) {
		value = value ? value : 'inexistent';
		$('.ap.'+value).css('font-weight', 'normal').css('color', 'black')}); 
	}">
					<?php echo $lastServ['serviciu'].' - '.mysql_num_rows($subventii_q); ?>
					</div>
					<div id="tooltip-<?php echo $lastServ['serv_id']; ?>" style="display: none; width: 150px;"><?php 
					while ($subventii_r = mysql_fetch_assoc($subventii_q))  
						 echo $subventii_r['ap'].', '; 
					?></div>
				
			</th>
			
			<?php foreach ($value1 as $key2 => $value2): ?>
			<td>
				<?php echo ($value2['costx'] ? $value2['costx'] : 0); $lastVal = $value2; ?>
			</td>
			<?php endforeach; ?>
			
			<td><?php if ($lastVal['costx'] == NULL && $lpCurenta_r['procesata'] == 0) : 
					switch (strtolower($lastVal['serviciu'])) {
						case 'apa rece':
							echo '<a target="_blank" href="index.php?link=facturi_aparece&asoc_id='.$asoc_id.'&tipFactura=1">Adauga factura</a>';
							break;
						case 'iluminat':
							echo '<a target="_blank" href="index.php?link=facturi_iluminat&asoc_id='.$asoc_id.'&tipFactura=2&scara_id='.$scara_id.'">Adauga factura</a>';
							break;
						case 'apa calda':
							echo '<a target="_blank" href="index.php?link=facturi_apacalda&asoc_id='.$asoc_id.'&tipFactura=2&scara_id='.$scara_id.'">Adauga factura</a>';
							break;
						case 'incalzire':
							echo '<a target="_blank" href="index.php?link=facturi_incalzire&asoc_id='.$asoc_id.'&tipFactura=2&scara_id='.$scara_id.'">Adauga factura</a>';
							break;
						case 'gaz':
							echo '<a target="_blank" href="index.php?link=facturi_gaz&asoc_id='.$asoc_id.'&tipFactura=2&scara_id='.$scara_id.'">Adauga factura</a>';
							break;
						default:
							echo '<a target="_blank" href="index.php?link=facturi&asoc_id='.$asoc_id.'&tipFactura='.$lastVal['nivel'].'&furnizor='.$lastVal['fur_id'].'&scara_id='.$scara_id.'">Adauga factura</a>';
							break;
					}?>
				<?php elseif($lpCurenta_r['procesata'] == 0) : ?>
				<form action="index.php?link=proceseazaFacturi" method="post" target="_blank">
					<input type="hidden" name="factura" value="<?php echo $lastVal['fact_id']; ?>" />
					<input type="hidden" name="deProcesat" value="<?php echo $lastVal['procesata'] == 1 ? 'Revert' : 'OK'; ?>" />
					<input type="submit" value="<?php echo $lastVal['procesata'] == 1 ? 'Deproceseaza' : 'Proceseaza'; ?>" />
				</form>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>