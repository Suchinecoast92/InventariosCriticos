<?php 
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

$query = "DELETE FROM mermas WHERE idMerma = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=error");
}
exit;
?>
