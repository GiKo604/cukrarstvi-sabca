# Sabčino zázračné cukrářství - Struktura projektu

## 📁 Struktura složek

```
cukrarstvi-sabca/
├── 📁 admin/                    # Administrace
│   ├── admin-new.php           # Hlavní admin rozhraní
│   ├── admin.php              # Starý admin (záloha)
│   └── 📁 admin-sections/      # Admin sekce
│       ├── stranky.php         # Správa stránek
│       ├── recepty.php         # Správa receptů
│       ├── galerie.php         # Správa galerie
│       ├── zpravy.php          # Správa zpráv
│       └── nastaveni.php       # Nastavení systému
│
├── 📁 config/                   # Konfigurace
│   ├── databaze-config.php     # Databázové připojení
│   ├── email-nastaveni.php     # Nastavení emailů
│   └── konfigurace-formulare.php # Konfigurace formulářů
│
├── 📁 includes/                 # Zpracování a funkce
│   └── zpracovani-formulare.php # Zpracování kontaktního formuláře
│
├── 📁 tests/                    # Testovací soubory
│   ├── test-databaze.php       # Test databázového připojení
│   └── test-formular.php       # Test kontaktního formuláře
│
├── 📁 tools/                    # Nástroje a kontroly
│   ├── import-dat.php          # Import testovacích dat
│   ├── kontrola-galerie.php    # Kontrola galerie
│   ├── kontrola-kategorie-receptu.php # Kontrola kategorií
│   ├── vymazat-duplicity.php   # Vymazání duplikátů
│   ├── vymazat-duplicity-kategorie.php
│   └── vymazat-vse-galerie.php
│
├── 📁 css/, js/, img/          # Statické soubory
├── 📁 uploads/                 # Nahrané soubory
├── 📁 zalohy/                  # Zálohy
├── index.html                  # Hlavní stránka
├── kontakt.html               # Kontaktní formulář
├── recepty.html              # Stránka receptů
├── galerie.html              # Stránka galerie
└── databaze-schema.sql       # SQL schéma databáze
```

## 🚀 Přístupové URL

### Hlavní stránky
- **Hlavní stránka:** `http://localhost/cukrarstvi-sabca/`
- **Recepty:** `http://localhost/cukrarstvi-sabca/recepty.html`
- **Galerie:** `http://localhost/cukrarstvi-sabca/galerie.html`
- **Kontakt:** `http://localhost/cukrarstvi-sabca/kontakt.html`

### Administrace
- **Admin rozhraní:** `http://localhost/cukrarstvi-sabca/admin/admin-new.php`
- **Starý admin:** `http://localhost/cukrarstvi-sabca/admin/admin.php`

### Konfigurace
- **Email nastavení:** `http://localhost/cukrarstvi-sabca/config/email-nastaveni.php`

### Testování
- **Test databáze:** `http://localhost/cukrarstvi-sabca/tests/test-databaze.php`
- **Test formuláře:** `http://localhost/cukrarstvi-sabca/tests/test-formular.php`

### Nástroje
- **Import dat:** `http://localhost/cukrarstvi-sabca/tools/import-dat.php`
- **Kontrola galerie:** `http://localhost/cukrarstvi-sabca/tools/kontrola-galerie.php`
- **Kontrola kategorií:** `http://localhost/cukrarstvi-sabca/tools/kontrola-kategorie-receptu.php`
- **Vymazání duplikátů:** `http://localhost/cukrarstvi-sabca/tools/vymazat-duplicity.php`

## 📋 Změny v cestách

Všechny cesty byly automaticky aktualizovány:
- ✅ Kontaktní formulář → `includes/zpracovani-formulare.php`
- ✅ Admin sekce → `../config/` pro konfiguraci
- ✅ Všechny tools a tests → `../config/databaze-config.php`
- ✅ Odkazy v admin rozhraní aktualizovány

## 🎯 Výhody nové struktury

1. **Přehlednost** - Logické rozdělení podle funkcí
2. **Bezpečnost** - Admin soubory odděleně
3. **Údržba** - Snadné nalezení souborů
4. **Rozšiřitelnost** - Připraveno na růst projektu
5. **Profesionalita** - Standardní struktura PHP projektů