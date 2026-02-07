<?php 
include("header.php");

$username = isset($_GET['profile_username']) ? $_GET['profile_username'] : '';
$userFullName = $email = $firstName = $lastName = $phoneNumber = $bio = $profilePic = "";
$success_message = $error_message = "";

if (!empty($username)) {
    $username_escaped = mysqli_real_escape_string($con, $username);
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username_escaped' LIMIT 1");
    
    if(mysqli_num_rows($user_details_query) > 0) {
        $user_array = mysqli_fetch_array($user_details_query);
        $userFullName = htmlspecialchars($user_array['first_name'] . " " . $user_array['last_name']);
        $email = htmlspecialchars($user_array['email']);
        $firstName = htmlspecialchars($user_array['first_name']);
        $lastName = htmlspecialchars($user_array['last_name']);
        
        // ✅ FIX LIGNE 18
        $phoneNumber = isset($user_array['phoneNumber']) ? htmlspecialchars($user_array['phoneNumber']) : '';
        
        // ✅ FIX LIGNE 19
        $bio = isset($user_array['bio']) ? htmlspecialchars($user_array['bio']) : '';
        
        $profilePic = isset($user_array['profilePic']) ? $user_array['profilePic'] : '';
    }
}


// UPDATE SÉCURISÉ
if (isset($_POST['profile-updateBtn']) && !empty($username)) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $bio = trim($_POST['bio']);
    
    if (strlen($firstName) >= 2 && strlen($lastName) >= 2) {
        $firstName_esc = mysqli_real_escape_string($con, $firstName);
        $lastName_esc = mysqli_real_escape_string($con, $lastName);
        $phoneNumber_esc = mysqli_real_escape_string($con, $phoneNumber);
        $bio_esc = mysqli_real_escape_string($con, $bio);
        $username_esc = mysqli_real_escape_string($con, $username);
        
        mysqli_query($con, "UPDATE users SET first_name='$firstName_esc', last_name='$lastName_esc', phoneNumber='$phoneNumber_esc', bio='$bio_esc' WHERE username='$username_esc'");
        $success_message = "✅ Profil mis à jour !";
        header("Location: $username");
        exit();
    }
}

$editBtn = (isset($_SESSION['username']) && $_SESSION['username'] == $username) 
    ? '<button class="edit-btn" onclick="openEdit()"><i class="fas fa-edit"></i> Modifier</button>' 
    : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - E-Learning</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
        :root {
            --bg-primary: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 50%, #f8fafc 100%);
            --card-bg: #f8fafc;
            --neumo-shadow: 12px 12px 24px #e1e5e9, -12px -12px 24px #ffffff;
            --accent-500: #4f46e5;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-primary); 
            padding: 100px 2rem 2rem;
        }
        .profile-container {
            max-width: 1000px; margin: 0 auto; 
            display: grid; grid-template-columns: 300px 1fr; gap: 3rem;
        }
        .side-bar, .profile {
            background: var(--card-bg); border-radius: 32px; padding: 2.5rem;
            box-shadow: var(--neumo-shadow); transition: all 0.4s ease;
        }
        .side-bar:hover, .profile:hover { transform: translateY(-8px); }
        .profile__image img {
            width: 140px; height: 140px; border-radius: 50%; object-fit: cover;
            box-shadow: var(--neumo-shadow);
        }
        .edit-btn {
            position: absolute; top: 2rem; right: 2rem; background: var(--card-bg);
            color: var(--accent-500); padding: 0.8rem 1.5rem; border-radius: 20px;
            cursor: pointer; box-shadow: var(--neumo-shadow);
        }
        .backdrop {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 2000;
        }
        .modal {
            display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%);
            background: var(--card-bg); padding: 3rem; border-radius: 32px; z-index: 2001;
            max-width: 500px; width: 90vw;
        }
        .form-group input, .form-group textarea {
            width: 100%; padding: 1rem; border-radius: 16px; border: none;
            background: var(--card-bg); box-shadow: inset 4px 4px 8px #e1e5e9;
        }
        @media (max-width: 768px) {
            .profile-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <section class="side-bar">
            <div class="profile__image">
                <img src="<?php echo $profilePic ?: 'assets/images/default-profile.png'; ?>" alt="Profil">
            </div>
            <a href="upload.php">📸 Nouvelle photo</a>
            <p><strong>@<?php echo htmlspecialchars($username); ?></strong></p>
        </section>
        
        <section class="profile" style="position: relative;">
            <?php echo $editBtn; ?>
            <div style="display: grid; gap: 1.5rem;">
                <div><strong>👤 Nom:</strong> <?php echo $userFullName; ?></div>
                <div><strong>📧 Email:</strong> <?php echo $email; ?></div>
                <div><strong>📱 Téléphone:</strong> <?php echo $phoneNumber ?: 'Non renseigné'; ?></div>
                <?php if($bio): ?><div style="grid-column: 1;-1;"><strong>💬 Bio:</strong> <?php echo $bio; ?></div><?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="backdrop" onclick="closeModal()"></div>
    <div class="modal">
        <form method="POST">
            <div class="form-group"><label>Prénom:</label><input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required></div>
            <div class="form-group"><label>Nom:</label><input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required></div>
            <div class="form-group"><label>Téléphone:</label><input type="tel" name="phoneNumber" value="<?php echo htmlspecialchars($phoneNumber); ?>"></div>
            <div class="form-group"><label>Bio:</label><textarea name="bio"><?php echo htmlspecialchars($bio); ?></textarea></div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="closeBtn" onclick="closeModal()" style="padding:1rem 2rem;border-radius:16px;background:var(--card-bg);">Annuler</button>
                <input type="submit" name="profile-updateBtn" value="Mettre à jour" style="padding:1rem 2rem;border-radius:16px;background:var(--card-bg);color:#10b981;cursor:pointer;">
            </div>
        </form>
    </div>

    <script>
        function openEdit() { document.querySelector('.modal').style.display = 'block'; document.querySelector('.backdrop').style.display = 'block'; }
        function closeModal() { document.querySelector('.modal').style.display = 'none'; document.querySelector('.backdrop').style.display = 'none'; }
    </script>
</body>
</html>
