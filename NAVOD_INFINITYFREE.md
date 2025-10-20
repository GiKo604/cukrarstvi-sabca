# 🌐 Nasazení na InfinityFree - Krok za krokem

## 🚀 **Jak nahrát web na InfinityFree.net**

### **KROK 1: Registrace na InfinityFree** 📝
1. Jděte na **https://www.infinityfree.net/**
2. Klikněte **"Sign Up"** (Registrace)
3. Vyplňte:
   - **Email:** váš email
   - **Password:** silné heslo
   - **Confirm Password:** znovu heslo
4. Klikněte **"Create Account"**
5. **Ověřte email** (zkontrolujte schránku + spam)

---

### **KROK 2: Vytvoření nového webu** 🌐
1. Po přihlášení klikněte **"Create Account"** (Vytvořit účet)
2. Vyberte **subdoménu**:
   - Napište: `cukrarstvi-sabca` nebo `sabcacakes`
   - Vyberte doménu: `.epizy.com`
   - **Výsledek:** `cukrarstvi-sabca.epizy.com`
3. Klikněte **"Create"**

---

### **KROK 3: Příprava souborů pro upload** 📁

Před nahráním upravíme konfiguraci databáze:

#### **A) Upravení databázové konfigurace:**
1. **Otevřete:** `config/databaze-config.php`
2. **Změňte tyto řádky:**
   ```php
   // PŮVODNÍ (pro lokální XAMPP):
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cukrarstvi_sabca');
   define('DB_USER', 'root');
   define('DB_PASS', '');

   // NOVÉ (pro InfinityFree):
   define('DB_HOST', 'sql200.epizy.com');     // Dostanete v Control Panel
   define('DB_NAME', 'epiz_xxxxx_databaze');  // Dostanete v Control Panel  
   define('DB_USER', 'epiz_xxxxx');           // Dostanete v Control Panel
   define('DB_PASS', 'vaše_heslo');           // Nastavíte si sami
   ```

#### **B) Vytvoření ZIP souboru:**
1. **Vyberte všechny soubory** kromě:
   - `.git/` složky
   - `*.log` souborů
2. **Vytvořte ZIP:** `cukrarstvi-sabca.zip`

---

### **KROK 4: Nahrání souborů** 📤

#### **A) Přístup k File Manager:**
1. V InfinityFree **Control Panel** klikněte **"File Manager"**
2. **Přihlaste se** (stejné údaje jako účet)
3. Otevřete složku **"htdocs"**

#### **B) Upload souborů:**
1. Klikněte **"Upload"** 
2. **Vyberte váš ZIP** soubor
3. **Počkejte na upload** (může trvat 5-10 minut)
4. **Rozbalte ZIP** - klikněte pravým tlačítkem → Extract

---

### **KROK 5: Vytvoření MySQL databáze** 🗄️

#### **A) Vytvoření databáze:**
1. V Control Panel klikněte **"MySQL Databases"**
2. **Database Name:** napište `databaze` (bude: `epiz_xxxxx_databaze`)
3. Klikněte **"Create Database"**

#### **B) Vytvoření uživatele:**
1. **Username:** napište `admin` (bude: `epiz_xxxxx_admin`)
2. **Password:** vytvořte silné heslo
3. Klikněte **"Create User"**

#### **C) Přiřazení oprávnění:**
1. **Vyberte databázi** a **uživatele**
2. **Zaškrtněte "All Privileges"**
3. Klikněte **"Add User to Database"**

---

### **KROK 6: Aktualizace konfigurace** ⚙️

Nyní **aktualizujte** `config/databaze-config.php` se správnými údaji:

```php
define('DB_HOST', 'sql200.epizy.com');           // Z MySQL Databases
define('DB_NAME', 'epiz_xxxxx_databaze');        // Váš název databáze
define('DB_USER', 'epiz_xxxxx_admin');           // Váš uživatel
define('DB_PASS', 'vaše_heslo_z_kroku5');       // Heslo z kroku 5
```

---

### **KROK 7: Instalace databáze** 🔧

1. **Jděte na:** `https://cukrarstvi-sabca.epizy.com/install.php`
2. **Automaticky se vytvoří tabulky** a naimportují demo data
3. **Sledujte postup** na obrazovce

---

### **KROK 8: Testování webu** ✅

#### **Frontend:**
📧 **https://cukrarstvi-sabca.epizy.com/**

#### **Admin:**
🔐 **https://cukrarstvi-sabca.epizy.com/admin/admin-new.php**
- **Login:** admin
- **Heslo:** admin123

---

## 🎉 **HOTOVO! Váš web je online!**

### **📧 Co pošlete kamarádce:**
**"Koukni na můj nový web: https://cukrarstvi-sabca.epizy.com"**

### **⚡ Výhody:**
- ✅ **Živá stránka** - funguje okamžitě
- ✅ **74 demo obrázků** - plně funkční galerie
- ✅ **Admin rozhraní** - můžete spravovat obsah
- ✅ **Zdarma navždy** - žádné platby
- ✅ **Rychlý odkaz** - pošlete komukoliv

---

## 🛠️ **Řešení problémů:**

### **Nefunguje databáze?**
- Zkontrolujte údaje v `config/databaze-config.php`
- Ověřte, že jste vytvořili databázi správně

### **Nevidím obrázky?**
- Zkontrolujte, že jste nahráli složku `uploads/`
- Ověřte oprávnění souborů

### **Nelze se přihlásit?**
- Zkuste admin / admin123
- Zkontrolujte, že proběhla instalace databáze

---

**🎯 Za 30 minut budete mít živý web který můžete ukázat celému světu! 🌟**