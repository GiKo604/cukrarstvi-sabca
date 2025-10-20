<?php
    // Načtení databázové konfigurace
    require_once 'config/databaze-config.php';
    
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
    ];
    
    // Načtení databázových stránek
    $databazoveStranky = [];
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT * FROM stranky WHERE aktivni = 1 ORDER BY poradi, nazev");
        while ($stranka = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $databazoveStranky[$stranka['slug']] = [
                "id" => $stranka['slug'],
                "titulek" => $stranka['nazev'],
                "menu" => $stranka['menu_nazev'] ?: $stranka['nazev'],
                "obsah" => $stranka['obsah'],
                "meta_popis" => $stranka['meta_popis'],
                "meta_klicova_slova" => $stranka['meta_klicova_slova']
            ];
        }
    } catch (Exception $e) {
        // Pokud databáze nefunguje, pokračujeme jen se statickými stránkami
    }
    
    // Sloučení statických a databázových stránek
    $vsechnyStranky = array_merge($poleStranek, $databazoveStranky);
    
    $idStranky = "domu"; //výchozí stránka
    if (array_key_exists("id-stranky", $_GET)) {
        $idStranky = $_GET["id-stranky"];
    }
    if (! array_key_exists($idStranky, $vsechnyStranky)) {
        // pokud stránka neexistuje, nastavím na výchozí
        $idStranky = "domu";
    }
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $vsechnyStranky[$idStranky]["titulek"]; ?></title>
    <?php if (isset($vsechnyStranky[$idStranky]['meta_popis']) && !empty($vsechnyStranky[$idStranky]['meta_popis'])): ?>
    <meta name="description" content="<?php echo htmlspecialchars($vsechnyStranky[$idStranky]['meta_popis']); ?>">
    <?php endif; ?>
    <?php if (isset($vsechnyStranky[$idStranky]['meta_klicova_slova']) && !empty($vsechnyStranky[$idStranky]['meta_klicova_slova'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($vsechnyStranky[$idStranky]['meta_klicova_slova']); ?>">
    <?php endif; ?>
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
            <h1><?php echo $vsechnyStranky[$idStranky]["titulek"]; ?></h1>
            <?php
                require "./komponenty/menu.php";
            ?>
    </header>
    <?php
        // Kontrola, zda je to databázová stránka
        if (isset($vsechnyStranky[$idStranky]['obsah'])) {
            // Databázová stránka
            echo "<main>";
            echo "<div class='container'>";
            echo "<div class='obsah-stranky'>";
            echo $vsechnyStranky[$idStranky]['obsah'];
            echo "</div>";
            echo "</div>";
            echo "</main>";
        } else {
            // Statická HTML stránka
            $htmlSoubor = "{$idStranky}.html";
            if (file_exists($htmlSoubor)) {
                include $htmlSoubor;
            } else {
                echo "<main><div class='container'><h1>Stránka nenalezena</h1><p>Požadovaná stránka neexistuje.</p></div></main>";
            }
        }
    ?>
</body>
</html>