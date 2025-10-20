<?php
// Správa receptů s upload obrázků

if (!$db) {
    echo '<div class="chyba">Databáze není k dispozici.</div>';
    return;
}

$akce = $_GET['akce'] ?? 'seznam';
$id = $_GET['id'] ?? null;

// Zpracování upload obrázků
function zpracovatUploadObrazku($receptId, $soubor) {
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
    $cilCesta = UPLOAD_RECEPTY_DIR . '/' . $nazevSouboru;
    
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
            case 'pridat':
                $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $_POST['slug']);
                
                // Upload hlavního obrázku
                $hlavniObrazek = null;
                if (isset($_FILES['hlavni_obrazek']) && $_FILES['hlavni_obrazek']['error'] === UPLOAD_ERR_OK) {
                    $hlavniObrazek = zpracovatUploadObrazku(null, $_FILES['hlavni_obrazek']);
                }
                
                // Zpracování nové kategorie
                $kategorieId = $_POST['kategorie_id'] ?: null;
                if (!empty($_POST['nova_kategorie']) && empty($kategorieId)) {
                    // Vytvoříme novou kategorii
                    $novaKategorie = trim($_POST['nova_kategorie']);
                    
                    // Kontrola, zda kategorie s tímto názvem už neexistuje
                    $existujici = $db->fetchOne("SELECT id FROM kategorie_receptu WHERE nazev = ?", [$novaKategorie]);
                    if (!$existujici) {
                        $db->query(
                            "INSERT INTO kategorie_receptu (nazev, barva, poradi, aktivni) VALUES (?, '#3498db', 1, 1)",
                            [$novaKategorie]
                        );
                        $kategorieId = $db->getConnection()->lastInsertId();
                    } else {
                        $kategorieId = $existujici['id'];
                    }
                }
                
                $data = [
                    'nazev' => $_POST['nazev'],
                    'slug' => $slug,
                    'popis' => $_POST['popis'],
                    'ingredience' => $_POST['ingredience'],
                    'postup' => $_POST['postup'],
                    'cas_pripravy' => (int)$_POST['cas_pripravy'],
                    'pocet_porci' => (int)$_POST['pocet_porci'],
                    'obtiznost' => $_POST['obtiznost'],
                    'kategorie_id' => $kategorieId,
                    'hlavni_obrazek' => $hlavniObrazek,
                    'meta_popis' => $_POST['meta_popis']
                ];
                
                $receptId = $db->insert('recepty', $data);
                
                // Upload dalších obrázků
                if (isset($_FILES['dalsi_obrazky'])) {
                    foreach ($_FILES['dalsi_obrazky']['tmp_name'] as $index => $tmpName) {
                        if ($_FILES['dalsi_obrazky']['error'][$index] === UPLOAD_ERR_OK) {
                            $soubor = [
                                'tmp_name' => $tmpName,
                                'name' => $_FILES['dalsi_obrazky']['name'][$index],
                                'size' => $_FILES['dalsi_obrazky']['size'][$index],
                                'error' => $_FILES['dalsi_obrazky']['error'][$index]
                            ];
                            
                            $nazevSouboru = zpracovatUploadObrazku($receptId, $soubor);
                            
                            $db->insert('obrazky_receptu', [
                                'recept_id' => $receptId,
                                'nazev_souboru' => $nazevSouboru,
                                'puvodni_nazev' => $_FILES['dalsi_obrazky']['name'][$index],
                                'poradi' => $index
                            ]);
                        }
                    }
                }
                
                $uspech = "Recept byl úspěšně vytvořen!";
                $akce = 'seznam';
                break;
                
            case 'upravit':
                // Zpracování nové kategorie
                $kategorieId = $_POST['kategorie_id'] ?: null;
                if (!empty($_POST['nova_kategorie']) && empty($kategorieId)) {
                    // Vytvoříme novou kategorii
                    $novaKategorie = trim($_POST['nova_kategorie']);
                    
                    // Kontrola, zda kategorie s tímto názvem už neexistuje
                    $existujici = $db->fetchOne("SELECT id FROM kategorie_receptu WHERE nazev = ?", [$novaKategorie]);
                    if (!$existujici) {
                        $db->query(
                            "INSERT INTO kategorie_receptu (nazev, barva, poradi, aktivni) VALUES (?, '#3498db', 1, 1)",
                            [$novaKategorie]
                        );
                        $kategorieId = $db->getConnection()->lastInsertId();
                    } else {
                        $kategorieId = $existujici['id'];
                    }
                }
                
                $data = [
                    'nazev' => $_POST['nazev'],
                    'popis' => $_POST['popis'],
                    'ingredience' => $_POST['ingredience'],
                    'postup' => $_POST['postup'],
                    'cas_pripravy' => (int)$_POST['cas_pripravy'],
                    'pocet_porci' => (int)$_POST['pocet_porci'],
                    'obtiznost' => $_POST['obtiznost'],
                    'kategorie_id' => $kategorieId,
                    'meta_popis' => $_POST['meta_popis'],
                    'aktivni' => isset($_POST['aktivni']) ? 1 : 0
                ];
                
                // Upload nového hlavního obrázku
                if (isset($_FILES['hlavni_obrazek']) && $_FILES['hlavni_obrazek']['error'] === UPLOAD_ERR_OK) {
                    $data['hlavni_obrazek'] = zpracovatUploadObrazku($id, $_FILES['hlavni_obrazek']);
                }
                
                $db->update('recepty', $data, 'slug = :slug', ['slug' => $id]);
                $uspech = "Recept byl úspěšně aktualizován!";
                break;
                
            case 'smazat':
                // Smazání obrázků
                $obrazky = $db->fetchAll("SELECT nazev_souboru FROM obrazky_receptu WHERE recept_id = (SELECT id FROM recepty WHERE slug = :slug)", ['slug' => $_POST['slug']]);
                foreach ($obrazky as $obrazek) {
                    $cesta = UPLOAD_RECEPTY_DIR . '/' . $obrazek['nazev_souboru'];
                    if (file_exists($cesta)) {
                        unlink($cesta);
                    }
                    $thumbnail = UPLOAD_THUMBNAILS_DIR . '/thumb_' . $obrazek['nazev_souboru'];
                    if (file_exists($thumbnail)) {
                        unlink($thumbnail);
                    }
                }
                
                $db->delete('recepty', 'slug = :slug', ['slug' => $_POST['slug']]);
                $uspech = "Recept byl smazán!";
                $akce = 'seznam';
                break;
                
            case 'smazat_obrazek':
                $obrazek = $db->fetchOne("SELECT * FROM obrazky_receptu WHERE id = :id", ['id' => $_POST['obrazek_id']]);
                if ($obrazek) {
                    $cesta = UPLOAD_RECEPTY_DIR . '/' . $obrazek['nazev_souboru'];
                    if (file_exists($cesta)) {
                        unlink($cesta);
                    }
                    $thumbnail = UPLOAD_THUMBNAILS_DIR . '/thumb_' . $obrazek['nazev_souboru'];
                    if (file_exists($thumbnail)) {
                        unlink($thumbnail);
                    }
                    
                    $db->delete('obrazky_receptu', 'id = :id', ['id' => $_POST['obrazek_id']]);
                    $uspech = "Obrázek byl smazán!";
                }
                break;
        }
    } catch (Exception $e) {
        $chyba = "Chyba: " . $e->getMessage();
    }
}

// Zobrazení podle akce
switch ($akce) {
    case 'pridat':
    case 'upravit':
        // Načtení kategorií - původní dotaz s debug informacemi
        try {
            // Použijeme GROUP BY pro odstranění duplikátů podle názvu
            $kategorie = $db->fetchAll("
                SELECT * FROM kategorie_receptu 
                WHERE aktivni = 1 
                GROUP BY nazev
                ORDER BY poradi, nazev
            ");
        } catch (Exception $e) {
            $kategorie = [];
            echo "<div class='chyba'>Chyba při načítání kategorií: " . h($e->getMessage()) . "</div>";
        }
        
        // Načtení dat pro úpravu
        $recept = null;
        $obrazky = [];
        if ($akce === 'upravit' && $id) {
            $recept = $db->fetchOne("SELECT * FROM recepty WHERE slug = :slug", ['slug' => $id]);
            if (!$recept) {
                echo '<div class="chyba">Recept nenalezen!</div>';
                return;
            }
            
            $obrazky = $db->fetchAll("SELECT * FROM obrazky_receptu WHERE recept_id = :id ORDER BY poradi", ['id' => $recept['id']]);
        }
        ?>
        
        <h2>
            <i class="fas fa-<?php echo $akce === 'pridat' ? 'plus' : 'edit'; ?>"></i>
            <?php echo $akce === 'pridat' ? 'Přidat nový recept' : 'Upravit recept'; ?>
        </h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="akce" value="<?php echo $akce; ?>">
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <!-- Základní informace -->
                    <div class="form-group">
                        <label for="nazev">Název receptu *</label>
                        <input type="text" id="nazev" name="nazev" 
                               value="<?php echo h($recept['nazev'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <?php if ($akce === 'pridat'): ?>
                    <div class="form-group">
                        <label for="slug">URL slug *</label>
                        <input type="text" id="slug" name="slug" 
                               placeholder="napr-cokoladovy-dort" 
                               pattern="[a-zA-Z0-9\-_]+" 
                               required>
                        <small>Pouze písmena, čísla, pomlčky a podtržítka</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="popis">Krátký popis</label>
                        <textarea id="popis" name="popis" rows="3"><?php echo h($recept['popis'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Ingredience -->
                    <div class="form-group">
                        <label for="ingredience">Ingredience *</label>
                        <textarea id="ingredience" name="ingredience" class="wysiwyg-editor" required>
                            <?php echo h($recept['ingredience'] ?? ''); ?>
                        </textarea>
                        <small>Použijte HTML seznam (&lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;)</small>
                    </div>
                    
                    <!-- Postup -->
                    <div class="form-group">
                        <label for="postup">Postup přípravy *</label>
                        <textarea id="postup" name="postup" class="wysiwyg-editor" required>
                            <?php echo h($recept['postup'] ?? ''); ?>
                        </textarea>
                        <small>Použijte číslovaný seznam (&lt;ol&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ol&gt;)</small>
                    </div>
                </div>
                
                <div>
                    <!-- Kategorie a parametry -->
                    <div class="form-group">
                        <label for="kategorie_id">Kategorie</label>
                        <select id="kategorie_id" name="kategorie_id">
                            <option value="">-- Vyberte kategorii --</option>
                            <?php foreach ($kategorie as $kat): ?>
                                <option value="<?php echo $kat['id']; ?>" 
                                        <?php echo ($recept['kategorie_id'] ?? '') == $kat['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($kat['nazev']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_kategorie">Nebo vytvořit novou kategorii</label>
                        <input type="text" id="nova_kategorie" name="nova_kategorie" 
                               placeholder="Název nové kategorie (pokud nevyberete existující)">
                        <small>Pokud vyplníte toto pole, vytvoří se nová kategorie</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cas_pripravy">Čas přípravy (minuty)</label>
                        <input type="number" id="cas_pripravy" name="cas_pripravy" 
                               value="<?php echo h($recept['cas_pripravy'] ?? '30'); ?>" 
                               min="1" max="1440">
                    </div>
                    
                    <div class="form-group">
                        <label for="pocet_porci">Počet porcí</label>
                        <input type="number" id="pocet_porci" name="pocet_porci" 
                               value="<?php echo h($recept['pocet_porci'] ?? '4'); ?>" 
                               min="1" max="50">
                    </div>
                    
                    <div class="form-group">
                        <label for="obtiznost">Obtížnost</label>
                        <select id="obtiznost" name="obtiznost">
                            <option value="snadná" <?php echo ($recept['obtiznost'] ?? '') === 'snadná' ? 'selected' : ''; ?>>Snadná</option>
                            <option value="střední" <?php echo ($recept['obtiznost'] ?? 'střední') === 'střední' ? 'selected' : ''; ?>>Střední</option>
                            <option value="těžká" <?php echo ($recept['obtiznost'] ?? '') === 'těžká' ? 'selected' : ''; ?>>Těžká</option>
                        </select>
                    </div>
                    
                    <?php if ($akce === 'upravit'): ?>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="aktivni" 
                                   <?php echo ($recept['aktivni'] ?? 1) ? 'checked' : ''; ?>>
                            Recept je aktivní
                        </label>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Upload obrázků -->
                    <div class="form-group">
                        <label for="hlavni_obrazek">Hlavní obrázek</label>
                        <input type="file" id="hlavni_obrazek" name="hlavni_obrazek" accept="image/*">
                        <?php if ($akce === 'upravit' && $recept['hlavni_obrazek']): ?>
                            <div style="margin-top: 10px;">
                                <img src="../<?php echo WEB_UPLOAD_THUMBNAILS_DIR . '/thumb_' . h($recept['hlavni_obrazek']); ?>" 
                                     alt="Současný obrázek" style="max-width: 150px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($akce === 'pridat'): ?>
                    <div class="form-group">
                        <label for="dalsi_obrazky">Další obrázky</label>
                        <input type="file" id="dalsi_obrazky" name="dalsi_obrazky[]" multiple accept="image/*">
                        <small>Můžete vybrat více obrázků najednou</small>
                    </div>
                    <?php endif; ?>
                    
                    <!-- SEO -->
                    <div class="form-group">
                        <label for="meta_popis">Meta popis (SEO)</label>
                        <textarea id="meta_popis" name="meta_popis" rows="3" 
                                  placeholder="Krátký popis receptu pro vyhledávače"><?php echo h($recept['meta_popis'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Akční tlačítka -->
                    <div class="akce-tlacitka" style="margin-top: 30px;">
                        <button type="submit" class="admin-button btn-success">
                            <i class="fas fa-save"></i> 
                            <?php echo $akce === 'pridat' ? 'Vytvořit recept' : 'Uložit změny'; ?>
                        </button>
                        <a href="?sekce=recepty" class="admin-button">
                            <i class="fas fa-arrow-left"></i> Zpět na seznam
                        </a>
                        <?php if ($akce === 'upravit' && $recept): ?>
                        <a href="index.php?id-stranky=recepty#recept-<?php echo h($recept['slug']); ?>" 
                           class="admin-button" target="_blank">
                            <i class="fas fa-eye"></i> Zobrazit recept
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Správa dalších obrázků při úpravě -->
        <?php if ($akce === 'upravit' && !empty($obrazky)): ?>
            <div style="margin-top: 40px;">
                <h3><i class="fas fa-images"></i> Další obrázky</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                    <?php foreach ($obrazky as $obrazek): ?>
                        <div style="background: white; padding: 15px; border-radius: 8px; text-align: center;">
                            <img src="../<?php echo WEB_UPLOAD_THUMBNAILS_DIR . '/thumb_' . h($obrazek['nazev_souboru']); ?>" 
                                 alt="<?php echo h($obrazek['alt_text']); ?>" 
                                 style="max-width: 100%; height: 120px; object-fit: cover; border-radius: 4px;"
                                 onerror="console.log('Image failed to load:', this.src); this.style.border='2px solid red';">
                            <p style="margin: 10px 0 5px 0; font-size: 12px;">
                                <?php echo h($obrazek['puvodni_nazev']); ?>
                            </p>
                            <button onclick="smazatObrazek(<?php echo $obrazek['id']; ?>, '<?php echo h($obrazek['puvodni_nazev']); ?>')" 
                                    class="admin-button btn-danger btn-small">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        break;
        
    default: // seznam
        try {
            $recepty = $db->fetchAll("
                SELECT r.*, k.nazev as kategorie_nazev, k.barva as kategorie_barva
                FROM recepty r
                LEFT JOIN kategorie_receptu k ON r.kategorie_id = k.id
                ORDER BY r.datum_vytvoreni DESC
            ");
        } catch (Exception $e) {
            echo '<div class="chyba">Chyba při načítání receptů: ' . h($e->getMessage()) . '</div>';
            return;
        }
        ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-utensils"></i> Správa receptů</h2>
            <a href="?sekce=recepty&akce=pridat" class="admin-button btn-success">
                <i class="fas fa-plus"></i> Přidat nový recept
            </a>
        </div>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <?php if (empty($recepty)): ?>
            <div class="info">
                <h3>Žádné recepty</h3>
                <p>Zatím nemáte vytvořené žádné recepty.</p>
                <a href="?sekce=recepty&akce=pridat" class="admin-button btn-success">
                    <i class="fas fa-plus"></i> Vytvořit první recept
                </a>
            </div>
        <?php else: ?>
            <table class="tabulka">
                <thead>
                    <tr>
                        <th>Obrázek</th>
                        <th>Název</th>
                        <th>Kategorie</th>
                        <th>Čas/Porce</th>
                        <th>Obtížnost</th>
                        <th>Stav</th>
                        <th>Vytvořeno</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recepty as $recept): ?>
                        <tr>
                            <td>
                                <?php if ($recept['hlavni_obrazek']): ?>
                                <img src="../<?php echo WEB_UPLOAD_THUMBNAILS_DIR . '/thumb_' . h($recept['hlavni_obrazek']); ?>" 
                                     alt="<?php echo h($recept['nazev']); ?>"
                                     style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: #ccc;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo h($recept['nazev']); ?></strong>
                                <?php if ($recept['popis']): ?>
                                    <br><small style="color: #666;"><?php echo h(substr($recept['popis'], 0, 50)); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($recept['kategorie_nazev']): ?>
                                    <span style="background: <?php echo h($recept['kategorie_barva']); ?>; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                                        <?php echo h($recept['kategorie_nazev']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999;">Bez kategorie</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <i class="fas fa-clock"></i> <?php echo $recept['cas_pripravy']; ?> min<br>
                                <i class="fas fa-users"></i> <?php echo $recept['pocet_porci']; ?> porcí
                            </td>
                            <td>
                                <?php
                                $obtiznost_barvy = [
                                    'snadná' => '#4caf50',
                                    'střední' => '#ff9800',
                                    'těžká' => '#f44336'
                                ];
                                $barva = $obtiznost_barvy[$recept['obtiznost']] ?? '#999';
                                ?>
                                <span style="color: <?php echo $barva; ?>; font-weight: bold;">
                                    <?php echo ucfirst($recept['obtiznost']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($recept['aktivni']): ?>
                                    <span style="color: green; font-weight: bold;">✓ Aktivní</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">✗ Neaktivní</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y', strtotime($recept['datum_vytvoreni'])); ?></td>
                            <td style="white-space: nowrap; width: 140px;">
                                <div style="display: flex; gap: 5px; flex-wrap: nowrap;">
                                    <a href="?sekce=recepty&akce=upravit&id=<?php echo urlencode($recept['slug']); ?>" 
                                       style="background: #f57c00; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        ✏️
                                    </a>
                                    <a href="index.php?id-stranky=recepty#recept-<?php echo urlencode($recept['slug']); ?>" 
                                       target="_blank"
                                       style="background: #2196f3; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        👁️
                                    </a>
                                    <button onclick="smazatRecept('<?php echo h($recept['slug']); ?>', '<?php echo h($recept['nazev']); ?>')" 
                                            style="background: #f44336; color: white; padding: 6px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px;">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <!-- Modal pro smazání receptu -->
        <div id="smazat-recept-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('smazat-recept-modal')">&times;</span>
                <h3>Smazat recept</h3>
                <p>Opravdu chcete smazat recept <strong id="nazev-receptu"></strong>?</p>
                <p style="color: red;"><strong>Tato akce je nevratná a smaže i všechny přiřazené obrázky!</strong></p>
                
                <form method="post" id="smazat-recept-form">
                    <input type="hidden" name="akce" value="smazat">
                    <input type="hidden" name="slug" id="slug-receptu">
                    <div class="akce-tlacitka">
                        <button type="submit" class="admin-button btn-danger">
                            <i class="fas fa-trash"></i> Smazat recept
                        </button>
                        <button type="button" onclick="closeModal('smazat-recept-modal')" class="admin-button">
                            Zrušit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Modal pro smazání obrázku -->
        <div id="smazat-obrazek-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('smazat-obrazek-modal')">&times;</span>
                <h3>Smazat obrázek</h3>
                <p>Opravdu chcete smazat obrázek <strong id="nazev-obrazku"></strong>?</p>
                
                <form method="post" id="smazat-obrazek-form">
                    <input type="hidden" name="akce" value="smazat_obrazek">
                    <input type="hidden" name="obrazek_id" id="id-obrazku">
                    <div class="akce-tlacitka">
                        <button type="submit" class="admin-button btn-danger">
                            <i class="fas fa-trash"></i> Smazat obrázek
                        </button>
                        <button type="button" onclick="closeModal('smazat-obrazek-modal')" class="admin-button">
                            Zrušit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
            function smazatRecept(slug, nazev) {
                document.getElementById('nazev-receptu').textContent = nazev;
                document.getElementById('slug-receptu').value = slug;
                openModal('smazat-recept-modal');
            }
            
            function smazatObrazek(id, nazev) {
                document.getElementById('nazev-obrazku').textContent = nazev;
                document.getElementById('id-obrazku').value = id;
                openModal('smazat-obrazek-modal');
            }
            
            // Automatické generování slug z názvu
            document.getElementById('nazev')?.addEventListener('input', function() {
                const slugInput = document.getElementById('slug');
                if (slugInput && !slugInput.dataset.manual) {
                    let slug = this.value
                        .toLowerCase()
                        .replace(/[áäâà]/g, 'a')
                        .replace(/[éěëê]/g, 'e')
                        .replace(/[íìî]/g, 'i')
                        .replace(/[óöôò]/g, 'o')
                        .replace(/[úůüûù]/g, 'u')
                        .replace(/[ýÿ]/g, 'y')
                        .replace(/č/g, 'c')
                        .replace(/ď/g, 'd')
                        .replace(/ň/g, 'n')
                        .replace(/ř/g, 'r')
                        .replace(/š/g, 's')
                        .replace(/ť/g, 't')
                        .replace(/ž/g, 'z')
                        .replace(/[^a-z0-9]/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    slugInput.value = slug;
                }
            });
            
            // Označit slug jako manuálně upravený
            document.getElementById('slug')?.addEventListener('input', function() {
                this.dataset.manual = 'true';
            });
        </script>
        
        <?php
        break;
}
?>