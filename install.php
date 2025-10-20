<?php
/**
 * 🚀 AUTOMATICKÁ INSTALACE CMS SYSTÉMU
 * 
 * Tento skript automaticky vytvoří databázi a tabulky
 * Spusťte jednou po stažení projektu
 */

// Konfigurace databáze
$host = 'localhost';
$username = 'root';
$password = '';  // XAMPP má prázdné heslo pro root
$database = 'cukrarstvi_sabca';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>🔧 Instalace CMS Systému</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #007bff; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff; }
        h1 { color: #343a40; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🧁 Instalace CMS pro Cukrářství</h1>";

try {
    // KROK 1: Připojení k MySQL (bez databáze)
    echo "<div class='step'>";
    echo "<h3>📡 KROK 1: Připojení k MySQL serveru</h3>";
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Připojení k MySQL úspěšné!</p>";
    echo "</div>";

    // KROK 2: Vytvoření databáze
    echo "<div class='step'>";
    echo "<h3>🗄️ KROK 2: Vytvoření databáze</h3>";
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✅ Databáze '$database' vytvořena!</p>";
    echo "</div>";

    // KROK 3: Připojení k nové databázi
    echo "<div class='step'>";
    echo "<h3>🔌 KROK 3: Připojení k databázi</h3>";
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Připojeno k databázi '$database'!</p>";
    echo "</div>";

    // KROK 4: Čtení a spuštění SQL souboru
    echo "<div class='step'>";
    echo "<h3>📋 KROK 4: Import databázových tabulek</h3>";
    
    $sqlFile = 'database/schema.sql';
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Rozdělení na jednotlivé příkazy
        $statements = explode(';', $sql);
        $executed = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
                $executed++;
            }
        }
        
        echo "<p class='success'>✅ Import dokončen! Spuštěno $executed SQL příkazů.</p>";
        
        // Aplikování patch pro správné fungování admin stránek
        echo "<p class='info'>🔧 Aplikování oprav pro admin rozhraní...</p>";
        $patchFile = 'database/patch-stranky.sql';
        if (file_exists($patchFile)) {
            try {
                $patchSql = file_get_contents($patchFile);
                $patchStatements = explode(';', $patchSql);
                $patchExecuted = 0;
                
                foreach ($patchStatements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement) && !preg_match('/^--/', $statement) && !preg_match('/^DESCRIBE/', $statement)) {
                        $pdo->exec($statement);
                        $patchExecuted++;
                    }
                }
                echo "<p class='success'>✅ Admin opravy aplikovány! ($patchExecuted příkazů)</p>";
            } catch (Exception $e) {
                echo "<p class='info'>ℹ️ Některé opravy již byly aplikovány nebo nejsou potřeba.</p>";
            }
        }
    } else {
        echo "<p class='error'>❌ SQL soubor nenalezen: $sqlFile</p>";
        echo "<p class='info'>💡 Zkopírujte SQL soubor do složky database/</p>";
    }
    echo "</div>";

    // KROK 5: Ověření instalace
    echo "<div class='step'>";
    echo "<h3>✅ KROK 5: Ověření instalace</h3>";
    
    // Kontrola tabulek
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p class='success'>✅ Nalezeno " . count($tables) . " databázových tabulek:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>📋 $table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ Žádné tabulky nebyly vytvořeny!</p>";
    }
    echo "</div>";

    // KROK 6: Kontrola konfigurace
    echo "<div class='step'>";
    echo "<h3>⚙️ KROK 6: Kontrola konfigurace</h3>";
    
    $configFile = 'config/databaze-config.php';
    if (file_exists($configFile)) {
        echo "<p class='success'>✅ Konfigurační soubor nalezen: $configFile</p>";
        echo "<p class='info'>💡 Zkontrolujte připojení v tomto souboru pokud máte problémy</p>";
    } else {
        echo "<p class='error'>❌ Konfigurační soubor nenalezen!</p>";
    }
    echo "</div>";

    // VÝSLEDEK
    echo "<div class='step' style='border-left-color: #28a745; background: #d4edda;'>";
    echo "<h3>🎉 INSTALACE DOKONČENA!</h3>";
    echo "<p><strong>Váš CMS systém je připraven k použití:</strong></p>";
    echo "<ul>";
    echo "<li>🌐 <strong>Web:</strong> <a href='../index.html' target='_blank'>http://localhost/cukrarstvi-sabca/</a></li>";
    echo "<li>🔐 <strong>Admin:</strong> <a href='../admin/admin-new.php' target='_blank'>http://localhost/cukrarstvi-sabca/admin/admin-new.php</a></li>";
    echo "<li>👤 <strong>Přihlášení:</strong> admin / admin123</li>";
    echo "</ul>";
    echo "<p class='info'>⚠️ <strong>Důležité:</strong> Změňte admin heslo po prvním přihlášení!</p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='step' style='border-left-color: #dc3545; background: #f8d7da;'>";
    echo "<h3>❌ CHYBA PRI INSTALACI</h3>";
    echo "<p class='error'>Chyba: " . $e->getMessage() . "</p>";
    echo "<p class='info'><strong>Řešení:</strong></p>";
    echo "<ul>";
    echo "<li>🔧 Zkontrolujte, že XAMPP běží (Apache + MySQL)</li>";
    echo "<li>🌐 Otevřete <a href='http://localhost/phpmyadmin/' target='_blank'>phpMyAdmin</a></li>";
    echo "<li>📁 Ověřte, že jste ve správné složce</li>";
    echo "<li>🗄️ Zkontrolujte existenci database/cukrarstvi_sabca.sql</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div style='margin-top: 30px; text-align: center; color: #6c757d;'>";
echo "<p>📧 Tento soubor můžete po úspěšné instalaci smazat</p>";
echo "<p><small>Automatická instalace CMS systému pro cukrářství</small></p>";
echo "</div>";

echo "</div>
</body>
</html>";
?>