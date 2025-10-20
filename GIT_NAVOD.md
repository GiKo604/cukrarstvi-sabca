# 🚀 Jak nahrát projekt na Git - Kompletní návod

## 1. 📥 Instalace Git

### Windows:
1. Stáhněte Git z: https://git-scm.com/download/win
2. Spusťte installer a postupujte podle pokynů
3. V PowerShell zkontrolujte: `git --version`

### Alternativa - Git přes Chocolatey:
```powershell
# Pokud máte Chocolatey
choco install git

# Nebo přes Winget
winget install Git.Git
```

## 2. ⚙️ Konfigurace Git (pouze při první instalaci)

```bash
git config --global user.name "Vaše Jméno"
git config --global user.email "vas@email.com"
```

## 3. 🏗️ Vytvoření GitHub repozitáře

1. Jděte na **GitHub.com**
2. Klikněte **"New repository"**
3. Název: `cukrarstvi-sabca`
4. Popis: `Admin CMS pro cukrářství s PHP a MySQL`
5. **Ponechte prázdné** (nezaškrtávejte README, .gitignore, license)
6. Klikněte **"Create repository"**

## 4. 📤 Inicializace a nahrání projektu

Otevřte PowerShell v složce projektu a spusťte:

```powershell
# Přejděte do složky projektu
cd C:\xampp\htdocs\cukrarstvi-sabca

# Inicializace Git repozitáře
git init

# Přidání všech souborů (respektuje .gitignore)
git add .

# První commit
git commit -m "🎉 Inicializace CMS systému pro cukrářství

✨ Funkce:
- Admin rozhraní s autentifikací
- Správa stránek s WYSIWYG editorem
- Správa receptů s upload obrázků
- Správa galerie s batch upload
- Správa kontaktních zpráv
- Automatické thumbnail generování
- Responzivní design
- SQL databázové schéma"

# Připojení k GitHub repozitáři (nahraďte YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/cukrarstvi-sabca.git

# Nastavení hlavní větve
git branch -M main

# Nahrání na GitHub
git push -u origin main
```

## 5. 🔒 Zabezpečení citlivých dat

### Důležité! Před nahráním změňte:

1. **Databázové heslo** v `config/databaze-config.php`:
```php
// ZMĚŇTE pro produkci!
define('DB_PASS', 'silne_heslo_123');
```

2. **Admin heslo** v `admin/admin-new.php`:
```php
// ZMĚŇTE výchozí heslo!
$spravne_heslo = 'nove_admin_heslo_456';
```

### Pro produkční nasazení vytvořte:
```php
// config/production.php
<?php
define('DB_HOST', 'your-server.com');
define('DB_NAME', 'production_db');
define('DB_USER', 'production_user');
define('DB_PASS', 'super_secure_password');
?>
```

## 6. 📋 Checklist před nahráním

- [ ] ✅ Změněno admin heslo
- [ ] ✅ Změněno DB heslo pro produkci
- [ ] ✅ Zkontrolován .gitignore
- [ ] ✅ Odstraněny debug soubory
- [ ] ✅ Zkontrolována funkčnost admin rozhraní
- [ ] ✅ Otestovány všechny sekce
- [ ] ✅ Ověřena databázová struktura

## 7. 🔄 Pravidelné aktualizace

Pro další změny:
```powershell
# Přidání změn
git add .

# Commit s popisem
git commit -m "🔧 Oprava zobrazování tlačítek v admin rozhraní"

# Nahrání na GitHub
git push
```

## 8. 🌐 Nasazení na webhosting

### Příprava souborů:
1. Stáhněte projekt z GitHub
2. Nahrajte přes FTP na webhosting
3. Importujte `database/schema.sql` do MySQL
4. Upravte `config/databaze-config.php` pro produkci
5. Nastavte oprávnění složek `uploads/` na 755

### Rychlé nasazení:
```bash
# Na serveru
git clone https://github.com/YOUR_USERNAME/cukrarstvi-sabca.git
cd cukrarstvi-sabca
chmod 755 uploads/ uploads/*/ 
```

## 9. 🛠️ Tipy pro správu

### Zálohování:
```bash
# Vytvoření větve pro zálohu
git checkout -b backup-$(date +%Y%m%d)
git push origin backup-$(date +%Y%m%d)
```

### Verzování:
```bash
# Tag pro vydání verze
git tag -a v1.0 -m "První stabilní verze"
git push origin v1.0
```

## 10. 🆘 Řešení problémů

### Git není rozpoznán:
- Restartujte PowerShell po instalaci Git
- Přidejte Git do PATH: `C:\Program Files\Git\bin`

### Chyba autentifikace:
- Použijte Personal Access Token místo hesla
- GitHub → Settings → Developer settings → Personal access tokens

### Velké soubory:
```bash
# Pokud jsou soubory >100MB, použijte Git LFS
git lfs track "*.jpg"
git lfs track "*.png"
```

---

**🎯 Po dokončení máte:**
- ✅ Verzovaný kód na GitHub
- ✅ Bezpečné uložení bez citlivých dat  
- ✅ Připravenost k nasazení
- ✅ Možnost spolupráce s týmem