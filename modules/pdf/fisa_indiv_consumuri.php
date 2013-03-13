<?php

$asocId = $_GET['asoc_id'];
$scaraId = $_GET['scara_id'];
$luna = $_GET['luna'];

$FI_FC_s = 'SELECT * FROM locatari WHERE scara_id='.$scaraId;
$FI_FC_q = mysql_query($FI_FC_s) or die('Nu pot afla locatarii din scara selectata');

while($FI_FC_r = mysql_fetch_array($FI_FC_q)) {
	$_GET['loc_id'] = $FI_FC_r['loc_id'];
	include("fisa_individuala.php");
	$PDF_newPage = false;
	include("fisa_consumuri.php");
}