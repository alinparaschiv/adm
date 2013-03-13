<?php
        $dbhost = 'localhost';
        $dbuser = 'rurb4601';
        $dbpass = 'Urbica2009';

        $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die (mysql_error());

        $dbname = 'rurb4601_urb';
        mysql_select_db($dbname);
        
        mysql_set_charset('utf8');
        
        $oraseArr = array('Iasi', 'Vaslui', 'Botosani');
        $uMasuraArr = array('apartament', 'cota indiviza', 'persoana', 'metri cubi', 'giga calorii', 'kw', 'repartitor');
        $nivelServiciiArr = array('jump0','asociatie', 'scara', 'apartament', 'locatar');
        $apaDeclaraArr = array('Numar Persoane','Cota Indiviza', 'Numar Apometre', 'Pe Apartamente', 'Proportional Consumului', 'Din Oficiu (1 persoana)', 'Pe Apartamente Locuite', 'Proportional Consumului Total (R+C)', 'Jumate proportional cons, jumate prop ap nedeclarate');
        /*$apaNuDeclaraArr = array('Pausal Amenda', 'Pausal cu modificarea indexului', 'Dif consum declarat amenda', 'Dif nr persoane prezente amenda', 'Dif nr persoane din of 1 amenda', 'Dif pe apartamente amenda', 'Dif pe apartamente locuite amenda', 'Dif ap 0 amenda', 'Dif sup amenda', 'Dif nr apometre amenda', 'Dif Nr apometre cu modificarea indexului', 'Dif nr persoane prez cu modificarea indexului',
                                 'Dif nr pers din of 1 cu modificarea indexului', 'Dif pe apartamente cu modif indexului', 'Dif pe apartamente locuite cu modificarea indexului', 'Dif pe apartamente 0 cu modificarea indexului', 'Dif suprafata cu modificarea indexului');*/
        $apaNuDeclaraArr = array('Pausal Amenda', 'Pausal cu modificarea indexului', 'Dif nr persoane prezente amenda',  'Dif pe apartamente amenda',  'Dif nr persoane prezente cu modificarea indexului', 'Dif pe apartamente cu modif indexului');
        $asocCriteriuArr = array('Creeaza Fond Apa Vital','Recalc fact imp valfact pe mc');
        $asocCriteriuGresitArr = array('Recalc val intretinere prin modif in minus','Consum 0', 'Creere apometru nou');
        $asocCriteriuImpartireArr = array('Consumul locatarilor din fiecare scara','Nr de apartamente din fiecare scara', 'Nr de persoane din fiecare scara', 'Suprafata utila din fiecare scara', 'Consumul total de apa (Rece+Calda)');

	$modImpartireFacturiArr = array('Nr. apartamente', 'Nr. persoane', 'Suprafata', 'Indecsi', 'Repartitoare', 'Valoare Apa Calda', 'Valoare Incalzire', 'Consum Apa Rece');
	
        $adresaOfficeUrbica = '                                                           
                                   <div>Internet: http://www.urbica.ro</div>
                                   <div>E-mail: contact@urbica.ro</div>
                                   <div>Telefon: (+4)0 332 411 555</div>
                                   <div>Adresă: Iași, str. Cuza Vodă nr. 13</div>
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
                                    Puteti plati la orice casierie Urbica sau prin banca in contul RO17RNCB0175033572460001 (specificati codul client).
        ';
		
	$drepturi = array (
						   //Super Administrator
						   0 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "adauga_user", "schimba_parola", "wizard", "w_asoc_dat", "w_asoc_fonduri", "w_asoc_setari", "w_locatari_fond", "w_scari", "w_scari_furnizori", "w_scari_setari", "w_scari_dat", "w_locatari", "w_locatari_apometre", "w_locatari_dat", "plata", "istoric_plati", "servicii", "strazi", "conturi", "pachete", "asociatii", "asoc_dat", "asoc_fonduri", "asoc_setari", "scari", "scari_furnizori", "scari_setari", "scari_pasante", "locatari", "locatari_apometre", "locatari_ap", "locatari_dat", "locatar", "locatari_sub", "furnizori", "furnizori-servicii", "facturi", "facturiV2", "facturiV3", "facturi_aparece", "facturi_apacalda", "facturi_incalzire", "facturi_iluminat", "facturi_gaz", "plati-furnizori", "pachete-urbica", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "fisa_furnizori", "fisa_fonduri", "lista_plata", "genereazaListe", "proceseazaFacturi", "situatieActivPasiv", 'locatar2'),
						   //Operator Sef
						   1 => array ("ok", "logout", "ByeBye", "no_access", "wizard", "asociatii", "asoc_dat", "asoc_fonduri", "asoc_setari", "scari", "scari_furnizori", "scari_setari", "scari_pasante", "locatari", "locatari_apometre", "locatari_ap", "locatari_dat", "locatar", "furnizori", "furnizori-servicii", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "fisa_furnizori", "fisa_fonduri", "lista_plata", "schimba_parola"),
						   //Operator
						   2 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "istoric_plati", "locatari", "locatar", "furnizori", "facturi", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "fisa_furnizori", "fisa_fonduri", "lista_plata", "schimba_parola"),
						   //Administrator asociatie
						   3 => array ("ok", "logout", "ByeBye", "no_access", "locatari", "locatari", "locatari_apometre", "locatari_ap", "schimba_parola"),
						   //Coordonator Economic
						   4 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "plata", "istoric_plati", "furnizori", "furnizori-servicii", "facturi", "rapoarte", "reg_jurn", "reg_inv", "rap_eco", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "fisa_furnizori", "fisa_fonduri", "lista_plata", "schimba_parola"),
						   //Casier
						   5 => array ("ok", "logout", "ByeBye", "no_access", "casierie", "plata", "istoric_plati", "schimba_parola", "fisa_pen", "fisa_indv", "fisa_cons", "fisa_cont", "fisa_fonduri", "lista_plata", "locatari", "locatari_apometre"),
						   //Relatii Furnizori
						   6 => array ("ok", "logout", "ByeBye", "no_access", "furnizori", "furnizori-servicii", "facturi", "schimba_parola", "pachete-urbica")
						   );
		
        $tableHeaderColorPdf = '#808080';
        $trColorPdf = '#C0C0C0';
        $pdfFont = 12;
        
        $valUnitaraRece = 1.33;
        $valUnitaraCalda = 1.66;

?>
