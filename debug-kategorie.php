<?php
// Debug skript pro kontrolu kategorie selectu

require_once 'databaze-config.php';

echo "<h2>Debug kategorie selectu</h2>";

// Kategorie seznam
$kategorie_seznam = [
    'dorty-torty' => 'Dorty a Torty',
    'cupcakes-muffiny' => 'Cupcakes a Muffiny', 
    'susenky-cukrovi' => 'Sušenky a Cukroví',
    'dezerty-speciality' => 'Dezerty a Speciality'
];

echo "<h3>Test kategorie selectu:</h3>";
echo "<select>";
echo "<option value=''>-- Vyberte kategorii --</option>";
foreach ($kategorie_seznam as $hodnota => $nazev) {
    echo "<option value='" . htmlspecialchars($hodnota) . "'>" . htmlspecialchars($nazev) . "</option>";
}
echo "</select>";

echo "<h3>Kategorie seznam:</h3>";
echo "<pre>";
print_r($kategorie_seznam);
echo "</pre>";

try {
    $db = Database::getInstance();
    $obrazky = $db->fetchAll("SELECT kategorie, COUNT(*) as pocet FROM galerie GROUP BY kategorie");
    
    echo "<h3>Kategorie v databázi:</h3>";
    echo "<pre>";
    print_r($obrazky);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>Chyba databáze: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #834912; }
select { padding: 10px; font-size: 16px; width: 300px; }
</style>