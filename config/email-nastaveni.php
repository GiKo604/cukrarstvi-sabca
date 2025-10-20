<?php
// Načtení konfigurace
require_once 'konfigurace-formulare.php';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email nastavení - <?php echo NAZEV_CUKRARSTVI; ?></title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .info-container {
            max-width: 800px;
            margin: 80px auto 40px auto;
            padding: 40px;
            background: rgb(240, 223, 179);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
        }
        
        .status-ok {
            color: #2e7d32;
            background: #e8f5e8;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #4caf50;
            margin: 20px 0;
        }
        
        .status-warning {
            color: #f57c00;
            background: #fff3e0;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #ff9800;
            margin: 20px 0;
        }
        
        .code-block {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            border-left: 4px solid #2196f3;
        }
        
        .nav-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #834912 0%, #654321 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-button:hover {
            background: linear-gradient(135deg, #654321 0%, #543310 100%);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <main>
        <div class="info-container">
            <h1><i class="fas fa-envelope-open-text"></i> Email nastavení</h1>
            
            <h2>Aktuální stav</h2>
            
            <?php if (POVOLIT_EMAILY): ?>
                <div class="status-ok">
                    <strong><i class="fas fa-check-circle"></i> Emaily jsou ZAPNUTÉ</strong><br>
                    Zprávy z formuláře se odesílají na: <strong><?php echo EMAIL_CUKRARSTVI; ?></strong>
                </div>
            <?php else: ?>
                <div class="status-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Emaily jsou VYPNUTÉ</strong><br>
                    Zprávy se ukládají pouze do souboru pro lokální vývoj.
                </div>
            <?php endif; ?>
            
            <?php if (POVOLIT_ZALOHY): ?>
                <div class="status-ok">
                    <strong><i class="fas fa-save"></i> Zálohy jsou ZAPNUTÉ</strong><br>
                    Zprávy se ukládají do: <strong><?php echo SLOZKA_ZALOHY . '/' . SOUBOR_ZALOHY; ?></strong>
                </div>
            <?php else: ?>
                <div class="status-warning">
                    <strong><i class="fas fa-times-circle"></i> Zálohy jsou VYPNUTÉ</strong><br>
                    Zprávy se neukládají do souboru.
                </div>
            <?php endif; ?>
            
            <h2>Nastavení pro lokální vývoj (XAMPP)</h2>
            <p>Pro lokální vývoj na XAMPP je doporučeno mít emaily vypnuté, protože XAMPP nemá ve výchozím nastavení nakonfigurovaný SMTP server.</p>
            
            <h3>Postup pro vypnutí emailů:</h3>
            <ol>
                <li>Otevřete soubor <code>konfigurace-formulare.php</code></li>
                <li>Najděte řádek:</li>
            </ol>
            <div class="code-block">define('POVOLIT_EMAILY', true);</div>
            <ol start="3">
                <li>Změňte na:</li>
            </ol>
            <div class="code-block">define('POVOLIT_EMAILY', false);</div>
            
            <h2>Nastavení pro produkční server</h2>
            <p>Na produkčním serveru můžete emaily zapnout:</p>
            
            <h3>Postup pro zapnutí emailů:</h3>
            <ol>
                <li>Ujistěte se, že server má funkční SMTP</li>
                <li>V souboru <code>konfigurace-formulare.php</code> nastavte:</li>
            </ol>
            <div class="code-block">
define('POVOLIT_EMAILY', true);<br>
define('EMAIL_CUKRARSTVI', 'vas-email@domena.cz');
            </div>
            
            <h2>Test funkčnosti</h2>
            <p>Pro otestování formuláře můžete použít:</p>
            <ul>
                <li><a href="test-formular.php" class="nav-button">Testovací formulář</a></li>
                <li><a href="kontakt.html" class="nav-button">Kontaktní stránka</a></li>
                <li><a href="admin.php" class="nav-button">Administrace</a></li>
            </ul>
            
            <div class="status-ok" style="margin-top: 30px;">
                <strong>Tip:</strong> I s vypnutými emaily budou zprávy uloženy do záložního souboru, 
                takže žádná zpráva se neztratí!
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php" class="nav-button">
                    <i class="fas fa-home"></i> Zpět na hlavní stránku
                </a>
            </div>
        </div>
    </main>
</body>
</html>