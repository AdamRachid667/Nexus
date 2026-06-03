<?php
session_start();
require 'db.php';

$erreur = '';

// si le formulaire est envoye
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];
    $mdp2 = $_POST['mdp2'];

    // je verifie les erreurs
    if ($pseudo == '' || $mdp == '' || $mdp2 == '') {
        $erreur = 'Remplis tous les champs';
    } elseif ($mdp != $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas';
    } else {
        // je verifie si le pseudo est deja pris
        $result = mysqli_query($connexion, "SELECT * FROM users WHERE pseudo = '$pseudo'");
        if (mysqli_num_rows($result) > 0) {
            $erreur = 'Ce pseudo est deja pris';
        } else {
            // password_hash() ca transforme le mot de passe en hash (impossible a lire)
            $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
            // j'insere le nouvel utilisateur dans ma table users
            mysqli_query($connexion, "INSERT INTO users (pseudo, mdp) VALUES ('$pseudo', '$mdp_hash')");
            // je le connecte directement apres l'inscription
            $_SESSION['user'] = $pseudo;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Nexus</title>
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
        <h2>Inscription</h2>
        <?php if ($erreur != ''): ?>
            <p class="error-msg visible"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="pseudo" placeholder="Pseudo" required>
            <input type="password" name="mdp" placeholder="Mot de passe" required>
            <input type="password" name="mdp2" placeholder="Confirmer le mot de passe" required>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        <p class="form-link">Deja un compte ? <a href="login.php">Connecte-toi</a></p>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>
