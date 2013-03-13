<?php
        $dbhost = 'localhost';
        $dbuser = 'rurb4601';
        $dbpass = 'appl1cat1onsaga';

        $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die (mysql_error());

        $dbname = 'rurb4601_urb';
        mysql_select_db($dbname);
        
        $oraseArr = array('Iasi', 'Vaslui', 'Botosani');
        $uMasuraArr = array('apartament', 'cota indiviza', 'persoana', 'metri cubi', 'giga calorii', 'kw');
        $nivelServiciiArr = array('jump0','asociatie', 'scara', 'apartament', 'pachete');
        $apaDeclaraArr = array('Numar Persoane','Cota Indiviza', 'Numar Apometre', 'Pe Apartamente', 'Proportional Consumului', 'Numar Persoane of 1', 'Pe Apartamente Locuite', 'Pe Apartamente Locuite Consum Dif. 0' );
        /*$apaNuDeclaraArr = array('Pausal Amenda', 'Pausal cu modificarea indexului', 'Dif consum declarat amenda', 'Dif nr persoane prezente amenda', 'Dif nr persoane din of 1 amenda', 'Dif pe apartamente amenda', 'Dif pe apartamente locuite amenda', 'Dif ap 0 amenda', 'Dif sup amenda', 'Dif nr apometre amenda', 'Dif Nr apometre cu modificarea indexului', 'Dif nr persoane prez cu modificarea indexului', 
                                 'Dif nr pers din of 1 cu modificarea indexului', 'Dif pe apartamente cu modif indexului', 'Dif pe apartamente locuite cu modificarea indexului', 'Dif pe apartamente 0 cu modificarea indexului', 'Dif suprafata cu modificarea indexului');*/
        $apaNuDeclaraArr = array('Pausal Amenda', 'Pausal cu modificarea indexului', 'Dif nr persoane prezente amenda',  'Dif pe apartamente amenda',  'Dif nr persoane prezente cu modificarea indexului', 'Dif pe apartamente cu modif indexului');
        $asocCriteriuArr = array('Creeaza Fond Apa Vital','Recalc fact imp valfact pe mc');                                 
        $asocCriteriuGresitArr = array('Recalc val intretinere prin modif in minus','Consum 0', 'Creere apo nou');                                 
        $asocCriteriuImpartireArr = array('Consumul locatarilor din fiecare scara','Nr de apartamente din fiecare scara', 'Nr de persoane din fiecare scara', 'Suprafata utila din fiecare scara');                                 
        
        $adresaOfficeUrbica = '                                                           
                                   <div>Office: Iasi, str Cuza Voda nr 13</div>
                                   <div>Tel: 0332 - 411 555</div>
                                   <div>e-mail: office@urbica.ro</div>
                                   <div>web: www.urbica.ro</div>
        ';        
        $adresaOfficeUrbica1 = '
                                Office: Iasi, str Cuza Voda nr 13
                                Tel: 0332 - 411 555
                                e-mail: office@urbica.ro
                                web: www.urbica.ro
        ';
        $responsabilOfficeUrbica = '                                                           
                                    <div>Nume responsabil administrativ:</div>
                                    <div>Dan Olaru:</div>
                                    <div>Telefon:</div>
                                    <div>0744/476651</div>
        ';
        $programOfficeUrbica = '                                                           
                                    Program casierii: Cuza Voda 9:00-17:00,<br>
                                    Puteti plati la orice casierie urbica sau prin banca in contul RO17RNCB0175033572460001 (specificati codul client).
        ';
		
		$drepturi = array (
						   //Super Administrator
						   0 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "adauga_user", "schimba_parola", "wizard", "w_asoc_dat", "w_asoc_fonduri", "w_asoc_setari", "w_scari", "w_scari_furnizori", "w_scari_setari", "w_scari_dat", "w_locatari", "w_locatari_apometre", "w_locatari_dat", "plata", "istoric_plati", "servicii", "strazi", "conturi", "pachete", "asociatii", "asoc_dat", "asoc_fonduri", "asoc_setari", "scari", "scari_furnizori", "scari_setari", "locatari", "locatari_apometre", "locatari_ap", "locatari_dat", "locatar", "furnizori", "furnizori-servicii", "facturi", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "lista_plata", "version"),
						   //Administrator
						   1 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "wizard", "schimba_parola", "w_asoc_dat", "w_asoc_fonduri", "w_asoc_setari", "w_scari", "w_scari_furnizori", "w_scari_setari", "w_scari_dat", "w_locatari", "w_locatari_apometre", "w_locatari_dat", "plata", "istoric_plati", "servicii", "strazi", "conturi", "pachete", "asociatii", "asoc_dat", "asoc_fonduri", "asoc_setari", "scari", "scari_furnizori", "scari_setari", "locatari", "locatari_apometre", "locatari_ap", "locatari_dat", "locatar", "furnizori", "furnizori-servicii", "facturi", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "lista_plata", "version"),
						   //Operator
						   2 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "wizard", "schimba_parola", "w_asoc_dat", "w_asoc_fonduri", "w_asoc_setari", "w_scari", "w_scari_furnizori", "w_scari_setari", "w_scari_dat", "w_locatari", "w_locatari_apometre", "w_locatari_dat", "plata", "istoric_plati", "servicii", "strazi", "conturi", "pachete", "asociatii", "asoc_dat", "asoc_fonduri", "asoc_setari", "scari", "scari_furnizori", "scari_setari", "locatari", "locatari_apometre", "locatari_ap", "locatari_dat", "locatar", "furnizori", "furnizori-servicii", "facturi", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "lista_plata"),
						   //Casier
						   3 => array ("ok", "logout", "ByeBye", "no_access", "schimba_parola", "casierie", "plata", "istoric_plati"),
						   //Client
						   4 => array ("ok", "logout", "ByeBye", "no_access", "schimba_parola", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "lista_plata")
						   );
		
        $tableHeaderColorPdf = '#808080';
        $trColorPdf = '#C0C0C0';
        $pdfFont = 12;
        
        $valUnitaraRece = 1.33;
        $valUnitaraCalda = 1.66;

?>
