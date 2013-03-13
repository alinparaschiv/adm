<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once '../../componente/config.php';
include_once("../fise/Penalizare.class.php");
include_once '../../Util.php';
$asoc_id = $_GET['asoc_id'];
$scara_id = $_GET['scara_id'];
$luna = $_GET['luna'];


//==============================================================
define("_JPGRAPH_PATH", '../../jpgraph_5/jpgraph/'); // must define this before including mpdf.php file
$JpgUseSVGFormat = true;
define('_MPDF_URI','mpdf/'); 	// must be  a relative or absolute URI - not a file system path

ini_set("memory_limit","556M");
ini_set('max_execution_time', 3000);

$ResultsPDFContent = '
<style type="text/css">
#sigla {
    width: 48px;
    height: 73px;
}
#text-sigla,
#date-beneficiar {
    font-size: 11px;
    color: silver;
    padding-top: 10px;
    padding-left: 10px;
}

#text-sigla {
    width: 25%;
    vertical-align: top;
    padding-top: 25px;
}

#titlu-beneficiar {
    width: 50%;
    text-align: left;
}

#titlu {
    padding-top: 40px;
    font-size: 18px;
    font-weight: bold;
}

#header > td {
    background-color: lightgray;
    border-radius: 40px 10px;
    text-shadow: white 0 1px;
    color: dodgerblue;
    font-weight: bold;
}

#gol {
    width: 25%;
}

#antet {
    padding-bottom: 50px;
}

body {
    position: relative;
}

.pagebreak {
    position: relative;
    page-break-before: always;
}

.orientation-L {
    @media print{@page {size: landscape}}
}

.LP, .FI, .FCons{
    width: 100%;
}

.FCons{
    position: relative;
}

.noBreak{
    page-break-inside: avoid;
    height: 100%;
}
</style>
';

function pdf_header($titlu) {

    return '
<table id="antet">
    <tr>
    <td><img id="sigla" src="sigla-urbica-verticala.png"/></td>
    <td id="text-sigla">
        <div>http://www.urbica.ro</div>
        <div>contact@urbica.ro</div>
        <div>(+4)0 332 411 555</div>
        <div>Str. Cuza Vodă nr. 13</div>
    </td>
    <td id="gol">&nbsp;</td>
    <td id="titlu-beneficiar">
        <div id="titlu">'.$titlu.'</div>
    </td>
    </tr>
</table>';
}

//==============================================================
//==============================================================
if (!isset($_GET['afisare']))
{
?>
<html>
<head>
</script>

</head>

<body>
<form>
<input type="hidden" name="asoc_id" value="<?php echo $asoc_id; ?>">
<input type="hidden" name="scara_id" value="<?php echo $scara_id; ?>">
<input type="hidden" name="luna" value="<?php echo $luna; ?>">
<input type="hidden" name="afisare" value="OK">

<input type="checkbox" name="LP" value="0"/>Lista plata<br />
<input type="checkbox" name="FI" value="0" />Fise individuale<br />
<input type="checkbox" name="FC" value="0" />Fisa consumuri<br />
<input type="checkbox" name="FC/FI" value="0" />Fisa indiv/cons<br />
<input type="checkbox" name="FCon" value="0" />Fisa cont<br />
<input type="checkbox" name="FPen" value="0" />Fisa penalizari<br />
<?php 
//<input type="checkbox" name="RJ" value="0"/>Registru jurnal<br />
//<input type="checkbox" name="FRU" value="0" />Registru fond rulment<br />
//<input type="checkbox" name="FRE" value="0" />Registru fond reparatii<br />
?>
<input type="checkbox" name="AP" value="0" />Tabel apometre<br />
<input type="checkbox" name="F" value="0" />Tabel fond<br />

<input type="submit" value="Genereaza PDF" onClick="submit();"/>

</form>
</body>
</html>
<?php
} else {


//valori default pt o serie de parametrii din pdf-uri
//cofig
/*
$thHeight = 17; //dimensiunea inregistrarilor din tabelul general
$hedder_font_size_LP = 15;
$td_hedder_style_LP = 'font-size:16x;';
$td_content_style_LP = 'font-size:16px;';
/*/
$thHeight = 12; //dimensiunea inregistrarilor din tabelul general
$hedder_font_size_LP = 12;
$td_hedder_style_LP = '';//'font-size:10x;';
$td_content_style_LP = 'font-size:12px;';
$ap_nume = 'Ap.';
$fond_nume = 'rul'; //[rul, spe]
$afiseaza_cont_bancar = true;

$restanta_fond = true;
$fond_rep = true;
$incasari = false;
// */


$conf_array = array();

//Bl H2-2
$conf_array[1] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl H35
$conf_array[2] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl 634
$conf_array[3] = array(
                        'thHeight' => 21,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:16px;');
$conf_array[3][3] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');

//Bl G1-3
$conf_array[4] = array(
                        'thHeight' => 21,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:16px;');
//Bl F4-3
$conf_array[5] = array(
                        'thHeight' => 16,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:15px;');
//Bl NOVA
$conf_array[7] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl E14
$conf_array[10] = array(
                        'thHeight' => 13,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Bl 620A
$conf_array[13] = array(
                        'thHeight' => 13,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Bl 361
$conf_array[15] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl 656
$conf_array[16] = array(
                        'thHeight' => 14,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:15px;');
//985
$conf_array[18] = array(
                        'thHeight' => 18,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:16x;',
                        'td_content_style_LP' => 'font-size:18px;');
//Bl S6-S7
$conf_array[22] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl 339, Mihai Eminescu
$conf_array[23] = array('afiseaza_cont_bancar' => false,
                        'incasari' => false,
                        'thHeight' => 15,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Bl A4-1 Soseaua Nationala
$conf_array[24] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl N5
$conf_array[26] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl P11
$conf_array[27] = array(
                        'thHeight' => 13,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Bl 655 A
$conf_array[1003] = array(
                        'thHeight' => 13,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Bl B7 Bis Decebal
$conf_array[1006] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl A1 Mircea
$conf_array[1007] = array(
                        'thHeight' => 22,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:17px;');
//Bl 655 B
$conf_array[1013] = array(
                        'thHeight' => 13,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:14px;');
//Dream Village
$conf_array[1008] = array(
                        'thHeight' => 12,
                        'hedder_font_size_LP' => 14,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:13px;');
$conf_array[1008][2523] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl G1 Splai Bahlui
$conf_array[1018] = array(
                        'thHeight' => 12,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:13px;');
//Bl C1 Tatarasi
$conf_array[1029] = array(
                        'thHeight' => 16,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:17px;');
//Cronicar Mustea
$conf_array[1033] = array(
                        'ap_nume' => 'Lot',
                        'thHeight' => 18,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:16x;',
                        'td_content_style_LP' => 'font-size:18px;');
//Bl D2B
$conf_array[1035] = array(
                        'fond_nume' => 'spe',
                        'thHeight' => 14,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:15px;');
//Bl E1
$conf_array[1036] = array(
                        'thHeight' => 22,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:17px;');
//Bl DC7
$conf_array[1039] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl N1
$conf_array[1040] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl P3
$conf_array[1052][2603] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
$conf_array[1052][2604] = array(
                        'thHeight' => 23,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:19px;');
//Bl J2
$conf_array[1060] = array(
                        'thHeight' => 22,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:17px;');
//Bl N, Macazului
$conf_array[1074] = array(
                        'thHeight' => 25,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:17x;',
                        'td_content_style_LP' => 'font-size:20px;');
$conf_array[1075] = array(
                        'thHeight' => 18,
                        'hedder_font_size_LP' => 15,
                        'td_hedder_style_LP' => 'font-size:16x;',
                        'td_content_style_LP' => 'font-size:16px;');
//Bl 339 Tr. 2
$conf_array[1077] = array(
                        'thHeight' => 16,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:17px;');
//Bl H36
$conf_array[1078] = array(
                        'thHeight' => 14,
                        'hedder_font_size_LP' => 13,
                        'td_hedder_style_LP' => '',
                        'td_content_style_LP' => 'font-size:15px;');


//se verifica daca trebuie suprascrisi parametrii default
if (isset($conf_array[$asoc_id])) {
    if(isset($conf_array[$asoc_id]['thHeight']))
        $thHeight = $conf_array[$asoc_id]['thHeight'];
    if(isset($conf_array[$asoc_id]['hedder_font_size_LP']))
        $hedder_font_size_LP = $conf_array[$asoc_id]['hedder_font_size_LP'];
    if(isset($conf_array[$asoc_id]['td_hedder_style_LP']))
        $td_hedder_style_LP = $conf_array[$asoc_id]['td_hedder_style_LP'];
    if(isset($conf_array[$asoc_id]['td_content_style_LP']))
        $td_content_style_LP = $conf_array[$asoc_id]['td_content_style_LP'];
    if(isset($conf_array[$asoc_id]['ap_nume']))
        $ap_nume = $conf_array[$asoc_id]['ap_nume'];
    if(isset($conf_array[$asoc_id]['fond_nume']))
        $fond_nume = $conf_array[$asoc_id]['fond_nume'];
    if(isset($conf_array[$asoc_id]['afiseaza_cont_bancar']))
        $afiseaza_cont_bancar = $conf_array[$asoc_id]['afiseaza_cont_bancar'];

    if(isset($conf_array[$asoc_id]['restanta_fond']))
        $restanta_fond = $conf_array[$asoc_id]['restanta_fond'];
    if(isset($conf_array[$asoc_id]['fond_rep']))
        $fond_rep = $conf_array[$asoc_id]['fond_rep'];
    if(isset($conf_array[$asoc_id]['incasari']))
        $incasari = $conf_array[$asoc_id]['incasari'];

    if (isset($conf_array[$asoc_id][$scara_id])) {
        if(isset($conf_array[$asoc_id][$scara_id]['thHeight']))
            $thHeight = $conf_array[$asoc_id][$scara_id]['thHeight'];
        if(isset($conf_array[$asoc_id][$scara_id]['hedder_font_size_LP']))
            $hedder_font_size_LP = $conf_array[$asoc_id][$scara_id]['hedder_font_size_LP'];
        if(isset($conf_array[$asoc_id][$scara_id]['td_hedder_style_LP']))
            $td_hedder_style_LP = $conf_array[$asoc_id][$scara_id]['td_hedder_style_LP'];
        if(isset($conf_array[$asoc_id][$scara_id]['td_content_style_LP']))
            $td_content_style_LP = $conf_array[$asoc_id][$scara_id]['td_content_style_LP'];
        if(isset($conf_array[$asoc_id][$scara_id]['ap_nume']))
            $ap_nume = $conf_array[$asoc_id][$scara_id]['ap_nume'];
        if(isset($conf_array[$asoc_id][$scara_id]['afiseaza_cont_bancar']))
            $afiseaza_cont_bancar = $conf_array[$asoc_id][$scara_id]['afiseaza_cont_bancar'];
    }
}




$PDF_start = false;
$PDF_orientation = "L";
$PDF_newPage = true;

if (isset($_GET['LP'])) {
	$PDF_start = true;
	include("lista_plata.php");
	$PDF_newPage = false;
}
if (isset($_GET['FI'])){
	$PDF_start = true;
	include("fisa_individuala.php");
	$PDF_newPage = false;
}
if (isset($_GET['FC'])) {
	$PDF_start = true;
	include("fisa_consumuri.php");
	$PDF_newPage = false;
}
if (isset($_GET['FC/FI'])) {
	$PDF_start = true;
	include("fisa_indiv_consumuri.php");
    $PDF_newPage = false;
}
if (isset($_GET['FCon'])) {
	if (!$PDF_start) {
		$PDF_orientation = "P";
	} 
	$PDF_start = true;
	include("fisa_cont.php");
    $PDF_newPage = false;
}
if (isset($_GET['FPen'])){
	if (!$PDF_start) {
		$PDF_orientation = "P";
	}
	$PDF_start = true;
	include("fisa_pen.php");
    $PDF_newPage = false;
}

if (isset($_GET['RJ']))
include("registru_jurnal_general.php");

if (isset($_GET['FRU']))
include("registru_fond_rulment.php");

if (isset($_GET['FRE']))
include("registru_fond_reparatii.php");

if (isset($_GET['AP'])){
    if (!$PDF_start) {
        $PDF_orientation = "P";
    }
    $PDF_start = true;
    include("tabel_apometre.php");
    $PDF_newPage = false;
}

if (isset($_GET['F'] )){
    $PDF_start = true;
    include("tabel_fond.php");
    $PDF_newPage = false;
}
//==============================================================
//==============================================================

$ResultsPDFContent = str_replace('</pagebreak>', '</div>', $ResultsPDFContent);

$ResultsPDFContent = str_replace('<pagebreak>', '<div class="pagebreak" >', $ResultsPDFContent);

$ResultsPDFContent = str_replace('<pagebreak orientation="L" type="SINGLE-SIDED">', '<div class="pagebreak orientation-L">', $ResultsPDFContent);


echo '<html><body>'.$ResultsPDFContent.'</body></html>';
}
?>