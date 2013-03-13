<?php
function mydate() {
$zisapt = date('l');
switch($zisapt) {
	case 'Monday':
	$zisapt = "Luni";
	break;
	case 'Tuesday':
	$zisapt = "Marti";
	break;
	case 'Wednesday':
	$zisapt = "Miercuri";
	break;
	case 'Thursday':
	$zisapt = "Joi";
	break;
	case 'Friday':
	$zisapt = "Vineri";
	break;
	case 'Saturday':
	$zisapt = "Sambata";
	break;
	case 'Sunday':
	$zisapt = "Duminica";
	break;
	}
$month = date('F');
switch($month) {
	case 'January':
	$month = 'Ianuarie';
	break;
	case 'February':
	$month = 'Februarie';
	break;
	case 'March':
	$month = 'Martie';
	break;
	case 'April':
	$month = 'Aprilie';
	break;
	case 'May':
	$month = 'Mai';
	break;
	case 'June':
	$month = 'Iunie';
	break;
	case 'July':
	$month = 'Iulie';
	break;
	case 'August':
	$month = 'August';
	break;
	case 'September':
	$month = 'Septembrie';
	break;
	case 'October':
	$month = 'Octombrie';
	break;
	case 'November':
	$month = 'Noiembrie';
	break;
	case 'December':
	$month = 'Decembrie';
	break;	
}
echo $zisapt.", ".date('d')." ".$month." ".date('Y');
	//Tuesday, 30 March 2010
	
}
     if ($link == 'home') {
            $title = 'Acasa';
/************* CASIERIE ******************/            
		} else if ($link == 'casierie') {
	     $title = 'Casierie';
		} else if ($link == 'plata') {
	     $title = 'Casierie'; 
		} else if ($link == 'istoric_plati') {
	     $title = 'Istoric Plati';

/************* MENIU INFORMATII ******************/            
        } else if ($link == 'furnizori') {
            $title = 'Furnizori';
        } else if ($link == 'plati-furnizori') {
            $title = 'Plati Catre Furnizori';
        } else if ($link == 'strazi') {
            $title = 'Strazi';
        } else if ($link == 'servicii') {
            $title = 'Servicii';
        } else if ($link == 'conturi') {
            $title = 'Conturi Bancare';
        } else if ($link == 'pachete') {
            $title = 'Pachete Servicii';
        } else if ($link == 'furnizori-servicii') {
            $title = 'Furnizori Servicii';

/************* WIZARD ******************/
        } else if ($link == 'wizard') {
            $title = 'Wizard: Adauga Asociatie';
        } else if ($link == 'w_asoc_dat') {
            $title = 'Wizard: Adauga Furnizori Asociatie';
        } else if ($link == 'w_asoc_fonduri') {
            $title = 'Wizard: Adauga Fonduri Asociatie';
        } else if ($link == 'w_asoc_setari') {
            $title = 'Wizard: Setari Asociatie';
        } else if ($link == 'w_scari') {
            $title = 'Wizard: Adauga Scari';
        } else if ($link == 'w_scari_setari') {
            $title = 'Wizard: Adauga Setari Initiale Scari';
        } else if ($link == 'w_scari_furnizori') {
            $title = 'Wizard: Adauga Furnizori Scari';
        } else if ($link == 'w_scari_setari') {
            $title = 'Wizard: Adauga Setari Scari';
        } else if ($link == 'w_locatari') {
            $title = 'Wizard: Locatari';
        } else if ($link == 'w_locatari_apometre') {
            $title = 'Wizard: Adauga Apometre Locatari';
        } else if ($link == 'w_scari_dat') {
            $title = 'Wizard: Datorii Scari';
        } else if ($link == 'w_locatari_dat') {
            $title = 'Wizard: Datorii Locatari';
        } else if ($link == 'w_locatari_fond') {
            $title = 'Wizard: Fonduri Locatari';

/************* ADAUGA USER ******************/
        } else if ($link == 'adauga_user') {
            $title = 'Adauga User'; 
		} else if ($link == 'schimba_parola') {
            $title = 'Schimba Parola';

/************* MENIU ******************/            
        } else if ($link == 'asociatii') {
            $title = 'Asociatii';            
        } else if ($link == 'asoc_dat') {
            $title = 'Furnizori Asociatii';            
        } else if ($link == 'asoc_fonduri') {
            $title = 'Fonduri Asociatii';            
        } else if ($link == 'asoc_setari') {
            $title = 'Setari Initiale Asociatii';            
        
        } else if ($link == 'scari') {
            $title = 'Scari';            
        } else if ($link == 'scari_dat') {
            $title = 'Datorii Scari';            
        } else if ($link == 'scari_furnizori') {
            $title = 'Furnizori Scari';            
        } else if ($link == 'scari_setari') {
            $title = 'Setari Scari';      
		} else if ($link == 'scari_pasante') {
            $title = 'Configurare Pasante Scari'; 
        
        } else if ($link == 'locatari') {
            $title = 'Locatari';            
        } else if ($link == 'locatari_ap') {
            $title = 'Apometre Initiale Locatari';            
        } else if ($link == 'locatari_apometre') {
            $title = 'Apometre Locatari';            

        } else if ($link == 'facturi') {
            $title = 'Facturi';  
		} else if ($link == 'facturiV2') {
            $title = 'Facturi V2';
		} else if ($link == 'facturiV3') {
            $title = 'Facturi V3';
		} else if ($link == 'pachete-urbica') {
            $title = 'Genereaza Factura Urbica';

        
        } else if ($link == 'rapoarte') {
            $title = 'Rapoarte';            
        
	 } else if ($link == 'fisa_pen') {
	     $title = 'Fisa Penalizari';
	 } else if ($link == 'fisa_indv') {
	     $title = 'Fisa Individuala';
	 } else if ($link == 'fisa_cons') {
	     $title = 'Fisa Consumuri';
	 } else if ($link == 'fisa_cont') {
	     $title = 'Fisa Cont';
	 } else if ($link == 'fisa_fonduri') {
	     $title = 'Fisa Fonduri';
	 } else if ($link == 'fisa_furnizori') {
	     $title = 'Fisa Furnizori';

	 } else if ($link == 'lista_plata') {
	     $title = 'Lista Plata';
		 
	 } else if ($link == 'genereazaListe') {
	     $title = 'Genereaza Liste Finale';
	 } else if ($link == 'proceseazaFacturi') {
	     $title = 'Proceseaza Facturi';

	 } else if ($link == 'XXX') {
	     $title = 'Testing Purposes';

	//pagina la care nu are acces
	} else if ($link == 'no_access') {
	     $title = 'Nu aveti acces la aceasta pagina!';
                     
        } else {            
            $title = 'Acasa';
        }
		
		//verific sa nu intre aiurea pe pagini pe care nu au acces
		$adresa = $_GET['link'];
		$u_rights = $_SESSION['uid'];
		
		if ($adresa != null){
			if (!in_array($adresa, $drepturi[$u_rights])){
				echo "<script>
						window.location = 'index.php?link=no_access'
					  </script>
				";
			}
		}

?>

<div id="header"><img style="margin-left: 10px;" src="images/sigla-urbica-72dpi.png" alt="URBICA" />

<div id="nume"><?php  echo $_SESSION['user_name']; ?></div>
<div id="info"><?php  echo $_SESSION['functie'];  ?>, <a href="index.php?link=logout">Logout</a></div>

</div>
<div id="time">
<span style="float:left; margin-left:5px; font-weight:bold;">
<?php echo $title; ?>
</span>
<?php mydate(); ?> ~&nbsp;<div id="txt"></div></div>



