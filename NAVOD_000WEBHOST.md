# 🚀 000webhost - Nejjednodušší způsob nasazení

## 🌟 **Proč 000webhost?**
- ✅ **Zdarma navždy** - žádné platby
- ✅ **Drag & Drop** - prostě přetáhnete soubory  
- ✅ **Žádné úpravy kódu** - funguje s XAMPP nastavením
- ✅ **1 GB místa** - stačí pro váš projekt
- ✅ **Rychlý setup** - 10 minut celkem

---

## 🎯 **Krok za krokem (10 minut):**

### **KROK 1: Registrace (2 minuty)** 📝
1. **Otevřete:** https://www.000webhost.com/
2. **Klikněte:** "Free Hosting" → "Claim Your Free Website"
3. **Vyplňte:**
   - **Website Name:** `cukrarstvi-sabca` (bude: cukrarstvi-sabca.000webhostapp.com)
   - **Email:** váš email
   - **Password:** silné heslo
4. **Klikněte:** "Claim Now"
5. **Ověřte email** (zkontrolujte spam)

---

### **KROK 2: Upload souborů (3 minuty)** 📁

#### **A) Otevření File Manager:**
1. **Přihlaste se** na 000webhost
2. **Klikněte:** "Manage Website"
3. **Klikněte:** "File Manager"

#### **B) Upload všech souborů:**
1. **Otevřete:** složku `public_html`
2. **Vyberte všechny soubory** z `C:\xampp\htdocs\cukrarstvi-sabca\`
3. **Přetáhněte je** do File Manager
4. **Počkejte** na dokončení uploadu

> 💡 **Tip:** Můžete uploadovat i ZIP a rozbalit ho přímo v File Manager

---

### **KROK 3: Vytvoření databáze (3 minuty)** 🗄️

#### **A) Přístup k databázím:**
1. **Zpět na dashboard** (Website → Dashboard)
2. **Klikněte:** "Manage Database"
3. **Klikněte:** "New Database"

#### **B) Vytvoření databáze:**
1. **Database name:** `cukrarstvi_sabca`
2. **Database user:** `admin` 
3. **Password:** váš choice (zapamatujte si!)
4. **Klikněte:** "Create Database"

#### **C) Import SQL dat:**
1. **Klikněte:** "Manage" u vaší databáze
2. **Otevře se phpMyAdmin**
3. **Záložka "Import"**
4. **Choose file:** vyberte `database/cukrarstvi_sabca.sql`
5. **Klikněte:** "Import"

---

### **KROK 4: Úprava konfigurace (2 minuty)** ⚙️

**Upravte jediný soubor:** `config/databaze-config.php`

```php
// ZMĚŇTE POUZE TYTO ŘÁDKY:
define('DB_HOST', 'localhost');        // ← zůstává stejné!
define('DB_NAME', 'id21234567_cukrarstvi_sabca');  // ← váš DB název
define('DB_USER', 'id21234567_admin');              // ← váš DB user
define('DB_PASS', 'vaše_heslo_z_kroku3');           // ← heslo z kroku 3
```

> 📝 **Najdete přesné údaje** v Database → Manage → Connection Details

---

### **KROK 5: Testování (1 minuta)** ✅

#### **Frontend:**
🌐 **https://cukrarstvi-sabca.000webhostapp.com/**

#### **Admin:**
🔐 **https://cukrarstvi-sabca.000webhostapp.com/admin/admin-new.php**
- **Login:** `admin`
- **Heslo:** `admin123`

---

## 🎉 **HOTOVO! Web je online!**

### **📧 Pošlete komukoliv:**
**"Podívej se na můj web: https://cukrarstvi-sabca.000webhostapp.com"**

### **⚡ Co máte:**
- ✅ **Živá stránka** na internetu
- ✅ **74 demo obrázků** 
- ✅ **Funkční admin** 
- ✅ **Responzivní design**
- ✅ **Zdarma navždy**

---

## 🛠️ **Řešení problémů:**

### **"Database connection failed"**
- Zkontrolujte údaje v `config/databaze-config.php`
- Zkopírujte přesné názvy z Database → Connection Details

### **"Page not found"**
- Ověřte, že jste nahráli soubory do `public_html/`
- Zkontrolujte, že existuje `index.html`

### **Nevidím obrázky**
- Zkontrolujte, že jste nahráli složku `uploads/`
- Vyčkejte 5-10 minut na propagaci

---

## 🚀 **Pro pokročilé:**

### **Vlastní doména (volitelné):**
1. **Kupte doménu** (např. GoDaddy, Wedos)
2. **DNS nastavení:** směřujte na 000webhost servery
3. **Add Domain** v 000webhost panelu

### **SSL certifikát:**
- **Automaticky se aktivuje** za 24 hodin
- **https://** bude fungovat automaticky

---

**🎯 Za 10 minut máte živý web který vidí celý svět! 🌟**