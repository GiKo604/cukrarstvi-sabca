<?php
// Test připojení databáze a obsahu

require_once '../config/databaze-config.php';

echo "<h2>Test databáze</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Databáze připojena</p>";
    
    // Test receptů
    $recepty = $db->fetchAll("SELECT COUNT(*) as pocet FROM recepty");
    echo "<p>Počet receptů: " . ($recepty[0]['pocet'] ?? 0) . "</p>";
    
    // Test galerie
    $galerie = $db->fetchAll("SELECT COUNT(*) as pocet FROM galerie");
    echo "<p>Počet obrázků v galerii: " . ($galerie[0]['pocet'] ?? 0) . "</p>";
    
    // Test stránek
    $stranky = $db->fetchAll("SELECT COUNT(*) as pocet FROM stranky");
    echo "<p>Počet stránek: " . ($stranky[0]['pocet'] ?? 0) . "</p>";
    
    // Seznam tabulek
    $tabulky = $db->fetchAll("SHOW TABLES");
    echo "<h3>Dostupné tabulky:</h3><ul>";
    foreach ($tabulky as $tabulka) {
        $nazev = array_values($tabulka)[0];
        echo "<li>$nazev</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
    echo "<p>Pravděpodobně potřebujete vytvořit databázi. Importujte soubor databaze-schema.sql do vašeho MySQL.</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #834912; }
</style>