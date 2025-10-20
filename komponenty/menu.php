     <nav class="hl-nav">
                <ul>
                    <?php
                        foreach($poleStranek as $stranka) {
                            if ($stranka['menu'] == "") {
                                continue; 
                            }
                            //pokud je id stranky 404, tak ji v menu neukazujeme
                           echo "<li><a href='index.php?id-stranky={$stranka['id']}'>{$stranka['menu']}</a></li>";

                        }
                    ?>
                </ul>
            </nav>
