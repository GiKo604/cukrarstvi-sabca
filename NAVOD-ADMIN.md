# Návod na nastavení pokročilé administrace

## 🗄️ **Krok 1: Vytvoření databáze**

### **V phpMyAdmin:**
1. Otevřete **http://localhost/phpmyadmin**
2. Klikněte na **"Nová databáze"**
3. Název: `cukrarstvi_sabca`
4. Kódování: `utf8mb4_czech_ci`
5. Klikněte **"Vytvořit"**

### **Import SQL schématu:**
1. Vyberte databázi `cukrarstvi_sabca`
2. Klikněte na záložku **"Importovat"**
3. Vyberte soubor `databaze-schema.sql`
4. Klikněte **"Provést"**

## 🚀 **Krok 2: Přístup k nové administraci**

Otevřete: **http://localhost/cukrarstvi-sabca/admin-new.php**

**Heslo:** `admin123`

## ✨ **Nové funkce administrace:**

### **📄 Dashboard**
- Přehled statistik
- Rychlé akce
- Systémové informace
- Nejnovější obsah

### **📝 Správa stránek**
- ✅ **WYSIWYG editor** (TinyMCE)
- ✅ **Přidávání nových stránek**
- ✅ **Úprava existujících stránek**
- ✅ **Mazání stránek**
- ✅ **SEO nastavení** (meta popisy, klíčová slova)
- ✅ **Správa pořadí v menu**

### **🍰 Správa receptů** (připraveno)
- CRUD operace pro recepty
- Upload obrázků
- Kategorie receptů
- Strukturovaná data (ingredience, postup)

### **🖼️ Správa galerie** (připraveno)
- Nahrávání obrázků
- Kategorizace
- Thumbnail generování
- Batch upload

### **📧 Správa zpráv** (připraveno)
- Databázové uložení kontaktních zpráv
- Označování stavu (nová, přečtená, odpovězená)
- Export do různých formátů

## 🛠️ **Technické detaily:**

### **Databázové tabulky:**
- `stranky` - obsah stránek
- `recepty` - recepty s ingrediencemi
- `kategorie_receptu` - kategorie receptů
- `obrazky_receptu` - obrázky k receptům
- `galerie` - galerie obrázků
- `kontaktni_zpravy` - zprávy z formulářů
- `admin_uzivatele` - uživatelé administrace

### **Upload systém:**
- Automatické vytváření thumbnailů
- Bezpečné názvy souborů
- Omezení typů souborů
- Správa velikosti

### **WYSIWYG Editor:**
- TinyMCE 6
- Český jazyk
- Integrace s CSS webu
- Správa obrázků

## 🔧 **Řešení problémů:**

### **Chyba připojení k databázi:**
1. Zkontrolujte, že XAMPP MySQL běží
2. Ověřte název databáze v `databaze-config.php`
3. Zkontrolujte import SQL souboru

### **WYSIWYG editor se nenačítá:**
1. Zkontrolujte internetové připojení (CDN)
2. Zkuste obnovit stránku
3. Zkontrolujte konzoli v prohlížeči (F12)

### **Upload nefunguje:**
1. Zkontrolujte práva složky `uploads/`
2. Ověřte limity uploadu v `php.ini`
3. Zkontrolujte GD extension pro obrázky

## 📋 **Checklist před použitím:**

- [ ] Databáze `cukrarstvi_sabca` vytvořena
- [ ] SQL schéma importováno
- [ ] Administrace na `admin-new.php` funguje
- [ ] WYSIWYG editor se načítá
- [ ] Upload složky mají správná práva
- [ ] Heslo změněno na bezpečnější

## 🔄 **Migrace ze starého systému:**

Stávající HTML soubory (`domu.html`, `galerie.html` atd.) můžete:
1. **Importovat** do databáze přes administraci
2. **Nechat** jako zálohu
3. **Postupně přenést** obsah do nového systému

Nový systém je **kompatibilní** se stávajícím designem a CSS.

## 🎯 **Další vývoj:**

Po otestování základních funkcí můžeme přidat:
- Pokročilé uživatelské role
- Verzování obsahu
- Pokročilé SEO nástroje
- Integraci s Google Analytics
- Backup systém
- Vícejazyčnost

---

**Tip:** Začněte testováním na dashboardu a postupně vyzkoušejte všechny sekce!