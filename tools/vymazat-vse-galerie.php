<?php
// Vymazání celé galerie

require_once '../config/databaze-config.php';

echo "<h2>Vymazání celé galerie</h2>";

if ($_GET['potvrzeni'] === 'ano') {
    try {
        $db = Database::getInstance();
        
        // Počet záznamů před smazáním
        $pocet = $db->fetchValue("SELECT COUNT(*) FROM galerie");
        
        // Smaž všechny záznamy
        $db->execute("DELETE FROM galerie");
        
        // Reset AUTO_INCREMENT
        $db->execute("ALTER TABLE galerie AUTO_INCREMENT = 1");
        
        echo "<h3 style='color: green;'>✅ Hotovo!</h3>";
        echo "<p>Smazáno záznamů: <strong>$pocet</strong></p>";
        echo "<p>Galerie je nyní prázdná a můžete znovu importovat data.</p>";
        echo "<a href='import-dat.php'>🔄 Znovu importovat data</a><br>";
        echo "<a href='kontrola-galerie.php'>← Zpět na kontrolu</a>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
    }
} else {
    // Zobrazení potvrzení
    echo "<div style='background: #ffebee; padding: 20px; border-radius: 5px; border: 1px solid #f44336;'>";
    echo "<h3>🚨 POZOR!</h3>";
    echo "<p>Tato akce smaže <strong>VŠECHNY</strong> obrázky z galerie v databázi!</p>";
    echo "<p><strong>Tato akce je nevratná!</strong></p>";
    echo "<p>Soubory na disku zůstanou, ale záznamy v databázi budou smazány.</p>";
    echo "<a href='?potvrzeni=ano' style='background: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ano, smazat vše</a>";
    echo " ";
    echo "<a href='kontrola-galerie.php' style='background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ne, vrátit se</a>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #834912; }
</style>