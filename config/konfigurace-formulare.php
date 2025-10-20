<?php
// Konfigurace pro zpracování formulářů
// Tuto stránku můžete upravit podle vašich potřeb

// ==================================================
// ZÁKLADNÍ NASTAVENÍ
// ==================================================

// Email cukrářství, na který se budou posílat zprávy
define('EMAIL_CUKRARSTVI', 'sobekjoey@gmail.com');

// Název cukrářství
define('NAZEV_CUKRARSTVI', 'Sabčino zázračné cukrářství');

// ==================================================
// NASTAVENÍ EMAILU
// ==================================================

// Předmět emailu
define('EMAIL_PREDMET', 'Nová zpráva z webu - ' . NAZEV_CUKRARSTVI);

// ==================================================
// NASTAVENÍ VALIDACE
// ==================================================

// Minimální délka jména
define('MIN_DELKA_JMENO', 2);

// Maximální délka jména
define('MAX_DELKA_JMENO', 50);

// Minimální délka zprávy
define('MIN_DELKA_ZPRAVA', 10);

// Maximální délka zprávy
define('MAX_DELKA_ZPRAVA', 1000);

// ==================================================
// NASTAVENÍ ZÁLOHOVÁNÍ
// ==================================================

// Povolit zálohování zpráv do souboru
define('POVOLIT_ZALOHY', true);

// Povolit odesílání emailů (false pro lokální vývoj)
define('POVOLIT_EMAILY', false);

// Název složky pro zálohy
define('SLOZKA_ZALOHY', 'zalohy');

// Název souboru pro zálohy
define('SOUBOR_ZALOHY', 'zpravy.txt');

// ==================================================
// BEZPEČNOSTNÍ NASTAVENÍ
// ==================================================

// Povolit logování IP adres
define('LOGOVAT_IP', true);

// Seznam povolených domén pro přesměrování (security)
$povolene_domeny = [
    'localhost',
    'sabcinecukrarstvi.cz',
    'www.sabcinecukrarstvi.cz'
];

// ==================================================
// ZPRÁVY PRO UŽIVATELE
// ==================================================

$zpravy = [
    'uspech' => 'Děkujeme! Vaše zpráva byla úspěšně odeslána. Odpovíme vám co nejdříve.',
    'chyba_odeslani' => 'Omlouváme se, ale zprávu se nepodařilo odeslat. Zkuste to prosím později nebo nás kontaktujte přímo na telefonu.',
    'prazdne_jmeno' => 'Prosím vyplňte své jméno.',
    'kratke_jmeno' => 'Jméno musí mít alespoň ' . MIN_DELKA_JMENO . ' znaky.',
    'dlouhe_jmeno' => 'Jméno může mít maximálně ' . MAX_DELKA_JMENO . ' znaků.',
    'prazdny_email' => 'Prosím vyplňte svůj email.',
    'neplatny_email' => 'Prosím zadejte platný email.',
    'prazdna_zprava' => 'Prosím napište svou zprávu.',
    'kratka_zprava' => 'Zpráva musí mít alespoň ' . MIN_DELKA_ZPRAVA . ' znaků.',
    'dlouha_zprava' => 'Zpráva je příliš dlouhá (max ' . MAX_DELKA_ZPRAVA . ' znaků).',
    'neplatny_pristup' => 'Tato stránka slouží pouze pro zpracování formulářů.'
];

// ==================================================
// FUNKCE PRO VALIDACI
// ==================================================

/**
 * Validuje jméno
 */
function validovat_jmeno($jmeno) {
    global $zpravy;
    
    if (empty($jmeno)) {
        return $zpravy['prazdne_jmeno'];
    }
    
    if (strlen($jmeno) < MIN_DELKA_JMENO) {
        return $zpravy['kratke_jmeno'];
    }
    
    if (strlen($jmeno) > MAX_DELKA_JMENO) {
        return $zpravy['dlouhe_jmeno'];
    }
    
    return null; // Bez chyby
}

/**
 * Validuje email
 */
function validovat_email($email) {
    global $zpravy;
    
    if (empty($email)) {
        return $zpravy['prazdny_email'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $zpravy['neplatny_email'];
    }
    
    return null; // Bez chyby
}

/**
 * Validuje zprávu
 */
function validovat_zpravu($zprava) {
    global $zpravy;
    
    if (empty($zprava)) {
        return $zpravy['prazdna_zprava'];
    }
    
    if (strlen($zprava) < MIN_DELKA_ZPRAVA) {
        return $zpravy['kratka_zprava'];
    }
    
    if (strlen($zprava) > MAX_DELKA_ZPRAVA) {
        return $zpravy['dlouha_zprava'];
    }
    
    return null; // Bez chyby
}

// ==================================================
// INFORMACE O KONFIGURACI
// ==================================================

/*
NÁVOD PRO POUŽITÍ:

1. Upravte email cukrářství v konstantě EMAIL_CUKRARSTVI
2. Upravte název cukrářství v konstantě NAZEV_CUKRARSTVI
3. Můžete upravit limity pro validaci (MIN_DELKA_*, MAX_DELKA_*)
4. Můžete upravit zprávy pro uživatele v poli $zpravy
5. Pro vypnutí zálohování nastavte POVOLIT_ZALOHY na false

BEZPEČNOST:
- Všechny vstupy jsou automaticky sanitizovány
- IP adresy se logují pro bezpečnost
- Povolené domény pro přesměrování jsou omezené

ZÁLOHY:
- Všechny zprávy se automaticky ukládají do souboru
- Zálohy obsahují datum, jméno, email, IP a zprávu
- Soubor se nachází ve složce 'zalohy/zpravy.txt'
*/

?>