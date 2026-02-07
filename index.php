<?php 
include("header.php");

$username = $user['username'];
$userCode = $user2['courseCode'];
$post = new Post($con, $username, $userCode);
$checkTeaching = $post->checkTeachingClass();
$checkEnrolled = $post->checkEnrolledClass();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Classes - E-Learning</title>
    <style>
        /* ===== DASHBOARD NEUMORPHISM 2026 ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 4px 4px 8px #e1e5e9, inset -4px -4px 8px #ffffff;
            --accent-500: #4f46e5;
            --accent-600: #4338ca;
            --success-500: #10b981;
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
            padding-top: 80px; /* Pour header fixe */
        }

        .bg {
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
        }

        .wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            gap: 3rem;
            grid-template-columns: 1fr 1fr;
        }

        /* ===== SECTION TEACHING ===== */
        .teaching {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 2.5rem;
            box-shadow: var(--neumo-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .teaching:hover {
            transform: translateY(-8px);
            box-shadow: 16px 16px 32px #d1d9e6, -16px -16px 32px #ffffff;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid rgba(79, 70, 229, 0.1);
        }

        .header {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ===== GRID CLASSES ===== */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .classBox, .EnrolledclassBox {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--neumo-shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .classBox::before, .EnrolledclassBox::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-500), var(--accent-600));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .classBox:hover::before, .EnrolledclassBox:hover::before {
            opacity: 1;
        }

        .classBox:hover, .EnrolledclassBox:hover {
            transform: translateY(-10px);
            box-shadow: 10px 10px 25px #d1d9e6, -10px -10px 25px #ffffff;
        }

        .classBox h3, .EnrolledclassBox h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .classBox a, .EnrolledclassBox a {
            color: var(--accent-500);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .classBox a:hover, .EnrolledclassBox a:hover {
            color: var(--accent-600);
            text-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        }

        /* ===== ENROLLED SECTION ===== */
        .enrolled {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 2.5rem;
            box-shadow: var(--neumo-shadow);
            transition: all 0.4s ease;
        }

        .enrolled:hover {
            transform: translateY(-8px);
            box-shadow: 16px 16px 32px #d1d9e6, -16px -16px 32px #ffffff;
        }

        /* ===== EMPTY STATE ===== */
        #nullTeachingEnrolled {
            grid-column: 1 / -1;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 4rem 3rem;
            text-align: center;
            box-shadow: var(--neumo-shadow);
            max-width: 600px;
            margin: 0 auto;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }

        #nullTeachingEnrolled:hover {
            transform: translateY(0);
            box-shadow: 16px 16px 40px #d1d9e6, -16px -16px 40px #ffffff;
        }

        #nullTeachingEnrolled p {
            font-size: 1.3rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .null-button {
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

        .null-button:hover {
            transform: translateY(-3px);
            box-shadow: 10px 10px 25px #d1d9e6, -10px -10px 25px #ffffff;
            color: var(--accent-600);
        }

        .null-button:active {
            box-shadow: var(--neumo-inset);
            transform: translateY(0);
        }

        /* ===== DELETE BUTTON ===== */
        .delete-class-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 4px 4px 8px rgba(239, 68, 68, 0.4);
            transition: all 0.2s ease;
            opacity: 0;
        }

        .classBox:hover .delete-class-btn,
        .EnrolledclassBox:hover .delete-class-btn {
            opacity: 1;
        }

        .delete-class-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .wrapper {
                grid-template-columns: 1fr;
                padding: 0 1rem;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .teaching, .enrolled {
                padding: 2rem 1.5rem;
            }
            
            .classes-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            #nullTeachingEnrolled {
                padding: 3rem 2rem;
                margin: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg">
        <div class="wrapper">
            <?php 
            if ($checkTeaching == true): 
            ?>
                <div class="teaching">
                    <div class="section-header">
                        <h3 class="header">👨‍🏫 Mes Classes (Enseignant)</h3>
                    </div>
                    <div class="classes-grid">
                        <?php $post->loadTeachingClasses(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php 
            if ($checkEnrolled == true): 
            ?>
                <div class="enrolled">
                    <div class="section-header">
                        <h3 class="header">📚 Mes Classes (Inscrit)</h3>
                    </div>
                    <div class="classes-grid">
                        <?php $post->loadEnrolledClasses(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php 
            if (!$checkTeaching && !$checkEnrolled): 
            ?>
                <div id="nullTeachingEnrolled">
                    <p>🎯 Il semble que vous n'ayez pas encore créé de classe<br>ou que vous ne vous soyez pas inscrit à une classe !</p>
                    <p>🚀 Cliquez ci-dessous pour commencer votre aventure d'apprentissage</p>
                    <a href="createJoinClass.php">
                        <button class="null-button">🎓 Créer/Rejoindre une classe</button>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(function(){
            // Confirmation suppression classe
            $('a.delete-class-btn').click(function(e){
                if(confirm("⚠️ Êtes-vous sûr de vouloir supprimer cette classe ?") === false){
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html>
