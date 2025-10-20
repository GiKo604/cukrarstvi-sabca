# 🧁 CMS Systém pro Cukrářství

Kompletní admin systém pro správu webových stránek cukrářství s recepty, galerií a kontaktními informacemi.

## 🚀 **Rychlé spuštění (5 minut)**

### **Požadavky:**
- **XAMPP** (Apache + MySQL + PHP)
- **Webový prohlížeč**

---

## 📥 **1. Stažení a instalace**

### **A) Stažení z GitHub:**
```bash
git clone https://github.com/GiKo604/cukrarstvi-sabca.git
```

### **B) Nebo stažení ZIP:**
1. Klikněte **"Code" → "Download ZIP"** na GitHub
2. Rozbalte do `C:\xampp\htdocs\cukrarstvi-sabca\`

---

## 🗄️ **2. Nastavení databáze**

### **Spuštění XAMPP:**
1. Spusťte **XAMPP Control Panel**
2. Zapněte **Apache** a **MySQL**

### **Vytvoření databáze:**
1. Otevřete http://localhost/phpmyadmin/
2. Klikněte **"New"** (Nová)
3. Název databáze: **`cukrarstvi_sabca`**
4. Klikněte **"Create"**

### **Import dat:**
1. Vyberte databázi `cukrarstvi_sabca`
2. Klikněte záložku **"Import"**
3. Vyberte soubor: `database/cukrarstvi_sabca.sql`
4. Klikněte **"Import"** (Go)

---

## 🌐 **3. Spuštění webu**

### **Frontend (návštěvníci):**
📧 **http://localhost/cukrarstvi-sabca/**

### **Admin rozhraní:**
🔐 **http://localhost/cukrarstvi-sabca/admin/admin-new.php**

**Přihlašovací údaje:**
- **Uživatel:** `admin`  
- **Heslo:** `admin123`

> ⚠️ **DŮLEŽITÉ:** Změňte heslo po prvním přihlášení!

---

## ✨ **Co systém umí**

### **� Pro návštěvníky:**
- 🏠 **Hlavní stránka** s prezentací
- 🍰 **Recepty** s obrázky a postupy  
- 🖼️ **Galerie** s fotografiemi výrobků
- 📞 **Kontakty** s formulářem

### **🔧 Admin funkce:**
- ✅ **Správa stránek** (editace obsahu)
- 📝 **Správa receptů** (přidání/úprava/smazání)
- 🖼️ **Správa galerie** (nahrávání obrázků)
- 🗂️ **Automatické thumbnail** generování
- 📧 **Správa kontaktů** a zpráv

---

## 🛠️ **Řešení problémů**

### **Nevidím obrázky:**
- Zkontrolujte oprávnění složky `uploads/` (775)
- Ověřte, že Apache běží

### **Chyba připojení k databázi:**
- Zkontrolujte běh MySQL v XAMPP
- Ověřte nastavení v `config/databaze-config.php`

### **Nelze se přihlásit do adminu:**
- Uživatel: `admin`, heslo: `admin123`
- Zkontrolujte import databáze

---

## 📁 **Struktura projektu**

```
cukrarstvi-sabca/
├── 📄 index.html              # Hlavní stránka
├── 🔐 admin/                  # Admin rozhraní
├── ⚙️ config/                # Konfigurace databáze
├── 📸 uploads/               # Nahrané obrázky (74 demo)
├── 🗄️ database/             # SQL soubory
├── 🧩 includes/             # PHP komponenty
└── 📖 README.md             # Tento návod
```

---

## Detailní popis funkcí

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