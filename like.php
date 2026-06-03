<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non connecte']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'];

$posts = json_decode(file_get_contents('data/posts.json'), true);

// Chercher le post et liker/unliker
for ($i = 0; $i < count($posts); $i++) {
    if ($posts[$i]['id'] == $post_id) {

        // Si deja like, on unlike
        if (in_array($_SESSION['user'], $posts[$i]['liked_by'])) {
            $posts[$i]['likes']--;
            $index = array_search($_SESSION['user'], $posts[$i]['liked_by']);
            unset($posts[$i]['liked_by'][$index]);
            $posts[$i]['liked_by'] = array_values($posts[$i]['liked_by']);
        } else {
            // Sinon on like
            $posts[$i]['likes']++;
            $posts[$i]['liked_by'][] = $_SESSION['user'];
        }

        file_put_contents('data/posts.json', json_encode($posts));
        echo json_encode(['likes' => $posts[$i]['likes']]);
        exit;
    }
}
?>
