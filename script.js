// === VALIDATION DU FORMULAIRE DE POST ===
var formPost = document.getElementById('form-post');
if (formPost) {
    formPost.addEventListener('submit', function(e) {
        var titre = document.getElementById('titre').value.trim();
        var contenu = document.getElementById('contenu').value.trim();
        var erreur = document.getElementById('error-msg');

        // Validation
        if (titre == '' || contenu == '') {
            e.preventDefault();
            erreur.textContent = 'Le titre et le contenu sont obligatoires !';
            erreur.style.display = 'block';
            return;
        }

        if (titre.length > 100) {
            e.preventDefault();
            erreur.textContent = 'Le titre ne peut pas dépasser 100 caractères !';
            erreur.style.display = 'block';
            return;
        }

        if (contenu.length > 2000) {
            e.preventDefault();
            erreur.textContent = 'Le contenu ne peut pas dépasser 2000 caractères !';
            erreur.style.display = 'block';
            return;
        }

        // Verifier l'image
        var image = document.getElementById('image');
        if (image.files.length > 0) {
            var fichier = image.files[0];
            var extensions = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (extensions.indexOf(fichier.type) == -1) {
                e.preventDefault();
                erreur.textContent = 'Format d\'image non supporté ! (JPG, PNG, GIF, WEBP)';
                erreur.style.display = 'block';
                return;
            }
            if (fichier.size > 5000000) {
                e.preventDefault();
                erreur.textContent = 'L\'image ne doit pas dépasser 5 Mo !';
                erreur.style.display = 'block';
                return;
            }
        }

        erreur.style.display = 'none';
    });
}

// === VALIDATION DU FORMULAIRE D'INSCRIPTION ===
var formRegister = document.getElementById('form-register');
if (formRegister) {
    formRegister.addEventListener('submit', function(e) {
        var pseudo = formRegister.querySelector('input[name="pseudo"]').value.trim();
        var mdp = formRegister.querySelector('input[name="mdp"]').value;
        var mdp2 = formRegister.querySelector('input[name="mdp2"]').value;

        if (pseudo.length < 3 || pseudo.length > 20) {
            e.preventDefault();
            alert('Le pseudo doit faire entre 3 et 20 caractères.');
            return;
        }

        if (mdp.length < 4) {
            e.preventDefault();
            alert('Le mot de passe doit faire au moins 4 caractères.');
            return;
        }

        if (mdp != mdp2) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas !');
            return;
        }
    });
}

// === SYSTEME DE LIKE (AJAX) ===
function likePost(id) {
    fetch('like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.likes !== undefined) {
            document.getElementById('likes-' + id).textContent = data.likes;
        } else if (data.error == 'Non connecte') {
            alert('Connecte-toi pour liker !');
        }
    });
}

// === AFFICHER/MASQUER COMMENTAIRES ===
function toggleComments(id) {
    var section = document.getElementById('comments-' + id);
    if (section.style.display == 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// === AJOUTER UN COMMENTAIRE (AJAX) ===
function addComment(e, postId) {
    e.preventDefault();
    var input = document.getElementById('comment-input-' + postId);
    var texte = input.value.trim();

    if (texte == '') return;

    if (texte.length > 500) {
        alert('Commentaire trop long (max 500 caractères) !');
        return;
    }

    fetch('add_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: postId, texte: texte})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            // Ajouter le commentaire dans la page
            var liste = document.getElementById('comments-' + postId).querySelector('.comments-list');
            var div = document.createElement('div');
            div.className = 'comment';
            div.innerHTML = '<strong>' + escapeHtml(data.commentaire.auteur) + '</strong>' +
                '<span class="comment-date"> à l\'instant</span>' +
                '<p>' + escapeHtml(data.commentaire.texte) + '</p>';
            liste.insertBefore(div, liste.firstChild);
            input.value = '';
        } else if (data.error == 'Non connecte') {
            alert('Connecte-toi pour commenter !');
        }
    });
}

// === SUPPRIMER UN COMMENTAIRE ===
function deleteComment(postId, index) {
    if (!confirm('Supprimer ce commentaire ?')) return;

    fetch('delete_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({post_id: postId, comment_index: index})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('comment-' + postId + '-' + index).remove();
        }
    });
}

// === PROTECTION XSS ===
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// === MODE SOMBRE / CLAIR ===
var btnDark = document.getElementById('btn-dark-mode');
if (btnDark) {
    // Charger la preference
    if (localStorage.getItem('theme') == 'light') {
        document.body.classList.add('light-mode');
        btnDark.textContent = 'Mode';
    }

    btnDark.addEventListener('click', function() {
        document.body.classList.toggle('light-mode');
        if (document.body.classList.contains('light-mode')) {
            localStorage.setItem('theme', 'light');
            btnDark.textContent = 'Mode';
        } else {
            localStorage.setItem('theme', 'dark');
            btnDark.textContent = 'Mode';
        }
    });
}
