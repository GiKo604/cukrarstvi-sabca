<?php
// Nastavení administrace

$akce = $_GET['akce'] ?? 'prehled';

// Zpracování formulářů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['akce'] ?? '') {
            case 'aktualizovat_heslo':
                if (empty($_POST['nove_heslo']) || empty($_POST['potvrzeni_hesla'])) {
                    throw new Exception("Všechna pole jsou povinná!");
                }
                if ($_POST['nove_heslo'] !== $_POST['potvrzeni_hesla']) {
                    throw new Exception("Hesla se neshodují!");
                }
                if (strlen($_POST['nove_heslo']) < 6) {
                    throw new Exception("Heslo musí mít alespoň 6 znaků!");
                }
                
                if ($db) {
                    $hash = password_hash($_POST['nove_heslo'], PASSWORD_DEFAULT);
                    $db->update('admin_uzivatele', 
                        ['heslo' => $hash, 'datum_zmeny_hesla' => date('Y-m-d H:i:s')], 
                        'uzivatel = :uzivatel', 
                        ['uzivatel' => $_SESSION['admin_user']]
                    );
                }
                $uspech = "Heslo bylo úspěšně změněno!";
                break;
                
            case 'vymazat_cache':
                $cache_slozky = [
                    UPLOAD_DIR . '/thumbnails',
                    UPLOAD_DIR . '/cache'
                ];
                
                $smazano = 0;
                foreach ($cache_slozky as $slozka) {
                    if (is_dir($slozka)) {
                        $soubory = glob($slozka . '/*');
                        foreach ($soubory as $soubor) {
                            if (is_file($soubor)) {
                                unlink($soubor);
                                $smazano++;
                            }
                        }
                    }
                }
                $uspech = "Vymazáno $smazano souborů z cache!";
                break;
                
            case 'optimalizovat_databazi':
                if ($db) {
                    $tabulky = ['stranky', 'recepty', 'kategorie_receptu', 'kategorie_galerie', 'obrazky_receptu', 'galerie', 'kontaktni_zpravy'];
                    foreach ($tabulky as $tabulka) {
                        $db->query("OPTIMIZE TABLE $tabulka");
                    }
                    $uspech = "Databáze byla optimalizována!";
                } else {
                    throw new Exception("Databáze není připojena!");
                }
                break;
        }
    } catch (Exception $e) {
        $chyba = "Chyba: " . $e->getMessage();
    }
}

switch ($akce) {
    case 'system':
        ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-server"></i> Systémové informace</h2>
            <a href="?sekce=nastaveni" class="admin-button">
                <i class="fas fa-arrow-left"></i> Zpět na přehled
            </a>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- PHP informace -->
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-code"></i> PHP</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Verze:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo phpversion(); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Upload max:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo ini_get('upload_max_filesize'); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Post max:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo ini_get('post_max_size'); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Memory limit:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo ini_get('memory_limit'); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Max execution:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo ini_get('max_execution_time'); ?>s</td></tr>
                </table>
                
                <h4 style="margin-top: 20px;">PHP rozšíření:</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php
                    $pozadovana_rozsireni = ['gd', 'pdo_mysql', 'mbstring', 'fileinfo', 'json'];
                    foreach ($pozadovana_rozsireni as $ext) {
                        $aktivni = extension_loaded($ext);
                        echo '<span style="background: ' . ($aktivni ? '#4caf50' : '#f44336') . '; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">';
                        echo $ext . ($aktivni ? ' ✓' : ' ✗');
                        echo '</span>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Diskový prostor -->
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-hdd"></i> Diskový prostor</h3>
                <?php
                $volny_prostor = disk_free_space(__DIR__);
                $celkovy_prostor = disk_total_space(__DIR__);
                $pouzity_prostor = $celkovy_prostor - $volny_prostor;
                $procenta = round(($pouzity_prostor / $celkovy_prostor) * 100, 1);
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="background: #f0f0f0; height: 20px; border-radius: 10px; overflow: hidden;">
                        <div style="background: <?php echo $procenta > 80 ? '#f44336' : ($procenta > 60 ? '#ff9800' : '#4caf50'); ?>; height: 100%; width: <?php echo $procenta; ?>%;"></div>
                    </div>
                    <small>Využito <?php echo $procenta; ?>%</small>
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Celkem:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo formatujVelikostSouboru($celkovy_prostor); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Použito:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo formatujVelikostSouboru($pouzity_prostor); ?></td></tr>
                    <tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Volno:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo formatujVelikostSouboru($volny_prostor); ?></td></tr>
                </table>
                
                <?php
                // Velikost složek
                $slozky_velikosti = [];
                if (is_dir(UPLOAD_DIR)) {
                    $velikost = 0;
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UPLOAD_DIR));
                    foreach ($iterator as $soubor) {
                        if ($soubor->isFile()) {
                            $velikost += $soubor->getSize();
                        }
                    }
                    $slozky_velikosti['Nahrané soubory'] = $velikost;
                }
                ?>
                
                <h4 style="margin-top: 20px;">Velikost složek:</h4>
                <?php foreach ($slozky_velikosti as $nazev => $velikost): ?>
                    <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee;">
                        <span><?php echo $nazev; ?>:</span>
                        <span><?php echo formatujVelikostSouboru($velikost); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Databázové informace -->
        <?php if ($db): ?>
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-top: 20px;">
                <h3><i class="fas fa-database"></i> Databáze</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <?php
                    $tabulky = ['stranky', 'recepty', 'kategorie_receptu', 'obrazky_receptu', 'galerie', 'kontaktni_zpravy'];
                    foreach ($tabulky as $tabulka) {
                        try {
                            $pocet = $db->fetchValue("SELECT COUNT(*) FROM $tabulka");
                            $velikost_result = $db->fetchOne("SHOW TABLE STATUS LIKE '$tabulka'");
                            $velikost = $velikost_result ? ($velikost_result['Data_length'] + $velikost_result['Index_length']) : 0;
                            
                            echo '<div style="text-align: center; padding: 15px; border: 2px solid #834912; border-radius: 10px;">';
                            echo '<h4 style="margin: 0; color: #834912;">' . ucfirst($tabulka) . '</h4>';
                            echo '<div style="font-size: 24px; font-weight: bold; color: #834912; margin: 10px 0;">' . $pocet . '</div>';
                            echo '<div style="font-size: 12px; color: #666;">' . formatujVelikostSouboru($velikost) . '</div>';
                            echo '</div>';
                        } catch (Exception $e) {
                            echo '<div style="text-align: center; padding: 15px; border: 2px solid #f44336; border-radius: 10px;">';
                            echo '<h4 style="margin: 0; color: #f44336;">' . ucfirst($tabulka) . '</h4>';
                            echo '<div style="color: #f44336;">Chyba</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        break;
        
    default: // prehled
        ?>
        <h2><i class="fas fa-cog"></i> Nastavení administrace</h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Změna hesla -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-key"></i> Změna hesla</h3>
                <form method="post">
                    <input type="hidden" name="akce" value="aktualizovat_heslo">
                    
                    <div class="form-group">
                        <label>Nové heslo:</label>
                        <input type="password" name="nove_heslo" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label>Potvrzení hesla:</label>
                        <input type="password" name="potvrzeni_hesla" required minlength="6">
                    </div>
                    
                    <button type="submit" class="admin-button btn-warning">
                        <i class="fas fa-save"></i> Změnit heslo
                    </button>
                </form>
            </div>
            
            <!-- Údržba systému -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-tools"></i> Údržba systému</h3>
                
                <div style="margin-bottom: 15px;">
                    <h4>Vymazat cache:</h4>
                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Smaže všechny miniatury a cache soubory.</p>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="akce" value="vymazat_cache">
                        <button type="submit" class="admin-button btn-warning" onclick="return confirm('Opravdu chcete vymazat cache?')">
                            <i class="fas fa-trash"></i> Vymazat cache
                        </button>
                    </form>
                </div>
                
                <?php if ($db): ?>
                    <div>
                        <h4>Optimalizovat databázi:</h4>
                        <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Optimalizuje všechny tabulky databáze.</p>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="akce" value="optimalizovat_databazi">
                            <button type="submit" class="admin-button btn-success" onclick="return confirm('Opravdu chcete optimalizovat databázi?')">
                                <i class="fas fa-database"></i> Optimalizovat
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Informace o systému -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-info-circle"></i> Informace o systému</h3>
                
                <div style="margin-bottom: 15px;">
                    <strong>Přihlášený uživatel:</strong><br>
                    <span style="color: #834912;"><?php echo h($_SESSION['admin_user']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Posledního přihlášení:</strong><br>
                    <span style="color: #666;"><?php echo date('d.m.Y H:i:s', $_SESSION['admin_login_time']); ?></span>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <strong>Databáze:</strong><br>
                    <span style="color: <?php echo $db ? '#4caf50' : '#f44336'; ?>;">
                        <?php echo $db ? 'Připojeno' : 'Nepřipojeno'; ?>
                    </span>
                </div>
                
                <a href="?sekce=nastaveni&akce=system" class="admin-button">
                    <i class="fas fa-server"></i> Systémové informace
                </a>
            </div>
            
            <!-- Složky a soubory -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-folder"></i> Složky projektu</h3>
                
                <?php
                $dulezite_slozky = [
                    UPLOAD_DIR => 'Nahrané soubory',
                    UPLOAD_DIR . '/recepty' => 'Obrázky receptů',
                    UPLOAD_DIR . '/galerie' => 'Galerie',
                    UPLOAD_DIR . '/thumbnails' => 'Miniatury',
                    SLOZKA_ZALOHY => 'Zálohy'
                ];
                
                foreach ($dulezite_slozky as $cesta => $nazev) {
                    $existuje = is_dir($cesta);
                    $zapisovatelna = $existuje && is_writable($cesta);
                    
                    echo '<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">';
                    echo '<span>' . $nazev . ':</span>';
                    echo '<span>';
                    
                    if ($existuje) {
                        if ($zapisovatelna) {
                            echo '<span style="color: #4caf50;"><i class="fas fa-check"></i> OK</span>';
                        } else {
                            echo '<span style="color: #ff9800;"><i class="fas fa-exclamation-triangle"></i> Jen čtení</span>';
                        }
                    } else {
                        echo '<span style="color: #f44336;"><i class="fas fa-times"></i> Neexistuje</span>';
                    }
                    
                    echo '</span>';
                    echo '</div>';
                }
                ?>
            </div>
            
            <!-- Rychlé odkazy -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-external-link-alt"></i> Rychlé odkazy</h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="index.html" class="admin-button" target="_blank">
                        <i class="fas fa-home"></i> Hlavní stránka
                    </a>
                    
                    <a href="kontakt.html" class="admin-button" target="_blank">
                        <i class="fas fa-envelope"></i> Kontaktní formulář
                    </a>
                    
                    <a href="../config/email-nastaveni.php" class="admin-button" target="_blank">
                        <i class="fas fa-cog"></i> Nastavení emailů
                    </a>
                    
                    <?php if (file_exists('phpmyadmin.php') || file_exists('../phpmyadmin/index.php')): ?>
                        <a href="<?php echo file_exists('phpmyadmin.php') ? 'phpmyadmin.php' : '../phpmyadmin/index.php'; ?>" 
                           class="admin-button" target="_blank">
                            <i class="fas fa-database"></i> phpMyAdmin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bezpečnost -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3><i class="fas fa-shield-alt"></i> Bezpečnost</h3>
                
                <div style="margin-bottom: 15px;">
                    <h4>Přístupová práva:</h4>
                    <?php
                    $htaccess_existuje = file_exists('.htaccess');
                    $admin_chraneno = $htaccess_existuje && strpos(file_get_contents('.htaccess'), 'admin') !== false;
                    ?>
                    <div style="color: <?php echo $admin_chraneno ? '#4caf50' : '#f44336'; ?>;">
                        <i class="fas fa-<?php echo $admin_chraneno ? 'shield-alt' : 'exclamation-triangle'; ?>"></i>
                        <?php echo $admin_chraneno ? 'Admin je chráněn' : 'Admin není chráněn .htaccess'; ?>
                    </div>
                </div>
                
                <div>
                    <h4>SSL/HTTPS:</h4>
                    <?php $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'; ?>
                    <div style="color: <?php echo $https ? '#4caf50' : '#ff9800'; ?>;">
                        <i class="fas fa-<?php echo $https ? 'lock' : 'unlock'; ?>"></i>
                        <?php echo $https ? 'Spojení je zabezpečené' : 'Spojení není zabezpečené'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        break;
}
?>