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
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $numero = $_POST['numero'];
    $statut = $_POST['statut'];
    $id_formation = $_POST['id_formation'];
    $date_inscription = $_POST['date_inscription'];

    // Crypter le mot de passe
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
if ($statut === 'inscrit'|| $statut === 'participant') {
        $query_ajouter = "INSERT INTO inscription (Nom, Prenom, Email, password, Status, id_formation, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_ajouter = $connect->prepare($query_ajouter);
        $stmt_ajouter->bind_param("sssssss", $nom, $prenom, $email, $password_hashed, $statut, $id_formation, $date_inscription);
    }

    if ($stmt_ajouter->execute()) {
        echo "Le inscription ou l'inscrit a été ajouté avec succès.";

        if ($statut === 'inscrit'|| $statut === 'participant') {
            header("Refresh: 5; URL=inscription.php");
            echo "Vous serez redirigé vers la page des inscrits dans 5 secondes.";
        }
    } else {
        echo "Erreur lors de l'ajout du inscription ou de l'inscrit : " . $stmt_ajouter->error;
    }

    $stmt_ajouter->close();
}

$connect->close();
?>
