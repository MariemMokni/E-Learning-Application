<?php 
include("header.php");

$classCode = isset($_GET['classCode']) ? $_GET['classCode'] : '';
 if(empty($classCode)) {
    header("Location: index.php");
    exit();
}

// Sécurité SQL
$classCode_esc = mysqli_real_escape_string($con, $classCode);

// Récupération classe
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

// Gestion POST sécurisée
if(isset($_POST['post'])) {
    $post_text = mysqli_real_escape_string($con, trim($_POST['post_text']));
    if(!empty($post_text)) {
        $post = new Post($con, $_SESSION['username'], $classCode);
        $post->submitPost($post_text, 'none', 'none', $teacherName);
    }
    header("Location: classRoom.php?classCode=$classCode");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $courseName; ?> - E-Learning</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== CLASSROOM NEUMORPHISM 2026 ===== */
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
            font-size: 2.2rem; font-weight: 700; text-align: center;
            background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .class-info { 
            color: var(--text-dark); font-size: 1.1rem; margin-bottom: 2rem; 
            line-height: 1.6;
        }

        .search__form {
            position: relative; margin-bottom: 2rem;
        }

        #search-bar {
            width: 100%; padding: 1.2rem 1.5rem 1.2rem 3rem;
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
            background: none; border: none; color: var(--accent-500);
            font-size: 1.2rem; cursor: pointer;
        }

        .assignment_box {
            display: flex; flex-direction: column; gap: 1rem;
        }

        .assignment_box a {
            display: flex; align-items: center; gap: 1rem;
            padding: 1.2rem; background: var(--card-bg); border-radius: 20px;
            text-decoration: none; color: var(--text-dark); font-weight: 600;
            box-shadow: var(--neumo-shadow); transition: all 0.3s ease;
            position: relative;
        }

        .assignment_box a:hover {
            transform: translateY(-4px); box-shadow: 10px 10px 25px #d1d9e6, -10px -10px 25px #ffffff;
            color: var(--accent-500);
        }

        /* ===== PEOPLE COLUMN ===== */
        .people_column {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); height: fit-content;
            position: sticky; top: 120px;
        }

        .people_column h4 {
            color: var(--accent-500); font-size: 1.3rem; margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .teacher-link {
            display: flex; align-items: center; gap: 1rem; color: var(--text-dark);
            text-decoration: none; padding: 1rem; border-radius: 16px;
            transition: all 0.3s ease; margin-bottom: 2rem;
        }

        .teacher-link:hover {
            background: rgba(79,70,229,0.1); transform: translateX(8px);
        }

        .teacher-link img {
            width: 45px; height: 45px; border-radius: 50%; object-fit: cover;
            box-shadow: var(--neumo-shadow);
        }

        .students-list {
            max-height: 300px; overflow-y: auto; padding-right: 1rem;
        }

        /* ===== MAIN COLUMN ===== */
        .main_column {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); min-height: 70vh;
        }

        /* ===== POST FORM ===== */
        .post_form textarea {
            width: 100%; min-height: 120px; padding: 1.5rem;
            background: var(--card-bg); border: none; border-radius: 24px;
            box-shadow: var(--neumo-inset); font-family: inherit;
            font-size: 1.1rem; resize: vertical; margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .post_form textarea:focus {
            outline: none; box-shadow: var(--neumo-shadow);
            transform: translateY(-2px);
        }

        #post_button {
            background: var(--gradient-primary, linear-gradient(135deg,var(--accent-500),var(--accent-600)));
            color: white; border: none; padding: 1rem 2.5rem;
            border-radius: 24px; font-weight: 600; cursor: pointer;
            box-shadow: var(--neumo-shadow); transition: all 0.3s ease;
            float: right;
        }

        #post_button:hover {
            transform: translateY(-3px); box-shadow: 8px 8px 20px rgba(79,70,229,0.4);
        }

        /* ===== MODALS ===== */
        #modal, #modal2 {
            display: none; position: fixed; z-index: 2000;
            left: 0; top: 0; width: 100%; height: 100%;
            backdrop-filter: blur(10px); background: rgba(0,0,0,0.6);
        }

        #modal_container, #edit_box {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            background: var(--card-bg); border-radius: 24px; padding: 2rem;
            box-shadow: 0 24px 48px rgba(0,0,0,0.3);
            text-align: center; max-width: 400px;
        }

        #close_btn { 
            position: absolute; right: 1rem; top: 1rem; font-size: 2rem;
            cursor: pointer; color: var(--text-light); 
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .Wrapper2 { grid-template-columns: 1fr 1fr; }
        }
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
            <h1><i class="fa fa-chalkboard-teacher"></i> <?php echo $courseName; ?></h1>
            <div class="class-info">
                <strong>📚 Section:</strong> <?php echo $sec; ?><br>
                <strong>🔑 Code:</strong> <?php echo $classCode; ?>
                <span id="code_expand" style="cursor:pointer;float:right;"><i class="fas fa-expand-arrows-alt"></i></span>
            </div>
            
            <form action="" method="POST" class="search__form">
                <input type="text" placeholder="Rechercher des posts..." id="search-bar" name="searched_text">
                <button id="search__btn" name="search__btn"><i class="fas fa-search"></i></button>
            </form>
            
            <div class="assignment_box">
                <a href="#" id="postBtn">
                    <i class="fab fa-megaport"></i> Poster
                </a>
                <a href="#" id="assignmentBtn">
                    <i class="far fa-file-word"></i> Devoirs
                </a>
            </div>
        </div>

        <!-- PEOPLE -->
        <div class="people_column">
            <h4>👨‍🏫 Instructeur :</h4>
            <a href="<?php echo $teacherName; ?>" class="teacher-link">
                <img src="<?php echo htmlspecialchars($teacherDetails['profilePic']); ?>" alt="Teacher">
                <?php echo htmlspecialchars($teacherDetails['first_name'] . ' ' . $teacherDetails['last_name']); ?>
            </a>
            
            <div class="students-list">
                <p><strong>👥 Membres:</strong></p>
                <?php 
                $students = new User($con, $classCode, $_SESSION['username']);
                $students->getStudentsInfo($classID);
                ?>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main_column">
            <div id="first">
                <form class="post_form" method="POST">
                    <textarea name="post_text" id="post_text_area" placeholder="Partagez quelque chose avec la classe..."></textarea>
                    <input type="submit" name="post" id="post_button" value="📤 Publier">
                </form>
                
                <?php
                $post = new Post($con, $_SESSION['username'], $classCode);
                $post->loadPosts();
                ?>
            </div>

            <div id="second" style="display:none;">
                <form class="assignment_form" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file" id="fileToUpload" accept=".pdf,.docx,.doc,.xlsx,.pptx,.jpg,.png">
                    <textarea name="assignment_text" id="assignment-textarea" placeholder="Description du devoir..."></textarea>
                    <input type="submit" name="upload" id="assignment-upload-button" value="📁 Publier">
                </form>
                <hr style="margin: 2rem 0; opacity: 0.3;">
                <?php $post->loadFiles(); ?>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    <div id="modal">
        <div id="modal_container">
            <span id="close_btn">&times;</span>
            <h3>🔑 Code de la classe</h3>
            <p id="code_modal" style="font-size:2rem;font-weight:700;color:var(--accent-500);"><?php echo $classCode; ?></p>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    // Modal code
    $('#code_expand, #close_btn').click(function(){
        $('#modal').fadeToggle(300);
    });

    // Toggle Post/Assignment
    $("#postBtn").click(function(e){
        e.preventDefault();
        $("#first").slideDown("slow");
        $("#second").slideUp("slow");
    });

    $("#assignmentBtn").click(function(e){
        e.preventDefault();
        $("#second").slideDown("slow");
        $("#first").slideUp("slow");
    });

    // Auto-resize textarea
    $('textarea').each(function(){
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px');
        $(this).on('input', function(){ this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'; });
    });
    </script>
</body>
</html>
