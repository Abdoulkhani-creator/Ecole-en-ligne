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
    // En supposant que 'id_formateur' est auto-incrémenté
    // $formateur_num = $_POST['formateur_num'];
    $formateur_nom = $_POST['formateur_nom'];
    $formateur_prenom = $_POST['formateur_prenom'];
    $formateur_email = $_POST['formateur_email'];
    $formateur_password = $_POST['formateur_password'];
    $formateur_numero = $_POST['formateur_numero'];

    $query_ajouter = "INSERT INTO formateur (Nom_formateur, Prenom_formateur, email, password, numero) VALUES (?, ?, ?, ?, ?)";
    $stmt_ajouter = $connect->prepare($query_ajouter);
    $stmt_ajouter->bind_param("ssssi", $formateur_nom, $formateur_prenom, $formateur_email, $formateur_password, $formateur_numero);

    if ($stmt_ajouter->execute()) {
        echo "Le formateur a été ajouté avec succès.";

        // Rediriger vers la page des employés après l'ajout réussi
        header("Refresh: 5; URL=formateur.php");
        echo "Vous serez redirigé vers la page des employés dans 5 secondes.";
    } else {
        echo "Erreur lors de l'ajout du formateur : " . $stmt_ajouter->error;
    }

    $stmt_ajouter->close();
}

$connect->close();
?>
