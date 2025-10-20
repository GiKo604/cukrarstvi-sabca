# Cukrářství Šábca - Admin CMS

## Popis
Kompletní systém pro správu obsahu pekařství a cukrářství s administrátorským rozhraním, správou receptů, galerie a kontaktních zpráv.

## ✨ Funkce

### 🔧 Administrace
- **Správa stránek** - WYSIWYG editor, SEO nastavení, URL slugy
- **Správa receptů** - Upload obrázků, kategorie, ingredience, postup
- **Správa galerie** - Batch upload, kategorizace, thumbnail generování
- **Správa zpráv** - Kontaktní formulář, filtry, stavy zpráv
- **Nastavení systému** - Změna hesla, údržba, systémové informace

### 🎨 Frontend
- Responzivní design
- Moderní CSS s gradients
- Optimalizované obrázky s thumbnaily
- SEO optimalizace

### 🛠️ Technické vlastnosti
- **PHP 7.4+** s PDO databází
- **MySQL** databáze
- **TinyMCE** WYSIWYG editor
- **FontAwesome** ikony
- **Bezpečnostní funkce** - session management, SQL injection ochrana
- **Upload systém** - automatické thumbnail generování

## Struktura projektu

### Hlavní soubory
- `index.php` - Hlavní vstupní bod s PHP routingem
- `domu.html` - Domovská stránka
- `galerie.html` - Galerie obrázků rozdělená do kategorií
- `recepty.html` - Stránka s recepty
- `kontakt.html` - Kontaktní formulář
- `zpracovani-formulare.php` - PHP zpracování kontaktního formuláře

### Konfigurace a administrace
- `konfigurace-formulare.php` - Konfigurace pro formuláře
- `admin.php` - Administrační rozhraní pro zobrazení přijatých zpráv
- `.htaccess` - Bezpečnostní a optimalizační nastavení

### Složky
- `css/` - Stylové soubory
- `img/` - Obrázky
- `komponenty/` - PHP komponenty (menu.php)
- `zalohy/` - Automaticky vytvořená složka pro zálohy zpráv

## Instalace a nastavení

### 1. Základní nastavení
1. Nahrajte všechny soubory na web server s podporou PHP
2. Ujistěte se, že máte práva na zápis pro vytvoření složky `zalohy/`

### 2. Konfigurace emailu
Upravte soubor `konfigurace-formulare.php`:
```php
// Změňte email na váš skutečný email
define('EMAIL_CUKRARSTVI', 'vas-email@domena.cz');

// Upravte název cukrářství
define('NAZEV_CUKRARSTVI', 'Váš název cukrářství');
```

### 3. Administrace
- Přístup: `http://vase-domena.cz/admin.php`
- Výchozí heslo: `admin123`
- **DŮLEŽITÉ:** Změňte heslo v souboru `admin.php` před nasazením do produkce!

### 4. Bezpečnost
Pro vyšší bezpečnost:
1. Změňte heslo v `admin.php`
2. Odkomentujte řádky v `.htaccess` pro omezení přístupu k admin.php pouze z lokální sítě
3. Pravidelně zálohujte složku `zalohy/`

## Funkce

### Kontaktní formulář
- Automatická validace vstupů
- Odesílání HTML emailů
- Automatické zálohování zpráv do souboru
- Logování IP adres (volitelné)
- Responzivní design

### Galerie
- 4 kategorie produktů
- Responzivní grid layout
- Hover efekty
- Optimalizováno pro různé velikosti obrazovek

### Recepty
- 9 detailních receptů
- Informace o čase přípravy a porcích
- Přehledné zobrazení ingrediencí a postupu

### Administrace
- Přehled všech přijatých zpráv
- Statistiky (počet zpráv, velikost souboru)
- Zabezpečené přihlášení

## Technické požadavky
- PHP 7.0 nebo vyšší
- Apache web server (pro .htaccess)
- Funkční mail() funkce PHP pro odesílání emailů

## Customizace

### Barvy
Hlavní barvy jsou definovány v `css/style.css`:
- Hlavní barva: `#834912` (hnědá)
- Střední barva: `#654321`
- Tmavá barva: `#543310`
- Pozadí: `rgb(240, 223, 179)` (béžová)

### Přidání nových stránek
1. Vytvořte nový HTML soubor
2. Přidejte jej do pole `$poleStranek` v `index.php`
3. Přidejte odkaz do `komponenty/menu.php`

### Přidání obrázků do galerie
1. Nahrajte obrázky do složky `img/`
2. Přidejte `<img>` tagy do příslušné kategorie v `galerie.html`

## Řešení problémů

### Nefunguje odesílání emailů
1. Zkontrolujte, zda server podporuje `mail()` funkci
2. Ověřte SMTP nastavení serveru
3. Zkontrolujte spam složku

### Nezobrazují se obrázky
1. Zkontrolujte cesty k obrázkům
2. Ověřte, že obrázky existují ve složce `img/`
3. Zkontrolujte oprávnění ke čtení

### Chyby 500
1. Zkontrolujte error log serveru
2. Ověřte syntaxi PHP souborů
3. Zkontrolujte oprávnění souborů

## Kontakt a podpora
Pro technickou podporu nebo otázky kontaktujte vývojáře.

## Verze
- v1.0 - Základní funkčnost
- v1.1 - PHP zpracování formulářů a administrace
- v1.2 - Bezpečnostní vylepšení a optimalizace