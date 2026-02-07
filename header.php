<?php
session_start();
require_once 'config/config.php';
require_once 'includes/classes/User.php';      
require_once 'includes/classes/User2.php';     
require_once 'includes/classes/Post.php';      

// Vérifier session
if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: register.php");
    exit();
}

$userLoggedIn = $_SESSION['username'];

// ✅ SÉCURITÉ : escape string (pas d'injection SQL)
$userLoggedIn = mysqli_real_escape_string($con, $userLoggedIn);

// Requête users (étudiant)
$user_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn' LIMIT 1");
if(mysqli_num_rows($user_query) === 0) {
    header("Location: register.php");
    exit();
}
$user = mysqli_fetch_array($user_query);

// Requête createclass (prof) - SANS User2 class
$user2_query = mysqli_query($con, "SELECT * FROM createclass WHERE username='$userLoggedIn' ORDER BY id DESC LIMIT 1");
$user2 = mysqli_num_rows($user2_query) > 0 ? mysqli_fetch_array($user2_query) : null;

// Nettoyage
unset($user_query, $user2_query);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer/Rejoindre Classe - E-Learning</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/background/graduation.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== CREATE/JOIN CLASS NEUMORPHISM 2026 ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 4px 4px 8px #e1e5e9, inset -4px -4px 8px #ffffff;
            --accent-500: #4f46e5;
            --accent-600: #4338ca;
            --success-500: #10b981;
            --warning-500: #f59e0b;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            padding-top: 80px;
        }

        /* ===== TOP BAR MODERNE ===== */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1;
            background: 
                radial-gradient(circle at 25% 25%, rgba(79,70,229,0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(16,185,129,0.1) 0%, transparent 50%);
        }

        .top_bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: var(--neumo-shadow);
        }

        .logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
        }

        .logo a:hover {
            transform: translateY(-2px);
        }

        .icon nav {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .icon a {
            position: relative;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.8rem;
            border-radius: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .icon a:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--accent-500);
            transform: translateY(-2px);
        }

        .notification_badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 12px;
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 18px;
            text-align: center;
            box-shadow: 2px 2px 6px rgba(239, 68, 68, 0.4);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            min-height: calc(100vh - 120px);
        }

        /* ===== CREATE CLASS CARD ===== */
        .create-class-card {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 3rem;
            box-shadow: var(--neumo-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
        }

        .create-class-card:hover {
            transform: translateY(-10px);
            box-shadow: 16px 16px 40px #d1d9e6, -16px -16px 40px #ffffff;
        }

        .create-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .create-class-card h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .create-class-card p {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .create-btn {
            all: unset;
            background: var(--card-bg);
            border-radius: 24px;
            padding: 1.2rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent-500);
            cursor: pointer;
            box-shadow: var(--neumo-shadow);
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .create-btn:hover {
            transform: translateY(-3px);
            box-shadow: 10px 10px 25px #d1d9e6, -10px -10px 25px #ffffff;
            color: var(--accent-600);
        }

        /* ===== JOIN CLASS CARD ===== */
        .join-class-card {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 3rem;
            box-shadow: var(--neumo-shadow);
            transition: all 0.4s ease;
        }

        .join-class-card:hover {
            transform: translateY(-10px);
            box-shadow: 16px 16px 40px #d1d9e6, -16px -16px 40px #ffffff;
        }

        .join-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--success-500), #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .join-class-card h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        /* ===== FORM JOIN CLASS ===== */
        .join-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            background: var(--card-bg);
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            color: var(--text-dark);
            box-shadow: var(--neumo-inset);
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            box-shadow: 6px 6px 12px #e1e5e9, -6px -6px 12px #ffffff;
            transform: translateY(-1px);
        }

        .join-btn {
            width: 100%;
            padding: 1.3rem;
            background: var(--card-bg);
            border: none;
            border-radius: 24px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--success-500);
            cursor: pointer;
            box-shadow: var(--neumo-shadow);
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .join-btn:hover {
            transform: translateY(-2px);
            box-shadow: 8px 8px 20px #d1d9e6, -8px -8px 20px #ffffff;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 0 1rem;
            }
        }

        @media (max-width: 768px) {
            .top_bar {
                padding: 0 1rem;
                flex-wrap: wrap;
            }
            
            .icon nav {
                gap: 1rem;
                flex-wrap: wrap;
            }
            
            .create-class-card, .join-class-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Background -->
    <div class="background"></div>
    
    <!-- Top Bar -->
    <div class="top_bar">
        <div class="logo">
            <a href="index.php">Système E-Learning</a>
        </div>
        
        <div class="icon">
    <nav>
        <?php 
        // Notifications
        $notifications = new User2($con, $userLoggedIn);
        $num_notifications = $notifications->getUnreadNumber();
        ?>
        
        <!-- ✅ PROFIL CORRIGÉ -->
        <a href="profile.php?profile_username=<?php echo urlencode($userLoggedIn); ?>">
            👤 <?php echo htmlspecialchars($user['first_name']); ?>
        </a>
        
        <a href="index.php">
            <i class="fas fa-home"></i> Accueil
        </a>
        
        <a href="notifications.php">
            <i class="fas fa-bell"></i> Notifications
            <?php if($num_notifications > 0): ?>
                <span class="notification_badge"><?php echo $num_notifications; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="createJoinClass.php" class="active">
            <i class="fas fa-plus"></i> Classe
        </a>
        
        <a href="includes/handlers/logout.php">
            <i class="fas fa-power-off"></i> Déconnexion
        </a>
    </nav>
</div>

    </div>

    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="assets/js/createJoinClass.js"></script>
</body>
</html>
