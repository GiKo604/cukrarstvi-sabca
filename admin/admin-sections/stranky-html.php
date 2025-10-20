<?php
// Správa HTML stránek - editace skutečných souborů

$akce = $_GET['akce'] ?? 'seznam';
$id = $_GET['id'] ?? null;

// Definice editovatelných stránek
$editovatelneStranek = [
    'domu' => [
        'nazev' => 'Domovská stránka',
        'soubor' => '../domu.html',
        'popis' => 'Hlavní stránka webu s uvítáním a představením'
    ],
    'galerie' => [
        'nazev' => 'Galerie',
        'soubor' => '../galerie.html', 
        'popis' => 'Fotogalerie výrobků a cukrárny'
    ],
    'kontakt' => [
        'nazev' => 'Kontakt',
        'soubor' => '../kontakt.html',
        'popis' => 'Kontaktní informace a formulář'
    ],
    'recepty' => [
        'nazev' => 'Recepty', 
        'soubor' => '../recepty.php',
        'popis' => 'Dynamické recepty z databáze (jen pro pokročilé úpravy)'
    ]
];

// Zpracování formulářů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['akce'] ?? '') {
            case 'upravit':
                $strankaId = $_POST['stranka_id'];
                $novyObsah = $_POST['obsah'];
                
                if (!isset($editovatelneStranek[$strankaId])) {
                    throw new Exception("Neplatné ID stránky!");
                }
                
                $soubor = $editovatelneStranek[$strankaId]['soubor'];
                
                // Vytvoření zálohy
                $zalohaSlozka = '../zalohy/stranky/';
                if (!file_exists($zalohaSlozka)) {
                    mkdir($zalohaSlozka, 0755, true);
                }
                
                if (file_exists($soubor)) {
                    $zalohaNazev = $zalohaSlozka . $strankaId . '_' . date('Y-m-d_H-i-s') . '.html';
                    copy($soubor, $zalohaNazev);
                }
                
                // Uložení nového obsahu
                if (file_put_contents($soubor, $novyObsah) !== false) {
                    $uspech = "Stránka '{$editovatelneStranek[$strankaId]['nazev']}' byla úspěšně aktualizována!";
                } else {
                    throw new Exception("Nepodařilo se uložit soubor!");
                }
                
                $akce = 'seznam';
                break;
        }
    } catch (Exception $e) {
        $chyba = "Chyba: " . $e->getMessage();
    }
}

// Funkce pro načtení obsahu HTML souboru
function nacistObsahHTML($soubor) {
    if (!file_exists($soubor)) {
        return '';
    }
    return file_get_contents($soubor);
}

// Zobrazení podle akce
switch ($akce) {
    case 'upravit':
        if (!$id || !isset($editovatelneStranek[$id])) {
            echo '<div class="chyba">Neplatné ID stránky!</div>';
            return;
        }
        
        $stranka = $editovatelneStranek[$id];
        $obsah = nacistObsahHTML($stranka['soubor']);
        ?>
        
        <h2>
            <i class="fas fa-edit"></i>
            Upravit: <?php echo h($stranka['nazev']); ?>
        </h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <div class="admin-box">
            <form method="post" class="admin-form">
                <input type="hidden" name="akce" value="upravit">
                <input type="hidden" name="stranka_id" value="<?php echo h($id); ?>">
                
                <div class="form-group">
                    <label>Stránka:</label>
                    <div class="info-text">
                        <strong><?php echo h($stranka['nazev']); ?></strong><br>
                        <small><?php echo h($stranka['popis']); ?></small><br>
                        <small>Soubor: <?php echo h($stranka['soubor']); ?></small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="obsah">HTML obsah:</label>
                    <textarea name="obsah" id="obsah" rows="20" style="width: 100%; font-family: monospace;"><?php echo h($obsah); ?></textarea>
                    <small>Tip: Upravte HTML kód přímo. Změny se projeví okamžitě na webu.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="admin-button btn-success">
                        <i class="fas fa-save"></i> Uložit změny
                    </button>
                    <a href="?sekce=stranky-html" class="admin-button">
                        <i class="fas fa-arrow-left"></i> Zpět na seznam
                    </a>
                    <a href="../index.php?id-stranky=<?php echo h($id); ?>" 
                       class="admin-button btn-info" target="_blank">
                        <i class="fas fa-eye"></i> Zobrazit stránku
                    </a>
                </div>
            </form>
        </div>
        
        <div class="admin-box" style="margin-top: 20px;">
            <h3><i class="fas fa-info-circle"></i> Nápověda pro editaci</h3>
            <ul>
                <li><strong>HTML tagy:</strong> Používejte standardní HTML tagy jako &lt;h1&gt;, &lt;p&gt;, &lt;div&gt;, atd.</li>
                <li><strong>CSS třídy:</strong> Můžete používat třídy z style.css pro styling</li>
                <li><strong>Zálohy:</strong> Před každou změnou se vytvoří automatická záloha</li>
                <li><strong>Náhled:</strong> Klikněte na "Zobrazit stránku" pro okamžitý náhled</li>
            </ul>
        </div>
        
        <?php
        break;
    
    default: // Seznam stránek
        ?>
        <h2>
            <i class="fas fa-file-alt"></i>
            Správa HTML stránek
        </h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <div class="admin-box">
            <p class="info-text">
                <i class="fas fa-info-circle"></i>
                Zde můžete upravovat obsah HTML stránek. Změny se projeví okamžitě na webu.
            </p>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Stránka</th>
                        <th>Popis</th>
                        <th>Soubor</th>
                        <th>Poslední úprava</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($editovatelneStranek as $strankaId => $stranka): ?>
                        <tr>
                            <td><strong><?php echo h($stranka['nazev']); ?></strong></td>
                            <td><?php echo h($stranka['popis']); ?></td>
                            <td><code><?php echo h($stranka['soubor']); ?></code></td>
                            <td>
                                <?php 
                                if (file_exists($stranka['soubor'])) {
                                    echo date('d.m.Y H:i', filemtime($stranka['soubor']));
                                } else {
                                    echo '<span style="color: #f44336;">Soubor neexistuje</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?sekce=stranky-html&akce=upravit&id=<?php echo urlencode($strankaId); ?>" 
                                       style="background: #f57c00; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        ✏️
                                    </a>
                                    <a href="../index.php?id-stranky=<?php echo urlencode($strankaId); ?>" 
                                       target="_blank"
                                       style="background: #2196f3; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        👁️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="admin-box" style="margin-top: 20px;">
            <h3><i class="fas fa-history"></i> Zálohy</h3>
            <p>
                Při každé úpravě se automaticky vytvoří záloha v složce <code>zalohy/stranky/</code>.
                Zálohy jsou pojmenovány podle vzoru: <code>nazev-stranky_YYYY-MM-DD_HH-MM-SS.html</code>
            </p>
        </div>
        
        <?php
        break;
}
?>