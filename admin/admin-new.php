<?php
// Načtení konfigurací
require_once '../config/konfigurace-formulare.php';
require_once '../config/databaze-config.php';

// Ochrana proti XSS
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Kontrola přihlášení
session_start();

// Jednoduché heslo pro přístup (později nahradíme databází)
$admin_heslo = "admin123"; // Výchozí heslo - ZMĚŇTE PO INSTALACI!

if (isset($_POST['prihlasit'])) {
    if ($_POST['heslo'] == $admin_heslo) {
        $_SESSION['admin_prihlasen'] = true;
        $_SESSION['admin_cas'] = time();
        $_SESSION['admin_user'] = 'admin'; // Pro kompatibilitu
        $_SESSION['admin_login_time'] = time(); // Pro kompatibilitu
    } else {
        $chyba_prihlaseni = "Nesprávné heslo!";
    }
}

if (isset($_POST['odhlasit'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Kontrola timeoutu
if (isset($_SESSION['admin_prihlasen']) && 
    isset($_SESSION['admin_cas']) && 
    (time() - $_SESSION['admin_cas']) > ADMIN_SESSION_TIMEOUT) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$prihlasen = isset($_SESSION['admin_prihlasen']) && $_SESSION['admin_prihlasen'];

// Aktuální sekce
$sekce = $_GET['sekce'] ?? 'dashboard';

// Inicializace databáze
$db = null;
if ($prihlasen) {
    try {
        $db = Database::getInstance();
    } catch (Exception $e) {
        $db_chyba = "Chyba připojení k databázi: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace - <?php echo NAZEV_CUKRARSTVI; ?></title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- TinyMCE WYSIWYG Editor - lokální verze -->
    <script>
        // Jednoduchý WYSIWYG editor bez závislosti na TinyMCE
        function initEditor() {
            const textareas = document.querySelectorAll('.wysiwyg-editor');
            textareas.forEach(textarea => {
                // Vytvoření editovatelného divu
                const editor = document.createElement('div');
                editor.style.cssText = `
                    border: 2px solid #834912;
                    border-radius: 8px;
                    padding: 15px;
                    min-height: 300px;
                    background: white;
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                `;
                editor.contentEditable = true;
                editor.innerHTML = textarea.value;
                
                // Toolbar
                const toolbar = document.createElement('div');
                toolbar.style.cssText = `
                    margin-bottom: 10px;
                    padding: 10px;
                    background: #f5f5f5;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    display: flex;
                    gap: 5px;
                    flex-wrap: wrap;
                `;
                toolbar.innerHTML = `
                    <button type="button" onclick="document.execCommand('bold')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;"><b>Tučné</b></button>
                    <button type="button" onclick="document.execCommand('italic')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;"><i>Kurzíva</i></button>
                    <button type="button" onclick="document.execCommand('underline')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;"><u>Podtržené</u></button>
                    <button type="button" onclick="document.execCommand('insertUnorderedList')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;">• Seznam</button>
                    <button type="button" onclick="document.execCommand('insertOrderedList')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;">1. Seznam</button>
                    <button type="button" onclick="document.execCommand('formatBlock', false, 'h2')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;">Nadpis 2</button>
                    <button type="button" onclick="document.execCommand('formatBlock', false, 'h3')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;">Nadpis 3</button>
                    <button type="button" onclick="document.execCommand('formatBlock', false, 'p')" style="padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 3px; cursor: pointer;">Odstavec</button>
                `;
                
                // Skrytí původního textarea
                textarea.style.display = 'none';
                
                // Vložení editoru před textarea
                textarea.parentNode.insertBefore(toolbar, textarea);
                textarea.parentNode.insertBefore(editor, textarea);
                
                // Synchronizace obsahu při změně
                editor.addEventListener('input', () => {
                    textarea.value = editor.innerHTML;
                });
                
                // Synchronizace při odeslání formuláře
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        textarea.value = editor.innerHTML;
                    });
                }
            });
        }
        
        // Inicializace po načtení DOM
        document.addEventListener('DOMContentLoaded', initEditor);
    </script>
    
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: rgb(240, 223, 179);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #834912;
        }
        
        .admin-header h1 {
            color: #834912;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .admin-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            justify-content: center;
        }
        
        .nav-button, .admin-button {
            padding: 12px 20px;
            background: linear-gradient(135deg, #834912 0%, #654321 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .nav-button:hover, .admin-button:hover {
            background: linear-gradient(135deg, #654321 0%, #543310 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .nav-button.active {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        }
        
        .prihlaseni-form {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .prihlaseni-form input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #834912;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        
        .obsah-sekce {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .statistiky {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-karta {
            background: linear-gradient(135deg, #834912 0%, #654321 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-cislo {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stat-popis {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .tabulka {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            table-layout: fixed;
        }
        
        /* Explicitní šířky sloupců pro recepty */
        .tabulka th:nth-child(1) { width: 80px; }   /* Obrázek */
        .tabulka th:nth-child(2) { width: 200px; }  /* Název */
        .tabulka th:nth-child(3) { width: 120px; }  /* Kategorie */
        .tabulka th:nth-child(4) { width: 100px; }  /* Čas/Porce */
        .tabulka th:nth-child(5) { width: 80px; }   /* Obtížnost */
        .tabulka th:nth-child(6) { width: 80px; }   /* Stav */
        .tabulka th:nth-child(7) { width: 100px; }  /* Vytvořeno */
        .tabulka th:nth-child(8) { width: 120px; }  /* Akce */
        
        .tabulka th, .tabulka td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0e7d0;
        }
        
        .tabulka th {
            background: #834912;
            color: white;
            font-weight: 600;
        }
        
        .tabulka tr:hover {
            background: #f9f9f9;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
        }
        
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #834912;
        }
        
        .akce-tlacitka {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            min-width: 120px;
            justify-content: flex-start;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f57c00 0%, #ef6c00 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
        }
        
        .chyba {
            color: #c62828;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            padding: 15px;
            background: #ffebee;
            border: 1px solid #e57373;
            border-radius: 8px;
        }
        
        .uspech {
            color: #2e7d32;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            padding: 15px;
            background: #e8f5e8;
            border: 1px solid #81c784;
            border-radius: 8px;
        }
        
        .info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                margin: 10px;
                padding: 15px;
            }
            
            .admin-nav {
                flex-direction: column;
                align-items: stretch;
            }
            
            .nav-button {
                text-align: center;
            }
            
            .statistiky {
                grid-template-columns: 1fr;
            }
            
            .tabulka {
                font-size: 12px;
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }
            
            .tabulka thead,
            .tabulka tbody,
            .tabulka th,
            .tabulka td,
            .tabulka tr {
                display: table;
                table-layout: fixed;
                width: 100%;
            }
            
            .akce-tlacitka {
                display: flex;
                flex-direction: row;
                gap: 3px;
                flex-wrap: nowrap;
                min-width: 100px;
            }
            
            .btn-small {
                padding: 4px 8px;
                font-size: 10px;
                min-width: 28px;
            }
            
            .modal-content {
                width: 95%;
                margin: 2% auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php if (!$prihlasen): ?>
        <div class="admin-container">
            <div class="admin-header">
                <h1><i class="fas fa-lock"></i> Administrace</h1>
                <p>Přihlášení do administrační části</p>
            </div>
            
            <?php if (isset($chyba_prihlaseni)): ?>
                <div class="chyba"><?php echo $chyba_prihlaseni; ?></div>
            <?php endif; ?>
            
            <form method="post" class="prihlaseni-form">
                <div class="form-group">
                    <input type="password" name="heslo" placeholder="Zadejte heslo" required>
                </div>
                <button type="submit" name="prihlasit" class="admin-button">
                    <i class="fas fa-sign-in-alt"></i> Přihlásit se
                </button>
                
                <div class="info" style="margin-top: 20px;">
                    <strong>Výchozí heslo:</strong> admin123<br>
                    <small>Změňte heslo před nasazením do produkce!</small>
                </div>
            </form>
        </div>
        
    <?php else: ?>
        <div class="admin-container">
            <div class="admin-header">
                <h1><i class="fas fa-cogs"></i> Administrace</h1>
                <p><?php echo NAZEV_CUKRARSTVI; ?></p>
            </div>
            
            <?php if (isset($db_chyba)): ?>
                <div class="chyba">
                    <strong>Problém s databází:</strong> <?php echo $db_chyba; ?>
                    <br><small>Ujistěte se, že máte vytvořenou databázi podle souboru databaze-schema.sql</small>
                </div>
            <?php endif; ?>
            
            <!-- Navigace -->
            <nav class="admin-nav">
                <a href="?sekce=dashboard" class="nav-button <?php echo $sekce == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="?sekce=stranky" class="nav-button <?php echo $sekce == 'stranky' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Stránky
                </a>
                <a href="?sekce=recepty" class="nav-button <?php echo $sekce == 'recepty' ? 'active' : ''; ?>">
                    <i class="fas fa-utensils"></i> Recepty
                </a>
                <a href="?sekce=galerie" class="nav-button <?php echo $sekce == 'galerie' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Galerie
                </a>
                <a href="?sekce=zpravy" class="nav-button <?php echo $sekce == 'zpravy' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Zprávy
                </a>
                <a href="?sekce=nastaveni" class="nav-button <?php echo $sekce == 'nastaveni' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Nastavení
                </a>
                <form method="post" style="display: inline;">
                    <button type="submit" name="odhlasit" class="nav-button">
                        <i class="fas fa-sign-out-alt"></i> Odhlásit
                    </button>
                </form>
            </nav>
            
            <!-- Obsah sekcí -->
            <div class="obsah-sekce">
                <?php
                // Zajištění správných session proměnných pro kompatibilitu
                if (!isset($_SESSION['admin_user'])) {
                    $_SESSION['admin_user'] = 'admin';
                }
                if (!isset($_SESSION['admin_login_time'])) {
                    $_SESSION['admin_login_time'] = $_SESSION['admin_cas'] ?? time();
                }
                
                switch ($sekce) {
                    case 'dashboard':
                        include 'admin-sections/dashboard.php';
                        break;
                    case 'stranky':
                        include 'admin-sections/stranky.php';
                        break;
                    case 'recepty':
                        include 'admin-sections/recepty.php';
                        break;
                    case 'galerie':
                        include 'admin-sections/galerie.php';
                        break;
                    case 'zpravy':
                        include 'admin-sections/zpravy.php';
                        break;
                    case 'nastaveni':
                        include 'admin-sections/nastaveni.php';
                        break;
                    default:
                        echo "<h2>Neplatná sekce</h2>";
                }
                ?>
            </div>
        </div>
        
        <!-- Modální funkcionalita -->
        <script>
            // Modal funkcionalita
            function openModal(modalId) {
                document.getElementById(modalId).style.display = 'block';
            }
            
            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }
            
            // Zavření modalu při kliku mimo obsah
            window.onclick = function(event) {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                });
            }
        </script>
        
    <?php endif; ?>
</body>
</html>