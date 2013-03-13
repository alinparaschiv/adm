<?php

if ($_POST['deProcesat'] == "ok"){
	$procesare = "SELECT * FROM facturi WHERE procesata=0 AND fact_id=".$_POST['factura'];
	$procesare = mysql_query($procesare) or die("Nu pot selecta facturile neprocesate<br />".mysql_error());
	
	if (mysql_num_rows($procesare) == 0){
		echo "Nu exista facturi neprocesate!";
	} else {
		while ($proc = mysql_fetch_array($procesare)){
		// Informatii Generale
			$factId = $proc['fact_id'];
			$asocId = $proc['asoc_id'];
			$scaraId = $proc['scara_id'];
			$tipFactura = $proc['tipFactura'];
			$tipServiciu = $proc['tipServiciu'];
		
		//  Informatii Factura
			$numarFactura = $proc['numarFactura'];
			$serieFactura = $proc['serieFactura'];
			$dataEmitere = $proc['dataEmitere'];
			$dataScadenta = $proc['dataScadenta'];
			$observatii = $proc['observatii'];
			
		//  Informatii Asociatie
			$debite = $proc['$debite'];
			$penalizari = $proc['penalizari'];
			$nrRate = $proc['nrRate'];
			$luna = $proc['luna'];
		
		//  Facturi cu Indecsi (sau NU)
			$verif = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
			$verif = mysql_query($verif) or die ("Nu pot selecta serviciul ales<br />".mysql_error());
			
			if (mysql_result($verif, 0, 'cu_indecsi') == 'da'){
				$indexNou = $proc['indexNou'];
				$indexVechi = $proc['indexVechi'];
				$cantitate = $proc['indexNou'] - $proc['indexVechi'];
				$cost = $proc['cost'];
				$ppu = $proc['ppu'];		
			} else {
				$cantitate = $proc['cantitate']; 		
				$cost = $proc['cost'];
				$ppu = $proc['ppu']; 			//apare in servicii
			}	
			
		//  Facturi cu Pasant
			$alegServiciu = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
			$alegServiciu = mysql_query($alegServiciu) or die ("Nu pot accesa serviciile<br />".mysql_error());
				
			if (mysql_result($alegServiciu, 0, 'serviciu') == 'apa rece' || mysql_result($alegServiciu, 0, 'serviciu') == 'apa calda'){
				$arePasant = "SELECT * FROM scari_setari WHERE scara_id=".$scaraId;
				$arePasant = mysql_query($arePasant) or die ("Nu pot accesa setarile scarii<br />".mysql_error());
						
				if (mysql_result($arePasant, 0, 'pasant') == "da"){
					$tipApa = mysql_result($alegServiciu, 0, 'serviciu');
					$tipApa = explode(' ', $tipApa);
					
					if ($tipApa[1] == "rece"){
						$pasant_rece = $proc["pasant_rece"];
						$pasant_calda = -1;
					} else
					if ($tipApa[1] == "calda"){
						$pasant_calda = $proc["pasant_calda"];
						$pasant_rece = -1;
					}
				} else {
					$pasant_rece = -1;
					$pasant_calda = -1;
				}
			}
		
		//  Facturi pe Apartament
			if ($tipFactura == 3){
				$locatari = $proc['locatari'];
				$cost = $proc['cost'];
			}
		
		//  Facturi pe Locatari
			if ($tipFactura == 4){
				$locatari = $proc['locatari'];
			}
			
			//verific pentru fiecare factura, de ce tip e
			$sql = "SELECT * FROM servicii WHERE serv_id=".$tipServiciu;
			$sql = mysql_query($sql) or die("Nu pot afla tipul serviciului<br />".mysql_error());
			
			//daca este fond		--			Trebuie sa le mai inserez in fisa individuala
			if (mysql_result($sql, 0, 'fonduri') == "da"){
				$sql1 = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND data='".$luna."' ORDER BY loc_id ASC";
				$sql1 = mysql_query($sql1) or die ("Nu ma pot conecta la fisa_fonduri<br />".mysql_error()); 
				
				//fond rulment
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "fond rulment"){
					if (mysql_num_rows($sql1) != 0){
						while ($row = mysql_fetch_array($sql1)){
							$aveaDePlata = $row['fond_rul_rest'];
							$aveaDePlata = $aveaDePlata + $ppu;
							
							$fondRulment = "UPDATE fisa_fonduri SET fond_rul_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$luna."'";
							$fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de rulment<br />".mysql_error());
						}
					} else {
						$iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
						$iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());
						
						//calculez luna anterioara
						$luna1 = explode("-",$luna);
						$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
						$lunaAnt = date('m-Y',$lunaAnt);
						
						while ($amLocatar = mysql_fetch_array($iiDamDePlata)){
							//restul de plata de luna trecuta
							$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
							$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
							
							if (mysql_num_rows($aveaDePlata) != 0){
								$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
								$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
								$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
								$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
								$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
							} else {
								$fondRulRest = 0;
								$fondRepRest = 0;
								$fondSpecRest = 0;
								$fondPenConst = 0;
								$luna_trecuta = 0;
							}
							
							//insertul pt fiecare locatar
							$fondRulRest = $ppu + $fondRulRest;
							
							$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
							$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
						}
					}
				}
				
				//fond reparatii
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "fonduri reparatii"){
					if (mysql_num_rows($sql1) != 0){
						while ($row = mysql_fetch_array($sql1)){
							$aveaDePlata = $row['fond_rep_rest'];
							$aveaDePlata = $aveaDePlata + $ppu;
							
							$fondRulment = "UPDATE fisa_fonduri SET fond_rep_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$luna."'";
							$fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de reparatii<br />".mysql_error());
						}
					} else {
						$iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
						$iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());
						
						//calculez luna anterioara
						$luna1 = explode("-",$luna);
						$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
						$lunaAnt = date('m-Y',$lunaAnt);
						
						while ($amLocatar = mysql_fetch_array($iiDamDePlata)){
							//restul de plata de luna trecuta
							$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
							$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
							
							if (mysql_num_rows($aveaDePlata) != 0){
								$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
								$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
								$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
								$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
								$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
							} else {
								$fondRulRest = 0;
								$fondRepRest = 0;
								$fondSpecRest = 0;
								$fondPenConst = 0;
								$luna_trecuta = 0;
							}
							
							//insertul pt fiecare locatar
							$fondRepRest = $ppu + $fondRepRest;
							
							$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
							$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
						}
					}
				}
				
				//fonduri speciale
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "fonduri speciale"){
					$locatari = explode(',', $locatari);
					
					if (mysql_num_rows($sql1) != 0){
						while ($row = mysql_fetch_array($sql1)){
							if (in_array($row['loc_id'], $locatari)){
								$aveaDePlata = $row['fond_spec_rest'];
								$aveaDePlata = $aveaDePlata + $ppu;
							
								$fondRulment = "UPDATE fisa_fonduri SET fond_spec_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$luna."'";
								$fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de reparatii<br />".mysql_error());
							}
						}
					} else {
						$iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId;
						$iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());
						
						//calculez luna anterioara
						$luna1 = explode("-",$luna);
						$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
						$lunaAnt = date('m-Y',$lunaAnt);
						
						while ($amLocatar = mysql_fetch_array($iiDamDePlata)){
							if (in_array($amLocatar['loc_id'], $locatari)){
							//restul de plata de luna trecuta
								$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
								$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
								
								if (mysql_num_rows($aveaDePlata) != 0){
									$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
									$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
									$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
									$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
									$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
								} else {
									$fondRulRest = 0;
									$fondRepRest = 0;
									$fondSpecRest = 0;
									$fondPenConst = 0;
									$luna_trecuta = 0;
								}

								//insertul pt fiecare locatar
								$fondSpecRest = $ppu + $fondSpecRest;
								
								$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
								$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
							} else {
								$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
								$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
								
								if (mysql_num_rows($aveaDePlata) != 0){
									$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
									$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
									$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
									$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
									$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
								} else {
									$fondRulRest = 0;
									$fondRepRest = 0;
									$fondSpecRest = 0;
									$fondPenConst = 0;
									$luna_trecuta = 0;
								}
								
								$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
								$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul special<br />".mysql_error());
							}
						}
					}				
				}
				
				//reparatii
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "reparatii"){
					$locatari = explode(',', $locatari); 
					$cost = explode(',', $cost); 
					$eLocatar = 0; //folosesc varabila asta ca sa fac corespondenta intre locatari si plati
					
					if (mysql_num_rows($sql1) != 0){					//daca avem deja fonduri introduse pentru luna dorita
						while ($row = mysql_fetch_array($sql1)){
							if (in_array($row['loc_id'], $locatari)){
								$aveaDePlata = $row['fond_rep_rest'];
								$aveaDePlata = $aveaDePlata + $cost[$eLocatar];
							
								$fondRulment = "UPDATE fisa_fonduri SET fond_rep_rest=".$aveaDePlata." WHERE asoc_id=".$asocId." AND loc_id=".$row['loc_id']." AND scara_id=".$scaraId." AND data='".$luna."'";
								$fondRulment = mysql_query($fondRulment) or die ("Nu pot updata fondul de reparatii<br />".mysql_error());
								$eLocatar ++;
							}
						}
					} else { 											//daca nu avem fonduri introduse pentru luna dorita
						$iiDamDePlata = "SELECT * FROM locatari WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." ORDER BY loc_id ASC";		// selectam locatarii de pe scara pe care dorim sa introducem fondurile
						$iiDamDePlata = mysql_query($iiDamDePlata) or die("Nu pot selecta locatarii<br />".mysql_error());
						
						//calculez luna anterioara
						$luna1 = explode("-",$luna);
						$lunaAnt = mktime(0, 0, 0, $luna1[0]-1, 1, $luna1[1]);
						$lunaAnt = date('m-Y',$lunaAnt);
						
						$eLocatar = 0;									// cu variabila asta verific daca locatarul chiar are ceva de plata pe luna dorita
						while ($amLocatar = mysql_fetch_array($iiDamDePlata)){
							if (in_array($amLocatar['loc_id'], $locatari)){		//daca are ceva de plata luna asta

								$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
								$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
								
								// aflu restul de plata de luna trecuta la fondul de reparatii
								if (mysql_num_rows($aveaDePlata) != 0){
									$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
									$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
									$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
									$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
									$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
								} else {
									$fondRulRest = 0;
									$fondRepRest = 0;
									$fondSpecRest = 0;
									$fondPenConst = 0;
									$luna_trecuta = 0;
								}
								
								//insertul pt fiecare locatar
								$fondRepRest = $cost[$eLocatar] + $fondRepRest;
								
								$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
								$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera fondul de rulment<br />".mysql_error());
								$eLocatar ++;
							} else {		// daca nu are de plata pe luna asta, ii punem doar luna trecuta
								$aveaDePlata = "SELECT * FROM fisa_fonduri WHERE asoc_id=".$asocId." AND scara_id=".$scaraId." AND loc_id=".$amLocatar['loc_id']." AND data='".$lunaAnt."'";
								$aveaDePlata = mysql_query($aveaDePlata) or die ("Nu pot afla informatii despre luna anterioara<br />".mysql_error());
								
								$luna_trecuta = 0;
								if (mysql_num_rows($aveaDePlata) != 0){
									$fondRulRest = mysql_result($aveaDePlata, 0, 'fond_rul_rest');
									$fondRepRest = mysql_result($aveaDePlata, 0, 'fond_rep_rest');
									$fondSpecRest = mysql_result($aveaDePlata, 0, 'fond_spec_rest');
									$fondPenConst = mysql_result($aveaDePlata, 0, 'fond_pen_constituit');
									$luna_trecuta = $fondRulRest + $fondRepRest + $fondSpecRest + $fondPenConst;
								} else {
									$fondRulRest = 0;
									$fondRepRest = 0;
									$fondSpecRest = 0;
									$fondPenConst = 0;
									$luna_trecuta = 0;
								}
								
								$iiDamSaDuca = "INSERT INTO fisa_fonduri VALUES (null, '$luna', '$asocId', '$scaraId', ".$amLocatar['loc_id'].", 0, '$fondRulRest', 0, '$fondRepRest', 0, '$fondSpecRest', 0, 0, 0, '$luna_trecuta')"; 
								$iiDamSaDuca = mysql_query($iiDamSaDuca) or die ("Nu pot insera reparatiile<br />".mysql_error());
							}
						}
					}				
				}
			}
			
			//daca este serviciu 
			/**
				le adaug in fisa individuala si in lista de plata
				daca nu exista inregistrari pe luna curenta trebuie sa le creez
				altfel doar adaug
			*/
			if (mysql_result($sql, 0, 'servicii') == "da"){
				//insert in fisa individuala
				$alegLocatarii = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
				$alegLocatarii = mysql_query($alegLocatarii) or die ("Nu pot afla locatarii<br />".mysql_error());
				
				while ($parcurgere = mysql_fetch_array($alegLocatarii)){
					$areDePlata = $ppu * $parcurgere['cota'];
					$um = mysql_result($sql, 0, 'unitate');
					$facturaCurr = $serieFactura.'/'.$numarFactura;
					
					$ok = "INSERT INTO fisa_indiv VALUES(null, '$asocId', '$scaraId', ".$parcurgere['loc_id'].", '$luna', '$tipServiciu', ".$parcurgere['cota'].", '$ppu', '$facturaCurr', '$cantitate')";
					$ok = mysql_query($ok) or die ("Nu pot insera factura pentru servicii<br />".mysql_error());
				}
			}
				
			if (mysql_result($sql, 0, 'cu_indecsi') == "da"){
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "gaz"){
					$auGaz = "SELECT SUM(nr_pers) FROM locatari WHERE gaz='da' AND scara_id=".$scaraId;
					$auGaz = mysql_query($auGaz) or die ("Nu pot afla cate persoane au gaz<br />".mysql_error());
					$auGaz = mysql_result($auGaz, 0, 'SUM(nr_pers)'); 
					
					if ($auGaz == 0){
						echo 'Nu sunt persoane pe aceasta scara care sa beneficieze acest serviciu. Va rugam sa verificati factura. <br />Mergi la pagina <a href="http://urbica.ro/app/index.php?link=proceseazaFacturi">anterioara</a>.';
						exit(0);
					} else {
						$plataGaz = "SELECT * FROM locatari WHERE gaz='da' AND scara_id=".$scaraId;
						$plataGaz = mysql_query($plataGaz) or die ("Nu pot selecta locatarii care folosesc gaz<br />".mysql_error());
						
						while ($platesteGaz = mysql_fetch_array($plataGaz)){
							//inserez in fisa idividuala
							$pppers = $cost / $auGaz;
							
							$gaz = $platesteGaz['nr_pers'];
							$facturaCurr = $serieFactura.'/'.$numarFactura;
							$pret_unitar2 = $cost/$cantitate;
							$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$platesteGaz['loc_id']."', '$luna', '$tipServiciu', '$gaz', '$pppers', 'persoana','$facturaCurr','$pret_unitar2', '$cantitate')";
							$plateste = mysql_query($plateste) or die ("Nu pot insera factura de gaz<br />".mysql_error());
						}
					}
				}
				
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "iluminat"){
					$plataCurent = "SELECT * FROM locatari WHERE scara_id=".$scaraId;
					$plataCurent = mysql_query($plataCurent) or die ("Nu pot selecta locatarii<br />".mysql_error());
					
					$totLoc = "SELECT SUM(nr_pers) FROM locatari WHERE scara_id=".$scaraId;
					$totLoc = mysql_query($totLoc) or die ("Nu pot afla numarul total de locuitori<br />".mysql_error());
					
					$totLoc = mysql_result($totLoc, 0, 'SUM(nr_pers)');
					
					while ($platesteCurent = mysql_fetch_array($plataCurent)){
						//inserez in fisa individuala
						$pppers = $cost / $totLoc;
						$ppu = $cantitate / $totLoc;
						$curent = $ppu * $platesteCurent['nr_pers'];
						$facturaCurr = $serieFactura.'/'.$numarFactura;							
						$pret_unitar2 = $cost/$cantitate;
						
						$plateste = "INSERT INTO fisa_indiv VALUES (null, '$asocId', '$scaraId', '".$platesteCurent['loc_id']."', '$luna', '$tipServiciu', '".$platesteCurent['nr_pers']."', '$pppers','persoana', '$facturaCurr','$pret_unitar2', '$cantitate')";
						$plateste = mysql_query($plateste) or die ("Nu pot insera factura de curent<br />".mysql_error());
					}
				}
				
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "apa rece"){
					echo "Procesez factura de apa rece";
				}

				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "apa calda"){
					echo "Procesez factura de apa calda";
				}
				
				if (strtolower(mysql_result($sql, 0, 'serviciu')) == "incalzire"){
					echo "Procesez factura de incalzire";
				}
			}
			
			if ($nrRate>1){
				$nrRate --;
				$proprietariApFS = $proc['locatari'];	
				
				$lunaUrm = mktime(0, 0, 0, date('m')+1, 1, date('Y'));
				$lunaUrm = date('m-Y',$lunaUrm); 
				
				$azi = date('d-m-Y');
	
				$scadNrRate = "INSERT INTO facturi VALUES (null, '$azi', '$asocId', '$scaraId', '$tipFactura', '$tipServiciu', '$numarFactura', '$serieFactura', '$dataEmitere', '$dataScadenta', 0, 0, '$nrRate', '$lunaUrm', 0, 0, '$cantitate', '$cost', '$ppu', 0, 0, '$proprietariApFS', '$observatii', 0)";
				$scadNrRate = mysql_query($scadNrRate) or die ("Nu pot introduce factura pentru luna urmatoare<br />".mysql_error());
			}
		
			$update = "UPDATE facturi SET procesata=1 WHERE fact_id=".$factId;
			$update = mysql_query($update) or die ("Nu pot updata factura<br />".mysql_error());
		}
	}
	unset($_POST);
}
///////////////////////////////////////////////////////////////

function afiseazaFacturi(){
	$lunaAsta = date('m-Y');
	$sql = "SELECT * FROM facturi WHERE luna<='".$lunaAsta."' ORDER BY fact_id DESC";
	$sql = mysql_query($sql) or die ("Nu ma pot conecta la facturi<br />".mysql_error());
	
	$i = 1;
	if (mysql_num_rows($sql)>0){
		while ($row = mysql_fetch_array($sql)){
			if ($i%2 == 0) {  $color = "#EEEEEE"; } else { $color="#CCCCCC";  }
			echo '<form action="" method="post">';
			echo '<tr bgcolor="'.$color.'">';
				echo '<input type="hidden" name="deProcesat" value="ok">';
				echo '<input type="hidden" name="factura" value="'.$row['fact_id'].'">';
				echo '<td>'.$i.'</td>';
					
					$asociatie = "SELECT * FROM asociatii WHERE asoc_id=".$row['asoc_id'];
					$asociatie = mysql_query($asociatie) or die("Nu pot afisa asociatia<br />".mysql_error());
					
					if ($row['scara_id'] != null){
					$scara = "SELECT * FROM scari WHERE scara_id=".$row['scara_id'];
					$scara = mysql_query($scara) or die("Nu pot afisa scara<br />".mysql_error());}
					
				if (mysql_num_rows($asociatie) > 0 && mysql_num_rows($scara) > 0){
					echo '<td>'.mysql_result($asociatie, 0, 'asociatie').' / '.mysql_result($scara, 0, 'scara').'</td>';
				} else 
				if (mysql_num_rows($asociatie) > 0 && mysql_num_rows($scara) == 0){
					echo '<td>'.mysql_result($asociatie, 0, 'asociatie').' / - </td>';
				} else
				if (mysql_num_rows($asociatie) == 0 && mysql_num_rows($scara) > 0){
					echo '<td> - / '.mysql_result($scara, 0, 'scara').'</td>';
				} else {
					echo '<td> - / - </td>';
				}
				
				echo '<td>'.$row['serieFactura'].' / '.$row['numarFactura'].'</td>';
				
					$serviciu = "SELECT * FROM servicii WHERE serv_id=".$row['tipServiciu'];
					$serviciu = mysql_query($serviciu) or die("Nu pot afisa serviciile<br />".mysql_error());
					
				echo '<td>'.mysql_result($serviciu, 0, 'serviciu').'</td>';
				echo '<td>'.$row['luna'].'</td>';
				echo '<td>'.$row['dataEmitere'].' / '.$row['dataScadenta'].'</td>';
				echo '<td>'.$row['cantitate'].'</td>';
				echo '<td>'.$row['cost'].'</td>';
				echo '<td>'.$row['nrRate'].'</td>';
					if ($row['procesata'] == 1){
						echo '<td> <img src="images/ok.png" width="20px" height="20px" border="0px" /> </td>';
					} else {
						echo '<td> <input type="submit" value="Proceseaza" /> </td>';
					}
			echo '</tr>';
			echo '</form>';
			$i++;
		}
	} else {
	echo "Nu sunt inregistrate facturi";
	}
}
?>


<style type="text/css">
thead tr td { border:solid 1px #000; color:#FFF; }
tbody { border:solid 1px #000; }
tbody tr td input { width:100%; border:none; height:100%; }
tbody tr.newline td { border:solid 1px #0CC;   }
tfoot { color:#FFF; }
.addnew {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:5px;  }
.addnew2 {position:absolute; width:120px; background-color:none; background-image:url(images/adauga.jpg); width:19px; height:20px; border:none; background-color:none; cursor:pointer; margin-left:95px; margin-top:-9px;  }
tr.newline input { text-align:center; }
.pdf1 { clear:both; width:51px; height:51px; float:left; background-image:url(images/pdf_down.jpg); margin-left:900px; margin-top:-20px; text-decoration:none; border-bottom:0px solid white; }
a.pdf1:hover { background-image:url(images/pdf_up.jpg);  }
#print {float:left; margin-left:900px; margin-top:15px;}
</style>
<table width="950" style="top:250px; background-color:white;">
<thead>
<tr>
  <td bgcolor="#666666">Nr. Crt.</td>
  <td bgcolor="#666666">Asocitatie / Scara</td>
  <td bgcolor="#666666">Serie/Numar</td>
  <td bgcolor="#666666">Serviciu</td>
  <td bgcolor="#666666">Luna</td>
  <td bgcolor="#666666">Data Emitere/Data Scadenta</td>
  <td bgcolor="#666666">Cantitate</td>
  <td bgcolor="#666666">Cost</td>
  <td bgcolor="#666666">Rate</td>
  <td bgcolor="#666666">Stare</td>
  </tr>
</thead>
<tbody>
<?php  afiseazaFacturi();  ?>

</tbody>
</table>
<!-- <a href="#">print</a> -->
</form>
