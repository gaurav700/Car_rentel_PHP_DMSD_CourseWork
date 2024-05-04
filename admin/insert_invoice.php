<?php
session_start();

include 'connect.php';

// Get form data
$Id = $_POST['Id'];
$status = $_POST['status'];
$date = date("Y-m-d");
$taxes = $_POST['taxes'];
$total = $_POST['total'];

try {
    // Insert data into the invoice table
    $stmt = $con->prepare("INSERT INTO invoice (Id, Status, Date, Taxes, Total) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$Id, $status, $date, $taxes, $total]);

    // Update the agreement table with the InvoiceID
    $updateStmt = $con->prepare("UPDATE agreement SET InvoiceID = ? WHERE ReservationID = ?");
    $updateStmt->execute([$Id, $Id]);
    
    // Optionally, you can check for successful execution and handle any errors here
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
