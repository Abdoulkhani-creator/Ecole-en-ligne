<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Vérifie si l'ID de l'administrateur est présent dans la session
if (!$admin_id) {
    // Redirige vers la page de connexion si l'administrateur n'est pas connecté
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion à la base de données
if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Vérifie si le paramètre 'delete' est passé dans l'URL
if (isset($_GET['delete'])) {
    $formation_id = $_GET['delete'];

    // Supprimer les chapitres associés à la formation
    $query_delete_chapitres = "DELETE FROM chapitre WHERE id_formation = ?";
    $stmt_delete_chapitres = $connect->prepare($query_delete_chapitres);
    $stmt_delete_chapitres->bind_param("i", $formation_id);
    $stmt_delete_chapitres->execute();
    $stmt_delete_chapitres->close();

    // Supprimer les sessions associées à la formation
    $query_delete_sessions = "DELETE FROM session WHERE id_formation = ?";
    $stmt_delete_sessions = $connect->prepare($query_delete_sessions);
    $stmt_delete_sessions->bind_param("i", $formation_id);
    $stmt_delete_sessions->execute();
    $stmt_delete_sessions->close();

    // Supprimer la formation
    $query_delete_formation = "DELETE FROM products WHERE id = ?";
    $stmt_delete_formation = $connect->prepare($query_delete_formation);
    $stmt_delete_formation->bind_param("i", $formation_id);
    $stmt_delete_formation->execute();
    $stmt_delete_formation->close();

    // Afficher un message de suppression réussie
    echo "La formation, ses sessions et ses chapitres associés ont été supprimés avec succès.";

    // Rediriger vers la page des formations après la suppression
    header("Refresh: 5; URL=formation.php");
    exit();
}

// Ferme la connexion à la base de données
$connect->close();
?>
