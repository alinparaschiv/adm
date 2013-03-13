<?php

  if (isset($_POST['insereaza']) && $_POST['insereaza'] == "OK") {
    $setari_sc_SQL = "SELECT * FROM scari_setari WHERE scara_id=".$_GET['scara_id'];
    $setari_sc = mysql_query($setari_sc_SQL) or die ("Nu pot accesa setarile scarilor<br />".mysql_error());
    $setari_sc = mysql_fetch_assoc($setari_sc);

    $asocId = $_GET['asoc_id'];                 //id-ul asociatiei
    $tipFactura = $_GET['tipFactura'];          //fact. pe asoc, scara, ap, locatari
    $furnizor = $_POST['fur_id'];              //id-ul furnizorului
    $scaraId = $_GET['scara_id'];       	//salvez id-ul scarii

    $afluServiciu = "SELECT servicii.serv_id, servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
    $afluServiciu = mysql_query($afluServiciu) or die ("#Facturi: 90 -- Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());

    $servId = mysql_result($afluServiciu, 0, 'serv_id');
    $serviciu = mysql_result($afluServiciu, 0, 'serviciu');

    $indexVechi = $_POST['iluminat_iv'].','.$_POST['lift_iv'].','.$_POST['centrala_iv'];
    $indexNou =  $_POST['iluminat_in'].','.$_POST['lift_in'].','.$_POST['centrala_in'];
    $diferenta =  ($_POST['iluminat_in'] - $_POST['iluminat_iv']).','.($_POST['lift_in'] - $_POST['lift_iv']).','.( $_POST['centrala_in'] - $_POST['centrala_iv']);

    $serv = $_POST['iluminat_s'].','.$_POST['lift_s'].','.$_POST['incalzire_s'].','.$_POST['apacalda_s'];

    $putCurent = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cantitate`, `cost`, `observatii`, `ppu`, `procesata`) ";
    $putCurent.= "VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$indexNou."', '".$indexVechi."', '".$diferenta."', '".$_POST['valoareFactura']."', '".$_POST['observatii']."', '".$serv."', 0)";

    $putCurent = mysql_query($putCurent) or die ("#Factura: 1003- -- Eroare la salvarea facturi<br />".mysql_error());

    //$getFacturaId = mysql_insert_id($putCurent);

  	$getFacturaIdSQL = "SELECT fact_id FROM facturi WHERE asoc_id='$asocId' AND numarFactura='".$_POST['numarFactura']."' AND serieFactura='".$_POST['serieFactura']."' ORDER BY `fact_id` DESC ;";
  	$getFacturaId =  mysql_query($getFacturaIdSQL) or die ("#Factura: 1001 -- Eroare citirea indexului facture curente <br />".$getFacturaIdSQL."<br />".mysql_error());
  	$getFacturaId = mysql_result($getFacturaId, 0, 'fact_id');


  	$dataScadentaTrans = explode('-', $_POST['dataScadenta']);
  	$dataScadentaTrans = $dataScadentaTrans[2].'-'.$dataScadentaTrans[1].'-'.$dataScadentaTrans[0];


    $insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', '$scaraId', NULL, '$furnizor', '$servId', '$getFacturaId', '".date('Y-m-d')."', '".$dataScadentaTrans."', '".$_POST['numarFactura']."/".$_POST['serieFactura']."', 'Factura', '".$_POST['valoareFactura']."', NULL, NULL);";
    mysql_query($insertFisaFurnizoriSQL) or die ("#Factura: 1003-'$t' -- Eroare la introducerea facturi in fisa_furnizori <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());


    if (isset($_FILES['file'])) {
      if ($_FILES['file']['error'] == 4){
        echo "<p>Factura a fost salvata fara fisier atasat.</p>";
      } else if ($_FILES['file']['type'] != "application/pdf") {
                echo "<p>Se accepta doar fisere PDF pentru upload,  factura a fost salvata fara fisier atasat.</p>";
            } else {
            $factFileNewName = date('Y-m-d').'_'.$asocId.'_'.$servId.'_'.$getFacturaId.'_'.$_FILES['file']['name'];

            if (file_exists('doc/'.$factFileNewName)) die ('Fisierul exista');


            move_uploaded_file($_FILES['file']['tmp_name'], 'doc/'.$factFileNewName);


            $file_insert = 'INSERT INTO `doc` (`id`, `tip`, `filename`, `time`, `obs`)
                            VALUES (NULL, \'factura\', \''.'doc/'.addslashes($factFileNewName).'\', \''.date('Y-m-d H:i:s').'\', NULL)';

            $file_query = mysql_query($file_insert) or die('Nu pot incarca documentul atasat in BD.');

            $fact_update_s = 'UPDATE facturi SET observatii='.mysql_insert_id().' WHERE fact_id='.$getFacturaId;
            $fact_update_q = mysql_query($fact_update_s) or die('Nu pot actualiza id-ul documentului pt factura introdusa.');


        }
    }


  }


//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                Utile pt. Asoc/Sc/Fur                 //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////


/*	SELECTUL PENTRU AFISAREA ASOCIATIILOR	*/
$asociatie = '';
if ($_GET['asoc_id'] != "") {
  $selectAsoc = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
} else {
  $selectAsoc = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
}
  $selectAsoc = mysql_query($selectAsoc) or die ("#Facturi: 1 -- Nu pot selecta asociatiile<br />".mysql_error());

while ($asoc = mysql_fetch_array($selectAsoc)) {
  $asociatie .= '<option value="'.$asoc[0].'">'.$asoc[1].'</option>';
}
$setari_sc;
/*	SELECTUL PENTRU AFISAREA SCARILOR	*/
$scara = '';
if ($_GET['asoc_id'] != '') {
  if ($_GET['scara_id'] != '') {
    $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id']." AND scara_id IN (SELECT scara_id FROM scari_furnizori SF WHERE fur_id IN (SELECT F.fur_id FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=FS.fur_id AND S.serviciu=\"iluminat\" AND S.serv_id=FS.serv_id))";

    $setari_sc_SQL = "SELECT * FROM scari_setari WHERE scara_id=".$_GET['scara_id'];
    $setari_sc = mysql_query($setari_sc_SQL) or die ("Nu pot accesa setarile scarilor<br />".mysql_error());
    $setari_sc = mysql_fetch_assoc($setari_sc);
  } else {
    $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id IN (SELECT scara_id FROM scari_furnizori SF WHERE fur_id IN (SELECT F.fur_id FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=FS.fur_id AND S.serviciu=\"iluminat\" AND S.serv_id=FS.serv_id))";
  }
  $selectScara = mysql_query($selectScara) or die ("#Facturi: 2 -- Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());

  while ($scari = mysql_fetch_array($selectScara)) {
    $scara .= '<option value="'.$scari[0].'">Bloc '.$scari[5].', scara '.$scari[2].'</option>';
  }
}
function getLastMonthServiciuId($nume) { //iluminat, lift, apacalda, incalzire
  return 90;
}

function getServiciuId($nume) { //iluminat, lift, apacalda, incalzire
  $servicii_sql = "SELECT * FROM servicii WHERE serv_id IN (";
  switch ($nume) {
    case 'iluminat':
      $servicii_sql .= '84, 90, 212';
      break;

    case 'lift':
      $servicii_sql .= '91, 107';
      break;

    case 'apacalda':
      $servicii_sql .= '92';
      break;

    case 'incalzire':
      $servicii_sql .= '93, 211';
      break;

    default:
      die('Nu cunosc serviciile pentru '.$nume);
      break;
  }

  $servicii_sql .= ")";

  $servicii = mysql_query($servicii_sql) or die('Nu pot afla ce servicii corespund '.$nume.'<br />'.$servicii_sql);
  //$servicii = mysql_fetch_assoc($servicii);

  $serv_string = '';
  while ($row = mysql_fetch_assoc($servicii)) {
    $serv_string .= '<option value="'.$row['serv_id'].'" '.((getLastMonthServiciuId($nume) == $row['serv_id']) ? 'selected="selected"' : '').'>'.$row['serviciu'].'</option>';
  }

  return $serv_string;
}

include_once('util_facturi.php');
?>
<script type="text/javascript">
    function select_asoc(value1){
        window.location = "index.php?link=facturi_iluminat&asoc_id=" + value1 + "&tipFactura=2" ;
    }

    function select_scara(value1, value2, value3){
        window.location = "index.php?link=facturi_iluminat&asoc_id=" + value1 + "&tipFactura=2&scara_id=" + value3;
    }

    function canti(valoare){
        var iNou = document.getElementById(valoare + '_in').value;
        var iVechi = document.getElementById(valoare + '_iv').value;

        if ((iNou != null) && (iVechi != null)){
            document.getElementById(valoare + '_c').value = iNou - iVechi;
        }
    }

$(function () {
	$("#FactEON").submit(function() {
		var valid=true;
		var focus_el;
		$('#FactEON :text[class*="ver_req"]').each(function(index) {
			if(valid){
				var ck_req = /^[\w -.]{1,20}$/;
				if(!ck_req.test(this.value)) {
					valid=false;
					alert(this.name+" nu este completat !");
					focus_el = focus_el ? focus_el : this;
				}}
		})

		$('#FactEON :text[class*="ver_nr"]').each(function(index) {
			if(valid) {
				var ck_nr = /^[0-9]{1,6}$/;
				if(!ck_nr.test(this.value)) {
					valid=false;
					alert(this.name+' nu este numar: "'+this.value+'"');
					focus_el = focus_el ? focus_el : this;
				}}
		})

		$(focus_el).focus();
		return valid;
	})
})
</script>

<div id="content" style="float:left;">
    <table width="400">
        <tr align="center">
            <td width="388" bgcolor="#CCCCCC" colspan="2"><strong>Inserare factura Iluminat</strong></td>
        </tr>
        <tr>
            <td width="173" align="left" bgcolor="#CCCCCC">( 1 ) Alegeti asociatia:</td>
            <td width="215" align="left" bgcolor="#CCCCCC">
                <select onChange="select_asoc(this.value)">
                    <?php  if($_GET['asoc_id']==null) {
                      echo '<option value="">----Alege----</option>';
                    }  else {
                      $afisAsoc = "SELECT * FROM asociatii WHERE asoc_id=".$_GET['asoc_id'];
                      $afisAsoc = mysql_query($afisAsoc) or die ("#Facturi: 8 -- Nu pot selecta asociatiile<br />".mysql_error());

                      echo '<option value="">Asociatia '.mysql_result($afisAsoc, 0, 'asociatie').'</option>';
                    }
                    ?>
                    <?php echo $asociatie; ?>
                </select>
            </td>
        </tr>
        <?php
  /*	AUXILIARE PENTRU SCARI	*/
  if (isset($_GET['asoc_id']) && $_GET['asoc_id'] != '') { ?>
    <tr>
    <td width="173" align="left" bgcolor="#CCCCCC">( 2 ) Alegeti scara:</td>
    <td width="215" align="left" bgcolor="#CCCCCC">
    <?php echo '<select onchange="select_scara('.$_GET['asoc_id'].','.$_GET['tipFactura'].', this.value);">';
    if ($_GET['scara_id'] == null) {
      echo '<option value="">----Alege----</option>';
    } else {
      $afisScari = "SELECT * FROM scari WHERE scara_id=".$_GET['scara_id'];
      $afisScari = mysql_query($afisScari) or die ("#Facturi: 11 -- Nu pot selecta scara pentru tipul de factura<br />".mysql_error());

      echo '<option value="">Bloc '.mysql_result($afisScari, 0, 'bloc').', Scara '.mysql_result($afisScari, 0, 'scara').'</option>';
    }
    echo $scara;
    echo '</select>';
    echo '</td>';
    echo '</tr>';
  }?>
  </table>
</div>

<br clear="left" />
<?php if (isset($_GET['asoc_id']) && $_GET['asoc_id'] != "" && isset($_GET['scara_id']) && $_GET['scara_id'] != "" ) : 
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                       Formular                       //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////?>
<form id="FactEON" action="" method="post" enctype="multipart/form-data">
  <input type="hidden" name="insereaza" value="OK" />
  <table width="750" style="float:left;  margin-top:10px; background-color:#BBBBBB;">
  <?php
  $furniz = "SELECT F.furnizor as furnizor, F.fur_id as id FROM furnizori F, furnizori_servicii FS, servicii S, scari_furnizori SF WHERE F.fur_id=FS.fur_id AND S.serviciu=\"iluminat\" AND S.serv_id=FS.serv_id AND F.fur_id=SF.fur_id AND scara_id='".$_GET['scara_id']."'";
  $furniz = mysql_query($furniz) or die ("#Factura: 4 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

  echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($furniz, 0, 'furnizor').' - Serviciu facturat: Iluminat </strong></td></tr>';
  ?>
    <tr bgcolor="#000000" style="color:#FFFFFF">
      <td width="125px">Serie Factura</td>
      <td width="125px">Numar Factura</td>
      <td width="125px">Data Emiterii</td>
      <td width="125px">Data Scadenta</td>
      <td width="125px">Valoare Factura</td>
    </tr>
    <tr bgcolor="#DDDDDD">
      <td><input type="text" class="ver_req" name="serieFactura" /></td>
      <td><input type="text" class="ver_req" name="numarFactura" /></td>
      <td><input type="text" class="ver_req datepicker" name="dataEmitere" /></td>
      <td><input type="text" name="dataScadenta" class="ver_req datepicker" /></td>
      <td><input type="text" class="ver_req" name="valoareFactura" id="valoareFactura" /></td>
    </tr>

    <tr bgcolor="#000000" style="color:#FFFFFF">
      <td width="125px">Debite</td>
      <td width="125px">Penalizari</td>
      <td width="125px">Factura</td>
      <td width="125px">Luna</td>
      <td width="125px">Furnizor</td>
    </tr>
    <tr bgcolor="#DDDDDD">';
      <td><input type="text" name="debite" disabled /></td>
      <td><input type="text" name="penalizari" disabled/></td>
      <td><input type="file" name="file" /></td>
      <td><select name="luna" style="width:125px">
<?php echo Util_facturi::getSelectLuna($_GET['asoc_id'], (isset($_POST['luna']) ? $_POST['luna'] : null)); ?>
          </select></td>
      <td>
      <?php $furn_id = mysql_result($furniz, 0, 'id');
      $furn_nume = mysql_result($furniz, 0, 'furnizor');
      echo $furn_nume; ?>
      <input type="hidden" name="fur_id" value="<?php echo $furn_id; ?>" /></td>
    </tr >
    <tr><td colspan="5"> <hr /></td></tr>
   <tr><td colspan="5">Detalii Iluminat Comun (Contor General)</td></tr>
   <tr bgcolor="#000000" style="color:#FFFFFF">
      <td width="125px">Index Vechi</td>
      <td width="125px">Index Nou</td>
      <td width="125px">Consum</td>
      <td width="125px" colspan="2">Serviciu Iluminat Comun</td>
    </tr>

    <tr bgcolor="#DDDDDD">
      <td><input type="text" class="ver_nr ver_req" id="iluminat_iv" name="iluminat_iv" onkeyup="canti('iluminat');" /></td>
      <td><input type="text" class="ver_nr ver_req" id="iluminat_in" name="iluminat_in" onkeyup="canti('iluminat');" /></td>
      <td><input type="text" id="iluminat_c"  name="iluminat_c" disabled/></td>
      <td colspan="2"><select name="iluminat_s" style="width:275px">
      <?php echo getServiciuId('iluminat'); ?>
      </select></td>
    </tr>
  <?php if ($setari_sc['contor_centrala'] != 0  OR ($setari_sc['ag_termic'] > 0)) : ?>
   <tr><td colspan="5"> <hr /></td></tr>
   <tr><td colspan="5">Contor Centrala</td></tr>
   <tr bgcolor="#000000" style="color:#FFFFFF">
    <?php if ($setari_sc['contor_centrala'] != 0) : ?>
      <td width="125px">Index Vechi</td>
      <td width="125px">Index Nou</td>
      <td width="125px">Consum</td>
    <?php else : ?>
      <td colspan="3">Procent Energie Centrala</td>
    <?php endif; ?>
      <td width="125px">Procent Incalzire</td>
      <td width="125px">Procent Apa Calda</td>
    </tr>
    <tr>
    <?php if ($setari_sc['contor_centrala'] != 0) : ?>
      <td><input type="text" class="ver_nr ver_req" id="centrala_iv" name="centrala_iv" onkeyup="canti('centrala');" /></td>
      <td><input type="text" class="ver_nr ver_req" id="centrala_in" name="centrala_in" onkeyup="canti('centrala');" /></td>
      <td><input type="text" id="centrala_c"  name="centrala_c" disabled/></td>
    <?php else : ?>
      <td colspan="3"><?php echo $setari_sc['ag_termic'].'%'; ?></td>
    <?php endif; ?>
      <td><?php echo $setari_sc['ag_termic_incalzire'].'%'; ?></td>
      <td><?php echo $setari_sc['ag_termic_calda'].'%'; ?></td>
    </tr>
  <?php endif; ?>
  <?php if ($setari_sc['contor_centrala'] != 0 OR $setari_sc['ag_termic'] > 0 ) : ?>
    <?php if ($setari_sc['ag_termic_incalzire'] > 0 && $setari_sc['ag_termic_incalzire'] <= 100) : ?>
    <tr></tr>
    <tr bgcolor="#DDDDDD">
      <td colspan="3" align="right"><strong>Serviciu Incalzire:</strong></strong></td>
      <td colspan="2"><select name="incalzire_s" style="width:275px" >
      <?php echo getServiciuId('incalzire'); ?>
      </select></td>
    </tr>
    <?php endif; ?>
    <?php if ($setari_sc['ag_termic_calda'] > 0 && $setari_sc['ag_termic_calda'] <= 100) : ?>
    <tr bgcolor="#DDDDDD">
      <td colspan="3" align="right"><strong>Serviciu Apa Calda:</strong></td>
      <td colspan="2"><select name="apacalda_s" style="width:275px" >
      <?php echo getServiciuId('apacalda'); ?>
      </select></td>
    </tr>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($setari_sc['are_lift'] == 1) : ?>
   <tr><td colspan="5"> <hr /></td></tr>
   <tr><td colspan="5">Lift</td></tr>
   <tr bgcolor="#000000" style="color:#FFFFFF">
    <?php if ($setari_sc['contor_lift'] != 0) : ?>
      <td width="125px">Index Vechi</td>
      <td width="125px">Index Nou</td>
      <td width="125px">Consum</td>
    <?php else : ?>
      <td colspan="3">Procent Energie Lift</td>
    <?php endif; ?>
      <td colspan="2" width="275px">Serviciu Energie Lift</td>
    </tr>
    <tr bgcolor="#DDDDDD">
    <?php if ($setari_sc['contor_lift'] != 0) : ?>
      <td><input type="text" class="ver_nr ver_req" id="lift_iv" name="lift_iv" onkeyup="canti('lift');" /></td>
      <td><input type="text" class="ver_nr ver_req" id="lift_in" name="lift_in" onkeyup="canti('lift');" /></td>
      <td><input type="text" id="lift_c"  name="lift_c" disabled/></td>
    <?php else : ?>
      <td colspan="3"><?php echo $setari_sc['iluminare_lift'].'%'; ?></td>
    <?php endif; ?>
      <td colspan="2"><select name="lift_s" style="width:275px">
      <?php echo getServiciuId('lift'); ?>
      </select></td>
  <?php endif; ?>
    <tr>
      <td colspan="4">&nbsp;</td>
      <td><input type="submit" value="Salveaza"/></td>
    </tr>
  </table>
</form>
<?php endif; ?>