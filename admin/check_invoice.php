<?php
// Assuming you have a PDO database connection established in connect.php
include 'connect.php';

// Check if the reservation ID is provided
if(isset($_POST['id'])) {
    $invoice_id = $_POST['id'];
    
    // Perform a database query to check if an agreement exists for the provided reservation ID
    $query = "SELECT * FROM agreement WHERE InvoiceID = :invoice_id";
    $statement = $con->prepare($query);
    $statement->bindValue(':invoice_id', $invoice_id);
    $statement->execute();
    
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($result && count($result) > 0) {
        // Agreement exists
        echo 'exists';
    } else {
        // Agreement does not exist
        echo 'not_exists';
    }
} else {
    // No reservation ID provided
    echo 'invalid_request';
}
?>
