<div id="meniu"> 
        <div id="myjquerymenu" class="jquerycssmenu">
        <?php
		$uid = $_SESSION['uid'];
		switch ($uid) {
			//Super Administrato
			case 0: echo '
                    <ul>
                            <li><a href="index.php">Acasa</a>
							<li><a href="#">Casierie</a>
								<ul>
									<li><a href="index.php?link=casierie">Casierie</a></li>
									<li><a href="index.php?link=istoric_plati">Istoric Plati</a></li>
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
						<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
                    			</ul>
                            </li> 
				<li><a href="#">Useri</a>
					<ul>
						<li><a href="index.php?link=adauga_user">Adauga User</a></li>                        
						<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
					</ul>
				</li>
				<li><a href="index.php?link=version">Versiune</a></li>
                    </ul> ';
					break;
		
			//Administrator
			case 1: echo '
                    <ul>
                            <li><a href="index.php">Acasa</a>
							<li><a href="#">Casierie</a>
								<ul>
									<li><a href="index.php?link=casierie">Casierie</a></li>
									<li><a href="index.php?link=istoric_plati">Istoric Plati</a></li>
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
									<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
                    			</ul>
                            </li>  
					<li><a href="#">Useri</a>
						<ul>
							<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
						</ul>
					</li>
					<li><a href="index.php?link=version">Versiune</a></li>
                    </ul> ';
					break;
			//Operator
			case 2: echo '
                    <ul>
                            <li><a href="index.php">Acasa</a>
							<li><a href="#">Casierie</a>
								<ul>
									<li><a href="index.php?link=casierie">Casierie</a></li>
									<li><a href="index.php?link=istoric_plati">Istoric Plati</a></li>
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
                            <li><a href="index.php?link=ok" class="wizard">Wizard</a></li>                            
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
                                    <li><a href="index.php?link=facturi">Facturi</a></li>
                                </ul>
                            </li>                          
                            <li><a href="index.php?link=rapoarte">Rapoarte & Registre</a>
 								<ul>
                                	<li><a href="index.php?link=reg_jurn">Registru Jurnal</a></li>
									<li><a href="index.php?link=reg_inv">Registru Inventar</a></li>
									<li><a href="index.php?link=rap_eco">Raport Economic</a></li>
                                </ul>
                            </li>   
							<li><a href="#">Fise & Liste</a>
 								<ul>
                        			<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
 									<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
									<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
									<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
									<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
                    			</ul>
                            </li> 
				<li><a href="#">Administrare Utilizatori</a>
					<ul>
						<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
					</ul>
				</li>							
                    </ul> ';
					break;
			//Casier
			case 3: echo '
                    <ul>
                        <li><a href="index.php">Acasa</a>
						<li><a href="#">Casierie</a>
							<ul>
								<li><a href="index.php?link=casierie">Casierie</a></li>
								<li><a href="index.php?link=istoric_plati">Istoric Plati</a></li>
							</ul>
						</li> 
						
						<li><a href="#">Administrare Utilizatori</a>
							<ul>
								<li><a href="index.php?link=schimba_parola">Schimba Parola</a></li>
							</ul>
						</li>
                    </ul> ';
					break;
			//Client
			case 4: echo '
                    <ul>
						<li><a href="#">Fise & Liste</a>
							<ul>
                        		<li><a href="index.php?link=fisa_pen">Fisa Penalizari</a></li>
 								<li><a href="index.php?link=fisa_indv">Fisa Individuala</a></li>
								<li><a href="index.php?link=fisa_cons">Fisa Consumuri</a></li>
								<li><a href="index.php?link=fisa_cont">Fisa Cont</a></li>
								<li><a href="index.php?link=lista_plata">Lista De Plata</a></li>
							</ul>
                        </li>

						<li><a href="#">Administrare Utilizatori</a>
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