<?php
include("../../admin/databases/db_to_mysql.php");

$sql = "SELECT id, dimension, poids FROM detail_carton";
$result = mysqli_query($conn, $sql);

$cartons = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cartons[] = [
            'id' => $row['id'],
            'dimension' => $row['dimension'],
            'poids' => $row['poids']
        ];
    }
}
mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($cartons);
?>
