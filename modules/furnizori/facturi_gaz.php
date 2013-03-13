<?php
if (isset($_POST['insereaza']) && $_POST['insereaza'] == "OK") {
    //iau datele principale ale facturii (asociatie, tip factura, furnizor, scara)
    $asocId = $_GET['asoc_id'];                 //id-ul asociatiei
    $tipFactura = $_GET['tipFactura'];          //fact. pe asoc, scara, ap, locatari
    $furnizor = $_POST['fur_id'];              //id-ul furnizorului
    $scaraId = $_GET['scara_id'];       	//salvez id-ul scarii


    //aflu numele serviciului
    $afluServiciu = "SELECT servicii.serv_id, servicii.serviciu FROM furnizori_servicii, servicii WHERE furnizori_servicii.fur_id=".$furnizor." AND furnizori_servicii.serv_id=servicii.serv_id";
    $afluServiciu = mysql_query($afluServiciu) or die ("#Facturi: 90 -- Nu pot afla serviciul pentru introducerea facturilor<br />".mysql_error());

    $servId = mysql_result($afluServiciu, 0, 'serv_id');
    $serviciu = mysql_result($afluServiciu, 0, 'serviciu');

    $tipulServiciului = "SELECT * FROM servicii WHERE serv_id=".$servId;
    $tipulServiciului = mysql_query($tipulServiciului) or die ("#Facturi: 91 -- Nu pot afla informatii despre serviciu<br />".mysql_error());

    $areIndecsi = mysql_result($tipulServiciului, 0, 'cu_indecsi');

    $facturaScaraCuIndecsi = "INSERT INTO facturi (`fact_id`, `data`, `asoc_id`, `scara_id`, `tipFactura`, `tipServiciu`, `numarFactura`, `serieFactura`, `dataEmitere`, `dataScadenta`, `debite`, `penalizari`, `luna`, `indexNou`, `indexVechi`, `cost`, `cantitate`, `observatii`, `procesata`)
    VALUES (null, '".date('d-m-Y')."', '$asocId', '$scaraId', '$tipFactura', '$servId', '".$_POST['numarFactura']."', '".$_POST['serieFactura']."', '".$_POST['dataEmitere']."', '".$_POST['dataScadenta']."', '".$_POST['debite']."', '".$_POST['penalizari']."', '".$_POST['luna']."', '".$_POST['indexNou']."', '".$_POST['indexVechi']."', '".$_POST['valoareFactura']."', '".$_POST['diferenta']."', '".$_POST['observatii']."', 0) ";
    $facturaScaraCuIndecsi = mysql_query($facturaScaraCuIndecsi) or die ("#Factura: 1004 -- Nu pot insera factura/scara - cu indecsi<br />".mysql_error());

	$getFacturaIdSQL = "SELECT fact_id FROM facturi WHERE asoc_id='$asocId' AND scara_id='$scaraId' AND tipServiciu='$servId' AND numarFactura='".$_POST['numarFactura']."' AND serieFactura='".$_POST['serieFactura']."' ORDER BY `fact_id` DESC ;";
	$getFacturaId =  mysql_query($getFacturaIdSQL) or die ("#Factura: 1004-'$t' -- Eroare citirea indexului facture curente <br />".$getFacturaIdSQL."<br />".mysql_error());
	$getFacturaId = mysql_result($getFacturaId, 0, 'fact_id');

	$dataScadentaTrans = explode('-', $_POST['dataScadenta']);
	$dataScadentaTrans = $dataScadentaTrans[2].'-'.$dataScadentaTrans[1].'-'.$dataScadentaTrans[0];


	$insertFisaFurnizoriSQL = "INSERT INTO `fisa_furnizori` (`id`, `operator_id`, `ip`, `asoc_id`, `scara_id`, `loc_id`, `fur_id`, `serviciu_id`, `fact_id`, `data_inreg`, `data_scadenta`, `document`, `explicatii`, `valoare`, `penalizare`, `procent`)
        	VALUES (NULL, '". $_SESSION['rank']."', '".$_SERVER['REMOTE_ADDR']."', '$asocId', '$scaraId', NULL, '$furnizor', '$servId', '$getFacturaId', '".date('Y-m-d')."', '".$dataScadentaTrans."', '".$_POST['numarFactura']."/".$_POST['serieFactura']."', 'Factura', '".$_POST['valoareFactura']."', NULL, NULL);";
	mysql_query($insertFisaFurnizoriSQL) or die ("#Factura: 1004 -- Eroare la introducerea facturi in fisa_furnizori <br />".$insertFisaFurnizoriSQL."<br />".mysql_error());

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
if ($_GET['asoc_id'] != "") {
    $selectAsoc = "SELECT * FROM asociatii WHERE asoc_id<>".$_GET['asoc_id']." ORDER BY administrator_id, asoc_id";
} else {
    $selectAsoc = "SELECT * FROM asociatii"." ORDER BY administrator_id, asoc_id";
}
$selectAsoc = mysql_query($selectAsoc) or die ("#Facturi: 1 -- Nu pot selecta asociatiile<br />".mysql_error());

while ($asoc = mysql_fetch_array($selectAsoc)) {
    $asociatie .= '<option value="'.$asoc[0].'">'.$asoc[1].'</option>';
}

/*	SELECTUL PENTRU AFISAREA SCARILOR	*/
if ($_GET['asoc_id'] != '') {
    if ($_GET['scara_id'] != '') {
        $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id<>".$_GET['scara_id']." AND scara_id IN (SELECT scara_id FROM scari_furnizori SF WHERE fur_id IN (SELECT F.fur_id FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=FS.fur_id AND S.serviciu=\"Gaz\" AND S.serv_id=FS.serv_id))";
    } else {
        $selectScara = "SELECT * FROM scari WHERE asoc_id=".$_GET['asoc_id']." AND scara_id IN (SELECT scara_id FROM scari_furnizori SF WHERE fur_id IN (SELECT F.fur_id FROM furnizori F, furnizori_servicii FS, servicii S WHERE F.fur_id=FS.fur_id AND S.serviciu=\"Gaz\" AND S.serv_id=FS.serv_id))";
    }
    $selectScara = mysql_query($selectScara) or die ("#Facturi: 2 -- Nu pot selecta scarile pentru asociatia aleasa<br />".mysql_error());

    while ($scari = mysql_fetch_array($selectScara)) {
        $scara .= '<option value="'.$scari[0].'">Bloc '.$scari[5].', scara '.$scari[2].'</option>';
    }
}
include_once('util_facturi.php');
?>

<script type="text/javascript">
    function select_asoc(value1){
        window.location = "index.php?link=facturi_gaz&asoc_id=" + value1 + "&tipFactura=2" ;
    }

    function select_scara(value1, value2, value3){
        window.location = "index.php?link=facturi_gaz&asoc_id=" + value1 + "&tipFactura=2&scara_id=" + value3;
    }

    function canti(valoare){
        var iNou = document.getElementById(valoare + 'indexNou').value;
        var iVechi = document.getElementById(valoare + 'indexVechi').value;

        if ((iNou != null) && (iVechi != null)){
            document.getElementById(valoare + 'diferenta').value = iNou - iVechi;
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
            <td width="388" bgcolor="#CCCCCC" colspan="2"><strong>Inserare factura Gaz</strong></td>
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
        if ($_GET['tipFactura'] == '2') {
            echo '<tr>';
            echo '<td width="173" align="left" bgcolor="#CCCCCC">( 2 ) Alegeti scara:</td>';
            echo '<td width="215" align="left" bgcolor="#CCCCCC">';
            echo '<select style="width:125px" onchange="select_scara('.$_GET['asoc_id'].','.$_GET['tipFactura'].', this.value);">';
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

<?php if ($_GET['asoc_id'] != "" && $_GET['scara_id'] != "" ) { 
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////                                                      //////////
//////////                       Formular                       //////////
//////////                                                      //////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
    ?>

<form id="FactEON" method="post" enctype="multipart/form-data">
    <input type="hidden" name="insereaza" value="OK" />
    <input type="hidden" name="tipFactura" value="2" />
    <table width="750" style="float:left;  margin-top:10px; background-color:#BBBBBB;">

    <?php
    $furniz = "SELECT F.furnizor as furnizor, F.fur_id as id FROM furnizori F, furnizori_servicii FS, servicii S, scari_furnizori SF WHERE F.fur_id=FS.fur_id AND S.serviciu=\"gaz\" AND S.serv_id=FS.serv_id AND F.fur_id=SF.fur_id AND scara_id='".$_GET['scara_id']."'";
    $furniz = mysql_query($furniz) or die ("#Factura: 4 -- Nu pot selecta furnizorii pentru asociatie<br />".mysql_error());

    echo '<tr bgcolor="#000" style="color:#FFF" align="center"><td colspan="5"><strong>Introducere factura emisa de '.mysql_result($furniz, 0, 'furnizor').' - Serviciu facturat: Gaz </strong></td></tr>';
    ?>
    <tr bgcolor="#CCC"><td colspan="5"><center><strong><?php if(isset($facturaScaraCuIndecsi) && $facturaScaraCuIndecsi==true) echo "Factura de gaz a fost incregistrata cu succes"; ?></strong></center> &nbsp; </td></tr>

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
        <td><input type="text" class="ver_req datepicker" name="dataScadenta" /></td>
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
        <td>
            <select name="luna" style="width:125px">
                <?php echo Util_facturi::getSelectLuna($_GET['asoc_id'], (isset($_POST['luna']) ? $_POST['luna'] : null)); ?>
            </select>
        </td>
        <td>
            <?php
            $furn_id = mysql_result($furniz, 0, 'id');
            $furn_nume = mysql_result($furniz, 0, 'furnizor');
            echo $furn_nume;
            ?>
            <input type="hidden" name="fur_id" value="<?php echo $furn_id; ?>" />

        </td>
    </tr>
    <tr bgcolor="#000000" style="color:#FFFFFF">
        <td width="125px">Index Vechi</td>
        <td width="125px">Index Nou</td>
        <td width="125px">Diferenta</td>
        <td colspan="2 "width="125px">&nbsp;</td>
    </tr>

    <tr bgcolor="#DDDDDD">
        <td><input type="text" class="ver_nr ver_req" name="indexVechi" id="indexVechi" onkeyup="canti('');"/></td>
        <td><input type="text" class="ver_nr ver_req" name="indexNou" id="indexNou" onkeyup="canti('');"/></td>
        <td><input type="text" class="ver_nr ver_req" name="diferenta" id="diferenta" readonly="readonly"/></td>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="4">&nbsp;</td>
        <td><input type="submit" value="Salveaza"/></td>
    </tr>
    </table>
</form>
    <?php
}	//endul de la "potAfisa"
?>