<?php 
// Démarrer la session en premier
session_start();

// Charger les configs et handlers
require_once 'config/config.php';
require_once 'includes/form_handlers/register_handler.php';
require_once 'includes/form_handlers/login_handler.php';

// Initialiser variables globales
$error_array = [];
$success_message = '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning - Connexion</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/background/graduation.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== NEUMORPHISM LOGIN 2026 - INTÉGRÉ ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 4px 4px 8px #e1e5e9, inset -4px -4px 8px #ffffff;
            --accent-500: #4f46e5;
            --accent-600: #4338ca;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120,119,198,0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,119,198,0.2) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes bgShift {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(180deg); }
        }

        .wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 420px;
            max-width: 1400px;
            margin: 0 auto;
            gap: 2rem;
            padding: 2rem;
        }

        /* Hero Section */
        .hero-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 2rem 0;
        }

        .brand {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .hero-content h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: var(--text-light);
            line-height: 1.6;
        }

        /* Landing overlay */
        .landing {
            position: fixed;
            inset: 0;
            background: rgba(248, 250, 252, 0.98);
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .landing.hidden {
            transform: translateY(-100%);
            opacity: 0;
        }

        #landing-btn {
            padding: 1.2rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            background: var(--card-bg);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: var(--neumo-shadow);
            color: var(--accent-500);
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        #landing-btn:hover {
            transform: translateY(-3px);
            box-shadow: 8px 8px 20px #e1e5e9, -8px -8px 20px #ffffff;
        }

        /* Form Container */
        .login_container {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: var(--neumo-shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            transform: translateY(30px);
            animation: slideUp 0.8s 0.6s forwards;
        }

        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .form_header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form_header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Form tabs */
        .form-tabs {
            display: flex;
            margin-bottom: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 4px;
            box-shadow: var(--neumo-shadow);
            width: 100%;
        }

        .tab-btn {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-light);
        }

        .tab-btn.active {
            background: var(--accent-500);
            color: white;
            box-shadow: var(--neumo-inset);
        }

        /* Form fields */
        .form-group {
            width: 100%;
            margin-bottom: 1.5rem;
            position: relative;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 1.2rem 1.5rem 1.2rem 1.5rem;
            background: var(--card-bg);
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            color: var(--text-dark);
            box-shadow: var(--neumo-inset);
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            box-shadow: 6px 6px 12px #e1e5e9, -6px -6px 12px #ffffff;
            transform: translateY(-1px);
        }

        input::placeholder {
            color: var(--text-light);
        }

        /* Messages */
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin: 0.5rem 0;
            padding: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 12px;
            border-left: 4px solid #ef4444;
            text-align: center;
        }

        .success-message {
            color: #10b981;
            font-size: 0.875rem;
            margin: 0.5rem 0;
            padding: 0.75rem;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 12px;
            border-left: 4px solid #10b981;
            text-align: center;
        }

        /* Button */
        #button {
            width: 100%;
            padding: 1.3rem;
            background: var(--card-bg);
            border: none;
            border-radius: 24px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent-500);
            cursor: pointer;
            box-shadow: var(--neumo-shadow);
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        #button:hover {
            transform: translateY(-2px);
            box-shadow: 8px 8px 16px #e1e5e9, -8px -8px 16px #ffffff;
        }

        /* Links */
        .switch-link {
            text-align: center;
            color: var(--accent-500);
            font-weight: 500;
            margin-top: 1.5rem;
            padding: 0.8rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: block;
        }

        .switch-link:hover {
            background: rgba(79, 70, 229, 0.1);
        }

        /* Form visibility */
        .form-content { display: none; }
        .form-content.active { display: block; }

        /* Responsive */
        @media (max-width: 1024px) {
            .wrapper { grid-template-columns: 1fr; padding: 1rem; }
            .hero-section { text-align: center; align-items: center; }
        }

        @media (max-width: 768px) {
            .login_container { margin: 1rem 0; padding: 2rem 1.5rem; }
        }
    </style>
</head>

<body>
    <!-- Landing Overlay -->
    <div class="landing" id="landingOverlay">
        <h1 class="brand">Système E-Learning</h1>
        <div class="hero-content">
            <h1>Rejoignez vos cours<br>depuis n'importe où</h1>
            <p>Connectez-vous à votre plateforme d'apprentissage en ligne et accédez à tous vos cours en un clic.</p>
            <button id="landing-btn">Commencer</button>
        </div>
    </div>

    <div class="wrapper">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="brand">E-Learning</h1>
            <div class="hero-content">
                <h1>Plateforme d'apprentissage moderne</h1>
                <p>Accédez à des milliers de cours, suivez votre progression et collaborez avec vos collègues en temps réel.</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="login_container">
            <div class="form_header">
                <h2>Bienvenue</h2>
            </div>

            <!-- Form Tabs -->
            <div class="form-tabs">
                <button class="tab-btn active" data-tab="login">Connexion</button>
                <button class="tab-btn" data-tab="register">Inscription</button>
            </div>

            <!-- Login Form -->
            <div id="login" class="form-content active">
                <?php if (in_array("Email or password was incorrect<br>", $error_array)): ?>
                    <div class="error-message">Email ou mot de passe incorrect</div>
                <?php endif; ?>

                <form action="register.php" method="POST" id="login-form">
                    <div class="form-group">
                        <input type="email" name="log_email" placeholder="Adresse email" 
                               value="<?php echo isset($_SESSION['log_email']) ? $_SESSION['log_email'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="log_password" placeholder="Mot de passe" required>
                    </div>
                    <button type="submit" name="login_button" id="button">Se connecter</button>
                </form>
                <a href="#" class="switch-link" id="showRegister">Besoin d'un compte ? S'inscrire</a>
            </div>

            <!-- Register Form -->
            <div id="register" class="form-content">
                <?php 
                // Affichage des erreurs d'inscription
                if (in_array("Email already in use<br>", $error_array)) {
                    echo '<div class="error-message">Cet email est déjà utilisé</div>';
                } elseif (in_array("Invalid email format<br>", $error_array)) {
                    echo '<div class="error-message">Format email invalide</div>';
                } elseif (in_array("Email do not match<br>", $error_array)) {
                    echo '<div class="error-message">Les emails ne correspondent pas</div>';
                } elseif (in_array("Your password do not match<br>", $error_array)) {
                    echo '<div class="error-message">Les mots de passe ne correspondent pas</div>';
                }

                if (isset($_SESSION['reg_success'])) {
                    echo '<div class="success-message">Inscription réussie ! Connectez-vous maintenant.</div>';
                    unset($_SESSION['reg_success']);
                }
                ?>

                <form action="register.php" method="POST" id="register-form">
                    <div class="form-group">
                        <input type="text" name="reg_fname" placeholder="Prénom" 
                               value="<?php echo isset($_SESSION['reg_fname']) ? $_SESSION['reg_fname'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="reg_lname" placeholder="Nom" 
                               value="<?php echo isset($_SESSION['reg_lname']) ? $_SESSION['reg_lname'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="reg_email" placeholder="Email" 
                               value="<?php echo isset($_SESSION['reg_email']) ? $_SESSION['reg_email'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="reg_email2" placeholder="Confirmer l'email" 
                               value="<?php echo isset($_SESSION['reg_email2']) ? $_SESSION['reg_email2'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="reg_password" placeholder="Mot de passe" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="reg_password2" placeholder="Confirmer le mot de passe" required>
                    </div>
                    <button type="submit" name="register_button" id="button">S'inscrire</button>
                </form>
                <a href="#" class="switch-link" id="showLogin">Déjà un compte ? Se connecter</a>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Masquer landing
            $('#landing-btn').click(function() {
                $('#landingOverlay').addClass('hidden');
            });

            // Switch tabs
            $('.tab-btn').click(function() {
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');
                
                $('.form-content').removeClass('active');
                $('#' + $(this).data('tab')).addClass('active');
            });

            // Switch links
            $('#showRegister').click(function(e) {
                e.preventDefault();
                $('.tab-btn[data-tab="register"]').click();
            });

            $('#showLogin').click(function(e) {
                e.preventDefault();
                $('.tab-btn[data-tab="login"]').click();
            });

            // Auto-show register si vient de POST register
            <?php if (isset($_POST['register_button'])): ?>
                $('.tab-btn[data-tab="register"]').click();
            <?php endif; ?>
        });
    </script>
</body>
</html>
