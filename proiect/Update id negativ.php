

<?php
require 'componente/config.php';

$select="SELECT ff.fact_id, f.fact_id fact_id_ok
        FROM fisa_furnizori ff, facturi f
        WHERE f.dataScadenta = DATE_FORMAT( ff.data_scadenta,  '%d-%m-%Y' ) 
        AND ff.valoare = f.cost
        AND ff.explicatii =  'Factura'
        AND ff.asoc_id = f.asoc_id
        AND ff.scara_id = f.scara_id
        AND ff.fact_id <> f.fact_id
        AND ff.fact_id <0";
$selectr=  mysql_query($select);
while($row = mysql_fetch_assoc($selectr)){
  
  $update="UPDATE fisa_furnizori 
          SET fact_id=".$row['fact_id_ok'].
          "WHERE fact_id=".$row['fact_id'];
  var_dump($update);
  //mysql_query($update);
}
?>

0749010570