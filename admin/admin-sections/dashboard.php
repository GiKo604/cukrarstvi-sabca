<?php
// Dashboard - přehled administrace

if (!$db) {
    echo '<div class="info">
        <h3><i class="fas fa-database"></i> Nastavení databáze</h3>
        <p>Pro správné fungování administrace je potřeba:</p>
        <ol>
            <li>Vytvořit databázi <code>cukrarstvi_sabca</code> v phpMyAdmin</li>
            <li>Importovat soubor <code>databaze-schema.sql</code></li>
            <li>Zkontrolovat nastavení v <code>databaze-config.php</code></li>
        </ol>
        <p><strong>Cesta k phpMyAdmin:</strong> <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></p>
    </div>';
    return;
}

try {
    // Statistiky
    $pocet_stranek = $db->fetchOne("SELECT COUNT(*) as pocet FROM stranky WHERE aktivni = 1")['pocet'] ?? 0;
    $pocet_receptu = $db->fetchOne("SELECT COUNT(*) as pocet FROM recepty WHERE aktivni = 1")['pocet'] ?? 0;
    $pocet_zprav = 0;
    
    // Počet zpráv ze souboru (pokud existuje)
    $soubor_cesta = SLOZKA_ZALOHY . '/' . SOUBOR_ZALOHY;
    if (file_exists($soubor_cesta)) {
        $obsah = file_get_contents($soubor_cesta);
        $pocet_zprav = substr_count($obsah, '-----');
    }
    
    // Počet obrázků v galerii
    $pocet_obrazku = 0;
    if (file_exists('img')) {
        $obrazky = glob('img/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $pocet_obrazku = count($obrazky);
    }
    
    // Nejnovější recepty
    $nejnovejsi_recepty = $db->fetchAll("
        SELECT nazev, slug, datum_vytvoreni 
        FROM recepty 
        WHERE aktivni = 1 
        ORDER BY datum_vytvoreni DESC 
        LIMIT 5
    ");
    
    // Nejnovější stránky
    $nejnovejsi_stranky = $db->fetchAll("
        SELECT nazev, slug, datum_upravy 
        FROM stranky 
        WHERE aktivni = 1 
        ORDER BY datum_upravy DESC 
        LIMIT 5
    ");
    
} catch (Exception $e) {
    echo '<div class="chyba">Chyba při načítání dat: ' . h($e->getMessage()) . '</div>';
    return;
}
?>

<h2><i class="fas fa-chart-line"></i> Dashboard</h2>

<div class="statistiky">
    <div class="stat-karta">
        <div class="stat-cislo"><?php echo $pocet_stranek; ?></div>
        <div class="stat-popis">Aktivních stránek</div>
    </div>
    <div class="stat-karta">
        <div class="stat-cislo"><?php echo $pocet_receptu; ?></div>
        <div class="stat-popis">Publikovaných receptů</div>
    </div>
    <div class="stat-karta">
        <div class="stat-cislo"><?php echo $pocet_zprav; ?></div>
        <div class="stat-popis">Kontaktních zpráv</div>
    </div>
    <div class="stat-karta">
        <div class="stat-cislo"><?php echo $pocet_obrazku; ?></div>
        <div class="stat-popis">Obrázků v galerii</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
    <!-- Nejnovější recepty -->
    <div>
        <h3><i class="fas fa-utensils"></i> Nejnovější recepty</h3>
        <?php if (empty($nejnovejsi_recepty)): ?>
            <p>Žádné recepty zatím nebyly vytvořeny.</p>
            <a href="?sekce=recepty&akce=pridat" class="admin-button btn-small">
                <i class="fas fa-plus"></i> Přidat první recept
            </a>
        <?php else: ?>
            <table class="tabulka">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Vytvořeno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nejnovejsi_recepty as $recept): ?>
                        <tr>
                            <td>
                                <a href="?sekce=recepty&akce=upravit&id=<?php echo $recept['slug']; ?>">
                                    <?php echo h($recept['nazev']); ?>
                                </a>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($recept['datum_vytvoreni'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="?sekce=recepty" class="admin-button btn-small">
                <i class="fas fa-list"></i> Všechny recepty
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Nejnovější stránky -->
    <div>
        <h3><i class="fas fa-file-alt"></i> Poslední úpravy stránek</h3>
        <?php if (empty($nejnovejsi_stranky)): ?>
            <p>Žádné stránky zatím nebyly upraveny.</p>
            <a href="?sekce=stranky&akce=pridat" class="admin-button btn-small">
                <i class="fas fa-plus"></i> Přidat stránku
            </a>
        <?php else: ?>
            <table class="tabulka">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Upraveno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nejnovejsi_stranky as $stranka): ?>
                        <tr>
                            <td>
                                <a href="?sekce=stranky&akce=upravit&id=<?php echo $stranka['slug']; ?>">
                                    <?php echo h($stranka['nazev']); ?>
                                </a>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($stranka['datum_upravy'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="?sekce=stranky" class="admin-button btn-small">
                <i class="fas fa-list"></i> Všechny stránky
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Rychlé akce -->
<div style="margin-top: 40px;">
    <h3><i class="fas fa-bolt"></i> Rychlé akce</h3>
    <div class="akce-tlacitka">
        <a href="?sekce=stranky&akce=pridat" class="admin-button btn-success">
            <i class="fas fa-plus"></i> Nová stránka
        </a>
        <a href="?sekce=recepty&akce=pridat" class="admin-button btn-success">
            <i class="fas fa-plus"></i> Nový recept
        </a>
        <a href="?sekce=galerie&akce=nahrat" class="admin-button btn-success">
            <i class="fas fa-upload"></i> Nahrát obrázky
        </a>
        <a href="?sekce=zpravy" class="admin-button">
            <i class="fas fa-envelope"></i> Zkontrolovat zprávy
        </a>
        <a href="index.php" class="admin-button" target="_blank">
            <i class="fas fa-eye"></i> Zobrazit web
        </a>
        <a href="email-nastaveni.php" class="admin-button" target="_blank">
            <i class="fas fa-cog"></i> Nastavení emailů
        </a>
    </div>
</div>

<!-- Systémové informace -->
<div style="margin-top: 40px;">
    <h3><i class="fas fa-info-circle"></i> Systémové informace</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
            <strong>PHP verze:</strong> <?php echo phpversion(); ?><br>
            <strong>MySQL verze:</strong> 
            <?php 
            try {
                echo $db->fetchOne("SELECT VERSION() as verze")['verze'];
            } catch (Exception $e) {
                echo "Nedostupné";
            }
            ?><br>
            <strong>Velikost uploads:</strong> 
            <?php 
            if (file_exists(UPLOAD_DIR)) {
                $size = 0;
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UPLOAD_DIR));
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
                echo round($size / 1024 / 1024, 2) . ' MB';
            } else {
                echo '0 MB';
            }
            ?>
        </div>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
            <strong>Databáze:</strong> <?php echo DB_NAME; ?><br>
            <strong>Host:</strong> <?php echo DB_HOST; ?><br>
            <strong>Charset:</strong> <?php echo DB_CHARSET; ?><br>
            <strong>Upload limit:</strong> <?php echo ini_get('upload_max_filesize'); ?>
        </div>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
            <strong>Složky:</strong><br>
            <?php 
            $folders = [UPLOAD_DIR, UPLOAD_RECEPTY_DIR, UPLOAD_GALERIE_DIR, SLOZKA_ZALOHY];
            foreach ($folders as $folder) {
                $exists = file_exists($folder);
                $writable = $exists && is_writable($folder);
                echo $folder . ': ';
                if ($exists && $writable) {
                    echo '<span style="color: green;">✓ OK</span>';
                } elseif ($exists) {
                    echo '<span style="color: orange;">⚠ Jen čtení</span>';
                } else {
                    echo '<span style="color: red;">✗ Neexistuje</span>';
                }
                echo '<br>';
            }
            ?>
        </div>
    </div>
</div>