<?php
session_start();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];
    $mdp2 = $_POST['mdp2'];

    if ($pseudo == '' || $mdp == '' || $mdp2 == '') {
        $erreur = 'Remplis tous les champs';
    } elseif ($mdp != $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas';
    } else {
        // Charger les utilisateurs
        $users = json_decode(file_get_contents('data/users.json'), true);

        // Verifier si le pseudo existe
        $existe = false;
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['pseudo'] == $pseudo) {
                $existe = true;
            }
        }

        if ($existe) {
            $erreur = 'Ce pseudo est deja pris';
        } else {
            // Creer le compte
            $users[] = [
                'pseudo' => $pseudo,
                'mdp' => password_hash($mdp, PASSWORD_DEFAULT),
                'date_inscription' => time()
            ];
            file_put_contents('data/users.json', json_encode($users));

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
