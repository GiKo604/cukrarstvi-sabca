<?php
// Správa kontaktních zpráv

if (!$db) {
    // Fallback na soubor se zprávami
    $soubor_cesta = SLOZKA_ZALOHY . '/' . SOUBOR_ZALOHY;
    
    echo '<h2><i class="fas fa-envelope"></i> Kontaktní zprávy (ze souboru)</h2>';
    
    if (file_exists($soubor_cesta)) {
        $obsah = file_get_contents($soubor_cesta);
        $pocet_zprav = substr_count($obsah, '-----');
        $velikost = round(filesize($soubor_cesta) / 1024, 2);
        $upraveno = date('d.m.Y H:i:s', filemtime($soubor_cesta));
        
        echo '<div class="statistiky">
            <div class="stat-karta">
                <div class="stat-cislo">' . $pocet_zprav . '</div>
                <div class="stat-popis">Celkem zpráv</div>
            </div>
            <div class="stat-karta">
                <div class="stat-cislo">' . $velikost . ' KB</div>
                <div class="stat-popis">Velikost souboru</div>
            </div>
            <div class="stat-karta">
                <div class="stat-cislo">' . $upraveno . '</div>
                <div class="stat-popis">Poslední zpráva</div>
            </div>
        </div>';
        
        echo '<div style="background: white; padding: 20px; border-radius: 10px; white-space: pre-wrap; font-family: monospace; font-size: 14px; line-height: 1.4; max-height: 600px; overflow-y: auto; border: 2px solid #834912;">';
        echo h($obsah);
        echo '</div>';
        
        echo '<div style="margin-top: 20px;">
            <a href="../config/email-nastaveni.php" class="admin-button" target="_blank">
                <i class="fas fa-cog"></i> Nastavení emailů
            </a>
            <a href="../tests/test-formular.php" class="admin-button" target="_blank">
                <i class="fas fa-envelope"></i> Testovací formulář
            </a>
        </div>';
    } else {
        echo '<div class="info">
            <h3>Žádné zprávy</h3>
            <p>Soubor se zprávami zatím neexistuje. Zprávy se objeví po prvním odeslání formuláře.</p>
            <a href="../kontakt.html" class="admin-button" target="_blank">
                <i class="fas fa-envelope"></i> Otevřít kontaktní formulář
            </a>
        </div>';
    }
    
    return;
}

$akce = $_GET['akce'] ?? 'seznam';
$id = $_GET['id'] ?? null;

// Zpracování formulářů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['akce'] ?? '') {
            case 'oznacit_stav':
                $db->update('kontaktni_zpravy', 
                    ['stav' => $_POST['stav']], 
                    'id = :id', 
                    ['id' => $_POST['id']]
                );
                $uspech = "Stav zprávy byl změněn!";
                break;
                
            case 'pridat_poznamku':
                $db->update('kontaktni_zpravy', 
                    ['poznamky' => $_POST['poznamky']], 
                    'id = :id', 
                    ['id' => $_POST['id']]
                );
                $uspech = "Poznámka byla uložena!";
                break;
                
            case 'smazat':
                $db->delete('kontaktni_zpravy', 'id = :id', ['id' => $_POST['id']]);
                $uspech = "Zpráva byla smazána!";
                $akce = 'seznam';
                break;
        }
    } catch (Exception $e) {
        $chyba = "Chyba: " . $e->getMessage();
    }
}

switch ($akce) {
    case 'detail':
        if (!$id) {
            echo '<div class="chyba">Chybí ID zprávy!</div>';
            return;
        }
        
        try {
            $zprava = $db->fetchOne("SELECT * FROM kontaktni_zpravy WHERE id = :id", ['id' => $id]);
            if (!$zprava) {
                echo '<div class="chyba">Zpráva nenalezena!</div>';
                return;
            }
            
            // Označit jako přečtenou
            if ($zprava['stav'] === 'nová') {
                $db->update('kontaktni_zpravy', 
                    ['stav' => 'přečtená', 'datum_zpracovani' => date('Y-m-d H:i:s')], 
                    'id = :id', 
                    ['id' => $id]
                );
                $zprava['stav'] = 'přečtená';
            }
            
        } catch (Exception $e) {
            echo '<div class="chyba">Chyba při načítání zprávy: ' . h($e->getMessage()) . '</div>';
            return;
        }
        
        $stavy_barvy = [
            'nová' => '#2196f3',
            'přečtená' => '#ff9800', 
            'odpovězená' => '#4caf50',
            'spam' => '#f44336'
        ];
        ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-envelope-open"></i> Detail zprávy</h2>
            <a href="?sekce=zpravy" class="admin-button">
                <i class="fas fa-arrow-left"></i> Zpět na seznam
            </a>
        </div>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
                <!-- Obsah zprávy -->
                <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;">
                        <h3 style="margin: 0; color: #834912;">Od: <?php echo h($zprava['jmeno']); ?></h3>
                        <p style="margin: 5px 0; color: #666;">
                            <i class="fas fa-envelope"></i> <?php echo h($zprava['email']); ?>
                        </p>
                        <p style="margin: 5px 0; color: #666; font-size: 14px;">
                            <i class="fas fa-clock"></i> <?php echo date('d.m.Y H:i:s', strtotime($zprava['datum_odeslani'])); ?>
                        </p>
                        <?php if ($zprava['ip_adresa']): ?>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">
                                <i class="fas fa-globe"></i> IP: <?php echo h($zprava['ip_adresa']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div style="line-height: 1.6;">
                        <h4>Zpráva:</h4>
                        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; white-space: pre-wrap;">
                            <?php echo h($zprava['zprava']); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Poznámky -->
                <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <h3><i class="fas fa-sticky-note"></i> Poznámky</h3>
                    <form method="post">
                        <input type="hidden" name="akce" value="pridat_poznamku">
                        <input type="hidden" name="id" value="<?php echo $zprava['id']; ?>">
                        
                        <div class="form-group">
                            <textarea name="poznamky" rows="5" placeholder="Přidejte své poznámky k této zprávě..."><?php echo h($zprava['poznamky']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="admin-button btn-success">
                            <i class="fas fa-save"></i> Uložit poznámku
                        </button>
                    </form>
                </div>
            </div>
            
            <div>
                <!-- Stav zprávy -->
                <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3><i class="fas fa-info-circle"></i> Stav zprávy</h3>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="background: <?php echo $stavy_barvy[$zprava['stav']]; ?>; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold;">
                            <?php echo ucfirst($zprava['stav']); ?>
                        </span>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="akce" value="oznacit_stav">
                        <input type="hidden" name="id" value="<?php echo $zprava['id']; ?>">
                        
                        <div class="form-group">
                            <label>Změnit stav:</label>
                            <select name="stav">
                                <option value="nová" <?php echo $zprava['stav'] === 'nová' ? 'selected' : ''; ?>>Nová</option>
                                <option value="přečtená" <?php echo $zprava['stav'] === 'přečtená' ? 'selected' : ''; ?>>Přečtená</option>
                                <option value="odpovězená" <?php echo $zprava['stav'] === 'odpovězená' ? 'selected' : ''; ?>>Odpovězená</option>
                                <option value="spam" <?php echo $zprava['stav'] === 'spam' ? 'selected' : ''; ?>>Spam</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="admin-button btn-warning">
                            <i class="fas fa-edit"></i> Změnit stav
                        </button>
                    </form>
                </div>
                
                <!-- Rychlé akce -->
                <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <h3><i class="fas fa-bolt"></i> Rychlé akce</h3>
                    
                    <div class="akce-tlacitka" style="flex-direction: column;">
                        <a href="mailto:<?php echo h($zprava['email']); ?>?subject=Re: Dotaz z webu" 
                           class="admin-button btn-success">
                            <i class="fas fa-reply"></i> Odpovědět emailem
                        </a>
                        
                        <button onclick="kopirovatEmail()" class="admin-button">
                            <i class="fas fa-copy"></i> Kopírovat email
                        </button>
                        
                        <button onclick="smazatZpravu(<?php echo $zprava['id']; ?>)" class="admin-button btn-danger">
                            <i class="fas fa-trash"></i> Smazat zprávu
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function kopirovatEmail() {
                navigator.clipboard.writeText('<?php echo h($zprava['email']); ?>');
                alert('Email byl zkopírován do schránky!');
            }
            
            function smazatZpravu(id) {
                if (confirm('Opravdu chcete smazat tuto zprávu? Tato akce je nevratná!')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="akce" value="smazat">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
        
        <?php
        break;
        
    default: // seznam
        try {
            $filtr_stav = $_GET['stav'] ?? '';
            $where = '';
            $params = [];
            
            if ($filtr_stav) {
                $where = 'WHERE stav = :stav';
                $params['stav'] = $filtr_stav;
            }
            
            $zpravy = $db->fetchAll("
                SELECT * FROM kontaktni_zpravy 
                $where
                ORDER BY 
                    CASE WHEN stav = 'nová' THEN 0 ELSE 1 END,
                    datum_odeslani DESC
            ", $params);
            
            // Statistiky
            $stats = $db->fetchAll("
                SELECT stav, COUNT(*) as pocet 
                FROM kontaktni_zpravy 
                GROUP BY stav
            ");
            
        } catch (Exception $e) {
            echo '<div class="chyba">Chyba při načítání zpráv: ' . h($e->getMessage()) . '</div>';
            return;
        }
        
        $stavy_barvy = [
            'nová' => '#2196f3',
            'přečtená' => '#ff9800', 
            'odpovězená' => '#4caf50',
            'spam' => '#f44336'
        ];
        ?>
        
        <h2><i class="fas fa-envelope"></i> Kontaktní zprávy</h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <!-- Statistiky -->
        <?php if (!empty($stats)): ?>
            <div class="statistiky">
                <?php 
                $celkem = 0;
                foreach ($stats as $stat) {
                    $celkem += $stat['pocet'];
                }
                ?>
                <div class="stat-karta">
                    <div class="stat-cislo"><?php echo $celkem; ?></div>
                    <div class="stat-popis">Celkem zpráv</div>
                </div>
                <?php foreach ($stats as $stat): ?>
                    <div class="stat-karta" style="background: <?php echo $stavy_barvy[$stat['stav']]; ?>;">
                        <div class="stat-cislo"><?php echo $stat['pocet']; ?></div>
                        <div class="stat-popis"><?php echo ucfirst($stat['stav']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Filtrování -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h3><i class="fas fa-filter"></i> Filtrování podle stavu</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <a href="?sekce=zpravy" class="admin-button <?php echo !$filtr_stav ? 'active' : ''; ?>">
                    Všechny zprávy
                </a>
                <a href="?sekce=zpravy&stav=nová" class="admin-button <?php echo $filtr_stav === 'nová' ? 'active' : ''; ?>">
                    Nové
                </a>
                <a href="?sekce=zpravy&stav=přečtená" class="admin-button <?php echo $filtr_stav === 'přečtená' ? 'active' : ''; ?>">
                    Přečtené
                </a>
                <a href="?sekce=zpravy&stav=odpovězená" class="admin-button <?php echo $filtr_stav === 'odpovězená' ? 'active' : ''; ?>">
                    Odpovězené
                </a>
                <a href="?sekce=zpravy&stav=spam" class="admin-button <?php echo $filtr_stav === 'spam' ? 'active' : ''; ?>">
                    Spam
                </a>
            </div>
        </div>
        
        <?php if (empty($zpravy)): ?>
            <div class="info">
                <h3>Žádné zprávy</h3>
                <p>
                    <?php if ($filtr_stav): ?>
                        Žádné zprávy ve stavu "<?php echo h($filtr_stav); ?>".
                    <?php else: ?>
                        Zatím nebyly přijaty žádné kontaktní zprávy.
                    <?php endif; ?>
                </p>
                <a href="../kontakt.html" class="admin-button" target="_blank">
                    <i class="fas fa-envelope"></i> Otevřít kontaktní formulář
                </a>
            </div>
        <?php else: ?>
            <table class="tabulka">
                <thead>
                    <tr>
                        <th>Stav</th>
                        <th>Odesílatel</th>
                        <th>Zpráva</th>
                        <th>Datum</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zpravy as $zprava): ?>
                        <tr <?php echo $zprava['stav'] === 'nová' ? 'style="background: #f0f8ff;"' : ''; ?>>
                            <td>
                                <span style="background: <?php echo $stavy_barvy[$zprava['stav']]; ?>; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                    <?php echo ucfirst($zprava['stav']); ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo h($zprava['jmeno']); ?></strong><br>
                                <small style="color: #666;"><?php echo h($zprava['email']); ?></small>
                            </td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden;">
                                    <?php echo h(substr($zprava['zprava'], 0, 100)); ?><?php echo strlen($zprava['zprava']) > 100 ? '...' : ''; ?>
                                </div>
                            </td>
                            <td>
                                <?php echo date('d.m.Y', strtotime($zprava['datum_odeslani'])); ?><br>
                                <small style="color: #666;"><?php echo date('H:i', strtotime($zprava['datum_odeslani'])); ?></small>
                            </td>
                            <td style="white-space: nowrap; width: 100px;">
                                <div style="display: flex; gap: 5px; flex-wrap: nowrap;">
                                    <a href="?sekce=zpravy&akce=detail&id=<?php echo $zprava['id']; ?>" 
                                       style="background: #2196f3; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        👁️
                                    </a>
                                    <a href="mailto:<?php echo h($zprava['email']); ?>" 
                                       style="background: #4caf50; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        📧
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <?php
        break;
}
?>