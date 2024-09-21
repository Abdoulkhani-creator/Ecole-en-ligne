<?php
session_start();

// Vérifie si l'administrateur est connecté
$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    // Redirige vers la page de connexion si l'administrateur n'est pas connecté
    header('location: connexion.php');
    exit();
}

// Informations de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

// Connexion à la base de données
$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion à la base de données
if ($connect->connect_error) {
    // Arrête le script en cas d'échec de connexion
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Vérifie si le formulaire a été soumis
if (isset($_POST['modifier_formateur'])) {
    // Récupère les données du formulaire
    $formateur_id = $_POST['id_formateur'];
    $formateur_nom = $_POST['nom_formateur'];
    $formateur_prenom = $_POST['prenom_formateur'];
    $formateur_email = $_POST['email'];
    $formateur_password = $_POST['password'];
    $formateur_numero = $_POST['numero'];

    // Prépare la requête de mise à jour
    $query_modifier = "UPDATE formateur SET Nom_formateur=?, Prenom_formateur=?, email=?, password=?, numero=? WHERE id_formateur=?";
    $stmt_modifier = $connect->prepare($query_modifier);
    
    // Lie les paramètres à la requête préparée
    $stmt_modifier->bind_param("sssssi", $formateur_nom, $formateur_prenom, $formateur_email, $formateur_password, $formateur_numero, $formateur_id);

    // Exécute la requête de mise à jour
    if ($stmt_modifier->execute()) {
        // Rediriger vers la page formateur.php après un court délai
        header("Refresh: 5; URL=formateur.php");
        echo "Le formateur a été modifié avec succès. Vous serez redirigé vers la page des employés dans 5 secondes.";
    } else {
        echo "Erreur lors de la modification du formateur : " . $stmt_modifier->error;
    }

    // Ferme la requête préparée
    $stmt_modifier->close();
}

// Ferme la connexion à la base de données
$connect->close();
?>
