<script type="text/javascript">
var actiune = "nedefinita";
var facturi = "";

var activValidation = false;

function select_change() {

	window.location = "index.php?link=plati-furnizori"+
	"&fur_id="+document.getElementById("change_furnizor").value+
	"&asoc_id="+document.getElementById("change_asociatie").value+
	"&plata="+document.getElementById("change_plata").value+
	"&orderBy="+document.getElementById("change_orderBy").value;
}

function selectPlata(value){
	var val = $("#text_factId_"+value.title);
	var op = $("#text_op_"+value.title);
	var cls_obj = val.attr('class');
	if(value.checked) {
		val.removeAttr('disabled');
		op.removeAttr('disabled');
		val.val(value.value);
		facturi += value.title + " ";
	} else {
		val.attr('disabled', 'disabled');
		op.attr('disabled', 'disabled');
		val.val("");
		op.val("");
		facturi = facturi.replace(value.title + " ","");
	}
	$("#facturi").val(facturi);
	cls_obj = cls_obj.split(' ');
	for (var row in cls_obj) {
		if (cls_obj[row].indexOf('furId_') != -1) {
			recalculareTotal(cls_obj[row].substring(cls_obj[row].indexOf('_')+1));
			break;
		}
	}

	//recalculareTotal(value.title);
}

function tipActiune(el){
	if (actiune=="Borderou") {
		var data = "Cont sursa"+String.fromCharCode(9)+"Cont destinatie"+String.fromCharCode(9)+"Suma Beneficiar"+String.fromCharCode(9)+"Detalii 1"+String.fromCharCode(9)+"Detalii 2\n";
		$("#form input:[id^='text_factId_']not([disabled])").each(function (index, el) {
			var cl = $(el).attr('class');
			var value = $(el).attr("value");
			cl = cl.split(' ');
			var contAsoc = ''; var contFur = ''; var codClient = ''; var document = '';
			for (var row in cl)
			{
				if (cl[row].indexOf('contAsoc_') != -1) {
					contAsoc = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('codClient_') != -1) {
					codClient = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('document_') != -1) {
					document = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('contFur_') != -1) {
					contFur = cl[row].substring(cl[row].indexOf('_')+1);
				}
			}
			data += contAsoc +String.fromCharCode(9)+ contFur +String.fromCharCode(9)+ value +String.fromCharCode(9)+"Fctura: "+ document +String.fromCharCode(9)+"Cod client: "+ codClient +"\n";
		});
		//alert(data);
		uri = 'data:text/plain;charset=utf-8;base64,' + escape(base64encode(data));
		window.open(uri);
		return false;
	} else
	if (actiune=="Inregistrare") {
		var er = false;
		activValidation = true;
		$("#form input:[id^='text_op_']not([disabled])").each(function (index, el) {
				op = $(el);
				if(op.val() == "")
				{
					op.addClass("error");
					er = true;
				} else if (op.hasClass("error")) {
					op.removeClass("error");
				}
		});
		if (er) {
			$("#error").removeClass("hidden");
		} else {
			$("#facturi").val(facturi.substring(0,facturi.length -1));
		}
                if ($("#facturi").val() == '') {
                    alert('Trebuie sa faceti macar o plata catre un furnizor')
                    return false;
                }
		return !er;
	} else
	if (actiune=="CSV") {
		var data = "Cont sursa"+","+"Cont destinatie"+","+"Suma Beneficiar"+","+"Detalii 1"+","+"Detalii 2\n";
		$("#form input:[id^='text_factId_']not([disabled])").each(function (index, el) {
			var cl = $(el).attr('class');
			var value = $(el).attr("value");
			cl = cl.split(' ');
			var contAsoc = ''; var contFur = ''; var codClient = ''; var document = '';
			for (var row in cl)
			{
				if (cl[row].indexOf('contAsoc_') != -1) {
					contAsoc = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('codClient_') != -1) {
					codClient = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('document_') != -1) {
					document = cl[row].substring(cl[row].indexOf('_')+1);
				}
				if (cl[row].indexOf('contFur_') != -1) {
					contFur = cl[row].substring(cl[row].indexOf('_')+1);
				}
			}
			data += contAsoc +","+ contFur +","+ value +","+"Fctura: "+ document +","+"Cod client: "+ codClient +"\n";
		});
		//alert(data);
		uri = 'data:text/csv;charset=utf-8;base64,' + escape(base64encode(data));
		window.open(uri);
		return false;
	}
	alert("Actiune nedefinita");
	return false;
}

function validare_op(op){
	var op = $(op);
	if(op.val() == "")
	{
		op.addClass("error");
	} else if (op.hasClass("error")) {
		op.removeClass("error");
	}
}

function onBorderou(){
	actiune="Borderou";
}

function onCSV(){
	actiune="CSV";
}

function onInregistrare(){
	actiune="Inregistrare";
}

function recalculareTotal(furId){
	var total = $('#total_'+furId);
	total_val_fact = 0;
	$(':not(:disabled):text.furId_'+furId).each(function (index, el) { total_val_fact += parseFloat($(el).val()) })
	total.val(total_val_fact);
}

var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

function base64encode(str) {
    var out; var i; var len;
    var c1; var c2; var c3;
    var len = str.length;
    var i = 0;
    out = "";
    while(i < len) {
        c1 = str.charCodeAt(i++) & 0xff;
        if(i == len) {
            out += base64EncodeChars.charAt(c1 >> 2);
            out += base64EncodeChars.charAt((c1 & 0x3) << 4);
            out += "==";
            break;
        }
        c2 = str.charCodeAt(i++);
        if(i == len) {
            out += base64EncodeChars.charAt(c1 >> 2);
            out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
            out += base64EncodeChars.charAt((c2 & 0xF) << 2);
            out += "=";
            break;
        }
        c3 = str.charCodeAt(i++);
        out += base64EncodeChars.charAt(c1 >> 2);
        out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += base64EncodeChars.charAt(c3 & 0x3F);
    }
    return out;


}

 $(function() {
    $( "#dataOperatiune" ).datepicker({ dateFormat: "yy-mm-dd" });
  });

</script>

<?php
include_once(  'modules/fise/Furnizori.class.php');

/*******************  SELECTEAZA FURNIZORUL  *******************/
$sql = "SELECT * FROM furnizori";
$sql = mysql_query($sql) or die("Nu pot selecta furnizorii pt afisarea lor in lista furnizorilor<br />".mysql_error());
$furnizori = '';
while($row = mysql_fetch_array($sql)) {
	$furnizori .= '<option ';
	if(isset($_GET['fur_id']) && $row[0] == $_GET['fur_id']) $furnizori .= 'selected="yes" ';
	$furnizori .= 'value="'.$row[0].'">'.$row[1].'</option>';
}

/*******************  SELECTEAZA Asociatii  *******************/
$sql = "SELECT * FROM asociatii";
$sql = mysql_query($sql) or die("Nu pot selecta asociatiile pt afisarea lor in lista asociatiilor<br />".mysql_error());
$asociatii = '';
while($row = mysql_fetch_array($sql)) {
	$asociatii .= '<option ';
	if(isset($_GET['asoc_id']) && $row[0] == $_GET['asoc_id']) $asociatii .= 'selected="yes" ';
	$asociatii .= 'value="'.$row[0].'">'.$row[1].'</option>';
}

/*******************  TIPURI FACTURI & STUFF  *******************/

?>

<style type="text/css">
	thead tr td { border:solid 1px #000; color:#FFF; }
	tbody { border:solid 1px #000; }
	tbody tr td input {border:none; }
	tbody tr.newline td { border:solid 1px #0CC;   }
	tfoot { color:#FFF; }
	.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
	.addnew2 {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:95px; margin-top:-9px;  }
	tr.newline input { text-align:center; }
	.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
	a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
	#print {float:left; margin-left:700px; margin-top:15px;}
	#error {background-color:#A63232; color:#E5F694; padding:5px; border:2px solid #AFB981;}
	.error {background-color:#942E2E; color:#FFFFFF; border:2px solid #790000;}
	.hidden {visibility:hidden;}
</style>

<div id="content" style="float:left;">
<table width="400">
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Alegeti Furnizorul:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_furnizor" onChange="select_change()">
				<?php echo '<option value="all">Toti</option>'; ?>
        <?php echo $furnizori; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Alegeti Asociatia:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_asociatie" onChange="select_change()">
				<?php echo '<option value="all">Toate</option>'; ?>
        <?php echo $asociatii; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Platiti:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_plata" onChange="select_change()">
				<option value="valoare" <?php if(isset($_GET['plata']) && $_GET['plata'] == 'valoare') echo 'selected="selected"'; ?> >Debite</option>
				<option value="penalizare" <?php if(isset($_GET['plata']) && $_GET['plata'] == 'penalizare') echo 'selected="selected"'; ?> >Penalizari</option>
			</select>
		</td>
	</tr>
	<tr>
		<td width="173" align="left" bgcolor="#CCCCCC">Ordonati dupa:</td>
		<td width="215" align="left" bgcolor="#CCCCCC">
			<select id="change_orderBy" onChange="select_change()">
				<option value="fur_id" <?php if(isset($_GET['orderBy']) && $_GET['orderBy'] == 'fur_id') echo 'selected="selected"'; ?> >Furnizori</option>
				<option value="asoc_id" <?php if(isset($_GET['orderBy']) && $_GET['orderBy'] == 'asoc_id') echo 'selected="selected"'; ?> >Asociatii</option>
			</select>
		</td>
	</tr>
</table>
</div>

<form id="form" action="index.php?link=plati-furnizori" method="post" onsubmit="return tipActiune(this)">
<input type="hidden" name="facturi" id="facturi" value="" />
<div id="print">
  	<a href="#">printeaza</a>
</div>
<table width="750" style="float:left;  margin-top:10px; background-color:#CCC;">
<tr><td colspan="8"><span id="error" class="hidden" ><b>A a parut o eroare, va rugam sa completati ordinul de plata pt toate facturile</b></span></td></tr>
<?php
if (isset($_POST['actiune']) && $_POST['actiune']=='Inregistrare') {
	$facturi = isset($_POST['facturi']) ? $_POST['facturi'] : '';
	$facturi = explode(' ', $facturi);
	// echo '<div>';
	// var_dump($facturi);
	// var_dump($_POST);
	// die('</div></table>');
	foreach ($facturi as $key => $data) {
		Furnizori::insertPlata($data, $_POST['val_'.$data], $_POST['op_'.$data], $_POST['tip_plata'], strtotime($_POST['dataOperatiune']));
	}
}?>

<?php 
	if ((!isset($_GET['fur_id']) || $_GET['fur_id']=='all') && (!isset($_GET['asoc_id']) || $_GET['asoc_id']=='all')) {}
	else {
		$plata = !isset($_GET['plata']) ? 'valoare' : (($_GET['plata'] != 'valoare' && $_GET['plata'] != 'penalizare') ? 'valoare' : $_GET['plata']);
		$orderBY = !isset($_GET['orderBy']) ? 'fur_id' : (($_GET['orderBy'] != 'fur_id' && $_GET['orderBy'] != 'asoc_id') ? 'fur_id' : $_GET['orderBy']);
		if(isset($_GET['asoc_id']) && $_GET['asoc_id']=='all') $_GET['asoc_id'] = NULL;
		if(isset($_GET['fur_id']) && $_GET['fur_id']=='all') $_GET['fur_id'] = NULL;
		$data = Furnizori::getPlati($orderBY, isset($_GET['asoc_id']) ? $_GET['asoc_id'] : NUll, isset($_GET['fur_id']) ? $_GET['fur_id'] : NUll);

		?><input type="hidden" name="tip_plata" value="<?php echo $plata; ?>" /><?php

		$furnizor = NULL;
		$asociatie = NULL;
		$scara = NULL;
		$facturaID = NULL;
	$facturaColor = 0;
foreach($data as $key => $row) {
	if(($row['fur_id'] != $furnizor && $orderBY == 'fur_id') || $row['asoc_id'] != $asociatie) {
		if($orderBY == 'fur_id' && $row['fur_id'] != $furnizor) {
			echo '<tr><td colspan="7" >'.$row['furnizor'].'('.$row['serviciu'].') => Total:<input type="text" readonly="readonly" disabled="disabled" id="total_'.$row['fur_id'].'" value="0" /></td></tr>';
			$furnizor = $row['fur_id'];
			$asociatie = null;
			$facturaID = null;
		}
		if($row['asoc_id'] != $asociatie) {
			echo '<tr><td colspan="7" >'.$row['asociatie'].' - '.$row['scara'].'</tr>';
			$asociatie = $row['asoc_id'];
			$facturaID = null;
		}
		?>
		<tr><td></td></tr>
		<tr>
			<?php if($orderBY == 'asoc_id') echo '<td bgcolor="#666666">Furnizor</td>'; ?>
			<td bgcolor="#666666">Document</td>
			<td bgcolor="#666666">Explicatie</td>
			<td bgcolor="#666666">Data scadenta</td>
			<td bgcolor="#666666">Zile</td>
                        <td bgcolor="#666666" width="70">Valoare</td>
			<td bgcolor="#666666" width="70">Penalizarii</td>
			<td bgcolor="#666666">Plata</td>
			<td bgcolor="#666666">Act Doveditor</td>
		</tr>
		<?php
		$infos = array();
		if($orderBY == 'asoc_id') $infos [] = 'furnizor';
		$infos [] = 'document';
		$infos [] = 'explicatii';
		$infos [] = 'data_scadenta';
		$infos [] = 'zile_ramase';
	}
	if ($facturaID != $row['fact_id']) {
		$facturaID = $row['fact_id'];
		$facturaColor ++;
		echo '<tr s><td  height="1"></td></tr>';
		$valFactura = 0;
	}
	$valFactura += $plata == 'valoare' ? $row['valoare'] : $row['penalizare'];
	?>
	<tr class="<?php echo"val_".$row['fact_id']; ?>" >
		<?php foreach ($infos as $info) {?>
		<td class="<?php echo $info; ?>" bgcolor="#<?php echo ($facturaColor % 2) == 0 ? 'aaaaaa' : '999999' ; ?>"><?php echo $row[$info]; ?></td>
		<?php }// endforeach; ?>
                <td class="<?php echo $info; ?>" bgcolor="#<?php echo ($facturaColor % 2) == 0 ? 'aaaaaa' : '999999' ; ?>" <?php echo $row['valoare'] >= 0 ? 'align="right"' : 'align="left"'; ?>><?php echo abs(round($row['valoare'],2)); ?></td>
		<td class="<?php echo $info; ?>" bgcolor="#<?php echo ($facturaColor % 2) == 0 ? 'aaaaaa' : '999999' ; ?>" <?php echo $row['penalizare'] >= 0 ? 'align="right"' : 'align="left"'; ?>><?php echo abs(round($row['penalizare'],2)); ?></td>
		<?php
		if (!isset( $data[$key+1]) || $facturaID != $data[$key+1]['fact_id'] || $data[$key+1]['asoc_id'] != $asociatie || $data[$key+1]['fur_id'] != $furnizor) {
			?>
			<td><input type="checkbox" class="furId_<?php echo $row['fur_id']; ?>" id="check_factId_<?php echo $row['fact_id']; ?>" value="<?php echo round($valFactura, 2); ?>" title="<?php echo $row['fact_id']; ?>" onchange="selectPlata(this)"/>
					<input type="text" size="7" class="furId_<?php echo $row['fur_id']; ?> contAsoc_<?php echo $row['cont_asociatie']; ?> codClient_<?php echo $row['codClient'];?> document_<?php echo $row['document'];?> contFur_<?php echo $row['cont_furnizor'];?>" id="text_factId_<?php echo $row['fact_id']; ?>" name="val_<?php echo $row['fact_id']; ?>" value="" disabled="disabled" onchange="recalculateTotal(<?php echo $row['fur_id']; ?>)" onblur="recalculateTotal(<?php echo $row['fur_id']; ?>)"/></td>
			<td><input type="text" size="10"  id="text_op_<?php echo $row['fact_id']; ?>" name="op_<?php echo $row['fact_id']; ?>" value="" disabled="disabled" onblur="validare_op(this)" /></td>
			<?php
		} else
			echo '<td colspan="2"></td>';
		?>
	</tr>
	<?php
}
}

?>
</table>
<input type="text" name="dataOperatiune" value="<?php echo date('Y-m-d'); ?>" id="dataOperatiune" />
<input type="submit" name="actiune" value="ING" onclick="onBorderou()" />
<input type="submit" name="actiune" value="CSV" onclick="onCSV()" />
<input type="submit" name="actiune" value="Inregistrare" onclick="onInregistrare()" />
</form>