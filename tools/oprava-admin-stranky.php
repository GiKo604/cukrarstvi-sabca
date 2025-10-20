<?php
/**
 * 🔧 OPRAVA ADMIN STRÁNEK
 * 
 * Tento skript opraví databázovou strukturu pro správné fungování
 * vytváření stránek v admin rozhraní
 */

require_once '../config/databaze-config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>🔧 Oprava Admin Stránek</title>
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

echo "<h1>🔧 Oprava Admin Rozhraní</h1>";
echo "<p class='info'>Tento skript opraví databázi pro správné fungování vytváření stránek.</p>";

try {
    // Připojení k databázi
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='step'>";
    echo "<h3>📡 KROK 1: Připojení k databázi</h3>";
    echo "<p class='success'>✅ Připojeno k databázi " . DB_NAME . "</p>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🔧 KROK 2: Kontrola struktury tabulky stranky</h3>";
    
    // Kontrola existence sloupců
    $stmt = $pdo->query("DESCRIBE stranky");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $hasMenuNazev = in_array('menu_nazev', $columns);
    $hasPoradi = in_array('poradi', $columns);
    
    if ($hasMenuNazev && $hasPoradi) {
        echo "<p class='success'>✅ Tabulka stranky má správnou strukturu!</p>";
        echo "<p class='info'>ℹ️ Oprava není potřeba.</p>";
    } else {
        echo "<p class='info'>🔍 Chybějící sloupce: ";
        if (!$hasMenuNazev) echo "menu_nazev ";
        if (!$hasPoradi) echo "poradi ";
        echo "</p>";
        
        echo "<h3>🛠️ KROK 3: Přidání chybějících sloupců</h3>";
        
        if (!$hasMenuNazev) {
            $pdo->exec("ALTER TABLE `stranky` ADD COLUMN `menu_nazev` varchar(200) DEFAULT NULL AFTER `slug`");
            echo "<p class='success'>✅ Přidán sloupec menu_nazev</p>";
        }
        
        if (!$hasPoradi) {
            $pdo->exec("ALTER TABLE `stranky` ADD COLUMN `poradi` int(11) DEFAULT 0 AFTER `meta_klicova_slova`");
            $pdo->exec("ALTER TABLE `stranky` ADD INDEX `poradi` (`poradi`)");
            echo "<p class='success'>✅ Přidán sloupec poradi s indexem</p>";
        }
        
        // Aktualizace existujících záznamů
        if (!$hasMenuNazev) {
            $pdo->exec("UPDATE `stranky` SET `menu_nazev` = `nazev` WHERE `menu_nazev` IS NULL");
            echo "<p class='success'>✅ Aktualizovány existující záznamy</p>";
        }
    }
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>✅ KROK 4: Ověření</h3>";
    
    // Zobrazení aktuální struktury
    $stmt = $pdo->query("DESCRIBE stranky");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='success'>✅ Aktuální struktura tabulky stranky:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr><th>Sloupec</th><th>Typ</th><th>Null</th><th>Klíč</th><th>Default</th></tr>";
    
    foreach ($structure as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='step' style='border-left-color: #28a745; background: #d4edda;'>";
    echo "<h3>🎉 OPRAVA DOKONČENA!</h3>";
    echo "<p><strong>Admin rozhraní je nyní připraveno:</strong></p>";
    echo "<ul>";
    echo "<li>🌐 <strong>Frontend:</strong> <a href='../index.php' target='_blank'>Hlavní stránka</a></li>";
    echo "<li>🔐 <strong>Admin:</strong> <a href='../admin/admin-new.php?sekce=stranky' target='_blank'>Správa stránek</a></li>";
    echo "<li>📝 <strong>Nyní můžete vytvářet nové stránky bez chyb!</strong></li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='step' style='border-left-color: #dc3545; background: #f8d7da;'>";
    echo "<h3>❌ CHYBA PRI OPRAVĚ</h3>";
    echo "<p class='error'>Chyba: " . $e->getMessage() . "</p>";
    echo "<p class='info'><strong>Řešení:</strong></p>";
    echo "<ul>";
    echo "<li>🔧 Zkontrolujte připojení k databázi</li>";
    echo "<li>🗄️ Ověřte, že tabulka 'stranky' existuje</li>";
    echo "<li>👤 Zkontrolujte oprávnění databázového uživatele</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div style='margin-top: 30px; text-align: center; color: #6c757d;'>";
echo "<p>📧 Tento soubor můžete po úspěšné opravě smazat</p>";
echo "<p><small>Oprava databáze pro admin stránky CMS systému</small></p>";
echo "</div>";

echo "</div>
</body>
</html>";
?>