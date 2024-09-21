<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = new mysqli($servername, $username, $password, $dbname);

if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

if (isset($_GET['delete'])) {
    $inscription_id = $_GET['delete'];

    // Supprimer l'inscription de la base de données
    $query_supprimer = "DELETE FROM inscription WHERE id_inscription = ?";
    $stmt_supprimer = $connect->prepare($query_supprimer);
    $stmt_supprimer->bind_param("i", $inscription_id);

    if ($stmt_supprimer->execute()) {
        echo "L'inscription a été supprimée avec succès.";

        // Rediriger vers la page des inscriptions après la suppression réussie
        header("Refresh: 5; URL=inscription.php");
        echo "Vous serez redirigé vers la page des inscriptions dans 5 secondes.";
    } else {
        echo "Erreur lors de la suppression de l'inscription : " . $stmt_supprimer->error;
    }

    $stmt_supprimer->close();
}

$connect->close();
?>
