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

if (isset($_POST['modifier_formation'])) {
    $formation_id = $_POST['id_formation'];
    $formateur_id = $_POST['id_formateur'];
    $formation_name = $_POST['formation_nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie = $_POST['categorie'];

    $image_path = "images/";

    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            echo "Désolé, le fichier existe déjà.";
            $uploadOk = 0;
        }

        if ($_FILES['image']['size'] > 500000000) {
            echo "Désolé, votre fichier est trop volumineux.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Désolé, seuls les fichiers JPG, JPEG et PNG sont autorisés.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Désolé, votre fichier n'a pas été téléchargé.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                echo "Le fichier " . htmlspecialchars(basename($_FILES['image']['name'])) . " a été téléchargé.";
                $image_path = $target_file;
            } else {
                echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            }
        }
    }

    // Préparation de la requête pour modifier la formation
    $query_modifier = "UPDATE products SET name=?, price=?, image=?, id_formateur=?, Description=?, price=?, categorie=? WHERE id=?";
    $stmt_modifier = $connect->prepare($query_modifier);
    $stmt_modifier->bind_param("sssssssi", $formation_name, $prix, $image_path, $formateur_id, $description, $prix, $categorie, $formation_id);

    // Exécution de la requête
    if ($stmt_modifier->execute()) {
        header("Refresh: 5; URL=formation.php");
        echo "La formation a été modifiée avec succès. Vous serez redirigé vers la page des formations dans 5 secondes.";
    } else {
        echo "Erreur lors de la modification de la formation : " . $stmt_modifier->error;
    }

    // Fermeture de l'instruction
    $stmt_modifier->close();
}
    // Récupérer les données du formulaire
    $id_formation = $_POST['id_formation'];
    $nombre_chapitres = $_POST['nombre_chapitres'];

// Parcourir les chapitres et mettre à jour les noms
for ($i = 1; $i <= $nombre_chapitres; $i++) {
    $nom_chapitre = $_POST['chapitre_' . $i];
    $chapitre_id = $_POST['chapitre_id_' . $i]; // Ajouter un champ caché pour stocker l'ID du chapitre

    // Préparer la requête SQL pour la mise à jour du nom du chapitre
    $query_update_chapitre = "UPDATE chapitre c
                              INNER JOIN products f ON c.id_formation = f.id
                              SET c.nom_chapitre = ?
                              WHERE c.id_chapitre = ? AND f.id = ?";
    // Préparer l'instruction SQL
    $stmt_update_chapitre = $connect->prepare($query_update_chapitre);
    // Lier les paramètres
    $stmt_update_chapitre->bind_param("sii", $nom_chapitre, $chapitre_id, $id_formation); // s pour String et i pour Integer
    // Exécuter la requête

    if ($stmt_update_chapitre->execute()) {
        echo "Chapitre $i mis à jour avec succès.<br>";
    } else {
        echo "Erreur lors de la mise à jour du chapitre $i.<br>";
    }
    // Fermer l'instruction
    $stmt_update_chapitre->close();
}



// Fermeture de la connexion
$connect->close();
?>

