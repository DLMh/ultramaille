<?php
ob_start();
header('Content-Type: application/json');
include("../../admin/databases/db_to_mysql.php");

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['pdfData'], $data['client'], $data['exp'])) {
    $pdfData = $data['pdfData'];
    $client = $conn->real_escape_string($data['client']);
    $exp = $conn->real_escape_string($data['exp']);
    $filePath = "pdfs/PackingList_{$client}_{$exp}.pdf";

    // Vérifier si le dossier est accessible
    if (!is_writable(dirname($filePath))) {
        echo json_encode(['success' => false, 'message' => 'Le dossier pdfs/ n\'est pas accessible en écriture.']);
        exit;
    }

    // Supprimer l'ancien fichier s'il existe
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Sauvegarder le fichier
    if (file_put_contents($filePath, base64_decode($pdfData)) === false) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Erreur de sauvegarde du fichier.']);
        exit;
    }

    // Mettre à jour ou insérer dans la base de données
    $sql = "SELECT id FROM packing_listfile WHERE nomcli='$client' AND ref_exp='$exp'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sql = "UPDATE packing_listfile SET file_path='$filePath' WHERE nomcli='$client' AND ref_exp='$exp'";
    } else {
        $sql = "INSERT INTO packing_listfile (nomcli, ref_exp, file_path) VALUES ('$client', '$exp', '$filePath')";
    }

    if ($conn->query($sql)) {
        ob_clean();
        echo json_encode(['success' => true]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
}

$conn->close();
?>
