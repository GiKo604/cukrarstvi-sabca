<?php
// Kontrola kategorií receptů v databázi

require_once '../config/databaze-config.php';

echo "<h2>Kontrola kategorií receptů</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Databáze připojena</p>";
    
    // Kontrola všech kategorií receptů
    $kategorie = $db->fetchAll("SELECT * FROM kategorie_receptu ORDER BY poradi, nazev");
    
    echo "<h3>Kategorie receptů (" . count($kategorie) . " celkem):</h3>";
    
    if (empty($kategorie)) {
        echo "<p>Žádné kategorie v databázi.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>ID</th><th>Název</th><th>Slug</th><th>Pořadí</th><th>Aktivní</th>";
        echo "</tr>";
        
        foreach ($kategorie as $kat) {
            echo "<tr>";
            echo "<td>" . $kat['id'] . "</td>";
            echo "<td>" . htmlspecialchars($kat['nazev']) . "</td>";
            echo "<td>" . htmlspecialchars($kat['slug']) . "</td>";
            echo "<td>" . $kat['poradi'] . "</td>";
            echo "<td>" . ($kat['aktivni'] ? 'Ano' : 'Ne') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Kontrola duplicit
    echo "<h3>Kontrola duplicitních kategorií:</h3>";
    $duplicity = $db->fetchAll("
        SELECT nazev, COUNT(*) as pocet 
        FROM kategorie_receptu 
        GROUP BY nazev 
        HAVING COUNT(*) > 1
    ");
    
    if (empty($duplicity)) {
        echo "<p style='color: green;'>✓ Žádné duplicitní kategorie</p>";
    } else {
        echo "<p style='color: red;'>⚠ Nalezeny duplicitní kategorie:</p>";
        echo "<ul>";
        foreach ($duplicity as $dup) {
            echo "<li>" . htmlspecialchars($dup['nazev']) . " (" . $dup['pocet'] . "x)</li>";
        }
        echo "</ul>";
    }
    
    // Test selectu
    echo "<h3>Test selectu kategorií:</h3>";
    echo "<select style='padding: 10px; font-size: 16px; width: 300px;'>";
    echo "<option value=''>-- Vyberte kategorii --</option>";
    foreach ($kategorie as $kat) {
        echo "<option value='" . $kat['id'] . "'>" . htmlspecialchars($kat['nazev']) . "</option>";
    }
    echo "</select>";
    
    // Akce pro vyčištění
    echo "<h3>Akce:</h3>";
    echo "<a href='vymazat-duplicity-kategorie.php' style='background: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Vymazat duplicitní kategorie</a>";
    
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