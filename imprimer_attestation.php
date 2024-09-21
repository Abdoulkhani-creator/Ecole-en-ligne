<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

// Vérifiez la session de l'administrateur
if (!$admin_id) {
    header('location: connexion.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

$connect = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion à la base de données
if ($connect->connect_error) {
    die("La connexion à la base de données a échoué : " . $connect->connect_error);
}

// Récupérez l'identifiant de l'inscription depuis l'URL
$id_inscription = $_GET['id'] ?? null;

// Vérifiez si l'identifiant de l'inscription est défini
if (!$id_inscription) {
    echo "Identifiant de l'inscription non spécifié.";
    exit();
}

// Requête SQL pour récupérer les informations de l'inscription
$sql = "SELECT * FROM inscription WHERE id_inscription = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $id_inscription);
$stmt->execute();
$result = $stmt->get_result();

// Vérifiez si l'inscription existe
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $font = "BRUSHSCI.TTF";
    $image = imagecreatefromjpeg("certificate.jpg");
    $color = imagecolorallocate($image, 19, 21, 22);
    $name = $row['Nom'] . " " . $row['Prenom'];
    imagettftext($image, 50, 0, 1021, 841, $color, $font, $name);

    // Requête SQL pour récupérer le nom de la formation à partir de l'id_formation
    $id_formation = $row['id_formation'];
    $sql_formation = "SELECT name FROM products WHERE id = ?";
    $stmt_formation = $connect->prepare($sql_formation);
    $stmt_formation->bind_param("i", $id_formation);
    $stmt_formation->execute();
    $result_formation = $stmt_formation->get_result();

    if ($result_formation->num_rows > 0) {
        $row_formation = $result_formation->fetch_assoc();
        $formation_name = $row_formation['name'];

        // Ajouter le nom de la formation à l'attestation avec un font, une taille et une couleur spécifiques
        imagettftext($image, 40, 0, 933, 1185, imagecolorallocate($image, 96, 74, 123), "AGENCYR.ttf", $formation_name);

    } else {
        // Gérer l'erreur si aucune formation n'est trouvée
        echo "Erreur: Aucune formation trouvée pour l'inscription.";
        exit();
    }

    $date = date("jS F Y");

    // Nom du fichier basé sur le nom de l'inscription et le temps
    $file = $row['Nom'] . "_" . $row['Prenom'] . "_" . time();

    // Ajouter le temps à l'attestation
    imagettftext($image, 20, 0, 1049, 1273, $color, "AGENCYR.TTF", $date );

    // Enregistrer le fichier d'attestation dans le dossier "certificate"
    imagejpeg($image, "certificate/" . $file . ".jpg");

    imagedestroy($image);
    echo "Attestation générée avec succès.";
    header("Refresh: 5; URL=inscription.php");
} else {
    echo "Aucune inscription trouvée avec cet identifiant.";
}

$stmt->close();
$connect->close();
?>
