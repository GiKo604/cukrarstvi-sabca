<?php
// Vymazání duplicitních kategorií receptů

require_once '../config/databaze-config.php';

echo "<h2>Vymazání duplicitních kategorií receptů</h2>";

if ($_GET['potvrzeni'] === 'ano') {
    try {
        $db = Database::getInstance();
        
        // Najdi duplicitní kategorie
        $duplicity = $db->fetchAll("
            SELECT nazev, MIN(id) as keep_id, COUNT(*) as pocet
            FROM kategorie_receptu 
            GROUP BY nazev 
            HAVING COUNT(*) > 1
        ");
        
        $smazano = 0;
        
        foreach ($duplicity as $dup) {
            // Smaž všechny kromě nejstaršího záznamu
            $vymazane = $db->execute("
                DELETE FROM kategorie_receptu 
                WHERE nazev = :nazev 
                AND id != :keep_id
            ", [
                'nazev' => $dup['nazev'],
                'keep_id' => $dup['keep_id']
            ]);
            
            $smazano += ($dup['pocet'] - 1);
            echo "<p>✓ Kategorie {$dup['nazev']}: ponechána 1, smazáno " . ($dup['pocet'] - 1) . "</p>";
        }
        
        echo "<h3 style='color: green;'>✅ Hotovo!</h3>";
        echo "<p>Celkem smazáno duplicitních kategorií: <strong>$smazano</strong></p>";
        echo "<a href='kontrola-kategorie-receptu.php'>← Zpět na kontrolu</a>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
    }
} else {
    // Zobrazení potvrzení
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
    echo "<h3>⚠ Pozor!</h3>";
    echo "<p>Tato akce smaže všechny duplicitní kategorie receptů. Ponechá pouze nejstarší záznam pro každou kategorii.</p>";
    echo "<p><strong>Tato akce je nevratná!</strong></p>";
    echo "<a href='?potvrzeni=ano' style='background: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ano, smazat duplicity</a>";
    echo " ";
    echo "<a href='kontrola-kategorie-receptu.php' style='background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ne, vrátit se</a>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #834912; }
</style>