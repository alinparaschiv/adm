<?php
  $servicii = explode(',', $proc['ppu']);
  $consumuri = explode(',', $proc['cantitate']);

  $serviciu_centrala_incalzire_sql = 'SELECT * FROM servicii WHERE serv_id='.$servicii[2];
  $serviciu_centrala_apacalda_sql = 'SELECT * FROM servicii WHERE serv_id='.$servicii[3];
  $serviciu_lift_sql = 'SELECT * FROM servicii WHERE serv_id='.$servicii[1];
  $serviciu_iluminat_sql = 'SELECT * FROM servicii WHERE serv_id='.$servicii[0];

  $servicii_incalzire = 'AND serviciu IN (25)';
  $servicii_apacalda = 'AND serviciu IN (26, 37, 38, 39, 40)';

  $pretKW = $cost / $consumuri[0];


$setari_sc_SQL = "SELECT * FROM scari_setari WHERE scara_id=".$scaraId;
$setari_sc = mysql_query($setari_sc_SQL) or die ("Nu pot accesa setarile scarilor<br />".mysql_error());
$setari_sc = mysql_fetch_assoc($setari_sc);

//==============================================================================
/* un array in care pe fiecare pozitie e id-ul locatariului ce va contine ce are de plata fiecare locatar */
  $locatari = array();

  $locatari_SQL = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
  $locatari_SQL = mysql_query($locatari_SQL) or die ("Nu pot afla locatarii acestei scari<br />".mysql_error());
while($l = mysql_fetch_assoc($locatari_SQL))
  $locatari[$l['loc_id']] = array();

//==============================================================================
$valoare_centrala = 0;
if ($setari_sc['contor_centrala'] != 0 OR ($setari_sc['ag_termic'] > 0)) {
  //factura de lumina se imparte si la centrala
  $valoare_incalzire = 0;
  $valoare_apacalda  = 0;

  if($setari_sc['contor_centrala'] != 0) {
    $valoare_centrala = $pretKW * $consumuri[2];
    $cost -= $valoare_centrala;
  }
  if($setari_sc['ag_termic'] > 0) $valoare_centrala = $cost * $setari_sc['ag_termic'] / 100;

  $valoare_incalzire = ($setari_sc['ag_termic_incalzire'] != 0) ? ($valoare_centrala * $setari_sc['ag_termic_incalzire'] / 100) : 0;
  $valoare_apacalda  = ($setari_sc['ag_termic_calda'] != 0)     ? ($valoare_centrala * $setari_sc['ag_termic_calda']     / 100) : 0;

  //======================================================================================
  // INCALZIRE
  if ($setari_sc['ag_termic_incalzire'] > 0 && $setari_sc['ag_termic_incalzire'] <= 100) {

	  $serviciu_centrala_incalzire = mysql_query($serviciu_centrala_incalzire_sql) or die('A aparut o problema la aflarea serviciului de centrala_incalzire <br />'.$serviciu_centrala_incalzire_sql);
	  $serviciu_centrala_incalzire = mysql_fetch_assoc($serviciu_centrala_incalzire);

  	  $incalzire_um = '';

	  $select_centrala_incalzire = "SELECT ";
	  switch ($serviciu_centrala_incalzire['mod_impartire']) {
		case 0: //pe nr apartamente
		  $select_centrala_incalzire .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_incalzire['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $incalzire_um = 'apartament';
		  break;
		case 1: //pe nr persoane
		  $select_centrala_incalzire .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_incalzire['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $incalzire_um = 'persoana';
		  break;
		case 2: //pe suparafata
		  $select_centrala_incalzire .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_incalzire['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $incalzire_um = 'cota indiviza';
		  break;
		case 6: //incalzire
		  $select_centrala_incalzire .= "L.loc_id, L.scara_id, L.asoc_id, SUM(FI.cant_fact_pers*FI.pret_unitar) as valoare, IFNULL( S.procent, 100 ) AS procent FROM fisa_indiv FI, locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_incalzire['serv_id'].") S ON L.loc_id = S.loc_id WHERE FI.loc_id=L.loc_id AND luna='".$proc['luna']."' ".$servicii_incalzire." AND L.scara_id=".$scaraId." GROUP BY L.loc_id";
		  $incalzire_um = 'KW';
		  break;
		default:
		  die("Incalzire Nu se poate procesa o factura cu acest mod de inpartire a facturi");
	  }

	  $query = mysql_query($select_centrala_incalzire) or die("A aparut o eroare la citirea informatiilor din BD la INCALZIRE <br />".$select_centrala_incalzire."<br />".  mysql_error());

	  $info = array();
	  $total = 0;
	  $facturaCurr = $serieFactura.'/'.$numarFactura;
	  while ($row = mysql_fetch_assoc($query)) {
		$info [] = $row;
		$total += $row['valoare']*$row['procent']/100;
	  }
	  $ppuIncalzire = $valoare_incalzire / $total;

	  foreach ($info as $key => $value) {
	  	$locatari[$value['loc_id']]['incalzire']['um'] = $incalzire_um;
		$locatari[$value['loc_id']]['incalzire']['total'] = $ppuIncalzire * ($value['valoare']*$value['procent']/100);
		$locatari[$value['loc_id']]['incalzire']['unitati'] = ($value['valoare']*$value['procent']/100);
	  }
  }
  //==================================================================================================================
  //apa calda

  if ($setari_sc['ag_termic_calda'] > 0 && $setari_sc['ag_termic_calda'] <= 100) {

	  $serviciu_centrala_apacalda = mysql_query($serviciu_centrala_apacalda_sql) or die('A aparut o problema la aflarea serviciului de centrala_apacalda <br />'.$serviciu_centrala_apacalda_sql);
	  $serviciu_centrala_apacalda = mysql_fetch_assoc($serviciu_centrala_apacalda);

  	  $apacalda_um = '';

	  $select_centrala_apacalda = "SELECT ";
	  switch ($serviciu_centrala_apacalda['mod_impartire']) {
		case 0: //pe nr apartamente
		  $select_centrala_apacalda .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_apacalda['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $apacalda_um = 'apartament';
		  break;
		case 1: //pe nr persoane
		  $select_centrala_apacalda .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_apacalda['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $apacalda_um = 'persoana';
		  break;
		case 2: //pe suparafata
		  $select_centrala_apacalda .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_apacalda['serv_id'].") S ON L.loc_id = S.loc_id WHERE L.scara_id=".$scaraId;
		  $apacalda_um = 'cota indiviza';
		  break;
		case 5: //apacalda
		  $select_centrala_apacalda .= "L.loc_id, L.scara_id, L.asoc_id, SUM(FI.cant_fact_pers*FI.pret_unitar) as valoare, IFNULL( S.procent, 100 ) AS procent FROM fisa_indiv FI, locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_centrala_apacalda['serv_id'].") S ON L.loc_id = S.loc_id WHERE FI.loc_id=L.loc_id AND luna='".$proc['luna']."' ".$servicii_apacalda." AND L.scara_id=".$scaraId." GROUP BY L.loc_id";
		  $apacalda_um = 'KW';
		  break;
		default:
		  die("Apa Calda Nu se poate procesa o factura cu acest mod de inpartire a facturi");
	  }

	  $query = mysql_query($select_centrala_apacalda) or die("A aparut o eroare la citirea informatiilor din BD la APA CALDA <br />".$select_centrala_apacalda."<br />".  mysql_error());

	  $info = array();
	  $total = 0;
	  $facturaCurr = $serieFactura.'/'.$numarFactura;
	  while ($row = mysql_fetch_assoc($query)) {
		$info [] = $row;
		$total += $row['valoare']*$row['procent']/100;
	  }
	  $ppuApaCalda = $valoare_apacalda / $total;

	  foreach ($info as $key => $value) {
	  	$locatari[$value['loc_id']]['apacalda']['um'] = $apacalda_um;
		$locatari[$value['loc_id']]['apacalda']['total'] = $ppuApaCalda * ($value['valoare']*$value['procent']/100);
		$locatari[$value['loc_id']]['apacalda']['unitati'] = ($value['valoare']*$value['procent']/100);
	  }
	}
}

//================================================================================================================
// LIFT
$valoare_lift = 0;

if ($setari_sc['are_lift'] == 1) {
  if ($setari_sc['contor_lift'] != 0) $valoare_lift = $pretKW * $consumuri[1];
  if ($setari_sc['contor_lift'] == 0 && $setari_sc['iluminare_lift'] > 0)$valoare_lift = $cost * $setari_sc['iluminare_lift'] / 100;

  $serviciu_lift = mysql_query($serviciu_lift_sql) or die('A aparut o problema la aflarea serviciului de Lift <br />'.$serviciu_lift_sql);
  $serviciu_lift = mysql_fetch_assoc($serviciu_lift);

  $lift_um = '';

  $select_lift = "SELECT ";
  switch ($serviciu_lift['mod_impartire']) {
    case 0: //pe nr apartamente
      $select_lift .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_lift['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
      $lift_um = 'apartament';
	  break;
    case 1: //pe nr persoane
      $select_lift .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_lift['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
      $lift_um = 'persoana';
	  break;
    case 2: //pe suparafata
      $select_lift .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_lift['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
      $lift_um = 'cota indiviza';
	  break;
    default:
      die("Lift Nu se poate procesa o factura cu acest mod de inpartire a facturi");
  }

  $select_lift .= ' L.scara_id='.$scaraId;

  $query = mysql_query($select_lift) or die("A aparut o eroare la citirea informatiilor din BD la LIFT <br />".$select."<br />".  mysql_error());
  $info = array();
  $total = 0;
  $facturaCurr = $serieFactura.'/'.$numarFactura;
  while ($row = mysql_fetch_assoc($query)) {
    $info [] = $row;
    $total += $row['valoare']*$row['procent']/100;
  }
  $ppuLift = $valoare_lift / $total;

  foreach ($info as $key => $value){
  	$locatari[$value['loc_id']]['lift']['um'] = $lift_um;
    $locatari[$value['loc_id']]['lift']['total'] = $ppuLift * ($value['valoare']*$value['procent']/100);
    $locatari[$value['loc_id']]['lift']['unitati'] = ($value['valoare']*$value['procent']/100);
  }
}
//==============================================================================
$valoare_lumina = $cost - $valoare_centrala - $valoare_lift;
if($setari_sc['contor_centrala'] != 0) $valoare_lumina += $valoare_centrala;

  $serviciu_iluminat = mysql_query($serviciu_iluminat_sql) or die('A aparut o problema la aflarea serviciului de Iluminat <br />'.$serviciu_iluminat_sql);
  $serviciu_iluminat = mysql_fetch_assoc($serviciu_iluminat);

  $comun_um = '';

$select_lumina = "SELECT ";
switch ($serviciu_iluminat['mod_impartire']) {
  case 0: //pe nr apartamente
    $select_lumina .= "L.loc_id, L.scara_id, L.asoc_id, 1 as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_iluminat['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
    $comun_um = 'apartament';
	break;
  case 1: //pe nr persoane
    $select_lumina .= "L.loc_id, L.scara_id, L.asoc_id, L.nr_pers as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_iluminat['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
    $comun_um = 'persoana';
	break;
  case 2: //pe suparafata
    $select_lumina .= "L.loc_id, L.scara_id, L.asoc_id, L.supr as valoare, IFNULL( S.procent, 100 ) AS procent FROM locatari L LEFT OUTER JOIN (SELECT * FROM subventii WHERE serv_id=".$serviciu_iluminat['serv_id'].") S ON L.loc_id = S.loc_id WHERE";
    $comun_um = 'cota indiviza';
	break;
  default:
    die("Iluminat ComunNu se poate procesa o factura cu acest mod de inpartire a facturi");
}

$select_lumina .= ' L.scara_id='.$scaraId;

$query = mysql_query($select_lumina) or die("A aparut o eroare la citirea informatiilor din BD la LUMINA <br />".$select."<br />".  mysql_error());
$info = array();
$total = 0;
$facturaCurr = $serieFactura.'/'.$numarFactura;
while ($row = mysql_fetch_assoc($query)) {
  $info [] = $row;
  $total += $row['valoare']*$row['procent']/100;
}
$ppuLumina = $valoare_lumina / $total;

foreach ($info as $key => $value) {
  $locatari[$value['loc_id']]['lumina']['um'] = $comun_um;
  $locatari[$value['loc_id']]['lumina']['total'] = $ppuLumina * ($value['valoare']*$value['procent']/100);
  $locatari[$value['loc_id']]['lumina']['unitati'] = ($value['valoare']*$value['procent']/100);
}


if($valoare_incalzire)
  echo 'Valoare la Eng pt incalzire      este: '.$valoare_incalzire.'<br />';
if($valoare_apacalda)
  echo 'Valoare la Eng pt apa calda      este: '.$valoare_apacalda.'<br />';
if($valoare_lift)
  echo 'Valoare la Eng pt lift           este: '.$valoare_lift.'<br />';
if($valoare_lumina)
  echo 'Valoare la Eng pt iluminat comun este: '.$valoare_lumina.'<br />';
echo 'Pretul unitar este: '.$pretKW.'<br />';

foreach ($locatari as $key => $value) {
  $insert = "INSERT INTO fisa_indiv (`id`, `asoc_id`, `scara_id`, `loc_id`, `luna`, `serviciu`, `cant_fact_pers`, `pret_unitar`, `um`, `factura`, `pret_unitar2`, `cant_fact_tot`) VALUES ";

  if ($serviciu_centrala_incalzire['unitate'] == 'KW') $value['incalzire']['unitati'] = $value['incalzire']['total'] / $pretKW;
  if ($serviciu_centrala_apacalda['unitate']  == 'KW') $value['apacalda']['unitati']  = $value['apacalda']['total']  / $pretKW;



  if ($valoare_incalzire != 0) $insert .= " (NULL , ".$asocId.",".$scaraId.", ".$key.", '$luna', '".$serviciu_centrala_incalzire['serv_id']."', '".$value['incalzire']['unitati']."', '".($value['incalzire']['total']/$value['incalzire']['unitati'])       ."', '".$value['incalzire']['um'] ."', '$facturaCurr', '$pretKW', '".($valoare_incalzire/$pretKW)."'), ";
  if ($valoare_apacalda != 0)  $insert .= " (NULL , ".$asocId.",".$scaraId.", ".$key.", '$luna', '".$serviciu_centrala_apacalda['serv_id'] ."', '".$value['apacalda']['unitati'] ."', '".($value['apacalda']['total']/$value['apacalda']['unitati'])         ."', '".$value['apacalda']['um']  ."', '$facturaCurr', '$pretKW', '".($valoare_apacalda/$pretKW)."'), ";
  if ($valoare_lift != 0)      $insert .= " (NULL , ".$asocId.",".$scaraId.", ".$key.", '$luna', '".$serviciu_lift['serv_id']              ."', '".$value['lift']['unitati']     ."', '".($value['lift']['total']/$value['lift']['unitati'])                 ."', '".$value['lift']['um']      ."', '$facturaCurr', '$pretKW', '".($valoare_lift/$pretKW)."'), ";
  if ($valoare_lumina != 0)    $insert .= " (NULL , ".$asocId.",".$scaraId.", ".$key.", '$luna', '".$serviciu_iluminat['serv_id']          ."', '".$value['lumina']['unitati']   ."', '".($value['lumina']['total']/$value['lumina']['unitati'])             ."', '".$value['lumina']['um']    ."', '$facturaCurr', '$pretKW', '".($valoare_lumina/$pretKW)."'), ";


  if(($valoare_lumina != 0) || ($valoare_lift != 0) || ($valoare_apacalda != 0) || ($valoare_incalzire != 0))
    mysql_query(substr($insert, 0, -2)) or die ("Nu pot insera factura (inpartitaa pe locatari) la locatartul cu loc_id=".$key."<br />".$insert."<br />".mysql_error());

}
