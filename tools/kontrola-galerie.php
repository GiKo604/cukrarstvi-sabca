<?php
// Kontrola dat v databázi - galerie

require_once '../config/databaze-config.php';

echo "<h2>Kontrola dat v galerii databázi</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Databáze připojena</p>";
    
    // Kontrola všech obrázků v galerii
    $obrazky = $db->fetchAll("SELECT * FROM galerie ORDER BY kategorie, nazev");
    
    echo "<h3>Všechny obrázky v galerii (" . count($obrazky) . " celkem):</h3>";
    
    if (empty($obrazky)) {
        echo "<p>Žádné obrázky v databázi.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>ID</th><th>Název</th><th>Kategorie</th><th>Soubor</th><th>Aktivní</th>";
        echo "</tr>";
        
        foreach ($obrazky as $obrazek) {
            echo "<tr>";
            echo "<td>" . $obrazek['id'] . "</td>";
            echo "<td>" . htmlspecialchars($obrazek['nazev']) . "</td>";
            echo "<td>" . htmlspecialchars($obrazek['kategorie']) . "</td>";
            echo "<td>" . htmlspecialchars($obrazek['nazev_souboru']) . "</td>";
            echo "<td>" . ($obrazek['aktivni'] ? 'Ano' : 'Ne') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Kontrola kategorií
    echo "<h3>Kategorie v databázi:</h3>";
    $kategorie = $db->fetchAll("SELECT kategorie, COUNT(*) as pocet FROM galerie GROUP BY kategorie ORDER BY kategorie");
    
    if (empty($kategorie)) {
        echo "<p>Žádné kategorie.</p>";
    } else {
        echo "<ul>";
        foreach ($kategorie as $kat) {
            echo "<li><strong>" . htmlspecialchars($kat['kategorie']) . "</strong>: " . $kat['pocet'] . " obrázků</li>";
        }
        echo "</ul>";
    }
    
    // Kontrola duplicit
    echo "<h3>Kontrola duplicitních záznamů:</h3>";
    $duplicity = $db->fetchAll("
        SELECT nazev_souboru, COUNT(*) as pocet 
        FROM galerie 
        GROUP BY nazev_souboru 
        HAVING COUNT(*) > 1
    ");
    
    if (empty($duplicity)) {
        echo "<p style='color: green;'>✓ Žádné duplicitní soubory</p>";
    } else {
        echo "<p style='color: red;'>⚠ Nalezeny duplicitní soubory:</p>";
        echo "<ul>";
        foreach ($duplicity as $dup) {
            echo "<li>" . htmlspecialchars($dup['nazev_souboru']) . " (" . $dup['pocet'] . "x)</li>";
        }
        echo "</ul>";
    }
    
    // Akce pro vyčištění
    echo "<h3>Akce:</h3>";
    echo "<a href='vymazat-duplicity.php' style='background: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Vymazat všechny duplicity</a>";
    echo " ";
    echo "<a href='vymazat-vse-galerie.php' style='background: #ff9800; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Vymazat celou galerii</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #834912; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background: #f5f5f5; }
</style>