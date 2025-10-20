# ⚡ Rychlý start pro Git upload

Pokud už máte nainstalovaný Git, stačí spustit v PowerShell:

```powershell
# 1. Přejděte do složky projektu
cd C:\xampp\htdocs\cukrarstvi-sabca

# 2. Vytvořte GitHub repozitář na github.com (prázdný!)

# 3. Spusťte tyto příkazy (nahraďte YOUR_USERNAME svým GitHub jménem):
git init
git add .
git commit -m "🎉 CMS pro cukrářství - kompletní systém"
git remote add origin https://github.com/YOUR_USERNAME/cukrarstvi-sabca.git
git branch -M main
git push -u origin main
```

## ⚠️ DŮLEŽITÉ před nahráním!

1. **Změňte admin heslo** v `admin/admin-new.php` (řádek ~25):
```php
$spravne_heslo = "NOVÉ_BEZPEČNÉ_HESLO";  // Změňte admin123!
```

2. **Zkontrolujte databázové připojení** v `config/databaze-config.php`

## 🎯 Co se nahraje:
✅ Kompletní admin systém  
✅ Databázové schéma  
✅ Upload systém s thumbnaily  
✅ **74 demo obrázků (~1.75 MB)** pro okamžitou funkčnost  
✅ Responzivní design  
✅ Dokumentace  

## 🚫 Co se NENAHRAJE (díky .gitignore):
❌ Debug soubory  
❌ Logy  
❌ Velmi velké dočasné soubory  
❌ IDE konfigurace  

---

**🆘 Problém s Git?** Podívejte se na `GIT_NAVOD.md` pro detailní instrukce.