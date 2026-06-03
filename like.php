<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];
$pseudo = $_SESSION['user'];

// Verifier si deja like
$result = mysqli_query($connexion, "SELECT * FROM likes WHERE post_id = $post_id AND pseudo = '$pseudo'");

if (mysqli_num_rows($result) > 0) {
    // Unlike
    mysqli_query($connexion, "DELETE FROM likes WHERE post_id = $post_id AND pseudo = '$pseudo'");
    mysqli_query($connexion, "UPDATE posts SET likes = likes - 1 WHERE id = $post_id");
} else {
    // Like
    mysqli_query($connexion, "INSERT INTO likes (post_id, pseudo) VALUES ($post_id, '$pseudo')");
    mysqli_query($connexion, "UPDATE posts SET likes = likes + 1 WHERE id = $post_id");
}

// Recuperer le nouveau nombre de likes
$result = mysqli_query($connexion, "SELECT likes FROM posts WHERE id = $post_id");
$post = mysqli_fetch_assoc($result);
echo json_encode(['likes' => $post['likes']]);
?>
