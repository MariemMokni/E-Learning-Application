<?php 
include("header.php");

// S'assurer que $notifications existe
if(!isset($notifications)) {
    $notifications = new User2($con, $_SESSION['username']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications </title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== NOTIFICATIONS NEUMORPHISM PREMIUM 2026 ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 6px 6px 12px #e1e5e9, inset -6px -6px 12px #ffffff;
            --accent-500: #4f46e5;
            --accent-600: #4338ca;
            --success-500: #10b981;
            --warning-500: #f59e0b;
            --danger-500: #ef4444;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --gradient-primary: linear-gradient(135deg, var(--accent-500), var(--accent-600));
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .bg {
            min-height: calc(100vh - 100px);
            padding: 2rem 0;
            position: relative;
        }

        .bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-500), transparent);
            opacity: 0.3;
        }

        .wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* ===== CONTENEUR PRINCIPAL ===== */
        .notif-container {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 36px;
            padding: 3.5rem;
            box-shadow: var(--neumo-shadow);
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
            min-height: 600px;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .notif-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
            border-radius: 36px 36px 0 0;
        }

        .notif-container:hover {
            transform: translateY(-12px);
            box-shadow: 20px 20px 40px #d1d9e6, -20px -20px 40px #ffffff;
        }

        .notif-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid rgba(79, 70, 229, 0.1);
        }

        .notif-title {
            font-size: clamp(2rem, 4vw, 2.5rem);
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .notif-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .notif-count {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* ===== LISTE NOTIFICATIONS ===== */
        .notifications-list {
            max-height: 70vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(79,70,229,0.3) transparent;
        }

        .notifications-list::-webkit-scrollbar {
            width: 8px;
        }

        .notifications-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .notifications-list::-webkit-scrollbar-thumb {
            background: rgba(79,70,229,0.3);
            border-radius: 4px;
        }

        /* ===== CHAQUE NOTIFICATION - MAGNIFIQUE ! ===== */
        .resultDisplayNotification {
            position: relative;
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 2rem 2.5rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255,255,255,0.3);
            overflow: hidden;
        }

        .resultDisplayNotification::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .resultDisplayNotification:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 12px 12px 32px rgba(0,0,0,0.15), -12px -12px 32px rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.8);
        }

        .resultDisplayNotification:hover::before {
            opacity: 1;
        }

        /* ===== NON LUES = SPÉCIALES ===== */
        .resultDisplayNotification[style*="DDEDFF"] {
            background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(34,197,94,0.1) 100%) !important;
            box-shadow: 
                12px 12px 24px rgba(16,185,129,0.3),
                -12px -12px 24px rgba(255,255,255,0.9),
                inset 0 0 20px rgba(16,185,129,0.1) !important;
            border: 1px solid rgba(16,185,129,0.3) !important;
        }

        .resultDisplayNotification[style*="DDEDFF"]::after {
            content: '🔔 NOUVEAU';
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--success-500);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 3px 3px 8px rgba(16,185,129,0.4);
        }

        /* ===== CONTENU NOTIFICATION ===== */
        .notification-content {
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: var(--neumo-shadow);
        }

        .notification-text {
            flex: 1;
        }

        .timestamp_smaller {
            color: var(--text-light) !important;
            font-size: 0.85rem !important;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-message {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.5;
            font-weight: 500;
        }

        /* ===== ÉTAT VIDE ===== */
        .no-notifications {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .no-notifications i {
            font-size: 6rem;
            color: rgba(100,116,139,0.3);
            margin-bottom: 2rem;
            display: block;
        }

        .no-notifications h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        /* ===== LOADING ===== */
        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
            color: var(--accent-500);
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .wrapper { padding: 0 1rem; }
            .notif-container { padding: 2rem 1.5rem; margin: 1rem 0; border-radius: 24px; }
            .notification-content { flex-direction: column; gap: 1rem; }
            .notification-icon { width: 45px; height: 45px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg">
        <div class="wrapper">
            <div class="notif-container">
                <div class="notif-header">
                    <h1 class="notif-title">🔔 Notifications</h1>
                    <div class="notif-count">
                        <?php 
                        $unread = $notifications->getUnreadNumber();
                        echo $unread > 0 ? "($unread non lues)" : "Tout à jour";
                        ?>
                    </div>
                </div>
                
                <div class="notifications-list" id="notif-container">
                    <?php 
                    $notifications_html = $notifications->getNotifications(['page' => 1], 20);
                    if(empty(trim(strip_tags($notifications_html)))) {
                        echo '<div class="no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <h3>Aucune notification</h3>
                                <p>Vous serez informé de toutes les activités importantes ici.</p>
                              </div>';
                    } else {
                        echo $notifications_html;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(function(){
        // Effet hover parfait
        $('.resultDisplayNotification').hover(
            function() { $(this).addClass('hover-effect'); },
            function() { $(this).removeClass('hover-effect'); }
        );

        // Auto-refresh doux (toutes les 2min)
        setTimeout(function(){
            location.reload();
        }, 120000);
    });
    </script>
</body>
</html>
