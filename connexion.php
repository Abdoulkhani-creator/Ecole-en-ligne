<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor";

// Vérifier si les données du formulaire ont été envoyées
if(isset($_POST["name"]) && isset($_POST["pass"])) {
    // Connexion à la base de données
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if (!$conn) {
        die("Oops! La connexion à la base de données a échoué : " . mysqli_connect_error());
    }

    // Récupération des données du formulaire
    $Nom = $_POST["name"];
    $motdepasse = $_POST["pass"];

    // Requête pour récupérer le mot de passe associé au nom d'administrateur
    $query = "SELECT password FROM administrateur WHERE Nom_admin='$Nom'";
    $result = mysqli_query($conn, $query);

    // Vérification de l'existence du nom d'administrateur dans la base de données
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];

        // Vérification du mot de passe
        if (password_verify($motdepasse, $hashedPassword)) {
            // Authentification réussie, redirection vers la page d'accueil
            $_SESSION['admin_id'] = $Nom;
            header("Location: Accueil.php");
            exit();
        } else {
            $error_message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
        }
    } else {
        $error_message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
    }
} else {
    $error_message = "Veuillez entrer un nom d'utilisateur et un mot de passe.";
}

// Supprimer les avertissements sur les clés non définies
error_reporting(E_ERROR | E_PARSE);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #333;
        }

        p {
            color: #dd3333;
            font-size: 18px;
            margin-bottom: 20px;
        }

        a {
            color: #3366cc;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Erreur de Connexion</h1>
    <p><?php echo $error_message; ?></p>
    <a href="admin_login.html">Retourner à la page de connexion</a>
</body>
</html>
