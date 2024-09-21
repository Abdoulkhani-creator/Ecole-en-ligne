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

if (isset($_POST['submit'])) {
    $nom_formation = $_POST['nom_formation']; // Récupérer le nom de la formation depuis le formulaire
    $jour = $_POST['jour'];
    $date_debut = $_POST['date_debut'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $date_fin = $_POST['date_fin'];
    $lieu = $_POST['lieu'];

    // Requête SQL pour obtenir l'ID de la formation à partir de son nom
    $query_obtenir_id_formation = "SELECT id FROM products WHERE name = ?";
    $stmt_obtenir_id_formation = $connect->prepare($query_obtenir_id_formation);
    $stmt_obtenir_id_formation->bind_param("s", $nom_formation);
    $stmt_obtenir_id_formation->execute();
    $result_obtenir_id_formation = $stmt_obtenir_id_formation->get_result();

    if ($result_obtenir_id_formation->num_rows > 0) {
        $row = $result_obtenir_id_formation->fetch_assoc();
        $id_formation = $row['id'];

        // Requête SQL pour mettre à jour la session
        $query_update_session = "UPDATE session SET jour = ?,date_debut = ?, heure_debut = ?, heure_fin = ?,Lieu = ?,date_fin = ? WHERE id_formation = ?";
        $stmt_update_session = $connect->prepare($query_update_session);
        $stmt_update_session->bind_param("ssssssi", $jour, $date_debut,$heure_debut, $heure_fin, $lieu, $date_fin, $id_formation);

        if ($stmt_update_session->execute()) {
            echo "La session de formation a été mise à jour avec succès.";

            // Redirection vers la page des sessions après la mise à jour réussie
            header("Refresh: 5; URL=emploi.php");
            echo "Vous serez redirigé vers la page des sessions dans 5 secondes.";
        } else {
            echo "Erreur lors de la mise à jour de la session de formation : " . $stmt_update_session->error;
        }

        $stmt_update_session->close();
    } else {
        echo "Aucune formation trouvée avec ce nom.";
    }

    $stmt_obtenir_id_formation->close();
}

$connect->close();
?>
