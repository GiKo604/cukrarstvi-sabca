# 📸 Demo obsah - obrázky a data

Tento repozitář obsahuje kompletní demo obsah pro prezentaci funkčnosti CMS systému.

## 🖼️ Obsah uploads složky

**Celková velikost**: ~1.75 MB  
**Počet souborů**: 74

### 📁 uploads/recepty/ 
- Obrázky receptů pro testování
- Formáty: JPG, JPEG
- Ukázkové fotografie jídel a dezertů

### 📁 uploads/galerie/
- Fotografie pro galerii 
- Různé kategorie produktů
- Demonstrační obsah pro admin rozhraní

### 📁 uploads/thumbnails/
- Automaticky generované miniatury (300x200px)
- Optimalizované pro rychlé načítání
- Thumbnaily pro všechny nahrané obrázky

## 🎯 Účel demo obsahu

### ✅ Pro vývojáře:
- Okamžitě funkční systém po instalaci
- Testovací data pro všechny funkce
- Příklady správného formátu obrázků

### ✅ Pro prezentaci:
- Plně funkční admin rozhraní s daty
- Možnost předvedení všech funkcí
- Realistické ukázky výsledného webu

### ✅ Pro testování:
- Ověření upload funkčnosti
- Test thumbnail generování
- Kontrola databázových vazeb

## 🔄 Pro produkční použití

Pokud chcete začít s prázdným systémem:

1. **Smažte obsah uploads složek**:
```bash
# Zachovejte strukturu, smažte obsah
rm uploads/recepty/* uploads/galerie/* uploads/thumbnails/*
```

2. **Resetujte databázi**:
```sql
-- Smažte testovací data
DELETE FROM recepty;
DELETE FROM galerie;
DELETE FROM obrazky_receptu;
DELETE FROM kontaktni_zpravy;
```

3. **Nebo použijte čistou instalaci** z `database/schema.sql`

## ⚠️ Poznámka o velikosti

Demo obrázky jsou optimalizované pro Git:
- Komprimované JPEG soubory
- Rozumná kvalita vs. velikost
- Celková velikost pod 2MB
- Rychlé klonování repozitáře

---

**💡 Tip**: Pro vlastní produkční nasazení nahraďte demo obrázky vlastními a upravte testovací recepty podle svých potřeb.