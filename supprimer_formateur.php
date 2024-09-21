<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Si l'ID de l'administrateur n'est pas présent dans la session, rediriger vers la page de connexion
if (!$admin_id) {
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor"; // Utilisation de la base de données 'tutor'

$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion à la base de données
if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Vérifier si le paramètre 'delete' est passé dans l'URL
if (isset($_GET['delete'])) {
    $id_formateur = $_GET['delete'];

    // Préparer la requête de suppression
    $query_delete_formateur = "DELETE FROM formateur WHERE id_formateur = ?";
    $stmt_delete_formateur = $connect->prepare($query_delete_formateur);
    $stmt_delete_formateur->bind_param("i", $id_formateur); // i pour indiquer que c'est un entier

    // Exécuter la requête de suppression
    if ($stmt_delete_formateur->execute()) {
        echo "Le formateur a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du formateur : " . $stmt_delete_formateur->error;
    }

    // Fermer la requête
    $stmt_delete_formateur->close();

    // Rediriger vers la page des administrateurs après la suppression
    header('location: formateur.php');
}

// Fermer la connexion à la base de données
$connect->close();
?>
