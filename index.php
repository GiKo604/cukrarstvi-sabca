<?php
    //napíšu si seznam stránek, které existují
    //ta která nebude v seznamu se zobrazí jako chyba 404
    $poleStranek = [
        "domu"       => [
            "id"      => "domu",
            "titulek" => "Sabčino zázračné cukrářství",
            "menu"    => "Domů",
        ],
        "galerie"    => [
            "id"      => "galerie",
            "titulek" => "Galerie",
            "menu"    => "Galerie",
        ],
        "kontakt"    => [
            "id"      => "kontakt",
            "titulek" => "Kontakt",
            "menu"    => "Kontakt",
        ],
        "recepty"    => [
            "id"      => "recepty",
            "titulek" => "Recepty",
            "menu"    => "Recepty",
        ],
        "objednavky" => [
            "id"      => "objednavky",
            "titulek" => "Objednávky",
            "menu"    => "Objednávky",
        ],
    ];
    $idStranky = "domu"; //výchozí stránka
    if (array_key_exists("id-stranky", $_GET)) {
        $idStranky = $_GET["id-stranky"];
    }
    if (! array_key_exists($idStranky, $poleStranek)) {
        // pokud stránka neexistuje, nastavím na výchozí
        $idStranky = "domu";
    }
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $poleStranek[$idStranky]["titulek"]; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
</head>
<body>
<header>
        <div class = "container">
            <div class = "headerTop">
                <a href="tel:+420123123123">+420 / 123 123 123</a>
                <div class = "socIkon">
                    <a href="#" target="_blank" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" target="_blank" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <h1><?php echo $poleStranek[$idStranky]["titulek"]; ?></h1>
            <?php
                require "./komponenty/menu.php";
            ?>
    </header>
    <?php
        $htmlSoubor = "{$idStranky}.html";
        if (file_exists($htmlSoubor)) {
            include $htmlSoubor;
        } else {
            echo "<main><h1>Stránka nenalezena</h1><p>Požadovaná stránka neexistuje.</p></main>";
        }
    ?>
</body>
</html>