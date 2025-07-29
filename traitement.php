<?php
// contact_form_handler.php
// Traitement sécurisé du formulaire de contact avec retours d’erreur HTTP

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom     = strip_tags(trim($_POST['nom']     ?? ''));
    $email   = filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL);
    $sujet   = strip_tags(trim($_POST['sujet']   ?? ''));
    $message = strip_tags(trim($_POST['message'] ?? ''));
    // Le champ "mode" n’existe que dans contact.html
    $mode    = isset($_POST['mode'])
               ? strip_tags(trim($_POST['mode']))
               : '';

    // Vérification des champs obligatoires
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        http_response_code(400);
        exit('Veuillez remplir tous les champs obligatoires.');
    }

    // Si le champ mode est soumis, il doit être non vide
    if (isset($_POST['mode']) && empty($mode)) {
        http_response_code(400);
        exit('Veuillez sélectionner un mode de contact.');
    }

    // Validation de l'adresse e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        exit('Adresse e‑mail invalide.');
    }

    // Protection basique contre l'injection d'en‑têtes
    function has_header_injection($str) {
        return preg_match("/[\r\n]/", $str);
    }
    if (has_header_injection($nom) || has_header_injection($email) || has_header_injection($sujet)) {
        http_response_code(400);
        exit('Valeurs invalides détectées.');
    }

    // Préparation du mail
    $to            = 'bonaventis.prjct@gmail.com';
    $email_subject = "Nouveau message de contact — $sujet";

    // Construction du corps du message
    $body  = "";
    if ($mode) {
        $body .= "Mode de contact souhaité : $mode\n";
    }
    $body .= "Nom / Institution       : $nom\n";
    $body .= "Adresse e‑mail          : $email\n\n";
    $body .= "Message :\n$message\n";

    // Headers
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: \"$nom\" <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Envoi du mail
    if (mail($to, $email_subject, $body, $headers)) {
        http_response_code(200);
        echo 'Votre message a bien été envoyé.';
    } else {
        http_response_code(500);
        echo 'Erreur lors de l\'envoi du message.';
    }

} else {
    // Redirection si accès direct sans POST
    header('Location: général.html', true, 302);
    exit;
}
?>
