<?php
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location: admin_login.html');
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
    // Récupération des données du formulaire
    $id_formation = $_POST['id_formation'];
    $id_formateur = $_POST['id_formateur'];
    $formation_name = $_POST['nom_formation'];
    $description = $_POST['description'];
    $categorie = $_POST['categorie']; // Récupérer la catégorie après celle de prix
    $prix = ($categorie === 'Gratuite') ? 0 : $_POST['prix']; // Définir la valeur par défaut pour le champ price
    $formation_image = $_FILES['formation_image']['name']; // Nom de l'image téléchargée
    $image_extension = pathinfo($_FILES['formation_image']['name'], PATHINFO_EXTENSION);

    // Vérifier l'extension de l'image
    $allowed_extensions = array("jpg", "jpeg", "png");
    if (!in_array($image_extension, $allowed_extensions)) {
        echo "L'extension de fichier n'est pas autorisée. Veuillez télécharger une image au format JPG, JPEG ou PNG.";
        exit();
    }

    // Emplacement temporaire de l'image téléchargée
    $image_tmp = $_FILES['formation_image']['tmp_name'];

    // Déplacer l'image vers un dossier de destination
    $destination_folder = "C:/xampp/htdocs/tutor/images/";
    $destination_path = $destination_folder . $formation_image;

    if (move_uploaded_file($image_tmp, $destination_path)) {
        // Préparation de la requête d'ajout
        $query_ajouter = "INSERT INTO products (id, name, id_formateur, Image,Description,price,categorie) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_ajouter = $connect->prepare($query_ajouter);
        $stmt_ajouter->bind_param("isissis", $id_formation, $formation_name, $id_formateur, $formation_image, $description,$prix,  $categorie);

        if ($stmt_ajouter->execute()) {
            // Récupérer le nombre de chapitres
            if (isset($_POST['nombre_chapitres'])) {
                $nombre_chapitres = $_POST['nombre_chapitres'];
                // Ajouter les chapitres après avoir vérifié que $nombre_chapitres est défini
                for ($i = 1; $i <= $nombre_chapitres; $i++) {
                    $nom_chapitre = $_POST['chapitre_' . $i];

                    // Préparation de la requête d'ajout de chapitre
                    $query_insert_chapitre = "INSERT INTO Chapitre (id_formation, nom_chapitre) VALUES (?, ?)";
                    $stmt_insert_chapitre = $connect->prepare($query_insert_chapitre);
                    $stmt_insert_chapitre->bind_param("is", $id_formation, $nom_chapitre);

                    if ($stmt_insert_chapitre->execute()) {
                        echo "Chapitre ajouté avec succès.<br>";
                    } else {
                        echo "Erreur lors de l'ajout du chapitre.<br>";
                    }

                    $stmt_insert_chapitre->close();
                }
            } else {
                echo "Le champ nombre_chapitres n'a pas été défini dans le formulaire.";
            }

            echo "La formation a été ajoutée avec succès.";

            // Rediriger vers la page des formations après l'ajout réussi
            header("Refresh: 5; URL=formation.php");
            echo "Vous serez redirigé vers la page des formations dans 5 secondes.";
        } else {
            echo "Erreur lors de l'ajout de la formation : " . $stmt_ajouter->error;
        }

        $stmt_ajouter->close();
    } else {
        echo "Erreur lors du téléchargement de l'image.";
    }

    $connect->close();
}
?>
