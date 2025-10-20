<?php
/**
 * 🌐 KONFIGURACE PRO INFINITYFREE HOSTING
 * 
 * Zkopírujte tyto nastavení do config/databaze-config.php
 * po vytvoření databáze na InfinityFree
 */

// ==================================================
// DATABÁZOVÉ PŘIPOJENÍ PRO INFINITYFREE
// ==================================================

/*
POZNÁMKA: Nahraďte tyto hodnoty skutečnými údaji z vašeho InfinityFree Control Panel

1. Jděte do Control Panel → MySQL Databases
2. Najděte tyto údaje:
   - MySQL Hostname: obvykle sql200.epizy.com nebo podobný
   - Database Name: epiz_xxxxx_názevdatabáze
   - Database Username: epiz_xxxxx_uživatel
   - Database Password: heslo které jste si nastavili

3. Zkopírujte je do config/databaze-config.php
*/

// PŘÍKLAD NASTAVENÍ PRO INFINITYFREE:
define('DB_HOST', 'sql200.epizy.com');              // MySQL Hostname z Control Panel
define('DB_NAME', 'epiz_xxxxx_cukrarstvi');         // Název vaší databáze
define('DB_USER', 'epiz_xxxxx');                    // Databázový uživatel  
define('DB_PASS', 'VaseBezpecneHeslo123');          // Heslo které jste nastavili
define('DB_CHARSET', 'utf8mb4');

// ==================================================
// POKYNY PRO NASTAVENÍ:
// ==================================================

/*
KROK 1: Vytvoření databáze na InfinityFree
- Control Panel → MySQL Databases
- Create New Database: "cukrarstvi" (výsledek: epiz_xxxxx_cukrarstvi)
- Create New User: váš uživatel (výsledek: epiz_xxxxx_user)
- Assign User to Database s ALL PRIVILEGES

KROK 2: Aktualizace konfigurace
- Otevřete config/databaze-config.php
- Nahraďte hodnoty podle údajů z Control Panel
- Uložte soubor

KROK 3: Spuštění instalace
- Otevřete: https://vašedoména.epizy.com/install.php
- Automatická instalace databázových tabulek
- Import demo dat (74 obrázků)

VÝSLEDEK:
- Web: https://vašedoména.epizy.com/
- Admin: https://vašedoména.epizy.com/admin/admin-new.php
- Login: admin / admin123
*/

// ==================================================
// BEZPEČNOSTNÍ POZNÁMKY:
// ==================================================

/*
⚠️ DŮLEŽITÉ PRO PRODUKCI:
1. Změňte admin heslo v admin/admin-new.php
2. Použijte silné databázové heslo
3. Pravidelně zálohujte databázi
4. Aktualizujte kontaktní údaje
5. Zkontrolujte všechny formuláře

🔒 DOPORUČENÁ HESLA:
- Databáze: min. 12 znaků, velká/malá písmena, čísla, symboly
- Admin: min. 8 znaků, bezpečné heslo
- Nepoužívejte slovníková slova
*/

?>