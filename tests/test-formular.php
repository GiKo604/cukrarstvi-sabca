<?php
// Jednoduché zpracování formuláře - verze pro debugging

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='cs'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Test zpracování formuláře</title>";
echo "</head>";
echo "<body>";

echo "<h1>Test zpracování formuláře</h1>";

// Test, zda byl formulář odeslán
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h2>Formulář byl odeslán</h2>";
    
    // Zobrazení přijatých dat
    echo "<h3>Přijatá data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Validace dat
    $jmeno = trim($_POST["jmeno"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $zprava = trim($_POST["zprava"] ?? "");
    
    echo "<h3>Zpracovaná data:</h3>";
    echo "<p><strong>Jméno:</strong> " . htmlspecialchars($jmeno) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Zpráva:</strong> " . htmlspecialchars($zprava) . "</p>";
    
    // Test validace
    $chyby = [];
    
    if (empty($jmeno)) {
        $chyby[] = "Jméno je povinné";
    }
    
    if (empty($email)) {
        $chyby[] = "Email je povinný";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $chyby[] = "Email není platný";
    }
    
    if (empty($zprava)) {
        $chyby[] = "Zpráva je povinná";
    }
    
    if (empty($chyby)) {
        echo "<h3 style='color: green;'>✅ Všechna data jsou validní!</h3>";
        
        // Test uložení do souboru
        $testSoubor = "test-zpravy.txt";
        $obsah = "Test zpráva: " . date('Y-m-d H:i:s') . "\n";
        $obsah .= "Jméno: $jmeno\n";
        $obsah .= "Email: $email\n";
        $obsah .= "Zpráva: $zprava\n";
        $obsah .= "---\n";
        
        if (file_put_contents($testSoubor, $obsah, FILE_APPEND)) {
            echo "<p style='color: green;'>✅ Zpráva byla uložena do souboru $testSoubor</p>";
        } else {
            echo "<p style='color: red;'>❌ Nepodařilo se uložit zprávu do souboru</p>";
        }
        
    } else {
        echo "<h3 style='color: red;'>❌ Chyby ve validaci:</h3>";
        echo "<ul>";
        foreach ($chyby as $chyba) {
            echo "<li style='color: red;'>$chyba</li>";
        }
        echo "</ul>";
    }
    
} else {
    echo "<p>Formulář nebyl odeslán - GET request</p>";
}

echo "<h2>Testovací formulář</h2>";
echo '<form method="post">';
echo '<p><label>Jméno: <input type="text" name="jmeno" required></label></p>';
echo '<p><label>Email: <input type="email" name="email" required></label></p>';
echo '<p><label>Zpráva: <textarea name="zprava" required></textarea></label></p>';
echo '<p><button type="submit">Odeslat test</button></p>';
echo '</form>';

echo "<h2>PHP Info</h2>";
echo "<p>PHP verze: " . phpversion() . "</p>";
echo "<p>Aktuální složka: " . getcwd() . "</p>";

// Test práv
if (is_writable('.')) {
    echo "<p style='color: green;'>✅ Složka je zapisovatelná</p>";
} else {
    echo "<p style='color: red;'>❌ Složka není zapisovatelná</p>";
}

echo "</body>";
echo "</html>";
?>