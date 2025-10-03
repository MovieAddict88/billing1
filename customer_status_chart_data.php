<?php
require_once "includes/headx.php";
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);
$location = $_SESSION['user_location'] ?? '';
$data = $admins->fetchCustomerStatusByLocation($location);

header('Content-Type: application/json');
echo json_encode($data);
?>