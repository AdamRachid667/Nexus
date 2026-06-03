<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['id'];

$posts = json_decode(file_get_contents('data/posts.json'), true);

// Garder tous les posts sauf celui a supprimer (si c'est le bon auteur)
$nouveaux_posts = [];
for ($i = 0; $i < count($posts); $i++) {
    if ($posts[$i]['id'] == $post_id && $posts[$i]['auteur'] == $_SESSION['user']) {
        // On supprime ce post (on ne l'ajoute pas)
    } else {
        $nouveaux_posts[] = $posts[$i];
    }
}

file_put_contents('data/posts.json', json_encode($nouveaux_posts));

header('Location: index.php');
?>
