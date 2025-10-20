<?php
// Import existujících obrázků a receptů do databáze

require_once '../config/databaze-config.php';

echo "<h2>Import obrázků a receptů do databáze</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Databáze připojena</p>";
    
    // Definice obrázků z galerie.html podle kategorií
    $galerie_obrazky = [
        'dorty-torty' => [
            ['nazev_souboru' => 'IMG_0032.jpeg', 'nazev' => 'Čokoládový dort', 'alt_text' => 'Čokoládový dort'],
            ['nazev_souboru' => 'IMG_0039.jpeg', 'nazev' => 'Svatební dort', 'alt_text' => 'Svatební dort'],
            ['nazev_souboru' => 'IMG_0040.jpeg', 'nazev' => 'Narozeninový dort', 'alt_text' => 'Narozeninový dort'],
            ['nazev_souboru' => 'IMG_0041.jpeg', 'nazev' => 'Krémový dort', 'alt_text' => 'Krémový dort'],
            ['nazev_souboru' => 'IMG_0216.jpeg', 'nazev' => 'Ovocný dort', 'alt_text' => 'Ovocný dort'],
            ['nazev_souboru' => 'IMG_0219.jpeg', 'nazev' => 'Zdobený dort', 'alt_text' => 'Zdobený dort'],
            ['nazev_souboru' => 'IMG_0222.jpeg', 'nazev' => 'Vícepatrový dort', 'alt_text' => 'Vícepatrový dort'],
            ['nazev_souboru' => 'IMG_0250.jpeg', 'nazev' => 'Vanilkový dort', 'alt_text' => 'Vanilkový dort'],
        ],
        'cupcakes-muffiny' => [
            ['nazev_souboru' => 'IMG_0323.jpeg', 'nazev' => 'Vanilkové cupcakes', 'alt_text' => 'Vanilkové cupcakes'],
            ['nazev_souboru' => 'IMG_0325.jpeg', 'nazev' => 'Čokoládové cupcakes', 'alt_text' => 'Čokoládové cupcakes'],
            ['nazev_souboru' => 'IMG_0326.jpeg', 'nazev' => 'Zdobené cupcakes', 'alt_text' => 'Zdobené cupcakes'],
            ['nazev_souboru' => 'IMG_0327.jpeg', 'nazev' => 'Ovocné muffiny', 'alt_text' => 'Ovocné muffiny'],
            ['nazev_souboru' => 'IMG_0328.jpeg', 'nazev' => 'Sezónní cupcakes', 'alt_text' => 'Sezónní cupcakes'],
            ['nazev_souboru' => 'IMG_0332.jpeg', 'nazev' => 'Mini cupcakes', 'alt_text' => 'Mini cupcakes'],
        ],
        'susenky-cukrovi' => [
            ['nazev_souboru' => 'IMG_0334.jpeg', 'nazev' => 'Vánoční cukroví', 'alt_text' => 'Vánoční cukroví'],
            ['nazev_souboru' => 'IMG_0335.jpeg', 'nazev' => 'Linecké sušenky', 'alt_text' => 'Linecké sušenky'],
            ['nazev_souboru' => 'IMG_0337.jpeg', 'nazev' => 'Makronky', 'alt_text' => 'Makronky'],
            ['nazev_souboru' => 'IMG_0338.jpeg', 'nazev' => 'Perníčky', 'alt_text' => 'Perníčky'],
            ['nazev_souboru' => 'IMG_0346.jpeg', 'nazev' => 'Oříškové cukroví', 'alt_text' => 'Oříškové cukroví'],
            ['nazev_souboru' => 'IMG_0348.jpeg', 'nazev' => 'Zdobené sušenky', 'alt_text' => 'Zdobené sušenky'],
        ],
        'dezerty-speciality' => [
            ['nazev_souboru' => 'IMG_0488.jpeg', 'nazev' => 'Tiramisu', 'alt_text' => 'Tiramisu'],
            ['nazev_souboru' => 'IMG_0489.jpeg', 'nazev' => 'Cheesecake', 'alt_text' => 'Cheesecake'],
            ['nazev_souboru' => 'IMG_0490.jpeg', 'nazev' => 'Crème brûlée', 'alt_text' => 'Crème brûlée'],
            ['nazev_souboru' => 'IMG_0491.jpeg', 'nazev' => 'Panna cotta', 'alt_text' => 'Panna cotta'],
            ['nazev_souboru' => 'IMG_0505.jpeg', 'nazev' => 'Čokoládový mousse', 'alt_text' => 'Čokoládový mousse'],
        ],
    ];
    
    // Import obrázků do galerie
    echo "<h3>Import obrázků do galerie...</h3>";
    $galerie_pocet = 0;
    
    foreach ($galerie_obrazky as $kategorie => $obrazky) {
        foreach ($obrazky as $obrazek) {
            // Kontrola, jestli obrázek už neexistuje
            $existuje = $db->fetchOne("SELECT id FROM galerie WHERE nazev_souboru = :nazev_souboru", [
                'nazev_souboru' => $obrazek['nazev_souboru']
            ]);
            
            if (!$existuje) {
                $db->insert('galerie', [
                    'nazev_souboru' => $obrazek['nazev_souboru'],
                    'nazev' => $obrazek['nazev'],
                    'alt_text' => $obrazek['alt_text'],
                    'kategorie' => $kategorie,
                    'popis' => 'Importováno z galerie.html',
                    'datum_nahrany' => date('Y-m-d H:i:s'),
                    'aktivni' => 1
                ]);
                $galerie_pocet++;
                echo "<p>✓ Přidán: {$obrazek['nazev']}</p>";
            } else {
                echo "<p>- Už existuje: {$obrazek['nazev']}</p>";
            }
        }
    }
    
    // Definice receptů z recepty.html
    $recepty_data = [
        [
            'slug' => 'cokoladovy-dort',
            'nazev' => 'Čokoládový dort',
            'popis' => 'Luxusní čokoládový dort s krémovou polevou',
            'ingredience' => "200g tmavé čokolády\n150g másla\n150g cukru\n3 vejce\n100g mouky\n250ml smetany na šlehání",
            'postup' => "1. Rozehřejte troubu na 180°C. Vymažte a vysypte formu na dort.\n2. Rozpusťte čokoládu s máslem ve vodní lázni.\n3. Ušlehejte vejce s cukrem do pěny, vmíchejte čokoládu a mouku.\n4. Pečte 35-40 minut. Nechte vychladnout.\n5. Ušlehejte smetanu a dort s ní potřete.",
            'cas_pripravy' => 60,
            'pocet_porci' => 8,
            'hlavni_obrazek' => 'first.jpeg',
            'kategorie_id' => 1, // Dorty
        ],
        [
            'slug' => 'vanilkove-cupcakes',
            'nazev' => 'Vanilkové cupcakes',
            'popis' => 'Jemné vanilkové cupcakes s krémovou polevou',
            'ingredience' => "200g mouky\n150g cukru\n100g másla\n2 vejce\n150ml mléka\n1 lžička vanilkového extraktu\n1 prášek do pečiva",
            'postup' => "1. Rozehřejte troubu na 175°C. Připravte formičky na muffiny.\n2. Ušlehejte máslo s cukrem do pěny.\n3. Postupně přidejte vejce a vanilkový extrakt.\n4. Vmíchejte střídavě mouku s práškem a mléko.\n5. Pečte 18-20 minut do zlatova.",
            'cas_pripravy' => 30,
            'pocet_porci' => 12,
            'hlavni_obrazek' => 'sec.jpeg',
            'kategorie_id' => 2, // Cupcakes
        ],
        [
            'slug' => 'jahody-krem',
            'nazev' => 'Jahody s krémem',
            'popis' => 'Osvěžující dezert s čerstvými jahodami',
            'ingredience' => "500g čerstvých jahod\n300ml smetany na šlehání\n3 lžíce moučkového cukru\n1 lžička vanilkového extraktu\nMátové lístky na ozdobu",
            'postup' => "1. Jahody omyjte a odřízněte stopky.\n2. Ušlehejte smetanu s cukrem a vanilkou do tuhé pěny.\n3. Jahody nakrájejte na poloviny.\n4. Servírujte v pohárech - střídejte jahody a krém.\n5. Ozdobte mátovými lístky.",
            'cas_pripravy' => 15,
            'pocet_porci' => 4,
            'hlavni_obrazek' => 'thi.jpeg',
            'kategorie_id' => 4, // Dezerty
        ],
        [
            'slug' => 'makronky',
            'nazev' => 'Makronky',
            'popis' => 'Francouzské makronky s ganache náplní',
            'ingredience' => "100g mletých mandlí\n200g moučkového cukru\n75g bílků (cca 2-3 vejce)\n25g cukru krupice\nPotravinářské barvivo\nGanache na plnění",
            'postup' => "1. Prosejte mandle s moučkovým cukrem.\n2. Ušlehejte bílky s krupicí do tuhého sněhu.\n3. Opatrně vmíchejte mandlovou směs a barvivo.\n4. Vytlačte na plech a nechte 30 min odležet.\n5. Pečte při 150°C 12-15 minut.\n6. Po vychladnutí slepte ganache.",
            'cas_pripravy' => 90,
            'pocet_porci' => 20,
            'hlavni_obrazek' => 'fou.jpeg',
            'kategorie_id' => 3, // Sušenky
        ],
        [
            'slug' => 'tiramisu',
            'nazev' => 'Tiramisu',
            'popis' => 'Klasické italské tiramisu s mascarpone',
            'ingredience' => "500g mascarpone\n4 vejce (rozdělené)\n100g cukru\n300ml silné kávy\n3 lžíce amaretta\n200g piškotů savoiardi\nKakao na posypání",
            'postup' => "1. Ušlehejte žloutky s cukrem do pěny.\n2. Vmíchejte mascarpone.\n3. Ušlehejte bílky na sníh a vmíchejte do krému.\n4. Smíchejte kávu s amarettem.\n5. Namočte piškoty a vrstvěte s krémem.\n6. Nechte vychladit přes noc a posypte kakaem.",
            'cas_pripravy' => 30,
            'pocet_porci' => 8,
            'hlavni_obrazek' => 'five.jpeg',
            'kategorie_id' => 4, // Dezerty
        ],
        [
            'slug' => 'cheesecake',
            'nazev' => 'Cheesecake',
            'popis' => 'Americký cheesecake s tvarohem a citronem',
            'ingredience' => "200g digestive sušenek\n100g másla\n600g tvarohu\n200g cukru\n3 vejce\n250ml smetany\nKůra z 1 citronu",
            'postup' => "1. Rozdrobte sušenky a smíchejte s rozpuštěným máslem.\n2. Vyložte dno formy a uhněte.\n3. Smíchejte tvaroh, cukr, vejce, smetanu a citronovou kůru.\n4. Nalijte na korpus a pečte při 160°C 50 minut.\n5. Nechte vychladnout v troubě s pootevřenými dvířky.",
            'cas_pripravy' => 75,
            'pocet_porci' => 10,
            'hlavni_obrazek' => 'six.jpeg',
            'kategorie_id' => 4, // Dezerty
        ],
        [
            'slug' => 'brownies',
            'nazev' => 'Brownies',
            'popis' => 'Čokoládové brownies s vlašskými ořechy',
            'ingredience' => "200g tmavé čokolády\n200g másla\n250g hnědého cukru\n4 vejce\n100g mouky\n50g kakaového prášku\n100g vlašských ořechů",
            'postup' => "1. Rozehřejte troubu na 175°C.\n2. Rozpusťte čokoládu s máslem.\n3. Ušlehejte vejce s cukrem, vmíchejte čokoládu.\n4. Přidejte mouku, kakao a nasekané ořechy.\n5. Pečte 25-30 minut. Střed zůstane měkký.",
            'cas_pripravy' => 45,
            'pocet_porci' => 16,
            'hlavni_obrazek' => 'sev.jpeg',
            'kategorie_id' => 1, // Dorty
        ],
        [
            'slug' => 'krem-karamel',
            'nazev' => 'Krém s karamelem',
            'popis' => 'Francouzský crème caramel',
            'ingredience' => "200g cukru na karamel\n500ml mléka\n4 vejce\n50g cukru\n2 lžíce škrobu\n1 lžička vanilky",
            'postup' => "1. Uvařte z cukru zlatý karamel a rozlijte do formiček.\n2. Ušlehejte vejce s cukrem a škrobem.\n3. Přilijte horké mléko s vanilkou a promíchejte.\n4. Nalijte do formiček s karamelem.\n5. Vařte ve vodní lázni 25 minut.\n6. Před podáváním převraťte na talíř.",
            'cas_pripravy' => 40,
            'pocet_porci' => 6,
            'hlavni_obrazek' => 'eig.jpeg',
            'kategorie_id' => 4, // Dezerty
        ],
        [
            'slug' => 'ovocny-tartik',
            'nazev' => 'Ovocný tartík',
            'popis' => 'Elegantní ovocný tart s krémem',
            'ingredience' => "200g mouky\n100g másla\n50g cukru\n1 vejce\n500g krému (mascarpone + smetana)\nČerstvé ovoce (jahody, kiwi, maliny)\nŽelatina na zrcadlení",
            'postup' => "1. Vyrobte linecké těsto z mouky, másla, cukru a vejce.\n2. Vyválejte a vyložte formu na tartík.\n3. Napečte na slepou při 180°C 15 minut.\n4. Naplňte krémem a ozdobte ovocem.\n5. Přelijte želatinou a nechte ztužit.",
            'cas_pripravy' => 50,
            'pocet_porci' => 8,
            'hlavni_obrazek' => 'nine.jpeg',
            'kategorie_id' => 4, // Dezerty
        ],
    ];
    
    // Import receptů
    echo "<h3>Import receptů...</h3>";
    $recepty_pocet = 0;
    
    foreach ($recepty_data as $recept) {
        // Kontrola, jestli recept už neexistuje
        $existuje = $db->fetchOne("SELECT id FROM recepty WHERE slug = :slug", [
            'slug' => $recept['slug']
        ]);
        
        if (!$existuje) {
            $db->insert('recepty', [
                'slug' => $recept['slug'],
                'nazev' => $recept['nazev'],
                'popis' => $recept['popis'],
                'ingredience' => $recept['ingredience'],
                'postup' => $recept['postup'],
                'cas_pripravy' => $recept['cas_pripravy'],
                'pocet_porci' => $recept['pocet_porci'],
                'hlavni_obrazek' => $recept['hlavni_obrazek'],
                'kategorie_id' => $recept['kategorie_id'],
                'datum_vytvoreni' => date('Y-m-d H:i:s'),
                'aktivni' => 1
            ]);
            $recepty_pocet++;
            echo "<p>✓ Přidán recept: {$recept['nazev']}</p>";
        } else {
            echo "<p>- Už existuje: {$recept['nazev']}</p>";
        }
    }
    
    echo "<h3 style='color: green;'>✅ Import dokončen!</h3>";
    echo "<p><strong>Přidáno obrázků do galerie:</strong> $galerie_pocet</p>";
    echo "<p><strong>Přidáno receptů:</strong> $recepty_pocet</p>";
    
    // Přesun obrázků z img/ do uploads/
    echo "<h3>Kopírování obrázků...</h3>";
    
    // Vytvoření potřebných složek
    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
    if (!is_dir('uploads/galerie')) mkdir('uploads/galerie', 0755, true);
    if (!is_dir('uploads/recepty')) mkdir('uploads/recepty', 0755, true);
    
    $kopirovano = 0;
    
    // Kopírování obrázků z galerie
    foreach ($galerie_obrazky as $kategorie => $obrazky) {
        foreach ($obrazky as $obrazek) {
            $zdroj = 'img/' . $obrazek['nazev_souboru'];
            $cil = 'uploads/galerie/' . $obrazek['nazev_souboru'];
            
            if (file_exists($zdroj) && !file_exists($cil)) {
                if (copy($zdroj, $cil)) {
                    $kopirovano++;
                    echo "<p>✓ Zkopírován: {$obrazek['nazev_souboru']}</p>";
                }
            }
        }
    }
    
    // Kopírování všech obrázků z receptů
    $recepty_obrazky = [
        'first.jpeg', 'sec.jpeg', 'thi.jpeg', 'fou.jpeg', 'five.jpeg', 
        'six.jpeg', 'sev.jpeg', 'eig.jpeg', 'nine.jpeg'
    ];
    
    foreach ($recepty_obrazky as $obrazek) {
        $zdroj = 'img/Middle/' . $obrazek;
        $cil = 'uploads/recepty/' . $obrazek;
        
        if (file_exists($zdroj) && !file_exists($cil)) {
            if (copy($zdroj, $cil)) {
                $kopirovano++;
                echo "<p>✓ Zkopírován: $obrazek</p>";
            }
        } elseif (!file_exists($zdroj)) {
            echo "<p style='color: orange;'>⚠ Nenalezen: $zdroj</p>";
        }
    }
    
    echo "<p><strong>Zkopírováno souborů:</strong> $kopirovano</p>";
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>
        <h3>🎉 Hotovo!</h3>
        <p>Nyní můžete přejít do administrace a upravovat obrázky a recepty:</p>
        <ul>
            <li><a href='admin-new.php?sekce=galerie' target='_blank'>Správa galerie</a></li>
            <li><a href='admin-new.php?sekce=recepty' target='_blank'>Správa receptů</a></li>
        </ul>
    </div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Chyba: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #834912; }
p { margin: 5px 0; }
a { color: #834912; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>