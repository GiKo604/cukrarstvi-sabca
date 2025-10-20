<?php
// Správa galerie s upload obrázků

if (!$db) {
    echo '<div class="chyba">Databáze není k dispozici.</div>';
    return;
}

$akce = $_GET['akce'] ?? 'seznam';

// Zpracování upload obrázků
function zpracovatUploadGalerie($soubor, $kategorie, $popis = '') {
    if ($soubor['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Chyba při uploadu souboru.');
    }
    
    if ($soubor['size'] > MAX_FILE_SIZE) {
        throw new Exception('Soubor je příliš velký. Maximální velikost je ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB.');
    }
    
    if (!isValidImage($soubor['name'])) {
        throw new Exception('Nepovolený typ souboru. Povolené jsou: ' . implode(', ', ALLOWED_IMAGE_TYPES));
    }
    
    $nazevSouboru = generateSafeFilename($soubor['name']);
    $cilCesta = UPLOAD_GALERIE_DIR . '/' . $nazevSouboru;
    
    if (!move_uploaded_file($soubor['tmp_name'], $cilCesta)) {
        throw new Exception('Nepodařilo se uložit soubor.');
    }
    
    // Vytvoření thumbnail
    $thumbnailCesta = UPLOAD_THUMBNAILS_DIR . '/thumb_' . $nazevSouboru;
    createThumbnail($cilCesta, $thumbnailCesta);
    
    return $nazevSouboru;
}

// Zpracování formulářů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['akce'] ?? '') {
            case 'nahrat':
                $kategorie = $_POST['kategorie'];
                $popis = $_POST['popis'];
                
                // Zpracování nové kategorie galerie
                if (!empty($_POST['nova_kategorie_galerie']) && empty($kategorie)) {
                    $novaKategorie = trim($_POST['nova_kategorie_galerie']);
                    
                    // Kontrola, zda kategorie s tímto názvem už neexistuje
                    $existujici = $db->fetchOne("SELECT id FROM kategorie_galerie WHERE nazev = ?", [$novaKategorie]);
                    if (!$existujici) {
                        $db->query(
                            "INSERT INTO kategorie_galerie (nazev, barva, poradi, aktivni) VALUES (?, '#3498db', 1, 1)",
                            [$novaKategorie]
                        );
                        $kategorieId = $db->getConnection()->lastInsertId();
                        // Pro galerii použijeme ID jako kategorii
                        $kategorie = $kategorieId;
                    } else {
                        $kategorie = $existujici['id'];
                    }
                }
                
                // Kontrola, zda máme kategorii (buď vybranou nebo nově vytvořenou)
                if (empty($kategorie)) {
                    throw new Exception('Musíte vybrat kategorii nebo vytvořit novou.');
                }
                
                // Batch upload více souborů
                if (isset($_FILES['obrazky'])) {
                    $pocetNahrano = 0;
                    foreach ($_FILES['obrazky']['tmp_name'] as $index => $tmpName) {
                        if ($_FILES['obrazky']['error'][$index] === UPLOAD_ERR_OK) {
                            $soubor = [
                                'tmp_name' => $tmpName,
                                'name' => $_FILES['obrazky']['name'][$index],
                                'size' => $_FILES['obrazky']['size'][$index],
                                'error' => $_FILES['obrazky']['error'][$index]
                            ];
                            
                            $nazevSouboru = zpracovatUploadGalerie($soubor, $kategorie, $popis);
                            
                            $data = [
                                'nazev' => pathinfo($_FILES['obrazky']['name'][$index], PATHINFO_FILENAME),
                                'nazev_souboru' => $nazevSouboru,
                                'alt_text' => $popis ?: pathinfo($_FILES['obrazky']['name'][$index], PATHINFO_FILENAME),
                                'popis' => $popis,
                                'kategorie' => $kategorie,
                                'poradi' => $index
                            ];
                            
                            $db->insert('galerie', $data);
                            $pocetNahrano++;
                        }
                    }
                    $uspech = "Úspěšně nahráno $pocetNahrano obrázků!";
                }
                break;
                
            case 'upravit':
                $data = [
                    'nazev' => $_POST['nazev'],
                    'alt_text' => $_POST['alt_text'],
                    'popis' => $_POST['popis'],
                    'kategorie' => $_POST['kategorie'],
                    'aktivni' => isset($_POST['aktivni']) ? 1 : 0
                ];
                
                $db->update('galerie', $data, 'id = :id', ['id' => $_POST['id']]);
                $uspech = "Obrázek byl úspěšně aktualizován!";
                break;
                
            case 'smazat':
                $obrazek = $db->fetchOne("SELECT * FROM galerie WHERE id = :id", ['id' => $_POST['id']]);
                if ($obrazek) {
                    $cesta = UPLOAD_GALERIE_DIR . '/' . $obrazek['nazev_souboru'];
                    if (file_exists($cesta)) {
                        unlink($cesta);
                    }
                    $thumbnail = UPLOAD_THUMBNAILS_DIR . '/thumb_' . $obrazek['nazev_souboru'];
                    if (file_exists($thumbnail)) {
                        unlink($thumbnail);
                    }
                    
                    $db->delete('galerie', 'id = :id', ['id' => $_POST['id']]);
                    $uspech = "Obrázek byl smazán!";
                }
                break;
        }
    } catch (Exception $e) {
        $chyba = "Chyba: " . $e->getMessage();
    }
}

// Načtení kategorií galerie z databáze
try {
    $kategorie_db = $db->fetchAll("SELECT * FROM kategorie_galerie WHERE aktivni = 1 ORDER BY poradi, nazev");
    $kategorie_seznam = [];
    foreach ($kategorie_db as $kat) {
        $kategorie_seznam[$kat['id']] = $kat['nazev'];
    }
} catch (Exception $e) {
    $kategorie_seznam = [];
    echo "<div class='chyba'>Chyba při načítání kategorií: " . h($e->getMessage()) . "</div>";
}

$filtr_kategorie = $_GET['kategorie'] ?? '';

switch ($akce) {
    case 'nahrat':
        ?>
        <h2><i class="fas fa-upload"></i> Nahrát obrázky do galerie</h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="akce" value="nahrat">
            
            <div style="max-width: 600px;">
                <div class="form-group">
                    <label for="obrazky">Vyberte obrázky *</label>
                    <input type="file" id="obrazky" name="obrazky[]" multiple accept="image/*" required>
                    <small>Můžete vybrat více obrázků najednou (Ctrl+klik)</small>
                </div>
                
                <div class="form-group">
                    <label for="kategorie_nahrani">Kategorie</label>
                    <select id="kategorie_nahrani" name="kategorie">
                        <option value="">-- Vyberte kategorii --</option>
                        <?php foreach ($kategorie_seznam as $hodnota => $nazev): ?>
                            <option value="<?php echo h($hodnota); ?>"><?php echo h($nazev); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nova_kategorie_galerie">Nebo vytvořit novou kategorii</label>
                    <input type="text" id="nova_kategorie_galerie" name="nova_kategorie_galerie" 
                           placeholder="Název nové kategorie (pokud nevyberete existující)">
                    <small>Pokud vyplníte toto pole, vytvoří se nová kategorie</small>
                </div>
                
                <div class="form-group">
                    <label for="popis">Popis (volitelné)</label>
                    <textarea id="popis" name="popis" rows="3" 
                              placeholder="Společný popis pro všechny nahrávané obrázky"></textarea>
                </div>
                
                <div class="akce-tlacitka">
                    <button type="submit" class="admin-button btn-success">
                        <i class="fas fa-upload"></i> Nahrát obrázky
                    </button>
                    <a href="?sekce=galerie" class="admin-button">
                        <i class="fas fa-arrow-left"></i> Zpět na seznam
                    </a>
                </div>
            </div>
        </form>
        <?php
        break;
        
    case 'upravit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="chyba">Chybí ID obrázku!</div>';
            return;
        }
        
        $obrazek = $db->fetchOne("SELECT * FROM galerie WHERE id = :id", ['id' => $id]);
        if (!$obrazek) {
            echo '<div class="chyba">Obrázek nenalezen!</div>';
            return;
        }
        ?>
        
        <h2><i class="fas fa-edit"></i> Upravit obrázek</h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <form method="post">
                    <input type="hidden" name="akce" value="upravit">
                    <input type="hidden" name="id" value="<?php echo $obrazek['id']; ?>">
                    
                    <div class="form-group">
                        <label for="nazev">Název obrázku</label>
                        <input type="text" id="nazev" name="nazev" 
                               value="<?php echo h($obrazek['nazev']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="alt_text">Alt text (SEO)</label>
                        <input type="text" id="alt_text" name="alt_text" 
                               value="<?php echo h($obrazek['alt_text']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="kategorie_editace">Kategorie</label>
                        <select id="kategorie_editace" name="kategorie">
                            <?php foreach ($kategorie_seznam as $hodnota => $nazev): ?>
                                <option value="<?php echo h($hodnota); ?>" 
                                        <?php echo $obrazek['kategorie'] === $hodnota ? 'selected' : ''; ?>>
                                    <?php echo h($nazev); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="popis">Popis</label>
                        <textarea id="popis" name="popis" rows="4"><?php echo h($obrazek['popis']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="aktivni" 
                                   <?php echo $obrazek['aktivni'] ? 'checked' : ''; ?>>
                            Obrázek je aktivní
                        </label>
                    </div>
                    
                    <div class="akce-tlacitka">
                        <button type="submit" class="admin-button btn-success">
                            <i class="fas fa-save"></i> Uložit změny
                        </button>
                        <a href="?sekce=galerie" class="admin-button">
                            <i class="fas fa-arrow-left"></i> Zpět na seznam
                        </a>
                    </div>
                </form>
            </div>
            
            <div>
                <h3>Náhled obrázku</h3>
                <img src="../<?php echo WEB_UPLOAD_GALERIE_DIR . '/' . h($obrazek['nazev_souboru']); ?>" 
                     alt="<?php echo h($obrazek['alt_text']); ?>" 
                     style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                
                <div style="margin-top: 15px; font-size: 14px; color: #666;">
                    <p><strong>Soubor:</strong> <?php echo h($obrazek['nazev_souboru']); ?></p>
                    <p><strong>Nahráno:</strong> <?php echo date('d.m.Y H:i', strtotime($obrazek['datum_nahrany'])); ?></p>
                </div>
            </div>
        </div>
        <?php
        break;
        
    default: // seznam
        try {
            $where = '';
            $params = [];
            
            if ($filtr_kategorie) {
                $where = 'WHERE kategorie = :kategorie';
                $params['kategorie'] = $filtr_kategorie;
            }
            
            $obrazky = $db->fetchAll("
                SELECT * FROM galerie 
                $where
                ORDER BY kategorie, poradi, datum_nahrany DESC
            ", $params);
            
            // Statistiky
            $stats = $db->fetchAll("
                SELECT kategorie, COUNT(*) as pocet 
                FROM galerie 
                WHERE aktivni = 1 
                GROUP BY kategorie
            ");
            
        } catch (Exception $e) {
            echo '<div class="chyba">Chyba při načítání galerie: ' . h($e->getMessage()) . '</div>';
            return;
        }
        ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-images"></i> Správa galerie</h2>
            <a href="?sekce=galerie&akce=nahrat" class="admin-button btn-success">
                <i class="fas fa-upload"></i> Nahrát obrázky
            </a>
        </div>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <!-- Statistiky kategorií -->
        <?php if (!empty($stats)): ?>
            <div class="statistiky">
                <?php foreach ($stats as $stat): ?>
                    <div class="stat-karta">
                        <div class="stat-cislo"><?php echo $stat['pocet']; ?></div>
                        <div class="stat-popis"><?php echo $kategorie_seznam[$stat['kategorie']] ?? $stat['kategorie']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Filtrování -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h3><i class="fas fa-filter"></i> Filtrování</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <a href="?sekce=galerie" class="admin-button <?php echo !$filtr_kategorie ? 'active' : ''; ?>">
                    Všechny kategorie
                </a>
                <?php foreach ($kategorie_seznam as $hodnota => $nazev): ?>
                    <a href="?sekce=galerie&kategorie=<?php echo urlencode($hodnota); ?>" 
                       class="admin-button <?php echo $filtr_kategorie === $hodnota ? 'active' : ''; ?>">
                        <?php echo $nazev; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (empty($obrazky)): ?>
            <div class="info">
                <h3>Žádné obrázky</h3>
                <p>
                    <?php if ($filtr_kategorie): ?>
                        V kategorii "<?php echo h($kategorie_seznam[$filtr_kategorie] ?? $filtr_kategorie); ?>" nejsou žádné obrázky.
                    <?php else: ?>
                        Zatím nemáte nahrané žádné obrázky do galerie.
                    <?php endif; ?>
                </p>
                <a href="?sekce=galerie&akce=nahrat" class="admin-button btn-success">
                    <i class="fas fa-upload"></i> Nahrát první obrázky
                </a>
            </div>
        <?php else: ?>
            <!-- Grid zobrazení obrázků -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                <?php 
                $aktualni_kategorie = '';
                foreach ($obrazky as $obrazek): 
                    if ($obrazek['kategorie'] !== $aktualni_kategorie && !$filtr_kategorie):
                        $aktualni_kategorie = $obrazek['kategorie'];
                        echo '<div style="grid-column: 1 / -1; margin: 20px 0 10px 0;">';
                        echo '<h3 style="color: #834912; border-bottom: 2px solid #834912; padding-bottom: 10px;">';
                        echo '<i class="fas fa-folder"></i> ' . h($kategorie_seznam[$aktualni_kategorie] ?? $aktualni_kategorie);
                        echo '</h3></div>';
                    endif;
                ?>
                    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <div style="position: relative;">
                            <img src="../<?php echo WEB_UPLOAD_THUMBNAILS_DIR . '/thumb_' . h($obrazek['nazev_souboru']); ?>" 
                                 alt="<?php echo h($obrazek['alt_text']); ?>" 
                                 style="width: 100%; height: 180px; object-fit: cover;">
                            
                            <?php if (!$obrazek['aktivni']): ?>
                                <div style="position: absolute; top: 10px; right: 10px; background: #f44336; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    Neaktivní
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="padding: 15px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 14px;">
                                <?php echo h($obrazek['nazev'] ?: 'Bez názvu'); ?>
                            </h4>
                            
                            <?php if ($obrazek['popis']): ?>
                                <p style="margin: 0 0 10px 0; font-size: 12px; color: #666; line-height: 1.4;">
                                    <?php echo h(substr($obrazek['popis'], 0, 80)); ?><?php echo strlen($obrazek['popis']) > 80 ? '...' : ''; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div style="font-size: 11px; color: #999; margin-bottom: 15px;">
                                Nahráno: <?php echo date('d.m.Y', strtotime($obrazek['datum_nahrany'])); ?>
                            </div>
                            
                            <div style="display: flex; gap: 5px; justify-content: center; margin-top: 10px;">
                                <a href="?sekce=galerie&akce=upravit&id=<?php echo $obrazek['id']; ?>" 
                                   style="background: #f57c00; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                    ✏️
                                </a>
                                <a href="../<?php echo WEB_UPLOAD_GALERIE_DIR . '/' . h($obrazek['nazev_souboru']); ?>" 
                                   target="_blank"
                                   style="background: #2196f3; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                    👁️
                                </a>
                                <button onclick="smazatObrazek(<?php echo $obrazek['id']; ?>, '<?php echo h($obrazek['nazev']); ?>')" 
                                        style="background: #f44336; color: white; padding: 6px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px;">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Modal pro smazání -->
        <div id="smazat-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('smazat-modal')">&times;</span>
                <h3>Smazat obrázek</h3>
                <p>Opravdu chcete smazat obrázek <strong id="nazev-obrazku"></strong>?</p>
                <p style="color: red;"><strong>Tato akce je nevratná!</strong></p>
                
                <form method="post" id="smazat-form">
                    <input type="hidden" name="akce" value="smazat">
                    <input type="hidden" name="id" id="id-obrazku">
                    <div class="akce-tlacitka">
                        <button type="submit" class="admin-button btn-danger">
                            <i class="fas fa-trash"></i> Smazat obrázek
                        </button>
                        <button type="button" onclick="closeModal('smazat-modal')" class="admin-button">
                            Zrušit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
            function smazatObrazek(id, nazev) {
                document.getElementById('nazev-obrazku').textContent = nazev || 'Bez názvu';
                document.getElementById('id-obrazku').value = id;
                openModal('smazat-modal');
            }
        </script>
        
        <?php
        break;
}
?>