<?php
// Načtení konfigurace
require_once '../config/konfigurace-formulare.php';
require_once '../config/databaze-config.php';

// Inicializace databáze
$db = Database::getInstance();

// Inicializace proměnných pro zprávy
$zprava = "";
$chyba = false;

// Ověření, zda byl formulář odeslán
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["odeslat"])) {
    
    // Sanitizace a validace vstupních dat
    $jmeno = trim($_POST["jmeno"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $zpravaOdKlienta = trim($_POST["zprava"] ?? "");
    
    // Validace pomocí funkcí z konfigurace
    $chybaValidace = validovat_jmeno($jmeno);
    if ($chybaValidace) {
        $chyba = true;
        $zprava = $chybaValidace;
    }
    
    if (!$chyba) {
        $chybaValidace = validovat_email($email);
        if ($chybaValidace) {
            $chyba = true;
            $zprava = $chybaValidace;
        }
    }
    
    if (!$chyba) {
        $chybaValidace = validovat_zpravu($zpravaOdKlienta);
        if ($chybaValidace) {
            $chyba = true;
            $zprava = $chybaValidace;
        }
    }
    
    // Pokud nejsou chyby, odešleme email
    if (!$chyba) {
        // Příprava emailu
        $predmet = EMAIL_PREDMET;
        
        // HTML obsah emailu
        $htmlObsah = "
        <!DOCTYPE html>
        <html lang='cs'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #834912; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #834912; }
                .value { margin-top: 5px; padding: 10px; background: white; border-left: 4px solid #834912; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>" . NAZEV_CUKRARSTVI . "</h1>
                    <p>Nová zpráva z kontaktního formuláře</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Jméno:</div>
                        <div class='value'>" . htmlspecialchars($jmeno) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>" . htmlspecialchars($email) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Zpráva:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($zpravaOdKlienta)) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Tato zpráva byla odeslána z kontaktního formuláře na webu " . NAZEV_CUKRARSTVI . "</p>
                    <p>Datum odeslání: " . date('d.m.Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Hlavičky emailu
        $hlavicky = array(
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
            'From' => "$jmeno <$email>",
            'Reply-To' => $email,
            'X-Mailer' => 'PHP/' . phpversion()
        );
        
        // Sestavení hlaviček pro mail()
        $hlavickyString = "";
        foreach ($hlavicky as $klic => $hodnota) {
            $hlavickyString .= "$klic: $hodnota\r\n";
        }
        
        // Pokus o odeslání emailu (pouze pokud je povoleno)
        $emailOdeslan = true;
        if (POVOLIT_EMAILY) {
            $emailOdeslan = mail(EMAIL_CUKRARSTVI, $predmet, $htmlObsah, $hlavickyString);
        }
        
        if ($emailOdeslan) {
            // Uložení zprávy do databáze
            try {
                $db->insert('kontaktni_zpravy', [
                    'jmeno' => $jmeno,
                    'email' => $email,
                    'zprava' => $zpravaOdKlienta,
                    'ip_adresa' => LOGOVAT_IP ? ($_SERVER['REMOTE_ADDR'] ?? null) : null,
                    'stav' => 'nová'
                ]);
            } catch (Exception $e) {
                // Pokud se nepodaří uložit do databáze, logujeme chybu
                error_log("Chyba při ukládání zprávy do databáze: " . $e->getMessage());
            }
            
            $zprava = POVOLIT_EMAILY ? $zpravy['uspech'] : "Zpráva byla úspěšně uložena. (Email odesílání je vypnuto pro lokální vývoj)";
            
            // Uložení zprávy do souboru (záloha) - pokud je povoleno
            if (POVOLIT_ZALOHY) {
                $zalohaData = array(
                    'datum' => date('Y-m-d H:i:s'),
                    'jmeno' => $jmeno,
                    'email' => $email,
                    'zprava' => $zpravaOdKlienta,
                    'ip' => LOGOVAT_IP ? ($_SERVER['REMOTE_ADDR'] ?? 'neznámá') : 'nelogováno'
                );
                
                $zalohaText = "\n" . str_repeat("-", 50) . "\n";
                $zalohaText .= "Datum: " . $zalohaData['datum'] . "\n";
                $zalohaText .= "Jméno: " . $zalohaData['jmeno'] . "\n";
                $zalohaText .= "Email: " . $zalohaData['email'] . "\n";
                $zalohaText .= "IP adresa: " . $zalohaData['ip'] . "\n";
                $zalohaText .= "Zpráva:\n" . $zalohaData['zprava'] . "\n";
                
                // Vytvoření složky pro zálohy, pokud neexistuje
                if (!file_exists(SLOZKA_ZALOHY)) {
                    mkdir(SLOZKA_ZALOHY, 0755, true);
                }
                
                // Uložení do souboru
                file_put_contents(SLOZKA_ZALOHY . '/' . SOUBOR_ZALOHY, $zalohaText, FILE_APPEND | LOCK_EX);
            }
            
        } else {
            $chyba = true;
            $zprava = POVOLIT_EMAILY ? $zpravy['chyba_odeslani'] : "Chyba při ukládání zprávy do souboru.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zpracování formuláře - <?php echo NAZEV_CUKRARSTVI; ?></title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .zprava-container {
            max-width: 600px;
            margin: 80px auto 40px auto;
            padding: 40px;
            background: rgb(240, 223, 179);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            font-family: 'Montserrat', sans-serif;
        }
        
        .zprava-uspech {
            color: #2e7d32;
            border: 2px solid #4caf50;
            background: #e8f5e8;
        }
        
        .zprava-chyba {
            color: #c62828;
            border: 2px solid #f44336;
            background: #ffebee;
        }
        
        .zprava-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .zprava-container p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .zprava-container .tlacitko {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #834912 0%, #654321 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(131, 73, 18, 0.3);
        }
        
        .zprava-container .tlacitko:hover {
            background: linear-gradient(135deg, #654321 0%, #543310 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(131, 73, 18, 0.4);
        }
        
        .ikona {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .uspech .ikona {
            color: #4caf50;
        }
        
        .chyba .ikona {
            color: #f44336;
        }
    </style>
</head>
<body>
    <main>
        <h1><?php echo NAZEV_CUKRARSTVI; ?></h1>
        
        <?php if (!empty($zprava)): ?>
            <div class="zprava-container <?php echo $chyba ? 'zprava-chyba chyba' : 'zprava-uspech uspech'; ?>">
                <div class="ikona">
                    <?php if ($chyba): ?>
                        <i class="fas fa-times-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-check-circle"></i>
                    <?php endif; ?>
                </div>
                
                <h1><?php echo $chyba ? 'Chyba' : 'Úspěch'; ?></h1>
                <p><?php echo htmlspecialchars($zprava); ?></p>
                
                <a href="<?php echo $chyba ? 'kontakt.html' : 'index.php'; ?>" class="tlacitko">
                    <?php echo $chyba ? 'Zpět na formulář' : 'Zpět na hlavní stránku'; ?>
                </a>
            </div>
        <?php else: ?>
            <div class="zprava-container zprava-chyba chyba">
                <div class="ikona">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1>Neplatný přístup</h1>
                <p><?php echo $zpravy['neplatny_pristup']; ?></p>
                <a href="kontakt.html" class="tlacitko">Přejít na kontaktní formulář</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>