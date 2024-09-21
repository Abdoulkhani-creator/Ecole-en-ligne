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
    $id_inscription = $_POST['id_inscription'];
    $nom = $_POST['Nom'];
    $prenom = $_POST['Prenom'];
    $email = $_POST['Email'];
    $mot_de_passe = $_POST['password'];
    $status = $_POST['Status'];
    $id_formation = $_POST['id_formation'];
    $date_inscription = $_POST['date_inscription'];

    // Crypter le mot de passe
    $mot_de_passe_hashed = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $query_modifier = "UPDATE inscription SET Nom=?, Prenom=?, Email=?, password=?, Status=?, id_formation=?, date_inscription=? WHERE id_inscription=?";
    $stmt_modifier = $connect->prepare($query_modifier);
    $stmt_modifier->bind_param("sssssssi", $nom, $prenom, $email, $mot_de_passe_hashed, $status, $id_formation, $date_inscription, $id_inscription);

    if ($stmt_modifier->execute()) {
        echo "L'inscription a été modifiée avec succès.";
        header("Refresh: 5; URL=inscription.php");
        echo "Vous serez redirigé vers la page des inscriptions dans 5 secondes.";
    } else {
        echo "Erreur lors de la modification de l'inscription : " . $stmt_modifier->error;
    }

    $stmt_modifier->close();
}

$connect->close();
?>
