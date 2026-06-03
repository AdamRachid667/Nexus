<?php
session_start();
require 'db.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    $result = mysqli_query($connexion, "SELECT * FROM users WHERE pseudo = '$pseudo'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($mdp, $user['mdp'])) {
        $_SESSION['user'] = $pseudo;
        header('Location: index.php');
        exit;
    } else {
        $erreur = 'Pseudo ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Nexus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-content">
        <h1><a href="index.php">Nexus</a></h1>
    </div>
</header>

<main>
    <section class="form-page">
        <h2>Connexion</h2>
        <?php if ($erreur != ''): ?>
            <p class="error-msg visible"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="pseudo" placeholder="Pseudo" required>
            <input type="password" name="mdp" placeholder="Mot de passe" required>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <p class="form-link">Pas encore de compte ? <a href="register.php">Inscris-toi</a></p>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>
