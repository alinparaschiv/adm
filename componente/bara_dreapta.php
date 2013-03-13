<div id="secCol">
        <h3 id="news">Link-uri utile</h3>        
        <ul><li class="clearfix">        
                <?php                        
                    if ($link == 'asociatii') {
                        if ($asocId == '') {
                                echo '<a href="index.php?link=wizard" class="more">Creeaza Asociatie noua</a><br />';
                                echo '<a href="index.php?link=scari" class="more">Scari</a><br />';
                                echo '<a href="index.php?link=locatari" class="more">Locatari</a> <br />';
                        } else {
                                echo '<a href="index.php?link=wizard" class="more">Creeaza Asociatie noua</a> <br />';
                                echo '<a href="index.php?link=asoc_dat&asoc_id='.$asocId.'" class="more">Furnizori asociatie</a> <br />';                        
                                echo '<a href="index.php?link=asoc_setari&asoc_id='.$asocId.'" class="more">Setari asociatie</a> <br />';
                                                                
                                echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                                echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';                                                        
                        }
                    }        
                    if ($link == 'asoc_dat') {                        
                                echo '<a href="index.php?link=wizard" class="more">Creeaza Asociatie noua</a> <br />';                                
                                echo '<a href="index.php?link=asoc_fonduri&asoc_id='.$asocId.'" class="more">Fonduri asociatie</a> <br />';                        
                                echo '<a href="index.php?link=asoc_setari&asoc_id='.$asocId.'" class="more">Setari asociatie</a> <br />';

                                echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                                echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';                                                                                
                    }        
                    if ($link == 'asoc_fonduri') {                        
                                echo '<a href="index.php?link=wizard" class="more">Creeaza Asociatie noua</a> <br />';
                                echo '<a href="index.php?link=asoc_dat&asoc_id='.$asocId.'" class="more">Furnizori asociatie</a> <br />';
                                echo '<a href="index.php?link=asoc_setari&asoc_id='.$asocId.'" class="more">Setari asociatie</a> <br />';

                                echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                                echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }
                    if ($link == 'asoc_setari') {                        
                                echo '<a href="index.php?link=wizard" class="more">Creeaza Asociatie noua</a> <br />';
                                echo '<a href="index.php?link=asoc_fonduri&asoc_id='.$asocId.'" class="more">Fonduri asociatie</a> <br />';
                                echo '<a href="index.php?link=asoc_dat&asoc_id='.$asocId.'" class="more">Furnizori asociatie</a> <br />';

                                echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                                echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }                                                                   
                    if ($link == 'scari') {                       
                                echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                                echo '<a href="index.php?link=scari_dat&asoc_id='.$asocId.'" class="more">Datorii Scari</a> <br />';
                                echo '<a href="index.php?link=scari_furnizori&asoc_id='.$asocId.'" class="more">Furnizori Scari</a> <br />';
                                echo '<a href="index.php?link=scari_setari&asoc_id='.$asocId.'" class="more">Setari Scari</a> <br />';
                                echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }                                        
                    if ($link == 'scari_dat') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=scari_furnizori&asoc_id='.$asocId.'" class="more">Furnizori Scari</a> <br />';
                            echo '<a href="index.php?link=scari_setari&asoc_id='.$asocId.'" class="more">Setari Scari</a> <br />';
                            echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }
                    if ($link == 'scari_furnizori') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=scari_dat&asoc_id='.$asocId.'" class="more">Datorii Scari</a> <br />';
                            echo '<a href="index.php?link=scari_setari&asoc_id='.$asocId.'" class="more">Setari Scari</a> <br />';
                            echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }
                    if ($link == 'scari_setari') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=scari_furnizori&asoc_id='.$asocId.'" class="more">Furnizori Scari</a> <br />';
                            echo '<a href="index.php?link=scari_dat&asoc_id='.$asocId.'" class="more">Datorii Scari</a> <br />'; 
                            echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                    }
                    if ($link == 'locatari') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=locatari_ap&asoc_id='.$asocId.'" class="more">Apometre Locatari</a> <br />';                           
                    }
                    if ($link == 'locatari_ap') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                            echo '<a href="index.php?link=locatari_ap&asoc_id='.$asocId.'" class="more">Apometre Locatari</a> <br />';                           
                            echo '<a href="index.php?link=locatari_dat&asoc_id='.$asocId.'" class="more">Datorii Locatari</a> <br />';                           
                    }
                    if ($link == 'locatari_dat') {
                            echo '<a href="index.php?link=asociatii&asoc_id='.$asocId.'" class="more">Asociatii</a> <br />';
                            echo '<a href="index.php?link=scari&asoc_id='.$asocId.'" class="more">Scari</a> <br />';
                            echo '<a href="index.php?link=locatari&asoc_id='.$asocId.'" class="more">Locatari</a> <br />';
                            echo '<a href="index.php?link=locatari_ap&asoc_id='.$asocId.'" class="more">Apometre Locatari</a> <br />';
                    }
                    if ($link == 'locatar' || $link == 'locatar_ap'  || $link == 'locatar_dat' ) {
                            echo '<a href="index.php?link=locatar&asoc_id='.$asocId.'&scara='.$scaraId.'&locatar='.$locId.'" class="more">Locatar</a> <br />';
                            echo '<a href="index.php?link=locatar_ap&asoc_id='.$asocId.'&scara='.$scaraId.'&locatar='.$locId.'" class="more">Apometre Locatar</a> <br />';
                            echo '<a href="index.php?link=locatar_dat&asoc_id='.$asocId.'&scara='.$scaraId.'&locatar='.$locId.'" class="more">Datorii Locatar</a> <br />';
                    }
                ?>                    
        </li></ul>
</div>