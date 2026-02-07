<?php 
include("header.php");
require 'config/config.php';
require 'includes/form_handlers/createJoinClass_handler.php';

// Gestion affichage form rejoindre
$showJoinForm = isset($_POST['joinClass_button']) || isset($_GET['join']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer/Rejoindre Classe - E-Learning</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== CREATE/JOIN NEUMORPHISM 2026 ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 6px 6px 12px #e1e5e9, inset -6px -6px 12px #ffffff;
            --accent-500: #4f46e5; --accent-600: #4338ca;
            --success-500: #10b981;
            --text-dark: #1e293b; --text-light: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-primary); 
            min-height: 100vh; 
            padding-top: 100px;
        }

        .bg {
            min-height: calc(100vh - 100px); 
            padding: 2rem 0; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .wrapper {
            max-width: 500px; width: 100%; padding: 0 2rem;
        }

        .creatClass_box {
            background: var(--card-bg); 
            border-radius: 36px; 
            padding: 3.5rem; 
            box-shadow: var(--neumo-shadow);
            position: relative; overflow: hidden;
            transition: all 0.5s ease;
        }

        .creatClass_box::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0;
            height: 6px; background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            border-radius: 36px 36px 0 0;
        }

        .creatClass_box:hover {
            transform: translateY(-8px);
            box-shadow: 20px 20px 40px #d1d9e6, -20px -20px 40px #ffffff;
        }

        .creatClass_header {
            text-align: center; margin-bottom: 2.5rem;
        }

        .creatClass_header h1 {
            font-size: clamp(2rem, 5vw, 2.5rem); 
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        /* ===== FORM FIELDS ===== */
        form {
            display: flex; flex-direction: column; gap: 1.5rem;
        }

        input[type="text"] {
            padding: 1.3rem 1.5rem; 
            background: var(--card-bg); 
            border: none; 
            border-radius: 24px;
            box-shadow: var(--neumo-inset); 
            font-size: 1.1rem; 
            font-family: inherit;
            transition: all 0.3s ease;
            color: var(--text-dark);
        }

        input[type="text"]:focus {
            outline: none; 
            box-shadow: var(--neumo-shadow);
            transform: translateY(-2px);
        }

        input[type="text"]::placeholder {
            color: var(--text-light);
        }

        /* ===== BUTTONS ===== */
        button, .cancel_button {
            padding: 1.2rem 2.5rem; 
            border: none; 
            border-radius: 24px; 
            font-weight: 600; 
            font-size: 1.1rem; 
            cursor: pointer;
            transition: all 0.3s ease; 
            text-decoration: none; 
            display: inline-block;
            text-align: center;
        }

        #create_class_button {
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            color: white; 
            box-shadow: var(--neumo-shadow);
            margin-bottom: 1rem;
        }

        #create_class_button:hover {
            transform: translateY(-3px);
            box-shadow: 8px 8px 20px rgba(79,70,229,0.4);
        }

        .cancel_button {
            background: var(--card-bg); 
            color: var(--text-light); 
            box-shadow: var(--neumo-shadow);
        }

        .cancel_button:hover {
            transform: translateY(-2px);
            color: var(--text-dark);
            box-shadow: 8px 8px 20px #d1d9e6;
        }

        /* ===== SWITCH LINKS ===== */
        .joinClass, .createClass {
            text-align: center; 
            color: var(--accent-500); 
            font-weight: 500; 
            cursor: pointer;
            padding: 1rem; 
            border-radius: 16px; 
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .joinClass:hover, .createClass:hover {
            background: rgba(79,70,229,0.1); 
            transform: translateY(-1px);
        }

        /* ===== ANIMATIONS SLIDE ===== */
        #first, #second {
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        #first.hidden, #second.hidden {
            display: none !important;
        }

        /* ===== SUCCESS STATE ===== */
        .success-message {
            background: rgba(16,185,129,0.2); 
            border: 2px solid rgba(16,185,129,0.3);
            border-radius: 20px; 
            padding: 2rem; 
            text-align: center;
            color: var(--success-500);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .wrapper { padding: 0 1rem; }
            .creatClass_box { padding: 2.5rem 2rem; margin: 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg">
        <div class="wrapper">
            <div class="creatClass_box">
                <?php if(isset($_SESSION['class_created_success'])): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle" style="font-size:3rem;margin-bottom:1rem;"></i>
                        <h2>Classe créée avec succès !</h2>
                        <p><strong><?php echo htmlspecialchars($_SESSION['class_created_success']); ?></strong></p>
                        <a href="index.php" class="cancel_button" style="width:100%;margin-top:1rem;">
                            <i class="fas fa-home"></i> Retour au tableau de bord
                        </a>
                    </div>
                <?php 
                    unset($_SESSION['class_created_success']);
                elseif(isset($_SESSION['class_joined_success'])): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle" style="font-size:3rem;margin-bottom:1rem;"></i>
                        <h2>Classe rejointe !</h2>
                        <p><strong><?php echo htmlspecialchars($_SESSION['class_joined_success']); ?></strong></p>
                        <a href="index.php" class="cancel_button" style="width:100%;margin-top:1rem;">
                            <i class="fas fa-home"></i> Retour au tableau de bord
                        </a>
                    </div>
                <?php 
                    unset($_SESSION['class_joined_success']);
                else: ?>

                <!-- FORM CRÉER CLASSE -->
                <div id="first" <?php echo $showJoinForm ? 'class="hidden"' : ''; ?>>
                    <div class="creatClass_header">
                        <h1><i class="fas fa-plus-circle"></i> Créer une classe</h1>
                    </div>
                    <form action="createJoinClass.php" method="POST">
                        <input type="text" name="className" autocomplete="off" 
                               placeholder="Nom de la classe/Code du cours" 
                               maxlength="50" required>
                        <br>
                        <input type="text" name="section" autocomplete="off" 
                               placeholder="Section" maxlength="20" required>
                        <br>
                        <input type="text" name="subject" autocomplete="off" 
                               placeholder="Matière/Titre du cours" maxlength="100" required>
                        <br>
                        <button type="submit" name="createClass_button" id="create_class_button">
                            <i class="fas fa-magic"></i> Créer ma classe
                        </button>
                        <button class="cancel_button" onclick="window.location.href='index.php'">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <br><br>
                        <a href="#" id="joinClass" class="joinClass">
                            👥 Vous voulez rejoindre une classe ? Cliquez ici
                        </a>
                    </form>
                </div>

                <!-- FORM REJOINDRE CLASSE -->
                <div id="second" <?php echo !$showJoinForm ? 'class="hidden"' : ''; ?>>
                    <div class="joinClass_header">
                        <h1><i class="fas fa-sign-in-alt"></i> Rejoindre une classe</h1>
                    </div>
                    <form action="createJoinClass.php" method="POST">
                        <input type="text" name="code" placeholder="Code de la classe" 
                               autocomplete="off" maxlength="20" required>
                        <br>
                        <button type="submit" name="joinClass_button" id="create_class_button">
                            <i class="fas fa-door-open"></i> Rejoindre
                        </button>
                        <button class="cancel_button" onclick="window.location.href='index.php'">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <br><br>
                        <a href="#" id="createClass" class="createClass">
                            ➕ Vous souhaitez créer une nouvelle classe ? Cliquez ici !
                        </a>
                    </form>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Switch forms
        $("#joinClass").click(function(e) {
            e.preventDefault();
            $("#first").addClass('hidden');
            $("#second").removeClass('hidden');
        });

        $("#createClass").click(function(e) {
            e.preventDefault();
            $("#second").addClass('hidden');
            $("#first").removeClass('hidden');
        });

        // Auto-resize inputs
        $('input[type="text"]').on('input', function() {
            this.style.width = (this.value.length + 1) + 'ch';
        });
    });
    </script>
</body>
</html>
