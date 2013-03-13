<div id="meniu">
        <div id="myjquerymenu" class="jquerycssmenu">
        <?php
		$uid = $_SESSION['uid'];
		switch ($uid) {
			//Super Administrator
			case 0: echo '
          <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="#">Casierie</a>
							<ul>
								<li><a href="index.php?link=casierie">Casierie</a></li>
								<li><a href="index.php?link=istoric_plati&deLa='.date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))).'&panaLa='.date('Y-m-d').'">Istoric Plati</a></li>
              </ul>
						</li>
						<li><a href="#">Informatii</a>
							<ul>
								<li><a href="index.php?link=servicii">Servicii</a></li>
								<li><a href="index.php?link=strazi">Strazi</a></li>
								<li><a href="index.php?link=conturi">Conturi Bancare</a></li>
								<li><a href="index.php?link=pachete">Pachete Servicii</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=wizard" class="wizard">Wizard</a></li>
						<li><a href="index.php?link=asociatii">Asociatii</a>
							<ul>
								<li><a href="index.php?link=asoc_dat">Furnizori</a></li>
								<li><a href="index.php?link=asoc_fonduri">Fonduri</a></li>
								<li><a href="index.php?link=asoc_setari">Setari Initiale</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=scari">Scari</a>
							<ul>
								<li><a href="index.php?link=scari_furnizori">Furnizori</a></li>
								<li><a href="index.php?link=scari_setari">Setari Initiale</a></li>
								<li><a href="index.php?link=scari_pasante">Setare Pasante</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=locatari">Locatari</a>
							<ul>
								<li><a href="index.php?link=locatari">Afiseaza Locatari</a></li>
								<li><a href="index.php?link=locatari_apometre">Apometre Locatari</a></li>
								<li><a href="index.php?link=locatari_ap">Apometre Initiale</a></li>
								<li><a href="index.php?link=locatari_dat">Datorii Locatari</a></li>
								<li><a href="index.php?link=locatari_sub">Subventii Locatari</a></li>
								<li><a href="index.php?link=locatar">Locatar</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=furnizori">Furnizori</a>
							<ul>
                <li><a href="index.php?link=facturi_aparece">Factura Apa rece</a></li>
                <li><a href="index.php?link=facturi_apacalda">Factura Apa calda</a></li>
                <li><a href="index.php?link=facturi_incalzire">Factura Incalzire</a></li>
                <li><a href="index.php?link=facturi_iluminat">Factura Iluminat</a></li>
                <li><a href="index.php?link=facturi_gaz">Factura Gaz</a></li>
                <li><a href="index.php?link=facturi">Facturi Speciale</a></li>
                <li><a href="index.php?link=furnizori">Furnizori</a></li>
                <li><a href="index.php?link=furnizori-servicii">Furnizori-Servicii</a></li>
                <li><a href="index.php?link=plati-furnizori">Plati Furnizori</a></li>
                <li><a href="index.php?link=pachete-urbica">Pachete Urbica</a></li>
                <li><a href="index.php?link=facturiV2">Facturi Generale</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=rapoarte">Rapoarte</a>
							<ul>
								<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
								<li><a href="index.php?link=reg_inv">Registru Inventar</a></li>
								<li><a href="index.php?link=situatieActivPasiv">Situate Activ/Pasiv</a></li>
							</ul>
						</li>
						<li><a href="#">Liste</a>
							<ul>
								<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=fisa_fonduri">Fisa Fonduri</a></li>
								<li><a href="index.php?link=fisa_furnizori">Fisa Furnizori</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
						</li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=adauga_user">Adauga User</a></li>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
						<li><a href="#">Liste Finale</a>
							<ul>
								<li><a href="index.php?link=genereazaListe">Genereaza Liste</a></li>
								<li><a href="index.php?link=proceseazaFacturi">Proceseaza Facturi</a></li>
							</ul>
						</li>
          </ul> ';
					break;

			//Operator Sef
			case 1: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="index.php?link=wizard" class="wizard">Wizard</a></li>
						<li><a href="index.php?link=asociatii">Asociatii</a>
							<ul>
								<li><a href="index.php?link=asoc_dat">Furnizori</a></li>
								<li><a href="index.php?link=asoc_fonduri">Fonduri</a></li>
								<li><a href="index.php?link=asoc_setari">Setari Initiale</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=scari">Scari</a>
							<ul>
								<li><a href="index.php?link=scari_furnizori">Furnizori</a></li>
								<li><a href="index.php?link=scari_setari">Setari Initiale</a></li>
								<li><a href="index.php?link=scari_pasante">Setare Pasante</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=locatari">Locatari</a>
							<ul>
								<li><a href="index.php?link=locatari">Afiseaza Locatari</a></li>
								<li><a href="index.php?link=locatari_apometre">Apometre Locatari</a></li>
								<li><a href="index.php?link=locatari_ap">Apometre Initiale</a></li>
								<li><a href="index.php?link=locatari_dat">Datorii Locatari</a></li>
								<li><a href="index.php?link=locatar">Locatar</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=furnizori">Furnizori</a>
							<ul>
								<li><a href="index.php?link=furnizori">Furnizori</a></li>
								<li><a href="index.php?link=furnizori-servicii">Furnizori-Servicii</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=rapoarte">Rapoarte</a>
							<ul>
								<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
								<li><a href="index.php?link=reg_inv">Registru Inventar</a></li>
								<li><a href="index.php?link=rap_eco">Raport Economic</a></li>
							</ul>
						</li>
						<li><a href="#">Liste</a>
							<ul>
								<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=fisa_furnizori">Fisa Furnizori</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
						</li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
					</ul> ';
					break;

			//Operator
			case 2: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="#">Casierie</a>
							<ul>
								<li><a href="index.php?link=casierie">Casierie</a></li>
								<li><a href="index.php?link=istoric_plati&deLa='.date('Y-m-d', strtotime('-10 days', strtotime(date('Y-m-d')))).'">Istoric Plati</a></li>
                            </ul>
						</li>
						<li><a href="#">Fise Locatari</a>
							<ul>
								<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=fisa_fonduri">Fisa Fonduri</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=locatari_apometre">Apometre</a></li>
						<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
					</ul> ';
					break;

			//Administrator asociatie
			case 3: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="index.php?link=locatari">Locatari</a>
							<ul>
								<li><a href="index.php?link=locatari">Afiseaza Locatari</a></li>
								<li><a href="index.php?link=locatari_apometre">Apometre Locatari</a></li>
								<li><a href="index.php?link=locatari_ap">Apometre Initiale</a></li>
							</ul>
						</li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
                    </ul> ';
					break;

			//Coordonator Economic
			case 4: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="#">Casierie</a>
							<ul>
								<li><a href="index.php?link=casierie">Casierie</a></li>
								<li><a href="index.php?link=istoric_plati">Istoric Plati</a></li>
                            </ul>
						</li>
						<li><a href="index.php?link=furnizori">Furnizori</a>
							<ul>
								<li><a href="index.php?link=furnizori">Furnizori</a></li>
								<li><a href="index.php?link=furnizori-servicii">Furnizori-Servicii</a></li>
								<li><a href="index.php?link=facturi">Facturi</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=rapoarte">Rapoarte</a>
							<ul>
								<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
								<li><a href="index.php?link=reg_inv">Registru Inventar</a></li>
								<li><a href="index.php?link=rap_eco">Raport Economic</a></li>
							</ul>
						</li>
						<li><a href="#">Liste</a>
							<ul>
								<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=fisa_furnizori">Fisa Furnizori</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
						</li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
					</ul> ';
					break;

			//Casier
			case 5: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="#">Casierie</a>
							<ul>
								<li><a href="index.php?link=casierie">Casierie</a></li>
								<li><a href="index.php?link=istoric_plati&deLa='.date('Y-m-d').'&panaLa='.date('Y-m-d').'">Istoric Plati</a></li>
								<li><a href="/app/modules/casierie/chitanta.php">Ultima Chitanta</a></li>
              				</ul>
						</li>
						<li><a href="#">Fise Locatari</a>
							<ul>
								<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=fisa_fonduri">Fisa Fonduri</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
						</li>
						<li><a href="index.php?link=locatari_apometre">Apometre</a></li>
						<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
					</ul> ';
					break;

			//Relatii Furnizori
			case 6: echo '
                    <ul>
						<li><a href="index.php">Acasa</a>
						<li><a href="index.php?link=furnizori">Furnizori</a>
							<ul>
								<li><a href="index.php?link=furnizori">Furnizori</a></li>
								<li><a href="index.php?link=furnizori-servicii">Furnizori-Servicii</a></li>
								<li><a href="index.php?link=facturi">Facturi</a></li>
								<li><a href="index.php?link=pachete-urbica">Pachete Urbica</a></li>
							</ul>
						</li>
						<li><a href="#">Useri</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
					</ul> ';
					break;

			}
		?>
                    <br style="clear: left" />
        </div>
</div>