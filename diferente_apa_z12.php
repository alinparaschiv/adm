<?php
require('componente/config.php');
require('modules/final/preprocesareApa.php');

$interogareAsociatieZ12 = "SELECT asoc_id
          FROM asociatii
          WHERE asociatie like '%Z12-Z13%'";
$rezultatInterogareAsociatie = mysql_query($interogareAsociatieZ12) or 
  die('Nu gasesc asociatia Z12-Z13');

$vectorAsociatie = mysql_fetch_assoc($rezultatInterogareAsociatie);

echo 'Asociatia: '.$vectorAsociatie['asoc_id'];

$interogareDataZ12="SELECT luna
          FROM lista_plata
          WHERE asoc_id =".$vectorAsociatie['asoc_id'].
          " AND procesata = 0
          GROUP by asoc_id";
  
$rezultatInterogareData = mysql_query($interogareDataZ12) or
  die('Nu gasesc data pentru asociatia Z12-Z13');

$vectorData = mysql_fetch_assoc($rezultatInterogareData);

echo 'Data: '.$vectorData['luna'];

transferDiferentePretApa($vectorAsociatie['asoc_id'], $vectorData['luna']);
?>
