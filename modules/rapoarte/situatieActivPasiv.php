<?php
//Afisez meniul pentru selectarea asociatiei
$obtiuniSelectareAsociatii = '';
$asocSql = "SELECT asoc_id, asociatie FROM asociatii ORDER BY administrator_id, asoc_id";
$asocQuery = mysql_query($asocSql) or die(mysql_error() .'<br />'.$asocSql);
while($asocRezult = mysql_fetch_assoc($asocQuery)) 
	$obtiuniSelectareAsociatii .= 
    '<option 
      value="'.$asocRezult['asoc_id'].'" 
      '.((isset($_GET['asoc_id']) && $_GET['asoc_id']==$asocRezult['asoc_id']) ? 'selected="selected"' : '').'>'.
        $asocRezult['asociatie']
    .'</option>';

?>

<script type="text/javascript">
function select_asoc(value) {
  window.location = "index.php?link=situatieActivPasiv&asoc_id=" + value;
}
</script>

<table width="400">
	<tr>
    <td width="173" align="left" bgcolor="#CCCCCC">Alegeti asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
      <select onChange="select_asoc(this.value)">
      	<?php echo $obtiuniSelectareAsociatii; ?>
      </select></td>
    </tr>
</table>

<?php
if(!isset($_GET['asoc_id']))
  exit();
// Aflu ultima LP pentru care s-a procesat LP pt toate scarile

$ultemeleListeProcesateSql    = "SELECT explicatie, data FROM fisa_cont WHERE asoc_id={$_GET['asoc_id']} AND act='LP' GROUP BY explicatie ORDER BY data DESC LIMIT 0, 1";
$ultemeleListeProcesateQuery  = mysql_query($ultemeleListeProcesateSql) or die(mysql_error(). '<br />' .$ultemeleListeProcesateSql);
$ultemeleListeProcesateRezult = mysql_fetch_assoc($ultemeleListeProcesateQuery);

//variabileGlobale
$totalDebiteAsociatie = 0;
$totalPenalizariAsociatie = 0;



//Aflu datoriile locatarilor
$ultimaListaPlataSql = sprintf(
  "SELECT CONCAT(l.nume,' (Ap. ', l.ap, ')') as denumire, 
    round(fc.valoare+fc.datorie, 2) as debite, 
    total_penalizari as penalizari
  FROM locatari l
  LEFT OUTER JOIN (
    SELECT * FROM fisa_cont WHERE explicatie='%s' AND asoc_id=%d) fc
    ON l.loc_id=fc.loc_id
  WHERE l.asoc_id=%d
  ORDER BY l.scara_id, l.ap",
  $ultemeleListeProcesateRezult['explicatie'],
  $_GET['asoc_id'],
  $_GET['asoc_id']);
?>

<?php echo afiseazaRaportSql($ultimaListaPlataSql, 'Obligatii de plata'); ?>

<?php
//Aflu facturile care inca nu au fost repartizate la locatari

$facturiNerepartizateSql = sprintf(
  "SELECT s.serviciu, f.serieFactura, f.numarFactura, f.cost as debit, 0 as penalizare
  FROM facturi f
  LEFT OUTER JOIN servicii s ON f.tipServiciu=s.serv_id
  WHERE asoc_id=%d
  AND STR_TO_DATE(luna, '%s')>='%s'",
  $_GET['asoc_id'],
  '%m-%Y',
  date('Y-m-00', strtotime($ultemeleListeProcesateRezult['data']))
  );
?>

<?php echo afiseazaRaportSql($facturiNerepartizateSql, 'Facturi Nerepartizate'); ?>


<?php
//aflu soldul final din registru jurnal
$sql_total ="SELECT 0 as denumire, sum(valoare) as debite, sum(penalizare) as penalizari FROM (
SELECT 0 as asoc_id, 0 as data_inreg, 0 as document, 0 as valoare, 0 as penalizare, 0 as tip, 0 as nume, 0 as explicatie
UNION ALL
SELECT 0 as asoc_id, 0 as data_inreg, 0 as document, 0 as valoare, 0 as penalizare, 1 as tip, 0 as nume, 0 as explicatie
UNION ALL
SELECT fisa.`asoc_id`, fisa.data_inreg, fisa.document, fisa.valoare, fisa.penalizare, 
0 as tip, f.furnizor as nume, 'plata' as explicatie
FROM `fisa_furnizori` fisa, furnizori f
WHERE explicatii='Ordin Plata' AND fisa.fur_id=f.fur_id AND fisa.asoc_id=".$_GET['asoc_id']." AND fisa.data_inreg < '".$ultemeleListeProcesateRezult['data']."'
UNION ALL
SELECT C.asoc_id, C.data_inserarii as data_inreg, concat(C.chitanta_serie,'/',C.chitanta_nr) as document,
CASE C.tip_plata WHEN 'Penalizare' THEN 0 ELSE round(C.suma, 2) END as valoare,
CASE C.tip_plata WHEN 'Penalizare' THEN round(C.suma, 2) ELSE 0 END as penalizare,
1 as tip, L.nume, C.reprezentand
FROM `casierie` C
LEFT OUTER JOIN locatari L ON C.loc_id=L.loc_id
WHERE C.asoc_id=".$_GET['asoc_id']." AND C.data_inserarii < '".$ultemeleListeProcesateRezult['data']."'
UNION ALL
SELECT asoc_id, 0 as data_inreg, 0 as document, suma as valoare, 0 as penalizare, 1 as tip, 0 as nume, 0 as explicatie
FROM casierie
WHERE asoc_id=".$_GET['asoc_id']." AND scara_id IS NULL AND loc_id IS NULL) as X";

?>

<?php echo afiseazaRaportSql($sql_total, 'Sold in casa'); ?>


<?php
//Aflu Debitele la furnizori
require ('modules/fise/Furnizori.class.php');
$pasivArray = Furnizori::getPlati('fur_is', $_GET['asoc_id']);
$pasiv = array();
foreach ($pasivArray as $value) {
  if (!isset($pasiv[$value['serviciu']])) {
    $pasiv[$value['serviciu']]['debite']     = 0;
    $pasiv[$value['serviciu']]['penalizari'] = 0;
  }
  
  $pasiv[$value['serviciu']]['denumire']   = $value['serviciu'];
  $pasiv[$value['serviciu']]['debite']     += $value['valoare'];
  $pasiv[$value['serviciu']]['penalizari'] += $value['penalizare'];
}
$totalDebite = 0;
$totalPenalizari = 0;
?>

<table>
  <tbody>
    <?php foreach ($pasiv as $row) : ?>
    <tr>
      <td><?php echo $row['denumire']; ?></td>
      <td><?php echo round($row['debite'], 2); ?></td>
      <td><?php echo round($row['penalizari'], 2); ?></td>
    </tr>
    <?php 
      $totalDebite     += $row['debite'];
      $totalPenalizari += $row['penalizari'];
    ?>
    <?php endforeach; ?>
  </tbody>
  <thead>
    <tr>
      <td width="200">Total Pasiv:</td>
      <th width="50"><?php echo $totalDebite; ?></th>
      <th width="50"><?php echo $totalPenalizari; ?></th>
    </tr>
  </thead>
</table>
<?php
$totalDebiteAsociatie -= $totalDebite;
$totalPenalizariAsociati -= $totalPenalizari;
?>


<table>
  <tr>
    <td width="200">Total:</td>
    <th width="50"><?php echo $totalDebiteAsociatie; ?></th>
    <th width="50"><?php echo $totalPenalizariAsociati; ?></th>
  </tr>
</table>

<?php

function afiseazaRaportSql($sql, $titlu) {
  global $totalDebiteAsociatie, $totalPenalizariAsociati;
  $query = mysql_query($sql) or die(mysql_error().'<br />'.$sql);

  $totalDebite = 0;
  $totalPenalizari = 0;

  ob_start();
?>
<table>
  <tbody style="display: none;">
    <?php while ($row = mysql_fetch_assoc($query)) : ?>
    <tr>
      <td><?php echo $row['denumire']; ?></td>
      <td><?php echo $row['debite']; ?></td>
      <td><?php echo $row['penalizari']; ?></td>
    </tr>
    <?php 
      $totalDebite     += $row['debite'];
      $totalPenalizari += $row['penalizari'];
    ?>
    <?php endwhile; ?>
  </tbody>
  <thead>
    <tr>
      <td width="200"><?php echo $titlu; ?>:</td>
      <th width="50"><?php echo $totalDebite; ?></th>
      <th width="50"><?php echo $totalPenalizari; ?></th>
    </tr>
  </thead>
</table>
<?php
$totalDebiteAsociatie += $totalDebite;
$totalPenalizariAsociati += $totalPenalizari;

  return ob_get_clean();
}