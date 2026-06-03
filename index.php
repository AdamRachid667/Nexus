<?php
session_start();

// Charger les posts
$posts = [];
$fichier = 'data/posts.json';
if (file_exists($fichier)) {
    $contenu = file_get_contents($fichier);
    $posts = json_decode($contenu, true);
    if (!is_array($posts)) $posts = [];
}

// Tri des posts par date (plus recent en premier)
usort($posts, function($a, $b) {
    return $b['date'] - $a['date'];
});
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

<header>
    <div class="header-content">
        <h1>Nexus</h1>
        <nav>
            <?php if (isset($_SESSION['user'])): ?>
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <a href="logout.php" class="btn btn-small">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-small">Connexion</a>
                <a href="register.php" class="btn btn-small btn-accent">Inscription</a>
            <?php endif; ?>
            <button id="btn-dark-mode" class="btn btn-small">Dark Mode</button>
        </nav>
    </div>
</header>

<main>
    <!-- Recherche -->
    <section class="search-section">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Rechercher un post..." required>
            <button type="submit" class="btn btn-small">Rechercher</button>
        </form>
    </section>

    <!-- Formulaire de publication -->
    <?php if (isset($_SESSION['user'])): ?>
    <section class="new-post-section">
        <h2>Publier un post</h2>
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

    <!-- Boutons de tri -->
    <section class="tri-section">
        <h2>Fil d'actualité</h2>
        <div class="tri-btns">
            <a href="index.php?tri=date" class="btn btn-small <?php echo (!isset($_GET['tri']) || $_GET['tri'] == 'date') ? 'active' : ''; ?>">Plus récents en premier</a>
            <a href="index.php?tri=likes" class="btn btn-small <?php echo (isset($_GET['tri']) && $_GET['tri'] == 'likes') ? 'active' : ''; ?>">Plus de likes en premier</a>
        </div>
    </section>

    <!-- Algorithme de tri -->
    <?php
    if (isset($_GET['tri']) && $_GET['tri'] == 'likes') {
        // TRI A BULLE par nombre de likes (algorithme obligatoire)
        $n = count($posts);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($posts[$j]['likes'] < $posts[$j + 1]['likes']) {
                    $temp = $posts[$j];
                    $posts[$j] = $posts[$j + 1];
                    $posts[$j + 1] = $temp;
                }
            }
        }
    }
    ?>

    <!-- Affichage des posts -->
    <section class="posts">
        <?php if (empty($posts)): ?>
            <p class="no-posts">Aucun post pour le moment. Sois le premier a publier !</p>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
        <article class="post-card">
            <div class="post-header">
                <span class="post-author">👤 <?php echo htmlspecialchars($post['auteur']); ?></span>
                <span class="post-date"><?php echo date('d/m/Y H:i', $post['date']); ?></span>
            </div>
            <h3><?php echo htmlspecialchars($post['titre']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['contenu'])); ?></p>
            <?php if (!empty($post['image'])): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Image du post" class="post-image">
            <?php endif; ?>
            <div class="post-actions">
                <button class="btn-like" onclick="likePost('<?php echo $post['id']; ?>')">
                    Like <span id="likes-<?php echo $post['id']; ?>"><?php echo $post['likes']; ?></span>
                </button>
                <button class="btn-comment" onclick="toggleComments('<?php echo $post['id']; ?>')">
                    <?php echo count($post['commentaires']); ?> commentaire(s)
                </button>
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] == $post['auteur']): ?>
                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer ce post ?')">Supprimer</a>
                <?php endif; ?>
            </div>

            <!-- Section commentaires -->
            <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display:none;">
                <div class="comments-list">
                    <?php for ($c = 0; $c < count($post['commentaires']); $c++): ?>
                        <div class="comment" id="comment-<?php echo $post['id'] . '-' . $c; ?>">
                            <strong><?php echo $post['commentaires'][$c]['auteur']; ?></strong>
                            <span class="comment-date"><?php echo date('d/m/Y H:i', $post['commentaires'][$c]['date']); ?></span>
                            <?php if (isset($_SESSION['user']) && $_SESSION['user'] == $post['commentaires'][$c]['auteur']): ?>
                                <button class="btn-delete" onclick="deleteComment('<?php echo $post['id']; ?>', <?php echo $c; ?>)">Supprimer</button>
                            <?php endif; ?>
                            <p><?php echo $post['commentaires'][$c]['texte']; ?></p>
                        </div>
                    <?php endfor; ?>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                <form class="comment-form" onsubmit="addComment(event, '<?php echo $post['id']; ?>')">
                    <input type="text" placeholder="Ton commentaire..." id="comment-input-<?php echo $post['id']; ?>" required>
                    <button type="submit" class="btn btn-small">Envoyer</button>
                </form>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </section>


</main>

<footer>
    <p>Nexus © 2026 - Réseau social gaming</p>
</footer>

<script src="script.js"></script>
</body>
</html>
