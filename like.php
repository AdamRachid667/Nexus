<?php
session_start();
require 'db.php';

// si pas connecte, j'arrete
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

// je recupere l'id du post que le JavaScript m'envoie
$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];
$pseudo = $_SESSION['user'];

// je verifie si j'ai deja like ce post
$result = mysqli_query($connexion, "SELECT * FROM likes WHERE post_id = $post_id AND pseudo = '$pseudo'");

if (mysqli_num_rows($result) > 0) {
    // j'ai deja like, donc je unlike (je supprime mon like)
    mysqli_query($connexion, "DELETE FROM likes WHERE post_id = $post_id AND pseudo = '$pseudo'");
    mysqli_query($connexion, "UPDATE posts SET likes = likes - 1 WHERE id = $post_id");
} else {
    // j'ai pas encore like, donc je like
    mysqli_query($connexion, "INSERT INTO likes (post_id, pseudo) VALUES ($post_id, '$pseudo')");
    mysqli_query($connexion, "UPDATE posts SET likes = likes + 1 WHERE id = $post_id");
}

// je renvoie le nouveau nombre de likes au JavaScript pour mettre a jour l'affichage
$result = mysqli_query($connexion, "SELECT likes FROM posts WHERE id = $post_id");
$post = mysqli_fetch_assoc($result);
echo json_encode(['likes' => $post['likes']]);
?>
