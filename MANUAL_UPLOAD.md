# 📦 Ruční upload na GitHub bez Git

Pokud nemůžete nainstalovat Git, můžete projekt nahrát ručně přes webové rozhraní GitHub.

## 🚀 Postup pro ruční upload:

### 1. **Vytvoření GitHub repozitáře**
1. Jděte na **GitHub.com** a přihlaste se
2. Klikněte **"New repository"** (zelené tlačítko)
3. Název: `cukrarstvi-sabca`
4. Popis: `Admin CMS pro cukrářství s PHP a MySQL`
5. Vyberte **"Public"** nebo **"Private"** podle potřeby
6. **NEZAŠKRTÁVEJTE** "Add a README file"
7. Klikněte **"Create repository"**

### 2. **Příprava souborů**
Před nahráním:

1. **Změňte admin heslo** v `admin/admin-new.php`:
   ```php
   // Najděte řádek cca 25 a změňte:
   $spravne_heslo = "VAŠE_NOVÉ_HESLO"; // místo admin123
   ```

2. **Zkontrolujte databázové připojení** v `config/databaze-config.php`

### 3. **Vytvoření ZIP archivu**
1. Vyberte všechny soubory v `C:\xampp\htdocs\cukrarstvi-sabca\`
2. **KROMĚ těchto složek/souborů:**
   - `.git/` (pokud existuje)
   - `*.log` soubory
   - `temp/` složky
3. Vytvořte ZIP archiv s názvem `cukrarstvi-sabca.zip`

### 4. **Upload na GitHub**
1. V nově vytvořeném repozitáři klikněte **"uploading an existing file"**
2. Přetáhněte váš ZIP soubor nebo klikněte **"choose your files"**
3. GitHub automaticky rozbalí ZIP
4. Do pole "Commit changes" napište:
   ```
   🎉 Inicializace CMS systému pro cukrářství

   ✨ Kompletní admin systém s demo obsahem
   - Admin rozhraní s autentifikací  
   - Správa stránek, receptů, galerie
   - 74 demo obrázků (~1.75 MB)
   - Automatické thumbnail generování
   - Responzivní design
   ```
5. Klikněte **"Commit changes"**

## 📋 Co se nahraje:
- ✅ Kompletní PHP/MySQL CMS systém
- ✅ Admin rozhraní se všemi funkcemi
- ✅ 74 demo obrázků a thumbnailů
- ✅ SQL databázové schéma
- ✅ Kompletní dokumentace

## 🔄 Pro budoucí změny:
Můžete používat GitHub webové rozhraní:
1. Klikněte na soubor který chcete upravit
2. Klikněte ikonu tužky (Edit)
3. Proveďte změny
4. Napište popis změny
5. Klikněte "Commit changes"

## 💡 Doporučení:
- Po nahrání si nainstalujte Git pro pohodlnější práci
- Použijte GitHub Desktop pro grafické rozhraní
- Nebo Visual Studio Code s Git rozšířením

---

**🎯 Výsledek:** Váš projekt bude na GitHub dostupný a funkční, i bez lokální instalace Git!