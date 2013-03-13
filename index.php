<?php
ini_set('display_errors','off');
//ini_set('display_errors','1');
//error_reporting(E_ALL);
        session_start();

        include_once 'componente/config.php';
        include_once 'Util.php';
        include_once 'log.php';
		//header("Expires: Sat, 26 Jul 2097 05:00:00 GMT");

myLog();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <title>uAdmin</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" type="text/css" href="css/jquerycssmenu.css" />
        <link rel="stylesheet" type="text/css" href="css/butoane.css" />

		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

		<!--
        <script type="text/javascript" src="js/jquery_min.js"></script>
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
		-->

        <script type="text/javascript" src="js/jquerycssmenu.js"></script>
        <script language="javascript" type="text/javascript" src="js/search/actb.js"></script>
        <script language="javascript" type="text/javascript" src="js/search/common.js"></script>

        <script language="JavaScript" src="js/calendar_us.js"></script>
		<script type="text/javascript" src="js/jquery.quicksearch.js"></script>
        <link rel="stylesheet" href="css/calendar.css" type="text/css" />
<style type="text/css">

#header { min-height:65px; width:100%; background-color: #CCC; margin:0; float:left; font-family:Arial, Helvetica, sans-serif; }
#header img { float:left; }
#time { background-color:#CCC; margin:0; float:left; width:100%; text-align:right; padding-top:3px; padding-bottom:3px; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333;  }
#txt { float:right; margin-right:5px; }
#nume { float:right; font-size:22px; font-weight:bold; color:#eee; margin-right:10px; text-align:right; margin-top:10px;  }
#info { clear:right; float:right; margin-top:0px; margin-right:10px; font-size:12px; color:#FFF; }
#info a { color:#0CC; text-decoration:none; }
#info a:hover { text-decoration:underline; }
#header h2 { padding:0; float:left; margin:25px 20px 0px 0px; color:white; }

</style>
<script type="text/javascript">


  $(document).ready(function() {
    $(".datepicker").datepicker({ dateFormat: 'dd-mm-yy' });
	$('#clicker').click(function() {
	var chc = $('.debifat').attr('checked');

	if(chc == true) {
		$('.debifat').removeAttr("checked");
	} else {
		$('.debifat').attr('checked','checked'); }
	});
  });

  $(document).ready(function() {
    $(".datepicker1").datepicker({ dateFormat: 'yy-mm-dd' });
  });


function startTime()
{
var today=new Date();
var h=today.getHours();
var m=today.getMinutes();
var s=today.getSeconds();
// add a zero in front of numbers<10
m=checkTime(m);
s=checkTime(s);
document.getElementById('txt').innerHTML=h+":"+m+":"+s;
t=setTimeout('startTime()',500);
}

function checkTime(i)
{
if (i<10)
  {
  i="0" + i;
  }
return i;
}




</script>

</head>
<body onload="startTime()">
    <?php
       if ($_SESSION['login'] != '') {
                $link = mysql_real_escape_string($_GET['link']);



                include_once 'componente/header.php';
                include_once 'componente/meniu.php';

                echo '<div id="wrapper"><div id="secWrapper"><div id="container" class="clearfix">';
                        if ($link == 'home') {
                            include_once 'modules/prima.php';

			   /************* MENIU CASIERIE ******************/
			   } else if ($link == 'casierie') {
				include_once 'modules/casierie/casierie.php';
			   } else if ($link == 'plata') {
				include_once 'modules/casierie/plata.php';
			   } else if ($link == 'istoric_plati') {
				include_once 'modules/casierie/istoric_plati.php';

			   /************* RESTRICTIONARE ACCES ******************/
			   } else if ($link == 'no_access') {
				include_once 'componente/no_access.php';

                        /************* MENIU INFORMATII ******************/
                        } else if ($link == 'strazi') {
                            include_once 'modules/informatii/strazi.php';
                        } else if ($link == 'servicii') {
                            include_once 'modules/informatii/servicii.php';
                        } else if ($link == 'conturi') {
                            include_once 'modules/informatii/conturi.php';
                        } else if ($link == 'pachete') {
                            include_once 'modules/informatii/pachete.php';

                        /************* WIZARD ******************/
                        } else if ($link == 'wizard') {
                            include_once 'modules/wizard/asociatii/asociatii.php';
                        } else if ($link == 'w_asoc_dat') {
                            include_once 'modules/wizard/asociatii/asociatii_dat_init.php';
                        } else if ($link == 'w_asoc_fonduri') {
                            include_once 'modules/wizard/asociatii/asociatii_fonduri.php';
                        } else if ($link == 'w_asoc_setari') {
                            include_once 'modules/wizard/asociatii/asociatii_setari_init.php';
                        } else if ($link == 'w_scari') {
                            include_once 'modules/wizard/scari/scari.php';
                        } else if ($link == 'w_scari_furnizori') {
                            include_once 'modules/wizard/scari/scari_furnizori.php';
                        } else if ($link == 'w_scari_setari') {
                            include_once 'modules/wizard/scari/scari_setari.php';
                        } else if ($link == 'w_scari_dat') {
                            include_once 'modules/wizard/scari/scari_dat_init.php';
                        } else if ($link == 'w_locatari') {
                            include_once 'modules/wizard/locatari/locatari.php';
                        } else if ($link == 'w_locatari_apometre') {
                            include_once 'modules/wizard/locatari/locatari_ap.php';
                        } else if ($link == 'w_locatari_dat') {
                            include_once 'modules/wizard/locatari/locatari_dat.php';
                        } else if ($link == 'w_locatari_fond') {
                       		include_once 'modules/wizard/locatari/locatari_fond.php';
                        } else if ($link == 'w_locatari_fond') {
                            include_once 'modules/wizard/locatari/locatari_fond.php';
			   /************* MANAGEMENT USERI ******************/
			   } else if ($link == 'adauga_user') {
                            include_once 'modules/wizard/users/adauga_user.php';
                           } else if ($link == 'schimba_parola') {
                            include_once 'modules/wizard/users/schimba_parola.php';

                        /************* MENIU ******************/
                        } else if ($link == 'asociatii') {
                            include_once 'modules/asociatii/asociatii.php';
                        } else if ($link == 'asoc_dat') {
                            include_once 'modules/asociatii/asociatii_dat_init.php';
                        } else if ($link == 'asoc_fonduri') {
                            include_once 'modules/asociatii/asociatii_fonduri.php';
                        } else if ($link == 'asoc_setari') {
                            include_once 'modules/asociatii/asociatii_setari_init.php';

			   /************* FISE ******************/
			   } else if ($link == 'fisa_pen') {
			     include_once 'modules/fise/fisa_penalizari.php';
			   } else if ($link == 'fisa_indv') {
			     include_once 'modules/fise/fisa_individuala.php';
			   } else if ($link == 'fisa_cons') {
			     include_once 'modules/fise/fisa_consumuri.php';
			   } else if ($link == 'fisa_cont') {
                            include_once 'modules/fise/fisa_cont.php';
			   } else if ($link == 'fisa_furnizori') {
                            include_once 'modules/fise/fisa_furnizori.php';
			   } else if ($link == 'fisa_fonduri') {
                            include_once 'modules/fise/fisa_fonduri.php';

			   } else if ($link == 'lista_plata') {
                            include_once 'modules/fise/lista_plata.php';

			   } else if ($link == 'scari') {
                            include_once 'modules/scari/scari.php';
                        } else if ($link == 'scari_dat') {
                            include_once 'modules/scari/scari_dat_init.php';
                        } else if ($link == 'scari_furnizori') {
                            include_once 'modules/scari/scari_furnizori.php';
                        } else if ($link == 'scari_setari') {
                            include_once 'modules/scari/scari_setari.php';
						} else if ($link == "scari_pasante") {
						echo '<h1>TEST</h1>';
                            include_once 'modules/scari/scari_pasante.php';
                        } else if ($link == 'locatari') {
                            include_once 'modules/locatari/locatari.php';
                        } else if ($link == 'locatari_ap') {
                            include_once 'modules/locatari/locatari_ap.php';
                        } else if ($link == 'locatari_dat') {
                            include_once 'modules/locatari/locatari_dat.php';
                        } else if ($link == 'locatari_apometre') {
                            include_once 'modules/locatari/locatari_apometre.php';
                        } else if ($link == 'locatari_sub') {
                        	include_once 'modules/locatari/locatari_subventii.php';

                        } else if ($link == 'locatar') {
                            include_once 'modules/locatar/locatar.php';
                        } else if ($link == 'locatar_ap') {
                            include_once 'modules/locatar/locatar_ap.php';
                        } else if ($link == 'locatar_dat') {
                            include_once 'modules/locatar/locatar_dat.php';

                        } else if ($link == 'facturi_aparece') {
                            include_once 'modules/furnizori/facturi_aparece.php';
                        } else if ($link == 'facturi_apacalda') {
                            include_once 'modules/furnizori/facturi_apacalda.php';
                        } else if ($link == 'facturi_incalzire') {
                            include_once 'modules/furnizori/facturi_incalzire.php';
                        } else if ($link == 'facturi_iluminat') {
                            include_once 'modules/furnizori/facturi_iluminat.php';
                        } else if ($link == 'facturi_gaz') {
                            include_once 'modules/furnizori/facturi_gaz.php';
                        } else if ($link == 'facturi') {
                            include_once 'modules/furnizori/facturi.php';
			} else if ($link == 'facturiV2') {
                            include_once 'modules/furnizori/facturiV2.php';
                        } else if ($link == 'facturiV3') {
			    include_once 'modules/furnizori/facturiV3.php';
			} else if ($link == 'pachete-urbica') {
			    include_once 'modules/furnizori/pachete_urbica.php';
                        } else if ($link == 'furnizori') {
                            include_once 'modules/furnizori/furnizori.php';
                        } else if ($link == 'furnizori-servicii') {
                            include_once 'modules/furnizori/furnizori-servicii.php';
                       } else if ($link == 'plati-furnizori') {
                        include_once 'modules/furnizori/plati_furnizori.php';


                         /************* MENIU ******************/
                        } else if ($link == 'raport') {
                            include_once 'modules/pdf/factura1.php';
                        } else if ($link == 'rapoarte') {
                            include_once 'modules/rapoarte/rapoarte.php';
						} else if ($link == 'reg_jurn') {
							include_once 'modules/rapoarte/reg_jurn.php';
                        } else if ($link == 'situatieActivPasiv') {
                            include_once 'modules/rapoarte/situatieActivPasiv.php';
                         /************* FINAL ******************/
			   } else if ($link == 'genereazaListe') {
                           include_once 'modules/final/lista_plata_cron.php';
			   } else if ($link == 'proceseazaFacturi') {
                           include_once 'modules/final/proceseazaFacturi.php';

			    /************* ALL BUT TESTS ******************/
			   } else if ($link == 'XXX') {
                           include_once 'modules/final/unused/lista_plata_cron1.php';

                        /************* LOGIN + PRIMA ******************/
                        } else if ($link == 'logout') {
                            include_once 'componente/logout.php';
                        } else {
                            include_once 'modules/prima.php';
                        }

                        if ($link != 'wizard' && $link !='w_scari_setari' && $link !='w_locatari' &&
                                $link !='w_locatari_apometre' &&
                                $link !='w_locatari_dat'
                            ) {
                           // include_once 'componente/bara_dreapta.php';
                        }
                echo '</div></div></div>';
       } else {
              echo '<div id="wrapper"><div id="secWrapper"><div id="container" style="height:600px;" class="clearfix"><br /><br /><br /><br /><br /><br /><br />';
              include_once 'modules/login.php';
              echo '</div></div></div>';
       }
    ?>
</body>
</html>
