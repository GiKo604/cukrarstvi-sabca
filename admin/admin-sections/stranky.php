<?php
// Správa stránek s WYSIWYG editorem

if (!$db) {
    echo '<div class="chyba">Databáze není k dispozici.</div>';
    return;
}

$akce = $_GET['akce'] ?? 'seznam';
$id = $_GET['id'] ?? null;

// Zpracování formulářů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['akce'] ?? '') {
            case 'pridat':
                $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $_POST['slug']);
                $data = [
                    'slug' => $slug,
                    'nazev' => $_POST['nazev'],
                    'menu_nazev' => $_POST['menu_nazev'],
                    'obsah' => $_POST['obsah'],
                    'meta_popis' => $_POST['meta_popis'],
                    'meta_klicova_slova' => $_POST['meta_klicova_slova'],
                    'poradi' => (int)$_POST['poradi']
                ];
                
                $db->insert('stranky', $data);
                $uspech = "Stránka byla úspěšně vytvořena!";
                $akce = 'seznam';
                break;
                
            case 'upravit':
                $data = [
                    'nazev' => $_POST['nazev'],
                    'menu_nazev' => $_POST['menu_nazev'],
                    'obsah' => $_POST['obsah'],
                    'meta_popis' => $_POST['meta_popis'],
                    'meta_klicova_slova' => $_POST['meta_klicova_slova'],
                    'poradi' => (int)$_POST['poradi'],
                    'aktivni' => isset($_POST['aktivni']) ? 1 : 0
                ];
                
                $db->update('stranky', $data, 'slug = :slug', ['slug' => $id]);
                $uspech = "Stránka byla úspěšně aktualizována!";
                break;
                
            case 'smazat':
                $db->delete('stranky', 'slug = :slug', ['slug' => $_POST['slug']]);
                $uspech = "Stránka byla smazána!";
                $akce = 'seznam';
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
        // Načtení dat pro úpravu
        $stranka = null;
        if ($akce === 'upravit' && $id) {
            $stranka = $db->fetchOne("SELECT * FROM stranky WHERE slug = :slug", ['slug' => $id]);
            if (!$stranka) {
                echo '<div class="chyba">Stránka nenalezena!</div>';
                return;
            }
        }
        ?>
        
        <h2>
            <i class="fas fa-<?php echo $akce === 'pridat' ? 'plus' : 'edit'; ?>"></i>
            <?php echo $akce === 'pridat' ? 'Přidat novou stránku' : 'Upravit stránku'; ?>
        </h2>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="akce" value="<?php echo $akce; ?>">
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <!-- Základní informace -->
                    <div class="form-group">
                        <label for="nazev">Název stránky *</label>
                        <input type="text" id="nazev" name="nazev" 
                               value="<?php echo h($stranka['nazev'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="menu_nazev">Název v menu *</label>
                        <input type="text" id="menu_nazev" name="menu_nazev" 
                               value="<?php echo h($stranka['menu_nazev'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <?php if ($akce === 'pridat'): ?>
                    <div class="form-group">
                        <label for="slug">URL slug *</label>
                        <input type="text" id="slug" name="slug" 
                               placeholder="napr-moje-stranka" 
                               pattern="[a-zA-Z0-9\-_]+" 
                               required>
                        <small>Pouze písmena, čísla, pomlčky a podtržítka</small>
                    </div>
                    <?php endif; ?>
                    
                    <!-- WYSIWYG Editor -->
                    <div class="form-group">
                        <label for="obsah">Obsah stránky</label>
                        <textarea id="obsah" name="obsah" class="wysiwyg-editor">
                            <?php echo h($stranka['obsah'] ?? ''); ?>
                        </textarea>
                    </div>
                </div>
                
                <div>
                    <!-- SEO a nastavení -->
                    <div class="form-group">
                        <label for="poradi">Pořadí v menu</label>
                        <input type="number" id="poradi" name="poradi" 
                               value="<?php echo h($stranka['poradi'] ?? '0'); ?>" 
                               min="0">
                    </div>
                    
                    <?php if ($akce === 'upravit'): ?>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="aktivni" 
                                   <?php echo ($stranka['aktivni'] ?? 1) ? 'checked' : ''; ?>>
                            Stránka je aktivní
                        </label>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="meta_popis">Meta popis (SEO)</label>
                        <textarea id="meta_popis" name="meta_popis" rows="3" 
                                  placeholder="Krátký popis stránky pro vyhledávače"><?php echo h($stranka['meta_popis'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_klicova_slova">Klíčová slova (SEO)</label>
                        <input type="text" id="meta_klicova_slova" name="meta_klicova_slova" 
                               value="<?php echo h($stranka['meta_klicova_slova'] ?? ''); ?>" 
                               placeholder="slovo1, slovo2, slovo3">
                    </div>
                    
                    <!-- Akční tlačítka -->
                    <div class="akce-tlacitka" style="margin-top: 30px;">
                        <button type="submit" class="admin-button btn-success">
                            <i class="fas fa-save"></i> 
                            <?php echo $akce === 'pridat' ? 'Vytvořit stránku' : 'Uložit změny'; ?>
                        </button>
                        <a href="?sekce=stranky" class="admin-button">
                            <i class="fas fa-arrow-left"></i> Zpět na seznam
                        </a>
                        <?php if ($akce === 'upravit' && $stranka): ?>
                        <a href="../index.php?id-stranky=<?php echo h($stranka['slug']); ?>" 
                           class="admin-button btn-info" target="_blank">
                            <i class="fas fa-eye"></i> Zobrazit stránku
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
        
        <?php
        break;
        
    default: // seznam
        try {
            $stranky = $db->fetchAll("
                SELECT * FROM stranky 
                ORDER BY poradi ASC, nazev ASC
            ");
        } catch (Exception $e) {
            echo '<div class="chyba">Chyba při načítání stránek: ' . h($e->getMessage()) . '</div>';
            return;
        }
        ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-file-alt"></i> Správa stránek</h2>
            <a href="?sekce=stranky&akce=pridat" class="admin-button btn-success">
                <i class="fas fa-plus"></i> Přidat novou stránku
            </a>
        </div>
        
        <?php if (isset($uspech)): ?>
            <div class="uspech"><?php echo $uspech; ?></div>
        <?php endif; ?>
        
        <?php if (isset($chyba)): ?>
            <div class="chyba"><?php echo $chyba; ?></div>
        <?php endif; ?>
        
        <?php if (empty($stranky)): ?>
            <div class="info">
                <h3>Žádné stránky</h3>
                <p>Zatím nemáte vytvořené žádné stránky.</p>
                <a href="?sekce=stranky&akce=pridat" class="admin-button btn-success">
                    <i class="fas fa-plus"></i> Vytvořit první stránku
                </a>
            </div>
        <?php else: ?>
            <table class="tabulka">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>URL slug</th>
                        <th>Menu</th>
                        <th>Pořadí</th>
                        <th>Stav</th>
                        <th>Upraveno</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stranky as $stranka): ?>
                        <tr>
                            <td>
                                <strong><?php echo h($stranka['nazev']); ?></strong>
                                <?php if ($stranka['meta_popis']): ?>
                                    <br><small style="color: #666;"><?php echo h(substr($stranka['meta_popis'], 0, 80)); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code><?php echo h($stranka['slug']); ?></code>
                            </td>
                            <td><?php echo h($stranka['menu_nazev']); ?></td>
                            <td><?php echo $stranka['poradi']; ?></td>
                            <td>
                                <?php if ($stranka['aktivni']): ?>
                                    <span style="color: green; font-weight: bold;">✓ Aktivní</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">✗ Neaktivní</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($stranka['datum_upravy'])); ?></td>
                            <td style="white-space: nowrap; width: 140px;">
                                <div style="display: flex; gap: 5px; flex-wrap: nowrap;">
                                    <a href="?sekce=stranky&akce=upravit&id=<?php echo urlencode($stranka['slug']); ?>" 
                                       style="background: #f57c00; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        ✏️
                                    </a>
                                    <a href="../index.php?id-stranky=<?php echo urlencode($stranka['slug']); ?>" 
                                       target="_blank"
                                       style="background: #2196f3; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                        👁️
                                    </a>
                                    <button onclick="smazatStranku('<?php echo h($stranka['slug']); ?>', '<?php echo h($stranka['nazev']); ?>')" 
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
        
        <!-- Modal pro smazání -->
        <div id="smazat-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('smazat-modal')">&times;</span>
                <h3>Smazat stránku</h3>
                <p>Opravdu chcete smazat stránku <strong id="nazev-stranky"></strong>?</p>
                <p style="color: red;"><strong>Tato akce je nevratná!</strong></p>
                
                <form method="post" id="smazat-form">
                    <input type="hidden" name="akce" value="smazat">
                    <input type="hidden" name="slug" id="slug-stranky">
                    <div class="akce-tlacitka">
                        <button type="submit" class="admin-button btn-danger">
                            <i class="fas fa-trash"></i> Smazat stránku
                        </button>
                        <button type="button" onclick="closeModal('smazat-modal')" class="admin-button">
                            Zrušit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
            function smazatStranku(slug, nazev) {
                document.getElementById('nazev-stranky').textContent = nazev;
                document.getElementById('slug-stranky').value = slug;
                openModal('smazat-modal');
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