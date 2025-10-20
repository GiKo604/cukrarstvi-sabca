<?php
// Testovací PHP soubor
echo "<h1>PHP Test</h1>";
echo "<p>PHP verze: " . phpversion() . "</p>";
echo "<p>Datum a čas: " . date('Y-m-d H:i:s') . "</p>";

// Test načítání konfigurace
try {
    if (file_exists('konfigurace-formulare.php')) {
        require_once 'konfigurace-formulare.php';
        echo "<p>✅ Konfigurace načtena úspěšně</p>";
        echo "<p>Email cukrářství: " . EMAIL_CUKRARSTVI . "</p>";
        echo "<p>Název cukrářství: " . NAZEV_CUKRARSTVI . "</p>";
    } else {
        echo "<p>❌ Konfigurační soubor neexistuje</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Chyba při načítání konfigurace: " . $e->getMessage() . "</p>";
}

// Test funkcí
echo "<h2>Test funkcí</h2>";
if (function_exists('mail')) {
    echo "<p>✅ Mail funkce je dostupná</p>";
} else {
    echo "<p>❌ Mail funkce není dostupná</p>";
}

if (function_exists('session_start')) {
    echo "<p>✅ Session funkce jsou dostupné</p>";
} else {
    echo "<p>❌ Session funkce nejsou dostupné</p>";
}

// Test práv na zápis
$testDir = 'zalohy';
if (is_writable('.')) {
    echo "<p>✅ Můžeme zapisovat do aktuální složky</p>";
    
    if (!file_exists($testDir)) {
        if (mkdir($testDir, 0755, true)) {
            echo "<p>✅ Složka $testDir byla vytvořena</p>";
        } else {
            echo "<p>❌ Nepodařilo se vytvořit složku $testDir</p>";
        }
    } else {
        echo "<p>✅ Složka $testDir již existuje</p>";
    }
} else {
    echo "<p>❌ Nemáme práva na zápis do aktuální složky</p>";
}

echo "<h2>Server Info</h2>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Neznámý') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Neznámý') . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Neznámý') . "</p>";

phpinfo();
?>