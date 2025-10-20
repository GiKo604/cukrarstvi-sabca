<?php
// Načtení databázové konfigurace
require_once 'config/databaze-config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Načtení aktivních receptů s kategoriemi
    $stmt = $pdo->query("
        SELECT r.*, k.nazev as kategorie_nazev, k.barva as kategorie_barva 
        FROM recepty r 
        LEFT JOIN kategorie_receptu k ON r.kategorie_id = k.id 
        WHERE r.aktivni = 1 
        ORDER BY r.datum_vytvoreni DESC
    ");
    $recepty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $recepty = [];
}

function h($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<main>
    <div class="recepty-container">
        
        <?php if (empty($recepty)): ?>
            <!-- Pokud nejsou žádné recepty z databáze, zobrazíme statické -->
            <div class="recept-info-box">
                <h2><i class="fas fa-info-circle"></i> Zatím žádné recepty</h2>
                <p>Recepty se zobrazí po jejich přidání v admin rozhraní.</p>
                <p><strong>Pro administrátory:</strong> Přidejte recepty v <a href="admin/admin-new.php?sekce=recepty" target="_blank">admin rozhraní</a>.</p>
            </div>
            
            <!-- Zachováme původní statické recepty jako ukázku -->
            <div class="recept-karta demo-recept" id="cokoladovy-dort">
                <img src="img/Middle/first.jpeg" alt="Čokoládový dort" class="recept-obrazek">
                <div class="recept-obsah">
                    <div class="demo-badge">DEMO RECEPT</div>
                    <h2>Čokoládový dort</h2>
                    <div class="recept-info">
                        <span><i class="fas fa-clock"></i> 60 min</span>
                        <span><i class="fas fa-users"></i> 8 porcí</span>
                    </div>
                    <h3>Ingredience:</h3>
                    <ul>
                        <li>200g tmavé čokolády</li>
                        <li>150g másla</li>
                        <li>150g cukru</li>
                        <li>3 vejce</li>
                        <li>100g mouky</li>
                        <li>250ml smetany na šlehání</li>
                    </ul>
                    <h3>Postup:</h3>
                    <ol>
                        <li>Rozehřejte troubu na 180°C. Vymažte a vysypte formu na dort.</li>
                        <li>Rozpusťte čokoládu s máslem ve vodní lázni.</li>
                        <li>Ušlehejte vejce s cukrem do pěny, vmíchejte čokoládu a mouku.</li>
                        <li>Pečte 35-40 minut. Nechte vychladnout.</li>
                        <li>Ušlehejte smetanu a dort s ní potřete.</li>
                    </ol>
                </div>
            </div>

            <div class="recept-karta demo-recept" id="vanilkove-cupcakes">
                <img src="img/Middle/sec.jpeg" alt="Vanilkové cupcakes" class="recept-obrazek">
                <div class="recept-obsah">
                    <div class="demo-badge">DEMO RECEPT</div>
                    <h2>Vanilkové cupcakes</h2>
                    <div class="recept-info">
                        <span><i class="fas fa-clock"></i> 45 min</span>
                        <span><i class="fas fa-users"></i> 12 kusů</span>
                    </div>
                    <h3>Ingredience:</h3>
                    <ul>
                        <li>200g másla</li>
                        <li>200g cukru</li>
                        <li>4 vejce</li>
                        <li>200g hladké mouky</li>
                        <li>1 sáček prášku do pečiva</li>
                        <li>100ml mléka</li>
                        <li>1 lžička vanilkového extraktu</li>
                    </ul>
                    <h3>Postup:</h3>
                    <ol>
                        <li>Rozehřejte troubu na 180°C.</li>
                        <li>Ušlehejte máslo s cukrem do pěny.</li>
                        <li>Postupně přidejte vejce a vanilku.</li>
                        <li>Vmíchejte mouku s práškem a mléko.</li>
                        <li>Rozdělte do košíčků a pečte 18-20 minut.</li>
                    </ol>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Zobrazení receptů z databáze -->
            <?php foreach ($recepty as $recept): ?>
                <div class="recept-karta" id="recept-<?php echo h($recept['slug']); ?>">
                    <?php if ($recept['hlavni_obrazek']): ?>
                        <img src="uploads/recepty/<?php echo h($recept['hlavni_obrazek']); ?>" 
                             alt="<?php echo h($recept['nazev']); ?>" 
                             class="recept-obrazek">
                    <?php else: ?>
                        <div class="recept-obrazek-placeholder">
                            <i class="fas fa-utensils"></i>
                            <span>Bez obrázku</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="recept-obsah">
                        <?php if ($recept['kategorie_nazev']): ?>
                            <div class="recept-kategorie" style="background-color: <?php echo h($recept['kategorie_barva'] ?? '#3498db'); ?>">
                                <?php echo h($recept['kategorie_nazev']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h2><?php echo h($recept['nazev']); ?></h2>
                        
                        <?php if ($recept['popis']): ?>
                            <p class="recept-popis"><?php echo h($recept['popis']); ?></p>
                        <?php endif; ?>
                        
                        <div class="recept-info">
                            <?php if ($recept['cas_pripravy']): ?>
                                <span><i class="fas fa-clock"></i> <?php echo h($recept['cas_pripravy']); ?> min</span>
                            <?php endif; ?>
                            <?php if ($recept['pocet_porci']): ?>
                                <span><i class="fas fa-users"></i> <?php echo h($recept['pocet_porci']); ?> porcí</span>
                            <?php endif; ?>
                            <?php if ($recept['obtiznost']): ?>
                                <span><i class="fas fa-star"></i> <?php echo h($recept['obtiznost']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($recept['ingredience']): ?>
                            <h3>Ingredience:</h3>
                            <div class="recept-ingredience">
                                <?php echo nl2br(h($recept['ingredience'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($recept['postup']): ?>
                            <h3>Postup:</h3>
                            <div class="recept-postup">
                                <?php echo nl2br(h($recept['postup'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="recept-meta">
                            <small><i class="fas fa-calendar"></i> Přidáno: <?php echo date('d.m.Y', strtotime($recept['datum_vytvoreni'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<style>
/* Dodatečné styly pro databázové recepty */
.recept-info-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
    text-align: center;
    border: 2px dashed #8d6e63;
}

.recept-info-box h2 {
    color: #8d6e63;
    margin-bottom: 15px;
}

.recept-info-box a {
    color: #8d6e63;
    text-decoration: none;
    font-weight: bold;
}

.recept-info-box a:hover {
    text-decoration: underline;
}

.demo-badge {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.demo-recept {
    opacity: 0.8;
    border: 2px dashed #ff6b6b;
}

.recept-kategorie {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    color: white;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.recept-popis {
    font-style: italic;
    color: #666;
    margin: 10px 0;
}

.recept-obrazek-placeholder {
    height: 200px;
    background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
    border-radius: 8px;
}

.recept-obrazek-placeholder i {
    font-size: 3em;
    margin-bottom: 10px;
}

.recept-ingredience,
.recept-postup {
    background: #f9f7f4;
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
    border-left: 4px solid #8d6e63;
}

.recept-meta {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
    color: #888;
}
</style>