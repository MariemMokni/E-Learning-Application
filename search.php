<?php 
include("header.php");

$classCode = $_GET['classCode'] ?? '';
$searchPost = $_GET['searchedPost'] ?? '';
$body = $post_id = '';

if(empty($classCode)) {
    header("Location: index.php");
    exit();
}

// Sécurité SQL
$classCode_esc = mysqli_real_escape_string($con, $classCode);

// Vérification classe existe
$class_query = mysqli_query($con, "SELECT * FROM createclass WHERE courseCode='$classCode_esc'");
if(mysqli_num_rows($class_query) == 0) {
    echo "<script>alert('Classe introuvable !'); history.back();</script>";
    exit();
}

$user_array = mysqli_fetch_array($class_query);
$courseName = htmlspecialchars($user_array['className']);
$sec = htmlspecialchars($user_array['section']);
$classID = $user_array['id'];
$teacherName = htmlspecialchars($user_array['username']);

// Détails enseignant
$teacher_query = mysqli_query($con, "SELECT * FROM users WHERE username='$teacherName'");
$teacherDetails = mysqli_fetch_array($teacher_query) ?: ['first_name' => 'N/A', 'last_name' => '', 'profilePic' => 'assets/images/default-profile.png'];

// Recherche
if(isset($_POST['search__btn'])) {
    $searchPost = trim($_POST['searched_text']);
    header("Location: search.php?classCode=$classCode&searchedPost=" . urlencode($searchPost));
    exit();
}

// Edit post
if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $post_id_esc = mysqli_real_escape_string($con, $post_id);
    $data_query = mysqli_query($con, "SELECT * FROM posts WHERE id='$post_id_esc'");
    if($row = mysqli_fetch_array($data_query)) {
        $body = htmlspecialchars($row['body']);
    }
}

if(isset($_POST['update'])) {
    $post = new Post($con, $_SESSION['username'], $classCode);
    $post->submitEditPost(trim($_POST['editedPost_text']), $post_id);
    header("Location: search.php?classCode=$classCode&searchedPost=" . urlencode($searchPost));
    exit();
}

if(isset($_POST['cancel'])) {
    header("Location: search.php?classCode=$classCode&searchedPost=" . urlencode($searchPost));
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - <?php echo $courseName; ?></title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== SEARCH NEUMORPHISM 2026 ===== */
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --neumo-inset: inset 6px 6px 12px #e1e5e9, inset -6px -6px 12px #ffffff;
            --accent-500: #4f46e5; --accent-600: #4338ca;
            --success-500: #10b981; --warning-500: #f59e0b;
            --text-dark: #1e293b; --text-light: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-primary); 
            min-height: 100vh; 
            padding-top: 100px;
        }

        .Wrapper2 {
            max-width: 1400px; margin: 2rem auto; padding: 0 2rem; 
            display: grid; grid-template-columns: 300px 300px 1fr; gap: 2rem;
        }

        /* ===== USER DETAILS ===== */
        .user_details {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); height: fit-content;
            position: sticky; top: 120px;
        }

        .user_details h1 {
            font-size: 2rem; font-weight: 700; text-align: center; margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .user_details h1 a {
            text-decoration: none; color: inherit;
        }

        .class-info { 
            color: var(--text-dark); font-size: 1.1rem; margin-bottom: 2rem; 
            line-height: 1.6;
        }

        .search__form {
            position: relative; margin-bottom: 1rem;
        }

        #search-bar {
            width: 100%; padding: 1.2rem 4rem 1.2rem 1.5rem;
            background: var(--card-bg); border: none; border-radius: 24px;
            box-shadow: var(--neumo-inset); font-size: 1rem;
            transition: all 0.3s ease;
        }

        #search-bar:focus {
            outline: none; box-shadow: var(--neumo-shadow);
            transform: translateY(-2px);
        }

        #search__btn {
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
            background: var(--accent-500); color: white; border: none;
            width: 45px; height: 45px; border-radius: 50%; cursor: pointer;
            box-shadow: var(--neumo-shadow); transition: all 0.3s ease;
        }

        #search__btn:hover {
            transform: translateY(-50%) scale(1.1);
            box-shadow: 6px 6px 16px rgba(79,70,229,0.4);
        }

        .search-stats {
            background: rgba(79,70,229,0.1); padding: 1.5rem; border-radius: 20px;
            text-align: center; margin-top: 1rem;
        }

        /* ===== PEOPLE COLUMN ===== */
        .people_column {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); height: fit-content;
            position: sticky; top: 120px;
        }

        .people_column h4 {
            color: var(--accent-500); font-size: 1.3rem; margin-bottom: 1.5rem;
        }

        .teacher-link {
            display: flex; align-items: center; gap: 1rem; color: var(--text-dark);
            text-decoration: none; padding: 1rem; border-radius: 16px;
            transition: all 0.3s ease; margin-bottom: 2rem;
        }

        .teacher-link:hover { background: rgba(79,70,229,0.1); transform: translateX(8px); }

        /* ===== MAIN SEARCH RESULTS ===== */
        .main_column {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); min-height: 70vh;
        }

        .search-header {
            display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;
            padding-bottom: 1.5rem; border-bottom: 2px solid rgba(79,70,229,0.1);
        }

        .search-header i { font-size: 2rem; color: var(--accent-500); }

        .search-term {
            color: var(--accent-500); font-weight: 600; font-size: 1.3rem;
        }

        .no-results {
            text-align: center; padding: 4rem 2rem; color: var(--text-light);
        }

        .no-results i { font-size: 5rem; opacity: 0.3; margin-bottom: 1.5rem; display: block; }

        /* ===== SEARCH RESULTS ===== */
        .search-result {
            background: rgba(255,255,255,0.7); border-radius: 24px; padding: 2rem;
            margin-bottom: 1.5rem; border-left: 5px solid var(--accent-500);
            transition: all 0.3s ease; cursor: pointer;
        }

        .search-result:hover {
            transform: translateY(-4px); box-shadow: var(--neumo-shadow);
            background: rgba(255,255,255,0.9);
        }

        .search-result h4 { color: var(--text-dark); margin-bottom: 0.5rem; }
        .search-result p { color: var(--text-light); line-height: 1.6; }

        /* ===== MODALS ===== */
        #modal, #modal2 {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0;
            width: 100%; height: 100%; backdrop-filter: blur(10px);
            background: rgba(0,0,0,0.6);
        }

        #modal_container, #edit_box {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            background: var(--card-bg); border-radius: 24px; padding: 2.5rem;
            box-shadow: 0 32px 64px rgba(0,0,0,0.3); max-width: 500px; width: 90%;
            text-align: center;
        }

        #close_btn { 
            position: absolute; right: 1.5rem; top: 1.5rem; font-size: 2rem;
            cursor: pointer; color: var(--text-light); transition: color 0.3s;
        }

        #close_btn:hover { color: var(--accent-500); }

        #edit_textarea {
            width: 100%; min-height: 150px; padding: 1.5rem; margin: 1rem 0;
            background: var(--card-bg); border: none; border-radius: 20px;
            box-shadow: var(--neumo-inset); font-family: inherit; resize: vertical;
        }

        .edit_box_btn {
            background: var(--card-bg); color: var(--accent-500); border: none;
            padding: 1rem 2rem; border-radius: 20px; font-weight: 600;
            cursor: pointer; box-shadow: var(--neumo-shadow); margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .edit_box_btn:hover {
            transform: translateY(-2px); box-shadow: 8px 8px 20px #d1d9e6;
        }

        @media (max-width: 1200px) { .Wrapper2 { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) { 
            .Wrapper2 { grid-template-columns: 1fr; padding: 0 1rem; }
            .user_details, .people_column, .main_column { position: static; }
        }
    </style>
</head>
<body>
    <div class="Wrapper2">
        <!-- USER DETAILS -->
        <div class="user_details cloumn">
            <h1>
                <i class="fa fa-chalkboard-teacher"></i>
                <a href="classRoom.php?classCode=<?php echo htmlspecialchars($classCode); ?>">
                    <?php echo $courseName; ?>
                </a>
            </h1>
            
            <div class="class-info">
                <strong>📚 Section:</strong> <?php echo $sec; ?><br>
                <strong>🔑 Code:</strong> <?php echo htmlspecialchars($classCode); ?>
                <span id="code_expand" style="cursor:pointer;float:right;font-size:1.2rem;">
                    <i class="fas fa-expand-arrows-alt"></i>
                </span>
            </div>
            
            <form action="" method="POST" class="search__form">
                <input type="text" placeholder="Rechercher dans les posts..." 
                       id="search-bar" name="searched_text" 
                       value="<?php echo htmlspecialchars($searchPost); ?>">
                <button id="search__btn" name="search__btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <?php if($searchPost): ?>
            <div class="search-stats">
                🔍 Recherche: "<strong><?php echo htmlspecialchars($searchPost); ?></strong>"
            </div>
            <?php endif; ?>
        </div>

        <!-- PEOPLE -->
        <div class="people_column">
            <h4>👨‍🏫 Instructeur:</h4>
            <a href="<?php echo htmlspecialchars($teacherName); ?>" class="teacher-link">
                <img src="<?php echo htmlspecialchars($teacherDetails['profilePic']); ?>" width="45" alt="Teacher">
                <?php echo htmlspecialchars($teacherDetails['first_name'] . ' ' . $teacherDetails['last_name']); ?>
            </a>
            <br>
            <p><strong>👥 Membres:</strong></p>
            <?php 
            $students = new User($con, $classCode, $_SESSION['username']);
            $students->getStudentsInfo($classID);
            ?>
        </div>

        <!-- MAIN SEARCH -->
        <div class="main_column">
            <div class="search-header">
                <i class="fas fa-search"></i>
                <h2 class="search-term">
                    <?php echo $searchPost ? 'Résultats pour "' . htmlspecialchars($searchPost) . '"' : 'Recherchez des posts...'; ?>
                </h2>
            </div>
            
            <?php 
            $post = new Post($con, $_SESSION['username'], $classCode);
            $post->searchPosts($searchPost);
            ?>
        </div>
    </div>

    <!-- MODALS -->
    <div id="modal">
        <div id="modal_container">
            <span id="close_btn">&times;</span>
            <h3>🔑 Code de la classe</h3>
            <p id="code_modal" style="font-size:2.5rem;font-weight:700;color:var(--accent-500);"><?php echo htmlspecialchars($classCode); ?></p>
        </div>
    </div>

    <div id="modal2">
        <div id="edit_box">
            <span id="close_btn2">&times;</span>
            <form class="edit_form" method="POST">
                <textarea name="editedPost_text" id="edit_textarea" placeholder="Modifier le post..."><?php echo $body; ?></textarea>
                <div style="display:flex;gap:1rem;justify-content:center;margin-top:1.5rem;">
                    <a href="search.php?classCode=<?php echo urlencode($classCode); ?>&searchedPost=<?php echo urlencode($searchPost); ?>">
                        <input type="submit" name="cancel" value="❌ Annuler" class="edit_box_btn">
                    </a>
                    <input type="submit" name="update" value="💾 Mettre à jour" class="edit_box_btn">
                </div>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    // Modal code
    $('#code_expand, #close_btn').click(function(){
        $('#modal').fadeToggle(300);
    });

    $('#close_btn2').click(function(){
        $('#modal2').fadeOut(300);
    });

    // Auto-focus search
    $('#search-bar').focus();

    // Effet clavier pour recherche
    $('#search-bar').on('keypress', function(e){
        if(e.which == 13) $('#search__btn').click();
    });
    </script>
</body>
</html>
