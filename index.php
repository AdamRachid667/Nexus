<?php
// je demarre la session pour garder en memoire qui est connecte
session_start();

// je me connecte a ma base de donnees
require 'db.php';

// je regarde si l'utilisateur veut trier par date ou par likes
$tri = $_GET['tri'] ?? 'date';

if ($tri == 'likes') {
    // je recupere tous les posts
    $result = mysqli_query($connexion, "SELECT * FROM posts ORDER BY date_creation DESC");
    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }

    // TRI A BULLE : je trie mes posts du plus like au moins like
    // je compare chaque post avec son voisin, et je les echange si c'est pas dans le bon ordre
    $n = count($posts);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($posts[$j]['likes'] < $posts[$j + 1]['likes']) {
                // j'echange les deux posts
                $temp = $posts[$j];
                $posts[$j] = $posts[$j + 1];
                $posts[$j + 1] = $temp;
            }
        }
    }
} else {
    // par defaut je trie par date, le plus recent en premier
    $result = mysqli_query($connexion, "SELECT * FROM posts ORDER BY date_creation DESC");
    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus - Réseau Social Gaming</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- mon header avec le nom du site et la navigation -->
<header>
    <div class="header-content">
        <h1>Nexus</h1>
        <nav>
            <!-- si l'utilisateur est connecte j'affiche son pseudo -->
            <?php if (isset($_SESSION['user'])): ?>
                <span class="user-info">👤 <?php echo $_SESSION['user']; ?></span>
                <a href="logout.php" class="btn btn-small">Deconnexion</a>
            <!-- sinon j'affiche les boutons connexion/inscription -->
            <?php else: ?>
                <a href="login.php" class="btn btn-small">Connexion</a>
                <a href="register.php" class="btn btn-small btn-accent">Inscription</a>
            <?php endif; ?>
            <!-- bouton pour switch entre mode sombre et clair -->
            <button id="btn-dark-mode" class="btn btn-small">Mode</button>
        </nav>
    </div>
</header>

<main>
    <!-- ma barre de recherche -->
    <section class="search-section">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Rechercher un post..." required>
            <button type="submit" class="btn btn-small">Rechercher</button>
        </form>
    </section>

    <!-- formulaire pour publier un post, visible seulement si connecte -->
    <?php if (isset($_SESSION['user'])): ?>
    <section class="new-post-section">
        <h2>Publier un post</h2>
        <!-- enctype multipart/form-data c'est obligatoire pour envoyer des fichiers -->
        <form action="add_post.php" method="POST" enctype="multipart/form-data" id="form-post">
            <input type="text" name="titre" id="titre" placeholder="Titre de ton post..." required>
            <textarea name="contenu" id="contenu" placeholder="Raconte ta game..." required></textarea>
            <div class="form-row">
                <input type="file" name="image" id="image" accept="image/*">
                <button type="submit" class="btn btn-primary">Publier</button>
            </div>
            <p class="error-msg" id="error-msg"></p>
        </form>
    </section>
    <?php endif; ?>

    <!-- mes boutons de tri -->
    <section class="tri-section">
        <h2>Fil d'actualite</h2>
        <div class="tri-btns">
            <!-- le ?tri=date dans l'url c'est comme ca que je dis au PHP quel tri je veux -->
            <a href="index.php?tri=date" class="btn btn-small <?php echo ($tri == 'date') ? 'active' : ''; ?>">Plus recents</a>
            <a href="index.php?tri=likes" class="btn btn-small <?php echo ($tri == 'likes') ? 'active' : ''; ?>">Plus likes</a>
        </div>
    </section>

    <!-- j'affiche tous les posts ici -->
    <section class="posts">
        <?php if (empty($posts)): ?>
            <p class="no-posts">Aucun post pour le moment. Sois le premier a publier !</p>
        <?php endif; ?>

        <!-- je boucle sur chaque post -->
        <?php for ($i = 0; $i < count($posts); $i++): ?>
        <?php
            $post = $posts[$i];
            // je recupere les commentaires de ce post
            $result_com = mysqli_query($connexion, "SELECT * FROM commentaires WHERE post_id = " . $post['id'] . " ORDER BY date_creation DESC");
            $commentaires = [];
            while ($com = mysqli_fetch_assoc($result_com)) {
                $commentaires[] = $com;
            }
        ?>
        <article class="post-card">
            <div class="post-header">
                <span class="post-author">👤 <?php echo $post['auteur']; ?></span>
                <!-- strtotime() ca convertit la date MySQL en format lisible -->
                <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['date_creation'])); ?></span>
            </div>
            <h3><?php echo $post['titre']; ?></h3>
            <!-- nl2br() ca transforme les retours a la ligne en <br> -->
            <p><?php echo nl2br($post['contenu']); ?></p>
            <!-- j'affiche l'image seulement si y en a une -->
            <?php if ($post['image'] != ''): ?>
                <img src="<?php echo $post['image']; ?>" alt="Image du post" class="post-image">
            <?php endif; ?>

            <!-- les boutons d'action : like, commentaire, supprimer -->
            <div class="post-actions">
                <!-- quand je clique ca appelle la fonction JS likePost() -->
                <button class="btn-like" onclick="likePost(<?php echo $post['id']; ?>)">
                    Like <span id="likes-<?php echo $post['id']; ?>"><?php echo $post['likes']; ?></span>
                </button>
                <button class="btn-comment" onclick="toggleComments(<?php echo $post['id']; ?>)">
                    <?php echo count($commentaires); ?> commentaire(s)
                </button>
                <!-- le bouton supprimer apparait seulement si c'est mon post -->
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] == $post['auteur']): ?>
                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer ce post ?')">Supprimer</a>
                <?php endif; ?>
            </div>

            <!-- les commentaires, caches par defaut, s'affichent quand je clique -->
            <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display:none;">
                <div class="comments-list">
                    <?php for ($c = 0; $c < count($commentaires); $c++): ?>
                        <div class="comment" id="comment-<?php echo $commentaires[$c]['id']; ?>">
                            <strong><?php echo $commentaires[$c]['auteur']; ?></strong>
                            <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($commentaires[$c]['date_creation'])); ?></span>
                            <!-- bouton supprimer seulement si c'est mon commentaire -->
                            <?php if (isset($_SESSION['user']) && $_SESSION['user'] == $commentaires[$c]['auteur']): ?>
                                <button class="btn-delete" onclick="deleteComment(<?php echo $commentaires[$c]['id']; ?>)">Supprimer</button>
                            <?php endif; ?>
                            <p><?php echo $commentaires[$c]['texte']; ?></p>
                        </div>
                    <?php endfor; ?>
                </div>
                <!-- formulaire pour ajouter un commentaire -->
                <?php if (isset($_SESSION['user'])): ?>
                <form class="comment-form" onsubmit="addComment(event, <?php echo $post['id']; ?>)">
                    <input type="text" placeholder="Ton commentaire..." id="comment-input-<?php echo $post['id']; ?>" required>
                    <button type="submit" class="btn btn-small">Envoyer</button>
                </form>
                <?php endif; ?>
            </div>
        </article>
        <?php endfor; ?>
    </section>
</main>

<footer>
    <p>Nexus 2026 - Reseau social gaming</p>
</footer>

<script src="script.js"></script>
</body>
</html>
