<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];
$texte = $data['texte'];

if ($texte == '') {
    echo json_encode(['error' => 'Texte vide']);
    exit;
}

$posts = json_decode(file_get_contents('data/posts.json'), true);

// Chercher le post et ajouter le commentaire
for ($i = 0; $i < count($posts); $i++) {
    if ($posts[$i]['id'] == $post_id) {
        $commentaire = [
            'auteur' => $_SESSION['user'],
            'texte' => $texte,
            'date' => time()
        ];
        $posts[$i]['commentaires'][] = $commentaire;
        file_put_contents('data/posts.json', json_encode($posts));
        echo json_encode(['success' => true, 'commentaire' => $commentaire]);
        exit;
    }
}
?>
