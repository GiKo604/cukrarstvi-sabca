<?php
// Načtení konfigurace
require_once '../config/konfigurace-formulare.php';

// Jednoduché heslo pro přístup (v produkci použijte lepší řešení)
$admin_heslo = "admin123"; // ZMĚŇTE TOTO HESLO!

// Kontrola přihlášení
session_start();

if (isset($_POST['prihlasit'])) {
    if ($_POST['heslo'] == $admin_heslo) {
        $_SESSION['admin_prihlasen'] = true;
    } else {
        $chyba_prihlaseni = "Nesprávné heslo!";
    }
}

if (isset($_POST['odhlasit'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$prihlasen = isset($_SESSION['admin_prihlasen']) && $_SESSION['admin_prihlasen'];

// Načítání zpráv ze souboru
$zpravy_obsah = "";
$soubor_cesta = SLOZKA_ZALOHY . '/' . SOUBOR_ZALOHY;

if ($prihlasen && file_exists($soubor_cesta)) {
    $zpravy_obsah = file_get_contents($soubor_cesta);
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace - <?php echo NAZEV_CUKRARSTVI; ?></title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 80px auto 40px auto;
            padding: 40px;
            background: rgb(240, 223, 179);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #834912;
        }
        
        .admin-header h1 {
            color: #834912;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .prihlaseni-form {
            max-width: 300px;
            margin: 0 auto;
            text-align: center;
        }
        
        .prihlaseni-form input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #834912;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        
        .admin-button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #834912 0%, #654321 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .admin-button:hover {
            background: linear-gradient(135deg, #654321 0%, #543310 100%);
            transform: translateY(-2px);
        }
        
        .zpravy-obsah {
            background: white;
            padding: 20px;
            border-radius: 10px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 14px;
            line-height: 1.4;
            max-height: 600px;
            overflow-y: auto;
            border: 2px solid #834912;
        }
        
        .chyba {
            color: #c62828;
            text-align: center;
            margin: 20px 0;
            font-weight: 700;
        }
        
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        
        .admin-menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .statistiky {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-karta {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stat-cislo {
            font-size: 32px;
            font-weight: 700;
            color: #834912;
            margin-bottom: 10px;
        }
        
        .stat-popis {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main>
        <div class="admin-container">
            <?php if (!$prihlasen): ?>
                <div class="admin-header">
                    <h1><i class="fas fa-lock"></i> Administrace</h1>
                    <p>Přihlášení do administrační části</p>
                </div>
                
                <?php if (isset($chyba_prihlaseni)): ?>
                    <div class="chyba"><?php echo $chyba_prihlaseni; ?></div>
                <?php endif; ?>
                
                <form method="post" class="prihlaseni-form">
                    <input type="password" name="heslo" placeholder="Zadejte heslo" required>
                    <button type="submit" name="prihlasit" class="admin-button">
                        <i class="fas fa-sign-in-alt"></i> Přihlásit se
                    </button>
                </form>
                
                <div class="info">
                    <strong>Výchozí heslo:</strong> admin123<br>
                    <strong>Důležité:</strong> Změňte heslo v souboru admin.php před používáním v produkci!
                </div>
                
            <?php else: ?>
                <div class="admin-header">
                    <h1><i class="fas fa-cogs"></i> Administrace zpráv</h1>
                    <p><?php echo NAZEV_CUKRARSTVI; ?></p>
                </div>
                
                <div class="admin-menu">
                    <div>
                        <strong>Přihlášen jako:</strong> Administrátor
                    </div>
                    <div>
                        <a href="index.php" class="admin-button">
                            <i class="fas fa-home"></i> Hlavní stránka
                        </a>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="odhlasit" class="admin-button">
                                <i class="fas fa-sign-out-alt"></i> Odhlásit se
                            </button>
                        </form>
                    </div>
                </div>
                
                <?php if (file_exists($soubor_cesta)): ?>
                    <?php
                    // Statistiky
                    $pocet_zprav = substr_count($zpravy_obsah, '-----');
                    $velikost_souboru = round(filesize($soubor_cesta) / 1024, 2);
                    $posledni_uprava = date('d.m.Y H:i:s', filemtime($soubor_cesta));
                    ?>
                    
                    <div class="statistiky">
                        <div class="stat-karta">
                            <div class="stat-cislo"><?php echo $pocet_zprav; ?></div>
                            <div class="stat-popis">Celkem zpráv</div>
                        </div>
                        <div class="stat-karta">
                            <div class="stat-cislo"><?php echo $velikost_souboru; ?> KB</div>
                            <div class="stat-popis">Velikost souboru</div>
                        </div>
                        <div class="stat-karta">
                            <div class="stat-cislo"><?php echo $posledni_uprava; ?></div>
                            <div class="stat-popis">Poslední zpráva</div>
                        </div>
                    </div>
                    
                    <h2><i class="fas fa-envelope"></i> Přijaté zprávy</h2>
                    <div class="zpravy-obsah"><?php echo htmlspecialchars($zpravy_obsah); ?></div>
                    
                <?php else: ?>
                    <div class="info">
                        <strong>Žádné zprávy:</strong> Soubor se zprávami zatím neexistuje.<br>
                        Zprávy se objeví po prvním odeslání formuláře.
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
        </div>
    </main>
</body>
</html>