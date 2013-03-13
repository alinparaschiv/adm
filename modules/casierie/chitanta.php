<?php
  error_reporting(E_ALL);
ini_set('display_errors', '1');
 session_start();
include_once '../../componente/config.php';
include_once("../fise/Penalizare.class.php");
include_once '../../Util.php';

//==============================================================

//==============================================================
define("_JPGRAPH_PATH", '../../jpgraph_5/jpgraph/'); // must define this before including mpdf.php file
$JpgUseSVGFormat = true;
define('_MPDF_URI','../pdf/mpdf/'); 	// must be  a relative or absolute URI - not a file system path

//http://mpdf1.com/manual/index.php

ini_set("memory_limit","32M");
ini_set('max_execution_time', 500);

$id_chitanta = isset($_GET['id']) ? $_GET['id'] : (isset($_SESSION['chitanta_id']) ? $_SESSION['chitanta_id'] : die("Nu a fost emisa nici o chitanta de curand !!!"));

$chitanta_sql = "SELECT C.chitanta_serie, C.chitanta_nr, C.suma, C.reprezentand, DATE_FORMAT(C.data_inserarii, '%d.%m.%Y') as data,
 L.nume, L.ap, STR.strada, S.nr, S.bloc, S.scara, U.nume as casier
FROM casierie C, locatari L, scari S, asociatii A, strazi STR, admin U
WHERE C.loc_id=L.loc_id AND C.scara_id=S.scara_id AND C.asoc_id=A.asoc_id AND S.strada=STR.str_id AND C.casier_id=U.id AND C.id=".$id_chitanta;

$chitanta = mysql_query($chitanta_sql) or die ('Nu pot afla informatii despre ultima chitanta eliberata');
$chitanta = mysql_fetch_array($chitanta);

$adresa = 'Str. '.$chitanta['strada'].', nr. '.$chitanta['nr'].', Bl. '.$chitanta['bloc'].', Sc. '.$chitanta['scara'].', Ap. '.$chitanta['ap'];


$ResultsPDFContent = '<table width="100%" height="50%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td rowspan="2" align="left" valign="top"  style="font-size: 10px;"><strong><p style="font-size: 16px;">S.C. URBICA ADM SRL</p></strong>
    Nr. ord.Reg.Com./an:<strong>J22/1456/2009</strong><br />
    Cod Unic de Inregistrare: <strong>RO25935553</strong><br />
    Sediu: <strong>Cuza Voda nr. 13, Iasi</strong><br />
    Judetul: <strong>Iasi</strong><br />
    Tel./Fax: <strong>0332-411.555</strong><br />
    Capital social: <strong>200 lei (ron)</strong></td>
    <td align="right" valign="top"><img src="../pdf/sigla.jpg" width="180"></td>
  </tr>
  <tr>
    <td align="left" valign="middle"  width="250">Seria <strong>'.$chitanta['chitanta_serie'].'</strong> nr.: <strong>'.str_pad($chitanta['chitanta_nr'], 6, "0", STR_PAD_LEFT).'</strong></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><br /><strong>CHITANTA</strong> Nr. '.str_pad($chitanta['chitanta_nr'], 6, "0", STR_PAD_LEFT).' Data '.$chitanta['data'].'<br /></td>
  </tr>
  <tr>
    <td colspan="2" valign="middle"><br /><p>Am primit de la <strong>'.$chitanta['nume'].'</strong></p>
    <p>Adresa '.$adresa.'</p>
    <p>Suma de <strong>'.$chitanta['suma'].'</strong> lei adica  '.Util::traducere2($chitanta['suma']).' <br />Reprezentand <strong>'.$chitanta['reprezentand'].'</strong></p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><br />Casier,<br />'.$chitanta['casier'].'</td>
  </tr>
</table>';

$ResultsPDFContent .= '<br /><br /><br /><hr /><br /><br />'.$ResultsPDFContent.'<br /><br /><hr /><br /><br />'.$ResultsPDFContent;

include("../pdf/mpdf/mpdf.php");

$mpdf=new mPDF('s','A4','12','corbel',3,3,3,3,0,0,"P");
/*
   $mpdf->progbar_heading = 'mPDF file progress (Advanced)';
   $mpdf->StartProgressBarOutput(2);

   $mpdf->mirrorMargins = 1;
   $mpdf->SetDisplayMode('fullpage','two');
   $mpdf->useGraphs = true;
   $mpdf->list_number_suffix = ')';
   $mpdf->hyphenate = true;

   $mpdf->debug  = true;
   //*/
$mpdf->WriteHTML($ResultsPDFContent);

$mpdf->Output();

exit;
?>