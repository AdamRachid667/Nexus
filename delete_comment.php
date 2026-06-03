<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];
$comment_index = $data['comment_index'];

$posts = json_decode(file_get_contents('data/posts.json'), true);

// Chercher le post et supprimer le commentaire
for ($i = 0; $i < count($posts); $i++) {
    if ($posts[$i]['id'] == $post_id) {
        // Verifier que c'est bien l'auteur du commentaire
        if ($posts[$i]['commentaires'][$comment_index]['auteur'] == $_SESSION['user']) {
            array_splice($posts[$i]['commentaires'], $comment_index, 1);
            file_put_contents('data/posts.json', json_encode($posts));
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['error' => 'Impossible de supprimer']);
?>
