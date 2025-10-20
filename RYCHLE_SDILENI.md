# ⚡ Rychlé sdílení bez hostingu

Pokud nechcete řešit hosting, můžete **okamžitě sdílet** lokální web!

## 🚀 **Možnost 1: ngrok (nejjednodušší)**

### **Postup:**
1. **Stáhněte:** https://ngrok.com/download
2. **Spusťte XAMPP** (Apache + MySQL)
3. **Otevřete terminál** v složce s ngrok
4. **Spusťte:**
   ```bash
   ngrok http 80
   ```
5. **Zkopírujte URL:** např. `https://abc123.ngrok-free.app`

### **Pošlete odkaz:**
**"Koukni na můj web: https://abc123.ngrok-free.app/cukrarstvi-sabca"**

---

## 🌐 **Možnost 2: Cloudflare Tunnel (zdarma)**

### **Postup:**
1. **Registrace na Cloudflare.com** (zdarma)
2. **Stažení cloudflared:** https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/
3. **Spuštění:**
   ```bash
   cloudflared tunnel --url http://localhost/cukrarstvi-sabca
   ```
4. **Dostanete trvalou URL** (funguje i po restartu)

---

## 📱 **Možnost 3: Serveo (bez instalace)**

### **Postup:**
1. **Spusťte v terminálu:**
   ```bash
   ssh -R 80:localhost:80 serveo.net
   ```
2. **Dostanete URL:** např. `https://random.serveo.net`
3. **Pošlete:** `https://random.serveo.net/cukrarstvi-sabca`

---

## ✅ **Výhody rychlého sdílení:**
- ⚡ **Okamžité** - za 2 minuty máte odkaz
- 🆓 **Zdarma** - žádné platby
- 🔧 **Žádné nastavení** - funguje s XAMPP
- 🌐 **Skutečná URL** - pošlete komukoliv

## ⚠️ **Nevýhody:**
- 💻 **Počítač musí běžet** - když ho vypnete, web nefunguje
- 🔗 **URL se mění** - při každém spuštění nová adresa
- 🌍 **Pomalejší** - tunnel přes internet

---

**🎯 Pro rychlé ukázání = ngrok, pro trvalý web = 000webhost!**