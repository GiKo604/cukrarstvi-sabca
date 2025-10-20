<?php
// Vymazání duplicitních záznamů z galerie

require_once '../config/databaze-config.php';

echo "<h2>Vymazání duplicitních záznamů</h2>";

if ($_GET['potvrzeni'] === 'ano') {
    try {
        $db = Database::getInstance();
        
        // Najdi duplicitní záznamy
        $duplicity = $db->fetchAll("
            SELECT nazev_souboru, MIN(id) as keep_id, COUNT(*) as pocet
            FROM galerie 
            GROUP BY nazev_souboru 
            HAVING COUNT(*) > 1
        ");
        
        $smazano = 0;
        
        foreach ($duplicity as $dup) {
            // Smaž všechny kromě nejstaršího záznamu
            $vymazane = $db->execute("
                DELETE FROM galerie 
                WHERE nazev_souboru = :nazev_souboru 
                AND id != :keep_id
            ", [
                'nazev_souboru' => $dup['nazev_souboru'],
                'keep_id' => $dup['keep_id']
            ]);
            
            $smazano += ($dup['pocet'] - 1);
            echo "<p>✓ Soubor {$dup['nazev_souboru']}: ponechan 1, smazáno " . ($dup['pocet'] - 1) . "</p>";
        }
        
        echo "<h3 style='color: green;'>✅ Hotovo!</h3>";
        echo "<p>Celkem smazáno duplicitních záznamů: <strong>$smazano</strong></p>";
        echo "<a href='kontrola-galerie.php'>← Zpět na kontrolu</a>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
    }
} else {
    // Zobrazení potvrzení
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
    echo "<h3>⚠ Pozor!</h3>";
    echo "<p>Tato akce smaže všechny duplicitní záznamy z galerie. Ponechá pouze nejstarší záznam pro každý soubor.</p>";
    echo "<p><strong>Tato akce je nevratná!</strong></p>";
    echo "<a href='?potvrzeni=ano' style='background: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ano, smazat duplicity</a>";
    echo " ";
    echo "<a href='kontrola-galerie.php' style='background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ne, vrátit se</a>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #834912; }
</style>