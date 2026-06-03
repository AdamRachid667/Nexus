<?php
session_start();

$query = $_GET['q'] ?? '';
$resultats = [];

if ($query != '') {
    $posts = json_decode(file_get_contents('data/posts.json'), true);

    // Chercher dans les posts
    for ($i = 0; $i < count($posts); $i++) {
        if (strpos(strtolower($posts[$i]['titre']), strtolower($query)) !== false ||
            strpos(strtolower($posts[$i]['contenu']), strtolower($query)) !== false) {
            $resultats[] = $posts[$i];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - Nexus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-content">
        <h1><a href="index.php">Nexus</a></h1>
    </div>
</header>

<main>
    <section class="search-section">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($query); ?>" required>
            <button type="submit" class="btn btn-small">Rechercher</button>
        </form>
    </section>

    <section class="posts">
        <h2>Resultats (<?php echo count($resultats); ?>)</h2>

        <?php if (empty($resultats)): ?>
            <p class="no-posts">Aucun resultat</p>
        <?php endif; ?>

        <?php for ($i = 0; $i < count($resultats); $i++): ?>
        <article class="post-card">
            <div class="post-header">
                <span class="post-author">👤 <?php echo $resultats[$i]['auteur']; ?></span>
                <span class="post-date"><?php echo date('d/m/Y H:i', $resultats[$i]['date']); ?></span>
            </div>
            <h3><?php echo $resultats[$i]['titre']; ?></h3>
            <p><?php echo $resultats[$i]['contenu']; ?></p>
        </article>
        <?php endfor; ?>
    </section>

    <a href="index.php" class="btn btn-primary" style="margin-top:20px;">Retour</a>
</main>

<script src="script.js"></script>
</body>
</html>
